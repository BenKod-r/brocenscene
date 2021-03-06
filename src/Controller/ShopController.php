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
use App\Form\ImageType;
use App\Form\ProductType;
use App\Form\UpdateProductType;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * Return furnitures on the shop page
     * @Route("/furnitures",name="shop_index")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository) :Response
    {
        return $this->render('shop/shop.html.twig', [
            'products' => $productRepository->findBy(['category' => 'Meuble'], ['creationDate' => 'DESC'])
        ]);
    }

    /**
     * Return decorations on the shop_deco page
     * @Route("/decorations",name="shop_deco")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function deco(ProductRepository $productRepository) :Response
    {
        return $this->render('shop/deco.html.twig', [
            'products' => $productRepository->findBy(['category' => 'Decoration'], ['creationDate' => 'DESC'])
        ]);
    }

    /**
     * Create new product on the shop page
     * @Route("/product/new", name="product_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
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

            if ($product->getCategory() === 'Meuble') return $this->redirectToRoute('shop_index');
            else return $this->redirectToRoute('shop_deco');
        }

        return $this->render('shop/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Adopt product on the shop page
     * @Route("/adopt/{product}", name="shop_adopt")
     * @IsGranted("ROLE_ADMIN")
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function adopt(Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($product->getStatus() === true) $product->setStatus(false);
        else $product->setStatus(true);

        $entityManager->flush();

        if ($product->getCategory() === 'Meuble') return $this->redirectToRoute('shop_index');
        else return $this->redirectToRoute('shop_deco');
    }

    /**
     * Return product for choice one action (add, edit, ...) on the actions page
     * @Route("/actions/{product}", name="shop_actions")
     * @IsGranted("ROLE_ADMIN")
     * @param Product $product
     * @return Response
     */
    public function actions(Product $product): Response
    {
        return $this->render('shop/actions.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * Add image in product on the shop actions page
     * @Route("/image/{product}/new", name="product_new_image", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @param Product $product
     * @return Response
     */
    public function newImage(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Product $product): Response
    {
        $form = $this->createForm(ImageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $files */
            $files = $form->get('img')->getData();

            foreach ($files as $file) {
                /** @noinspection DuplicatedCode */
                try {
                    $imgSlug = $fileUploader->upload($file, $product->getName());
                } catch (IniSizeFileException | FormSizeFileException $e) {
                    $this->addFlash('warning' , 'Votre fichier est trop lourd, il ne doit pas dépasser 1Mo.');
                    return $this->redirectToRoute('product_new');
                } catch (ExtensionFileException $e) {
                    $this->addFlash('warning' , 'Le format de votre fichier n\'est pas supporté.
                    Votre fichier doit être au format jpeg, jpg ou png.');
                    return $this->redirectToRoute('product_new');
                } catch (PartialFileException | NoFileException | CannotWriteFileException $e) {
                    $this->addFlash('warning' , 'Fichier non enregistré, veuillez réessayer.
                    Si le problème persiste, veuillez contacter l\'administrateur du site');
                    return $this->redirectToRoute('product_new');
                }

                $image = new image();
                $image->setName($product->getName());
                $image->setSlug($imgSlug);
                $image->addIproduct($product);
                $entityManager->persist($image);
            }

            $entityManager->flush();

            if ($product->getCategory() === 'Meuble') return $this->redirectToRoute('shop_index');
            else return $this->redirectToRoute('shop_deco');
        }

        return $this->render('shop/image.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit product on the shop page
     * @Route("/product/{product}/edit", name="product_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UpdateProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if ($product->getCategory() === 'Meuble') return $this->redirectToRoute('shop_index');
            else return $this->redirectToRoute('shop_deco');
        }

        return $this->render('shop/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete product on the home page
     * Delete poster in relation to product on the home page
     * Delete image in relation to product on the home page
     * @Route("/product/{product}", name="product_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {

            if (!empty($product->getPoster())) {
                if (file_exists($this->getParameter('uploads_directory') . '/' . $product->getPoster()->getSlug()))
                    unlink($this->getParameter('uploads_directory') . '/' . $product->getPoster()->getSlug());
                $entityManager->remove($product->getPoster());
            }

            if (!empty($product->getImage())) {
                foreach ($product->getImage() as $image) {
                    if (file_exists($this->getParameter('uploads_directory') . '/' . $image->getSlug()))
                        unlink($this->getParameter('uploads_directory') . '/' . $image->getSlug());
                    $product->removeImage($image);
                    $entityManager->remove($image);
                }
            }

            $entityManager->remove($product);
            $entityManager->flush();
        }

        if ($product->getCategory() === 'Meuble') return $this->redirectToRoute('shop_index');
        else return $this->redirectToRoute('shop_deco');
    }
}