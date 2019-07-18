<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Quack;
use App\Form\CommentsType;
use App\Form\QuackType;
use App\Repository\CommentRepository;
use App\Repository\DucksRepository;
use App\Repository\QuackRepository;
use App\Service\FileUploader;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/quack")
 */
class QuackController extends AbstractController
{
    /**
     * @Route("/quackAdmin", name="quack_index", methods={"GET"})
     */
    public function index(QuackRepository $quackRepository): Response
    {

        return $this->render('quack/index.html.twig', [
            'quacks' => $quackRepository->findAll(),
        ]);
    }


    /**
     * @Route("/all", name="quack_all", methods={"GET"})
     * @Route("/all/{quack}", name="quack_id", methods={"POST"})
     * @param QuackRepository $quackRepository
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @return Response
     * @throws \Exception
     */
    public function lastQuack(QuackRepository $quackRepository, Request $request, CommentRepository $commentRepository, Quack $quack = null): Response
    {
        $forms = $quackRepository->findAll();
        foreach ($forms as $index => $form) {
            $comment = new Comment();
            $forms[$index] = $this->createForm(CommentsType::class, $comment);

        }

        foreach ($forms as $form) {
            $form->handleRequest($request);

        }
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setQuack($quack);
            $comment->setCreatedAt(new \DateTime('now', (new \DateTimeZone('Europe/Paris'))));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('quack_all');
        }
        foreach ($forms as &$form) {
            $form = $form->createView();
        }


        return $this->render('quack/quack.html.twig', [
            'quacks' => $quackRepository->findBy(array(), array('created_at' => 'DESC')),
            'forms' => $forms,
        ]);


    }

    /**
     * @Route("/new", name="quack_new", methods={"GET","POST"})
     * @throws \Exception
     */
    public function new(Request $request, FileUploader $fileUploader ): Response
    {

        if ($this->getUser()) {


            $quack = new Quack();
            $form = $this->createForm(QuackType::class, $quack);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $photo = $form['photo']->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($photo) {
                    $photoFileName = $fileUploader->upload($photo);
                    // updates the 'photoname' property to store the PDF file name
                    // instead of its contents
                    $quack->setPhoto('/uploads/photo_directory/'.$photoFileName);
                }
                $quack->setAuthor($this->getUser());
                $quack->setCreatedAt(new \DateTime('now', (new \DateTimeZone('Europe/Paris'))));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($quack);
                $entityManager->flush();

                return $this->redirectToRoute('quack_all');
            }

            return $this->render('quack/new.html.twig', [
                'quack' => $quack,
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('app_login');

    }

    /**
     * @Route("/{id}", name="quack_show", methods={"GET"})
     */
    public function show(Quack $quack): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $quack);
        return $this->render('quack/show.html.twig', [
            'quack' => $quack,
        ]);


        return $this->redirectToRoute('quack_all');
    }

    /**
     * @Route("/{id}/edit", name="quack_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quack $quack): Response
    {


        $this->denyAccessUnlessGranted('EDIT', $quack);
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quack_all');
        }

        return $this->render('quack/edit.html.twig', [
            'quack' => $quack,
            'form' => $form->createView(),
        ]);


        return $this->redirectToRoute('quack_all');
    }


    /**
     * @Route("/{id}", name="quack_delete", methods={"DELETE"})
     * @param Request $request
     * @param Quack $quack
     * @return Response
     */
    public function delete(Request $request, Quack $quack): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $quack);
        if ($this->isCsrfTokenValid('delete' . $quack->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quack_all');

        return $this->redirectToRoute('quack_all');
    }
}


