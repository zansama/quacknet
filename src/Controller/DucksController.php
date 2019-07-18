<?php

namespace App\Controller;

use App\Entity\Ducks;
use App\Form\DucksType;
use App\Repository\DucksRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("/ducks")
 */
class DucksController extends AbstractController
{
    /**
     * @Route("/", name="ducks_index", methods={"GET"})
     */
    public function index(DucksRepository $ducksRepository): Response
    {
        return $this->render('ducks/index.html.twig', [
            'ducks' => $ducksRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ducks_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $duck = new Ducks();
        $form = $this->createForm(DucksType::class, $duck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form['photo']->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $photoFileName = $fileUploader->upload($photo);
                // updates the 'photoname' property to store the PDF file name
                // instead of its contents
                $duck->setPhoto('/uploads/photo_directory/'.$photoFileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($duck);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('ducks/new.html.twig', [
            'duck' => $duck,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ducks_show", methods={"GET"})
     */
    public function show(Ducks $duck): Response
    {
        return $this->render('ducks/show.html.twig', [
            'duck' => $duck,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ducks_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ducks $duck, FileUploader $fileUploader): Response
    {
        if ($this->getUser()) {
            $form = $this->createForm(DucksType::class, $duck);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $photo = $form['photo']->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($photo) {
                    $photoFileName = $fileUploader->upload($photo);
                    // updates the 'photoname' property to store the PDF file name
                    // instead of its contents
                    $duck->setPhoto('/uploads/photo_directory/'.$photoFileName);
                }
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('ducks_edit', ['id' => $duck->getId()]);
            }

            return $this->render('userAccount/accountUser.html.twig', [
                'duck' => $duck,
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('app_login');

    }
    /**
     * @Route("/{id}", name="ducks_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ducks $duck): Response
    {
        if ($this->isCsrfTokenValid('delete'.$duck->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($duck);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ducks_index');
    }
}
