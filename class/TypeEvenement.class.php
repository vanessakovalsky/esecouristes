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

Classe servant pour les types d'evènement pour l'application esecouristes
**/

class TypeEvenement
{
	protected 	$_te_code,
                        $_te_libelle,
                        $_cev_code,
                        $_ta_code;
	
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
		return !empty($this->_te_libelle);
	}
	
            
	/**
         * Méthode permettant de savoir si le libelle est nouveau
         * @return bool
         */
    public function isNew()
        {
            return empty($this->_te_code);
        }
	
	/** Getters
	pour récupérer les valeurs de l'objet 
	**/
	
	public function te_code() {
		return $this->_te_code;
	}
		
	public function te_libelle() {
		return $this->_te_libelle;
	}
	
	public function cev_code() {
		return $this->_cev_code;
	}
	
	public function ta_code() {
		return $this->_ta_code;
	}
	

	/** Setters
	Assigner des valeurs aux variables
	**/
	
	public function setTe_code($te_code) {
		 $this->_te_code = $te_code;
	}
	
	public function setTe_libelle($te_libelle) {
			if (is_string($te_libelle))
			{
				$this->_te_libelle = $te_libelle;
			}
	}
	
	public function setCev_code($cev_code) {
                $this->_cev_code = $cev_code;
	}
	
	public function setTa_code($ta_code) {
		$this->_ta_code = $ta_code;
			
	}

}// fin de la classe
?>
