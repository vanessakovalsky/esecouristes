<?php

  # written by: Nicolas MARCHE, Jean-Pierre KUNTZ
  # contact: nico.marche@free.fr
  # project: eBrigade
  # homepage: http://sourceforge.net/projects/ebrigade/
  # version: 2.5

  # Copyright (C) 2004, 2010 Nicolas MARCHE
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

include_once ("../config.php");
$urlExec = $_SERVER['PHP_SELF'];
check_all(27);
writehead();
if ( isset($_GET["map_mode"])) $map_mode=$_GET["map_mode"];
else $map_mode=0;
$_SESSION["map_mode"]=$map_mode;
$maps=array(
'Opérations de secours ',
'Autres Opérations',
'Formations',
'Membres',
'Veille opérationnelle',
'Tous les événements',
'DPS',
'Matériel '.$cisname,
'Véhicules'
);

?>
<script language="JavaScript">
function orderfilter(report){
	 self.location.href="index.php?map_mode="+report;
	 return true;
}
</script>
</head>
<?php

echo "<body>
	<div align=center>
	<h1>Carte des ".$maps[$map_mode]."</h1>
	Choisir une carte<select id='report' name='report' 
		onchange=\"orderfilter(document.getElementById('report').value)\">";

for ( $i=0; $i < count($maps); $i++ ) {
	if ( $i == 0 ) echo "<optgroup class='categorie' label='Affichage du Personnel'>";
	if ( $i == 5 ) echo "<optgroup class='categorie' label='Affichage des Evénements'>";
	if ( $i == 7 ) echo "<optgroup class='categorie' label='Affichage des Véhicules et du Matériel'>";
	if ($map_mode  == $i ) $selected='selected';
	else $selected ='';
	echo "<option value='$i' $selected>".$maps[$i]."</option>";	
}
echo "</select><p>";


include('map.php');

?>