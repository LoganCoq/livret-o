<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
//    Controller pour l'affichage de la page d'accueil
    public function indexAction()
    {
//        On met deux route en paramètres:
//        /login pour passer par l'authentification et /public pour accéder au module public
        return $this->render('IUTOLivretBundle:Home:index.html.twig', array('onclick' => '/login', 'modulePublic' => '/public'));
    }

    public function chooseModuleAction()
    {
        $idUniv = \phpCAS::getUser();

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneByIdUniv($idUniv);
        $roles = $user->getRoles();

        $role_stud = in_array('ROLE_student', $roles);
        $role_empl = in_array('ROLE_employee', $roles);
        $role_facu = in_array('ROLE_faculty', $roles);
        $role_admi = in_array('ROLE_admin', $roles);
        $role_chie = in_array('ROLE_chief', $roles);

        return $this->render('IUTOLivretBundle:Home:chooseModule.html.twig', array(
            'statutCAS' => 'modules',
            'info' => array('Choix module'),
            'routing_info' => array('/modules'),
            'routing_statutCAShome' => '/modules',
            'ROLE_student' => $role_stud,
            'ROLE_employee' => $role_empl,
            'ROLE_faculty' => $role_facu,
            'ROLE_admin' => $role_admi,
            'ROLE_chief' => $role_chie,
            'user' => $user,
        ));
    }
}
