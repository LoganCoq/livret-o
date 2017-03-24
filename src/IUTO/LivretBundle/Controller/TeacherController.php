<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetModifType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\ProjetValideType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use Symfony\Component\HttpFoundation\Request;
use IUTO\LivretBundle\Entity\Commentaire;



class TeacherController extends Controller
{
    public function teacherhomeAction()
    {
        // recupération de l'utilisateur connecté
        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $names = $repository->findOneById($id)->getUsername();

        return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig', array('statutCAS' => 'professeur',
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
        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $projets = $repository->findOneById($id)->getProjetSuivis();


        $projetsValides = array();
        foreach($projets as $elem){
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
        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();


        $form = $this->createForm(ProjetModifType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $proj = new Projet();

            $proj -> setIntituleProjet($_POST['iuto_livretbundle_projet']['intituleProjet']);
            $proj -> setDateDebut($_POST['iuto_livretbundle_projet']['dateDebut']);
            $proj -> setDateFin($_POST['iuto_livretbundle_projet']['dateFin']);

            $em2 = $this->getDoctrine()->getManager();
            $em2->persist($proj);
            $em2->flush();


            return $this->redirectToRoute();
        }

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $com = $repository->findByProjet($projet);

        $commentaires = array();
        foreach($com as $elem){
            $x=array();
            $user = $elem->getUser();
            array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
            array_push($x, $elem->getContenu());
            array_push($x, $elem->getDate());
            array_push($x, $user->getRole());
            array_push($commentaires, $x);

        };

        $idProjet = $projet->getId();

        $form2 = $this->createForm(CommentaireCreateType::class, $com);
        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()) {
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            $repository2 = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('IUTOLivretBundle:User');
            $user = $repository2->findOneById($id);
            $comReponse->setUser($user);
            $comReponse->setContenu($_POST['iuto_livretbundle_commentaire']['contenu']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comReponse);
            $em->flush();

            return $this->render( 'IUTOLivretBundle:Teacher:correctionTeacher2.html.twig',
                array('form' => $form->createView(),
                    'formCom' => $form2->createView(),
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

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher2.html.twig',
            array('form' => $form->createView(),
                'formCom' => $form2->createView(),
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
        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

        $form = $this->createForm(ProjetContenuType::class, $projet);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $proj = new Projet();

            $proj -> setBilanProjet($_POST['iuto_livretbundle_projet']['bilanProjet']);
            $proj -> setDescripProjet($_POST['iuto_livretbundle_projet']['descripProjet']);

            $em2 = $this->getDoctrine()->getManager();
            $em2->persist($proj);
            $em2->flush();

            return $this->redirectToRoute('');
        }

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $com = $repository->findByProjet($projet);

        $commentaires = array();
        foreach($com as $elem){
            $x=array();
            $user = $elem->getUser();
            array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
            array_push($x, $elem->getContenu());
            array_push($x, $elem->getDate());
            array_push($x, $user->getRole());
            array_push($commentaires, $x);

        };

        $idProjet = $projet->getId();

        $form2 = $this->createForm(CommentaireCreateType::class, $com);
        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()) {
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            $repository2 = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('IUTOLivretBundle:User');
            $user = $repository2->findOneById($id);
            $comReponse->setUser($user);
            $comReponse->setContenu($_POST['iuto_livretbundle_commentaire']['contenu']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comReponse);
            $em->flush();

            return $this->render( 'IUTOLivretBundle:Teacher:correctionTeacher2.html.twig',
                array('form' => $form->createView(),
                    'formCom' => $form2->createView(),
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

        $repository2 = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $com = $repository2->findByProjet($projet);

        $commentaires = array();
        foreach($com as $elem){
            $x=array();
            $user = $elem->getUser();
            array_push($x, $user->getRole());
            array_push($x, $elem->getContenu());
            array_push($commentaires, $x);

        };

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher3.html.twig',
            array('form' => $form->createView(),
                'formCom' => $form2->createView(),
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
        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

        $form = $this->createForm(ProjetValideType::class, $projet);
        $form->handleRequest($request);

        $idProjet = $projet->getId();


        if ($form->isSubmitted() && $form->isValid()) {

            if($projet->getValiderProjet()==0){
                $projet->setValiderProjet(1);
            }

            $em = $this->getDoctrine()->getManager();
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
        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $professeur = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $professeur->getId();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $projets = $repository->findOneById($id)->getProjetSuivis();



        $projetsValides = array();
        foreach($projets as $elem){
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
