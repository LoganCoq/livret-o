<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;

class ChiefController extends Controller
{
    public function chiefhomeAction($id)
    {
        return $this->render('IUTOLivretBundle:Chief:chiefhome.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'options' => array('Générer un livret au format pdf', 'Modifier la présentation du département', 'Sélection des projets', 'Afficher la liste des projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/'.$id.'/chef/presentation', '#', '/'.$id.'/chef/Info/liste', '#'),
            'routing_options' => array('#', '/'.$id.'/chef/presentation', '#', '/'.$id.'/chef/Info/liste', '#'),
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
        $projects = $this->getDoctrine()->getRepository('IUTOLivretBundle:Projet')->findBy(['id' => $nomDep]);
        return $this->render('IUTOLivretBundle:Chief:chiefliste.html.twig', array('statutCAS' => 'chef de département',
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
        $nomProjets = array();
        foreach($projets as $elem){
            array_push($nomProjets, $elem->getIntituleProjet());
        };



        return $this->render('IUTOLivretBundle:Chief:correctionChief1.html.twig', array('id' => $id,
            'statutCAS' => 'Chef de département',
            'projets' => $nomProjets,
            'routing_statutCAShome' => '/'.$id.'/chef',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_info' => array('/'.$id.'/correctionChief1', '#')));

    }

    public function correctionChief2Action(Request $request, $id, Projet $projet)
    {

        $form = $this->createForm(ProjetModifType::class, $projet);
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

        return $this->render('IUTOLivretBundle:Chief:correctionChief2.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'Chef de département',
                'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
                'routing_statutCAShome' => '/'.$id.'/chef',
                'commentaires' => $commentaires,
                'routing_info' => array('/'.$id.'/correctionChief1', '#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => array('/'.$id.'/correctionChief1'),
                'pageSuiv' => array('/'.$id.'/'.$idProjet.'/correctionChief3')
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
                'routing_info' => array('/'.$id.'/correctionChief1', '#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => array('/'.$id.'/'.$idProjet.'/correctionChief2'),
                'pageSuiv' => array('/'.$id.'/correctionChief1')
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
