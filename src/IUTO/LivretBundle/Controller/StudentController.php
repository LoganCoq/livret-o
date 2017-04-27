<?php

namespace IUTO\LivretBundle\Controller;

use Exception;
use IUTO\LivretBundle\Entity\Commentaire;
use IUTO\LivretBundle\Entity\Image;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\AddImageType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use IUTO\LivretBundle\Form\ProjetAddKeyWordType;
use IUTO\LivretBundle\Form\ProjetCompleteType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\ProjetCreateType;
use IUTO\LivretBundle\Form\DeleteImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StudentController extends Controller
{
//    controlleur pour le home de l'étudiant connecté
//    arguments :
//
    public function studenthomeAction()
    {
        $em = $this->getDoctrine()->getManager();

        // recupération de l'utilisateur connecté
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

//    controlleur pour la gestion de la création d'un projet par un étudiant
//    arguments :
//        request : objet pour gérer les requettes des formulaires
    public function createProjectAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Recuperation de l'étudiant connecté
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

        //verifie si le formulaire est valide ou non et si il est envoyé
        if ($form->isSubmitted() && $form->isValid()) {

            // récupération de la date et changement de son format
            $dateFormD = $form['dateDebut']->getData();
            $dateFormF = $form['dateFin']->getData();

            //stockage de la date dans le projet
            $projet->setDateDebut(new \DateTime($dateFormD));
            $projet->setDateFin(new \DateTime($dateFormF));

            $etusForm = $form['etudiants']->getData();
            $tutsForm = $form['tuteurs']->getData();

            foreach ( $etusForm as $etu )
            {
                $projet->addEtudiant($etu);
                $etu->addProjetFait($projet);
                $em->persist($etu);
            }

            foreach ( $tutsForm as $tut )
            {
                $projet->addTuteur($tut);
                $tut->addProjetSuivi($projet);
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

//    controlleur pour la gestion de l'ajout de contenu lors de la création d'un projet
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des ajouts d'information ( celui créer dans createProject )
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

            // redirection vers le home de l'étudiant
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

        // affichage de la page du formulaire d'ajout de contenu au projet
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

//    controller pour l'affichage des projets d'un étudiant ( validés ou non )
//    arguments :
//
    public function chooseProjectAction()
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

        // affichage de la page de selection du projet à modifier ou prévisualiser
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

//    controlleur pour l'affichage du formulaire de correction du projet
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des modification
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

        // attente d'action sur le formulaire
        $form->handleRequest($request);

        //récupération des commentaires appartenant au projet actuel
        $com = $em->getRepository(Commentaire::class)->findByProjet($projet);

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

        // vérification de la validité du formulaire et si il à été envoyer
        if ($form->isSubmitted() && $form->isValid())
        {

            $newProjet = new Projet();

            $newProjet->setIntituleProjet($projet->getIntituleProjet());
            $newProjet->setDescripProjet($projet->getDescripProjet());
            $newProjet->setBilanProjet($projet->getBilanProjet());
            $newProjet->setMarquantProjet($projet->getMarquantProjet());
            $newProjet->setMotsClesProjet($projet->getMotsClesProjet());
            $newProjet->setClientProjet($projet->getClientProjet());
            $newProjet->setValiderProjet($projet->getValiderProjet());
            $newProjet->setNomDpt($projet->getNomDpt());
            $newProjet->setImages($projet->getImages());

            foreach ( $newProjet->getImages() as $oneImg )
            {
                $oneImg->setProjet($newProjet);
            }

            // recupération des dates dans le formulaire
            $dateFormD = $form['dateDebut']->getData();
            $dateFormF = $form['dateFin']->getData();

            //affectations des date dans le formulaire au bon format
            $newProjet->setDateDebut(new \DateTime($dateFormD));
            $newProjet->setDateFin(new \DateTime($dateFormF));

            $etus = $form['etudiants']->getData();
            $tuts = $form['tuteurs']->getData();


            foreach ( $etus as $etu )
            {
                $etu->addProjetFait($newProjet);
                $newProjet->addEtudiant($etu);
                $em->persist($etu);
            }

            foreach ( $tuts as $tut )
            {
                $tut->addProjetSuivi($newProjet);
                $newProjet->addTuteur($tut);
                $em->persist($tut);
            }

            foreach ( $com as $c )
            {
                $c->setProjet($newProjet);
            }

            // enregistrement des données dans la base
            $em->persist($newProjet);
            $em->remove($projet);
            $em->flush();

            // affichage d'un message success si le projet à bien été modifié
            $request->getSession()->getFlashBag()->add('success', 'Projet bien modifié.');

            // redirection vers la page de prévisualisation ou de retour à l'accueil une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_add_word_image', array(
                    'statutCAS' => 'étudiant',
                    'info' => array('Créer un compte rendu', 'Voir mes projets'),
                    'routing_info' => array('/create/project',
                        '/choose/project',
                        '#',),
                    'routing_statutCAShome' => '/etudiant',
                    'id' => $id,
                    'projet' => $newProjet->getId(),
                )
            );
        }



        //creation du formulaire pour la section de chat/commentaire
        $formCom = $this->createForm(CommentaireCreateType::class, $com);
        $formCom->handleRequest($request);

        // vérification de la validité du formulaire si celui-ci à été envoyer
        if ($formCom->isSubmitted() && $formCom->isValid()) {
            // création et affectation des informations dans le nouveau commentaire
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            // ajout de l'user au commentaire
            $repository2 = $em->getRepository('IUTOLivretBundle:User');
            $user = $repository2->findOneById($id);
            $comReponse->setUser($user);
            $comReponse->setContenu($formCom['contenu']->getData());

            // sauvegarde des commentaires dans la base de données
            $em->persist($comReponse);
            $em->flush();

            //actualisation des commentaires une fois le nouveau ajouté
            //recupération des commentaires
            $com = $em->getRepository(Commentaire::class)->findByProjet($projet);
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

            //rechargement du formulaire pour les commentaires
            return $this->render('IUTOLivretBundle:Student:completeProject.html.twig', array(
                    'form' => $form->createView(),
                    'formCom' => $formCom->createView(),
                    'statutCAS' => 'etudiant',
                    'info' => array('Créer un compte rendu', 'Voir mes projets'),
                    'routing_statutCAShome' => '/etudiant',
                    'routing_info' => array('/create/project', '/choose/project'),
                        'projet' => $projet,
                        'commentaires' => $commentaires,
                ));
        }


        // affichage du formulaire pour compléter le projet
        return $this->render('IUTOLivretBundle:Student:completeProject.html.twig', array(
            'form' => $form->createView(),
            'formCom' => $formCom->createView(),
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_info' => array('/create/project',
                '/choose/project',
                '#',),
            'routing_statutCAShome' => '/etudiant',
                'projet' => $projet,
                'commentaires' => $commentaires,
            )
        );
    }

//    controlleur pour l'ajout de mots clés et d'image à un projet lors de la correction
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des modification
    public function addWordImageAction(Request $request, Projet $projet)
    {
        //récupération des informations de l'utilisateur connecter
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();


//        récupération des mots clés du projet
        $motsCles = $projet->getMotsClesProjet();

//        création du formulaire d'ajout d'une image
        $formMot = $this->createForm(ProjetAddKeyWordType::class);
        $formMot->handleRequest($request);

        //récupération des commentaires appartenant au projet actuel
        $com = $em->getRepository(Commentaire::class)->findByProjet($projet);

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

        //creation du formulaire pour la section de chat/commentaire
        $formCom = $this->createForm(CommentaireCreateType::class, $com);
        $formCom->handleRequest($request);

//        récupération des images du projet
        $images = $em->getRepository(Image::class)->findByProjet($projet->getId());

        // vérification de la validité du formulaire si celui-ci à été envoyer
        if ($formCom->isSubmitted() && $formCom->isValid()) {
            // création et affectation des informations dans le nouveau commentaire
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            // ajout de l'user au commentaire
            $repository2 = $em->getRepository('IUTOLivretBundle:User');
            $user = $repository2->findOneById($id);
            $comReponse->setUser($user);
            $comReponse->setContenu($formCom['contenu']->getData());

            // sauvegarde des commentaires dans la base de données
            $em->persist($comReponse);
            $em->flush();

            //actualisation des commentaires une fois le nouveau ajouté
            //recupération des commentaires
            $com = $em->getRepository(Commentaire::class)->findByProjet($projet);
            $commentaires = array();
            foreach ($com as $elem) {
                $x = array();
                $user = $elem->getUser();
                array_push($x, $user->getPrenomUser() . " " . $user->getNomUser());
                array_push($x, $elem->getContenu());
                array_push($x, $elem->getDate());
                array_push($x, $user->getRole());
                array_push($commentaires, $x);

            };

            //rechargement du formulaire pour les commentaires
            return $this->render('IUTOLivretBundle:Student:addWordImageProject.html.twig', array(
                'formCom' => $formCom->createView(),
                'formMot' => $formMot->createView(),
                'statutCAS' => 'etudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_statutCAShome' => '/etudiant',
                'routing_info' => array('/create/project', '/choose/project'),
                'projet' => $projet,
                'images' => $images,
                'motsCles' => $motsCles,
                'commentaires' => $commentaires,
            ));
        }

        if ($formMot->isSubmitted() && $formMot->isValid())
        {
            $newWord = $formMot['mot']->getData();
            $projet->addMotCleProjet($newWord);
            $motsCles = $projet->getMotsClesProjet();

            $em->persist($projet);
            $em->flush();

            //rechargement du formulaire pour les mots clés
            return $this->render('IUTOLivretBundle:Student:addWordImageProject.html.twig', array(
                'formCom' => $formCom->createView(),
                'formMot' => $formMot->createView(),
                'statutCAS' => 'etudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_statutCAShome' => '/etudiant',
                'routing_info' => array('/create/project', '/choose/project'),
                'projet' => $projet,
                'images' => $images,
                'motsCles' => $motsCles,
                'commentaires' => $commentaires,
            ));
        }


        return $this->render('IUTOLivretBundle:Student:addWordImageProject.html.twig', array(
            'formCom' => $formCom->createView(),
            'formMot' => $formMot->createView(),
            'statutCAS' => 'etudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_statutCAShome' => '/etudiant',
            'routing_info' => array('/create/project', '/choose/project'),
            'projet' => $projet,
            'images' => $images,
            'motsCles' => $motsCles,
            'commentaires' => $commentaires,
        ));

    }

//    controlleur pour l'ajout d'une nouvelle image au projet
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va ajouter une image
    public function addImageAction(Request $request, Projet $projet)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();

//        récupération des images du projet
        $images = $em->getRepository(Image::class)->findByProjet($projet->getId());

//        récupération des mots clés du projet
        $motsCles = $projet->getMotsClesProjet();

        $image = new Image();

        $form = $this->createForm(AddImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ( count($projet->getImages()) < 2 )
            {
                $image->setProjet($projet);
                $em->persist($image);
                $em->flush();
            }
            else
            {
                throw new Exception('Seulement 2 images peuvent être liées au projet.');
            }

            // redirection vers la page de prévisualisation ou de retour à l'accueil une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_add_word_image', array(
                    'statutCAS' => 'étudiant',
                    'info' => array('Créer un compte rendu', 'Voir mes projets'),
                    'routing_info' => array('/create/project',
                        '/choose/project',
                        '#',),
                    'routing_statutCAShome' => '/etudiant',
                    'id' => $id,
                    'projet' => $projet->getId(),
                    'images' => $images,
                    'motsCles' => $motsCles,
                )
            );

        }

        return $this->render('IUTOLivretBundle:Student:addImageProject.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/create/project',
                    '/choose/project',
                    '#',),
                'routing_statutCAShome' => '/etudiant',
                'projet' => $projet,
            )
        );
    }

//    controlleur pour la vue d'apres ajout d'information à un projet pour voir le pdf ou le télécharger
//    arguments :
//        projet  : projet modifié précedemment
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

//    controlleur pour voir le pdf d'un projet validé ou pour le télécharger.
//    arguments :
//        projet  : projet validé
    public function viewFinishedProjectAction(Projet $projet){

        // récupération des inforamtions dur l'utilsateur connecté
        $manager = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas

//      rendu de la vue pour un projet fini
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
