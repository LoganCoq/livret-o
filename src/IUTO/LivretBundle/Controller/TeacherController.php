<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TeacherController extends Controller
{
  public function teacherAction()
  {
    return $this->render('IUTOLivretBundle:Teacher:teacher.html.twig', array('statutCAS' => 'étudiant', 'options' => array('Voir les demandes de correction de projets', 'Voir les projets validés')));
  }
}
