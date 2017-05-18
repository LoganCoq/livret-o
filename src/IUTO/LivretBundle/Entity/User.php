<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="nomUser", type="string", length=255)
     */
    private $nomUser;

    /**
     * @var string
     * @ORM\Column(name="prenomUser", type="string", length=255)
     */
    private $prenomUser;

    /**
     * @var string
     * @ORM\Column(name="mailUser", type="string", length=255)
     */
    private $mailUser;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\Column(name="roles", type="string", length=255)
     */
    private $roles;

    /**
     * @var string
     * @ORM\Column(name="idUniv", type="string", length=255, nullable=false, unique=true)
     */
    private $idUniv;

    /**
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Formation", mappedBy="users")
    */
    private $formations;

    /**
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Projet", inversedBy="etudiants",cascade={"persist"})
    * @ORM\JoinTable(name="user_projetfaits")
    */
    private $projetFaits;

    /**
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Projet", inversedBy="tuteurs")
    * @ORM\JoinTable(name="user_projetsuivis")
    */
    private $projetSuivis;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->formations = new ArrayCollection();
        $this->projetFaits = new ArrayCollection();
        $this->projetSuivis = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set role
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $newRoles
     *
     * @return User
     */
    public function setRoles($newRoles)
    {
        $this->roles = $newRoles;

        return $this;
    }

    public function getPassword()
    {
        return "";
    }

    public function getSalt()
    {
        return "";
    }

    public function getUsername()
    {
        return $this->idUniv;
    }

    public function eraseCredentials()
    {
        return;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get nomUser
     *
     * @return string
     */
    public function getNomUser()
    {
        return $this->nomUser;
    }

    /**
     * Set nomUser
     *
     * @param string $nomUser
     *
     * @return User
     */
    public function setNomUser($nomUser)
    {
        $this->nomUser = $nomUser;

        return $this;
    }

    /**
     * Get prenomUser
     *
     * @return string
     */
    public function getPrenomUser()
    {
        return $this->prenomUser;
    }

    /**
     * Set prenomUser
     *
     * @param string $prenomUser
     *
     * @return User
     */
    public function setPrenomUser($prenomUser)
    {
        $this->prenomUser = $prenomUser;

        return $this;
    }

    /**
     * Get mailUser
     *
     * @return string
     */
    public function getMailUser()
    {
        return $this->mailUser;
    }

    /**
     * Set mailUser
     *
     * @param string $mailUser
     *
     * @return User
     */
    public function setMailUser($mailUser)
    {
        $this->mailUser = $mailUser;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->roles[0];
    }

    /**
     * Add role
     *
     * @param string $role
     *
     * @return User
     */
    public function addRole($role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param string $role
     */
    public function removeRole($role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Get idUniv
     *
     * @return string
     */
    public function getIdUniv()
    {
        return $this->idUniv;
    }

    /**
     * Set idUniv
     *
     * @param string $idUniv
     *
     * @return User
     */
    public function setIdUniv($idUniv)
    {
        $this->idUniv = $idUniv;

        return $this;
    }

    /**
     * Add formation
     *
     * @param \IUTO\LivretBundle\Entity\Formation $formation
     *
     * @return User
     */
    public function addFormation(\IUTO\LivretBundle\Entity\Formation $formation)
    {
        $this->formations[] = $formation;

        return $this;
    }

    /**
     * Remove formation
     *
     * @param \IUTO\LivretBundle\Entity\Formation $formation
     */
    public function removeFormation(\IUTO\LivretBundle\Entity\Formation $formation)
    {
        $this->formations->removeElement($formation);
    }

    /**
     * Get formations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormations()
    {
        return $this->formations;
    }

    /**
     * Add projetFait
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projetFait
     *
     * @return User
     */
    public function addProjetFait(\IUTO\LivretBundle\Entity\Projet $projetFait)
    {
        $this->projetFaits[] = $projetFait;

        return $this;
    }

    /**
     * Remove projetFait
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projetFait
     */
    public function removeProjetFait(\IUTO\LivretBundle\Entity\Projet $projetFait)
    {
        $this->projetFaits->removeElement($projetFait);
    }

    /**
     * Get projetFaits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjetFaits()
    {
        return $this->projetFaits;
    }

    /**
     * Add projetSuivi
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projetSuivi
     *
     * @return User
     */
    public function addProjetSuivi(\IUTO\LivretBundle\Entity\Projet $projetSuivi)
    {
        $this->projetSuivis[] = $projetSuivi;

        return $this;
    }

    /**
     * Remove projetSuivi
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projetSuivi
     */
    public function removeProjetSuivi(\IUTO\LivretBundle\Entity\Projet $projetSuivi)
    {
        $this->projetSuivis->removeElement($projetSuivi);
    }

    /**
     * Get projetSuivis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjetSuivis()
    {
        return $this->projetSuivis;
    }
}
