<?php

  # written by: Nicolas MARCHE, Jean-Pierre KUNTZ
  # contact: nico.marche@free.fr
  # project: eBrigade
  # homepage: http://sourceforge.net/projects/ebrigade/
  # version: 2.6

  # Copyright (C) 2004, 2011 Nicolas MARCHE
  # This program is free software; you can redistribute it and/or modify
  # it under the terms of the GNU General Public License as published by
  # the Free Software Foundation; either version 2 of the License, or
  # (at your option) any later version.
  #
  # This program is distributed in the hope that it will be useful,
  # but WITHOUT ANY WARRANTY; without even the implied warranty of
  # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  # GNU General Public License for more details.
  # You should have received a copy of the GNU General Public License
  # along with this program; if not, write to the Free Software
  # Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
  
include_once ("config.php");
check_all(0);
$id=$_SESSION['id'];
writehead();

?>
<script type='text/javascript' src='checkForm.js'></script>
<script>
function change(what,tab) 
{ 
 	var id = "btaction" + tab;
	if  (what.value == '' ) {
		document.getElementById(id).disabled=true;
	}
	else {
		document.getElementById(id).disabled=false;
	}
} 
 </script>
</head>
<?php

$msgerr ="";$voircompta=false;
$evenement=(isset($_POST['evenement'])?intval($_POST['evenement']):(isset($_GET['evenement'])?intval($_GET['evenement']):0));

// le chef, le cadre de l'événement ont toujours accès à cette fonctionnalité, les autres doivent avoir 29 et/ou 24
if (( ! check_rights($id, 29, get_section_organisatrice($evenement))) and ( get_chef_evenement($evenement) <> $_SESSION['id'] )) {
 	check_all(29);
	check_all(24);
}
$voircompta = true;

function datesql2txt($sqldate){
	$retour ="";
	if($sqldate!=""){
		$date = explode("-",$sqldate);
		$retour = $date[0]." ".date_fran_mois($date[1])." ".$date[2];
	}
	return $retour;
}

$evtType = "";
$evtTitre = "";
$evtLieu = "";
$evtDateDebut = "";
$evtDateFin = "";
$evtNB ="";
$evtKm="";
$evtNbPers="";
$evtOrga="";
$evtContact="";
$evtAdresse="";
$evtCP="";
$evtVille="";
$evtMobile="";
$evtTel="";
$evtFax="";
$evtEmail="";

$styleEvt="background-color:white;color:black;";
$factureStatut ="Aucun devis, facture, paiement";

$sqlevt = "SELECT e.*, eh.*, te.TE_LIBELLE,
			date_format(eh.eh_date_debut,'%d-%m-%Y') dtdb, 
			date_format(eh.eh_date_fin,'%d-%m-%Y') dtfn 
          FROM evenement e, type_evenement te, evenement_horaire eh
		  WHERE e.TE_CODE = te.TE_CODE
		  and e.e_code=eh.e_code
		  and e.E_CODE=$evenement";
$resevt=mysql_query($sqlevt);
$defaultDateHeure='';
$evtDuree=0;
$evtDureeTotale=0;
if($resevt){
	while($rowevt=mysql_fetch_array($resevt)){
	    $EH_ID=$rowevt['EH_ID'];
	    if ( $EH_ID == 1 ) {
			$evtType = $rowevt['TE_CODE'];
			$evtTypeLibelle = $rowevt['TE_LIBELLE'];
			$evtConvention = $rowevt['E_CONVENTION'];
			$evtTitre = $rowevt['E_LIBELLE'];
			$evtLieu = $rowevt['E_LIEU'];
			$evtNB = $rowevt['E_NB'];
			$evtNB1 = $rowevt['E_NB1'];//." Interventions");
			$evtNB2 = $rowevt['E_NB2'];//." Evacuations");
			$evtSection = $rowevt['S_ID'];//." Section");
			$evtClosed = $rowevt['E_CLOSED'];//." Cloturé ");
			$evtCompany=$rowevt['C_ID'];
		}
		$evtDateDebut = $rowevt['dtdb'];
		$evtDateFin = $rowevt['dtfn'];
		$evt_hdtdb = substr($rowevt['EH_DEBUT'],0,5);
		$evt_hdtfn = substr($rowevt['EH_FIN'],0,5);
		$evtDuree= $rowevt['EH_DUREE'] + $evtDuree;
		$evtDureeTotale= $rowevt['E_NB'] * $rowevt['EH_DUREE'] + $evtDureeTotale;
		
		if ($evtDateDebut!=$evtDateFin) 
			$defaultDateHeure .= "du ".datesql2txt($evtDateDebut)." à ".$evt_hdtdb." au ".datesql2txt($evtDateFin)." à ".$evt_hdtfn.",\n";
		else 
			$defaultDateHeure .= "le ".datesql2txt($evtDateDebut)." de ".$evt_hdtdb." à ".$evt_hdtfn.",\n";

	}
	$evtDuree .= " Heures / intervenant";
	$defaultDateHeure = substr($defaultDateHeure,0,strlen($defaultDateHeure) -2);
	
	if ( $evtCompany <> '' ) {
		$queryC="select C_NAME, C_ADDRESS, C_ZIP_CODE, C_CITY, C_EMAIL, C_FAX, C_PHONE, C_CONTACT_NAME
				from company where C_ID=".$evtCompany;
		$resultC=mysql_query($queryC);
		$rowC=mysql_fetch_array($resultC);
		$evtOrga=$rowC['C_NAME'];
		$evtAdresse=$rowC['C_ADDRESS'];
		$evtCP=$rowC['C_ZIP_CODE'];
		$evtVille=$rowC['C_CITY'];
		if (substr($rowC['C_PHONE'],0,2)=='06' ) $evtMobile=$rowC['C_PHONE'];
		else $evtTel=$rowC['C_PHONE'];
		$evtFax=$rowC['C_FAX'];
		$evtEmail=$rowC['C_EMAIL'];
		$evtContact=$rowC['C_CONTACT_NAME'];
	}
}


$devisLieu=(isset($_POST['devisLieu'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisLieu'])):"$evtLieu");
$devisDateHeure=(isset($_POST['devisDateHeure'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisDateHeure'])):"$defaultDateHeure");
$devisDate=(isset($_POST['devisDate'])?mysql_real_escape_string($_POST['devisDate']):'');
if ( $devisDate <> '') {
	$tmp=explode ( "/",$devisDate); $year=$tmp[2]; $month=$tmp[1]; $day=$tmp[0];
	$devisDate=$year.'-'.$month.'-'.$day;
}
$devisMontant=(isset($_POST['devisMontant'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisMontant'])):"");
$devisNumero=(isset($_POST['devisNumero'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisNumero'])):"");
$devisCom=(isset($_POST['devisCom'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisCom'])):"");
$devisOrga=(isset($_POST['devisOrga'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisOrga'])):"$evtOrga");
$devisCivilite=(isset($_POST['devisCivilite'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisCivilite'])):"Madame, Monsieur");
$devisContact=(isset($_POST['devisContact'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisContact'])):"$evtContact");
$devisAdresse=(isset($_POST['devisAdresse'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisAdresse'])):"$evtAdresse");
$devisCP=(isset($_POST['devisCP'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisCP'])):"$evtCP");
$devisVille=(isset($_POST['devisVille'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisVille'])):"$evtVille");
$devisTel1=(isset($_POST['devisTel1'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisTel1'])):"$evtMobile");
$devisTel2=(isset($_POST['devisTel2'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisTel2'])):"$evtTel");
$devisFax=(isset($_POST['devisFax'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisFax'])):"$evtFax");
$devisEmail=(isset($_POST['devisEmail'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisEmail'])):"$evtEmail");
$devisURL=(isset($_POST['devisURL'])?mysql_real_escape_string(STR_replace("\"","",$_POST['devisURL'])):"");
$devisAccepte=(isset($_POST['devisAccepte'])?mysql_real_escape_string($_POST['devisAccepte']):"0");

$factLieu=(isset($_POST['factLieu'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factLieu'])):'');
$factDateHeure=(isset($_POST['factDateHeure'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factDateHeure'])):'');
$factDate=(isset($_POST['factDate'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factDate'])):'');
if ( $factDate <> '' ) {
	$tmp=explode ( "/",$factDate); $year=$tmp[2]; $month=$tmp[1]; $day=$tmp[0];
	$factDate=$year.'-'.$month.'-'.$day;
}
$factNumero=(isset($_POST['factNumero'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factNumero'])):"");
$factMontant=(isset($_POST['factMontant'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factMontant'])):"");
$factCom=(isset($_POST['factCom'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factCom'])):"");
$factOrga=(isset($_POST['factOrga'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factOrga'])):"");
$factCivilite=(isset($_POST['factCivilite'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factCivilite'])):"Madame, Monsieur");
$factContact=(isset($_POST['factContact'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factContact'])):"");
$factAdresse=(isset($_POST['factAdresse'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factAdresse'])):"");
$factCP=(isset($_POST['factCP'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factCP'])):"");
$factVille=(isset($_POST['factVille'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factVille'])):"");
$factTel1=(isset($_POST['factTel1'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factTel1'])):"");
$factTel2=(isset($_POST['factTel2'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factTel2'])):"");
$factFax=(isset($_POST['factFax'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factFax'])):"");
$factEmail=(isset($_POST['factEmail'])?mysql_real_escape_string(STR_replace("\"","",$_POST['factEmail'])):"");

$relanceDate=(isset($_POST['relanceDate'])?mysql_real_escape_string(STR_replace("\"","",$_POST['relanceDate'])):'');
if ( $relanceDate <> '') {
	$tmp=explode ( "/",$relanceDate); $year=$tmp[2]; $month=$tmp[1]; $day=$tmp[0];
	$relanceDate=$year.'-'.$month.'-'.$day;
}
$relanceNum=(isset($_POST['relanceNum'])?mysql_real_escape_string(STR_replace("\"","",$_POST['relanceNum'])):"");
$relanceCom=(isset($_POST['relanceCom'])?mysql_real_escape_string(STR_replace("\"","",$_POST['relanceCom'])):"");
$paiementDate=(isset($_POST['paiementDate'])?$_POST['paiementDate']:'');
if ( $paiementDate <> '' && $paiementDate <> '00/00/0000') {
	$tmp=explode ( "/",$paiementDate); $year=$tmp[2]; $month=$tmp[1]; $day=$tmp[0];
	$paiementDate=$year.'-'.$month.'-'.$day;
}
$paiementCom=(isset($_POST['paiementCom'])?mysql_real_escape_string(STR_replace("\"","",$_POST['paiementCom'])):"");
$frmaction="Créer";

// GESTION AJOUT / MODIFICATION
if (isset($_POST['frmaction'])){
	//echo "<br />D $devisDate - F $factDate - R $relanceDate - P $paiementDate";
	switch($_POST['frmaction']){
	case 'Modifier':
		$sql = "update evenement_facturation SET
devis_lieu= ".("$devisLieu"<>''?"\"".$devisLieu."\"":'NULL' )."
,devis_date_heure= ".("$devisDateHeure"<>''?"\"".$devisDateHeure."\"":'NULL' )."
,devis_Date = ".($devisDate<>''?"'".$devisDate."'":'NULL')."
,devis_montant = \"$devisMontant\"
,devis_numero = \"$devisNumero\"
,devis_comment = \"$devisCom\"
,devis_orga = \"$devisOrga\"
,devis_civilite = \"$devisCivilite\"
,devis_contact = \"$devisContact\"
,devis_adresse = \"$devisAdresse\"
,devis_cp = \"$devisCP\"
,devis_ville = \"$devisVille\"
,devis_tel1 = \"$devisTel1\"
,devis_tel2 = \"$devisTel2\"
,devis_fax = \"$devisFax\"
,devis_email = \"$devisEmail\"
,devis_url = \"$devisURL\"
,devis_accepte = \"$devisAccepte\"

,facture_lieu= ".("$factLieu"<>''?"\"".$factLieu."\"":'NULL' )."
,facture_date_heure= ".("$factDateHeure"<>''?"\"".$factDateHeure."\"":'NULL' )."
,facture_Date = ".($factDate<>''?"\"".$factDate."\"":'NULL')."
,facture_numero = \"$factNumero\"
,facture_montant = \"$factMontant\"
,facture_comment = \"$factCom\"
,facture_orga = \"$factOrga\"
,facture_civilite = \"$factCivilite\"
,facture_contact = \"$factContact\"
,facture_adresse = \"$factAdresse\"
,facture_cp = \"$factCP\"
,facture_ville = \"$factVille\"
,facture_tel1 = \"$factTel1\"
,facture_tel2 = \"$factTel2\"
,facture_fax = \"$factFax\"
,facture_email = \"$factEmail\"
,relance_Date = ".($relanceDate<>''?"\"".$relanceDate."\"":'NULL')."
,relance_num = \"$relanceNum\"
,relance_comment = \"$relanceCom\"
,paiement_date = ".($paiementDate<>''?"\"".$paiementDate."\"":'NULL')."
,paiement_comment = \"$paiementCom\"
WHERE E_ID='$evenement';
";
		break;
	case 'Créer':
		$sql = "INSERT into evenement_facturation(e_id,devis_lieu,devis_date_heure,
devis_Date,devis_numero,devis_Montant,devis_comment,devis_Orga,devis_Civilite,devis_Contact,devis_Adresse,devis_CP,devis_Ville,devis_Tel1,devis_Tel2,devis_Fax,devis_Email,devis_URL,devis_accepte,
facture_lieu,facture_date_heure,facture_Date,facture_numero,facture_Montant,facture_comment,facture_Orga,facture_Civilite,facture_Contact,facture_Adresse,facture_CP,facture_Ville,facture_Tel1,facture_Tel2,facture_Fax,facture_Email,
relance_Date,relance_num,relance_comment,
paiement_Date,paiement_comment
) VALUES('$evenement',".("$devisLieu"<>''?"\"".$devisLieu."\"":'NULL' ).",".("$devisDateHeure"<>''?"\"".$devisDateHeure."\"":'NULL' ).",
".($devisDate<>''?"\"".$devisDate."\"":'NULL').",\"$devisNumero\",\"$devisMontant\",\"$devisCom\",\"$devisOrga\",\"$devisCivilite\",\"$devisContact\",\"$devisAdresse\",\"$devisCP\",\"$devisVille\",\"$devisTel1\",\"$devisTel2\",\"$devisFax\",\"$devisEmail\",\"$devisURL\",\"$devisAccepte\",".("$factLieu"<>''?"\"".$factLieu."\"":'NULL' ).",".("$factDateHeure"<>''?"\"".$factDateHeure."\"":'NULL' ).",".($factDate<>''?"\"".$factDate."\"":'NULL').",\"$factNumero\",\"$factMontant\",\"$factCom\",\"$factOrga\",\"$factCivilite\",\"$factContact\",\"$factAdresse\",\"$factCP\",\"$factVille\",\"$factTel1\",\"$factTel2\",\"$factFax\",\"$factEmail\",".($relanceDate<>''?"\"".$relanceDate."\"":'NULL').",\"$relanceNum\",\"$relanceCom\",
".($paiementDate<>''?"\"".$paiementDate."\"":'NULL').",\"$paiementCom\"
)";
	// notifier
     $destid=get_granted(35,"$evtSection",'parent','yes');            
     $subject  = "Devis émis pour ".$evtTitre;
     $message  = "Bonjour,\n";
	 $message .= "Un devis a été émis pour l'événement: ".$evtTitre."\n";
	 $message .= "organisé par: ".get_section_code($evtSection)."\n";
     $message .= "lieu: ".$devisLieu."\n";
     $message .= "dates et heures: ".$devisDateHeure."\n";
		
     $nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );
	 break;
		
	case 'CopierDevis':
		$sql="delete from evenement_facturation_detail 
	where e_id = '$evenement'
	AND ef_type='facture'";
		$res = mysql_query($sql);
		$msgerr .= (mysql_errno()>0?"<br>$sqldetail<br>".mysql_error():'');
		
		$sqldetail="insert into evenement_facturation_detail(e_id,ef_lig,ef_type,ef_txt,ef_qte,ef_pu,ef_rem)
(select e_id,ef_lig,'facture',ef_txt,ef_qte,ef_pu,ef_rem from evenement_facturation_detail 
where e_id = '$evenement'
and ef_type='devis'
)";
		$res = mysql_query($sqldetail);
		$msgerr .= (mysql_errno()>0?"<br>$sqldetail<br>".mysql_error():'');
		// calcul le montant à facturer selon dle détail
		$sqlcalc="select * from evenement_facturation_detail 
where e_id = '$evenement'
and ef_type='facture'";
		$res = mysql_query($sqlcalc);
		$msgerr .= (mysql_errno()>0?"<br>$sqlcalc<br>".mysql_error():'');
		$out="";
		$num=0;
		$TotalDoc=0;
		while($rowcalc=mysql_fetch_array($res)){
			$num++;
			$TotalLigne = ($rowcalc['ef_qte']*$rowcalc['ef_pu']*(1-($rowcalc['ef_rem']/100)));	
			$TotalDoc += $TotalLigne;
		}
		$sql = "UPDATE evenement_facturation 
SET facture_montant = ".($TotalDoc<>0?$TotalDoc:$devisMontant).",
facture_date = now()
WHERE E_ID='$evenement'";
		break;
	default:
	}
	$res = mysql_query($sql);
	$msgerr .= (mysql_errno()>0?"<br>$sql<br>".mysql_error():'');
}// Fin Action

$sqlevtveh = "SELECT count(v_id) 'NbVeh', sum(ev_km) 'KM' FROM evenement_vehicule WHERE E_CODE=$evenement";
$resevtveh=mysql_query($sqlevtveh);
if($resevt){
	while($rowevtveh=mysql_fetch_array($resevtveh)){
		$evtKm = $rowevtveh['KM'];
		$evtNbVeh = $rowevtveh['NbVeh'];
	}
}
$sqlevtper = "SELECT count(p_id) 'NbPers' FROM evenement_participation WHERE E_CODE=$evenement";
$resevtper=mysql_query($sqlevtper);
if($resevtper){
	while($rowevtper=mysql_fetch_array($resevtper)){
		$evtNbPers = $rowevtper['NbPers'];
	}
}
$sqlfact = "SELECT * FROM evenement_facturation WHERE E_ID=$evenement";
$resfact=mysql_query($sqlfact);
//echo (mysql_errno()>0?"<br>$sqlfact<br>".mysql_error():'');
if($resfact){
	while($rowfact=mysql_fetch_array($resfact)){
	    // DEVIS
	    $devisLieu=$rowfact['devis_lieu'];
		if ( $devisLieu=='') $devisLieu = $evtLieu;
		$devisDateHeure=$rowfact['devis_date_heure'];
		if ( $devisDateHeure =='') {
			$devisDateHeure=$defaultDateHeure;
		}
		$devisDate=$rowfact['devis_date'];
		$devisNumero=$rowfact['devis_numero'];
		if ( $devisNumero == '' ) $devisNumero= $evenement;
		$devisAccepte=$rowfact['devis_accepte'];
		if($devisDate!=""){
			$tmp=explode ( "-",$devisDate); $year=$tmp[0]; $month=$tmp[1]; $day=$tmp[2];
			$devisDate=$day.'/'.$month.'/'.$year;
			if(checkdate($month,$day,$year)){
				$factureStatut = "Devis transmis le $devisDate";
				$styleEvt=(($devisAccepte==0)?"background-color:grey;color:white;":"background-color:green;color:white;");
			}else{
				$devisDate="";
			}
		}else{
			$devisDate="";
		}
		$devisMontant=$rowfact['devis_montant'];
		$devisCom=$rowfact['devis_comment'];
		$devisOrga=$rowfact['devis_orga'];
		$devisCivilite=$rowfact['devis_civilite'];
		$devisContact=$rowfact['devis_contact'];
		$devisAdresse=$rowfact['devis_adresse'];
		$devisCP=$rowfact['devis_cp'];
		$devisVille=$rowfact['devis_ville'];
		$devisTel1=$rowfact['devis_tel1'];
		$devisTel2=$rowfact['devis_tel2'];
		$devisFax=$rowfact['devis_fax'];
		$devisEmail=$rowfact['devis_email'];
		$devisURL=$rowfact['devis_url'];

		// FACTURE
		$factLieu=$rowfact['facture_lieu'];
		if ( $factLieu=='') $factLieu = $evtLieu;
		$factDateHeure=$rowfact['facture_date_heure'];
		if ( $factDateHeure =='') {
			$factDateHeure=$defaultDateHeure;
		}
		$factDate=$rowfact['facture_date'];
		if($factDate!=""){
			$tmp=explode ( "-",$factDate); $year=$tmp[0]; $month=$tmp[1]; $day=$tmp[2];
			$factDate=$day.'/'.$month.'/'.$year;
			if(checkdate($month,$day,$year)){
				$factureStatut = "Facture émise le $factDate";
				$styleEvt="background-color:orange;color:black;";
			}else{
				$factDate="";
			}	
		}else{
			$factDate="";
		}
		$factNumero=$rowfact['facture_numero'];
		if ( $factNumero == '' ) $factNumero= $evenement;
		$factMontant=$rowfact['facture_montant'];
		$factOrga=$rowfact['facture_orga'];
		$factCivilite=$rowfact['facture_civilite'];
		$factContact=$rowfact['facture_contact'];
		$factAdresse=$rowfact['facture_adresse'];
		$factCP=$rowfact['facture_cp'];
		$factVille=$rowfact['facture_ville'];
		$factTel1=$rowfact['facture_tel1'];
		$factTel2=$rowfact['facture_tel2'];
		$factFax=$rowfact['facture_fax'];
		$factEmail=$rowfact['facture_email'];
		$factCom=$rowfact['facture_comment'];

		// RELANCE
		$relanceDate=$rowfact['relance_date'];
		if($relanceDate!=""){
			$tmp=explode ( "-",$relanceDate); $year=$tmp[0]; $month=$tmp[1]; $day=$tmp[2];
			$relanceDate=$day.'/'.$month.'/'.$year;
			if(checkdate($month,$day,$year)){
				$factureStatut = "Relance en date du $relanceDate...";
				$styleEvt="background-color:red;color:white;";
			}else{
				$relanceDate="";
			}	
		}else{
			$relanceDate="";
		}

		$relanceNum=$rowfact['relance_num'];
		$relanceCom=$rowfact['relance_comment'];

		$paiementDate=$rowfact['paiement_date'];
		if($paiementDate!=""){
			$tmp=explode ( "-",$paiementDate); $year=$tmp[0]; $month=$tmp[1]; $day=$tmp[2];
			$paiementDate=$day.'/'.$month.'/'.$year;
			if(checkdate($month,$day,$year)){
				$factureStatut = "Paiement enregistré...";
				$styleEvt="background-color:white;color:grey;";
			}else{
				$paiementDate="";
			}	
		}else{
			$paiementDate="";
		}

		$paiementCom=$rowfact['paiement_comment'];

		$frmaction="Modifier";
	}
}else{
	$msgerr .= "Pas de facturation en cours...";
}
?>
<script type="text/javascript" src="js/jquery.js"></script>
<style type="text/css">@import url(js/datepicker/ui.datepicker.css);</style>
<script type="text/javascript" src="js/datepicker/ui.datepicker.js"></script>
<script type="text/javascript" src="js/datepicker/ui.datepicker-fr.js"></script>

<style type="text/css" >@import url(js/tabs/ui.tabs.css);</style>
<script type="text/javascript" src="js/tabs/ui.tabs.js"></script>
<script type="text/javascript">
$(document).ready(function() {	
	$('#TabsTriFact > ul').tabs();	
	$.datepicker.setDefaults({showOn: 'button', buttonImageOnly: true, 
buttonImage: 'js/datepicker/calbtn.gif', buttonText: 'Calendrier', firstDay: 1});
	$('#dtdb').datepicker({beforeShow: customRange});     
	$('#dtfn').datepicker({beforeShow: customRange});     
	$('#devisDate').datepicker();
	$('#factDate').datepicker();
	$('#relanceDate').datepicker();
	$('#paiementDate').datepicker();
	
	$("input#factNumero").keyup(function(){
		var trouve;
		trouve = $("input#factNumero").val();
		$.post("evenement_facturation_num.php",{trouve:trouve,section:<?php echo $evtSection; ?>,evenement:<?php echo $evenement; ?>},	
		function (data){		
			$("#infoNum").empty();
			$("#infoNum").append(data);
		});
	});
});
// Customize two date pickers to work as a date range 
function customRange(input) { 
	return {minDate: (input.id == 'dtfn' ? $('#dtdb').datepicker('getDate') : null), 
maxDate: (input.id == 'dtdb' ? $('#dtfn').datepicker('getDate') : null)}; 
}
function RecupAdresse(input){
	if (input.checked==false){
		if(confirm("Voulez-vous effacer ces informations?")==true){
		 	$("#factLieu").val("");
		 	$("#factDateHeure").val("");
			$("#factOrga").val("");
			$("#factCivilite").val("");
			$("#factContact").val("");
			$("#factAdresse").val("");
			$("#factCP").val("");
			$("#factVille").val("");
			$("#factTel1").val("");
			$("#factTel2").val("");
			$("#factFax").val("");
			$("#factEmail").val("");
		}
	}else{
	    $("#factLieu").val($("#devisLieu").val());
	    $("#factDateHeure").val($("#devisDateHeure").val());
		$("#factOrga").val($("#devisOrga").val());
		$("#factCivilite").val($("#devisCivilite").val());
		$("#factContact").val($("#devisContact").val());
		$("#factAdresse").val($("#devisAdresse").val());
		$("#factCP").val($("#devisCP").val());
		$("#factVille").val($("#devisVille").val());
		$("#factTel1").val($("#devisTel1").val());
		$("#factTel2").val($("#devisTel2").val());
		$("#factFax").val($("#devisFax").val());
		$("#factEmail").val($("#devisEmail").val());
	}
}
function CopierDevis(){
	$("#frmaction").val("CopierDevis");
	$("form").submit();
}

function bouton_redirect(cible) {
    self.location.href = cible;
}

// <!--
var cX = 0; var cY = 0; var rX = 0; var rY = 0;
function UpdateCursorPosition(e){ cX = e.pageX; cY = e.pageY;}
function UpdateCursorPositionDocAll(e){ cX = event.clientX; cY = event.clientY;}
if(document.all) { document.onmousemove = UpdateCursorPositionDocAll; }
else { document.onmousemove = UpdateCursorPosition; }
function AssignPosition(d) {
if(self.pageYOffset) {
	rX = self.pageXOffset;
	rY = self.pageYOffset;
	}
else if(document.documentElement && document.documentElement.scrollTop) {
	rX = document.documentElement.scrollLeft;
	rY = document.documentElement.scrollTop;
	}
else if(document.body) {
	rX = document.body.scrollLeft;
	rY = document.body.scrollTop;
	}
if(document.all) {
	cX += rX; 
	cY += rY;
	}
d.style.left = (cX+10) + "px";
d.style.top = (cY+10) + "px";
}
function HideContent(d) {
if(d.length < 1) { return; }
document.getElementById(d).style.display = "none";
}
function ReverseContentDisplay(d) {
if(d.length < 1) { return; }
var dd = document.getElementById(d);
AssignPosition(dd);
if(dd.style.display == "none") { dd.style.display = "block"; }
else { dd.style.display = "none"; }
}
//-->

</script>
<style type="text/css">
form p{
margin:0;
padding:0;
}
form label{
float:left;
clear:left;
width:200px;
	text-align:right;
	padding-right:1em;
}
input, select, textarea{
float:left;
}
input:focus, textarea:focus, select:focus{
	background-color:#ffffcc;
}
#intro{
display:block;
width:100%;
<?php echo $styleEvt; ?>
}
#frmaction{
clear:both;
width:100%;
	border-top:1px solid black;
}
#frmaction input{
margin:auto;
}
div#resultat{
	text-align:left;
}
#factNumero{
	font-weight:bold;
}
#devisNumero{
	font-weight:bold;
}
#infoNum{
clear:both;
color:red;
font-weight:bold;
margin-left:200px;
}
</style>
<?php

// fonction pour afficher les boutons sauver, retour, imprimer, détail
function Buttons($tab = 'null'){
    global $evtType,$devisDate,$factDate,$relanceDate,$paiementDate,$factNumero,$evenement;
    if ( $tab == 'devis' && $devisDate == "") $btndisabled='disabled';
    else if ( $tab == 'facture' && $factDate == "") $btndisabled='disabled';
    else if ( $tab == 'relance' && $relanceDate == "") $btndisabled='disabled';
    else if ( $tab == 'paiement' && $paiementDate == "") $btndisabled='disabled';
	else  $btndisabled='';
    echo " <input type='button' id='retour' value=retour 
		   onclick='bouton_redirect(\"evenement_display.php?from=facturation&evenement=$evenement\");'>
		  <input type='submit' id='btaction".$tab."' value=sauver $btndisabled>";
	echo "<a href=\"javascript:ReverseContentDisplay('evenement');\" 
		  	 title=\"afficher le détail de l'événement\"><img border=0 src=images/".$evtType."small.gif></a>";
	if ( $tab == 'facture' && $factDate <> ""){
		echo " <a href='pdf.php?id=".$evenement."&pdf=facture' target='_blank'><img src='images/printer.gif' height=24 border='0' title='Imprimer la facture PDF' ></a></p>";
	}
	if( $tab == 'devis' && $devisDate<>""){
		echo " <a href='pdf.php?id=".$evenement."&pdf=devis' target='_blank'><img src='images/printer.gif' height=24 border='0' title='Imprimer au format pdf'></a></p>";
	}
	if( $tab == 'relance' && $relanceDate<>""){
		echo " <a href='pdf.php?id=".$evenement."&pdf=relance' target='_blank'><img src='images/printer.gif' height=24 border='0' title='Imprimer au format pdf'></a></p>";
	}
}
//================================================
// EN TETE
//================================================

if ( isset($_GET['status']) ) $status=$_GET['status'];
else $status=get_etat_facturation($evenement,"code");
if ( isset ($_POST['frmaction']))
  if ( $_POST['frmaction'] =='CopierDevis') $status ='facture';

$etatfacturation=get_etat_facturation($evenement,"txt");
$cssfacturation=get_etat_facturation($evenement,"css");

echo "<div>
        <table>
		<tr>
		  <td rowspan=2><img src=images/money.png></td>
		  <td ><font size=4><b>".$evtTitre."</b></font>
		</td></tr>
		<tr>
		  <td>".$evtTypeLibelle." ".$evtLieu." - ".$evtDateDebut."</td>
		</tr></table>
		</div>";

        echo  "<div id='evenement' 
				style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width:300px;
					   height:160px;
					   padding: 5px;'>
				<img border=0 src=images/".$evtType."small.gif>".$evtTypeLibelle."
				<br><b>N° Convention :</b> $evtConvention 
				<br><b>Lieu :</b> $evtLieu
				<br><b>Dates :</b> $defaultDateHeure
				<br><b>Durée effective :</b> $evtDuree";
		if ( $evtNbPers  <> "") 
			echo "<br><b>Nombre d'intervenants :</b> $evtNbPers inscrits / $evtNB demandés ";
		else
			echo "<br><b>Nombre d'intervenants demandés :</b> $evtNB";	
		echo "<br><b>Durée Totale prévue:</b> $evtDureeTotale Heures ";
		if($evtType=="DPS"){
			if ( $evtNB1 <> "" ) echo "<br><b>Nombre d'interventions :</b> $evtNB1";	
			if ( $evtNB2 <> "" ) echo "<br><b>Nombre d'évacuations :</b> $evtNB2";
		}
		if ( $evtKm <> "" ) echo "<br><b>Kilomètres parcourus:</b> $evtKm";

		echo "<p align=center><a onmouseover=\"HideContent('evenement'); return true;\"
   					href=\"javascript:HideContent('evenement')\"><i>fermer</i></a>
			</div>"; 

echo "<form name='frmGesCom' method='post'>";

echo "<span style=\"color:red;width:100%;clear:both;\">$msgerr</span>"; 
echo "<div id='TabsTriFact'>
<ul>";

if ($voircompta){
    // DEVIS
	if ( $status == 'devis' ) {
	 	$class='ui-tabs-selected';
		if ( $devisAccepte == 1 ) $color="green";
	 	else $color="#5A5A5A";
	}
	else {
	 	$color='#27537a';
	 	$class='';
	}
	if ( $devisAccepte == 1 ) $cmt = "devis du ".$devisDate." (accepté)";
	else if ( $devisDate <> '' ) $cmt = "devis du ".$devisDate;
	else $cmt="devis";
	echo "<li class=\"$class\">
	<a href='#devis' title='Devis'>
	<span style='color:$color'>".$cmt."</span></a></li>";
	
	// FACTURE
	if ( $status == 'facture' ) {
	 	$class='ui-tabs-selected';
		$color='orange';
	}
	else {
	 	$color='#27537a';
	 	$class='';
	}
	if ( $factDate <> '' ) $cmt = "facture émise le ".$factDate;
	else $cmt="facture";
	echo "<li class=\"$class\">
	<a href='#facture' title='Facture'>
	<span style='color:$color'>".$cmt."</span></a></li>";
	
	// RELANCE
	if ( $status == 'relance' ) {
	 	$class='ui-tabs-selected';
		$color='red';
	}
	else {
	 	$color='#27537a'; 
	 	$class='';
	}
	if ( $relanceDate <> '' ) $cmt = "Relance le ".$relanceDate;
	else $cmt="Relance";	
	echo "<li class=\"$class\">
	<a href='#relance' title='Relance impayé'>
	<span style='color:$color'>".$cmt."</span></a></li>";
	
	// PAIEMENT
	if ( $status == 'paiement' ) {
	 	$class='ui-tabs-selected';
		$color='black';
	}
	else {
	 	$color='#27537a';
	 	$class='';
	}
	if ( $paiementDate <> '' ) $cmt= "Paiement le ".$paiementDate;
	else $cmt="Paiement";
	echo "<li class=\"$class\">
	<a href='#paiement' title='Enregistrer le paiement'>
	<span style='color:$color'>".$cmt."</span></a></li>";
}
echo "</ul>";
echo "\n"."</div>";// fin tabs

if ($voircompta){
//================================================
// DEVIS
//================================================
	
	echo "<div id='devis'>";
	echo EbDeb("Devis");
	echo "<p><label for='devisDate' title='JJ/MM/AAAA'><b>Date du devis</label></b>
		     <input type='text' name='devisDate' id='devisDate' title='JJ/MM/AAAA' value=\"".$devisDate."\"
			 onchange=\"change(this,'devis')\"></p>";
	$queryF="select count(1) as NB from evenement_facturation
			where e_id='$evenement'";
	$resF = mysql_query($queryF);
	$rowF=mysql_fetch_array($resF);
	
	$query="select count(1) as NB from evenement_facturation_detail
			where e_id='$evenement'
			and ef_type='devis'";
	$res = mysql_query($query);
	$row=mysql_fetch_array($res);
	if ( $row['NB'] > 0 ) $disabled='disabled';
	else $disabled='';
	if ( $rowF['NB'] > 0 ) {
		echo "<p><label for='devisMontant'><b>Montant</b></label>
	         <input type='hidden' name='devisMontant' id='devisMontant' value=\"".$devisMontant."\">
		     <input type='text' name='devisMontant' id='devisMontant' value=\"".$devisMontant."\" $disabled>";
		echo " 
		     <a href='evenement_facturation_detail.php?evenement=".$evenement."&type=devis'>Détail</a>";
	}
	echo "	</p>
		  <p><label for='devisNumero'><b>Devis Numéro</b></label>
		  	 <input type='text' name='devisNumero' id='devisNumero' value=\"".$devisNumero."\"></p>
		  <p><label for='devisCom'>Commentaire</label>
		     <textarea name='devisCom' id='devisCom' cols='30'
			 style='font-size:10pt; font-family:Arial;'>".$devisCom."</textarea></p>
		  <p><label for='efLieu'><b>Lieu</b></label>
		     <input type='text' name='devisLieu' id='devisLieu' size='30' maxlength='50' value=\"".$devisLieu."\"></p>
		  <p><label for='devisDateHeure'><b>Dates, heures</b></label>
		     <textarea name='devisDateHeure' id='devisDateHeure' cols='40'
			 style='font-size:10pt; font-family:Arial;'>".$devisDateHeure."</textarea></p>
		  <p><label for='devisOrga'><b>Organisme demandeur</b></label>
		     <input type='text' name='devisOrga' id='devisOrga' value=\"".$devisOrga."\"></p>
		  <p><label for='devisContact'>Civilité</label>
		     <input type='text' name='devisCivilite' id='devisCivilite' value=\"".$devisCivilite."\"></p>
		  <p><label for='devisContact'>Contact</label>
		     <input type='text' name='devisContact' id='devisContact' value=\"".$devisContact."\"></p>
		  <p><label for='devisAdresse'>Adresse</label>
		     <textarea name='devisAdresse' id='devisAdresse' cols='30'
			 style='font-size:10pt; font-family:Arial;'>".$devisAdresse."</textarea></p>
		  <p><label for='devisCP'>CP</label>
		     <input type='text' name='devisCP' id='devisCP' value=\"".$devisCP."\"></p>
		  <p><label for='devisVille'>Ville</label>
		     <input type='text' name='devisVille' id='devisVille' value=\"".$devisVille."\"></p>
		  <p><label for='devisTel1'>Tél mobile</label>
		     <input type='text' name='devisTel1' id='devisTel1' value=\"".$devisTel1."\"></p>
		  <p><label for='devisTel2'>Tél fixe</label>
		     <input type='text' name='devisTel2' id='devisTel2' value=\"".$devisTel2."\"></p>
		  <p><label for='devisFax'>Fax</label>
		     <input type='text' name='devisFax' id='devisFax' value=\"".$devisFax."\"></p>
		  <p><label for='devisEmail'>Email</label>
		     <input type='text' name='devisEmail' id='devisEmail' value=\"".$devisEmail."\"></p>
		  <p><label for='devisURL'>Site internet</label>
		     <input type='text' name='devisURL' id='devisURL' value=\"".$devisURL."\"></p>
		  <p><label for='devisAccepte'>Devis accepté</label>
	<select name='devisAccepte' id='devisAccepte'>
	<option value='0' ".($devisAccepte==0?' selected':'').">Non</option>
	<option value='1' ".($devisAccepte==1?' selected':'').">Oui</option>
	</select>
	</p>";
	echo EbFin();
	echo Buttons('devis');
	echo "</div>";

//================================================
// FACTURE
//================================================
	echo "<div id='facture'>";
	if ($evtClosed==0) 
		echo "</font><img src=images/miniwarn.png> Attention, cet événement n'est pas clôturé !!! Il faut fermer les inscriptions<p>";
	echo EbDeb("Facture");
	if ( $factMontant==$devisMontant ) $cmt='';
	else $cmt="Devis = ".$devisMontant;
	echo "<p><label for='factDate'>Date de facturation (JJ/MM/AAAA)</label>
			 <input type='text' name='factDate' id='factDate' value=\"".$factDate."\"
			 onchange=\"change(this,'facture')\"></p>";
	$query="select count(1) as NB from evenement_facturation_detail
			where e_id='$evenement'
			and ef_type='facture'";
	$res = mysql_query($query);
	$row=mysql_fetch_array($res);
	if ( $row['NB'] > 0 ) $disabled='disabled';
	else $disabled='';
	if ("$factMontant"=='') $factMontant=$devisMontant;
	echo "<p><input type='hidden' name='factMontant' id='factMontant' value=\"".$factMontant."\">
	         <label for='factMontant'><b>Montant prestation</b></label>
		     <input type='text' name='factMontant' id='factMontant' value=\"".$factMontant."\" $disabled> ";		 
		echo " <a href='evenement_facturation_detail.php?evenement=".$evenement."&type=facture'>Détail</a>";
		echo "</p>";
		if($factMontant=="0" && $devisMontant<>"0"){ 
	 		echo "<p><label for='CopieDevis'><i>Copier le montant du devis</i></label>
		     <input type='checkbox' name='CopieDevis' id='CopieDevis' onclick='CopierDevis();'></p>";
		} 
		if ( ("$factContact"=="$devisContact") and ("$factOrga" == "$devisOrga") and ("$factAdresse" == "$devisAdresse") ) 
			$checked ='checked';
		else 
			$checked='';
		echo "<p><label for='factNumero'><b>Facture Numéro</b></label>
		  	 <input type='text' name='factNumero' id='factNumero' value=\"".$factNumero."\"><div id='infoNum'></div></p>
	      <p><label for='factCom'>Commentaire</label>
		     <textarea name='factCom' id='factCom' cols='30'
			 style='font-size:10pt; font-family:Arial;'>".$factCom."</textarea></p>
		  <p><label for='factIdem'><i>Identique à informations du devis</i></label>
		     <input type='checkbox' name='factIdem' id='factIdem' onclick='javascript:RecupAdresse(this);' $checked></p>
		  <p><label for='factLieu'><b>Lieu</b></label>
		     <input type='text' name='factLieu' id='factLieu' size='30' maxlength='50' value=\"".$factLieu."\"></p>
		  <p><label for='factDateHeure'><b>Dates, heures</b></label>
		     <textarea name='factDateHeure' id='factDateHeure' cols='40'
			 style='font-size:10pt; font-family:Arial;'>".$factDateHeure."</textarea></p>
	      <p><label for='factOrga'><b>Organisme payeur</b></label>
		     <input type='text' name='factOrga' id='factOrga' value=\"".$factOrga."\" ></p>
		  <p><label for='factContact'>Civilité</label>
		     <input type='text' name='factCivilite' id='factCivilite' value=\"".$factCivilite."\" ></p>
		  <p><label for='factContact'>Contact</label>
		     <input type='text' name='factContact' id='factContact' value=\"".$factContact."\" ></p>
		  <p><label for='factAdresse'>Adresse</label>
		     <textarea name='factAdresse' id='factAdresse' cols='30'
			 style='font-size:10pt; font-family:Arial;'>".$factAdresse."</textarea></p>
		  <p><label for='factCP'>CP</label>
		     <input type='text' name='factCP' id='factCP' value=\"".$factCP."\"></p>
		  <p><label for='factVille'>Ville</label>
		     <input type='text' name='factVille' id='factVille' value=\"".$factVille."\"></p>
		  <p><label for='factTel1'>Tél mobile</label>
		     <input type='text' name='factTel1' id='factTel1' value=\"".$factTel1."\"></p>
		  <p><label for='factTel2'>Tél fixe</label>
		     <input type='text' name='factTel2' id='factTel2' value=\"".$factTel2."\"></p>
		  <p><label for='factFax'>Fax</label>
		     <input type='text' name='factFax' id='factFax' value=\"".$factFax."\"></p>
		  <p><label for='factEmail'>Email</label>
		     <input type='text' name='factEmail' id='factEmail' value=\"".$factEmail."\"></p>";
echo EbFin();
echo Buttons('facture');
echo "</div>";

//================================================
// RELANCE
//================================================
echo "<div id='relance'>";
echo EbDeb("Relance");
echo "<p><label for='relanceDate'>Date de relance (JJ/MM/AAAA)</label>
		 <input type='text' name='relanceDate' id='relanceDate' value=\"".$relanceDate."\"
		 onchange=\"change(this,'relance')\"></p>
	  <p><label for='relanceNum'>Nombre de relance</label>
	     <input type='text' name='relanceNum' id='relanceNum' value=\"".$relanceNum."\"></p>
      <p><label for='relanceCom'>Commentaire</label>
	     <textarea name='relanceCom' id='relanceCom' cols='30'>".$relanceCom."</textarea></p>";
echo EbFin();	     
echo Buttons('relance');
echo "</div>";

//================================================
// PAIEMENT
//================================================
echo "<div id='paiement'>";
echo EbDeb("Paiement");
echo "<p><label for='paiementDate'>Date du paiement (JJ/MM/AAAA)</label>
         <input type='text' name='paiementDate' id='paiementDate' value=\"".$paiementDate."\"
		 onchange=\"change(this,'paiement')\"></p>
      <p><label for='paiementCom'>Commentaire</label>
	     <textarea name='paiementCom' id='paiementCom' cols='30'>".$paiementCom."</textarea></p>";
echo EbFin();
echo Buttons('paiement');
echo "</div>";
}
echo "<div id='action'>";
echo "<input type='hidden' name='frmaction' id='frmaction' value=".$frmaction.">";
echo "<input type='hidden' name='evenement' id='evenement' value=".$evenement.">";
echo "</div>";
echo "</form>";

echo "\n"."</body></html>";
