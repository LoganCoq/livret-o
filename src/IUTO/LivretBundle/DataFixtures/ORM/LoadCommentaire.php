<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadCommentaire.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\Commentaire;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\Personnel;

class LoadCommentaire implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $commentaire = new Commentaire();
        $commentaire->setContenu("Bonjour, je ne sais pas à quoi correspond votre projet.");
        $commentaire->setReponse("Bonjour, le projet correspond à ... vous pouvez le lire dès à présent");
        $commentaire->setProjet($manager->getRepository(Projet::class)->findOneByIntituleProjet("Faire la vaisselle"));
        $commentaire->setsetPersonnel($manager->getRepository(Personnel::class)->findOneByMailPers("sebastien.limet@univ-orleans.fr"));

        $manager->persist($commentaire);

        $commentaire = new Commentaire();
        $commentaire->setContenu("Bonjour, vous pouvez supprmier la partie sur l'explication de l'implémentation.");
        $commentaire->setProjet($manager->getRepository(Projet::class)->findOneByIntituleProjet("Faire la vaisselle"));
        $commentaire->setsetPersonnel($manager->getRepository(Personnel::class)->findOneByMailPers("denys.duchier@univ-orleans.fr"));

        $manager->persist($commentaire);


        $manager->flush();
    }
}
