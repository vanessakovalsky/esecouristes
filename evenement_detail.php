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
check_all(41);
$id=$_SESSION['id'];
$mysection=$_SESSION['SES_SECTION'];

$evenement=intval($_GET["evenement"]);
if (isset ($_GET["what"])) $what=$_GET["what"];
else $what='personnel';

if (isset ($_GET["company"])) $company=mysql_real_escape_string($_GET["company"]);
else $company=-1;


writehead();

if ($what == 'personnelexterne') $mylightcolor=$mygreencolor;
?>
<STYLE type="text/css">
.categorie{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
.type{color:<?php echo $mydarkcolor; ?>; background-color:white; font-size:9pt;}
</STYLE>
<script type='text/javascript'>
function inscrireP(evenement,action, pid) {
     if ( pid > 0 ) {
 	 	cible="evenement_inscription.php?evenement="+evenement+"&action="+action+"&P_ID="+pid;
 	 	if ( confirm("Vous allez inscrire une personne sur l'événement\n Continuer?") ) {
     		opener.document.location.href=cible;
     	}
     }
}

function inscrireV(evenement,action, vehicule) { 
	 if ( vehicule > 0 ) {
     	cible="evenement_vehicule_add.php?evenement="+evenement+"&action="+action+"&V_ID="+vehicule+"&from=evenement";
     	opener.document.location.href=cible;
     }
}

function inscrireM(evenement,action, materiel) {
	 if ( materiel > 0 ) {
     	cible="evenement_materiel_add.php?evenement="+evenement+"&action="+action+"&MA_ID="+materiel+"&from=evenement";
     	opener.document.location.href=cible;
     }
}

function choisirR(evenement,action, pid) {
    cible="evenement_inscription.php?evenement="+evenement+"&action="+action+"&P_ID="+pid;;
    opener.document.location.href=cible;
    self.close();
}

function filter(evenement,what, company) {
     	cible="evenement_detail.php?evenement="+evenement+"&what=personnelexterne&company="+company;
     	self.location.href=cible;
}

function closeme(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}
</script>

<?php 
echo "</head>";
echo "<body>";
	  
//=====================================================================
// recupérer infos evenement
//=====================================================================
$query="select TE_CODE, E_LIBELLE, E_CLOSED, E_CANCELED, E_OPEN_TO_EXT, S_ID, E_CHEF
		from evenement where E_CODE=".$evenement;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$TE_CODE=$row["TE_CODE"];
$E_LIBELLE=$row["E_LIBELLE"];
$E_CLOSED=$row["E_CLOSED"];
$E_CANCELED=$row["E_CANCELED"];
$E_OPEN_TO_EXT=$row["E_OPEN_TO_EXT"];
$S_ID=$row["S_ID"];
$E_CHEF=$row["E_CHEF"];

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td>
      <font size=4><b><img src=images/".$TE_CODE."small.gif> ".$E_LIBELLE."</b></font></td></tr>
	  </table>";

$organisateur= $S_ID;
if (get_level("$organisateur") > $nbmaxlevels - 2 ) $departement=get_family(get_section_parent("$organisateur"));
else $departement=get_family("$organisateur");


$granted_event=false;
$granted_personnel=false;
$granted_vehicule=false;
$chef=false;

if ( $id == $E_CHEF ) {
 $granted_event=true;
 $granted_personnel=true;
 $granted_vehicule=true;
 $chef=true;
}
else if (check_rights($id, 26, $organisateur)) { 
 	$veille=true;
	$SECTION_CADRE=get_highest_section_where_granted($id,26);
}
else $veille=false;

if (check_rights($id, 17, $organisateur) or $granted_event) $granted_vehicule=true;

// cas particulier
if (check_rights($id, 17) and (! $granted_vehicule))  {
 	if ( $E_OPEN_TO_EXT == 1 ) $granted_vehicule=true;
}

//=====================================================================
// inscrire d'autres personnes
//=====================================================================

if ( $what == 'personnel' or $what == 'personnelexterne') {

if (check_rights($id, 10, $organisateur) or check_rights($id, 15, $organisateur)) $granted_personnel=true;
else if (!$granted_personnel) check_all(10);


echo "<div align=center><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	   <td width=400 class=TabHeader colspan=2><b>Personnel</b></td>
      </tr>";
	  

// filtre 1 : personnel sous ma responsabilité
if (check_rights($id, 24)) $allowed1="";
else if ( $chef or check_rights($id, 15,"$organisateur")) $allowed1="$departement";
else if ( $veille ) $allowed1=get_family("$SECTION_CADRE");
else $allowed1=get_family(get_highest_section_where_granted($id,10));
     
// filtre 2 : personnel habilité
if ( $E_OPEN_TO_EXT == 1 ) {
    $allowed2="";
}
else  {
	// si niveau antenne on peut ajouter le personnel des antennes voisines
   	$allowed2=get_family("$departement");
}

if ( $what == 'personnelexterne') {
	// personnel externe: filtre company
	echo "<tr bgcolor=$mylightcolor >
      <td align=right><b> Entreprise</b></td>";
	echo "<td align=left><select id='company' name='company' onchange=\"filter('$evenement','personnelexterne',this.value);\">";

	if ( $company == -1 ) $selected ='selected'; else $selected='';
	echo "<option value='-1' $selected>... Pas de filtre par entreprise ...</option>";     	  
	echo companychoice($mysection,$company);
	echo "</select></td> ";
	echo "</tr>";	  
}

// déjà inscrits
$inscrits="0";
$query="select ep.P_ID from evenement_participation ep, evenement e
 		where e.E_CODE=$evenement
		and ep.E_CODE=e.E_PARENT
		union select ep.P_ID from evenement_participation ep, evenement e
 		where e.E_PARENT=$evenement
		and ep.E_CODE=e.E_CODE
		union select ep.P_ID from evenement_participation ep
 		where ep.E_CODE=$evenement";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
	$inscrits .= $row["P_ID"].",";
}
$inscrits .= "0";

// liste des personnes
if ( $nbsections <> 1 ) $sectionfilter=true;
else $sectionfilter=false;
    
$query="select P_ID, P_PRENOM, P_NOM, S_CODE , C_NAME, company.C_ID, null as GP_DESCRIPTION from pompier, section, company 
   		 where P_ID not in (".$inscrits.")
   		 and section.S_ID = P_SECTION
   		 and pompier.C_ID = company.C_ID
   		 and P_OLD_MEMBER = 0
		 and P_CODE <> '1234'";

// externes ou internes
if ( $nbsections == 0 ) {
	if ( $what == 'personnelexterne') {
	 	$query .= " and P_STATUT = 'EXT'";
	 	if ( $company >=0 ) $query .= " and company.C_ID = $company";
	}
	else $query .= " and P_STATUT <> 'EXT'";
}

if ( $sectionfilter ) {
   	if ( $allowed1 <> "" ) $query .= " and P_SECTION in (".$allowed1.")";
    if ( $allowed2 <> "" ) $query .= " and P_SECTION in (".$allowed2.")";
}		 

// et aussi ceux de l'organigramme
if ( $what <> 'personnelexterne' and ( $allowed1 <> "" or $allowed2 <> "")) {
	$query .= " union all select pompier.P_ID, P_PRENOM, P_NOM, S_CODE , null as C_NAME, 0 as C_ID, GP_DESCRIPTION  
	     from pompier, section, section_role, groupe
   		 where pompier.P_ID not in (".$inscrits.")
   		 and section.S_ID = section_role.S_ID
		 and section_role.P_ID = pompier.P_ID
		 and groupe.GP_ID = section_role.GP_ID
   		 and P_OLD_MEMBER = 0
		 and P_CODE <> '1234'";
		 
	if ( $sectionfilter ) {
		if ( $allowed1 <> "" ) {
			$query .= " and section_role.S_ID in (".$allowed1.")";
			$query .= " and P_SECTION not in (".$allowed1.")";
		}
		if ( $allowed2 <> "" ) {
			$query .= " and section_role.S_ID in (".$allowed2.")";
			$query .= " and P_SECTION not in (".$allowed2.")";
		}
	}	
}

if ( $what == 'personnelexterne') $query .= " order by C_NAME, P_NOM";
else $query .= " order by P_NOM";

$result=mysql_query($query);

echo "<tr bgcolor=$mylightcolor align=right><td><b>inscrire </b></td>";
echo "<td align=left><select id='add' name='add' 
		onchange=\"inscrireP('".$evenement."','inscription',document.getElementById('add').value)\">
      <option value='0' selected>choix personne</option>\n";
while ($row=@mysql_fetch_array($result)) {
         $P_NOM=$row["P_NOM"];
         $P_PRENOM=$row["P_PRENOM"];
         $P_ID=$row["P_ID"];
         $S_CODE=$row["S_CODE"];
         $C_ID=$row["C_ID"];
         $C_NAME=$row["C_NAME"];
		 $GP_DESCRIPTION=$row["GP_DESCRIPTION"];
         if ( $what == 'personnelexterne' ) {
		  	if ( $C_ID > 0 ) $cmtentreprise="".$C_NAME." - ";
		  	else $cmtentreprise="";
		 }
		 else $cmtentreprise="";
         if ( $nbsections <> 1 ) $cmt=" ( ".$GP_DESCRIPTION." ".$cmtentreprise.$S_CODE." )";
         else $cmt="";
         echo "<option value='".$P_ID."'>".strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM).$cmt."</option>\n";
}
echo "</select>
	  </td>";
echo "</tr>";
echo "</table>";// end left table

echo "</td></tr></table>"; // end cadre
echo "</div>";
}

//=====================================================================
// choix responsable
//=====================================================================
if ( $what == 'responsable' ) {
if (check_rights($id, 15, $organisateur) or $chef) $granted_responsable=true;
echo "<div align=center><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	   <td width=400 class=TabHeader colspan=2><b>Responsable</b></td>
      </tr>";
      
// liste des personnes
if ( $nbsections <> 1 ) $sectionfilter=true;
else $sectionfilter=false;
    
$query="select P_ID, P_PRENOM, P_NOM, S_CODE
         from pompier, section
   		 where  section.S_ID = P_SECTION
   		 and P_OLD_MEMBER = 0
		 and P_CODE <> '1234'
		 and P_STATUT <> 'EXT'
		 and S_ID in (".get_family("$organisateur").")";
		 
// si antenne locale, ajouter les cadres du département
if ( get_level("$organisateur") >= $nbmaxlevels -1 ) {
	$query .=" union select P_ID, P_PRENOM, P_NOM, S_CODE
         from pompier, section
   		 where  section.S_ID = P_SECTION
   		 and P_OLD_MEMBER = 0
		 and P_CODE <> '1234'
		 and P_STATUT <> 'EXT'
		 and P_ID in (".get_granted(15,"$organisateur", 'parent').$id.")";
}

// et aussi ceux de l'organigramme
if ( $what <> 'personnelexterne') {
	$query .= " union select pompier.P_ID, P_PRENOM, P_NOM, s2.S_CODE  
	     from pompier, section, section_role, section s2
   		 where section.S_ID = section_role.S_ID
		 and section_role.P_ID = pompier.P_ID
		 and P_SECTION = s2.S_ID
   		 and P_OLD_MEMBER = 0
		 and P_CODE <> '1234'
		 and section.S_ID in (".get_family("$organisateur").")";
}

$query .=" order by P_NOM, P_PRENOM";

$result=mysql_query($query);

echo "<tr bgcolor=$mylightcolor align=right><td><b>choisir </b></td>";
echo "<td align=left><select id='newchef' name='newchef' 
		onchange=\"choisirR('".$evenement."','responsable',document.getElementById('newchef').value)\">";
if ( $E_CHEF == '' ) $selected='selected';
else $selected='';		
echo "<option value='0' $selected>choix responsable</option>\n";
while ($row=@mysql_fetch_array($result)) {
         $P_NOM=$row["P_NOM"];
         $P_PRENOM=$row["P_PRENOM"];
         $P_ID=$row["P_ID"];
         $S_CODE=$row["S_CODE"];
         if ( $nbsections <> 1 ) $cmt=" (".$S_CODE.")";
         else $cmt="";
         if ( $P_ID == $E_CHEF ) $selected='selected';
         else $selected='';
         echo "<option value='".$P_ID."' $selected>".strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM).$cmt."</option>\n";
}
echo "</select>
	  </td>";
echo "</tr>";
echo "</table>";// end left table

echo "</td></tr></table>"; // end cadre
echo "</div>";
} 
//=====================================================================
// inscrire véhicules
//=====================================================================

else if (( $what == 'vehicule' ) and ( $granted_vehicule )) {
    if ( $nbsections <> 1 ) $sectionfilter=true;
    else $sectionfilter=false;
	$query="select distinct v.V_ID, v.TV_CODE, v.V_MODELE, v.V_IMMATRICULATION, v.V_INDICATIF,
			s.S_DESCRIPTION, s.S_ID, s.S_CODE, s.S_DESCRIPTION
	        from vehicule v, section s, vehicule_position vp
			where vp.VP_ID = v.VP_ID
			and vp.VP_OPERATIONNEL > 0
			and v.V_ID not in (select ev.V_ID from evenement_vehicule ev, evenement e
 							where ( e.E_CODE=$evenement or e.E_PARENT=$evenement )
							and ev.E_CODE=e.E_CODE)";
	     
	$list = preg_split('/,/' , get_family("$organisateur"));

	if ( $chef or check_rights($id, 24)) {
       if ( $E_OPEN_TO_EXT == 1 ) {
			$sectionfilter=false;
	   } 
       else $allowed=$departement;
	}
	elseif ( $veille ) {
	   if ( $SECTION_CADRE == 0 ) $sectionfilter=false; 
	   else $allowed=get_family("$SECTION_CADRE").",".$departement;
	}
	elseif ( in_array($mysection,$list)) {
		$allowed=get_family(get_highest_section_where_granted($id,17));
	}
	else {
		if ( $mysection == 0 ) $sectionfilter=false; 
	 	else $allowed=get_family("$mysection");
	}

	if ( $sectionfilter) $query .= "     and s.S_ID in (".$allowed.")";        
	$query .= " 	and s.S_ID = v.S_ID";
	if ( $nbsections == 0 ) $query .= " order by s.S_CODE, v.TV_CODE";
	else $query .= " order by v.TV_CODE";
    $result=mysql_query($query);

	echo "<div align=center><table>";
	echo "<tr>
	<td class='FondMenu'>";

	echo "<table cellspacing=0 border=0>";
	echo "<tr>
      	   <td width=400 class=TabHeader>Véhicule</td>
      </tr>";
	  
	echo "<tr bgcolor=$mylightcolor><td><b>engager </b>";
    echo "<select id='addvehicule' name='addvehicule' 
        onchange=\"inscrireV('".$evenement."','demande',document.getElementById('addvehicule').value)\">
		<option value='0' selected>choix du véhicule</option>\n";
		
	$prevS_ID=-1;
    while ($row=@mysql_fetch_array($result)) {
      $V_ID=$row["V_ID"];
      $S_ID=$row["S_ID"];
      $S_CODE=$row["S_CODE"];
      $S_DESCRIPTION=$row["S_DESCRIPTION"];
      $TV_CODE=$row["TV_CODE"];
      $V_MODELE=$row["V_MODELE"];
	  if (( $prevS_ID <> $S_ID ) and ( $nbsections == 0 )) echo "<OPTGROUP LABEL='".$S_CODE." - ".$S_DESCRIPTION."' class='section'>";
      $prevS_ID=$S_ID;
      $V_IMMATRICULATION=$row["V_IMMATRICULATION"];
      $V_INDICATIF=$row["V_INDICATIF"];
      
      if ( $V_INDICATIF <> '' ) $V_IDENT = $V_INDICATIF;
      else $V_IDENT = $V_IMMATRICULATION;
      
      echo "<option value='".$V_ID."' class='materiel'>
			".$TV_CODE." - ".$V_MODELE." - ".$V_IDENT."</option>\n";
     }
	echo "</select>
		<td>";
	echo "</tr>";
	echo "</td></tr></table>";// end left table
	echo "</td></tr></table>"; // end cadre
	echo "</div>";
}	
//=====================================================================
// inscrire matériel
//=====================================================================	
else if (( $what == 'materiel' ) and ( $granted_vehicule )) {	
	if ( $nbsections <> 1 ) $sectionfilter=true;
    else $sectionfilter=false;
	$query="select distinct m.MA_ID, tm.TM_CODE, m.MA_MODELE, m.MA_NUMERO_SERIE, m.MA_NB, tm.TM_USAGE,
			s.S_DESCRIPTION, s.S_ID, s.S_CODE, m.MA_LIEU_STOCKAGE, tm.TM_LOT
	        from materiel m, section s, type_materiel tm, vehicule_position vp
	        where tm.TM_ID = m.TM_ID
	        and vp.VP_ID = m.VP_ID
	        and s.S_ID = m.S_ID
	        and vp.VP_OPERATIONNEL > 0
			and m.MA_ID not in (select em.MA_ID from evenement_materiel em, evenement e
 							where ( e.E_CODE=$evenement or e.E_PARENT=$evenement )
							and em.E_CODE=e.E_CODE)";
	
	$list = preg_split('/,/'  , get_family("$organisateur"));

	if ( $chef or check_rights($id, 24)) {
       if ( $E_OPEN_TO_EXT == 1 ) {
			$sectionfilter=false;
	   } 
       else $allowed=$departement;
	}
	elseif ( $veille ) {
	   if ( $SECTION_CADRE == 0 ) $sectionfilter=false; 
	   else $allowed=get_family("$SECTION_CADRE").",".$departement;
	}
	elseif ( in_array($mysection,$list)) {
		$allowed=get_family(get_highest_section_where_granted($id,17));
	}
	else {
		if ( $mysection == 0 ) $sectionfilter=false; 
	 	else $allowed=get_family("$mysection");
	}

    if ( $sectionfilter ) $query .= "     and s.S_ID in (".$allowed.")";
	if ( $nbsections == 0 ) $query .= " order by s.S_CODE, tm.TM_USAGE, tm.TM_CODE, m.MA_MODELE";
	else $query .= " order by tm.TM_USAGE, tm.TM_CODE, m.MA_MODELE";
    $result=mysql_query($query);

	echo "<div align=center><table>";
	echo "<tr>
	<td class='FondMenu'>";

	echo "<table cellspacing=0 border=0>";
	echo "<tr>
      	   <td width=400 class=TabHeader>Matériel</td>
      </tr>";

    echo "<tr bgcolor=$mylightcolor><td><b>engager </b>";
    echo "<select id='addmateriel' name='addmateriel' 
   		onchange=\"inscrireM('".$evenement."','demande',document.getElementById('addmateriel').value)\">
		<option value='0' selected>choix du matériel</option>\n";
	
   $prevS_ID=-1; $prevTM_USAGE="";
   while ($row=@mysql_fetch_array($result)) {
      $MA_ID=$row["MA_ID"];
      $TM_LOT=$row["TM_LOT"];
      if ( $TM_LOT == 1 ) {
       		$query2="select count(1) from materiel where MA_PARENT=".$MA_ID;
       		$result2=mysql_query($query2);
       		$row2=@mysql_fetch_array($result2);
       		$elements=$row2[0];
      }
      else $elements=-1;
      $MA_NB=$row["MA_NB"];
      $S_ID=$row["S_ID"];
      $S_CODE=$row["S_CODE"];
      $TM_USAGE=$row["TM_USAGE"];
      $TM_CODE=$row["TM_CODE"];
      $MA_NUMERO_SERIE=$row["MA_NUMERO_SERIE"];
      $MA_MODELE=$row["MA_MODELE"];
      $S_DESCRIPTION=$row["S_DESCRIPTION"];
      $MA_LIEU_STOCKAGE=$row["MA_LIEU_STOCKAGE"];
	  if (( $prevS_ID <> $S_ID ) and ( $nbsections == 0 )) echo "<OPTGROUP LABEL='".$S_CODE." - ".$S_DESCRIPTION."' class='section'>";
      $prevS_ID=$S_ID;
      if ( $prevTM_USAGE <> $TM_USAGE ) echo "<OPTGROUP LABEL='...".$TM_USAGE."' class='categorie'>";
      $prevTM_USAGE=$TM_USAGE;
      if ( $MA_NB > 1 ) $add=" (".$MA_NB.")";
      else $add="";
      if ( $elements >= 0 ) $add2=" (".$elements." éléments dans ce lot)";
      else $add2="";
      if ( $MA_NUMERO_SERIE <> "" ) $add.=" ".$MA_NUMERO_SERIE;
      echo "<option value='".$MA_ID."' class='materiel'>".$TM_CODE." - ".$MA_MODELE.$add.$add2.". ".$MA_LIEU_STOCKAGE."</option>\n";
      
  }
	echo "</select>
		<td>";
	echo "</tr>";
	echo "</td></tr></table>";// end left table
	echo "</td></tr></table>"; // end cadre
	echo "</div>";
	
}
 echo "<div align=center><p><input type=submit value='fermer cette page' onclick='closeme();'></div>";
?>
