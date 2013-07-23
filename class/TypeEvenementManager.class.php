<?php
/** Esecouristes - Juin 2013 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Classe servant à gérer types d'évènements
**/

class TypeEvenementManager extends TypeEvenement
{
	// Déclaration des variables
	protected $_db;

	public function __construct(PDO $db) {
		$this->_db = $db;
	}

	public function listerTypeEvenement() {

		$listeTypeEvenement = array();
		$sql = $this->_db->prepare('SELECT TE_CODE, TE_LIBELLE, CEV_CODE, TA_CODE FROM type_evenement');
		$sql->execute();
		while ($type_evenement = $sql->fetch(PDO::FETCH_ASSOC))
				{
					$listeTypeEvenement[] = new TypeEvenement($type_evenement);
				}

		return $listeTypeEvenement;
	}
	
	public function listerTypeEvenementSection($section) {
            $listeTypeEvenementSection = array();
            $sql = $this->_db->prepare('SELECT DISTINCT te.TE_CODE, te.TE_LIBELLE, te.CEV_CODE FROM `type_evenement` AS te LEFT JOIN type_agrement AS ta ON te.TA_CODE = ta.TA_CODE LEFT JOIN agrement AS ag ON te.TA_CODE = ag.TA_CODE WHERE (ag.A_FIN >= NOW( ) OR ag.A_FIN IS NULL)AND ag.S_ID = :section  ORDER BY te.CEV_CODE desc, te.TE_CODE asc');
            $sql->bindValue(':section',$section);
            $sql->execute();
           while ($type_evenement_section = $sql->fetch(PDO::FETCH_ASSOC))
				{
					$listeTypeEvenementSection[] = new TypeEvenement($type_evenement_section);
				}

		return $listeTypeEvenementSection;
        }
        
        public function get($id) {
    		//$id = (int) $id;
             //echo $id;
            $q = $this->_db->prepare('SELECT TE_CODE, TE_LIBELLE, CEV_CODE, TA_CODE FROM type_evenement WHERE TE_CODE = :te_code');
            $q->bindValue(':te_code', $id);
            $q->execute();
            $donnees = $q->fetch(PDO::FETCH_ASSOC);
                
            return new TypeEvenement($donnees);
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
            $new = $type_evenement->isNew();
                if ($new == 1) {
                        $this->ajouterTypeEvenement($type_evenement);
                }
                else {
                    $this->modifierTypeEvenement($type_evenement);
                }
        }
        
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
	
} // fin de la class
?>
