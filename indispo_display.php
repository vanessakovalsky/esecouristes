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
check_all(11);


$code=intval($_GET["code"]);

writehead();
?>

<script>
function bouton_redirect(cible, action) {
    if ( confirm ("Attention : vous allez "+action+" cette demande de congés. Voulez vous continuer ?" )) {
	 self.location.href = cible;
    }
}
</script>
</head>
<?php
$query="select p.P_ID, p.P_NOM, p.P_PRENOM, DATE_FORMAT(i.I_DEBUT, '%d-%m-%Y') as I_DEBUT, DATE_FORMAT(i.I_FIN, '%d-%m-%Y') as I_FIN, i.TI_CODE, p.P_STATUT,
        ti.TI_LIBELLE, i.I_COMMENT, ist.I_STATUS_LIBELLE, i.I_STATUS, date_format(i.IH_DEBUT,'%H:%i') IH_DEBUT, date_format(i.IH_FIN,'%H:%i') IH_FIN, i.I_JOUR_COMPLET
        from pompier p, indisponibilite i, type_indisponibilite ti, indisponibilite_status ist
        where i.I_CODE = ".$code."
        and p.P_ID=i.P_ID
        and i.TI_CODE=ti.TI_CODE
		and i.I_STATUS=ist.I_STATUS";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
	   $P_ID=$row["P_ID"];
	   $section=get_section_of($P_ID);
       $P_NOM=$row["P_NOM"];
       $P_PRENOM=$row["P_PRENOM"];
       $I_DEBUT=$row["I_DEBUT"];
       $I_FIN=$row["I_FIN"];
       $TI_CODE=$row["TI_CODE"];
       $P_STATUT=$row["P_STATUT"];
       $TI_LIBELLE=$row["TI_LIBELLE"];
       $I_COMMENT=$row["I_COMMENT"];
       $I_STATUS=$row["I_STATUS"];
       $IH_DEBUT=$row["IH_DEBUT"];
       $IH_FIN=$row["IH_FIN"];
       $I_JOUR_COMPLET=$row["I_JOUR_COMPLET"];
       $I_STATUS_LIBELLE=$row["I_STATUS_LIBELLE"];	
echo "<body>";

$tmp=explode ( "-",$I_DEBUT); $year1=$tmp[2]; $month1=$tmp[1]; $day1=$tmp[0];
$date1=mktime(0,0,0,$month1,$day1,$year1);

$tmp=explode ( "-",$I_FIN); $year2=$tmp[2]; $month2=$tmp[1]; $day2=$tmp[0];
$date2=mktime(0,0,0,$month2,$day2,$year2);

//=====================================================================
// debut tableau
//=====================================================================

echo "<p><font size=3><b>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</b></font><br><hr>";
if ( $I_STATUS == 'VAL' ) $mytxtcolor='green';
if (( $I_STATUS == 'ANN' ) or ( $I_STATUS == 'REF' )) $mytxtcolor='red';
if (( $I_STATUS == 'ATT' )or ( $I_STATUS == 'PRE' ))  $mytxtcolor='orange';
echo "<p><table cellspacing=0 border=0 width=350>";
echo "<tr><td width=40%><b>Demande pour: </b></td>
   	 <td> ".$TI_LIBELLE."</font></td></tr>";
echo "<tr><td width=40%><b>Statut demande: </b></td>
   	 <td><b> ".$I_STATUS_LIBELLE."</b></td></tr>";


//compteur de jours d'absence
$abs=my_date_diff($I_DEBUT,$I_FIN) + 1;


if ( $I_JOUR_COMPLET == 0 ) {
    if ( $abs == 1 ) {
      	if ( substr($IH_FIN,0,1) == '0' ) $fin = substr($IH_FIN,1,1);
      	else  $fin = substr($IH_FIN,0,2);
      	if ( substr($IH_DEBUT,0,1) == '0' ) $debut = substr($IH_DEBUT,1,1);
      	else  $debut = substr($IH_DEBUT,0,2);      		 	
      	$abs = $fin - $debut;
      	$abs .= ' heure(s)';
    }
    else $abs .= ' jour(s)';
    $cmtdeb="de ".$IH_DEBUT;
    $cmtfin=" à ".$IH_FIN;
}
else {
 	$abs .= ' jour(s)';
	$cmtdeb='';
	$cmtfin='';
}
   	 
if ( $I_DEBUT == $I_FIN ) {
 	echo "<tr><td width=40%><b>Jour: </b></td>
   	 <td> ".date_fran($month1, $day1 ,$year1)." ".moislettres($month1)." ".$year1."<br>".$cmtdeb.$cmtfin."</td></tr>";
}
else {
	echo "<tr><td width=40%><b>premier jour: </b></td>
   	 <td> ".date_fran($month1, $day1 ,$year1)." ".moislettres($month1)." ".$year1." ".$cmtdeb."</td></tr>";
	echo "<tr><td width=40%><b>dernier jour: </b></td>
   	 <td> ".date_fran($month2, $day2 ,$year2)." ".moislettres($month2)." ".$year2." ".$cmtfin."</td></tr>";   	
}
   	
//=====================================================================
// soit nb jours d'absence
//=====================================================================

echo "<tr><td width=40%><b>Durée absence:</b></td>
   	 <td> ".$abs."</td></tr>";   	

//=====================================================================
// décompte CP
//=====================================================================
$abs=0;
if (( $TI_CODE == 'CP' ) and ($gardes == 1) and ($P_STATUT== 'SPP')) {
   
   // nb de jours de travail qui sautent
   if ( $TI_CODE == 'CP' ) {
    $num1=date("z",mktime( 0,0,0,$month1,$day1,$year1));
   	$num2=date("z",mktime( 0,0,0,$month2,$day2,$year2));
   	$i=0;
   	for ( $mydate=$num1; $mydate <= $num2 ; $mydate++ ) {
       	      if (should_be_working($P_ID, $year1, $month1, $day1 + $i)) {
    	           $abs++;
              }	
              $i++;
        }
   }	
   if ( $I_STATUS == 'ATT' ) {
       echo "<tr><td width=40%><b>Consommation:</b></td>
   	    <td> ".$abs."</td></tr>";
   }		    	     	
}

echo "</table>";
//=====================================================================
// absences déjà enregistrées
//=====================================================================
if ( $P_STATUT == "SPP" ) {
   $query="select distinct p.P_ID, p.P_NOM, p.P_PRENOM, p.P_GRADE,p.P_SECTION, i.I_DEBUT, i.I_FIN, ti.TI_LIBELLE
        from pompier p, indisponibilite i, type_indisponibilite ti
        where p.P_ID=i.P_ID
        and p.P_ID <>".$person."
	and i.TI_CODE=ti.TI_CODE
	and p.P_STATUT = 'SPP'
	and i.I_DEBUT <= '$year2-$month2-$day2' 
	and i.I_FIN   >= '$year1-$month1-$day1'";

   $result=mysql_query($query);
   $num=mysql_num_rows($result);
   if ( $num > 0 ) {
      echo "<p><table cellspacing=0 border=0 width=350>";
      echo "<tr><td width=15%>".$warning_pic."</td>
             <td><b>Attention: </b>déjà ".$num." SPP absent(s)</td>";

      while ($row=mysql_fetch_array($result)) {
       	    $P_ID=$row["P_ID"];
       	    $P_NOM=$row["P_NOM"];
       	    $P_PRENOM=$row["P_PRENOM"];
       	    $P_GRADE=$row["P_GRADE"];
       	    $I_DEBUT=$row["I_DEBUT"];
       	    $I_FIN=$row["I_FIN"];
       	    $TI_LIBELLE=$row["TI_LIBELLE"];
      	    echo "<tr><td></td><td>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."<br>".$TI_LIBELLE." du ".$I_DEBUT." au ".$I_FIN."</td></tr>";
     }
     echo "</table>";
  }
  else {
      echo "<p>Aucun pros absent sur la période<br>";
  }
  echo "</font>";
}
//=====================================================================
// boutons
//=====================================================================
echo "<hr><div align=center><input type=submit value='fermer' onclick='window.close();'>";

if (( $TI_CODE == 'CP' || $TI_CODE == 'RTT' ) and (check_rights($_SESSION['id'], 13, $section))) {
   if ( $I_STATUS == 'ATT' ) {
      echo " <input type=submit value='valider' 
	  	onclick=\"bouton_redirect('indispo_status.php?code=$code&action=valider','valider');\">";
      echo " <input type=submit value='refuser' 
	  	onclick=\"bouton_redirect('indispo_status.php?code=$code&action=refuser','refuser');\">";
   }
   else {
      	echo " <input type=submit value='supprimer' 
		  onclick=\"bouton_redirect('indispo_status.php?code=$code&action=supprimer','supprimer');\">";
   }
}
if ( check_rights($_SESSION['id'], 12, $section)) {
   if (( $I_STATUS <> 'VAL' and $I_STATUS <> 'REF') or ( $TI_CODE <> 'CP' and $TI_CODE <> 'RTT')) {
       echo " <input type=submit value='supprimer' onclick=\"bouton_redirect('indispo_status.php?code=$code&action=supprimer','supprimer');\">";
   }
}

?>
