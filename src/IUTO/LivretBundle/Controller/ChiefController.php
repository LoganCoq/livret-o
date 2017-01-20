<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;

class ChiefController extends Controller
{
    public function chiefhomeAction()
    {
        return $this->render('IUTOLivretBundle:Chief:chiefhome.html.twig', array('statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'options' => array('Générer un livret au format pdf', 'Modifier la présentation du département', 'Sélection des projets', 'Afficher la liste des projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/chef/presentation', '#', '/chef/Info/liste', '#'),
            'routing_options' => array('#', '/chef/presentation', '#', '/chef/Info/liste', '#')));
    }

    public function chiefpresentationAction()
    {
        return $this->render('IUTOLivretBundle:Chief:chiefpresentation.html.twig', array('statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/chef/presentation', '#', '/chef/Info/liste', '#')));
    }

    public function chieflisteAction($nomDep)
    {
        $projects = $this->getDoctrine()->getRepository('IUTOLivretBundle:Projet')->findBy(['id' => $nomDep]);
        return $this->render('IUTOLivretBundle:Chief:chiefliste.html.twig', array('statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/chef/presentation', '#', '/chef/Info/liste', '#'),
            'projets' => $projects));
    }
} 
