<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadFonction.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\Fonction;

class LoadFonction implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $names = array(
            'Enseignant',
            'Chef de departement',
            'Service communication'
        );

        foreach ($names as $name){
            $fonction = new Fonction();
            $fonction->setNomFonction($name);
            $manager->persist($fonction);
        }

        $manager->flush();
    }
}
