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
get_session_parameters();

writehead();
echo "
<script type='text/javascript' src='checkForm.js'></script>
</script>
";
echo "</head>";
echo "<body>";

//=====================================================================
// affiche la fiche type de matériel
//=====================================================================

$query="select CM_DESCRIPTION,PICTURE_LARGE from categorie_materiel
		where TM_USAGE='ALL'";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$cmt=$row["CM_DESCRIPTION"];
$picture=$row["PICTURE_LARGE"];

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/$picture></td><td>
      <font size=4><b>Nouveau type de matériel</b></font></td></tr></table>";


echo "<form name='materiel' action='save_type_materiel.php'>";
echo "<input type='hidden' name='operation' value='insert'>";
echo "<input type='hidden' name='TM_ID' value='0'>";
echo "<input type='hidden' name='TM_LOT' value='0'>";
echo "<input type='hidden' name='TM_CONTROLE' value='0'>";
	
//=====================================================================
// ligne 1
//=====================================================================

echo "<p><TABLE>
<TR>
<TD class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr height=10>
      	  <td class=TabHeader colspan=2>informations type de matériel</td>
      </tr>";


//=====================================================================
// ligne catégorie
//=====================================================================

$query="select TM_USAGE, CM_DESCRIPTION from categorie_materiel
		 where TM_USAGE<>'ALL' order by TM_USAGE asc";
$result=mysql_query($query);

echo "<tr>
      	  <td bgcolor=$mylightcolor width=200><b>Catégorie</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor width=250 align=left>
		  <select name='TM_USAGE'>";
		  	 while ($row=@mysql_fetch_array($result)) {
		  	    if ( $row["TM_USAGE"] == $catmateriel ) $selected='selected';
		  	    else $selected='';
		  	    echo "<option value=\"".$row["TM_USAGE"]."\" $selected>".$row["TM_USAGE"]." - ".$row["CM_DESCRIPTION"]."</option>";
	     	 }
 echo "</select>";
 echo "</td>
 	 </tr>";

//=====================================================================
// ligne code
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>type</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='TM_CODE' size='20' value=''>";		
echo " </td>
      </tr>";
  
	  
//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Description</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='TM_DESCRIPTION' size='40' value=''>";		
echo " </td>
      </tr>";
	  
//=====================================================================
// lot de matériel
//=====================================================================
echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Lot de matériel</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='TM_LOT' value='1'
			title=\"Cochez la case si ce type définit un lot de matériel\">
			<font size=1><i>des pièces de matériel peuvent être intégrées dans un lot<i></font>";		
echo " </td>
      </tr>";	

//=====================================================================
// Contrôle du matériel
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Matériel soumis à contrôle</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='TM_CONTROLE' value='1'
			title=\"Cochez la case si ce type de matériel est soumis à contrôle\">";		
echo " </td>
      </tr>";	

//=====================================================================
// Période de Contrôle du matériel
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor width=200><b>Périodicité de contrôle</b></td>
      	  <td bgcolor=$mylightcolor width=250 align=left>
		  <select name='TM_PERIODE_CONTROLE'>";
			echo "<option value=\"1 mois\"";		  	    
				if ( $TM_PERIODE_CONTROLE == "1 mois" ) 	    
					echo " selected";
			echo ">1 mois</option>";
			echo "<option value=\"2 mois\"";
				if ( $TM_PERIODE_CONTROLE == "2 mois" )
					echo " selected";
			echo ">2 mois</option>";
			echo "<option value=\"3 mois\"";
				if ( $TM_PERIODE_CONTROLE == "3 mois" )				
					echo " selected";
			echo ">3 mois</option>";
			echo "<option value=\"6 mois\"";
				if ( $TM_PERIODE_CONTROLE == "6 mois" )
					 echo "selected";
			echo ">6 mois</option>";
			echo "<option value=\"1 an\"";
				if ( $TM_PERIODE_CONTROLE == "1 an" )
					echo " selected";
			echo ">1 an</option>";
				echo "</select>";
 echo "</td>
 	 </tr>";

echo "</table></tr></table>";
echo "<p><input type='submit' value='sauver'> ";

echo "<input type='button' value='Retour' name='annuler' onclick=\"javascript:history.back(1);\">";
echo "</form>";
echo "</div>";
?>
