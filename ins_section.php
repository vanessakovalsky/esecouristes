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
check_all(22);
if ( check_rights($_SESSION['id'], 24)) $mysection='0';
else $mysection=get_highest_section_where_granted($_SESSION['id'], 22);
if ( $mysection == '' ) $mysection=$_SESSION['SES_SECTION'];
writehead();

?>
<script type='text/javascript' src='checkForm.js'></script>
</head>
<?php

//=====================================================================
// affiche la fiche personnel
//=====================================================================
echo "<div align=center><font size=4><b>Ajouter une section<br></b></font>";


echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<form name='personnel' action='save_section.php' method='POST' >";
echo "<input type='hidden' name='S_ID' value='100'>";
echo "<input type='hidden' name='chef' value=''>";
echo "<input type='hidden' name='adjoint' value=''>";
echo "<input type='hidden' name='cadre' value=''>";
echo "<input type='hidden' name='description' value=''>";
echo "<input type='hidden' name='operation' value='insert'>";
//=====================================================================
// ligne 1
//=====================================================================

echo "<tr height=10>
      	   <td width=300 colspan=2 class=TabHeader>Informations obligatoires</td>
      </tr>";
      
echo "<tr height=5>
      	  <td bgcolor=$mylightcolor width=300 colspan=2></td>";		
echo "</tr>";

//=====================================================================
// code
//=====================================================================
echo "<tr>
      	  <td bgcolor=$mylightcolor width=150 ><b>Code</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left height=25><input type='text' name='code' size='10' value=''>";		
echo "</tr>";

//=====================================================================
// parent section 
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150><b>Sous section de</b></font></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>";
echo "<select id='parent' name='parent'>";

if ( $mysection <> 0 ){ 	
	$level=get_level($mysection);
	if ( $level == 0 ) $mycolor=$myothercolor;
	elseif ( $level == 1 ) $mycolor=$my2darkcolor;
	elseif ( $level == 2 ) $mycolor=$my2lightcolor;
	elseif ( $level == 3 ) $mycolor=$mylightcolor;
	else $mycolor='white';
	$class="style='background: $mycolor;'";
	echo "<option value='$mysection' $class >".
		str_repeat(". ",$level)." ".get_section_code($mysection)." - ".get_section_name($mysection)."</option>";
	display_children2($mysection, $level +1, $mysection, $nbmaxlevels - 1);
}
else {
   $mycolor=$myothercolor;
   $class="style='background: $mycolor;'";
   echo "<option value='0' $class >".get_section_code('0')." - ".get_section_name('0')."</option>";
 	display_children2(0, 1, $mysection, $nbmaxlevels - 1);
} 	
echo "</select></td> ";
echo "</tr>";	  

//=====================================================================
// intercalaire
//=====================================================================

echo "<tr>
      	   <td width=300 colspan=2 class=TabHeader>
			 		<i>Informations facultatives</i>
		    </td>
      </tr>";

//=====================================================================
// name
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150 >Nom long</td>
      	  <td bgcolor=$mylightcolor width=150 align=left height=25><input type='text' name='nom' size='40' value='' >";		
echo "</tr>";

//=====================================================================
// ligne address
//=====================================================================

echo "<tr height=50>
      	  <td bgcolor=$mylightcolor width=150 align=left>Adresse</font></td>
      	  <td bgcolor=$mylightcolor width=150 align=left><textarea name='address' cols='20' rows='3' value=''></textarea></td>";
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150 align=left>Code postal</font></td>
      	  <td bgcolor=$mylightcolor width=150 align=left><input type='text' name='zipcode' size='10' value=''></td>";
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150 align=left>Ville</font></td>
      	  <td bgcolor=$mylightcolor width=150 align=left><input type='text' name='city' size='20' value=''></td>";
echo "</tr>";

//=====================================================================
// ligne phone
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150>Téléphone</td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<input type='text' name='phone' size='20' value='' onchange='checkPhone(form.phone,\"\")'>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150>Téléphone 2</td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<input type='text' name='phone2' size='20' value='' onchange='checkPhone(form.phone2,\"\")'>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150>Fax</td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<input type='text' name='fax' size='20' value='' onchange='checkPhone(form.fax,\"\")'>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150>Email commun</font></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<input type='text' name='email' size='20' value='' onchange='mailCheck(form.email,\"\")'>";		
echo "</tr>";
echo "<tr>
      	  <td bgcolor=$mylightcolor width=150>Email secrétariat</font></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<input type='text' name='email2' size='20' value='' onchange='mailCheck(form.email2,\"\")'>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150>Site web</td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<input type='text' name='url' size='40' value=''>";		
echo "</tr>";

echo "</td></tr></table>";// end left table
echo "</td></tr></table>"; // end cadre
echo "<p><input type='submit' value='sauver'></form>";
echo "<input type='button' value='Annuler' name='annuler' onclick=\"javascript:history.back(1);\"></div>";

?>
