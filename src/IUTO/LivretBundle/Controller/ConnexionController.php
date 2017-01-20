<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Enty;
use IUTO\LivretBundle\Entity\Etudiant;


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

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Etudiant');
        $etudiant = $repository->findByMailEtu($infosPersonne->getAttribute("mail"));

        if ($etudiant==null){
            $manager = $this->getDoctrine()->getManager();
            $etudiant = new Etudiant();
            $etudiant->setPrenomEtu($infosPersonne->getAttribute("givenName")[0]);
            $etudiant->setNomEtu($infosPersonne->getAttribute("sn")[0]);
            $etudiant->setMailEtu($infosPersonne->getAttribute("mail")[0]);

            $manager->persist($etudiant);
            $manager->flush();
        }

        if (strcmp($infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0],"student")==0){
            return $this->redirectToRoute("iuto_livret_studenthomepage");
        }
        else{
            return $this->redirectToRoute("iuto_livret_teacherhomepage");
        }

    }
}
