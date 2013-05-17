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
writehead();
?>

<html>
<SCRIPT language=JavaScript>
function redirect1(month,year,day, equipe) {
     url="processing.php?month="+month+"&year="+year+"&day="+day+"&equipe="+equipe;
     self.location.href=url;
}
function redirect2(month,year,equipe) {
     url="tableau_garde.php?month="+month+"&year="+year+"&person=0&section=0&print=NO&equipe="+equipe;
     self.location.href=url;
}

</SCRIPT>
<?php
include_once ("config.php");

$month=$_GET["month"];
$year=$_GET["year"];
$day=$_GET["day"];
$equipe=$_GET["equipe"];


if ( $day == 1 ) del_1_mois_garde($year, $month, $equipe);

// les pros ne font pas les préventives, ils peuvent les faire en tant que SPV
// si il y a des pros alors on doit avoir des sections.
if (( $equipe <> 2 ) and ($nbsections == 3 )) { 
   if ( equipeactive($equipe,'J') == 1 ) {
      	fill_priorite_spp ($equipe,$year, $month, $day,'J');
      	fill_1_garde ($equipe,$year, $month, $day, 'J','PRO');
	  }
   if ( equipeactive($equipe,'N') == 1 ) {
      fill_priorite_spp ($equipe,$year, $month, $day,'N');
      fill_1_garde ($equipe,$year, $month, $day, 'N','PRO');
   }
}

// volontaires
if ( equipeactive($equipe,'J') == 1 ) {
   fill_priorite_spv ($equipe,$year, $month, $day, 'J');
   //debug_display();
   fill_1_garde ($equipe,$year, $month, $day, 'J','SPV');
}
if ( equipeactive($equipe,'N') == 1 ) {
   fill_priorite_spv ($equipe,$year, $month, $day, 'N');
   fill_1_garde ($equipe,$year, $month, $day, 'N','SPV' );
}
$d=nbjoursdumois($month, $year);
if ( $day < $d ) {
   $next =$day + 1;
   echo "<body onload='redirect1($month,$year,$next,$equipe)'>";
   echo "<div id='Layer2' style='position:absolute; width:600px; height:200px; z-index:1; left: 150px; top: 150px'>";
   echo "<font size=3><b>Calcul pour le $day</b><br></font>";
}
else {
  echo "<body onload='redirect2($month,$year,$equipe)'>";
}
    	
?>
