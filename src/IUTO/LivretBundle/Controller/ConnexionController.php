<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Departement;
use IUTO\LivretBundle\Entity\Formation;
use IUTO\LivretBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
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

        if (count($roles) > 1)
        {
            $role_stud = in_array('ROLE_student', $roles);
            $role_empl = in_array('ROLE_employee', $roles);
            $role_facu = in_array('ROLE_faculty', $roles);
            $role_admi = in_array('ROLE_admin', $roles);
            $role_chie = in_array('ROLE_chief', $roles);

            return $this->redirectToRoute("iuto_livret_choose_module", array(
                'ROLE_student' => $role_stud,
                'ROLE_employee' => $role_empl,
                'ROLE_faculty' => $role_facu,
                'ROLE_admin' => $role_admi,
                'ROLE_chief' => $role_chie,
            ));
        } else {
            //        Vérificate du role de l'utilisateur et redirection suivant celui-ci
            if (strcmp($roles[0], "ROLE_student") == 0)
            {
                return $this->redirectToRoute("iuto_livret_studenthomepage", array());
            }
            else if (strcmp($roles[0], "ROLE_employee") == 0)
            {
                return $this->redirectToRoute("iuto_livret_communicationhomepage", array());
            }
            else if (strcmp($roles[0], "ROLE_faculty") == 0)
            {
                return $this->redirectToRoute("iuto_livret_teacherhomepage", array());
            }
            else if (strcmp($roles[0], "ROLE_affiliate") == 0)
            {
                return $this->redirectToRoute("iuto_livret_choose_module", array());
            }
        }
        return $this->redirectToRoute("iuto_livret_public");
    }
}
