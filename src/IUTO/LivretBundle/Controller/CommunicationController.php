<?php

namespace IUTO\LivretBundle\Controller;

use Doctrine\ORM\Query\Expr\Select;
use IUTO\LivretBundle\Entity\Livret;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use IUTO\LivretBundle\Form\EditoType;
use IUTO\LivretBundle\Form\ProjetCreateType;
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
        return $this->render('IUTOLivretBundle:Communication:communicationhome.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Générer des livrets au format PDF', 'Corriger des projets', 'Créer l\'édito du directeur'),
            'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/communication/generation', '#', '/communication/selectionlivret')));
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

    public function communicationgenerationlivretAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationgenerationlivret.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'routing_statutCAShome' => '/communication',
            'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#')));
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
                        'class' => 'selectpicker departement',
                        'data-live-search' => 'true',
                        'name' => 'livret',
                        'data-selected-text-format' => 'count > 5',
                        'data-count-selected-text' => 'Tous les livrets',
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
