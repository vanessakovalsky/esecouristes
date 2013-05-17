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
function displaymanager(p1){
	 self.location.href="ins_poste.php?EQ_ID="+p1;
	 return true
}
function redirect() {
     cible="poste.php?order=PS_ID&filter=ALL";
     self.location.href=cible;
}
</script>
</head>
<?php
if ( isset($_GET["EQ_ID"])) $MYEQ_ID=intval($_GET["EQ_ID"]);
else if (isset($_SESSION['typequalif'])) $MYEQ_ID=intval($_SESSION['typequalif']);
else {
	$type='ALL';
	$MYEQ_ID=0;
}

if ( $MYEQ_ID > 0 ) {
	$query="select EQ_TYPE from equipe where EQ_ID=".$MYEQ_ID;
	$result=mysql_query($query);
	$row=@mysql_fetch_array($result);
	$type=$row["EQ_TYPE"];
}

$title="Compétence";
echo "<div align=center><font size=4><b>Ajout $title<br></b></font>";
echo "<form name='poste' action='save_poste.php'>";
echo "<input type='hidden' name='PS_ID' value=''>";
echo "<input type='hidden' name='NEWPS_ID' value=''>";
echo "<input type='hidden' name='operation' value='insert'>";
echo "<input type='hidden' name='TYPE' value=''>";
echo "<input type='hidden' name='DESCRIPTION' value=''>";
echo "<input type='hidden' name='PO_JOUR' value='0'>";
echo "<input type='hidden' name='PO_NUIT' value='0'>";
echo "<input type='hidden' name='PS_EXPIRABLE' value='0'>";
echo "<input type='hidden' name='PS_AUDIT' value='0'>";
echo "<input type='hidden' name='PS_DIPLOMA' value='0'>";
echo "<input type='hidden' name='PS_SECOURISME' value='0'>";
echo "<input type='hidden' name='PS_NATIONAL' value='0'>";
echo "<input type='hidden' name='PS_RECYCLE' value='0'>";
echo "<input type='hidden' name='PS_USER_MODIFIABLE' value='0'>";
echo "<input type='hidden' name='PS_PRINTABLE' value='0'>";
echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

//=====================================================================
// ligne 1
//=====================================================================
echo "<tr>
      	  <td colspan=2 class=TabHeader>$title</td>
      </tr>";

//=====================================================================
// ligne type
//=====================================================================

$query="select EQ_TYPE, EQ_ID, EQ_NOM from equipe";
if ( $gardes == 0 ) $query .=" where EQ_TYPE <> 'GARDE'";
$query .=" order by EQ_TYPE desc";

$oldEQ_TYPE='';
echo "<tr>
      	<td bgcolor=$mylightcolor ><b>Type <font color=red>*</font></b></td>
      	<td bgcolor=$mylightcolor align=left>
		<select id ='EQ_ID' name='EQ_ID' 
		onchange=\"displaymanager(document.getElementById('EQ_ID').value)\">";
if ( $type == 'ALL') echo "<option value='ALL'>Choisissez un type</option>";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
 	$EQ_ID=$row["EQ_ID"];
	$EQ_TYPE=$row["EQ_TYPE"];
	$EQ_NOM=$row["EQ_NOM"];
 	if ( $EQ_TYPE <> $oldEQ_TYPE) {
 	 	echo "\n<OPTGROUP LABEL=".$EQ_TYPE." style='background-color:$mylightcolor'>";
 	 	$oldEQ_TYPE=$EQ_TYPE;
 	}
 	if ( $EQ_ID == $MYEQ_ID ) $selected='selected';
 	else $selected='';
    echo "<option value='".$EQ_ID."' ".$selected." style='background-color:#FFFFFF'>".$EQ_NOM."</option>";
}

echo "</select>";
echo "</tr>";

//=====================================================================
// ligne numero
//=====================================================================

if ( $MYEQ_ID > 0 ) {
for ($i=0 ; $i<=$nbmaxpostes ; $i++) $t[$i]=$i;

$query2="select distinct PS_ID, DESCRIPTION from poste
		 order by PS_ID";
$result2=mysql_query($query2);

while ($row2=@mysql_fetch_array($result2)) {
		 $NEWPS_ID=$row2["PS_ID"];
		 $t[$NEWPS_ID]=0;
}

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Numéro <font color=red>*</font></b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <select name='NEWPS_ID'>";
		     for ($i=1 ; $i<=$nbmaxpostes ; $i++) {
		     	  if ($t[$i] <> 0) {
		          	echo "<option value='$i'>$i</option>";
	     	     }
	    	}
 	        echo "</select>";
echo "</tr>";

//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Description <font color=red>*</font></b></font></td>
      	  <td bgcolor=$mylightcolor align=left height=25><input type='text' name='DESCRIPTION' size='25' value=''>";		
echo "</tr>";
      

//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Nom court <font color=red>*</font></b></font></td>
      	  <td bgcolor=$mylightcolor align=left height=25><input type='text' name='TYPE' size='5' value=''>";		
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
			if ( $row2[0] == 4 ) $selected='selected';
			else $selected='';
		    echo "<option value='".$row2[0]."' $selected>".$row2[0]." - ".$row2[1]."</option>";
        }
 	    echo "</select>";
echo "</tr>";

//=====================================================================
// ligne jour / nuit
//=====================================================================
if ($type == 'GARDE' ) {
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Actif le jour</b></font></td>
      	  <td bgcolor=$mylightcolor align=left><input type='checkbox' name='PO_JOUR' value='1' checked >";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Actif la nuit</b></font></td>
      	  <td bgcolor=$mylightcolor align=left><input type='checkbox' name='PO_NUIT' value='1' checked>";		
echo "</tr>";
}

//=====================================================================
// ligne expirable
//=====================================================================
if ($type == 'COMPETENCE' ) {
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Date d'expiration</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_EXPIRABLE'  value='1'>
				<font size=1><i>On peut définir une date d'expiration sur cette compétence</i></font>
				</td>";		
echo "</tr>";
}
//=====================================================================
// ligne audit
//=====================================================================
if ($type == 'COMPETENCE' ) {
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Alerter si modifications</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_AUDIT' value='1'>
				<font size=1><i>Un mail est envoyé au secrétariat en cas de modification</i></font>
				</td>";		
echo "</tr>";
}

//=====================================================================
// ligne diplome
//=====================================================================
if ($type == 'COMPETENCE' ) {
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Diplôme délivré</b></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_DIPLOMA'  value='1'>
				<font size=1><i>Un diplôme est délivré après formation</i></font>
				</td>";		
 echo "</tr>";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Secourisme</b></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_SECOURISME'  value='1'>
				<font size=1><i>Compétence officielle de secourisme</i></font>
				</td>";		
 echo "</tr>";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Diplôme national</b></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_NATIONAL'  value='1'>
				<font size=1><i>Diplôme délivré au niveau national seulement</i></font>
				</td>";		
 echo "</tr>";
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Diplôme imprimable</b></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_PRINTABLE'  value='1'>
				<font size=1><i>Possibilité d'imprimer un diplôme</i></font>
				</td>";		
 echo "</tr>";
}
//=====================================================================
// ligne recycle
//=====================================================================
if ($type == 'COMPETENCE' ){
 echo "<tr>
      	  <td bgcolor=$mylightcolor >
				<b>Formation continue</b></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_RECYCLE'  value='1'>
				<font size=1><i>Une formation continue régulière est nécessaire</i></font>
				</td>";		
echo "</tr>";
}
//=====================================================================
// ligne recycle
//=====================================================================
if ($type == 'COMPETENCE' ){
 echo "<tr>
      	  <td bgcolor=$mylightcolor>
				<b>Modifiable</b></td>
      	  <td bgcolor=$mylightcolor align=left>
				<input type='checkbox' name='PS_USER_MODIFIABLE'  value='1'>
				<font size=1><i>Modifiable par chaque utilisateur</i></font>
				</td>";		
echo "</tr>";
}
}
echo "</table>";
echo "</td></tr></table>";  
echo "<p><input type='submit' value='sauver'></form>";
echo "<input type='button' value='Annuler' name='annuler' onclick='redirect();'\"></div>";

?>
