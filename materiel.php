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

$possibleorders= array('TM_USAGE', 'MA_NB','S_CODE','MA_MODELE','MA_NUMERO_SERIE','VP_OPERATIONNEL',
					   'MA_REV_DATE','MA_LIEU_STOCKAGE','MA_COMMENT','AFFECTED_TO','MA_ANNEE',
					   'MA_EXTERNE','V_ID','TM_LOT','MA_PARENT');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='TM_USAGE,TM_CODE';

if ( is_numeric($type)) {
   $query="select TM_USAGE from type_materiel where TM_ID='".$type."'";
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   $usage=$row["TM_USAGE"];
}
else {
   $usage=$type;
}

writehead();
?>

<STYLE type="text/css">
.categorie{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
.materiel{color:<?php echo $mydarkcolor; ?>; background-color:white; font-size:9pt;}
</STYLE>

<script type='text/javascript' src='popupBoxes.js'></script>
<script language="JavaScript">
function orderfilter(p1,p2,p3,p4,p5){
	 self.location.href="materiel.php?order="+p1+"&filter="+p2+"&type="+p3+"&subsections="+p4+"&old="+p5;
	 return true
}

function orderfilter2(p1,p2,p3,p4,p5){
 	 if (p4.checked) s = 1;
 	 else s = 0;
	 self.location.href="materiel.php?order="+p1+"&filter="+p2+"&type="+p3+"&subsections="+s+"&old="+p5;
	 return true
}

function orderfilter3(p1,p2,p3,p4,p5){
 	 if (p5.checked) s = 1;
 	 else s = 0;
	 self.location.href="materiel.php?order="+p1+"&filter="+p2+"&type="+p3+"&subsections="+p4+"&old="+s;
	 return true
}

function displaymanager(p1){
	 self.location.href="upd_materiel.php?mid="+p1;
	 return true
}

function bouton_redirect(cible) {
	 self.location.href = cible;
}

</script>
<?php

$querycnt="select count(*) as NB";
$query1="select m.TM_ID, tm.TM_CODE,tm.TM_DESCRIPTION,tm.TM_USAGE,
		 m.VP_ID, vp.VP_OPERATIONNEL,vp.VP_LIBELLE,
		 m.MA_ID, m.MA_NUMERO_SERIE, m.MA_COMMENT, m.MA_MODELE, cm.PICTURE_SMALL,m.MA_EXTERNE,
		 m.MA_ANNEE, m.MA_NB, m.S_ID, s.S_CODE ,m.MA_LIEU_STOCKAGE, m.AFFECTED_TO, m.V_ID,
		 tm.TM_LOT, MA_PARENT,
		 v.TV_CODE, v.V_MODELE, v.V_INDICATIF, v.V_IMMATRICULATION,
		 DATE_FORMAT(m.MA_REV_DATE, '%d-%m-%Y') as MA_REV_DATE";
         
$queryadd=" \n	from type_materiel tm, section s, vehicule_position vp, categorie_materiel cm, materiel m
		 left join vehicule v on v.V_ID = m.V_ID
		 where m.TM_ID=tm.TM_ID
		 and cm.TM_USAGE = tm.TM_USAGE
		 and m.VP_ID=vp.VP_ID
		 and s.S_ID=m.S_ID";	 
		 
if ( $type <> 'ALL' ) $queryadd .= "\n and (tm.TM_ID='".$type."' or tm.TM_USAGE='".$type."')";

// choix section
if ( $nbsections == 0 ) {
    if ( $subsections == 1 ) {
  	   $queryadd .= "\nand m.S_ID in (".get_family("$filter").")";
    }
    else {
  	   $queryadd .= "\nand m.S_ID =".$filter;
    }
}

if ( $old == 1 ) {
 	 $queryadd .="\nand vp.VP_OPERATIONNEL <0";
 	 $mylightcolor=$mygreycolor;
 	 $statusinfo = " réformés";
}
else {
	 $queryadd .="\nand vp.VP_OPERATIONNEL >=0";
	 $statusinfo = "";
}

$querycnt .= $queryadd;
$query1 .= $queryadd." order by ". $order;
if ( $order == 'TM_USAGE' ) $query1 .=",TM_CODE";

if ( $order == 'AFFECTED_TO' || $order == 'MA_EXTERNE' || $order == 'V_ID' || $order == 'TM_LOT' || $order == 'MA_PARENT') $query1 .=" desc";

if ( $type == 'ALL' ) {
 $query="select CM_DESCRIPTION as type, PICTURE_LARGE from categorie_materiel
 	where TM_USAGE='".$type."'";
}
elseif (is_numeric($type)){
    $query="select cm.TM_USAGE, concat(tm.TM_CODE,' - ',tm.TM_DESCRIPTION) as type, cm.PICTURE_LARGE
		from categorie_materiel cm, type_materiel tm
		where tm.TM_USAGE=cm.TM_USAGE
		and tm.TM_ID='".$type."'";
}
else {
     $query="select cm.TM_USAGE as type, cm.PICTURE_LARGE
		from categorie_materiel cm
		where cm.TM_USAGE='".$type."'";
}
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$cmt=$row["type"];
$picture=$row["PICTURE_LARGE"];
if ( $picture == '' ) $picture ='engine.png';

$resultcnt=mysql_query($querycnt);
$rowcnt=@mysql_fetch_array($resultcnt);
$number = $rowcnt[0];

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width =60><img src=images/$picture></td><td>
      <font size=4><b> ".ucfirst($cmt)." ".$statusinfo."</b></font><i>(".$number."  articles)</i></td></tr></table>";

echo "<table cellspacing=5 border=0 >";
echo "<tr height=40>";

//filtre section
if ($nbsections == 0 ) {
    echo "<td>".choice_section_order('materiel.php')."</td>";
	echo "<td><select id='filter' name='filter' 
		onchange=\"orderfilter('".$order."',document.getElementById('filter').value,'".$type."','".$subsections."','".$old."')\">";
	  display_children2(-1, 0, $filter, $nbmaxlevels, $sectionorder);
	  echo "</select></td> ";
	if ( get_children("$filter") <> '' ) {
	  if ($subsections == 1 ) $checked='checked';
	  else $checked='';
	  echo "<td><input type='checkbox' name='sub' $checked
	   onClick=\"orderfilter2('".$order."',document.getElementById('filter').value,'".$type."', this,'".$old."')\"/>
	   <font size=1>inclure les<br>sous sections</td>";
	}
}

//filtre ancien materiel
if ($old == 1 ) $checked='checked';
else $checked='';
echo "<td align=left><input type='checkbox' name='old' $checked
	   onClick=\"orderfilter3('".$order."','".$filter."','".$type."', '".$subsections."',this)\"/>
	   <font size=1>matériel<br>réformé</td>";

//filtre type
echo "<td><select id='type' name='type' 
	onchange=\"orderfilter('".$order."','".$filter."',document.getElementById('type').value,'".$subsections."','".$old."')\">";

if ( $type == 'ALL' ) $selected='selected';
else $selected='';
echo "<option value='ALL' $selected>tous types de matériel</option>";
$query2="select TM_ID, TM_CODE,TM_USAGE,TM_DESCRIPTION from type_materiel order by TM_USAGE, TM_CODE";
$result2=mysql_query($query2);
$prevUsage='';
while ($row=@mysql_fetch_array($result2)) {
 	  $TM_ID=$row["TM_ID"];
      $TM_CODE=$row["TM_CODE"];
      $TM_USAGE=$row["TM_USAGE"];
      $TM_DESCRIPTION=$row["TM_DESCRIPTION"];
      if ( $prevUsage <> $TM_USAGE ){
       	echo "<option class='categorie' value='".$TM_USAGE."'";
       	if ($TM_USAGE == $type ) echo " selected ";
        echo ">".$TM_USAGE."</option>\n";
      }
      $prevUsage=$TM_USAGE;
      echo "<option class='materiel' value='".$TM_ID."' title=\"".$TM_DESCRIPTION."\"";
      if ($TM_ID == $type ) echo " selected ";
      echo ">".$TM_CODE."</option>\n";
}
echo "</select></td> ";

if ( check_rights($_SESSION['id'], 17)) {
   echo "<td><input type='button' value='Ajouter' name='ajouter' onclick=\"bouton_redirect('ins_materiel.php?usage=$usage&type=$type');\"></td>";
}
echo "<td><img src='images/xls.jpg' id='StartExcel' height='24' border='0' alt='Excel' title='Excel' onclick=\"window.open('materiel_xls.php?filter=$filter&type=$type&subsections=$subsections&order=$order&old=$old')\" class='noprint' align='right' /></td>";

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


echo "<tr>";
if ( $type == 'ALL' ) {
echo "<td width=100 align=center class=TabHeader>
	<a href=materiel.php?order=TM_USAGE class=TabHeader>Catégorie</a>
	<td bgcolor=$mydarkcolor width=0></td>";
}
echo "<td width=130 align=center>
	<a href=materiel.php?order=TM_CODE class=TabHeader>Matériel</a>
	<a href=materiel.php?order=TM_LOT class=TabHeader>lot</a></td></td>
    <td bgcolor=$mydarkcolor width=0></td>"; 
echo "<td width=20 align=center>
	<a href=materiel.php?order=MA_NB class=TabHeader>Nb</a></td>
    <td bgcolor=$mydarkcolor width=0></td>";
if ( $nbsections == 0 ) {      	  
  echo "<td width=80 align=center>
		<a href=materiel.php?order=S_CODE class=TabHeader>Section</a></td>";
		echo "<td bgcolor=$mydarkcolor width=0></td>";
}     	  
echo "<td width=90 align=center><a href=materiel.php?order=MA_MODELE class=TabHeader>Modèle</a></td>
      <td bgcolor=$mydarkcolor width=0></td>
      <td width=90 align=center><a href=materiel.php?order=MA_NUMERO_SERIE class=TabHeader>N°Série</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=80 align=center><a href=materiel.php?order=VP_OPERATIONNEL class=TabHeader>Statut</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=50 align=center>
	<a href=materiel.php?order=MA_REV_DATE class=TabHeader title=\"Prochaine révision ou péremption\">
	Date Limite</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=100 align=center><a href=materiel.php?order=MA_LIEU_STOCKAGE class=TabHeader>Lieu stockage</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=90 align=center><a href=materiel.php?order=AFFECTED_TO class=TabHeader>Affecté à</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=130 align=center class=TabHeader><a href=materiel.php?order=V_ID class=TabHeader>Véhicule</a>
							  / <a href=materiel.php?order=MA_PARENT class=TabHeader>Lot</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=20 align=center><a href=materiel.php?order=MA_ANNEE class=TabHeader>ann.</a></td>";
if ( $nbsections == 0 )
echo "<td bgcolor=$mydarkcolor width=0></td>
     <td width=20 align=center><a href=materiel.php?order=MA_EXTERNE class=TabHeader
		title=\"Mis à disposition par $cisname\">MàD</a></td>";
echo "</tr>";

// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result1)) {
 		$TM_USAGE=$row["TM_USAGE"];
		$TM_CODE=$row["TM_CODE"];
		$TM_ID=$row["TM_ID"];
		$TM_DESCRIPTION=$row["TM_DESCRIPTION"];
		$TM_USAGE=$row["TM_USAGE"];
		$MA_ID=$row["MA_ID"];
		$TM_LOT=$row["TM_LOT"];
		$MA_PARENT=$row["MA_PARENT"];
		$MA_NUMERO_SERIE=$row["MA_NUMERO_SERIE"];
		$MA_COMMENT=$row["MA_COMMENT"];
		$MA_MODELE=$row["MA_MODELE"];
		$MA_ANNEE=$row["MA_ANNEE"]; if ( $MA_ANNEE == '') $MA_ANNEE = '?';
		$MA_NB=$row["MA_NB"];
		$MA_REV_DATE=$row["MA_REV_DATE"];
		$S_ID=$row["S_ID"];
		$VP_LIBELLE=$row["VP_LIBELLE"];
		$VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
		$PICTURE_SMALL=$row["PICTURE_SMALL"];
		$S_CODE=$row["S_CODE"];
		$MA_LIEU_STOCKAGE=$row["MA_LIEU_STOCKAGE"];
		$AFFECTED_TO=$row["AFFECTED_TO"];
		$MA_EXTERNE=$row["MA_EXTERNE"];
        $V_ID=$row["V_ID"];
        $TV_CODE=$row["TV_CODE"];
        $V_MODELE=$row["V_MODELE"];
        $V_IMMATRICULATION=$row["V_IMMATRICULATION"];
        $V_INDICATIF=$row["V_INDICATIF"];
		if ( $MA_EXTERNE == 1 ) $img3="<img src=images/YES.gif title=\"matériel mis à disposition par $cisname\">";
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
		if ( $MA_PARENT <> '' ) {
      		$queryp="select m.MA_ID, m.MA_MODELE, tm.TM_CODE, m.MA_NUMERO_SERIE 
						from materiel m, type_materiel tm 
						where m.TM_ID = tm.TM_ID
						and m.MA_ID=".$MA_PARENT;
       		$resultp=mysql_query($queryp);
			$rowp=@mysql_fetch_array($resultp);
			$_MA_ID=$rowp["MA_ID"];
			$_MA_MODELE=$rowp["MA_MODELE"];
			$_TM_CODE=$rowp["TM_CODE"];
			$_MA_NUMERO_SERIE=$rowp["MA_NUMERO_SERIE"]; 
       		$parent=$_TM_CODE." ".$_MA_MODELE." ".$_MA_NUMERO_SERIE;
      	}
		else $parent='';
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }
    
    if ( $VP_OPERATIONNEL == -1 ) $opcolor="black";
    else if ( $VP_OPERATIONNEL == 1) $opcolor=$red;      
	else if ( my_date_diff(getnow(),$MA_REV_DATE) < 0 ) {
	  	$opcolor=$orange;
	  	$VP_LIBELLE = "date dépassée";
	}
	else if ( $VP_OPERATIONNEL == 2) {
	  	$opcolor=$orange;
	} 
    else $opcolor=$green;
	
	if ( $TM_LOT == 1 ) $img1="<img src=images/YES.gif title='Lot de matériel'>";
    else $img1='';
	  
 
echo "<tr bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$mycolor'\" onclick=\"this.bgColor='#33FF00'; displaymanager($MA_ID)\" >";
if ( $type == 'ALL' ) {    
	echo "<td  align=left><img src=images/$PICTURE_SMALL height=16 border=0><font size=1> $TM_USAGE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}   
echo "    <td align=center><font color=$opcolor size=1><B>$TM_CODE</B></font> ".$img1."</td>
		  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$MA_NB</font></td>
		  <td bgcolor=$mydarkcolor width=0></td>";
if ( $nbsections == 0 ) {    
	echo "<td align=center><font size=1>$S_CODE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}
echo "	  <td align=center><font size=1>$MA_MODELE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$MA_NUMERO_SERIE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font color=$opcolor size=1><b>$VP_LIBELLE</b></font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$MA_REV_DATE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>".substr($MA_LIEU_STOCKAGE,0,15)."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
          <td align=center><font size=1><a href=upd_personnel.php?from=vehicules&pompier=".$AFFECTED_TO.">".$owner."</a></font></td>
          <td bgcolor=$mydarkcolor width=0></td>
          <td align=center><font size=1>
		  	<a href=upd_vehicule.php?vid=".$V_ID." title=\"".$TV_CODE." ".$V_MODELE." ".$V_INDICATIF." ".$V_IMMATRICULATION."\">
		  	".$V_MODELE." ".$V_INDICATIF."</a>
			<a href=upd_materiel.php?mid=".$MA_PARENT.">".$parent."</a>
			</font></td>
          <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$MA_ANNEE</font></td>";
if ( $nbsections == 0 )
echo "    <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>".$img3."</font></td>";
echo "</tr>";
      
}
echo "</table>";
echo "</td></tr></table>";

?>
