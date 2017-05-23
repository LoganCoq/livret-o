<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Edito
 *
 * @ORM\Table(name="edito")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\EditoRepository")
 */
class Edito
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
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenuEdito", type="text")
     */
    private $contenuEdito;


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
     * Set titre
     *
     * @param string $titre
     *
     * @return Edito
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenuEdito
     *
     * @param string $contenuEdito
     *
     * @return Edito
     */
    public function setContenuEdito($contenuEdito)
    {
        $this->contenuEdito = $contenuEdito;

        return $this;
    }

    /**
     * Get contenuEdito
     *
     * @return string
     */
    public function getContenuEdito()
    {
        return $this->contenuEdito;
    }
}

