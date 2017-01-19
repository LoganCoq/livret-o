<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Etudiant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StudentController extends Controller
{
  public function studenthomeAction()
  {
      return $this->render('IUTOLivretBundle:Student:studenthome.html.twig', array('statutCAS' => 'étudiant', 'info' => array('Créer un compte rendu', 'Correction compte rendu'),'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu')));
  }

    public function createProjectAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $etudiant = $manager->getRepository(Etudiant::class)->findOneByNomEtu("Dubernet");

        $formation = $etudiant->getFormation();

        $anneeDebut = $formation[0]->getYearDebut();
        $anneeFin = $formation[0]->getYearFin();

        $departement = $formation[0]->getDepartement();

        return $this->render('IUTOLivretBundle:Student:createProject.html.twig',array('formation' => $formation,
            'departement' => $departement, 'anneeDebut' => $anneeDebut, 'anneeFin' => $anneeFin)
        );
    }
}
