<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Ldap\LdapClient;


class TestController extends Controller
{
    public function testAction()
    {
        $ldap = new LdapClient('ldap-univ.iut45.univ-orleans.fr', 636, 3, false, true);
        $ldap->find('ou=People,dc=univ-orleans,dc=fr',"uid=o2150040");
        return $this->render('IUTOLivretBundle:Test:test.html.twig');
    }
}
