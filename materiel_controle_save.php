<?php

  # written by: Nicolas MARCHE, Jean-Pierre KUNTZ, Vanessa KOVALSKY
  # contact: nico.marche@free.fr, vanessa.kovalsky@free.fr
  # project: eBrigade
  # homepage: http://sourceforge.net/projects/ebrigade/
  # version: 2.7

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
check_all(17);
$id=$_SESSION['id'];
$section=$_SESSION['SES_SECTION'];
?>

<html>
<SCRIPT language=JavaScript>

function redirect(url) {
     self.location.href=url;
}

</SCRIPT>

<?php

include_once ("config.php");

$MAC_COMMENT=STR_replace("\"","",$_GET["MAC_COMMENT"]);
$MA_ID=intval($_GET["MA_ID"]);
echo "l'id du materiel est".$MA_ID;
$MAC_COMMENT=mysql_real_escape_string($MAC_COMMENT);
$MAC_CONTROLE_DATE=mysql_real_escape_string($_GET["MAC_CONTROLE_DATE"]);
$CONTROLED_BY=($_GET["CONTROLED_BY"]);
$MAC_TYPE=($_GET["MAC_TYPE"]);

// verifier les permissions de modification
if (! check_rights($_SESSION['id'], 17,"$S_ID")) {
 check_all(24);
}

$tmp=explode ("-",$MAC_CONTROLE_DATE); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2];
$MAC_CONTROLE_DATE = "\"".$year1."-".$month1."-".$day1."\"";


//=====================================================================
// insertion nouvelle fiche de contrôle
//=====================================================================

$query="insert into materiel_controle 
   (MA_ID, MAC_CONTROLED_BY, MAC_COMMENT, MAC_CONTROLE_DATE, MAC_TYPE)
   values($MA_ID,$CONTROLED_BY,\"$MAC_COMMENT\",$MAC_CONTROLE_DATE,\"$MAC_TYPE\")";
   $result=mysql_query($query) or die (mysql_error());
   $_SESSION['sectionchoice'] = $S_ID;


   echo "<body onload=redirect('upd_materiel.php')>";

?>
