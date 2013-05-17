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
check_all(18);

?>

<html>
<SCRIPT language=JavaScript>

function redirect(p1) {
     url="paramfn.php?filter="+p1;
     self.location.href=url;
}

function suppress(p1) {
  if ( confirm("Voulez vous vraiment supprimer cette fonction? \n")) {
     url="paramfn_save.php?operation=delete&confirmed=1&TP_ID="+p1;
     self.location.href=url;
  }
  else{
       redirect();
  }
}
</SCRIPT>

<?php

include_once ("config.php");

$TP_ID=intval($_GET["TP_ID"]);

if (isset($_GET["operation"])) $operation=mysql_real_escape_string($_GET["operation"]);
else $operation="update";
if (isset($_GET["filter"])) $filter=mysql_real_escape_string($_GET["filter"]);
else $filter='ALL';

if ( $operation <> 'delete' ) {
	$TP_NUM=intval($_GET["TP_NUM"]);
	$PS_ID=intval($_GET["PS_ID"]);
	$PS_ID2=intval($_GET["PS_ID2"]);
	$INSTRUCTOR=intval($_GET["INSTRUCTOR"]);
	$TE_CODE=mysql_real_escape_string($_GET["TE_CODE"]);
	$TP_LIBELLE=mysql_real_escape_string($_GET["TP_LIBELLE"]);
}
else if (isset($_GET["confirmed"])) $confirmed=true;
else $confirmed=false;
//=====================================================================
// update la fiche
//=====================================================================

if ( $operation == 'update' ) {
   $query="update type_participation set
	       TP_NUM=".$TP_NUM.",
	       PS_ID=".$PS_ID.",
		   PS_ID2=".$PS_ID2.",
		   INSTRUCTOR=".$INSTRUCTOR.",
	       TP_LIBELLE=\"".$TP_LIBELLE."\",
	       TE_CODE=\"".$TE_CODE."\"
		  where TP_ID=".$TP_ID ;
   $result=mysql_query($query);
}

//=====================================================================
// insertion nouvelle fiche
//=====================================================================

if ( $operation == 'insert' ) {
   $query="insert into type_participation
   (TE_CODE, TP_NUM, TP_LIBELLE, PS_ID, PS_ID2, INSTRUCTOR)
   values
   (\"$TE_CODE\", $TP_NUM, \"$TP_LIBELLE\", $PS_ID, $PS_ID2, $INSTRUCTOR)";
   $result=mysql_query($query);
}

if ($operation == 'delete' ) {
   if ( $confirmed) {
    	$query="delete from type_participation where TP_ID=".$TP_ID;
    	$result=mysql_query($query);
    	$query="update evenement_participation set TP_ID=0 where TP_ID=".$TP_ID;
      	$result=mysql_query($query);
      	echo "<body onload=redirect('".$filter."')>";
   }
   else
		echo "<body onload=suppress('$TP_ID')>";
}
else {
  echo "<body onload=redirect('".$filter."')>";
}
?>
