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

//        $corresLDAP = array(
//            'IO1320' => array('1A', 'Chimie'),
//            'IO1321' => array('2A', 'Chimie'),
//            'IO1330' => array('1A', 'GMP'),
//            'IO1331' => array('2A', 'GMP'),
//            'IO1340' => array('1A', 'Informatique'),
//            'IO1341' => array('2A', 'Informatique'),
//            'IO1342' => array('AS', 'Informatique'),
//            'IO1350' => array('1A', 'QLIO'),
//            'IO1351' => array('2A', 'QLIO'),
//            'IO1360' => array('1A', 'GEA'),
//            'IO1361' => array('2AGCF', 'GEA'),
//            'IO1364' => array('AS', 'GEA'),
//            'IO1362' => array('2AGMO', 'GEA'),
//            'IO1D62' => array('2AGMOD', 'GEA'),
//            'IO1363' => array('2AGRH', 'GEA'),
//            'IO1370' => array('1A', 'GTE'),
//            'IO1371' => array('2A', 'GTE'),
//            'ILPO28' => array('LPRT', 'Informatique'),
//            'ILPO25' => array('LPPCPC', 'Chimie'),
//            'ILPO24' => array('LPCF', 'Chimie'),
//            'ILPO23' => array('LPCAC', 'Chimie'),
//            'ILPO26' => array('LPCSACE', 'GMP'),
//            'ILPO27' => array('LPCPA', 'GMP'),
//            'ILPO22' => array('LPGPI', 'QLIO'),
//            'ILPO20' => array('LPGRH', 'GEA'),
//            'ILPO29' => array('LPMCF', 'GEA'),
//            'ILPO21' => array('LPEBSI', 'GTE'),
//        );
//        $primaryaffil = 'student';

//        if (!$user) {
//            $user = new User();

//            $user -> setPrenomUser("Logan");
//            $user -> setNomUser("Coquerant");
//            $user -> setMailUser("logan.coquerant@etu.univ-orleans.fr");
//            $user -> setRole("ROLE_student");
//            $user -> setIdUniv("o2151179");
//            $primaryaffil = 'student';

//             $user->setPrenomUser($infosPersonne->getAttribute("givenName")[0]);
//             $user->setNomUser($infosPersonne->getAttribute("sn")[0]);
//             $user->setMailUser($infosPersonne->getAttribute("mail")[0]);
//             $user->setRole("ROLE_" . $infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0]);
//             $user->setIdUniv($infosPersonne->getAttribute("uid")[0]);

//            if ($user->getRole() == "ROLE_student")
//            {
//                 $codeFormation = $infosPersonne->getAttribute("unrcEtape")[0];
//                $codeFormation = 'IO1341';

//                $infForm = $corresLDAP[$codeFormation];
//                $formation = $em->getRepository(Formation::class)->findOneBy(array("departement" => ($em->getRepository(Departement::class)->findOneByNomDpt($infForm[0])), "typeFormation" => $infForm[0]));
//                if (!$formation)
//                {
//                    $newF = new Formation();
//                    $newF->setTypeFormation($infForm[0]);
//                    $newF->setDepartement($em->getRepository("IUTOLivretBundle:Departement")->findOneByNomDpt($corresLDAP[$codeFormation][1]));
//                    $dDeb = new \DateTime();
//                    $dFin = new \DateTime();
//                    if (date("m") < 9)
//                    {
//                        $dDeb->setDate(date("y"), 1, 15);
//                        $dFin->setDate(date("y"), 8, 15);
//                        $newF->setSemestre(2);
//
//                    }
//                    else
//                    {
//                        $dDeb->setDate(date("y") - 1, 8, 16);
//                        $dFin->setDate(date("y"), 1, 14);
//                        $newF->setSemestre(1);
//                    }
//                    $newF->setDateDebut($dDeb);
//                    $newF->setDateFin($dFin);
//                    $formation = $newF;
//                    $em->persist($formation);
//                }
//                $user->addFormation($formation);
//                $formation->addUser($user);
//            }

//            $em->persist($user);
//            $em->flush();

//        } else {
            // TODO vérifier avec LDAP si les infos sont à jours
//        }
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
            return $this->redirectToRoute("iuto_livret_choose_module", array("id" => $id, 'statutCAS' => 'test',
                'info' => array('#'),
                'routing_info' => array('#'),
                'routing_statutCAShome' => '/modules',));
        }
        return $this->redirectToRoute("iuto_livret_public");
    }
}
