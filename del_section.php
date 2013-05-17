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
check_all(14);
?>

<html>
<SCRIPT language=JavaScript>

function redirect() {
     url="section.php";
     self.location.href=url;
}

</SCRIPT>

<?php
$id=$_SESSION['id'];
$S_ID=intval($_GET["S_ID"]);
$parent=get_section_parent($S_ID);


//=====================================================================
// suppression fiche
//=====================================================================

$query="delete from section where S_ID=".$S_ID ;
$result=mysql_query($query);

$query="delete from document where S_ID=".$S_ID ;
$result=mysql_query($query);

rebuild_section_flat(-1,0,6);

$mypath=$filesdir."/files_section/".$S_ID;
if(is_dir($mypath)) {
   full_rmdir($mypath);
}

//=====================================================================
// mise à jour données
//=====================================================================

$tables = array ('vehicule','evenement','planning_garde',
				 'planning_garde_status','disponibilite','indisponibilite',
				 'audit','message','qualification','smslog','company','materiel');

for ( $n = 0; $n < sizeof($tables); $n++ ) {
 	$query="update ".$tables[$n]." set S_ID=".$parent." where S_ID=".$S_ID ;
 	$result=mysql_query($query);
}

$query="update pompier set P_SECTION =".$parent." where P_SECTION =".$S_ID ;
$result=mysql_query($query);

$query="update section set S_PARENT =".$parent." where S_PARENT =".$S_ID ;
$result=mysql_query($query);

echo "<body onload=redirect()>";

?>
