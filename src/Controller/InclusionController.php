<?php

namespace App\Controller;

use App\Entity\Inclusion;
use App\Service\InclusionManager;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inclusion")
 */
class InclusionController extends AbstractController
{
    /**
     * @Route("/", name="listInclusion")
     * @Method({"GET"})
     */
    public function list(InclusionManager $inclusionManager)
    {
        $inclusions =$inclusionManager->listInclusion();

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucune Inclusion trouver !',
        );

        if (!empty($inclusions)) {
            $response = array(
                'code' => 1,
                'data' => $inclusions,
            );
        }

        return $this->json($response, $jsonResponse);
    }


    /**
     * @Route("/{id}", requirements={"id"="\d+"}, name="showOneInclusion")
     * @Method({"GET"})
     */
    public function showOne(InclusionManager $inclusionManager, $id)
    {
        $inclusion = $inclusionManager->showOneInclusion($id);

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucune Inclusion trouver !',
        );

        if (!empty($inclusion)) {
            $response = array(
                'code' => 1,
                'data' => $inclusion,
            );
        }

        return $this->json($response, $jsonResponse);
    }


    /**
     * @Route("/add", name="addInclusion")
     * @Method({"POST"})
     */
    public function add(InclusionManager $inclusionManager, Request $request)
    {
        /**
         * @var Serializer $serializer
         */
        $jsonResponse = Response::HTTP_OK;
        $message = "";

        $inclusion = json_decode($request->getContent(), Inclusion::class) ?: [];

        $newInclusion =  new Inclusion();
        $newInclusion->setLabel($inclusion['label']);
        $newInclusion->setIcon($inclusion['icon']);

        $result = $inclusionManager->addInclusion($newInclusion);
        if ($result == 1) {
            $message = "Inclusion crée avec succès !";
        } elseif ($result == -1) {
            $message = "Inclusion exist déjà!";
        } elseif($result == -2) {
            $message = 'aucune Inclusion Crée !';
        }

        $response = array(
            'code' => $result,
            'message' => $message ,
        );

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/update/{id}", requirements={"id"="\d+"}, defaults={"id": 0} ,name="updateInclusion")
     * @Method({"POST"})
     */
    public function update(InclusionManager $inclusionManager, Request $request, $id)
    {
        /**
         * @var Serializer $serializer
         */
        $jsonResponse = Response::HTTP_OK;
        $message = "";

        $serializer = $this->get('serializer');
        $data = $serializer->deserialize($request->getContent(), Inclusion::class, 'json');
        $inclusion = $inclusionManager->showOneInclusion($id);

        $result = $inclusionManager->updateInclusion($inclusion,$data);

        if ($result == 1) {
            $message = "Inclusion à bien été modifié !";
        } elseif ($result == -1) {
            $message = "Inclusion exist déjà!";
        } elseif($result == -2) {
            $message = 'Modification d\'Inclusion échoué !';
        }

        $response = array(
            'code' => $result,
            'message' => $message ,
        );

        return $this->json($response, $jsonResponse);
    }


    /**
     * @Route("/delete/{id}",name="deleteInclusion")
     * @Method({"DELETE"})
     */
    public function delete(InclusionManager $inclusionManager, $id)
    {
        $result = $inclusionManager->deleteInclusion($id);

        $jsonResponse = Response::HTTP_OK;
        $message = '';
        if ($result == 1) {
            $message = "Inclusion à bien été supprimer";
        } elseif ($result == -2) {
            $message = "Suppression impossible, inclusion déjà utilisé";
        } elseif($result == -1) {
            $message = 'aucune Inclusion trouver !';
        }

        $response = array(
            'code' => $result,
            'message' => $message ,
        );

        return $this->json($response, $jsonResponse);
    }
}
