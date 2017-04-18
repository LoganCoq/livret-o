<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;

/**
 * Projet
 *
 * @ORM\Table(name="projet")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\ProjetRepository")
 */
class Projet
{
    public $nomDep;
    public $listeEtudiants;
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
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\User", mappedBy="projetFaits")
    */
    private $etudiants;
    /**
     * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\User", mappedBy="projetSuivis")
     */
    private $tuteurs;
    /**
     * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\Livret", inversedBy="projets")
     */
    private $livrets;

    /**
     * @ORM\OneToMany(targetEntity="IUTO\LivretBundle\Entity\Image", mappedBy="projet")
     */
    private $images;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->etudiants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tuteur = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function setNomDpt($nom){
        $this->nomDep = $nom;
    }

    /**
     * Get nomDepartement
     *
     * @return string
     */
    public function getNomDpt()
    {
        return $this->nomDep;
    }

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
     * Get intituleProjet
     *
     * @return string
     */
    public function getIntituleProjet()
    {
        return $this->intituleProjet;
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
     * Get descripProjet
     *
     * @return string
     */
    public function getDescripProjet()
    {
        return $this->descripProjet;
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
     * Get bilanProjet
     *
     * @return string
     */
    public function getBilanProjet()
    {
        return $this->bilanProjet;
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
     * Get marquantProjet
     *
     * @return bool
     */
    public function getMarquantProjet()
    {
        return $this->marquantProjet;
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
     * Get motsClesProjet
     *
     * @return array
     */
    public function getMotsClesProjet()
    {
        return $this->motsClesProjet;
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
     * Get clientProjet
     *
     * @return string
     */
    public function getClientProjet()
    {
        return $this->clientProjet;
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
     * Get validerProjet
     *
     * @return bool
     */
    public function getValiderProjet()
    {
        return $this->validerProjet;
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
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return Projet
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return Projet
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Add etudiant
     *
     * @param \IUTO\LivretBundle\Entity\User $etudiant
     *
     * @return Projet
     */
    public function addEtudiant(\IUTO\LivretBundle\Entity\User $etudiant)
    {
        $this->etudiants[] = $etudiant;

        return $this;
    }

    /**
     * Remove etudiant
     *
     * @param \IUTO\LivretBundle\Entity\User $etudiant
     */
    public function removeEtudiant(\IUTO\LivretBundle\Entity\User $etudiant)
    {
        $this->etudiants->removeElement($etudiant);
    }

    /**
     * Get etudiants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEtudiants()
    {
        return $this->etudiants;
    }

    /**
     * Add tuteur
     *
     * @param \IUTO\LivretBundle\Entity\User $tuteur
     *
     * @return Projet
     */
    public function addTuteur(\IUTO\LivretBundle\Entity\User $tuteur)
    {
        $this->tuteurs[] = $tuteur;

        return $this;
    }

    /**
     * Remove tuteur
     *
     * @param \IUTO\LivretBundle\Entity\User $tuteur
     */
    public function removeTuteur(\IUTO\LivretBundle\Entity\User $tuteur)
    {
        $this->tuteurs->removeElement($tuteur);
    }

    /**
     * Get tuteurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTuteurs()
    {
        return $this->tuteurs;
    }

    /**
     * Add livret
     *
     * @param \IUTO\LivretBundle\Entity\Livret $livret
     *
     * @return Projet
     */
    public function addLivret(\IUTO\LivretBundle\Entity\Livret $livret)
    {
        $this->livrets[] = $livret;

        return $this;
    }

    /**
     * Remove livret
     *
     * @param \IUTO\LivretBundle\Entity\Livret $livret
     */
    public function removeLivret(\IUTO\LivretBundle\Entity\Livret $livret)
    {
        $this->livrets->removeElement($livret);
    }

    /**
     * Get livrets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLivrets()
    {
        return $this->livrets;
    }

    /**
     * Add image
     *
     * @param \IUTO\LivretBundle\Entity\Image $image
     *
     * @return Projet
     */
    public function addImage(\IUTO\LivretBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \IUTO\LivretBundle\Entity\Image $image
     */
    public function removeImage(\IUTO\LivretBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }
}
