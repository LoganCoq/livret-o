<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
//    Controller pour l'affichage de la page d'accueil
    public function indexAction()
    {
//        On met deux route en paramètres:
//        /login pour passer par l'authentification et /public pour accéder au module public
        return $this->render('IUTOLivretBundle:Home:index.html.twig', array('onclick' => '/login','modulePublic' => '/public'));
    }

    public function chooseModuleAction()
    {
        return $this->render('IUTOLivretBundle:Home:chooseModule.html.twig');
    }
}
