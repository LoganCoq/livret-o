<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Projet
 *
 * @ORM\Table(name="projet")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\ProjetRepository")
 */
class Projet
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
     * @ORM\Column(name="intituleProjet", type="text")
     */
    private $intituleProjet;

    /**
     * @var string
     *
     * @ORM\Column(name="descripProjet", type="text", nullable=true)
     */
    private $descripProjet;

    /**
     * @var string
     *
     * @ORM\Column(name="bilanProjet", type="text", nullable=true)
     */
    private $bilanProjet;

    /**
     * @var bool
     *
     * @ORM\Column(name="marquantProjet", type="boolean")
     */
    private $marquantProjet;

    /**
     * @var array
     *
     * @ORM\Column(name="motsClesProjet", type="array", nullable=true)
     */
    private $motsClesProjet;

    /**
     * @var string
     *
     * @ORM\Column(name="clientProjet", type="string", length=255, nullable=true)
     */
    private $clientProjet;

    /**
     * @var bool
     *
     * @ORM\Column(name="validerProjet", type="boolean")
     */
    private $validerProjet;

    /**
     * @var date
     *
     * @ORM\Column(name="dateDebut", type="date")
     */
    private $dateDebut;

    /**
     * @var date
     *
     * @ORM\Column(name="dateFin", type="date")
     */
    private $dateFin;

    /**
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Etudiant", mappedBy="projets")
    */
    private $etudiants;

    /**
     * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Personnel", mappedBy="projets")
     */
    private $personnels;

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
     * Set intituleProjet
     *
     * @param string $intituleProjet
     *
     * @return Projet
     */
    public function setIntituleProjet($intituleProjet)
    {
        $this->intituleProjet = $intituleProjet;

        return $this;
    }

    /**
     * Get intituleProjet
     *
     * @return string
     */
    public function getIntituleProjet()
    {
        return $this->intituleProjet;
    }

    /**
     * Set descripProjet
     *
     * @param string $descripProjet
     *
     * @return Projet
     */
    public function setDescripProjet($descripProjet)
    {
        $this->descripProjet = $descripProjet;

        return $this;
    }

    /**
     * Get descripProjet
     *
     * @return string
     */
    public function getDescripProjet()
    {
        return $this->descripProjet;
    }

    /**
     * Set bilanProjet
     *
     * @param string $bilanProjet
     *
     * @return Projet
     */
    public function setBilanProjet($bilanProjet)
    {
        $this->bilanProjet = $bilanProjet;

        return $this;
    }

    /**
     * Get bilanProjet
     *
     * @return string
     */
    public function getBilanProjet()
    {
        return $this->bilanProjet;
    }

    /**
     * Set marquantProjet
     *
     * @param boolean $marquantProjet
     *
     * @return Projet
     */
    public function setMarquantProjet($marquantProjet)
    {
        $this->marquantProjet = $marquantProjet;

        return $this;
    }

    /**
     * Get marquantProjet
     *
     * @return bool
     */
    public function getMarquantProjet()
    {
        return $this->marquantProjet;
    }

    /**
     * Set motsClesProjet
     *
     * @param array $motsClesProjet
     *
     * @return Projet
     */
    public function setMotsClesProjet($motsClesProjet)
    {
        $this->motsClesProjet = $motsClesProjet;

        return $this;
    }

    /**
     * Get motsClesProjet
     *
     * @return array
     */
    public function getMotsClesProjet()
    {
        return $this->motsClesProjet;
    }

    /**
     * Set clientProjet
     *
     * @param string $clientProjet
     *
     * @return Projet
     */
    public function setClientProjet($clientProjet)
    {
        $this->clientProjet = $clientProjet;

        return $this;
    }

    /**
     * Get clientProjet
     *
     * @return string
     */
    public function getClientProjet()
    {
        return $this->clientProjet;
    }

    /**
     * Set validerProjet
     *
     * @param boolean $validerProjet
     *
     * @return Projet
     */
    public function setValiderProjet($validerProjet)
    {
        $this->validerProjet = $validerProjet;

        return $this;
    }

    /**
     * Get validerProjet
     *
     * @return bool
     */
    public function getValiderProjet()
    {
        return $this->validerProjet;
    }
}
