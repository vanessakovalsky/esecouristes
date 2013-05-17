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
check_all(42);

if ( check_rights($_SESSION['id'], 24)) $section='0';
else $section=$_SESSION['SES_SECTION'];
$mysectionparent=get_section_parent($section);

if ( isset($_GET["from"]))$from=$_GET["from"];
else $from='default';

writehead();
echo "
<STYLE type='text/css'>
.categorie{color:$mydarkcolor; background-color:$mylightcolor; font-size:10pt;}
.type{color:$mydarkcolor; background-color:white; font-size:9pt;}
</STYLE>
<script type='text/javascript' src='checkForm.js'></script>
<script type='text/javascript'>
function fermerfenetre(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}
function addmateriel(mid, addthis){
	url='upd_materiel.php?mid=' + mid + '&addthis=' + addthis;
	self.location.href=url;
}
function redirect(){
	self.location.href='materiel.php';
}

</script>
";
echo "</head>";
echo "<body>";

if (isset ($_GET["id"])) {
	$MA_ID=intval($_GET["id"]);
	$from='export';
}
else $MA_ID=intval($_GET["mid"]);

//=====================================================================
// affiche la fiche matériel
//=====================================================================

$query="select distinct m.TM_ID,tm.TM_CODE,tm.TM_DESCRIPTION,
        tm.TM_USAGE,m.VP_ID,vp.VP_LIBELLE, vp.VP_OPERATIONNEL,vp.VP_LIBELLE,m.MA_EXTERNE, m.MA_INVENTAIRE,
		 m.MA_ID, m.MA_NUMERO_SERIE, m.MA_COMMENT,m.MA_LIEU_STOCKAGE, m.MA_MODELE,  m.VP_ID,
		 m.MA_ANNEE, m.MA_NB, m.S_ID, s.S_CODE, DATE_FORMAT(m.MA_UPDATE_DATE,'%d-%m-%Y') as MA_UPDATE_DATE,
		 DATE_FORMAT(m.MA_REV_DATE, '%d-%m-%Y') as MA_REV_DATE,
		 m.MA_UPDATE_BY, m.AFFECTED_TO, m.V_ID, m.MA_PARENT, tm.TM_LOT, tm.TM_CONTROLE
        from materiel m, type_materiel tm, section s, vehicule_position vp
		where m.TM_ID=tm.TM_ID
		and m.VP_ID=vp.VP_ID
		and s.S_ID=m.S_ID
		and m.MA_ID=".$MA_ID;

$result=mysql_query($query);
$row=mysql_fetch_array($result);
$TM_ID=$row["TM_ID"];
$TM_CODE=$row["TM_CODE"];
$TM_LOT=$row["TM_LOT"];
$TM_CONTROLE=$row["TM_CONTROLE"];
$TM_DESCRIPTION=$row["TM_DESCRIPTION"];
$TM_USAGE=$row["TM_USAGE"];
$MA_ID=$row["MA_ID"];
$MA_EXTERNE=$row["MA_EXTERNE"];
$MA_NUMERO_SERIE=$row["MA_NUMERO_SERIE"];
$MA_COMMENT=$row["MA_COMMENT"];
$MA_PARENT=$row["MA_PARENT"];
$MA_LIEU_STOCKAGE=$row["MA_LIEU_STOCKAGE"];
$MA_MODELE=$row["MA_MODELE"];
$MA_REV_DATE=$row["MA_REV_DATE"];
$MA_INVENTAIRE=$row["MA_INVENTAIRE"];
$MA_ANNEE=$row["MA_ANNEE"]; if ( $MA_ANNEE == '0000' ) $MA_ANNEE ='';
$MA_NB=$row["MA_NB"]; 
if ( $MA_NB == '' ) $MA_NB = 1;
$S_ID=$row["S_ID"];
$VP_LIBELLE=$row["VP_LIBELLE"];
$VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
$VP_ID=$row["VP_ID"];
$MA_UPDATE_DATE=$row["MA_UPDATE_DATE"];
$MA_UPDATE_BY=$row["MA_UPDATE_BY"];
$AFFECTED_TO=$row["AFFECTED_TO"];
$V_ID=$row["V_ID"];
if ( $AFFECTED_TO <> '' ) {
	$queryp="select P_NOM, P_PRENOM, P_OLD_MEMBER from pompier where P_ID=".$AFFECTED_TO;
    $resultp=mysql_query($queryp);
	$rowp=@mysql_fetch_array($resultp);
	$P_NOM=$rowp["P_NOM"];
	$P_PRENOM=$rowp["P_PRENOM"];
	$P_OLD_MEMBER=$rowp["P_OLD_MEMBER"]; 	
    $owner=strtoupper(substr($P_PRENOM,0,1).".".$P_NOM);
    if ( $P_OLD_MEMBER == 1 ) $warning="<img src=images/miniwarn.png title=\"Attention $owner est un ancien membre\">";
    else $warning="";
}
else $warning="";
if ( $VP_OPERATIONNEL  < 0 ) $mylightcolor=$mygreycolor;

// permettre les modifications si je suis habilité sur la fonctionnalité 17 au bon niveau
// ou je suis habilité sur la fonctionnalité 24 )
if (check_rights($_SESSION['id'], 17,"$S_ID")) $responsable_materiel=true;
else $responsable_materiel=false;

if ( $responsable_materiel ) $disabled=""; 
else $disabled="disabled";

if ( $MA_EXTERNE == '1' ) {
	if (check_rights($_SESSION['id'], 24)) $disabled='';
	else $disabled='disabled';
}

//=====================================================================
// sauver changements sur lot de matériel
//=====================================================================
if ( $disabled == '' ) {
	if ( isset($_GET["del"])) {
		$del=intval($_GET["del"]);
		$query="update materiel set MA_PARENT=null where MA_ID=".$del." and MA_PARENT=".$MA_ID;
		$result=mysql_query($query);
		$from='lot';
	}
	if ( isset($_GET["addthis"])) {
		$addthis=intval($_GET["addthis"]);
		$query="update materiel set V_ID = null, MA_PARENT=".$MA_ID." where MA_ID=".$addthis;
		$result=mysql_query($query);
		$from='lot';
	}
} 

//=====================================================================
// afficher fiche matériel
//=====================================================================

$query="select CM_DESCRIPTION,PICTURE_LARGE from categorie_materiel
		where TM_USAGE='".$TM_USAGE."'";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$cmt=$row["CM_DESCRIPTION"];
$picture=$row["PICTURE_LARGE"];

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/$picture></td><td>
      <font size=4><b>".$TM_CODE." - ".$TM_DESCRIPTION." ".$MA_MODELE."</b></font></td></tr></table>";


echo "<form name='materiel' action='save_materiel.php'>";
echo "<input type='hidden' name='MA_ID' value='$MA_ID'>";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='MA_NUMERO_SERIE' value=\"$MA_NUMERO_SERIE\">";
echo "<input type='hidden' name='MA_COMMENT' value=\"$MA_COMMENT\">";
echo "<input type='hidden' name='VP_ID' value='$VP_ID'>";
echo "<input type='hidden' name='MA_MODELE' value=\"$MA_MODELE\">";
echo "<input type='hidden' name='MA_ANNEE' value='$MA_ANNEE'>";
echo "<input type='hidden' name='MA_REV_DATE' value='$MA_REV_DATE'>";
echo "<input type='hidden' name='TM_USAGE' value='$TM_USAGE'>";
echo "<input type='hidden' name='from' value='$from'>";

//=====================================================================
// ligne 1
//=====================================================================

echo "<p><TABLE>
<TR>
<TD class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	  <td class=TabHeader colspan=2>informations matériel</td>
      </tr>";


//=====================================================================
// ligne type de matériel
//=====================================================================

$query2="select distinct TM_ID, TM_USAGE, TM_CODE, TM_DESCRIPTION, TM_LOT from type_materiel
		 order by TM_USAGE,TM_CODE";
$result2=mysql_query($query2);

echo "<tr>
      	  <td bgcolor=$mylightcolor width=200><b>Type</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor width=250 align=left>
		  <select name='TM_ID' $disabled>";
		     $prevTM_USAGE=-1;
		     while ($row2=@mysql_fetch_array($result2)) {
		          $TM_USAGE=$row2["TM_USAGE"];
		          $NEWTM_ID=$row2["TM_ID"];
				  $NEWTM_LOT=$row2["TM_LOT"];
				  if ( $NEWTM_LOT == 1 ) $lot=" (lot)";
				  else $lot="";
		          $NEWTM_CODE=$row2["TM_CODE"];
		          $NEWTM_DESCRIPTION=$row2["TM_DESCRIPTION"];
		          if ( $prevTM_USAGE <> $TM_USAGE ) echo "<OPTGROUP class='categorie' LABEL='".$TM_USAGE."'>";
		          if ( $NEWTM_ID == $TM_ID ) $selected='selected';
		          else $selected='';
		          if ( $NEWTM_DESCRIPTION <> "" ) $addcmt= " - ".$NEWTM_DESCRIPTION;
		          else $addcmt="";
		          echo "<option class='type' value='".$NEWTM_ID."' $selected>".substr($NEWTM_CODE.$addcmt,0,45).$lot."</option>";
		          $prevTM_USAGE=$TM_USAGE;
	     	     }
 	        echo "</select>";
 echo "</td>
 	 </tr>";

//=====================================================================
// ligne modèle
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Marque/modèle</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left height=25><input type='text' name='MA_MODELE' size='25' value=\"$MA_MODELE\" $disabled>";		
echo "</td>
      </tr>";

//=====================================================================
// ligne section
//=====================================================================

if (  $nbsections == 0 ) {
 	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Section</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>";
 	echo "<select id='groupe' name='groupe' $disabled>"; 

   if ( $responsable_materiel ) {
       $mysection=get_highest_section_where_granted($_SESSION['id'],17);
       if ( $mysection == '' ) $mysection=$S_ID;
       if ( ! is_children($section,$mysection)) $mysection=$section;
   }
   else $mysection=$S_ID;
   
   $level=get_level($mysection);
   if ( $level == 0 ) $mycolor=$myothercolor;
   elseif ( $level == 1 ) $mycolor=$my2darkcolor;
   elseif ( $level == 2 ) $mycolor=$my2lightcolor;
   elseif ( $level == 3 ) $mycolor=$mylightcolor;
   else $mycolor='white';
   $class="style='background: $mycolor;'";
   if ( check_rights($_SESSION['id'], 24))
   	  display_children2(-1, 0, $S_ID, $nbmaxlevels);
   else {
   		echo "<option value='$mysection' $class >".str_repeat(". ",$level)." ".
      		get_section_code($mysection)." - ".get_section_name($mysection)."</option>";
   		if ( $disabled == '') display_children2($mysection, $level +1, $S_ID, $nbmaxlevels);
   }
   echo "</select></td> ";
   echo "</tr>";	  
}
else echo "<input type='hidden' name='groupe' value='0'>";

//=====================================================================
// ligne nombre
//=====================================================================
      
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Nombre de pièces</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left height=25>
			<input type='text' name='quantity' size='6' value='$MA_NB' onchange='checkNumber(form.quantity,\"$MA_NB\")' $disabled></td>";		
echo "</tr>";

//=====================================================================
// ligne statut
//=====================================================================

if ( $VP_OPERATIONNEL == -1 ) $opcolor='black';
else if ( $VP_OPERATIONNEL == 1 ) $opcolor=$red;
else if ( $VP_OPERATIONNEL == 2 ) $opcolor=$orange;
else $opcolor=$green;

$query2="select VP_LIBELLE, VP_ID, VP_OPERATIONNEL
         from vehicule_position
		 where VP_OPERATIONNEL <> 0
		 order by  VP_OPERATIONNEL desc";
$result2=mysql_query($query2);

echo "<tr>
      	  <td bgcolor=$mylightcolor ><font color=$opcolor><b>Position du matériel</b> <font color=red>*</font></font></td>
      	  <td bgcolor=$mylightcolor align=left>
		<select name='VP_ID' $disabled>";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $NEWVP_ID=$row2["VP_ID"];
		          $NEWVP_LIBELLE=$row2["VP_LIBELLE"];
		          $NEWVP_OPERATIONNEL=$row2["VP_OPERATIONNEL"];
		          if ($VP_ID == $NEWVP_ID) $selected='selected';
		          else $selected='';
		          echo "<option value='$NEWVP_ID' class=\"".$NEWVP_OPERATIONNEL."\" $selected>$NEWVP_LIBELLE</option>";
	     	     }
 	        echo "</select>";
echo " </td>
      </tr>";

if ( $VP_OPERATIONNEL < 0 ) {
	if ( $MA_UPDATE_DATE <> "" )
		echo "<tr> 
              <td bgcolor=$mylightcolor align=right><i>Modifié le: </i></td> 
              <td bgcolor=$mylightcolor align=left> ".$MA_UPDATE_DATE."</td> 
              </tr>"; 
       if ( $MA_UPDATE_BY <> "") 
       echo "<tr> 
              <td bgcolor=$mylightcolor align=right><i>Modifié par: </i></td> 
              <td bgcolor=$mylightcolor align=left> 
                            <a href=upd_personnel.php?pompier=$MA_UPDATE_BY > 
                            ".ucfirst(get_prenom($MA_UPDATE_BY))." ".strtoupper(get_nom($MA_UPDATE_BY))."</a></td> 
              </tr>"; 
}

//=====================================================================
// ligne numéro de série
//=====================================================================

echo "<p><tr>
      	  <td bgcolor=$mylightcolor ><b>Numéro de série</b></td>
      	  <td bgcolor=$mylightcolor align=left height=25><input type='text' name='MA_NUMERO_SERIE' size='20' value=\"$MA_NUMERO_SERIE\" $disabled>";		
echo " </td>
      </tr>";

//=====================================================================
// ligne année
//=====================================================================

$curyear=date("Y");
$year=$curyear - 30; 
$found=false;
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Année</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<select name='MA_ANNEE' $disabled>";
if ( $MA_ANNEE == '' ) $selected = 'selected';
else  $selected = '';
echo "<option value='null' selected>inconnue</option>";
while ( $year <= $curyear + 1 ) {
			if ( $year == $MA_ANNEE ) {
				$selected = 'selected';
				$found=true;
			}
			else $selected = '';
			echo "<option value='$year' $selected>$year</option>";
			$year++;
		}
		if (( ! $found  ) and ($MA_ANNEE <> ''))  echo "<option value='$MA_ANNEE' selected>$MA_ANNEE</option>";
		
echo "</select></tr>";
      

//=====================================================================
// ligne commentaire
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Commentaire</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='MA_COMMENT' size='45' value=\"$MA_COMMENT\" $disabled>";		
echo " </td>
      </tr>";

//=====================================================================
// affecté à 
//=====================================================================

$query2="select p.P_ID, p.P_PRENOM, p.P_NOM , s.S_CODE
		from pompier p, section s
   		 where S_ID= P_SECTION
		 and ( p.P_SECTION in (".get_family($S_ID).") or p.P_ID = '".$AFFECTED_TO."' )
         and p.P_CODE <> '1234'
         and (p.P_OLD_MEMBER = 0 or p.P_ID = '".$AFFECTED_TO."' )
		 order by p.P_NOM";
$result2=mysql_query($query2);

echo "<tr >
      	  <td bgcolor=$mylightcolor ><b>Affecté à ".$warning."</b></td>
      	  <td bgcolor=$mylightcolor align=left>";		
   echo "<select id='affected_to' name='affected_to' $disabled>
   		<option value='0' selected >--personne--</option>\n";
   while ($row2=@mysql_fetch_array($result2)) {
      $P_NOM=$row2["P_NOM"];
      $P_PRENOM=$row2["P_PRENOM"];
      $P_ID=$row2["P_ID"];
      $S_CODE=$row2["S_CODE"];
      if ( $P_ID == $AFFECTED_TO ) $selected='selected';
      else $selected="";
      if ( $nbsections <> 1 ) $cmt=" (".$S_CODE.")";
      else $cmt="";
      echo "<option value='".$P_ID."' $selected>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM).$cmt."</option>\n";
   }
echo "</select>";
echo "</td></tr>";

//=====================================================================
// dans un véhicule / dans un lot de matériel
//=====================================================================

echo "<tr >
      	  <td bgcolor=$mylightcolor ><b>Dans un véhicule / lot matériel</b></td>
      	  <td bgcolor=$mylightcolor align=left>";		
  echo "<select id='vid' name='vid' $disabled
	     title=\"Attention un lot de matériel ne peut pas être rattaché à un autre lot de matériel\">
   		<option value='0' selected >--non--</option>\n";

	$query2="select v.V_ID, v.TV_CODE, v.V_MODELE, v.V_INDICATIF, v.V_IMMATRICULATION, s.S_CODE
		from vehicule v, section s
   		 where s.S_ID= v.S_ID
		 and ( s.S_ID in (".get_family($S_ID).") or v.V_ID = '".$V_ID."' )
         and ( v.VP_ID in (select VP_ID from vehicule_position where VP_OPERATIONNEL>= 0 ) 
		 		or v.V_ID = '".$V_ID."' )
		 order by v.TV_CODE, v.V_MODELE";
	$result2=mysql_query($query2);	
		
	
  echo "<OPTGROUP class='categorie' label='Dans un véhicule'>";
   while ($row2=@mysql_fetch_array($result2)) {
      $_V_ID=$row2["V_ID"];
      $TV_CODE=$row2["TV_CODE"];
      $V_MODELE=$row2["V_MODELE"];
      $V_IMMATRICULATION=$row2["V_IMMATRICULATION"];
      $V_INDICATIF=$row2["V_INDICATIF"];
      $S_CODE=$row2["S_CODE"];
      if ( $_V_ID == $V_ID ) $selected='selected';
      else $selected="";
      if ( $nbsections <> 1 ) $cmt=" (".$S_CODE.")";
      else $cmt="";
      echo "<option class='type' value='V".$_V_ID."' $selected>".$TV_CODE." ".$V_MODELE." ".$V_INDICATIF.$cmt."</option>\n";
   }
   
   // choix lot matériel (parent)
   $query3="select m.MA_ID, m.MA_MODELE, tm.TM_CODE, m.MA_NUMERO_SERIE, s.S_CODE
		 from materiel m, type_materiel tm, section s
   		 where s.S_ID= m.S_ID
		 and m.TM_ID=tm.TM_ID
		 and tm.TM_LOT=1
		 and ( s.S_ID =".$S_ID." or m.MA_ID = '".$MA_PARENT."' )
         and ( m.VP_ID in (select VP_ID from vehicule_position where VP_OPERATIONNEL>= 0 ) 
		 		or m.MA_ID = '".$MA_PARENT."' )
		 and m.MA_ID <> '".$MA_ID."'
		 order by tm.TM_CODE, m.MA_MODELE";
   $result3=mysql_query($query3);
   
   //if ( $TM_LOT == 0 ) {
		echo "<OPTGROUP class='categorie' label='Dans un lot de matériel'>";
		while ($row3=@mysql_fetch_array($result3)) {
			$_MA_ID=$row3["MA_ID"];
			$_TM_CODE=$row3["TM_CODE"];
			$_MA_MODELE=$row3["MA_MODELE"];
			$S_CODE=$row3["S_CODE"];
			$_MA_NUMERO_SERIE=$row3["MA_NUMERO_SERIE"];
			if ( $_MA_ID == $MA_PARENT ) $selected='selected';
			else $selected="";
			if ( $nbsections <> 1 ) $cmt=" (".$S_CODE.")";
			else $cmt="";
			echo "<option class='type' value='M".$_MA_ID."' $selected>".$_TM_CODE." ".$_MA_MODELE." ".$_MA_NUMERO_SERIE." ".$cmt."</option>\n";
	//	}   
   }
echo "</select>";
echo "</td></tr>";


//=====================================================================
// ligne inventaire
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>N°d'inventaire</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='MA_INVENTAIRE' size='45' value=\"$MA_INVENTAIRE\" $disabled>";		
echo " </td>
      </tr>";
 
//=====================================================================
// ligne lieu stockage
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Lieu de stockage</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='MA_LIEU_STOCKAGE' size='45' value=\"$MA_LIEU_STOCKAGE\" $disabled>";		
echo " </td>
      </tr>";
	  
//=====================================================================
// dates de prochaine révision ou péremption
//=====================================================================

echo "<input type='hidden' name='dc0' value='".getnow()."'>";

$revision=$mydarkcolor;
if ( my_date_diff(getnow(),$MA_REV_DATE) < 0 ) $revision=$orange;

// date
echo "<tr>
      	  <td bgcolor=$mylightcolor ><font color=$revision><b>Prochaine révision ou péremption</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>";
?>
<input class="plain" name="dc1" value=
<?php
echo "\"".$MA_REV_DATE."\" $disabled";
?>
size="12" onchange=checkDate2(this.form.dc1)><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.materiel.dc0,document.materiel.dc1);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php
 
//=====================================================================
// materiel externe
//=====================================================================

if (check_rights($_SESSION['id'], 24)) $disabled2='';
else $disabled2='disabled';

if ( $MA_EXTERNE == 1 )$checked='checked';
else $checked='';

if (( $disabled2=='' or $checked=='checked' ) and  ($nbsections ==  0 )){
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>matériel $cisname</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='MA_EXTERNE' value='1' $checked $disabled2>
			<font size=1><i>mis à disposition (utilisable, non modifiable)<i></font>";		
	echo " </td>
      </tr>";
}

//=====================================================================
// matériel inclus
//=====================================================================

if ( $TM_LOT==1 ) {
$query2="select m.TM_ID, tm.TM_CODE, tm.TM_CODE,tm.TM_USAGE,
		 m.VP_ID, vp.VP_OPERATIONNEL,vp.VP_LIBELLE,
		 m.MA_ID, m.MA_NUMERO_SERIE, m.MA_COMMENT, m.MA_MODELE, cm.PICTURE_SMALL,
		 m.MA_ANNEE, m.MA_NB, m.MA_REV_DATE, tm.TM_LOT
		 from type_materiel tm, vehicule_position vp, categorie_materiel cm, materiel m
		 where m.TM_ID=tm.TM_ID
		 and cm.TM_USAGE = tm.TM_USAGE
		 and m.VP_ID=vp.VP_ID
		 and m.MA_PARENT=".$MA_ID;
$result2=mysql_query($query2);

echo "<tr class=TabHeader><td colspan=2>Matériel inclus dans ce lot</td>
         </tr>";
	
if ( mysql_num_rows($result2) > 0 ) {
	     while ($row2=@mysql_fetch_array($result2)) {
	      	$_TM_CODE=$row2["TM_CODE"];
	      	$_TM_USAGE=$row2["TM_USAGE"];
	      	$_VP_OPERATIONNEL=$row2["VP_OPERATIONNEL"];
	      	$_VP_LIBELLE=$row2["VP_LIBELLE"];
	      	$_MA_ID=$row2["MA_ID"];
			$_TM_LOT=$row2["TM_LOT"];
	      	$_MA_REV_DATE=$row2["MA_REV_DATE"];
	      	$_MA_MODELE=$row2["MA_MODELE"];
			if ( $_TM_LOT == 1 ) $lot=" (lot)";
			else $lot="";
	      	if ($row2["MA_NUMERO_SERIE"] <> "" ) 
	      		$_MA_NUMERO_SERIE=" - ".$row2["MA_NUMERO_SERIE"];
	      	else $_MA_NUMERO_SERIE="";
	      	$_MA_NB=$row2["MA_NB"]; if ( $MA_NB == 1 ) $MA_NB="";
	      	$_PICTURE_SMALL=$row2["PICTURE_SMALL"];
	      	
	      	if ( $_VP_OPERATIONNEL == -1) $mytxtcolor='black';
      		else if ( $_VP_OPERATIONNEL == 1) $mytxtcolor=$red;      
	  		else if ( my_date_diff(getnow(),$_MA_REV_DATE) < 0 ) {
	  			$mytxtcolor=$orange;
	  			$_VP_LIBELLE = "date dépassée";
	  		}
	  		else if ( $_VP_OPERATIONNEL == 2) {
	  			$mytxtcolor=$orange;
	  		}
      		else $mytxtcolor=$green;
	      	
			$code=$_MA_NB." ".$_MA_MODELE." ".$_MA_NUMERO_SERIE;
			if ( $code == '  ' ) $code='voir';
			
		 	echo "<tr>
      	  	<td bgcolor=$mylightcolor align=right>".$_TM_CODE." ".$lot." 
				<img src=images/".$_PICTURE_SMALL." title='".$_TM_USAGE."'></td>
      	  	<td bgcolor=$mylightcolor align=left>
				<a href=upd_materiel.php?mid=".$_MA_ID.">".$code."</a>
				<font size=1 color=".$mytxtcolor."> ".$_VP_LIBELLE."</font>";
			if ($disabled == ""	) {
				echo "	<a href=upd_materiel.php?mid=".$MA_ID."&del=".$_MA_ID.">
						<img height=14 border=0 src=images/trash.png title='Enlever ce matériel du lot'></a>";
			}
			echo " </td>
      		</tr>";
      	}
}
else echo "<tr><td colspan=2 bgcolor=$mylightcolor ><i>Aucun matériel inclus dans ce lot</i></td></tr>";

if ( $TM_LOT==1 and $disabled=='') {
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Ajouter du matériel dans ce lot</b></td>
      	  <td bgcolor=$mylightcolor align=left>";		
	echo "<select id='add' name='add' $disabled onchange=\"javascript:addmateriel('".$MA_ID."',this.form.add.value);\">";
	echo "<option class='type' value='0' selected>Choisir matériel</a>";
	// choix matériel à ajouter dans ce lot
	$query3="select m.MA_ID, m.MA_MODELE, tm.TM_CODE, m.MA_NUMERO_SERIE, s.S_CODE, tm.TM_USAGE
		 from materiel m, type_materiel tm, section s
   		 where s.S_ID= m.S_ID
		 and m.TM_ID=tm.TM_ID
		 and tm.TM_LOT=0
		 and ( m.MA_PARENT <> $MA_ID or m.MA_PARENT is null ) 
		 and s.S_ID = ".$S_ID."
         and m.VP_ID in (select VP_ID from vehicule_position where VP_OPERATIONNEL>= 0 )
		 order by tm.TM_USAGE, tm.TM_CODE, m.MA_MODELE";
	$result3=mysql_query($query3);
	$prevTM_USAGE="";
	while ($row3=@mysql_fetch_array($result3)) {
			$_MA_ID=$row3["MA_ID"];
			$_TM_CODE=$row3["TM_CODE"];
			$_MA_MODELE=$row3["MA_MODELE"];
			$S_CODE=$row3["S_CODE"];
			$_TM_USAGE=$row3["TM_USAGE"];
			$_MA_NUMERO_SERIE=$row3["MA_NUMERO_SERIE"];
			if ( $prevTM_USAGE <> $_TM_USAGE ) echo "<OPTGROUP LABEL='...".$_TM_USAGE."' class='categorie'>";
			$prevTM_USAGE=$_TM_USAGE;
			echo "<option class='type' value='".$_MA_ID."'>".$_TM_CODE." ".$_MA_MODELE." ".$_MA_NUMERO_SERIE." </option>\n";
		}   

	echo "</select>";
	echo "</td></tr>";
}
}

echo "</table></tr></table>";
if ( $disabled == "") {
    echo "<input type='submit' value='sauver'> ";
}
echo "</form>";


if ( check_rights($_SESSION['id'], 19, "$S_ID")) {
	echo "<form name='materiel2' action='save_materiel.php'>";
	echo "<input type='hidden' name='MA_ID' value='$MA_ID'>";
	echo "<input type='hidden' name='TM_CODE' value='$TM_CODE'>";
	echo "<input type='hidden' name='TM_ID' value='$TM_ID'>";
	echo "<input type='hidden' name='MA_NUMERO_SERIE' value='$MA_NUMERO_SERIE'>";
	echo "<input type='hidden' name='MA_COMMENT' value='$MA_COMMENT'>";
	echo "<input type='hidden' name='MA_LIEU_STOCKAGE' value='$MA_LIEU_STOCKAGE'>";
	echo "<input type='hidden' name='VP_ID' value='$VP_ID'>";
	echo "<input type='hidden' name='vid' value='$V_ID'>";
	echo "<input type='hidden' name='dc1' value=''>";
	echo "<input type='hidden' name='quantity' value='$MA_NB'>";
	echo "<input type='hidden' name='groupe' value='$S_ID'>";
	echo "<input type='hidden' name='from' value='$from'>";
	echo "<input type='hidden' name='TM_USAGE' value='$TM_USAGE'>";
	echo "<input type='hidden' name='MA_MODELE' value='$MA_MODELE'>";
	echo "<input type='hidden' name='MA_ANNEE' value='$MA_ANNEE'>";
	echo "<input type='hidden' name='MA_REV_DATE' value='$MA_REV_DATE'>";
	echo "<input type='hidden' name='MA_INVENTAIRE' value='$MA_INVENTAIRE'>";
	echo "<input type='hidden' name='operation' value='delete'>";
	echo "<input type='submit' value='supprimer'> ";
	echo "</form>";
}

if ( $from == 'export' ) {
	echo "<input type=submit value='fermer cette page' onclick='fermerfenetre();'> ";
}
else if ( $from == 'lot' ) {
	echo "<input type='button' value='Retour' name='annuler' onclick='redirect()'>";
}
else {
	echo "<input type='button' value='Retour' name='annuler' onclick=\"javascript:history.back(1);\">";
}

//==================================================================
//Ajout d'un controle et affichage des contrôles existants
//==================================================================

if ($TM_CONTROLE == 1 ) {
	echo "<form action=\"materiel_controle_ajout.php\">";
	echo "<input type='hidden' name='MA_ID' value='$MA_ID'>";
	echo "<input type='submit' value='Ajouter un contrôle'>";
	echo "</form>";

$query_controle="SELECT DISTINCT MAC_COMMENT, MAC_TYPE, MAC_CONTROLED_BY, p.P_PRENOM, p.P_NOM, DATE_FORMAT(MAC_CONTROLE_DATE, '%d-%m-%Y') as MAC_CONTROLE_DATE
				FROM materiel_controle mc, pompier p
				WHERE mc.MA_ID=".$MA_ID."
				AND mc.MAC_CONTROLED_BY=p.P_ID";
$result_controle=mysql_query($query_controle);

echo "<table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================


echo "<tr class=TabHeader>";
echo "<td width=90 align=center>Date du contrôle</td>
      <td bgcolor=$mydarkcolor width=0></td>
      <td width=90 align=center>Contrôleur</td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=90 align=center>Type de contrôle</td>
    <td bgcolor=$mydarkcolor width=0></td>
    <td width=150 align=center>Commentaire</td>
    <td bgcolor=$mydarkcolor width=0></td>";
echo "</tr>";

// ===============================================
//Corps du tableau du contrôle du matériel
// ===============================================
$i=0;
while($row_controle=mysql_fetch_array($result_controle)){
		$MAC_CONTROLE_DATE=$row_controle["MAC_CONTROLE_DATE"];
		$MAC_CONTROLED_BY=$row_controle["P_NOM"].$row_controle["P_PRENOM"];
		$MAC_TYPE=$row_controle["MAC_TYPE"];
		$MAC_COMMENT=$row_controle["MAC_COMMENT"];
		
		$i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }
		
		echo "<tr bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$mycolor'\" onclick=\"this.bgColor='#33FF00'; displaymanager($MA_ID)\" >";
		echo "<td align=center><font color=$opcolor size=1><B>$MAC_CONTROLE_DATE</B></font></td>
		  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$MAC_CONTROLED_BY</font></td>
		  <td bgcolor=$mydarkcolor width=0></td>
		  <td align=center><font color=$opcolor size=1><B>$MAC_TYPE</B></font></td>
		  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>$MAC_COMMENT</font></td>
		  <td bgcolor=$mydarkcolor width=0></td>";
		echo "</tr>";

}
echo "</table>";
}

echo "</div>";
?>

<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
</BODY>
</HTML>
