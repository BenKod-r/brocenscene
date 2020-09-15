<?php
/**
 * Created by IntelliJ IDEA.
 * User: Benharrat Khaled
 * Date: 15/08/2020
 * Time: 12:37
 */
namespace App\Controller;

use App\Entity\Content;
use App\Entity\Image;
use App\Form\EditContentType;
use App\Form\EditTextType;
use App\Form\EditPosterType;
use App\Form\IndexPosterType;
use App\Form\ContentType;
use App\Repository\ContentRepository;
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

class IndexController extends AbstractController
{
    /**
     * Home page display
     * Return the coach members
     * @Route("/",name="home_index")
     * @param ContentRepository $contentRepository
     * @return Response
     */
    public function index(ContentRepository $contentRepository) :Response
    {
        $response = file_get_contents('https://www.instagram.com/brocenscene/?__a=1');
        $data = json_decode($response, true);
        $followers = $data["graphql"]["user"]["edge_followed_by"];

        return $this->render('index.html.twig', [
            'content_flash' => $contentRepository->findOneBy(['category' => 'flash']),
            'content_poster' => $contentRepository->findOneBy(['category' => 'index']),
            'content_projects' => $contentRepository->findBy(['category' => 'project'], ['creationDate' => 'DESC']),
            'followers' => $followers,
            ]);
    }

    /**
     * @Route("/new/index/flash", name="index_new_flash", methods={"GET","POST"})
     * @param Request $request
     * @param ContentRepository $contentRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function newEditFlash(Request $request, ContentRepository $contentRepository, EntityManagerInterface $entityManager): Response
    {
        $content = $contentRepository->findOneBy(['category' => 'flash']) ?? new Content();

        $form = $this->createForm(EditContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content->setCategory('flash');
            $entityManager->persist($content);
            $entityManager->flush();

            return $this->redirectToRoute('home_index');
        }

        return $this->render('content/new.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new/index/poster", name="index_new_poster", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @return Response
     * @noinspection DuplicatedCode
     */
    public function newPoster(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $content = new Content();
        $form = $this->createForm(IndexPosterType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content->setCategory('index');
            $content->setTitle('BROC EN SCÈNE');

            /** @var UploadedFile $posterFile */
            $posterFile = $form->get('img')->getData();
            try {
                $imgSlug = $fileUploader->upload($posterFile, 'index poster');
            } catch (IniSizeFileException | FormSizeFileException $e) {
                $this->addFlash('warning', 'Votre fichier est trop lourd, il ne doit pas dépasser 1Mo.');
                return $this->redirectToRoute('index_new_poster');
            } catch (ExtensionFileException $e) {
                $this->addFlash('warning', 'Le format de votre fichier n\'est pas supporté.
                    Votre fichier doit être au format jpeg, jpg ou png.');
                return $this->redirectToRoute('index_new_poster');
            } catch (PartialFileException | NoFileException | CannotWriteFileException $e) {
                $this->addFlash('warning', 'Fichier non enregistré, veuillez réessayer.
                    Si le problème persiste, veuillez contacter l\'administrateur du site');
                return $this->redirectToRoute('index_new_poster');
            }

            $image = new Image();
            $image->setName($content->getTitle());
            $image->setSlug($imgSlug);
            $entityManager->persist($image);

            $content->setPoster($image);
            $entityManager->persist($content);

            $entityManager->flush();
            return $this->redirectToRoute('home_index');
        }

        return $this->render('content/new.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("edit/index/text/{content}", name="index_edit_text", methods={"GET","POST"})
     * @param Request $request
     * @param Content $content
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editIndexText(Request $request, Content $content, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditTextType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('home_index');
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/index/poster/{content}", name="index_edit_poster", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @param Content $content
     * @return Response
     * @noinspection DuplicatedCode
     */
    public function EditPoster(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Content $content): Response
    {
        $form = $this->createForm(EditPosterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $files */
            $file = $form->get('img')->getData();
                /** @noinspection DuplicatedCode */
            try {
                $imgSlug = $fileUploader->upload($file, 'index poster');
            } catch (IniSizeFileException | FormSizeFileException $e) {
                $this->addFlash('warning' , 'Votre fichier est trop lourd, il ne doit pas dépasser 1Mo.');
                return $this->redirectToRoute('index_edit_poster');
            } catch (ExtensionFileException $e) {
                $this->addFlash('warning' , 'Le format de votre fichier n\'est pas supporté.
                Votre fichier doit être au format jpeg, jpg ou png.');
                return $this->redirectToRoute('index_edit_poster');
            } catch (PartialFileException | NoFileException | CannotWriteFileException $e) {
                $this->addFlash('warning' , 'Fichier non enregistré, veuillez réessayer.
                Si le problème persiste, veuillez contacter l\'administrateur du site');
                return $this->redirectToRoute('index_edit_poster');
            }

            if (!empty($content->getPoster()))
                if (file_exists($this->getParameter('uploads_directory') . '/' . $content->getPoster()->getSlug()))
                    unlink($this->getParameter('uploads_directory') . '/' . $content->getPoster()->getSlug());

            $image = $content->getPoster();
            $image->setSlug($imgSlug);
            $content->setPoster($image);

            $entityManager->flush();
            return $this->redirectToRoute('home_index');
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new/index/project", name="index_new_project", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function newProject(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content->setCategory('project');

            /** @var UploadedFile $posterFile */
            $posterFile = $form->get('img')->getData();
            try {
                $imgSlug = $fileUploader->upload($posterFile, 'project');
            } catch (IniSizeFileException | FormSizeFileException $e) {
                $this->addFlash('warning', 'Votre fichier est trop lourd, il ne doit pas dépasser 1Mo.');
                return $this->redirectToRoute('index_new_poster');
            } catch (ExtensionFileException $e) {
                $this->addFlash('warning', 'Le format de votre fichier n\'est pas supporté.
                    Votre fichier doit être au format jpeg, jpg ou png.');
                return $this->redirectToRoute('index_new_poster');
            } catch (PartialFileException | NoFileException | CannotWriteFileException $e) {
                $this->addFlash('warning', 'Fichier non enregistré, veuillez réessayer.
                    Si le problème persiste, veuillez contacter l\'administrateur du site');
                return $this->redirectToRoute('index_new_poster');
            }

            $image = new Image();
            $image->setName($content->getTitle());
            $image->setSlug($imgSlug);
            $entityManager->persist($image);

            $content->setPoster($image);
            $entityManager->persist($content);

            $entityManager->flush();
            return $this->redirectToRoute('home_index');
        }

        return $this->render('content/new.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("edit/index/project/{content}", name="index_edit_project", methods={"GET","POST"})
     * @param Request $request
     * @param Content $content
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editProjectText(Request $request, Content $content, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('home_index');
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/index/{content}", name="index_delete", methods={"DELETE"})
     * @param Request $request
     * @param Content $content
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @noinspection DuplicatedCode
     */
    public function delete(Request $request, Content $content,EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$content->getId(), $request->request->get('_token'))) {
            if (!empty($content->getPoster())) {
                if (file_exists($this->getParameter('uploads_directory') . '/' . $content->getPoster()->getSlug()))
                    unlink($this->getParameter('uploads_directory') . '/' . $content->getPoster()->getSlug());
                $entityManager->remove($content->getPoster());
            }

            $entityManager->remove($content);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home_index');
    }
}