<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadFormation.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\Departement;
use IUTO\LivretBundle\Entity\Formation;
use IUTO\LivretBundle\Entity\Etudiant;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadFormation implements FixtureInterface,DependentFixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $formation = new Formation();
        $formation->setTypeFormation("1A");
        $formation->setSemestre(1);
        $formation->setDepartement($manager->getRepository(Departement::class)->findOneByNomDpt("Informatique"));
        $dateDebut = new \DateTime();
        $dateFin = new \DateTime();
        $formation->setDateDebut($dateDebut);
        $formation->setDateFin($dateFin);

        $manager->persist($formation);

        $formation = new Formation();
        $formation->setTypeFormation("1A");
        $formation->setSemestre(2);
        $formation->setDepartement($manager->getRepository(Departement::class)->findOneByNomDpt("Informatique"));
        $dateDebut = new \DateTime();
        $dateFin = new \DateTime();
        $formation->setDateDebut($dateDebut);
        $formation->setDateFin($dateFin);

        $etudiant = new Etudiant();
        $etudiant->setPrenomEtu("Juliette");
        $etudiant->setNomEtu("Dubernet");
        $etudiant->setMailEtu("juliette.dubernet@etu.univ-orleans.fr");
        $etudiant->addFormation($formation);

        $manager->persist($etudiant);
        $manager->persist($formation);

        $formation = new Formation();
        $formation->setTypeFormation("2A");
        $formation->setSemestre(1);
        $formation->setDepartement($manager->getRepository(Departement::class)->findOneByNomDpt("Informatique"));
        $dateDebut = new \DateTime();
        $dateFin = new \DateTime();
        $formation->setDateDebut($dateDebut);
        $formation->setDateFin($dateFin);

        $manager->persist($formation);

        $formation = new Formation();
        $formation->setTypeFormation("2A");
        $formation->setSemestre(2);
        $formation->setDepartement($manager->getRepository(Departement::class)->findOneByNomDpt("Informatique"));
        $dateDebut = new \DateTime();
        $dateFin = new \DateTime();
        $formation->setDateDebut($dateDebut);
        $formation->setDateFin($dateFin);

        $etudiant = new Etudiant();
        $etudiant->setPrenomEtu("Quentin");
        $etudiant->setNomEtu("Zerguini");
        $etudiant->setMailEtu("quentin.zerguini@etu.univ-orleans.fr");
        $etudiant->addFormation($formation);

        $manager->persist($etudiant);
        $manager->persist($formation);

        $formation = new Formation();
        $formation->setTypeFormation("1A");
        $formation->setSemestre(1);
        $formation->setDepartement($manager->getRepository(Departement::class)->findOneByNomDpt("GEA"));
        $dateDebut = new \DateTime();
        $dateFin = new \DateTime();
        $formation->setDateDebut($dateDebut);
        $formation->setDateFin($dateFin);

        $manager->persist($formation);

        $formation = new Formation();
        $formation->setTypeFormation("1A");
        $formation->setSemestre(2);
        $formation->setDepartement($manager->getRepository(Departement::class)->findOneByNomDpt("GEA"));
        $dateDebut = new \DateTime();
        $dateFin = new \DateTime();
        $formation->setDateDebut($dateDebut);
        $formation->setDateFin($dateFin);

        $manager->persist($formation);

        $manager->flush();
    }
    function getDependencies(){
      return array(LoadDepartement::class);
    }
}
