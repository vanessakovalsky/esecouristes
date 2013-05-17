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
check_all(17);

$section=$_SESSION['SES_SECTION'];
$mysection=get_highest_section_where_granted($_SESSION['id'],17);
if ( check_rights($_SESSION['id'], 24) ) $section='0';
else if ( $mysection <> '' ) {
 	if ( is_children($section,$mysection)) 
 		$section=$mysection;
}

//=====================================================================
// affiche la fiche vehicule
//=====================================================================

writehead();
echo "
<script type='text/javascript' src='checkForm.js'></script>
</script>
";
echo "</head>";
echo "<body>";

echo "<div align=center><font size=4><b>Ajout d'un nouveau véhicule<br></b></font>";

echo "<p><TABLE>
<TR>
<TD class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<form name='vehicule' action='save_vehicule.php'>";
echo "<input type='hidden' name='V_ID' value=''>";
echo "<input type='hidden' name='groupe' value=''>";
echo "<input type='hidden' name='EQ_ID' value='1'>";
echo "<input type='hidden' name='TV_CODE' value=''>";
echo "<input type='hidden' name='V_IMMATRICULATION' value=''>";
echo "<input type='hidden' name='V_COMMENT' value=''>";
echo "<input type='hidden' name='VP_ID' value=''>";
echo "<input type='hidden' name='V_ANNEE' value=''>";
echo "<input type='hidden' name='V_ASS_DATE' value=''>";
echo "<input type='hidden' name='V_CT_DATE' value=''>";
echo "<input type='hidden' name='V_REV_DATE' value=''>";
echo "<input type='hidden' name='V_INVENTAIRE' value=''>";
echo "<input type='hidden' name='V_INDICATIF' value=''>";
echo "<input type='hidden' name='operation' value='insert'>";
echo "<input type='hidden' name='from' value=''>";
for ( $i = 1 ; $i <= 8 ; $i++) {
	echo "<input type='hidden' name='P".$i."' value=''>";
}
//=====================================================================
// ligne 1
//=====================================================================

echo "<tr class=TabHeader>
      	   <td bgcolor=$mydarkcolor width=200 align=right></td>
      	  <td bgcolor=$mydarkcolor width=250 align=right><b>informations véhicule</b></td>
      </tr>";


//=====================================================================
// ligne type
//=====================================================================

$query2="select distinct TV_CODE, TV_LIBELLE from type_vehicule
		 order by TV_CODE";
$result2=mysql_query($query2);

echo "<tr>
      	  <td bgcolor=$mylightcolor width=200><b>Type</b><font color=red> *</font></td>
      	  <td bgcolor=$mylightcolor width=250 align=left>
		<select name='TV_CODE'>";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $TV_CODE=$row2["TV_CODE"];
		          $TV_LIBELLE=$row2["TV_LIBELLE"];
		          echo "<option value='$TV_CODE'>$TV_CODE - $TV_LIBELLE</option>";
	     	     }
 	        echo "</select>";
 echo "</td>
 	 </tr>";


//=====================================================================
// ligne immatriculation
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Immatriculation</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_IMMATRICULATION' size='20' value=''>";		
echo "</tr>";

//=====================================================================
// numéro d'indicatif
//=====================================================================

echo "<tr >
      	  <td bgcolor=$mylightcolor ><b>Indicatif</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_INDICATIF' size='30' value=''>";		
echo " </td>
      </tr>";
//=====================================================================
// ligne année
//=====================================================================

$curyear=date("Y");
$year=$curyear - 30; 
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Année</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<select name='V_ANNEE'>";
		while ( $year <= $curyear + 1 ) {
			if ( $year == $curyear ) $selected = 'selected';
			else $selected = '';
			echo "<option value='$year' $selected>$year</option>";
			$year++;
		}		
echo "</select></tr>";


//=====================================================================
// ligne kilometrage
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Kilométrage</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='V_KM' size='5' value='0' onchange='checkNumber(form.V_KM,\"0\")'>";		
echo "</tr>";


//=====================================================================
// ligne modèle
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Modèle</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_MODELE' size='25' value=''>";		
echo "</tr>";

//=====================================================================
// ligne section
//=====================================================================

if (  $nbsections == 0 ) {
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Section</b><font color=red> *</font></td>
      	  <td bgcolor=$mylightcolor align=left>";
 	echo "<select id='groupe' name='groupe'>";
 	
 	$level=get_level($section);
 	if ( $level == 0 ) $mycolor=$myothercolor;
	elseif ( $level == 1 ) $mycolor=$my2darkcolor;
    elseif ( $level == 2 ) $mycolor=$my2lightcolor;
    elseif ( $level == 3 ) $mycolor=$mylightcolor;
    else $mycolor='white';
    $class="style='background: $mycolor;'";
    
    if ( isset($_SESSION['sectionchoice']) ) $defaultsection=$_SESSION['sectionchoice'];
    else $defaultsection=$_SESSION['SES_SECTION'];
	echo "<option value='$section' $class >".
		str_repeat(". ",$level)." ".get_section_code($section)." - ".get_section_name($section)."</option>";
	display_children2($section, $level +1, $defaultsection, $nbmaxlevels);
 	
	echo "</select></td> ";
	echo "</tr>";	  
}
else echo "<input type='hidden' name='groupe' value='0'>";

//=====================================================================
// ligne type
//=====================================================================
if ( $gardes == 1 ) {
	$query2="select EQ_ID, EQ_NOM from equipe where EQ_TYPE='GARDE' order by EQ_ID";
	$result2=mysql_query($query2);

	echo "<tr>
      	  <td bgcolor=$mylightcolor >
			<b>Usage principal</b></td>
      	  <td bgcolor=$mylightcolor align=left>
		<select name='EQ_ID'>";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $EQ_ID=$row2["EQ_ID"];
		          $EQ_NOM=$row2["EQ_NOM"];
		          echo "<option value='$EQ_ID'>$EQ_NOM</option>";
	     	     }
 	        echo "</select>";
	echo "</tr>";
}

//=====================================================================
// dates d'assurance de contrôle technique et de révision
//=====================================================================

echo "<input type='hidden' name='dc0' value='".getnow()."'>";


// assurance
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Fin assurance</b></td>
      	  <td bgcolor=$mylightcolor align=left>";
?>
<input class="plain" name="dc1" value=""
size="12" onfocus="this.blur()" readonly><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.vehicule.dc0,document.vehicule.dc1);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php

// contrôle technique
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Prochain contrôle technique</b></td>
      	  <td bgcolor=$mylightcolor align=left>";
?>
<input class="plain" name="dc2" value=""
size="12" onfocus="this.blur()" readonly><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.vehicule.dc0,document.vehicule.dc2);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php

// révision
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Prochaine révision</b></td>
      	  <td bgcolor=$mylightcolor align=left>";
?>
<input class="plain" name="dc3" value=""
size="12" onfocus="this.blur()" readonly><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.vehicule.dc0,document.vehicule.dc3);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php

//=====================================================================
// numéro d'inventaire
//=====================================================================

echo "<tr >
      	  <td bgcolor=$mylightcolor ><b>N°d'inventaire</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_INVENTAIRE' size='30' value=''>";		
echo " </td>
      </tr>";

      
//=====================================================================
// ligne commentaire
//=====================================================================

echo "<tr >
      	  <td bgcolor=$mylightcolor ><b>Commentaire</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_COMMENT' size='30' value=''>";		
echo " </td>
      </tr>";

     
//=====================================================================
// vehicule externe
//=====================================================================

if (check_rights($_SESSION['id'], 24) and ($nbsections ==  0 )) {
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>véhicule $cisname</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='V_EXTERNE' value='1'>
			<font size=1><i>mis à disposition (utilisable, non modifiable)<i></font>";		
echo " </td>
      </tr>";
}	        

echo "</table></td></tr></table>";
echo "<p><input type='submit' value='sauver'></form>";
echo "<input type='button' value='Annuler' name='annuler' onclick=\"javascript:history.back(1);\"></div>";

?>

<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
</BODY>
</HTML>