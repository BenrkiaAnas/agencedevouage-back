<?php


namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RatingRepository")
 */
class Rating
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=2)
     */
    private $vote;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanningVoyage",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planningVoyage;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVote(): ?float
    {
        return $this->vote;
    }

    public function setVote(float $vote): void
    {
        $this->vote = $vote;
    }

    public function getPlanningVoyage(): ?PlanningVoyage
    {
        return $this->planningVoyage;
    }

    public function setPlanningVoyage(PlanningVoyage $planningVoyage): self
    {
        $this->planningVoyage = $planningVoyage;

        return $this;
    }




}
