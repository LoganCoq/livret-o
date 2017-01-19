<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Personnel
 *
 * @ORM\Table(name="personnel")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\PersonnelRepository")
 */
class Personnel
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nomPers", type="string", length=255)
     */
    private $nomPers;

    /**
     * @var string
     *
     * @ORM\Column(name="prenomPers", type="string", length=255)
     */
    private $prenomPers;

    /**
     * @var string
     *
     * @ORM\Column(name="mailPers", type="string", length=255, unique=true)
     */
    private $mailPers;

    /**
    * @ORM\ManyToOne(targetEntity="IUTO\LivretBundle\Entity\Fonction")
    */
    private $fonction;

    /**
     * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Projet", inversedBy="personnels")
     */
    private $projets;

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
     * Set nomPers
     *
     * @param string $nomPers
     *
     * @return Personnel
     */
    public function setNomPers($nomPers)
    {
        $this->nomPers = $nomPers;

        return $this;
    }

    /**
     * Get nomPers
     *
     * @return string
     */
    public function getNomPers()
    {
        return $this->nomPers;
    }

    /**
     * Set prenomPers
     *
     * @param string $prenomPers
     *
     * @return Personnel
     */
    public function setPrenomPers($prenomPers)
    {
        $this->prenomPers = $prenomPers;

        return $this;
    }

    /**
     * Get prenomPers
     *
     * @return string
     */
    public function getPrenomPers()
    {
        return $this->prenomPers;
    }

    /**
     * Set mailPers
     *
     * @param string $mailPers
     *
     * @return Personnel
     */
    public function setMailPers($mailPers)
    {
        $this->mailPers = $mailPers;

        return $this;
    }

    /**
     * Get mailPers
     *
     * @return string
     */
    public function getMailPers()
    {
        return $this->mailPers;
    }
    /**
     * Set fonction
     *
     * @param \IUTO\LivretBundle\Entity\Fonction $fonction
     *
     * @return Personnel
     */
    public function setFonction(\IUTO\LivretBundle\Entity\Fonction $fonction = null)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return \IUTO\LivretBundle\Entity\Fonction
     */
    public function getFonction()
    {
        return $this->fonction;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add projet
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     *
     * @return Personnel
     */
    public function addProjet(\IUTO\LivretBundle\Entity\Projet $projet)
    {
        $this->projets[] = $projet;

        return $this;
    }

    /**
     * Remove projet
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     */
    public function removeProjet(\IUTO\LivretBundle\Entity\Projet $projet)
    {
        $this->projets->removeElement($projet);
    }

    /**
     * Get projets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjets()
    {
        return $this->projets;
    }
}
