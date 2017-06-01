<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Commentaire;
use IUTO\LivretBundle\Entity\Image;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\AddImageType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use IUTO\LivretBundle\Form\ProjetAddKeyWordType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\ProjetMarquantType;
use IUTO\LivretBundle\Form\ProjetModifType;
use IUTO\LivretBundle\Form\ProjetNotMarquantType;
use IUTO\LivretBundle\Form\ProjetNotValideType;
use IUTO\LivretBundle\Form\ProjetValideType;
use phpCAS;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class TeacherController extends Controller
{
//    Controlleur pour l'affichage du home de teacher
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des modification
    public function teacherhomeAction()
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

//        rendu de la page home du professeur avec les arguments nécessaires
        return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig', array(
            'statutCAS' => 'professeur',
            'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
            'options' => array('Voir les demandes de correction de projets', 'Voir les projets validés', 'Voir tous les projets'),
            'routing_statutCAShome' => '/professeur',
            'professeur' => $professeur,
            'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
            'routing_options' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
            '#'));
    }

//    controlleur pour l'affichage des projets du professeur
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des modification
    public function correctionTeacher1Action()
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

//        récupération des projets suivis par l'utilisateur
        $projets = $professeur->getProjetSuivis();


        $projetsValides = array();
        foreach ($projets as $elem) {
//            ajout des projet qui n'ont pas étés validés par un utilisateur
            if ($elem->getValiderProjet() == 0)
                array_push($projetsValides, $elem);
        };

//        rendu de la page d'affichage des projets suivis
        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher1.html.twig', array(
            'statutCAS' => 'professeur',
            'projets' => $projetsValides,
            'routing_statutCAShome' => '/professeur',
            'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
            'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
        ));

    }

//    controlleur pour la correction d'un projet
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des modification
    public function correctionTeacher2Action(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        $allUsers = $em->getRepository(User::class)->findAll();
        $etudiants = array();
        $tuteurs = array();
        foreach ($allUsers as $curUser) {
            if (in_array('ROLE_student', $curUser->getRoles())) {
                array_push($etudiants, $curUser);
            }
            if (in_array('ROLE_faculty', $curUser->getRoles())) {
                array_push($tuteurs, $curUser);
            }
        }

//        creation du formulaire de modification
        $formModif = $this->createForm(ProjetModifType::class, $projet, ['etudiants' => $etudiants, 'tuteurs' => $tuteurs]);

        // insertion des dates en string
        $formModif['dateDebut']->setData($projet->getDateDebut()->format('d/m/Y'));
        $formModif['dateFin']->setData($projet->getDateFin()->format('d/m/Y'));

//        mise du formulaire en attente de submit
        $formModif->handleRequest($request);

        //        récupération des commentaires appartenant au porjet actuel
        $repositoryCommentaire = $em->getRepository('IUTOLivretBundle:Commentaire');
        $com = $repositoryCommentaire->findByProjet($projet);

        //recuperation des commentaires
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

//        vérification de la validité du formulaire et de si il à été envoyer
        if ($formModif->isSubmitted() && $formModif->isValid()) {
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
            $newProjet->setDescriptionClientProjet(($projet->getDescriptionClientProjet()));

//            récupération des dates du formulaire
            $dateFormD = $formModif['dateDebut']->getData();
            $dateFormF = $formModif['dateFin']->getData();

//            affectation des valeurs des dates dans le projet
            $newProjet->setDateDebut(\DateTime::createFromFormat('d/m/Y', $dateFormD));
            $newProjet->setDateFin(\DateTime::createFromFormat('d/m/Y', $dateFormF));

            $etus = $formModif['etudiants']->getData();
            $tuts = $formModif['tuteurs']->getData();


            foreach ($etus as $etu) {
                $etu->addProjetFait($newProjet);
                $newProjet->addEtudiant($etu);
                $em->persist($etu);
            }
            foreach ($tuts as $tut) {
                $tut->addProjetSuivi($newProjet);
                $newProjet->addTuteur($tut);
                $em->persist($tut);
            }
            foreach ($com as $c) {
                $c->setProjet($newProjet);
            }
            // enregistrement des données dans la base
            $em->persist($newProjet);
//            suppression de l'ancien projet de la base
            $em->remove($projet);
            $em->flush();

//            redirection vers le formulaire de modification suivant une fois le formulaire envoyer
            return $this->redirectToRoute(
                'iuto_livret_correctionProf3', array(
                'projet' => $newProjet->getId(),
            ));
        }

//        creation du formulaire d'ajout d'un commentaire
        $formCom = $this->createForm(CommentaireCreateType::class, $com);
//        mise en attente du formulaire d'une action sur celui ci ( submit )
        $formCom->handleRequest($request);

//        vérification de lavalidité du formulaire et de son envoi
        if ($formCom->isSubmitted() && $formCom->isValid()) {
//            création d'une nouvelle entité de commentaire
            $comReponse = new Commentaire;
//            ajout des infirmations nécessaire au commentaire
            $comReponse->setDate();
            $comReponse->setProjet($projet);


            $comReponse->setUser($professeur);
            $comReponse->setContenu($formCom['contenu']->getData());

//            enregistrement des données dans la base
            $em->persist($comReponse);
            $em->flush();

            //actualisation des commentaires
            $com = $repositoryCommentaire->findByProjet($projet);
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

//            reaffichage du rendu de la section de commentaires
            return $this->render('IUTOLivretBundle:Teacher:correctionTeacher2.html.twig',
                array(
                    'form' => $formModif->createView(),
                    'formCom' => $formCom->createView(),
                    'statutCAS' => 'professeur',
                    'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
                    'routing_statutCAShome' => '/professeur',
                    'commentaires' => $commentaires,
                    'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
                ));
        }

//        rendu de la page de correction du projet
        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher2.html.twig', array(
            'form' => $formModif->createView(),
            'formCom' => $formCom->createView(),
            'statutCAS' => 'professeur',
            'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
            'routing_statutCAShome' => '/professeur',
            'commentaires' => $commentaires,
            'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
        ));
    }

//    controlleur pour la seconde partie de la correction d'un projet
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des modification
    public function correctionTeacher3Action(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        $images = $em->getRepository(Image::class)->findByProjet($projet->getId());
//        creation du formulaire de modification du contenu du projet
        $formContent = $this->createForm(ProjetContenuType::class, $projet);
        $formContent->handleRequest($request);

//        vérification de la validité et de l'envoi du formulaire
        if ($formContent->isSubmitted() && $formContent->isValid()) {
//            enregistrement du formulaire dans la base
            $em->persist($projet);
            $em->flush();

//            redirection vers la page de fin de correction du projet
            return $this->redirectToRoute('iuto_livret_add_img_word_teacher', array(
                    'projet' => $projet->getId(),
                )
            );
        }

        $repositoryCommentaire = $em->getRepository('IUTOLivretBundle:Commentaire');
//        recupération des commentaires associés au projet actuel
        $com = $repositoryCommentaire->findByProjet($projet);

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


//        creation du formulaire d'ajout d'un commentaire
        $formCom = $this->createForm(CommentaireCreateType::class, $com);
//        mise en attente du formulaire d'une action sur celui-ci
        $formCom->handleRequest($request);

//        verification de l'envoie et de la validité du formulaire
        if ($formCom->isSubmitted() && $formCom->isValid()) {
//            creation d'un nouveau commentaire et ajout des informations à celui ci
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            $comReponse->setUser($professeur);
            $comReponse->setContenu($formCom['contenu']->getData());

//            enregistrement du commentaire dans la base
            $em->persist($comReponse);
            $em->flush();

            //actualisation des commentaires
            $com = $repositoryCommentaire->findByProjet($projet);
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

//            rendu du formulaire de modification du contenu du projet + formulaire d'ajout de commentaire
            return $this->render('IUTOLivretBundle:Teacher:correctionTeacher3.html.twig', array(
                'form' => $formContent->createView(),
                'formCom' => $formCom->createView(),
                'statutCAS' => 'professeur',
                'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
                'routing_statutCAShome' => '/professeur',
                'commentaires' => $commentaires,
                'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
                'projet' => $projet,
                'image' => $images,
            ));
        }

//        rendu de la page de modification du projet et du formulaire d'ajout d'un commentaire
        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher3.html.twig', array(
            'form' => $formContent->createView(),
            'formCom' => $formCom->createView(),
            'statutCAS' => 'professeur',
            'commentaires' => $commentaires,
            'routing_statutCAShome' => '/professeur',
            'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
            'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
            'projet' => $projet,
            'images' => $images,
        ));
    }

//    controlleur pour la gestion de l'ajout d'images et de mots clés
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va effectuer des modification
    public function correctionTeacherWordImageAction(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

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

        // vérification de la validité du formulaire si celui-ci à été envoyer
        if ($formCom->isSubmitted() && $formCom->isValid()) {
            // création et affectation des informations dans le nouveau commentaire
            $comReponse = new Commentaire;
            $comReponse->setDate();
            $comReponse->setProjet($projet);
            // ajout de l'user au commentaire
            $comReponse->setUser($professeur);
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
            return $this->render('IUTOLivretBundle:Teacher:correctionTeacherWordImage.html.twig', array(
                'formMot' => $formMot->createView(),
                'formCom' => $formCom->createView(),
                'statutCAS' => 'professeur',
                'routing_statutCAShome' => '/professeur',
                'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
                'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
                'projet' => $projet,
                'motsCle' => $motsCles,
                'images' => $images,
                'logo' => $logo,
                'commentaires' => $commentaires,
            ));
        }

        if ($formMot->isSubmitted() && $formMot->isValid()) {
            $newWord = $formMot['mot']->getData();
            $projet->addMotCleProjet($newWord);
            $motsCles = $projet->getMotsClesProjet();


            $em->persist($projet);
            $em->flush();

            //rechargement du formulaire pour les mots clés
            return $this->render('IUTOLivretBundle:Teacher:correctionTeacherWordImage.html.twig', array(
                'formMot' => $formMot->createView(),
                'formCom' => $formCom->createView(),
                'statutCAS' => 'professeur',
                'routing_statutCAShome' => '/professeur',
                'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
                'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
                'projet' => $projet,
                'motsCle' => $motsCles,
                'images' => $images,
                'logo' => $logo,
                'commentaires' => $commentaires,
            ));
        }
        return $this->render('IUTOLivretBundle:Teacher:correctionTeacherWordImage.html.twig', array(
            'formMot' => $formMot->createView(),
            'formCom' => $formCom->createView(),
            'statutCAS' => 'professeur',
            'routing_statutCAShome' => '/professeur',
            'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
            'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
            'projet' => $projet,
            'motsCle' => $motsCles,
            'images' => $images,
            'logo' => $logo,
            'commentaires' => $commentaires,
        ));
    }

//    controlleur pour la page finale de  modification d'un projet
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet que l'on va pouvoir afficher, télécharger, valider ou "marquer"
    public function correctionTeacher4Action(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

//        creation du formulaire de validation d'un projet
        $formSetValide = $this->createForm(ProjetValideType::class, $projet);
        $formSetValide->handleRequest($request);

        $formSetMarquant = $this->createForm(ProjetMarquantType::class, $projet);
        $formSetMarquant->handleRequest($request);

        $formUnSetValide = $this->createForm(ProjetNotValideType::class, $projet);
        $formUnSetValide->handleRequest($request);

        $formUnSetMarquant = $this->createForm(ProjetNotMarquantType::class, $projet);
        $formUnSetMarquant->handleRequest($request);


        $idProjet = $projet->getId();

        if ($formSetValide->isSubmitted() && $formSetValide->isValid()) {
//            validation du projet si le formaulaire est envoyé
            $projet->setValiderProjet(true);

//            enregistrement des données dans la base
            $em->persist($projet);
            $em->flush();

//            rendu du home de teacher
            return $this->redirectToRoute('iuto_livret_correctionProf4',
                array(
                    'projet' => $projet->getId(),
                    'professeur' => $professeur,
                    'projetO' => $projet,
                ));
        }

        if ($formUnSetValide->isSubmitted() && $formUnSetValide->isValid()) {
//            validation du projet si le formaulaire est envoyé
            $projet->setValiderProjet(0);

//            enregistrement des données dans la base
            $em->persist($projet);
            $em->flush();

//            rendu du home de teacher
            return $this->redirectToRoute('iuto_livret_correctionProf4',
                array(
                    'projet' => $projet->getId(),
                    'professeur' => $professeur,
                    'projetO' => $projet,
                ));
        }

        if ($formSetMarquant->isSubmitted() && $formSetMarquant->isValid()) {
            $projet->setMarquantProjet(true);

            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_correctionProf4',
                array(
                    'projet' => $projet->getId(),
                    'professeur' => $professeur,
                    'projetO' => $projet,
                ));
        }
        if ($formUnSetMarquant->isSubmitted() && $formUnSetMarquant->isValid()) {
            $projet->setMarquantProjet(false);

            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_correctionProf4',
                array(
                    'projet' => $projet->getId(),
                    'professeur' => $professeur,
                    'projetO' => $projet,
                ));
        }

//        rendu de la page finale de modification
        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher4.html.twig',
            array(
                'formSetValide' => $formSetValide->createView(),
                'formSetMarquant' => $formSetMarquant->createView(),
                'formUnSetValide' => $formUnSetValide->createView(),
                'formUnSetMarquant' => $formUnSetMarquant->createView(),
                'projet' => $idProjet,
                'statutCAS' => 'professeur',
                'routing_statutCAShome' => '/professeur',
                'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
                'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
                'projetO' => $projet,
            )
        );
    }

//    controlleur pour la gestion des ajouts d'images
//    arguments :
//        request : objet pour gérer les requettes des formulaires
//        projet  : projet sur le quel on va ajouter (ou non) une image
    public function addImageCorrectionAction(Request $request, Projet $projet)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $image = new Image();
        $image->setIsLogo(false);


        $form = $this->createForm(AddImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image->setProjet($projet);
            $em->persist($image);
            $em->flush();


            // redirection vers la page de prévisualisation ou de retour à l'accueil une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_add_img_word_teacher', array(
                    'projet' => $projet->getId(),
                )
            );
        }

        return $this->render('IUTOLivretBundle:Teacher:addImageProject.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'professeur',
                'routing_statutCAShome' => '/professeur',
                'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
                'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
                'projet' => $projet,
            )
        );
    }

//    controlleur pour l'affichage des projets qui ont été validé par un professeur
//    arguments :
//
    public function projetsValidesTeacher1Action()
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $professeur = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

//        recupération des projets suivis par l'utilisateur
        $projets = $professeur->getProjetSuivis();


        $projetsValides = array();
        foreach ($projets as $elem) {
//            recupération des projets validés
            if ($elem->getValiderProjet() == 1)
                array_push($projetsValides, $elem);
        };
        //  rendu de la page de selections des projets validés
        return $this->render('IUTOLivretBundle:Teacher:projetsValidesTeacher1.html.twig', array(
            'statutCAS' => 'professeur',
            'projets' => $projetsValides,
            'routing_statutCAShome' => '/professeur',
            'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
            'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
            'pagePrec' => '/professeur',
        ));

    }

//    controlleur pour l'affichage du formulaire de suppression d'un projet
    public function deleteProjetAction(Request $request, Projet $projet)
    {
        // récupération des inforamtions dur l'utilsateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

//        creation d'un formulaire vide pour supprimmer le projet
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            suppression des images associées au projet
            $images = $em->getRepository(Image::class)->findByProjet($projet->getId());
            foreach ($images as $img) {
                $em->remove($img);
            }
//            suppression des commentaires associés au projet
            $com = $em->getRepository(Commentaire::class)->findByProjet($projet);
            foreach ($com as $c) {
                $em->remove($c);
            }

//            suppression du projet et enregistrement des modification dans la base de données
            $em->remove($projet);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le projet a bien été supprimé.");

//            redirection vers la page de choix des projets non finis
            return $this->redirectToRoute('iuto_livret_correctionProf1', array(
                'projet' => $projet,
            ));
        }

//        creation du rendu de la prage de suppression d'un projet
        return $this->render('IUTOLivretBundle:Teacher:confirmProjectDelete.html.twig', array(
            'projet' => $projet,
            'form' => $form->createView(),
            'statutCAS' => 'professeur',
            'routing_statutCAShome' => '/professeur',
            'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
            'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
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


            return $this->redirectToRoute('iuto_livret_add_img_word_teacher', array(
                'projet' => $projet->getId(),
            ));
        }


        return $this->render('IUTOLivretBundle:Teacher:confirmImageDelete.html.twig', array(
            'image' => $image,
            'form' => $form->createView(),
            'statutCAS' => 'professeur',
            'routing_statutCAShome' => '/professeur',
            'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
            'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
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
            return $this->redirectToRoute('iuto_livret_add_img_word_teacher', array(
                    'projet' => $projet->getId(),
                )
            );
        }

        return $this->render('IUTOLivretBundle:Teacher:addImageProject.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'professeur',
                'routing_statutCAShome' => '/professeur',
                'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
                'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
                'projet' => $projet,
            )
        );
    }

    public function chooseProjectAction()
    {
        //récupération des informations sur l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        // récuperation des projets d'un étudiant
        $projets = $em->getRepository(Projet::class)->findAll();
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
        return $this->render('IUTOLivretBundle:Teacher:chooseProject.html.twig', array(
                'statutCAS' => 'professeur',
                'routing_statutCAShome' => '/professeur',
                'info' => array('Demandes de correction', 'Projets validés', 'Voir tous les projets'),
                'routing_info' => array('/professeur/correctionProf1', '/professeur/projetsValides1', '/professeur/projets/choose'),
                'projetsFinis' => $projetsFinis,
                'projetsSuivis' => $projetsSuivis,)
        );
    }
}
