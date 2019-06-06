<?php

namespace App\Controller;

use App\Entity\Promo;
use App\Repository\PlanningVoyageRepository;
use App\Repository\PromoRepository;
use App\Service\PlanningVoyageManager;
use App\Service\PromoManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/promo")
 */

class PromoController extends AbstractController
{

    /**
     * @Route("/add", name="add_promo" , methods={"POST"})
     */
    public function addPromo(PromoManager $promoManager, Request $request)
    {
        $datadtBegin =  new \DateTime($request->get('dateBegin'));
        $datadtEnd = new \DateTime($request->get('dateEnd'));
        $dataPourcentage = $request->get('pourcentage');
        $dataNbrPersonne = $request->get('nbrPersonne');
        $dataTypePromo = $request->get('typePromo');
        $dataLabelPromo = $request->get('label');
        //$planning = $request->get('planningVoyage');

        $promo = $promoManager->addPromo(
            $dataLabelPromo,
            $dataPourcentage,
            $datadtBegin,
            $datadtEnd,
            $dataTypePromo,
            $dataNbrPersonne
        );
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Create Promotion  Success !',
        );

        if ($promo == -1) {
            $response = array(
                'code' => -1,
                'message' => 'No Planning Created !',
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/update/{id}", requirements={"id"="\d+"}, defaults={"id": 0}, name="update_Promo", methods={"PUT"})
     */

    public function update(PromoManager $promoManager, Request $request,$id)
    {
        $dataPourcentage = $request->get('pourcentage');
        $dataDtBegin = new \DateTime($request->get('dateBegin'));
        $dataDtEnd = new \DateTime($request->get('dateEnd'));
        $dataTypePromo = $request->get('typePromo');
        $dataNbrPersonne = $request->get('nbrPersonne');
        $dataLabelPromo = $request->get('label');
        $data = array(
            'label' => $dataLabelPromo ,
            'pourcentage' => $dataPourcentage,
            'DtBegin' => $dataDtBegin,
            'DtEnd' => $dataDtEnd,
            'typePromotion' => $dataTypePromo,
            'nbrPersonn' => $dataNbrPersonne
        );

        $result = $promoManager->updatePromo($id,$data);

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Update Promo Success !',

        );

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'No Promo Updated !',
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/", name="list_Promo" , methods={"GET"})
     *
     */
    public function showAll(PromoManager $promoManager)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'No Promo Found !',
        );

        $promo = $promoManager->listPromos();

        if (!empty($promo)) {
            $jsonResponse = Response::HTTP_OK;
            $response = array(
                'code' => 1,
                'data' => $promo,
            );
        }

        return $this->json($response, $jsonResponse);
    }
    
    /**
     * @Route("/type/{type}",name="show_type_promo" ,methods={"GET"})
     */
    public function showPromoByType(PromoRepository $repository,Request $request,$type)
    {

        $promo = $repository->findBy(['typePromo' => $type]);

        if (empty($promo))
        {
            $response=array(
                'code'=>-1,
                'message'=>'No Promo Find',
            );
            return $this->json($response);
        }
        $response=array(
            'code' => 1,
            'data' => $promo
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }
   

    /**
     * @Route("/delete/{id}", name="delete_Promo" ,methods={"DELETE"})
     */
    public function delete(PromoManager $promoManager, $id)
    {
        $result = $promoManager->deletePromo($id);

        if ($result == false) {

            $response = array(
                'code' => -1,
                'message' => 'Promo Not found !',
            );
            return $this->json($response);
        }
        $response = array(
            'code' => 1,
            'message' => ' Delete Promo Success !',

        );
        return $this->json($response, 200);

    }

    /**
     * @Route("/{id}", name="showOne_Promo" , requirements={"id"="\d+"}  , methods={"GET"})
     *
     */
    public function showOnePromo(PromoManager $promoManager,$id)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'No Promo Found !',
        );

        $promos = $promoManager->showOnePromo($id);

        if (!empty($promos)) {
            $response = array(
                'code' => 1,
                'data' => $promos,
            );
        }
        return $this->json($response, $jsonResponse);
    }
    /**
     * @Route("/visible/{id}" , methods={"PUT"} , name="visible_Promo")
     */
    public function setVisibility(PromoRepository $repository ,ObjectManager $manager, $id)
    {
        $promo = $repository->find($id);
        $message = "";
        $result = false;
        if(!empty($promo))
        {
            if($promo->getVisible())
            {
                $promo->setVisible(false);
                $message = "Promo Is Invisible now";
            }else{
                $promo->setVisible(true);
                $message = "Promo Is Visible now";
            }
            $manager->flush();
            $result = true;
        }
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => $message,
        );

        if ($result == false) {

            $response = array(
                'code' => -1,
                'message' => 'No Promo Found !',
            );
        }

        return $this->json($response, $jsonResponse);
    }
    /**
     * @Route("/findVisible/{visibility}" , methods={"GET"} , name="find_visible_Promo")
     */
    public function findPromoByVisibility(PromoRepository $repository , $visibility)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'No Promo Found !',
        );
        $promo = $repository->findBy(['visible' => $visibility]);
        if(!empty($promo))
        {
            $response = array(
                'code' => 1,
                'data' => $promo
            );
        }
        return $this->json($response, $jsonResponse);

    }

    /**
     * @Route("/byDateInterval", methods={"POST"},  name="byDateIntervalPromo")
     */
    public function byDateInterval(PromoManager $promoManager, Request $request)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'No Promo Found !',
        );
        $dtBegin = new \DateTime($request->get('dateBegin'));
        $dtEnd = new \DateTime($request->get('dateEnd'));
        $visible = $request->get('visible');

        $promos = $promoManager->listPromoByDateInterval($dtBegin, $dtEnd, $visible);
        if (!empty($promos)) {
            $response = array(
                'code' => 1,
                'data' => $promos,
            );
        }

        return $this->json($response, $jsonResponse);
    }
    /**
     * @Route("/addPromoToMultiplePlanning/{promoId}", methods={"POST"},  name="addPromoToMultiplePlanning")
     */
    public function addPromoToMultiplePlanning($promoId , Request $request , PromoManager $promoManager )
    {
        $promo = $promoManager->showOnePromo($promoId);
        $arrayPlanning = $request->get("planning");
        $result = false;
        if(!empty($promo))
        {
            $result = $promoManager->addPromoToMultiplePlanning($promo,$arrayPlanning);

        }
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Add Promo To Multiple Planning Success !',

        );
        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'No Promo Found !',
            );
        }
        return $this->json($response, $jsonResponse);
    }
    /**
     * @Route("/checkUsingPromo/{id}" , methods={"POST"} , name="check_using_promo_to_planning")
     */
    public function checkUsingPromoForPlannings($id , PromoManager $promoManager)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'Ce promo n est pas utiliser !',
        );
        $result = $promoManager->checkUsingPromoForPlanning($id);
        if($result == true)
        {
            $response = array(
                'code' => 1,
                'message' => 'Ce promo Est utiliser !',
            );
        }
        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/verifyDateExpirationPromo" , methods={"GET"} , name = "verify_date_expiration_promo")
     */
    public function verifyDateExpirationPromo(PromoManager $promoManager)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'No Promo Expire !',
        );

        $result = $promoManager->verifierDateExpirationPromo();

        if ($result) {
            $jsonResponse = Response::HTTP_OK;
            $response = array(
                'code' => 1,
                'message' => 'Promo Expire !',
            );
        }

        return $this->json($response, $jsonResponse);
    }
}
