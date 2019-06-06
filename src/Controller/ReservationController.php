<?php

namespace App\Controller;

use App\Entity\PlanningVoyage;
use App\Entity\Reservation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/add/{id}", name="reservation_add")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function add(Request $request,$id)
    {
        $reservation = new Reservation();
        $reservation->setReference(uniqid("REF-"));
        $reservation->setDate(new \DateTime());
        $reservation->setNbrAdult($request->get('nbrAdult'));
        $reservation->setNbrchild($request->get('nbrchild'));
        $planningVoyage= $this->getDoctrine()->getRepository(PlanningVoyage::class)
            ->find($id);
        $reservation->setPlanningVoyage($planningVoyage);


       $prixAdult= $planningVoyage->getPriceAdult();
       $prixChild= $planningVoyage->getPriceAdult();
       $prixTotal = ($request->get('nbrAdult')*$prixAdult) + ($request->get('nbrchild')*$prixChild);

       $reservation->setPrixTotal($prixTotal);
       $em= $this->getDoctrine()->getManager();
       $em-> persist($reservation);
       $em-> flush();
       $idvoyage= $reservation->getId();

        $response=array(
            'code'=>0,
            'id' => $idvoyage,
            'total' => $prixTotal,
            'message'=>'reservation created!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }

    /**
     * @Route("/", name="allReservation")
     * @Method({"GET"})
     */
    public function list()
    {
        $repository= $this->getDoctrine()->getRepository(Reservation::class);
        $items= $repository->findAll();

        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucune Reservation Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $items
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"}, name="oneReservation")
     * @Method({"GET"})
     */
    public function listOne($id)
    {
        $repository= $this->getDoctrine()->getRepository(Reservation::class);
        $items= $repository->find($id);

        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucune Reservation Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $items
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/valide/{id}", name="reservation_valide")
     * @Method({"PUT"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function valide(Request $request,$id)
    {
        $reservation= $this->getDoctrine()->getRepository(Reservation::class)->find($id);

        $reservation->setEtat('Valider');

        $em= $this->getDoctrine()->getManager();
        $em-> flush();


        $response=array(
            'code'=>0,
            'message'=>'reservation Valider!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }
}
