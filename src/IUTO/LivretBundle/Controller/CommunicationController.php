<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CommunicationController extends Controller
{
    public function communicationhomeAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationhome.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Générer des livrets au format PDF', 'Corriger des projets', 'Créer l\'édito du directeur'),
            'routing_info' => array('/communication/generation', '/communication/edito', '#'),
            'routing_options' => array('/communication/generation', '#', '/communication/edito')));
    }

    public function communicationeditoAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationedito.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'routing_info' => array('/communication/generation', '/communication/edito', '#')));
    }

    public function communicationgenerationlivretAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationgenerationlivret.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'routing_info' => array('/communication/generation', '/communication/edito', '#')));
    }
}
