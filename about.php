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

if (isset($application_title_specific)) 
	echo "<h1>".$application_title_specific."</h1>
		  <p> Est une application de $cisname, utilisant le projet opensource $application_title";

echo "
    <p><b>eBrigade $version : application pour la gestion opérationnelle
    <br> des sapeurs pompiers et du personnel de secours
    <br>Copyright &copy; 2004-2010  Nicolas MARCHE</b> 

    <p>This program is free software; you can redistribute it and/or modify
    <br>it under the terms of the GNU General Public License as published by
    <br>the Free Software Foundation; either version 2 of the License, or
    <br>(at your option) any later version.

    <p>This program is distributed in the hope that it will be useful,
    <br>but WITHOUT ANY WARRANTY; without even the implied warranty of
    <br>MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    <br>GNU General Public License for more details.

    <p>You should have received a copy of the GNU General Public License along
    <br>with this program; if not, write to the Free Software Foundation, Inc.,
    <br>51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
    
    <p><b>authors: Nicolas MARCHE, Jean-Pierre KUNTZ</b> 
    <br>project page : <a href=http://sourceforge.net/projects/ebrigade target =_blank>http://sourceforge.net/projects/ebrigade</a>
    <br>Contact : <a href=mailto:nico.marche@free.fr>nico.marche@free.fr</a>
";

echo "<p><table>";

echo "<tr><td><a href=license_fr.txt target=_blank><img src=images/frenchflag.gif border=0></a></td><td><a href=license_fr.txt target=_blank> Lire la license en français </a></td></tr>";
echo "<tr><td><a href=license.txt target=_blank><img src=images/englishflag.gif  border=0></a></td><td><a href=license.txt target=_blank>  Read english license </a></font></td></tr>";
echo "</table>";

echo "<p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'>";
?>
    