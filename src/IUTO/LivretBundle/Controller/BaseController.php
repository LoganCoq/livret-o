<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
  public function baseAction($id) // Renvoie le statut de la personne qui se connecte
  {
    if ($id == "etudiant"){ // Si un étudiant se connecte
        return $this->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'étudiant', 'info' => array('Créer un compte rendu', 'Correction compte rendu')));
    }
    elseif ($id == "professeur"){ // Si un professeur se connecte
        return $this->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'professeur', 'info' => array('Demandes de correction', 'Projets validés')));
    }
    elseif ($id == "communication"){ // Si un membre du service communication se connecte
        return $this->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'professeur', 'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets')));
    }
    elseif ($id == "chef"){ // Si un chef de département se connecte
        return $this->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'professeur', 'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet')));
    }
  }
}
