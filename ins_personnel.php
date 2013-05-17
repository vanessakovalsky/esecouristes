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
check_all(0);
$section=$_SESSION['SES_SECTION'];

if (isset ($_GET["suggestedsection"])) $suggestedsection=intval($_GET["suggestedsection"]);
else $suggestedsection=$section;

if (isset ($_GET["suggestedcompany"])) $suggestedcompany=intval($_GET["suggestedcompany"]);
else $suggestedcompany=0;

if (isset ($_GET["category"])) $statut=$_GET["category"];
else $statut='';

if ( $statut == 'EXT' ) {
 	$mylightcolor=$mygreencolor;
 	check_all(37);
 	$mysection=get_highest_section_where_granted($_SESSION['id'],37);
}
else { // internes
 	check_all(1);
 	$mysection=get_highest_section_where_granted($_SESSION['id'],1);
 	$suggestedcompany=0;
}

if ( check_rights($_SESSION['id'], 24) ) $section='0';
else if ( $mysection <> '' ) {
 	if ( is_children($section,$mysection)) 
 		$section=$mysection;
}

writehead();
echo "<script type='text/javascript' src='checkForm.js'></script>";
?>
<script>
function changedType() {
 	var type = document.getElementById('statut');
 	var ts=document.getElementById('type_salarie');
 	var h=document.getElementById('heures');
 	var tsRow = document.getElementById('tsRow');
 	var gRow = document.getElementById('gRow');
 	var cRow2 = document.getElementById('cRow2');
 	var iRow = document.getElementById('iRow');
	var uRow0 = document.getElementById('uRow0');
 	var uRow1 = document.getElementById('uRow1');
 	var uRow2 = document.getElementById('uRow2');
 	var uRow3 = document.getElementById('uRow3');
 	var uRow4 = document.getElementById('uRow4');
 	var aRow = document.getElementById('aRow');
 	var yRow = document.getElementById('yRow');
    if (type.value == 'SAL') {
		tsRow.style.display = '';	
	} else {
		ts.value='0';
		h.value='';
		tsRow.style.display = 'none';
	}
	if (type.value == 'EXT') {
	 	gRow.style.display = '';
	 	cRow2.style.display = 'none';
	 	iRow.style.display = 'none';
		uRow0.style.display = 'none';
	 	uRow1.style.display = 'none';
	 	uRow2.style.display = 'none';
	 	uRow3.style.display = 'none';
	 	uRow4.style.display = 'none';
	 	aRow.style.display = 'none';
	 	yRow.style.display = '';
	}
	else {
	 	gRow.style.display = '';
	 	cRow2.style.display = '';
		iRow.style.display = '';
		uRow1.style.display = '';
		uRow1.style.display = '';
	 	uRow2.style.display = '';
	 	uRow3.style.display = '';
	 	uRow4.style.display = '';
	 	aRow.style.display = '';
	 	yRow.style.display = '';
	}
}
function changedSalarie() {
 	var ts=document.getElementById('type_salarie');
 	var h=document.getElementById('heures');
    if (ts.value == 'TC') {
		h.value='35';
	}
}

</script>
<?php
echo "</head>";
echo "<body onload='changedType();'>";

//=====================================================================
// affiche la fiche personnel
//=====================================================================
$disabled="";

echo "<div align=center><font size=4><b>Ajouter une personne<br></b></font>";


echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<form name='personnel' action='save_personnel.php'>";
echo "<input type='hidden' name='P_ID' value='100'>";
echo "<input type='hidden' name='operation' value='insert'>";
echo "<input type='hidden' name='habilitation' value='0'>";
echo "<input type='hidden' name='habilitation2' value='-1'>";
echo "<input type='hidden' name='old_member' value='0'>";

//=====================================================================
// ligne 1
//=====================================================================

echo "<tr class=TabHeader>
      	   <td width=400 colspan=2>Informations obligatoires</td>
      </tr>";
      
echo "<tr>
      	  <td bgcolor=$mylightcolor width=400 colspan=2></td>";		
echo "</tr>";
//=====================================================================
// ligne grade
//=====================================================================

if ( $grades == 1 ) {      	  

  $query2="select G_GRADE, G_DESCRIPTION from grade
      	 order by G_LEVEL ASC";
  $result2=mysql_query($query2);

  echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Grade</b></td>
      	  <td bgcolor=$mylightcolor align=left>
		<select name='grade' $disabled>";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $G_GRADE=$row2["G_GRADE"];
			      $G_DESCRIPTION=$row2["G_DESCRIPTION"];
		          echo "<option value='$G_GRADE'>$G_DESCRIPTION</option>";
	     	     }
 	        echo "</select>";
echo "</tr>";
}
     
//=====================================================================
// ligne type
//=====================================================================

$query2="select S_STATUT, S_DESCRIPTION from statut 
         where S_CONTEXT =".$nbsections;

if ( $statut == 'EXT' ) {
 	$query2 .= " and S_STATUT='EXT'";
}
else {
 	$query2 .= " and S_STATUT <> 'EXT'";
}

$result2=mysql_query($query2);

echo "<tr>
      	<td bgcolor=$mylightcolor><b>Statut</b> <font color=red>*</font></td>
      	<td bgcolor=$mylightcolor align=left>
		<select name='statut' id='statut' $disabled onchange=\"javascript:changedType();\">";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $S_STATUT=$row2["S_STATUT"];
            	  $S_DESCRIPTION=$row2["S_DESCRIPTION"];
            	  $selected='';
            	  if ( $statut == 'EXT' )
            	     if ( $S_STATUT == 'EXT' ) $selected='selected'; 
            	  else
            	  	 if (( $S_STATUT == 'BEN' ) or ( $S_STATUT == 'SPV' )) $selected='selected';  
		          echo "<option value='$S_STATUT' $selected>$S_DESCRIPTION</option>";
	     	     }
 	        echo "</select>";
 	       
$query2="select TS_CODE, TS_LIBELLE from type_salarie order by TS_CODE asc";
$result2=mysql_query($query2);
echo "</tr>";

if ( $nbsections == 0 ) {
echo "<tr id='tsRow'>
	  <td bgcolor=$mylightcolor><b>Salarié</b> <font color=red>*</font></td>
	  <td bgcolor=$mylightcolor align=left>";
echo " <select name='type_salarie' id='type_salarie'
			onchange=\"javascript:changedSalarie();\"
			title='A préciser pour le personnel salarié seulement'>";
echo "<option value='0'>---choisir---</option>";
	 while ($row2=@mysql_fetch_array($result2)) {
		          $TS_CODE=$row2["TS_CODE"];
            	  $TS_LIBELLE=$row2["TS_LIBELLE"]; 
		          echo "<option value='$TS_CODE'>$TS_LIBELLE</option>";
	     	     }
echo "</select>";
 	        
echo " <i><font size=1>heures / semaine</font></i> 
			<input type='text' name='heures' id='heures' size='3' value=''
		  		title='A préciser pour le personnel salarié seulement'
		  		onchange='checkNumber(form.heures,\"\");'>";
echo "</tr>";
}
else {
 echo "<tr id='tsRow' style='display:none'>
 	<input type='hidden' id='type_salarie' value=''>
 	<input type='hidden' id='heures' value=''>
	</tr>";
}

//=====================================================================
// ligne nom
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Nom</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='nom' size='20' value='' $disabled onchange='isValid3(form.nom);'>";		
echo "</tr>";
      
//=====================================================================
// ligne prénom
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Prénom</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='prenom' size='20' value='' $disabled onchange='isValid3(form.prenom);'>";		
echo "</tr>";


//=====================================================================
// ligne matricule
//=====================================================================

echo "<tr id=iRow>
      	  <td bgcolor=$mylightcolor><b>";
if ( $grades == 1) echo "Matricule";
else echo "Identifiant";
echo "</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='matricule' size='10' value='' $disabled onchange='isValid(form.matricule);' >";		
echo "</tr>";

//=====================================================================
// ligne sexe
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Sexe</b> <font color=red>*</font></font></td>
      	  <td bgcolor=$mylightcolor align=left>
      	  <label for='M'>Masculin</label><input type='radio' name='sexe' id='M' value='M' checked /> 
		  <label for='F'>Féminin</label><input type='radio' name='sexe' id='F' value='F'  />";		
echo "</tr>";


//=====================================================================
// section
//=====================================================================

if (  $nbsections == 1 ) {
	echo "<input type='hidden' name='groupe' value='0'>";
}

else{
	echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Section</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>";
 	echo "<select id='groupe' name='groupe' $disabled>";
 	
 	$level=get_level($section);
 	if ( $level == 0 ) $mycolor=$myothercolor;
	elseif ( $level == 1 ) $mycolor=$my2darkcolor;
    elseif ( $level == 2 ) $mycolor=$my2lightcolor;
    elseif ( $level == 3 ) $mycolor=$mylightcolor;
    else $mycolor='white';
    $class="style='background: $mycolor;'";
	echo "<option value='$section' $class >".
		str_repeat(". ",$level)." ".get_section_code($section)." - ".get_section_name($section)."</option>";
	display_children2($section, $level +1, $suggestedsection, $nbmaxlevels);
 	
	echo "</select></td> ";
	echo "</tr>";	  
}

//=====================================================================
// company
//=====================================================================

if (  $nbsections == 0 ) {
echo "<tr id='yRow'>
      	  <td bgcolor=$mylightcolor><b>Entreprise</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>";
      	  
echo "<select id='company' name='company' $disabled>";      	  
echo companychoice($section,$suggestedcompany, true, $statut);
echo "</select>";
echo "</td></tr>";	  
}
else echo "<input type='hidden' name='company' id='company' value='0'>";

//=====================================================================
// habilitations appli
//=====================================================================


# can grant admin only if granted on 9
$query2="select GP_ID, GP_DESCRIPTION, GP_USAGE from groupe where GP_ID < 100";

if ( $statut == 'EXT' ) 
	$query2 .= "  and GP_USAGE in ('all','externes')";
else 
	$query2 .= "  and GP_USAGE in ('all','internes')";	

if (! check_rights($_SESSION['id'], 9) )
    $query2 .="   and not exists (select 1 from habilitation h, fonctionnalite f
					where f.F_ID = h.F_ID
					and f.F_TYPE = 2
					and h.GP_ID= groupe.GP_ID)";        

if (! check_rights($_SESSION['id'], 46) )
    $query2 .="   and not exists (select 1 from habilitation h, fonctionnalite f
					where f.F_ID = h.F_ID
					and f.F_TYPE = 3
					and h.GP_ID= groupe.GP_ID)";        

$query2 .="   order by GP_ID asc";

$result2=mysql_query($query2);

if ((check_rights($_SESSION['id'], 9) ) or (check_rights($_SESSION['id'], 25) ))
	$disabled2=""; 
else $disabled2="disabled";

echo "<tr id=gRow>
      	  <td bgcolor=$mylightcolor><b>Groupe</b> <font color=red>*</font>
		  <a href=habilitations.php>".$miniquestion_pic."</a></td>
      	  <td bgcolor=$mylightcolor align=left>
		
		 <select name='habilitation' $disabled2>";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $GP_ID=$row2["GP_ID"];
			  	  $GP_DESCRIPTION=$row2["GP_DESCRIPTION"];
			  	  $GP_USAGE=$row2["GP_USAGE"];
			  	  
			  	  if ($statut == 'EXT') $default=-1;
			  	  else $default=0;
			  	  if ($GP_ID == $default ) $selected='selected';
			  	  else $selected='';

		          echo "<option value='$GP_ID' $selected>$GP_DESCRIPTION</option>";
	     	 }
 	        echo "</select></td>";
echo "</tr>";

//=====================================================================
// ligne premier engagement
//=====================================================================

$curyear=date("Y");
$year=$curyear - 50; 
echo "<tr id='aRow'>
      	  <td bgcolor=$mylightcolor><b>Année engagement</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<select name='debut' $disabled>";
		while ( $year <= $curyear + 1 ) {
			if ( $year == $curyear ) $selected = 'selected';
			else $selected = '';
			echo "<option value='$year' $selected>$year</option>";
			$year++;
		}		
echo "</select></tr>";

//=====================================================================
// intercalaire
//=====================================================================

echo "<tr class=TabHeader>
      	   <td width=300 colspan=2>Informations personnelles</td>
      </tr>";

//=====================================================================
// ligne date de naissance
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>date de naissance</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='birth' size='10' value='' $disabled onchange='checkDate(form.birth)'>
			<font size=1>JJ/MM/AAAA</font></td>";		
echo "</tr>";

//=====================================================================
// lieu de naissance
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>lieu de naissance</b></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <input type='text' name='birthplace' size='25' value='' $disabled></td>";		
echo "</tr>";
//=====================================================================
// ligne email
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor width=300 colspan=2></td>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>E-Mail</td>
      	  <td bgcolor=$mylightcolor align=left>
      	  	<input type='text' name='email' size='25' $disabled
			value='' onchange='mailCheck(form.email,\"\")'></td>";	
echo "</tr>";
      
//=====================================================================
// ligne phone
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Téléphone portable</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='phone' size='12' value='' $disabled onchange='checkPhone(form.phone,\"\")'> Numéro abrégé
      	  <input type='text' name='abbrege' size='5' value='' $disabled></td>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Autre Téléphone</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='phone2' size='12
			' value='' $disabled onchange='checkPhone(form.phone2,\"\")'>";		
echo "</tr>";
//=====================================================================
// ligne address
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Adresse</td>
      	  <td bgcolor=$mylightcolor align=left>
			<textarea name='address' cols='20' rows='3' value='' style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;' ></textarea></td>";
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Code postal</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='zipcode' size='6' value=''
			onchange='checkZipcode(form.zipcode,\"\")'></td>";
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Ville</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='city' size='20' value=''></td>";
echo "</tr>";

echo "<tr id=uRow0>
      	  <td bgcolor=$mylightcolor align=right>Contact Skype</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='skype' size='20' value=''></td>";
echo "</tr>";

//=====================================================================
// ligne contact
//=====================================================================
echo "<tr id=uRow1>
      	  <td colspan=2 bgcolor=$mylightcolor width=300 align=left><b>Personne à prévenir en cas d'urgence</b></td>";
echo "</tr>";

echo "<tr id=uRow2>
      	  <td bgcolor=$mylightcolor align=right>Nom</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='relation_nom' size='20' value=''></td>";
echo "</tr>";
echo "<tr id=uRow3>
      	  <td bgcolor=$mylightcolor align=right>Prénom</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='relation_prenom' size='20' value=''></td>";
echo "</tr>";
echo "<tr id=uRow4>
      	  <td bgcolor=$mylightcolor align=right>Téléphone</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='relation_phone' size='12' 
			value='' onchange='checkPhone(form.relation_phone,\"\")'></td>";
echo "</tr>";


//=====================================================================
// hide my contact infos?
//=====================================================================
$checked="";
echo "<tr id=cRow2>
      	  <td bgcolor=$mylightcolor align=right>Infos de contact</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='hide'  value='1' $checked title='Si cette case est cochée, seules certaines personnes habilitées pourront voir mes informations de contact'>
			<i> Masquer au public</i></td>";		
	echo "</tr>";


echo "</td></tr></table>";// end left table
echo "</td></tr></table>"; // end cadre
echo "<p><input type='submit' value='sauver'>";
echo " <input type='button' value='Annuler' name='annuler' onclick=\"javascript:history.back(1);\"></form></div>";

?>
