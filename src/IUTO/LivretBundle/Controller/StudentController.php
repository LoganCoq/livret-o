<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetCompleteType;
use IUTO\LivretBundle\Form\ProjetCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class StudentController extends Controller
{
    // controlleur pour le home de l'étudiant connecté
    public function studenthomeAction($id)
    {

        $manager = $this->getDoctrine()->getManager();
        // recupération de l'utilisateur connecté
        $user = $manager->getRepository(User::class)->findOneById($id);

        return $this->render('IUTOLivretBundle:Student:studenthome.html.twig', array(
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'options' => array('Créer un compte rendu',
                'Voir mes projets'),
            'routing_info' => array('/'.$id.'/create/project',
                '/'.$id.'/choose/project',
                '#'),
            'routing_options' => array('/'.$id.'/create/project',
                '/'.$id.'/choose/project',
                '#'),
            'routing_statutCAShome' => '/'.$id.'/etudiant',
                'id' => $id,
                'user' => $user,)
        );
    }

    public function createProjectAction(Request $request, $id)
    {
        $projet = new Projet();

        // Recuperation de l'étudiant connecté et des ses informations
        $manager = $this->getDoctrine()->getManager();
        $etudiant = $manager->getRepository(User::class)->findOneById($id); //TODO recuperation cas
        $formation = $etudiant->getFormations()[0];
        $anneeDebut = $formation->getDateDebut();
        $anneeFin = $formation->getDateFin();
        $departement = $formation->getDepartement()->getNomDpt();

        // remplissage des données de bases du projet
        $projet->setNomDpt($departement);
        $projet->setMarquantProjet(false);
        $projet->setValiderProjet(false);
        $projet->addEtudiant($etudiant);

        // création du formulaire de création d'un projet
        $form = $this->createForm(ProjetCreateType::class, $projet, ['annee' => $formation->getYearDebut()]);//TODO changer l'année
        $form->handleRequest($request);

        //verifie si le formulaire est valide ou pas
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // récupération de la date et changement de son format
            $dateD = \DateTime::createFromFormat('mm/dd/yyyy', $form["dateDebut"]->getData());
            $dateF = \DateTime::createFromFormat('mm/dd/yyyy', $form["dateFin"]->getData());
            $projet->setDateDebut(new \DateTime($dateD));
            $projet->setDateFin(new \DateTime($dateF));
            foreach ( $projet->getEtudiants() as $etu ){
                $etu->addProjetFait($projet);
                $em->persist($etu);
            }
            foreach ( $projet->getTuteurs() as $tut){
                $tut->addProjetSuivi($projet);
                $em->persist($tut);
            }

            // enregistrement des données dans la base
            $em->persist($projet);
            $em->flush();

            // affichage d'un message de confirmation que le projet à bien été créer
            $request->getSession()->getFlashBag()->add('success', 'Projet bien ajouté.');

            return $this->redirectToRoute('iuto_livret_studenthomepage', array(
                    'statutCAS' => 'étudiant',
                    'info' => array('Créer un compte rendu', 'Voir mes projets'),
                    'options' => array('Créer un compte rendu', ' Voir mes projets'),
                    'routing_info' => array('/'.$id.'/etudiant',
                        '/'.$id.'/choose/project',
                        '#',),
                    'routing_statutCAShome' => '/'.$id.'/etudiant',
                    'id' => $id,)
            );
        }

        // affichage de la page du formulaire
        return $this->render('IUTOLivretBundle:Student:createProject.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/'.$id.'/create/project',
                    '/'.$id.'/choose/project',
                    '#',),
                'routing_statutCAShome' => '/'.$id.'/etudiant',
                'id' => $id,)
        );

    }

    public function chooseProjectAction(Request $request, $id)
    {
        $manager = $this->getDoctrine()->getManager();
        $repositoryUser = $manager->getRepository(User::class);

        // récuperation des projets d'un étudiant
        $projets = $repositoryUser->findOneById($id)->getProjetFaits();
        $projetsSuivis = array();
        $projetsFinis = array();

        // récupération des projets fait et non fait de l'étudiant
        foreach ( $projets as $proj ){
            if ( $proj->getValiderProjet() == 1){
                $projetsFinis[] = $proj;
            }
            else{
                $projetsSuivis[] = $proj;
            }
        }

        return $this->render('IUTOLivretBundle:Student:chooseProject.html.twig',
            array('statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/'.$id.'/create/project',
                    '/'.$id.'/choose/project',
                    '#',),
                'routing_statutCAShome' => '/'.$id.'/etudiant',
                'id' => $id,
                'projetsFinis' => $projetsFinis,
                'projetsSuivis' => $projetsSuivis,)
        );
    }

    public function completeProjectAction(Request $request, $id, Projet $projet)
    {

        //creation du formulaire pour completer un projet
        $form = $this->createForm(ProjetCompleteType::class, $projet);


        $form['dateDebut']->setData($projet->getDateDebut()->format('m/d/Y'));
        $form['dateFin']->setData($projet->getDateFin()->format('m/d/Y'));

        $form->handleRequest($request);
        $manager = $this->getDoctrine()->getManager();



        // vérification de la validité du formulaire et si il à été envoyer
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $dateD = \DateTime::createFromFormat('mm/dd/yyyy', $form["dateDebut"]->getData());
            $dateF = \DateTime::createFromFormat('mm/dd/yyyy', $form["dateFin"]->getData());
            $projet->setDateDebut(new \DateTime($dateD));
            $projet->setDateFin(new \DateTime($dateF));
            // enregistrement des modifications dans la base de données
            $em->persist($projet);
            $em->flush();

            // redirection une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_confirmCompleteProject', array(
                    'statutCAS' => 'étudiant',
                    'info' => array('Créer un compte rendu', 'Voir mes projets'),
                    'options' => array('Créer un compte rendu', 'Voir mes projets'),
                    'routing_info' => array('/'.$id.'/create/project',
                        '/'.$id.'/choose/project',
                        '#',),
                    'routing_statutCAShome' => '/'.$id.'/etudiant',
                    'id' => $id,
                    'projet' => $projet->getId(),
                )
            );
        }


        return $this->render('IUTOLivretBundle:Student:completeProject.html.twig', array(
            'form' => $form->createView(),
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_info' => array('/'.$id.'/create/project',
                '/'.$id.'/choose/project',
                '#',),
            'routing_statutCAShome' => '/'.$id.'/etudiant',
            )
        );
    }

    // vue d'apres ajout d'information à un projet pour voir le pdf ou le télécharger
    public function confirmCompleteProjectAction($id, Projet $projet)
    {

        return $this->render('IUTOLivretBundle:Student:confirmCompleteProject.html.twig', array(
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'options' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_info' => array('/'.$id.'/create/project',
                '/'.$id.'/choose/project',
                '#',),
            'routing_options' => array('/'.$id.'/create/project',
                '/'.$id.'/choose/project',
                '#',),
            'routing_statutCAShome' => '/'.$id.'/etudiant',
            'id' => $id,
            'projet' => $projet)
        );
    }

    // controlleur pour voir le pdf d'un projet validé.
    public function viewFinishedProjectAction(Request $request, Projet $projet, $id){

        return $this->render('IUTOLivretBundle:Student:finishedProject.html.twig', array(
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'options' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/'.$id.'/create/project',
                    '/'.$id.'/choose/project',
                    '#',),
                'routing_options' => array('/'.$id.'/create/project',
                    '/'.$id.'/choose/project',
                    '#',),
                'routing_statutCAShome' => '/'.$id.'/etudiant',
                'projet' => $projet
        ));
    }
}
