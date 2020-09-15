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
use App\Entity\Schedule;
use App\Form\ContentType;
use App\Form\EditContentType;
use App\Form\EditPosterType;
use App\Repository\ContentRepository;
use App\Repository\ScheduleRepository;
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
use \DateTime;

/**
 * Class ShowroomController
 * @package App\Controller
 * @Route("/showroom")
 */
class ShowroomController extends AbstractController
{
    /**
     * @Route("/",name="showroom_index")
     * @param ContentRepository $contentRepository
     * @param ScheduleRepository $scheduleRepository
     * @return Response
     */
    public function showroom(ContentRepository $contentRepository, ScheduleRepository $scheduleRepository) :Response
    {
        return $this->render('showroom/showroom.html.twig', [
            'content_showroom' => $contentRepository->findOneBy(['category' => 'showroom']),
            'schedules' => $scheduleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/poster", name="showroom_new_poster", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @return Response
     * @noinspection DuplicatedCode
     */
    public function newShowroom(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content->setCategory('showroom');

            /** @var UploadedFile $posterFile */
            $posterFile = $form->get('img')->getData();
            try {
                $imgSlug = $fileUploader->upload($posterFile, 'showroom');
            } catch (IniSizeFileException | FormSizeFileException $e) {
                $this->addFlash('warning', 'Votre fichier est trop lourd, il ne doit pas dépasser 1Mo.');
                return $this->redirectToRoute('showroom_new_poster');
            } catch (ExtensionFileException $e) {
                $this->addFlash('warning', 'Le format de votre fichier n\'est pas supporté.
                    Votre fichier doit être au format jpeg, jpg ou png.');
                return $this->redirectToRoute('showroom_new_poster');
            } catch (PartialFileException | NoFileException | CannotWriteFileException $e) {
                $this->addFlash('warning', 'Fichier non enregistré, veuillez réessayer.
                    Si le problème persiste, veuillez contacter l\'administrateur du site');
                return $this->redirectToRoute('showroom_new_poster');
            }

            $image = new Image();
            $image->setName($content->getTitle());
            $image->setSlug($imgSlug);
            $entityManager->persist($image);

            $content->setPoster($image);
            $entityManager->persist($content);

            $entityManager->flush();
            return $this->redirectToRoute('showroom_index');
        }

        return $this->render('content/new.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/poster/{content}", name="showroom_edit_poster", methods={"GET","POST"})
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
                $imgSlug = $fileUploader->upload($file, 'showroom');
            } catch (IniSizeFileException | FormSizeFileException $e) {
                $this->addFlash('warning', 'Votre fichier est trop lourd, il ne doit pas dépasser 1Mo.');
                return $this->redirectToRoute('showroom_edit_poster');
            } catch (ExtensionFileException $e) {
                $this->addFlash('warning', 'Le format de votre fichier n\'est pas supporté.
                Votre fichier doit être au format jpeg, jpg ou png.');
                return $this->redirectToRoute('showroom_edit_poster');
            } catch (PartialFileException | NoFileException | CannotWriteFileException $e) {
                $this->addFlash('warning', 'Fichier non enregistré, veuillez réessayer.
                Si le problème persiste, veuillez contacter l\'administrateur du site');
                return $this->redirectToRoute('showroom_edit_poster');
            }

            if (!empty($content->getPoster()))
                if (file_exists($this->getParameter('uploads_directory') . '/' . $content->getPoster()->getSlug()))
                    unlink($this->getParameter('uploads_directory') . '/' . $content->getPoster()->getSlug());

            $image = $content->getPoster();
            $image->setSlug($imgSlug);
            $content->setPoster($image);

            $entityManager->flush();
            return $this->redirectToRoute('showroom_index');
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/text/{content}", name="showroom_edit_text", methods={"GET","POST"})
     * @param Request $request
     * @param Content $content
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editShowroomText(Request $request, Content $content, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('showroom_index');
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/initialize", name="showroom_initialize", methods={"GET", "POST"})
     * @param ScheduleRepository $scheduleRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function initializeSchedule(ScheduleRepository $scheduleRepository, EntityManagerInterface $entityManager): Response
    {
        $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        if (empty($scheduleRepository->findAll())) {
            foreach ($days as $day) {
                $schedule = new Schedule();
                $schedule->setDay($day);
                $schedule->setStartMorning(new DateTime('07:30'));
                $schedule->setEndMorning(new DateTime('12:00'));
                $schedule->setStartAfternoon(new DateTime('14:30'));
                $schedule->setEndAfternoon(new DateTime('18:30'));
                $schedule->setOpen('Ouvert');

                $entityManager->persist($schedule);
            }

            $entityManager->flush();
        }

        return $this->redirectToRoute('showroom_index');
    }
}