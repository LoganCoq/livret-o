<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Enty;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Entity\Formation;
use IUTO\LivretBundle\Entity\Departement;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;


class LogoutController extends Controller
{
    public function logoutAction()
    {
        return $this->redirectToRoute("iuto_livret_homepage");
    }
}
