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
check_all(17);

if ( isset($_GET["type"])) $type=mysql_real_escape_string($_GET["type"]);
else $type='ALL';
if ( isset($_GET["usage"])) $usage=mysql_real_escape_string($_GET["usage"]);
else $usage='ALL';

$section=$_SESSION['SES_SECTION'];
$mysection=get_highest_section_where_granted($_SESSION['id'],17);
if ( check_rights($_SESSION['id'], 24) ) $section='0';
else if ( $mysection <> '' ) {
 	if ( is_children($section,$mysection)) 
 		$section=$mysection;
}

//=====================================================================
// affiche la fiche materiel
//=====================================================================

writehead();
?>
<script type='text/javascript' src='checkForm.js'></script>
<script language="JavaScript">
function displaymanager(p1){
	 self.location.href="ins_materiel.php?usage="+p1;
	 return true
}
function redirect() {
     cible="materiel.php?order=TM_USAGE&type=ALL";
     self.location.href=cible;
}
</script>
</head>
<?php

$query="select CM_DESCRIPTION,PICTURE_LARGE from categorie_materiel
		where TM_USAGE='".$usage."'";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$cmt=$row["CM_DESCRIPTION"];
$picture=$row["PICTURE_LARGE"];

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/$picture></td><td>
      <font size=4><b> Ajout d'un nouveau ".$cmt."</b></font></td></tr></table>";

echo "<p><TABLE>
<TR>
<TD class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<form name='vehicule' action='save_materiel.php'>";
echo "<input type='hidden' name='MA_ID' value=''>";
echo "<input type='hidden' name='TM_ID' value=''>";
echo "<input type='hidden' name='groupe' value=''>";
echo "<input type='hidden' name='MA_NUMERO_SERIE' value=''>";
echo "<input type='hidden' name='MA_COMMENT' value=''>";
echo "<input type='hidden' name='VP_ID' value=''>";
echo "<input type='hidden' name='MA_ANNEE' value=''>";
echo "<input type='hidden' name='MA_INVENTAIRE' value=''>";
echo "<input type='hidden' name='operation' value='insert'>";
echo "<input type='hidden' name='from' value=''>";
echo "<input type='hidden' name='MA_REV_DATE' value=''>";


//=====================================================================
// ligne 1
//=====================================================================

echo "<tr class=TabHeader>
      	   <td bgcolor=$mydarkcolor width=200 align=right></td>
      	  <td bgcolor=$mydarkcolor width=250 align=right><b>informations matériel</b></td>
      </tr>";

//=====================================================================
// ligne catégorie
//=====================================================================
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Catégorie <font color=red>*</font></b></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <select id ='TM_USAGE'name='TM_USAGE' 
		  onchange=\"displaymanager(document.getElementById('TM_USAGE').value)\">";
if ( $usage == 'ALL') echo "<option value='ALL'>Choisissez une catégorie</option>";
$query2="select cm.TM_USAGE, cm.CM_DESCRIPTION from categorie_materiel cm
		where exists (select 1 from type_materiel tm where cm.TM_USAGE =tm.TM_USAGE)
		order by cm.CM_DESCRIPTION";
$result2=mysql_query($query2);
while ($row=@mysql_fetch_array($result2)) {
      $TM_USAGE=$row["TM_USAGE"];
      $CM_DESCRIPTION=$row["CM_DESCRIPTION"];
      echo "<option value='".$TM_USAGE."'";
      if ($TM_USAGE == $usage ) echo " selected ";
      echo ">".$CM_DESCRIPTION."</option>\n";
}
echo "</select>";
echo "</td>
 	 </tr>";
//=====================================================================
// ligne type
//=====================================================================

if ( $usage <> 'ALL' ) {
$query2="select distinct TM_ID, TM_CODE, TM_DESCRIPTION, TM_LOT from type_materiel where TM_USAGE='".$usage."'
		 order by TM_CODE";
$result2=mysql_query($query2);

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Type</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor  align=left>
		 <select name='TM_ID'>";
		     while ($row2=@mysql_fetch_array($result2)) {
		      	  $TM_ID=$row2["TM_ID"];
				  $TM_LOT=$row2["TM_LOT"];
				  if ( $TM_LOT == 1 ) $lot=" (lot)";
				  else $lot="";
		          $TM_CODE=$row2["TM_CODE"];
		          $TM_DESCRIPTION=$row2["TM_DESCRIPTION"];
		          if ( $TM_DESCRIPTION <> "" ) $addcmt= " - ".$TM_DESCRIPTION;
		          else $addcmt="";
		          if ($TM_ID == $type ) $selected = 'selected';
				  else $selected =''; 
		          echo "<option value='$TM_ID' $selected>".substr($TM_CODE.$addcmt,0,45).$lot."</option>";
	     	     }
 	        echo "</select>";
 echo "</td>
 	 </tr>";
	 
//=====================================================================
// ligne modèle
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Marque/Modèle</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='MA_MODELE' size='25' value=''>";		
echo "</tr>";

//=====================================================================
// ligne section
//=====================================================================

if (  $nbsections == 0 ) {
	echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Section</b> <font color=red>*</font></td>
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
// ligne nombre
//=====================================================================
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Nombre de pièces</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left height=25>
			<input type='text' name='quantity' size='6' value='1' onchange='checkNumber(form.quantity,\"1\")'></td>";		
echo "</tr>";

//=====================================================================
// ligne statut
//=====================================================================

$query2="select VP_LIBELLE, VP_ID, VP_OPERATIONNEL
         from vehicule_position
		 where VP_OPERATIONNEL <> 0
		 order by  VP_OPERATIONNEL desc";
$result2=mysql_query($query2);

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Position du matériel</b> <font color=red>*</font></font></td>
      	  <td bgcolor=$mylightcolor align=left>
		<select name='VP_ID' >";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $VP_ID=$row2["VP_ID"];
		          $VP_LIBELLE=$row2["VP_LIBELLE"];
		          $VP_OPERATIONNEL=$row2["VP_OPERATIONNEL"];
		          if ($VP_ID == 'OP') $selected='selected';
		          else $selected='';
		          echo "<option value='$VP_ID' $selected>$VP_LIBELLE</option>";
	     	     }
 	        echo "</select>";
echo " </td>
      </tr>";

//=====================================================================
// ligne numéro de série
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Numéro de série</b></td>
      	  <td bgcolor=$mylightcolor align=left height=25><input type='text' name='MA_NUMERO_SERIE' size='20' value=''>";		
echo "</tr>";

//=====================================================================
// ligne année
//=====================================================================

$curyear=date("Y");
$year=$curyear - 30; 
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Année</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<select name='MA_ANNEE'>";
echo "<option value='' selected>inconnue</option>";
while ( $year <= $curyear + 1 ) {
			echo "<option value='$year'>$year</option>";
			$year++;
		}		
echo "</select></tr>";

//=====================================================================
// ligne commentaire
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Commentaire</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='MA_COMMENT' size='40' value=''>";		
echo " </td>
      </tr>";


//=====================================================================
// ligne inventaire
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>N°d'inventaire</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='MA_INVENTAIRE' size='40' value=''>";		
echo " </td>
      </tr>";

//=====================================================================
// ligne lieu stockage
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Lieu de stockage</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='MA_LIEU_STOCKAGE' size='40' value=''>";		
echo " </td>
      </tr>";

//=====================================================================
// dates de prochaine révision ou péremption
//=====================================================================

echo "<input type='hidden' name='dc0' value='".getnow()."'>";


// assurance
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Prochaine révision ou péremption</b></td>
      	  <td bgcolor=$mylightcolor align=left>";
?>
<input class="plain" name="dc1" value=""
size="12" onfocus="this.blur()" readonly><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.vehicule.dc0,document.vehicule.dc1);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php
      
//=====================================================================
// materiel externe
//=====================================================================

if (check_rights($_SESSION['id'], 24) and ($nbsections ==  0 )) {
echo "<tr>
      	  <td bgcolor=$mylightcolor><b>matériel $cisname</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='MA_EXTERNE' value='1'>
			<font size=1><i>mis à disposition (utilisable, non modifiable)<i></font>";		
echo " </td>
      </tr>";
}	        

echo "</table></td></tr></table>";
echo "<p><input type='submit' value='sauver'></form>";
echo "<input type='button' value='Annuler' name='annuler' onclick='redirect();'></div>";
}
?>

<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
</BODY>
</HTML>