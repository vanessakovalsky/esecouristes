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
check_all(42);
get_session_parameters();

$possibleorders= array('TV_CODE','V_IMMATRICULATION','V_INDICATIF','V_MODELE','V_COMMENT','VP_OPERATIONNEL',
'V_ASS_DATE','V_CT_DATE','V_KM','V_FLAG1','V_FLAG2','AFFECTED_TO','AFFECTED_TO','S_CODE','V_ANNEE');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='TV_CODE';

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="liste-des-vehicules.xls"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header("Expires: 0");
$charset="ISO-8859-15";

echo  "<html>";
echo  "<head>
<meta http-equiv=\"Content-type\" content=\"text/html;charset=".$charset."\" />
<style id=\"Classeur1_16681_Styles\"></style>
<style type=\"text/css\">";
echo  "</style>
</head>
<body>
<div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\">";
echo  "\n"."<table x:num border=1 cellpadding=0 cellspacing=0 width=100% style=\"border-collapse: collapse\">";

$query1="select distinct v.V_ID ,v.VP_ID, v.TV_CODE, v.V_MODELE, v.EQ_ID,vp.VP_LIBELLE, v.V_INDICATIF,
		tv.TV_LIBELLE, vp.VP_OPERATIONNEL, v.V_IMMATRICULATION, v.V_COMMENT, v.V_INVENTAIRE,v.V_KM,v.V_EXTERNE, 
		v.V_ANNEE, tv.TV_USAGE, s.S_ID, s.S_CODE, 
		DATE_FORMAT(v.V_ASS_DATE, '%d-%m-%Y') as V_ASS_DATE,
		DATE_FORMAT(v.V_CT_DATE, '%d-%m-%Y') as V_CT_DATE,
		DATE_FORMAT(v.V_REV_DATE, '%d-%m-%Y') as V_REV_DATE, v.V_FLAG1, v.V_FLAG2, v.AFFECTED_TO
        from vehicule v, type_vehicule tv, vehicule_position vp, section s
		where v.TV_CODE=tv.TV_CODE
		and s.S_ID=v.S_ID
		and vp.VP_ID=v.VP_ID";
if ( $filter2 <> 'ALL' ) $query1 .= "\nand (tv.TV_USAGE='".$filter2."' or tv.TV_CODE='".$filter2."')";

// choix section
if ( $nbsections == 0 ) {
    if ( $subsections == 1 ) {
  	   $query1 .= "\nand v.S_ID in (".get_family("$filter").")";
    }
    else {
  	   $query1 .= "\nand v.S_ID =".$filter;
    }
}
if ( $old == 1 ) $query1 .="\nand vp.VP_OPERATIONNEL <0";
else $query1 .="\nand vp.VP_OPERATIONNEL >=0";

$query1 .="\norder by ". $order;
if ( $order == 'TV_USAGE' ) $query1 .=" desc";

$result1=mysql_query($query1);
$number=mysql_num_rows($result1);

if ( $filter <> 0 and $nbsections == 0 ) $cmt=" de ".get_section_name("$filter");
else $cmt=" de ".$cisname;

if ( $nbsections == 0 ) $colspan=9;
else $colspan=8;
echo "<tr>
  <td colspan= $colspan ><b>Véhicules et engins".$cmt."</b> ($number véhicules)</td>
 </tr>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr>
    <td>Véhicule</td>
    <td>Immat</td>
	<td>N°d'inventaire</td>";      	  
if ( $nbsections == 0 ) {      	  
  echo "<td>Section</td>";
}     	  
echo "<td>Modèle</td>
    <td>Commentaire</td>
    <td>Statut</td>
    <td>Année</td>
    <td>Indicatif</td>
    <td>Fin assurance</td>
    <td>Prochain CT</td>
    <td>Prochaine révision</td>";
if ( $nbsections == 0 )
	echo "<td>Mis à disposition</td>";
echo "<td>km</td>
	  <td>Neige</td>
	  <td>Clim.</td>
	  <td>Affecté à</td>
</tr>";

while ($row=@mysql_fetch_array($result1)) {
      $TV_CODE=$row["TV_CODE"];
      $V_COMMENT=$row["V_COMMENT"];
      $V_ID=$row["V_ID"];
      $VP_LIBELLE=$row["VP_LIBELLE"];
      $TV_LIBELLE=$row["TV_LIBELLE"];
      $VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
      $V_IMMATRICULATION=$row["V_IMMATRICULATION"];
      $V_COMMENT=$row["V_COMMENT"];
      $V_MODELE=$row["V_MODELE"];
      $EQ_ID=$row["EQ_ID"];
      $V_KM=$row["V_KM"];
      $V_ANNEE=$row["V_ANNEE"];
      $V_ASS_DATE=$row["V_ASS_DATE"];
      $V_CT_DATE=$row["V_CT_DATE"];
      $V_REV_DATE=$row["V_REV_DATE"];
      $TV_USAGE=$row["TV_USAGE"];
      $V_MODELE=$row["V_MODELE"];
      $V_INVENTAIRE=$row["V_INVENTAIRE"];
      $V_INDICATIF=$row["V_INDICATIF"];
      if ( $row["V_EXTERNE"] == 1 ) $V_EXTERNE='oui';
      else $V_EXTERNE='non';
      $S_ID=$row["S_ID"];
      $V_FLAG1=$row["V_FLAG1"]; 
	  if ( $V_FLAG1 == 1 ) $V_FLAG1='oui';
	  else $V_FLAG1='';
      $V_FLAG2=$row["V_FLAG2"];
	  if ( $V_FLAG2 == 1 ) $V_FLAG2='oui';
	  else $V_FLAG2='';
      $AFFECTED_TO=$row["AFFECTED_TO"];
      if ( $AFFECTED_TO <> '' ) {
       	$owner=substr(get_prenom($AFFECTED_TO),0,1).".".get_nom($AFFECTED_TO);
      }
      else $owner='';
      $S_CODE="'".$row["S_CODE"];
  
  	  if ( $VP_OPERATIONNEL > 0 ) {
	  	if ( my_date_diff(getnow(),$V_ASS_DATE) < 0 ) {
	  		$VP_LIBELLE = "assurance périmée";
	  	}
	  	else if ( my_date_diff(getnow(),$V_CT_DATE) < 0 ) {
	  		$VP_LIBELLE = "CT périmé";	  
	  	}
	  	else if (( my_date_diff(getnow(),$V_REV_DATE) < 0 ) and ( $VP_OPERATIONNEL <> 1)) {
			$VP_LIBELLE = "révision à faire";
	  	}  
      }
echo "<tr>
      	  <td><B>$TV_CODE</B></td>
      	  <td>$V_IMMATRICULATION</td>
		  <td>$V_INDICATIF</td>";
if ( $nbsections == 0 ) {    
	echo "<td>$S_CODE</td>";
}
echo "	  <td>$V_MODELE</td>
      	  <td>$V_COMMENT</td>
      	  <td><b>$VP_LIBELLE</b></td>
      	  <td>$V_ANNEE</td>
      	  <td>$V_INVENTAIRE</td>
      	  <td>$V_ASS_DATE</td>
      	  <td>$V_CT_DATE</td>
      	  <td>$V_REV_DATE</td>";
if ( $nbsections == 0 )
	echo "	  <td>$V_EXTERNE</td>";
echo "<td>$V_KM</td>
	 <td>$V_FLAG1</td>
	 <td>$V_FLAG2</td>
	 <td>".strtoupper($owner)."</td>
	</tr>";
      
}
echo "</table>";

?>
