<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TeacherController extends Controller
{
  public function teacherhomeAction()
  {
      return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig', array('statutCAS' => 'professeur', 'info' => array('Demandes de correction', 'Projets validés'),'options' => array('Voir les demande de correction de projets', 'Voir les projets validés')));
  }
}
