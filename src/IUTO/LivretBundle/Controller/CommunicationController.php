<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Commentaire;
use IUTO\LivretBundle\Entity\Departement;
use IUTO\LivretBundle\Entity\Edito;
use IUTO\LivretBundle\Entity\Image;
use IUTO\LivretBundle\Entity\Livret;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\AddImageType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use IUTO\LivretBundle\Form\EditoType;
use IUTO\LivretBundle\Form\LivretChooseProjectsType;
use IUTO\LivretBundle\Form\NewLivretType;
use IUTO\LivretBundle\Form\ProjetAddKeyWordType;
use IUTO\LivretBundle\Form\ProjetCompleteType;
use phpCAS;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Form\LivretCreateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CommunicationController extends Controller
{

    public function communicationhomeAction()
    {
        $em = $this->getDoctrine()->getManager();

        // recupération de l'utilisateur connecté
        $idUniv = phpCAS::getUser();
        $user = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        return $this->render('IUTOLivretBundle:Communication:communicationhome.html.twig', array(
                'statutCAS' => 'service de communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
                'routing_statutCAShome' => '/communication',
                'options' => array('Créer un livret au format PDF', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                'routing_options' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
                'user' => $user,
            )
        );
    }

    public function communicationCreateLivretAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $idUniv = phpCAS::getUser();

        $newLivret = new Livret();

        $newLivret->setDateCreationLivret(new \DateTime());

        $formCreate = $this->createForm(NewLivretType::class, $newLivret);
        $formCreate->handleRequest($request);

        if ($formCreate->isSubmitted() && $formCreate->isValid()) {
            $manager->persist($newLivret);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_communication_livret_project_choice', array(
                    'livretId' => $newLivret->getId(),
                )
            );
        }

        return $this->render('IUTOLivretBundle:Communication:communicationCreateLivret.html.twig', array(
                'statutCAS' => 'communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
                'routing_statutCAShome' => '/communication',
                'formCreate' => $formCreate->createView(),
            )
        );
    }

    public function communicationgenerationlivretAction(Request $request, Livret $livretId)
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
                $livretId->addProjet($p);
                $p->addLivret($livretId);
                array_push($idProjs, $p->getId());
                $manager->persist($p);
            }

            $manager->persist($livretId);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_choose_livret_projects', array(
                'livret' => $livretId->getId(),
            ));
        }

        return $this->render('IUTOLivretBundle:Communication:communicationgenerationlivret.html.twig', array(
            'form' => $form->createView(),
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
        ));
    }

    public function communicationSelectProjectsAction(Request $request, Livret $livret)
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

            return $this->redirectToRoute('iuto_livret_communicationChoixLivret', array(
                'statutCAS' => 'communication',
            ));
        }

        return $this->render('IUTOLivretBundle:Communication:communicationChooseLivretProjects.html.twig', array(
            'livret' => $livret,
            'form' => $form->createView(),
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
        ));
    }

    public function communicationChooseLivretAction()
    {
        //récupération des informations sur l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        // récuperation des projets d'un étudiant
        $livrets = $em->getRepository('IUTOLivretBundle:Livret')->findAll();

        // affichage de la page de selection du projet à modifier ou prévisualiser
        return $this->render('IUTOLivretBundle:Communication:communicationChooseLivret.html.twig', array(
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
            'livrets' => $livrets,
        ));
    }

    public function communicationModifLivretAction(Request $request, Livret $livret)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $formModif = $this->createForm(NewLivretType::class, $livret);
        $formModif->handleRequest($request);

        if ($formModif->isSubmitted() && $formModif->isValid()) {
            $em->persist($livret);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_choose_livret_projects', array(
                'livret' => $livret->getId(),
            ));
        }

        return $this->render('IUTOLivretBundle:Communication:communicationCreateLivret.html.twig', array(
            'formCreate' => $formModif->createView(),
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
        ));
    }

    public function communicationvalidationCRAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationvalidationCR.html.twig', array(
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'options' => array('Apercu du compte rendu', 'Renvoyer la correction aux élèves', 'Valider', 'Retour'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/generate/1', '#', '/communication/validation', 'communication/selection')));
    }

    public function communicationChoixAction()
    {
        $idUniv = phpCAS::getUser();
        $manager = $this->getDoctrine()->getManager();
        $dpt = $manager->getRepository(Departement::class)->findAll();
        $projets = $manager->getRepository(Projet::class)->findAll();
        $annee = array(1 => date("y"), 2 => date("y") - 1, 3 => date("y") - 2, 4 => date("y") - 3, 5 => date("y") - 4);
        $proms = array(1 => "1A", 2 => "2A", 3 => "AS", 4 => "LP");


        return $this->render('IUTOLivretBundle:Communication:communicationChoix.html.twig', array(
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'options' => array('Valider', 'Retour'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/generate/1', '/communication'),
            'dpt' => $dpt,
            'projets' => $projets,
            'annee' => $annee,
            'promos' => $proms,
        ));
    }

    public function communicationDeleteProjetAction(Request $request, Projet $projet)
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

            return $this->redirectToRoute('iuto_livret_communicationChoix', array());
        }

        return $this->render('IUTOLivretBundle:Communication:communicationConfirmProjetDelete.html.twig', array(
            'projet' => $projet,
            'form' => $form->createView(),
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
        ));
    }

    public function communicationSelectionlivretAction(Request $request)
    {
        $idUniv = phpCAS::getUser();

        $manager = $this->getDoctrine()->getManager();
        $livrets = $manager->getRepository(Livret::class)->findAll();
        $l = array();
        $session = $this->get('session');
        foreach ($livrets as $li) {
            $l[$li->getIntituleLivret()] = $li->getId();
        }

        $form = $this->createFormBuilder()
            ->add('choixLivret', ChoiceType::class, array(
                'label' => 'Choix du Livret',
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => 'true',
                    'name' => 'livret',
                    'title' => 'Aucun livret sélectionné'
                ],
                'choices' => $l
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Valider'
            ))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $session->set('numEdito', $ed = $form['choixLivret']->getData());
            return $this->redirectToRoute("iuto_livret_communicationEdito", array());
        }

        return $this->render('IUTOLivretBundle:Communication:communicationSelectionLivret.html.twig', array(
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
            'form' => $form->createView(),
            'routing_options' => array('/communication/edito', '/communication')
        ));

    }

    public function communicationCorrectionProjetAction(Request $request, Projet $projet)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();
        $user = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

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

        $formCorrect = $this->createForm(ProjetCompleteType::class, $projet, ['etudiants' => $etudiants, 'tuteurs' => $tuteurs]);

        // insertion des dates en string
        $formCorrect['dateDebut']->setData($projet->getDateDebut()->format('d/m/Y'));
        $formCorrect['dateFin']->setData($projet->getDateFin()->format('d/m/Y'));

        // attente d'action sur le formulaire
        $formCorrect->handleRequest($request);

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
        if ($formCorrect->isSubmitted() && $formCorrect->isValid()) {

//            création d'un nouveau projet afin d'enregistrer les
            $newProjet = new Projet();

//            récupération et affectation des données du projet dans le nouveau projet
            $newProjet->setIntituleProjet($projet->getIntituleProjet());
            $newProjet->setDescripProjet($projet->getDescripProjet());
            $newProjet->setBilanProjet($projet->getBilanProjet());
            $newProjet->setMarquantProjet($projet->getMarquantProjet());
            $newProjet->setMotsClesProjet($projet->getMotsClesProjet());
            $newProjet->setClientProjet($projet->getClientProjet());
            $newProjet->setValiderProjet($projet->getValiderProjet());
            $newProjet->setNomDpt($projet->getNomDpt());
            $newProjet->setImages($projet->getImages());
            $newProjet->setDescriptionClientProjet($projet->getDescriptionClientProjet());

//            actualisation du projet associé aux images du projet
            foreach ($newProjet->getImages() as $oneImg) {
                $oneImg->setProjet($newProjet);
            }

            // recupération des dates dans le formulaire
            $dateFormD = $formCorrect['dateDebut']->getData();
            $dateFormF = $formCorrect['dateFin']->getData();

            //affectations des date dans le formulaire au bon format
            //stockage de la date dans le projet
            $newProjet->setDateDebut(\DateTime::createFromFormat('d/m/Y', $dateFormD));
            $newProjet->setDateFin(\DateTime::createFromFormat('d/m/Y', $dateFormF));

//            récupération des étudiants et tuteurs selectionnées dans le formulaire
            $etus = $formCorrect['etudiants']->getData();
            $tuts = $formCorrect['tuteurs']->getData();

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
            return $this->redirectToRoute('iuto_livret_communication_wordImg_projet', array(
                    'statutCAS' => 'communication',
                    'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                    'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
                    'routing_statutCAShome' => '/communication',
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
            return $this->render('IUTOLivretBundle:Communication:communicationCorrectionProjet.html.twig', array(
                'formCorrect' => $formCorrect->createView(),
                'formCom' => $formCom->createView(),
                'statutCAS' => 'communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', 'communication/choose/edito'),
                'routing_statutCAShome' => '/communication',
                'projet' => $projet,
                'commentaires' => $commentaires,
            ));
        }


        // affichage du formulaire pour compléter le projet
        return $this->render('IUTOLivretBundle:Communication:communicationCorrectionProjet.html.twig', array(
                'formCorrect' => $formCorrect->createView(),
                'formCom' => $formCom->createView(),
                'statutCAS' => 'communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', 'communication/choose/edito'),
                'routing_statutCAShome' => '/communication',
                'projet' => $projet,
                'commentaires' => $commentaires,
            )
        );
    }

    public function communicationWordImgProjetAction(Request $request, Projet $projet)
    {
        //        récupération de l'entity manager
        $em = $this->getDoctrine()->getManager();
        //récupération des informations de l'utilisateur connecter
        $idUniv = phpCAS::getUser();
        $user = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

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
            return $this->render('IUTOLivretBundle:Communication:communicationWordImgProjet.html.twig', array(
                'formCom' => $formCom->createView(),
                'formMot' => $formMot->createView(),
                'statutCAS' => 'communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
                'routing_statutCAShome' => '/communication',
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
            return $this->render('IUTOLivretBundle:Communication:communicationWordImgProjet.html.twig', array(
                'formCom' => $formCom->createView(),
                'formMot' => $formMot->createView(),
                'statutCAS' => 'communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', 'communication/choose/edito'),
                'routing_statutCAShome' => '/communication',
                'projet' => $projet,
                'images' => $images,
                'motsCles' => $motsCles,
                'commentaires' => $commentaires,
                'logo' => $logo,
            ));
        }

//        rendu de la page d'ajout de mots clés et de d'images
        return $this->render('IUTOLivretBundle:Communication:communicationWordImgProjet.html.twig', array(
            'formCom' => $formCom->createView(),
            'formMot' => $formMot->createView(),
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', 'communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
            'projet' => $projet,
            'images' => $images,
            'motsCles' => $motsCles,
            'commentaires' => $commentaires,
            'logo' => $logo,
        ));
    }

    public function communicationAddImageAction(Request $request, Projet $projet)
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
//            vérification que la limite du nombre d'images est respectée
            $image->setProjet($projet);
            $em->persist($image);
            $em->flush();

            // redirection vers la page de prévisualisation ou de retour à l'accueil une fois le formulaire envoyer
            return $this->redirectToRoute('iuto_livret_communication_wordImg_projet', array(
                    'projet' => $projet->getId(),
                )
            );
        }
        return $this->render('IUTOLivretBundle:Communication:communicationAddImg.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
                'routing_statutCAShome' => '/communication',
                'projet' => $projet,
            )
        );
    }

    public function communicationDeleteLivretAction(Request $request, Livret $livret)
    {
        // récupération des inforamtions dur l'utilsateur connecté
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($livret->getProjets() as $curProj) {
                $curProj->removeLivret($livret);
                $em->persist($curProj);
            }

            $em->remove($livret);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le livret a bien été supprimé.");


            return $this->redirectToRoute('iuto_livret_communicationChoixLivret', array());
        }

        return $this->render('IUTOLivretBundle:Communication:confirmLivretDelete.html.twig', array(
            'livret' => $livret,
            'form' => $form->createView(),
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
        ));
    }

    public function communicationDeleteImageAction(Request $request, Image $image)
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


            return $this->redirectToRoute('iuto_livret_communication_wordImg_projet', array(
                'projet' => $projet->getId(),
            ));
        }


        return $this->render('IUTOLivretBundle:Communication:communicationConfirmImageDelete.html.twig', array(
            'image' => $image,
            'form' => $form->createView(),
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
            'projet' => $projet,
        ));
    }

    public function communicationAddLogoAction(Request $request, Projet $projet)
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
            return $this->redirectToRoute('iuto_livret_communication_wordImg_projet', array(
                    'projet' => $projet->getId(),
                )
            );
        }

        return $this->render('IUTOLivretBundle:Communication:communicationAddImg.html.twig', array(
                'form' => $form->createView(),
                'statutCAS' => 'communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
                'routing_statutCAShome' => '/communication',
                'projet' => $projet,
            )
        );
    }

    public function communicationCreateEditoAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $idUniv = phpCAS::getUser();

        $edito = new Edito();

        $formEdit = $this->createForm(EditoType::class, $edito);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {
            $manager->persist($edito);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_communication_choose_edito', array());
        }
        return $this->render('IUTOLivretBundle:Communication:communicationEdito.html.twig', array(
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
            'formCreate' => $formEdit->createView(),
        ));
    }

    public function communicationChooseEditoAction()
    {
        //récupération des informations sur l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $idUniv = phpCAS::getUser();

        // récuperation des projets d'un étudiant
        $editos = $em->getRepository('IUTOLivretBundle:Edito')->findAll();

        // affichage de la page de selection du projet à modifier ou prévisualiser
        return $this->render('IUTOLivretBundle:Communication:communicationChooseEdito.html.twig', array(
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
            'editos' => $editos,
        ));
    }

    public function communicationModifEditoAction(Request $request, Edito $edito)
    {
        $manager = $this->getDoctrine()->getManager();

        $idUniv = phpCAS::getUser();

        $formEdit = $this->createForm(EditoType::class, $edito);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {
            $manager->persist($edito);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_communication_choose_edito', array());
        }

        return $this->render('IUTOLivretBundle:Communication:communicationEdito.html.twig', array(
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
            'formCreate' => $formEdit->createView(),
        ));

    }

    public function communicationDeleteEditoAction(Request $request, Edito $edito)
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

            return $this->redirectToRoute('iuto_livret_communication_choose_edito', array());
        }

        return $this->render('IUTOLivretBundle:Communication:communicationConfirmEditoDelete.html.twig', array(
            'edito' => $edito,
            'form' => $form->createView(),
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Rechercher des projets', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/create/edito', '/communication/choose/edito'),
            'routing_statutCAShome' => '/communication',
        ));
    }

}
