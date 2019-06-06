<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromoRepository")
 */
class Promo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2 , nullable=true)
     */
    private $pourcentage;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateBegin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typePromo;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrPersonne;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlanningVoyage", mappedBy="promo")
     */
    private $planningVoyage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

    /**
     * @ORM\Column(type="boolean")
     */
    private $usingEtat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    public function __construct()
    {
        $this->planningVoyage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPourcentage()
    {
        return $this->pourcentage;
    }

    public function setPourcentage($pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    public function getDateBegin(): ?\DateTimeInterface
    {
        return $this->dateBegin;
    }

    public function setDateBegin(\DateTimeInterface $dateBegin): self
    {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getTypePromo(): ?string
    {
        return $this->typePromo;
    }

    public function setTypePromo(string $typePromo): self
    {
        $this->typePromo = $typePromo;

        return $this;
    }

    public function getNbrPersonne(): ?int
    {
        return $this->nbrPersonne;
    }

    public function setNbrPersonne(int $nbrPersonne): self
    {
        $this->nbrPersonne = $nbrPersonne;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param mixed $visible
     */
    public function setVisible($visible): void
    {
        $this->visible = $visible;
    }

    public function getUsingEtat(): ?bool
    {
        return $this->usingEtat;
    }

    public function setUsingEtat(bool $usingEtat): self
    {
        $this->usingEtat = $usingEtat;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }


    /**
     * @return Collection|PlanningVoyage[]
     */
   /* public function getPlanningVoyage(): Collection
    {
        return $this->planningVoyage;
    }*/

    /*public function addPlanningVoyage(PlanningVoyage $planningVoyage): self
    {
        if (!$this->planningVoyage->contains($planningVoyage)) {
            $this->planningVoyage[] = $planningVoyage;
            $planningVoyage->setPromo($this);
        }

        return $this;
    }*/

    /*public function removePlanningVoyage(PlanningVoyage $planningVoyage): self
    {
        if ($this->planningVoyage->contains($planningVoyage)) {
            $this->planningVoyage->removeElement($planningVoyage);
            // set the owning side to null (unless already changed)
            if ($planningVoyage->getPromo() === $this) {
                $planningVoyage->setPromo(null);
            }
        }

        return $this;
    }*/


}
