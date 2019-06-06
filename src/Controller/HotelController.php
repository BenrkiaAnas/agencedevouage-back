<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Service\HotelManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/hotel")
 */
class HotelController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="listHotel")
     */
    public function list(HotelManager $hotelManager)
    {
        $hotels = $hotelManager->listHotel();

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Hotel trouver !',
        );

        if (!empty($hotels)) {
            $response = array(
                'code' => 1,
                'data' => $hotels,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/{id}", methods={"GET"}, requirements={"id"="\d+"}, name="showOneHotel")
     */
    public function showOne(HotelManager $hotelManager, $id)
    {
        $hotel = $hotelManager->showOneHotel($id);

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Hotel trouver !',
        );

        if (!empty($hotel)) {
            $response = array(
                'code' => 1,
                'data' => $hotel,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/add", methods={"POST"}, name="addHotel")
     */
    public function add(HotelManager $hotelManager, Request $request)
    {
        /**
         * @var Serializer $serializer
         */
        $jsonResponse = Response::HTTP_OK;
        $message = "";

        $hotel = json_decode($request->getContent(), Hotel::class) ?: [];

        $result = $hotelManager->addHotel($hotel);
        if ($result == 1) {
            $message = "Hotel crée avec succès !";
        } elseif ($result == -1) {
            $message = "Hotel exist déjà!";
        } elseif($result == -2) {
            $message = 'aucun Hotel Crée !';
        }

        $response = array(
            'code' => $result,
            'message' => $message ,
        );

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/update/{id}", methods={"PUT"}, requirements={"id"="\d+"},defaults={"id": 0}, name="updateHotel")
     */
    public function update(HotelManager $hotelManager, Request $request, $id)
    {
        /**
         * @var Serializer $serializer
         */
        $jsonResponse = Response::HTTP_OK;
        $message = "";

        $serializer = $this->get('serializer');
        $data = $serializer->deserialize($request->getContent(), Hotel::class, 'json');

        $hotel = $hotelManager->showOneHotel($id);
        $result = $hotelManager->updateHotel($hotel, $data);

        if ($result == 1) {
            $message = "Hotel à bien été modifié !";
        } elseif ($result == -1) {
            $message = "Hotel exist déjà!";
        } elseif($result == -2) {
            $message = 'Modification d\'Hotel échoué !';
        }

        $response = array(
            'code' => $result,
            'message' => $message ,
        );

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/delete/{id}", methods={"DELETE"}, requirements={"id"="\d+"}, defaults={"id": 0}, name="deleteHotel")
     */
    public function delete(HotelManager $hotelManager, $id)
    {
        $result = $hotelManager->deleteHotel($id);

        $jsonResponse = Response::HTTP_OK;
        $message = '';
        if ($result == 1) {
            $message = "Hotel à bien été supprimer";
        } elseif ($result == -2) {
            $message = "Suppression impossible, hôtel déjà utilisé";
        } elseif($result == -1) {
            $message = 'aucun Hotel trouver !';
        }

        $response = array(
            'code' => $result,
            'message' => $message ,
        );

        return $this->json($response, $jsonResponse);
    }
}
