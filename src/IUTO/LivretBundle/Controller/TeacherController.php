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
    public function teacherhomeAction()
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

        $repositoryUser = $em->getRepository('IUTOLivretBundle:User');
        $names = $repositoryUser->findOneById($id)->getUsername();

        return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig', array(
            'statutCAS' => 'professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'options' => array('Voir les demande de correction de projets', 'Voir les projets validés'),
            'routing_statutCAShome' => '/professeur',
            'id' => $id,
            'professeur' => $professeur,
            'routing_info' => array('/correctionProf1', '/projetsValides1'),
            'routing_options' => array('/correctionProf1', '/projetsValides1'),
            'names' => $names, '#'));
    }

    public function correctionTeacher1Action()
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

        $repositoryUser = $em->getRepository('IUTOLivretBundle:User');
        $projets = $repositoryUser->findOneById($id)->getProjetSuivis();


        $projetsValides = array();
        foreach($projets as $elem)
        {
            if ($elem->getValiderProjet() == 0)
            array_push($projetsValides, $elem);
        };

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

    public function correctionTeacher2Action(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();


        $formModif = $this->createForm(ProjetModifType::class, $projet);

        // insertion des dates en string
        $formModif['dateDebut']->setData($projet->getDateDebut()->format('m/d/Y'));
        $formModif['dateFin']->setData($projet->getDateFin()->format('m/d/Y'));

        $formModif->handleRequest($request);

        if ($formModif->isSubmitted() && $formModif->isValid())
        {
            $dateFormD = $formModif['dateDebut']->getData();
            $dateFormF = $formModif['dateFin']->getData();

            $projet->setDateDebut(new \DateTime($dateFormD));
            $projet->setDateFin(new \DateTime($dateFormF));

            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute(
                'iuto_livret_correctionProf3', array(
                    'statusCAS' => 'professeur',
                    'info' => array('Demandes de correction', 'Projets validés'),
                    'routing_info' => array('/correctionProf1', '/projetsValides1'),
                    'projet' => $projet->getId(),
                )
            );
        }

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

        $formCom = $this->createForm(CommentaireCreateType::class, $com);
        $formCom->handleRequest($request);

        if ($formCom->isSubmitted() && $formCom->isValid())
        {
            $repositoryUser = $em->getRepository('IUTOLivretBundle:User');

            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);

            $user = $repositoryUser->findOneById($id);

            $comReponse->setUser($user);
            $comReponse->setContenu($formCom['contenu']->getData());

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

    public function correctionTeacher3Action(Request $request, Projet $projet)
    {

        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

        $formContent = $this->createForm(ProjetContenuType::class, $projet);
        $formContent->handleRequest($request);


        if ($formContent->isSubmitted() && $formContent->isValid())
        {
            $em->persist($projet);
            $em->flush();

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

        $formCom = $this->createForm(CommentaireCreateType::class, $com);
        $formCom->handleRequest($request);

        if ($formCom->isSubmitted() && $formCom->isValid())
        {
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            $user = $repositoryUser->findOneById($id);
            $comReponse->setUser($user);
            $comReponse->setContenu($formCom['contenu']->getData());

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

            return $this->render( 'IUTOLivretBundle:Teacher:correctionTeacher2.html.twig', array(
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

    public function correctionTeacher4Action(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

        $form = $this->createForm(ProjetValideType::class, $projet);
        $form->handleRequest($request);

        $idProjet = $projet->getId();

        if ($form->isSubmitted() && $form->isValid())
        {
            if($projet->getValiderProjet()==0)
            {
                $projet->setValiderProjet(1);
            }

            $em->persist($projet);
            $em->flush();

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

        $repositoryUser = $em->getRepository('IUTOLivretBundle:User');
        $projets = $repositoryUser->findOneById($id)->getProjetSuivis();



        $projetsValides = array();
        foreach($projets as $elem)
        {
            if ($elem->getValiderProjet() == 1)
                array_push($projetsValides, $elem);
        };

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
