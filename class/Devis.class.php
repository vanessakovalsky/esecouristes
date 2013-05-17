<?php
/** Esecouristes - Mars 2013 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Classe servant à créer les factures pour l'application esecouristes
**/

class Devis {

// Déclaration des variables

	protected $id_devis,
		  $date_devis,
		  $section_id,
		  $evenement_id,
		  $evenement_nom,
		  $numero_devis,
		  $remise_globale,
		  $montant,
		  $organisateur_id,
		  $commentaire,
		  $statut_devis;

	public function __construct(array $donnees) {
        	$this->hydrate($donnees);
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
	
	public function montantValide() {
		return !empty($this->_montant);
	}

	/* Getters */
	
	public function id_devis() {
		return $this->_id_devis;
	}
	
	public function date_devis() {
		return $this->_date_devis;
	}

	public function section_id() {
		return $this->_section_id;
	}

	public function evenement_id() {
		return $this->_evenement_id;
	}

	public function evenement_nom() {
		return $this->_evenement_nom;
	}

	public function numero_devis() {
		return $this->_numero_devis;
	}

	public function remise_globale() {
		return $this->_remise_globale;
	}

	public function montant() {
		return $this->_montant;
	}

	public function organisateur_id() {
		return $this->_organisateur_id;
	}

	public function commentaire() {
		return $this->_commentaire;
	}

	public function statut_devis() {
		return $this->statut_devis;
	}
	
	/* Setters */
	
	public function setId_devis($id_devis) {
		return $this->_id_devis = $id_devis;
	}
	
	public function setDate_devis($date_devis) {
		return $this->_date_devis = $date_devis;
	}

	public function setSection_id($section_id) {
		return $this->_section_id = $section_id;
	}

	public function setEvenement_id($evenement_id) {
		return $this->_evenement_id = $evenement_id;
	}

	public function setEvenement_nom($evenement_nom) {
		return $this->_evenement_nom = $evenement_nom;
	}

	public function setNumero_devis($numero_devis) {
		return $this->_numero_devis = $numero_devis;
	}

	public function setRemise_globale($remise_globale) {
		return $this->_remise_globale = $remise_globale;
	}

	public function setMontant($montant) {
		return $this->_montant = $montant;
	}

	public function setOrganisateur_id($organisateur_id) {
		return $this->_organisateur_id = $organisateur_id;
	}
	
	public function setCommentaire($commentaire) {
		return $this->_commentaire = $commentaire;
	}

	public function setStatut_devis($statut_devis) {
		return $this->statut_devis = $statut_devis;
	}

	/**
         * Méthode permettant de savoir si le devis est nouveau
         * @return bool
         */
    public function isNew()
        {
            return empty($this->_id_devis);
        }


	/* Fonctions spécifiques à la classe Devis */


	/*function envoyerDevis {
	}

	function accepterDevis {
	}

	function refuserDevis {
	}

	function transformerDevisFacture {
	}

	function payerFacture {
	}
	
	function relanceFacture {
	}
*/
}
?>
