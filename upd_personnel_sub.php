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

header('Content-Type: text/html; charset=ISO-8859-1');
include_once ("config.php");
check_all(0);
writehead();

if ( isset($_GET["id"])) $pompier=intval($_GET["id"]);
else $pompier=intval($_GET["pompier"]);
if ( isset($_GET["from"]))$from=$_GET["from"];
else $from="default";
if ( isset($_GET["tab"]))$tab=$_GET["tab"];
else $tab="1";
$SES_NOM=$_SESSION['SES_NOM'];
$SES_PRENOM=$_SESSION['SES_PRENOM'];
$SES_GRADE=$_SESSION['SES_GRADE'];
$browser=$_SESSION['SES_BROWSER'];
$section=$_SESSION['SES_SECTION'];

$id=$_SESSION['id'];
$mycompany=$_SESSION['SES_COMPANY'];
if ($id == $pompier) $allowed=true;
else if ( $mycompany == get_company($pompier) and check_rights($_SESSION['id'], 45) and $mycompany > 0) {
	$allowed=true;
}
else check_all(40);

if ( isset ( $_GET['order'])) {
	$order = mysql_real_escape_string($_GET['order']);
	$tab=3;
}
else $order='PS_ID';

// check input parameters
$pompier=intval(mysql_real_escape_string($pompier));
if ( $pompier == 0 ) {
	param_error_msg();
	exit;
}

$iphone=is_iphone();
	
//=====================================================================
// affiche la fiche personnel
//=====================================================================
$P_SECTION=0;
$query="select distinct p.P_CODE ,p.P_ID , p.P_NOM , p.P_PRENOM, p.P_GRADE, p.P_HIDE, p.P_SEXE,
		   DATE_FORMAT(p.P_BIRTHDATE, '%d/%m/%Y') as P_BIRTHDATE , p.P_BIRTHPLACE, p.P_OLD_MEMBER,
		   g.G_DESCRIPTION as P_DESCRIPTION, p.GP_ID2, DATE_FORMAT(p.P_LAST_CONNECT,'%d-%m-%Y %H:%i') P_LAST_CONNECT, p.P_NB_CONNECT,
	       p.P_STATUT, s1.S_DESCRIPTION as P_DESC_STATUT ,P_DEBUT, G_TYPE, P_SECTION, P_SKYPE,
	       s2.S_DESCRIPTION as P_DESC_SECTION, gp.GP_DESCRIPTION , gp.GP_ID, 
		   p.P_EMAIL, p.P_PHONE, p.P_PHONE2, p.P_ABBREGE, p.P_UPDATED_BY, DATE_FORMAT(p.P_FIN,'%d-%m-%Y') as P_FIN,
	       p.P_ADDRESS, p.P_ZIP_CODE, p.P_CITY, p.P_CREATED_BY, DATE_FORMAT(p.P_CREATE_DATE,'%d-%m-%Y' ) P_CREATE_DATE,
	       p.TS_CODE, p.TS_HEURES, p.P_NOSPAM, p.C_ID,
		   p.P_RELATION_NOM, p.P_RELATION_PRENOM, p.P_RELATION_PHONE, p.P_PHOTO,
		   EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),P_BIRTHDATE))))+0 AS age,
		   p.GP_FLAG1, p.GP_FLAG2
         from pompier p , grade g, statut s1, section s2, groupe gp
	 where p.P_GRADE=g.G_GRADE
	 and s2.S_ID=p.P_SECTION
	 and s1.S_STATUT=p.P_STATUT
	 and gp.GP_ID=p.GP_ID
	 and p.P_ID=".$pompier;	
	 
$result=mysql_query($query);

// check input parameters
if ( mysql_num_rows($result) <> 1 ) {
 	param_error_msg();
	exit;
}

$row=mysql_fetch_array($result);
$P_CODE=$row["P_CODE"];
$P_ID=$pompier;
$P_HIDE=$row["P_HIDE"];
$P_SEXE=$row["P_SEXE"];
$P_PRENOM=$row["P_PRENOM"];
$P_NOM=$row["P_NOM"];
$P_GRADE=$row["P_GRADE"];
$P_BIRTHDATE=$row["P_BIRTHDATE"];
$P_BIRTHPLACE=$row["P_BIRTHPLACE"];
$P_DESC_STATUT=$row["P_DESC_STATUT"];
$P_DEBUT=$row["P_DEBUT"];
$P_FIN=$row["P_FIN"];
$P_OLD_MEMBER=$row["P_OLD_MEMBER"];
$P_CREATED_BY=$row["P_CREATED_BY"];
$P_CREATE_DATE=$row["P_CREATE_DATE"];
$P_UPDATED_BY=$row["P_UPDATED_BY"];
$P_STATUT=$row["P_STATUT"];
$P_DESCRIPTION=$row["P_DESCRIPTION"];
$G_TYPE=$row["G_TYPE"];
$P_SECTION=$row["P_SECTION"];
$C_ID=$row["C_ID"];
$P_DESC_SECTION=$row["P_DESC_SECTION"];
$P_GP_DESCRIPTION=$row["GP_DESCRIPTION"];
$P_GP_DESCRIPTION=$row["GP_DESCRIPTION"];
$P_GP_ID=$row["GP_ID"];
$P_GP_ID2=$row["GP_ID2"];
$P_EMAIL=$row["P_EMAIL"];
$P_PHONE=$row["P_PHONE"];
$P_PHONE2=$row["P_PHONE2"];
$P_ABBREGE=$row["P_ABBREGE"];
$P_ADDRESS=$row["P_ADDRESS"];
$P_SKYPE=$row["P_SKYPE"];
$P_ZIP_CODE=$row["P_ZIP_CODE"];
$P_CITY=$row["P_CITY"];
$P_RELATION_NOM=$row["P_RELATION_NOM"];
$P_RELATION_PRENOM=$row["P_RELATION_PRENOM"];
$P_RELATION_PHONE=$row["P_RELATION_PHONE"];
$P_PHOTO=$row["P_PHOTO"];
$P_LAST_CONNECT=$row["P_LAST_CONNECT"];
$P_NB_CONNECT=$row["P_NB_CONNECT"];
$AGE=$row["age"];
$GP_FLAG1=$row["GP_FLAG1"];
$GP_FLAG2=$row["GP_FLAG2"];
$TS_CODE=$row["TS_CODE"];
$TS_HEURES=$row["TS_HEURES"];
$P_NOSPAM=$row["P_NOSPAM"];

if ( $P_OLD_MEMBER > 0 ) {
	$mylightcolor=$mygreycolor;
}

if ( $P_STATUT == 'EXT' ) {
	$mylightcolor=$mygreencolor;
}

// permettre les modifications si je suis habilité sur la fonctionnalité 2
// (et si la personne fait partie de mes sections filles ou alors je suis habilité sur la fonctionnalité 24 )
if (check_rights($_SESSION['id'], 37,"$P_SECTION") and $P_STATUT == 'EXT') $update_allowed=true;
else if (check_rights($_SESSION['id'], 2,"$P_SECTION") and $P_STATUT <> 'EXT') $update_allowed=true;
else $update_allowed=false;

if (check_rights($_SESSION['id'], 3,"$P_SECTION")) $delete_allowed=true;
else $delete_allowed=false;

// permission de modifier les compétences?
$competence_allowed=false;
$query="select distinct F_ID from poste order by F_ID";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
	if (check_rights($_SESSION['id'], $row['F_ID'],"$P_SECTION")) {
		$competence_allowed=true;
		break;
	}
}
$change_formation_allowed=false;
if (check_rights($_SESSION['id'], 4,"$P_SECTION")) $change_formation_allowed=true;


if ($update_allowed) $disabled="";
else $disabled="disabled";

if ($update_allowed) $disabled_del="";
else $disabled_del="disabled";

if (( ($P_HIDE == 1) )
	and (! $update_allowed ) 
	and ( $pompier <> $id )
	and (! check_rights($_SESSION['id'], 12)))
$disabled_infos="disabled";
else $disabled_infos="";

// ne pas afficher au 'public' les infos concernant la personne a prévenir en cas d'urgence
// mais toujours visible dans le code source de la page pour ne pas bloquer les formulaires.
if (    (! $update_allowed )  
	and ( $nbsections == 0 )
	and ( $pompier <> $id )
	and (! check_rights($_SESSION['id'], 12)))
$hide_contacturgence=" style=\"display:none;\" ";
else $hide_contacturgence="";

if ( $id == $pompier) $disabled_matricule='';
else $disabled_matricule=$disabled;

if ( $tab == 1 ) {
//=====================================================================
// ligne photo
//=====================================================================
echo "<div style=\"float:right;\">";
if($P_PHOTO!=""){
	if(file_exists($trombidir."/".$P_PHOTO)) {
		echo "<img src=\"".$trombidir."/".$P_PHOTO."\" 
		border=\"0\" width=\"100\" alt=\"$P_PHOTO\" title=\"\">";
	}
	else {
		echo "Photo non trouvée sur le serveur.";
	}
	if ( $disabled_matricule == '' ) {
		echo "<br /><a href=\"upd_personnel_photo.php?pompier=$P_ID&photo=$P_PHOTO\" 
		onclick=\"window.open($(this).attr('href'),'_blank','resizable=yes,scrollbars=yes,width=500,height=500');return false;\">
		Modifier la photo</a>";
	}
}
else {
	if ( $disabled_matricule == '' ) {
		echo "<br /><a href=\"upd_personnel_photo.php?pompier=$P_ID\" 
		onclick=\"window.open($(this).attr('href'),'_blank','resizable=yes,scrollbars=yes,width=500,height=500');return false;\">
		Ajouter une photo</a>";
	}
}
echo "</div>";

//=====================================================================
// table information personnel
//=====================================================================
echo "<form name='personnel' action='save_personnel.php'>";
echo "<input type='hidden' name='P_ID' value='$P_ID'>";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='activite' value='$P_OLD_MEMBER'>";
echo "<input type='hidden' name='groupe' value='$P_SECTION'>";

if ( $P_CREATED_BY <> '' ) 
	$author = "<font size=1><i> - fiche ajoutée par ".ucfirst(get_prenom($P_CREATED_BY))." ".strtoupper(get_nom($P_CREATED_BY))."
			   le ". $P_CREATE_DATE."
				</i></font>";
else 
	$author='';

echo "<table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table bgcolor=$mylightcolor cellspacing=0 border=0>";
echo "<tr class=TabHeader>
      	   <td align=left>Informations obligatoires ".$author."</td>
      	   <td></td>
      	   <td align=left>Informations personnelles</td>
      </tr>";

echo "<tr>";
echo "<td valign=top><table cellspacing=0 border=0 bgcolor=$mylightcolor>";

//=====================================================================
// ligne grade
//=====================================================================
if ( $grades == 1 ) {      	  

  $query2="select G_GRADE, G_DESCRIPTION from grade
         where G_GRADE != '$P_GRADE'
      	 order by G_LEVEL desc";
  $result2=mysql_query($query2);

  echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Grade</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
		<select name='grade' $disabled>
		     <option value='$P_GRADE' selected>$P_DESCRIPTION</option>";
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
         where S_CONTEXT =".$nbsections."
		 and S_STATUT <> '".$P_STATUT."'" ;
		 

if (! check_rights($_SESSION['id'], 37) ){
 	$query2 .= "and S_STATUT <> 'EXT'";
}
         
$result2=mysql_query($query2);
echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Statut</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <select name='statut' id='statut' $disabled onchange=\"javascript:changedType();\">
		     <option value='$P_STATUT' selected>$P_DESC_STATUT</option>";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $S_STATUT=$row2["S_STATUT"];
            	  $S_DESCRIPTION=$row2["S_DESCRIPTION"];
		          echo "<option value='$S_STATUT'>$S_DESCRIPTION</option>";
	     	     }
 	        echo "</select>";
echo "</tr>";

$query2="select TS_CODE, TS_LIBELLE from type_salarie order by TS_CODE asc";
$result2=mysql_query($query2);
echo "</tr>";

if ( $nbsections == 0 ) {
 
if ( $P_STATUT == 'SAL' ) $style="";
else  $style="style='display:none'";
 
echo "<tr id='tsRow' $style>
	  <td bgcolor=$mylightcolor><b>Salarié</b> <font color=red>*</font></td>
	  <td bgcolor=$mylightcolor align=left>";
echo " <select name='type_salarie' id='type_salarie' $disabled
			onchange=\"javascript:changedSalarie();\"
			title='A préciser pour le personnel salarié seulement'>";
echo "<option value='0'>---choisir---</option>";
	 while ($row2=@mysql_fetch_array($result2)) {
		          $NTS_CODE=$row2["TS_CODE"];
            	  $NTS_LIBELLE=$row2["TS_LIBELLE"]; 
            	  if ( $TS_CODE == $NTS_CODE ) $selected='selected';
            	  else $selected='';
		          echo "<option value='$NTS_CODE' $selected>$NTS_LIBELLE</option>";
	     	     }
echo "</select>";
 	        
echo " <i><font size=1>heures / semaine</font></i> 
			<input type='text' name='heures' id='heures' size='3' value='".$TS_HEURES."'
		  		title='A préciser pour le personnel salarié seulement'
		  		onchange='checkNumber(form.heures,\"".$TS_HEURES."\");' $disabled>";
echo "</tr>";
}
else {
 $style="style='display:none'";
 echo "<tr id='tsRow' $style>
 	<input type='hidden' id='type_salarie' value=''>
 	<input type='hidden' id='heures' value=''>
	</tr>";
}

//=====================================================================
// ligne nom
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Nom</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='nom' size='20' value=\"$P_NOM\" $disabled onchange='isValid3(personnel.nom);' >";		
echo "</tr>";
      
//=====================================================================
// ligne prénom
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Prénom</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='prenom' size='20' value=\"$P_PRENOM\" $disabled onchange='isValid3(personnel.prenom);' >";		
echo "</tr>";


//=====================================================================
// ligne matricule
//=====================================================================

//if ( $P_STATUT == 'EXT' ) $style="style='display:none'";
//else  $style="";

echo "<tr id=iRow>
      	  <td bgcolor=$mylightcolor><b>";
if ( $grades == 1) echo "Matricule";
else echo "Identifiant";
echo "</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>
		<input type='text' name='matricule' size='20' value=\"$P_CODE\" $disabled_matricule
		onchange='isValid(form.matricule);' >";		
echo "</tr>";

//=====================================================================
// ligne sexe
//=====================================================================

$checked1='';$checked2='';
if ( $P_SEXE == 'M' ) $checked1='checked';
if ( $P_SEXE == 'F' ) $checked2='checked';
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Sexe</b></font> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
      	  <label for='M'>Masculin</label><input type='radio' name='sexe' id='M' value='M' $checked1 $disabled_matricule/> 
		  <label for='F'>Féminin</label><input type='radio' name='sexe' id='F' value='F' $checked2 $disabled_matricule/>";		
echo "</tr>";

//=====================================================================
// section
//=====================================================================

if (  $nbsections == 1 ) {
	echo "<input type='hidden' name='groupe' value='$P_SECTION'>";
}
else {
	if ( check_rights($_SESSION['id'], 44) ) 
		$section_info="<a href=upd_section.php?S_ID=".$P_SECTION." title='Voir informations sur cette section'>Section</a></b> <font color=red>*</font>";
	else 
		$section_info='Section';
 	echo "<tr>
      	  <td bgcolor=$mylightcolor><b>
			$section_info</td>
      	  <td bgcolor=$mylightcolor align=left>";
 	 
   
   if ( $update_allowed ) {
       if ( $P_STATUT == 'EXT' )  $mysection=get_highest_section_where_granted($_SESSION['id'],37);
       else $mysection=get_highest_section_where_granted($_SESSION['id'],2);
       if ( $mysection == '' ) $mysection=$P_SECTION;
       if ( ! is_children($section,$mysection)) $mysection=$section;
       if ( check_rights($_SESSION['id'], 24) ) $mysection='0';
   }
   else $mysection=$P_SECTION;
   
   echo "<select id='groupe' name='groupe' $disabled>";
   $level=get_level($mysection);
   if ( $level == 0 ) $mycolor=$myothercolor;
   elseif ( $level == 1 ) $mycolor=$my2darkcolor;
   elseif ( $level == 2 ) $mycolor=$my2lightcolor;
   elseif ( $level == 3 ) $mycolor=$mylightcolor;
   else $mycolor='white';
   $class="style='background: $mycolor;'";
   if ( check_rights($_SESSION['id'], 24))
   	  display_children2(-1, 0, $P_SECTION, $nbmaxlevels);
   else {
   		echo "<option value='$mysection' $class >".str_repeat(". ",$level)." ".
      		get_section_code($mysection)." - ".get_section_name($mysection)."</option>";
   		if ( $disabled == '') display_children2($mysection, $level +1, $P_SECTION, $nbmaxlevels);
   }
   echo "</select></td> ";
   echo "</tr>";	
   
   //=====================================================================
   // company
   //=====================================================================

   if (  $nbsections == 0 ) {
   		echo "<tr id='yRow'>
      	  <td bgcolor=$mylightcolor><b>";
   		if ( ($C_ID > 0) and (check_rights($_SESSION['id'], 37)))
	   		echo "<a href=upd_company.php?C_ID=".$C_ID." title='Voir informations sur cette entreprise'>Entreprise</a></b> <font color=red>*</font>";
   		else
	   		echo "Entreprise</b> <font color=red>*</font>";
  		 echo "</td>
      	  <td bgcolor=$mylightcolor align=left>";  	  
   		echo "<select id='company' name='company' $disabled width=>";
   		echo companychoice($mysection,$C_ID, true, $P_STATUT);
   		echo "</select>";
   		echo "</td></tr>";
   }
   else echo "<input type='hidden' name='company' id='company' value='0'>";
}

//=====================================================================
// habilitations appli
//=====================================================================

# can grant admin only if granted on 9
$query2="select GP_ID, GP_DESCRIPTION, GP_USAGE from groupe where GP_ID < 100";

if ( $P_STATUT == 'EXT' ) 
	$query2 .= "  and GP_USAGE in ('all','externes')";
else 
	$query2 .= "  and GP_USAGE in ('all','internes')";
	
if (! check_rights($_SESSION['id'], 9)) {
    $query2 .="   and not exists (select 1 from habilitation h, fonctionnalite f
					where f.F_ID = h.F_ID
					and f.F_TYPE = 2
					and h.GP_ID= groupe.GP_ID
					and groupe.GP_ID <> $P_GP_ID";
	if ($P_GP_ID2 <> ""	) $query2 .=" and groupe.GP_ID <> $P_GP_ID2 "; 
	$query2 .=" )";
}

if (! check_rights($_SESSION['id'], 46)) {
    $query2 .="   and not exists (select 1 from habilitation h, fonctionnalite f
					where f.F_ID = h.F_ID
					and f.F_TYPE = 3
					and h.GP_ID= groupe.GP_ID
					and groupe.GP_ID <> $P_GP_ID";
	if ($P_GP_ID2 <> ""	) $query2 .=" and groupe.GP_ID <> $P_GP_ID2 "; 
	$query2 .=" )";
}

$query2 .="   order by GP_ID asc";
$result2=mysql_query($query2);

if (($update_allowed) and 
 ((check_rights($_SESSION['id'], 9)) or (check_rights($_SESSION['id'], 25))))
	$disabled2="";
else $disabled2="disabled";

if (check_rights($_SESSION['id'], 2,get_section_parent("$P_SECTION"))) $disabled3='';
else $disabled3="disabled";

if ( $nbsections <> 1 ) 
	$pic="<a href=habilitations.php>".$miniquestion_pic."</a>";
else $pic="";

echo "<input type='hidden' name='habilitation' value='$P_GP_ID'>";
echo "<tr id=gRow>
    <td bgcolor=$mylightcolor><b>Permissions </b> <font color=red>*</font>
	".$pic."</td>
    <td bgcolor=$mylightcolor align=left>
	 <select name='habilitation' $disabled2>";
$found=false;
while ($row2=@mysql_fetch_array($result2)) {
	$GP_ID=$row2["GP_ID"];
	$GP_DESCRIPTION=$row2["GP_DESCRIPTION"];
	if ( $P_GP_ID == $GP_ID ) {
		$selected='selected';
		$found=true;
	}
	else $selected='';
	echo "<option value='$GP_ID' $selected >".$GP_DESCRIPTION."</option>";
}
if (! $found ) 
	echo "<option value='$P_GP_ID' selected>".$P_GP_DESCRIPTION."</option>";
echo "</select>";
 	        
if ( $GP_FLAG1 == 1 ) $checked="checked";
else $checked="";
			
if ( $P_STATUT == 'EXT' ) $style="style='display:none'";
else  $style="";
			
if ( $nbsections <> 1) 
echo " <input type=checkbox id='flag1' name='flag1' value='1' $style $disabled2 $disabled3 $checked 
	title=\"Si coché, les droits s'appliquent au niveau supérieur à la section d'appartenance\">
	</td>";
 	        
echo "</tr>";

$result2=mysql_query($query2);

if ( $P_GP_ID2 <> '' ) {
	$query3="select GP_DESCRIPTION from groupe where GP_ID=".$P_GP_ID2;
	$result3=mysql_query($query3);
	$row3=@mysql_fetch_array($result3);
	$P_GP_DESCRIPTION2=$row3["GP_DESCRIPTION"];
} 
else {
 $P_GP_ID2=0;
 $P_GP_DESCRIPTION2="aucun";
}

$found=false;
echo "<input type='hidden' name='habilitation2' value='$P_GP_ID2'>";
echo "<tr id=gRow2>
    <td bgcolor=$mylightcolor><b>Permissions 2</b> <font color=red>*</font></td>
    <td bgcolor=$mylightcolor align=left>
	<select name='habilitation2' $disabled2>";
while ($row2=@mysql_fetch_array($result2)) {
	$GP_ID=$row2["GP_ID"];
	$GP_DESCRIPTION=$row2["GP_DESCRIPTION"];
	if ( $P_GP_ID2 == $GP_ID ) {
		$selected='selected';
		$found=true;
	}
	else $selected='';
	// ne pas proposer -1 ici, pour les externes réduire les choix
	//if ($GP_ID >= 0 or $P_GP_ID2 == $GP_ID )
	echo "<option value='$GP_ID' $selected>".$GP_DESCRIPTION."</option>";
}
if (! $found ) 
	echo "<option value='$P_GP_ID2' selected>".$P_GP_DESCRIPTION2."</option>";
echo "</select>";

if ( $P_STATUT == 'EXT' ) $style="style='display:none'";
else  $style="";
 	        
if ( $GP_FLAG2 == 1 ) $checked="checked";
else $checked="";
if ( $nbsections <> 1) 
echo " <input type=checkbox name='flag2' value='1' $disabled2 $disabled3 $checked $style
		title=\"Si coché, les droits s'appliquent au niveau supérieur à la section d'appartenance\">
		</td>";
echo "</tr>";

//=====================================================================
// ligne premier engagement
//=====================================================================

$curyear=date("Y");
$year=$curyear - 50; 
$found=false;
echo "<tr id=aRow $style>
      	  <td bgcolor=$mylightcolor><b>Année engagement</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<select name='debut' $disabled>";
		while ( $year <= $curyear + 1 ) {
			if ( $year == $P_DEBUT ) {
				$selected = 'selected';
				$found=true;
			}
			else $selected = '';
			echo "<option value='$year' $selected>$year</option>";
			$year++;
		}
		if ( ! $found ) echo "<option value='$P_DEBUT' selected>$P_DEBUT</option>";
		
echo "</select></tr>";
//=====================================================================
// ancien membre
//=====================================================================

if (( $nbsections == 0 ) and ($update_allowed)) {
 	// seuls les chefs de sections et adjoints (sauf niveau antenne locale) 
	// ou admin (9) ou habilités sécurité locale (25) (sauf niveau antenne locale)
	// peuvent modifier le statut des membres
	$disabled2='disabled';
	if (check_rights($_SESSION['id'], 9)) $disabled2='';
	elseif((check_rights($_SESSION['id'], 25, "$P_SECTION")))
			$disabled2='';
}
else $disabled2 = $disabled;

$query2="select TM_ID, TM_CODE from type_membre";
$result2=mysql_query($query2);

echo "<tr id=pRow $style>
      	  <td bgcolor=$mylightcolor><b>Actif / Ancien </b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <select name='activite' $disabled2>";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $TM_ID=$row2["TM_ID"];
            	  $TM_CODE=$row2["TM_CODE"];
            	  if ( $TM_ID == $P_OLD_MEMBER ) $selected='selected';
            	  else  $selected='';
		          echo "<option value='$TM_ID' $selected>$TM_CODE</option>";
	     	     }
 	        echo "</select>";
echo "</tr>";

if ( $P_OLD_MEMBER > 0 ) {
   if ( $P_FIN <> "" ) 
   echo "<tr $style>
      	  <td bgcolor=$mylightcolor align=right><i>Modifié le: </i></td>
      	  <td bgcolor=$mylightcolor align=left> ".$P_FIN."</td>
      	  </tr>";	
   if ( $P_UPDATED_BY <> "")
   echo "<tr $style>
      	  <td bgcolor=$mylightcolor align=right><i>Modifié par: </i></td>
      	  <td bgcolor=$mylightcolor align=left> 
			<a href=upd_personnel.php?pompier=$P_UPDATED_BY >
			".ucfirst(get_prenom($P_UPDATED_BY))." ".strtoupper(get_nom($P_UPDATED_BY))."</a></td>
      	  </tr>";

}

//=====================================================================
// identifiant ebrigade
//=====================================================================
if ( $nbsections == 0 ) {
	if (isset($application_title_specific)) $application_title=$application_title_specific;
	echo "<tr>
      	  <td bgcolor=$mylightcolor align=left><b>N° membre $application_title</b></td>
      	  <td bgcolor=$mylightcolor align=left>".$P_ID."</td>
      	  </tr>";
}
//=====================================================================
// connexions
//=====================================================================
if ( $P_STATUT <> 'EXT' ) {
	$query2="select DATE_FORMAT(min(P_LAST_CONNECT),'%d-%m-%Y') MIN_LAST_CONNECT from pompier";
	$result2=mysql_query($query2);
	$row2=mysql_fetch_array($result2);
	$MIN_LAST_CONNECT=$row2["MIN_LAST_CONNECT"];

	if ( $P_LAST_CONNECT <> "" ) {
		echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Dernière connexion le</b></td>
      	  <td bgcolor=$mylightcolor align=left><font size=1> ".$P_LAST_CONNECT." 
			 <a title='".$P_NB_CONNECT." connexions depuis le ".$MIN_LAST_CONNECT."'>(".$P_NB_CONNECT.")</a></font></td>
      	  </tr>";	
	}
	else  {
		echo "<tr>
      	  <td bgcolor=$mylightcolor align=right><font size=1><i>Aucune connexion depuis le: </i></font></td>
      	  <td bgcolor=$mylightcolor align=left><font size=1> ".$MIN_LAST_CONNECT."</font></td>
      	  </tr>";	 
 
	}

//=====================================================================
// positions éventuelles
//=====================================================================
   	echo "<tr class=TabHeader>
      	   <td colspan=2 >Rôles dans l'organigramme</td>
      </tr>";
	//organigramme
	$query2="select s.S_ID, s.S_CODE, s.S_DESCRIPTION, g.GP_DESCRIPTION
		from section_role sr, section s , groupe g
		where sr.P_ID=".$P_ID." 
		and sr.GP_ID = g.GP_ID
		and sr.S_ID = s.S_ID";
	$result2=mysql_query($query2);
	if ( mysql_num_rows($result2) == 0 ) {
	 	echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Aucun.</b></td>
      	  <td bgcolor=$mylightcolor align=left></td>";
		echo "</tr>";
	 
	}
	else {
    while ($row2=@mysql_fetch_array($result2)) {
 		$S_ID=$row2["S_ID"];
		$S_CODE=$row2["S_CODE"];
		$S_DESCRIPTION=$row2["S_DESCRIPTION"];
		$GP_DESCRIPTION=$row2["GP_DESCRIPTION"];
		
		// cas specifique association, pas de président sur les antennes
		if (( get_level("$S_ID") + 1 == $nbmaxlevels ) and ( $nbsections == 0 )) {
			if ( $GP_DESCRIPTION == "Président (e)" ) $GP_DESCRIPTION="Responsable d'antenne";
			if ( $GP_DESCRIPTION == "Vice président (e)" ) $GP_DESCRIPTION="Responsable adjoint";
		}
		
		echo "<tr>
      	  <td bgcolor=$mylightcolor><b>".$GP_DESCRIPTION." de</b></td>
      	  <td bgcolor=$mylightcolor align=left><a href=upd_section.php?S_ID=$S_ID>
			<font size=1>".$S_CODE." - ".$S_DESCRIPTION."</a></td>";
		echo "</tr>";	
 		}
	}
	
	// entreprises
	$query2="select c.C_ID, cr.TCR_CODE, tcr.TCR_DESCRIPTION, c.C_NAME
		from company_role cr, company c, type_company_role tcr
		where cr.P_ID=".$P_ID." 
		and cr.TCR_CODE = tcr.TCR_CODE
		and cr.C_ID = c.C_ID";
	$result2=mysql_query($query2);
	if ( mysql_num_rows($result2) <> 0 ) {
   		echo "<tr class=TabHeader>
      	   <td colspan=2 >Rôles dans les entreprises</td>
      </tr>";
    while ($row2=@mysql_fetch_array($result2)) {
 		$C_ID=$row2["C_ID"];
		$TCR_CODE=$row2["TCR_CODE"];
		$TCR_DESCRIPTION=$row2["TCR_DESCRIPTION"];
		$C_NAME=$row2["C_NAME"];
		
		echo "<tr>
      	  <td bgcolor=$mylightcolor><b>".$TCR_DESCRIPTION."</b></td>
      	  <td bgcolor=$mylightcolor align=left><a href=upd_company.php?C_ID=$C_ID>
			<font size=1>".$C_NAME."</a></td>";
		echo "</tr>";	
 		}
	}
	
	
}

echo "</table></td>";
echo "<td width=0 bgcolor=$mydarkcolor></td>";

// partie droite
echo "<td valign=top><table cellspacing=0 border=0 bgcolor=$mylightcolor>";

//=====================================================================
// ligne contact infos
//=====================================================================
if ( $disabled_infos == "disabled") {
		echo "<tr>
      	  <td bgcolor=$mylightcolor colspan=2>
			Vous n'avez pas le droit de voir<br> les informations pour cette personne.</td>";		
		echo "</tr>";
}
else {

if ( $pompier == $id ) $disabled='';

//=====================================================================
// ligne date de naissance
//=====================================================================
if ( $AGE <> "") $cmt=" <b>($AGE ans)</b>";
else $cmt="";

echo "<tr >
      	  <td bgcolor=$mylightcolor><b>date de naissance</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='birth' size='10' value='".$P_BIRTHDATE."' $disabled onchange='checkDate(personnel.birth)'>
			<font size=1><i>JJ/MM/AAAA</i></font>".$cmt."</td>";		
echo "</tr>";

//=====================================================================
// lieu de naissance
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>lieu de naissance</b></td>
      	  <td bgcolor=$mylightcolor align=left>
		  <input type='text' name='birthplace' size='25' value=\"$P_BIRTHPLACE\" $disabled></td>";		
echo "</tr>";

//=====================================================================
// ligne email
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor colspan=2></td>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>E-Mail</td>
      	  <td bgcolor=$mylightcolor align=left>	
			<input type='text' name='email' size='35' $disabled
			value='$P_EMAIL' onchange='mailCheck(form.email,\"".$P_EMAIL."\")'>";	
echo "</td>";
echo "</tr>";
      
//=====================================================================
// ligne phone
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Téléphone portable</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='phone' size='12' value='$P_PHONE' $disabled onchange='checkPhone(personnel.phone,\"".$P_PHONE."\")'>
			Abrégé <input type='text' name='abbrege' size='5' value='$P_ABBREGE' $disabled>";		
echo "</tr>";

//=====================================================================
// ligne phone 2
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Autre Téléphone</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='phone2' size='12' value='$P_PHONE2' $disabled 
			onchange='checkPhone(form.phone2,\"".$P_PHONE2."\")'>";		
echo "</tr>";
    


//=====================================================================
// ligne address
//=====================================================================

$map="";
if ( $P_ADDRESS <> "" ) {
	$querym="select count(*) as NB from geolocalisation where TYPE='P' and CODE=".$P_ID;
	$resultm=mysql_query($querym);
	$rowm=mysql_fetch_array($resultm);
	if ( $rowm["NB"] == 0 ) gelocalize($P_ID, 'P');
	$resultm=mysql_query($querym);
	$rowm=mysql_fetch_array($resultm);
	if ( $rowm["NB"] == 1 ) $map="<a href=map.php?type=P&code=".$P_ID." target=_blank><img src=images/mapsmall.png title='Voir la carte Google Maps' border=0></a>";
}


echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Adresse</td>
      	  <td bgcolor=$mylightcolor align=left>
			<textarea name='address' cols='25' rows='2' 
			style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;'
			value=\"$P_ADDRESS\"  $disabled>".$P_ADDRESS."</textarea> ".$map."</td>";
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Code postal</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='zipcode' size='6' value='$P_ZIP_CODE'  $disabled
			onchange='checkZipcode(personnel.zipcode,\"".$P_ZIP_CODE."\")'></td>";
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Ville</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='city' size='20' value=\"$P_CITY\"  $disabled></td>";
echo "</tr>";

echo "<tr id=uRow0 $style>
      	  <td bgcolor=$mylightcolor align=right>Contact Skype</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='skype' size='20' value=\"$P_SKYPE\"  $disabled></td>";
echo "</tr>";

//=====================================================================
// ligne contact
//=====================================================================
echo "<tr id=uRow1 $style>
      	  <td colspan=2 bgcolor=$mylightcolor align=left><b>Personne à prévenir en cas d'urgence</b>".(($hide_contacturgence<>"")?" (confidentiel)":"")."</td>";
echo "</tr>";

echo "<tr $hide_contacturgence id=uRow2 $style>
      	  <td bgcolor=$mylightcolor align=right>Nom</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='relation_nom' size='20' value='$P_RELATION_NOM' $disabled></td>";
echo "</tr>";
echo "<tr $hide_contacturgence id=uRow3 $style>
      	  <td bgcolor=$mylightcolor align=right>Prénom</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='relation_prenom' size='20' value='$P_RELATION_PRENOM' $disabled></td>";
echo "</tr>";
echo "<tr $hide_contacturgence id=uRow4 $style>
      	  <td bgcolor=$mylightcolor align=right>Téléphone</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='relation_phone' size='12' 
			value='$P_RELATION_PHONE' $disabled onchange='checkPhone(form.relation_phone,\"".$P_RELATION_PHONE."\")'></td>";  
echo "</tr>";

//=====================================================================
// hide my contact infos?
//=====================================================================
if ( $P_HIDE == 1 ) $checked="checked";
else $checked="";
echo "<tr $hide_contacturgence id=cRow2 $style>
      	  <td bgcolor=$mylightcolor align=right>Infos de contact</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='hide'  value='1' $disabled $checked title='Si cette case est cochée, seules certaines personnes habilitées pourront voir les informations de contact'>
			<i> Masquer au public</i></td>";		
echo "</tr>";

//=====================================================================
// no spam?
//=====================================================================
if ( $P_NOSPAM == 1 ) $checked="checked";
else $checked="";
echo "<tr id=sRow2 $style>
      	  <td bgcolor=$mylightcolor align=right>Notifications</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='nospam'  value='1' $disabled $checked 
			title=\"Si cette case est cochée, les mails de notifications destinés aux responsables (création d'événements, changement de statut du personnel ou des véhicules, changements de compétences ...) ne seront pas envoyées\">
			<i> Ne pas recevoir</i></td>";		
echo "</tr>";

}

echo "</table></td></tr>";

echo "</table></td></tr></table>";

if ( $disabled == "") {
  if (( $P_STATUT == 'EXT'  and  check_rights($_SESSION['id'], 37))
  	or ( $P_STATUT <> 'EXT'  and check_rights($_SESSION['id'], 2)))
   		echo "<p><input type='submit' value='sauver'> ";
  elseif ( $pompier == $id )
   		echo "<p><input type='submit' value='sauver'> ";

  if ( $P_EMAIL <> "" ) 
  	if ( check_rights($id, 25,"$section") or check_rights($id, 9))
  	echo " <input type='button' value='Envoyer nouveau mot de passe' 
  			title=\"Envoyer un mail à ".ucfirst($P_PRENOM)." ".strtoupper($P_NOM)." avec son identifiant de connexion et un nouveau mot de passe généré automatiquement.\"
			onclick=\"bouton_redirect('send_id.php?pid=".$P_ID."')\"> ";
}

echo "</form>";
echo "<form name='personnel2' action='save_personnel.php'>";
if (( $disabled_del == "") and ( check_rights($_SESSION['id'], 3))) {
	echo "<input type='hidden' name='P_ID' value='$P_ID'>";
	echo "<input type='hidden' name='matricule' value='$P_CODE'>";
	echo "<input type='hidden' name='grade' value='$P_GRADE'>";
	echo "<input type='hidden' name='sexe' value='$P_SEXE'>";
	echo "<input type='hidden' name='statut' value='0'>";
	echo "<input type='hidden' name='nom' value=''>";
	echo "<input type='hidden' name='prenom' value=''>";
	echo "<input type='hidden' name='debut' value='0'>";
	echo "<input type='hidden' name='birth' value=''>";
	echo "<input type='hidden' name='birthplace' value=''>";
	echo "<input type='hidden' name='groupe' value='0'>";
	echo "<input type='hidden' name='email' value='0'>";
	echo "<input type='hidden' name='phone' value='0'>";
	echo "<input type='hidden' name='phone2' value='0'>";
	echo "<input type='hidden' name='abbrege' value='0'>";

	echo "<input type='hidden' name='address' value='0'>";
	echo "<input type='hidden' name='city' value='0'>";
	echo "<input type='hidden' name='zipcode' value='0'>";
	echo "<input type='hidden' name='relation_prenom' value='0'>";
	echo "<input type='hidden' name='relation_nom' value='0'>";
	echo "<input type='hidden' name='relation_phone' value='0'>";
    echo "<input type='hidden' name='activite' value='0'>";
    echo "<input type='hidden' name='hide' value='0'>";
    
	echo "<input type='hidden' name='habilitation' value='0'>";
	echo "<input type='hidden' name='habilitation2' value='0'>";
	echo "<input type='hidden' name='operation' value='delete'>";
    echo "<input type='submit' value='supprimer'> ";
}


if ( $from == 'export' ) {
	echo "<input type='button' value='fermer cette page' onclick='fermerfenetre();'> ";
}
elseif  ( $from == 'created' ) {
    if ( ! $iphone)
		echo "<input type='button' name='annuler' value='Retour' onclick=\"javascript:history.go(-3);\">";
}
else  {
 	if ( ! $iphone)
		echo "<input type='button' id='annuler' name='annuler' value='Retour'
				onclick=\"history.back(1);\">";
}
echo "</form>";

if($nbsections == 0 && $P_EMAIL != '' && $P_OLD_MEMBER == 0 && check_rights($_SESSION['id'], 43)){
	echo  "<form name=\"FrmEmail\" method=\"post\" action=\"mail_create.php\">
	<input type=\"hidden\" name=\"SelectionMail\" value=\"$P_ID\" />";
	echo "Contact: ";
    echo "<input type='submit' value='message' title=\"envoi de message à partir de l'application web\"/>";
	
	if (( $P_STATUT == 'EXT'  and  check_rights($_SESSION['id'], 37))
  	or ( $P_STATUT <> 'EXT'  and check_rights($_SESSION['id'], 2))) {
		$subject="Message de ".str_replace("'","",ucfirst(get_prenom($id))." ".strtoupper(get_nom($id)));
		echo " <input type='button' value='mailto' 
		onclick='parent.location=\"mailto:".$P_EMAIL."?subject=$subject\"' 
		title=\"envoi de message à partir de votre logiciel de messagerie\"/>";
	}
    echo "</form>";
}

}

if ( $tab == 2 ) {
//=====================================================================
// Table compétences
//=====================================================================

echo "<table bgcolor=$mylightcolor cellspacing=0 border=0>"; 

echo "<table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<tr class=TabHeader>
      	   <td width=350 colspan=2>Compétences</td>
      </tr>";

//=====================================================================
// affectations
//=====================================================================

$OLDEQ_NOM="NULL";
$query2="select e.EQ_ID, e.EQ_NOM, p.PS_ID, TYPE, p.DESCRIPTION, q.Q_VAL, e.EQ_TYPE,
		 DATE_FORMAT(q.Q_EXPIRATION, '%d / %m / %Y') as Q_EXPIRATION, p.PS_DIPLOMA, p.PS_RECYCLE,
		 DATEDIFF(q.Q_EXPIRATION,NOW()) as NB,
		 q.Q_UPDATED_BY, DATE_FORMAT(q.Q_UPDATE_DATE, '%d-%m-%Y %k:%i') Q_UPDATE_DATE
         from equipe e, poste p, qualification q
	     where q.PS_ID=p.PS_ID
	     and e.EQ_ID=p.EQ_ID
	     and q.P_ID=".$P_ID."
	     union 
	     select e.EQ_ID, e.EQ_NOM, p.PS_ID, TYPE, p.DESCRIPTION, -1 as Q_VAL, e.EQ_TYPE,
	     null as Q_EXPIRATION, p.PS_DIPLOMA, p.PS_RECYCLE, 0 as NB,
		 null as Q_UPDATED_BY, null as Q_UPDATE_DATE
	     from equipe e, poste p, personnel_formation pf
	     where pf.PS_ID=p.PS_ID
	     and e.EQ_ID=p.EQ_ID
	     and pf.P_ID=".$P_ID."
	     and not exists (select 1 from qualification q where q.PS_ID = p.PS_ID and q.P_ID = pf.P_ID)
	     order by EQ_ID, PS_ID";

$result2=mysql_query($query2);
while ($row2=@mysql_fetch_array($result2)) {
    $show=true;
	$EQ_NOM=$row2["EQ_NOM"];
 	$PS_ID=$row2["PS_ID"];
 	$TYPE=$row2["TYPE"];
 	$EQ_TYPE=$row2["EQ_TYPE"];
 	$NB=$row2["NB"];
 	$Q_VAL=$row2["Q_VAL"];
 	$Q_UPDATED_BY=$row2["Q_UPDATED_BY"];
 	$Q_UPDATE_DATE=$row2["Q_UPDATE_DATE"];
 	$PS_DIPLOMA=$row2["PS_DIPLOMA"];
 	$PS_RECYCLE=$row2["PS_RECYCLE"];
 	$Q_EXPIRATION=$row2["Q_EXPIRATION"];
 	$DESCRIPTION=strip_tags($row2["DESCRIPTION"]);
 	$D = $DESCRIPTION;
 	if ( $EQ_TYPE == 'COMPETENCE') $label='Expiration';
 	else $label='';
 	if ( $EQ_NOM <> $OLDEQ_NOM) {
 		$OLDEQ_NOM =  $EQ_NOM;
 		echo "<tr> 
 		<td colspan=2 bgcolor=$mylightcolor width=75%>
		 <i><b>$EQ_NOM</b></i></td>
		<td bgcolor=$mylightcolor width=20% align=right>	
		 <i><b>".$label."</b></i></td>
		<td bgcolor=$mylightcolor width=5% align=right></td>
		 </tr>";
 	}
 	if ( $Q_VAL == -1 ) {
	 	$mycolor='black';
	 	$D = $DESCRIPTION." <font size=1>(formation en cours)</font>";
	 	// cas particulier: ne pas montrer PSE1 si PSE2 valide
	 	if ( $TYPE == 'PSE1') {
	 	 	$query="select count(*) as NB from qualification q, poste p
		 		where q.P_ID=".$P_ID." and p.PS_ID=q.PS_ID and p.TYPE='PSE2'";
		 	$result=mysql_query($query);
		 	$row=@mysql_fetch_array($result);
		 	$NB=$row["NB"];
		  	if ( $NB == 1 ) $show=false;
		}
	}
 	else if ( $Q_VAL == 1 ) $mycolor='green';
 	else $mycolor='darkblue';
	if ( $Q_EXPIRATION <> '') {
		if ($NB < 61) $mycolor='orange';
 		if ($NB <= 0) $mycolor='red';
 	}
 	if (( $PS_DIPLOMA == 1 || $PS_RECYCLE == 1) and ( $EQ_TYPE == 'COMPETENCE')) {
 		$query="select count(*) as NB from personnel_formation 
		 		where P_ID=".$P_ID." and PS_ID=".$PS_ID;
		$result=mysql_query($query);
 		$row=@mysql_fetch_array($result);
 		$NB=$row["NB"];
	  	$cmt=$D." <a href=personnel_formation.php?P_ID=$pompier&PS_ID=$PS_ID>
		 <img src=images/page_white_medal.png height=16 border=0
		   title=\"détails sur la formation $DESCRIPTION de ".ucfirst($P_PRENOM)." ".strtoupper($P_NOM)."\"></a>";
		if ( $NB > 0 ) $cmt .=" <font size=1>(x ".$NB.")</font>";
	}
 	else $cmt = $DESCRIPTION;
 	
 	if ( $Q_UPDATED_BY <> '' ) {
 		$audit="<img src=images/texte2.png 
		 	title=\"Modifié par ".ucfirst(get_prenom($Q_UPDATED_BY))." ".strtoupper(get_nom($Q_UPDATED_BY))." le ".$Q_UPDATE_DATE."\">";
 	}
 	else $audit='';
 	
 	if ( $show)
	echo "<tr>  	
			 <td bgcolor=$mylightcolor width=5%></td>    
      	     <td bgcolor=$mylightcolor width=70% align=left>
			 <font color=$mycolor> $cmt</font></td>
			 <td bgcolor=$mylightcolor width=20% align=right>	
			 <font color=$mycolor>$Q_EXPIRATION</font></td>
			 <td bgcolor=$mylightcolor width=5% align=right>".$audit."</td>
		</tr>";
}

echo "</td></tr>";
echo "</table></td></tr></table>"; 

echo "<p>";

$queryn="select count(*) as NB from poste where PS_USER_MODIFIABLE = 1";
$resultn=mysql_query($queryn);
$rown=@mysql_fetch_array($resultn);
$n=$rown["NB"];

if ($competence_allowed or ($n > 0 and $P_ID == $id)) {
    if ($P_ID == $id) $t='Modifier mes compétences';
    else $t='Modifier les compétences';
	echo " <input type=submit value=\"$t\" 
   onclick='bouton_redirect(\"qualifications.php?pompier=$P_ID&order=GRADE&from=personnel\");'> ";
}
}

if ( $tab == 3 ) {
//=====================================================================
// liste des formations
//=====================================================================

$query="select pf.PS_ID, p.TYPE, pf.PF_ID, pf.PF_COMMENT, pf.PF_ADMIS, DATE_FORMAT(pf.PF_DATE, '%d-%m-%Y') as PF_DATE, 
		pf.PF_RESPONSABLE, pf.PF_LIEU, pf.E_CODE, tf.TF_LIBELLE, pf.PF_DIPLOME,
		DATE_FORMAT(pf.PF_PRINT_DATE, '%d-%m-%Y %H:%i') as PF_PRINT_DATE,
		DATE_FORMAT(pf.PF_UPDATE_DATE, '%d-%m-%Y %H:%i') as PF_UPDATE_DATE, 
		pf.PF_PRINT_BY, pf.PF_UPDATE_BY, p.PS_PRINTABLE
	    from personnel_formation pf, type_formation tf, poste p
	    where tf.TF_CODE=pf.TF_CODE
	    and p.PS_ID = pf.PS_ID
        and pf.P_ID=".$P_ID."
		order by pf.".$order;
$result=mysql_query($query);
$num=mysql_num_rows($result);
if ( $num > 0 ) {
   echo "\n"."<a href=\"formations_xls.php?pompier=$P_ID&order=$order\" target=_blank><img src=\"images/xls.jpg\" align=\"right\" height=\"24\" alt=\"ical\" title=\"Télécharger le fichier excel\" class=\"noprint\" border=\"0\"></a>";
   echo "<p><table>";
   echo "<tr>
	  <td class='FondMenu'>";
   echo "<table cellspacing=0 border=0 bgcolor=$mylightcolor>";
   echo "<tr class=TabHeader>
      <td width=50><a href=upd_personnel.php?pompier=".$P_ID."&order=PS_ID class=TabHeader>Type</a></td>
	  <td width=80><a href=upd_personnel.php?pompier=".$P_ID."&order=PF_DATE class=TabHeader>Date</a></td>
	  <td width=150><a href=upd_personnel.php?pompier=".$P_ID."&order=TF_CODE class=TabHeader>Type de formation</a></td>
	  <td width=100><a href=upd_personnel.php?pompier=".$P_ID."&order=PF_DIPLOME class=TabHeader>N° diplôme</a></td>
	  <td width=50><a href=upd_personnel.php?pompier=".$P_ID."&order=PF_UPDATE_BY class=TabHeader>info</a></td>
	  <td width=130><a href=upd_personnel.php?pompier=".$P_ID."&order=PF_LIEU class=TabHeader>Lieu</a></td>
	  <td width=150><a href=upd_personnel.php?pompier=".$P_ID."&order=PF_RESPONSABLE class=TabHeader>Délivré par</a></td>
	  <td width=130><a href=upd_personnel.php?pompier=".$P_ID."&order=PF_COMMENT class=TabHeader>Commentaire</a></td>";
   if ($change_formation_allowed)
   		echo "<td width=30>Suppr</td>";
   echo "</tr>";
   $i=0;
   while ($row=@mysql_fetch_array($result)) {
   	   $PS_ID=$row["PS_ID"];
   	   $TYPE=$row["TYPE"];
	   $PF_ID=$row["PF_ID"];
	   $PF_COMMENT=$row["PF_COMMENT"];
	   $PF_ADMIS=$row["PF_ADMIS"];
	   $PF_DATE=$row["PF_DATE"];
	   $PF_RESPONSABLE=$row["PF_RESPONSABLE"];
	   $PF_LIEU=$row["PF_LIEU"];
	   $PS_PRINTABLE=$row["PS_PRINTABLE"];
	   $PF_DIPLOME=$row["PF_DIPLOME"];
	   $E_CODE=$row["E_CODE"];
	   $TF_LIBELLE=$row["TF_LIBELLE"];
	   $PF_UPDATE_BY=$row["PF_UPDATE_BY"];
	   $PF_UPDATE_DATE=$row["PF_UPDATE_DATE"];
	   $PF_PRINT_BY=$row["PF_PRINT_BY"];
	   $PF_PRINT_DATE=$row["PF_PRINT_DATE"];
	   
	   $i=$i+1;
	   if ( $i%2 == 0 ) {
      	    $mycolor=$mylightcolor;
	   }
	   else {
      	    $mycolor="#FFFFFF";
	   }
	   
	   if ($change_formation_allowed)
	   		echo "<tr bgcolor=$mycolor 
	      		onMouseover=\"this.bgColor='yellow'\" 
	      		onMouseout=\"this.bgColor='$mycolor'\"   
		  		onclick=\"this.bgColor='#33FF00';update($P_ID,$PS_ID,$PF_ID)\">";
	   else
	   	  	echo "<tr bgcolor=$mycolor >";
	   echo "<td><font size=1><b>".$TYPE."</b></font></td>";
	   echo "<td><font size=1>".$PF_DATE."</font></td>";
	   if ( intval($E_CODE) <> 0)
	     	echo "<td width=150><font size=1>
			 <a href=evenement_display.php?evenement=".$E_CODE."&from=formation>".$TF_LIBELLE."</a></font></td>";
	   else 
		 	echo "<td><font size=1>".$TF_LIBELLE."</font></td>";
	   echo "<td><font size=1><b>".$PF_DIPLOME."</b></font></td>";
	   
	   echo "<td>";
   	   if ( intval($E_CODE) <> 0 ) {
			$querye="select TF_CODE, E_CLOSED from evenement where E_CODE=".$E_CODE;
			$resulte=mysql_query($querye);
	   		$rowe=@mysql_fetch_array($resulte);
			
   	   		if ( check_rights($id,4,"$P_SECTION") and $rowe["E_CLOSED"] == 1) {
		  		echo " <a href=pdf_document.php?section=".$P_SECTION."&evenement=".$E_CODE."&mode=2&P_ID=".$P_ID.">
				<img border=0 src=images/smallerpdf.jpg
				title=\"imprimer l'attestation de formation\"></a>";
	   		}
	   		if ( $PS_PRINTABLE == 1 ) {
	   		   	if ( $id == $P_ID or check_rights($id,48,"$P_SECTION")) {
	   		   	 	if ($rowe["TF_CODE"] == "I" and $PF_DIPLOME <> "")
		  				echo " <a href=pdf_diplome.php?section=".$P_SECTION."&evenement=".$E_CODE."&mode=4&P_ID=".$P_ID.">
						<img border=0 src=images/smallerpdf.jpg
						title=\"imprimer le duplicata du diplôme\"></a>";
				}
	   		}
       }
	   $popup="";
	   if ( $PF_UPDATE_BY <> "" )
	   		$popup="Enregistré par:
".ucfirst(get_prenom($PF_UPDATE_BY))." ".strtoupper(get_nom($PF_UPDATE_BY))." le ".$PF_UPDATE_DATE."
";
	   if ( $PF_PRINT_BY <> "" )		
			$popup .="Diplôme imprimé par:
".ucfirst(get_prenom($PF_PRINT_BY))." ".strtoupper(get_nom($PF_PRINT_BY))." le ".$PF_PRINT_DATE;
	   
	   if ( $popup <> "" ) 
	   		$popup=" <img src=images/texte2.png title=\"".$popup."\">";
	   echo $popup."</td>";
	   
	   echo "<td><font size=1>".$PF_LIEU."</font></td>
	     <td><font size=1>".$PF_RESPONSABLE."</font></td>
	     <td><font size=1>".$PF_COMMENT."</font></td>";
	   if ($change_formation_allowed)
	   		echo "<td>
	   		<a href=del_personnel_formation.php?P_ID=".$P_ID."&PS_ID=".$PS_ID."&PF_ID=".$PF_ID."&from=formations>
		 	<img src=images/trash.png border=0 title='supprimer cette information'></a></td>";
	   echo "</tr>";
   }

   echo "</table>";
   echo "</td></tr></table>";
}
else {
	echo "<p>Aucune information disponible pour les formations suivies.<br>";
	$action = "nothingyet";
	}
}
//=====================================================================
// affichage des engagements futurs
//=====================================================================
if ( $tab == 4 ) {

$out ="";
$sql = "select eh.eh_id, e.te_code, e.e_code, e.e_libelle, date_format(eh.eh_date_debut,'%d-%m-%Y') 'datedeb', eh.eh_date_debut sortdate,
        date_format(eh.eh_debut, '%H:%i') eh_debut, 
		date_format(eh.eh_fin, '%H:%i') eh_fin,
	    date_format(eh.eh_date_fin,'%d-%m-%Y') 'datefin',
	    e.e_lieu,
	    date_format(ep.ep_date_debut,'%d-%m-%Y') 'epdatedeb',
	    date_format(ep.ep_debut, '%H:%i') ep_debut, date_format(ep.ep_fin, '%H:%i') ep_fin,
	    date_format(ep.ep_date_fin,'%d-%m-%Y') 'epdatefin',
	    ep.ep_flag1,
		ep.ep_comment,
		ep.tp_id
        from evenement e, evenement_participation ep, evenement_horaire eh
        where e.e_code = ep.e_code
        and ep.eh_id = eh.eh_id
        and e.e_code = eh.e_code
        AND  ep.p_id = '$P_ID'
        AND e.e_canceled = 0
        and ( date_format(eh.eh_date_debut,'%Y%m%d') >= date_format(now(),'%Y%m%d') or 
        	  ( date_format(eh.eh_date_debut,'%Y%m%d') < date_format(now(),'%Y%m%d') and date_format(eh.eh_date_fin,'%Y%m%d') >= date_format(now(),'%Y%m%d'))
        	 )
        union all
        select 1 eh_id, 'GAR' te_code, 0 e_code, e.eq_nom e_libelle, date_format(pg.pg_date,'%d-%m-%Y') 'datedeb', 
		pg.pg_date sortdate,
        pg.type eh_debut, 
		'' eh_fin,
        '' datefin,
        '' e_lieu,
        '' epdatedeb,
        '' ep_debut,
        '' ep_fin,
        '' epdatefin,
        0 ep_flag1,
        '' ep_comment,
        0 tp_id
        from planning_garde pg, equipe e
        where e.eq_id = pg.eq_id
        and pg.p_id='$P_ID'
        and date_format(pg.pg_date,'%Y%m%d') >= date_format(now(),'%Y%m%d')
        order by sortdate asc, eh_debut asc";

$res = mysql_query($sql);
if (mysql_num_rows($res)>0){
	  echo "\n"."<a href=\"evenement_ical.php?pid=$P_ID&section=$section\" target=_blank><img src=\"images/ical.png\" align=\"right\" height=\"24\" alt=\"ical\" title=\"Télécharger le fichier ical\" class=\"noprint\" border=\"0\"></a>";
      echo "<p>Participations en cours ou prévues:<p><table>
        <tr>
        <td class='FondMenu'>
        <table cellspacing=0 border=0>
        <tr class=TabHeader>
           <td width=30>Type</td>
      	   <td width=150>Date</td>
      	   <td width=100>Heures</td>
      	   <td width=100>Lieu</td>
		   <td width=150>Description</td>
		   <td width=10>?</td>
        </tr>";  
      while($row=mysql_fetch_array($res)){
      
         if ( $row['e_code'] == 0 ) {
         //garde
         echo "<tr bgcolor=$mylightcolor>";
         echo "<td align=left><img border=0 src=images/".$row['te_code']."small.gif></td>";
         echo "<td>".$row['datedeb']." </td>";
         if ( $row['eh_debut'] == 'J' )  echo "<td>Jour</td>";
         if ( $row['eh_debut'] == 'N' )  echo "<td>Nuit</td>";
         echo "<td>".$row['e_lieu']."</td>";
         $tmp=explode ( "-",$row['datedeb']); $year=$tmp[2]; $month=$tmp[1]; $day=$tmp[0];
         echo "<td><a href=\"garde_jour.php?year=".$year."&month=".$month."&day=".$day."\">";
         echo $row['e_libelle'];
         echo "</a></td>";
         echo "<td></td>";
         echo "</tr>";
          
         }
         else {
         // evenement
         
         if ( $row['epdatedeb'] == "" ) {
      		$datedeb=$row['datedeb'];
      		$datefin=$row['datefin'];
      		$debut=$row['eh_debut'];
      		$fin=$row['eh_fin'];
      	 }
      	 else {
       		$datedeb=$row['epdatedeb'];
      		$datefin=$row['epdatefin'];
      		$debut=$row['ep_debut'];
      		$fin=$row['ep_fin'];     	 
      	 }
         
         // commentaire sur l'inscription
         $cmt="";
		 if ( $row['tp_id'] > 0 ) {
		 	$cmt=get_fonction($row['tp_id'])."\n";
		 }
         $cmt .= $row['ep_comment'];
         
         if ( $row['ep_flag1'] == 1 ) { 
		 	$txtimg="texte3.png";
		 	$cmt="Participation en tant que salarié(e)\n".$cmt;
		 }
         else if ( $cmt  <> '' ) $txtimg="texte2.png";	

		 if ( $cmt <> '' ) $txtimg="<img src=images/".$txtimg." title=\"".$cmt."\"></td>";
		 else $txtimg="";
         
         $n=get_nb_sessions($row['e_code']);
         if ( $n > 1 ) $part=" partie ".$row['eh_id']."/".$n;
         else $part="";
         echo "<tr bgcolor=$mylightcolor>";
         echo "<td align=left><img border=0 src=images/".$row['te_code']."small.gif height=16></td>";
         if ( $datedeb !=$datefin ) echo "<td>".$datedeb." au ".$datefin."</td>";
         else echo "<td>".$row['datedeb']." </td>";
         echo "<td><font size=1>".$debut."-".$fin."</font></td>";
         echo "<td>".$row['e_lieu']."</td>";
         echo "<td> <a href=\"evenement_display.php?evenement=".$row['e_code']."&from=personnel&pid=".$P_ID."\">";
         echo $row['e_libelle']." ".$part;
         echo "</a></td>";
         echo "<td>".$txtimg."</td>";
         echo "</tr>";
         }
      }
      $out .= "</table></td></tr></table>";
	  
   }
   else {
	  $out= "<p>Aucune information disponible, concernant les prochaines participations.<br>";
   }
   echo $out;
   
   // toutes les participations
   echo "<table><tr><td>Extraire la liste de toutes les participations</td><td> 
   		<img src='images/xls.jpg' id='StartExcel' height='24' border='0' alt='Excel' title='Excel' 
		onclick=\"window.open('personnel_evenement_xls.php?pid=$P_ID')\" class='noprint'/>
		</td></tr></table>";

}
//=====================================================================
// véhicules et matériel affectés
//=====================================================================
if ( $tab == 5 ) {

$query2="select v.V_ID, s.S_CODE, v.V_MODELE, v.TV_CODE, v.V_IMMATRICULATION, tv.TV_LIBELLE
		 from vehicule v, type_vehicule tv, section s
		 where v.TV_CODE=tv.TV_CODE
		 and s.S_ID=v.S_ID
		 and v.AFFECTED_TO=".$pompier;
$result2=mysql_query($query2);

$query3="select s.S_CODE, cm.PICTURE_SMALL, tm.TM_DESCRIPTION, tm.TM_USAGE, tm.TM_CODE, m.MA_ID, m.MA_MODELE
		from materiel m, type_materiel tm, categorie_materiel cm, section s
		 where cm.TM_USAGE=tm.TM_USAGE
		 and s.S_ID=m.S_ID
		 and tm.TM_ID=m.TM_ID
		 and m.AFFECTED_TO=".$pompier;
$result3=mysql_query($query3);

if (mysql_num_rows($result2) > 0 || mysql_num_rows($result3) > 0 ) {
	echo "<p><table>";
	echo "<tr>
	  <td class='FondMenu'>";
   	echo "<table cellspacing=0 border=0 bgcolor=$mylightcolor>";
   	echo "<tr class=TabHeader>";
	echo "<td colspan=2>Véhicules et Matériel affectés</b></td>";
    while ($row2=@mysql_fetch_array($result2)) {
		    $V_ID=$row2["V_ID"];
		    $S_CODE=$row2["S_CODE"];
            $V_MODELE=$row2["V_MODELE"];
            $TV_CODE=$row2["TV_CODE"];
            $TV_LIBELLE=$row2["TV_LIBELLE"];
            $V_IMMATRICULATION=$row2["V_IMMATRICULATION"];
      	  	echo "<tr><td bgcolor=$mylightcolor width=50><img src=images/car.png 
				title=\"".$TV_LIBELLE."\"></td>";
			if ($nbsections == 0 ) $cmt="<i> (".$S_CODE.")</i>";
			else $cmt="";
            echo "<td bgcolor=$mylightcolor width=350>
			<a href=upd_vehicule.php?vid=".$V_ID.">".$TV_CODE." ".$V_MODELE." ".$V_IMMATRICULATION."</a>".$cmt."</td></tr>";
    }
    while ($row3=@mysql_fetch_array($result3)) {
		    $PICTURE_SMALL=$row3["PICTURE_SMALL"];
            $TM_DESCRIPTION=$row3["TM_DESCRIPTION"];
            $TM_USAGE=$row3["TM_USAGE"];
            $MA_MODELE=$row3["MA_MODELE"];
            $TM_CODE=$row3["TM_CODE"];
            $MA_ID=$row3["MA_ID"];
            $S_CODE=$row3["S_CODE"];
            if ($nbsections == 0 ) $cmt="<i> (".$S_CODE.")</i>";
			else $cmt="";
      	  	echo "<tr><td bgcolor=$mylightcolor width=50><img src=images/".$PICTURE_SMALL." 
				title=\"".$TM_DESCRIPTION."\"></td>";
            echo "<td bgcolor=$mylightcolor width=350>
			<a href=upd_materiel.php?mid=".$MA_ID.">".$TM_CODE." ".$MA_MODELE."</a>".$cmt."</td></tr>";
    }
      	  
	echo "</table></td></tr></table>";
}
else
	echo "<p>Aucun véhicule ou matériel affecté.<br>";

}
?>
