<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
  public function studentAction()
  {
    return $this->render('IUTOLivretBundle:Student:studenthome.html.twig', array('statutCAS' => 'étudiant','options' => array('Créer un compte rendu', 'Voir corrections compte-rendu')));
  }
}
