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
check_all(19);

?>

<html>
<SCRIPT language=JavaScript>

function redirect() {
     url="materiel.php?order=TM_USAGE";
     self.location.href=url;
}

</SCRIPT>

<?php
$id=$_SESSION['id'];
$MA_ID=$_GET["MA_ID"];

// verifier les permissions de suppression: on a que le droit sur la section pere et ses descendants
$query="select S_ID from materiel where MA_ID=".$MA_ID;
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$S_ID=$row["S_ID"];
$mysection=$_SESSION['SES_SECTION'];
$mysectionparent=get_section_parent($mysection);
if (! is_children($S_ID,$mysectionparent)) {
check_all(24);
}

//=====================================================================
// suppression fiche
//=====================================================================

$query="delete from materiel where MA_ID=".$MA_ID ;
$result=mysql_query($query);

$query="update materiel set MA_PARENT = null where MA_ID=".$MA_ID ;
$result=mysql_query($query);

$query="delete from evenement_materiel where MA_ID=".$MA_ID ;
$result=mysql_query($query);

echo "<body onload=redirect()>";

?>
