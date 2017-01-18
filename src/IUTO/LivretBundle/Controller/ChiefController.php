<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ChiefController extends Controller
{
  public function chiefController()
  {
    return $this->render('IUTOLivretBundle:Chief:chiefhome.html.twig', array('statutCAS' => 'Chef de département', 'options' => array('Générer un livret', 'Modifier la présentation du département', 'Sélection des projets', 'Afficher la liste des projets du département', 'Ajouter un projet')));
  }
}
