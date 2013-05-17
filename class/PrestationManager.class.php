<?php
/** Esecouristes - Aout 2012 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Classe servant à gérer les prestations pour les factures pour l'application esecouristes
**/

class PrestationManager extends Prestation
{
	// Déclaration des variables
	protected $_db;

	public function __construct(PDO $db) {
		$this->_db = $db;
	}

	public function listerPrestations($section, $section_parent) {

		$listePrestations = array();
		$sql = $this->_db->prepare('SELECT id_prestation, libelle, prix, section_id FROM type_prestation WHERE section_id = :section');
		$sql->execute(array(':section' => $section));
		while ($prestations = $sql->fetch(PDO::FETCH_ASSOC))
				{
					$listePrestations[] = new Prestation($prestations);
				}

		// On ajoute les prestations de la section mère
		if ($section_parent !== NULL) {
		$sql_national = $this->_db->prepare('SELECT id_prestation, libelle, prix, section_id
						FROM type_prestation
						WHERE section_id = :section_parent
						AND id_prestation NOT IN (SELECT id_prestation_parent FROM type_prestation WHERE section_id = :section)');
		
		$sql_national->execute(array(':section' => $section, ':section_parent' => $section_parent));
		while ($prestations_national = $sql_national->fetch(PDO::FETCH_ASSOC))
				{
					$listePrestations[] = new Prestation($prestations_national);
				}
		}
		return $listePrestations;
	}
	
	 public function get($id) {
    		$id = (int) $id;
            $q = $this->_db->query('SELECT id_prestation, libelle, prix, section_id, date_modification, id_prestation_parent FROM type_prestation WHERE id_prestation = '.$id);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);
                
            return new Prestation($donnees);
	}
	
	public function get_parent($id_parent,$section) {
		$id_parent = (int) $id_parent;
        	$q = $this->_db->prepare('SELECT id_prestation FROM type_prestation WHERE id_prestation_parent = :id_parent AND section_id = :section');
		$q->execute(array(':id_parent' => $id_parent, ':section' => $section));
        	$donnees = $q->fetch(PDO::FETCH_ASSOC);
                
            return $donnees;
	}
	    /**
         * Méthode permettant d'ajouter une prestation
         * @param $presta Prestation La prestation à ajouter
         * @return void
         */
	
	protected function ajouterPrestation(Prestation $presta)
   {      
    	$q = $this->_db->prepare('INSERT INTO type_prestation SET libelle = :libelle, prix = :prix, section_id = :section, id_prestation_parent = :id_prestation_parent');
        $q->bindValue(':libelle', $presta->libelle());
	$q->bindValue(':prix', $presta->prix());
	$q->bindValue(':section', $presta->section_id());
	$q->bindValue(':id_prestation_parent', $presta->id_prestation_parent());
        $q->execute();
	}	
    
        /**
         * Méthode permettant de modifier une prestation
         * @param $presta Prestation la prestation à modifier
         * @return void
         */
    
    public function modifierPrestation(Prestation $presta)   
    {
    	$q = $this->_db->prepare('UPDATE type_prestation SET libelle = :libelle, prix = :prix WHERE id_prestation = :id AND section_id = :section');
        $q->bindValue(':libelle', $presta->libelle());
        $q->bindValue(':prix', $presta->prix());
        $q->bindValue(':id', $presta->id_prestation(), PDO::PARAM_INT);
	$q->bindValue(':section', $presta->section_id());
        $q->execute();
    }
        
	/**
		Méthode pour déterminer si on doit mettre à jour un enregistrement ou ajouter une nouvelle prestation
	**/
	public function savePrestation(Prestation $presta)
        {
            if ($presta->libelleValide())
            {
                $presta->isNew() ? $this->ajouterPrestation($presta) : $this->modifierPrestation($presta);
            }
            else
            {
                throw new RuntimeException('La prestation doit être valide pour être enregistrée');
            }
        }
        
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
	
} // fin de la class
?>
