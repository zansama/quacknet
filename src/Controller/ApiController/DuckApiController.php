<?php

namespace App\Controller\ApiController;

use App\Entity\Ducks;
use App\Entity\Quack;
use App\Repository\DucksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DuckApiController extends AbstractController
{
    /**
     * @Route("/api/whoami", name="api_user", methods={"GET"})
     */
    public function index()
    {
        $result  = $this->getUser();
        return $this->json($result);
    }


    /**
     * @Route("/api/deleteduck/{id}", name="api_delete", methods={"DELETE"})
     * @param Ducks $duck
     * @return Response
     */
    public function delete(Ducks $duck): Response {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($duck);
        $entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );


    }



}
