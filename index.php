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
@session_start();
connect();
if ( ! isset($_SESSION['id'])) {
	if (stristr($_SERVER['HTTP_USER_AGENT'], "iPhone")  
	|| strpos($_SERVER['HTTP_USER_AGENT'], "iPod")
	|| strpos($_SERVER['HTTP_USER_AGENT'], "iPad")) { 
    	include ("iphone.php");
	} 
	else {
		include ("identification.php");
	}
}
else {
    writehead();
    if (isset($linkframepage) and check_rights($_SESSION['id'], 44))
	echo " 
	  <FRAMESET ROWS='31,*' frameborder='NO' border='0' framespacing='0'>
		<frame src=link.php scrolling='NO' name='A'>
        <frameset cols='20%,80%' rows='*' frameborder='NO' border='0' framespacing='0' name='B'>
        	<frame src='index_g.php' name='gauche'>
        	<frame src='index_d.php' name='droite'>
        </frameset>
      </frameset>";
    else
    echo " 
      <frameset cols='15%,80%' rows='*' frameborder='NO' border='0' framespacing='0'>
        	<frame src='index_g.php' name='gauche''>
        	<frame src='index_d.php' name='droite'>
      </frameset>";
}
?>
  


