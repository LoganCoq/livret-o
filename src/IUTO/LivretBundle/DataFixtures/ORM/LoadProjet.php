<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadProjet.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Entity\Projet;



class LoadProjet implements FixtureInterface, DependentFixtureInterface
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
        $projet->setDateDebut(new \DateTime(date('Y').'01-01'));
        $projet->setDateFin(new \DateTime(date('Y').'12-31'));


        $etu1 = $manager->getRepository(Etudiant::class)->findOneByNomEtu("Zerguini");
        $etu2 = $manager->getRepository(Etudiant::class)->findOneByNomEtu("Dubernet");
        if ($etu1)
            $projet->addEtudiant($etu1);
        if ($etu2)
            $projet->addEtudiant($etu2);

        $pers1 = $manager->getRepository(Personnel::class)->findOneByMailPers("sebastien.limet@univ-orleans.fr");
        $pers2 = $manager->getRepository(Personnel::class)->findOneByMailPers("toto.titi@univ-orleans.fr");

        $projet->addPersonnel($pers1);
        $projet->addPersonnel($pers2);


        $manager->persist($projet);
        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return array(LoadUser::class);
    }
}
