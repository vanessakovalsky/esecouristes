<?php
/** Esecouristes - Aout 2012 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Classe servant pour les types d'agrément pour l'application esecouristes
**/

class TypeAgrement
{
	protected 	$_ta_code,
                        $_ca_code,
                        $_ta_description,
                        $_ta_flag;
	
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
		return !empty($this->_ta_description);
	}
	
	
	/**
         * Méthode permettant de savoir si le libelle est nouveau
         * @return bool
         */
    public function isNew()
        {
            return empty($this->_ca_code);
        }
	
	/** Getters
	pour récupérer les valeurs de l'objet 
	**/
	
	public function ta_code() {
		return $this->_ta_code;
	}
		
	public function ca_code() {
		return $this->_ca_code  ;
	}
	
	public function ta_description() {
		return $this->_ta_description;
	}
	
	public function ta_flag() {
		return $this->_ta_flag;
	}
	

	/** Setters
	Assigner des valeurs aux variables
	**/
	
	public function setTa_code($ta_code) {
		 $this->_ta_code = $ta_code;
	}
	
	public function setCa_code($ca_code) {
		$this->_te_libelle = $te_libelle;
	}
	
	public function setTa_description($ta_description) {
        	if (is_string($ta_description))
		{
                    $this->_ta_description = $ta_description; 
                }
        }
	
	public function setTa_flag($ta_flag) {
		$this->_ta_flag = $ta_flag;
			
	}

}// fin de la classe
?>