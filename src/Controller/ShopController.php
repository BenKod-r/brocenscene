<?php
/**
 * Created by IntelliJ IDEA.
 * User: Benharrat Khaled
 * Date: 15/08/2020
 * Time: 12:37
 */

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException;
use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;
use Symfony\Component\HttpFoundation\File\Exception\FormSizeFileException;
use Symfony\Component\HttpFoundation\File\Exception\IniSizeFileException;
use Symfony\Component\HttpFoundation\File\Exception\NoFileException;
use Symfony\Component\HttpFoundation\File\Exception\PartialFileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShopController
 * @package App\Controller
 * @Route("/shop")
 */
class ShopController extends AbstractController
{
    /**
     * @Route("/",name="shop_index")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository) :Response
    {
        return $this->render('shop/shop.html.twig', [
            'products' => $productRepository->findBy([], ['creationDate' => 'DESC'])
        ]);
    }

    /**
     * @Route("/product/new", name="product_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $posterFile */
            $posterFile = $form->get('img')->getData();
            try {
                $imgSlug = $fileUploader->upload($posterFile, $product->getName());
            } catch (IniSizeFileException | FormSizeFileException $e) {
                $this->addFlash('warning', 'Votre fichier est trop lourd, il ne doit pas dépasser 1Mo.');
                return $this->redirectToRoute('product_new');
            } catch (ExtensionFileException $e) {
                $this->addFlash('warning', 'Le format de votre fichier n\'est pas supporté.
                    Votre fichier doit être au format jpeg, jpg ou png.');
                return $this->redirectToRoute('product_new');
            } catch (PartialFileException | NoFileException | CannotWriteFileException $e) {
                $this->addFlash('warning', 'Fichier non enregistré, veuillez réessayer.
                    Si le problème persiste, veuillez contacter l\'administrateur du site');
                return $this->redirectToRoute('product_new');
            }

            $image = new Image();
            $image->setName($product->getName());
            $image->setSlug($imgSlug);
            $entityManager->persist($image);

            $product->setPoster($image);
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('shop_index');
        }

        return $this->render('shop/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}/edit", name="product_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('shop/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {

            if(!empty($product->getImage())) {
                foreach ($product->getImage() as $image) {
                    if (file_exists($this->getParameter('uploads_directory') . '/' . $image->getSlug()))
                        unlink($this->getParameter('uploads_directory') . '/' . $image->getSlug());
                    $product->removeImage($image);
                }
            }

            if (file_exists($this->getParameter('uploads_directory') . '/' . $product->getPoster()->getSlug()))
                unlink($this->getParameter('uploads_directory') . '/' . $product->getPoster()->getSlug());

            $entityManager->remove($product->getPoster());
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('shop_index');
    }
}