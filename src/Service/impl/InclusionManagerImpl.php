<?php


namespace App\Service\impl;


use App\Entity\Inclusion;
use App\Entity\PlanningVoyage;
use App\Service\InclusionManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

class InclusionManagerImpl implements InclusionManager
{
    private $inclusionManager;
    private $inclusionRepository;
    private $planningRepository;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->inclusionRepository = $doctrine->getRepository(Inclusion::class);
        $this->planningRepository = $doctrine->getRepository(PlanningVoyage::class);
        $this->inclusionManager = $doctrine->getManager();
    }

    public function listInclusion()
    {
        return $this->inclusionRepository->findAll();
    }

    public function showOneInclusion($id)
    {
        return $this->inclusionRepository->find($id);
    }

    public function addInclusion(Inclusion $inclusion)
    {
        if (!empty($inclusion)) {
            if (!$this->existInclusion($inclusion->getLabel())) {
                $this->inclusionManager->persist($inclusion);
                $this->inclusionManager->flush();
                return 1;
            }
            return -1;

        } else {
            return -2;
        }
    }

    public function updateInclusion(Inclusion $inclusion, Inclusion $data)
    {
        if (!empty($inclusion) AND !empty($data)) {
            if ($inclusion->getLabel() === $data->getLabel()){
                $inclusion->setIcon($data->getIcon());
                $this->inclusionManager->flush();
                return 1;
            }elseif (!$this->existInclusion($data->getLabel())) {
                $inclusion->setLabel($data->getLabel());
                $inclusion->setIcon($data->getIcon());
                $this->inclusionManager->flush();
                return 1;
            }
            return -1;
        } else {
            return -2;
        }
    }

    public function deleteInclusion($id)
    {
        $inclusion = $this->showOneInclusion($id);

        if (!empty($inclusion)) {
            $plannings = $this->planningRepository->findAllPlanningUsingInclusion($inclusion);
            if (empty($plannings)) {
                $this->inclusionManager->remove($inclusion);
                $this->inclusionManager->flush();
                return 1;
            }
            return -2;
        } else {
            return -1;
        }
    }

    public function existInclusion($name)
    {
        $inclusion = $this->inclusionRepository->findBy(['label' => $name]);
        if (!empty($inclusion)) {
            return true;
        } else {
            return false;
        }
    }
}
