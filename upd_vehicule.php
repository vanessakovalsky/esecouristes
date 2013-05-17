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
check_all(42);

if ( check_rights($_SESSION['id'], 24)) $section='0';
else $section=$_SESSION['SES_SECTION'];
$mysectionparent=get_section_parent($section);

if ( isset($_GET["from"]))$from=$_GET["from"];
else $from="default";

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
function addmateriel(vid, addthis){
	url='upd_vehicule.php?vid=' + vid + '&addthis=' + addthis;
	self.location.href=url;
}
function redirect(){
	self.location.href='vehicule.php';
}

</script>
";
echo "</head>";
echo "<body>";

if (isset($_GET["id"])) {
	$V_ID=intval($_GET["id"]);
	$from='export';
} 
else $V_ID=intval($_GET["vid"]);

//=====================================================================
// récupérer infos véhicule
//=====================================================================

$query="select  v.V_ID, v.VP_ID, v. TV_CODE, v.V_IMMATRICULATION, v.V_COMMENT , v.V_EXTERNE, 
		v.V_KM , v.V_ANNEE,v.EQ_ID, v.V_MODELE, v.S_ID, s.S_DESCRIPTION, v.V_INVENTAIRE,v.V_INDICATIF,
		DATE_FORMAT(v.V_ASS_DATE, '%d-%m-%Y') as V_ASS_DATE,
		DATE_FORMAT(v.V_CT_DATE, '%d-%m-%Y') as V_CT_DATE,
		DATE_FORMAT(v.V_REV_DATE, '%d-%m-%Y') as V_REV_DATE,
	    tv.TV_USAGE, tv.TV_LIBELLE, vp.VP_LIBELLE, v.VP_ID, vp.VP_OPERATIONNEL,
	    DATE_FORMAT(v.V_UPDATE_DATE,'%d-%m-%Y') as V_UPDATE_DATE, v.V_UPDATE_BY,
	    v.V_FLAG1, v.V_FLAG2, v.AFFECTED_TO
        from vehicule v, type_vehicule tv, vehicule_position vp, section s
		where tv.TV_CODE=v.TV_CODE
		and v.VP_ID=vp.VP_ID
		and v.V_ID=".$V_ID;	
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$TV_CODE=$row["TV_CODE"];
$V_IMMATRICULATION=$row["V_IMMATRICULATION"];
$V_COMMENT=$row["V_COMMENT"];
$VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
$V_KM=$row["V_KM"];
$V_ANNEE=$row["V_ANNEE"];
$V_EXTERNE=$row["V_EXTERNE"];
$V_ASS_DATE=$row["V_ASS_DATE"];
$V_CT_DATE=$row["V_CT_DATE"];
$V_REV_DATE=$row["V_REV_DATE"];
$VP_ID=$row["VP_ID"];
$EQ_ID=$row["EQ_ID"];
$S_ID=$row["S_ID"];
$S_DESCRIPTION=get_section_name($S_ID);
$V_MODELE=$row["V_MODELE"];
$VP_LIBELLE=$row["VP_LIBELLE"];
$TV_USAGE=$row["TV_USAGE"];
$TV_LIBELLE=$row["TV_LIBELLE"];
$V_INVENTAIRE=$row["V_INVENTAIRE"];
$V_INDICATIF=$row["V_INDICATIF"];
$V_UPDATE_DATE=$row["V_UPDATE_DATE"];
$V_UPDATE_BY=$row["V_UPDATE_BY"];
$V_FLAG1=$row["V_FLAG1"];
$V_FLAG2=$row["V_FLAG2"];
$AFFECTED_TO=$row["AFFECTED_TO"];
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
if (check_rights($_SESSION['id'], 17,"$S_ID")) $responsable_vehicule=true;
else $responsable_vehicule=false;

if ($responsable_vehicule ) $disabled=""; 
else $disabled="disabled";

if ( $V_EXTERNE == '1' ) {
	if (check_rights($_SESSION['id'], 24)) $disabled='';
	else $disabled='disabled';
}

//=====================================================================
// sauver changements sur  matériel embarqué
//=====================================================================
if ( $disabled == '' ) {
	if ( isset($_GET["del"])) {
		$del=intval($_GET["del"]);
		$query="update materiel set V_ID=null where MA_ID=".$del." and V_ID=".$V_ID;
		$result=mysql_query($query);
		$from='lot';
	}
	if ( isset($_GET["addthis"])) {
		$addthis=intval($_GET["addthis"]);
		$query="update materiel set V_ID = ".$V_ID.", MA_PARENT= null where MA_ID=".$addthis;
		$result=mysql_query($query);
		$from='lot';
	}
} 

//=====================================================================
// affiche la fiche véhicule
//=====================================================================

echo "<div align=center><font size=4><b>$TV_CODE - $V_IMMATRICULATION <br></b></font>";

echo "<form name='vehicule' action='save_vehicule.php'>";
echo "<input type='hidden' name='V_ID' value='$V_ID'>";
echo "<input type='hidden' name='operation' value='update'>";
echo "<input type='hidden' name='V_IMMATRICULATION' value='$V_IMMATRICULATION'>";
echo "<input type='hidden' name='V_COMMENT' value='$V_COMMENT'>";
echo "<input type='hidden' name='VP_ID' value='$VP_ID'>";
echo "<input type='hidden' name='V_KM' value='$V_KM'>";
echo "<input type='hidden' name='EQ_ID' value='$EQ_ID'>";
echo "<input type='hidden' name='V_MODELE' value='$V_MODELE'>";
echo "<input type='hidden' name='V_ANNEE' value='$V_ANNEE'>";
echo "<input type='hidden' name='V_ASS_DATE' value='$V_ASS_DATE'>";
echo "<input type='hidden' name='V_CT_DATE' value='$V_CT_DATE'>";
echo "<input type='hidden' name='V_REV_DATE' value='$V_REV_DATE'>";
echo "<input type='hidden' name='V_INVENTAIRE' value='$V_INVENTAIRE'>";
echo "<input type='hidden' name='V_INDICATIF' value='$V_INDICATIF'>";
echo "<input type='hidden' name='from' value='$from'>";
for ( $i = 1 ; $i <= 8 ; $i++) {
	echo "<input type='hidden' name='P".$i."' value='".get_poste($V_ID,$i)."'>";
}


//=====================================================================
// ligne 1
//=====================================================================

echo "<p><TABLE>
<TR>
<TD class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	  <td class=TabHeader colspan=2>informations véhicule</td>
      </tr>";


//=====================================================================
// ligne type de vehicule
//=====================================================================

$query2="select distinct TV_CODE, TV_LIBELLE from type_vehicule
		 order by TV_CODE";
$result2=mysql_query($query2);

echo "<tr>
      	  <td bgcolor=$mylightcolor width=200><b>Type</b><font color=red> *</font></td>
      	  <td bgcolor=$mylightcolor width=250 align=left>
		  <select name='TV_CODE' $disabled>";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $NEWTV_CODE=$row2["TV_CODE"];
		          $NEWTV_LIBELLE=$row2["TV_LIBELLE"];
		          if ( $NEWTV_CODE == $TV_CODE ) $selected='selected';
		          else $selected='';
		          echo "<option value='".$NEWTV_CODE."' $selected>".$NEWTV_CODE." - ".$NEWTV_LIBELLE."</option>";
	     	     }
 	        echo "</select>";
 echo "</td>
 	 </tr>";

//=====================================================================
// ligne modèle
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Marque/modèle</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_MODELE' size='25' value=\"$V_MODELE\" $disabled>";		
echo "</td>
      </tr>";

//=====================================================================
// ligne section
//=====================================================================

if (  $nbsections == 0 ) {
 	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Section</b><font color=red> *</font></td>
      	  <td bgcolor=$mylightcolor align=left>";
 	echo "<select id='groupe' name='groupe' $disabled>"; 

   if ( $responsable_vehicule ) {
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
// ligne immatriculation
//=====================================================================

echo "<p><tr>
      	  <td bgcolor=$mylightcolor ><b>Immatriculation</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_IMMATRICULATION' size='20' value=\"$V_IMMATRICULATION\" $disabled>";		
echo " </td>
      </tr>";
      
//=====================================================================
// numéro d'indicatif
//=====================================================================

echo "<tr >
      	  <td bgcolor=$mylightcolor ><b>Indicatif</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_INDICATIF' size='30' value=\"$V_INDICATIF\" $disabled>";		
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
			<select name='V_ANNEE' $disabled>";
		while ( $year <= $curyear + 1 ) {
			if ( $year == $V_ANNEE ) {
				$selected = 'selected';
				$found=true;
			}
			else $selected = '';
			echo "<option value='$year' $selected>$year</option>";
			$year++;
		}
		if ( ! $found ) echo "<option value='$V_ANNEE' selected>$V_ANNEE</option>";
		
echo "</select></tr>";
      
//=====================================================================
// ligne kilometrage
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Kilométrage</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='text' name='V_KM' size='6' value='$V_KM' onchange='checkNumber(form.V_KM,\"$V_KM\")' $disabled >";		
echo "</td>
      </tr>";
      

//=====================================================================
// dates d'assurance de contrôle technique et de révision
//=====================================================================

echo "<input type='hidden' name='dc0' value='".getnow()."'>";

$assurance=$mydarkcolor;
$controle=$mydarkcolor;
$revision=$mydarkcolor;
if ( my_date_diff(getnow(),$V_ASS_DATE) < 0 ) $assurance=$red;
if ( my_date_diff(getnow(),$V_CT_DATE) < 0 ) $controle=$red;  
if ( my_date_diff(getnow(),$V_REV_DATE) < 0 ) $revision=$orange;

// assurance
echo "<tr>
      	  <td bgcolor=$mylightcolor ><font color=$assurance><b>Fin assurance</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>";
?>
<input class="plain" name="dc1" value=
<?php
echo "\"".$V_ASS_DATE."\" $disabled";
?>
size="12" onchange=checkDate2(this.form.dc1) ><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.vehicule.dc0,document.vehicule.dc1);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php

// contrôle technique
echo "<tr height=20>
      	  <td bgcolor=$mylightcolor ><font color=$controle><b>Prochain contrôle technique</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>";
?>
<input class="plain" name="dc2" value=
<?php
echo "\"".$V_CT_DATE."\" $disabled";
?>
size="12" onchange=checkDate2(this.form.dc2)><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.vehicule.dc0,document.vehicule.dc2);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php

// révision
echo "<tr>
      	  <td bgcolor=$mylightcolor ><font color=$revision><b>Prochaine révision</b></font></td>
      	  <td bgcolor=$mylightcolor  align=left>";
?>
<input class="plain" name="dc3" value=
<?php
echo "\"".$V_REV_DATE."\" $disabled";
?>
size="12" onchange=checkDate2(this.form.dc3)><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.vehicule.dc0,document.vehicule.dc3);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php

      

//=====================================================================
// ligne statut
//=====================================================================

if ( $VP_OPERATIONNEL == -1 ) $opcolor="black";
else if ( $VP_OPERATIONNEL == 1 ) $opcolor=$red;
else if ( $VP_OPERATIONNEL == 2 ) $opcolor=$orange;
else $opcolor=$green;

$query2="select VP_LIBELLE, VP_ID, VP_OPERATIONNEL
         from vehicule_position
		 where VP_OPERATIONNEL <> 0
		 order by  VP_OPERATIONNEL desc";
$result2=mysql_query($query2);

echo "<tr>
      	  <td bgcolor=$mylightcolor ><font color=$opcolor><b>Position véhicule</b></font></td>
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
   if ( $V_UPDATE_DATE <> "" ) 
   echo "<tr>
      	  <td bgcolor=$mylightcolor align=right><i>Modifié le: </i></td>
      	  <td bgcolor=$mylightcolor align=left> ".$V_UPDATE_DATE."</td>
      	  </tr>";	
   if ( $V_UPDATE_BY <> "")
   echo "<tr>
      	  <td bgcolor=$mylightcolor align=right><i>Modifié par: </i></td>
      	  <td bgcolor=$mylightcolor align=left> 
			<a href=upd_personnel.php?pompier=$V_UPDATE_BY >
			".ucfirst(get_prenom($V_UPDATE_BY))." ".strtoupper(get_nom($V_UPDATE_BY))."</a></td>
      	  </tr>";

}


//=====================================================================
// ligne principal
//=====================================================================

if ( $gardes == 1 ) {

	$query2="select distinct EQ_ID, EQ_NOM from equipe where EQ_TYPE='GARDE' 
		 order by EQ_ID";
	$result2=mysql_query($query2);

	echo "<tr>
      	<td bgcolor=$mylightcolor ><b>Usage principal</b></td>
      	<td bgcolor=$mylightcolor align=left>
		<select name='EQ_ID' $disabled>";
		while ($row2=@mysql_fetch_array($result2)) {
		        $NEWEQ_ID=$row2["EQ_ID"];
		        $NEWEQ_NOM=$row2["EQ_NOM"];
		        if ( $NEWEQ_ID == $EQ_ID) $selected='selected';
		        else $selected='';
		        echo "<option value='$NEWEQ_ID' $selected>$NEWEQ_NOM</option>";
	    }
 	    echo "</select>";
	echo " </tr>";
}

//=====================================================================
// numéro d'inventaire
//=====================================================================

echo "<tr >
      	  <td bgcolor=$mylightcolor ><b>N°d'inventaire</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_INVENTAIRE' size='30' value=\"$V_INVENTAIRE\" $disabled>";		
echo " </td>
      </tr>";

//=====================================================================
// ligne commentaire
//=====================================================================

echo "<tr >
      	  <td bgcolor=$mylightcolor ><b>Commentaire</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='text' name='V_COMMENT' size='45' value=\"$V_COMMENT\" $disabled>";		
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
// equipement neige, clim
//=====================================================================
if ( $V_FLAG1 == 1 )$checked='checked';
else $checked='';
  
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Equipement neige</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='V_FLAG1' value='1' $checked $disabled
			title='Cocher la case si le véhicule est équipé pour rouler sur la neige'>";		
echo " </td>
      </tr>";
      
if ( $V_FLAG2 == 1 )$checked='checked';
else $checked='';
echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Climatisation</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='V_FLAG2' value='1' $checked $disabled
			title='Cocher la case si le véhicule est équipé de climatisation'>";		
echo " </td>
      </tr>";
      
//=====================================================================
// vehicule externe
//=====================================================================

if (check_rights($_SESSION['id'], 24)) $disabled2='';
else $disabled2='disabled';

if ( $V_EXTERNE == 1 )$checked='checked';
else $checked='';

if (( $disabled2=='' or $checked=='checked' ) and ($nbsections ==  0 )) {
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>véhicule $cisname</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='V_EXTERNE' value='1' $checked $disabled2>
			<font size=1><i>mis à disposition (utilisable, non modifiable)<i></font>";		
	echo " </td>
      </tr>";
}

//=====================================================================
// matériel embarqué
//=====================================================================
if ( $materiel == 1 ) {
    $query2="select m.TM_ID, tm.TM_CODE, tm.TM_CODE,tm.TM_USAGE,
		 m.VP_ID, vp.VP_OPERATIONNEL,vp.VP_LIBELLE,
		 m.MA_ID, m.MA_NUMERO_SERIE, m.MA_COMMENT, m.MA_MODELE, cm.PICTURE_SMALL,
		 m.MA_ANNEE, m.MA_NB, tm.TM_LOT,
		 DATE_FORMAT(m.MA_REV_DATE, '%d-%m-%Y') as MA_REV_DATE
		 from type_materiel tm, vehicule_position vp, categorie_materiel cm, materiel m
		 where m.TM_ID=tm.TM_ID
		 and cm.TM_USAGE = tm.TM_USAGE
		 and m.VP_ID=vp.VP_ID
		 and m.V_ID=".$V_ID;
	$result2=mysql_query($query2);
	
	echo "<tr class=TabHeader><td colspan=2>Matériel embarqué</td>
         </tr>";
	
	if ( mysql_num_rows($result2) > 0 ) {
	 
	     while ($row2=@mysql_fetch_array($result2)) {
	      	$TM_CODE=$row2["TM_CODE"];
	      	$TM_USAGE=$row2["TM_USAGE"];
	      	$VP_OPERATIONNEL=$row2["VP_OPERATIONNEL"];
	      	$VP_LIBELLE=$row2["VP_LIBELLE"];
	      	$MA_ID=$row2["MA_ID"];
			$TM_LOT=$row2["TM_LOT"];
	      	$MA_REV_DATE=$row2["MA_REV_DATE"];
	      	$MA_MODELE=$row2["MA_MODELE"];
			if ( $TM_LOT == 1 ) $lot=" (lot)";
			else $lot="";
	      	if ($row2["MA_NUMERO_SERIE"] <> "" ) 
	      		$MA_NUMERO_SERIE=" - ".$row2["MA_NUMERO_SERIE"];
	      	else $MA_NUMERO_SERIE="";
	      	$MA_NB=$row2["MA_NB"]; if ( $MA_NB == 1 ) $MA_NB="";
	      	$PICTURE_SMALL=$row2["PICTURE_SMALL"];
	      	
	      	if ( $VP_OPERATIONNEL == -1) $mytxtcolor='black';
      		else if ( $VP_OPERATIONNEL == 1) $mytxtcolor=$red;      
	  		else if ( my_date_diff(getnow(),$MA_REV_DATE) < 0 ) {
	  			$mytxtcolor=$orange;
	  			$VP_LIBELLE = "date dépassée";
	  		}
	  		else if ( $VP_OPERATIONNEL == 2) {
	  			$mytxtcolor=$orange;
	  		}
      		else $mytxtcolor=$green;
	      	
			$code=$MA_NB." ".$MA_MODELE." ".$MA_NUMERO_SERIE;
			if ( $code == '  ' ) $code='voir';
			
		 	echo "<tr>
      	  	<td bgcolor=$mylightcolor align=right>".$TM_CODE." ".$lot." 
				<img src=images/".$PICTURE_SMALL." title='".$TM_USAGE."'></td>
      	  	<td bgcolor=$mylightcolor align=left>
				<a href=upd_materiel.php?mid=".$MA_ID.">".$code."</a>
				<font size=1 color=".$mytxtcolor."> ".$VP_LIBELLE."</font>";
			if ($disabled == ""	) {
				echo "	<a href=upd_vehicule.php?vid=".$V_ID."&del=".$MA_ID.">
						<img height=14 border=0 src=images/trash.png title='Enlever ce matériel du lot'></a>";
			}
			echo " </td>
      		</tr>";
      	}
    }
	else echo "<tr><td colspan=2 bgcolor=$mylightcolor ><i>Aucun matériel embarqué dans ce véhicule</i></td></tr>";
	
	if ( $disabled=='') {
	echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Embarquer du matériel dans ce véhicule</b></td>
      	  <td bgcolor=$mylightcolor align=left>";		
	echo "<select id='add' name='add' $disabled onchange=\"javascript:addmateriel('".$V_ID."',this.form.add.value);\">";
	echo "<option class='type' value='0' selected>Choisir matériel</a>";
	// choix matériel à ajouter dans ce lot
	$query3="select m.MA_ID, m.MA_MODELE, tm.TM_CODE, m.MA_NUMERO_SERIE, s.S_CODE, tm.TM_USAGE
		 from materiel m, type_materiel tm, section s
   		 where s.S_ID= m.S_ID
		 and m.TM_ID=tm.TM_ID
		 and ( m.V_ID <> $V_ID or m.V_ID is null ) 
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
//=====================================================================
// grille de départ
//=====================================================================

if ( $gardes == 1 ) {
 
 	$query2="select ROLE_NAME, ROLE_ID
	     from type_vehicule_role 
		 where TV_CODE='".$TV_CODE."'";

	$result2=mysql_query($query2);
	if ( mysql_num_rows($result2) > 0 ) {
 
     //=====================================================================
     // intercalaire
     //=====================================================================

	echo "<tr>
      	  <td class=TabHeader colspan=2>Grille de départ par défaut</td>
      </tr>";

    //=====================================================================
    // equipage par défaut
    //=====================================================================

	while ($row2=@mysql_fetch_array($result2)) { 
		$ROLE_NAME=$row2["ROLE_NAME"];
 		$ROLE_ID=$row2["ROLE_ID"];
 		
 		echo "<tr>
      	     <td bgcolor=$mylightcolor align=right><b> $ROLE_NAME</b></td>";
 		echo "<td bgcolor=$mylightcolor align=left>";
 		echo "<select name='P".$ROLE_ID."' $disabled>";
 		echo "<option value='0'> aucun poste de garde</option>";
 		$CURR_POSTE=get_poste($V_ID,$ROLE_ID);
 		
		$query3 = "select p.EQ_ID, e.EQ_NOM, p.PS_ID, p.DESCRIPTION 
 				   from poste p, equipe e 
 				   where e.EQ_ID = p.EQ_ID
 				   and e.EQ_TYPE='GARDE'
				   order by p.EQ_ID, p.PS_ID";
 
 		$result3=mysql_query($query3);
 		
 		$EQ_ID_OLD = 0;

		while ($row3=@mysql_fetch_array($result3)) {
			$EQ_ID=$row3["EQ_ID"];
			$PS_ID=$row3["PS_ID"];	
 			$EQ_NOM=$row3["EQ_NOM"];
 			$DESCRIPTION=$row3["DESCRIPTION"];
			if ( $EQ_ID <> $EQ_ID_OLD)  echo "<OPTGROUP LABEL='".$EQ_NOM."'>";
			$EQ_ID_OLD = $EQ_ID;
			if ( $PS_ID == $CURR_POSTE ) $selected="selected";
			else $selected = "";
			echo "<option value='$PS_ID' $selected>$DESCRIPTION</option>";		

	    }
	    echo "</select></td>
        </tr>";
        //echo $query3;
      }
    }
	
}

echo "</table></tr></table>";
if ( $disabled == "") {
    echo "<p><input type='submit' value='sauver'> ";

	echo "</form><form name='vehicule2' action='save_vehicule.php'>";
	echo "<input type='hidden' name='V_ID' value='$V_ID'>";
	echo "<input type='hidden' name='TV_CODE' value='$TV_CODE'>";
	echo "<input type='hidden' name='V_IMMATRICULATION' value=\"$V_IMMATRICULATION\">";
	echo "<input type='hidden' name='V_COMMENT' value='V_COMMENT'>";
	echo "<input type='hidden' name='VP_ID' value='$VP_ID'>";
	echo "<input type='hidden' name='V_KM' value='$V_KM'>";
	echo "<input type='hidden' name='EQ_ID' value='$EQ_ID'>";
	echo "<input type='hidden' name='groupe' value='$S_ID'>";
	echo "<input type='hidden' name='dc1' value=''>";
	echo "<input type='hidden' name='dc2' value=''>";
	echo "<input type='hidden' name='dc3' value=''>";
	echo "<input type='hidden' name='from' value='$from'>";
	for ( $i = 1 ; $i <= 8 ; $i++) {
		echo "<input type='hidden' name='P".$i."' value=''>";
	}
	if ( check_rights($_SESSION['id'], 19)) {
		echo "<input type='hidden' name='V_MODELE' value=\"$V_MODELE\">";
		echo "<input type='hidden' name='V_ANNEE' value='$V_ANNEE'>";
		echo "<input type='hidden' name='V_INVENTAIRE' value=\"$V_INVENTAIRE\">";
		echo "<input type='hidden' name='V_INDICATIF' value=\"$V_INDICATIF\">";
		echo "<input type='hidden' name='operation' value='delete'>";

		echo "<input type='submit' value='supprimer'> ";
	}
}
echo "</form>";
if ( $from == 'export' ) {
	echo "<input type=submit value='fermer cette page' onclick='fermerfenetre();'> ";
}
else if ( $from == 'lot' ) {
	echo "<input type='button' value='Retour' name='annuler' onclick='redirect()'>";
}
else {
	echo "<input type='button' value='Retour' name='annuler' onclick=\"javascript:history.back(1);\">";
}

echo "</div>";
?>

<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
</BODY>
</HTML>
