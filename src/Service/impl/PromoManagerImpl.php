<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 26/04/2019
 * Time: 10:24
 */
namespace App\Service\impl;


use App\Entity\PlanningVoyage;
use App\Entity\Promo;
use App\Service\PromoManager;
use Symfony\Bridge\Doctrine\RegistryInterface;


class PromoManagerImpl implements PromoManager
{

    private $promoManager;
    private $promoRepository;
    private $planningRepository;
    private $planningManager;

    /**
     * PromoManagerImpl constructor.
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->promoManager = $doctrine->getManager();
        $this->promoRepository = $doctrine->getRepository(Promo::class);
        $this->planningRepository = $doctrine->getRepository(PlanningVoyage::class);
        $this->planningManager = $doctrine->getManager();
    }
    
    public function addPromo(
        string $label,
        float $pourcentage,
        \DateTime $dtBegin,
        \DateTime $dtEnd,
        string $typePromo,
        int $nbrPersonne
    ) : int
    {
        $promo = new Promo();
        if(empty($promo))
        {
            return -1;
        }else{

            $dataVisible = false;
            $dataUsing = false;
            $promo->setLabel($label);
            $promo->setDateBegin($dtBegin);
            $promo->setDateEnd($dtEnd);
            $promo->setVisible($dataVisible);
            $promo->setUsingEtat($dataUsing);

            if($typePromo === "stable")
            {
                $promo->setPourcentage($pourcentage);
                $promo->setTypePromo($typePromo);
                $promo->setNbrPersonne($nbrPersonne);

            }else{
                $promo->setTypePromo($typePromo);
                $promo->setNbrPersonne($nbrPersonne);
                $promo->setPourcentage($pourcentage);
            }
            //$planning->setPromo($promo);
            $this->promoManager->persist($promo);
            $this->promoManager->flush();
            return  1;
        }
    }
    
    public function listPromos()
    {
        return $this->promoRepository->findAll();
    }

    public function listPromoByDateInterval(\DateTime $dtBegin, \DateTime $dtEnd, $visible)
    {
        return $this->promoRepository->findByDateInterval($dtBegin, $dtEnd, $visible);
    }

    public function showOnePromo($idPromo)
    {
        return $this->promoRepository->find($idPromo);
    }

    public function deletePromo($id)
    {
        $promo = $this->showOnePromo($id);

        if (!empty($promo)) {

            $this->promoManager->remove($promo);
            $this->promoManager->flush();

            return true;
        } else {
            return false;
        }
    }
    
    public function updatePromo(int $idPromo ,array $data)
    {

        $promo = $this->showOnePromo($idPromo);
        $pourcentage = $data['pourcentage'];
        $dtBegin = $data['DtBegin'];
        $dtEnd = $data['DtEnd'];
        $typePromo = $data['typePromotion'];
        $nbrPersonne = $data['nbrPersonn'];
        $label = $data['label'];
        if (!empty($promo)) {

            $promo->setPourcentage($pourcentage);
            $promo->setDateBegin($dtBegin);
            $promo->setDateEnd($dtEnd);
            $promo->setTypePromo($typePromo);
            $promo->setNbrPersonne($nbrPersonne);
            $promo->setLabel($label);
            $this->promoManager->flush();

            return true;
        } else {
            return false;
        }
    }
    public function addPromoToMultiplePlanning(Promo $promo, array $arrayPlanning)
    {
        if (!empty($promo))
        {
            foreach ($arrayPlanning as $dataPlanning)
            {
                $planning = $this->planningRepository->find($dataPlanning['id']);
                if(!empty($planning))
                {
                    $planning->setPromo($promo);
                    $promo->setUsingEtat(true);
                }
            }
            $this->planningManager->flush();
            return true;
        }
        else{
            return false;
        }
    }
    public function checkUsingPromoForPlanning($id)
    {
        $promo = $this->promoRepository->find($id);
        if(!empty($promo))
        {
            if($promo->getUsingEtat())
            {
                return true;
            }else{
                return false;
            }
        }
    }
    public function verifierDateExpirationPromo()
    {
        $dateToday = new \DateTime();
        $promos = $this->promoRepository->findPromoExpire($dateToday);
        $dataVisible = false;
        foreach ($promos as $promo)
        {
                $promo->setVisible($dataVisible);
        }
        $this->promoManager->flush();
        return true;
    }

}
