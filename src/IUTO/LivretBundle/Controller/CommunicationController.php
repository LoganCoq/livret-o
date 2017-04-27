<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Departement;
use IUTO\LivretBundle\Entity\Livret;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\NewLivretType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Form\EditoType;
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
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $user = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas

        return $this->render('IUTOLivretBundle:Communication:communicationhome.html.twig', array(
            'statutCAS' => 'service de communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
            'routing_statutCAShome' => '/communication',
            'options' => array('Créer un livret au format PDF', 'Voir les livrets', 'Corriger des projets', 'Créer l\'édito du directeur'),
            'routing_options' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '/communication/selectionlivret'),
            'user' => $user,
            )
        );
    }

    public function communicationeditoAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $user = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas


        $session = $this->get('session');

        $livret = $manager->getRepository(Livret::class)->findOneById($session->get("numEdito")); //TODO recuperation cas
        $form = $this->createForm(EditoType::class, $livret);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('submit')->isClicked()) {
                $ed = $form['editoLivret']->getData();
                $livret->setEditoLivret($ed);
                $manager->persist($livret);
                $manager->flush();
            }
            if ($form->get('previsualiser')->isClicked())
            {
                $session->set('edito',$ed = $form['editoLivret']->getData());
                return $this->redirectToRoute('iuto_livret_communicationEditoPrevisualiser');
            }
            return $this->redirectToRoute('iuto_livret_communicationhomepage');
        }

        return $this->render('IUTOLivretBundle:Communication:communicationedito.html.twig', array(
            'statutCAS' => 'service de communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
            'routing_statutCAShome' => '/communication',
            'form' => $form->createView()));
    }

    public function communicationCreateLivretAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $user = $manager->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas

        $newLivret = new Livret();

        $newLivret->setDateCreationLivret(new \DateTime());

        $formCreate = $this->createForm(NewLivretType::class, $newLivret);
        $formCreate->handleRequest($request);

        if ($formCreate->isSubmitted() && $formCreate->isValid())
        {
            $manager->persist($newLivret);
            $manager->flush();

            return $this->redirectToRoute( 'iuto_livret_communication_livret_project_choice', array(
                    'livretId' => $newLivret->getId(),
                    'statutCAS' => 'service de communication',
                    'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
                    'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
                    'options' => array('Visualiser'),
                    'routing_statutCAShome' => '/communication',
                    'routing_options' => array('/communication/editoprevisualiser'),
                )
            );
        }

        return $this->render('IUTOLivretBundle:Communication:communicationCreateLivret.html.twig', array(
                'statutCAS' => 'service de communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
                'routing_statutCAShome' => '/communication',
                'formCreate' => $formCreate->createView(),
            )
        );
    }

    public function communicationgenerationlivretAction(Request $request, Livret $livretId)
    {

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
                $livretId->addProjet($p);
                $p->addLivret($livretId);
                array_push($idProjs, $p->getId());
                $manager->persist($p);
            }

            $manager->persist($livretId);
            $manager->flush();

            return $this->redirectToRoute('iuto_livret_communicationChoixLivret', array(
                    'statutCAS' => 'service de communication',
                    'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
                    'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
                    'routing_statutCAShome' => '/communication',
                )
            );
        }

        return $this->render('IUTOLivretBundle:Communication:communicationgenerationlivret.html.twig', array(
            'form' => $form->createView(),
            'statutCAS' => 'service de communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
            'routing_statutCAShome' => '/communication',
        ));
    }

    public function communicationChooseLivretAction()
    {
        //récupération des informations sur l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $etudiant = $em->getRepository(User::class)->findOneByIdUniv($idUniv); //TODO recuperation cas
        $id = $etudiant->getId();

        // récuperation des projets d'un étudiant
        $livrets = $em->getRepository('IUTOLivretBundle:Livret')->findAll();

        // affichage de la page de selection du projet à modifier ou prévisualiser
        return $this->render('IUTOLivretBundle:Communication:communicationChooseLivret.html.twig',
            array('statutCAS' => 'communication',
                'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
                'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
                'routing_statutCAShome' => '/communication',
                'livrets' => $livrets,
            )
        );
    }

    public function communicationModifLivretAction(Request $request, Livret $livret)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = $this->container->get('security.token_storage')->getToken()->getUser();
        $user = $em->getRepository(User::class)->findOneByIdUniv($idUniv);

        $formModif = $this->createForm(NewLivretType::class, $livret);
        $formModif->handleRequest($request);

        if ( $formModif->isSubmitted() && $formModif->isValid() )
        {
            $em->persist($livret);
            $em->flush();

            return $this->redirectToRoute('iuto_livret_communicationChoixLivret', array(
                    'statutCAS' => 'communication',
                    'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
                    'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
                    'routing_statutCAShome' => '/communication',
                )
            );
        }

        return $this->render('IUTOLivretBundle:Communication:communicationCreateLivret.html.twig', array(
            'formCreate' => $formModif->createView(),
            'statutCAS' => 'communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
            'routing_statutCAShome' => '/communication',
        ));
    }

    public function communicationvalidationCRAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationvalidationCR.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
            'options' => array('Apercu du compte rendu', 'Renvoyer la correction aux élèves', 'Valider', 'Retour'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/generate/1', '#', '/communication/validation', 'communication/selection'))); // TODO "generate/1" a changer en id
    }

    public function communicationChoixAction()
    {

        return $this->render('IUTOLivretBundle:Communication:communicationChoix.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
            'options' => array('Valider', 'Retour'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/communication/validation', '/communication'),
        ));

    }

    public function communicationChoixValideAction()
    {

        $manager = $this->getDoctrine()->getManager();
        $dpt = $manager->getRepository(Departement::class)->findAll();
        $projets = $manager->getRepository(Projet::class)->findByValiderProjet(1);
        $annee = array(1 => date("y"), 2 => date("y") - 1, 3 => date("y") - 2, 4 => date("y") - 3, 5 => date("y") - 4);



        return $this->render('IUTOLivretBundle:Communication:communicationChoixValide.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
            'options' => array('Valider', 'Retour'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/generate/1', '/communication'),
            'dpt' => $dpt,
            'projets' => $projets,
            'annee' => $annee,
            ));
    }

    public function communicationChoixNValideAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $dpt = $manager->getRepository(Departement::class)->findAll();
        $projets = $manager->getRepository(Projet::class)->findByValiderProjet(0);
        $annee = array(1 => date("y"), 2 => date("y") - 1, 3 => date("y") - 2, 4 => date("y") - 3, 5 => date("y") - 4);



        return $this->render('IUTOLivretBundle:Communication:communicationChoixNValide.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
            'options' => array('Valider', 'Retour'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/generate/1', '/communication'),
            'dpt' => $dpt,
            'projets' => $projets,
            'annee' => $annee,
        ));
    }

    public function communicationSelectionlivretAction(Request $request)
    {
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
                    'choices' =>$l
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

        return $this->render('IUTOLivretBundle:Communication:communicationSelectionLivret.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Créer un livret', 'Voir les livrets', 'Corriger des projets', 'Créer un édito'),
            'routing_info' => array('/communication/create/livret', '/communication/chooseLivret', '/communication/selection', '#'),
                'routing_statutCAShome' => '/communication',
            'form' => $form->createView(),
                'routing_options' => array('/communication/edito', '/communication')));

        }


}
