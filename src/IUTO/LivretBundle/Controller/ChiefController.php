<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ChiefController extends Controller
{
  public function chiefController()
  {
    return $this->render('IUTOLivretBundle:Chief:chief.html.twig', array('statutCAS' => 'professeur', 'options' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet')));
  }
}
