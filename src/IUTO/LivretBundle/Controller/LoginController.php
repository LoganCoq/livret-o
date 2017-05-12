<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller
{
//    Action pour la redirection vers CAS lors de l'acces a la page /login
    public function loginAction()
    {
//        On récupérer la route que l'on veux accéder apres l'authentification CAS
        $target = urlencode($this->container->getParameter('cas_login_target'));
//        On récupère l'url d'accès à la connexion CAS
        $url = 'https://'.$this->container->getParameter('cas_host') . '/login?service=';

//        On redirige l'utilisateur vers la connection cas en précisant la route vers laquelle CAS va nous redirigé
        return $this->redirect($url . $target );
    }

    /**
     * @Route("/force", name="force")
     */
    public function forceAction() {

        if (!isset($_SESSION)) {
            session_start();
        }

        session_destroy();

        return $this->redirect($this->generateUrl('iuto_livret_login'));
    }

}
