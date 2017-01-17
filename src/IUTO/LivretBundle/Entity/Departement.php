<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Departement
 *
 * @ORM\Table(name="departement")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\DepartementRepository")
 */
class Departement
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
     * @ORM\Column(name="nomDpt", type="string", length=255, unique=true)
     */
    private $nomDpt;


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
     * Set nomDpt
     *
     * @param string $nomDpt
     *
     * @return Departement
     */
    public function setNomDpt($nomDpt)
    {
        $this->nomDpt = $nomDpt;

        return $this;
    }

    /**
     * Get nomDpt
     *
     * @return string
     */
    public function getNomDpt()
    {
        return $this->nomDpt;
    }
}

