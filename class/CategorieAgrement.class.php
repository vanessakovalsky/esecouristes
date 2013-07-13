<?php
/** Esecouristes - Aout 2012 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3
)

Classe servant pour les catégorie d'agrément pour l'application esecouristes
**/

class CategorieAgrement
{
    
	protected       $_ca_code,
                        $_ca_description,
                        $_ca_flag;
	
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
		return !empty($this->_ca_description);
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
	
	public function ca_code() {
		return $this->_ca_code;
	}
	
	public function ca_description() {
		return $this->_ca_description;
	}
        
        public function ca_flag() {
                return $this->_ca_flag;
        }
	

	/** Setters
	Assigner des valeurs aux variables
	**/
	
	public function setCa_code($ca_code) {
                $this->_ca_code = $ca_code;
	}
	
	public function setCa_description($ca_description) {
		$this->_ca_description = $ca_description;
			
	}
        
        public function setCa_flag($ca_flag) {
                $thhis->_ca_flag = $ca_flag;
        }

}// fin de la classe
?>