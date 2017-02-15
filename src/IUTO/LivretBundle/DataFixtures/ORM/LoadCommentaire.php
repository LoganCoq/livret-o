<?php
// src/IUTO/LivretBundle/DataFixtures/ORM/LoadCommentaire.php

namespace IUTO\LivretBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IUTO\LivretBundle\Entity\Commentaire;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\User;

class LoadCommentaire implements FixtureInterface, DependentFixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $commentaire = new Commentaire();
        $commentaire->setContenu("Bonjour, je ne sais pas à quoi correspond votre projet.");
        $date = new \DateTime();
        $commentaire->setProjet($manager->getRepository(Projet::class)->findOneByClientProjet("Soukaïna El Abdellaoui"));
        $commentaire->setUser($manager->getRepository(User::class)->findOneByMailUser("sebastien.limet@univ-orleans.fr"));

        $manager->persist($commentaire);

        $commentaire = new Commentaire();
        $commentaire->setContenu("Bonjour, il correspond à faire le ménage");
        $date = new \DateTime();
        $commentaire->setProjet($manager->getRepository(Projet::class)->findOneByClientProjet("Soukaïna El Abdellaoui"));
        $commentaire->setUser($manager->getRepository(User::class)->findOneByMailUser("quentin.zerguini@etu.univ-orleans.fr"));

        $manager->persist($commentaire);

        $commentaire = new Commentaire();
        $commentaire->setContenu("Mais non pas du tout Quentin, c'est pour faire la vaisselle chez Sou");
        $date = new \DateTime();
        $commentaire->setProjet($manager->getRepository(Projet::class)->findOneByClientProjet("Soukaïna El Abdellaoui"));
        $commentaire->setUser($manager->getRepository(User::class)->findOneByMailUser("juliette.dubernet@etu.univ-orleans.fr"));

        $manager->persist($commentaire);

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
        return array(LoadUser::class,LoadProjet::class );
    }
}
