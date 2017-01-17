<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
  public function baseAction() // Test étudiant
  {
    $content = $this->get('templating')->render('LivretBundle:Default:base.html.twig', array('statutCAS' => 'étudiant'));
    return new Response($content);
  }
}
