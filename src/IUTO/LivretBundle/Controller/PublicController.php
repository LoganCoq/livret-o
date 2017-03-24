<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\Departement;


class PublicController extends Controller
{
    public function publicAction()
    {
        return $this->render('IUTOLivretBundle:Public:indexPublic.html.twig', array('routing_statutCAShome' => '/public', 'statutCAS' => 'Public',
            'info' => array('Voir les projets', "A propos de l'IUT'O"),
            'routing_info' => array('/public/projets', '/public/iuto')
        ));
    }

    public function projetsAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $projets = $manager->getRepository(Projet::class)->findAll();
        $dpt = $manager->getRepository(Departement::class)->findAll();
        $annee = array(1 => date("y"), 2 => date("y") - 1, 3 => date("y") - 2, 4 => date("y") - 3, 5 => date("y") - 4);

        return $this->render('IUTOLivretBundle:Public:projetsPublic.html.twig', array('routing_statutCAShome' => '/public', 'statutCAS' => 'Public',
            'info' => array('Voir les projets', "A propos de l'IUT'O"),
            'routing_info' => array('/public/projets', '/public/iuto'),
            'projets' => $projets,
            'dpt' => $dpt,
            'annee' => $annee
        ));
    }

    public function iutoAction()
    {
        return $this->render('IUTOLivretBundle:Public:iutoPublic.html.twig', array('routing_statutCAShome' => '/public', 'statutCAS' => 'Public',
            'info' => array('Voir les projets', "A propos de l'IUT'O"),
            'routing_info' => array('/public/projets', '/public/iuto')
        ));
    }
}
