<?php

namespace IUTO\LivretBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use IUTO\LivretBundle\Entity\Departement;
use IUTO\LivretBundle\Entity\Formation;
use IUTO\LivretBundle\Entity\User;
use phpCAS;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
      $config = array(
            'host' => 'ldap-univ.iut45.univ-orleans.fr',
            'port' => 636,
            'version' => 3,
            'encryption' => 'ssl'
        );
        $AdapterInterface = new Adapter($config);
        $ldap = new Ldap($AdapterInterface);
        $ldap->bind();
        $infosPersonne = $ldap->query("ou=People,dc=univ-orleans,dc=fr", "uid=" . $username)->execute()->toArray()[0];


        $user = $this->findOneByIdUniv($username);

        $corresLDAP = array(
            'IO1320' => array('1A', 'Chimie'),
            'IO1321' => array('2A', 'Chimie'),
            'IO1330' => array('1A', 'GMP'),
            'IO1331' => array('2A', 'GMP'),
            'IO1340' => array('1A', 'Informatique'),
            'IO1341' => array('2A', 'Informatique'),
            'IO1342' => array('AS', 'Informatique'),
            'IO1350' => array('1A', 'QLIO'),
            'IO1351' => array('2A', 'QLIO'),
            'IO1360' => array('1A', 'GEA'),
            'IO1361' => array('2AGCF', 'GEA'),
            'IO1364' => array('AS', 'GEA'),
            'IO1362' => array('2AGMO', 'GEA'),
            'IO1D62' => array('2AGMOD', 'GEA'),
            'IO1363' => array('2AGRH', 'GEA'),
            'IO1370' => array('1A', 'GTE'),
            'IO1371' => array('2A', 'GTE'),
            'ILPO28' => array('LPRT', 'Informatique'),
            'ILPO25' => array('LPPCPC', 'Chimie'),
            'ILPO24' => array('LPCF', 'Chimie'),
            'ILPO23' => array('LPCAC', 'Chimie'),
            'ILPO26' => array('LPCSACE', 'GMP'),
            'ILPO27' => array('LPCPA', 'GMP'),
            'ILPO22' => array('LPGPI', 'QLIO'),
            'ILPO20' => array('LPGRH', 'GEA'),
            'ILPO29' => array('LPMCF', 'GEA'),
            'ILPO21' => array('LPEBSI', 'GTE'),
        );
	if ($infosPersonne){
          if (!$user) {
            $user = new User();

             $user->setPrenomUser($infosPersonne->getAttribute("givenName")[0]);
             $user->setNomUser($infosPersonne->getAttribute("sn")[0]);
             $user->setMailUser($infosPersonne->getAttribute("mail")[0]);
             $user->setRole("ROLE_" . $infosPersonne->getAttribute("eduPersonPrimaryAffiliation")[0]);
             $user->setIdUniv($infosPersonne->getAttribute("uid")[0]);

            if ($user->getRole() == "ROLE_student")
            {
                 $codeFormation = $infosPersonne->getAttribute("unrcEtape")[0];

                $infForm = $corresLDAP[$codeFormation];
                $formation = $em->getRepository(Formation::class)->findOneBy(array("departement" => ($em->getRepository(Departement::class)->findOneByNomDpt($infForm[0])), "typeFormation" => $infForm[0]));
                if (!$formation)
                {
                    $newF = new Formation();
                    $newF->setTypeFormation($infForm[0]);
                    $newF->setDepartement($em->getRepository("IUTOLivretBundle:Departement")->findOneByNomDpt($corresLDAP[$codeFormation][1]));
                    $dDeb = new \DateTime();
                    $dFin = new \DateTime();
                    if (date("m") < 9)
                    {
                        $dDeb->setDate(date("y"), 1, 15);
                        $dFin->setDate(date("y"), 8, 15);
                        $newF->setSemestre(2);

                    }
                    else
                    {
                        $dDeb->setDate(date("y") - 1, 8, 16);
                        $dFin->setDate(date("y"), 1, 14);
                        $newF->setSemestre(1);
                    }
                    $newF->setDateDebut($dDeb);
                    $newF->setDateFin($dFin);
                    $formation = $newF;
                    $em->persist($formation);
                }
                $user->addFormation($formation);
                $formation->addUser($user);
            }

            $em->persist($user);
            $em->flush();

          } else {
            // TODO vérifier avec LDAP si les infos sont à jours
          }
	  return $user;
	}
        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }

//    public function findOneByName($name)
//    {
//        $qb = $this->createQueryBuilder('e');
//
//        $qb
//            ->where('e.nomEtu = :name')
//            ->setParameter('name', $name);
//
//        return $qb->getQuery()->getResult();
//    }
}
