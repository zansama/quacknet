<?php

namespace App\Controller\ApiController;

use App\Entity\Quack;
use App\Repository\QuackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class QuackApiController extends AbstractController
{
    /**
     * @Route("/api/quack", name="api_quack")
     * @param QuackRepository $quackRepository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(QuackRepository $quackRepository)
    {
        $result  = $quackRepository->findBy(array(), array('created_at' => 'DESC'));
        return $this->json($result);
    }

    /**
     * @Route("/api/deletequack/{id}", name="api_delete_quack", methods={"DELETE"})
     * @param Quack $quack
     * @return JsonResponse
     */
    public function delete(Quack $quack): JsonResponse
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($quack);
        $entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );


    }



    /**
     * @Route("/api/quack/{id}", name="quack_findby_id", methods={"GET"})
     *
     */
    public function findById(QuackRepository $quackRepository, Quack $quack) {

        $result = $quackRepository->find($quack);
        return $this->json($result);

    }



}
