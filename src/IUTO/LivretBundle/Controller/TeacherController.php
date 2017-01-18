<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TeacherController extends Controller
{
  public function teacherAction()
  {
    return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig', array('statutCAS' => 'professeur', 'options' => array('Voir les demandes de correction de projets', 'Voir les projets validés')));
  }
}
