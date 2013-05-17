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
$mysection=$_SESSION['SES_SECTION'];

writehead();
$poste=mysql_real_escape_string($_GET["poste"]);
$section=mysql_real_escape_string($_GET["section"]);
$dispo=mysql_real_escape_string($_GET["dispo"]);

if ($dispo == '0')
 	$type='YN';
else {
	$type='O';
	$P=explode("-",$dispo);
	$udate=mktime(0,0,0,$P[1],$P[2],$P[0]);
	$day= date('j',$udate);
	$month= date('n',$udate);
	$year= date('Y',$udate);
}

include_once ("config.php");

echo "<body>";

// ===============================================
// personnel disponible
// ===============================================

echo "<table >";
echo "<tr>
<td class='FondMenu'>";
echo "<table border=0 cellspacing=0 cellpadding=0>
      <tr class=TabHeader>";
echo "<td width=300>liste des destinataires</td>";
echo "<td width=50>mail</td>";
echo "<td width=50>tél</td>";
echo "</tr>";
if ( $type == 'O' )
	personnel_dispo($year, $month, $day, $type, $poste, $section);
else
	personnel_dispo_ou_non($poste, $section);
echo "</table>";
echo "</td></tr></table>";

echo "<div align=center><input type=submit value='fermer' onclick='window.close();'>";
?>
