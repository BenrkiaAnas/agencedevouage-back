<?php


namespace App\Service;


use App\Entity\Inclusion;

interface InclusionManager
{
    public function listInclusion();

    public function showOneInclusion($id);

    public function addInclusion(Inclusion $inclusion);

    public function updateInclusion(Inclusion $inclusion, Inclusion $data);

    public function deleteInclusion($id);

    public function existInclusion($name);
}
