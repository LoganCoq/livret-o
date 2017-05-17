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
        //CONNECTION SANS CAS : decommenter le numero avec lequel on souhaite se connecter

//        -----PROFESSEUR-----
//        $numPersonne = "p2171";
//        $numPersonne = "p7184";
//        $numPersonne = "p22732";
//        $numPersonne = "p46975";
//        $numPersonne = "p15987";

//        -----ETUDIANT-----
//        $numPersonne = "o2151178";
//        $numPersonne = "o2154952";
//        $numPersonne = "o2151485";
//        $numPersonne = "o2153164";
//        $numPersonne = "o2151841";
//        $numPersonne = "o2150380";


//        -----COMMUNICATION-----
//        $numPersonne = "p51955";

	$numPersonne = phpCAS::getUser();
        $config = array(
            'host' => 'ldap-univ.iut45.univ-orleans.fr',
            'port' => 636,
            'version' => 3,
            'encryption' => 'ssl'
        );
        $AdapterInterface = new Adapter($config);
        $ldap = new Ldap($AdapterInterface);
        $ldap->bind();
        $infosPersonne = $ldap->query("ou=People,dc=univ-orleans,dc=fr", "uid=" . $numPersonne)->execute()->toArray()[0];


        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("IUTOLivretBundle:User")->findOneByIdUniv($numPersonne);

//        $token = new UsernamePasswordToken($user->getIdUniv(), null, "main", $user->getRoles());
//        $this->get("security.token_storage")->setToken($token);

//        $request = new Request();
//        $event = new InteractiveLoginEvent($request, $token);
//        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        $id = $em->getRepository("IUTOLivretBundle:User")->findOneByIdUniv($numPersonne)->getId();

	if (strcmp($infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0], "student") == 0)
        {
//            if (strcmp($primaryaffil, "student") == 0)
            return $this->redirectToRoute("iuto_livret_studenthomepage", array("id" => $id));
        }
        else if (strcmp($infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0], "employee") == 0)
        {
            return $this->redirectToRoute("iuto_livret_communicationhomepage", array("id" => $id));
        }
        else if (strcmp($infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0], "faculty") == 0)
        {
            return $this->redirectToRoute("iuto_livret_teacherhomepage", array("id" => $id));
        }
        else if (strcmp($infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0], "affiliate") == 0)
        {
            return $this->redirectToRoute("iuto_livret_choose_module", array("id" => $id));
        }
        return $this->redirectToRoute("iuto_livret_public");
    }
}
