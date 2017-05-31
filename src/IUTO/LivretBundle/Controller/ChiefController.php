<?php

namespace IUTO\LivretBundle\Controller;

use Exception;
use IUTO\LivretBundle\Entity\Commentaire;
use IUTO\LivretBundle\Entity\Departement;
use IUTO\LivretBundle\Entity\Edito;
use IUTO\LivretBundle\Entity\Image;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\AddImageType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use IUTO\LivretBundle\Form\EditoType;
use IUTO\LivretBundle\Form\LivretChooseProjectsType;
use IUTO\LivretBundle\Form\LivretCreateType;
use IUTO\LivretBundle\Form\NewLivretType;
use IUTO\LivretBundle\Form\ProjetAddKeyWordType;
use IUTO\LivretBundle\Form\ProjetChiefCreateType;
use IUTO\LivretBundle\Form\ProjetMarquantType;
use IUTO\LivretBundle\Form\ProjetNotMarquantType;
use IUTO\LivretBundle\Form\ProjetNotValideType;
use IUTO\LivretBundle\Form\ProjetValideType;
use phpCAS;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetContenuType;
use Symfony\Component\HttpFoundation\Request;
use IUTO\LivretBundle\Entity\Livret;

class ChiefController extends Controller
{
    public function chiefhomeAction()
    {
        $idUniv = phpCAS::getUser();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneByIdUniv($idUniv);


        return $this->render('IUTOLivretBundle:Chief:chiefhome.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'options' => array('Générer un livret au format pdf', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_options' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'routing_statutCAShome' => '/chef',
            'user' => $user,
        ));
    }

    public function chiefCreateLivretAction(Request $request)
    {
        $idUniv = phpCAS::getUser();

        $manager = $this->getDoctrine()->getManager();

        $newLivret = new Livret();
        $newLivret->setDateCreationLivret(new \DateTime());

        $formCreate = $this->createForm(NewLivretType::class, $newLivret);
        $formCreate->handleRequest($request);

        if ($formCreate->isSubmitted() && $formCreate->isValid()) {
            $manager->persist($newLivret);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_chief_newLivret_selectOptions', array(
                'livret' => $newLivret->getId(),
            ));
        }

        return $this->render('IUTOLivretBundle:Chief:chiefCreateLivret.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'routing_statutCAShome' => '/chef',
            'formCreate' => $formCreate->createView(),
        ));
    }

    public function chiefSelectOptionsLivretAction(Request $request, Livret $livret)
    {
        $idUniv = phpCAS::getUser();

        $manager = $this->getDoctrine()->getManager();
        $repositoryProjet = $manager->getRepository('IUTOLivretBundle:Projet');

        $form = $this->createForm(LivretCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dateDebutSelection = \DateTime::createFromFormat('d/m/Y', $form["dateDebut"]->getData());
            $dateFinSelection = \DateTime::createFromFormat('d/m/Y', $form["dateFin"]->getData());
            $formationsSelectionnes = $form["listeFormation"]->getData();
            $departementsSelectionnes = $form["listeDepartements"]->getData();
            $projetsMarquants = $form["projetMarquants"]->getData();

            $qb = $repositoryProjet->createQueryBuilder('p');
            $qb->where('p.dateDebut > :dateDebut')
                ->setParameter('dateDebut', $dateDebutSelection)
                ->andWhere('p.dateFin < :dateFin')
                ->setParameter('dateFin', $dateFinSelection)
                ->andWhere('p.validerProjet = 1')
                ->andWhere('p.marquantProjet = :isMarquant')
                ->setParameter('isMarquant', $projetsMarquants);

            $projets = $qb->getQuery()->getResult();
            $livretProjets = array();
            foreach ($projets as $curProj) {
                $curFormation = $curProj->getEtudiants()[0]->getFormations()[0];
                $curDept = $curFormation->getDepartement()->getNomDpt();
                $curTypeFormation = $curFormation->getTypeFormation();

                foreach ($formationsSelectionnes as $curFormSelected) {
                    if ($curFormSelected == $curTypeFormation) {
                        foreach ($departementsSelectionnes as $curDeptSelected) {
                            if ($curDeptSelected == $curDept) {
                                array_push($livretProjets, $curProj);
                            }
                        }
                    }
                }
            }

            $idProjs = array();
            foreach ($livretProjets as $p) {
                $livret->addProjet($p);
                $p->addLivret($livret);
                array_push($idProjs, $p->getId());
                $manager->persist($p);
            }
            $manager->persist($livret);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_chief_livretProjects', array(
                'livret' => $livret->getId(),
            ));
        }

        return $this->render('IUTOLivretBundle:Communication:communicationgenerationlivret.html.twig', array(
            'form' => $form->createView(),
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'routing_statutCAShome' => '/chef',
        ));
    }

    public function chiefSelectLivretProjectsAction(Request $request, Livret $livret)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $oldProjects = $livret->getProjets();

        $form = $this->createForm(LivretChooseProjectsType::class);
        $form->get('projects')->setData($oldProjects);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newLivret = new Livret();

            $newLivret->setIntituleLivret($livret->getIntituleLivret());
            foreach ($livret->getEditos() as $edito) {
                $newLivret->addEdito($edito);
            }

            $newLivret->setDateCreationLivret($livret->getDateCreationLivret());

            $newProjects = $form['projects']->getData();

            foreach ($newProjects as $curPro) {
                $newLivret->addProjet($curPro);
                $curPro->addLivret($newLivret);
                $em->persist($curPro);
            }

            $em->persist($newLivret);
            $em->remove($livret);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_chief_choose_livret', array());
        }

        return $this->render('IUTOLivretBundle:Chief:chiefChooseLivretProjects.html.twig', array(
            'livret' => $livret,
            'form' => $form->createView(),
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'routing_statutCAShome' => '/chef',
        ));
    }

    public function chiefChooseLivretAction()
    {
        //récupération des informations sur l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        // récuperation des projets d'un étudiant
        $livrets = $em->getRepository('IUTOLivretBundle:Livret')->findAll();

        // affichage de la page de selection du projet à modifier ou prévisualiser
        return $this->render('IUTOLivretBundle:Chief:chiefChooseLivret.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'routing_statutCAShome' => '/chef',
            'livrets' => $livrets,
        ));
    }

    public function chiefModifLivretAction(Request $request, Livret $livret)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $formModif = $this->createForm(NewLivretType::class, $livret);
        $formModif->handleRequest($request);

        if ($formModif->isSubmitted() && $formModif->isValid()) {
            $em->persist($livret);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_chief_livretProjects', array(
                'livret' => $livret->getId(),
            ));
        }

        return $this->render('IUTOLivretBundle:Chief:chiefCreateLivret.html.twig', array(
            'formCreate' => $formModif->createView(),
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'routing_statutCAShome' => '/chef',
        ));
    }

    public function chiefDeleteLivretAction(Request $request, Livret $livret)
    {
        $idUnic = phpCAS::getUser();

        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($livret->getProjets() as $curProjet) {
                $curProjet->removeLivret($livret);
                $em->persist($curProjet);
            }

            $em->remove($livret);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "Le livret a bien été supprimé.");


            return $this->redirectToRoute('iuto_livret_chief_choose_livret', array());
        }

        return $this->render('IUTOLivretBundle:Chief:chiefDeleteLivret.html.twig', array(
            'livret' => $livret,
            'form' => $form->createView(),
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'routing_statutCAShome' => '/chef',
        ));
    }

    public function chiefChooseProjectAction()
    {
        $idUniv = phpCAS::getUser();
        $manager = $this->getDoctrine()->getManager();


        $projets = $manager->getRepository(Projet::class)->findAll();
        $dpt = $manager->getRepository(Departement::class)->findAll();
        $annee = array(1 => date("y"), 2 => date("y") - 1, 3 => date("y") - 2, 4 => date("y") - 3, 5 => date("y") - 4);
        $proms = array(1 => "1A", 2 => "2A", 3 => "AS", 4 => "LP");

        return $this->render('IUTOLivretBundle:Chief:chiefliste.html.twig', array(
            'routing_statutCAShome' => '/chef',
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'projets' => $projets,
            'dpt' => $dpt,
            'annee' => $annee,
            'promos' => $proms,
        ));

    }

    public function chiefChooseEditoAction()
    {
        //récupération des informations sur l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        // récuperation des projets d'un étudiant
        $editos = $em->getRepository('IUTOLivretBundle:Edito')->findAll();

        // affichage de la page de selection du projet à modifier ou prévisualiser
        return $this->render('IUTOLivretBundle:Chief:chiefChooseEdito.html.twig', array(
            'routing_statutCAShome' => '/chef',
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'editos' => $editos,
        ));
    }

    public function chiefCreateEditoAction(Request $request)
    {
        $idUniv = phpCAS::getUser();
        $manager = $this->getDoctrine()->getManager();

        $newEdito = new Edito();

        $formCreate = $this->createForm(EditoType::class, $newEdito);
        $formCreate->handleRequest($request);

        if ($formCreate->isSubmitted() && $formCreate->isValid()) {
            $manager->persist($newEdito);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_chief_choose_edito', array());
        }

        return $this->render('IUTOLivretBundle:Chief:chiefEdito.html.twig', array(
            'routing_statutCAShome' => '/chef',
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'form' => $formCreate->createView(),
        ));
    }

    public function chiefDeleteEditoAction(Request $request, Edito $edito)
    {
        // récupération des inforamtions dur l'utilsateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em->remove($edito);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "L'édito a bien été supprimé.");

            return $this->redirectToRoute('iuto_livret_chief_choose_edito', array());
        }

        return $this->render('IUTOLivretBundle:Chief:chiefConfirmEditoDelete.html.twig', array(
            'edito' => $edito,
            'form' => $form->createView(),
            'routing_statutCAShome' => '/chef',
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/project', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
        ));
    }

    public function chiefCorrectionProjetAction(Request $request, Projet $projet)
    {
        //        récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        // Recuperation de l'étudiant connecté
        $chef = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        $allUsers = $em->getRepository(User::class)->findAll();
        $etudiants = array();
        $tuteurs = array();
        foreach ($allUsers as $curUser) {
            if (in_array('ROLE_student', $curUser->getRoles()) && !$curUser->getFormations()->isEmpty()) {
                array_push($etudiants, $curUser);
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


        $form->handleRequest($request);

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
            array_push($x, $user->getRole());
            array_push($commentaires, $x);
        };

        //verifie si le formulaire est valide ou non et si il est envoyé
        if ($form->isSubmitted() && $form->isValid()) {

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


            // récupération de la date et changement de son format
            $dateFormD = $form['dateDebut']->getData();
            $dateFormF = $form['dateFin']->getData();

            //stockage de la date dans le projet
            $newProjet->setDateDebut(\DateTime::createFromFormat('d/m/Y', $dateFormD));
            $newProjet->setDateFin(\DateTime::createFromFormat('d/m/Y', $dateFormF));

//            récupération des étudiants selectionnées dans le formulaire
            $etus = $form['etudiants']->getData();
            $tuts = $form['tuteurs']->getData();

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

            //redirection vers la page suivante
            return $this->redirectToRoute('iuto_livret_chief_correction_img_word', array(
                    'projet' => $newProjet->getId())
            );
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


            $comReponse->setUser($chef);
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
                array_push($x, $user->getRole());
                array_push($commentaires, $x);
            };

//            reaffichage du rendu de la section de commentaires
            return $this->render('IUTOLivretBundle:Chief:chiefCorrectionProjet.html.twig',
                array(
                    'form' => $form->createView(),
                    'formCom' => $formCom->createView(),
                    'commentaires' => $commentaires,
                    'routing_statutCAShome' => '/chef',
                    'statutCAS' => 'chef de département',
                    'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                    'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
                ));
        }

        // affichage de la page du formulaire
        return $this->render('IUTOLivretBundle:Chief:chiefCorrectionProjet.html.twig', array(
                'form' => $form->createView(),
                'formCom' => $formCom->createView(),
                'commentaires' => $commentaires,
                'routing_statutCAShome' => '/chef',
                'statutCAS' => 'chef de département',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            )
        );
    }

    public function chiefCorrectionImgWordAction(Request $request, Projet $projet)
    {
//      recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $chef = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

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
            array_push($x, $user->getRole());
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
            $comReponse->setUser($chef);
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
            return $this->render('IUTOLivretBundle:Chief:chiefCorrectionImgWord.html.twig', array(
                'formMot' => $formMot->createView(),
                'formCom' => $formCom->createView(),
                'routing_statutCAShome' => '/chef',
                'statutCAS' => 'chef de département',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
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
            return $this->render('IUTOLivretBundle:Chief:chiefCorrectionImgWord.html.twig', array(
                'formMot' => $formMot->createView(),
                'formCom' => $formCom->createView(),
                'routing_statutCAShome' => '/chef',
                'statutCAS' => 'chef de département',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
                'projet' => $projet,
                'motsCle' => $motsCles,
                'images' => $images,
                'logo' => $logo,
                'commentaires' => $commentaires,
            ));
        }
        return $this->render('IUTOLivretBundle:Chief:chiefCorrectionImgWord.html.twig', array(
            'formMot' => $formMot->createView(),
            'formCom' => $formCom->createView(),
            'routing_statutCAShome' => '/chef',
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'projet' => $projet,
            'motsCle' => $motsCles,
            'images' => $images,
            'logo' => $logo,
            'commentaires' => $commentaires,
        ));
    }

    public function chiefCreateProjectAction(Request $request)
    {
        //        récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        // Recuperation de l'étudiant connecté
        $chef = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        //creation d'un nouveau projet
        $projet = new Projet();

        $allUsers = $em->getRepository(User::class)->findAll();
        $etudiants = array();
        $tuteurs = array();
        foreach ($allUsers as $curUser) {
            if (in_array('ROLE_student', $curUser->getRoles()) && !$curUser->getFormations()->isEmpty()) {
                array_push($etudiants, $curUser);
            }
            if (in_array('ROLE_faculty', $curUser->getRoles())) {
                array_push($tuteurs, $curUser);
            }
        }
        // création du formulaire de création d'un projet
        $form = $this->createForm(ProjetChiefCreateType::class, $projet, ['etudiants' => $etudiants, 'tuteurs' => $tuteurs]);
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
            return $this->redirectToRoute('iuto_livret_chief_addWordImage_project', array(
                    'projet' => $projet->getId())
            );
        }

        // affichage de la page du formulaire
        return $this->render('IUTOLivretBundle:Chief:chiefCreateProject.html.twig', array(
                'form' => $form->createView(),
                'routing_statutCAShome' => '/chef',
                'statutCAS' => 'chef de département',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            )
        );
    }

    public function chiefDeleteProjetAction(Request $request, Projet $projet)
    {
        // récupération des inforamtions dur l'utilsateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em->remove($projet);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "Le projet a bien été supprimé.");

            return $this->redirectToRoute('iuto_livret_chief_choose_project', array());
        }

        return $this->render('IUTOLivretBundle:Chief:chiefConfirmProjetDelete.html.twig', array(
            'projet' => $projet,
            'form' => $form->createView(),
            'statutCAS' => 'chef de département',
            'routing_statutCAShome' => '/chef',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
        ));
    }

    public function chiefValiderProjetAction(Request $request, Projet $projet)
    {
        // recupération de l'utilisateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

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
            return $this->redirectToRoute('iuto_livret_chief_valider_projet',
                array(
                    'projet' => $projet->getId(),
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
            return $this->redirectToRoute('iuto_livret_chief_valider_projet',
                array(
                    'projet' => $projet->getId(),
                    'projetO' => $projet,
                ));
        }

        if ($formSetMarquant->isSubmitted() && $formSetMarquant->isValid()) {
            $projet->setMarquantProjet(true);

            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_chief_valider_projet',
                array(
                    'projet' => $projet->getId(),
                    'projetO' => $projet,
                ));
        }
        if ($formUnSetMarquant->isSubmitted() && $formUnSetMarquant->isValid()) {
            $projet->setMarquantProjet(false);

            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_chief_valider_projet',
                array(
                    'projet' => $projet->getId(),
                    'projetO' => $projet,
                ));
        }

//        rendu de la page finale de modification
        return $this->render('IUTOLivretBundle:Chief:chiefValiderProjet.html.twig',
            array(
                'formSetValide' => $formSetValide->createView(),
                'formSetMarquant' => $formSetMarquant->createView(),
                'formUnSetValide' => $formUnSetValide->createView(),
                'formUnSetMarquant' => $formUnSetMarquant->createView(),
                'projet' => $idProjet,
                'statutCAS' => 'chef de département',
                'routing_statutCAShome' => '/chef',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
                'projetO' => $projet,
            )
        );
    }

    public function chiefAddLogoAction(Request $request, Projet $projet)
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
            return $this->redirectToRoute('iuto_livret_chief_correction_img_word', array(
                    'projet' => $projet->getId(),
                )
            );
        }

        return $this->render('IUTOLivretBundle:Chief:chiefAddImageLogoProject.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'chef de département',
                'routing_statutCAShome' => '/chef',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
                'projet' => $projet,
            )
        );
    }

    public function chiefAddImageAction(Request $request, Projet $projet)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $image = new Image();

        $form = $this->createForm(AddImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (count($projet->getImages()) < 2) {
                $image->setProjet($projet);
                $em->persist($image);
                $em->flush();
            } else {
                throw new Exception('Seulement 2 images peuvent être liées au projet.');
            }

            // redirection vers la page de prévisualisation ou de retour à l'accueil une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_chief_correction_img_word', array(
                    'projet' => $projet->getId(),
                )
            );
        }

        return $this->render('IUTOLivretBundle:Chief:chiefAddImageLogoProject.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'chef de département',
                'routing_statutCAShome' => '/chef',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
                'projet' => $projet,
            )
        );
    }

    public function  chiefDeleteImageLogoAction(Request $request, Image $image)
    {
//      récupération des inforamtions dur l'utilsateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $projet = $image->getProjet();

        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($image);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "L'image a bien été supprimée.");


            return $this->redirectToRoute('iuto_livret_chief_correction_img_word', array(
                'projet' => $projet->getId(),
            ));
        }


        return $this->render('IUTOLivretBundle:Chief:chiefConfirmImageLogoDelete.html.twig', array(
            'image' => $image,
            'form' => $form->createView(),
            'statutCAS' => 'chef de département',
            'routing_statutCAShome' => '/chef',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'projet' => $projet,
        ));
    }
} 
