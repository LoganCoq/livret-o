<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Etudiant;
use IUTO\LivretBundle\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StudentController extends Controller
{
    public function studenthomeAction()
    {
        return $this->render('IUTOLivretBundle:Student:studenthome.html.twig', array('statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Correction compte rendu'),
            'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu'),
            'routing_info' => array('/create/project', '#'),
            'routing_options' => array('/create/project', '#')));
    }

    public function createProjectAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $etudiant = $manager->getRepository(Etudiant::class)->findOneByNomEtu("Dubernet");

        $formation = $etudiant->getFormation()[0];

        $anneeDebut = $formation->getYearDebut();
        $anneeFin = $formation->getYearFin();

        $departement = $formation->getDepartement()->getNomDpt();

        $formation = $formation->getTypeFormation();

        return $this->render('IUTOLivretBundle:Student:createProject.html.twig', array('formation' => $formation,
                'departement' => $departement, 'anneeDebut' => $anneeDebut, 'anneeFin' => $anneeFin,
                'statutCAS' => 'étudiant', 'info' => array('Créer un compte rendu', 'Correction compte rendu'),
                'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu'))
        );
    }
}
