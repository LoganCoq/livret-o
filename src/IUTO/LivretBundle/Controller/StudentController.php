<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Etudiant;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

    public function createProjectAction(Request $request)
    {

        $projet = new Projet();

        $manager = $this->getDoctrine()->getManager();
        $etudiant = $manager->getRepository(Etudiant::class)->findOneByNomEtu("Dubernet");
        $formation = $etudiant->getFormation()[0];
        $anneeDebut = $formation->getYearDebut();
        $anneeFin = $formation->getYearFin();
        $departement = $formation->getDepartement()->getNomDpt();
        $formation = $formation->getTypeFormation();

//        $listeEtudiants = ;
        $form = $this->createForm(ProjetType::class, $projet);
//        $form = $this->createForm(ProjetType::class, $projet,['listeEtudiants' => $listeEtudiants]);//TODO
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('');
        }

        return $this->render('IUTOLivretBundle:Student:createProject.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'étudiant', 'info' => array('Créer un compte rendu', 'Correction compte rendu'),
                'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu')));




    }
}
