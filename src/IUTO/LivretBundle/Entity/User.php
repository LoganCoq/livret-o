<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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
     * @var string
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;

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
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Projet", inversedBy="etudiants")
    * @ORM\JoinTable(name="user_projetfaits")
    */
    private $projetFaits;

    /**
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Projet", inversedBy="tuteurs")
    * @ORM\JoinTable(name="user_projetsuivis")
    */
    private $projetSuivis;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Get nomUser
     *
     * @return string
     */
    public function getNomUser()
    {
        return $this->nomUser;
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
     * Get prenomUser
     *
     * @return string
     */
    public function getPrenomUser()
    {
        return $this->prenomUser;
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
     * Get mailUser
     *
     * @return string
     */
    public function getMailUser()
    {
        return $this->mailUser;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
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
     * Get idUniv
     *
     * @return string
     */
    public function getIdUniv()
    {
        return $this->idUniv;
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
     * Get formation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormation()
    {
        return $this->formations;
    }

    /**
     * Add projetSuivis
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     *
     * @return User
     */
    public function addProjetSuivis(\IUTO\LivretBundle\Entity\Projet $projet)
    {
        $this->projetSuivis[]= $projet;

        return $this;
    }

    /**
     * Remove projetSuivis
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     */
    public function removeProjetSuivis(\IUTO\LivretBundle\Entity\Projet $projet)
    {
        $this->projetSuivis->removeElement($projet);
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

    /**
     * Add projetFaits
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     *
     * @return User
     */
    public function addProjetFaits(\IUTO\LivretBundle\Entity\Projet $projet)
    {
        $this->projetFaits[] = $projet;

        return $this;
    }

    /**
     * Remove projetFaits
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     */
    public function removeProjetFaits(\IUTO\LivretBundle\Entity\Projet $projet)
    {
        $this->projetFaits->removeElement($projet);
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

    public function getRoles()
    {
        return $this->role;
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
}
