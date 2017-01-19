<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetModifType;


class TeacherController extends Controller
{
    public function teacherhomeAction()
    {
        return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig', array('statutCAS' => 'professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'options' => array('Voir les demande de correction de projets', 'Voir les projets validés'),
            'routing_info' => array('#', '#'),
            'routing_options' => array('#', '#')));
    }

    public function correctionTeacher1Action($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Personnel');
        $projets = $repository->findOneById($id)->getProjets();

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher1.html.twig', array('id' => $id, 'statutCAS' => 'professeur', 'projets' => $projets, 'info' => array('Demandes de correction', 'Projets validés'), 'options' => array('Voir les demande de correction de projets', 'Voir les projets validés')));

    }

    public function correctionTeacher2Action(Projet $projet)
    {
    //     $titre = $projet->getIntituleProjet();
    //     $etudiants = $projet->getEtudiants();
    //     $anneeDebut = $projet->getDateDebut();
    //     $anneeFin = $projet->getDateFin();
    //
    //     $infos = $manager->getRepository(Etudiant::class)->findOneByNomEtu($etudiants[0]->getNomEtu());
    //     $formation = $infos->getFormation()[0];
    //     $departement = $formation->getDepartement()->getNomDpt();
    //     $professeur = $projet->getPersonnels();

        // $commentaires = $manager->getRepository(Commentaire::class)->findOneByProjet($projet);
        // $contenu = $commentaires->getContenu();

        $form = $this->createForm(ProjetModifType::class, $projet);
        // $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
          $this->getDoctrine()->getManager()->flush();

          return $this->redirectToRoute('');
        }

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher2.html.twig',
            array('form' => $form->createView()));
    }

    public function correctionTeacher3Action($idTeacher, $idProjet)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:');
        $projet = $repository->findOneById($idProjet);
        $presentation = $projet->getDescripProjet();
        $resultats = $projet->getBilanProjet();

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher3.html.twig',
            array('presentation' => $presentation,
                'resultats' => $resultats));
    }
}
