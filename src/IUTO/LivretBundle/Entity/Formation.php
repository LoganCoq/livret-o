<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Formation
 *
 * @ORM\Table(name="formation")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\FormationRepository")
 */
class Formation
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
     * @ORM\Column(name="typeFormation", type="string", length=255)
     */
    private $typeFormation;

    /**
     * @var int
     *
     * @ORM\Column(name="semestre", type="integer")
     */
    private $semestre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="date")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="date")
     */
    private $dateFin;

    /**
    * @ORM\ManyToOne(targetEntity="IUTO\LivretBundle\Entity\Departement", cascade={"persist"})
    */
    private $departement;

    /**
    * @ORM\ManyToMany(targetEntity="IUTO\LivretBundle\Entity\User", inversedBy="formations")
    */
    private $users;

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
     * Set typeFormation
     *
     * @param string $typeFormation
     *
     * @return Formation
     */
    public function setTypeFormation($typeFormation)
    {
        $this->typeFormation = $typeFormation;

        return $this;
    }

    /**
     * Get typeFormation
     *
     * @return string
     */
    public function getTypeFormation()
    {
        return $this->typeFormation;
    }

    /**
     * Set semestre
     *
     * @param integer $semestre
     *
     * @return Formation
     */
    public function setSemestre($semestre)
    {
        $this->semestre = $semestre;

        return $this;
    }

    /**
     * Get semestre
     *
     * @return int
     */
    public function getSemestre()
    {
        return $this->semestre;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return Formation
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

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
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return Formation
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

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
     * Set departement
     *
     * @param \IUTO\LivretBundle\Entity\Departement $departement
     *
     * @return Formation
     */
    public function setDepartement(\IUTO\LivretBundle\Entity\Departement $departement = null)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get departement
     *
     * @return \IUTO\LivretBundle\Entity\Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * @return int
     */
    public function getYearDebut()
    {
        return (int)$this->dateDebut->format('Y');
    }

    /**
     * @return int
     */
    public function getYearFin()
    {
        return (int)$this->dateFin->format('Y');
    }




    /**
     * Add user
     *
     * @param \IUTO\LivretBundle\Entity\User $user
     *
     * @return Projet
     */
    public function addUser(\IUTO\LivretBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \IUTO\LivretBundle\Entity\User $user
     */
    public function removeUser(\IUTO\LivretBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
