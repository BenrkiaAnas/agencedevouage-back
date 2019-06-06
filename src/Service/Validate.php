<?php
/**
 * Created by PhpStorm.
 * User: wijdanechakir
 * Date: 01/04/2019
 * Time: 09:46
 */

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class Validate
{

    public function generateReference()
    {
        $reference = \DateTime;
        return  $reference;
    }
}