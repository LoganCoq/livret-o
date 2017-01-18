<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CommController extends Controller
{
  public function commAction()
  {
    return $this->render('IUTOLivretBundle:Communication:communicationhome.html.twig', array('statutCAS' => 'Service de communication', 'options' => array('Générer un livret', 'Créer un édito', 'Corriger des comptes-rendus de projets')));
  }
}
