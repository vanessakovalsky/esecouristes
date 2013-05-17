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
check_all(17);
$id=$_SESSION['id'];
$section=$_SESSION['SES_SECTION'];
?>

<html>
<SCRIPT language=JavaScript>

function redirect(url) {
     self.location.href=url;
}

function suppress(p1, p2) {
  if ( confirm("Voulez vous vraiment supprimer le véhicule "+ p1 +"?")) {
     url="del_vehicule.php?V_ID="+p2;
     self.location.href=url;
  }
  else{
       redirect('vehicule.php');
  }
}
</SCRIPT>

<?php

include_once ("config.php");

$V_ID=$_GET["V_ID"];

// check input parameters
$V_ID=intval(mysql_real_escape_string($V_ID));

$V_MODELE=STR_replace("\"","",$_GET["V_MODELE"]);
$V_IMMATRICULATION=STR_replace("\"","",$_GET["V_IMMATRICULATION"]);
$V_COMMENT=STR_replace("\"","",$_GET["V_COMMENT"]);
$V_INDICATIF=STR_replace("\"","",$_GET["V_INDICATIF"]);
$V_INVENTAIRE=STR_replace("\"","",$_GET["V_INVENTAIRE"]);

$TV_CODE=mysql_real_escape_string($_GET["TV_CODE"]);
$V_IMMATRICULATION=mysql_real_escape_string($V_IMMATRICULATION);
$V_COMMENT=mysql_real_escape_string($V_COMMENT);
$V_INVENTAIRE=mysql_real_escape_string($V_INVENTAIRE);
$V_INDICATIF=mysql_real_escape_string($V_INDICATIF);
$VP_ID=mysql_real_escape_string($_GET["VP_ID"]);
if (isset($_GET["V_KM"])) $V_KM=mysql_real_escape_string($_GET["V_KM"]);
else $V_KM=0;
$V_MODELE=mysql_real_escape_string($V_MODELE);
$EQ_ID=mysql_real_escape_string($_GET["EQ_ID"]);
$V_ANNEE=mysql_real_escape_string($_GET["V_ANNEE"]);
$V_ASS_DATE=mysql_real_escape_string($_GET["dc1"]);
$V_CT_DATE=mysql_real_escape_string($_GET["dc2"]);
$V_REV_DATE=mysql_real_escape_string($_GET["dc3"]);
$S_ID=mysql_real_escape_string($_GET["groupe"]);
$operation=mysql_real_escape_string($_GET["operation"]);
if (isset($_GET["V_EXTERNE"])) $V_EXTERNE=mysql_real_escape_string($_GET["V_EXTERNE"]);
else $V_EXTERNE=0;
if (isset($_GET["V_FLAG1"])) $V_FLAG1=intval($_GET["V_FLAG1"]);
else $V_FLAG1=0;
if (isset($_GET["V_FLAG2"])) $V_FLAG2=intval($_GET["V_FLAG2"]);
else $V_FLAG2=0;
if (isset($_GET["affected_to"])) $AFFECTED_TO=intval($_GET["affected_to"]);
else $AFFECTED_TO='null';
if ( $AFFECTED_TO == 0 )$AFFECTED_TO='null'; 

if ($operation == 'delete' ) check_all(19);

$P = array();
for ( $i = 1 ; $i <= 8 ; $i++) {
 $P[$i] = intval($_GET["P$i"]);
}

// verifier les permissions de modification
if (! check_rights($_SESSION['id'], 17,"$S_ID")) {
 check_all(24);
}
if ( $V_EXTERNE == 1 ) check_all(24);

if (isset ($_GET["from"])) $from=mysql_real_escape_string($_GET["from"]);
else $from=0;

//=====================================================================
// update la fiche
//=====================================================================

if ( $operation == 'update' ) {

	$query="select vp.VP_OPERATIONNEL, vp.VP_LIBELLE from vehicule v, vehicule_position vp
			where vp.VP_ID = v.VP_ID
			and v.V_ID=".$V_ID ;
	$result=mysql_query($query);
	$row=@mysql_fetch_array($result);
	$OLD_VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
	$OLD_VP_LIBELLE=$row["VP_LIBELLE"];
	
	$query="select VP_OPERATIONNEL, VP_LIBELLE from vehicule_position 
			where VP_ID = '".$VP_ID."'";
	$result=mysql_query($query);
	$row=@mysql_fetch_array($result);
	$NEW_VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
	$NEW_VP_LIBELLE=$row["VP_LIBELLE"];
	
    $query="update vehicule set
	       TV_CODE='".$TV_CODE."',
	       V_IMMATRICULATION=\"".$V_IMMATRICULATION."\",
	       V_COMMENT=\"".$V_COMMENT."\",
	       V_INVENTAIRE=\"".$V_INVENTAIRE."\",
	       V_INDICATIF=\"".$V_INDICATIF."\",
	       V_MODELE=\"".$V_MODELE."\",
	       EQ_ID='".$EQ_ID."',
	       VP_ID='".$VP_ID."',
	       V_KM='".$V_KM."',
	       V_EXTERNE=".$V_EXTERNE.",
	       V_FLAG1=".$V_FLAG1.",
	       V_FLAG2=".$V_FLAG2.",
	       V_ANNEE='".$V_ANNEE."',
		   AFFECTED_TO=".$AFFECTED_TO.",";
	if ( $V_ASS_DATE <> '') {
		$tmp=explode ("-",$V_ASS_DATE); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2];
	    $query .= "V_ASS_DATE='".$year1."-".$month1."-".$day1."',";
	}
	else  $query .= "V_ASS_DATE= null,";
	if ( $V_CT_DATE <> '') {
		$tmp=explode ("-",$V_CT_DATE); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];
	    $query .= "V_CT_DATE='".$year2."-".$month2."-".$day2."',";
	}
	else  $query .= "V_CT_DATE= null,";
	if ( $V_REV_DATE <> '') {
		$tmp=explode ("-",$V_REV_DATE); $month3=$tmp[1]; $day3=$tmp[0]; $year3=$tmp[2];
	    $query .= "V_REV_DATE='".$year3."-".$month3."-".$day3."',";
	}
	else  $query .= "V_REV_DATE= null,";	
    $query .= "   S_ID=".$S_ID." 
		   where V_ID=".$V_ID ;
    $result=mysql_query($query);
    insert_log('UPDV', $V_ID);

	// si reforme, vendu, detruit, on enregistre des infos
	// et envoyer un mail au responsable des véhicules
	if ( $OLD_VP_OPERATIONNEL <> $NEW_VP_OPERATIONNEL ) {
			insert_log('UPDSTV', $V_ID, ($NEW_VP_OPERATIONNEL >= 0)?"de nouveau opérationnel":"réformé");
		if (( $OLD_VP_OPERATIONNEL >= 0 ) and ( $NEW_VP_OPERATIONNEL < 0 )) {
			$query="update vehicule set V_UPDATE_BY=$id, V_UPDATE_DATE=NOW() where V_ID=".$V_ID;
        	$result=mysql_query($query);
		}
		if (( $OLD_VP_OPERATIONNEL < 0 ) and ( $NEW_VP_OPERATIONNEL >= 0 )) {
			$query="update vehicule set V_UPDATE_BY=null, V_UPDATE_DATE=null where V_ID=".$V_ID;
        	$result=mysql_query($query);
		}
        
        
        if ( $nbsections == 0 ) {
            if ((( $OLD_VP_OPERATIONNEL < 0 ) and ( $NEW_VP_OPERATIONNEL >= 0 )) or
            	(( $OLD_VP_OPERATIONNEL >= 0 ) and ( $NEW_VP_OPERATIONNEL < 0 ))) {
   				if (get_level("$S_ID")  >= $nbmaxlevels -1) { // antenne locale
   	  				$destid=get_granted(34,"$S_ID",'parent','yes');
   				}
   				else { // département, région
      				$destid=get_granted(34,"$S_ID",'local','yes');
      			}
      			$message  = "Bonjour,\n";
      			$m=get_section_name("$S_ID");
      			$n=$TV_CODE." ".$V_MODELE." - ".$V_IMMATRICULATION."";
      			$subject = "Changement de situation pour - ".$n;	               
      			$message = "La situation du véhicule a été modifiée pour ".$n;	
      			$message .= "\ndans la section: ".$m;
      			if ( $NEW_VP_OPERATIONNEL >= 0 ) $message .= "\nCe véhicule est de nouveau utilisable.";
      			else $message .= "\nCe véhicule est maintenant inutilisable car ".$NEW_VP_LIBELLE.".";
      			if ( $destid <> "" )
      				$nb = mysendmail("$destid" , $id , "$subject" , "$message" );
      			
      			$query="select s.S_EMAIL2, sf.NIV
				from section_flat sf, section s
				where s.S_ID = sf.S_ID
				and sf.NIV < 4
				and s.S_ID in (".$S_ID.",".get_section_parent("$S_ID").")
				order by sf.NIV";
	  			$result=mysql_query($query);
	  			$row=@mysql_fetch_array($result);
	  			$S_EMAIL2=$row["S_EMAIL2"];
	  			if ( $S_EMAIL2 <> "" )
					$nb2 = mysendmail2("$S_EMAIL2" , $_SESSION['id'] , "$subject" , "$message" );
   			}
	   	}
    }
	// update default grid
    $query = "delete from equipage where V_ID = ".$V_ID ;  
	$result=mysql_query($query);  
    for ( $i = 1 ; $i <= 8 ; $i++) {
     	$query = "insert equipage (PS_ID,ROLE_ID,V_ID)
     			  values (".$P[$i].",".$i.",".$V_ID.")";
     	$result=mysql_query($query); 
     	
     	//remove duplicates
     	$query = "update equipage set PS_ID = 0
		 		  where V_ID = ".$V_ID."
		 		  and PS_ID = ".$P[$i]."
				  and ROLE_ID <> ".$i ;  
		$result=mysql_query($query);  
	}

	$query="update vehicule set V_ANNEE=null where V_ANNEE='0000' and V_ID=".$V_ID;
	$result=mysql_query($query);
}

//=====================================================================
// insertion nouvelle fiche
//=====================================================================

if ( $operation == 'insert' ) {
   $query="select max(V_ID)+1 as NB from vehicule";
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   $NB=$row["NB"];
   if ($NB == '') $NB=1;
   
   $query="insert into vehicule 
   (V_ID, VP_ID, TV_CODE, V_IMMATRICULATION, V_COMMENT, V_KM, V_ANNEE, 
     EQ_ID, V_MODELE, S_ID, V_ASS_DATE, V_CT_DATE, V_REV_DATE, V_EXTERNE,V_INVENTAIRE,V_INDICATIF)
   values
   ($NB,'OP','$TV_CODE',\"$V_IMMATRICULATION\",\"$V_COMMENT\",'$V_KM','$V_ANNEE', 
    $EQ_ID, \"$V_MODELE\", '$S_ID', ";

	if ( $V_ASS_DATE <> '') {
		$tmp=explode ("-",$V_ASS_DATE); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2];
	    $query .= "'".$year1."-".$month1."-".$day1."',";
	}
	else  $query .= "null,";
	if ( $V_CT_DATE <> '') {
		$tmp=explode ("-",$V_CT_DATE); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];
	    $query .= "'".$year2."-".$month2."-".$day2."',";
	}
	else  $query .= "null,";
	if ( $V_REV_DATE <> '') {
		$tmp=explode ("-",$V_REV_DATE); $month3=$tmp[1]; $day3=$tmp[0]; $year3=$tmp[2];
	    $query .= "'".$year3."-".$month3."-".$day3."'";
	}
	else  $query .= "null";	
	
	$query .= ",'$V_EXTERNE',\"$V_INVENTAIRE\",\"$V_INDICATIF\")";
    $result=mysql_query($query);
    insert_log('INSV', $NB);
    $_SESSION['sectionchoice'] = $S_ID;
}

if ($operation == 'delete' ) {
   echo "<body onload=suppress('".$TV_CODE."','".$V_ID."')>";
}
else {
 if ( $from == 'garde' )
    echo "<body onload=redirect('garde_jour.php?P2=1')>";
 else
   echo "<body onload=redirect('vehicule.php')>";
}
?>
