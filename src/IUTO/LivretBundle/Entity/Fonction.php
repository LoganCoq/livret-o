<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fonction
 *
 * @ORM\Table(name="fonction")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\FonctionRepository")
 */
class Fonction
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
     * @ORM\Column(name="nomFonction", type="string", length=255)
     */
    private $nomFonction;


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
     * Set nomFonction
     *
     * @param string $nomFonction
     *
     * @return Fonction
     */
    public function setNomFonction($nomFonction)
    {
        $this->nomFonction = $nomFonction;

        return $this;
    }

    /**
     * Get nomFonction
     *
     * @return string
     */
    public function getNomFonction()
    {
        return $this->nomFonction;
    }
}

