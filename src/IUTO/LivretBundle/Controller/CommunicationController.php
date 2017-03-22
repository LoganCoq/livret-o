<?php

namespace IUTO\LivretBundle\Controller;

use Doctrine\ORM\Query\Expr\Select;
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
            'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#'),
            'routing_statutCAShome' => '/communication',
            'routing_options' => array('/communication/generation', '#', '/communication/selectionlivret')));
    }

    public function communicationeditoAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationedito.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Visualiser','Valider'),
            'routing_statutCAShome' => '/communication',
            'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#'),
            'routing_options' => array('/communication/editoprevisualiser','/communication')));
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

    public function communicationSelectionlivretAction()
    {
        return $this->render('IUTOLivretBundle:Communication:communicationSelectionLivret.html.twig', array('statutCAS' => 'service de communication',
            'info' => array('Générer livrets', 'Créer un édito', 'Corriger des projets'),
            'options' => array('Valider', 'Retour'),
            'routing_info' => array('/communication/generation', '/communication/selectionlivret', '#'),
            'routing_statutCAShome' => '/communication',
            'livrets' => $this->getDoctrine()->getRepository('IUTOLivretBundle:Livret')->findAll(),
            'routing_options' => array('/communication/edito', '/communication')));

    }

    public function communicationLivretTransfererAction()
    {
        // On crée un objet Advert
        $livret = new CommunicationController();

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $livret);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('choixlivret',   Select::class)

            ->add('Valider',      SubmitType::class)
        ;

        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('IUTOLivretBundle:Communication:communicationSelectionLivret.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
