<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadProjet.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadProjet implements FixtureInterface, DependentFixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
//        $projet = new Projet();
//        $projet->setIntituleProjet("Faire la vaisselle");
//        $projet->setDescripProjet("Frotter les assiettes");
//        $projet->setBilanProjet("Tout est propre");
//        $projet->setClientProjet("Soukaïna El Abdellaoui");
//        $projet->setMarquantProjet(true);
//        $projet->setMotsClesProjet(array("vaisselle","eau","laver"));
//        $projet->setValiderProjet(true);
//        $projet->setDateDebut(new \DateTime(date('Y').'01-01'));
//        $projet->setDateFin(new \DateTime(date('Y').'12-31'));
//
//
//        $etu1 = $manager->getRepository(User::class)->findOneByNomUser("Zerguini");
//        $etu2 = $manager->getRepository(User::class)->findOneByNomUser("Dubernet");
//        if ($etu1)
//            $projet->addEtudiant($etu1);
//            $etu1->addProjetFait($projet);
//        if ($etu2)
//            $projet->addEtudiant($etu2);
//            $etu2->addProjetFait($projet);
//
//        $pers1 = $manager->getRepository(User::class)->findOneByMailUser("sebastien.limet@univ-orleans.fr");
//        $pers2 = $manager->getRepository(User::class)->findOneByMailUser("guillaume.cleuziou@univ-orleans.fr");
//
//        $projet->addTuteur($pers1);
//        $pers1->addProjetSuivi($projet);
//
//        $projet->addTuteur($pers2);
//        $pers2->addProjetSuivi($projet);
//
//
//        $manager->persist($projet);
//        $manager->flush();
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
