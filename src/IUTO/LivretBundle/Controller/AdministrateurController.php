<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetModifType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use Symfony\Component\HttpFoundation\Request;
use IUTO\LivretBundle\Form\PresentationType;
use IUTO\LivretBundle\Entity\Livret;

class AdministrateurController extends Controller
{
    public function adminHomeAction()
    {

        // creation de la vue home
        return $this->render('IUTOLivretBundle:Administrateur:adminHome.html.twig', array(
            'statutCAS' => 'administrateur',
            'info' => array('Gérer un utilisateur'),
            'options' => array('Gérer un utilisateur'),
            'routing_info' => array('/admin/users', '#'),
            'routing_options' => array('/admin/users', '#'),
            'routing_statutCAShome' => '/administrateur',
        ));
    }

    public function adminChooseUser()
    {

        return $this->render('IUTOLivretBundle:Administrateur:adminChooseUser.html.twig', array(
            'statusCAs' => 'administrateur',
            'info' => array('Gérer un utilisateur'),
            'routing_info' => array("/admin/users","#"),
            'routing_statutCAShome' => 'administrateur',
        ));
    }
}
