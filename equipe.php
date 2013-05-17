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
check_all(18);
writehead();

?>
<script language="JavaScript">

function displaymanager(p1){
	 self.location.href="upd_equipe.php?pid="+p1;
	 return true
}

function bouton_redirect(cible) {
	 self.location.href = cible;
}

</script>
<?php
echo "<body>";

$query1="select EQ_ID, EQ_NOM, EQ_JOUR,EQ_NUIT, EQ_TYPE
	     from equipe order by EQ_ID";

$result1=mysql_query($query1);
$number=mysql_num_rows($result1);

$title="Paramétrage des types de Compétences";
echo "<div align=center><font size=4><b>$title<br></b></font>";
echo "<table width=250 cellspacing=0 border=0 >";
echo "<tr height=60>";

echo "<td align=center>$number types</td>";
if ( check_rights($_SESSION['id'], 18)) {
   $query="select count(1) as NB from equipe";	
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   if ( $row["NB"] < $nbmaxequipes )
   		echo "<td><input type='button' value='Ajouter' name='ajouter' onclick=\"bouton_redirect('ins_equipe.php');\"></td>";
   else
   		echo "<font color=red size=2><b>Vous ne pouvez plus ajouter de $title ( maximum atteint: $nbmaxequipes)</b></font>";
}
echo "</tr></table>";

echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr height=10>
      	  <td width=20 align=center class=TabHeader>ID</td>
      	  <td width=0 class=TabHeader></td>
      	  <td width=230 align=center class=TabHeader>Description</td>
		  ";
      	   
if ( $gardes == 1 ) 
echo   	 "<td width=0 class=TabHeader></td>
      	  <td width=100 align=center class=TabHeader>Type</td>
		  <td width=0 class=TabHeader></td>
		  <td  width=20 align=center class=TabHeader>Jour</td>
      	  <td  width=0 class=TabHeader></td>
      	  <td  width=20 align=center class=TabHeader>Nuit</td>";
else   {
	$query2="select distinct CEV_CODE, CEV_DESCRIPTION from categorie_evenement";
	$result2=mysql_query($query2);
	while ($row=@mysql_fetch_array($result2)) {
		$CEV_CODE=$row["CEV_CODE"];
		$CEV_DESCRIPTION=$row["CEV_DESCRIPTION"];
		echo   	 "<td width=0 class=TabHeader></td>
      	  		 <td align=center class=TabHeader title=\"".$CEV_DESCRIPTION."\">
					 Afficher<br>".str_replace("C_","",$CEV_CODE)."</td>";
	}
}    
echo "</tr>";

// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result1)) {
      $EQ_ID=$row["EQ_ID"];
      $EQ_JOUR=$row["EQ_JOUR"];
      $EQ_NUIT=$row["EQ_NUIT"];
      $EQ_NOM=$row["EQ_NOM"];
      $EQ_TYPE=$row["EQ_TYPE"];
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      if ($EQ_TYPE == 'GARDE' ) {
      	if ( $EQ_JOUR == 1) $jour="<img src=images/green.gif>";
      	else $jour="<img src=images/red.gif>";
      	if ( $EQ_NUIT == 1) $nuit="<img src=images/green.gif>";
      	else $nuit="<img src=images/red.gif>";
      }
      else {
      	$jour="-";
      	$nuit="-";
      }

echo "<tr height=10 bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$mycolor'\" onclick=\"this.bgColor='#33FF00'; displaymanager($EQ_ID)\" >
      	  <td width=20 align=center>$EQ_ID</td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td  width=230 align=center>$EQ_NOM</td>";
if ( $gardes == 1 )
echo "    <td bgcolor=$mydarkcolor width=0></td>
      	  <td  width=100 align=center>$EQ_TYPE</td>  
		  <td bgcolor=$mydarkcolor width=0></td>
		  <td  width=20 align=center><B>$jour</B></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td  width=20 align=center><B>$nuit</B></td>";
else {
	$query2="select distinct ce.CEV_CODE, ce.CEV_DESCRIPTION, cea.FLAG1 
			from categorie_evenement ce, categorie_evenement_affichage cea
			where ce.CEV_CODE=cea.CEV_CODE
			and cea.EQ_ID=".$EQ_ID;
	$result2=mysql_query($query2);
	while ($row=@mysql_fetch_array($result2)) {
		$CEV_CODE=$row["CEV_CODE"];
		$CEV_DESCRIPTION=$row["CEV_DESCRIPTION"];
		$FLAG1=$row["FLAG1"];
		if ( $FLAG1 == 1 ) $show="<img src=images/YES.gif 
	  		title = \"Les compétences de la catégorie ".$CEV_DESCRIPTION." sont visibles sur la page des événements\">";
      	else $show="";
		echo  "<td width=0></td>
      	  	  <td align=center>".$show."</td>";
	}
}
echo "</tr>";
      
}

// ===============================================
// le bas du tableau
// ===============================================
echo "</table>";
echo "</td></tr></table>";   

?>
