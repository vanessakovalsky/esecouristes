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
check_all(17);
$id=$_SESSION['id'];
$section=$_SESSION['SES_SECTION'];
?>

<html>
<SCRIPT language=JavaScript>

function redirect(url) {
     self.location.href=url;
}

function suppress(id) {
  if ( confirm("Voulez vous vraiment supprimer ce matériel?")) {
     url="del_materiel.php?MA_ID="+id;
     self.location.href=url;
  }
  else{
       redirect('materiel.php');
  }
}
</SCRIPT>

<?php

include_once ("config.php");

$MA_NUMERO_SERIE=STR_replace("\"","",$_GET["MA_NUMERO_SERIE"]);
$MA_COMMENT=STR_replace("\"","",$_GET["MA_COMMENT"]);
$MA_INVENTAIRE=STR_replace("\"","",$_GET["MA_INVENTAIRE"]);
$MA_MODELE=STR_replace("\"","",$_GET["MA_MODELE"]);
$MA_LIEU_STOCKAGE=STR_replace("\"","",$_GET["MA_LIEU_STOCKAGE"]);

$TM_ID=mysql_real_escape_string($_GET["TM_ID"]);
$TM_USAGE=mysql_real_escape_string($_GET["TM_USAGE"]);
$MA_ID=intval($_GET["MA_ID"]);
$MA_NUMERO_SERIE=mysql_real_escape_string($MA_NUMERO_SERIE);
$MA_COMMENT=mysql_real_escape_string($MA_COMMENT);
$MA_INVENTAIRE=mysql_real_escape_string($MA_INVENTAIRE);
$MA_LIEU_STOCKAGE=mysql_real_escape_string($MA_LIEU_STOCKAGE);
$MA_MODELE=mysql_real_escape_string($MA_MODELE);
$MA_ANNEE=mysql_real_escape_string($_GET["MA_ANNEE"]); if ($MA_ANNEE== '') $MA_ANNEE='null';
$MA_NB=intval($_GET["quantity"]);
$VP_ID=mysql_real_escape_string($_GET["VP_ID"]);
$S_ID=intval($_GET["groupe"]);
$MA_REV_DATE=mysql_real_escape_string($_GET["dc1"]);
if (isset($_GET["MA_EXTERNE"])) $MA_EXTERNE=intval($_GET["MA_EXTERNE"]);
else $MA_EXTERNE=0;
$operation=$_GET["operation"];
if (isset($_GET["affected_to"])) $AFFECTED_TO=intval($_GET["affected_to"]);
else $AFFECTED_TO='null';
if ( $AFFECTED_TO == 0 ) $AFFECTED_TO='null';
$V_ID='null';//$MA_PARENT='null';
if (isset($_GET["vid"])) {
	if ( substr($_GET["vid"],0,1) == 'V') {
		$V_ID=intval(substr($_GET["vid"],1));
		$MA_PARENT='null';
	}
	if ( substr($_GET["vid"],0,1) == 'M') {
		$MA_PARENT=intval(substr($_GET["vid"],1));
		$V_ID='null';
	}
}
else {
	$V_ID='null';
	$MA_PARENT='null';
}
if ( $V_ID == 0 ) $V_ID='null'; 
if ( $MA_PARENT == 0 ) $MA_PARENT='null';

// verifier les permissions de modification
if (! check_rights($_SESSION['id'], 17,"$S_ID")) {
 check_all(24);
}
if ( $MA_EXTERNE == 1 ) check_all(24);

if (isset ($_GET["from"])) $from=$_GET["from"];
else $from=0;


if ( $MA_REV_DATE <> '') {
	$tmp=explode ("-",$MA_REV_DATE); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2];
	$MA_REV_DATE = "\"".$year1."-".$month1."-".$day1."\"";
}
else  $MA_REV_DATE .= 'null';


//=====================================================================
// update la fiche
//=====================================================================

if ( $operation == 'update' ) {

	$query="select vp.VP_OPERATIONNEL from materiel m, vehicule_position vp 
                            where vp.VP_ID = m.VP_ID 
                            and MA_ID=".$MA_ID ; 
    $result=mysql_query($query); 
    $row=@mysql_fetch_array($result); 
    $OLD_VP_OPERATIONNEL=$row["VP_OPERATIONNEL"]; 
     
    $query="select VP_OPERATIONNEL from vehicule_position 
                            where VP_ID = '".$VP_ID."'"; 
    $result=mysql_query($query); 
    $row=@mysql_fetch_array($result); 
    $NEW_VP_OPERATIONNEL=$row["VP_OPERATIONNEL"]; 

    $query="update materiel set
	       TM_ID=\"".$TM_ID."\",
	       MA_NUMERO_SERIE=\"".$MA_NUMERO_SERIE."\",
	       MA_COMMENT=\"".$MA_COMMENT."\",
	       MA_INVENTAIRE=\"".$MA_INVENTAIRE."\",
	       MA_LIEU_STOCKAGE=\"".$MA_LIEU_STOCKAGE."\",
	       MA_NB=\"".$MA_NB."\",
	       MA_MODELE=\"".$MA_MODELE."\",
	       VP_ID=\"".$VP_ID."\",
	       MA_ANNEE=$MA_ANNEE,
	       MA_EXTERNE=".$MA_EXTERNE.",
	       AFFECTED_TO=".$AFFECTED_TO.",
	       V_ID=".$V_ID.",
		   MA_PARENT=".$MA_PARENT.",
		   MA_REV_DATE=".$MA_REV_DATE.",";
	       
    $query .= "S_ID=\"".$S_ID."\"
		   where MA_ID =".$MA_ID;

    $result=mysql_query($query);

	// si reforme, vendu, detruit, on enregistre des infos 
    if (( $OLD_VP_OPERATIONNEL > 0 ) and ( $NEW_VP_OPERATIONNEL < 0 )) { 
        $query="update materiel set MA_UPDATE_BY=$id, MA_UPDATE_DATE=NOW() where MA_ID=".$MA_ID; 
        $result=mysql_query($query); 
    } 
    if (( $OLD_VP_OPERATIONNEL < 0 ) and ( $NEW_VP_OPERATIONNEL > 0 )) { 
        $query="update materiel set MA_UPDATE_BY=null, MA_UPDATE_DATE=null where MA_ID=".$MA_ID; 
        $result=mysql_query($query); 
    }
	
	$query="select TM_LOT from type_materiel where TM_ID=$TM_ID";
	$result=mysql_query($query); 
	$row=@mysql_fetch_array($result);
	// si lot de matériel, ne peut pas être inclus dans un lot (éviter les hiérarchies)
	//if ( $row[0] == 1 ) {
        //$query="update materiel set MA_PARENT=null where MA_ID=".$MA_ID; 
        //$result=mysql_query($query);
	//}
	
	// si plus lot, enlever les pièces de matériel attachées
	//else
	 if ( $row['TM_LOT'] == 0 ){
	    $query="update materiel set MA_PARENT=null where MA_PARENT=".$MA_ID; 
        $result=mysql_query($query);
	}	
	
	if (!empty($MA_PARENT)) {
	// Si le matériel n'est plus opérationnel, le lot non plus'
		$query_update_operationnel="update materiel set VP_ID='".$VP_ID."' WHERE MA_ID=".$MA_PARENT;
		$result_update_operationnel=mysql_query($query_update_operationnel) or die ("MySQL ERROR: ".mysql_error());
	}
}

//=====================================================================
// insertion nouvelle fiche
//=====================================================================

if ( $operation == 'insert' ) {
   $query="insert into materiel 
   (VP_ID, TM_ID, MA_NUMERO_SERIE, MA_COMMENT,MA_LIEU_STOCKAGE, MA_ANNEE, MA_EXTERNE, MA_MODELE, MA_NB, S_ID, MA_INVENTAIRE, MA_REV_DATE, V_ID)
   values
   (\"$VP_ID\",\"$TM_ID\",\"$MA_NUMERO_SERIE\",\"$MA_COMMENT\",\"$MA_LIEU_STOCKAGE\",$MA_ANNEE, $MA_EXTERNE, \"$MA_MODELE\", \"$MA_NB\",\"$S_ID\",\"$MA_INVENTAIRE\",$MA_REV_DATE,$V_ID)";
   $result=mysql_query($query);
   $_SESSION['sectionchoice'] = $S_ID;
}

if ($operation == 'delete' ) {
   echo "<body onload=suppress('".$MA_ID."')>";
}
else {
   echo "<body onload=redirect('materiel.php')>";
}
?>
