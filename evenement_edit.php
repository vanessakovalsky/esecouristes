<?php

  # written by: Nicolas MARCHE, Jean-Pierre KUNTZ, Vanessa KOVALSKY
  # contact: vanessa.kovalsky@free.fr
  # project: esecouristes
  # homepage: http://sourceforge.net/projects/ebrigade/
  # version: 0.1

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
$section=$_SESSION['SES_SECTION'];
writehead();
?>
<script type='text/javascript' src='checkForm.js'></script>
<script type='text/javascript' src='dateFunctions.js'></script>

<script>
function deletefile(event, file) {
   if ( confirm ("Voulez vous vraiment supprimer le fichier " + file +  "?" )) {
         self.location = "delete_event_file.php?number=" + event + "&file=" + file + "&type=evenement";
   }
}

function warning_cancel(checkbox) {
  if (checkbox.checked)
     alert("Attention : vous devez renseigner la raison de cette annulation dans la case ci contre.\n(manque de secouristes, probl�me de mat�riel, annul� par l'organisateur ...)" );
    }
 
function change() 
{ 
	what=document.getElementById('type');
	if  (what.value == 'ALL' ) {
		document.getElementById("sauver").disabled=true;
	}
	else {
		document.getElementById("sauver").disabled=false;
	}
	if  (what.value == 'DPS' ) {
	 	document.getElementById('rowflag1').style.display = '';
	 	document.getElementById('rowpp').style.display = '';
	}
	else  {
	    document.getElementById('rowflag1').style.display = 'none';
	    document.getElementById('flag1').checked = false;
	     document.getElementById('rowpp').style.display = 'none';
	    document.getElementById('pp').checked = false;
	}
}

function showNextRow(debrow,finrow,heurerow,lieurow,prevplusrow,plusrow,flag,nextdeb) {
	document.getElementById(prevplusrow).style.display = 'none';
	document.getElementById(debrow).style.display = '';
	document.getElementById(finrow).style.display = '';
	document.getElementById(heurerow).style.display = '';
	document.getElementById(lieurow).style.display = '';
	if ( flag == 0 ) {
	    if ( document.getElementById(nextdeb).style.display == 'none' ) {
			document.getElementById(plusrow).style.display = '';
		}
	}
}

function hideRow(debrow,finrow,heurerow,lieurow,prevplusrow,plusrow,date1,date2,debut,fin,duree) {
	document.getElementById(date1).value = '';
	document.getElementById(date2).value = '';
	document.getElementById(debut).value = '';
	document.getElementById(fin).value = '';
	document.getElementById(duree).value = '';
	document.getElementById(heure_rdv).value = '';
	document.getElementById(lieu_rdv).value = '';
	document.getElementById(prevplusrow).style.display = '';
	document.getElementById(debrow).style.display = 'none';
	document.getElementById(finrow).style.display = 'none';
	document.getElementById(heurerow).style.display = 'none';
	document.getElementById(lieurow).style.display = 'none';
	document.getElementById(plusrow).style.display = 'none';
}

function makeVisibleExternal(checkbox) {
    if (checkbox.checked) {
       document.getElementById('rowcomment2').style.display = '';
    }
    else {
       document.getElementById('rowcomment2').style.display = 'none';
    }
}

function updfin(dtdebut,dtfin) {
   checkDate2(dtdebut);
   if ( dtfin.value == '' ) {
      dtfin.value = dtdebut.value;
   }
}
</script>
 
<STYLE type="text/css">
.categorie{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
.type{color:<?php echo $mydarkcolor; ?>; background-color:white; font-size:9pt;}
</STYLE>

</head>
<?php

function display_evt_accepte_renfort($evt,$renfortde="null"){
// Affiche les �v�nements de m�me type aux m�mes dates de d�but et fin
// e1 : Evenement renfort possible
// e2 : Evenement courant
$out='';

$sql = "select e1.e_code, e1.e_libelle , e1.s_id
from evenement e1, evenement e2 , evenement_horaire eh1, evenement_horaire eh2
where eh1.eh_date_debut = eh2.eh_date_debut
and eh1.eh_date_fin = eh2.eh_date_fin
and e1.te_code = e2.te_code
and e1.e_code = eh1.e_code
and e2.e_code = eh2.e_code
and e1.E_ALLOW_REINFORCEMENT = 1
and e2.e_code=$evt
and e1.e_code<>$evt
union
select e.e_code, e.e_libelle , e.s_id
from evenement e
where e.e_code=".$renfortde;

$res= mysql_query($sql);
	while($row=mysql_fetch_array($res)){
		$out .= "\n<option value=\"".$row['e_code']."\" ".(($renfortde==$row['e_code'])?" selected":"").">(".get_section_code($row['s_id']).") ".$row['e_libelle']."</option>";
	}
	echo "<select name=\"parent\" title=\"Ev�nement(s) � la m�me date\">";
	if ( $renfortde == "null" ) 
		echo "<option value=\"null\">Lier en tant que renfort de...</option>";
	else 
		echo "<option value=\"null\">D�sactiver le renfort</option>";
	echo $out;
	echo "</select>";
}

$action=$_GET["action"];
$copydetailsfrom='';
$copycheffrom='';
$copymode='';

if ( $action == "copy" ) {
	if (! isset($_GET["copymode"])) {
		$evenement=intval($_GET["evenement"]);
		$nbrenforts=get_nb_renforts($evenement);
		if ( $nbrenforts > 0 ) $avec = '+ renforts';
		else $avec='';
		$message = "Vous allez dupliquer cet �v�nement du calendrier.";
		$message .= " Vous pourrez modifier les param�tres (date, heure, lieu ...).";
		$message .= " Veuillez pr�ciser comment l'�v�nement doit �tre dupliqu�:";
		$message .= "<p><a href=evenement_edit.php?evenement=".$evenement."&action=copy&copymode=simple>
				 Ev�nement seul</a>";
		$message .= "<p><a href=evenement_edit.php?evenement=".$evenement."&action=copy&copymode=matos>
				 Ev�nement + v�hicules + mat�riel </a>";
		$message .= "<p><a href=evenement_edit.php?evenement=".$evenement."&action=copy&copymode=perso>
				 Ev�nement + personnel</a>";
		$message .= "<p><a href=evenement_edit.php?evenement=".$evenement."&action=copy&copymode=full>
				 Ev�nement $avec + personnel + v�hicules + mat�riel</a>";
		$message .= "<p><a href=evenement_display.php?evenement=".$evenement.">Annuler la duplication</a>";
		write_msgbox("question", $question_pic, $message, 30,30, 600 );
		exit;
	}
	else {
	 	$copymode=$_GET["copymode"];
	 	$copycheffrom=intval($_GET["evenement"]);
	 	if ($copymode == 'full' or $copymode == 'matos' or $copymode == 'perso') {
 			$copydetailsfrom=intval($_GET["evenement"]);
 		}
 	}
}

if ( $action == "create" ) {
   $MYTE_CODE="";
   $MYE_LIBELLE="";
   $MYE_LIEU="";
   $MYS_ID=$section;
   $MYE_NB="0";
   $MYE_FLAG1="0";
   $MYE_PP="0";
   $MYE_FILE="";
   $MYE_CHEF="0";
   $MYE_COMMENT="";
   $MYE_COMMENT2="";
   $MYE_CANCEL_DETAIL="";
   $MYC_ID="";
   $MYE_CONTACT_LOCAL="";
   $MYE_CONTACT_TEL="";
   $MYE_CLOSED="0";
   $MYE_OPEN_TO_EXT="0";
   $MYE_CANCELED="0";
   $MYE_MAIL1="0";
   $MYE_MAIL2="0";
   $MYE_MAIL3="0";
   $MYE_CONVENTION="";
   $MYE_NB1="null";
   $MYE_NB2="null";
   $MYE_NB3="null";
   $MYE_PARENT="null";
   $MYE_ADDRESS="";
   $MYE_VISIBLE_OUTSIDE="0";
   $MYE_ALLOW_REINFORCEMENT="0";
	$MYE_CONSIGNES="";
	$MYE_MOYENS="";
	$MYE_NB_VPSP="0";
	$MYE_NB_AUTRES_VEHICULES="0";
	$MYE_CLAUSES="";
	$MYE_CLAUSES2="";
    $MYE_REPAS="";
	$MYE_TRANSPORT="";
}

$MYEH_ID=array();
$MYE_DEBUT=array();
$MYE_FIN=array();
$MYE_DUREE=array();
$MYE_DEBUT=array();
$MYE_DATE_DEBUT=array();
$MYE_DATE_FIN=array();
$MYE_HEURE_RDV=array();
$MYE_LIEU_RDV=array();

for ( $i=1; $i <= $nbmaxsessionsparevenement; $i++) {
   $MYEH_ID[$i]=$i;
   $MYE_DEBUT[$i]="8:00";
   $MYE_FIN[$i]="17:00";
   $MYE_DUREE[$i]="9";
   $MYE_DATE_DEBUT[$i]="";
   $MYE_DATE_FIN[$i]="";
   $MYE_HEURE_RDV[$i]="8:00";
   $MYE_LIEU_RDV[$i]="Au si&egrave;ge de l'association";
} 

if (( $action == "update" ) or ( $action == "copy" ) or ( $action == "renfort" )) {
   $evenement=$_GET["evenement"];
   // check input parameters
	$evenement=intval($evenement);
	if ( $evenement == 0 ) {
		param_error_msg();
		exit;
	}
   $query="select TE_CODE,E_LIBELLE,E_LIEU,S_ID,E_CHEF,E_ALLOW_REINFORCEMENT,EH_ID,
	   		DATE_FORMAT(EH_DATE_DEBUT, '%d-%m-%Y') as EH_DATE_DEBUT,
	   		DATE_FORMAT(EH_DATE_FIN, '%d-%m-%Y') as EH_DATE_FIN,
            TIME_FORMAT(EH_DEBUT, '%k:%i') as EH_DEBUT,
	   		TIME_FORMAT(EH_FIN, '%k:%i') as EH_FIN, EH_DUREE, TIME_FORMAT(EH_HEURE_RDV, '%k:%i') as EH_HEURE_RDV, EH_LIEU_RDV, E_CONVENTION, E_CONSIGNES, E_MOYENS_INSTALLATION,  E_NB_VPSP, E_NB_AUTRES_VEHICULES, E_CLAUSES_PARTICULIERES, E_CLAUSES_PARTICULIERES2, E_REPAS, E_TRANSPORT, E_OPEN_TO_EXT, E_PARENT, 
	   		E_NB, E_NB_DPS, E_COMMENT, E_COMMENT2, E_CLOSED, E_CANCELED, E_MAIL1,E_MAIL2, E_MAIL3, E_NB1, E_NB2,E_NB3, E_CANCEL_DETAIL,
	   		C_ID, E_CONTACT_LOCAL, E_CONTACT_TEL, E_FLAG1, E_PP, E_ADDRESS, E_VISIBLE_OUTSIDE
	   		from evenement, evenement_horaire
	   		where evenement.E_CODE = evenement_horaire.E_CODE
			and evenement.E_CODE=".$evenement."
			order by EH_ID";
   $result=mysql_query($query);
   while ( $row=mysql_fetch_array($result) ) {
   $z=$row["EH_ID"];
   if ( $z == 1 ) {
	 $MYTE_CODE=$row["TE_CODE"];
	 $MYE_LIBELLE=stripslashes($row["E_LIBELLE"]);
	 $MYE_LIEU=stripslashes($row["E_LIEU"]);
     $MYE_CHEF=$row["E_CHEF"];
     $MYS_ID=$row["S_ID"];
     $MYE_NB=$row["E_NB"];
     $MYE_FLAG1=$row["E_FLAG1"];
     $MYE_PP=$row["E_PP"];
     $MYE_NB_DPS=$row["E_NB_DPS"];
     $MYE_FILE="";
     $MYE_COMMENT=stripslashes($row["E_COMMENT"]);
     $MYE_COMMENT2=stripslashes($row["E_COMMENT2"]);
     $MYE_PARENT=$row["E_PARENT"];
     $MYE_CLOSED=$row["E_CLOSED"];
     $MYE_OPEN_TO_EXT=$row["E_OPEN_TO_EXT"];
     $MYE_CANCELED=$row["E_CANCELED"];
     $MYE_CANCEL_DETAIL=$row["E_CANCEL_DETAIL"];
     $MYC_ID=$row["C_ID"];
     $MYE_CONTACT_LOCAL=$row["E_CONTACT_LOCAL"];
     $MYE_CONTACT_TEL=$row["E_CONTACT_TEL"];
     $MYE_MAIL1=$row["E_MAIL1"];
     $MYE_MAIL2=$row["E_MAIL2"];
     $MYE_MAIL3=$row["E_MAIL3"];
     $MYE_CONVENTION=$row["E_CONVENTION"];
	 $MYE_CONSIGNES=stripslashes($row["E_CONSIGNES"]);
	 $MYE_MOYENS=stripslashes($row["E_MOYENS_INSTALLATION"]);
	 $MYE_NB_VPSP=$row["E_NB_VPSP"];
	 $MYE_NB_AUTRES_VEHICULES=$row["E_NB_AUTRES_VEHICULES"];
	 $MYE_CLAUSES=stripslashes($row["E_CLAUSES_PARTICULIERES"]);
	 $MYE_CLAUSES2=stripslashes($row["E_CLAUSES_PARTICULIERES2"]);
     $MYE_REPAS=$row["E_REPAS"];
	 $MYE_TRANSPORT=$row["E_TRANSPORT"];
	 $MYE_ADDRESS=stripslashes($row["E_ADDRESS"]);
	 $MYE_VISIBLE_OUTSIDE=$row["E_VISIBLE_OUTSIDE"];
     $MYE_ALLOW_REINFORCEMENT=$row["E_ALLOW_REINFORCEMENT"];
     $MYE_NB1=$row["E_NB1"];
     $MYE_NB2=$row["E_NB2"];
     $MYE_NB3=$row["E_NB3"];
     if ( $MYE_NB1 == '') $MYE_NB1="null";
     if ( $MYE_NB2 == '') $MYE_NB2="null";
     if ( $MYE_NB3 == '') $MYE_NB3="null";
   }
   $MYE_ID[$z]=$row["EH_ID"];
   $MYE_DATE_DEBUT[$z]=$row["EH_DATE_DEBUT"];
   $MYE_DATE_FIN[$z]=$row["EH_DATE_FIN"];
   $MYE_DEBUT[$z]=$row["EH_DEBUT"];
   $MYE_FIN[$z]=$row["EH_FIN"];
   $MYE_DUREE[$z]=$row["EH_DUREE"];
   $MYE_HEURE_RDV[$z]=$row["EH_HEURE_RDV"];
   $MYE_LIEU_RDV[$z]=$row["EH_LIEU_RDV"];
   }
}
if ( $action == "renfort" ) {
    $MYE_PARENT=$evenement;
    $MYE_LIBELLE="Renfort ".$MYE_LIBELLE;
}
	
if ( $MYE_PARENT == '' ) $MYE_PARENT='null';

if (( $action == "create" ) or ($action == "copy" ) or ( $action == "renfort" )) {
   $evenement=0;
}
if ( $action == "renfort" ) $MYS_ID=$section;


if ( $id <> $MYE_CHEF ) {
	check_all(15);
	if (! check_rights($id, 15, "$MYS_ID")) check_all(24);
}

$mysection=get_highest_section_where_granted($id,15);
if ( $mysection == '' ) $mysection=$section;
if ( ! is_children($section,$mysection)) $mysection=$section;


//=====================================================================
// debut tableau
//=====================================================================


echo "<body onload='change();'>";

if ($copymode == 'full') $txt="Duplication compl�te d'un �v�nement";
else if ($copymode == 'perso' or $copymode == 'matos') $txt="Duplication d'un �v�nement";
else if ($copymode == 'simple') $txt="Duplication simple d'un �v�nement";
else $txt='Saisie &eacute;v&egrave;nement';
echo "<div align=center><font size=4><b>".$txt."</b></font><br>";

echo "<form name=demoform action='evenement_save.php' method='POST' enctype='multipart/form-data'>";

echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
//=====================================================================
// ligne 1
//=====================================================================
echo "<tr><td CLASS='MenuRub' colspan=3>informations</td></tr>";


echo "<input type='hidden' name='copydetailsfrom' value='$copydetailsfrom'>";
echo "<input type='hidden' name='copymode' value='$copymode'>";
echo "<input type='hidden' name='copycheffrom' value='$copycheffrom'>";

//=====================================================================
// type
//=====================================================================
echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Type d'&eacute;v&egrave;nement</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";


echo "<select id='type' name='type' onchange='change()'>";
echo "<option value='ALL'>Choisir un type ...</option>";

/*$query="select distinct te.CEV_CODE, ce.CEV_DESCRIPTION, te.TE_CODE, te.TE_LIBELLE
        from type_evenement te, categorie_evenement ce
		where te.CEV_CODE=ce.CEV_CODE";
if (( $action == 'create' ) or ( $MYTE_CODE <> 'INS' )) $query .= " and TE_CODE <> 'INS' ";
$query .= " order by te.CEV_CODE desc, te.TE_CODE asc";
$result=mysql_query($query);
$prevCat='';



while ($row=@mysql_fetch_array($result)) {
      $TE_CODE=$row["TE_CODE"];
      $TE_LIBELLE=$row["TE_LIBELLE"];
      $CEV_DESCRIPTION=$row["CEV_DESCRIPTION"];
      $CEV_CODE=$row["CEV_CODE"];
      if ( $prevCat <> $CEV_CODE ){
       	echo "<optgroup class='categorie' label='".$CEV_DESCRIPTION."'";
        echo ">".$CEV_DESCRIPTION."</option>\n";
      }
      $prevCat=$CEV_CODE;
      echo "<option class='type' value='".$TE_CODE."' title=\"".$TE_LIBELLE."\"";
      if ($TE_CODE == $MYTE_CODE ) echo " selected ";
      echo ">".$TE_LIBELLE."</option>\n";
}*/

    
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
    
echo "</select>";
echo " </tr>";

//=====================================================================
// DPS inter associatif?
//=====================================================================

if ( $MYE_FLAG1 == 1 )$checked="checked";
else $checked="";

if ($MYTE_CODE <> 'DPS' ) $style="";
else  $style="style='display:none'";

echo "<tr id='rowflag1' $style>
      	  <td bgcolor=$mylightcolor><b>DPS interassociatif?</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='checkbox' name='flag1' id='flag1' value='1' $checked
			 title='Cocher cette case si le DPS est de type interassociatif'></td>";		
echo "</tr>";

//===============================================================
// DPS demand� par les pouvoirs publics ?
//===============================================================

if ( $MYE_PP == 1 )$checked="checked";
else $checked="";

if ($MYTE_CODE <> 'DPS' ) $style="";
else  $style="style='display:none'";

echo "<tr id='rowpp' $style>
      	  <td bgcolor=$mylightcolor><b>DPS demand� par les pouvoirs publics?</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='checkbox' name='pp' id='pp' value='1' $checked
			 title='Cocher cette case si le DPS a �t� demand� par les pouvoirs publics'></td>";		
echo "</tr>";


//=====================================================================
// section organisatice
//=====================================================================
   
if (  $nbsections == 1 ) {
	echo "<input type='hidden' name='section' value='0'>";
}
else {
 	echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Organisation par</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
 	echo "<select id='section' name='section'>"; 

   $level=get_level($mysection);
   if ( $level == 0 ) $mycolor=$myothercolor;
   elseif ( $level == 1 ) $mycolor=$my2darkcolor;
   elseif ( $level == 2 ) $mycolor=$my2lightcolor;
   elseif ( $level == 3 ) $mycolor=$mylightcolor;
   else $mycolor='white';
   $class="style='background: $mycolor;'";
   
   
   if (check_rights($_SESSION['id'], 24))
   	  display_children2(-1, 0, $MYS_ID, $nbmaxlevels);
   else {
        $list = preg_split('/,/' , get_family("$mysection"));
        if (in_array($MYS_ID, $list) or ($mysection == $MYS_ID )) {
   			echo "<option value='$mysection' $class >".str_repeat(". ",$level)." ".
      			get_section_code($mysection)." - ".get_section_name($mysection)."</option>";
   		    display_children2($mysection, $level +1, $MYS_ID, $nbmaxlevels);
   		}
   		else
   			echo "<option value='$MYS_ID' $class selected>".
				get_section_code($MYS_ID)." - ".get_section_name($MYS_ID)."</option>";
   }
   echo "</select></td> ";
   echo "</tr>";	  
}

//=====================================================================
// description
//=====================================================================
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Libell&eacute;</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='text' name='libelle' size='40' value=\"$MYE_LIBELLE\" colspan=2>";		
echo "</tr>";

//=====================================================================
// lieu
//=====================================================================
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Lieu</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>
			<input type='text' name='lieu' size='50' value=\"$MYE_LIEU\">";		
echo "</tr>";

//=====================================================================
// nombre de personnes requises
//===================================================================== 
echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Nombre maximum personnes</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
echo "<select id='nombre' name='nombre'>";
for ( $i=1; $i <= 49; $i++ ) {
	if ( $i == $MYE_NB ) $selected="selected";
	else $selected="";
	echo "<option value=".$i." $selected>".$i."</option>\n";
}
for ( $i=50; $i <= 100; $i = $i +1 ) {
	if ( $i == $MYE_NB ) $selected="selected";
	else $selected="";
	echo "<option value=".$i." $selected>".$i."</option>\n";
}
if ( $MYE_NB == 0 ) $selected="selected";
	else $selected="";
echo "<option value=0 $selected>pas de limite</option>";
echo "</select>";

$dim=false;
if ( $MYTE_CODE == 'DPS' ){
	// le chef, le cadre de l'�v�nement ont toujours acc�s � cette fonctionnalit�, les autres doivent avoir 15 ou 24
	if (check_rights($_SESSION['id'],15,get_section_organisatrice($evenement)))
		$dim=true;
	else if ( get_chef_evenement ( $evenement ) == $_SESSION['id'] )
		$dim=true;
	else if ( get_cadre (get_section_organisatrice ( $evenement )) == $_SESSION['id'] )
		$dim=true;
	
	if ( $MYE_PARENT <> 'null' ) echo " <a href=evenement_display.php?evenement=$MYE_PARENT >Voir �v�nement principal</a>";			
	else echo " Effectif minimum: <b>".(isset($MYE_NB_DPS)?$MYE_NB_DPS:" ? ")."</b>";		
}
if ( $dim and ( $MYE_PARENT == 'null' ))
  echo " <a href='dps.php?evenement=$evenement' target='_blank'>
  <img src='images/calculette.png' height='24' border='0' alt='Dimensionnement DPS' title='Dimensionnement DPS'></a>";

echo "</td></tr>";

//=====================================================================
// ouvert aux personnes externes
//=====================================================================

if ( $MYE_OPEN_TO_EXT == 1 )$checked="checked";
else $checked="";

if ( $nbsections <> 1 ) {
echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Ouvert aux autres ".$niv3."s</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='checkbox' name='open_to_ext' value='1' $checked></td>";		
echo "</tr>";
}
else echo "<input name='open_to_ext' type='hidden' value='1'>";

//=====================================================================
// accepter les renforts mais pas les sous-renforts
//=====================================================================

if ( $action == "renfort" ) {
 	$disabled = 'disabled';
 	$checked='';
}
else if (( $action == "update" ) and ( $MYE_PARENT <> 'null' )) {
  	$disabled = 'disabled';
 	$checked='';
}
else {
 	if ( $MYE_ALLOW_REINFORCEMENT == 1 )$checked="checked";
	else $checked="";
 	$disabled = '';
}

if ( $nbsections == 0 ) {
echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Renforts possibles</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>
				<input type='checkbox' name='allow_reinforcement' value='1' $checked $disabled>
		  </td>";		
echo "</tr>";
}
else echo "<input name='allow_reinforcement' type='hidden' value='1'>";

//=====================================================================
// date heure d�but
//=====================================================================

for ($k=1; $k <= $nbmaxsessionsparevenement; $k++) {

if ($k==1 or ($MYE_DATE_DEBUT[$k] <> '') )  $style="";
else  $style="style='display:none'";

$next = $k + 1;
$previous = $k - 1;
echo "<tr id=debrow[".$k."] $style>
      <td bgcolor=$mylightcolor rowspan=2><b>Dates partie &#x2116; ".$k."</b> ";
		  
if ( $k == 1 ) echo " <font color=red>*</font>";
else {
	echo "<img src=images/trash.png title='Supprimer cette partie' 
	onclick=\"javascript:hideRow('debrow[$k]','finrow[$k]','heurerow[$k]','lieurow[$k]','plusrow[$previous]','plusrow[$k]','dc1_$k','dc2_$k','debut_$k','fin_$k','duree_$k');\">";
}	
echo "</td>";

echo " <td bgcolor=$mylightcolor align=left> du <font color=red>*</font>";
echo "<input class=\"plain\" name=\"dc1_$k\" id=\"dc1_$k\" value=\"".$MYE_DATE_DEBUT[$k]."\"
size=\"12\" onchange=\"updfin(document.demoform.dc1_$k,document.demoform.dc2_$k);\" title=\"Date d&eacute;but format jj-mm-yyyy\">
<a href=\"javascript:void(0)\" onclick=\"if(self.gfPop)gfPop.fStartPop(document.demoform.dc1_$k,document.demoform.dc2_$k);return false;\" HIDEFOCUS>
<img name=\"popcal\" align=\"absmiddle\" src=\"images/calbtn.gif\" width=\"34\" height=\"22\" border=\"0\" alt=\"\" 
onblur=\"updfin(document.demoform.dc1_$k,document.demoform.dc2_$k);\"></a>";

echo " &agrave; <select id='debut_$k' name='debut_$k' 
onchange=\"EvtCalcDuree(document.demoform.dc1_$k,document.demoform.dc2_$k,document.demoform.debut_$k,document.demoform.fin_$k,document.demoform.duree_$k);\">";
for ( $i=0; $i <= 24; $i++ ) {
    $check = $i.":00";
    if (  $check == $MYE_DEBUT[$k] ) $selected="selected";
    else $selected="";
    echo "<option value=".$i.":00 ".$selected.">".$i.":00</option>\n";
    if ( $i.":30" == $MYE_DEBUT[$k] ) $selected="selected";
    else $selected="";
    if ( $i < 24 )
       echo "<option value=".$i.":30 ".$selected.">".$i.":30</option>\n";
}
echo "</select>";

echo "<td bgcolor=$mylightcolor rowspan=2>dur&eacute;e ";
echo "<input type=\"text\" name=\"duree_$k\" id=\"duree_$k\" value=\"".$MYE_DUREE[$k]."\" size=\"3\" length=3
onfocus=\"EvtCalcDuree(document.demoform.dc1_$k,document.demoform.dc2_$k,document.demoform.debut_$k,document.demoform.fin_$k,document.demoform.duree_$k);\" 
title='dur&eacute;e en heures de la partie &#x2116; $k'>h ";
echo "</td>";

echo "<tr id=finrow[".$k."] $style>";
echo "<td bgcolor=$mylightcolor align=left> au <font color=red>*</font>";
echo "<input class=\"plain\" name=\"dc2_$k\" id=\"dc2_$k\" value=\"".$MYE_DATE_FIN[$k]."\"
size=\"12\" onchange=\"checkDate2(document.demoform.dc2_$k)\" title=\"Date fin format jj-mm-yyyy\">
<a href=\"javascript:void(0)\" onclick=\"if(self.gfPop)gfPop.fEndPop(document.demoform.dc1_$k,document.demoform.dc2_$k);return false;\" HIDEFOCUS>
<img name=\"popcal\" align=\"absmiddle\" src=\"images/calbtn.gif\" width=\"34\" height=\"22\" border=\"0\" alt=\"\" ></a>";
echo " &agrave; <select id='fin_$k' name='fin_$k' 
onchange=\"EvtCalcDuree(document.demoform.dc1_$k,document.demoform.dc2_$k,document.demoform.debut_$k,document.demoform.fin_$k,document.demoform.duree_$k);\">";
for ( $i=0; $i <= 24; $i++ ) {
   if ( $i.":00" == $MYE_FIN[$k] ) $selected="selected";
   else $selected="";
   echo "<option value=".$i.":00 $selected>".$i.":00</option>\n";
   if ( $i.":30" == $MYE_FIN[$k] ) $selected="selected";
   else $selected="";
   if ( $i < 24 )
      echo "<option value=".$i.":30 $selected>".$i.":30</option>\n";	  
}
echo "</select></td></tr>";

echo "<tr id=heurerow[".$k."] $style>
      	  <td bgcolor=$mylightcolor ><b>Heure du rdv pour les intervenants secouristes</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
	echo " <select name='heure_rdv_$k' id='heure_rdv_$k'>";
		for ( $i=0; $i <= 24; $i++ ) {
		    //$check = $i.":00";
			if (  $i.":00" == $MYE_HEURE_RDV[$k] )  {
	    			echo "<option value=".$i.":00 selected>".$i.":00</option>\n";
				echo "<option value=".$i.":15>".$i.":15</option>\n";
	       			echo "<option value=".$i.":30>".$i.":30</option>\n";
				echo "<option value=".$i.":45>".$i.":45</option>\n";
				
			}
			elseif ( $i.":15" == $MYE_HEURE_RDV[$k] ) {
				echo "<option value=".$i.":00 >".$i.":00</option>\n";
				echo "<option value=".$i.":15 selected >".$i.":15</option>\n";
	       			echo "<option value=".$i.":30 >".$i.":30</option>\n";
				echo "<option value=".$i.":45 >".$i.":45</option>\n";
			}	
			elseif ( $i.":30" == $MYE_HEURE_RDV[$k] ) {
				echo "<option value=".$i.":00 >".$i.":00</option>\n";
				echo "<option value=".$i.":15 >".$i.":15</option>\n";
	       			echo "<option value=".$i.":30 selected>".$i.":30</option>\n";
				echo "<option value=".$i.":45 >".$i.":45</option>\n";
			}
			elseif ( $i.":45" == $MYE_HEURE_RDV[$k] ) {
				echo "<option value=".$i.":00 >".$i.":00</option>\n";
				echo "<option value=".$i.":15 >".$i.":15</option>\n";
	       			echo "<option value=".$i.":30 >".$i.":30</option>\n";
				echo "<option value=".$i.":45 selected>".$i.":45</option>\n";
			}
			else {
				$selected="";
	   			if ( $i < 24 ) {
				echo "<option value=".$i.":00 >".$i.":00</option>\n";
				echo "<option value=".$i.":15 >".$i.":15</option>\n";
	       			echo "<option value=".$i.":30 >".$i.":30</option>\n";
				echo "<option value=".$i.":45 >".$i.":45</option>\n";
				}	
			}
		}
	echo "</select>";
	echo "</tr>";
	
	echo "<tr id=lieurow[".$k."] $style>
      	  <td bgcolor=$mylightcolor ><b>Lieu du RDV pour les intervenants secouristes</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='text' name='lieu_rdv_$k' id='lieu_rdv_$k' size='30' value=\"".$MYE_LIEU_RDV[$k]."\">";		
	echo "</tr>";

if ( $k == 1 and $MYE_DATE_DEBUT[$k] == "" ) $style="style=''";
else if (isset ($MYE_DATE_DEBUT[$k+1])) {
	if ($MYE_DATE_DEBUT[$k+1] <> "")  {
	 	$style="style='display:none'";
	}
	else if (isset ($MYE_DATE_DEBUT[$k])) {
	 	if ($MYE_DATE_DEBUT[$k] == "") $style="style='display:none'";
	}
	else $style="style=''";
}
else  $style="style='display:none'";


if ( $k <= $nbmaxsessionsparevenement ) {
    if ($k + 1 == $nbmaxsessionsparevenement ) $last = 1;
	else $last = 0;
	$afternext = $next + 1;
	echo "<tr id='plusrow[$k]' $style>
	<td bgcolor=$mylightcolor ></td>
	<td bgcolor=$mylightcolor align=center colspan=2>
	<img src=images/plusgreen.png border=0 title='Ajouter une partie n�$k dates/heures '
	onclick=\"javascript:showNextRow('debrow[$next]','finrow[$next]','heurerow[$next]','lieurow[$next]','plusrow[$k]','plusrow[$next]',$last,'debrow[$afternext]');\" >
	</td></tr>";
 }
}

//=====================================================================
// inscriptions ferm�es
//=====================================================================

if ( $MYE_CLOSED == 1 )$checked="checked";
else $checked="";

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Inscriptions ferm&eacute;es</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='checkbox' name='closed'  value='1' $checked></td>";		
echo "</tr>";
      
	  
//=====================================================================
// �v�nement annul�
//=====================================================================

if ( $MYE_CANCELED == 1 )$checked="checked";
else $checked="";

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>&Eacute;v&egrave;nement annul&eacute;</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>
				<input type='checkbox' name='canceled'  value='1' $checked 	onclick='warning_cancel(this)'>
				<font size=1> Pourquoi? </font>
				<input type='text' name='cancel_detail' size='22' value=\"$MYE_CANCEL_DETAIL\"></td>";		
echo "</tr>";


//=====================================================================
// visible outside
//=====================================================================

if ( $MYE_VISIBLE_OUTSIDE == 1 )$checked="checked";
else $checked="";

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Visible de l'ext&eacute;rieur</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>
				<input type='checkbox' name='visible_outside'  value='1' $checked onclick='makeVisibleExternal(this)'
				title=\"Si cette case est coch&eacute;e, l'&eacute;v&egrave;nement peut &ecirc;tre visible sans identification dans un site web externe\">";		
echo "</tr>";

//=====================================================================
// adresse facultatif
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Adresse exacte<br>avec code postal</b>
		  <img src='images/miniquestion.png' border=0 title=\"si l'adresse renseign�e est correcte, alors un lien Google Maps est activ�\">
		  </td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
echo "<input type='text' name='address' size='45' value=\"$MYE_ADDRESS\" title=\"Saisir ici l'adresse exacte de l'�v�nement, pour g�olocalisation google maps\"></td>";
echo "</tr>";

//=====================================================================
// commentaire facultatif
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Commentaire </b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
echo "<textarea name='comment' cols='50' rows='3' style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;' value=\"$MYE_COMMENT\">".$MYE_COMMENT."</textarea></td>";
echo "</tr>";

//=====================================================================
// commentaire ext�rieur
//=====================================================================
if ( $MYE_VISIBLE_OUTSIDE == 1 )  $style="style=''";
else  $style="style='display:none'";
echo "<tr id=rowcomment2 $style>
      	  <td bgcolor=$mylightcolor><b>Commentaire ext�rieur</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
echo "<textarea name='comment2' id='comment2' cols='50' rows='3' style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;' value=\"$MYE_COMMENT2\">".$MYE_COMMENT2."</textarea></td>";
echo "</tr>";

//=====================================================================
// entreprise
//=====================================================================

if ( $nbsections == 0 ) {
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Pour le compte de:</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
	echo "<select id='company' name='company'>";
	if ( $MYC_ID == "" ) { 
		$selected='selected';
		$MYC_ID = 0;
	}
	else $selected ='';
	echo "<option value='' $selected >... Non pr&eacute;cis&eacute; ...</option>";      	  
	echo companychoice($mysection,$MYC_ID,$includeparticulier=false);
	echo "</select>";
	echo "</tr>";
	
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Nom du contact sur place</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='text' name='contact_name' size='30' value=\"$MYE_CONTACT_LOCAL\">";		
	echo "</tr>";
	
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>T&eacute;l du contact sur place</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='text' name='contact_tel' id='contact_tel'  size='20' value=\"$MYE_CONTACT_TEL\" onchange='checkPhone(form.contact_tel,\"$MYE_CONTACT_TEL\");'>";		
	echo "</tr>";
	
}
else {
 	echo "<input type='hidden' name='company' value=''>";
    echo "<input type='hidden' name='contact_name' value=''>";
    echo "<input type='hidden' name='contact_tel' value=''>";
}

//=====================================================================
// convention
//=====================================================================
if ($MYTE_CODE == 'DPS' ) $style2="";
else  $style2="style='display:none'";

if ( $nbsections == 0 ) {
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>&#x2116; Convention</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='text' name='convention' size='20' value=\"$MYE_CONVENTION\">";		
	echo "</tr>";
}
else echo "<input type='hidden' name='convention' value=''>";

	echo "<tr $style2>
      	  <td bgcolor=$mylightcolor ><b>Consignes pour les intervenants secouristes</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
	echo "<textarea name='consignes' id='consignes' cols='50' rows='3' style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;' value=\"$MYE_CONSIGNES\">".$MYE_CONSIGNES."</textarea></td>";	
	echo "</tr>";

if ( $nbsections == 0 ) {
	echo "<tr $style2>
      	  <td bgcolor=$mylightcolor ><b>Nombre de VPSP pr�vus</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='text' name='nb_vpsp' size='20' value=\"$MYE_NB_VPSP\">";		
	echo "</tr>";
}
else echo "<input type='hidden' name='nb_vpsp' value=''>";

if ( $nbsections == 0 ) {
	echo "<tr $style2>
      	  <td bgcolor=$mylightcolor ><b>Nombre d'autres v�hicules pr�vus</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2><input type='text' name='nb_autres_vehicules' size='20' value=\"$MYE_NB_AUTRES_VEHICULES\">";		
	echo "</tr>";
}
else echo "<input type='hidden' name='nb_autres_vehicules' value=''>";

	echo "<tr $style2>
      	  <td bgcolor=$mylightcolor ><b>Moyens d'installation</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
	echo "<textarea name='moyens' id='moyens' cols='50' rows='3' style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;' value=\"$MYE_MOYENS\">".$MYE_MOYENS."</textarea></td>";		
	echo "</tr>";

	echo "<tr $style2>
      	  <td bgcolor=$mylightcolor ><b>Clause particuli�re</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
	echo "<textarea name='clauses' id='clauses' cols='50' rows='3' style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;' value=\"$MYE_CLAUSES\">".$MYE_CLAUSES."</textarea></td>";		
	echo "</tr>";


	echo "<tr $style2>
      	  <td bgcolor=$mylightcolor ><b>Clause particuli�re 2</b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
	echo "<textarea name='clauses2' id='clauses2' cols='50' rows='3' style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;' value=\"$MYE_CLAUSES2\">".$MYE_CLAUSES2."</textarea></td>";	
	echo "</tr>";

if ( $nbsections == 0 ) {
	echo "<tr $style2>
      	  <td bgcolor=$mylightcolor ><b>Repas fournis par l'organisateur : </b></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
	echo "<select id='repas' name='repas'>";
	echo "<option value='oui' ";
	if ($MYE_REPAS == "oui") 
	echo "SELECTED ";
	echo ">oui</option>";  
	echo "<option value='non'";
	if ($MYE_REPAS == "non" ) 
	echo "SELECTED";
	echo " >non</option>"; 
	echo "</select>";
	echo "</tr>";
}
else echo "<input type='hidden' name='repas' value=''>";

if ( $nbsections == 0) {
	echo "<tr $style2>
		  <td bgcolor=$mylightcolor ><b> Transport assur� par l'association : </b></td>
		  <td bgcolor=$mylightcolor aligne=left colspan=2>";
	echo "<select id='transport' name='transport'>";
	echo "<option value='non'";
	if ($MYE_TRANSPORT =="non")
	echo "SELECTED ";
	echo ">non</option>";
	echo "<option value='oui' ";
	if ($MYE_TRANSPORT =="oui")
	echo "SELECTED ";
	echo ">oui</option>";
	echo "</select>";
	echo "</tr>";
}
else echo "<input type='hidden' name='transport' value''>";

//=====================================================================
// emails envoy�s
//=====================================================================
if ( $action <> 'create' ) {
	if ( $MYE_MAIL1 == 1 )$checked="checked";
	else $checked="";

	echo "<tr>
      	  <td bgcolor=$mylightcolor>
			<font size=1>Email ouverture envoy�</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>
			<input type='checkbox' name='mail1'  value='1' $checked></td>";		
	echo "</tr>";

	if ( $MYE_MAIL2 == 1 )$checked="checked";
	else $checked="";
	echo "<tr>
      	  <td bgcolor=$mylightcolor>
			<font size=1 >Email cl�ture envoy�</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>
			<input type='checkbox' name='mail2'  value='1' $checked></td>";		
	echo "</tr>";
      
	if ( $MYE_MAIL3 == 1 )$checked="checked";
	else $checked="";
	echo "<tr>
      	  <td bgcolor=$mylightcolor>
			<font size=1 >Email annulation envoy�</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>
			<input type='checkbox' name='mail3'  value='1' $checked></td>";		
	echo "</tr>";
}
else {
	echo "<input name='mail1' type='hidden' value='0'>";
	echo "<input name='mail2' type='hidden' value='0'>";
	echo "<input name='mail3' type='hidden' value='0'>";
}

//=====================================================================
// lien renfort
//=====================================================================

// si l'�v�nement a d�j� des renforts, on ne peut pas le rattacher comme renfort
// d'un autre �v�nement (�viter les renforts en cascade)
if ( $nbsections == 0 ) {
	$query="select count(*) as NB from evenement where E_PARENT=".$evenement;
	$result=mysql_query($query);	
	$row=mysql_fetch_array($result);
	$NB=$row["NB"];

	if (( $NB == 0 ) and ( $action == 'update' )){
		echo "<tr>
      	  <td bgcolor=$mylightcolor>
		  <font size=1 >Renfort de</font></td>
      	  <td bgcolor=$mylightcolor align=left colspan=2>";
		display_evt_accepte_renfort($evenement,$MYE_PARENT);
		echo "</td>
		</tr>";
	}
	else {
		echo "<input name='parent' type='hidden' value='$MYE_PARENT'>";
	}
}
echo "</table>";
echo "</td></tr></table>";

//=====================================================================
// boutons enregistrement
//=====================================================================

echo "<input name='evenement' type='hidden' value='$evenement'>";
echo "<input name='action' type='hidden' value='$action'>";
if ( $copymode == "full" ) $copydetails=$evenement;
else $copydetails="";
echo "<input name='copydetails' type='hidden' value='$copydetails'>";
echo "<input name='nb1' type='hidden' value='$MYE_NB1'>";
echo "<input name='nb2' type='hidden' value='$MYE_NB2'>";

if ( $action == 'create' ) $disabled='disabled';
else  $disabled='';
echo "<input type='submit' id='sauver' value='enregistrer' $disabled> ";
echo "<input type=button value='retour' onclick='javascript:history.back(1);'> ";
echo "</form></div>";
?>

<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
</BODY>
</HTML>
