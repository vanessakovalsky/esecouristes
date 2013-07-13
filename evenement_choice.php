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
$id=$_SESSION['id'];

get_session_parameters();

$fixed_company = false;
if ( $_SESSION['SES_STATUT'] == 'EXT' ) {
	if (! check_rights($_SESSION['id'], 41)) {
		check_all(45);
		$company=$_SESSION['SES_COMPANY'];
		$_SESSION['company'] = $company;
		$fixed_company = true;
	}
}
else check_all(41);

$section = $filter;
if ($company <= 0 ) check_all(41);


// Libell� �v�nement
$lib=((isset ($_GET["lib"]))?"%".mysql_real_escape_string($_GET["lib"])."%":"%");
writehead();

?>
<script type='text/javascript' src='checkForm.js'></script>
<script type='text/javascript' src='popupBoxes.js'></script>
<SCRIPT>
function redirect(type, section, sub, debut, fin, can, company) {
	 url = "evenement_choice.php?type_evenement="+type+"&filter="+section+"&dtdb="+debut+"&subsections="+sub+"&dtfn="+fin+"&canceled="+can+"&company="+company;
	 self.location.href = url;
}
function redirect2(type, section, sub, debut, fin, can, company) {
	 if (sub.checked) s = 1;
	 else s = 0;	 
	 url = "evenement_choice.php?type_evenement="+type+"&filter="+section+"&dtdb="+debut+"&subsections="+s+"&dtfn="+fin+"&canceled="+can+"&company="+company;
	 self.location.href = url;
}
function redirect3(type, section, sub, debut, fin, can, company) {
	 if (can.checked) c = 1;
	 else c = 0;	 
	 url = "evenement_choice.php?type_evenement="+type+"&filter="+section+"&dtdb="+debut+"&subsections="+sub+"&dtfn="+fin+"&canceled="+c+"&company="+company;
	 self.location.href = url;
}

function bouton_redirect(cible) {
	 self.location.href = cible;
}
function impression(){ 
    parent.frames[ 'droite' ].print(); 
}
</SCRIPT>

<STYLE type="text/css">
.categorie{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
.type{color:<?php echo $mydarkcolor; ?>; background-color:white; font-size:9pt;}
</STYLE>

</HEAD>

<?php
include_once ("config.php");

$query="select E.TE_CODE, TE.TE_LIBELLE, E.E_LIEU, EH.EH_ID,
	DATE_FORMAT(EH.EH_DATE_DEBUT, '%d-%m-%Y') as EH_DATE_DEBUT,
	DATE_FORMAT(EH.EH_DATE_FIN, '%d-%m-%Y') as EH_DATE_FIN, 
	TIME_FORMAT(EH.EH_DEBUT, '%k:%i') as EH_DEBUT, 
	TIME_FORMAT(EH.EH_FIN, '%k:%i') as  EH_FIN, 
	E.E_NB, E.E_LIBELLE, E.E_CODE, E.E_CLOSED, E.E_OPEN_TO_EXT, E.E_CANCELED, S.S_CODE, E.S_ID,
	E.E_PARENT, E.TAV_ID
    from evenement E, evenement_horaire EH, type_evenement TE, section S
	where E.TE_CODE=TE.TE_CODE
	and E.E_CODE=EH.E_CODE
	and E.S_ID = S.S_ID";

if ( $type_evenement <> 'ALL' ) 
	$query .= "\n and (TE.TE_CODE = '".$type_evenement."' or TE.CEV_CODE = '".$type_evenement."')";

if (( is_formateur($id) == 0 ) 
	and (! check_rights($_SESSION['id'], 15))) 
	$query .= "\n and E.TE_CODE <> 'INS'";

if ( $nbsections <> 1 ) {
 	if ( $subsections == 1 )
 		$query .= "\n and S.S_ID in (".get_family("$section").")";
 	else 
 		$query .= "\n and S.S_ID =".$section;
}
if ( $canceled == 0 )
	$query .= "\n and E.E_CANCELED = 0";

if ( $company <> '-1' )
	$query .= "\n and E.C_ID =".$company;
	
if($lib<>'%'){
	$query .= "\n and (E.E_LIBELLE like '$lib' or E.E_LIEU like '$lib')";
}

$tmp=explode ( "-",$dtdb); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2]; 
$tmp=explode ( "-",$dtfn); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];

$query .="\n and EH.EH_DATE_DEBUT <= '$year2-$month2-$day2' 
			 and EH.EH_DATE_FIN   >= '$year1-$month1-$day1'";
$query .="\n order by EH.EH_DATE_DEBUT, EH.EH_DEBUT";

$result=mysql_query($query);
$number=mysql_num_rows($result);

echo "<body>";
echo "<form name='formf' action='evenement_choice.php'>";
echo "<input type=hidden name=subsections id=subsections value=\"0\" />";
echo "<input type=hidden name=canceled id=canceled value=\"0\" />";

echo "<div align=center>
<table border=0>
<tr>
<td><font size=4><b>Liste des &eacute;v&egrave;nements</b><font size=2><i> ($number trouv&eacute;s)</i></font>
</td>";
echo "<td>
<a href=\"evenement_ical.php?section=$section\"><img src=\"images/ical.png\" height=\"24\" alt=\"ical\" 
title=\"T&eacute;l&eacute;charger le fichier ical de tous ces &eacute;v&egrave;nements\" class=\"noprint\" border=\"0\"></a>";
echo " <img src=\"images/printer.gif\" height=\"24\" alt=\"imprimer\" 
title=\"imprimer\" class=\"noprint\" onclick=\"impression();\">";
echo "<img src=\"images/xls.jpg\" id=\"StartExcel\" height=\"24\" border=\"0\" alt=\"Excel\" 
title=\"Excel\" onclick=\"window.open('evenement_list_xls.php');\" class=\"noprint\" />
</td></tr></table>";

echo "<p><table cellspacing=0 border=0><tr>
<td rowspan=".(($nbsections == 1 )?"6":"7").">";
if ( check_rights($_SESSION['id'], 15)) {
   echo "<input type='button' value='Ajouter' name='add_event' 
   	  onclick=\"bouton_redirect('evenement_edit.php?action=create')\">";
}
echo "</td>";

// choix type �v�nement
echo "<td align=right> Type d'activit&eacute; </td>";
echo "<td align=left><select id='type' name='type' 
   onchange=\"redirect(document.formf.type.options[document.formf.type.selectedIndex].value, '$section','$subsections', '$dtdb', '$dtfn', '$canceled','$company')\">";
echo "<option value='ALL' selected>Toutes activit&eacute;s </option>\n";

    require 'lib/autoload.inc.php';
 
    $db = DBFactory::getMysqlConnexionWithPDO();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On �met une alerte � chaque fois qu'une requ�te a �chou�
    
    $manager = new TypeEvenementManager($db);
    $managerCategorie = new CategorieEvenementManager($db);

    //$type_e = $manager->listerTypeEvenementSection($section);
    //print_r($type_e);
    $prevCat = '';
    foreach($manager->listerTypeEvenementSection($section) as $type_evenement_section) {
        $categTypeEv = $type_evenement_section->cev_code();
        $categorie = $managerCategorie->get($categTypeEv);
        $categEv = $categorie->cev_code();
        
        if ($categTypeEv <> $prevCat ) {   
        ?>
<option class='categorie' value='<?php echo $categTypeEv; ?>' label = '<?php echo $categorie->cev_description(); ?>'<?php  if ($categTypeEv  == $type_evenement ) echo " selected "; ?>><?php echo $categorie->cev_description(); ?></option>
        <?php
        }
        $prevCat = $categTypeEv;
        $TE_CODE = $type_evenement_section->te_code();
        ?>
<option class='type' value='<?php echo $TE_CODE; ?>' title='<?php echo $type_evenement_section->te_libelle();?>'<?php  if ($TE_CODE == $type_evenement ) echo " selected "; ?>>
    <?php echo $type_evenement_section->te_libelle(); ?></option>
        <?php
    };
    
echo "</select></td></tr>";
//echo 'le type devenement'.$type_evenement;
// choix section
if ( $nbsections <> 1) {
  echo "<tr><td align=right >";
  
  if ( $_SESSION['SES_STATUT'] == 'EXT' ) {
  	echo "Organisateur";
  }
  else {
  	echo choice_section_order('evenement_choice.php');
  }
  echo " </td>";

  // choix section
  echo "<td align=left>
  	<select id='filter' name='filter' 
	 title=\"cliquer sur Organisateur pour choisir le mode d'affichage de la liste\"
     onchange=\"redirect('$type_evenement', document.formf.filter.options[document.formf.filter.selectedIndex].value, 
	 			'$subsections', '$dtdb', '$dtfn', '$canceled', '$company')\">";

  // pour personnel externe on limite g�ographiquement la vilibilit�
  if ( $_SESSION['SES_STATUT'] == 'EXT' ) {
  	$_level=get_level("$mysection");
  	echo "<option value='$mysection' $class >".str_repeat(". ",$_level)." ".
      			get_section_code("$mysection")." - ".get_section_name("$mysection")."</option>";
    display_children2($mysection, $_level + 1, $section, $nbmaxlevels);
  }
  else  
  	display_children2(-1, 0, $section, $nbmaxlevels, $sectionorder);
  echo "</select></td></tr>";
}

echo "<tr><td align=right ></td>";
echo "<td align=left>";
if ( $nbsections <> 1 ) {
  if ( get_children("$section") <> '' ) {
	  if ($subsections == 1 ) $checked='checked';
	  else $checked='';
	  echo "<input type='checkbox' name='subsections' id='subsections' value='1' $checked 
	   onClick=\"redirect2('$type_evenement', '$section', this , '$dtdb', '$dtfn', '$canceled', '$company')\"/>
	   <label for='subsections'>inclure les sous sections</label>";
	}
}
// y compris les annul�s
if ($canceled == 1 ) $checked='checked';
else $checked='';
echo " <input type='checkbox' name='canceled' id='canceled' value='1' $checked 
	   onClick=\"redirect3('$type_evenement', '$section', '$subsections' , '$dtdb', '$dtfn', this, '$company')\"/>
	   <label for='canceled'>inclure les &eacute;v&egrave;nements annul&eacute;s</label></td></tr>";


// filtre entreprise
if ( $fixed_company ) $disabled='disabled';
else $disabled='';
echo "<tr><td align=right>Pour le compte de</td>";
echo "<td align=left>
  	<select id='company' name='company' $disabled
	 title=\"Ev�nements organis�s pour le compte d'une entreprise\"
     onchange=\"redirect('$type_evenement', '$section', '$subsections', '$dtdb', '$dtfn', '$canceled',
	 			document.formf.company.options[document.formf.company.selectedIndex].value)\">";
				
if ( $company == -1 ) $selected ='selected'; else $selected='';
echo "<option value='-1' $selected>... Pas de filtre par entreprise ...</option>";
		
$treenode=get_highest_section_where_granted($_SESSION['id'],37);
if ( $treenode == '' ) $treenode=$mysection;
if ( check_rights($_SESSION['id'], 24) ) $treenode='0';
echo companychoice("$treenode","$company",$includeparticulier=false);
echo "</select>";


// Choix Dates

echo "<tr><td align=right >D&eacute;but:</td><td align=left>";
?>
<input class="plain" name="dtdb" id="dtdb" value=
<?php
echo "\"".$dtdb."\"";
?>
size="12" onchange="checkDate2(document.formf.dtdb)"><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fStartPop(document.formf.dtdb,document.formf.dtfn);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt="" ></a>
<?php
echo "</td></tr>";


echo "<tr><td align=right >Fin :</td><td align=left>";
?>
<input class="plain" name="dtfn" id="dtfn" value=
<?php
echo "\"".$dtfn."\"";
?>
size="12" onchange="checkDate2(document.formf.dtfn)"><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.formf.dtdb,document.formf.dtfn);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt="" ></a>
<?php
// DEB libell� �v�nement
echo "<tr>
<td align=right >Libell&eacute; ou Lieu contient</td>
<td align=left>";
echo "<input type=\"text\" name=\"lib\" value=\"".preg_replace("/\%/","",$lib)."\" size=\"30\" alt=\"\" title=\"Utilisez le signe % pour remplacer des caract�res\"/>";
// FIN libell� �v�nement

echo " <input type='submit' value='go'>";
echo "</td></tr></table>";
echo "<tr><td colspan=3>";
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
	$query .= $pages->limit;
}
$result=mysql_query($query);

echo "</td></tr></table>";
echo "</form>";

if ( $number > 0 ) {
   if ( $nbsections <> 1) $organisateur="<font size=1> (organisateur)</font>";
   else $organisateur="";
   
   echo "<p><table>";
   echo "<tr>
    <td class='FondMenu'>";
   echo "<table cellspacing=0 border=0>";
   echo "<tr class=TabHeader>
      	  <td width=350 align=center>Activit&eacute;".$organisateur."</td>
    	  <td width=0></td>";
   if ($type_evenement == 'DPS')
      echo "<td width=60 align=center>DPS</td>
		 	<td width=0></td>";
   echo "<td width=180 align=center>Lieu</td>
      	  <td width=0></td>
      	  <td width=150 align=center>Date</td>
      	  <td width=0></td>
      	  <td width=80 align=center>Horaire</td>
      	  <td width=0></td>
      	  <td width=60 align=center>Inscrits</td>";
    if(check_rights($_SESSION['id'], 29))
         echo "<td width=0></td>
      	  <td width=20 align=center>Fac.</td>";
   echo "</tr>
      ";

   $i=0;
   while ($row=@mysql_fetch_array($result)) {
       $TE_CODE=$row["TE_CODE"];
       $TE_LIBELLE=$row["TE_LIBELLE"];
       $E_LIBELLE=stripslashes($row["E_LIBELLE"]);
       $E_LIEU=stripslashes($row["E_LIEU"]);
       $E_CODE=$row["E_CODE"];
       $EH_ID=$row["EH_ID"];
       $EH_DEBUT=$row["EH_DEBUT"];
       $EH_DATE_DEBUT=$row["EH_DATE_DEBUT"];
       $EH_DATE_FIN=$row["EH_DATE_FIN"];
       $EH_FIN=$row["EH_FIN"];
       $E_NB=$row["E_NB"];
       $TAV_ID=$row["TAV_ID"];
       $S_ID=$row["S_ID"];
       $S_CODE=$row["S_CODE"];
       $E_CLOSED=$row["E_CLOSED"];
       $E_CANCELED=$row["E_CANCELED"];
       $E_OPEN_TO_EXT=$row["E_OPEN_TO_EXT"];
       $E_PARENT=$row["E_PARENT"];

      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }

      $tmp=explode ( "-",$EH_DATE_DEBUT); $day1=$tmp[0]; $month1=$tmp[1]; $year1=$tmp[2];
      $date1=mktime(0,0,0,$month1,$day1,$year1);
      $ladate=date_fran($month1, $day1 ,$year1)." ".moislettres($month1);

      if ( $EH_DATE_FIN <> '' and $EH_DATE_FIN <> $EH_DATE_DEBUT) {
	  	$tmp=explode ( "-",$EH_DATE_FIN); $day1=$tmp[0]; $month1=$tmp[1]; $year1=$tmp[2];
      	$date1=mktime(0,0,0,$month1,$day1,$year1);
      	$ladate=$ladate." au<br> ".date_fran($month1, $day1 ,$year1)." ".moislettres($month1)." ".$year1;
      }
      else $ladate=$ladate." ".$year1;
	  //$timenow = time();
	  //if ($timenow > $date1) $E_CLOSED =1;

	  $attached="";
	  $f_arr = array(); $f = 0;
      $mypath=$filesdir."/files/".$E_CODE;
      if (is_dir($mypath)) {
   	     $dir=opendir($mypath); 
   	     while ($file = readdir ($dir)) { 
      	    if ($file != "." && $file != ".." and (file_extension($file) <> "db")) {
      	       $f_arr[$f++] = $file;
      	    }
   	     } 
   	     closedir($dir);

   	     if (count( $f_arr ) > 0) {
   		    sort( $f_arr ); reset( $f_arr );
		    for( $p=0; $p < count( $f_arr ); $p++ ) {
		       if ( in_array(strtolower(file_extension($f_arr[$p])), $supported_ext)) {
		     	   $attached="<img border=0 src=images/smaller".strtolower(file_extension($f_arr[$p])).".jpg 
								title='".$f_arr[$p]."' height=16> ".$attached; 	
		       } 
		       else {
		     	$myimg="<img border=0 src =images/miniquestion.png>";
			   }
    	     }
	      }
      }

	  $S_DESCRIPTION=get_section_name($S_ID);
	  if ( $nbsections <> 1) $organisateur="<font size=1 >(".$S_CODE.")</font>";
	  else $organisateur="";

	  if ( $E_CANCELED == 1 ) $myimg="<img src=images/red.gif title='�v�nement annul�'>";
	  elseif ( $E_CLOSED == 1 ) $myimg="<img src=images/yellow.gif title='inscriptions ferm�es'>";
	  else {
	    $myimg="<img src=images/green.gif title='inscriptions ouvertes'>";
	    // si inscription interdite pour les externes alors on v�rifie si l'agent fait partie d'une sous section 
		//ou d'un niveau plus�lev� : auquel cas on l'autorise.
	  	if (( $nbsections <> 1) and ( $E_OPEN_TO_EXT == 0 ) and ( $mysection <> $S_ID )) {
	  	 	if ( get_section_parent("$mysection") <> get_section_parent("$S_ID")) {
	  	 		$list = preg_split('/,/' , get_family_up("$S_ID"));
	  	 		if (! in_array($mysection,$list)) {
			   		$list = preg_split('/,/' , get_family("$S_ID"));  
			   		if (! in_array($mysection,$list))
	  	 				$myimg="<img src=images/yellow.gif 
						   title='inscriptions interdites pour personnes ext�rieures'>";
	  	 			}
	  		}
	  		else {// je peux inscrire sur les antennes voisines mais pas les d�partements voisins
	  			if ( get_level("$mysection") + 2 <= $nbmaxlevels )
	  				$myimg="<img src=images/yellow.gif 
					  title='inscriptions interdites pour personnes ext�rieures'>";
	  		}
	  	}
	  }
	  $query2="select count(*) as NB from evenement_horaire where E_CODE=".$E_CODE;
      $result2=mysql_query($query2);
      $row2=mysql_fetch_array($result2);
      $nbsessions=$row2["NB"];	  
	  
	  $query2="select count(*) as NP from evenement_participation ep, evenement e
 			  where e.E_CODE=$E_CODE
			  and ep.E_CODE=e.E_CODE
			  and e.E_CANCELED=0
			  and ep.EH_ID=".$EH_ID;
      $result2=mysql_query($query2);
      $row2=mysql_fetch_array($result2);
      $NP=$row2["NP"];
	  
	  $query2="select count(*) as NP from evenement_participation ep, evenement e
 			  where e.E_PARENT=$E_CODE
			  and ep.E_CODE=e.E_CODE
			  and e.E_CANCELED=0
			  and ep.EH_ID=".$EH_ID;
      $result2=mysql_query($query2);
      $row2=mysql_fetch_array($result2);
      $NP=$row2["NP"] + $NP;


	  if ( $E_PARENT <> '' and $E_PARENT > 0 )
	      $b1 = "<td align=left><img src=images/renfortsmall.png height=16 title='renfort sur un autre �v�nement'><a href=evenement_display.php?evenement=".$E_PARENT."></a>";
	  else $b1="<td align=left><img src=images/".$TE_CODE."small.gif height=16 title='".$TE_LIBELLE."'>";

	  $query2="select count(*) as NR from evenement where E_PARENT=".$E_CODE;
	  $result2=mysql_query($query2);
      $row2=mysql_fetch_array($result2);
      $NR=$row2["NR"];
      $b2="";
      if ( $NR > 0 ) $b2 .= "<img src=images/renfortverysmall.png border=0 title='$NR renfort externe' height=16>";
      if ( $NR > 1 ) $b2 .= "<b><font color=green>(x$NR)</font></b>";

	  if ( $nbsessions > 1 )  {
	  	$session="<font size=1 > partie &#x2116; ".$EH_ID."/".$nbsessions."</font>";
	  }
	  else $session="";
	  
      echo "<tr height=10 bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$mycolor'\" onclick=\"this.bgColor='#33FF00'; bouton_redirect('evenement_display.php?evenement=".$E_CODE."&from=choice');\" >
      	  $b1<b> ".$E_LIBELLE.$session."</b> ".$organisateur." ".$b2." ".$attached."</td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
     if ( $type_evenement =='DPS') {
       	$query2="select TA_SHORT from type_agrement_valeur 
		   		 where TA_CODE = 'D' and TAV_ID=".$TAV_ID;
		$result2=mysql_query($query2);
        $row2=mysql_fetch_array($result2);
        $TA_SHORT=$row2["TA_SHORT"];
        echo "<td align=center>".$TA_SHORT."</td>
			  <td bgcolor=$mydarkcolor width=0></td>";
      }
      echo "<td align=center>".$E_LIEU."</td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>".$ladate."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>".$EH_DEBUT."-".$EH_FIN."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
      if ( $E_NB == 0 ) $cmt = $myimg." ".$NP;
      else $cmt = $myimg." ".$NP."/".$E_NB;
      echo "<td align=left>".$cmt."</td>";
      if (check_rights($_SESSION['id'], 29)) {
         if (check_rights($_SESSION['id'], 29, "$S_ID")) 
		 	$myfact=get_etat_facturation($E_CODE, "ico");
         else 
		 	$myfact="";
       	 echo "<td bgcolor=$mydarkcolor width=0></td>
      	       <td align=center><a href=evenement_facturation.php?evenement=".$E_CODE.">".$myfact."</a></td>";
       }
      echo "</tr>";
   }
   echo "</table>";
   echo "</td></tr></table>";
}
else {
     echo "<p><b>Aucune activit&eacute; ne correspond aux crit&egrave;res choisis</b>";
}
echo "<iframe width=132 height=142 name=\"gToday:contrast:agenda.js\" id=\"gToday:contrast:agenda.js\" src=\"ipopeng.htm\" scrolling=\"no\" frameborder=\"0\" style=\"visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;\"></iframe>";
echo "</BODY>
</HTML>";

?>
