<?php

namespace Esecouristes\SectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SectionAgrement
 *
 * @ORM\Table(name="section_agrement")
 * @ORM\Entity(repositoryClass="Esecouristes\SectionBundle\Entity\SectionAgrementRepository")
 */
class SectionAgrement
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Esecouristes\SectionBundle\Entity\Section")
     */
    private $section;
    
     /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Esecouristes\SectionBundle\Entity\Agrement")
     */
    private $agrement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="datetime")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="datetime")
     */
    private $dateFin;


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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return SectionAgrement
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
     * @return SectionAgrement
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
}
