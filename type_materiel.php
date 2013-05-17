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
check_all(18);
get_session_parameters();

$possibleorders= array('TM_USAGE','TM_CODE','TM_DESCRIPTION','TM_LOT','TM_CONTROLE');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='TM_USAGE';
writehead();
?>

<script language="JavaScript">
function orderfilter(p1,p2){
	 self.location.href="type_materiel.php?order="+p1+"&catmateriel="+p2;
	 return true
}
function displaymanager(p1){
	 self.location.href="upd_type_materiel.php?id="+p1;
	 return true
}

function bouton_redirect(cible) {
	 self.location.href = cible;
}

</script>
<?php


$query="select CM_DESCRIPTION,PICTURE_LARGE from categorie_materiel
		where TM_USAGE='".$catmateriel."'";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$cmt=$row["CM_DESCRIPTION"];
$picture=$row["PICTURE_LARGE"];

$query1="select tm.TM_ID,tm.TM_CODE,tm.TM_DESCRIPTION,tm.TM_USAGE,tm.TM_LOT,tm.TM_CONTROLE,cm.PICTURE_SMALL
        from type_materiel tm, categorie_materiel cm
		where cm.TM_USAGE=tm.TM_USAGE ";
if ( $catmateriel <> 'ALL' ) $query1 .= "\nand tm.TM_USAGE='".$catmateriel."'";
$query1 .="\norder by tm.". $order;
if ( $order == 'TM_LOT' ) $query1 .=" desc";
if ( $order == 'TM_CONTROLE' ) $query1 .=" desc";

$result1=mysql_query($query1);
$number=mysql_num_rows($result1);

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/$picture></td><td>
      <font size=4><b> Catégorie: ".$cmt."</b></font></td></tr></table>";

echo "<p><table cellspacing=0 border=0 >";
echo "<tr>";

//filtre type
echo "<td align=center><select id='usage' name='usage' 
	onchange=\"orderfilter('".$order."',document.getElementById('usage').value)\">";

$query2="select TM_USAGE,CM_DESCRIPTION from categorie_materiel order by TM_USAGE asc";
$result2=mysql_query($query2);
while ($row=@mysql_fetch_array($result2)) {
      $TM_USAGE=$row["TM_USAGE"];
      $CM_DESCRIPTION=$row["CM_DESCRIPTION"];
      echo "<option value='".$TM_USAGE."'";
      if ($TM_USAGE == $catmateriel ) echo " selected ";
      echo ">".$TM_USAGE." - ".$CM_DESCRIPTION."</option>\n";
}
echo "</select></td> ";
echo "<td align=center><i>$number type(s)</i></td>";
echo "<td><input type='button' value='Ajouter' name='ajouter' onclick=\"bouton_redirect('ins_type_materiel.php?catmateriel=$catmateriel');\"></td>
</tr><tr><td colspan=3>";


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

echo "</td></tr></table>";

if ( $number > 0 ) {
echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr>
    <td width=100 align=center><a href=type_materiel.php?order=TM_USAGE class=TabHeader>Catégorie</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=20 align=center><a href=type_materiel.php?order=TM_LOT class=TabHeader>Lot</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
	<td width=20 align=center><a href=type_materiel.php?order=TM_CONTROLE class=TabHeader>Contrôle</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=200 align=center><a href=type_materiel.php?order=TM_CODE class=TabHeader>Code</a></td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=300 align=center><a href=type_materiel.php?order=TM_DESCRIPTION class=TabHeader>Description</a></td>
</tr>";

// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result1)) {
 		$TM_ID=$row["TM_ID"];
		$TM_LOT=$row["TM_LOT"];
		$TM_CONTROLE=$row["TM_CONTROLE"];
		$TM_CODE=$row["TM_CODE"];
		$PICTURE_SMALL=$row["PICTURE_SMALL"];
		$TM_DESCRIPTION=$row["TM_DESCRIPTION"];
		$TM_USAGE=$row["TM_USAGE"];
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }
	  if ( $TM_LOT == 1 ) $img1="<img src=images/YES.gif title='Lot de matériel'>";
      else $img1='';
	  if ( $TM_CONTROLE == 1 ) $img2="<img src=images/YES.gif title='Contrôle du matériel'>";
      else $img2='';
      
echo "<tr bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$mycolor'\" 
onclick=\"this.bgColor='#33FF00'; displaymanager('$TM_ID')\" >
      	  <td width=100 align=left><img src=images/$PICTURE_SMALL height=16 border=0> <B>$TM_USAGE</B></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
		  <td width=20 align=center>".$img1."</td>
		  <td bgcolor=$mydarkcolor width=0></td>
		  <td width=20 align=center>".$img2."</td>
		  <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=200 align=center>$TM_CODE</td>
		  <td bgcolor=$mydarkcolor width=0></td>
		  <td width=300 align=center>$TM_DESCRIPTION</td>
      </tr>";
      
}
echo "</table>";
echo "</td></tr></table>";
}
?>
