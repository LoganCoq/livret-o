<?php

// src/IUTO/LivretBundle/Entity/Image


namespace IUTO\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="IUTO\LivretBundle\Repository\ImageRepository")
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
     * @Vich\UploadableField(mapping="projet_image", fileNameProperty="imageName", size="imageSize")
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
     * @ORM\JoinColumn(name="projet_id", referencedColumnName="id", nullable=true)
     */
    private $projet;

    /**
     * @var bool
     *
     * @ORM\Column(name="isLogo", type="boolean")
     */
    private $isLogo;


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

    /**
     * @return File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File $image
     *
     * @return Image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     *
     * @return Image
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get isLogo
     *
     * @return bool
     */
    public function getIsLogo()
    {
        return $this->isLogo;
    }

    /**
     * Set marquantProjet
     *
     * @param boolean $isLogo
     *
     * @return Image
     */
    public function setIsLogo($isLogo)
    {
        $this->isLogo = $isLogo;

        return $this;
    }
}
