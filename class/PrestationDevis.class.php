<?php

/** Esecouristes - Juin 2013 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Classe servant pour les prestations dans les devis de l'application esecouristes
**/

class PrestationDevis

{

	protected 	$_id,
			$_id_devis,
			$_id_prestation,
			$_quantite,
			$_sous_total;
	
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

	/** Getters
	pour récupérer les valeurs de l'objet 
	**/
	
        public function id() {
            return $this->_id;
	}
        
	public function id_devis() {
		return $this->_id_devis;
	}
		
	public function id_prestation() {
		return $this->_id_prestation;
	}
	
	public function quantite() {
		return $this->_quantite;
	}
	
	public function sous_total() {
		return $this->_sous_total;
	}
	

	/** Setters
	Assigner des valeurs aux variables
	**/
	
	public function setId($id) {
		 $id = (int) $id;
            
            if ($id > 0)
            {
                $this->_id= $id;
            }
	}
	
	public function setId_devis($id_devis) {
        	$this->_id_devis = $id_devis;
	}
	
	public function setId_prestation($id_prestation) {
		$this->_id_prestation = $id_prestation;
	}
	
	public function setQuantite($quantite) {
		$quantite = (float) $quantite;
		$this->_quantite = $quantite;
			
	}
	
	public function setSous_total($sous_total) {
		$sous_total = (float) $sous_total;
		$this->_sous_total = $sous_total;
	}      

}
?>
