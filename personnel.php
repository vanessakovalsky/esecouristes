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
check_all(0);
$id=$_SESSION['id'];
get_session_parameters();

$possibleorders= array('G_LEVEL','P_PHOTO','P_STATUT','P_NOM','P_PRENOM','P_SECTION','P_DEBUT','P_END','C_NAME');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='P_NOM';

$fixed_company = false;
if ( $category == 'EXT' ) {
	if (! check_rights($_SESSION['id'], 37)) {
		check_all(45);
		$company=$_SESSION['SES_COMPANY'];
		$_SESSION['company'] = $company;
		$fixed_company = true;
	}
} 
else check_all(40);


$ischef=is_chef($id,$filter);


$disabled="disabled";
$hide_phone=true;
$envoisEmail=false;
if ($position == 'actif'){
	if ( check_rights($_SESSION['id'], 15) )
	$envoisEmail=true;
}
writehead();
?>

<script type='text/javascript' src='popupBoxes.js'></script>
<script language="JavaScript">
function orderfilter(p1,p2,p3,p4,p5,p6){
	 self.location.href="personnel.php?order="+p1+"&filter="+p2+"&subsections="+p3+"&position="+p4+"&category="+p5+"&company="+p6;
	 return true
}

function orderfilter2(p1,p2,p3,p4,p5,p6){
 	 if (p3.checked) s = 1;
 	 else s = 0;
	 self.location.href="personnel.php?order="+p1+"&filter="+p2+"&subsections="+s+"&position="+p4+"&category="+p5+"&company="+p6;
	 return true
}

function displaymanager(p1){
	 self.location.href="upd_personnel.php?pompier="+p1;
	 return true
}

function bouton_redirect(cible) {
	 self.location.href = cible;
}

</script>
<?php
echo "</head>";
echo "<body>";

$querycnt="select count(*) as NB";

$query1="select distinct P_ID, P_CODE , P_NOM , P_PRENOM, P_HIDE, P_SEXE, pompier.C_ID, C_NAME,
		P_GRADE, P_STATUT, P_DEBUT, YEAR(P_FIN) 'P_END', P_SECTION, P_PHONE, P_PHONE2, S_CODE, P_EMAIL, P_PHOTO";

$queryadd=" from pompier , grade, section, company
	 where P_GRADE=G_GRADE
	 and pompier.C_ID = company.C_ID
	 and pompier.P_SECTION=section.S_ID
	 and P_NOM <> 'admin' ";
	 
if ( $company >=0 ) $queryadd .= " and company.C_ID = $company";

if ( $category == 'EXT' ) {
	$queryadd .= " and P_STATUT = 'EXT'";
	$mylightcolor=$mygreencolor;
	$title='Liste du personnel externe';
}
else if ( $position == 'actif' ) {
	$queryadd .= " and P_OLD_MEMBER = 0 and P_STATUT <> 'EXT'";
	$title='Liste du personnel actif';
}
else {
	$queryadd .= " and P_OLD_MEMBER > 0";
	$mylightcolor=$mygreycolor;
	$title='Liste des anciens membres';
}

if ( $nbsections <> 1) {
	if ( $subsections == 1 ) {
  	   $queryadd .= "\nand P_SECTION in (".get_family("$filter").")";
	}
	else {
  	   $queryadd .= "\nand P_SECTION =".$filter;
	}
}
$querycnt .= $queryadd;
$query1 .= $queryadd." order by ". $order;
if ( $order == "G_LEVEL" or $order == "P_PHOTO")  $query1 .=" desc";

$resultcnt=mysql_query($querycnt);
$rowcnt=@mysql_fetch_array($resultcnt);
$number = $rowcnt[0];

echo "<div align=center><font size=4><b>$title</b></font><i> ($number personnes)</i>";
echo "<p><table cellspacing=5 border=0 >";
echo "<tr height=40>";

echo "<td>
   <a href=trombinoscope.php?position=".$position."&category=".$category.">
	<img src=images/trombinoscope.png border=0 title='voir le trombinoscope'></a></td>";

// section
if ($nbsections <> 1 ) {
	if ( $_SESSION['SES_STATUT'] == 'EXT' ) {
		echo "Section";
	}
	else {
    	echo "<td>".choice_section_order('personnel.php')."</td>";
	}
	echo "<td><select id='filter' name='filter' title='filtre par section'
		onchange=\"orderfilter('".$order."',document.getElementById('filter').value,'".$subsections."','".$position."','".$category."','".$company."')\">";
	  display_children2(-1, 0, $filter, $nbmaxlevels, $sectionorder);
	echo "</select>";
	
	if ($nbsections == 0  ) {
		if ( $fixed_company ) $disabled='disabled';
		else $disabled='';
		echo "<br><select id='company' name='company' title='filtre par entreprise' $disabled
		onchange=\"orderfilter('".$order."','".$filter."','".$subsections."','".$position."','".$category."',document.getElementById('company').value)\">";
				
		echo "<option value='-1' 'selected'>... Pas de filtre par entreprise ...</option>";
		
		$treenode=get_highest_section_where_granted($_SESSION['id'],37);
		if ( $treenode == '' ) $treenode=$mysection;
		if ( check_rights($_SESSION['id'], 24) ) $treenode=$filter;
		echo companychoice("$treenode","$company");
		echo "</select>";
	}
	echo "</td>";
	if ( get_children("$filter") <> '' ) {
	  if ($subsections == 1 ) $checked='checked';
	  else $checked='';
	  echo "<td align=center><input type='checkbox' name='sub' $checked
	   onClick=\"orderfilter2('".$order."',document.getElementById('filter').value, this,'".$position."','".$category."','".$company."')\"/>
	   <font size=1>inclure les<br>sous sections</td>";
	}
}
if (( check_rights($_SESSION['id'], 1) and $category=='interne') or (check_rights($_SESSION['id'], 37) and $category=='EXT')) {
   if ( $position == 'actif' ) {
    	$querynb="select count(*) as NB from pompier";
    	$resultnb=mysql_query($querynb);
		$rownb=@mysql_fetch_array($resultnb);
		$nb = $rownb[0];
    
   		if ( $nb <= $nbmaxpersonnes )
   		echo "<td><input type='button' value='Ajouter' name='ajouter' 
		   	onclick=\"bouton_redirect('ins_personnel.php?category=$category&suggestedsection=$filter&suggestedcompany=$company');\">";
   		else
   			echo "<font color=red><b>Vous ne pouvez plus ajouter de personnel (maximum atteint: $nbmaxpersonnes)</b></font>";
   	}
}
echo "</td>";

echo "</tr><tr><td colspan=4>";
// ====================================
// pagination
// ====================================
require_once('paginator.class.php');
$pages = new Paginator;  
$pages->items_total = $number;  
$pages->mid_range = 9;  
$pages->paginate();  
if ( $number > 10 ) {
	echo $pages->display_pages();
	echo $pages->display_jump_menu(); 
	echo $pages->display_items_per_page(); 
	$query1 .= $pages->limit;
}
$result1=mysql_query($query1);
$numberrows=mysql_num_rows($result1);

echo "</td></tr></table>";

if ($envoisEmail) {   
echo "<script type=\"text/javascript\">
function SendMailTo(formName, checkTab,message,doc){
	var dest = '';
	for (i=0; i<document.forms[formName].elements[checkTab].length; i++) {
		if(document.forms[formName].elements[checkTab][i].checked) {
			dest += ','+document.forms[formName].elements[checkTab][i].value;
		}
	}
	if(dest!=''){		
		if(doc=='badge'){
			document.forms[formName].action = 'pdf.php?pdf=badge';
		}
		if(doc=='listemails'){
			document.forms[formName].action = 'listemails.php';
		}
		document.forms[formName].SelectionMail.value = dest.substr(1,dest.length);
		document.forms[formName].submit();
		return true;
	}
	alert (message);   
	return false;
}
function DirectMailTo(formName, checkTab, message, doc){
	var dest = '';
	var max = 80;
	var m = 0;
	for (i=0; i<document.forms[formName].elements[checkTab].length; i++) {
		if(document.forms[formName].elements[checkTab][i].checked) {
			dest += ','+document.forms[formName].elements[checkTab][i].value;
			m++;
		}
		if (m>max){
			alert ('Maximum '+max+' destinataires par mail avec la fonction mailto');
			return false;
		}
	}
	if(dest!=''){		
		destid=dest.substr(1,dest.length);
		cible='mailto.php?destid='+ destid;
	 	self.location.href=cible;
        return true;
	}
	alert (message);   
	return false;
}

function checkAll(field,checkValue)
{
for (i = 0; i < field.length; i++)
	field[i].checked = ((checkValue!=true)?false:true) ;
}
</script>";

// permettre les modifications si je suis habilité sur la fonctionnalité 2 ( ou 37 pour externes)
// (et si la personne fait partie de mes sections filles ou alors je suis habilité sur la fonctionnalité 24 )

if ((is_children($filter,$mysection)) or (check_rights($_SESSION['id'], 24))) {
 	if ( check_rights($_SESSION['id'], 2) and $category=='interne' ) { $disabled="";$hide_phone=false; }
 	if ( check_rights($_SESSION['id'], 37) and $category=='EXT' ) { $disabled="";$hide_phone=false; }
 	if ( check_rights($_SESSION['id'], 12) and $category=='interne' ) { $disabled="";$hide_phone=false; }
}
if ( $ischef ) {
 	$disabled="";
 	$hide_phone=false;
}

echo "<form name=\"frmPersonnel\" id=\"frmPersonnel\" method=\"post\" action=\"mail_create.php\">";
if ( $number > 0 ) {
 	if ( $category <> 'EXT' ) 
	echo "<input type=\"button\" onclick=\"SendMailTo('frmPersonnel','SendMail','Vous devez sélectionner au moins un destinataire !','mail');\" value=\"Message\" title=\"envoyer un message à partir de cette application\">";
	if ( check_rights($_SESSION['id'], 2)) {
		echo " <input type=\"button\" onclick=\"DirectMailTo('frmPersonnel','SendMail','Vous devez sélectionner au moins un destinataire !','mail');\" value=\"Mailto\" title=\"envoyer un message avec votre logiciel de messagerie\">";	
		echo " <input type=\"button\" onclick=\"SendMailTo('frmPersonnel','SendMail','Vous devez sélectionner au moins un destinataire !','listemails');\" value=\"Listemails\" title=\"Récupérer la liste des adresses email\">";	
	}
	if ( check_rights($_SESSION['id'], 30) and $nbsections == 0 ) {
		echo " <input type=\"button\" onclick=\"SendMailTo('frmPersonnel','SendMail',
		'Vous devez sélectionner au moins une personne !','badge');\" value=\"Editer les badges\" title=\"imprimer des badges\">";
	}
	echo " <input type=\"button\" onclick=\"window.open('wab.php?section=$filter&subsections=$subsections&category=$category');\" value=\"Exporter\"  title=\"exporter les données vers un fichier csv, utilisable dans excel\">";
}
echo "<input type=\"hidden\" name=\"SelectionMail\" id=\"SelectionMail\">";
}
echo "<table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr height=10>";
if ($envoisEmail) {      	  
	echo "	  <th width=60 class=TabHeader>";
	if ( $numberrows > 0 )
		echo "<input type=checkbox name=CheckAll id=CheckAll onclick=checkAll(document.frmPersonnel.SendMail,this.checked); title='sélectionner/désélectionner tous'>";
	echo "</th><td bgcolor=$mydarkcolor width=0></td>";
}
if ( $grades == 1 ) {      	  
	echo "<td width=60 align=center >
					<a href=personnel.php?order=G_LEVEL class=TabHeader>Grade</a></td>
		  <td bgcolor=$mydarkcolor width=0></td>";
}
echo "	  <td width=15 align=center>
			<a href=personnel.php?order=P_PHOTO class=TabHeader><img src = images/photosmall.png border=0 title='personnel avec une photo'></td>";
echo "	  <td width=130 align=center>
			<a href=personnel.php?order=P_NOM class=TabHeader>Nom</td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td class=TabHeader align=center width=100>
				<a href=personnel.php?order=P_PRENOM class=TabHeader>Prénom</a></td>
				<td bgcolor=$mydarkcolor width=0></td>";
if ( $grades == 1 ) {      	  
	echo "	  <td class=TabHeader  align=center width=60>
					<a href=personnel.php?order=P_STATUT class=TabHeader>Statut</a></td>
      	  	  <td bgcolor=$mydarkcolor width=0></td>";
}
if ($nbsections <> 1 ) {      	
	echo "<td class=TabHeader align=center width=100><a href=personnel.php?order=P_SECTION class=TabHeader>Section</a></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}
if ( $category <> 'EXT'){
	echo "    <td class=TabHeader  align=center width=60>
					<a href=personnel.php?order=P_DEBUT class=TabHeader>Entrée</a></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
    if ( $position <> 'actif')
	echo "    <td class=TabHeader  align=center width=60>
					<a href=personnel.php?order=P_END class=TabHeader>Sortie</a></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}

if ($nbsections == 0 ) { 
echo "    <td class=TabHeader align=center width=180>
		<a href=personnel.php?order=C_NAME class=TabHeader>Entreprise</a></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}
echo "<td class=TabHeader  align=center width=70>Téléphone</td>
<td bgcolor=$mydarkcolor width=0></td>";
echo "<td class=TabHeader  align=center width=70>Téléphone 2</td>";
echo " </tr>";

echo "<input type=hidden name=SendMail id=SendMail value=\"0\" />";
// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result1)) {
      $P_SECTION=$row["P_SECTION"];
      $P_ID=$row["P_ID"];
      $P_SEXE=$row["P_SEXE"];
      $P_CODE=$row["P_CODE"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_NOM=$row["P_NOM"];
      $P_GRADE=$row["P_GRADE"];
      $P_STATUT=$row["P_STATUT"];
      $P_DEBUT=$row["P_DEBUT"];
      $P_PHONE=$row["P_PHONE"];
	  $P_PHONE2=$row["P_PHONE2"];
      $S_CODE=$row["S_CODE"];
      $P_HIDE=$row["P_HIDE"];
	  $P_EMAIL=$row["P_EMAIL"];
	  $P_PHOTO=$row["P_PHOTO"];
	  $P_END=$row["P_END"];
	  $C_ID=$row["C_ID"];
	  if ( $C_ID == 0 ) $C_NAME='';
	  else $C_NAME=$row["C_NAME"];

      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      if ( $P_SEXE == 'F' ) $prcolor='purple';
      else $prcolor=$mydarkcolor;
      
      if( ($P_PHOTO <> "") and file_exists($trombidir."/".$P_PHOTO)) $img = "<img src=images/photosmall.png>";
      else $img="";
      
	echo "<tr bgcolor=$mycolor 
	      onMouseover=\"this.bgColor='yellow'\" 
	      onMouseout=\"this.bgColor='$mycolor'\"   
		  onclick=\"this.bgColor='#33FF00'\">";
if ($envoisEmail) {
	echo "	  <td align=center>";
    if (($P_EMAIL!='') or  check_rights($_SESSION['id'], 30)) {
		echo "<input type=checkbox name=SendMail id=SendMail value=\"$P_ID\" />";
	}
    echo "  </td><td bgcolor=$mydarkcolor width=0></td>";
}	
if ( $grades == 1 ) {      	  
echo "	  <td align=center onclick='displaymanager($P_ID)'>$P_GRADE</td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}
echo "    <td align=center onclick='displaymanager($P_ID)'><b>".$img."</b></td>";
echo "    <td onclick='displaymanager($P_ID)'><b>".strtoupper($P_NOM)."</b></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td onclick='displaymanager($P_ID)'><font color=$prcolor>".my_ucfirst($P_PRENOM)."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";

if ( $grades == 1 ) {      	  
echo "	  
      	  <td align=center onclick='displaymanager($P_ID)'>$P_STATUT</td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}


if ($nbsections <> 1 ) {
		echo "<td align=center onclick='displaymanager($P_ID)'><b>$S_CODE</b></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}

if ( $category <> 'EXT'){
	echo"     <td align=center onclick='displaymanager($P_ID)'>$P_DEBUT</td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
    if ( $position <> 'actif')
    	echo"     <td align=center onclick='displaymanager($P_ID)'>$P_END</td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}

if ($nbsections == 0 ) {
	echo"     <td align=center onclick='displaymanager($P_ID)'>$C_NAME</td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}					
	
if ( $row["P_PHONE"] <> '' ) {
 		if (($P_HIDE == 1) and $hide_phone)
	  			$P_PHONE="**********";
}	

echo"     <td align=center onclick='displaymanager($P_ID)'>$P_PHONE</td>
<td bgcolor=$mydarkcolor width=0></td>";

if ( $row["P_PHONE2"] <> '' ) {
 		if (($P_HIDE == 1) and $hide_phone)
	  			$P_PHONE2="**********";
}	

echo"     <td align=center onclick='displaymanager($P_ID)'>$P_PHONE2</td>";
	echo "</tr>";
      
}
echo "</table>";
echo "</td></tr></table>";
if ($envoisEmail) {   
echo "</form>";
}
?>
