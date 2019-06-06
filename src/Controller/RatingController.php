<?php

namespace App\Controller;

use App\Entity\PlanningVoyage;
use App\Entity\Rating;
use App\Service\PlanningVoyageManager;
use App\Service\RatingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/rating")
 */
class RatingController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="listRating")
     */
    public function list(RatingManager $ratingManager)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Vote trouver !',
        );

        $ratings = $ratingManager->listRating();

        if (!empty($ratings)) {
            $response = array(
                'code' => 1,
                'data' => $ratings,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"}, name="showOneRating")
     */
    public function showOne(RatingManager $ratingManager, $id)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Vote trouver !',
        );

        $ratings = $ratingManager->showOneRating($id);

        if (!empty($ratings)) {
            $response = array(
                'code' => 1,
                'data' => $ratings,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/add", methods={"POST"}, name="addRating")
     */
    public function add(RatingManager $ratingManager, Request $request)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Vote crée avec succès !',
        );

        $dataVote = $request->get('vote');
        $dataPlanning = $request->get('planningVoyage');

        $result = $ratingManager->addRating($dataPlanning['id'], $dataVote);

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'aucun Vote Crée !',
            );
        }

        return $this->json($response, $jsonResponse);
    }


    /**
     * @Route("/update/{id}", defaults={"id": 0}, requirements={"id"="\d+"}, methods={"PUT"}, name="updateRating")
     */
    public function update(RatingManager $ratingManager, Request $request, $id)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Vote à bien été modifié !',
        );

        $dataVote = $request->get('vote');
        $dataPlanning = $request->get('planningVoyage');

        $result = $ratingManager->updateRating($id, $dataPlanning['id'], $dataVote);

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'Modification du Vote échoué !',
            );
        }

        return $this->json($response, $jsonResponse);

    }

    /**
     * @Route("/delete/{id}", defaults={"id": 0}, requirements={"id"="\d+"}, methods={"DELETE"}, name="deleteRating")
     */
    public function delete(RatingManager $ratingManager, $id)
    {
        $result = $ratingManager->deleteRating($id);

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Vote à bien été supprimer',
        );

        if ($result == false) {

            $response = array(
                'code' => -1,
                'message' => 'aucun Vote trouver !',
            );
        }

        return $this->json($response, $jsonResponse);

    }

}
