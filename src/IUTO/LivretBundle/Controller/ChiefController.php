<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ChiefController extends Controller
{
  public function chiefController()
  {
      if ($id == "etudiant"){ // Si un étudiant se connecte
          return $this->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'étudiant', 'info' => array('Créer un compte rendu', 'Correction compte rendu'),'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu')));
      }
      elseif ($id == "professeur"){ // Si un professeur se connecte
          return $this->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'professeur', 'info' => array('Demandes de correction', 'Projets validés')));
      }
      elseif ($id == "communication"){ // Si un membre du service communication se connecte
          return $this->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'Service de communication', 'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets')));
      }
      elseif ($id == "chef"){ // Si un chef de département se connecte
          return $this->render('IUTOLivretBundle:Base:base.html.twig', array('statutCAS' => 'Chef de département', 'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'), 'options' => array('Générer un livret', 'Modifier la présentation du département', 'Sélection des projets', 'Afficher la liste des projets du département', 'Ajouter un projet')));
      }
  }
}
