<?php


namespace App\Service;


use App\Entity\Hotel;

interface HotelManager
{

    public function listHotel();

    public function showOneHotel($id);

    public function addHotel($hotel);

    public function updateHotel(Hotel $hotel, Hotel $data);

    public function deleteHotel($id);

    public function existHotel($name);
}
