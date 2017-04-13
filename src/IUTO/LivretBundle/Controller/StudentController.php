<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Commentaire;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use IUTO\LivretBundle\Form\ProjetCompleteType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\ProjetCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

        //creation d'un nouveau projet
        $projet = new Projet();

        // Recuperation des informations de l'étudiant
        $formation = $etudiant->getFormations()[0];
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
            $dateFormD = $form['dateDebut']->getData();
            $dateFormF = $form['dateFin']->getData();

            //stockage de la date dans le projet
            $projet->setDateDebut(new \DateTime($dateFormD));
            $projet->setDateFin(new \DateTime($dateFormF));

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

            //redirection vers la page suivante
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
        //recuperation des informations sur l'utilisateur
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

//    controller pour l'affichage des projets d'un étudiant
    public function chooseProjectAction(Request $request)
    {
        //récupération des informations sur l'utilisateur
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
                // ajout du projet à la liste si il est valider
                $projetsFinis[] = $proj;
            }
            else{
                // ajout du projet à la liste si il est en cours de suivi
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
        //récupération des informations de l'utilisateur connecter
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

            // recupération des dates dans le formulaire
            $dateFormD = $form['dateDebut']->getData();
            $dateFormF = $form['dateFin']->getData();

            //affectations des date dans le formulaire
            $projet->setDateDebut(new \DateTime($dateFormD));
            $projet->setDateFin(new \DateTime($dateFormF));


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

        //récupération du repositoriry Commentaire
        $com = $em->getRepository(Commentaire::class)->findByProjet($projet);

        //recupération des commentaires
        $commentaires = array();
        foreach($com as $elem){
            $x=array();
            $user = $elem->getUser();
            array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
            array_push($x, $elem->getContenu());
            array_push($x, $elem->getDate());
            array_push($x, $user->getRole());
            array_push($commentaires, $x);

        };

        $formCom = $this->createForm(CommentaireCreateType::class, $com);
        $formCom->handleRequest($request);

        if ($formCom->isSubmitted() && $formCom->isValid()) {
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            $repository2 = $em->getRepository('IUTOLivretBundle:User');
            $user = $repository2->findOneById($id);
            $comReponse->setUser($user);
            $comReponse->setContenu($formCom['contenu']->getData());

            // sauvegarde des commentaires dans la base de données
            $em->persist($comReponse);
            $em->flush();

            $com = $em->getRepository(Commentaire::class)->findByProjet($projet);

            //recupération des commentaires
            $commentaires = array();
            foreach($com as $elem){
                $x=array();
                $user = $elem->getUser();
                array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
                array_push($x, $elem->getContenu());
                array_push($x, $elem->getDate());
                array_push($x, $user->getRole());
                array_push($commentaires, $x);

            };

            return $this->render('IUTOLivretBundle:Student:completeProject.html.twig', array(
                    'commentaires' => $commentaires,
                    'form' => $form->createView(),
                    'formCom' => $formCom->createView(),
                    'statutCAS' => 'etudiant',
                    'info' => array('Créer un compte rendu', 'Voir mes projets'),
                    'routing_statutCAShome' => '/etudiant',
                    'routing_info' => array('/create/project', '/choose/project'),
                ));
        }

        // affichage du formulaire pour complété le projet
        return $this->render('IUTOLivretBundle:Student:completeProject.html.twig', array(
            'commentaires' => $commentaires,
            'form' => $form->createView(),
            'formCom' => $formCom->createView(),
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
        // récupération des informations de l'utilisateur connecter
        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();

        // affichage de la page de confirmation des modification du projet
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

        // récupération des inforamtions dur l'utilsateur connecté
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
