<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {   
        
        $envoi = new Type();
        $envoi->setLibelle("retrait");
        $manager->persist($envoi);

        $retrait = new Type();
        $retrait->setLibelle("envoi");
        
        $manager->persist($retrait);

        $manager->flush();
    }
}