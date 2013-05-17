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
check_all(13);
$id=$_SESSION['id'];

$code=intval($_GET["code"]);
$action=$_GET["action"];

$query="select p.P_ID, DATE_FORMAT(i.I_DEBUT, '%d-%m-%Y') as I_DEBUT, 
		DATE_FORMAT(i.I_FIN, '%d-%m-%Y') as I_FIN, i.TI_CODE, p.P_STATUT,
        ti.TI_LIBELLE, i.I_COMMENT, ist.I_STATUS_LIBELLE, i.I_STATUS,
        date_format(i.IH_DEBUT,'%H:%i') IH_DEBUT, date_format(i.IH_FIN,'%H:%i') IH_FIN, i.I_JOUR_COMPLET
        from pompier p, indisponibilite i, type_indisponibilite ti, indisponibilite_status ist
        where i.I_CODE = ".$code."
        and p.P_ID=i.P_ID
        and i.TI_CODE=ti.TI_CODE
		and i.I_STATUS=ist.I_STATUS";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$person=$row["P_ID"];
$section=get_section_of($person);
$debut=$row["I_DEBUT"];
$fin=$row["I_FIN"];
$IH_DEBUT=$row["IH_DEBUT"];
$IH_FIN=$row["IH_FIN"];
$I_JOUR_COMPLET=$row["I_JOUR_COMPLET"];
$type=$row["TI_CODE"];

$section=get_section_of($person);

if (! check_rights($_SESSION['id'], 13 , $section)) check_all(24);

if ($I_JOUR_COMPLET == 1  and  $debut == $fin )
	$periode="du ".$debut;
else if ($I_JOUR_COMPLET == 1 )
	$periode="du ".$debut." au ".$fin;
else if ( $debut == $fin )
	$periode="du ".$debut." de ".$IH_DEBUT." à ".$IH_FIN;
else
	$periode="du ".$debut." (".$IH_DEBUT.") au ".$fin." (".$IH_FIN.")";

if ( $action == "valider" )  {
   //valider la demande
   $query="update indisponibilite
	   set I_STATUS='VAL',
	   I_ACCEPT=NOW()
       where I_CODE=".$code;
   $result=mysql_query($query);
   $st="acceptée";
   if ($log_actions == 1)
	 insert_log('VALABS', $person, $type." ".$periode." ".$st);
}
if ( $action == "refuser" )  {
   //refuser la demande
   $query="update indisponibilite
	   set I_STATUS='REF',
	   I_CANCEL=NOW()
       where I_CODE=".$code;
   $result=mysql_query($query);
   $st="refusée";
   if ($log_actions == 1)
	 insert_log('REFABS', $person, $type." ".$periode." ".$st);
}
if ( $action == "supprimer" )  {
   //supprimer la demande
   $query="delete from  indisponibilite
           where I_CODE=".$code;
   $result=mysql_query($query);
   $st="supprimée";
   if ($log_actions == 1)
	 insert_log('DELABS', $person, $type." ".$periode." ".$st);
}

// envoi email de notification
if ( $type== 'CP' ||  $type== 'RTT' ) {
 
 	$destid=get_granted(13,"$section",'parent','no').$person;
 	// notifier auss les responsables d'autres sections selon les rôles de l'organigramme de la personne
	$query="select S_ID from section_role where S_ID <> ".$section ."
			and P_ID = ".$person;
	$result=mysql_query($query);
	while ($row=mysql_fetch_array($result)) {
	 	$destid .= ",".get_granted(13,$row["S_ID"],'local','no');
	}
 
	$subject="demande de ".$type." ".$st." pour ".ucfirst(get_prenom($person))." ".strtoupper(get_nom($person));
	$message="Bonjour,\n
La demande de ".$type." de ".ucfirst(get_prenom($person))." ".strtoupper(get_nom($person))."\n";
	$message .= $periode;
	$message .="\na été ".$st." par ".ucfirst(get_prenom($id))." ".strtoupper(get_nom($id)).".\n";
	$nb = mysendmail("$destid" , $_SESSION['id'] , $subject , "$message" );
}

echo "<body onload=\"opener.document.location.reload();window.close();\">";
?>

