<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetModifType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use Symfony\Component\HttpFoundation\Request;

class ChiefController extends Controller
{
    public function chiefhomeAction($id)
    {
        return $this->render('IUTOLivretBundle:Chief:chiefhome.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'options' => array('Générer un livret au format pdf', 'Modifier la présentation du département', 'Sélection des projets', 'Afficher la liste des projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/'.$id.'/chef/presentation', '/'.$id.'/correctionChief1', '/'.$id.'/chef/Info/liste', '#'),
            'routing_options' => array('#', '/'.$id.'/chef/presentation', '/'.$id.'/correctionChief1', '/'.$id.'/chef/Info/liste', '#'),
            'routing_statutCAShome' => '/'.$id.'/chef',));
    }

    public function chiefpresentationAction($id)
    {
        return $this->render('IUTOLivretBundle:Chief:chiefpresentation.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/'.$id.'/chef/presentation', '#', '/'.$id.'/chef/Info/liste', '#'),
            'routing_statutCAShome' => '/'.$id.'/chef',));
    }

    public function chieflisteAction($nomDep, $id)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Projet');
        $projets = $repository->findBy(['id' => $nomDep]);

        $projects = array();
        foreach($projets as $elem){
            $nom = $projets->getIntitule();
            array_push($projects, $nom);
        };

        return $this->render('IUTOLivretBundle:Chief:chiefliste.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/'.$id.'/chef/presentation', '#', '/'.$id.'/chef/Info/liste', '#'),
            'routing_statutCAShome' => '/'.$id.'/chef',
            'projets' => $projects));
    }

    public function correctionChief1Action($id)
    {
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
            'routing_info' => array('#', '/'.$id.'/chef/presentation', '#', '/'.$id.'/chef/Info/liste', '#'),
            'routing_statutCAShome' => '/'.$id.'/chef',
            'pagePrec' => '/'.$id.'/chef',
            'projets' => $projetsValides));

    }

    public function correctionChief2Action(Request $request, $id, Projet $projet)
    {

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
                'routing_statutCAShome' => '/'.$id.'/chef',
                'commentaires' => $commentaires,
                'routing_info' => array('#','/'.$id.'/correctionChief1','#', '/'.$id.'/projetsValidesChief1','#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/'.$id.'/correctionChief1',
                'pageSuiv' => '/'.$id.'/'.$idProjet.'/correctionChief3'
            ));
    }

    public function correctionChief3Action(Request $request, $id, Projet $projet)
    {
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
                'routing_statutCAShome' => '/'.$id.'/chef',
                'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
                'routing_info' => array('#','/'.$id.'/correctionChief1','#', '/'.$id.'/chef','#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/'.$id.'/'.$idProjet.'/correctionChief2',
                'pageSuiv' => '/'.$id.'/'.$idProjet.'/correctionChief4'
            ));
    }

    public function correctionChief4Action(Request $request, $id, Projet $projet)
    {
        $idProjet = $projet->getId();

        return $this->render('IUTOLivretBundle:Chief:correctionChief4.html.twig',
            array('id' => $id,
                'statutCAS' => 'professeur',
                'routing_statutCAShome' => '/'.$id.'/chef',
                'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
                'routing_info' => array('#','/'.$id.'/correctionChief1','#', '/'.$id.'/chef','#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/'.$id.'/'.$idProjet.'/correctionChief3'
            ));
    }

    public function genLivretChiefAction()
    {
        return $this->render('IUTOLivretBundle:Chief:genLivretChief.html.twig', array('statutCAS' => 'Chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_statutCAShome' => '/chef',
            'routing_info' => array('/chef/generation', '/chef/edito', '#')));
    }

    public function presentationDptAction()
    {
        return $this->render('IUTOLivretBundle:Chief:presentationDpt.html.twig', array('statutCAS' => 'Chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'projets du département', 'Ajouter un projet'),
            'options' => array('Visualiser','valider'),
            'routing_statutCAShome' => '/chef',
            'routing_info' => array('/communication/generation', '/chef/edito', '#'),
            'routing_options' => array('/chef/editoprevisualiser','/chef')));

    }
} 
