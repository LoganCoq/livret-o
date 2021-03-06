<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use phpCAS;

class ConnexionController extends Controller
{
    public function connexionAction()
    {
//        Récupération de l'utilisateur connecté et à rediriger
        $numPersonne = phpCAS::getUser();

        $em = $this->getDoctrine()->getManager();
//	    Récupération de l'utilisateur grâce a son numéro universitaire
        $user = $em->getRepository(User::class)->findOneByIdUniv($numPersonne);
//        Récupération du role de l'utilisateur pour effectuer la redirection
        $roles = $user->getRoles();

        if (count($roles) > 1) {

            return $this->redirectToRoute("iuto_livret_choose_module", array());
        } else {
            //        Vérificate du role de l'utilisateur et redirection suivant celui-ci
            if (strcmp($roles[0], "ROLE_student") == 0) {
                return $this->redirectToRoute("iuto_livret_studenthomepage", array());
            } else if (strcmp($roles[0], "ROLE_employee") == 0) {
                return $this->redirectToRoute("iuto_livret_communicationhomepage", array());
            } else if (strcmp($roles[0], "ROLE_faculty") == 0) {
                return $this->redirectToRoute("iuto_livret_teacherhomepage", array());
            } else if (strcmp($roles[0], "ROLE_affiliate") == 0) {
                return $this->redirectToRoute("iuto_livret_choose_module", array());
            }
        }
        return $this->redirectToRoute("iuto_livret_public");
    }
}
