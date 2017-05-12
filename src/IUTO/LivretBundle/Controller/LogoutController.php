<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Ldap\Enty;
use Symfony\Component\HttpFoundation\Request;
use phpCAS;

class LogoutController extends Controller
{
//    Action pour la déconnexion de l'utilisateur
    public function logoutAction(Request $request)
    {
//      On met le token de sécurité a null
	    $this->get('security.token_storage')->setToken(null);
//	    On invalide la session afin de déconnectée l'utilisateur
	    $request->getSession()->invalidate();

//        On récupére la route vers laquelle CAS va nous redirigé après le logout
	    $target = urlencode($this->container->getParameter('cas_logout_target'));
//	      On récupére l'url de déconnexion CAS
        $url = 'https://'.$this->container->getParameter('cas_host') . '/logout?service=';
//        On redirige vers l'url de déconnexion cas en précisant vers quelle route l'utilisateur
//        va être rédirigé après sa déconnexion
	    return $this->redirect($url . $target);
    }


}
