<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
  public function statutAction($id) // Renvoie le statut de la personne qui se connecte
  {
    if ($id == 'étudiant')
    {
      $content = $this->get('templating')->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'étudiant'));
      return new Response($content);
    }
  }

  public function optionAction($id) // Renvoie les caractéristiques propre à la personne qui se connecte
  {
    if (statutAction($id) == "étudiant") // Si un étudiant se connecte
    {
      $info = $this->get('templating')->render('IUTOLivretBundle:Base:base.html.twig', array('Correction compte rendu','Créer un compte rendu'));
      return new Response($info);
    }
    elseif (statutAction($id) == "professeur") // Si un professeur se connecte
    {
      $info = $this->get('templating')->render('IUTOLivretBundle:Base:base.html.twig', array('Demandes de correction','Projets validés'));
      return new Response($info);
    }
    elseif (statutAction($id) == "communication") // Si un membre du service communication se connecte
    {
      $info =$this->get('templating')->render('IUTOLivretBundle:Base:base.html.twig', array('Générer un livret','Créer un édito','Corriger des projets'));
      return new Response($info);
    }
  }
}
