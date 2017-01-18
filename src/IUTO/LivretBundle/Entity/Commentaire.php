<?php

namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\CommentaireRepository")
 */
class Commentaire
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
     * @ORM\Column(name="contenu", type="text", nullable=true)
     */
    private $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="reponse", type="text", nullable=true)
     */
    private $reponse;

    /**
     * @ORM\ManyToOne(targetEntity="IUTO\LivretBundle\Entity\Projet")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projet;

    /**
     * @ORM\ManyToOne(targetEntity="IUTO\LivretBundle\Entity\Personnel")
     * @ORM\JoinColumn(nullable=false)
     */
    private $personnel;


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
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Commentaire
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set reponse
     *
     * @param string $reponse
     *
     * @return Commentaire
     */
    public function setReponse($reponse)
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get reponse
     *
     * @return string
     */
    public function getReponse()
    {
        return $this->reponse;
    }



    /**
     * Set projet
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     *
     * @return Commentaire
     */
    public function setProjet(\IUTO\LivretBundle\Entity\Projet $projet)
    {
        $this->projet = $projet;

        return $this;
    }

    /**
     * Get projet
     *
     * @return \IUTO\LivretBundle\Entity\Projet
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * Set personnel
     *
     * @param \IUTO\LivretBundle\Entity\Personnel $personnel
     *
     * @return Commentaire
     */
    public function setPersonnel(\IUTO\LivretBundle\Entity\Personnel $personnel)
    {
        $this->personnel = $personnel;

        return $this;
    }

    /**
     * Get personnel
     *
     * @return \IUTO\LivretBundle\Entity\Personnel
     */
    public function getPersonnel()
    {
        return $this->personnel;
    }
}
