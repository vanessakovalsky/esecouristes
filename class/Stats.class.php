<?php
/** Esecouristes - Aout 2012 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Classe servant pour les statistiques de l'application esecouristes
**/

class Statistiques
{
	public  $nbpaps,
		$nbdpspe,
		$nbdpsme,
		$nbdpsge,
		$nbdpsgr,
		$nbdpsgrpp;
	
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


	

?>
