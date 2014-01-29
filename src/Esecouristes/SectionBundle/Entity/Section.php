<?php

namespace Esecouristes\SectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Section
 *
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="Esecouristes\SectionBundle\Entity\SectionRepository")
 */
class Section
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var integer
     *
     * @ORM\Column(name="section_parent", type="integer", nullable=true)
     */
    private $sectionParent;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_long", type="string", length=255, nullable=true)
     */
    private $nomLong;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="text", nullable=true)
     */
    private $adresse;

    /**
     * @var integer
     *
     * @ORM\Column(name="code_postal", type="integer", nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="cedex", type="string", length=255, nullable=true)
     */
    private $cedex;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=14, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="portable_urgence", type="string", length=14, nullable=true)
     */
    private $portableUrgence;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=14, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="email_secretariat", type="string", length=255, nullable=true)
     */
    private $emailSecretariat;

    /**
     * @var string
     *
     * @ORM\Column(name="site_web", type="string", length=255, nullable=true)
     */
    private $siteWeb;

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
     * Set nom
     *
     * @param string $nom
     * @return Section
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set sectionParent
     *
     * @param integer $sectionParent
     * @return Section
     */
    public function setSectionParent($sectionParent)
    {
        $this->sectionParent = $sectionParent;

        return $this;
    }

    /**
     * Get sectionParent
     *
     * @return integer 
     */
    public function getSectionParent()
    {
        return $this->sectionParent;
    }

    /**
     * Set nomLong
     *
     * @param string $nomLong
     * @return Section
     */
    public function setNomLong($nomLong)
    {
        $this->nomLong = $nomLong;

        return $this;
    }

    /**
     * Get nomLong
     *
     * @return string 
     */
    public function getNomLong()
    {
        return $this->nomLong;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Section
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set codePostal
     *
     * @param integer $codePostal
     * @return Section
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return integer 
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Section
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set cedex
     *
     * @param string $cedex
     * @return Section
     */
    public function setCedex($cedex)
    {
        $this->cedex = $cedex;

        return $this;
    }

    /**
     * Get cedex
     *
     * @return string 
     */
    public function getCedex()
    {
        return $this->cedex;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return Section
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set portableUrgence
     *
     * @param string $portableUrgence
     * @return Section
     */
    public function setPortableUrgence($portableUrgence)
    {
        $this->portableUrgence = $portableUrgence;

        return $this;
    }

    /**
     * Get portableUrgence
     *
     * @return string 
     */
    public function getPortableUrgence()
    {
        return $this->portableUrgence;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return Section
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Section
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set emailSecretariat
     *
     * @param string $emailSecretariat
     * @return Section
     */
    public function setEmailSecretariat($emailSecretariat)
    {
        $this->emailSecretariat = $emailSecretariat;

        return $this;
    }

    /**
     * Get emailSecretariat
     *
     * @return string 
     */
    public function getEmailSecretariat()
    {
        return $this->emailSecretariat;
    }

    /**
     * Set siteWeb
     *
     * @param string $siteWeb
     * @return Section
     */
    public function setSiteWeb($siteWeb)
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    /**
     * Get siteWeb
     *
     * @return string 
     */
    public function getSiteWeb()
    {
        return $this->siteWeb;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->agrements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add agrements
     *
     * @param \Esecouristes\SectionBundle\Entity\Agrement $agrements
     * @return Section
     */
    public function addAgrement(\Esecouristes\SectionBundle\Entity\Agrement $agrements)
    {
        $this->agrements[] = $agrements;

        return $this;
    }

    /**
     * Remove agrements
     *
     * @param \Esecouristes\SectionBundle\Entity\Agrement $agrements
     */
    public function removeAgrement(\Esecouristes\SectionBundle\Entity\Agrement $agrements)
    {
        $this->agrements->removeElement($agrements);
    }

    /**
     * Get agrements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAgrements()
    {
        return $this->agrements;
    }
}
