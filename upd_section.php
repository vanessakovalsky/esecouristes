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
check_all(44);

get_session_parameters();

$possibleorders= array('date','file','security','type','author','extension');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='date';

if ( isset($_GET["from"]))$from=$_GET["from"];
else $from="default";

if ( isset($_GET["page"])) $status="documents";
else if ( isset($_GET['status']) ) {
	$status=$_GET['status'];
	$_SESSION['status']=$status;
} 
else if ( isset($_SESSION['status']) ) $status=$_SESSION['status'];
else $status='infos';

if ( isset($_GET["from"]))$from=$_GET["from"];
else $from="default";

if ( isset($_GET["S_ID"])) {
 	$S_ID=intval($_GET["S_ID"]);
 	$filter=$S_ID;
 	$_SESSION['sectionchoice'] = $filter;
}
else $S_ID=$filter;

if ( check_rights($_SESSION['id'], 26, "$S_ID")) $perm26=true;
else $perm26=false;


writehead();

?>
<script type='text/javascript' src='checkForm.js'></script>
<script type='text/javascript' src='popupBoxes.js'></script>
<script type="text/javascript" src="js/jquery.js"></script>
<style type="text/css" >@import url(js/tabs/ui.tabs.css);</style>
<script type="text/javascript" src="js/tabs/ui.tabs.js"></script>
<script type="text/javascript">
$(document).ready(function() {	
	$('#TabsTriFact > ul').tabs();	
});
</script>
<script type="text/javascript">
function fermerfenetre(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}
function suppr_section(section) {
    if ( confirm ("Attention : vous allez supprimer cette section.\nLe personnel et les véhicules seront\nréaffectés dans la section supérieure.\nVoulez vous continuer ?" )){
     	cible = "del_section.php?S_ID=" + section;
     	self.location.href = cible;
    }
}
function deletefile(section, file) {
   if ( confirm ("Voulez vous vraiment supprimer le fichier " + file +  "?" )) {
         self.location = "delete_event_file.php?number=" + section + "&file=" + file + "&type=section";
   }
}

var fenetreDetail=null;
function displaymanager(p1,p2){
 	 fermerDetail();
	 url="upd_responsable.php?S_ID="+p1+"&GP_ID="+p2;
	 fenetre=window.open(url,'Responsable','toolbar=no, location=no, directories=no, status=no, scrollbars=no, resizable=no, copyhistory=no,' + 'width=450' + ',height=200');
	 fenetreDetail = fenetre;
	 return true
}

function fermerDetail() {
	 if (fenetreDetail != null) {
	    fenetreDetail.close( );
	    fenetreDetail = null;
         }
}

var NewDocument=null;
function openNewDocument(section){
	 url="upd_document.php?section="+section;
	 fenetre=window.open(url,'Note','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,   copyhistory=no,' + 'width=450' + ',height=300');
	 NewDocument = fenetre;
	 return true
}

function closeNewDocument() {
	 if (NewDocument != null) {
	    NewDocument.close( );
	    NewDocument = null;
         }
}

function filterdoc(section,type) {
	url="upd_section.php?S_ID="+section+"&td="+type+"&status=documents";
	self.location.href=url;
}


</script>
<style type="text/css">
textarea{
FONT-SIZE: 10pt; 
FONT-FAMILY: Arial;
width:90%;
}
</style>
</head>

<?php

//=====================================================================
// get infos
//=====================================================================
$disabled="disabled";
if (check_rights($_SESSION['id'], 22, "$S_ID"))
$disabled="";

if (check_rights($_SESSION['id'], 47, "$S_ID") || $disabled=="" )
$documentation=true;
else $documentation=false;

if (check_rights($_SESSION['id'], 29, "$S_ID") || $disabled=="" )
$showfact=true;
else $showfact=false;

if (check_rights($_SESSION['id'], 30, "$S_ID") || $disabled==""  )
$showbadge=true;	
else $showbadge=false;

if (check_rights($_SESSION['id'], 36, "$S_ID")) {
   $granted_agrement=true;
   $disabled_agrement='';
}
else {
   $granted_agrement=false;
   $disabled_agrement='disabled';
}

if ( $disabled == '' ) $unlock_save=true;
else $unlock_save=false;

$query1="select S_ID, S_CODE, S_DESCRIPTION, S_PARENT, S_URL,
		S_PHONE, S_PHONE2, S_FAX, S_ADDRESS, S_ZIP_CODE, S_CITY, S_CEDEX, S_EMAIL, S_EMAIL2,
		S_PDF_PAGE, S_PDF_SIGNATURE, S_PDF_MARGE_TOP, S_PDF_MARGE_LEFT, S_PDF_TEXTE_TOP, S_PDF_TEXTE_BOTTOM,
		S_PDF_BADGE, S_DEVIS_DEBUT, S_DEVIS_FIN, S_FACTURE_DEBUT, S_FACTURE_FIN, DPS_MAX_TYPE, S_FRAIS_ANNULATION		
 		from section
		where S_ID=".$S_ID;
$result1=mysql_query($query1);
$row1=@mysql_fetch_array($result1);
$S_ID=$row1["S_ID"];
$S_CODE=stripslashes($row1["S_CODE"]);
$S_DESCRIPTION=stripslashes($row1["S_DESCRIPTION"]);
$S_PARENT=$row1["S_PARENT"];
$S_URL=$row1["S_URL"];
$S_PHONE=$row1["S_PHONE"];
$S_PHONE2=$row1["S_PHONE2"];
$S_FAX=$row1["S_FAX"];
$S_ADDRESS=stripslashes($row1["S_ADDRESS"]);
$S_ZIP_CODE=$row1["S_ZIP_CODE"];
$S_CITY=stripslashes($row1["S_CITY"]); 
$S_CEDEX=stripslashes($row1["S_CEDEX"]);
$S_EMAIL=$row1["S_EMAIL"]; 
$S_EMAIL2=$row1["S_EMAIL2"]; 
$DPS_MAX_TYPE=$row1["DPS_MAX_TYPE"]; 

$S_PDF_PAGE = (isset($row1["S_PDF_PAGE"])?$row1["S_PDF_PAGE"]:"");
$S_PDF_SIGNATURE = (isset($row1["S_PDF_SIGNATURE"])?$row1["S_PDF_SIGNATURE"]:"");//$row1["S_PDF_PAGE"];  // Le pdf peut avoir 2 pages
$S_PDF_MARGE_TOP=(isset($row1["S_PDF_MARGE_TOP"])?$row1["S_PDF_MARGE_TOP"]:15);
$S_PDF_MARGE_LEFT=(isset($row1["S_PDF_MARGE_LEFT"])?$row1["S_PDF_MARGE_LEFT"]:15);
$S_PDF_TEXTE_TOP=(isset($row1["S_PDF_TEXTE_TOP"])?$row1["S_PDF_TEXTE_TOP"]:40);
$S_PDF_TEXTE_BOTTOM=(isset($row1["S_PDF_TEXTE_BOTTOM"])?$row1["S_PDF_TEXTE_BOTTOM"]:25);
$S_PDF_BADGE = (isset($row1["S_PDF_BADGE"])?$row1["S_PDF_BADGE"]:"");//$row1["S_PDF_BADGE"]; // format gif ou png

$devis_debut=stripslashes(isset($row1['S_DEVIS_DEBUT'])?$row1['S_DEVIS_DEBUT']:"");
$devis_fin=stripslashes(isset($row1['S_DEVIS_FIN'])?$row1['S_DEVIS_FIN']:"");
$facture_debut=stripslashes(isset($row1['S_FACTURE_DEBUT'])?$row1['S_FACTURE_DEBUT']:"");
$facture_fin=stripslashes(isset($row1['S_FACTURE_FIN'])?$row1['S_FACTURE_FIN']:"");
$frais_annulation=stripslashes(isset($row1['S_FRAIS_ANNULATION'])?$row1['S_FRAIS_ANNULATION']:"");

$query1="select NIV from section_flat where S_ID=".$S_ID;
$result1=mysql_query($query1);
$row1=@mysql_fetch_array($result1);
$NIV=$row1["NIV"];

//=====================================================================
// entete
//=====================================================================

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/network.png></td><td>
      <font size=4><b>".$S_CODE." - ".$S_DESCRIPTION."</b></font></td></tr></table>";

echo "<p><div id='TabsTriFact'>
<ul>";
if ( $status == 'infos' ) $class='ui-tabs-selected';
else $class='';
echo "<li class=\"$class\">
	<a href='#infos' title='Informations'>
	<span>Informations</span></a></li>";

if ( $nbsections <> 1 or check_rights($_SESSION['id'], 22)) $showresponsable=true;
else $showresponsable=false;
if ( $showresponsable) { 	
	if ( $status == 'responsables' ) $class='ui-tabs-selected';
	else $class='';
	echo "<li class=\"$class\">
		<a href='#responsables' title='Responsables'>
		<span>Responsables</span></a></li>";
}

if ( $status == 'documents' ) $class='ui-tabs-selected';
else $class='';
echo "<li class=\"$class\">
	<a href='#documents' title='documents'>
	<span>Documents</span></a></li>";

if ( $nbsections == 0 ) {
	if ( $showfact or $showbadge) {
	if ( $status == 'parametrage' ) $class='ui-tabs-selected';
	else $class='';
	echo "<li class=\"$class\">
		<a href='#parametrage' title='Paramétrage'>
		<span>Paramétrage</span></a></li>";
	}
	
	if ( $NIV < $nbmaxlevels -1 ) {	
		if ( $status == 'agrements' ) $class='ui-tabs-selected';
		else $class='';
		echo "<li class=\"$class\">
			<a href='#agrements' title='agrements'>
			<span>Agréments</span></a></li>";
	}	
}
echo "</ul>";
echo "\n"."</div>";// fin tabs

//=====================================================================
// tab infos
//=====================================================================
echo "<div id='infos'>";

echo "<form name='sectionform1' action='save_section.php' method='POST' enctype='multipart/form-data'>";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='S_ID' value='$S_ID'>";
echo "<input type='hidden' name='status' value='infos'>";

echo "<p><table>";

echo "<tr><td colspan=2 width=200 align=center><b>Effectif total de la section:</b></td>";
if ( get_children("$S_ID") <> '' and $nbsections <> 1) { 
  	echo "<td colspan=2 width=200 align=center><b>Dont hors sous-sections:</b></td>";
}
echo "</tr>";
echo "<tr><td align=right><img src=images/user.png title='nombre de personnes'></td>
	 <td align=center><b><a href=personnel.php?category=interne&order=P_NOM&filter=".$S_ID."&subsections=1> 
	 	".get_section_tree_nb_person("$S_ID")."</a></td>";
if ( get_children($S_ID) <> ''  and $nbsections <> 1) { 
	echo "<td align=right><img src=images/user.png title='nombre de personnes'></td>";
	echo "<td align=center><a href=personnel.php?category=interne&order=P_NOM&filter=".$S_ID."&subsections=0>
	".get_section_nb_person("$S_ID")."</td>";
}
echo "</tr>";

echo "<tr><td align=right ><img src=images/car.png title='nombre de véhicules'></td>
	<td align=center><b> <a href=vehicule.php?order=TV_USAGE&filter=".$S_ID."&filter2=ALL&subsections=1>
		".get_section_tree_nb_vehicule("$S_ID")."</td>";
if ( get_children($S_ID) <> ''  and $nbsections <> 1) { 
    echo "<td align=right><img src=images/car.png title='nombre de véhicules'></td>";
	echo "<td align=center>
	<a href=vehicule.php?order=TV_USAGE&filter=".$S_ID."&filter2=ALL&subsections=0> ".get_section_nb_vehicule($S_ID)."</td>";
}
echo "</tr>";	
echo "</table>";

// affichage des sous-sections
if ( $nbsections <> 1) {
$queryz="select S_ID, S_CODE from section where S_PARENT=".$S_ID." order by S_CODE asc";
$resultz=mysql_query($queryz);
$num = mysql_num_rows($resultz);
if ( $num > 0 ) {
   echo "<p><b>Liste des sous-sections:<br>
   <table border=0>";
   echo "<tr><td align=center>";
   $i=0;
   while ( $rowz=@mysql_fetch_array($resultz) ) {
	  $i++;
	  $zS_ID=$rowz["S_ID"];
	  $zS_CODE=$rowz["S_CODE"];
	  echo "<a href=upd_section.php?S_ID=$zS_ID>".$zS_CODE."</a>";
      if ( $i % 8 == 0 ) echo "<br>";
      else if ( $i < $num ) echo " | ";
   }
   echo "</td></tr></table>";
}
}
echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	   <td colspan=2 class=TabHeader>Informations obligatoires</td>
      </tr>";
      
echo "<tr>
      	  <td bgcolor=$mylightcolor colspan=2></td>";		
echo "</tr>";

//=====================================================================
// code
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor width=150 ><b>Identifiant</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left><b>$S_ID</b></td>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Code</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='code' size='20' value=\"$S_CODE\" $disabled>";		
echo "</tr>";

//=====================================================================
// parent section 
//=====================================================================


if ( $nbsections == 0 ) $disabledparent="";
else $disabledparent="disabled";
	  
if ( $S_ID == 0 ) {
 	echo "<input type='hidden' name='parent' value='-1'>";
}
else {
 echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>
			<a href=upd_section.php?S_ID=$S_PARENT>Section supérieure</a></b></td>
      	  <td bgcolor=$mylightcolor align=left>";

 if ( check_rights($_SESSION['id'], 24)) $mysection='0';
 else {
 	$mysection=get_highest_section_where_granted($_SESSION['id'],22);
 	if ( $mysection == '' ) $mysection=$_SESSION['SES_SECTION'];
 }
 
 if (( $disabled == "" ) and ( $mysection <> $S_ID )) {
     echo "<select id='parent' name='parent' $disabledparent>"; 
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
	     display_children2($mysection, $level +1, $S_PARENT, $nbmaxlevels - 1);
	}
	else {
	    $mycolor=$myothercolor;
   		$class="style='background: $mycolor;'";
   		echo "<option value='0' $class >".get_section_code('0')." - ".get_section_name('0')."</option>";
 		display_children2(0, 1, $S_PARENT , $nbmaxlevels - 1);
	}
  	echo "</select></td> "; 	  
  }
  else {
  	echo "<a href=upd_section.php?S_ID=$S_PARENT>".get_section_name($S_PARENT)."</a>";
  	echo "<input type='hidden' name='parent' value='$S_PARENT'>";
  }
  echo "</tr>";
}

//=====================================================================
// intercalaire
//=====================================================================

echo "<tr >
      	   <td width=300 colspan=2 class=TabHeader>
			 		<i>Informations facultatives</i>
		    </td>
      </tr>";


echo "<tr >
      	  <td bgcolor=$mylightcolor >Nom long</td>
      	  <td bgcolor=$mylightcolor align=left ><input type='text' name='nom' size='40' value=\"$S_DESCRIPTION\" $disabled>";		
echo "</tr>";

//=====================================================================
// ligne address
//=====================================================================

if ( $nbsections == 0 ) {

$map="";
if ( $S_ADDRESS <> "" ) {
	$querym="select count(*) as NB from geolocalisation where TYPE='S' and CODE=".$S_ID;
	$resultm=mysql_query($querym);
	$rowm=mysql_fetch_array($resultm);
	if ( $rowm["NB"] == 0 ) gelocalize($S_ID, 'S');
	$resultm=mysql_query($querym);
	$rowm=mysql_fetch_array($resultm);
	if ( $rowm["NB"] == 1 ) $map="<a href=map.php?type=S&code=".$S_ID." target=_blank><img src=images/mapsmall.png title='Voir la carte Google Maps' border=0></a>";
}



echo "<tr >
      	  <td bgcolor=$mylightcolor align=left>Adresse</td>
      	  <td bgcolor=$mylightcolor align=left><textarea name='address' cols='25' rows='2' value=\"$S_ADDRESS\" $disabled>$S_ADDRESS</textarea> ".$map."</td>";
echo "</tr>";

echo "<tr >
      	  <td bgcolor=$mylightcolor align=left>Code postal</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='zipcode' size='10' value='$S_ZIP_CODE' $disabled></td>";
echo "</tr>";

echo "<tr >
      	  <td bgcolor=$mylightcolor align=left>Ville</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='city' size='30' value=\"$S_CITY\" $disabled></td>";
echo "</tr>";

echo "<tr >
      	  <td bgcolor=$mylightcolor align=left>Cedex</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='cedex' size='16' value='$S_CEDEX' $disabled></td>";
echo "</tr>";


//=====================================================================
// ligne phone
//=====================================================================

echo "<tr >
      	  <td bgcolor=$mylightcolor >Téléphone</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='phone' size='20' 
			value='$S_PHONE' $disabled onchange='checkPhone(form.phone,\"".$S_PHONE."\")'></td>";		
echo "</tr>";

echo "<tr >
      	  <td bgcolor=$mylightcolor >TPH veille opérationnelle</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='phone2' size='20' 
			value='$S_PHONE2' $disabled onchange='checkPhone(form.phone2,\"".$S_PHONE2."\")'></td>";		
echo "</tr>";

echo "<tr >
      	  <td bgcolor=$mylightcolor >Fax</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='fax' size='20' 
			value='$S_FAX' $disabled onchange='checkPhone(form.fax,\"".$S_FAX."\")'></td>";		
echo "</tr>";

//=====================================================================
// ligne email
//=====================================================================

echo "<tr >
      	  <td bgcolor=$mylightcolor >Email commun</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='email' size='40' 
			value='$S_EMAIL' $disabled onchange='mailCheck(form.email,\"".$S_EMAIL."\")'></td>";		
echo "</tr>";

echo "<tr >
      	  <td bgcolor=$mylightcolor >Email secrétariat</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='email2' size='40' 
			value='$S_EMAIL2' $disabled onchange='mailCheck(form.email2,\"".$S_EMAIL2."\")'></td>";		
echo "</tr>";


echo "<tr>
      	  <td bgcolor=$mylightcolor >Site web</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='url' size='40' value='$S_URL' $disabled>";		
echo "</tr>";

//=====================================================================
// autorisation DPS des antennes
//=====================================================================
  if ( $NIV == $nbmaxlevels -1 ) {

	// agrément DPS du département
	$queryag="select TAV_ID from agrement where  TA_CODE='D' and S_ID=".$S_PARENT;
	$resultag=mysql_query($queryag);
	$rowag=mysql_fetch_array($resultag);
	$tagid = $rowag["TAV_ID"];

	if ( $tagid <> '') {
		$querydps="select TAV_ID,TA_VALEUR,TA_FLAG from type_agrement_valeur where TA_CODE='D' and TAV_ID <=".$tagid;
		$resultdps=mysql_query($querydps);

		echo "<tr >
      	<td bgcolor=$mylightcolor >Permission DPS</td>
      	<td bgcolor=$mylightcolor align=left>
		<select id='dps' name='dps' $disabled>";
		if ($DPS_MAX_TYPE == '' ) 
			echo "<option value='' selected>à définir</option>";
		while ( $rowdps=mysql_fetch_array($resultdps)) {
			$TAV_ID = $rowdps["TAV_ID"];
			$TA_VALEUR = $rowdps["TA_VALEUR"];
			$TA_FLAG = $rowdps["TA_FLAG"];
			if ($DPS_MAX_TYPE == $TAV_ID ) $selected='selected';
			else $selected='';
			echo "<option value='".$TAV_ID."' $selected>".$TA_VALEUR."</option>";
		}			
		echo "</select></td>";		
		echo "</tr>";

	}
  }
}
echo "</td></tr></table>";// end left table
echo "</td></tr></table>"; // end cadre

if ($unlock_save) {
	echo "<p><input type='submit' value='sauver infos'>";
	if (( $disabled == "" ) and ( $S_ID <> 0) and ( $nbsections == 0 )) {
    if ( check_rights($_SESSION['id'], 14, "$S_ID"))
    echo " <input type='button' value='supprimer' onclick=\"suppr_section('".$S_ID."')\">";
}
	
}
echo "</form>";
echo "</div>"; // fin tab infos

//=====================================================================
// tab 2 responsables
//=====================================================================
if ( $showresponsable) {
echo "<div id='responsables'>";
echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	   <td width=200 class=TabHeader>Organigramme</td>
		   <td colspan=2><a href=habilitations.php?category=R title='voir les habilitations de chaque rôle' 
		   class=TabHeader>
			 <img src=images/miniquestion.png border=0><font size=1>voir les habilitations de chaque rôle</font></a>
			 </td>
      </tr>";
		
$query="SELECT g.GP_ID, g.GP_DESCRIPTION, g.TR_SUB_POSSIBLE, r.P_ID, r.P_NOM, r.P_PRENOM, r.P_SECTION, r.S_CODE
FROM groupe g
LEFT JOIN (
SELECT p.P_ID, p.P_NOM, p.P_PRENOM, p.P_SECTION, s.S_CODE, sr.GP_ID
FROM section_role sr, pompier p, section s
WHERE sr.P_ID = p.P_ID
AND s.S_ID = p.P_SECTION
AND sr.S_ID =".$S_ID."
) AS r 
ON g.GP_ID = r.GP_ID
WHERE g.GP_ID >100
ORDER BY GP_ID ASC";

$result=mysql_query($query);
	 
$i=0;
while ($row=@mysql_fetch_array($result)) {
    $c=$row["GP_ID"];
    $GP_DESCRIPTION=$row["GP_DESCRIPTION"];
    $TR_SUB_POSSIBLE=$row["TR_SUB_POSSIBLE"];
	$CURPID=$row["P_ID"];
	$CURPNOM=$row["P_NOM"];
	$CURPPRENOM=$row["P_PRENOM"];
	$CURPSECTION=$row["P_SECTION"];
	$CURSECTIONCODE=$row["S_CODE"];

    $i=$i+1;
    if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
    }
    else {
      	 $mycolor="#FFFFFF";
    }
	// cas specifique association, pas de président sur les antennes
	if (( get_level("$S_ID") + 1 == $nbmaxlevels ) and ( $nbsections == 0 )) {
		if ( $GP_DESCRIPTION == "Président (e)" ) $GP_DESCRIPTION="Responsable d'antenne";
		if ( $GP_DESCRIPTION == "Vice président (e)" ) $GP_DESCRIPTION="Responsable adjoint";
	}
	
	echo "<tr>
      	  <td bgcolor=$mycolor width=200 >".$GP_DESCRIPTION."</td>
      	  <td bgcolor=$mycolor width=250 align=left>";		
    echo "<a href=upd_personnel.php?pompier=".$CURPID.">".strtoupper($CURPNOM)." ".my_ucfirst($CURPPRENOM)."</a>"; 
	if ( $CURSECTIONCODE <> "" ) echo " <font size=1>(".$CURSECTIONCODE.")</font>";
    echo "</td>
	<td bgcolor=$mycolor width=20>";
	
	$cadre=false;
	if ( $perm26 and $c == 107 ) {
	 	$cadre=true;
	}
	
	// le cadre de permanence peut se changer
	if (( $disabled == "") or ($cadre)){
	    echo "<img src=images/user.png border=0 title='choisir une personne pour ce rôle'
		   onclick=\"displaymanager(".$S_ID.",".$c.")\">";
	}
	echo "</td></tr>";
}
echo "</td></tr></table>";// end left table
echo "</td></tr></table>"; // end cadre
echo "</div>";
}
//=====================================================================
// tab 3 parametrage
//=====================================================================
if (( $showfact or $showbadge) and ( $nbsections == 0 )){

echo "<div id='parametrage'>";

echo "<form name='sectionform3' action='save_section.php' method='POST' enctype='multipart/form-data'>";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='S_ID' value='$S_ID'>";
echo "<input type='hidden' name='status' value='parametrage'>";

echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
if ($showfact) {
echo "<tr >
      	   <td colspan=2 class=TabHeader>
			 		<i>Papier à entête</i>
		    </td>
      </tr>";
echo "<tr >
      	  <td bgcolor=$mylightcolor width=150>Modèle (.PDF)</td>
      	  <td bgcolor=$mylightcolor width=150 align=left>"
		  .(($S_PDF_PAGE!="")?(file_exists($basedir."/images/user-specific/".$S_PDF_PAGE)?
		  "<a href=\"".$basedir."/images/user-specific/".$S_PDF_PAGE."\" target=\"_blank\">Voir</a>"
		  :"<font size=1 color=red>Fichier non trouvé sur le serveur</font>")
		  ."  <input type=\"checkbox\" name=\"delpage\"> Supprimer"
		  :"<input type='file' name='pdf_page' size='20' value=\"$S_PDF_PAGE\">")
		  ."</td>";		
echo "</tr>";
echo "<tr >
      	  <td bgcolor=$mylightcolor >Marge Haut</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='pdf_marge_top' size='5' value=\"$S_PDF_MARGE_TOP\">
			<font size=1><i> mm</td>";		
echo "</tr>";
echo "<tr >
      	  <td bgcolor=$mylightcolor >Marge Gauche / Droite</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='pdf_marge_left' size='5' value=\"$S_PDF_MARGE_LEFT\">
			<font size=1><i> mm</td>";		
echo "</tr>";
echo "<tr >
      	  <td bgcolor=$mylightcolor >Début de la zone de texte</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='pdf_texte_top' size='5' value=\"$S_PDF_TEXTE_TOP\">
			<font size=1><i>mm du haut de la feuille</i></td>";		
echo "</tr>";
echo "<tr >
      	  <td bgcolor=$mylightcolor >Fin de la zone de texte</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='pdf_texte_bottom' size='5' value=\"$S_PDF_TEXTE_BOTTOM\">
			<font size=1><i>mm du bas de la feuille</i></td>";		
echo "</tr>";

echo "<tr>
   	    <td width=300 colspan=2 class=TabHeader>
			<i>Textes par défaut</i>
		</td>
      </tr>";
      
echo "<tr >
      	  <td bgcolor=$mylightcolor align=left>Signature des documents</td>
      	  <td bgcolor=$mylightcolor align=left>
			<textarea name='pdf_signature' cols='30' rows='2'>$S_PDF_SIGNATURE</textarea></td>";
echo "</tr>";
echo "<tr >
      	  <td bgcolor=$mylightcolor align=left>Début du devis</td>
      	  <td bgcolor=$mylightcolor align=left>
			<textarea name='devis_debut' cols='30' rows='2'>$devis_debut</textarea></td>";
echo "</tr>";	  	  
echo "<tr >
      	  <td bgcolor=$mylightcolor align=left>Fin de devis</td>
      	  <td bgcolor=$mylightcolor align=left>
			<textarea name='devis_fin' cols='30' rows='2'>$devis_fin</textarea></td>";
echo "</tr>";	  
echo "<tr >
      	  <td bgcolor=$mylightcolor align=left>Début de facture</td>
      	  <td bgcolor=$mylightcolor align=left>
			<textarea name='facture_debut' cols='30' rows='2'>$facture_debut</textarea></td>";
echo "</tr>";	 
echo "<tr >
      	  <td bgcolor=$mylightcolor align=left>Fin de facture</td>
      	  <td bgcolor=$mylightcolor align=left>
			<textarea name='facture_fin' cols='30' rows='2'>$facture_fin</textarea></td>";
echo "</tr>";	  
echo "<tr>
		  <td bgcolor=$mylightcolor align=left>Montant des frais d'annulation pour un DPS</td>
		  <td bgcolor=$mylightcolor align=left>
		  <input type='text' name='frais_annulation' size='5' value=\"$frais_annulation\"><i>euros</i></td>";
echo "</tr>";
}
else $showfact=false;

//------------------------------
// ligne badge
//------------------------------
if ($showbadge) { 
   echo "<tr>
   	    <td colspan=2 class=TabHeader>
			<i>Badge</i>
		</td>
      </tr>";
   echo "<tr >
      	  <td bgcolor=$mylightcolor >Image de fond du badge</td>
      	  <td bgcolor=$mylightcolor align=left>
		  ".(($S_PDF_BADGE!="")?(file_exists($basedir."/images/user-specific/".$S_PDF_BADGE)?
		  "<a href=\"".$basedir."/images/user-specific/".$S_PDF_BADGE."\" target=\"_blank\">Voir</a>"
		  :"<font size=1 color=red>Fichier non trouvé sur le serveur</font>")
		  ." <input type=\"checkbox\" name=\"delbadge\"> Supprimer"
		  :"<input type='file' name='pdf_badge' size='20' value=\"$S_PDF_BADGE\">
		  <br><font size=1><i>Image .gif, Taille 86mm x 54mm")."
		  </td>";		  
}

echo "</td></tr></table>";// end left table
echo "</td></tr></table>"; // end cadre

if ($showbadge or $showfact ) {
	echo "<p><input type='submit' value='sauver paramétrage'>";
}
echo "</form>";

echo "</div>"; // fin tab 3
} // if $showfact or $showbadge


//=====================================================================
// tab 4 documents
//=====================================================================

echo "<div id='documents'>";

$query="select TD_CODE, TD_LIBELLE from type_document order by TD_LIBELLE";
$result=mysql_query($query);

echo "<p><table border=0><tr>
      	  <td><i>Type de documents</i></td>
      	  <td align=left>
		<select id='td' name='td' onchange=\"javascript:filterdoc('".$S_ID."',this.value);\">
		     <option value='ALL'>Tous types</option>";
		     while ($row=@mysql_fetch_array($result)) {
		          $TD_CODE=$row["TD_CODE"];
			  	  $TD_LIBELLE=$row["TD_LIBELLE"];
			  	  if ( $td == $TD_CODE ) $selected = 'selected';
			  	  else $selected='';
		          echo "<option value='$TD_CODE' $selected>$TD_LIBELLE</option>";
	     	     }
echo "</select>
	  </td></tr></table>";

$f = 0;
$f_arr = array();
$d_arr = array();
$t_arr = array();
$t_lib_arr = array();
$s_arr = array();
$s_arr = array();
$s_lib_arr = array();
$f_arr = array();
$ext_arr = array();

$mypath=$filesdir."/files_section/".$S_ID;
if (is_dir($mypath)) {
   	$dir=opendir($mypath); 
   	while ($file = readdir ($dir)) { 
      	if ($file != "." && $file != ".." and (file_extension($file) <> "db")) {
      	    $query="select d.D_ID,d.S_ID,d.D_NAME,d.TD_CODE,d.DS_ID, td.TD_LIBELLE, 
			  		ds.DS_LIBELLE, ds.F_ID, d.D_CREATED_BY
					from document d, document_security ds, type_document td
					where td.TD_CODE=d.TD_CODE
					and d.DS_ID=ds.DS_ID
					and d.S_ID=".$S_ID."
					and d.D_NAME=\"".$file."\"";
			$result=mysql_query($query);
			$nb=mysql_num_rows($result);
			$row=@mysql_fetch_array($result);
			
			if ( $td == "ALL" or $td == $row["TD_CODE"]) {
				if ($row["F_ID"] == 0 
					or check_rights($_SESSION['id'], $row["F_ID"], "$S_ID")
					or check_rights($_SESSION['id'], 47, "$S_ID") 
					or $row["D_CREATED_BY"] == $_SESSION['id']) {
						$ext_arr[$f] = strtolower(file_extension($file));
						$f_arr[$f] = $file;
      	    			$d_arr[$f] = date("Y-m-d H:i",filemtime($mypath."/".$f_arr[$f]));
						if ( $nb > 0 ) {
							$t_arr[$f] = $row["TD_CODE"];
							$s_arr[$f] = $row["DS_ID"];
							$t_lib_arr[$f] = $row["TD_LIBELLE"];
							$s_lib_arr[$f] =$row["DS_LIBELLE"];
							$fo_arr[$f] = $row["F_ID"];
							$cb_arr[$f] = $row["D_CREATED_BY"];
							
						}
						else {	
							$t_arr[$f] = "";
							$s_arr[$f] = "1";
							$t_lib_arr[$f] = "choisir";
							$s_lib_arr[$f] ="";
							$fo_arr[$f] = "0";
							$cb_arr[$f] = "";
						}
      	    			$f++;
      	    	}
      	    }
      	}
   	}
   	closedir($dir);
   	if ( $f > 0 ) {
   		if ( $order == 'date' ) 
		   array_multisort($d_arr, SORT_DESC, $f_arr, $t_arr,$s_arr,$t_lib_arr,$s_lib_arr,$fo_arr,$cb_arr,$ext_arr);
   		else if ( $order == 'file' ) 
		   array_multisort($f_arr, SORT_ASC, $d_arr, $t_arr,$s_arr,$t_lib_arr,$s_lib_arr,$fo_arr,$cb_arr,$ext_arr);
   		else if ( $order == 'type' ) 
		   array_multisort($t_arr, SORT_ASC, $f_arr, $d_arr,$s_arr,$t_lib_arr,$s_lib_arr,$fo_arr,$cb_arr,$ext_arr);
		else if ( $order == 'security' ) 
		   array_multisort($s_arr, SORT_DESC, $f_arr, $d_arr, $t_arr,$t_lib_arr,$s_lib_arr,$fo_arr,$cb_arr,$ext_arr);
   		else if ( $order == 'author' ) 
		   array_multisort($cb_arr, SORT_ASC, $f_arr, $d_arr,$s_arr,$t_lib_arr,$s_lib_arr,$fo_arr,$t_arr,$ext_arr);
		else if ( $order == 'extension' ) 
		   array_multisort($ext_arr,$f_arr, $cb_arr, SORT_DESC, $d_arr,$s_arr,$t_lib_arr,$s_lib_arr,$fo_arr,$t_arr);
   		if ( count( $f_arr ) > 0 ) {
   			$queryt="select TD_CODE, TD_LIBELLE from type_document order by TD_LIBELLE";
   			$querys="select DS_ID, DS_LIBELLE,F_ID from document_security";
   	
   	
   	    $number = count( $f_arr );
   	    // ------------------------------------
	    // pagination
		// ------------------------------------
		require_once('paginator.class.php');
		$pages = new Paginator;  
		$pages->items_total = $number;  
		$pages->mid_range = 9;  
		$pages->paginate();  
		if ( $number > 10 ) {
			echo $pages->display_pages();
			echo $pages->display_jump_menu(); 
			echo $pages->display_items_per_page(); 
			//$query1 .= $pages->limit;
		}
   	
   		echo "<p><table>";
		echo "<tr>
			<td class='FondMenu'>";
		echo "<table cellspacing=0 border=0>";
		echo "<tr>
		   <td width=20>
			 	<a href=upd_section.php?order=extension&status=documents&S_ID=".$S_ID." class=TabHeader
				 title='trier par extension'>Ext</a></td>
      	   <td width=330>
			 	<a href=upd_section.php?order=file&status=documents&S_ID=".$S_ID." class=TabHeader>Documents attachés</a></td>
		   <td width=30>
			 	<a href=upd_section.php?order=security&status=documents&S_ID=".$S_ID." class=TabHeader
				 title='trier par sécurité' >Sécu.</a></td>
      	   <td width=170>
				<a href=upd_section.php?order=type&status=documents&S_ID=".$S_ID." class=TabHeader>Type</a></td>
      	   <td width=120>
			 	<a href=upd_section.php?order=author&status=documents&S_ID=".$S_ID." class=TabHeader>Auteur</a></td>
      	   <td width=100>
			 	<a href=upd_section.php?order=date&status=documents&S_ID=".$S_ID." class=TabHeader>Date</a></td>
      	   <td width=20 class=TabHeader>Suppr.</td>
      	</tr>";
   		
   		$low=$pages->low;
   		$high= $pages->items_per_page +  $low;
   		if ( $high > $number ) $high=$number;
		for( $i=$low ; $i < $high  ; $i++ ) {
			   // extension
		       if ( in_array(strtolower(file_extension($f_arr[$i])), $supported_ext)) {
		     	   $myimg="<img border=0 src=images/smaller".strtolower(file_extension($f_arr[$i])).".jpg>"; 	
		       } 
		       else {
		     		$myimg="<img border=0 src=images/miniquestion.png>";
			   }
			   echo "<td bgcolor=$mylightcolor align=left>
				  <a href=showfile.php?section=".$S_ID."&file=".$f_arr[$i].">".$myimg." </a>
				</td>";
      	  	
      	  	   // document name
			   echo "<td bgcolor=$mylightcolor >
				  <a href=showfile.php?section=".$S_ID."&file=".$f_arr[$i]."> 
				  	<font size=1>".$f_arr[$i]."</font></a></td>";
				
				// security
				echo "<td bgcolor=$mylightcolor >";
				if ( $s_arr[$i] > 1 ) $img="<img border=0 src=images/locksmall.png title=\"".$s_lib_arr[$i]."\" height=14>";
			  	else $img="<img border=0 src=images/unlocksmall.png title=\"".$s_lib_arr[$i]."\" height=16>";
			  	if ($documentation)
			  		echo "<a href=\"javascript:ReverseContentDisplay('".$f_arr[$i]."');\">
			  		<font size=1>".$img."</font></a>";
			  	else
			  		echo $img;
			  	echo "</td>";
			  	
			  	// type document
			  	echo "<td bgcolor=$mylightcolor >";
				if ($documentation) 
					echo "<a href=\"javascript:ReverseContentDisplay('".$f_arr[$i]."');\">
			  		<font size=1>".$t_lib_arr[$i]."</font></a>";
			  	else if ( $t_lib_arr[$i] <> 'choisir' ) 
				  	echo "<font size=1>".$t_lib_arr[$i]."</font>";


      			echo  "<div id='".$f_arr[$i]."' 
					style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width: 430px;
					   height: 150px;
					   padding: 5px;'>
				<form name='form".$f_arr[$i]."' action='save_section.php' method=POST>
				<input type='hidden' name='operation' value='updatedoc'>
				<input type='hidden' name='S_ID' value='".$S_ID."'>
				<input type='hidden' name='doc' value='".$f_arr[$i]."'>
				<table border=0>
				<tr><td colspan=2><b>Informations liées au document:</b></td></tr>
				<tr><td colspan=2>".$myimg."<i> ".$f_arr[$i]."</i></td></tr>";
	  			echo "<tr><td align=right><i>type </i></td>
				    <td align=left><select name='type' id='type'>";
	  			$resultt=mysql_query($queryt);
				while ($rowt=@mysql_fetch_array($resultt)) {
					if ( $rowt["TD_CODE"] == $t_arr[$i]) $selected='selected';
					else $selected='';
					echo "<option value='".$rowt["TD_CODE"]."' $selected>".$rowt["TD_LIBELLE"]."</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><td align=right><i>Sécurité</i></td>
				<td align=left><select name='security' id='security'>";
	  			$results=mysql_query($querys);
				while ($rows=@mysql_fetch_array($results)) {
					if ( $rows["DS_ID"] == $s_arr[$i]) $selected='selected';
					else $selected='';
					echo "<option value='".$rows["DS_ID"]."' $selected>".$rows["DS_LIBELLE"]."</option>";
				}
				echo "</select></td></tr>	
				<tr><td colspan=2 align=center><input type=submit name='s".$f_arr[$i]."' value='OK'\"
		    		title='cliquer pour valider les changements'></td></tr></table>
		  		<div align=center><a onmouseover=\"HideContent('".$f_arr[$i]."'); return true;\"
   					href=\"javascript:HideContent('".$f_arr[$i]."')\"><i>fermer</i></a>
   				</div>
   				</form>
			 	</div>"; 
			 	echo "</td>";	
			
				if ( $cb_arr[$i] <> "" ) $author = "<a href=upd_personnel.php?pompier=".$cb_arr[$i].">".
				  my_ucfirst(get_prenom($cb_arr[$i]))." ".strtoupper(get_nom($cb_arr[$i]))."</a>";
				else $author="";
				echo "<td bgcolor=$mylightcolor ><font size=1>".$author."</a></font></td>";
				
				echo "<td bgcolor=$mylightcolor >
				<font size=1>".$d_arr[$i]."</td>";
				
      	  		if ( $documentation)
			      echo "<td bgcolor=$mylightcolor width=10><a href=\"javascript:deletefile('".$S_ID."','".$f_arr[$i]."')\">
				  <img src=images/trash.png alt='supprimer' border=0></a></td>";
				else echo "<td bgcolor=$mylightcolor width=10></td>";
				echo "</tr>";
    	  	}
	    	}
	    }
}

if ($documentation) {
	echo "<tr>
      	  <td colspan=7 bgcolor=$mylightcolor colspan=2 align=left ><b>Attacher un nouveau fichier :</b>";
	echo "<input type='button' id='userfile' name='userfile' value='Ajouter'
			onclick=\"closeNewDocument(); openNewDocument('".$S_ID."');\" ></td>";
	echo " </tr>";
}   
    
echo "</table>";// end left table
echo "</td></tr></table>"; // end cadre

echo "</div>";

//=====================================================================
// tab 5 agréments - sauf niveau antenne locale
//=====================================================================

if (( $NIV < $nbmaxlevels -1 ) and ( $nbsections == 0 )) {
 
echo "<div id='agrements'>";

echo "<form name='sectionform5' action='save_section.php' method='POST' enctype='multipart/form-data'>";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='S_ID' value='$S_ID'>";
echo "<input type='hidden' name='status' value='agrements'>";

echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";

$query2="select ca.CA_CODE, ca.CA_DESCRIPTION, ta.TA_CODE, ta.TA_DESCRIPTION
		 from categorie_agrement ca, type_agrement ta
		 where ca.CA_CODE =ta.CA_CODE
		 order by ca.CA_CODE desc, ta.TA_CODE ";
$result2=mysql_query($query2);

$old_CA_CODE="";
while ($row2=@mysql_fetch_array($result2)) {
    $CA_CODE=$row2["CA_CODE"];
    $CA_DESCRIPTION=$row2["CA_DESCRIPTION"];
    $TA_CODE=$row2["TA_CODE"];    
    $TA_DESCRIPTION=$row2["TA_DESCRIPTION"];  


	if ( $old_CA_CODE <> $CA_CODE )  {
		echo "<tr>
      	  <td colspan=3 class=TabHeader >".$CA_DESCRIPTION."</td>
		  <td class=TabHeader >Début</td>
		  <td class=TabHeader >Fin</td>";
		echo "</tr>";
	 	$old_CA_CODE = $CA_CODE;
	}
    $mycolor=$mylightcolor;
    
    $query="select date_format(a.A_DEBUT,'%d-%m-%Y') A_DEBUT, date_format(a.A_FIN,'%d-%m-%Y') A_FIN, 
			a.TAV_ID , tav.TA_VALEUR, tav.TA_FLAG
			from agrement a
			left outer join type_agrement_valeur tav
			on a.TAV_ID= tav.TAV_ID
			where a.S_ID=".$S_ID." 
			and a.TA_CODE='".$TA_CODE."'";
	$result=mysql_query($query);
    $row=@mysql_fetch_array($result);
    $CURA_DEBUT=$row["A_DEBUT"];
    $CURA_FIN=$row["A_FIN"];
    $CURTAV_ID=$row["TAV_ID"];
	$CURTA_VALEUR=$row["TA_VALEUR"];
	$CURTA_FLAG=$row["TA_FLAG"];
    
    $agr=0;
    if (( $CURA_DEBUT == '' ) and ( $CURA_FIN == '' )) $agr=0;
    else if (( $CURA_FIN <> '' ) and ( $CURA_DEBUT == '' )){
    	if (my_date_diff(getnow(),$CURA_FIN) > 0) $agr=1;
    	else $agr=-1;
    }
    else if (( $CURA_DEBUT <> '' ) and ( $CURA_FIN == '' )) {
     	if (my_date_diff($CURA_DEBUT,getnow()) > 0) $agr=1;
    }
    else { // 2 dates renseignées
	 	if (my_date_diff(getnow(),$CURA_FIN) < 0)$agr=-1;
	 	else if ((my_date_diff($CURA_DEBUT,getnow()) > 0) and (my_date_diff(getnow(),$CURA_FIN) > 0)) $agr=1;
    }
	if ( $agr == 1 ) $img="<img src=images/miniok.png title='agrément actif'>";
    else if ( $agr == -1 ) $img="<img src=images/minino.png title='agrément périmé'>";
	else  $img='';
    
	echo "<tr>
      	  <td bgcolor=$mycolor width=70 ><b>".$TA_CODE."</b></td>
      	  <td bgcolor=$mycolor width=350 align=left>".$TA_DESCRIPTION."</td>
		  <td bgcolor=$mycolor width=10 align=left>".$img."</td>";
    if ( $granted_agrement ) {
    	echo "<td bgcolor=$mycolor width=100> <input type=text size=10 maxlength=10 name='deb_".$TA_CODE."'
				value='$CURA_DEBUT' title='JJ-MM-AAAA' 
				onchange='checkDate2(sectionform5.deb_".$TA_CODE.")'></td>";
		echo "<td bgcolor=$mycolor width=100> <input type=text size=10 maxlength=10 name='fin_".$TA_CODE."'
				value='$CURA_FIN' title='JJ-MM-AAAA' 
				onchange='checkDate2(sectionform5.fin_".$TA_CODE.")'>";
	}
	else {
	   echo "<td bgcolor=$mycolor width=100>$CURA_DEBUT</td>";
	   echo "<td bgcolor=$mycolor width=100>$CURA_FIN</td>";
	}
	echo "</tr>";
	
	$query="select TAV_ID, TA_CODE, TA_VALEUR from type_agrement_valeur where TA_CODE='".$TA_CODE."'";
	$result=mysql_query($query);
	if ( mysql_num_rows($result) > 0 ) {
	 	echo "<tr><td bgcolor=$mycolor align=right><font size=1>agrément</font></td>";
	 	echo "<td bgcolor=$mycolor colspan=4 align=left>";
	 	if ( $granted_agrement ) {
			echo " <select name='val_".$TA_CODE."'>";
			while ($row=@mysql_fetch_array($result)) {
		 		$TAV_ID=$row["TAV_ID"];
		 		$TA_VALEUR=$row["TA_VALEUR"];
		 		if ( $CURTAV_ID == $TAV_ID ) $selected='selected';
		 		else $selected='';
		 		echo "<option value=".$TAV_ID." $selected>".$TA_VALEUR."</option>";
			}
			echo "</select>";
		}
		else {
		 	echo "<i>".$CURTA_VALEUR."</i>";
		}
		echo "</td></tr>";
	}

	

}
echo "</td></tr></table>";// end left table
echo "</td></tr></table>"; // end cadre

if ($granted_agrement) {
	echo "<p><input type='submit' value='sauver agréments'>";
}
echo "</form>";
echo "</div>";
}

//=====================================================================
// save buttons
//=====================================================================

if ( $from == 'export' ) {
	echo " <input type=submit value='fermer cette page' onclick='fermerfenetre();'> ";
}
elseif ( $from == 'save' ) {
 	echo " <input type='button' value='Retour' name='annuler' onclick=\"javascript:self.location.href='index_d.php';\">";
 	$_SESSION['status'] = "infos";
}
else {
	echo " <input type='button' value='Retour' name='annuler' onclick=\"javascript:history.back(1);\">";
	$_SESSION['status'] = "infos";
}

echo "</form>";


?>
