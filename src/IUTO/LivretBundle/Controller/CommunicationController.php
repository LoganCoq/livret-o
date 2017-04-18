<?php

namespace IUTO\LivretBundle\Controller;

use Doctrine\ORM\Query\Expr\Select;
use IUTO\LivretBundle\Entity\Livret;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use IUTO\LivretBundle\Form\EditoType;
use IUTO\LivretBundle\Form\ProjetCreateType;
use IUTO\LivretBundle\Form\LivretCreateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;

class CommunicationController extends Controller
{


    public function communicationhomeAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationhome.html.twig', array(
            'statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Générer des livrets au format PDF', 'Corriger des projets', 'Créer l\'édito du directeur'),
            'routing_info' => array('/communication/generation', '/communication/selection', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/communication/generation', '/communication/selection', '/communication/selectionlivret')));
    }

    public function communicationeditoAction(Request $request)
    {
        $session = $this->get('session');
        $manager = $this->getDoctrine()->getManager();
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




        return $this->render('IUTOLivretBundle:Communication:communicationedito.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Visualiser'),
            'routing_statutCAShome' => '/communication',
            'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#'),
            'routing_options' => array('/communication/editoprevisualiser'),
            'form' => $form->createView()));
    }

    public function communicationgenerationlivretAction(Request $request2)
    {
        $form = $this->createForm(LivretCreateType::class);
        $form->handleRequest($request2);
        $manager = $this
            ->getDoctrine()
            ->getManager();
        $repository = $manager
            ->getRepository('IUTOLivretBundle:Projet');

        $livret = new Livret();
        $livret->setIntituleLivret("livret test");
        $livret->setDateCreationLivret(new \DateTime());
        $livret->setEditoLivret("Si si la pharmacie ça marche");
        //creation d'un nouveau livret pour la bd

        if ($form->isSubmitted() && $form->isValid()) {
          $dateDebutSelection = $form["dateDebut"]->getData();
          $dateFinSelection = $form["dateFin"]->getData();
          $formationsSelectionnes = $form["listeFormation"]->getData();
          $departementsSelectionnes = $form["listeDepartements"]->getData();

          $projets = $repository->findAll();
          //recuperation de tous les projets de la BD

          $html2pdf = $this->get('app.html2pdf');
          $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));
          //preparation du PDF
          echo "1";

          foreach ($projets as $projet) {
            $toutesLesFormations = $projet->getEtudiants()[0]->getFormations();
            foreach ($toutesLesFormations as $formation) {

              foreach ($formationsSelectionnes as $formationSelectionnee) {
                if($formation->getTypeFormation() == $formationSelectionnee){
                  //chaque projet qui a le bon type de formation

                  echo "2";

                  $dateDeFormation = $formation->getDateDebut();
                  if(($dateDeFormation>=$dateDebutSelection)&&($dateDeFormation<=$dateFinSelection)){
                    //chaque projet qui a le bon type de formation à la bonne date
                    echo "3";

                    foreach ($departementsSelectionnes as $departementSelectionne) {
                      if ($formation->getDepartement()->getNomDpt()==$departementSelectionne) {
                        //chaque projet qui a le bon type de formation à la bonne date et le bon department

                        echo "ok";
                        $nomP = $projet->getIntituleProjet();
                        $descripP = $projet->getDescripProjet();
                        $bilanP = $projet->getBilanProjet();
                        $clientP = $projet->getClientProjet();
                        $etudiants = $projet->getEtudiants();
                        $tuteurs = $projet->getTuteurs();
                        $departement = $formation->getDepartement()->getNomDpt();
                        //recuperation des infos du projet

                        $projet->addLivret($livret);
                        $livret->addProjet($projet);
                        //ajout de la relation livret/projet

                        $template = $this->renderView('::pdf.html.twig',
                            ['nom' => $nomP,
                                'descrip' => $descripP,
                                'bilan' => $bilanP,
                                'client' => $clientP,
                                'etudiants' => $etudiants,
                                'tuteurs' => $tuteurs,
                                'departement' => $departement,
                                'formation' => $formation->getTypeFormation()
                            ]);
                            //creation du template

                        $html2pdf->create($template);
                        //ajout de la page au livret
                      }
                    }
                  }
                }
              }
            }
          }
        }
        $manager->persist($livret);
        $manager->flush();
        // return $this->render('IUTOLivretBundle:Communication:communicationgenerationlivret.html.twig', array('form' => $form->createView(), 'statutCAS' => 'service de communication',
        //     'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
        //     'routing_statutCAShome' => '/communication',
        //     'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#')));
    }

    public function communicationvalidationCRAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationvalidationCR.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Apercu du compte rendu', 'Renvoyer la correction aux élèves', 'Valider', 'Retour'),
            'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/generate/1', '#', '/communication/validation', 'communication/selection'))); //"generate/1" a changer en id
    }

    public function communicationChoixAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationChoix.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Valider', 'Retour'),
            'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/communication/validation', '/communication')));

    }

    public function communicationChoixValideAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationChoix.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Valider', 'Retour'),
            'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/generate/1', '/communication')));

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
                'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
                'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#'),
                'routing_statutCAShome' => '/communication',
            'form' => $form->createView(),
                'routing_options' => array('/communication/edito', '/communication')));

        }


}
