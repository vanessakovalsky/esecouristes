<?php
/** Esecouristes - Juin 2013 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Classe servant à gérer types d'évènements
**/

class TypeAgrementManager extends TypeAgrement
{
	// Déclaration des variables
	protected $_db;

	public function __construct(PDO $db) {
		$this->_db = $db;
	}

	public function listerTypeAgrement() {

		$listeTypeAgrement = array();
		$sql = $this->_db->prepare('SELECT TA_CODE, CA_CODE, TA_DESCRIPTION, TA_FLAG FROM type_agrement');
		$sql->execute();
		while ($type_agrement = $sql->fetch(PDO::FETCH_ASSOC))
				{
					$listeTypeAgrement[] = new TypeAgrement($type_agrement);
				}

		return $listeTypeAgrement;
	}
	
	 public function get($id) {
    		//$id = (int) $id;
             //echo $id;
            $q = $this->_db->prepare('SELECT TA_CODE, CA_CODE, TA_DESCRIPTION, TA_FLAG FROM type_agrement WHERE TA_CODE = :ta_code');
            $q->bindValue(':ta_code', $id);
            $q->execute();
            $donnees = $q->fetch(PDO::FETCH_ASSOC);
                
            return new TypeAgrement($donnees);
	}
	
	    /**
         * Méthode permettant d'ajouter un type d'agrément
         * @param $type_agrement TypeAgrement Le type d'agrément à ajouter
         * @return void
         */
	
	protected function ajouterTypeAgrement(TypeAgrement $type_agrement)
   {      
    	$q = $this->_db->prepare('INSERT INTO type_agrement SET TA_CODE = :ta_code, TA_DESCRIPTION = :ta_description, CA_CODE = :ca_code');
        $q->bindValue(':ta_code', $type_agrement->ta_code());
	$q->bindValue(':ta_description', $type_agrement->ta_description());
	$q->bindValue(':ca_code', $type_agrement->ca_code());
        $q->execute();
	}	
    
        /**
         * Méthode permettant de modifier un type d'agrément
         * @param $type_agrement TypeAgrément le type d'agrément à modifier
         * @return void
         */
    
    public function modifierTypeAgrement(TypeAgrement $type_agrement)   
    {
    	$q = $this->_db->prepare('UPDATE type_agrement SET TA_CODE = :ta_code, TA_DESCRIPTION = :ta_description, CA_CODE = :ca_code WHERE TA_CODE = :ta_code');
        $q->bindValue(':ta_code', $type_agrement->ta_code());
	$q->bindValue(':ta_description', $type_agrement->ta_description());
	$q->bindValue(':ca_code', $type_agrement->ca_code());
        $q->execute();
    }
        
	/**
		Méthode pour déterminer si on doit mettre à jour un enregistrement ou ajouter un nouveau type d'agrément
	**/
	public function saveTypeAgrement(TypeAgrement $type_agrement)
        {
                 $new = $type_agrement->isNew();
                if ($new == 1) {
                        $this->ajouterTypeAgrement($type_agrement);
                }
                else {
                    $this->modifierTypeAgrement($type_agrement);
                }
        }
        
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
	
} // fin de la class
?>
