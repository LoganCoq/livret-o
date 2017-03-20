<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetCompleteType;
use IUTO\LivretBundle\Form\ProjetCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StudentController extends Controller
{
    public function studenthomeAction($id)
    {
        return $this->render('IUTOLivretBundle:Student:studenthome.html.twig', array(
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Correction compte rendu'),
            'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu'),
            'routing_info' => array('/'.$id.'/create/project', '#'),
            'routing_options' => array('/'.$id.'/create/project', '#'),
            'routing_statutCAShome' => '/'.$id.'/etudiant',));
    }

    public function createProjectAction(Request $request, $id)
    {
        $projet = new Projet();

        $manager = $this->getDoctrine()->getManager();
        $etudiant = $manager->getRepository(User::class)->findOneByNomUser("Dubernet"); //TODO recuperation cas
        $formation = $etudiant->getFormations()[0];
        $anneeDebut = $formation->getDateDebut();
        $anneeFin = $formation->getDateFin();
        $departement = $formation->getDepartement()->getNomDpt();
//        $formation = $formation->getTypeFormation();

        $projet->setNomDpt($departement);
        $projet->setDateFin($anneeFin);
        $projet->setDateDebut($anneeDebut);
        $projet->setMarquantProjet(false);
        $projet->setValiderProjet(false);
//        $listeEtudiants = $manager->getRepository(Formation::class)->findAllEtudiantByFormation($formation->getId());

        $form = $this->createForm(ProjetCreateType::class, $projet);//TODO
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($projet);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Projet bien ajouté.');

            return $this->redirectToRoute('iuto_livret_studenthomepage', array(
                    'statutCAS' => 'étudiant',
                    'info' => array('Créer un compte rendu', 'Correction compte rendu'),
                    'options' => array('Créer un compte rendu', ' Voir corrections compte-rendu'),
                    'routing_statutCAShome' => '/'.$id.'/etudiant',)
            );
        }

        return $this->render('IUTOLivretBundle:Student:createProject.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Correction compte rendu'),
                'routing_info' => array('/'.$id.'/create/project', '#'),
                'routing_statutCAShome' => '/'.$id.'/etudiant',));

    }

    public function completeProjectAction(Request $request, Projet $projet, $id)
    {
        $form = $this->createForm(ProjetCompleteType::class, $projet);//TODO
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_confirmCompleteProject', array(
                    'statutCAS' => 'étudiant',
                    'info' => array('Créer un compte rendu', 'Correction compte rendu'),
                    'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu'),
                    'projet' => $projet->getId(),
                    'routing_info' => array('/'.$id.'/create/project', '#'),
                    'routing_statutCAShome' => '/'.$id.'/etudiant',
                    'id' => $id,)
            );
        }

        return $this->render('IUTOLivretBundle:Student:completeProject.html.twig', array(
            'form' => $form->createView(),
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Correction compte rendu'),
            'routing_info' => array('/'.$id.'/create/project', '#'),
            'routing_statutCAShome' => '/'.$id.'/etudiant',));

    }

    public function confirmCompleteProjectAction(Request $request, Projet $projet, $id)
    {

        return $this->render('IUTOLivretBundle:Student:confirmCompleteProject.html.twig', array(
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Correction compte rendu'),
            'options' => array('Créer un compte rendu', 'Voir corrections compte-rendu'),
            'routing_info' => array('/'.$id.'/create/project', '#'),
            'routing_options' => array('/'.$id.'/create/project', '#'),
            'routing_statutCAShome' => '/'.$id.'/etudiant',
            'projet' => $projet->getId()));
    }
}
