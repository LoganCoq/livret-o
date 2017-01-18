<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CommunicationController extends Controller
{
  public function communicationhomeAction()
  {
      return $this->render('IUTOLivretBundle:Communication:communicationhome.html.twig', array('statutCAS' => 'service de communication', 'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'), 'options' => array('Générer un livret', 'Créer un édito', 'Corriger des comptes-rendus de projets')));
  }
}
