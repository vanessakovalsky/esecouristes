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
$C_ID=intval($_GET["C_ID"]);
check_all(0);

if ( $C_ID == 0 ) check_all(37);
else if (! check_rights($_SESSION['id'], 45) or $C_ID <> $_SESSION['SES_COMPANY'] )
check_all(37);

if ( isset($_GET["from"]))$from=$_GET["from"];
else $from="default";

$query="select TC_CODE, C_NAME, S_ID, C_DESCRIPTION, C_CREATED_BY, DATE_FORMAT(C_CREATE_DATE, '%d-%m-%Y') C_CREATE_DATE,
		C_ADDRESS, C_ZIP_CODE, C_CITY, C_EMAIL, C_PHONE, C_FAX, C_CONTACT_NAME, C_PARENT, C_SIRET
		FROM company
		where C_ID =".$C_ID;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$C_NAME=$row["C_NAME"];
$TC_CODE=$row["TC_CODE"];
$C_DESCRIPTION=$row["C_DESCRIPTION"];
$S_ID=$row["S_ID"];
$C_ADDRESS=$row["C_ADDRESS"];
$C_ZIP_CODE=$row["C_ZIP_CODE"];
$C_CITY=$row["C_CITY"];
$C_EMAIL=$row["C_EMAIL"];
$C_PHONE=$row["C_PHONE"];
$C_FAX=$row["C_FAX"];
$C_PARENT=$row["C_PARENT"];
$C_CREATED_BY=$row["C_CREATED_BY"];
$C_CREATE_DATE=$row["C_CREATE_DATE"];
$C_CONTACT_NAME=$row["C_CONTACT_NAME"];
$C_SIRET=$row["C_SIRET"];

if ( check_rights($_SESSION['id'], 37, "$S_ID")) $disabled='';
else $disabled='disabled';

$section=$_SESSION['SES_SECTION'];
$mysection=get_highest_section_where_granted($_SESSION['id'],37);
if ( check_rights($_SESSION['id'], 24) ) $section='0';
else if ( $mysection <> '' ) {
 	if ( is_children($section,$mysection)) 
 		$section=$mysection;
}

writehead();
echo "
<script type='text/javascript' src='checkForm.js'></script>
<script>

var fenetreDetail=null;
function displaymanager(p1,p2){
 	 fermerDetail();
	 url='upd_company_role.php?C_ID='+p1+'&TCR_CODE='+p2;
	 fenetre=window.open(url,'Responsable','toolbar=no, location=no, directories=no, status=no, scrollbars=no, resizable=no, copyhistory=no,' + 'width=600' + ',height=200');
	 fenetreDetail = fenetre;
	 return true
}

function fermerDetail() {
	 if (fenetreDetail != null) {
	    fenetreDetail.close( );
	    fenetreDetail = null;
         }
}

function fermerfenetre(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}

</script>
";
echo "</head>";
echo "<body>";

//=====================================================================
// affiche la fiche entreprise
//=====================================================================

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td>
      <font size=4><b>".$C_NAME."</b></font></td></tr></table>";

$query1="select count(*) as NB from pompier where P_STATUT='EXT' and C_ID = ".$C_ID;
$result1=mysql_query($query1);
$row1=@mysql_fetch_array($result1);

$query2="select count(*) as NB from pompier where P_STATUT <> 'EXT' and P_OLD_MEMBER=0 and C_ID = ".$C_ID;
$result2=mysql_query($query2);
$row2=@mysql_fetch_array($result2);

$query3="select count(*) as NB from company where C_PARENT = ".$C_ID;
$result3=mysql_query($query3);
$row3=@mysql_fetch_array($result3);

echo "<table border=0><tr>";
echo "<td align=right>Nombre de personnes: </td>
<td align =left><a href=personnel.php?order=P_NOM&filter=0&subsections=1&position=actif&category=EXT&company=".$C_ID." 
title='voir le personnel externe'>".$row1["NB"]."</a>";
if ( $row2["NB"] > 0 ) 
	echo " externes <a href=personnel.php?order=P_NOM&filter=0&subsections=1&position=actif&category=interne&company=".$C_ID." 
	title='voir le personnel membre $cisname'>".$row2["NB"]."</a> en interne";
echo "</td></tr><tr>";
echo "<td align=right>Etablissements secondaires:</td><td align=left> ".$row3["NB"]."</td></tr></table>";
echo "<form name='company' action='save_company.php'>";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='C_ID' value='$C_ID'>";

//=====================================================================
// ligne 1
//=====================================================================

if ( $C_CREATED_BY <> '' ) 
	$author = "<font size=1><i> - créée par ".ucfirst(get_prenom($C_CREATED_BY))." ".strtoupper(get_nom($C_CREATED_BY))."
			   le ". $C_CREATE_DATE."
				</i></font>";
else 
	$author='';

echo "<p><TABLE>
<TR>
<TD class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr height=10>
      	  <td class=TabHeader colspan=2>informations entreprise".$author."</td>
      </tr>";


//=====================================================================
// ligne type
//=====================================================================

$query="select TC_CODE,TC_LIBELLE from type_company order by TC_LIBELLE";
$result=mysql_query($query);

echo "<tr>
      	  <td bgcolor=$mylightcolor width=200><b>Type</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor width=250 align=left>
		  <select name='TC_CODE' $disabled>";
		  	 while ($row=@mysql_fetch_array($result)) {
		  	    if ( $row["TC_CODE"] == $TC_CODE ) $selected='selected';
		  	    else $selected='';
		  	    echo "<option value='".$row["TC_CODE"]."' $selected>".$row["TC_LIBELLE"]."</option>";
	     	 }
 echo "</select>";
 echo "</td>
 	 </tr>";

//=====================================================================
// ligne nom
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Nom</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='C_NAME' size='30' value=\"$C_NAME\" $disabled>";		
echo " </td>
      </tr>";

//=====================================================================
// ligne code ebrigade
//=====================================================================
if (isset($application_title_specific)) $application_title=$application_title_specific;
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Code $application_title</b></td>
      	  <td bgcolor=$mylightcolor align=left>".$C_ID."</td>
      </tr>";
      

//=====================================================================
// ligne section
//=====================================================================

if (  $nbsections == 0 ) {
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Section de rattachement</b> <font color=red>*</font></td>
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
	display_children2($section, $level +1, $S_ID, $nbmaxlevels);
 	
	echo "</select></td> ";
	echo "</tr>";	  
}
else echo "<input type='hidden' name='groupe' value='0'>";

//=====================================================================
// parent company 
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Etablissement secondaire de</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>";
echo "<select id='parent' name='parent' $disabled>";

if ( $C_PARENT == '' ) $selected ='selected';
else $selected ='';
echo "<option value='null' $selected>aucun</option>";

$query="select C_ID, C_NAME, C_DESCRIPTION from company 
		where S_ID=0 
		and C_ID > 0 
		and C_ID <> ".$C_ID."
		order by C_NAME";
$result=mysql_query($query);
while ( $row=@mysql_fetch_array($result)) {
 	if ( $C_PARENT == $row["C_ID"] ) $selected ='selected';
	else $selected ='';
	$code=$row["C_NAME"];
 	if ( $row["C_DESCRIPTION"] <> "" ) $code .=" - ".$row["C_DESCRIPTION"];
	echo "<option value='".$row["C_ID"]."' $selected>".$code."</option>";
}
echo "</select></td> ";
echo "</tr>";	

//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Description</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='C_DESCRIPTION' size='40' value=\"$C_DESCRIPTION\" $disabled>";		
echo " </td>
      </tr>";

//=====================================================================
// ligne siret
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>N° SIRET</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='C_SIRET' size='30' value=\"$C_SIRET\" onchange='checkNumber(form.C_SIRET,\"$C_SIRET\")' $disabled>";		
echo " </td>
      </tr>";
      
//=====================================================================
// ligne address
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Adresse</td>
      	  <td bgcolor=$mylightcolor align=left>
			<textarea name='address' cols='20' rows='3' value='' style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;' $disabled >".$C_ADDRESS."</textarea></td>";
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Code postal</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='zipcode' size='10' value=\"$C_ZIP_CODE\" $disabled></td>";
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Ville</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='city' size='20' value=\"$C_CITY\" $disabled></td>";
echo "</tr>";

//=====================================================================
// ligne contact
//=====================================================================

echo "<tr id=uRow2>
      	  <td bgcolor=$mylightcolor align=right>Nom du contact</td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='relation_nom' size='20' value=\"$C_CONTACT_NAME\" $disabled></td>";
echo "</tr>";

//=====================================================================
// ligne phone
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Téléphone</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='phone' size='20' value=\"$C_PHONE\" onchange='checkPhone(form.phone,\"\");' $disabled>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>Fax</td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='fax' size='20' value=\"$C_FAX\" onchange='checkPhone(form.fax,\"\");' $disabled>";		
echo "</tr>";

//=====================================================================
// ligne email
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>E-Mail</td>
      	  <td bgcolor=$mylightcolor align=left>
      	  	<input type='text' name='email' size='40'
			value=\"$C_EMAIL\" onchange='mailCheck(form.email,\"\");' $disabled></td>";	
echo "</tr>";
      
//=====================================================================
// rôles
//=====================================================================

echo "<tr>
      	  <td colspan=2 height=45 bgcolor=$mylightcolor align=left><b>Responsables pour l'entreprise</b></td>	
</tr>";

$query="SELECT r.P_ID, r.P_NOM, r.P_PRENOM, r.P_SECTION, tcr.TCR_CODE, tcr.TCR_DESCRIPTION, r.S_CODE
		from type_company_role tcr
		left join (
		select p.P_ID, p.P_NOM, p.P_PRENOM, p.P_SECTION, s.S_CODE, cr.TCR_CODE
		from pompier p, company_role cr, section s
		where cr.P_ID = p.P_ID
		and s.S_ID = p.P_SECTION
		and cr.C_ID = ".$C_ID."
		) as r
		on r.TCR_CODE = tcr.TCR_CODE";

if ( $S_ID > 0 ) $query .=" where tcr.TCR_FLAG is null";	
		
$query .=" order by tcr.TCR_CODE asc";

$result=mysql_query($query);
	 
$i=0;
while ($row=@mysql_fetch_array($result)) {
    $c=$row["TCR_CODE"];
    $TCR_DESCRIPTION=$row["TCR_DESCRIPTION"];
	  
	$CURPID=$row["P_ID"];
	$CURPNOM=$row["P_NOM"];
	$CURPPRENOM=$row["P_PRENOM"];
	$CURPSECTION=$row["P_SECTION"];
	$CURSECTIONCODE=$row["S_CODE"];
   
	echo "<tr>
      	  <td bgcolor=$mylightcolor align=right>".$TCR_DESCRIPTION."</td>";
    echo "<td bgcolor=$mylightcolor align=left>";
	if (( $disabled == "") ){
	    echo "<img src=images/user.png border=0 title='choisir une personne pour ce rôle'
		   onclick=\"displaymanager('".$C_ID."','".$c."')\"> ";
	}     	  
		
    echo "<a href=upd_personnel.php?pompier=".$CURPID.">".strtoupper($CURPNOM)." ".ucfirst($CURPPRENOM)."</a>"; 
	if ( $CURSECTIONCODE <> "" ) echo " <font size=1>(".$CURSECTIONCODE.")</font>";

	echo "</td></tr>";
}

echo "</table></tr></table>";
if ( check_rights($_SESSION['id'], 37))
	echo "<p><input type='submit' value='sauver' $disabled> ";
echo "</form>";

if ( check_rights($_SESSION['id'], 19)) {
	echo "<form name='delcompany' action='save_company.php'>";
	echo "<input type='hidden' name='C_ID' value='$C_ID'>";
	echo "<input type='hidden' name='operation' value='delete'>";
	echo "<input type='submit' value='supprimer'> ";
}
if ( $from == 'export' ) {
	echo " <input type=submit value='fermer' onclick='fermerfenetre();'> ";
}
else
	echo "<input type='button' value='Retour' name='annuler' onclick=\"javascript:history.back(1);\">";
echo "</form>";
echo "</div>";
?>
