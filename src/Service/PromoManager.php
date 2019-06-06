<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 26/04/2019
 * Time: 10:23
 */

namespace App\Service;


use App\Entity\PlanningVoyage;
use App\Entity\Promo;

interface PromoManager
{
    public function addPromo(string $label ,float $pourcentage,\DateTime $dtBegin,\DateTime $dtEnd , string $typePromo , int $nbrPersonne);
    public function listPromos();
    public function showOnePromo($idPromo);
    public function updatePromo(int $idPromo ,array $data);
    public function deletePromo($id);
    public function listPromoByDateInterval(\DateTime $dtBegin, \DateTime $dtEnd, $visible);
    public function addPromoToMultiplePlanning(Promo $promo , array $arrayPlanning);
    public function checkUsingPromoForPlanning($id);
    public function verifierDateExpirationPromo();
}


