<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Commentaire;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\ProjetModifType;
use IUTO\LivretBundle\Form\ProjetValideType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class TeacherController extends Controller
{
//    Controlleur pour l'affichage du home de teacher
    public function teacherhomeAction()
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

//        rendu de la page home du professeur avec les arguments nécessaires
        return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig', array(
            'statutCAS' => 'professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'options' => array('Voir les demande de correction de projets', 'Voir les projets validés'),
            'routing_statutCAShome' => '/professeur',
            'id' => $id,
            'professeur' => $professeur,
            'routing_info' => array('/correctionProf1', '/projetsValides1'),
            'routing_options' => array('/correctionProf1', '/projetsValides1'),
            '#'));
    }

//    controlleur pour l'affichage des projets du professeur
    public function correctionTeacher1Action()
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

//        récupération des projets suivis par l'utilisateur
        $repositoryUser = $em->getRepository('IUTOLivretBundle:User');
        $projets = $repositoryUser->findOneById($id)->getProjetSuivis();


        $projetsValides = array();
        foreach($projets as $elem)
        {
//            ajout des projet qui n'ont pas étés validés par un utilisateur
            if ($elem->getValiderProjet() == 0)
            array_push($projetsValides, $elem);
        };

//        rendu de la page d'affichage des projets suivis
        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher1.html.twig', array(
            'id' => $id,
            'statutCAS' => 'professeur',
            'projets' => $projetsValides,
            'routing_statutCAShome' => '/professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'routing_info' => array('/correctionProf1', '/projetsValides1'),
            'pagePrec' => '/professeur',
        ));

    }

//    controlleur pour la correction d'un projet
    public function correctionTeacher2Action(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

//        creation du formulaire de modification
        $formModif = $this->createForm(ProjetModifType::class, $projet);

        // insertion des dates en string
        $formModif['dateDebut']->setData($projet->getDateDebut()->format('m/d/Y'));
        $formModif['dateFin']->setData($projet->getDateFin()->format('m/d/Y'));

//        mise du formulaire en attente de submit
        $formModif->handleRequest($request);

//        vérification de la validité du formulaire et de si il à été envoyer
        if ($formModif->isSubmitted() && $formModif->isValid())
        {
//            récupération des dates du formulaire
            $dateFormD = $formModif['dateDebut']->getData();
            $dateFormF = $formModif['dateFin']->getData();
//            affectation des valeurs des dates dans le projet
            $projet->setDateDebut(new \DateTime($dateFormD));
            $projet->setDateFin(new \DateTime($dateFormF));
//            enregistrement du projet dans la base
            $em->persist($projet);
            $em->flush();

//            redirection vers le formulaire de modification suivant une fois le formulaire envoyer
            return $this->redirectToRoute(
                'iuto_livret_correctionProf3', array(
                    'statusCAS' => 'professeur',
                    'info' => array('Demandes de correction', 'Projets validés'),
                    'routing_info' => array('/correctionProf1', '/projetsValides1'),
                    'projet' => $projet->getId(),
                )
            );
        }

//        récupération des commentaires appartenant au porjet actuel
        $repositoryCommentaire = $em->getRepository('IUTOLivretBundle:Commentaire');
        $com = $repositoryCommentaire->findByProjet($projet);

        //recuperation des commentaires
        $commentaires = array();
        foreach($com as $elem)
        {
            $x=array();
            $user = $elem->getUser();
            array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
            array_push($x, $elem->getContenu());
            array_push($x, $elem->getDate());
            array_push($x, $user->getRole());
            array_push($commentaires, $x);
        };

        $idProjet = $projet->getId();

//        creation du formulaire d'ajout d'un commentaire
        $formCom = $this->createForm(CommentaireCreateType::class, $com);
//        mise en attente du formulaire d'une action sur celui ci ( submit )
        $formCom->handleRequest($request);

//        vérification de lavalidité du formulaire et de son envoi
        if ($formCom->isSubmitted() && $formCom->isValid())
        {
            $repositoryUser = $em->getRepository('IUTOLivretBundle:User');
//            création d'une nouvelle entité de commentaire
            $comReponse = new Commentaire;
//            ajout des infirmations nécessaire au commentaire
            $comReponse->setDate();
            $comReponse->setProjet($projet);

            $user = $repositoryUser->findOneById($id);

            $comReponse->setUser($user);
            $comReponse->setContenu($formCom['contenu']->getData());

//            enregistrement des données dans la base
            $em->persist($comReponse);
            $em->flush();

            //actualisation des commentaires
            $com = $repositoryCommentaire->findByProjet($projet);
            $commentaires = array();
            foreach($com as $elem)
            {
                $x=array();
                $user = $elem->getUser();
                array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
                array_push($x, $elem->getContenu());
                array_push($x, $elem->getDate());
                array_push($x, $user->getRole());
                array_push($commentaires, $x);
            };

//            reaffichage du rendu de la section de commentaires
            return $this->render( 'IUTOLivretBundle:Teacher:correctionTeacher2.html.twig',
                array(
                    'form' => $formModif->createView(),
                    'formCom' => $formCom->createView(),
                    'statutCAS' => 'professeur',
                    'info' => array('Demandes de correction', 'Projets validés'),
                    'routing_statutCAShome' => '/professeur',
                    'commentaires' => $commentaires,
                    'routing_info' => array('/correctionProf1', '/projetsValides1'),
                    'routing_options' => array('#', '#'),
                    'pagePrec' => '/correctionProf1',
                    'pageSuiv' => '/'.$idProjet.'/correctionProf3',
                ));
        }

//        rendu de la page de correction du projet
        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher2.html.twig', array(
                'form' => $formModif->createView(),
                'formCom' => $formCom->createView(),
                'statutCAS' => 'professeur',
                'info' => array('Demandes de correction', 'Projets validés'),
                'routing_statutCAShome' => '/professeur',
                'commentaires' => $commentaires,
                'routing_info' => array('/correctionProf1', '/projetsValides1'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/correctionProf1',
                'pageSuiv' => '/'.$idProjet.'/correctionProf3'
            ));
    }

//    controlleur pour la seconde partie de la correction d'un projet
    public function correctionTeacher3Action(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

//        creation du formulaire de modification du contenu du projet
        $formContent = $this->createForm(ProjetContenuType::class, $projet);
        $formContent->handleRequest($request);

//        vérification de la validité et de l'envoi du formulaire
        if ($formContent->isSubmitted() && $formContent->isValid())
        {
//            enregistrement du formulaire dans la base
            $em->persist($projet);
            $em->flush();

//            redirection vers la page de fin de correction du projet
            return $this->redirectToRoute('iuto_livret_correctionProf4', array(
                    'statusCAS' => 'professeur',
                    'info' => array('Demandes de correction', 'Projets validés'),
                    'routing_info' => array('/correctionProf1', '/projetsValides1'),
                    'projet' => $projet->getId(),
                )
            );
        }

        $repositoryUser = $em->getRepository('IUTOLivretBundle:User');
        $repositoryCommentaire = $em->getRepository('IUTOLivretBundle:Commentaire');
//        recupération des commentaires associés au projet actuel
        $com = $repositoryCommentaire->findByProjet($projet);

        $commentaires = array();
        foreach($com as $elem)
        {
            $x=array();
            $user = $elem->getUser();
            array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
            array_push($x, $elem->getContenu());
            array_push($x, $elem->getDate());
            array_push($x, $user->getRole());
            array_push($commentaires, $x);
        };

        $idProjet = $projet->getId();

//        creation du formulaire d'ajout d'un commentaire
        $formCom = $this->createForm(CommentaireCreateType::class, $com);
//        mise en attente du formulaire d'une action sur celui-ci
        $formCom->handleRequest($request);

//        verification de l'envoie et de la validité du formulaire
        if ($formCom->isSubmitted() && $formCom->isValid())
        {
//            creation d'un nouveau commentaire et ajout des informations à celui ci
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            $user = $repositoryUser->findOneById($id);
            $comReponse->setUser($user);
            $comReponse->setContenu($formCom['contenu']->getData());

//            enregistrement du commentaire dans la base
            $em->persist($comReponse);
            $em->flush();

            //actualisation des commentaires
            $com = $repositoryCommentaire->findByProjet($projet);
            $commentaires = array();
            foreach($com as $elem)
            {
                $x=array();
                $user = $elem->getUser();
                array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
                array_push($x, $elem->getContenu());
                array_push($x, $elem->getDate());
                array_push($x, $user->getRole());
                array_push($commentaires, $x);
            };

//            rendu du formulaire de modification du contenu du projet + formulaire d'ajout de commentaire
            return $this->render( 'IUTOLivretBundle:Teacher:correctionTeacher3.html.twig', array(
                    'form' => $formContent->createView(),
                    'formCom' => $formCom->createView(),
                    'statutCAS' => 'professeur',
                    'info' => array('Demandes de correction', 'Projets validés'),
                    'routing_statutCAShome' => '/professeur',
                    'commentaires' => $commentaires,
                    'routing_info' => array('/correctionProf1', '/projetsValides1'),
                    'routing_options' => array('#', '#'),
                    'pagePrec' => '/correctionProf1',
                    'pageSuiv' => '/'.$idProjet.'/correctionProf3',
                ));
        }

//        rendu de la page de modification du projet et du formulaire d'ajout d'un commentaire
        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher3.html.twig', array(
                'form' => $formContent->createView(),
                'formCom' => $formCom->createView(),
                'statutCAS' => 'professeur',
                'commentaires' => $commentaires,
                'routing_statutCAShome' => '/professeur',
                'info' => array('Demandes de correction', 'Projets validés'),
                'routing_info' => array('/correctionProf1', '/projetsValides1'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/'.$idProjet.'/correctionProf2',
                'pageSuiv' => '/'.$idProjet.'/correctionProf4'
            ));
    }

//    controlleur pour la page finale de  modification d'un projet
    public function correctionTeacher4Action(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

//        creation du formulaire de validation d'un projet
        $form = $this->createForm(ProjetValideType::class, $projet);
        $form->handleRequest($request);

        $idProjet = $projet->getId();

        if ($form->isSubmitted() && $form->isValid())
        {
//            validation du projet si le formaulaire est envoyé
            if($projet->getValiderProjet()==0)
            {
                $projet->setValiderProjet(1);
            }

//            enregistrement des données dans la base
            $em->persist($projet);
            $em->flush();

//            rendu du home de teacher
            return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig',
                array(
                    'form' => $form->createView(),
                    'id' => $id,
                    'statutCAS' => 'professeur',
                    'options' => array('Voir les demande de correction de projets', 'Voir les projets validés'),
                    'routing_statutCAShome' => '/professeur',
                    'info' => array('Demandes de correction', 'Projets validés'),
                    'routing_info' => array('/correctionProf1', '/projetsValides1'),
                    'routing_options' => array('/correctionProf1', '/projetsValides1'),
                    'professeur' => $professeur,
                    'pagePrec' => '/'.$idProjet.'/correctionProf3',
                    'projet' => $idProjet,
                ));
        }

//        rendu de la page finale de modification
        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher4.html.twig',
            array(
                'form' => $form->createView(),
                'id' => $id,
                'statutCAS' => 'professeur',
                'routing_statutCAShome' => '/professeur',
                'info' => array('Demandes de correction', 'Projets validés'),
                'routing_info' => array('/correctionProf1', '/projetsValides1'),
                'pagePrec' => '/'.$idProjet.'/correctionProf3',
                'projet' => $idProjet,
                ));
    }

    public function projetsValidesTeacher1Action(Request $request)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

//        recupération des projets suivis par l'utilisateur
        $repositoryUser = $em->getRepository('IUTOLivretBundle:User');
        $projets = $repositoryUser->findOneById($id)->getProjetSuivis();


        $projetsValides = array();
        foreach($projets as $elem)
        {
//            recupération des projets validés
            if ($elem->getValiderProjet() == 1)
                array_push($projetsValides, $elem);
        };
    //  rendu de la page de selections des projets validés
        return $this->render('IUTOLivretBundle:Teacher:projetsValidesTeacher1.html.twig', array(
            'id' => $id,
            'statutCAS' => 'professeur',
            'projets' => $projetsValides,
            'routing_statutCAShome' => '/professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'routing_info' => array('/correctionProf1', '/projetsValides1'),
            'pagePrec' => '/professeur',
        ));

    }

}
