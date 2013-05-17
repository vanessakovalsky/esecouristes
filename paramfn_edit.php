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
?>
<script language="JavaScript">
function displaymanager(p1,p2) {
     cible="paramfn.php?pid="+p1+"&type_evenement="+p2;
     self.location.href=cible;
}
function redirect(p1) {
     cible="paramfn.php?type_evenement="+p1;
     self.location.href=cible;
}

function change(what) 
{ 
	if  (what.value == 'ALL' ) {
		document.getElementById("sauver").disabled=true;
	}
	else {
		document.getElementById("show").src = "images/"+what.value+"small.gif";
		document.getElementById("sauver").disabled=false;
	}
} 

</script>
</head>
<?php

$title="Fonction";
if (isset($_GET["TP_ID"])) $TP_ID=intval($_GET["TP_ID"]);
else $TP_ID=0;


echo "<form name='paramfn' action='paramfn_save.php'>";
//=====================================================================
// affiche la fiche poste
//=====================================================================

if ( $TP_ID > 0 ) {
$query="select tp.TE_CODE, tp.TP_NUM,  tp.TP_LIBELLE, tp.INSTRUCTOR,
		tp.PS_ID, tp.PS_ID2, p.TYPE, p.DESCRIPTION, p2.TYPE TYPE2, p2.DESCRIPTION DESCRIPTION2, te.TE_LIBELLE
	  	from type_participation tp
	  	left join poste p on p.PS_ID=tp.PS_ID
		left join poste p2 on p2.PS_ID=tp.PS_ID2
		join type_evenement te on te.TE_CODE=tp.TE_CODE
		where tp.TP_ID=".$TP_ID;	
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$TE_CODE=$row["TE_CODE"];
$TE_CODE=$row["TE_CODE"];
$TP_NUM=$row["TP_NUM"];
$PS_ID=$row["PS_ID"];
$PS_ID2=$row["PS_ID2"];
$TYPE=$row["TYPE"];
$DESCRIPTION=$row["DESCRIPTION"];
$TYPE2=$row["TYPE2"];
$DESCRIPTION2=$row["DESCRIPTION2"];
$INSTRUCTOR=$row["INSTRUCTOR"];
$TE_LIBELLE=$row["TE_LIBELLE"];
$TP_LIBELLE=$row["TP_LIBELLE"];

echo "<div align=center><font size=4>
<b>Fonction ".$TP_LIBELLE." </b></font>
<img src=images/".$TE_CODE."small.gif id='show' title=\"utilisable pour les événements de type $TE_LIBELLE\">";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='TP_ID' value=".$TP_ID.">";
echo "<input type='hidden' name='INSTRUCTOR' value='".$INSTRUCTOR."'>";
}
else {
$TE_CODE=$type_evenement;
$TP_NUM=1;
$TP_LIBELLE='';
$INSTRUCTOR=0;
$TE_LIBELLE='à choisir';
if ($type_evenement == 'ALL' ) $img="miniquestion.png";
else $img=$type_evenement."small.gif";
echo "<div align=center><font size=4><b>Nouvelle fonction </b></font>
<img src=images/".$img." id='show' title=\"utilisable pour les événements de ce type\">";
echo "<input type='hidden' name='operation' value='insert'>";
echo "<input type='hidden' name='TP_ID' value='0'>";
echo "<input type='hidden' name='INSTRUCTOR' value='0'>";
}

echo "<input type='hidden' name='filter' value=".$type_evenement.">";
echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

//=====================================================================
// ligne 1
//=====================================================================

echo "<tr>
      	  <td colspan=2 class=TabHeader>Fonction</td>
      </tr>";

//=====================================================================
// ligne type
//=====================================================================


$query2="select TE_CODE, TE_LIBELLE from type_evenement";

echo "<tr>
      	<td bgcolor=$mylightcolor width=150><b>Type d'événement<font color=red>*</font></b></td>
      	<td bgcolor=$mylightcolor align=left>
		<select id ='TE_CODE' name='TE_CODE' onchange='change(this)'
		title=\"Choisir ici le type d'événement pour lequel la fonction pourra s'appliquer\">";
echo "<option value='ALL'>Choisissez un type d'événement</option>";
$result2=mysql_query($query2);
while ($row2=@mysql_fetch_array($result2)) {
 	$NEWTE_CODE=$row2["TE_CODE"];
	$NEWTE_LIBELLE=$row2["TE_LIBELLE"];
 	if ( $NEWTE_CODE == $TE_CODE ) $selected='selected';
 	else $selected='';
    echo "<option value='".$NEWTE_CODE."' $selected style='background-color:#FFFFFF'>".$NEWTE_LIBELLE."</option>";
}

echo "</select>";
echo "</tr>";

//=====================================================================
// ligne ordre
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Ordre dans la liste<font color=red>*</font></b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <select name='TP_NUM'
		  title=\"Choisir l'ordre de la fonction dans la liste déroulante listant les fonctions applicables au type d'événement\">";
		  for ($i=1 ; $i<=10 ; $i++) {
			if ($TP_NUM == $i) $selected="selected";
			else $selected="";
		    echo "<option value='$i' $selected>$i</option>";
		  }
 	      echo "</select>";
echo "</tr>";


//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor >
			<b>Libellé <font color=red>*</font></b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='TP_LIBELLE' size='35' value=\"".$TP_LIBELLE."\"
			title=\"Choisir le libellé de la fonction, maximum 40 caractères\" >";		
echo "</tr>";

//=====================================================================
// ligne moniteur?
//=====================================================================

 if ( $INSTRUCTOR == 1 ) $checked="checked";
 else $checked="";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Fonction d'instructeur? <font color=red>*</font></b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='INSTRUCTOR'  value='1' $checked title=\"cocher cette case si il s'agit d'une fonction d'instructeur ou moniteur\">
				</td>";		
 echo "</tr>";

//=====================================================================
// compétence requise
//=====================================================================

if ( $competences == 1 ) {

$query2="select p.PS_ID, p.EQ_ID, p.TYPE, p.DESCRIPTION, e.EQ_NOM
		from poste p, equipe e
		where p.EQ_ID=e.EQ_ID 
		and e.EQ_TYPE='COMPETENCE'
		order by p.EQ_ID, p.DESCRIPTION";

echo "<tr>
      	  <td bgcolor=$mylightcolor >
			<b>Compétence requise </b></font></td>
      	  <td bgcolor=$mylightcolor align=left>";
echo "<select id ='PS_ID' name='PS_ID' title='Une compétence peut être requise pour pouvoir exercer la fonction, définir laquelle'>";
echo "<option value='0'>Aucune compétence requise</option>";
$result2=mysql_query($query2);
$prevEQ_ID=-1;
while ($row2=@mysql_fetch_array($result2)) {
      $NEWPS_ID=$row2["PS_ID"];
      $NEWEQ_ID=$row2["EQ_ID"];
      $NEWTYPE=$row2["TYPE"];
      $NEWDESCRIPTION=$row2["DESCRIPTION"];
      $NEWEQ_NOM=$row2["EQ_NOM"];
	  if ($prevEQ_ID <> $NEWEQ_ID ) echo "<OPTGROUP LABEL=\"".$NEWEQ_NOM."\" class='section'>";
      $prevEQ_ID=$NEWEQ_ID;
      if ( $PS_ID ==  $NEWPS_ID ) $selected='selected';
      else $selected='';
      echo "<option value='".$NEWPS_ID."' $selected>
			".$NEWTYPE." - ".$NEWDESCRIPTION."</option>\n";
}
echo "</select></td></tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor >
			<b>Ou </b></font></td>
      	  <td bgcolor=$mylightcolor align=left>";
echo "<select id ='PS_ID2' name='PS_ID2' title='Une autre compétence peut être requise pour pouvoir exercer la fonction, définir laquelle'>";
echo "<option value='0'>Aucune compétence requise</option>";
$result2=mysql_query($query2);
$prevEQ_ID=-1;
while ($row2=@mysql_fetch_array($result2)) {
      $NEWPS_ID=$row2["PS_ID"];
      $NEWEQ_ID=$row2["EQ_ID"];
      $NEWTYPE=$row2["TYPE"];
      $NEWDESCRIPTION=$row2["DESCRIPTION"];
      $NEWEQ_NOM=$row2["EQ_NOM"];
	  if ($prevEQ_ID <> $NEWEQ_ID ) echo "<OPTGROUP LABEL=\"".$NEWEQ_NOM."\" class='section'>";
      $prevEQ_ID=$NEWEQ_ID;
      if ( $PS_ID2 ==  $NEWPS_ID ) $selected='selected';
      else $selected='';
      echo "<option value='".$NEWPS_ID."' $selected>
			".$NEWTYPE." - ".$NEWDESCRIPTION."</option>\n";
}
echo "</select></td></tr>";
}
else {
	echo "<input type=hidden id ='PS_ID' name='PS_ID' value='0'>";
	echo "<input type=hidden id ='PS_ID2' name='PS_ID2' value='0'>";
}
//=====================================================================
// bas de tableau
//=====================================================================

echo "</table>";
echo "</td></tr></table>";  
if ($TP_ID == 0  and $TE_CODE == 'ALL' ) $disabled='disabled';
else  $disabled='';
echo "<p><input type='submit' id='sauver' value='sauver' $disabled> ";
echo "</form>";
if ( $TP_ID > 0 ) {
	echo "<form name='paramfn' action='paramfn_save.php'>";
	echo "<input type='hidden' name='TP_ID' value='$TP_ID'>";
	echo "<input type='hidden' name='operation' value='delete'>";
	echo "<input type='submit' value='supprimer'> ";
}
echo "<input type=button value=Retour name=annuler onclick=\"redirect('".$type_evenement."');\">";
echo "</form>";
echo "</div>";

?>
