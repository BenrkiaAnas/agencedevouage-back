<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrAdult;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrchild;

    /**
     * @ORM\Column(type="float")
     */
    private $prixTotal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanningVoyage", fetch="EAGER")
     */
    private $planningVoyage;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $etat = "En cour";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNbrAdult(): ?int
    {
        return $this->nbrAdult;
    }

    public function setNbrAdult(int $nbrAdult): self
    {
        $this->nbrAdult = $nbrAdult;

        return $this;
    }

    public function getNbrchild(): ?int
    {
        return $this->nbrchild;
    }

    public function setNbrchild(int $nbrchild): self
    {
        $this->nbrchild = $nbrchild;

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(float $prixTotal): self
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getPlanningVoyage(): ?PlanningVoyage
    {
        return $this->planningVoyage;
    }

    public function setPlanningVoyage(?PlanningVoyage $planningVoyage): self
    {
        $this->planningVoyage = $planningVoyage;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
