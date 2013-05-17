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
check_all(4);

$P_ID=intval($_POST["P_ID"]);
$PS_ID=intval($_POST["PS_ID"]);
$PF_ID=intval($_POST["PF_ID"]);
if (isset($_GET["from"])) $from=$_GET["from"];
else $from='qualif';

$mysection=$_SESSION['SES_SECTION'];
if (! check_rights($_SESSION['id'], 4, get_section_of("$P_ID"))) check_all(24);

$evenement=intval($_POST["evenement"]);
$tf=mysql_real_escape_string($_POST["tf"]);
$dc=mysql_real_escape_string($_POST["dc"]);
$lieu=mysql_real_escape_string($_POST["lieu"]);
$resp=mysql_real_escape_string($_POST["resp"]);
$numdiplome=mysql_real_escape_string($_POST["numdiplome"]);
$comment=mysql_real_escape_string($_POST["comment"]);

$dc=STR_replace("\"","",$dc);
$lieu=STR_replace("\"","",$lieu);
$resp=STR_replace("\"","",$resp);
$comment=STR_replace("\"","",$comment);
$numdiplome=STR_replace("\"","",$numdiplome);

?>
<html>
<SCRIPT language=JavaScript>
function redirect(P_ID,PS_ID) {
     url="personnel_formation.php?P_ID="+P_ID+"&PS_ID="+PS_ID;
     self.location.href=url;
}
function redirect2(P_ID) {
     url="upd_personnel.php?pompier="+P_ID+"&from=formations";
     self.location.href=url;
}
</SCRIPT>
<?php

//=====================================================================
// enregistrer les infos pour la formation initiale
//=====================================================================
if ( $PF_ID <> 0 ) {
	// update data
	$tmp=explode ("-",$dc);
    $month=$tmp[1]; $day=$tmp[0]; $year=$tmp[2];
	$query="update personnel_formation set	
					  TF_CODE='".$tf."',
					  PF_DIPLOME=\"".$numdiplome."\",
					  PF_DATE='".$year."-".$month."-".$day."',
					  PF_RESPONSABLE=\"".$resp."\",
					  PF_LIEU=\"".$lieu."\",
					  PF_COMMENT=\"".$comment."\",
					  E_CODE=\"".$evenement."\"
		where PF_ID=".$PF_ID."
		and P_ID=".$P_ID."
		and PS_ID=".$PS_ID;
	$result=mysql_query($query);

}
else {
	// add data in personnel_formation
	save_personnel_formation($P_ID, $PS_ID, "$tf", "$dc", "$lieu", "$resp", "$comment","", "$numdiplome");
}
if ( $from='formations' ) 
	echo "<body onload=redirect2('".$P_ID."');>";
else
	echo "<body onload=redirect('".$P_ID."','".$PS_ID."');>";

?>