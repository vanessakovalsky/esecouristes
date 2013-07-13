<?php
/** Esecouristes - Aout 2012 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3
)

Classe servant pour les catégorie d'evènement pour l'application esecouristes
**/

class CategorieEvenement
{
    
	protected       $_cev_code,
                        $_cev_description;
	
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
		return !empty($this->_cev_description);
	}
	
	
	/**
         * Méthode permettant de savoir si le libelle est nouveau
         * @return bool
         */
    public function isNew()
        {
            return empty($this->_cev_code);
        }
	
	/** Getters
	pour récupérer les valeurs de l'objet 
	**/
	
	public function cev_code() {
		return $this->_cev_code;
	}
	
	public function cev_description() {
		return $this->_cev_description;
	}
	

	/** Setters
	Assigner des valeurs aux variables
	**/
	
	public function setCev_code($cev_code) {
                $this->_cev_code = $cev_code;
	}
	
	public function setCev_description($cev_description) {
		$this->_cev_description = $cev_description;
			
	}

}// fin de la classe
?>