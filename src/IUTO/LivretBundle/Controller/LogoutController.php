<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Ldap\Enty;
use Symfony\Component\HttpFoundation\Request;
use phpCAS;

class LogoutController extends Controller
{
    public function logoutAction(Request $request)
    {
	$this->get('security.token_storage')->setToken(null);
	$request->getSession()->invalidate();
//	\phpCAS::handleLogoutRequests();
//	\phpCAS::logoutWithRedirectService(urlencode($this->container->getParameter('cas_login_target')));
//	\phpCAS::logout();

	$target = urlencode($this->container->getParameter('cas_logout_target'));
        $url = 'https://'.$this->container->getParameter('cas_host') . '/logout?service=';
	
	return $this->redirect($url . $target);
    }

//    /**
//     * @Route("/force", name="force")
//     */
//    public function forceAction() {

//        if (!isset($_SESSION)) {
//            session_start();
//        }

//        session_destroy();

//        return $this->redirect($this->generateUrl('iuto_livret_homepage'));
//    }

}

