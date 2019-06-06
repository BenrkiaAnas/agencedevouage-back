<?php

namespace App\DataFixtures;

use App\Entity\PlanningVoyage;
use App\Entity\Rating;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $dtD=  new \DateTime('@'. strtotime('now'));

       // $dtF=  new \DateTime('@'. strtotime('now'));

        $planning = new PlanningVoyage();
        $planning1 = new PlanningVoyage();
        $planning2 = new PlanningVoyage();
        $rating = new Rating();
        $rating1 = new Rating();
        $rating2 = new Rating();

        $planning->setNbrDays(2);
        $planning->setNbrNight(2);
        $planning->setNbrPlace(54);
        $planning->setPriceAdult(850.00);
        $planning->setPriceChild(650.00);
        $planning->setReference("OV-201904010000");
        $planning->setVisible(true);

        $planning1->setNbrDays(5);
        $planning1->setNbrNight(5);
        $planning1->setNbrPlace(17);
        $planning1->setPriceAdult(3200.00);
        $planning1->setPriceChild(3200.00);
        $planning1->setReference("OV-201904010075");
        $planning1->setVisible(true);

        $planning2->setNbrDays(8);
        $planning2->setNbrNight(8);
        $planning2->setNbrPlace(24);
        $planning2->setPriceAdult(17000.00);
        $planning2->setPriceChild(19000.00);
        $planning2->setReference("OV-201904010025");
        $planning2->setVisible(false);

        $rating->setVote(4);
        $rating->setPlanningVoyage($planning);
        $rating1->setVote(4.5);
        $rating1->setPlanningVoyage($planning);
        $rating2->setVote(2);
        $rating2->setPlanningVoyage($planning);

        $manager->persist($planning2);
        $manager->persist($planning1);
        $manager->persist($planning);
        $manager->persist($rating);
        $manager->persist($rating1);
        $manager->persist($rating2);
        $manager->flush();
    }
}
