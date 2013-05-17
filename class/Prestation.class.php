<?php
/** Esecouristes - Aout 2012 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Requete SQL pour générer la table qui va avec : 
CREATE TABLE IF NOT EXISTS `type_prestation` (
  `id_prestation` int(64) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(256) NOT NULL,
  `prix` float NOT NULL,
  `section_id` int(64) NOT NULL DEFAULT '0',
  `date_modification` DATE NULL ,
  `id_prestation_parent` INT( 64 ) NULL, 
  PRIMARY KEY (`id_prestation`),
  UNIQUE KEY `id_prestation` (`id_prestation`)
)

Classe servant pour les prestations pour les factures pour l'application esecouristes
**/

class Prestation
{
	protected 	$_id_prestation,
			$_libelle,
			$_prix,
			$_section_id,
			$_date_modification,
			$_id_prestation_parent;
	
	public function __construct(array $donnees) {
        {
            $this->hydrate($donnees);
        }
    }
    
	public function hydrate(array $donnees) {
		foreach ($donnees as $key => $value)
		        {
		            $method = 'set'.ucfirst($key);
		            
		            if (method_exists($this, $method))
		            {
		                $this->$method($value);
		            }
		        }
        
	}
	
	public function libelleValide() {
		return !empty($this->_libelle);
	}
	
	
	/**
         * Méthode permettant de savoir si le libelle est nouveau
         * @return bool
         */
    public function isNew()
        {
            return empty($this->_id_prestation);
        }
	
	/** Getters
	pour récupérer les valeurs de l'objet 
	**/
	
	public function id_prestation() {
		return $this->_id_prestation;
	}
		
	public function libelle() {
		return $this->_libelle;
	}
	
	public function prix() {
		return $this->_prix;
	}
	
	public function section_id() {
		return $this->_section_id;
	}
	
	public function id_prestation_parent() {
		return $this->_id_prestation_parent;
	}

	/** Setters
	Assigner des valeurs aux variables
	**/
	
	public function setId_prestation($id_prestation) {
		 $id_prestation = (int) $id_prestation;
            
            if ($id_prestation > 0)
            {
                $this->_id_prestation = $id_prestation;
            }
	}
	
	public function setLibelle($libelle) {
			if (is_string($libelle))
			{
				$this->_libelle = $libelle;
			}
	}
	
	public function setPrix($prix) {
		$prix = (float) $prix;
		
			if ($prix > 0)
			{
				$this->_prix = $prix;
			}
	}
	
	public function setSection_id($section) {
		$section = (int) $section;
		$this->_section_id = $section;
			
	}
	
	public function setId_prestation_parent($id_parent) {
		$id_parent = (int) $id_parent;
		$this->_id_prestation_parent = $id_parent;
	}

}// fin de la classe
?>
