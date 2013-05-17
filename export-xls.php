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


//include_once("export-sql.php");
/*
Mise en forme de cellules
x:str
x:num=
x:fmla=
*/
header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="' . $export_name . '.xls"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header("Expires: 0");

$colSomme= array();	
$out ="";
$lig=0;
$nbcol = count($tab[0]);
//======================
$charset="ISO-8859-15";
//$charset="UTF8";
//======================

echo  "<html>";
echo  "<head>
<meta http-equiv=\"Content-type\" content=\"text/html;charset=".$charset."\" />
<style id=\"Classeur1_16681_Styles\"></style>
<style type=\"text/css\">";
echo  "</style>
</head>
<body>
<div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\">";
echo  "\n"."<table x:num border=1 cellpadding=0 cellspacing=0 width=100% style=\"border-collapse: collapse\">\n";
//
// Titres
//
	echo  "\n"."<tr>";	
	for($col=0;$col<$nbcol;$col++){
		echo "<th nowrap><b>".$tab[$lig][$col]."</b></th>";
	}
	echo  "</tr>";
//
// valeurs
//
	for($lig=1;$lig<count($tab);$lig++){
		echo "\n"."<tr>";
		for($col=0;$col<$nbcol;$col++){			
			//$cell = htmlspecialchars(NettoyerTexte($tab[$lig][$col]));
			$cell = NettoyerTexte($tab[$lig][$col]);
			//this is useful with french regional settings
			$tobeformated = array('Heures','Total','h/p.');
			if ( in_array( substr($tab[0][$col],0,6), $tobeformated) ) $cell = str_replace('.', ',', $cell);
			echo  "<td nowrap>".$cell."</td>";		
		}
		echo "</tr>";
	}
	echo  "\n"."</table>";
	echo  "\n"."</div>";
	echo  "\n"."</body>
	</html>";
//echo $out;
?>
