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
  
include_once ("config.php");
check_all(40);
writehead();
?>
<head>
<script src="livesearch.js"></script> 
<style type="text/css"> 
#livesearch
  { 
  margin:0px;
  width:194px; 
  }
#txt1
  { 
  margin:0px;
  } 
</style>
</head>
<body>
<?php
echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 70 ><img src=images/xmag.png></td><td>
      <font size=4><b>Recherche de personnel</b></font></td></tr></table>";

echo "<p><form>";
echo "<table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table width=300 cellspacing=0 border=0>";
echo "<tr bgcolor=$mylightcolor>";
echo "<td align=center><font size=1>tapez les premières lettres du nom de famille</font></td>";
echo "</tr>"; 
echo "<tr bgcolor=$mylightcolor>";    	  
echo "<td align=center><input type='text' id='txt1' size='30'";
echo "onkeyup='showResult(this.value)'><div id='livesearch'></div>";
echo "</td></tr>";
echo "</form></div></body>";
?>

