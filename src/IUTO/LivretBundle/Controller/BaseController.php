<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
  public function statutAction($id) // Renvoie le statut de la personne qui se connecte
  {
    if ($id == "étudiant"){ // Si un étudiant se connecte
      return $this->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'étudiant', 'info' => array('Créer un compte rendu', 'Correction compte rendu')));
    }
  }
}
