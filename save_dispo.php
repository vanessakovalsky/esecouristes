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
$SES_NOM=$_SESSION['SES_NOM'];
$SES_PRENOM=$_SESSION['SES_PRENOM'];
$SES_GRADE=$_SESSION['SES_GRADE'];

$nbjours=intval($_GET["nbjours"]);
$month=intval($_GET["month"]);
$year=intval($_GET["year"]);
$person=intval($_GET["person"]);
if (get_matricule($person) == '' ) {
	param_error_msg();
	exit;
}

if ( $id <> $person ) {
 check_all(10);
 if (! check_rights($id, 10, get_section_of($person))) check_all(24);
}

echo "<body bgcolor=#FFFFFF text=$mydarkcolor link=$mydarkcolor vlink=$mydarkcolor alink=$mydarkcolor>";

//=====================================================================
// purger les disponibilités de la personne pour le mois en cours
//=====================================================================

$query="delete from disponibilite
        where P_ID=".$person."
	and D_DATE>='".$year."-".$month."-01'
 	and D_DATE<='".$year."-".$month."-".$nbjours."'";
$result=mysql_query($query);

//=====================================================================
// enregistrer les disponibilités saisies
//=====================================================================

$query="select P_NOM,P_PRENOM, P_EMAIL from pompier where P_ID=".$person;
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$prenom=my_ucfirst($row["P_PRENOM"]);
$nom=strtoupper($row["P_NOM"]);
$email = $row["P_EMAIL"];

$i=1;
while ( $i <=$nbjours ) {
    // récupérer les infos
    $query="insert into disponibilite (P_ID, D_DATE, D_JOUR, D_NUIT)
	      values ( ".$person.", '".$year."-".$month."-".$i."', ".intval($_GET["J$i"]).",".intval($_GET["N$i"]).")";
    $result=mysql_query($query);
    $i=$i+1;
}

$query="select count(*) as NB from disponibilite
        where P_ID=".$person."
        and D_JOUR=1
	and D_DATE>='".$year."-".$month."-01'
 	and D_DATE<='".$year."-".$month."-".$nbjours."'";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$J=$row["NB"];

$query="select count(*) as NB from disponibilite
        where P_ID=".$person."
        and D_NUIT=1
	and D_DATE>='".$year."-".$month."-01'
 	and D_DATE<='".$year."-".$month."-".$nbjours."'";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$N=$row["NB"];		


$moislettres=moislettres($month);
if ( $id == $person ) {
   write_msgbox("OK", $star_pic, "Merci <b>".$prenom."</B> tes disponibilités pour <b>".$moislettres."</b> ont été enregistrées (".$J." jours et ".$N." nuits)<p align=center><a href=index_d.php> $myspecialfont retour accueil</font></a>",30,0);
}
else {
   write_msgbox("OK", $star_pic, "Les disponibilités de ".$prenom." ".$nom." pour <b>".$moislettres."</b> ont été enregistrées (".$J." jours et ".$N." nuits)<p align=center><a href=dispo.php?person=$id > $myspecialfont retour</font></a>",30,0);
}

insert_log('UPDDISPO', $person, $moislettres." ".$year.": ".$J." jours + ".$N." nuits");

if ( $email <> "" ) {
	$message  = "Bonjour,\n";
	$subject = "Disponibilites enregistrees pour ".$moislettres." ".$year;	               
	$message = "Les disponibilités de ".$prenom." ".$nom."\n";
	$message .= "ont bien été enregistrées pour le mois de ".$moislettres." ".$year."\n";
	$message .= $J." jours et ".$N." nuits\n";
	$nb = mysendmail("$person" , $id , "$subject" , "$message" ,"yes" );
}

?>
