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
  
header('Content-Type: text/html; charset=ISO-8859-1');

echo "<html>";
include("config.php");
check_all(40);

if ( $gardes == 1 ) $title="Affectations";
else $title="Compétences";
writehead();

echo "
<script type=\"text/javascript\" src=\"js/jquery.js\"></script>
<script type=\"text/javascript\">
$(\"input#trouve\").keyup(function(){
	var trouve;
	trouve = $(\"input#trouve\").val();
	oldmbr = $(\"select#oldmbr option:selected\").val();
	$.post(\"search_personnel_result.php\",{trouve:trouve,typetri:'nom'},	
	function (data){		
		$(\"#export\").empty();
		$(\"#export\").append(data);
	});
});
</script>";
echo "</head>";
echo "<body>";
$frm = "<form name=\"frmPers\" method=\"post\" action=\"search_personnel_result.php\">";
$frm .= "<p style=\"font-size:small;\">Tapez les premières lettres du nom de famille</p>";
$frm .= "<input type=\"text\" name=\"trouve\" id=\"trouve\" value=\"\">";
$frm .= "<input type=\"hidden\" name=\"typetri\" id=\"typetri\" value=\"nom\">";
$frm .= "<script type=\"text/javascript\"></script>
<noscript>
<input type=\"hidden\" name=\"retour\" id=\"retour\" value=\"search_personnel_nom.php\">
<input type=\"submit\">
</noscript>";
$frm .= "</form>";
//$frm .= "<div id=\"export\"></div>";
echo $frm;
echo "</body></html>";
?>