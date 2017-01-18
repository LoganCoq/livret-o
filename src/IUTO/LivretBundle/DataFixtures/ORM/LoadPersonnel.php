<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadPersonnel.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\Personnel;
use IUTO\LivretBundle\Entity\Fonction;

class LoadPersonnel implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $personnel = new Personnel();
        $personnel->setNomPers("Cleuziou");
        $personnel->setPrenomPers("Guillaume");
        $personnel->setMailPers("guillaume.cleuziou@univ-olreans.fr");
        $personnel->setFonction($manager->getRepository(Fonction::class)->findOneByNomFonction("Chef de departement"));

        $manager->persist($personnel);

        $personnel = new Personnel();
        $personnel->setNomPers("Limet");
        $personnel->setPrenomPers("Sebastien");
        $personnel->setMailPers("sebastien.limet@univ-olreans.fr");
        $personnel->setFonction($manager->getRepository(Fonction::class)->findOneByNomFonction("Enseignant"));

        $manager->persist($personnel);

        $personnel = new Personnel();
        $personnel->setNomPers("Duchier");
        $personnel->setPrenomPers("Denys");
        $personnel->setMailPers("denys.duchier@univ-olreans.fr");
        $personnel->setFonction($manager->getRepository(Fonction::class)->findOneByNomFonction("Enseignant"));

        $manager->persist($personnel);

        $personnel = new Personnel();
        $personnel->setNomPers("Titi");
        $personnel->setPrenomPers("Toto");
        $personnel->setMailPers("toto.titi@univ-olreans.fr");
        $personnel->setFonction($manager->getRepository(Fonction::class)->findOneByNomFonction("Service communication"));

        $manager->persist($personnel);



        $manager->flush();
    }
}
