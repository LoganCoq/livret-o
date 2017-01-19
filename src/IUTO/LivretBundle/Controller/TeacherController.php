<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class TeacherController extends Controller
{
    public function teacherhomeAction()
    {
        return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig', array('statutCAS' => 'professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'options' => array('Voir les demande de correction de projets', 'Voir les projets validés'),
            'routing_info' => array('#', '#'),
            'routing_options' => array('#', '#')));
    }

    public function correctionTeacher1Action($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Personnel');
        $projets = $repository->findOneById($id)->getProjets();

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher1.html.twig', array('id' => $id, 'statutCAS' => 'professeur', 'projets' => $projets, 'info' => array('Demandes de correction', 'Projets validés'), 'options' => array('Voir les demande de correction de projets', 'Voir les projets validés')));


    }
}
