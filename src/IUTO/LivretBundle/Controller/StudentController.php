<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetCompleteType;
use IUTO\LivretBundle\Form\ProjetCreateType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class StudentController extends Controller
{
    // controlleur pour le home de l'étudiant connecté
    public function studenthomeAction()
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();

        // creation de la vue home
        return $this->render('IUTOLivretBundle:Student:studenthome.html.twig', array(
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'options' => array('Créer un compte rendu',
                'Voir mes projets'),
            'routing_info' => array('/create/project',
                '/choose/project',
                '#'),
            'routing_options' => array('/create/project',
                '/choose/project',
                '#'),
            'routing_statutCAShome' => '/etudiant',
                'id' => $id,
                'user' => $etudiant,
        ));
    }

    public function createProjectAction(Request $request)
    {

        // Recuperation de l'étudiant connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();

        $projet = new Projet();

        // Recuperation des informations de l'étudiant
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
        $form = $this->createForm(ProjetCreateType::class, $projet, ['annee' => $formation->getYearDebut()]);
        $form->handleRequest($request);

        //verifie si le formulaire est valide ou pas
        if ($form->isSubmitted() && $form->isValid()) {

            // récupération de la date et changement de son format
            $dateD = \DateTime::createFromFormat('mm/dd/yyyy', $form["dateDebut"]->getData());
            $dateF = \DateTime::createFromFormat('mm/dd/yyyy', $form["dateFin"]->getData());
            $projet->setDateDebut(new \DateTime($dateD));
            $projet->setDateFin(new \DateTime($dateF));

            // ajout des etudiant au projet et du projet aux étudiant
            foreach ( $projet->getEtudiants() as $etu ){
                $etu->addProjetFait($projet);
                $projet->addEtudiant($etu);
                $em->persist($etu);
            }
            // ajout des tuteurs au projet et du projet aux tuteurs
            foreach ( $projet->getTuteurs() as $tut){
                $tut->addProjetSuivi($projet);
                $projet->addTuteur($tut);
                $em->persist($tut);
            }

            // enregistrement des données dans la base
            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_contenuProject', array(
                    'statutCAS' => 'étudiant',
                    'info' => array('Créer un compte rendu', 'Voir mes projets'),
                    'routing_info' => array('/etudiant',
                        '/choose/project',
                        '#',),
                    'routing_statutCAShome' => '/etudiant',
                    'id' => $id,
                    'projet' => $projet->getId())
            );
        }

        // affichage de la page du formulaire
        return $this->render('IUTOLivretBundle:Student:createProject.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/create/project',
                    '/choose/project',
                    '#',),
                'routing_statutCAShome' => '/etudiant',
                'id' => $id,)
        );
    }

    public function contenuProjectAction(Request $request, Projet $projet)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();


        //creation du formulaire d'ajout de contenu au projet
        $form = $this->createForm(ProjetContenuType::class, $projet);
        $form->handleRequest($request);

        // verifie la validité du projet si il est envoyer
        if ($form->isSubmitted() && $form->isValid()) {

            // enregistrement des données dans la base
            $em->persist($projet);
            $em->flush();

            // affichage d'un message de confirmation que le projet à bien été créer
            $request->getSession()->getFlashBag()->add('success', 'Projet bien ajouté.');

            return $this->redirectToRoute('iuto_livret_studenthomepage', array(
                    'statutCAS' => 'étudiant',
                    'info' => array('Créer un compte rendu', 'Voir mes projets'),
                    'options' => array('Créer un compte rendu', ' Voir mes projets'),
                    'routing_info' => array('/etudiant',
                        '/choose/project',
                        '#',),
                    'routing_statutCAShome' => '/etudiant',
                    'id' => $id,)
            );
        }

        // affichage de la page du formulaire
        return $this->render('IUTOLivretBundle:Student:contenuProject.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/create/project',
                   '/choose/project',
                   '#',),
                'routing_statutCAShome' => '/etudiant',
                'id' => $id,
                'projet' => $projet->getId())
            );
    }

    public function chooseProjectAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();

        // récuperation des projets d'un étudiant
        $projets = $etudiant->getProjetFaits();
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
                'routing_info' => array('/create/project',
                    '/choose/project',
                    '#',),
                'routing_statutCAShome' => '/etudiant',
                'id' => $id,
                'projetsFinis' => $projetsFinis,
                'projetsSuivis' => $projetsSuivis,)
        );
    }

    public function completeProjectAction(Request $request, Projet $projet)
    {

        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();

        //creation du formulaire pour completer un projet
        $form = $this->createForm(ProjetCompleteType::class, $projet);

        // insertion des dates en string
        $form['dateDebut']->setData($projet->getDateDebut()->format('m/d/Y'));
        $form['dateFin']->setData($projet->getDateFin()->format('m/d/Y'));

        $form->handleRequest($request);

        // vérification de la validité du formulaire et si il à été envoyer
        if ($form->isSubmitted() && $form->isValid()) {

            $dateD = \DateTime::createFromFormat('mm/dd/yyyy', $form["dateDebut"]->getData());
            $dateF = \DateTime::createFromFormat('mm/dd/yyyy', $form["dateFin"]->getData());
            $projet->setDateDebut(new \DateTime($dateD));
            $projet->setDateFin(new \DateTime($dateF));
            // enregistrement des modifications dans la base de données
            $em->persist($projet);
            $em->flush();

            // affichage d'un message success si le projet à bien été modifié
            $request->getSession()->getFlashBag()->add('success', 'Projet bien modifié.');

            // redirection une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_confirmCompleteProject', array(
                    'statutCAS' => 'étudiant',
                    'info' => array('Créer un compte rendu', 'Voir mes projets'),
                    'routing_info' => array('/create/project',
                        '/choose/project',
                        '#',),
                    'routing_statutCAShome' => '/etudiant',
                    'id' => $id,
                    'projet' => $projet->getId(),
                )
            );
        }


        return $this->render('IUTOLivretBundle:Student:completeProject.html.twig', array(
            'form' => $form->createView(),
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_info' => array('/create/project',
                '/choose/project',
                '#',),
            'routing_statutCAShome' => '/etudiant',
            )
        );
    }

    // vue d'apres ajout d'information à un projet pour voir le pdf ou le télécharger
    public function confirmCompleteProjectAction(Projet $projet)
    {

        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();


        return $this->render('IUTOLivretBundle:Student:confirmCompleteProject.html.twig', array(
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_info' => array('/create/project',
                '/choose/project',
                '#',),
            'routing_statutCAShome' => '/etudiant',
            'id' => $id,
            'projet' => $projet)
        );
    }

    // controlleur pour voir le pdf d'un projet validé.
    public function viewFinishedProjectAction(Request $request, Projet $projet){

        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();


        return $this->render('IUTOLivretBundle:Student:finishedProject.html.twig', array(
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/create/project',
                    '/choose/project',
                    '#',),
                'routing_statutCAShome' => '/etudiant',
                'projet' => $projet
        ));
    }
}
