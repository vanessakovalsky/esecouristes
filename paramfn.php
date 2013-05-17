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
get_session_parameters();
$possibleorders= array('TE_CODE','TP_NUM','TP_LIBELLE','INSTRUCTOR','DESCRIPTION','DESCRIPTION2');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='TE_CODE';
writehead();
?>

<script language="JavaScript">
function orderfilter(p1,p2){
	 self.location.href="paramfn.php?order="+p1+"&type_evenement="+p2;
	 return true
}

function displaymanager(p1,p2){
	 url="paramfn_edit.php?action=update&TP_ID="+p1+"&type_evenement="+p2;
	 self.location.href=url;
	 return true
}

function bouton_redirect(cible) {
	 self.location.href = cible;
}

</script>
<?php
echo "<body>";

$query1="select tp.TE_CODE, tp.TP_ID, tp.TP_LIBELLE, tp.TP_NUM, 
		tp.PS_ID, tp.PS_ID2, tp.INSTRUCTOR, p.TYPE, p.DESCRIPTION, p2.TYPE TYPE2, p2.DESCRIPTION DESCRIPTION2, te.TE_LIBELLE
	  	from type_participation tp
	  	left join poste p on p.PS_ID=tp.PS_ID
		left join poste p2 on p2.PS_ID=tp.PS_ID2
		join type_evenement te on te.TE_CODE=tp.TE_CODE";

if ( $type_evenement <> 'ALL' ) $query1 .= "\nwhere tp.TE_CODE='".$type_evenement."'";
$query1 .="\norder by ". $order;
if ( $order == 'DESCRIPTION' or $order == 'DESCRIPTION2' or $order == 'INSTRUCTOR') 
$query1 .= " desc";

$result1=mysql_query($query1);
$number=mysql_num_rows($result1);

echo "<div align=center><font size=4><b>Paramétrage des Fonctions</b></font><i> (".$number." trouvées)</i>";
echo "<p><table cellspacing=0 border=0 >";
echo "<tr>";
echo "<td><font size=1>fonctions pour événements de type:</font></td>
	  <td><select id='type_evenement' name='type_evenement' 
	  	onchange=\"orderfilter('".$order."',document.getElementById('type_evenement').value)\">
	  <option value='ALL'>tous types</option>";

$query2="select distinct TE_CODE, TE_LIBELLE from type_evenement";
$result2=mysql_query($query2);
while ($row=@mysql_fetch_array($result2)) {
      $TE_CODE=$row["TE_CODE"];
      $TE_LIBELLE=$row["TE_LIBELLE"];
      echo "<option value='".$TE_CODE."'";
      if ($TE_CODE == $type_evenement ) echo " selected ";
      echo ">".$TE_LIBELLE."</option>\n";
}
echo "</select></td> ";
if ( check_rights($_SESSION['id'], 18) )
   	echo "<td> <input type='button' value='Ajouter' name='ajouter' 
	   onclick=\"bouton_redirect('paramfn_edit.php?type=".$type_evenement."');\"></td>";
   
echo "</tr></table>";

echo "</tr><tr><td colspan=3>";
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

echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr class=TabHeader>
      	  <td width=30 align=center>
			<a href=paramfn.php?order=TE_CODE class=TabHeader>Type</a></td>
      	  <td width=0></td>
      	  <td width=20 align=center>
			<a href=paramfn.php?order=TP_NUM class=TabHeader>Ordre</a></td>
      	  <td width=0></td>
      	  <td width=200 align=center>
			<a href=paramfn.php?order=TP_LIBELLE class=TabHeader>Fonction</a></td>
		  <td width=0></td>
		  <td width=30 align=center>
			<a href=paramfn.php?order=INSTRUCTOR class=TabHeader>Moniteur.</a></td>";
if ( $competences == 1 ) {
    echo "<td width=0></td>
           <td width=200 align=center>
			<a href=paramfn.php?order=DESCRIPTION class=TabHeader>Compétence requise</a></td>";
	echo "<td width=0></td>
           <td width=200 align=center>
			<a href=paramfn.php?order=DESCRIPTION2 class=TabHeader>Ou</a></td>";
}
echo "</tr>";

// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result1)) {
      $PS_ID=$row["PS_ID"];
	  $PS_ID2=$row["PS_ID2"];
	  $INSTRUCTOR=$row["INSTRUCTOR"];
      $TYPE=$row["TYPE"];
      $DESCRIPTION=strip_tags($row["DESCRIPTION"]);
      $TYPE2=$row["TYPE2"];
      $DESCRIPTION2=strip_tags($row["DESCRIPTION2"]);
      $TE_CODE=$row["TE_CODE"];
      $TE_LIBELLE=$row["TE_LIBELLE"];
      $TP_ID=$row["TP_ID"];
      $TP_LIBELLE=$row["TP_LIBELLE"];
      $TP_NUM=$row["TP_NUM"];
      
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      
if ( $INSTRUCTOR == 1 ) $ins="<img src=images/YES.gif title='Instructeur ou moniteur'>";
else $ins="";
echo "<tr bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$mycolor'\" onclick=\"this.bgColor='#33FF00'; displaymanager('".$TP_ID."','".$type_evenement."')\" >
      	  <td align=center>
				<img src=images/".$TE_CODE."small.gif height=16 title=\"".$TE_LIBELLE."\"></font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center>$TP_NUM</td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=left>$TP_LIBELLE</td>
		  <td bgcolor=$mydarkcolor width=0></td>
		  <td align=center>$ins</td>";
if ( $competences == 1 ) {
    echo "<td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>".$TYPE." - ".$DESCRIPTION."</font></td>";
    echo "<td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>".$TYPE2." - ".$DESCRIPTION2."</font></td>";
}
echo "</tr>";
      
}

// ===============================================
// le bas du tableau
// ===============================================
echo "</table>";  
echo "</td></tr></table>";  
?>
