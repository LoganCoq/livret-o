<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetModifType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use Symfony\Component\HttpFoundation\Request;
use IUTO\LivretBundle\Form\PresentationType;
use IUTO\LivretBundle\Entity\Livret;

class ChiefController extends Controller
{
    public function chiefhomeAction()
    {
        $idUniv = \phpCAS::getUser();

        return $this->render('IUTOLivretBundle:Chief:chiefhome.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'options' => array('Générer un livret au format pdf', 'Modifier la présentation du département', 'Sélection des projets', 'Afficher la liste des projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/chef/presentation', '/chef/correctionChief1', '/chef/liste', '#'),
            'routing_options' => array('#', '/chef/presentation', '/chef/correctionChief1', '/chef/liste', '#'),
            'routing_statutCAShome' => '/chef',));
    }

    public function chiefpresentationAction(Request $request)
    {
        $idUniv = \phpCAS::getUser();

        $session = $this->get('session');
        $manager = $this->getDoctrine()->getManager();
        $livret = $manager->getRepository(Livret::class)->findOneById($session->get("numEdito")); //TODO recuperation cas
        $form = $this->createForm(PresentationType::class, $livret);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('submit')->isClicked()) {
                $ed = $form['editoLivret']->getData();
                $livret->setEditoLivret($ed);
                $manager->persist($livret);
                $manager->flush();
            }
            if ($form->get('previsualiser')->isClicked())
            {
                $session->set('edito',$ed = $form['editoLivret']->getData());
                return $this->redirectToRoute('iuto_livret_chiefEditoPrevisualiser');
            }
            return $this->redirectToRoute('iuto_livret_chiefhomepage');
        }




        return $this->render('IUTOLivretBundle:Chief:chiefpresentation.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'options' => array('Visualiser'),
            'routing_statutCAShome' => '/chef',
            'routing_info' => array('#', '/chef/presentation', '/chef/correctionChief1', '/chef/liste', '#'),
            'routing_options' => array('/chef/editoprevisualiser'),
            'form' => $form->createView()));
    }

    public function chieflisteAction()
    {
        $idUniv = \phpCAS::getUser();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $formation = $repository->findOneById($id)->getFormations();
        $f = array();
        foreach($formation as $elem){
            $fId = $elem->getDepartement();
            array_push($f,$fId);
        }

        $nomDpt = array();
        foreach ($f as $x){
            $nom = $x->getNomDpt();
            array_push($nomDpt,$nom);
        }

        $manager = $this->getDoctrine()->getManager();
        $projets = $manager->getRepository(Projet::class)->findAll();


        return $this->render('IUTOLivretBundle:Chief:chiefliste.html.twig',array(
            'routing_statutCAShome' => '/chef',
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/chef/presentation', '#', '/chef/liste', '#'),
            'departement' => $nomDpt,
            'projets' => $projets
        ));

    }


    public function correctionChief1Action()
    {
        $idUniv = \phpCAS::getUser();
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


        return $this->render('IUTOLivretBundle:Chief:correctionChief1.html.twig', array('id' => $id,
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/chef/presentation', '#', '/chef/Info/liste', '#'),
            'routing_statutCAShome' => '/chef',
            'pagePrec' => '/chef',
            'projets' => $projetsValides));

    }

    public function correctionChief2Action(Request $request, Projet $projet)
    {
        $idUniv = \phpCAS::getUser();

        $form = $this->createForm(ProjetModifType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('');
        }

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $com = $repository->findByProjet($projet);

        $form2 = $this->createForm(CommentaireCreateType::class, $com);
        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('');
        }

        $idProjet = $projet->getId();

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

        return $this->render('IUTOLivretBundle:Chief:correctionChief2.html.twig',
            array('form' => $form->createView(),
                'formCom' => $form2->createView(),
                'statutCAS' => 'chef de département',
                'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
                'routing_statutCAShome' => '/chef',
                'commentaires' => $commentaires,
                'routing_info' => array('#','/chef/correctionChief1','#', '/chef/projetsValidesChief1','#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/chef/correctionChief1',
                'pageSuiv' => '/chef/'.$idProjet.'/correctionChief3'
            ));
    }

    public function correctionChief3Action(Request $request, Projet $projet)
    {
        $idUniv = \phpCAS::getUser();

        $form = $this->createForm(ProjetContenuType::class, $projet);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('');
        }

        $idProjet = $projet->getId();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $commentaires = $repository->findOneByProjet($idProjet);
        $idProjet = $projet->getId();

        return $this->render('IUTOLivretBundle:Chief:correctionChief3.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'Chef de département',
                'commentaires' => $commentaires,
                'routing_statutCAShome' => '/chef',
                'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
                'routing_info' => array('#','/chef/correctionChief1','#', '/chef','#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/chef/'.$idProjet.'/correctionChief2',
                'pageSuiv' => '/chef/'.$idProjet.'/correctionChief4'
            ));
    }

    public function correctionChief4Action(Request $request, Projet $projet)
    {
        $idUniv = \phpCAS::getUser();

        $idProjet = $projet->getId();

        return $this->render('IUTOLivretBundle:Chief:correctionChief4.html.twig',
            array('id' => $id,
                'statutCAS' => 'chef de département',
                'routing_statutCAShome' => '/chef',
                'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
                'routing_info' => array('#','/chef/correctionChief1','#', '/chef','#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/chef/'.$idProjet.'/correctionChief3'
            ));
    }

    public function genLivretChiefAction()
    {
        $idUniv = \phpCAS::getUser();

        return $this->render('IUTOLivretBundle:Chief:genLivretChief.html.twig', array('statutCAS' => 'Chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_statutCAShome' => '/chef',
            'routing_info' => array('/chef/generation', '/chef/edito', '#')));
    }

    public function presentationDptAction()
    {
        $idUniv = \phpCAS::getUser();

        return $this->render('IUTOLivretBundle:Chief:presentationDpt.html.twig', array('statutCAS' => 'Chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'projets du département', 'Ajouter un projet'),
            'options' => array('Visualiser','valider'),
            'routing_statutCAShome' => '/chef',
            'routing_info' => array('/communication/generation', '/chef/edito', '#'),
            'routing_options' => array('/chef/editoprevisualiser','/chef')));

    }
} 
