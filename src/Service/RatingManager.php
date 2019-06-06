<?php


namespace App\Service;


use App\Entity\Rating;

interface  RatingManager
{
    public function listRating();
    public function showOneRating($id);
    public function addRating(int $idPlanning, float $vote);
    public function updateRating(int $idRating,int $idPlanning, float $vote);
    public function deleteRating($id);
}
