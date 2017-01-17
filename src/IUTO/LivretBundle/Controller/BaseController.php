<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
  public function baseAction() // Test du controlleur
  {
    return new Response("Hello World");
  }

  public function StudentAction() // récupère le status de la personne qui se connecte
  {
    $content = $this->get('templating')->render('LivretBundle:Default:base.html.twig', array('statutCAS' => 'étudiant'));
    return new Response($content);
  }

  public function ProfAction()
  {
    $content = $this->get('templating')->render('LivretBundle:Default:base.html.twig', array('statutCAS' => 'professeur'));
    return new Response($content);
  }

  public function CommAction()
  {
    $content = $this->get('templating')->render('LivretBundle:Default:base.html.twig', array('statutCAS' => 'communication'));
    return new Response($content);
  }

  public function ChefAction()
  {
    $content = $this->get('templating')->render('LivretBundle:Default:base.html.twig', array('statutCAS' => 'chef'));
    return new Response($content);
  }
}
