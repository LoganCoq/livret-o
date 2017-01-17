<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadDepartement.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\Departement;

class LoadDepartement implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $names = array(
            'Informatique',
            'GEA',
            'GMP',
            'GTE',
            'Chimie',
            'QLIO'
        );

        foreach ($names as $name){
            $departement = new Departement();
            $departement->setNomDpt($name);
            $manager->persist($departement);
        }

        $manager->flush();
    }
}