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
         * Méthode permettant d'ajouter un type d'évènement
         * @param $type_evenement TypeEvenement Le type d'évènement à ajouter
         * @return void
         */
	
	protected function ajouterTypeEvenement(TypeEvenement $type_evenement)
   {      
    	$q = $this->_db->prepare('INSERT INTO type_evenement SET TE_CODE = :te_code, TE_LIBELLE = :te_libelle, CEV_CODE = :cev_code, TA_CODE = :ta_code ');
        $q->bindValue(':te_code', $type_evenement->te_code());
	$q->bindValue(':te_libelle', $type_evenement->te_libelle());
	$q->bindValue(':cev_code', $type_evenement->cev_code());
	$q->bindValue(':ta_code', $type_evenement->ta_code());
        $q->execute();
	}	
    
        /**
         * Méthode permettant de modifier un type d'évènement
         * @param $type_evenement TypeEvenement le type d'évènement à modifier
         * @return void
         */
    
    public function modifierTypeEvenement(TypeEvenement $type_evenement)   
    {
    	$q = $this->_db->prepare('UPDATE type_evenement SET TE_LIBELLE = :te_libelle, CEV_CODE = :cev_code, TA_CODE = :ta_code WHERE TE_CODE = :te_code');
        $q->bindValue(':te_code', $type_evenement->te_code());
	$q->bindValue(':te_libelle', $type_evenement->te_libelle());
	$q->bindValue(':cev_code', $type_evenement->cev_code());
	$q->bindValue(':ta_code', $type_evenement->ta_code());
        $q->execute();
    }
        
	/**
		Méthode pour déterminer si on doit mettre à jour un enregistrement ou ajouter un nouveau type d'évènement
	**/
	public function saveTypeEvenement(TypeEvenement $type_evenement)
        {
                $type_evenement->isNew() ? $this->ajouterTypeEvenement($type_evenement) : $this->modifierTypeEvenement($type_evenement);
        }
        
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
	
} // fin de la class
?>
