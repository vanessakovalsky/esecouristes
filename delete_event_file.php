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
?>
<script language="JavaScript">
function redirect(url){
	self.location.href=url;
	return true
}
</script>
<?php

$file=$_GET["file"];
$number=$_GET["number"];
$type=$_GET["type"];

if ( $type == 'evenement' ) {
	if (! check_rights($_SESSION['id'], 47, get_section_organisatrice("$number"))
		and get_chef_evenement($number) <> $_SESSION['id'] )
		check_all(15);
	$path=$filesdir."/files/".$number;
	
	$query="delete from document where E_CODE=".$number." and D_NAME=\"".$file."\"";
	$result=mysql_query($query);
	$url="evenement_display.php?from=document&evenement=".$number;
	
}
else if ( $type == 'section' ) {
	if (! check_rights($_SESSION['id'], 47, "$number"))
		check_all(22);
	$path=$filesdir."/files_section/".$number;
	
	$query="delete from document where S_ID=".$number." and D_NAME=\"".$file."\"";
	$result=mysql_query($query);
	$url="upd_section.php?S_ID=".$number."#documents";
	
}
else check_all(14);

unlink($path."/".$file);

echo "<body onload=redirect('".$url."');>";

?>
