<?php

namespace App\Controller;

use App\Entity\PlanningVoyage;
use App\Entity\Rating;
use App\Repository\RatingRepository;
use App\Service\Functions;
use App\Service\HotelManager;
use App\Service\InclusionManager;
use App\Service\PlanningVoyageManager;
use App\Service\PromoManager;
use App\Service\RatingManager;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/planning")
 */
class PlanningVoyageController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="listPlanning")
     */
    public function list(PlanningVoyageManager $planningVoyageManager)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Planning trouver !',
        );

        $plannings = $planningVoyageManager->listPlanningVoyage();

        if (!empty($plannings)) {
            $response = array(
                'code' => 1,
                'data' => $plannings,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"}, name="showOnePlanning")
     */
    public function showOne(PlanningVoyageManager $planningVoyageManager, $id)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Planning trouver !',
        );

        $planning = $planningVoyageManager->showOnePlanningVoyage($id);

        if (!empty($planning)) {

            $ratings = $planningVoyageManager->getRatingsPlanningVoyage($planning);

            $response = array(
                'code' => 1,
                'data-planning' => $planning,
                'data-rating' => $ratings,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/add", methods={"POST"}, name="planning_add")
     */
    public function add(PlanningVoyageManager $planningVoyageManager, Request $request)
    {
        $dataNDay = $request->get('nbrDays');
        $dataBegin = new \DateTime($request->get('dateBegin'));
        $dataEnd = new \DateTime($request->get('dateBegin'));
        $dataEnd->add(\DateInterval::createFromDateString("$dataNDay days"));

        $data = array(
            "NDay" => $dataNDay,
            "NNight" => $request->get('nbrNight'),
            "NPlace" => $request->get('nbrPlace'),
            "PrxAdult" => $request->get('priceAdult'),
            "PrxChild" => $request->get('priceChild'),
            "description" => $request->get('description'),
            "hotel" => $request->get('hotel'),
            "voyage" => $request->get('voyageOrganise'),
            "dateBegin" => $dataBegin,
            "dateEnd" => $dataEnd
        );
        $result = $planningVoyageManager->addPlanningVoyage($data);

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => ' Planning crée avec succès !',
            'result' => $result
        );

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'aucun Planning Crée !',
            );
        }

        return $this->json($response, $jsonResponse);

    }

    /**
     * @Route("/update/{id}", requirements={"id"="\d+"},defaults={"id": 0}, methods={"PUT"}, name="updatePlanning")
     */
    public function update(PlanningVoyageManager $planningVoyageManager, Request $request, $id)
    {

        $dataNDay = $request->get('nbrDays');
        $dataBegin = new \DateTime($request->get('dateBegin'));
        $dataEnd = new \DateTime($request->get('dateBegin'));
        $dataEnd->add(\DateInterval::createFromDateString("$dataNDay days"));

        $result = false;
        $data = array(
            "NDay" => $dataNDay,
            "NNight" => $request->get('nbrNight'),
            "NPlace" => $request->get('nbrPlace'),
            "PrxAdult" => $request->get('priceAdult'),
            "PrxChild" => $request->get('priceChild'),
            "description" => $request->get('description'),
            "hotel" => $request->get('hotel'),
            "voyage" => $request->get('voyageOrganise'),
            "dateBegin" => $dataBegin,
            "dateEnd" => $dataEnd
        );
        $planning = $planningVoyageManager->showOnePlanningVoyage($id);

        if (!empty($planning)) {
            $result = $planningVoyageManager->updatePlanningVoyage($planning, $data);
        }

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Planning à bien été modifié !',
            'result' => $result
        );

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'Modification du Planning échoué !',
            );
        }

        return $this->json($response, $jsonResponse);

    }

    /**
     * @Route("/delete/{id}", requirements={"id"="\d+"},defaults={"id": 0}, methods={"DELETE"}, name="deletePlanning")
     */
    public function delete(PlanningVoyageManager $planningVoyageManager, $id)
    {
        $result = $planningVoyageManager->deletePlanningVoyage($id);

        $jsonResponse = Response::HTTP_OK;
        $message = '';
        if ($result == 1) {
            $message = "Planning à bien été supprimer";
        } elseif ($result == -2) {
            $message = "Suppression impossible, planning n'est pas archiver ou déjà utilisé";
        } elseif($result == -1) {
            $message = 'aucun Planning trouver !';
        }

        $response = array(
            'code' => $result,
            'message' => $message ,
        );
        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/addHotel", methods={"PUT"}, name="addHotelOnPlanning")
     */
    public function addHotel(
        PlanningVoyageManager $planningVoyageManager,
        HotelManager $hotelManager,
        Request $request
    )
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Hotel est affecter avec succès !',
        );

        $dataHotel = $request->get('hotel');
        $dataPlanning = $request->get('planningVoyage');

        $planning = $planningVoyageManager->showOnePlanningVoyage($dataPlanning['id']);
        $hotel = $hotelManager->showOneHotel($dataHotel['id']);

        $result = $planningVoyageManager->addHotel($planning, $hotel);

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'Affectation d\'Hotel échoué',
            );
        }

        return $this->json($response, $jsonResponse);

    }

    /**
     * @Route("/addRating", methods={"POST"}, name="addRatingOnPlanning")
     */
    public function addRating(RatingManager $ratingManager, Request $request)
    {
        $dataVote = $request->get('vote');
        $dataPlinning = $request->get('planningVoyage');
        $result = $ratingManager->addRating($dataPlinning['id'], $dataVote);

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Vote effectuer! ',
        );

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'Affectation de vote échoué! ',
            );
        }

        return $this->json($response, $jsonResponse);

    }

    /**
     * @Route("/addInclusion", methods={"POST"}, name="addInclusionOnPlanning")
     */
    public function addInclusion(
        PlanningVoyageManager $planningVoyageManager,
        InclusionManager $inclusionManager,
        Request $request
    )
    {
        $dataPlinning = $request->get('planning');
        $arrayInclusion = $request->get('inclusions');
        $planning = $planningVoyageManager->showOnePlanningVoyage($dataPlinning['id']);
        $result = false;
        if (!empty($planning)) {

            $result = $planningVoyageManager->removeInclusionOnPlanning($planning);

            if ($result === true) {
                $result = $planningVoyageManager->addInclusionOnPlanning($planning, $arrayInclusion);
            }
        }

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Inclusion est affecter avec succès !',

        );

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'aucun Planning trouver !',
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/removeInclusion", methods={"DELETE"}, name="removeInclusionFromPlanning")
     */
    public function removeInclusion(
        PlanningVoyageManager $planningVoyageManager,
        InclusionManager $inclusionManager,
        Request $request
    )
    {
        $em = $this->getDoctrine()->getManager();
        $dataPlinning = $request->get('data-planning');
        $arrayInclusion = $request->get('data-inclusion');
        $planning = $planningVoyageManager->showOnePlanningVoyage($dataPlinning['id']);
        $result = false;
        if (!empty($planning)) {
            foreach ($arrayInclusion as $dataInclusion) {
                $inclusion = $inclusionManager->showOneInclusion($dataInclusion['id']);
                if (!empty($inclusion)) {
                    $planning->removeInclusion($inclusion);
                }
            }
            $em->flush();
            $result = true;
        }

        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 1,
            'message' => 'Inclusion à bien été supprimer',

        );

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'aucun Planning trouver !',
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/visible/{id}",requirements={"id"="\d+"},defaults={"id": 0}, methods={"POST"}, name="activerPlanning")
     */
    public function setVisiblePlanning(PlanningVoyageManager $planningVoyageManager, $id)
    {
        $planning = $planningVoyageManager->showOnePlanningVoyage($id);
        $result = false;
        $code = -1;
        if (!empty($planning)) {
            $result = $planningVoyageManager->setVisiblePlanning($planning);
            if ($planning->getVisible()) {
                $code = 1;
                $message = "Planning est archiver";
            } else {
                $code = -2;
                $message = "Planning est désarchiver";
            }
        }
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => $code,
            'message' => $message,
        );

        if ($result == false) {

            $response = array(
                'code' => -1,
                'message' => 'aucun Planning trouver !',
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/activer/{id}",requirements={"id"="\d+"},defaults={"id": 0}, methods={"POST"}, name="visiblePlanning")
     */
    public function setIsActiverPlanning(PlanningVoyageManager $planningVoyageManager, $id)
    {
        $planning = $planningVoyageManager->showOnePlanningVoyage($id);
        $result = false;
        $code = -1;
        if (!empty($planning)) {
            $result = $planningVoyageManager->setIsActiverPlanning($planning);
            if ($planning->getIsActiver()) {
                $code = 1;
                $message = "le Planning est visible dans partie Client";
            } else {
                $code = 2;
                $message = "le Planning n'est pas visible dans partie Client";
            }
        }
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => $code,
            'message' => $message,
        );

        if ($result == false) {

            $response = array(
                'code' => -1,
                'message' => 'aucun Planning trouver !',
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/byVisible/{vsbl}",requirements={"vsbl"="\d+"},defaults={"vsbl": 0}, methods={"GET"},  name="byVisiblePlanning")
     */
    public function byVisible(PlanningVoyageManager $planningVoyageManager, $vsbl)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Planning trouver !',
        );
        $plannings = $planningVoyageManager->byVisiblePlanningVoyage($vsbl);
        if (!empty($plannings)) {

            //$ratings = $planningVoyageManager->getRatingsPlanningVoyage($planning);

            $response = array(
                'code' => 1,
                'data-planning' => $plannings,
                //'data-rating' => $ratings,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/byDateInterval", methods={"POST"},  name="byDateIntervalPlanning")
     */
    public function byDateInterval(PlanningVoyageManager $planningVoyageManager, Request $request)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Planning trouver !',
        );
        $dtBegin = new \DateTime($request->get('date-debut'));
        $dtEnd = new \DateTime($request->get('date-fin'));

        $plannings = $planningVoyageManager->byDateInterval($dtBegin, $dtEnd);
        if (!empty($plannings)) {
            $response = array(
                'code' => 1,
                'data-planning' => $plannings,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/byReference", methods={"POST"}, name="byReferencePlanning")
     */
    public function byReference(PlanningVoyageManager $planningVoyageManager, Request $request)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Planning trouver !',
        );
        $reference = $request->get('reference');
        $planning = $planningVoyageManager->byReferencePlanningVoyage($reference);
        if (!empty($planning)) {

            $response = array(
                'code' => 1,
                'data-planning' => $planning,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/byPrix", methods={"POST"}, name="byPrixPlanning")
     */
    public function byPrix(PlanningVoyageManager $planningVoyageManager, Request $request)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'aucun Planning trouver !',
        );
        $prxMin = $request->get('prix-min');
        $prxMax = $request->get('prix-max');

        $plannings = $planningVoyageManager->byPrixPlanningVoyage($prxMin, $prxMax);
        if (!empty($plannings)) {

            $response = array(
                'code' => 1,
                'data-planning' => $plannings,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/addPromoToPlanning/{plng}" , methods={"POST"} , name="add_promo_to_planning")
     */
    public function addPromoToPlanning($plng, Request $request, PromoManager $promoManager, PlanningVoyageManager $voyageManager)
    {
        $jsonResponse = Response::HTTP_OK;

        $planning = $voyageManager->showOnePlanningVoyage($plng);
        $dataPromo = $request->get('promo');
        $promo = $promoManager->showOnePromo($dataPromo['id']);
        $result = $voyageManager->addPromoToPlanning($planning, $promo);
        if ($result == -1) {
            $response = array(
                'code' => -1,
                'message' => 'aucun Planning ou Promotion trouver !',
            );
        } elseif ($result == -2) {
            $response = array(
                'code' => -2,
                'message' => 'probléme d\'intersection entre date promotion et planning!',
            );
        } else {
            $response = array(
                'code' => 1,
                'message' => 'Promotion et affecter au planning avec succès !',
            );
        }
        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/removePromoFromoPlanning/{id}" , methods={"DELETE"} , name="delete_promo_to_planning")
     */
    public function deletePromoFromPlanning($id, PlanningVoyageManager $voyageManager)
    {
        $jsonResponse = Response::HTTP_OK;

        $planning = $voyageManager->showOnePlanningVoyage($id);
        $result = $voyageManager->deletePromoFromPlanning($planning);

        if ($result == false) {
            $response = array(
                'code' => -1,
                'message' => 'aucune promotion n\'est supprimer!',
            );
        } else {
            $response = array(
                'code' => 1,
                'message' => 'Promo à bien été supprimer',
            );
        }
        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/pv",  methods={"GET"}, name="showPlanningwithVoyage")
     */
    public function showWithVoyage(PlanningVoyageManager $planningVoyageManager)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 0,
            'message' => 'aucun Planning trouver !',
        );

        $planning = $planningVoyageManager->listPlanningVoyage();

        if (!empty($planning)) {

            $response = array(
                'code' => 1,
                'data' => $planning
            );
        }

        return $this->json($response, $jsonResponse);
    }
    /**
     * @Route("/pv/{id}",  methods={"GET"}, name="showOnePlanningwithVoyage")
     */
    public function showOneWithVoyage(PlanningVoyageManager $planningVoyageManager,$id)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => 0,
            'message' => 'aucun Planning trouver !',
        );

        $planning = $planningVoyageManager->showOnePlanningVoyage($id);

        if (!empty($planning)) {

            $response = array(
                'code' => 1,
                'data' => $planning
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/allPlanningByPromo", methods={"POST"},  name="all_promo_by_Planning")
     */
    public function findPlanningByDatePromo(PlanningVoyageManager $planningVoyageManager , Request $request)
    {
        $jsonResponse = Response::HTTP_OK;
        $response = array(
            'code' => -1,
            'message' => 'No Planning Found !',
        );
        $dtBegin = new \DateTime($request->get('dateBegin'));
        $dtEnd = new \DateTime($request->get('dateEnd'));
        $visible = $request->get('visible');
        $planning = $planningVoyageManager->listPlanningByDateInterval($dtBegin,$dtEnd,$visible);
        if (!empty($planning)) {
            $response = array(
                'code' => 1,
                'data' => $planning,
            );
        }

        return $this->json($response, $jsonResponse);
    }

}
