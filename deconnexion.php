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
@connect();
?>
<html>
<SCRIPT language=JavaScript>
function redirect(url) {
     top.location.href=url;
}
</SCRIPT>
<?php

if (isset($_SESSION['id'])) {
 	$query="update audit set A_FIN=NOW() where A_FIN is NULL and P_ID=".$_SESSION['id'];
 	$result=@mysql_query($query);
 	session_destroy();
}
if (isset($deconnect_redirect)) $identpage=$deconnect_redirect;
echo "<body onload='redirect(\"$identpage\")'></body>";
?>
