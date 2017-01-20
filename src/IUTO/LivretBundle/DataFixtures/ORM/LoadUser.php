<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadUser.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Entity\Formation;

class LoadUser implements FixtureInterface, DependentFixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setPrenomUser("Juliette");
        $user->setNomUser("Dubernet");
        $user->setMailUser("juliette.dubernet@etu.univ-orleans.fr");
        $user->setRole("Etudiant");
        $user->addFormation($manager->getRepository(Formation::class)->findAllByTypeFormation("2A")->findOneBySemestre(1));

        $manager->persist($user);

        $user = new User();
        $user->setPrenomUser("Quentin");
        $user->setNomUser("Zerguini");
        $user->setMailUser("quentin.zerguini@etu.univ-orleans.fr");
        $user->setRole("Etudiant");
        $user->addFormation($manager->getRepository(Formation::class)->findAllByTypeFormation("2A")->findOneBySemestre(1));

        $manager->persist($user);

        $user = new User();
        $user->setNomUser("Cleuziou");
        $user->setPrenomUser("Guillaume");
        $user->setMailUser("guillaume.cleuziou@univ-orleans.fr");
        $user->setRole("Chef de departement");
        $user->addFormation($manager->getRepository(Formation::class)->findAllByTypeFormation("prof info"));

        $manager->persist($user);

        $user = new User();
        $user->setNomPers("Limet");
        $user->setPrenomPers("Sebastien");
        $user->setMailPers("sebastien.limet@univ-orleans.fr");
        $user->addFormation($manager->getRepository(Formation::class)->findAllByTypeFormation("prof info"));

        $manager->persist($user);

        $manager->flush();
    }
    function getDependencies(){
      return array(LoadFormation::class);
    }
}
