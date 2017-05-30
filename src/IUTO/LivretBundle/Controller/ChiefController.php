<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Departement;
use IUTO\LivretBundle\Entity\Edito;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\EditoType;
use IUTO\LivretBundle\Form\LivretChooseProjectsType;
use IUTO\LivretBundle\Form\LivretCreateType;
use IUTO\LivretBundle\Form\NewLivretType;
use IUTO\LivretBundle\Form\ProjetChiefCreateType;
use IUTO\LivretBundle\Form\ProjetCreateType;
use phpCAS;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetModifType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
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
            $request->getSession()->getFlashBag()->add('info', "Le livret a bien été supprimé.");


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

    public function correctionChief1Action()
    {
        $idUniv = phpCAS::getUser();
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $projets = $repository->findOneById($id)->getProjetSuivis();


        $projetsValides = array();
        foreach ($projets as $elem) {
            if ($elem->getValiderProjet() == 1)
                array_push($projetsValides, $elem);
        };


        return $this->render('IUTOLivretBundle:Chief:correctionChief1.html.twig', array('id' => $id,
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
            'routing_info' => array('/chef/create/livret', '#', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            'routing_statutCAShome' => '/chef',
            'pagePrec' => '/chef',
            'projets' => $projetsValides));

    }

    public function correctionChief2Action(Request $request, Projet $projet)
    {
        $idUniv = phpCAS::getUser();

        $form = $this->createForm(ProjetModifType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('');
        }

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $com = $repository->findByProjet($projet);

        $form2 = $this->createForm(CommentaireCreateType::class, $com);
        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('');
        }

        $idProjet = $projet->getId();

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

        return $this->render('IUTOLivretBundle:Chief:correctionChief2.html.twig',
            array('form' => $form->createView(),
                'formCom' => $form2->createView(),
                'statutCAS' => 'chef de département',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
                'routing_statutCAShome' => '/chef',
                'commentaires' => $commentaires,
            ));
    }

    public function correctionChief3Action(Request $request, Projet $projet)
    {
        $idUniv = phpCAS::getUser();

        $form = $this->createForm(ProjetContenuType::class, $projet);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('');
        }

        $idProjet = $projet->getId();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $commentaires = $repository->findOneByProjet($idProjet);
        $idProjet = $projet->getId();

        return $this->render('IUTOLivretBundle:Chief:correctionChief3.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'Chef de département',
                'commentaires' => $commentaires,
                'routing_statutCAShome' => '/chef',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
            ));
    }

    public function correctionChief4Action(Request $request, Projet $projet)
    {
        $idUniv = phpCAS::getUser();

        $idProjet = $projet->getId();

        return $this->render('IUTOLivretBundle:Chief:correctionChief4.html.twig',
            array(
                'statutCAS' => 'chef de département',
                'routing_statutCAShome' => '/chef',
                'info' => array('Générer livrets', 'Créer un projet', 'Créer un édito', 'Voir les éditos', 'Voir les livrets', 'Voir les projets'),
                'routing_info' => array('/chef/create/livret', '/chef/create/projet', '/chef/create/edito', '/chef/choose/edito', '/chef/choose/livret', '/chef/choose/projet'),
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
            return $this->redirectToRoute('iuto_livret_chiefhomepage', array(
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

} 
