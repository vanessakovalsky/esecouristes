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
check_all(18);

?>

<html>
<SCRIPT language=JavaScript>

function redirect() {
     url="equipe.php";
     self.location.href=url;
}

</SCRIPT>

<?php
$id=$_SESSION['id'];
$EQ_ID=intval($_GET["EQ_ID"]);

//=====================================================================
// suppression fiche
//=====================================================================

$query="delete from planning_garde where EQ_ID=".$EQ_ID ;
$result=mysql_query($query);

$query="delete from planning_garde_status where EQ_ID=".$EQ_ID ;
$result=mysql_query($query);

$query="delete from poste where EQ_ID=".$EQ_ID;
$result=mysql_query($query);

$query="delete from qualification where PS_ID not in (select PS_ID from poste)";
$result=mysql_query($query);

$query="delete from equipe where EQ_ID=".$EQ_ID ;
$result=mysql_query($query);

$query="delete from categorie_evenement_affichage where EQ_ID=".$EQ_ID ;
$result=mysql_query($query);

echo "<body onload=redirect()>";

?>
