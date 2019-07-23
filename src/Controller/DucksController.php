<?php

namespace App\Controller;

use App\Entity\Ducks;
use App\Form\DuckRegistrationType;
use App\Form\DucksType;
use App\Repository\DucksRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/ducks")
 */
class DucksController extends AbstractController
{
    /**
     * @Route("/", name="ducks_index", methods={"GET"})
     * @param DucksRepository $ducksRepository
     * @return Response
     */
    public function index(DucksRepository $ducksRepository): Response
    {
        return $this->render('ducks/index.html.twig', [
            'ducks' => $ducksRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ducks_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function new(Request $request, FileUploader $fileUploader, UserPasswordEncoderInterface $encoder): Response
    {
        $duck = new Ducks();
        $form = $this->createForm(DuckRegistrationType::class, $duck);
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
            $plainPassword = $form['password']->getdata();
            $encoded = $encoder->encodePassword($duck, $plainPassword);
            $duck->setPassword($encoded);
            $duck->setRoles(['ROLE_USER']);
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
     * @param Ducks $duck
     * @return Response
     */
    public function show(Ducks $duck): Response
    {
        return $this->render('ducks/show.html.twig', [
            'duck' => $duck,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ducks_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Ducks $duck
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function edit(Request $request, Ducks $duck, FileUploader $fileUploader, UserPasswordEncoderInterface $encoder): Response
    {
        if ($this->getUser()) {
            $form = $this->createForm(DuckRegistrationType::class, $duck);
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
                $plainPassword = $form['password']->getdata();
                $encoded = $encoder->encodePassword($duck, $plainPassword);
                $duck->setPassword($encoded);
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
     * @param Request $request
     * @param Ducks $duck
     * @return Response
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
