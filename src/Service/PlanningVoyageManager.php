<?php


namespace App\Service;


use App\Entity\Hotel;
use App\Entity\PlanningVoyage;
use App\Entity\Promo;

interface PlanningVoyageManager
{

    public function listPlanningVoyage();
    public function showOnePlanningVoyage($id);
    public function byVisiblePlanningVoyage($vsbl);
    public function byReferencePlanningVoyage($reference);
    public function byPrixPlanningVoyage($prxMin, $prxMax);
    public function getRatingsPlanningVoyage(PlanningVoyage $planning);
    public function addPlanningVoyage(array $data);
    public function updatePlanningVoyage(PlanningVoyage $planning, array $data);
    public function deletePlanningVoyage($id);
    public function addPromoToPlanning(PlanningVoyage $planningVoyage , Promo $data);
    public function deletePromoFromPlanning(PlanningVoyage $planningVoyage);
    public function addHotel(PlanningVoyage $planning, Hotel $hotel);
    public function removeInclusionOnPlanning(PlanningVoyage $planning);
    public function addInclusionOnPlanning(PlanningVoyage $planning, array $arrayInclusion);
    public function setVisiblePlanning(PlanningVoyage $planning);
    public function setIsActiverPlanning(PlanningVoyage $planning);
    public function byDateInterval(\DateTime $dtBegin, \DateTime $dtEnd);
    public function listPlanningByDateInterval(\DateTime $dtBegin , \DateTime $dtEnd , $visible);
}
