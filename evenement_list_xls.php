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

$section=$filter;

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
header('Content-Disposition: attachment; filename="liste-evenements.xls"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header("Expires: 0");
$charset="ISO-8859-15";

// Libellé événement
$lib=((isset ($_GET["lib"]))?"%".$_GET["lib"]."%":"%");

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

$query="select E.TE_CODE, TE.TE_LIBELLE, E.E_LIEU, 
	DATE_FORMAT(EH.EH_DATE_DEBUT, '%d-%m-%Y') as EH_DATE_DEBUT,
	DATE_FORMAT(EH.EH_DATE_FIN, '%d-%m-%Y') as EH_DATE_FIN, 
	TIME_FORMAT(EH.EH_DEBUT, '%k:%i') as EH_DEBUT, 
	TIME_FORMAT(EH.EH_FIN, '%k:%i') as  EH_FIN, 
	E.E_NB, E.E_LIBELLE, E.E_CODE, E.E_CLOSED, E.E_OPEN_TO_EXT, E.E_CANCELED, S.S_CODE, E.S_ID,
	E.E_PARENT, E.E_NB1, E.E_NB2, E.E_NB3, E.TAV_ID
    from evenement E, type_evenement TE, section S, evenement_horaire EH
	where E.TE_CODE=TE.TE_CODE
	and E.E_CODE = EH.E_CODE
	and E.S_ID = S.S_ID";

if ( $type_evenement <> 'ALL' ) 
	$query .= "\n and E.TE_CODE = '".$type_evenement."'";

if (( is_formateur($id) == 0 ) 
	and (! check_rights($_SESSION['id'], 15))) 
	$query .= "\n and E.TE_CODE <> 'INS'";

if ( $nbsections <> 1 ) {
 	if ( $subsections == 1 )
 		$query .= "\n and S.S_ID in (".get_family("$section").")";
 	else 
 		$query .= "\n and S.S_ID =".$section;
}
if ( $canceled == 0 )
	$query .= "\n and E.E_CANCELED = 0";

if ( $company <> '-1' )
	$query .= "\n and E.C_ID =".$company;

if($lib<>'%'){
	$query .= "\n and E.E_LIBELLE like '$lib' ";
}

$tmp=explode ( "-",$dtdb); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2]; 
$tmp=explode ( "-",$dtfn); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];

$query .="\n and EH.EH_DATE_DEBUT <= '$year2-$month2-$day2' 
			 and EH.EH_DATE_FIN   >= '$year1-$month1-$day1'";
$query .="\n order by EH.EH_DATE_DEBUT, EH.EH_DEBUT";

$result=mysql_query($query);
$num=mysql_num_rows($result);
if ( $num > 0 ) {
   echo "<tr>
   		  <td>Type</td>";
   if($type_evenement == 'DPS')
         echo "<td>DPS</td>";
   echo " <td>Activité</td>
      	  <td>Statut</td>
      	  <td>Organisateur</td>
      	  <td>Renfort</td>
      	  <td>Lieu</td>
      	  <td>Début</td>
      	  <td>Fin</td>
      	  <td>Heure début</td>
      	  <td>Heure fin</td>
      	  <td>Inscrits</td>
      	  <td>Requis</td>
		  <td>Stat 1</td>
		  <td>Stat 2</td>
		  <td>Stat 3</td>";

    if(check_rights($_SESSION['id'], 29))
         echo "<td>Facture</td>";
   echo "</tr>
      ";

   $i=0;
   while ($row=@mysql_fetch_array($result)) {
       $TE_CODE=$row["TE_CODE"];
       $TE_LIBELLE=$row["TE_LIBELLE"];
       $E_LIBELLE=$row["E_LIBELLE"];
       $E_LIEU=$row["E_LIEU"];
       $E_CODE=$row["E_CODE"];
       $EH_DEBUT=$row["EH_DEBUT"];
       $EH_DATE_DEBUT=$row["EH_DATE_DEBUT"];
       $EH_DATE_FIN=$row["EH_DATE_FIN"];
       $EH_FIN=$row["EH_FIN"];
       $E_NB=$row["E_NB"];
       $S_ID=$row["S_ID"];
       $S_CODE=$row["S_CODE"];
       $E_CLOSED=$row["E_CLOSED"];
       $E_CANCELED=$row["E_CANCELED"];
       $E_OPEN_TO_EXT=$row["E_OPEN_TO_EXT"];
       $E_PARENT=$row["E_PARENT"];
       $E_NB1=$row["E_NB1"];
	   $E_NB2=$row["E_NB2"];
	   $E_NB3=$row["E_NB3"];
	   $TAV_ID=$row["TAV_ID"];

	  $S_DESCRIPTION=get_section_name("$S_ID");
	  if ( $nbsections <> 1) $organisateur=$S_CODE;
	  else $organisateur="";

	  if ( $E_CANCELED == 1 ) $state="événement annulé";
	  elseif ( $E_CLOSED == 1 ) $state="inscriptions fermées";
	  elseif (($nbsections <> 1) and ( $E_OPEN_TO_EXT == 0 )) $state="inscriptions interdites pour personnes extérieures";
	  else  $state="inscriptions ouvertes";
	    
	  $query2="select count(*) as NP from evenement_participation ep, evenement e
 			  where ( e.E_CODE=$E_CODE or e.E_PARENT=$E_CODE )
			  and ep.E_CODE=e.E_CODE";
      $result2=mysql_query($query2);
      $row2=mysql_fetch_array($result2);
      $NP=$row2["NP"];

	  if ( $E_PARENT <> '' ) $renfort="renfort";
	  else $renfort="";

	  $query2="select count(*) as NR from evenement where E_PARENT=".$E_CODE;
	  $result2=mysql_query($query2);
      $row2=mysql_fetch_array($result2);
      $NR=$row2["NR"];
      $b2="";
      if ( $NR > 0 ) $renfort=$NR;

      echo "<tr>
      	  <td>".$TE_CODE."</td>";
      if ( $type_evenement =='DPS') {
       	$query2="select TA_SHORT from type_agrement_valeur 
		   		 where TA_CODE = 'D' and TAV_ID=".$TAV_ID;
		$result2=mysql_query($query2);
        $row2=mysql_fetch_array($result2);
        $TA_SHORT=$row2["TA_SHORT"];
        echo "<td >".$TA_SHORT."</td>";
      }
      echo "<td>".$E_LIBELLE."</td> 
      	  <td>".$state."</td> 
		  <td>".$organisateur."</td>
      	  <td>".$renfort."</td>
      	  <td>".$E_LIEU."</td>
      	  <td>".$EH_DATE_DEBUT."</td>
      	  <td>".$EH_DATE_FIN."</td>
      	  <td>".$EH_DEBUT."</td>
		  <td>".$EH_FIN."</td>
		  <td>".$NP."</td>
          <td>".$E_NB."</td>
		  <td>".$E_NB1."</td>
		  <td>".$E_NB2."</td>
		  <td>".$E_NB3."</td>";

      if (check_rights($_SESSION['id'], 29)) {
         if (check_rights($_SESSION['id'], 29, "$S_ID")) 
		 	$myfact=get_etat_facturation($E_CODE, "txt");
         else 
		 	$myfact="";
       	 echo "<td>".$myfact."</td>";
       }
      echo "</tr>";
   }
   echo "</td></tr></table>";
}
else {
     echo "<p><b>Aucune activité ne correspond aux critères choisis</b>";
}
echo "</body>
</html>";

?>
