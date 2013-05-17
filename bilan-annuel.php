<?php

/** Application esecouristes
Vanessa Kovalsky David - Janvier 2013 (vanessa.kovalsky@free.fr)
Page pour générér un bilan annuel pour envoyer au ministère pour obtenir l'agrément MSC.
Licence GNU/GPL V3.

**/

include_once ("bilan-sql.php");

//echo $section;

function bilan_annuel($section, $subsection, $year) {

// On définit une variable globale pour mettre l'ensemble des éléments
$bilan = array();

$evenements = nb_evenement_par_mois($section, $year);
//print_r($evenements);
//echo $evenements['months'];

foreach ($evenements as $evenement)
	{
		
		$month = $evenement['months'];
		//echo $month;
		// On récupère le nombre d'évènement : 
		
		$plaie = $evenement['NB1_1'];
		if (!$plaie) {
			$plaie = 0;
		}
		$trauma = $evenement['NB1_2'];
		if (!$trauma) {
			$trauma = 0;
		}
		$malaise = $evenement['NB1_3'];
		if (!$malaise) {
			$malaise = 0;
		}
		$pci = $evenement['NB1_4'];
		if (!$pci) {
			$pci = 0;
		}		
		$acr = $evenement['NB1_5'];
		if (!$acr) {
			$acr = 0;
		}
		$autres_inter = $evenement['NB1_6'];
		if (!$autres_inter) {
			$autres_inter = 0;
		}
		$inters = $evenement['NB1'];
		if (!$inters) {
			$inters = 0;
		}
		$evac_vpsp = $evenement['NB2_1'];
		if (!$evac_vpsp) {
			$evac_vpsp = 0;
		}
		$evac_autres = $evenement['NB2_2'];
		if (!$evac_autres) {
			$evac_autres = 0;
		}
		$evacs = $evenement['NB2'];
		if (!$evacs) {
			$evacs = 0;
		}

// On récupère les données depuis la base de données
		
	$paps = nb_paps($section, $month, $year);
	$dpspe = nb_dpspe($section, $month, $year);
	$dpsme = nb_dpsme($section, $month, $year);
	$dpsge = nb_dpsge($section, $month, $year);
	$dpsgr = nb_dpsgr($section, $month, $year);
	$grpp = nb_grpp($section, $month, $year);
	$missionA = mission_a($section, $month, $year);
	$missionB = mission_b($section, $month, $year);
	$missionC = mission_c($section, $month, $year);
	$missionD = mission_d($section, $month, $year);
	$heures = heures_is($section, $month, $year);
	$heures_orange = heures_orange($section, $month, $year);
	$heures_lp_corporate = heures_lp_corporate($section, $month, $year);
	$heures_lp_colis = heures_lp_colis($section, $month, $year);
	$heures_lp_enseigne = heures_lp_enseigne($section, $month, $year);
	$heures_lp_sf = heures_lp_sf($section, $month, $year);
	$heures_lp_courrier = heures_lp_courrier($section, $month, $year);
	$heures_autres = heures_autres($section, $month, $year);
	$equipiers = nb_equipiers($section, $month, $year);
	$equipiers_orange = equipiers_orange($section, $month, $year);
	$equipiers_laposte = 0;
	//$equipiers_laposte = equipiers_lp($section, $month, $year);
	$equipiers_courrier = equipiers_lp_courrier($section, $month, $year);
	$equipiers_colis = equipiers_lp_colis($section, $month, $year);
	$equipiers_enseigne = equipiers_lp_enseigne($section, $month, $year);
	$equipiers_sf = equipiers_lp_sf($section, $month, $year);
	$equipiers_corporate = equipiers_lp_corporate($section, $month, $year);
	$equipiers_autres = equipiers_autres($section, $month, $year);
	//$month = convertir_mois($month);

	// On calcule le nombre d'unité DSC :
	$unite_dsc = $heures/16;

		$bilan[$month] = array('paps'=>$paps,'dpspe'=>$dpspe,'dpsme'=>$dpsme,'dpsge'=>$dpsge,'dpsgr'=>$dpsgr,'grpp'=>$grpp,'missionA'=>$missionA,'missionB'=>$missionB,'missionC'=>$missionC,'missionD'=>$missionD,'heures'=>$heures,'heures_orange'=>$heures_orange,'heures_lp_corporate'=>$heures_lp_corporate,'heures_lp_colis'=>$heures_lp_colis,'heures_lp_enseigne'=>$heures_lp_enseigne,'heures_lp_sf'=>$heures_lp_sf,'heures_lp_courrier'=>$heures_lp_courrier,'heures_autres'=>$heures_autres,'equipiers'=>$equipiers,'equipiers_orange'=>$equipiers_orange,'equipiers_laposte'=>$equipiers_laposte,'equipiers_courrier'=>$equipiers_courrier,'equipiers_colis'=>$equipiers_colis,'equipiers_enseigne'=>$equipiers_enseigne,'equipiers_sf'=>$equipiers_sf,'equipiers_corporate'=>$equipiers_corporate,'equipiers_autres'=>$equipiers_autres,'plaie'=>$plaie,'trauma'=>$trauma,'malaise'=>$malaise,'pci'=>$pci,'acr'=>$acr,'autres_inter'=>$autres_inter,'inters'=>$inters,'evac_vpsp'=>$evac_vpsp,'evac_autres'=>$evac_autres,'evacs'=>$evacs,'unite_dsc'=>$unite_dsc);
		//$bilan = array_merge($bilan, $bilan_new);
		//print_r($bilan);
		//return $bilan;

	// Calcul du total 
		$paps_total = $paps_total + $paps;
		$dpspe_total = $dpspe_total + $dpspe;
		$dpsme_total = $dpsme_total + $dpsme;
		$dpsge_total = $dpsge_total + $dpsge;
		$dpsgr_total = $dpsgr_total + $dpsgr;
		$grpp_total = $grpp_total + $grpp;
		$missionA_total = $missionA_total + $missionA;
		$missionB_total = $missionB_total + $missionB;
		$missionC_total = $missionC_total + $missionC;
		$missionD_total = $missionD_total + $missionD;
		$heures_total = $heures_total + $heures;
		$heures_orange_total = $heures_orange_total + $heures_orange;
		$heures_lp_corporate_total = $heures_lp_corporate_total + $heures_lp_corporate;
		$heures_lp_colis_total = $heures_lp_colis_total + $heures_lp_colis;
		$heures_lp_enseigne_total = $heures_lp_enseigne_total + $heures_lp_enseigne;
		$heures_lp_sf_total = $heures_lp_sf_total + $heures_lp_sf;
		$heures_lp_courrier_total = $heures_lp_courrier_total + $heures_lp_courrier;
		$heures_autres_total = $heures_autres_total + $heures_autres;
		$equipiers_total = $equipiers_total + $equipiers;
		$equipiers_orange_total = $equipiers_orange_total + $equipiers_orange;
		$equipiers_laposte_total = $equipiers_laposte_total + $equipiers_laposte;
		$equipiers_courrier_total = $equipiers_courrier_total + $equipiers_courrier;
		$equipiers_colis_total = $equipiers_colis_total + $equipiers_colis;
		$equipiers_enseigne_total = $equipiers_enseigne_total + $equipiers_enseigne;
		$equipiers_sf_total = $equipiers_sf_total + $equipiers_sf;
		$equipiers_corporate_total = $equipiers_corporate_total + $equipiers_corporate; 
		$equipiers_autres_total = $equipiers_autres_total + $equipiers_autres; 
		$plaie_total = $plaie_total + $plaie;
		$trauma_total = $trauma_total + $trauma;
		$malaise_total = $malaise_total + $malaise;
		$pci_total = $pci_total + $pci;
		$acr_total = $acr_total + $acr;
		$autres_inter_total = $autres_inter_total + $autres_inter;
		$inters_total = $inters_total + $inters;
		$evac_vpsp_total = $evac_vpsp_total + $evac_vpsp;
		$evac_autres_total = $evac_autres_total + $evac_autres;
		$evacs_total = $evacs_total + $evacs;
		$unite_dsc_total = $unite_dsc_total + $unite_dsc;

} //fin du while */
		
		$bilan['total'] = array('paps'=>$paps_total,'dpspe'=>$dpspe_total,'dpsme'=>$dpsme_total,'dpsge'=>$dpsge_total,'dpsgr'=>$dpsgr_total,'grpp'=>$grpp_total,'missionA'=>$missionA_total,'missionB'=>$missionB_total,'missionC'=>$missionC_total,'missionD'=>$missionD_total,'heures'=>$heures_total,'heures_orange'=>$heures_orange_total,'heures_lp_corporate'=>$heures_lp_corporate_total,'heures_lp_colis'=>$heures_lp_colis_total,'heures_lp_enseigne'=>$heures_lp_enseigne_total,'heures_lp_sf'=>$heures_lp_sf_total,'heures_lp_courrier'=>$heures_lp_courrier_total,'heures_autres'=>$heures_autres_total,'equipiers'=>$equipiers_total,'equipiers_orange'=>$equipiers_orange_total,'equipiers_laposte'=>$equipiers_laposte_total,'equipiers_courrier'=>$equipiers_courrier_total,'equipiers_colis'=>$equipiers_colis_total,'equipiers_enseigne'=>$equipiers_enseigne_total,'equipiers_sf'=>$equipiers_sf_total,'equipiers_corporate'=>$equipiers_corporate_total,'equipiers_autres'=>$equipiers_autres_total,'plaie'=>$plaie_total,'trauma'=>$trauma_total,'malaise'=>$malaise_total,'pci'=>$pci_total,'acr'=>$acr_total,'autres_inter'=>$autres_inter_total,'inters'=>$inters_total,'evac_vpsp'=>$evac_vpsp_total,'evac_autres'=>$evac_autres_total,'evacs'=>$evacs_total,'unite_dsc'=>$unite_dsc_total);
		
		//print_r($bilan);
		//var_dump($bilan);
		return $bilan;
}// fin bilan_annuel

?>
