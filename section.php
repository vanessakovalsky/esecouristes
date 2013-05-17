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

if ( isset ($_GET["expand"])) $expand=$_GET["expand"];
else $expand='false';

// sectionorder
if (isset ($_GET["sectionorder"])) {
   $_SESSION['sectionorder'] = $_GET["sectionorder"];
   $sectionorder=$_GET["sectionorder"];
}
else if ( isset($_SESSION['sectionorder']) ) {
   $sectionorder=$_SESSION['sectionorder'];
}
else {
   $sectionorder='alphabetique';
}



writehead();
$number=get_section_nb();
?>

<script language="JavaScript">
function displaymanager(p1){
	 self.location.href="upd_section.php?S_ID="+p1;
	 return true
}
function bouton_redirect(cible) {
	 self.location.href = cible;
}

function appear(id) {
    var d = document.getElementById(id);
    if (d.style.display!="none") {
        d.style.display ="none";
    } else {
        d.style.display ="";
    }
}

var imageURL = "images/tree_empty.png";
var te = new Image();
te.src = "images/tree_expand.png";
var tc = new Image();
tc.src = "images/tree_collapse.png";
var tec = new Image();
tec.src = "images/tree_expand_corner.png";
var tcc = new Image();
tcc.src = "images/tree_collapse_corner.png";

function changeImage(id) {
 		var i = document.getElementById(id);
        if (i.src == te.src ) i.src = tc.src;
        else if (i.src == tc.src) i.src = te.src;
        else if (i.src == tec.src) i.src = tcc.src;
        else if (i.src == tcc.src) i.src = tec.src;
}



</script>

<?php
echo "</head>";
echo "<body>";
 
if ( $nbsections == 0 ) $comment="Secteurs, Régions, Départements ou Antennes";
else $comment="Sections";

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 80 rowspan=2><img src=images/network.png></td>
	  <td width = 200><font size=4><b>Organigramme</b></font></td></tr>
	  <tr><td width = 200>$number $comment </td></tr>
	  </table>";

if ( $nbsections == 0 ) {

	echo "<p><table><tr>";
	if ( check_rights($_SESSION['id'], 22)) {
   		$query="select count(1) as NB from section";	
   		$result=mysql_query($query);
   		$row=@mysql_fetch_array($result);
   		if ( $row["NB"] <= $nbmaxsections )
   			echo "<td align=center><input type='button' value='Ajouter' name='ajouter' 
		   	onclick=\"bouton_redirect('ins_section.php');\"></td>";
    	else
  	 		echo "<td><font color=red>
		   	<b>Vous ne pouvez plus ajouter de sections <br>(maximum atteint: $nbmaxsections)</b></font></td>";
	}


	if ( $sectionorder=='hierarchique' ) {
		if ($expand == 'true') {
 			$checked_e='checked';
 			$checked_c='';
		}
		else {
 			$checked_c='checked';
 			$checked_e='';
		}


		echo "<td align=right><input type='radio' value='expand' ".$checked_e." 
		name='displaytype' onclick=\"bouton_redirect('section.php?expand=true')\"></td>
		<td align=center><font size=1>hiérarchie <br>dépliée</td>";

		echo "<td align=right><input type='radio' value='collapse' ".$checked_c." 
		name='displaytype' onclick=\"bouton_redirect('section.php?expand=false')\"></td>
		<td align=center><font size=1>hiérarchie <br>repliée</td>";
		
		echo "<td align=right><input type='radio' value='sectionorder' 
		name='displaytype' onclick=\"bouton_redirect('section.php?sectionorder=alphabetique')\"></td>
		<td align=center><font size=1>ordre <br>alphabétique</td>";
	}
	else {
		if ($sectionorder == 'hierarchique') {
 			$checked_h='checked';
 			$checked_a='';
		}
		else {
 			$checked_a='checked';
 			$checked_h='';
		}

		echo "<td align=right><input type='radio' value='sectionorder' ".$checked_h." 
		name='displaytype' onclick=\"bouton_redirect('section.php?sectionorder=hierarchique')\"></td>
		<td align=center><font size=1>ordre <br>hiérarchique</td>";

		echo "<td align=right><input type='radio' value='sectionorder' ".$checked_a." 
		name='displaytype' onclick=\"bouton_redirect('section.php?sectionorder=alphabetique')\"></td>
		<td align=center><font size=1>ordre <br>alphabétique</td>";
	
	}
	echo "</tr></table>";
}


echo "<p><TABLE>
<TR>
<TD class='FondMenu'>";
echo "<table width=450 cellspacing=0 cellpadding=0 border=0>";


// ===============================================
// le corps du tableau
// ===============================================
$End = array();
for ( $k=0; $k < $nbmaxlevels; $k++ ) {
	$End[$k] = 0;
	if ( $k == 10) return;
}

echo "<tr class=TabHeader><td>Sections 
	<font size=1>( personnel <img src=images/user.png title='nombre de personnes'>
	- véhicules <img src=images/car.png title='nombre de véhicules'> )</font></td></tr>";
echo "<tr bgcolor=white><td>";

display_children0(-1, 0, $nbmaxlevels, $expand, $sectionorder);

echo "</td></tr>";
echo "</table></td></tr></table>";

?>
