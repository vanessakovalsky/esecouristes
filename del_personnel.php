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
check_all(3);

?>

<html>
<SCRIPT language=JavaScript>

function redirect(section) {
     url="personnel.php?order=P_NOM";
     self.location.href=url;
}

</SCRIPT>

<?php
$id=$_SESSION['id'];
$P_ID=intval($_GET["P_ID"]);
if (! check_rights($_SESSION['id'], 3, get_section_of("$P_ID"))) check_all(24);

//=====================================================================
// suppression fiche
//=====================================================================
$nom=STR_replace(" ","",get_nom($P_ID));
$prenom=STR_replace(" ","",get_prenom($P_ID));
insert_log('DELP', 0, $prenom." ".$nom);

$query="delete from qualification where P_ID=".$P_ID ;
$result=mysql_query($query);

$query="delete from dispo where P_ID=".$P_ID ;
$result=mysql_query($query);

$query="delete from indisponibilite where P_ID=".$P_ID ;
$result=mysql_query($query);

$query="delete from pompier where P_ID=".$P_ID ;
$result=mysql_query($query);

$query="delete from evenement_participation where P_ID=".$P_ID ;
$result=mysql_query($query);

$query="delete from personnel_formation where P_ID=".$P_ID ;
$result=mysql_query($query);

$query="update message set P_ID=(select min(P_ID) from pompier where GP_ID=4) where P_ID=".$P_ID ;
$result=mysql_query($query);

$query="update smslog set P_ID=(select min(P_ID) from pompier where GP_ID=4) where P_ID=".$P_ID ;
$result=mysql_query($query);

$query="update vehicule set AFFECTED_TO=null where AFFECTED_TO=".$P_ID ;
$result=mysql_query($query);

$query="update materiel set AFFECTED_TO=null where AFFECTED_TO=".$P_ID ;
$result=mysql_query($query);

$query="update document set D_CREATED_BY=null where D_CREATED_BY=".$P_ID ;
$result=mysql_query($query);

$query="update company set C_CREATED_BY=null where C_CREATED_BY=".$P_ID ;
$result=mysql_query($query);

$query="update pompier set P_CREATED_BY=null where P_CREATED_BY=".$P_ID ;
$result=mysql_query($query);

$query="update pompier set P_UPDATED_BY=null where P_UPDATED_BY=".$P_ID ;
$result=mysql_query($query);

$query="delete from log_history where P_ID=".$P_ID ;
$result=mysql_query($query);

$query="delete from log_history where LH_WHAT=".$P_ID."
		and LT_CODE in (select LT_CODE from LOG_TYPE where LC_CODE='P')";
$result=mysql_query($query);



echo "<body onload=redirect()>";

?>
