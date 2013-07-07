<?php
/** Esecouristes - Juin 2013 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Classe servant à gérer les prestations pour les factures pour l'application esecouristes
**/

class PrestationDevisManager extends PrestationDevis
{
	// Déclaration des variables
	protected $_db;

	public function __construct(PDO $db) {
		$this->_db = $db;
	}

	/**
	  *	Méthode pour lister les prestations d'un devis
	  * @param $id_devis l'identifiant du devis concerné
	  * @return la liste des prestations d'un devis spécifique
	  */
	
	public function listerPrestationsDevis($id_devis)
	{
		$listePrestationsDevis = array();
		$sql = $this->_db->prepare('SELECT id, id_devis, id_prestation, quantite, sous_total FROM devis_prestation WHERE id_devis = :id_devis');
		$sql->execute(array(':id_devis' => $id_devis));
		while ($prestationsDevis = $sql->fetch(PDO::FETCH_ASSOC))
				{
					$listePrestationsDevis[] = new PrestationDevis($prestationsDevis);
				}

		return $listePrestationsDevis;

	}	

        public function enregistrerPrestationDevis(PrestationDevis $prestation_devis) {
            $q = $this->_db->prepare('INSERT INTO devis_prestation SET id_devis = :id_devis, id_prestation = :id_prestation, quantite = :quantite, sous_total = :sous_total');
            //print_r($prestation_devis->id_devis());
            //print_r($prestation_devis->id_prestation());
            //print_r($prestation_devis->quantite());
            //print_r($prestation_devis->sous_total());
            $q->bindValue(':id_devis', $prestation_devis->id_devis());
            $q->bindValue(':id_prestation', $prestation_devis->id_prestation());
            $q->bindValue(':quantite', $prestation_devis->quantite());
            $q->bindValue(':sous_total', $prestation_devis->sous_total());
            //print_r($q);
            $q->execute();
        }


}
?>
