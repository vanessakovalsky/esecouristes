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
$M_ID=intval($_GET["M_ID"]);
$catmessage=$_GET["catmessage"];

if ( $catmessage == 'amicale' or $nbsections == 0 )  $numfonction=16;
else $numfonction=8;
check_all($numfonction);

?>
<SCRIPT language=JavaScript>

function redirect(p1) {
     url="message.php?catmessage="+p1;
     self.location.href=url;
}
</SCRIPT>
<?php

$messages=$filesdir."/files_message";

if ( is_dir ($messages."/".$M_ID))
	full_rmdir($messages."/".$M_ID);
$query="delete from message where M_ID=".$M_ID;
$result=mysql_query($query);
echo "<body onload=redirect('".$catmessage."')></body>";

?>
