<?php
/** Esecouristes - Mars 2013 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3
Classe servant à gérer les prestations pour les factures pour l'application esecouristes
**/

class DevisManager extends Devis
{
	// Déclaration des variables
	protected $_db;

	public function __construct(PDO $db) {
		$this->_db = $db;
	}

	function voirDevis($id) {
		$id = (int) $id;
        	$q = $this->_db->query('SELECT devis.id AS id_devis, devis.date AS date_devis, devis.commentaire, evenement.E_CODE AS evenement_id, evenement.E_LIBELLE AS evenement_nom, numero AS numero_devis, remise_globale, montant, status AS status_devis, S_ID AS section_id, C_ID AS organisateur_id FROM devis, evenement WHERE devis.id = '.$id.' AND evenement.E_CODE = devis.evt_id');
        	$donnees = $q->fetch(PDO::FETCH_ASSOC);
                
            return new Devis($donnees);
	}

	public function listerDevis($section) {

		$listeDevis = array();
		$sql = $this->_db->prepare('SELECT id AS id_devis, commentaire, montant, evenement.E_LIBELLE AS evenement_nom, status AS status_devis FROM devis, evenement WHERE devis.evt_id = evenement.E_CODE AND S_ID = :section');
		$sql->execute(array(':section' => $section));
		while ($devis = $sql->fetch(PDO::FETCH_ASSOC))
				{
					$listeDevis[] = new Devis($devis);
				}
		return $listeDevis;
	}

         /* Méthode permettant de créer un devis
         * @param $devis Le devis à créer
         * @return void
         */

	function creerDevis(Devis $devis) {
		$q = $this->_db->prepare('INSERT INTO devis SET commentaire = :commentaire, evt_id = :evt_id, date = :date, numero = :numero, remise_globale = :remise_globale, montant = :montant, status = :status_devis');
        	$q->bindValue(':commentaire', $devis->commentaire());
		$q->bindValue(':evt_id', $devis->evenement_id());
		$q->bindValue(':date', $devis->date_devis());
		$q->bindValue(':numero', $devis->numero_devis());
		$q->bindValue(':remise_globale', $devis->remise_globale());	
		$q->bindValue(':montant', $devis->montant());        	
		$q->bindValue(':statut_devis', $devis->statut_devis());		
		$q->execute();

	}

         /* Méthode permettant d'enregistrer un devis
         * @param $devis Le devis à modifier
         * @return void
         */

	function modifierDevis(Devis $devis) {
		$q = $this->_db->prepare('UPDATE devis SET commentaire = :commentaire, evt_id = :evt_id, date = :date, numero = :numero, remise_globale = :remise_globale, montant = :montant, status = :statut_devis WHERE id = :id');
        	$q->bindValue(':commentaire', $devis->commentaire());
		$q->bindValue(':evt_id', $devis->evenement_id());
		$q->bindValue(':date', $devis->date_devis());
		$q->bindValue(':numero', $devis->numero_devis());
		$q->bindValue(':remise_globale', $devis->remise_globale());	
		$q->bindValue(':montant', $devis->montant());        	
		$q->bindValue(':statut_devis', $devis->statut_devis());
		$q->bindValue(':id', $devis->id_devis());		
		$q->execute();

	}

	/**
		Méthode pour déterminer si on doit mettre à jour un enregistrement ou ajouter un nouveau devis
	**/
	public function saveDevis(Devis $devis)
        {
                $devis->isNew() ? $this->creerDevis($devis) : $this->modifierDevis($devis);

        }

	
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
	
} // fin de la class
?>
