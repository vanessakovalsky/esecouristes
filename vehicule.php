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
check_all(42);
get_session_parameters();

$possibleorders= array('TV_CODE','V_IMMATRICULATION','V_INDICATIF','V_MODELE','V_COMMENT','VP_OPERATIONNEL',
'V_ASS_DATE','V_CT_DATE','V_KM','V_FLAG1','V_FLAG2','AFFECTED_TO','AFFECTED_TO','S_CODE','V_ANNEE');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='TV_CODE';

writehead();
?>

<STYLE type="text/css">
.categorie{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
.materiel{color:<?php echo $mydarkcolor; ?>; background-color:white; font-size:9pt;}
</STYLE>

<script type='text/javascript' src='popupBoxes.js'></script>
<script language="JavaScript">
function orderfilter(p1,p2,p3,p4,p5){
	 self.location.href="vehicule.php?order="+p1+"&filter="+p2+"&filter2="+p3+"&subsections="+p4+"&includeold="+p5;
	 return true
}

function orderfilter2(p1,p2,p3,p4,p5){
 	 if (p4.checked) s = 1;
 	 else s = 0;
	 self.location.href="vehicule.php?order="+p1+"&filter="+p2+"&filter2="+p3+"&subsections="+s+"&old="+p5;
	 return true
}
function orderfilter3(p1,p2,p3,p4,p5){
 	 if (p5.checked) s = 1;
 	 else s = 0;
	 self.location.href="vehicule.php?order="+p1+"&filter="+p2+"&filter2="+p3+"&subsections="+p4+"&old="+s;
	 return true
}
function displaymanager(p1){
	 self.location.href="upd_vehicule.php?vid="+p1;
	 return true
}

function bouton_redirect(cible) {
	 self.location.href = cible;
}

</script>
<?php

$querycnt="select count(*) as NB";

$query1="select distinct v.V_ID ,v.VP_ID, v.TV_CODE, v.V_MODELE, v.EQ_ID, vp.VP_LIBELLE, 
		tv.TV_LIBELLE, vp.VP_OPERATIONNEL, v.V_IMMATRICULATION, v.V_COMMENT, v.V_KM, 
		v.V_ANNEE, tv.TV_USAGE, s.S_ID, s.S_CODE, v.V_INDICATIF,
		DATE_FORMAT(v.V_ASS_DATE, '%d-%m-%Y') as V_ASS_DATE,
		DATE_FORMAT(v.V_CT_DATE, '%d-%m-%Y') as V_CT_DATE,
		DATE_FORMAT(v.V_REV_DATE, '%d-%m-%Y') as V_REV_DATE,
		v.V_FLAG1, v.V_FLAG2, v.AFFECTED_TO, v.V_EXTERNE";
		
$queryadd=" from vehicule v, type_vehicule tv, vehicule_position vp, section s
		where v.TV_CODE=tv.TV_CODE
		and s.S_ID=v.S_ID
		and vp.VP_ID=v.VP_ID";

if ( $filter2 <> 'ALL' ) $queryadd .= "\nand (tv.TV_USAGE='".$filter2."' or tv.TV_CODE='".$filter2."')";

$title="Véhicules et engins";
if ( $old == 1 ) {
 	 $queryadd .="\nand vp.VP_OPERATIONNEL <0";
 	 $mylightcolor=$mygreycolor;
 	 $title .= " réformés";
}
else {
	 $queryadd .="\nand vp.VP_OPERATIONNEL >=0";
}

// choix section
if ( $nbsections == 0 ) {
    if ( $subsections == 1 ) {
  	   $queryadd .= "\nand v.S_ID in (".get_family("$filter").")";
    }
    else {
  	   $queryadd .= "\nand v.S_ID =".$filter;
    }
}

$querycnt .= $queryadd;

$query1 .= $queryadd." \norder by ". $order;
if ( $order == 'TV_USAGE' || $order == 'V_FLAG1' || $order == 'V_FLAG2' || $order == 'AFFECTED_TO' || $order == 'V_EXTERNE' || $order == 'V_ANNEE') $query1 .=" desc";

$resultcnt=mysql_query($querycnt);
$rowcnt=@mysql_fetch_array($resultcnt);
$number = $rowcnt[0];

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/ambulance.png></td><td>
      <font size=4><b> $title</b></font><i> ($number véhicules)</i></td></tr></table>";

echo "<table cellspacing=5 border=0 >";
echo "<tr height=40>";

//filtre section
if ($nbsections == 0 ) {
    echo "<td>".choice_section_order('vehicule.php')."</td>";
	echo "<td> <select id='filter' name='filter' 
		onchange=\"orderfilter('".$order."',document.getElementById('filter').value,'".$filter2."','".$subsections."','".$old."')\">";
	  display_children2(-1, 0, $filter, $nbmaxlevels, $sectionorder);
	  echo "</select></td> ";
	if ( get_children("$filter") <> '' ) {
	  if ($subsections == 1 ) $checked='checked';
	  else $checked='';
	  echo "<td align=left><input type='checkbox' name='sub' $checked
	   onClick=\"orderfilter2('".$order."',document.getElementById('filter').value,'".$filter2."', this, '".$old."')\"/>
	   <font size=1>inclure les<br>sous sections</td>";
	}
}

//filtre anciens vehicules

if ($old == 1 ) $checked='checked';
else $checked='';
echo "<td align=left><input type='checkbox' name='old' $checked
	   onClick=\"orderfilter3('".$order."','".$filter."','".$filter2."', '".$subsections."',this)\"/>
	   <font size=1>véhicules<br>réformés</td>";



//filtre type
echo "<td> <select id='filter2' name='filter2' 
	onchange=\"orderfilter('".$order."','".$filter."',document.getElementById('filter2').value,'".$subsections."','".$old."')\">
	  <option value='ALL'>tous types</option>";

$query2="select distinct TV_CODE, TV_USAGE, TV_LIBELLE from type_vehicule 
		 order by TV_USAGE, TV_CODE";
$prevUsage='';
$result2=mysql_query($query2);
while ($row=@mysql_fetch_array($result2)) {
      $TV_USAGE=$row["TV_USAGE"];
      $TV_CODE=$row["TV_CODE"];
      $TV_LIBELLE=$row["TV_LIBELLE"];
      if ( $prevUsage <> $TV_USAGE ){
       	echo "<option class='categorie' value='".$TV_USAGE."'";
       	if ($TV_USAGE == $filter2 ) echo " selected ";
        echo ">".$TV_USAGE."</option>\n";
      }
      $prevUsage=$TV_USAGE;
      echo "<option class='materiel' value='".$TV_CODE."' title=\"".$TV_LIBELLE."\"";
      if ($TV_CODE == $filter2 ) echo " selected ";
      echo ">".$TV_CODE."</option>\n";
}
echo "</select></td> ";

if ( check_rights($_SESSION['id'], 17)) {
   echo "<td> <input type='button' value='Ajouter' name='ajouter' onclick=\"bouton_redirect('ins_vehicule.php');\"></td>";
}

echo "<td><img src='images/xls.jpg' id='StartExcel' height='24' border='0' alt='Excel' title='Excel' onclick=\"window.open('vehicule_xls.php?filter=$filter&filter2=$filter2&subsections=$subsections&order=$order&old=$old')\" class='noprint' align='right' /></td>";

echo "</tr><tr><td colspan=4>";
// ====================================
// pagination
// ====================================
require_once('paginator.class.php');
$pages = new Paginator;  
$pages->items_total = $number;  
$pages->mid_range = 9;  
$pages->paginate();  
if ( $number > 10 ) {
	echo $pages->display_pages();
	echo $pages->display_jump_menu(); 
	echo $pages->display_items_per_page(); 
	$query1 .= $pages->limit;
}

$result1=mysql_query($query1);
$numberrows=mysql_num_rows($result1);

echo "</td></tr></table>";



echo "<table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr>
    <td width=40 align=center><a href=vehicule.php?order=TV_CODE class=TabHeader>Type</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=80 align=center><a href=vehicule.php?order=V_IMMATRICULATION class=TabHeader>Immat.</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=100 align=center><a href=vehicule.php?order=V_INDICATIF class=TabHeader>Indicatif</a></td>
    <td bgcolor=$mydarkcolor width=0></td>";
      	  
if ( $nbsections == 0 ) {      	  
  echo "<td width=100 align=center>
		<a href=vehicule.php?order=S_CODE class=TabHeader>Section</a></td>
      	<td bgcolor=$mydarkcolor width=0></td>";
}     	  
echo "<td width=100 align=center><a href=vehicule.php?order=V_MODELE class=TabHeader>Modèle</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=160 align=center><a href=vehicule.php?order=V_COMMENT class=TabHeader>Commentaire</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=80 align=center><a href=vehicule.php?order=VP_OPERATIONNEL class=TabHeader>Statut</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=30 align=center><a href=vehicule.php?order=V_ANNEE class=TabHeader>Année</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=60 align=center><a href=vehicule.php?order=V_ASS_DATE class=TabHeader>Fin assurance</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=60 align=center><a href=vehicule.php?order=V_CT_DATE class=TabHeader>Prochain CT</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=20 align=center><a href=vehicule.php?order=V_KM class=TabHeader>km</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=20 align=center><a href=vehicule.php?order=V_FLAG1 class=TabHeader
		title=\"Véhicule équipé pour rouler sur la neige\">Neige</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=20 align=center><a href=vehicule.php?order=V_FLAG2 class=TabHeader
		title=\"Véhicule équipé de climatisation\">Clim</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=100 align=center><a href=vehicule.php?order=AFFECTED_TO class=TabHeader>Affecté à</a></td>";
if ( $materiel == 1 )	
    echo "<td bgcolor=$mydarkcolor width=0></td>
		  <td width=15 align=center class=TabHeader>Mat.</a></td>";

if ( $nbsections == 0 ) 
echo "<td bgcolor=$mydarkcolor width=0></td>
    <td width=20 align=center><a href=vehicule.php?order=V_EXTERNE class=TabHeader
		title=\"Mis à disposition par $cisname\">MàD</a></td>";
echo "</tr>";

// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result1)) {
      $TV_CODE=$row["TV_CODE"];
      $V_COMMENT=$row["V_COMMENT"];
      $V_ID=$row["V_ID"];
      $VP_LIBELLE=$row["VP_LIBELLE"];
      $TV_LIBELLE=$row["TV_LIBELLE"];
      $VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
      $V_IMMATRICULATION=$row["V_IMMATRICULATION"];
      $V_INDICATIF=$row["V_INDICATIF"];
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
      $S_ID=$row["S_ID"];
      $S_CODE=$row["S_CODE"]; 
      $V_FLAG1=$row["V_FLAG1"];
      $V_FLAG2=$row["V_FLAG2"];
	  $AFFECTED_TO=$row["AFFECTED_TO"];
	  $V_EXTERNE=$row["V_EXTERNE"];
	  $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      if ( $V_FLAG1 == 1 ) $img1="<img src=images/YES.gif title='ce véhicule est équipé pour rouler sur la neige'>";
      else $img1='';
      if ( $V_FLAG2 == 1 ) $img2="<img src=images/YES.gif title='ce véhicule est climatisé'>";
      else $img2='';

	  if ( $V_EXTERNE == 1 ) $img3="<img src=images/YES.gif title=\"véhicule mis à disposition par $cisname\">";
      else $img3='';     
      if ( $AFFECTED_TO <> '' ) {
      	$queryp="select P_NOM, P_PRENOM, P_OLD_MEMBER from pompier where P_ID=".$AFFECTED_TO;
       	$resultp=mysql_query($queryp);
		$rowp=@mysql_fetch_array($resultp);
		$P_NOM=$rowp["P_NOM"];
		$P_PRENOM=$rowp["P_PRENOM"];
		$P_OLD_MEMBER=$rowp["P_OLD_MEMBER"];    	
       	$owner=strtoupper(substr($P_PRENOM,0,1).".".$P_NOM);
       	if ( $P_OLD_MEMBER == 1 ) $owner="<font color=black title='ancien membre'><b>".$owner."</b><font>";
      }
      else $owner='';
      
      if ( $VP_OPERATIONNEL == -1 ) $opcolor="black";
      else if ( $VP_OPERATIONNEL == 1) $opcolor=$red;      
	  else if ( my_date_diff(getnow(),$V_ASS_DATE) < 0 ) {
	  		$opcolor=$red;
	  		$VP_LIBELLE = "assurance périmée";
	  }
	  else if ( my_date_diff(getnow(),$V_CT_DATE) < 0 ) {
	  		$opcolor=$red;
	  		$VP_LIBELLE = "CT périmé";	  
	  }
	  else if ( $VP_OPERATIONNEL == 2) {
	  	$opcolor=$orange;
	  }
	  else if (( my_date_diff(getnow(),$V_REV_DATE) < 0 ) and ( $VP_OPERATIONNEL <> 1)) {
	  	$opcolor=$orange;
		$VP_LIBELLE = "révision à faire";
	  }  
      else $opcolor=$green;
      
      // matériel embarqué
      $query2="select count(*) as NB from materiel where V_ID=".$V_ID;
	  $result2=mysql_query($query2);
	  $row2=@mysql_fetch_array($result2);
	  if ( $row2["NB"] > 0 ) $mat="<img src=images/smallengine.png title='matériel embarqué: ".$row2["NB"]." éléments'><font size=1>".$row2["NB"]."</font>";
	  else $mat="";
      
echo "<tr bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$mycolor'\" onclick=\"this.bgColor='#33FF00'; displaymanager($V_ID)\" >
      	  <td align=center><font color=$opcolor><B>$TV_CODE</B></font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center>$V_IMMATRICULATION</td>
      	  <td bgcolor=$mydarkcolor width=0></td>
		  <td align=center>$V_INDICATIF</td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
if ( $nbsections == 0 ) {    
	echo "<td align=center><font size=1>$S_CODE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}
echo "	  <td align=center><font size=1>$V_MODELE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$V_COMMENT</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font color=$opcolor size=1><b>$VP_LIBELLE</b></font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$V_ANNEE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$V_ASS_DATE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$V_CT_DATE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$V_KM</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$img1</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$img2</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
		  <td align=center><font size=1><a href=upd_personnel.php?from=vehicules&pompier=".$AFFECTED_TO.">".$owner."</a></font></td>";
if ( $materiel == 1 )	
    echo "<td bgcolor=$mydarkcolor width=0></td>
		  <td align=left>".$mat."</a></td>";		  
		  
if ( $nbsections == 0 )
	echo "<td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>".$img3."</font></td>";
	echo "</tr>";
      
}
echo "</table>";
echo "</td></tr></table>";


?>
