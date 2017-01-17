<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadFormation.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\Etudiant;



class LoadProjet implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $projet = new Projet();
        $projet->setIntituleProjet("Faire la vaisselle");
        $projet->setDescripProjet("Frotter les assiettes");
        $projet->setBilanProjet("Tout est propre");
        $projet->setClientProjet("Soukaïna El Abdellaoui");
        $projet->setMarquantProjet(true);
        $projet->setMotsClesProjet(array("vaisselle","eau","laver"));
        $projet->setValiderProjet(true);
        $projet->addEtudiant($manager->getRepository(Etudiant::class)->findByNomEtu("Zerguini"));
        $projet->addEtudiant($manager->getRepository(Etudiant::class)->findByNomEtu("Dubernet"));

        $manager->persist($projet);
        $manager->flush();
    }
}