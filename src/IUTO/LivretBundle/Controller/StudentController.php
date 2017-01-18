<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
  public function studentAction()
  {
    return $this->render('IUTOLivretBundle:Student:studenthome.html.twig', array('statutCAS' => 'étudiant','options' => array('Créer un compte rendu', 'Voir corrections compte-rendu')));
  }

    public function createProjectAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $etudiant = $manager->getRepository(Etudiant::class)->findOneByNomEtu("Dubernet");

        $formation = $etudiant->getFormation();

        $anneeDebut = $formation->getYearDebut();
        $anneeFin = $formation->getYearFin();

        $departement = $formation->getDepartement();

        return $this->render('IUTOLivretBundle:Student:createProject.html.twig',array('formation' => $formation),
            array('departement' => $departement), array('anneeDebut' => $anneeDebut), array('anneeFin' => $anneeFin)
        );
    }
}
