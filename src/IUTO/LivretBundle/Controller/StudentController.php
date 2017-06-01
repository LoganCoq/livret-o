<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Commentaire;
use IUTO\LivretBundle\Entity\Image;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\AddImageType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use IUTO\LivretBundle\Form\ProjetAddKeyWordType;
use IUTO\LivretBundle\Form\ProjetChiefCreateType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\ProjetCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use phpCAS;

class StudentController extends Controller
{
//    controlleur pour le home de l'étudiant connecté
//    arguments
//
    public function studenthomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        // recupération de l'utilisateur connecté
        $idUniv = phpCAS::getUser();
        $user = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        // creation de la vue home
        return $this->render('IUTOLivretBundle:Student:studenthome.html.twig', array(
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'options' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#'),
            'routing_options' => array('/etudiant/create/project', '/etudiant/choose/project', '#'),
            'routing_statutCAShome' => '/etudiant',
            'user' => $user,
        ));
    }

//    controlleur pour la gestion de la création d'un projet par un étudiant
//    arguments
//        request : objet pour gérer les requettes des formulaires
    public function createProjectAction(Request $request)
    {
//        récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        // Recuperation de l'étudiant connecté
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        //creation d'un nouveau projet
        $projet = new Projet();

        // Recuperation des informations sur la formation de l'étudiant connecté
        $formation = $etudiant->getFormations()->last();
        $departement = $formation->getDepartement()->getNomDpt();
        $promo = $formation->getTypeFormation();

        // remplissage des données de bases du projet
        $projet->setNomDpt($departement);
        $projet->addEtudiant($etudiant);

        $allUsers = $em->getRepository(User::class)->findAll();
        $etudiants = array();
        $tuteurs = array();
        foreach ($allUsers as $curUser) {
            if (in_array('ROLE_student', $curUser->getRoles()) && !$curUser->getFormations()->isEmpty()) {
                $curForm = $curUser->getFormations()->last()->getDepartement()->getNomDpt();
                $curProm = $curUser->getFormations()->last()->getTypeFormation();
                if ($curForm === $departement && $curProm == $promo) {
                    array_push($etudiants, $curUser);
                }
            }
            if (in_array('ROLE_faculty', $curUser->getRoles())) {
                array_push($tuteurs, $curUser);
            }
        }
        // création du formulaire de création d'un projet
        $form = $this->createForm(ProjetCreateType::class, $projet, ['annee' => $formation->getYearDebut(), 'etudiants' => $etudiants, 'tuteurs' => $tuteurs]);
        $form->handleRequest($request);

        //verifie si le formulaire est valide ou non et si il est envoyé
        if ($form->isSubmitted() && $form->isValid()) {

            // récupération de la date et changement de son format
            $dateFormD = $form['dateDebut']->getData();
            $dateFormF = $form['dateFin']->getData();

            //stockage de la date dans le projet
            $projet->setDateDebut(\DateTime::createFromFormat('d/m/Y', $dateFormD));
            $projet->setDateFin(\DateTime::createFromFormat('d/m/Y', $dateFormF));

//            récupération des étudiants selectionnées dans le formulaire
            $etusForm = $form['etudiants']->getData();
            $tutsForm = $form['tuteurs']->getData();

//              opérations sur les étudiants du projet
            foreach ($etusForm as $etu) {
//                ajout de l'étudiant au projet
                $projet->addEtudiant($etu);
//                ajout du projet à l'étudiant
                $etu->addProjetFait($projet);
//                on indique à doctrine que cette données va être sauvegardée
                $em->persist($etu);
            }

//            opération sur les tuteurs du projet
            foreach ($tutsForm as $tut) {
//                ajout du tuteur au projet
                $projet->addTuteur($tut);
//                ajout du projet aux projets suivis par le tuteur
                $tut->addProjetSuivi($projet);
//                on indique à doctrine que cette données va être sauvegardée
                $em->persist($tut);
            }

//            on indique à doctrine que la donnée va être sauvegardée
            $em->persist($projet);
            // enregistrement des données dans la base
            $em->flush();

            //redirection vers la page suivante
            return $this->redirectToRoute('iuto_livret_contenuProject', array(
                    'projet' => $projet->getId())
            );
        }

        // affichage de la page du formulaire
        return $this->render('IUTOLivretBundle:Student:createProject.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
                'routing_statutCAShome' => '/etudiant',
            )
        );
    }

//    controlleur pour la gestion de l'ajout de contenu lors de la création d'un projet
//    arguments
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des ajouts d'information ( celui créer dans createProject )
    public function contenuProjectAction(Request $request, Projet $projet)
    {
//        récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();

        //recuperation des informations sur l'utilisateur
        $idUniv = phpCAS::getUser();

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
            return $this->redirectToRoute('iuto_livret_studenthomepage', array()
            );
        }

        // affichage de la page du formulaire d'ajout de contenu au projet
        return $this->render('IUTOLivretBundle:Student:contenuProject.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
                'routing_statutCAShome' => '/etudiant',
                'projet' => $projet->getId())
        );
    }

//    controller pour l'affichage des projets d'un étudiant ( validés ou non )
//    arguments
//
    public function chooseProjectAction()
    {
        //récupération des informations sur l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        // récuperation des projets d'un étudiant
        $projets = $etudiant->getProjetFaits();
        $projetsSuivis = array();
        $projetsFinis = array();

        // récupération des projets fait et non fait de l'étudiant
        foreach ($projets as $proj) {
            if ($proj->getValiderProjet() == 1) {
                // ajout du projet à la liste si il est valider
                $projetsFinis[] = $proj;
            } else {
                // ajout du projet à la liste si il est en cours de suivi
                $projetsSuivis[] = $proj;
            }
        }

        // affichage de la page de selection du projet à modifier ou prévisualiser
        return $this->render('IUTOLivretBundle:Student:chooseProject.html.twig', array(
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
                'routing_statutCAShome' => '/etudiant',
                'projetsFinis' => $projetsFinis,
                'projetsSuivis' => $projetsSuivis,)
        );
    }

//    controlleur pour l'affichage du formulaire de correction du projet
//    arguments
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des modification
    public function completeProjectAction(Request $request, Projet $projet)
    {
        //récupération des informations de l'utilisateur connecter
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        // Recuperation des informations sur la formation de l'étudiant connecté
        $formation = $etudiant->getFormations()->last();
        $departement = $formation->getDepartement()->getNomDpt();
        $promo = $formation->getTypeFormation();


        $allUsers = $em->getRepository(User::class)->findAll();
        $etudiants = array();
        $tuteurs = array();
        foreach ($allUsers as $curUser) {
            if (in_array('ROLE_student', $curUser->getRoles()) && !$curUser->getFormations()->isEmpty()) {
                $curForm = $curUser->getFormations()->last()->getDepartement()->getNomDpt();
                $curProm = $curUser->getFormations()->last()->getTypeFormation();
                if ($curForm === $departement && $curProm == $promo) {
                    array_push($etudiants, $curUser);
                }
            }
            if (in_array('ROLE_faculty', $curUser->getRoles())) {
                array_push($tuteurs, $curUser);
            }
        }
        // création du formulaire de création d'un projet
        $form = $this->createForm(ProjetChiefCreateType::class, $projet, ['etudiants' => $etudiants, 'tuteurs' => $tuteurs]);

        // insertion des dates en string
        $form['dateDebut']->setData($projet->getDateDebut()->format('d/m/Y'));
        $form['dateFin']->setData($projet->getDateFin()->format('d/m/Y'));

        // attente d'action sur le formulaire
        $form->handleRequest($request);

        //récupération des commentaires appartenant au projet actuel
        $com = $em->getRepository(Commentaire::class)->findByProjet($projet);

        $commentaires = array();

        foreach ($com as $elem) {
            $x = array();
            $user = $elem->getUser();
            array_push($x, $user->getPrenomUser() . " " . $user->getNomUser());
            array_push($x, $elem->getContenu());
            array_push($x, $elem->getDate());
            array_push($x, $user->getRoles());
            array_push($commentaires, $x);
        };

        // vérification de la validité du formulaire et si il à été envoyer
        if ($form->isSubmitted() && $form->isValid()) {

//            création d'un nouveau projet afin d'enregistrer les
            $newProjet = new Projet();

//            récupération et affectation des données du projet dans le nouveau projet
            $newProjet->setIntituleProjet($projet->getIntituleProjet());
            $newProjet->setDescripProjet($projet->getDescripProjet());
            $newProjet->setBilanProjet($projet->getBilanProjet());
            $newProjet->setMarquantProjet($projet->getMarquantProjet());
            $newProjet->setMotsClesProjet($projet->getMotsClesProjet());
            $newProjet->setClientProjet($projet->getClientProjet());
            $newProjet->setDescriptionClientProjet($projet->getDescriptionClientProjet());
            $newProjet->setValiderProjet($projet->getValiderProjet());
            $newProjet->setNomDpt($projet->getNomDpt());
            $newProjet->setImages($projet->getImages());
            $newProjet->setDescriptionClientProjet($projet->getDescriptionClientProjet());

//            actualisation du projet associé aux images du projet
            foreach ($newProjet->getImages() as $oneImg) {
                $oneImg->setProjet($newProjet);
            }

            // recupération des dates dans le formulaire
            $dateFormD = $form['dateDebut']->getData();
            $dateFormF = $form['dateFin']->getData();

            //affectations des date dans le formulaire au bon format
            //stockage de la date dans le projet
            $newProjet->setDateDebut(\DateTime::createFromFormat('d/m/Y', $dateFormD));
            $newProjet->setDateFin(\DateTime::createFromFormat('d/m/Y', $dateFormF));

//            récupération des étudiants et tuteurs selectionnées dans le formulaire
            $etus = $form['etudiants']->getData();
            $tuts = $form['tuteurs']->getData();

//            opérations sur les étudiant selectionnées
            foreach ($etus as $etu) {
                $etu->addProjetFait($newProjet);
                $newProjet->addEtudiant($etu);
                $em->persist($etu);
            }

//            opérations sur les tuteurs sélectionnés
            foreach ($tuts as $tut) {
                $tut->addProjetSuivi($newProjet);
                $newProjet->addTuteur($tut);
                $em->persist($tut);
            }

//            actualisation du projet associé aux commentaires
            foreach ($com as $c) {
                $c->setProjet($newProjet);
            }

            // enregistrement des données dans la base
            $em->persist($newProjet);
//            suppression de l'ancien projet
            $em->remove($projet);
            $em->flush();

            // affichage d'un message success si le projet à bien été modifié
            $request->getSession()->getFlashBag()->add('success', 'Projet bien modifié.');

            // redirection vers la page de prévisualisation ou de retour à l'accueil une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_add_word_image', array(
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
            $user = $repository2->findOneByIdUniv($idUniv);
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
                array_push($x, $user->getRoles());
                array_push($commentaires, $x);

            };

            //rechargement du formulaire pour les commentaires
            return $this->render('IUTOLivretBundle:Student:completeProject.html.twig', array(
                'form' => $form->createView(),
                'formCom' => $formCom->createView(),
                'statutCAS' => 'etudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_statutCAShome' => '/etudiant',
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project'),
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
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
                'routing_statutCAShome' => '/etudiant',
                'projet' => $projet,
                'commentaires' => $commentaires,
            )
        );
    }

//    controlleur pour l'ajout de mots clés et d'image à un projet lors de la correction
//    arguments
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des modification
    public function addWordImageAction(Request $request, Projet $projet)
    {
//        récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();
        //récupération des informations de l'utilisateur connecter
        $idUniv = phpCAS::getUser();

//        récupération des mots clés du projet
        $motsCles = $projet->getMotsClesProjet();

//        création du formulaire d'ajout d'une image
        $formMot = $this->createForm(ProjetAddKeyWordType::class);
        $formMot->handleRequest($request);

        //récupération des commentaires appartenant au projet actuel
        $com = $em->getRepository(Commentaire::class)->findByProjet($projet);
        $commentaires = array();
        foreach ($com as $elem) {
            $x = array();
            $user = $elem->getUser();
            array_push($x, $user->getPrenomUser() . " " . $user->getNomUser());
            array_push($x, $elem->getContenu());
            array_push($x, $elem->getDate());
            array_push($x, $user->getRoles());
            array_push($commentaires, $x);
        };

        //creation du formulaire pour la section de chat/commentaire
        $formCom = $this->createForm(CommentaireCreateType::class, $com);
        $formCom->handleRequest($request);


//        récupération des images du projet
        $imagesL = $projet->getImages();
        $images = array();
        $logo = null;
        foreach ($imagesL as $img) {
            if ($img->getIsLogo()) {
                $logo = $img;
            } else {
                array_push($images, $img);
            }
        }

        // vérification de la validité du formulaire si celui-ci à été envoyer
        if ($formCom->isSubmitted() && $formCom->isValid()) {
            // création et affectation des informations dans le nouveau commentaire
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            // ajout de l'user au commentaire
            $repository2 = $em->getRepository('IUTOLivretBundle:User');
            $user = $repository2->findOneByIdUniv($idUniv);
            $comReponse->setUser($user);
            $comReponse->setContenu($formCom['contenu']->getData());

            // sauvegarde des commentaires dans la base de données
            $em->persist($comReponse);
            $em->flush();

            //actualisation des commentaires une fois le nouveau ajouté
            //recupération des commentaires associé au projet
            $com = $em->getRepository(Commentaire::class)->findByProjet($projet);
            $commentaires = array();
            foreach ($com as $elem) {
                $x = array();
                $user = $elem->getUser();
                array_push($x, $user->getPrenomUser() . " " . $user->getNomUser());
                array_push($x, $elem->getContenu());
                array_push($x, $elem->getDate());
                array_push($x, $user->getRoles());
                array_push($commentaires, $x);

            };

            //rechargement du formulaire pour les commentaires
            return $this->render('IUTOLivretBundle:Student:addWordImageProject.html.twig', array(
                'formCom' => $formCom->createView(),
                'formMot' => $formMot->createView(),
                'statutCAS' => 'etudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_statutCAShome' => '/etudiant',
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project'),
                'projet' => $projet,
                'images' => $images,
                'motsCles' => $motsCles,
                'commentaires' => $commentaires,
                'logo' => $logo,
            ));
        }

//        vérification de si le formulaire pour l'ajout de mots clés et envoyer et valide
        if ($formMot->isSubmitted() && $formMot->isValid()) {

//            ajouts du mot clé au projet
            $newWord = $formMot['mot']->getData();
            $projet->addMotCleProjet($newWord);
//            actualisation des mots clés du projet pour le rechargement de la page
            $motsCles = $projet->getMotsClesProjet();

//            enregistrement des donnees
            $em->persist($projet);
            $em->flush();

            //rechargement du formulaire pour les mots clés
            return $this->render('IUTOLivretBundle:Student:addWordImageProject.html.twig', array(
                'formCom' => $formCom->createView(),
                'formMot' => $formMot->createView(),
                'statutCAS' => 'etudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_statutCAShome' => '/etudiant',
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project'),
                'projet' => $projet,
                'images' => $images,
                'motsCles' => $motsCles,
                'commentaires' => $commentaires,
                'logo' => $logo,
            ));
        }

//        rendu de la page d'ajout de mots clés et de d'images
        return $this->render('IUTOLivretBundle:Student:addWordImageProject.html.twig', array(
            'formCom' => $formCom->createView(),
            'formMot' => $formMot->createView(),
            'statutCAS' => 'etudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_statutCAShome' => '/etudiant',
            'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project'),
            'projet' => $projet,
            'images' => $images,
            'motsCles' => $motsCles,
            'commentaires' => $commentaires,
            'logo' => $logo,
        ));

    }

//    controlleur pour l'ajout d'une nouvelle image au projet
//    arguments
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va ajouter une image
    public function addImageAction(Request $request, Projet $projet)
    {
//        récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();
//        récupération des données sur l'étudiant connecté
        $idUniv = phpCAS::getUser();


//        création d'une entité image qui va être remplie dans le formulaire
        $image = new Image();
        $image->setIsLogo(false);

//        creation du formulaire d'ajout d'image
        $form = $this->createForm(AddImageType::class, $image);
        $form->handleRequest($request);

//        vérification de l'envoie du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            $image->setProjet($projet);
            $em->persist($image);
            $em->flush();

            // redirection vers la page de prévisualisation ou de retour à l'accueil une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_add_word_image', array(
                    'projet' => $projet->getId(),
                )
            );
        }

        return $this->render('IUTOLivretBundle:Student:addImageProject.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
                'routing_statutCAShome' => '/etudiant',
                'projet' => $projet,
            )
        );
    }

//    controlleur pour la vue d'apres ajout d'information à un projet pour voir le pdf ou le télécharger
//    arguments
//        projet  : projet modifié précedemment
    public function confirmCompleteProjectAction(Projet $projet)
    {
        // récupération des informations de l'utilisateur connecter
        $idUniv = phpCAS::getUser();

        // affichage de la page de confirmation des modification du projet
        return $this->render('IUTOLivretBundle:Student:confirmCompleteProject.html.twig', array(
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
                'routing_statutCAShome' => '/etudiant',
                'projet' => $projet)
        );
    }

//    controlleur pour voir le pdf d'un projet validé ou pour le télécharger.
//    arguments
//        projet  : projet validé
    public function viewFinishedProjectAction(Projet $projet)
    {

        // récupération des inforamtions dur l'utilsateur connecté
        $idUniv = phpCAS::getUser();

//      rendu de la vue pour un projet fini
        return $this->render('IUTOLivretBundle:Student:finishedProject.html.twig', array(
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
            'routing_statutCAShome' => '/etudiant',
            'projet' => $projet
        ));
    }


    public function deleteProjetAction(Request $request, Projet $projet)
    {
        // récupération des inforamtions dur l'utilsateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $em->getRepository(Image::class)->findByProjet($projet->getId());
            foreach ($images as $img) {
                $em->remove($img);
            }
            $com = $em->getRepository(Commentaire::class)->findByProjet($projet);
            foreach ($com as $c) {
                $em->remove($c);
            }

            $em->remove($projet);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le projet a bien été supprimé.");


            return $this->redirectToRoute('iuto_livret_chooseProject', array());
        }

        return $this->render('IUTOLivretBundle:Student:confirmProjectDelete.html.twig', array(
            'projet' => $projet,
            'form' => $form->createView(),
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
            'routing_statutCAShome' => '/etudiant',
        ));

    }

    public function deleteImageAction(Request $request, Image $image)
    {
        // récupération des inforamtions dur l'utilsateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $projet = $image->getProjet();

        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($image);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "L'image a bien été supprimée.");


            return $this->redirectToRoute('iuto_livret_add_word_image', array(
                'projet' => $projet->getId(),
            ));
        }


        return $this->render('IUTOLivretBundle:Student:confirmImageDelete.html.twig', array(
            'image' => $image,
            'form' => $form->createView(),
            'statutCAS' => 'étudiant',
            'info' => array('Créer un compte rendu', 'Voir mes projets'),
            'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
            'routing_statutCAShome' => '/etudiant',
            'projet' => $projet,
        ));
    }

    public function addLogoAction(Request $request, Projet $projet)
    {
        //        récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();
//        récupération des données sur l'étudiant connecté
        $idUniv = phpCAS::getUser();


//        création d'une entité image qui va être remplie dans le formulaire
        $image = new Image();
        $image->setIsLogo(true);

//        creation du formulaire d'ajout d'image
        $form = $this->createForm(AddImageType::class, $image);
        $form->handleRequest($request);

//        vérification de l'envoie du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            $image->setProjet($projet);
            $em->persist($image);
            $em->flush();

            // redirection vers la page de prévisualisation ou de retour à l'accueil une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_add_word_image', array(
                    'projet' => $projet->getId(),
                )
            );
        }

        return $this->render('IUTOLivretBundle:Student:addImageProject.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'étudiant',
                'info' => array('Créer un compte rendu', 'Voir mes projets'),
                'routing_info' => array('/etudiant/create/project', '/etudiant/choose/project', '#',),
                'routing_statutCAShome' => '/etudiant',
                'projet' => $projet,
            )
        );
    }

}
