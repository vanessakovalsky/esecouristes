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

$eqid=intval($_GET["pid"]);

writehead();
?>
<script language="JavaScript">
function displaymanager(p1,p2){
	 self.location.href="upd_equipe.php?pid="+p1+"&type="+p2;
	 return true
}
</script>
</head>
<?php

$disabled="";
if ( check_rights($_SESSION['id'], 18)) $disabled=""; else $disabled="disabled";

//=====================================================================
// affiche la fiche equipe
//=====================================================================

$query="select e.EQ_ID, e.EQ_JOUR, e.EQ_NUIT , e.EQ_NOM, e.S_ID, e.S_ID_DATE, e.EQ_DUREE, e.EQ_TYPE 
	     from equipe e
	     where e.EQ_ID=".$eqid;	
$result=mysql_query($query);
$row=mysql_fetch_array($result);

$EQ_ID=$row["EQ_ID"];
$EQ_NOM=$row["EQ_NOM"];
$S_ID=$row["EQ_NOM"];
$S_ID_DATE=$row["EQ_NOM"];
$EQ_JOUR=$row["EQ_JOUR"];
$EQ_NUIT=$row["EQ_NUIT"];
$EQ_DUREE=$row["EQ_DUREE"];
$EQ_TYPE=$row["EQ_TYPE"];

if ( isset($_GET["type"])) $type=$_GET["type"];
else $type=$EQ_TYPE;

$title="Type de Compétence";
echo "<div align=center><font size=4><b>$title n° $EQ_ID - $EQ_NOM</b></font>";

echo "<form name='equipe' action='save_equipe.php'>";
echo "<p><table>
<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<input type='hidden' name='EQ_ID' value='$EQ_ID'>";
echo "<input type='hidden' name='NEWEQ_ID' value='$EQ_ID'>";
echo "<input type='hidden' name='EQ_NOM' value=\"$EQ_NOM\">";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='EQ_JOUR' value='0'>";
echo "<input type='hidden' name='EQ_NUIT' value='0'>";
echo "<input type='hidden' name='duree' value='12'>";
echo "<input type='hidden' name='S_ID_DATE' value='$S_ID_DATE'>";
echo "<input type='hidden' name='S_ID' value='$S_ID'>";
echo "<input type='hidden' name='EQ_TYPE' value='$EQ_TYPE'>";

$query2="select distinct CEV_CODE from categorie_evenement";
$result2=mysql_query($query2);
while ($row=@mysql_fetch_array($result2)) {
	echo "<input type='hidden' name='".$row["CEV_CODE"]."' value='0'>";
}

//=====================================================================
// ligne 1
//=====================================================================

echo "<tr>
	<td colspan=2 class=TabHeader>infos</td>
      </tr>";

//=====================================================================
// ligne numero
//=====================================================================

for ($i=0 ; $i<=$nbmaxequipes ; $i++) $t[$i]=$i;

$query2="select distinct EQ_ID, EQ_NOM from equipe";
$result2=mysql_query($query2);

while ($row2=@mysql_fetch_array($result2)) {
		 $NEWEQ_ID=$row2["EQ_ID"];
		 if ($NEWEQ_ID <> $EQ_ID ) $t[$NEWEQ_ID]=0;
}

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150><b>Numéro <font color=red>*</font></b></td>
      	  <td bgcolor=$mylightcolor width=250 align=left>
		  <select name='NEWEQ_ID' $disabled>";
		     for ($i=1 ; $i<=$nbmaxequipes ; $i++) {
		     	  if ($t[$i] <> 0) {
				  	if ($i == $EQ_ID) $selected="selected";
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
      	  <td bgcolor=$mylightcolor ><b>Description <font color=red>*</font></b></td>
      	  <td bgcolor=$mylightcolor align=left height=25>
			<input type='text' name='EQ_NOM' size='35' value=\"$EQ_NOM\">";		
echo "</tr>";

//=====================================================================
// ligne affichable sur evenements
//=====================================================================
if ($type == 'COMPETENCE' ) {
  $query2="select distinct ce.CEV_CODE, ce.CEV_DESCRIPTION, cea.FLAG1 
			from categorie_evenement ce, categorie_evenement_affichage cea
			where ce.CEV_CODE=cea.CEV_CODE
			and cea.EQ_ID=".$EQ_ID;
  $result2=mysql_query($query2);
  while ($row=@mysql_fetch_array($result2)) {
		$CEV_CODE=$row["CEV_CODE"];
		$CEV_DESCRIPTION=$row["CEV_DESCRIPTION"];
		$FLAG1=$row["FLAG1"];
		if ( $FLAG1 == 1 ) $checked="checked";
  		else $checked="";
  		echo "<tr>
      	  <td bgcolor=$mylightcolor >
			<b>Afficher</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='".$CEV_CODE."'  value='1' $checked
			title=\"cocher si ces compéténces de ce type doivent être affichées sur les événements de cette catégorie\" >
			<font size=1><i>".$CEV_DESCRIPTION."</i></font>
		  </td>";		
  		echo "</tr>";
   }
}

//=====================================================================
// ligne type
//=====================================================================
if ( $gardes == 1 ) {
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Type <font color=red>*</font></b></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <select id='EQ_TYPE' name='EQ_TYPE' disabled>";
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

//=====================================================================
// ligne jour / nuit
//=====================================================================
if ( $type == 'GARDE' ) {
if ( $EQ_JOUR == 1 )$checked="checked";
else $checked="";

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Actif le jour</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='EQ_JOUR'  value='1' $checked>";		
echo "</tr>";
if ( $EQ_NUIT == 1)$checked="checked";
else $checked="";

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Actif la nuit</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='EQ_NUIT'  value='1' $checked>";		
echo "</tr>";
}




//=====================================================================
// ligne section assurant ce type de garde aujourd'hui
//=====================================================================
if (( $type == 'GARDE' ) and ( $nbsections == 3 )) {	

    $section_today=get_section_pro_jour($EQ_ID,date("Y"), date("n"), date("d"));
    echo "<tr>
      	  <td bgcolor=$mylightcolor >Assurée aujourd'hui par
			</font></td>
      	  <td bgcolor=$mylightcolor align=left>";

    echo "<select id='section' name='section' $disabled>";
	$query2="select S_ID, S_DESCRIPTION from section order by S_ID";
	$result2=mysql_query($query2);
	while ($row=@mysql_fetch_array($result2)) {
      $NEWS_ID=$row["S_ID"];
      $S_DESCRIPTION=$row["S_DESCRIPTION"];
      echo "<option value='".$NEWS_ID."'";
      if ($NEWS_ID == $section_today ) echo " selected ";
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

//=====================================================================
// ligne postes
//=====================================================================

echo "<tr>
      	  <td colspan=2 class=TabHeader>
			<a href=poste.php?filter=$EQ_ID&order=PS_ID class=TabHeader>Compétences de ce type</a></td>";		
echo "</tr>";
      
$queryp="select PS_ID, TYPE, DESCRIPTION
	     from  poste p
	     where EQ_ID=$EQ_ID";
$resultp=mysql_query($queryp);
while ($rowp=@mysql_fetch_array($resultp)) {
      $PS_ID=$rowp["PS_ID"];
      $TYPE=$rowp["TYPE"];
      $DESCRIPTION=strip_tags($rowp["DESCRIPTION"]);
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b> $PS_ID</b></td>
      	  <td bgcolor=$mylightcolor align=left><a href=upd_poste.php?pid=$PS_ID>$DESCRIPTION</a>";		
	echo "</tr>";
}

//=====================================================================
// bas de tableau
//=====================================================================
echo "</table>";
echo "</td></tr></table>";
if ( $disabled == '') {
   echo "<p><input type='submit' value='sauver'> ";
}
echo "</form><form name='equipe2' action='save_equipe.php'>";
echo "<input type='hidden' name='EQ_ID' value='$EQ_ID'>";
echo "<input type='hidden' name='NEWEQ_ID' value='$NEWEQ_ID'>";
echo "<input type='hidden' name='EQ_JOUR' value='$EQ_JOUR'>";
echo "<input type='hidden' name='EQ_NUIT' value='$EQ_NUIT'>";
echo "<input type='hidden' name='EQ_TYPE' value='$EQ_TYPE'>";
echo "<input type='hidden' name='duree' value='$EQ_DUREE'>";
echo "<input type='hidden' name='EQ_NOM' value=\"$EQ_NOM\">";
$query2="select distinct CEV_CODE from categorie_evenement";
$result2=mysql_query($query2);
while ($row=@mysql_fetch_array($result2)) {
	echo "<input type='hidden' name='".$row["CEV_CODE"]."' value='0'>";
}
echo "<input type='hidden' name='operation' value='delete'>";

if ( $disabled == '') {
   echo "<input type='submit' value='supprimer'> ";
}

echo "<input type='button' value='Retour' name='annuler' onclick=\"javascript:history.back(1);\">";
echo "</form>";
echo "</div>";
?>
