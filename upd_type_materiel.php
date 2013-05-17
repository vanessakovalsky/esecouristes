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
echo "
<script type='text/javascript' src='checkForm.js'></script>
</script>
";
echo "</head>";
echo "<body>";

$TM_ID=$_GET["id"];
if (isset ($_GET["from"])) $from=$_GET["from"];
else $from=0;

//=====================================================================
// affiche la fiche type de matériel
//=====================================================================

$query="select tm.TM_CODE,tm.TM_DESCRIPTION,tm.TM_USAGE, cm.CM_DESCRIPTION,cm.PICTURE_LARGE, tm.TM_LOT, tm.TM_CONTROLE, tm.TM_PERIODE_CONTROLE
        from type_materiel tm, categorie_materiel cm
		where tm.TM_ID='".$TM_ID."'
		and cm.TM_USAGE=tm.TM_USAGE
		order by TM_USAGE asc";	
		
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$TM_CODE=$row["TM_CODE"];
$TM_DESCRIPTION=$row["TM_DESCRIPTION"];
$TM_USAGE=$row["TM_USAGE"];
$TM_LOT=$row["TM_LOT"];
$TM_CONTROLE=$row["TM_CONTROLE"];
$TM_PERIODE_CONTROLE=$row["TM_PERIODE_CONTROLE"];
$CM_DESCRIPTION=$row["CM_DESCRIPTION"];
$PICTURE_LARGE=$row["PICTURE_LARGE"];

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/$PICTURE_LARGE></td><td>
      <font size=4><b>".$TM_USAGE.' - '.$TM_CODE."</b></font></td></tr></table>";


echo "<form name='materiel' action='save_type_materiel.php'>";
echo "<input type='hidden' name='TM_ID' value='$TM_ID'>";
echo "<input type='hidden' name='TM_CODE' value=\"$TM_CODE\">";
echo "<input type='hidden' name='TM_USAGE' value='$TM_USAGE'>";
echo "<input type='hidden' name='TM_LOT' value='0'>";
echo "<input type='hidden' name='TM_CONTROLE' value='0'>";
echo "<input type='hidden' name='TM_PERIODE_CONTROLE' value='$TM_PERIODE_CONTROLE'>";
echo "<input type='hidden' name='operation' value='update'>";


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
		  	    if ( $row["TM_USAGE"] == $TM_USAGE ) $selected='selected';
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
      	  <td bgcolor=$mylightcolor ><b>type</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='TM_CODE' size='20' value=\"$TM_CODE\">";		
echo " </td>
      </tr>";
      
//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Description</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='TM_DESCRIPTION' size='40' value=\"$TM_DESCRIPTION\">";		
echo " </td>
      </tr>";
	  
//=====================================================================
// lot de matériel
//=====================================================================
if ( $TM_LOT == 1 ) $checked='checked';
else $checked='';

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Lot de matériel</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='TM_LOT' value='1' $checked
			title=\"Cochez la case si ce type définit un lot de matériel\">
			<font size=1><i>des pièces de matériel peuvent être intégrées dans un lot<i></font>";		
echo " </td>
      </tr>";	

//=====================================================================
// Contrôle du matériel
//=====================================================================
if ( $TM_CONTROLE == 1 ) $checked2='checked';
else $checked2='';

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Matériel soumis à contrôle</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='TM_CONTROLE' value='1' $checked2
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
echo "</form><form name='materiel2' action='save_type_materiel.php'>";
echo "<input type='hidden' name='TM_ID' value='$TM_ID'>";
echo "<input type='hidden' name='TM_CODE' value=\"$TM_CODE\">";
echo "<input type='hidden' name='TM_USAGE' value='$TM_USAGE'>";
echo "<input type='hidden' name='TM_LOT' value='$TM_LOT'>";
echo "<input type='hidden' name='TM_CONTROLE' value='$TM_CONTROLE'>";
echo "<input type='hidden' name='TM_PERIODE_CONTROLE' value='$TM_PERIODE_CONTROLE'>";
echo "<input type='hidden' name='TM_DESCRIPTION' value='$TM_DESCRIPTION'>";
echo "<input type='hidden' name='operation' value='delete'>";
echo "<input type='submit' value='supprimer'> ";

echo "<input type='button' value='Retour' name='annuler' onclick=\"javascript:history.back(1);\">";
echo "</form>";
echo "</div>";
?>
