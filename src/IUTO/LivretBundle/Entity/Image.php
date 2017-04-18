<?php

// src/IUTO/LivretBundle/Entity/Image


namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Entity\ImageRepository")
 * @Vich\Uploadable
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
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\ManyToOne(targetEntity="IUTO\LivretBundle\Entity\Projet", inversedBy="images")
     * @ORM\JoinColumn(name="projet_id", referencedColumnName="id", nullable=false)
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
