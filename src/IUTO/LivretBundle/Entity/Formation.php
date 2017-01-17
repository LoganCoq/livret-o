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
    * @ORM\ManyToOne(targetEntity="IUTO\LivretBundle\Entity\Departement", cascade={"persist"})
    */
    private $departement;

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
}
