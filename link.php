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

if ( isset($application_title_specific))
	$application_title = $application_title_specific;

if ( isset($_GET["tab"])) $tab=intval($_GET["tab"]);
else $tab=1;
if ( $tab < 1 or $tab > 3 ) $tab=1;

echo "
<html> 
<head>
<style type='text/css'> 
body { margin:0; padding:0 } 
</style>
</head>
<map name='navmap'>
<area shape='rect' coords='10,0,170,31' href='index.php' target='_top' title='accès ".$application_title."'>
<area shape='rect' coords='180,0,340,31' href='external_open.php' target='_top' title='accès procion'>";

if (isset($extlink1))
echo "<area shape='rect' coords='360,0,520,31' href='link1index.php' target='_top' title='accès e-compta'>";

echo "</map>
<body style='background-image:url(images/link_background.png); background-repeat:repeat-x;'>
<img src=images/bandeau-0".$tab.".png border=0 usemap='#navmap' />
</body>
</html>";

?>