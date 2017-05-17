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

	    $numPersonne = phpCAS::getUser();
	    $em = $this->getDoctrine()->getManager();
	    $user = $em->getRepository(User::class)->findOneByIdUniv($numPersonne);

        $role = $user->getRole();

	    if (strcmp($role, "ROLE_student") == 0)
        {
            return $this->redirectToRoute("iuto_livret_studenthomepage", array());
        }
        else if (strcmp($role, "ROLE_employee") == 0)
        {
            return $this->redirectToRoute("iuto_livret_communicationhomepage", array());
        }
        else if (strcmp($role, "ROLE_faculty") == 0)
        {
            return $this->redirectToRoute("iuto_livret_teacherhomepage", array());
        }
        else if (strcmp($role, "ROLE_affiliate") == 0)
        {
            return $this->redirectToRoute("iuto_livret_choose_module", array());
        }
        return $this->redirectToRoute("iuto_livret_public");
    }
}
