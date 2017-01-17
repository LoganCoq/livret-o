<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Etudiant
 *
 * @ORM\Table(name="etudiant")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\EtudiantRepository")
 */
class Etudiant
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
     * @ORM\Column(name="nomEtu", type="string", length=255)
     */
    private $nomEtu;

    /**
     * @var string
     *
     * @ORM\Column(name="prenomEtu", type="string", length=255)
     */
    private $prenomEtu;

    /**
     * @var string
     *
     * @ORM\Column(name="mailEtu", type="string", length=255, unique=true)
     */
    private $mailEtu;

    /**
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Projet", inversedBy="etudiants")
    */
    private $projets;

    /**
    * @ORM\ManytoMany(targetEntity="IUTO\LivretBundle\Entity\Formation")
    * @ORM\JoinColumn(nullable=false)
    */
    private $formation;

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
     * Set nomEtu
     *
     * @param string $nomEtu
     *
     * @return Etudiant
     */
    public function setNomEtu($nomEtu)
    {
        $this->nomEtu = $nomEtu;

        return $this;
    }

    /**
     * Get nomEtu
     *
     * @return string
     */
    public function getNomEtu()
    {
        return $this->nomEtu;
    }

    /**
     * Set prenomEtu
     *
     * @param string $prenomEtu
     *
     * @return Etudiant
     */
    public function setPrenomEtu($prenomEtu)
    {
        $this->prenomEtu = $prenomEtu;

        return $this;
    }

    /**
     * Get prenomEtu
     *
     * @return string
     */
    public function getPrenomEtu()
    {
        return $this->prenomEtu;
    }

    /**
     * Set mailEtu
     *
     * @param string $mailEtu
     *
     * @return Etudiant
     */
    public function setMailEtu($mailEtu)
    {
        $this->mailEtu = $mailEtu;

        return $this;
    }

    /**
     * Get mailEtu
     *
     * @return string
     */
    public function getMailEtu()
    {
        return $this->mailEtu;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->formation = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add projet
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     *
     * @return Etudiant
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

    /**
     * Add formation
     *
     * @param \IUTO\LivretBundle\Entity\Formation $formation
     *
     * @return Etudiant
     */
    public function addFormation(\IUTO\LivretBundle\Entity\Formation $formation)
    {
        $this->formation[] = $formation;

        return $this;
    }

    /**
     * Remove formation
     *
     * @param \IUTO\LivretBundle\Entity\Formation $formation
     */
    public function removeFormation(\IUTO\LivretBundle\Entity\Formation $formation)
    {
        $this->formation->removeElement($formation);
    }

    /**
     * Get formation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormation()
    {
        return $this->formation;
    }
}
