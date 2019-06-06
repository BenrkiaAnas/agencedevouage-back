<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlanningVoyageRepository")
 */
class PlanningVoyage
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
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrDays;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrNight;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrPlace;

    /**
     * @ORM\Column( type="decimal",precision=11, scale=2)
     */
    private $priceAdult;

    /**
     * @ORM\Column( type="decimal",precision=11, scale=2)
     */
    private $priceChild;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateBegin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_activer;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Inclusion")
     */
    private $inclusion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hotel",fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $hotel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\VoyageOrganise",fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $voyageOrganise;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Promo", inversedBy="planningVoyage" ,fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $promo;

    public function __construct()
    {
        $this->inclusion = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getNbrDays(): ?int
    {
        return $this->nbrDays;
    }

    public function getNbrNight(): ?int
    {
        return $this->nbrNight;
    }

    public function getNbrPlace(): ?int
    {
        return $this->nbrPlace;
    }

    public function getPriceAdult(): ?float
    {
        return $this->priceAdult;
    }

    public function getPriceChild(): ?float
    {
        return $this->priceChild;
    }

    public function getDateBegin(): ?\DateTimeInterface
    {
        return $this->dateBegin;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function getIsActiver(): ?bool
    {
        return $this->is_activer;
    }

    /**
     * @param mixed $is_activer
     */
    public function setIsActiver($is_activer): void
    {
        $this->is_activer = $is_activer;
    }


    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateBegin(\DateTimeInterface $dateBegin): self
    {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function setReference(String $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function setNbrDays(int $nbrDays): self
    {
        $this->nbrDays = $nbrDays;

        return $this;
    }

    public function setNbrNight(int $nbrNight): self
    {
        $this->nbrNight = $nbrNight;

        return $this;
    }

    public function setNbrPlace(int $nbrPlace): self
    {
        $this->nbrPlace = $nbrPlace;

        return $this;
    }

    public function setPriceAdult(float $priceAdult): self
    {
        $this->priceAdult = $priceAdult;

        return $this;
    }

    public function setPriceChild(float $priceChild): self
    {
        $this->priceChild = $priceChild;

        return $this;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getInclusion(): Collection
    {
        return $this->inclusion;
    }

    public function addInclusion(Inclusion $inclusion): self
    {
        if (!$this->inclusion->contains($inclusion)) {
            $this->inclusion[] = $inclusion;
        }

        return $this;
    }

    public function removeInclusion(Inclusion $inclusion): self
    {
        if ($this->inclusion->contains($inclusion)) {
            $this->inclusion->removeElement($inclusion);
        }

        return $this;
    }

    public function getHotel(): ?Hotel
    {
        return $this->hotel;
    }

    public function setHotel(?Hotel $hotel): self
    {
        $this->hotel = $hotel;

        return $this;
    }

    public function getPromo(): ?Promo
    {
        return $this->promo;
    }

    public function setPromo(?Promo $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    public function getVoyageOrganise(): ?VoyageOrganise
    {
        return $this->voyageOrganise;
    }

    public function setVoyageOrganise(?VoyageOrganise $voyageOrganise): self
    {
        $this->voyageOrganise = $voyageOrganise;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }


}
