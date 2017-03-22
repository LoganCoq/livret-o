<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PublicController extends Controller
{
    public function publicAction()
    {
        return $this->render('IUTOLivretBundle:Public:indexPublic.html.twig',array('routing_statutCAShome' => 'public','statutCAS' => 'Public',
            'info' => array('Voir les projets',"A propos de l'IUT'O"),
            'routing_info' => array('/public/projets','public/iuto')
            ));
    }
}
