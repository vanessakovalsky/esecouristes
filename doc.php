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
check_all(0);
writehead();

echo "<body>";

if (isset($application_title_specific)) $title=$application_title_specific;

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width =60><img src=images/info.png></td><td>
      <font size=4><b> Documentation $title </b></font></td></tr></table><p>";

echo "<p><table>";

$wikiurl="http://sourceforge.net/apps/mediawiki/ebrigade";

echo "<tr><td><img src=images/FORsmall.gif border=0></td>";
		echo "<td><a href=$wikiurl target=_blank>Aide en ligne $title</a>";
		echo "</td><td>";
		echo "</td></tr>";

if ( is_file($userguide) ||  is_file($adminguide)) {
	if ( is_file($userguide)) {
	 	echo "<tr><td><img src=images/FORsmall.gif border=0></td>";
		echo "<td><a href=$userguide>Aide en ligne habilitation public</a>";
		echo "</td><td>";
		echo "<font size=1><i>modifié le ".date("d-m-Y H:i",filemtime($userguide))."</font></i>";
		echo "</td></tr>";
	}

	if ( is_file($adminguide)) {
	 	echo "<tr><td><img src=images/FORsmall.gif border=0></td>";
		echo "<td><a href=$adminguide>Aide en ligne autres habilitations</a>";
		echo "</td><td>";
		echo "<font size=1><i>modifié le ".date("d-m-Y H:i",filemtime($adminguide))."</font></i>";
		echo "</td></tr>";
	}
}

echo "
	<td><a href='http://www.teamviewer.com' target='_blank'><img src=images/teamviewer.png border=0></a></td>
	<td colspan=2><a href='http://www.teamviewer.com/download/TeamViewer_Setup.exe'>Installer TeamViewer</a></td>
	</tr>";
	
echo "</table>";

echo "<p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'>
<div>";
?>
    