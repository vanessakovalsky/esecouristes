<?php

  # written by: Nicolas MARCHE <nico.marche@free.fr>
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

require('fpdf/fpdf.php');

$evenement=intval($_GET["evenement"]);
$mode=intval($_GET["mode"]);
if ( isset($_GET["P_ID"])) $pid=intval($_GET["P_ID"]);
else $pid=0;

if ( $mode == 3 and ( ! $printfulldiplome)) $mode=4;

// dates et infos événement
$query = "SELECT e.PS_ID, DATE_FORMAT(eh.EH_DATE_DEBUT, '%d-%m-%Y') as EH_DATE_DEBUT,
		  DATE_FORMAT(eh.EH_DATE_FIN, '%d-%m-%Y') as EH_DATE_FIN, e.E_LIEU, e.E_CHEF,
		  s.S_DESCRIPTION, s.S_ID, s.S_CODE, s.S_CITY, p.TYPE, p.PS_NATIONAL, p.PS_SECOURISME, eh.EH_ID
		  FROM evenement e, section s, poste p, evenement_horaire eh
		  WHERE e.S_ID=s.S_ID
		  and e.E_CODE = eh.E_CODE
		  and p.PS_ID = e.PS_ID
		  and e.E_CODE='$evenement'
		  order by eh.EH_ID" ;

$result=mysql_query($query); 
while ($row = mysql_fetch_array($result)) { 
	if ($row["EH_ID"] == 1) $debut=$row["EH_DATE_DEBUT"];
	$fin=$row["EH_DATE_FIN"];
	$lieu=$row["E_LIEU"];
	$organisateur=$cisname." (".$row["S_DESCRIPTION"].")";
	$organisateur_city=$row["S_CITY"];
	if ($organisateur_city == "" ) $organisateur_city=$lieu;
	$type=str_replace(" ", "",$row["TYPE"]);
	$psid=$row["PS_ID"];
	$national=$row["PS_NATIONAL"];
	$secourisme=$row["PS_SECOURISME"];
	$S_ID=$row["S_ID"];
	$chef=$row["E_CHEF"];
}
// verification si paramétrage existe
$query="select count(*) as NB from diplome_param where PS_ID=".$psid;
$result = mysql_query($query); 
$row = mysql_fetch_array($result);
if ( $row["NB"] == 0 ) {
	write_msgbox("paramétrage incomplet", $error_pic, "Le paramétrage de l'impression des diplômes n'est pas fait pour cette compétence.",10,0);
	exit;
}


if ( $fin == '' ) {
	$fin = $debut;
	$periode= "le ".$debut;
}
else
	$periode= "du ".$debut." au ".$fin;

// imprimer son duplicata
if ( $id == $pid and $mode == 4)
		check_all(0);
//sinon permission 48 requise
else {
	check_all(48);
	// vérifier les permissions pour cette section ou national
	if ( $national == 1 ) {
		if (! check_rights($id, 48, "0" )) check_all(24);
	}
	else if (! check_rights($id, 48, "$S_ID")) check_all(24);
}
// audit
if ( $mode <> 4 ) {
	$query="update personnel_formation set PF_PRINT_BY=".$id.", PF_PRINT_DATE=NOW()
	    where E_CODE=".$evenement;
	$result=mysql_query($query);
}
$affichage=array();
$edi_taille=array();
$edi_style=array();  
$edi_police=array();
$pos_x=array();
$pos_y =array();
$annexe=array();

$taille_org=array(8,9,10,11,12,14,16,18);
$style_org=array("","B","I","BI");	
$police_org=array("Courier","Arial","Times");

// paramétrage impression
$query="select FIELD,ACTIF,AFFICHAGE,TAILLE,STYLE,
			   POLICE,POS_X,POS_Y,ANNEXE 
			   from diplome_param where PS_ID=".$psid;
$result = mysql_query($query); 
$i=1;
while($data = mysql_fetch_array($result)) {
	$actif[$i]=$data['ACTIF'];
	$affichage[$i]=$data['AFFICHAGE'];
	$edi_taille[$i]= $taille_org[$data['TAILLE']];
	$edi_style[$i]=$style_org[$data['STYLE']];  
	$edi_police[$i]=$police_org[$data['POLICE']];
	$pos_x[$i]=$data['POS_X'];
	$pos_y[$i]=$data['POS_Y'];
	$annexe[$i]=$data['ANNEXE'];
	$i=$i+1;
};

$pdf= new FPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetCreator("$cisname - $organisateur");
$pdf->SetAuthor("$cisname");
$pdf->SetDisplayMode('fullpage','single');
$pdf->SetTitle("Diplome formation");
$pdf->SetAutoPageBreak(0);
$pdf->AliasNbPages();			

//recherche des stagiaires
$query="SELECT p.P_NOM, p.P_PRENOM, p.P_SEXE, p.P_BIRTHPLACE, pf.PF_DIPLOME,
		 DATE_FORMAT(p.P_BIRTHDATE, '%d-%m-%Y') P_BIRTHDATE, 
		 DATE_FORMAT(pf.PF_DATE, '%d-%m-%Y') PF_DATE
		 FROM pompier p, personnel_formation pf
		 WHERE pf.P_ID = p.P_ID
		 and pf.PF_ADMIS=1
		 and pf.PF_DIPLOME is not null 
		 and pf.PF_DIPLOME <> ''
		 and pf.E_CODE=".$evenement;
if ( $pid > 0 ) $query .= " and pf.P_ID = ".$pid;
		 
$query .= " ORDER BY P_NOM, P_PRENOM";


$result = mysql_query($query); 
$i=0;
while($data = mysql_fetch_array($result)) {
	$pdf->AddPage();
	
	if ( $mode == 3 ||  $mode == 4 ) {
		// Si disponible mettre image de fond
		$fond=$filesdir."/diplomes/diplome.jpg";
		$file=$filesdir."/diplomes/".$type.".jpg";
		if ( file_exists($file)) $fond=$file;
		if ( file_exists($fond))
			$pdf->Image($fond,0,0,297,210);
		if ( $mode == 4 )
			$pdf->Image("images/duplicata.gif",60,50);
	}
	
	for($j=1; $j != $numfields_org ; $j++) { 
     
		$pdf->SetDrawColor(237,242,247);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetXY($pos_x[$j],$pos_y[$j]);
		
		     if ($affichage[$j]=='0') $aff=strtoupper($data['P_NOM']).' '.ucfirst($data['P_PRENOM']);
		else if ($affichage[$j]=='1') $aff=strtoupper($data['P_NOM']).' '.strtoupper($data['P_PRENOM']);
		else if ($affichage[$j]=='2') $aff=ucfirst($data['P_NOM']).' '.ucfirst($data['P_PRENOM']);
		else if ($affichage[$j]=='3') $aff=$data['PF_DATE'];
		else if ($affichage[$j]=='4') $aff=$periode;
		else if ($affichage[$j]=='5') $aff=$data['P_BIRTHPLACE'];
		else if ($affichage[$j]=='6') $aff=$data['P_BIRTHDATE'];
		else if ($affichage[$j]=='7') if ( $mode > 1 ) $aff=$data['PF_DIPLOME']; else $aff="";
		else if ($affichage[$j]=='8') $aff=$fin;
		else if ($affichage[$j]=='9') $aff=$annexe[$j];
	    else if ($affichage[$j]=='10') $aff=$organisateur;
	    else if ($affichage[$j]=='11') $aff=$organisateur_city;
		
		$taille=$edi_taille[$j];
		if ($affichage[$j]<='2') {
		// diminution de la taille de la Police si le nom et prénom sont trop grand
			if (strlen($aff)>=24) { $taille=$taille-2;} ;
			if (strlen($aff)>=36) { $taille=$taille-1;} ;
		};
		if ( $actif[$j] == 1 ) {
			$pdf->SetFont($edi_police[$j],$edi_style[$j],$taille);
			$pdf->Text($pos_x[$j],$pos_y[$j], $aff) ;
		}	
	}
}
	
$pdf->Output();
?>

