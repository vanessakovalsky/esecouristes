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
$order=mysql_real_escape_string($_GET["order"]);
$filter=mysql_real_escape_string($_GET["filter"]);
$type=mysql_real_escape_string($_GET["type"]);
$old=intval($_GET['old']);
$subsections=intval($_GET['subsections']);

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="liste-du-materiel.xls"');
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

$query1="select distinct m.TM_ID, tm.TM_CODE,tm.TM_DESCRIPTION,tm.TM_USAGE,
		 m.VP_ID,vp.VP_LIBELLE, vp.VP_OPERATIONNEL,vp.VP_LIBELLE,
		 DATE_FORMAT(m.MA_REV_DATE, '%d-%m-%Y') as MA_REV_DATE,
		 m.MA_ID, m.MA_NUMERO_SERIE, m.MA_COMMENT, m.MA_MODELE, cm.PICTURE_SMALL,m.MA_EXTERNE,
		 m.MA_ANNEE, m.MA_NB, m.S_ID, s.S_CODE ,m.MA_LIEU_STOCKAGE, m.MA_INVENTAIRE, m.AFFECTED_TO
        from materiel m, type_materiel tm, section s, vehicule_position vp, categorie_materiel cm
		where m.TM_ID=tm.TM_ID
		and cm.TM_USAGE = tm.TM_USAGE
		and m.VP_ID=vp.VP_ID
		and s.S_ID=m.S_ID";
if ( $type <> 'ALL' ) $query1 .= "\n and (tm.TM_ID='".$type."' or tm.TM_USAGE='".$type."')";

// choix section
if ( $nbsections == 0 ) {
    if ( $subsections == 1 ) {
  	   $query1 .= "\nand m.S_ID in (".get_family("$filter").")";
    }
    else {
  	   $query1 .= "\nand m.S_ID =".$filter;
    }
}
if ( $old == 1 ) $query1 .="\nand vp.VP_OPERATIONNEL <0";
else $query1 .="\nand vp.VP_OPERATIONNEL >=0";

$query1 .="\norder by ".$order;
if ( $order == 'TM_USAGE' ) $query1 .=" desc";

$result1=mysql_query($query1);
$number=mysql_num_rows($result1);

if ( $filter <> 0 ) $cmt=" de ".get_section_name("$filter");
else $cmt=" de ".$cisname;

if ( $nbsections == 0 ) $colspan=9;
else $colspan=8;
echo "<tr>
  <td colspan= $colspan ><b>Liste du matériel".$cmt."</b> ($number articles)</td>
 </tr>";

// ===============================================
// premiere ligne du tableau
// ===============================================


echo "<tr>";
echo "<td>Catégorie</td>";
echo "<td>Matériel</td>";  
echo "<td>Nb</td>";
if ( $nbsections == 0 ) {      	  
  echo "<td>Section</td>";
}     	  
echo "<td>Modèle</td>
	<td>N°Série</td>
    <td>Statut</td>
    <td>Date limite</td>
    <td>N°inventaire</td>
    <td>Lieu stockage</td>
    <td>Commentaire</td>
    <td>année</td>
    <td>Mis à disposition</td>
    <td>affecté à</td>
</tr>";

while ($row=@mysql_fetch_array($result1)) {
 		$TM_USAGE=$row["TM_USAGE"];
		$TM_CODE=$row["TM_CODE"];
		$TM_ID=$row["TM_ID"];
		$TM_DESCRIPTION=$row["TM_DESCRIPTION"];
		$TM_USAGE=$row["TM_USAGE"];
		$MA_ID=$row["MA_ID"];
		$MA_NUMERO_SERIE=$row["MA_NUMERO_SERIE"];
		$MA_COMMENT=$row["MA_COMMENT"];
		$MA_INVENTAIRE=$row["MA_INVENTAIRE"];
		$MA_MODELE=$row["MA_MODELE"]; 
		$MA_ANNEE=$row["MA_ANNEE"]; if ( $MA_ANNEE == '') $MA_ANNEE = '?';
		$MA_NB=$row["MA_NB"];
		$S_ID=$row["S_ID"];
		$MA_REV_DATE=$row["MA_REV_DATE"];
		$VP_LIBELLE=$row["VP_LIBELLE"];
		$VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
		$PICTURE_SMALL=$row["PICTURE_SMALL"];
		$S_CODE="'".$row["S_CODE"];
		$MA_LIEU_STOCKAGE=$row["MA_LIEU_STOCKAGE"];
      	$AFFECTED_TO=$row["AFFECTED_TO"];
      	$MA_EXTERNE=$row["MA_EXTERNE"];
      	if ( $MA_EXTERNE == 1 )$ext='oui';
		else $ext='non';
      	if ( $AFFECTED_TO <> '' ) {
       		$owner=substr(get_prenom($AFFECTED_TO),0,1).".".get_nom($AFFECTED_TO);
      	}
      	else $owner='';

echo "<tr>";
echo "<td>$TM_USAGE</td>";
echo "<td><B>$TM_CODE</B></td>
      <td>$MA_NB</td>";
if ( $nbsections == 0 ) {    
	echo "<td>$S_CODE</td>";
}
echo "	  <td>$MA_MODELE</td>
		  <td>$MA_NUMERO_SERIE</td>
      	  <td><b>$VP_LIBELLE</b></td>
      	  <td>$MA_REV_DATE</td>
      	  <td>$MA_INVENTAIRE</td>
      	  <td>$MA_LIEU_STOCKAGE</td>
      	  <td>$MA_COMMENT</td>
      	  <td>$MA_ANNEE</td>
      	  <td>$ext</td>
      	  <td>".strtoupper($owner)."</td>
      </tr>";
      
}
echo "</table>";

?>
