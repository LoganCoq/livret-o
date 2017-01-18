<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CommController extends Controller
{
  public function commAction()
  {
    return $this->render('IUTOLivretBundle:Comm:comm.html.twig', array('statutCAS' => 'Service de communication', 'options' => array('Générer livrets', 'Créer un édito', 'Corriger des projets')));
  }
}
