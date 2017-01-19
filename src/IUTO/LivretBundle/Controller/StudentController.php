<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Etudiant;
use IUTO\LivretBundle\Entity\Formation;
use IUTO\LivretBundle\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class StudentController extends Controller
{
    public function studenthomeAction()
    {
        return $this->render('IUTOLivretBundle:Student:studenthome.html.twig', array('statutCAS' => 'étudiant', 'info' => array('Créer un compte rendu', 'Correction compte rendu'),'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu')));
    }

    public function createProjectAction(Request $request)
    {
        $projet = new Projet();
        $form = $this->get('form.factory')->createBuilder(FormType::class, $projet)
            ->add('etudiants',      CollectionType::class)
            ->add('intituleProjet', TextareaType::class)
            ->add('dateDebut',      DateType::class)
            ->add('dateFin',        DateType::class)
            ->add('personnels',     CollectionType::class)
            ->add('create',         SubmitType::class)
            ->getForm();

        if ($request->isMethod('POST')){
            $form->handleRequest($request);

            if ($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($projet);
                $em->flush();

                $request->getSession()->getFlashBag->add('notice', 'Projet bien créer.');

                return $this->redirectToRoute('iuto_livret_homapage',array('statutCAS' => 'étudiant', 'info' => array('Créer un compte rendu', 'Correction compte rendu'),
                    'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu')));
            }
        }

        $manager = $this->getDoctrine()->getManager();

        $etudiant = $manager->getRepository(Etudiant::class)->findOneByNomEtu("Dubernet");

        $formation = $etudiant->getFormation()[0];

        $anneeDebut = $formation->getYearDebut();
        $anneeFin = $formation->getYearFin();

        $departement = $formation->getDepartement()->getNomDpt();

        $formation = $formation->getTypeFormation();

        return $this->render('IUTOLivretBundle:Student:createProject.html.twig',array('formation' => $formation,
            'departement' => $departement, 'anneeDebut' => $anneeDebut, 'anneeFin' => $anneeFin,
            'statutCAS' => 'étudiant', 'info' => array('Créer un compte rendu', 'Correction compte rendu'),
            'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu'))
        );
    }
}
