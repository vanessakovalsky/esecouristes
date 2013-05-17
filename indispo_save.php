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
$person=intval($_GET["person"]);
$section=get_section_of($person);
$statut= get_statut($person);
writehead();

check_all(11);
if ($_SESSION['id'] <> $person ) {
 check_all(12);
 if (! check_rights($_SESSION['id'], 12 , $section)) check_all(24);
}

$type=mysql_real_escape_string($_GET["type"]);
$comment=mysql_real_escape_string($_GET["comment"]);
$dc1=mysql_real_escape_string($_GET["dc1"]);
$dc2=mysql_real_escape_string($_GET["dc2"]);
$debut=mysql_real_escape_string($_GET["debut"]);
$fin=mysql_real_escape_string($_GET["fin"]);

if (isset($_GET["full_day"])) $full_day=1;
else $full_day=0; ;

$nom=get_nom($person);
$prenom=get_prenom($person);

if ( $type == "") {
 	write_msgbox("Erreur type", $error_pic, 
	" Le type d'absence doit être renseigné.<p align=center>
	<a href='indispo.php?person=$person'>$myspecialfont retour
	</font></a> ",10,0);
}
else if ( $dc1 == "") {
 	write_msgbox("Erreur date", $error_pic, 
	" La date de début doit être renseignée.<p align=center>
	<a href='indispo.php?person=$person'>$myspecialfont retour
	</font></a> ",10,0);
}
else if ( $dc2 == "") {
 	write_msgbox("Erreur date", $error_pic, 
	" La date de fin doit être renseignée.<p align=center>
	<a href='indispo.php?person=$person'>$myspecialfont retour
	</font></a> ",10,0);
}
else if ((( $statut == 'SPV' ) or ( $statut == 'BEN' )) and (( $type == 'CP' ) or ( $type== 'RTT' ))) {
 	write_msgbox("Erreur type indisponibilité", $error_pic, 
	" Les absences de type CP /RTT ne sont pas possibles pour le personnel de cette catégorie.<p align=center>
	<a href='indispo.php?person=$person'>$myspecialfont retour
	</font></a> ",10,0);
}
else {
$tmp=explode ( "-",$dc1); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2];
$date1=mktime(0,0,0,$month1,$day1,$year1);
$tmp=explode ( "-",$dc2); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];
$date2=mktime(0,0,0,$month2,$day2,$year2);

if (( $type == 'CP' ) or ( $type == 'RTT' )) $STATUT='ATT';
else $STATUT='VAL';

//insert indisponibilite
$query="insert into  indisponibilite (P_ID,  TI_CODE,  I_STATUS,  I_DEBUT,  I_FIN, I_COMMENT, IH_DEBUT, IH_FIN, I_JOUR_COMPLET)
	values (".$person.",'".$type."','".$STATUT."','".$year1."-".$month1."-".$day1."','".$year2."-".$month2."-".$day2."',\"".$comment."\",'".$debut."','".$fin."',$full_day)";
$result=mysql_query($query);	

// suppression du tableau de garde et des disponibilités
if (  $full_day == 1 ) {
	$query="delete from planning_garde where P_ID=".$person."
		and PG_DATE >='".$year1."-".$month1."-".$day1."' and PG_DATE <='".$year2."-".$month2."-".$day2."'";
	$result=mysql_query($query);
	$query="delete from disponibilite where P_ID=".$person."
		and D_DATE >='".$year1."-".$month1."-".$day1."' and PG_DATE <='".$year2."-".$month2."-".$day2."'";
	$result=mysql_query($query);
}

if ($dc1 == $dc2) {
 	$period = "du ".$day1."-".$month1."-".$year1;
 	if ( $full_day == 0 ) $period .= " de ".$debut." à ".$fin;
}
else  {
 	$period = "du ".$day1."-".$month1."-".$year1;
	if ( $full_day == 0 ) $period .=" ($debut)";
	$period .= " au ".$day2."-".$month2."-".$year2;
	if ( $full_day == 0 ) $period .=" ($fin)";
}

if ($log_actions == 1)
	insert_log('INSABS', $person, $type." ".$period);

// envoi email de notification
if (( $type== 'CP' ) or ( $type== 'RTT' ) ) {
 
 	$destid=get_granted(13,"$section",'parent','no').$person;
	// notifier auss les responsables d'autres sections selon les rôles de l'organigramme de la personne
	$query="select S_ID from section_role where S_ID <> ".$section ."
			and P_ID = ".$person;
	$result=mysql_query($query);
	while ($row=mysql_fetch_array($result)) {
	 	$destid .= ",".get_granted(13,$row["S_ID"],'local','no');
	}
 	
	$subject="demande de ".$type." pour ".ucfirst($prenom)." ".strtoupper($nom);
	$message="Merci de valider la demande de ".$type." de ".ucfirst($prenom)." ".strtoupper($nom)."\n
		  ".$period;
	$nb = mysendmail("$destid" , $_SESSION['id'] , $subject , "$message" );
	$info="<br>De plus, un email a été envoyé à $nb personnes, pour qu'ils valident la demande.";
}
else {
 	$info="";
}

write_msgbox("demande enregistrée", $star_pic, 
" L'absence (".$type.") de ".strtoupper($nom)." 
".ucfirst($prenom)." ".$period.
" a été enregistrée.".$info."<p align=center>
<a href='indispo_choice.php?statut=ALL&type=ALL&person=ALL&date=FUTURE&validation=ALL'>$myspecialfont retour</font></a> ",10,0);
}


?>