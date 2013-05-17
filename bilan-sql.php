<?php

/** Application esecouristes
Vanessa Kovalsky David - Novembre 2012 (vanessa.kovalsky@free.fr)
Page pour générér un bilan annuel pour envoyer au ministère pour obtenir l'agrément MSC.
Licence GNU/GPL V3.

**/

//include('config.php');

global $bdd;
$bdd = connexion_bdd();
//echo $section;

// On récupère les données

// eclater les sections pour pouvoirs gérer les requetes en cas de sous-section
if ($section) {
global $inQuery;
$inQuery = implode(',', array_fill(0, count($section), '?'));
//echo $inQuery;
}
else {
$inQuery = '?';
}

// Celle liée aux évènements'

function nb_evenement_par_mois($section,$year) {

global $bdd;
global $inQuery;
//print_r($section);
$requete_compte_evenement = $bdd->prepare("SELECT COUNT( e.E_CODE ) as nb_eve , e.TAV_ID as types, SUM(eh.EH_DUREE), SUM(e.E_NB1) as NB1, SUM(e.E_NB1_1) as NB1_1, SUM(e.E_NB1_2) as NB1_2, SUM(e.E_NB1_3) as NB1_3, SUM(e.E_NB1_4) as NB1_4, SUM(e.E_NB1_5) as NB1_5, SUM(e.E_NB1_6) as NB1_6, SUM(e.E_NB2) as NB2, SUM(e.E_NB2_1) as NB2_1, SUM(e.E_NB2_2) as NB2_2, SUM(e.E_NB3), MONTH(eh.EH_DATE_DEBUT) as months
FROM evenement e, evenement_horaire eh
WHERE e.E_CODE = eh.E_CODE
AND YEAR( eh.EH_DATE_DEBUT ) = ?
AND e.S_ID IN (" . $inQuery . ")
AND eh.EH_ID =1
GROUP BY MONTH(eh.EH_DATE_DEBUT)
ORDER BY MONTH(eh.EH_DATE_DEBUT)");
		
	$requete_compte_evenement->bindValue(1, $year, PDO::PARAM_INT);
	//echo $section;
	if (is_array($section)) {
		$number_section = 1;
		foreach ($section as $k => $id) {
			$number_section = $number_section+1;
			$requete_compte_evenement->bindValue($number_section, $id);
		}
	}
	else {
		$requete_compte_evenement->bindValue(2, $section, PDO::PARAM_INT);
	}

$requete_compte_evenement->execute();

$evenements = $requete_compte_evenement->fetchAll();

	return $evenements;
} // fin nb_evenement_par_mois


function nb_evenement_par_section($year) {

global $bdd;
global $inQuery;
//print_r($section);
$requete_compte_evenement_section = $bdd->prepare("SELECT COUNT( e.E_CODE ) as nb_eve , e.TAV_ID as types, SUM(eh.EH_DUREE), SUM(e.E_NB1) as NB1, SUM(e.E_NB1_1) as NB1_1, SUM(e.E_NB1_2) as NB1_2, SUM(e.E_NB1_3) as NB1_3, SUM(e.E_NB1_4) as NB1_4, SUM(e.E_NB1_5) as NB1_5, SUM(e.E_NB1_6) as NB1_6, SUM(e.E_NB2) as NB2, SUM(e.E_NB2_1) as NB2_1, SUM(e.E_NB2_2) as NB2_2, SUM(e.E_NB3), e.S_ID, s.S_CODE, s.S_DESCRIPTION as section
FROM evenement e, evenement_horaire eh, section s
WHERE e.E_CODE = eh.E_CODE
AND YEAR( eh.EH_DATE_DEBUT ) = ?
AND e.S_ID = s.S_ID
AND eh.EH_ID =1
GROUP BY e.S_ID
ORDER BY s.S_CODE");
		
	$requete_compte_evenement_section->bindValue(1, $year, PDO::PARAM_INT);

$requete_compte_evenement_section->execute();

$evenements_par_section = $requete_compte_evenement_section->fetchAll();

	return $evenements_par_section;
} // fin nb_evenement_par_mois

function mission_A($section, $month, $year) {
	
	global $bdd;
	global $inQuery;
	//On récupère le nombre de mission A pour le mois sélectionner
		$A_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code IN (SELECT TE_CODE FROM type_evenement WHERE CEV_CODE="C_SEC")';
		if ($month) {		
			$A_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$A_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$missionA_requete = $bdd->prepare($A_prepare);
		if ($month) {
			$missionA_requete->bindValue(1, $month, PDO::PARAM_INT);
			$missionA_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$missionA_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$missionA_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$missionA_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$missionA_requete->bindValue(2, $section, PDO::PARAM_INT);
			}			
		}
		$missionA_query = $missionA_requete->execute();
		$missionA = $missionA_requete->fetchColumn();

	return $missionA;
}

function mission_B($section, $month, $year) {

	global $bdd;
	global $inQuery;
	//On récupère le nombre de mission B pour le mois sélectionner
		$B_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code IN (SELECT TE_CODE FROM type_evenement WHERE CEV_CODE="C_OPE")';
		if ($month) {		
			$B_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$B_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$missionB_requete = $bdd->prepare($B_prepare);
		if ($month) {
			$missionB_requete->bindValue(1, $month, PDO::PARAM_INT);
			$missionB_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$missionB_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$missionB_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$missionB_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$missionB_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}			
		$missionB_query = $missionB_requete->execute();
		$missionB = $missionB_requete->fetchColumn();

	return $missionB;
}

function mission_C($section, $month, $year) {
	global $bdd;
	global $inQuery;
	//On récupère le nombre de mission C pour le mois sélectionner
		$C_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code IN (SELECT TE_CODE FROM type_evenement WHERE CEV_CODE="C_ENC")';
		if ($month) {		
			$C_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$C_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$missionC_requete = $bdd->prepare($C_prepare);
		if ($month) {
			$missionC_requete->bindValue(1, $month, PDO::PARAM_INT);
			$missionC_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$missionC_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$missionC_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$missionC_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$missionC_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}			
		$missionC_query = $missionC_requete->execute();
		$missionC = $missionC_requete->fetchColumn();

	return $missionC;
}

function nb_paps($section, $month, $year) {
	//On récupère le nombre de PAPS pour le mois sélectionner
	global $bdd;
	global $inQuery;
		$paps_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code = "DPS" AND e.E_NB <=2 ';
		if ($month) {		
			$paps_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$paps_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$paps_requete = $bdd->prepare($paps_prepare);
		if ($month) {
			$paps_requete->bindValue(1, $month, PDO::PARAM_INT);
			$paps_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$paps_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$paps_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$paps_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$paps_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$paps_query = $paps_requete->execute();
		$paps = $paps_requete->fetchColumn();

	return $paps;
} // fin nb_paps

function nb_dpspe($section, $month, $year) {
	global $bdd;
	global $inQuery;
	//On récupère le nombre de DPS-PE pour le mois sélectionner
		$dpspe_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code = "DPS" AND e.E_NB BETWEEN 3 AND 12 ';
		if ($month) {		
			$dpspe_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$dpspe_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$dpspe_requete = $bdd->prepare($dpspe_prepare);
		if ($month) {
			$dpspe_requete->bindValue(1, $month, PDO::PARAM_INT);
			$dpspe_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$dpspe_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$dpspe_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$dpspe_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$dpspe_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$dpspe_query = $dpspe_requete->execute();
		$dpspe = $dpspe_requete->fetchColumn();

	return $dpspe;
} // fin nb_dpspe

function nb_dpsme($section,$month,$year) {
	global $bdd;	
	global $inQuery;
	//On récupère le nombre DPS-ME pour le mois sélectionner
		$dpsme_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code = "DPS" AND e.E_NB BETWEEN 13 AND 36 ';
	if ($month) {		
			$dpsme_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$dpsme_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$dpsme_requete = $bdd->prepare($dpsme_prepare);
		if ($month) {
			$dpsme_requete->bindValue(1, $month, PDO::PARAM_INT);
			$dpsme_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$dpsme_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$dpsme_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$dpsme_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$dpsme_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$dpsme_query = $dpsme_requete->execute();
		$dpsme = $dpsme_requete->fetchColumn();

	return $dpsme;
}

function nb_dpsge($section, $month, $year) {
	global $bdd;
	global $inQuery;	
	//On récupère le nombre de DPS-GE pour le mois sélectionner
		$dpsge_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code = "DPS" AND e.E_NB BETWEEN 37 AND 70 ';
		if ($month) {		
			$dpsge_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$dpsge_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$dpsge_requete = $bdd->prepare($dpsge_prepare);
		if ($month) {
			$dpsge_requete->bindValue(1, $month, PDO::PARAM_INT);
			$dpsge_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$dpsge_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$dpsge_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$dpsge_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$dpsge_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$dpsge_query = $dpsge_requete->execute();
		$dpsge = $dpsge_requete->fetchColumn();
	return $dpsge;
} // fin nb_dpsge

function nb_dpsgr($section, $month, $year) {
	global $bdd;
	global $inQuery;
	//On récupère le nombre de DPS-GR pour le mois sélectionner
		$dpsgr_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code = "DPS" AND e.E_NB > 70 ';
		if ($month) {		
			$dpsgr_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$dpsgr_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$dpsgr_requete = $bdd->prepare($dpsgr_prepare);
		if ($month) {
			$dpsgr_requete->bindValue(1, $month, PDO::PARAM_INT);
			$dpsgr_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$dpsgr_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$dpsgr_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$dpsgr_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$dpsgr_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$dpsgr_query = $dpsgr_requete->execute();
		$dpsgr = $dpsgr_requete->fetchColumn();
	return $dpsgr;
} // fin nb_dpsgr

function nb_grpp($section, $month, $year) {
	global $bdd;
	global $inQuery;
	//On récupère le nombre de GR-PP pour le mois sélectionner
		$grpp_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code = "DPS" AND e.E_NB > 70 AND e.E_PP=1 ';
		if ($month) {		
			$grpp_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$grpp_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$grpp_requete = $bdd->prepare($grpp_prepare);
		if ($month) {
			$grpp_requete->bindValue(1, $month, PDO::PARAM_INT);
			$grpp_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$grpp_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$grpp_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$grpp_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$grpp_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$grpp_query = $grpp_requete->execute();
		$grpp = $grpp_requete->fetchColumn();
	return $grpp;
} // nb_grpp

function mission_d($section, $month, $year) {
	global $bdd;	
	global $inQuery;
	//On récupère le nombre de mission D pour le mois sélectionner
		$missionD_prepare = 'SELECT COUNT(e.E_CODE) FROM evenement e, evenement_horaire eh WHERE e.E_CODE=eh.E_CODE AND eh.EH_ID =1
AND e.te_code = "DPS" ';
		if ($month) {		
			$missionD_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$missionD_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$missionD_requete = $bdd->prepare($missionD_prepare);
		if ($month) {
			$missionD_requete->bindValue(1, $month, PDO::PARAM_INT);
			$missionD_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$missionD_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$missionD_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$missionD_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$missionD_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$missionD_query = $missionD_requete->execute();
		$missionD = $missionD_requete->fetchColumn();
	return $missionD;
}// fin mission_D

	// Heures réalisées


function heures_orange($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Heures Orange - France Télécom
		$heures_orange_prepare = 'SELECT sum(eh.eh_duree) FROM evenement e, evenement_participation ep, section s, evenement_horaire eh, pompier p WHERE e.e_code = ep.e_code AND e.s_id = s.s_id AND ep.e_code = eh.e_code AND ep.eh_id = eh.eh_id AND e.E_CANCELED = 0 AND ep.P_ID = p.P_ID AND p.C_ID = 109 ';
		if ($month) {		
			$heures_orange_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$heures_orange_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND s.S_ID IN(' . $inQuery .')';
		$heures_orange_requete = $bdd->prepare($heures_orange_prepare);
		if ($month) {
			$heures_orange_requete->bindValue(1, $month, PDO::PARAM_INT);
			$heures_orange_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$heures_orange_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$heures_orange_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$heures_orange_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$heures_orange_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$heures_orange_query = $heures_orange_requete->execute();
		$heures_orange = $heures_orange_requete->fetchColumn();
		if (!$heures_orange) {
			$heures_orange = 0;
		}

	return $heures_orange;
} // fin heures_orange

function heures_lp_corporate($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Heures La Poste Corporate
		$heures_lp_corporate_prepare = 'SELECT sum(eh.eh_duree) FROM evenement e, evenement_participation ep, section s, evenement_horaire eh, pompier p WHERE e.e_code = ep.e_code AND e.s_id = s.s_id AND ep.e_code = eh.e_code AND ep.eh_id = eh.eh_id AND e.E_CANCELED = 0 AND ep.P_ID = p.P_ID AND p.C_ID = 13 ';	
		if ($month) {		
			$heures_lp_corporate_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$heures_lp_corporate_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND s.S_ID IN(' . $inQuery .')';
		$heures_lp_corporate_requete = $bdd->prepare($heures_lp_corporate_prepare);
		if ($month) {
			$heures_lp_corporate_requete->bindValue(1, $month, PDO::PARAM_INT);
			$heures_lp_corporate_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$heures_lp_corporate_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$heures_lp_corporate_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$heures_lp_corporate_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$heures_lp_corporate_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$heures_lp_corporate_query = $heures_lp_corporate_requete->execute();
		$heures_lp_corporate = $heures_lp_corporate_requete->fetchColumn();
		if (!$heures_lp_corporate) {
			$heures_lp_corporate = 0;
		}
	return $heures_lp_corporate;
}

function heures_lp_colis($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Heures La Poste Colis
		$heures_lp_colis_prepare = 'SELECT sum(eh.eh_duree) FROM evenement e, evenement_participation ep, section s, evenement_horaire eh, pompier p WHERE e.e_code = ep.e_code AND e.s_id = s.s_id AND ep.e_code = eh.e_code AND ep.eh_id = eh.eh_id AND e.E_CANCELED = 0 AND ep.P_ID = p.P_ID AND p.C_ID = 11 ';	
		if ($month) {		
			$heures_lp_colis_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$heures_lp_colis_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND s.S_ID IN(' . $inQuery .')';
		$heures_lp_colis_requete = $bdd->prepare($heures_lp_colis_prepare);
		if ($month) {
			$heures_lp_colis_requete->bindValue(1, $month, PDO::PARAM_INT);
			$heures_lp_colis_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$heures_lp_colis_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$heures_lp_colis_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$heures_lp_colis_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$heures_lp_colis_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$heures_lp_colis_query = $heures_lp_colis_requete->execute();
		$heures_lp_colis = $heures_lp_colis_requete->fetchColumn();
		if (!$heures_lp_colis) {
			$heures_lp_colis = 0;
		}
	return $heures_lp_colis;
} // fin heures_lp_colis

function heures_lp_enseigne($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Heures La Poste Enseigne
		$heures_lp_enseigne_prepare = 'SELECT sum(eh.eh_duree) FROM evenement e, evenement_participation ep, section s, evenement_horaire eh, pompier p WHERE e.e_code = ep.e_code AND e.s_id = s.s_id AND ep.e_code = eh.e_code AND ep.eh_id = eh.eh_id AND e.E_CANCELED = 0 AND ep.P_ID = p.P_ID AND p.C_ID = 12 ';	
		if ($month) {		
			$heures_lp_enseigne_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$heures_lp_enseigne_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND s.S_ID IN(' . $inQuery .')';
		$heures_lp_enseigne_requete = $bdd->prepare($heures_lp_enseigne_prepare);
		if ($month) {
			$heures_lp_enseigne_requete->bindValue(1, $month, PDO::PARAM_INT);
			$heures_lp_enseigne_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$heures_lp_enseigne_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$heures_lp_enseigne_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$heures_lp_enseigne_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$heures_lp_enseigne_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$heures_lp_enseigne_query = $heures_lp_enseigne_requete->execute();
		$heures_lp_enseigne = $heures_lp_enseigne_requete->fetchColumn();
		if (!$heures_lp_enseigne) {
			$heures_lp_enseigne = 0;
		}
	return $heures_lp_enseigne;
} // fin heures_lp_enseigne

function heures_lp_sf($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Heures La Poste SF
		$heures_lp_sf_prepare = 'SELECT sum(eh.eh_duree) FROM evenement e, evenement_participation ep, section s, evenement_horaire eh, pompier p WHERE e.e_code = ep.e_code AND e.s_id = s.s_id AND ep.e_code = eh.e_code AND ep.eh_id = eh.eh_id AND e.E_CANCELED = 0 AND ep.P_ID = p.P_ID AND p.C_ID = 14 ';	
		
		if ($month) {		
			$heures_lp_sf_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$heures_lp_sf_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND s.S_ID IN(' . $inQuery .')';
		$heures_lp_sf_requete = $bdd->prepare($heures_lp_sf_prepare);
		if ($month) {
			$heures_lp_sf_requete->bindValue(1, $month, PDO::PARAM_INT);
			$heures_lp_sf_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$heures_lp_sf_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$heures_lp_sf_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$heures_lp_sf_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$heures_lp_sf_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$heures_lp_sf_query = $heures_lp_sf_requete->execute();
		$heures_lp_sf = $heures_lp_sf_requete->fetchColumn();
		if (!$heures_lp_sf) {
			$heures_lp_sf = 0;
		}
	return $heures_lp_sf;
} // fin heures_lp_sf

function heures_lp_courrier($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Heures La Poste Courrier
		$heures_lp_courrier_prepare = 'SELECT sum(eh.eh_duree) FROM evenement e, evenement_participation ep, section s, evenement_horaire eh, pompier p WHERE e.e_code = ep.e_code AND e.s_id = s.s_id AND ep.e_code = eh.e_code AND ep.eh_id = eh.eh_id AND e.E_CANCELED = 0 AND ep.P_ID = p.P_ID AND p.C_ID = 10 ';	
		if ($month) {		
			$heures_lp_courrier_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$heures_lp_courrier_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND s.S_ID IN(' . $inQuery .')';
		$heures_lp_courrier_requete = $bdd->prepare($heures_lp_courrier_prepare);
		if ($month) {
			$heures_lp_courrier_requete->bindValue(1, $month, PDO::PARAM_INT);
			$heures_lp_courrier_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$heures_lp_courrier_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$heures_lp_courrier_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$heures_lp_courrier_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$heures_lp_courrier_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}				
		$heures_lp_courrier_query = $heures_lp_courrier_requete->execute();
		$heures_lp_courrier = $heures_lp_courrier_requete->fetchColumn();
		if (!$heures_lp_courrier) {
			$heures_lp_courrier = 0;
		}
	return $heures_lp_courrier;
} // fin heures_lp_courrier

function heures_autres($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Heures Autres
		$heures_autres_prepare = 'SELECT sum(eh.eh_duree) FROM evenement e, evenement_participation ep, section s, evenement_horaire eh, pompier p WHERE e.e_code = ep.e_code AND e.s_id = s.s_id AND ep.e_code = eh.e_code AND ep.eh_id = eh.eh_id AND e.E_CANCELED = 0 AND ep.P_ID = p.P_ID AND p.C_ID NOT IN(10,11,12,13,14,109) ';	
		if ($month) {		
			$heures_autres_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$heures_autres_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND s.S_ID IN(' . $inQuery .')';
		$heures_autres_requete = $bdd->prepare($heures_autres_prepare);
		if ($month) {
			$heures_autres_requete->bindValue(1, $month, PDO::PARAM_INT);
			$heures_autres_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$heures_autres_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$heures_autres_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$heures_autres_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$heures_autres_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}			
		$heures_autres_query = $heures_autres_requete->execute();
		$heures_autres = $heures_autres_requete->fetchColumn();
		if (!$heures_autres) {
			$heures_autres = 0;
		}
	return $heures_autres;
} // fin heures_autres

function heures_is($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// On récupère le nombre d'heure d'intervenant secouristes
		$heures_prepare = 'SELECT sum(eh.eh_duree) FROM evenement e, evenement_participation ep, section s, evenement_horaire eh, pompier p WHERE e.e_code = ep.e_code and e.s_id = s.s_id and ep.e_code = eh.e_code and ep.eh_id = eh.eh_id AND e.E_CANCELED = 0 AND ep.P_ID = p.P_ID ';
		if ($month) {		
			$heures_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$heures_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND s.S_ID IN(' . $inQuery .')';
		$heures_requete = $bdd->prepare($heures_prepare);
		if ($month) {
			$heures_requete->bindValue(1, $month, PDO::PARAM_INT);
			$heures_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$heures_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$heures_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$heures_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$heures_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}			
		$heures_query = $heures_requete->execute();
		$heures = $heures_requete->fetchColumn();
	return $heures;
} // fin heures_is

	// Nombre d'équipiers

function nb_equipiers($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// On récupère le nombre total d'équipier
		$equipiers_prepare = 'SELECT COUNT( ep.P_ID )
FROM evenement e, evenement_horaire eh, evenement_participation ep
WHERE e.E_CODE = eh.E_CODE
AND eh.EH_ID =1
AND ep.E_CODE = eh.E_CODE
AND e.TE_CODE = "DPS" ';
		if ($month) {		
			$equipiers_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$equipiers_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$equipiers_requete = $bdd->prepare($equipiers_prepare);
		if ($month) {
			$equipiers_requete->bindValue(1, $month, PDO::PARAM_INT);
			$equipiers_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$equipiers_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$equipiers_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$equipiers_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$equipiers_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$equipiers_query = $equipiers_requete->execute();
		$equipiers = $equipiers_requete->fetchColumn();
	return $equipiers;
} // fin nb_equipiers

function equipiers_orange($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Équipiers de Orange - France Télécom
		$equipiers_orange_prepare = 'SELECT COUNT( ep.P_ID )
FROM evenement e, evenement_horaire eh, evenement_participation ep, pompier p
WHERE e.E_CODE = eh.E_CODE
AND eh.EH_ID =1
AND ep.E_CODE = eh.E_CODE
AND e.TE_CODE = "DPS"
AND ep.P_ID = p.P_ID
AND p.C_ID = "109" ';
		if ($month) {		
			$equipiers_orange_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$equipiers_orange_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$equipiers_orange_requete = $bdd->prepare($equipiers_orange_prepare);
		if ($month) {
			$equipiers_orange_requete->bindValue(1, $month, PDO::PARAM_INT);
			$equipiers_orange_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$equipiers_orange_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$equipiers_orange_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$equipiers_orange_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$equipiers_orange_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$equipiers_orange_query = $equipiers_orange_requete->execute();
		$equipiers_orange = $equipiers_orange_requete->fetchColumn();
	return $equipiers_orange;
} // fin equipiers_orange

function equipiers_lp($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Équipiers de La Poste
		/*$equipiers_laposte_requete = $bdd->prepare('SELECT COUNT( ep.P_ID )
FROM evenement e, evenement_horaire eh, evenement_participation ep, pompier p
WHERE MONTH( eh.EH_DATE_DEBUT ) = :month
AND e.E_CODE = eh.E_CODE
AND eh.EH_ID =1
AND ep.E_CODE = eh.E_CODE
AND YEAR( eh.EH_DATE_DEBUT ) = :year
AND e.TE_CODE = "DPS"
AND ep.P_ID = p.P_ID
AND p.C_ID IN(10,11,12,13,14)
AND e.S_ID = :section');
		$equipiers_laposte_query = $equipiers_laposte_requete->execute(array('month'=>$month,'year'=>$year,'section'=>$section));
		$equipiers_laposte = $equipiers_laposte_requete->fetchColumn();*/
	return $equipiers_laposte;
} // fin equipiers_lp

function equipiers_lp_courrier($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Équipiers de La Poste - courrier
		$equipiers_courrier_prepare = 'SELECT COUNT( ep.P_ID )
FROM evenement e, evenement_horaire eh, evenement_participation ep, pompier p
WHERE e.E_CODE = eh.E_CODE
AND eh.EH_ID =1
AND ep.E_CODE = eh.E_CODE
AND e.TE_CODE = "DPS"
AND ep.P_ID = p.P_ID
AND p.C_ID = "10" ';
		if ($month) {		
			$equipiers_courrier_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$equipiers_courrier_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$equipiers_courrier_requete = $bdd->prepare($equipiers_courrier_prepare);
		if ($month) {
			$equipiers_courrier_requete->bindValue(1, $month, PDO::PARAM_INT);
			$equipiers_courrier_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$equipiers_courrier_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$equipiers_courrier_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$equipiers_courrier_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$equipiers_courrier_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}		
		$equipiers_courrier_query = $equipiers_courrier_requete->execute();
		$equipiers_courrier = $equipiers_courrier_requete->fetchColumn();
	return $equipiers_courrier;
} // fin equipiers_lp_courrier

function equipiers_lp_colis($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Équipiers de La Poste - Colis
		$equipiers_colis_prepare = 'SELECT COUNT( ep.P_ID )
FROM evenement e, evenement_horaire eh, evenement_participation ep, pompier p
WHERE e.E_CODE = eh.E_CODE
AND eh.EH_ID =1
AND ep.E_CODE = eh.E_CODE
AND e.TE_CODE = "DPS"
AND ep.P_ID = p.P_ID
AND p.C_ID = "11" ';
		if ($month) {		
			$equipiers_colis_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$equipiers_colis_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$equipiers_colis_requete = $bdd->prepare($equipiers_colis_prepare);
		if ($month) {
			$equipiers_colis_requete->bindValue(1, $month, PDO::PARAM_INT);
			$equipiers_colis_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$equipiers_colis_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$equipiers_colis_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$equipiers_colis_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$equipiers_colis_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$equipiers_colis_query = $equipiers_colis_requete->execute();
		$equipiers_colis = $equipiers_colis_requete->fetchColumn();
	return $equipiers_colis;
} // fin equipiers_lp_colis

function equipiers_lp_enseigne($section, $month, $year) {	
	global $bdd;
	global $inQuery;
		// Équipiers de La Poste - Enseigne
		$equipiers_enseigne_prepare = 'SELECT COUNT( ep.P_ID )
FROM evenement e, evenement_horaire eh, evenement_participation ep, pompier p
WHERE e.E_CODE = eh.E_CODE
AND eh.EH_ID =1
AND ep.E_CODE = eh.E_CODE
AND e.TE_CODE = "DPS"
AND ep.P_ID = p.P_ID
AND p.C_ID = "12" ';
		if ($month) {		
			$equipiers_enseigne_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$equipiers_enseigne_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$equipiers_enseigne_requete = $bdd->prepare($equipiers_enseigne_prepare);
		if ($month) {
			$equipiers_enseigne_requete->bindValue(1, $month, PDO::PARAM_INT);
			$equipiers_enseigne_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$equipiers_enseigne_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$equipiers_enseigne_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$equipiers_enseigne_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$equipiers_enseigne_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$equipiers_enseigne_query = $equipiers_enseigne_requete->execute();
		$equipiers_enseigne = $equipiers_enseigne_requete->fetchColumn();
	return $equipiers_enseigne;
}// fin equipiers_lp_enseigne

function equipiers_lp_sf($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Équipiers de La Poste - Services financiers
		$equipiers_sf_prepare = 'SELECT COUNT( ep.P_ID )
FROM evenement e, evenement_horaire eh, evenement_participation ep, pompier p
WHERE e.E_CODE = eh.E_CODE
AND eh.EH_ID =1
AND ep.E_CODE = eh.E_CODE
AND e.TE_CODE = "DPS"
AND ep.P_ID = p.P_ID
AND p.C_ID = "14" ';
		if ($month) {		
			$equipiers_sf_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$equipiers_sf_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$equipiers_sf_requete = $bdd->prepare($equipiers_sf_prepare);
		if ($month) {
			$equipiers_sf_requete->bindValue(1, $month, PDO::PARAM_INT);
			$equipiers_sf_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$equipiers_sf_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$equipiers_sf_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$equipiers_sf_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$equipiers_sf_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$equipiers_sf_query = $equipiers_sf_requete->execute();
		$equipiers_sf = $equipiers_sf_requete->fetchColumn();
	return $equipiers_sf;
} // fin equipiers_lp_sf

function equipiers_lp_corporate($section, $month, $year) {
	global $bdd;	
	global $inQuery;
		// Équipiers de La Poste - Corporate
		$equipiers_corporate_prepare = 'SELECT COUNT( ep.P_ID )
FROM evenement e, evenement_horaire eh, evenement_participation ep, pompier p
WHERE e.E_CODE = eh.E_CODE
AND eh.EH_ID =1
AND ep.E_CODE = eh.E_CODE
AND e.TE_CODE = "DPS"
AND ep.P_ID = p.P_ID
AND p.C_ID = "13" ';
		if ($month) {		
			$equipiers_corporate_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$equipiers_corporate_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$equipiers_corporate_requete = $bdd->prepare($equipiers_corporate_prepare);
		if ($month) {
			$equipiers_corporate_requete->bindValue(1, $month, PDO::PARAM_INT);
			$equipiers_corporate_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$equipiers_corporate_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$equipiers_corporate_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$equipiers_corporate_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$equipiers_corporate_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$equipiers_corporate_query = $equipiers_corporate_requete->execute();	
		$equipiers_corporate = $equipiers_corporate_requete->fetchColumn();
	return $equipiers_corporate;
} // fin equipiers_lp_corporate

function equipiers_autres($section, $month, $year) {
	global $bdd;
	global $inQuery;
		// Équipiers Autres
		$equipiers_autres_prepare = 'SELECT COUNT( ep.P_ID )
FROM evenement e, evenement_horaire eh, evenement_participation ep, pompier p
WHERE e.E_CODE = eh.E_CODE
AND eh.EH_ID =1
AND ep.E_CODE = eh.E_CODE
AND e.TE_CODE = "DPS"
AND ep.P_ID = p.P_ID
AND p.C_ID NOT IN(10,11,12,13,14,109) ';
		if ($month) {		
			$equipiers_autres_prepare .= 'AND MONTH(eh.EH_DATE_DEBUT) = ? ';
		}
		$equipiers_autres_prepare .= 'AND YEAR( eh.EH_DATE_DEBUT ) = ? AND e.S_ID IN(' . $inQuery .')';
		$equipiers_autres_requete = $bdd->prepare($equipiers_autres_prepare);
		if ($month) {
			$equipiers_autres_requete->bindValue(1, $month, PDO::PARAM_INT);
			$equipiers_autres_requete->bindValue(2, $year, PDO::PARAM_INT);
		}
		else {
			$equipiers_autres_requete->bindValue(1, $year, PDO::PARAM_INT);
		}
		if (is_array($section)) {
			if ($month) {
				$number_section = 2;
			}
			else {
				$number_section = 1;
			}
			foreach ($section as $k => $id) {
				$number_section = $number_section+1;
				$equipiers_autres_requete->bindValue($number_section, $id);
			}
		}
		else {
			if ($month) {
				$equipiers_autres_requete->bindValue(3, $section, PDO::PARAM_INT);
			}
			else {
				$equipiers_autres_requete->bindValue(2, $section, PDO::PARAM_INT);
			}
		}	
		$equipiers_autres_query = $equipiers_autres_requete->execute();
		$equipiers_autres = $equipiers_autres_requete->fetchColumn();
	return $equipiers_autres;
}// fin equipiers_autres

function convertir_mois($month) {
		switch($month)
		{
			case 1:
                $month = ' janvier ';
                break;

       		case 2:
                $month = ' f&eacute;vrier ';
                break;

        	case 3:
                $month = ' mars ';
                break;

        	case 4:
                $month = ' avril ';
                break;

        	case 5:
                $month = ' mai ';
                break;
	
        	case 6:
                $month = ' juin ';
                break;

        	case 7:
                $month = ' juillet ';
                break;

        	case 8:
                $month = ' ao&ucirc;t ';
                break;

        	case 9:
                $month = ' septembre ';
                break;

        	case 10:
                $month = ' octobre ';
                break;

        	case 11:
                $month = ' novembre ';
                break;

        	case 12:
                $month = ' d&eacute;cembre ';
		}// fin du switch
		
	return $month;
}
?>
