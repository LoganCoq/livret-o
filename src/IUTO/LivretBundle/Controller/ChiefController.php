<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChiefController extends Controller
{
    public function chiefhomeAction()
    {
        return $this->render('IUTOLivretBundle:Chief:chiefhome.html.twig', array('statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'options' => array('Générer un livret au format pdf', 'Modifier la présentation du département', 'Sélection des projets', 'Afficher la liste des projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/chef/presentation', '#', '#', '#'),
            'routing_options' => array('#', '/chef/presentation', '#', '#', '#')));
    }

    public function chiefpresentationAction()
    {
        return $this->render('IUTOLivretBundle:Chief:chiefpresentation.html.twig', array('statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/chef/presentation', '#', '#', '#')));
    }
} 
