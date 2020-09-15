<?php

namespace App\Controller;

use App\Entity\Content;
use App\Form\IndexPosterType;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/content")
 */
class ContentController extends AbstractController
{
    /**
     * @Route("/", name="content_index", methods={"GET"})
     * @param ContentRepository $contentRepository
     * @return Response
     */
    public function index(ContentRepository $contentRepository): Response
    {
        return $this->render('content/index.html.twig', [
            'contents' => $contentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="content_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $content = new Content();
        $form = $this->createForm(IndexPosterType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($content);
            $entityManager->flush();

            return $this->redirectToRoute('content_index');
        }

        return $this->render('content/new.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="content_show", methods={"GET"})
     * @param Content $content
     * @return Response
     */
    public function show(Content $content): Response
    {
        return $this->render('content/show.html.twig', [
            'content' => $content,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="content_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Content $content
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Content $content, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IndexPosterType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('content_index');
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="content_delete", methods={"DELETE"})
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
