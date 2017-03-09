<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadUser.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Entity\Departement;
use IUTO\LivretBundle\Entity\Formation;

class LoadUser implements FixtureInterface, DependentFixtureInterface
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
        $user = new User();
        $user->setPrenomUser("Juliette");
        $user->setNomUser("Dubernet");
        $user->setMailUser("juliette.dubernet@etu.univ-orleans.fr");
        $user->setRole("Etudiant");
        $user->setIdUniv("o05462");
        $user->addFormation($formation);
        $formation->addUser($user);

        $manager->persist($user);

        $user = new User();
        $user->setPrenomUser("Quentin");
        $user->setNomUser("Zerguini");
        $user->setMailUser("quentin.zerguini@etu.univ-orleans.fr");
        $user->setRole("Etudiant");
        $user->setIdUniv("o05684");
        $formation = $manager->getRepository(Formation::class)->findOneBy(array("departement" => ($manager->getRepository(Departement::class)->findOneByNomDpt("Informatique")), "typeFormation" => "2A"));
        $user->addFormation($formation);
        $formation->addUser($user);

        $manager->persist($user);


        $user = new User();
        $user->setNomUser("Cleuziou");
        $user->setPrenomUser("Guillaume");
        $user->setMailUser("guillaume.cleuziou@univ-orleans.fr");
        $user->setRole("Chef de departement");
        $user->setIdUniv("p35468");
        $user->addFormation($manager->getRepository(Formation::class)->findOneBy(array("departement" => ($manager->getRepository(Departement::class)->findOneByNomDpt("Informatique")), "typeFormation" => "prof")));

        $manager->persist($user);

        $user = new User();
        $user->setNomUser("Limet");
        $user->setPrenomUser("Sebastien");
        $user->setMailUser("sebastien.limet@univ-orleans.fr");
        $user->setRole("Enseignant");
        $user->setIdUniv("p54681");
        $user->addFormation($manager->getRepository(Formation::class)->findOneBy(array("departement" => ($manager->getRepository(Departement::class)->findOneByNomDpt("Informatique")), "typeFormation" => "prof")));
        $manager->persist($user);


        $manager->flush();
    }
    function getDependencies(){
      return array(LoadFormation::class);
    }
}
