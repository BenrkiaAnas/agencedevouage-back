<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotosRepository")
 */
class Photos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Image()
     */
    private $image;

    public function __construct()
    {
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }


//    /**
//     * @return mixed
//     */
//    public function getVoyages()
//    {
//        return $this->voyages;
//    }
//
//    /**
//     * @param mixed $voyages
//     */
//    public function setVoyages($voyages): void
//    {
//        $this->voyages = $voyages;
//    }

//    public function setImageFile(File $image = null)
//    {
//        $this->imageFile = $image;
//
//        if ($image) {
//            $this->updatedAt = new \DateTime('now');
//
//    }
//
//    public function getImageFile()
//    {
//        return $this->imageFile;
//    }


}
