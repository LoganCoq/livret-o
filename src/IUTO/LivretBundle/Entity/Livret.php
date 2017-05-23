<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;

/**
 * Livret
 *
 * @ORM\Table(name="livret")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\LivretRepository")
 */
class Livret
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
     * @ORM\Column(name="intituleLivret", type="string", length=255)
     */
    private $intituleLivret;

    /**
     * @var string
     *
     * @ORM\Column(name="donneesMaquetteLivret", type="string", length=255, nullable=true)
     */
    private $donneesMaquetteLivret;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreationLivret", type="date")
     */
    private $dateCreationLivret;


    /**
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Projet", mappedBy="livrets")
    */
    private $projets;

    /**
     * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Edito", cascade={"persist"})
     */
    private $editos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->editos = new ArrayCollection();
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
     * Get intituleLivret
     *
     * @return string
     */
    public function getIntituleLivret()
    {
        return $this->intituleLivret;
    }

    /**
     * Set intituleLivret
     *
     * @param string $intituleLivret
     *
     * @return Livret
     */
    public function setIntituleLivret($intituleLivret)
    {
        $this->intituleLivret = $intituleLivret;

        return $this;
    }

    /**
     * Get donneesMaquetteLivret
     *
     * @return string
     */
    public function getDonneesMaquetteLivret()
    {
        return $this->donneesMaquetteLivret;
    }

    /**
     * Set donneesMaquetteLivret
     *
     * @param string $donneesMaquetteLivret
     *
     * @return Livret
     */
    public function setDonneesMaquetteLivret($donneesMaquetteLivret)
    {
        $this->donneesMaquetteLivret = $donneesMaquetteLivret;

        return $this;
    }

    /**
     * Get dateCreationLivret
     *
     * @return \DateTime
     */
    public function getDateCreationLivret()
    {
        return $this->dateCreationLivret;
    }

    /**
     * Set dateCreationLivret
     *
     * @param \DateTime $dateCreationLivret
     *
     * @return Livret
     */
    public function setDateCreationLivret($dateCreationLivret)
    {
        $this->dateCreationLivret = $dateCreationLivret;

        return $this;
    }


    /**
     * Add projet
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     *
     * @return Livret
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


    public function addEdito(Edito $edito)
    {
        $this->editos[] = $edito;
    }

    public function removeCategory(Edito $edito)
    {
        $this->editos->removeElement($edito);
    }

    public function getEditos()
    {
        return $this->editos;
    }
}
