<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadLivret.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\Livret;
use IUTO\LivretBundle\Entity\Projet;

class LoadLivret implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $livret = new Livret();
        $livret->setIntituleLivret("Projet département Informatique");
        $livret->setDateCreationLivret(new \DateTime());
        $livret->setEditoLivret("Le département informatique ils sont au dessus.");

        $projet = $manager->getRepository(Projet::class)->findOneByIntituleProjet("Faire la vaisselle");

        if($projet)
            $livret->addProjet($projet);

        $manager->persist($livret);
        $manager->flush();
    }
}