<?php

namespace IUTO\LivretBundle\Controller;


use IUTO\LivretBundle\Entity\Etudiant;
use IUTO\LivretBundle\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CreateProjectController extends Controller{

    public function createProjectAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $etudiant = $manager->getRepository(Etudiant::class);

        $formation = $etudiant->getFormation();

        $anneeDebut = $formation->getYearDebut();
        $anneeFin = $formation->getYearFin();

        $departement = $formation->getDepartement();

        return $this->render('IUTOLivretBundleStudent:createProject.html.twig',array('formation' => $formation),
            array('departement' => $departement), array('anneeDebut' => $anneeDebut), array('anneeFin' => $anneeFin)
        );
    }

}