<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Livret;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;

class CommunicationController extends Controller
{


    public function communicationhomeAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationhome.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Générer des livrets au format PDF', 'Corriger des projets', 'Créer l\'édito du directeur'),
            'routing_info' => array('/communication/generation', '/communication/edito', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/communication/generation', '#', '/communication/edito')));
    }

    public function communicationeditoAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationedito.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Visualiser','valider'),
            'routing_statutCAShome' => '/communication',
            'routing_info' => array('/communication/generation', '/communication/edito', '#'),
            'routing_options' => array('/communication/editoprevisualiser','/communication')));
    }

    public function communicationgenerationlivretAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationgenerationlivret.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'routing_statutCAShome' => '/communication',
            'routing_info' => array('/communication/generation', '/communication/edito', '#')));
    }

    public function communicationvalidationCRAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationvalidationCR.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Apercu du compte rendu', 'Renvoyer la correction aux élèves', 'Valider', 'Retour'),
            'routing_info' => array('#', '/communication/validation', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/generate/1', '#', '/communication/validation', 'communication/selection'))); //"generate/1" a changer en id
    }

    public function communicationChoixAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationChoix.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Valider', 'Retour'),
            'routing_info' => array('/communication/validation', '/communication', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/communication/validation', '/communication')));

    }

    public function communicationChoixValideAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationChoix.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Valider', 'Retour'),
            'routing_info' => array('/generate/1', '/communication', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/generate/1', '/communication')));

    }

    public function communicationSelectionlivretAction($id)
    {
        $livrects = $this->getDoctrine()->getRepository('IUTOLivretBundle:Livret')->findBy(['id' => $id]);
        return $this->render('IUTOLivretBundle:Communication:communicationSelectionLivret.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'routing_info' => array('#', '#', '#', '#', '#'),
            'routing_statutCAShome' => '/communication',
            'livrets' => $livrects));
    }
}
