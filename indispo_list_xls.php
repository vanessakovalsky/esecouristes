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
check_all(0);
$id=$_SESSION['id'];
get_session_parameters();

$possibleorders= array('P_NOM','TI_CODE','I_STATUS','I_DEBUT','I_FIN','I_COMMENT');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='I_DEBUT';

$fixed_company = false;
if ( $_SESSION['SES_STATUT'] == 'EXT' ) {
	if (! check_rights($_SESSION['id'], 41)) {
		check_all(45);
		$company=$_SESSION['SES_COMPANY'];
		$_SESSION['company'] = $company;
		$fixed_company = true;
	}
}
else check_all(41);

if ($company <= 0 ) check_all(41);

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="liste-absences.xls"');
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


$query1="select distinct i.I_CODE, p.P_ID, p.P_NOM, p.P_PRENOM, p.P_OLD_MEMBER, DATE_FORMAT(i.I_DEBUT, '%d-%m-%Y') as I_DEBUT, DATE_FORMAT(i.I_FIN, '%d-%m-%Y') as I_FIN, i.TI_CODE,
        ti.TI_LIBELLE, i.I_COMMENT, ist.I_STATUS_LIBELLE, i.I_STATUS, date_format(i.IH_DEBUT,'%H:%i') IH_DEBUT, date_format(i.IH_FIN,'%H:%i') IH_FIN, i.I_JOUR_COMPLET
        from pompier p, indisponibilite i, type_indisponibilite ti, indisponibilite_status ist
        where p.P_ID=i.P_ID
	and i.TI_CODE=ti.TI_CODE
	and i.I_STATUS=ist.I_STATUS";

if ( $nbsections <> 1 ) {
	if ( $subsections == 1 ) 
		$query1 .= "\nand P_SECTION in (".get_family("$filter").")";
	else 
		$query1 .= "\nand  P_SECTION = ".$filter;
}	
if ( $statut <> "ALL") $query1 .= "\nand  p.P_STATUT = '".$statut."'";
if ( $type_indispo <> "ALL") $query1 .= "\nand  ti.TI_CODE = '".$type_indispo."'";
if ( intval($person) > 0 ) $query1 .= "\nand  p.P_ID = ".$person;
if ( $validation <> "ALL") $query1 .= "\nand  ist.I_STATUS = '".$validation."'";

$tmp=explode ( "-",$dtdb); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2]; 
$tmp=explode ( "-",$dtfn); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];
$query1 .="\n and i.I_DEBUT <= '$year2-$month2-$day2' 
			 and i.I_FIN   >= '$year1-$month1-$day1'";


if ( $order == 'P_NOM' ) $query1 .="\norder by p.P_NOM, p.P_PRENOM, i.I_DEBUT";
else $query1 .="\norder by i.".$order;

if ( $order == 'I_DEBUT' or $order == 'I_FIN' or $order == 'I_COMMENT' ) $query1 .=" desc";

$result1=mysql_query($query1);

echo "<tr>
      	<td width=150>Nom</td>
      	<td width=120>Absence</td>
      	<td width=120>début</td>
      	<td width=120>fin</td>
      	<td width=60>Durée</td>
      	<td width=100>Etat demande</td>
      	<td width=160>Commentaire</td>
      </tr>
      ";

$i=0;
while ($row=@mysql_fetch_array($result1)) {
       $I_CODE=$row["I_CODE"];
	   $I_JOUR_COMPLET=$row["I_JOUR_COMPLET"];
       $P_ID=$row["P_ID"];
       $P_NOM=$row["P_NOM"];
       $P_PRENOM=$row["P_PRENOM"];
       $I_DEBUT=$row["I_DEBUT"];
       $I_FIN=$row["I_FIN"];
       $TI_CODE=$row["TI_CODE"];
       $TI_LIBELLE=$row["TI_LIBELLE"];
       $I_COMMENT=$row["I_COMMENT"];
       $I_STATUS=$row["I_STATUS"];
       $IH_DEBUT=$row["IH_DEBUT"];
       $IH_FIN=$row["IH_FIN"];
       $I_STATUS_LIBELLE=$row["I_STATUS_LIBELLE"];
       $P_OLD_MEMBER=$row["P_OLD_MEMBER"];

      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      if ( $I_STATUS == 'VAL' ) $mytxtcolor='green';
      if (( $I_STATUS == 'ANN' ) or ( $I_STATUS == 'REF' )) $mytxtcolor='red';
      if (( $I_STATUS == 'ATT' )or ( $I_STATUS == 'PRE' ))  $mytxtcolor='orange';
      $abs=my_date_diff($I_DEBUT,$I_FIN) + 1;
      
      if ( $I_JOUR_COMPLET == 0 ) {
      		if ( $abs == 1 ) {
      		 	if ( substr($IH_FIN,0,1) == '0' ) $fin = substr($IH_FIN,1,1);
      		 	else  $fin = substr($IH_FIN,0,2);
      		 	if ( substr($IH_DEBUT,0,1) == '0' ) $debut = substr($IH_DEBUT,1,1);
      		 	else  $debut = substr($IH_DEBUT,0,2);      		 	
      		 	$abs = $fin - $debut;
      		 	$abs .= ' heures';
      		}
      		else $abs .= ' jours';
      		
      		$I_DEBUT=$I_DEBUT." ".$IH_DEBUT;
        	$I_FIN=$I_FIN." ".$IH_FIN;
      }
      else $abs .= ' jours';
      
      echo "<tr>
      	  <td>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</td>
      	  <td>".$TI_LIBELLE."</td>
      	  <td>".$I_DEBUT."</td>
      	  <td>".$I_FIN."</td>
      	  <td>".$abs."</td>
      	  <td>".$I_STATUS_LIBELLE."</td>
      	  <td>".$I_COMMENT."</td>
         </tr>";
}

echo "</table>"; 
echo "</body>
</html>";

?>
