<?php

// src/IUTO/LivretBundle/Entity/Image


namespace IUTO\LivretBundle\Entity;


/**

 * @ORM\Table(name="image")

 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Entity\ImageRepository")

 */

class Image

{

    /**

     * @ORM\Column(name="id", type="integer")

     * @ORM\Id

     * @ORM\GeneratedValue(strategy="AUTO")

     */

    private $id;


    /**

     * @ORM\Column(name="url", type="string", length=255)

     */

    private $url;


    /**

     * @ORM\Column(name="alt", type="string", length=255)

     */

    private $alt;

    /**
     * @ORM\ManyToOne(targetEntity="IUTO\LivretBundle\Entity\Projet")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projet;

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
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

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
     * Set projet
     *
     * @param \IUTO\LivretBundle\Entity\Projet $projet
     *
     * @return Image
     */
    public function setProjet(\IUTO\LivretBundle\Entity\Projet $projet)
    {
        $this->projet = $projet;

        return $this;
    }
}
