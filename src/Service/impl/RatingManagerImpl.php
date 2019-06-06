<?php


namespace App\Service\impl;


use App\Entity\PlanningVoyage;
use App\Entity\Rating;
use App\Service\RatingManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RatingManagerImpl implements RatingManager
{
    private $ratingManager;
    private $ratingRepository;
    private $planningRepository;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->planningRepository = $doctrine->getRepository(PlanningVoyage::class);
        $this->ratingRepository = $doctrine->getRepository(Rating::class);
        $this->ratingManager = $doctrine->getManager();
    }

    public function listRating()
    {
        return $this->ratingRepository->findAll();
        //return $this->ratingRepository->findAllRatings();
    }

    public function showOneRating($id)
    {
        return $this->ratingRepository->find($id);
    }

    public function addRating(int $idPlanning, float $vote)
    {
        $rating = new Rating();

        $planning = $this->planningRepository->find($idPlanning);

        if (!empty($planning)) {
            $rating->setVote($vote);
            $rating->setPlanningVoyage($planning);
            $this->ratingManager->persist($rating);
            $this->ratingManager->flush();

            return true;
        } else {
            return false;
        }
    }

    public function updateRating(int $idRating,int $idPlanning, float $vote)
    {
        $rating = $this->showOneRating($idRating);
        $planning = $this->planningRepository->find($idPlanning);

        if (!empty($rating) AND !empty($planning)) {
            $rating->setVote($vote);
            $rating->setPlanningVoyage($planning);
            $this->ratingManager->flush();

            return true;
        } else {
            return false;
        }
    }

    public function deleteRating($id)
    {
        $rating = $this->showOneRating($id);
        if (!empty($rating)) {
            $this->ratingManager->remove($rating);
            $this->ratingManager->flush();

            return true;
        } else {
            return false;
        }
    }
}
