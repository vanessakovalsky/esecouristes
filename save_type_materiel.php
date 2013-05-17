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
check_all(18);
$section=$_SESSION['SES_SECTION'];
?>

<html>
<SCRIPT language=JavaScript>

function redirect(url) {
     self.location.href=url;
}

function suppress(id) {
  if ( confirm("Voulez vous vraiment supprimer ce type de matériel?\n tous les articles de ce type seront supprimés")) {
     url="del_type_materiel.php?TM_ID="+id;
     self.location.href=url;
  }
  else{
       redirect();
  }
}
</SCRIPT>

<?php

include_once ("config.php");

$TM_ID=intval($_GET["TM_ID"]);
$TM_LOT=intval($_GET["TM_LOT"]);
$TM_CONTROLE=intval($_GET["TM_CONTROLE"]);
$TM_USAGE=mysql_real_escape_string($_GET["TM_USAGE"]);
$TM_CODE=mysql_real_escape_string($_GET["TM_CODE"]);
$TM_DESCRIPTION=mysql_real_escape_string($_GET["TM_DESCRIPTION"]);
$TM_PERIODE_CONTROLE=mysql_real_escape_string($_GET["TM_PERIODE_CONTROLE"]);
$operation=$_GET["operation"];

$TM_CODE=STR_replace("\"","",$TM_CODE);
$TM_DESCRIPTION=STR_replace("\"","",$TM_DESCRIPTION);

if (isset ($_GET["from"])) $from=$_GET["from"];
else $from=0;

//=====================================================================
// update la fiche
//=====================================================================

if ( $operation == 'update' ) {

    $query="update type_materiel set
	       TM_CODE=\"".$TM_CODE."\",
	       TM_USAGE=\"".$TM_USAGE."\",
	       TM_DESCRIPTION=\"".$TM_DESCRIPTION."\",
		   TM_LOT=".$TM_LOT.",
		   TM_CONTROLE=".$TM_CONTROLE.",
		   TM_PERIODE_CONTROLE=\"".$TM_PERIODE_CONTROLE."\"
		   where TM_ID =".$TM_ID;

    $result=mysql_query($query);
}


//=====================================================================
// insertion nouvelle fiche
//=====================================================================

if ( $operation == 'insert' ) {
   $query="insert into type_materiel 
   (TM_CODE, TM_DESCRIPTION, TM_USAGE,TM_LOT,TM_CONTROLE,TM_PERIODE_CONTROLE)
   values
   (\"".$TM_CODE."\",\"".$TM_DESCRIPTION."\",\"".$TM_USAGE."\",\"".$TM_LOT."\",\"".$TM_CONTROLE."\",'".$TM_PERIODE_CONTROLE."')";
   $result=mysql_query($query) or die ("MySQL ERROR: ".mysql_error());
   $_SESSION['catmateriel'] = $TM_USAGE;
}

if ($operation == 'delete' ) {
   echo "<body onload=suppress('".$TM_ID."')>";
}
else {
	echo $TM_CONTROLE;
   echo "<body onload=redirect('type_materiel.php?order=TM_USAGE&usage=$TM_USAGE')>";
}
?>
