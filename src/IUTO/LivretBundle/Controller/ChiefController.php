<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Departement;
use IUTO\LivretBundle\Entity\Edito;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\EditoType;
use IUTO\LivretBundle\Form\LivretChooseProjectsType;
use IUTO\LivretBundle\Form\LivretCreateType;
use IUTO\LivretBundle\Form\NewLivretType;
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
            'info' => array('Générer livrets', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'options' => array('Générer un livret au format pdf', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
            'routing_options' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
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

        if ( $formCreate->isSubmitted() && $formCreate->isValid())
        {
            $manager->persist($newLivret);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_chief_newLivret_selectOptions', array(
                'livret' => $newLivret->getId(),
            ));
        }

        return $this->render('IUTOLivretBundle:Chief:chiefCreateLivret.html.twig', array(
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
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

//            TODO projets marquants
            $qb = $repositoryProjet->createQueryBuilder('p');
            $qb->where('p.dateDebut > :dateDebut')
                ->setParameter('dateDebut', $dateDebutSelection)
                ->andWhere('p.dateFin < :dateFin')
                ->setParameter('dateFin', $dateFinSelection)
                ->andWhere('p.validerProjet = 1');

            $projets = $qb->getQuery()->getResult();
            $livretProjets =array();
            foreach ( $projets as $curProj )
            {
                $curFormation = $curProj->getEtudiants()[0]->getFormations()[0];
                $curDept = $curFormation->getDepartement()->getNomDpt();
                $curTypeFormation = $curFormation->getTypeFormation();

                foreach ( $formationsSelectionnes as $curFormSelected )
                {
                    if ( $curFormSelected == $curTypeFormation)
                    {
                        foreach ( $departementsSelectionnes as $curDeptSelected )
                        {
                            if ( $curDeptSelected == $curDept)
                            {
                                array_push($livretProjets, $curProj);
                            }
                        }
                    }
                }
            }

            $idProjs = array();
            foreach ($livretProjets as $p)
            {
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
            'info' => array('Générer livrets', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
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

        if ($form->isSubmitted() && $form->isValid())
        {
            $newLivret = new Livret();

            $newLivret->setIntituleLivret($livret->getIntituleLivret());
            foreach ($livret->getEditos() as $edito) {
                $newLivret->addEdito($edito);
            }

            $newLivret->setDateCreationLivret($livret->getDateCreationLivret());

            $newProjects = $form['projects']->getData();

            foreach ($newProjects as $curPro)
            {
                $newLivret->addProjet($curPro);
                $curPro->addLivret($newLivret);
                $em->persist($curPro);
            }

            $em->persist($newLivret);
            $em->remove($livret);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_chief_choose_livret', array(
            ));
        }

        return $this->render('IUTOLivretBundle:Chief:chiefChooseLivretProjects.html.twig', array(
            'livret' => $livret,
            'form' => $form->createView(),
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
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
            'info' => array('Générer livrets', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
            'routing_statutCAShome' => '/chef',
            'livrets' => $livrets,
        ));
    }

    public function chiefDeleteLivretAction(Request $request, Livret $livret)
    {
        $idUnic = \phpCAS::getUser();

        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid())
        {
            foreach ($livret->getProjets() as $curProjet)
            {
                $curProjet->removeLivret($livret);
                $em->persist($curProjet);
            }

            $em->remove($livret);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le livret a bien été supprimé.");


            return $this->redirectToRoute('iuto_livret_chief_choose_livret', array(
            ));
        }

        return $this->render('IUTOLivretBundle:Chief:chiefDeleteLivret.html.twig', array(
            'livret' => $livret,
            'form'   => $form->createView(),
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
            'routing_statutCAShome' => '/chef',
        ));
    }

    public function chiefChooseProjectAction()
    {
        $idUniv = \phpCAS::getUser();
        $manager = $this->getDoctrine()->getManager();

        $repository = $manager->getRepository('IUTOLivretBundle:User');

        $projets = $manager->getRepository(Projet::class)->findAll();
        $dpt = $manager->getRepository(Departement::class)->findAll();
        $annee = array(1 => date("y"), 2 => date("y") - 1, 3 => date("y") - 2, 4 => date("y") - 3, 5 => date("y") - 4);
        $proms = array(1 => "1A", 2 => "2A", 3 => "AS", 4 => "LP");

        return $this->render('IUTOLivretBundle:Chief:chiefliste.html.twig',array(
            'routing_statutCAShome' => '/chef',
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
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
            'info' => array('Générer livrets', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
            'editos' => $editos,
        ));
    }

    public function chiefCreateEditoAction(Request $request)
    {
        $idUniv = \phpCAS::getUser();
        $manager = $this->getDoctrine()->getManager();

        $newEdito = new Edito();

        $formCreate = $this->createForm(EditoType::class, $newEdito);
        $formCreate->handleRequest($request);

        if($formCreate->isSubmitted() && $formCreate->isValid())
        {
            $manager->persist($newEdito);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_chief_choose_edito', array(

            ));
        }

        return $this->render('IUTOLivretBundle:Chief:chiefEdito.html.twig', array(
            'routing_statutCAShome' => '/chef',
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Voir les livrets', 'Voir les projets', 'Créer un projet', 'Créer un édito', 'Voir les éditos'),
            'routing_info' => array('/chef/create/livret', '/chef/choose/livret', '/chef/choose/projet', '#', '/chef/create/edito', '/chef/choose/edito'),
            'form' => $formCreate->createView(),
        ));
    }

    public function correctionChief1Action()
    {
        $idUniv = \phpCAS::getUser();
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $projets = $repository->findOneById($id)->getProjetSuivis();


        $projetsValides = array();
        foreach($projets as $elem){
            if ($elem->getValiderProjet() == 1)
                array_push($projetsValides, $elem);
        };


        return $this->render('IUTOLivretBundle:Chief:correctionChief1.html.twig', array('id' => $id,
            'statutCAS' => 'chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_info' => array('#', '/chef/presentation', '#', '/chef/Info/liste', '#'),
            'routing_statutCAShome' => '/chef',
            'pagePrec' => '/chef',
            'projets' => $projetsValides));

    }

    public function correctionChief2Action(Request $request, Projet $projet)
    {
        $idUniv = \phpCAS::getUser();

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
        foreach($com as $elem){
            $x=array();
            $user = $elem->getUser();
            array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
            array_push($x, $elem->getContenu());
            array_push($x, $elem->getDate());
            array_push($x, $user->getRole());
            array_push($commentaires, $x);

        };

        return $this->render('IUTOLivretBundle:Chief:correctionChief2.html.twig',
            array('form' => $form->createView(),
                'formCom' => $form2->createView(),
                'statutCAS' => 'chef de département',
                'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
                'routing_statutCAShome' => '/chef',
                'commentaires' => $commentaires,
                'routing_info' => array('#','/chef/correctionChief1','#', '/chef/projetsValidesChief1','#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/chef/correctionChief1',
                'pageSuiv' => '/chef/'.$idProjet.'/correctionChief3'
            ));
    }

    public function correctionChief3Action(Request $request, Projet $projet)
    {
        $idUniv = \phpCAS::getUser();

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
                'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
                'routing_info' => array('#','/chef/correctionChief1','#', '/chef','#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/chef/'.$idProjet.'/correctionChief2',
                'pageSuiv' => '/chef/'.$idProjet.'/correctionChief4'
            ));
    }

    public function correctionChief4Action(Request $request, Projet $projet)
    {
        $idUniv = \phpCAS::getUser();

        $idProjet = $projet->getId();

        return $this->render('IUTOLivretBundle:Chief:correctionChief4.html.twig',
            array('id' => $id,
                'statutCAS' => 'chef de département',
                'routing_statutCAShome' => '/chef',
                'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
                'routing_info' => array('#','/chef/correctionChief1','#', '/chef','#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/chef/'.$idProjet.'/correctionChief3'
            ));
    }

    public function genLivretChiefAction()
    {
        $idUniv = \phpCAS::getUser();

        return $this->render('IUTOLivretBundle:Chief:genLivretChief.html.twig', array('statutCAS' => 'Chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'Projets du département', 'Ajouter un projet'),
            'routing_statutCAShome' => '/chef',
            'routing_info' => array('/chef/generation', '/chef/edito', '#')));
    }

    public function presentationDptAction()
    {
        $idUniv = \phpCAS::getUser();

        return $this->render('IUTOLivretBundle:Chief:presentationDpt.html.twig', array('statutCAS' => 'Chef de département',
            'info' => array('Générer livrets', 'Présentation département', 'Sélection des projets', 'projets du département', 'Ajouter un projet'),
            'options' => array('Visualiser','valider'),
            'routing_statutCAShome' => '/chef',
            'routing_info' => array('/communication/generation', '/chef/edito', '#'),
            'routing_options' => array('/chef/editoprevisualiser','/chef')));

    }
} 
