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
if ( isset($_GET["type"])) $type=$_GET["type"];
else $type='ALL';
?>
<script language="JavaScript">
function displaymanager(p1){
	 self.location.href="ins_equipe.php?type="+p1;
	 return true
}
function redirect() {
     cible="equipe.php";
     self.location.href=cible;
}
</script>
</head>
<?php

$title="Type de Compétence";

echo "<div align=center><font size=4><b>Ajout $title<br></b></font>";

echo "<form name='équipe' action='save_equipe.php'>";
echo "<input type='hidden' name='NEWEQ_ID' value=''>";
echo "<input type='hidden' name='operation' value='insert'>";
echo "<input type='hidden' name='EQ_JOUR' value='0'>";
echo "<input type='hidden' name='EQ_NUIT' value='0'>";
echo "<input type='hidden' name='EQ_DISPLAY_ON_EVENTS' value='0'>";
echo "<input type='hidden' name='EQ_ID' value=''>";
echo "<input type='hidden' name='S_ID_DATE' value=''>";
echo "<input type='hidden' name='S_ID' value=''>";
echo "<input type='hidden' name='duree' value='0'>";

$query2="select distinct CEV_CODE from categorie_evenement";
$result2=mysql_query($query2);
while ($row=@mysql_fetch_array($result2)) {
	echo "<input type='hidden' name='".$row["CEV_CODE"]."' value='0'>";
}

//=====================================================================
// ligne 1
//=====================================================================

echo "<p><table>
<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<tr class=TabHeader>
      	   <td align=right width=150></td>
      	  <td align=right width=250>$title</td>
      </tr>";

//=====================================================================
// ligne type
//=====================================================================
if ( $gardes == 1 ) {
	echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Type <font color=red>*</font></b></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <select id ='EQ_TYPE' name='EQ_TYPE' 
		  onchange=\"displaymanager(document.getElementById('EQ_TYPE').value)\">";
	if ( $type == 'ALL') echo "<option value='ALL'>Choisissez un type</option>";

	if ( $type == 'GARDE') $selected='selected';
	else  $selected='';
	echo "<option value='GARDE' $selected>GARDE - Postes de gardes</option>";

	if ( $type == 'COMPETENCE') $selected='selected';
	else  $selected='';
	echo "<option value='COMPETENCE' $selected>COMPETENCE - Compétences ou diplômes</option>";
	echo "</select>";
	echo "</td>
 	 </tr>";
}
else {
	$type='COMPETENCE';
	echo "<input type='hidden' name='EQ_TYPE' value='COMPETENCE'>";
}

if ( $type <> 'ALL' ) {
//=====================================================================
// ligne numero
//=====================================================================

for ($i=0 ; $i<=$nbmaxequipes ; $i++) $t[$i]=$i;

$query2="select distinct EQ_ID, EQ_NOM from equipe
		 order by EQ_ID";
$result2=mysql_query($query2);

while ($row2=@mysql_fetch_array($result2)) {
		 $NEWEQ_ID=$row2["EQ_ID"];
		 $t[$NEWEQ_ID]=0;
}

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Numéro <font color=red>*</font></b></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <select name='NEWEQ_ID'>";
		     for ($i=1 ; $i<=$nbmaxequipes ; $i++) {
		     	  if ($t[$i] <> 0) {
				  	if ($i == $EQ_ID) $selected="selected";
				  	else $selected="";
		          	echo "<option value='$i'>$i</option>";
	     	     }
	    	}
 	        echo "</select>";
echo "</tr>";


//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Description <font color=red>*</font></b></td>
      	  <td bgcolor=$mylightcolor align=left height=25><input type='text' name='EQ_NOM' size='25' value=''>";		
echo "</tr>";

//=====================================================================
// ligne affichable sur evenements
//=====================================================================
if ($type == 'COMPETENCE' ) {
  $query2="select distinct CEV_CODE, CEV_DESCRIPTION
			from categorie_evenement";
  $result2=mysql_query($query2);
  while ($row=@mysql_fetch_array($result2)) {
		$CEV_CODE=$row["CEV_CODE"];
		$CEV_DESCRIPTION=$row["CEV_DESCRIPTION"];
  		echo "<tr>
      	  <td bgcolor=$mylightcolor >
			<b>Afficher</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='".$CEV_CODE."'  value='1'
			title=\"cocher si ces compéténces de ce type doivent être affichées sur les événements de cette catégorie\" >
			<font size=1><i>".$CEV_DESCRIPTION."</i></font>
		  </td>";		
  		echo "</tr>";
   }
}

//=====================================================================
// ligne jour / nuit
//=====================================================================
if ($type == 'GARDE' ) {
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Actif le jour</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='checkbox' name='EQ_JOUR' value='1' checked >";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Actif la nuit</b></font></td>
      	  <td bgcolor=$mylightcolor align=left><input type='checkbox' name='EQ_NUIT' value='1' checked>";		
echo "</tr>";
}

//=====================================================================
// ligne section assurant ce type de garde aujourd'hui
//=====================================================================
if (( $type == 'GARDE' ) and ( $nbsections == 3 )) {	

    echo "<tr>
      	  <td bgcolor=$mylightcolor >Assurée aujourd'hui par
			</font></td>
      	  <td bgcolor=$mylightcolor align=left>";

    echo "<select id='section' name='section'>
	<option value='0'>toutes sections</option>";
	$query2="select S_ID, S_DESCRIPTION from section";
	$result2=mysql_query($query2);
	while ($row=@mysql_fetch_array($result2)) {
      $NEWS_ID=$row["S_ID"];
      $S_DESCRIPTION=$row["S_DESCRIPTION"];
      echo "<option value='".$NEWS_ID."'";
      if ($NEWS_ID == 1 ) echo " selected ";
      echo ">".$S_DESCRIPTION."</option>\n";
	}
	echo "</select></td> ";
					
    echo "</tr>";
}

//=====================================================================
// durée du poste en heures
//=====================================================================
if ( $type == 'GARDE' ) {	
    echo "<tr>
      	  <td bgcolor=$mylightcolor >Durée (heures J ou N)
			</font></td>
      	  <td bgcolor=$mylightcolor align=left>";

    echo "<select id='duree' name='duree' title='duree en heures de présence pour le jour ou pour la nuit'>";
    
	for ( $i=0; $i <= 12; $i++ ) {
		if ( $i == $EQ_DUREE ) $selected="selected";
		else $selected="";
		echo "<option value=".$i." $selected>".$i."</option>\n";
	}
	echo "</select></td> ";		
    echo "</tr>";
}
}
echo "</table>";
echo "</td></tr></table>";

if ( $type <> 'ALL' ) 
echo "<input type='submit' value='sauver'>";

echo "</form>";
echo "<input type='button' value='Annuler' name='annuler' onclick='redirect()';></div>";

?>
