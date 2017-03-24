<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Service\HTML2PDF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use IUTO\LivretBundle\Entity\Livret;
use IUTO\LivretBundle\Entity\Projet;


class LivretGeneratorController extends Controller
{
    public function communicationgenerationlivretAction(Request $request)
    {
        //liste des departments
        //liste des formations
        //dateDeb
        //dateFin
        $form = $this->createForm(ProjetCreateType::class, null, );//TODO changer l'année
        $form->handleRequest($request);
        //$formationsSelectionnes = $request->get('annee');
        //$departementsSelectionnes = $request->get('departement')->getClientData();

        $manager = $this
            ->getDoctrine()
            ->getManager();
        $repository = $manager
            ->getRepository('IUTOLivretBundle:Projet');

        $livret = new Livret();
        $livret->setIntituleLivret("Projet département Informatique");
        $livret->setDateCreationLivret(new \DateTime());
        $livret->setEditoLivret("Le département informatique ils sont au dessus.");
        //creation d'un nouveau livret pour la bd

        $projets = $repository->findAll();
        //recuperation de tous les projets de la BD

        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));
        //preparation du PDF

        foreach ($projets as $projet) {
          $toutesLesFormations = $projet->getEtudiants()[0]->getFormations();
          foreach ($toutesLesFormations as $formation) {
            foreach ($formationsSelectionnes as $formationSelectionnee) {
              if($formation->getTypeFormation() == $formationSelectionnee){
                //chaque projet qui a le bon type de formation

                $dateDeFormation = $formation->getDateDebut();
                if(($dateDeFormation>=$dateDebutSelection)&&($dateDeFormation<=$dateFinSelection)){
                  //chaque projet qui a le bon type de formation à la bonne date

                  foreach ($departementsSelectionnes as $departmentSelectionne) {
                    if ($formation->getDepartement()->getNomDpt()==$departementSelectionne) {
                      //chaque projet qui a le bon type de formation à la bonne date et le bon department

                      $nomP = $projet->getIntituleProjet();
                      $descripP = $projet->getDescripProjet();
                      $bilanP = $projet->getBilanProjet();
                      $clientP = $projet->getClientProjet();
                      $etudiants = $projet->getEtudiants();
                      $tuteurs = $projet->getTuteurs();
                      //recuperation des infos du projet

                      $projet->addLivrets($livret);
                      $livret->addProjet($projet);
                      //ajout de la relation livret/projet

                      $template = $this->renderView('::pdf.html.twig',
                          ['nom' => $nomP,
                              'descrip' => $descripP,
                              'bilan' => $bilanP,
                              'client' => $clientP,
                              'etudiants' => $etudiants,
                              'tuteurs' => $tuteurs
                          ]);
                          //creation du template

                      $html2pdf->writeHTML($template);
                      //ajout de la page au livret
                    }
                  }
                }
              }
            }
          }
        }
        $manager->persist($livret);
        $manager->flush();
        //ajout du livret dans la BD
        return $html2pdf;
        //retourne le livret
    }
  }
