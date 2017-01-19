<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ChiefController extends Controller
{
    public function chiefhomeAction()
    {
        return $this->render('IUTOLivretBundle:Chief:chiefhome.html.twig', array('statutCAS' => 'chef de département', 'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'), 'options' => array('Générer des livrets au format PDF', 'Modifier la présentation du département', 'Projets à mettre en avant', 'Afficher la liste des projets du département', 'Ajouter un projet')));
    }
}