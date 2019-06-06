<?php


namespace App\Service\impl;


use App\Entity\Hotel;
use App\Entity\PlanningVoyage;
use App\Service\HotelManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

class HotelManagerImpl implements HotelManager
{
    private $hotelManager;
    private $hotelRepository;
    private $planningRepository;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->hotelRepository = $doctrine->getRepository(Hotel::class);
        $this->planningRepository = $doctrine->getRepository(PlanningVoyage::class);
        $this->hotelManager = $doctrine->getManager();
    }

    public function listHotel()
    {
        return $this->hotelRepository->findAll();
    }

    public function showOneHotel($id)
    {
        return $this->hotelRepository->find($id);
    }

    public function addHotel($hotel)
    {
        $newHotel = new Hotel();

        if (!empty($hotel)) {
            $newHotel->setName($hotel['name']);
            $newHotel->setRating($hotel['rating']);
            if (!$this->existHotel($newHotel->getName())) {
                $this->hotelManager->persist($newHotel);
                $this->hotelManager->flush();
                return 1;
            }
            return -1;

        } else {
            return -2;
        }
    }

    public function updateHotel(Hotel $hotel, Hotel $data)
    {

        if (!empty($hotel) AND !empty($data)) {
            if ($hotel->getName() === $data->getName()) {
                $hotel->setRating($data->getRating());
                $this->hotelManager->flush();
                return 1;
            } elseif (!$this->existHotel($data->getName())) {
                $hotel->setName($data->getName());
                $hotel->setRating($data->getRating());
                $this->hotelManager->flush();
                return 1;
            }
            return -1;

        } else {
            return -2;
        }
    }

    public function deleteHotel($id)
    {
        $hotel = $this->showOneHotel($id);

        if (!empty($hotel)) {
            $plannings = $this->planningRepository->findBy(['hotel' => $hotel->getId()]);
            if (empty($plannings)) {
                $this->hotelManager->remove($hotel);
                $this->hotelManager->flush();
                return 1;
            }
            return -2;
        } else {
            return -1;
        }
    }

    public function existHotel($name)
    {
        $hotel = $this->hotelRepository->findBy(['name' => $name]);
        if (!empty($hotel)) {
            return true;
        } else {
            return false;
        }
    }
}
