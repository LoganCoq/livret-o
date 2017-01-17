<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
  public function statutAction($id) // Renvoie le statut de la personne qui se connecte
  {
      $content = $this->get('templating')->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'étudiant'));
      return new Response($content);
  }

  public function optionAction($id) // Renvoie les caractéristiques propre à la personne qui se connecte
  {
    echo $id;
    if ($id == "étudiant") // Si un étudiant se connecte
    {
      $info = $this->get('templating')->render('IUTOLivretBundle:Base:base.html.twig', array('info' => 'Correction compte rendu','Créer un compte rendu'));
      return new Response($info);
    }
  }
}
