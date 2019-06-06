<?php


namespace App\Service\impl;


use App\Entity\Hotel;
use App\Entity\Inclusion;
use App\Entity\PlanningVoyage;
use App\Entity\Promo;
use App\Entity\Rating;
use App\Entity\VoyageOrganise;
use App\Service\Functions;
use App\Service\PlanningVoyageManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PlanningVoyageManagerImpl implements PlanningVoyageManager
{
    private $ratingRepository;
    private $hotelRepository;
    private $voyageRepository;
    private $promoRepository;
    private $inclusionRepository;
    private $planningRepository;
    private $ratingManager;
    private $planningManager;
    private $promoManager;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->ratingRepository = $doctrine->getRepository(Rating::class);
        $this->hotelRepository = $doctrine->getRepository(Hotel::class);
        $this->voyageRepository = $doctrine->getRepository(VoyageOrganise::class);
        $this->promoRepository = $doctrine->getRepository(Promo::class);
        $this->inclusionRepository = $doctrine->getRepository(Inclusion::class);
        $this->planningRepository = $doctrine->getRepository(PlanningVoyage::class);
        $this->ratingManager = $doctrine->getManager();
        $this->planningManager = $doctrine->getManager();
        $this->promoManager = $doctrine->getManager();
    }

    public function listPlanningVoyage()
    {
        return $this->planningRepository->findAll();
    }

    public function showOnePlanningVoyage($id)
    {
        return $this->planningRepository->find($id);
    }

    public function byVisiblePlanningVoyage($vsbl)
    {
        return $this->planningRepository->findBy(['visible' => $vsbl]);
    }

    public function byReferencePlanningVoyage($reference)
    {
        return $this->planningRepository->findBy(['reference' => $reference]);
    }

    public function byPrixPlanningVoyage($prxMin, $prxMax)
    {
        return $this->planningRepository->findByPrix($prxMin, $prxMax);
    }

    public function getRatingsPlanningVoyage(PlanningVoyage $planning)
    {
        return $this->ratingRepository->findBy(['planningVoyage' => $planning]);
    }

    public function addPlanningVoyage(array $data)
    {
        $dataVoyage = $data['voyage'];
        $dataHotel = $data['hotel'];
        $planning = new PlanningVoyage();
        $voyage = $this->voyageRepository->find($dataVoyage['id']);
        $hotel = $this->hotelRepository->find($dataHotel['id']);
        if (isset($data['promo'])) {
            $dataPromo = $data['promo'];
            $promo = $this->promoRepository->find($dataPromo['id']);
        }
        $dataReference = strtoupper(uniqid("AVG"));
        $dataVisible = true;
        $dataIsActive = false;

        if (!empty($planning) AND !empty($hotel) AND !empty($voyage)) {

            if (!empty($promo)) {
                $planning->setPromo($promo);
            }

            $planning->setVoyageOrganise($voyage);
            $planning->setHotel($hotel);

            $planning->setReference($dataReference);
            $planning->setNbrDays($data['NDay']);
            $planning->setNbrNight($data['NNight']);
            $planning->setNbrPlace($data['NPlace']);
            $planning->setDescription($data['description']);
            $planning->setPriceAdult($data['PrxAdult']);
            $planning->setPriceChild($data['PrxChild']);
            $planning->setVisible($dataVisible);
            $planning->setIsActiver($dataIsActive);
            $planning->setDateBegin($data['dateBegin']);
            $planning->setDateEnd($data['dateEnd']);

            $this->planningManager->persist($planning);
            $this->planningManager->flush();

            return $planning;
        } else {
            return false;
        }
    }

    public function updatePlanningVoyage(PlanningVoyage $planning, array $data)
    {
        $dataVoyage = $data['voyage'];
        $dataHotel = $data['hotel'];
        $voyage = $this->voyageRepository->find($dataVoyage['id']);
        $hotel = $this->hotelRepository->find($dataHotel['id']);
        if (isset($data['promo'])) {
            $dataPromo = $data['promo'];
            $promo = $this->promoRepository->find($dataPromo['id']);
        }

        if (!empty($planning) AND !empty($hotel) AND !empty($voyage)) {

            if (!empty($promo)) {
                $planning->setPromo($promo);
            }

            $planning->setVoyageOrganise($voyage);
            $planning->setHotel($hotel);
            $planning->setNbrDays($data['NDay']);
            $planning->setNbrNight($data['NNight']);
            $planning->setNbrPlace($data['NPlace']);
            $planning->setPriceAdult($data['PrxAdult']);
            $planning->setPriceChild($data['PrxChild']);
            $planning->setDescription($data['description']);
            $planning->setDateBegin($data['dateBegin']);
            $planning->setDateEnd($data['dateEnd']);

            $this->planningManager->flush();

            return $planning;
        } else {
            return false;
        }
    }

        public function deletePlanningVoyage($id)
    {
        $planning = $this->showOnePlanningVoyage($id);

        if (!empty($planning)) {
            if ($planning->getVisible() === false){
                $ratings = $this->ratingRepository->findBy(['planningVoyage' => $planning->getId()]);
                if(!empty($ratings)){
                    foreach($ratings as $rating){
                        $this->ratingManager->remove($rating);
                    }
                }
                $this->planningManager->remove($planning);
                $this->planningManager->flush();
                return 1;
            }
            return -2;
        } else {
            return -1;
        }
    }

    public function addPromoToPlanning(PlanningVoyage $planningVoyage, Promo $data): int
    {
        $dataUsing = true;
        $promo = $this->promoRepository->find($data->getId());
        if (!empty($planningVoyage) && !empty($data)) {
            if ($data->getVisible() == true) {
                if ($data->getTypePromo() == 'stable') {
                    if ($planningVoyage->getDateBegin() > $data->getDateBegin() || $planningVoyage->getDateEnd() < $data->getDateEnd()) {
                        $planningVoyage->setPromo($data);
                        $promo->setUsingEtat($dataUsing);
                        $this->promoManager->flush();
                        $this->planningManager->flush();
                        return 1;
                    } else {
                        return -2;
                    }
                } else {
                    $planningVoyage->setPromo($data);
                    $this->planningManager->flush();
                    return 1;
                }
            }
        }
        return -1;
    }


    public function deletePromoFromPlanning(PlanningVoyage $planningVoyage)
    {
        if (empty($planningVoyage)) {
            return false;
        } else {
            $planningVoyage->setPromo(null);
            $promo = $this->promoRepository->find($planningVoyage->getPromo()->getId());
            $promo->setUsingEtat(false);
            $this->planningManager->flush();
            $this->promoManager->flush();
            return true;
        }
    }

    public function addHotel(PlanningVoyage $planning, Hotel $hotel)
    {
        if (!empty($planning) AND !empty($hotel)) {
            $planning->setHotel($hotel);
            $this->planningManager->flush();
            return true;
        } else {
            return false;
        }

    }

    public function removeInclusionOnPlanning(PlanningVoyage $planning)
    {

        if (!empty($planning)) {
            $arrayInclusion = $planning->getInclusion();
            foreach ($arrayInclusion as $inclusion) {
                $planning->removeInclusion($inclusion);
            }
            $this->planningManager->flush();
            return true;
        } else {
            return false;
        }
    }

    public function addInclusionOnPlanning(PlanningVoyage $planning, array $arrayInclusion)
    {
        if (!empty($planning)) {
            foreach ($arrayInclusion as $dataInclusion) {
                $inclusion = $this->inclusionRepository->find($dataInclusion['id']);
                if (!empty($inclusion)) {
                    $planning->addInclusion($inclusion);
                }
            }
            $this->planningManager->flush();
            return true;
        } else {
            return false;
        }
    }

    public function setVisiblePlanning(PlanningVoyage $planning)
    {
        if (!empty($planning)) {

            if ($planning->getVisible()) {
                $planning->setVisible(false);
                $planning->setIsActiver(false);
            } else {
                $planning->setVisible(true);
            }
            $this->planningManager->flush();
            return true;
        } else {
            return false;
        }
    }

    public function setIsActiverPlanning(PlanningVoyage $planning)
    {
        if (!empty($planning)) {

            if ($planning->getIsActiver()) {
                $planning->setIsActiver(false);
            } else {
                $planning->setIsActiver(true);
            }
            $this->planningManager->flush();
            return true;
        } else {
            return false;
        }
    }

    public function byDateInterval(\DateTime $dtBegin, \DateTime $dtEnd)
    {
        return $this->planningRepository->findByDateInterval($dtBegin, $dtEnd);
    }
    public function listPlanningByDateInterval(\DateTime $dtBegin , \DateTime $dtEnd , $visible)
    {
        return $this->planningRepository->findByDateIntervalPlanning($dtBegin,$dtEnd,$visible);
    }
}
