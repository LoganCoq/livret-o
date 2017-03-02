<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Enty;
use IUTO\LivretBundle\Entity\Etudiant;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


class ConnexionController extends Controller
{
    public function connexionAction()
    {
        $numPersonne = "o2154952";
        $config = array(
            'host' => 'ldap-univ.iut45.univ-orleans.fr',
            'port' => 636,
            'version' => 3,
            'encryption' => 'ssl'
        );
        $AdapterInterface = new Adapter($config);
        $ldap = new Ldap($AdapterInterface);
        $ldap->bind();
        $infosPersonne = $ldap->query("ou=People,dc=univ-orleans,dc=fr","uid=".$numPersonne)->execute()->toArray()[0];

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("IUTOLivretBundle:User")->findByIdUniv($numPersonne);

        $manager = $this->getDoctrine()->getManager();
        if (!$user) {
            $user = new User();
            $user->setPrenomUser($infosPersonne->getAttribute("givenName")[0]);
            $user->setNomUser($infosPersonne->getAttribute("sn")[0]);
            $user->setMailUser($infosPersonne->getAttribute("mail")[0]);
            $user->setRole($infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0]);
            $user->setIdUniv($infosPersonne->getAttribute("uid")[0]);
            if ($user->getRole()=="student"){
                // TODO FORMATIONS
            }



            $manager->flush();
            $user = $userC;
        } else {
            // TODO vérifier avec LDAP si les infos sont à jours
        }
        $token = new UsernamePasswordToken($user->getNumEtu(), null, "main", $user->getRoles());
        $this->get("security.context")->setToken($token);

        $request = $this->get("request");
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

        if (strcmp($infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0],"student")==0){
            return $this->redirectToRoute("iuto_livret_studenthomepage");
        }
        else{
            return $this->redirectToRoute("iuto_livret_teacherhomepage");
        }
//
//
//        if ($etudiant==null){
//            $manager = $this->getDoctrine()->getManager();
//            $etudiant = new Etudiant();
//            $etudiant->setPrenomEtu($infosPersonne->getAttribute("givenName")[0]);
//            $etudiant->setNomEtu($infosPersonne->getAttribute("sn")[0]);
//            $etudiant->setMailEtu($infosPersonne->getAttribute("mail")[0]);
//
//            $manager->persist($etudiant);
//            $manager->flush();
//        }
//
//        if (strcmp($infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0],"student")==0){
//            return $this->redirectToRoute("iuto_livret_studenthomepage");
//        }
//        else{
//            return $this->redirectToRoute("iuto_livret_teacherhomepage");
//        }

    }
}
