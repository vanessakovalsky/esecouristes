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
check_all(7);

$month=$_GET["month"];
$year=$_GET["year"];
$section=$_GET["section"];
$action=$_GET["action"];
if ( isset ($_GET["equipe"])) 
$equipe=$_GET["equipe"];
else $equipe=0;

?>
<SCRIPT language=JavaScript>
function redirect(cible) {
   self.location.href = cible;
}
</SCRIPT>

<?php
include_once ("config.php");
$query= "delete from planning_garde_status where PGS_YEAR=$year and PGS_MONTH=$month and EQ_ID=$equipe";
$result=mysql_query($query);

if (( $action == "montrer" ) || ( $action == "fermer" )){
   $query="insert into planning_garde_status( PGS_YEAR, PGS_MONTH, EQ_ID, PGS_STATUS)
	   values ($year, $month, $equipe,'OK')";
   $result=mysql_query($query);
}
if ( $equipe == 0 ) 
	echo "<body onload=\"redirect('dispo.php?month=$month&year=$year');\">";
else
	echo "<body onload=\"redirect('tableau_garde.php?month=$month&year=$year&person=0&section=$section&equipe=".$equipe."&print=NO');\">";

?>
