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
function displaymanager(p1,p2) {
     cible="upd_poste.php?pid="+p1+"&type="+p2;
     self.location.href=cible;
}
function redirect() {
     cible="poste.php?order=PS_ID";
     self.location.href=cible;
}
</script>
</head>
<?php

$PS_ID=intval($_GET["pid"]);

$disabled="";
if ( check_rights($_SESSION['id'], 18)) $disabled=""; else $disabled="disabled";

//=====================================================================
// affiche la fiche poste
//=====================================================================

$query="select p.PS_ID, p.EQ_ID, p.TYPE, p.DESCRIPTION, p.PO_JOUR, p.PO_NUIT , e.EQ_TYPE,
		e.EQ_NOM, e.EQ_JOUR, e.EQ_NUIT, p.PS_EXPIRABLE, p.PS_AUDIT, p.PS_DIPLOMA, p.PS_NATIONAL, 
		p.PS_RECYCLE, p.PS_USER_MODIFIABLE, p.PS_PRINTABLE, p.PS_SECOURISME, p.F_ID
	     from equipe e, poste p
	     where p.EQ_ID=e.EQ_ID
		 and p.PS_ID=".$PS_ID;	
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$EQ_ID=$row["EQ_ID"];
$EQ_TYPE=$row["EQ_TYPE"];
$TYPE=$row["TYPE"];
$F_ID=$row["F_ID"];
$DESCRIPTION=$row["DESCRIPTION"];
$PO_JOUR=$row["PO_JOUR"];
$PO_NUIT=$row["PO_NUIT"];
$EQ_NOM=$row["EQ_NOM"];
$EQ_JOUR=$row["EQ_JOUR"];
$EQ_NUIT=$row["EQ_NUIT"];
$PS_EXPIRABLE=$row["PS_EXPIRABLE"];
$PS_AUDIT=$row["PS_AUDIT"];
$PS_SECOURISME=$row["PS_SECOURISME"];
$PS_DIPLOMA=$row["PS_DIPLOMA"];
$PS_NATIONAL=$row["PS_NATIONAL"];
$PS_PRINTABLE=$row["PS_PRINTABLE"];
$PS_RECYCLE=$row["PS_RECYCLE"];
$PS_USER_MODIFIABLE=$row["PS_USER_MODIFIABLE"];

if (isset($_GET["type"])) {
 	$query2="select EQ_ID, EQ_TYPE from equipe where EQ_ID=".intval($_GET["type"]);
  	$result2=mysql_query($query2);
	$row2=mysql_fetch_array($result2);
	$EQ_TYPE=$row2["EQ_TYPE"];
	$EQ_ID=$row2["EQ_ID"];
}

$title="Compétence";
echo "<div align=center><font size=4><b>$title n° $PS_ID </b></font>(type  ".$EQ_NOM.")<br>";

echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<form name='poste' action='save_poste.php'>";
echo "<input type='hidden' name='PS_ID' value='$PS_ID'>";
echo "<input type='hidden' name='NEWPS_ID' value='$PS_ID'>";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='TYPE' value=\"$TYPE\">";
echo "<input type='hidden' name='DESCRIPTION' value=\"$DESCRIPTION\">";
echo "<input type='hidden' name='PO_JOUR' value='0'>";
echo "<input type='hidden' name='PO_NUIT' value='0'>";
echo "<input type='hidden' name='PS_EXPIRABLE' value='0'>";
echo "<input type='hidden' name='PS_AUDIT' value='0'>";
echo "<input type='hidden' name='PS_DIPLOMA' value='0'>";
echo "<input type='hidden' name='PS_SECOURISME' value='0'>";
echo "<input type='hidden' name='PS_NATIONAL' value='0'>";
echo "<input type='hidden' name='PS_PRINTABLE' value='0'>";
echo "<input type='hidden' name='PS_RECYCLE' value='0'>";
echo "<input type='hidden' name='PS_USER_MODIFIABLE' value='0'>";
echo "<input type='hidden' name='F_ID' value='4'>";

//=====================================================================
// ligne 1
//=====================================================================

echo "<tr>
      	  <td colspan=2 class=TabHeader>$title</td>
      </tr>";

//=====================================================================
// ligne type
//=====================================================================


$query2="select EQ_TYPE, EQ_ID, EQ_NOM from equipe";
if ( $gardes == 0 ) $query2 .=" where EQ_TYPE <> 'GARDE'";
$query2 .=" order by EQ_TYPE desc";

$oldEQ_TYPE='';
echo "<tr>
      	<td bgcolor=$mylightcolor ><b>Type <font color=red>*</font></b></td>
      	<td bgcolor=$mylightcolor align=left>
		<select id ='EQ_ID' name='EQ_ID' 
		onchange=\"displaymanager('".$PS_ID."',document.getElementById('EQ_ID').value)\">";
echo "<option value='ALL'>Choisissez un type</option>";
$result2=mysql_query($query2);
while ($row2=@mysql_fetch_array($result2)) {
 	$NEWEQ_ID=$row2["EQ_ID"];
	$NEWEQ_TYPE=$row2["EQ_TYPE"];
	$NEWEQ_NOM=$row2["EQ_NOM"];
 	if ( $NEWEQ_TYPE <> $oldEQ_TYPE) {
 	 	echo "\n<OPTGROUP LABEL=".$NEWEQ_TYPE." style='background-color:$mylightcolor'>";
 	 	$oldEQ_TYPE=$NEWEQ_TYPE;
 	}
 	if ( $NEWEQ_ID == $EQ_ID ) $selected='selected';
 	else $selected='';
    echo "<option value='".$NEWEQ_ID."' $selected style='background-color:#FFFFFF'>".$NEWEQ_NOM."</option>";
}

echo "</select>";
echo "</tr>";

//=====================================================================
// ligne numero
//=====================================================================

for ($i=0 ; $i<=$nbmaxpostes ; $i++) $t[$i]=$i;

$query2="select distinct PS_ID, DESCRIPTION from poste
		 order by PS_ID";
$result2=mysql_query($query2);

while ($row2=@mysql_fetch_array($result2)) {
		 $NEWPS_ID=$row2["PS_ID"];
		 if ($NEWPS_ID <> $PS_ID ) $t[$NEWPS_ID]=0;
}

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Numéro <font color=red>*</font></b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <select name='NEWPS_ID' $disabled>";
		     for ($i=1 ; $i<=$nbmaxpostes ; $i++) {
		     	  if ($t[$i] <> 0) {
				  	if ($i == $PS_ID) $selected="selected";
				  	else $selected="";
		          	echo "<option value='$i' $selected>$i</option>";
	     	     }
	    	}
 	        echo "</select>";
echo "</tr>";

//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor >
			<b>Description <font color=red>*</font></b></font></td>
      	  <td bgcolor=$mylightcolor  align=left >
			<input type='text' name='DESCRIPTION' size='35' value=\"".$DESCRIPTION."\" $disabled>
			";		
echo "</tr>";
      

//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Nom court <font color=red>*</font></b></font></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='TYPE' size='5' value=\"$TYPE\" $disabled>";		
echo "</tr>";

//=====================================================================
// ligne habilitation requise
//=====================================================================

$query2="select distinct F_ID, F_LIBELLE from fonctionnalite
		 where F_ID in (2,4,9,12,13,22,24,25,26,29,30,31,37,46)";
$result2=mysql_query($query2);
echo "<tr>
      	<td bgcolor=$mylightcolor ><b>Habilitation <font color=red>*</font></b></font></td>
      	<td bgcolor=$mylightcolor align=left>
		<select name='F_ID' title='Choisir la permission requise pour pouvoir modifier cette compétence'>";
		while ($row2=@mysql_fetch_array($result2)) {
			if ( $row2[0] == $F_ID ) $selected='selected';
			else $selected='';
		    echo "<option value='".$row2[0]."' $selected>".$row2[0]." - ".$row2[1]."</option>";
        }
 	    echo "</select>";
echo "</tr>";


//=====================================================================
// ligne jour / nuit
//=====================================================================
if ( $EQ_TYPE == 'GARDE' ) {
if (( $PO_JOUR == 1 ) and ( $EQ_JOUR == 1))$checked="checked";
else $checked="";
if ( $EQ_JOUR == 0) $j_disabled="disabled";
else $j_disabled="";

echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Actif le jour</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PO_JOUR'  value='1' $checked $j_disabled>
				</td>";		
echo "</tr>";
if (( $PO_NUIT == 1 ) and ( $EQ_NUIT == 1))$checked="checked";
else $checked="";
if ( $EQ_NUIT == 0) $n_disabled="disabled";
else $n_disabled="";

echo "<tr>
      	  <td bgcolor=$mylightcolor >
			<b>Actif la nuit</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='PO_NUIT'  value='1' $checked $n_disabled>
			</td>";		
echo "</tr>";
}

//=====================================================================
// ligne expirable
//=====================================================================
if ( $EQ_TYPE == 'COMPETENCE' ) {
 if ( $PS_EXPIRABLE == 1 ) $checked="checked";
 else $checked="";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Date d'expiration</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_EXPIRABLE'  value='1' $checked  $disabled >
				<font size=1><i>On peut définir une date d'expiration sur cette compétence</i></font>
				</td>";		
echo "</tr>";
}

//=====================================================================
// ligne audit
//=====================================================================
if ( $EQ_TYPE == 'COMPETENCE' ) {
 if ( $PS_AUDIT == 1 ) $checked="checked";
 else $checked="";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Alerter si modifications</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_AUDIT'  value='1' $checked  $disabled >
				<font size=1><i>Un mail est envoyé au secrétariat en cas de modification</i></font>
				</td>";		
echo "</tr>";
}

//=====================================================================
// ligne diplome
//=====================================================================
if ( $EQ_TYPE == 'COMPETENCE' ) {
 if ( $PS_DIPLOMA == 1 ) $checked="checked";
 else $checked="";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Diplôme délivré</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_DIPLOMA'  value='1' $checked  $disabled>
				<font size=1><i>Un diplôme est délivré après formation</i></font>
				</td>";		
 echo "</tr>";
 if ( $PS_SECOURISME == 1 ) $checked="checked";
 else $checked="";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Secourisme</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_SECOURISME'  value='1' $checked  $disabled>
				<font size=1><i>Compétence officielle de secourisme</i></font>
				</td>";		
 echo "</tr>";
 if ( $PS_NATIONAL == 1 ) $checked="checked";
 else $checked="";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Diplôme national</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_NATIONAL'  value='1' $checked  $disabled>
				<font size=1><i>Diplôme délivré au niveau national seulement</i></font>
				</td>";		
 echo "</tr>";
 if ( $PS_PRINTABLE == 1 ) $checked="checked";
 else $checked="";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Diplôme imprimable</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_PRINTABLE'  value='1' $checked  $disabled>
				<font size=1><i>Possibilité d'imprimer un diplôme</i></font>
				</td>";		
 echo "</tr>";
}
//=====================================================================
// ligne recycle
//=====================================================================
if ( $EQ_TYPE == 'COMPETENCE' ) {
 if ( $PS_RECYCLE == 1 ) $checked="checked";
 else $checked="";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Formation continue</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_RECYCLE'  value='1' $checked  $disabled>
				<font size=1><i>Une formation continue régulière est nécessaire</i></font>
				</td>";		
echo "</tr>";
}

//=====================================================================
// ligne modifiable
//=====================================================================
if ( $EQ_TYPE == 'COMPETENCE' ) {
 if ( $PS_USER_MODIFIABLE == 1 ) $checked="checked";
 else $checked="";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Modifiable</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_USER_MODIFIABLE'  value='1' $checked  $disabled>
				<font size=1><i>Modifiable par chaque utilisateur</i></font>
				</td>";		
echo "</tr>";
}


//=====================================================================
// bas de tableau
//=====================================================================

echo "</table>";
echo "</td></tr></table>";  

if ( $disabled == '' ) {
   echo "<p><input type='submit' value='sauver'> ";
}
echo "</form><form name='poste2' action='save_poste.php'>";
echo "<input type='hidden' name='PS_ID' value='$PS_ID'>";
echo "<input type='hidden' name='operation' value='delete'>";

if ( $disabled == '' ) {
   echo "<input type='submit' value='supprimer'> ";
}

echo "<input type='button' value='Retour' name='annuler' onclick='redirect();'\">";
echo "</form>";
echo "</div>";

?>
