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
$mysection=$_SESSION['SES_SECTION'];

if (isset ($_GET["evenement"])) $evenement=intval($_GET["evenement"]);
elseif (isset ($_POST["evenement"])) $evenement=intval($_POST["evenement"]);
if (isset ($_GET["pid"])) $pid=intval($_GET["pid"]);
elseif (isset ($_POST["pid"])) $pid=intval($_POST["pid"]);
else $pid=0;
if (isset ($_GET["vid"])) $vid=intval($_GET["vid"]);
elseif (isset ($_POST["vid"])) $vid=intval($_POST["vid"]);
else $vid=0;
writehead();

$evts=get_event_and_renforts($evenement);

?>
<script type='text/javascript' src='dateFunctions.js'></script>
<script type='text/javascript'>

function closeme(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}

function custom(k,d1,d2,t1,t2,dur) {
 	var identique = document.getElementById('identique_'+k);
 	var dc1 = document.getElementById('dc1_'+k);
 	var dc2 = document.getElementById('dc2_'+k);
 	var debut = document.getElementById('debut_'+k);
 	var fin = document.getElementById('fin_'+k);
 	var duree = document.getElementById('duree_'+k);
 	var popcal1 = document.getElementById('popcal1_'+k);
 	var popcal2 = document.getElementById('popcal2_'+k);
 	
 	if ( identique.checked ) {
 		dc1.value = d1;
		dc2.value = d2;
		debut.value = t1;
		fin.value = t2;
		duree.value = dur;
		dc1.disabled=true;
		dc2.disabled=true;
		debut.disabled=true;
		fin.disabled=true;
		duree.disabled=true;
		popcal1.style.display = 'none';
		popcal2.style.display = 'none';
	}
	else {
		dc1.disabled=false;
		dc2.disabled=false;
		debut.disabled=false;
		fin.disabled=false;
		duree.disabled=false;
		popcal1.style.display = '';
		popcal2.style.display = '';	 
	 
	}
}

function hideRow(k) {
	document.getElementById('identiquerow_'+k).style.display = 'none';
	document.getElementById('debrow_'+k).style.display = 'none';
	document.getElementById('finrow_'+k).style.display = 'none';
	document.getElementById('plusrow_'+k).style.display = '';
	var identique = document.getElementById('identique_'+k);
	var dc1 = document.getElementById('dc1_'+k);
	identique.checked = false;
	dc1.value = '';
}

function showRow(k) {
	document.getElementById('identiquerow_'+k).style.display = '';
	document.getElementById('debrow_'+k).style.display = '';
	document.getElementById('finrow_'+k).style.display = '';
	document.getElementById('plusrow_'+k).style.display = 'none';
	var identique = document.getElementById('identique_'+k);
	identique.checked = true;
}

function notdefault() {
 	var identique = document.getElementById('identique');
 	if ( identique.checked ) {
 	 	identique.checked = false;
 	}
}

<?php
echo "</script>";
echo "</head>";

//=====================================================================
// recupérer infos evenement
//=====================================================================
$query="select e.TE_CODE, e.E_LIBELLE, e.E_CLOSED, e.E_CANCELED, e.E_OPEN_TO_EXT, e.S_ID, e.E_CHEF, eh.EH_ID,
		DATE_FORMAT(eh.EH_DATE_DEBUT, '%d-%m-%Y') EH_DATE_DEBUT, DATE_FORMAT(eh.EH_DATE_FIN, '%d-%m-%Y') EH_DATE_FIN,  
		TIME_FORMAT(eh.EH_DEBUT, '%k:%i') EH_DEBUT, TIME_FORMAT(eh.EH_FIN, '%k:%i') EH_FIN, eh.EH_DUREE
		from evenement e, evenement_horaire eh
		where e.E_CODE=eh.E_CODE
		and eh.E_CODE=e.E_CODE
		and e.E_CODE=".$evenement."
		order by eh.EH_ID";
$result=mysql_query($query);

$EH_ID= array();
$EH_DEBUT= array();
$EH_DATE_DEBUT= array();
$EH_DATE_FIN= array();
$EH_FIN= array();
$EH_DUREE= array();
$E_DUREE_TOTALE = 0;

while ($row=@mysql_fetch_array($result)) {
   $i=$row["EH_ID"];
   if ( $i == 1 ) {
		$TE_CODE=$row["TE_CODE"];
		$E_LIBELLE=$row["E_LIBELLE"];
		$E_CLOSED=$row["E_CLOSED"];
		$E_CANCELED=$row["E_CANCELED"];
		$E_OPEN_TO_EXT=$row["E_OPEN_TO_EXT"];
		$S_ID=$row["S_ID"];
		$E_CHEF=$row["E_CHEF"];
	}
	$EH_ID[$i]=$i;
	$EH_DATE_DEBUT[$i]=$row["EH_DATE_DEBUT"];
	if ( $row["EH_DATE_FIN"] == '' ) 
		$EH_DATE_FIN[$i]=$row["EH_DATE_DEBUT"];
    else 
	    $EH_DATE_FIN[$i]=$row["EH_DATE_FIN"];
    $EH_FIN[$i]=$row["EH_FIN"];
    $EH_DEBUT[$i]=$row["EH_DEBUT"];
    $EH_DUREE[$i]=$row["EH_DUREE"];
    if ( $EH_DUREE[$i] == "") $EH_DUREE[$i]=0;
    $E_DUREE_TOTALE = $E_DUREE_TOTALE + $EH_DUREE[$i];
}

if ( $pid > 0 )
	$nom_inscrit=ucfirst(get_prenom($pid))." ".strtoupper(get_nom($pid));
if ( $vid > 0 ) {
    $query="select V_IMMATRICULATION,TV_CODE, V_MODELE, V_INDICATIF
	    from vehicule
        where V_ID=$vid";
    $result=mysql_query($query);
 	$row=@mysql_fetch_array($result);
   	$nom_vehicule=$row["TV_CODE"]." ".$row["V_MODELE"]." ".$row["V_INDICATIF"]." <i>".$row["V_IMMATRICULATION"]."</i>";
}

$nbsessions=sizeof($EH_ID);

// =================================================
// sauver changements personnel
// =================================================
if (isset ($_POST["pid"])) {
   //echo "<pre>";
   //print_r($_POST);
   //echo "</pre>";
   $dc1=array();
   $dc2=array();
   $debut=array();
   $fin=array();
   $duree=array();
   // récupérer les infos globales
   $query="select EP_DATE, EP_BY, TP_ID, EP_COMMENT,EP_FLAG1, EE_ID from evenement_participation 
	        where P_ID=$pid
			and E_CODE=$evenement";
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   $EP_DATE=$row["EP_DATE"];
   $EP_BY=$row["EP_BY"];
   $TP_ID=$row["TP_ID"];
   $EE_ID=$row["EE_ID"];
   $EP_COMMENT=$row["EP_COMMENT"];
   $EP_FLAG1=$row["EP_FLAG1"];
   // boucler pour chaque session
   for ($k=1; $k <= $nbmaxsessionsparevenement; $k++) {
      if (isset ($_POST["identique_".$k])) {
          $identique[$k]=1;
      }
      else if ( isset($_POST["dc1_".$k])) {
          if ( $_POST["dc1_".$k] <> '' ) {
          	$identique[$k]=0;
	  	  	$dc1[$k]=mysql_real_escape_string($_POST["dc1_".$k]);
          	$dc2[$k]=mysql_real_escape_string($_POST["dc2_".$k]);
          	if ( $dc2[$k] == "" ) $dc2[$k] = $dc1[$k];
   	  	  	$duree[$k]=mysql_real_escape_string($_POST["duree_".$k]);
   	  	  	$debut[$k]=mysql_real_escape_string($_POST["debut_".$k]);
      	  	$fin[$k]=mysql_real_escape_string($_POST["fin_".$k]);
      	  	$tmp=explode ( "-",$dc1[$k]); $year1=$tmp[2]; $month1=$tmp[1]; $day1=$tmp[0];
		  	$tmp=explode ( "-",$dc2[$k]); $year2=$tmp[2]; $month2=$tmp[1]; $day2=$tmp[0];
		  }
		  else $identique[$k]=-1;
      }
      else $identique[$k]=-1;
      

   	  $query="select EH_ID from evenement_participation 
	        where P_ID=$pid
			and E_CODE in (".$evts.")
			and EH_ID=$k";
      $result=mysql_query($query);
      $nbp=mysql_num_rows($result);
      
	  // cas 1 nouvel enregistrement ou update existant
	  if ( $identique[$k] >= 0)  {
	      if($nbp == 0)  {
	       	if ( $EE_ID == '' ) $EE_ID = 'null';
 	  		$query="insert into evenement_participation 
		        (E_CODE, EH_ID, P_ID, EP_DATE, EP_DATE_DEBUT, EP_DATE_FIN, EP_DEBUT, EP_FIN, EP_DUREE, 
				 EP_COMMENT, EP_BY, TP_ID, EP_FLAG1, EE_ID )
 			    values( $evenement, $k, $pid, '".$EP_DATE."', ";
 			if ( $identique[$k] == 0 ) {
		  		$query .="'".$year1."-".$month1."-".$day1."',";
		  		$query .="'".$year2."-".$month2."-".$day2."',";
		  		$query .="'".$debut[$k]."',";
		  		$query .="'".$fin[$k]."',";
		  		$query .="'".$duree[$k]."',";
		  	}
		  	else $query .="null,null,null,null,null,";
		  	if ( $EP_COMMENT <> '' ) $query .="\"".$EP_COMMENT."\",";
		  	else $query .= "null,";
		  	$query .=$EP_BY.",".$TP_ID.",".$EP_FLAG1.",".$EE_ID.")";
	      }
	      else  {	
	 		$query="update evenement_participation";
		  	if ( $identique[$k] == 0 )
 				$query .=" set EP_DATE_DEBUT='".$year1."-".$month1."-".$day1."',
 				EP_DATE_FIN='".$year2."-".$month2."-".$day2."',
 				EP_DEBUT='".$debut[$k]."',
 				EP_FIN='".$fin[$k]."',
 				EP_DUREE='".$duree[$k]."'";
 			else  $query .=" set EP_DATE_DEBUT=null,
 				EP_DATE_FIN=null,
 				EP_DEBUT=null,
 				EP_FIN=null,
 				EP_DUREE=null";
	 		$query .=" where P_ID=$pid
	 		    and EH_ID = $k
			    and E_CODE in (".$evts.")";
	   }
	}
	else // pas ou plus de participation pour la session
	 	$query="delete from evenement_participation where P_ID=$pid
				and E_CODE=$evenement
				and EH_ID=$k";
	// sauver
	$result=mysql_query($query);
	
	// cas particulier mêmes horaires que événement sans la coche
	if ( $identique[$k] == 0 ) {
		$query="update evenement_participation set 
				EP_DATE_DEBUT=null,
 				EP_DATE_FIN=null,
 				EP_DEBUT=null,
 				EP_FIN=null,
 				EP_DUREE=null
				where P_ID=$pid
				and E_CODE in (".$evts.")
				and DATE_FORMAT(EP_DATE_DEBUT, '%d-%m-%Y') ='".$EH_DATE_DEBUT[$k]."'
				and DATE_FORMAT(EP_DATE_FIN, '%d-%m-%Y') ='".$EH_DATE_FIN[$k]."'
				and TIME_FORMAT(EP_DEBUT, '%k:%i') = '".$EH_DEBUT[$k]."'
				and TIME_FORMAT(EP_FIN, '%k:%i') = '".$EH_FIN[$k]."'
				and EP_DUREE = '".$EH_DUREE[$i]."'
	 		    and EH_ID = ".$k;
	 	$result=mysql_query($query);
	}
  }
  echo "<body onload=\"javascript:opener.document.location.href='evenement_display.php?evenement=".$evenement."&from=inscription';closeme();\">";
}

// =================================================
// sauver changements vehicule
// =================================================
if (isset ($_POST["vid"])) {
   //echo "<pre>";
   //print_r($_POST);
   //echo "</pre>";
   $dc1=array();
   $dc2=array();
   $debut=array();
   $fin=array();
   $duree=array();

   // récupérer les infos globales
   $query="select EV_KM from evenement_vehicule
	        where V_ID=$vid
			and E_CODE=$evenement";
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   $EV_KM=$row["EV_KM"];
   // boucler pour chaque session
   for ($k=1; $k <= $nbmaxsessionsparevenement; $k++) {
      if (isset ($_POST["identique_".$k])) {
          $identique[$k]=1;
      }
      else if ( isset($_POST["dc1_".$k])) {
          if ( $_POST["dc1_".$k] <> '' ) {
          	$identique[$k]=0;
	  	  	$dc1[$k]=mysql_real_escape_string($_POST["dc1_".$k]);
          	$dc2[$k]=mysql_real_escape_string($_POST["dc2_".$k]);
          	if ( $dc2[$k] == "" ) $dc2[$k] = $dc1[$k];
   	  	  	$duree[$k]=mysql_real_escape_string($_POST["duree_".$k]);
   	  	  	$debut[$k]=mysql_real_escape_string($_POST["debut_".$k]);
      	  	$fin[$k]=mysql_real_escape_string($_POST["fin_".$k]);
      	  	$tmp=explode ( "-",$dc1[$k]); $year1=$tmp[2]; $month1=$tmp[1]; $day1=$tmp[0];
		  	$tmp=explode ( "-",$dc2[$k]); $year2=$tmp[2]; $month2=$tmp[1]; $day2=$tmp[0];
		  }
		  else $identique[$k]=-1;
      }
      else $identique[$k]=-1;
      

   	  $query="select EH_ID from evenement_vehicule
	        where V_ID=$vid
			and E_CODE in (".$evts.")
			and EH_ID=$k";
      $result=mysql_query($query);
      $nbp=mysql_num_rows($result);
      
	  // cas 1 nouvel enregistrement ou update existant
	  if ( $identique[$k] >= 0)  {
	      if($nbp == 0)  {
 	  		$query="insert into evenement_vehicule
		        (E_CODE, EH_ID, V_ID, EV_KM, EV_DATE_DEBUT, EV_DATE_FIN, EV_DEBUT, EV_FIN, EV_DUREE)
 			    values( $evenement, $k, $vid, ";
 			if ( $EV_KM <> '' ) $query .=$EV_KM.",";
		  	else $query .= "null,";
 			if ( $identique[$k] == 0 ) {
		  		$query .="'".$year1."-".$month1."-".$day1."',";
		  		$query .="'".$year2."-".$month2."-".$day2."',";
		  		$query .="'".$debut[$k]."',";
		  		$query .="'".$fin[$k]."',";
		  		$query .="'".$duree[$k]."'";
		  	}
		  	else $query .="null,null,null,null,null";
		  	$query .=")";
	      }
	      else  {	
	 		$query="update evenement_vehicule";
		  	if ( $identique[$k] == 0 )
 				$query .=" set EV_DATE_DEBUT='".$year1."-".$month1."-".$day1."',
 				EV_DATE_FIN='".$year2."-".$month2."-".$day2."',
 				EV_DEBUT='".$debut[$k]."',
 				EV_FIN='".$fin[$k]."',
 				EV_DUREE='".$duree[$k]."'";
 			else  $query .=" set EV_DATE_DEBUT=null,
 				EV_DATE_FIN=null,
 				EV_DEBUT=null,
 				EV_FIN=null,
 				EV_DUREE=null";
	 		$query .=" where V_ID=$vid
	 		    and EH_ID = $k
			    and E_CODE in (".$evts.")";
	   }
	}
	else // pas ou plus de participation pour la session
	 	$query="delete from evenement_vehicule where V_ID=$vid
				and E_CODE=$evenement
				and EH_ID=$k";
	// sauver
	$result=mysql_query($query);
	
	// cas particulier mêmes horaires que événement sans la coche
	if ( $identique[$k] == 0 ) {
		$query="update evenement_vehicule set 
				EV_DATE_DEBUT=null,
 				EV_DATE_FIN=null,
 				EV_DEBUT=null,
 				EV_FIN=null,
 				EV_DUREE=null
				where V_ID=$pid
				and E_CODE in (".$evts.")
				and DATE_FORMAT(EV_DATE_DEBUT, '%d-%m-%Y') ='".$EH_DATE_DEBUT[$k]."'
				and DATE_FORMAT(EV_DATE_FIN, '%d-%m-%Y') ='".$EH_DATE_FIN[$k]."'
				and TIME_FORMAT(EV_DEBUT, '%k:%i') = '".$EH_DEBUT[$k]."'
				and TIME_FORMAT(EV_FIN, '%k:%i') = '".$EH_FIN[$k]."'
	 		    and EH_ID = ".$k;
	 	$result=mysql_query($query);
	}
  }
  echo "<body onload=\"javascript:opener.document.location.href='evenement_display.php?evenement=".$evenement."&from=vehicule';closeme();\">";
}

// =================================================
// AFFICHAGE
// =================================================

echo "\n<div align=center><table cellspacing=0 border=0>
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

if ( $id == $E_CHEF or $id=$pid) {
 $granted_event=true;
 $granted_personnel=true;
 $chef=true;
}
else if (check_rights($id, 26, $organisateur)) { 
 	$veille=true;
	$SECTION_CADRE=get_highest_section_where_granted($id,26);
}
else $veille=false;


//=====================================================================
// modifier horaires d'une personne
//=====================================================================
if ( $pid > 0 ) {
if (check_rights($id, 10, $organisateur) or check_rights($id, 15, $organisateur)) $granted_personnel=true;
else if (!$granted_personnel) check_all(10);

for ($k=1; $k <= $nbmaxsessionsparevenement; $k++) {
    if ( isset ($EH_ID[$k])){
		$query="select EH_ID, DATE_FORMAT(EP_DATE_DEBUT,'%d-%m-%Y') EP_DATE_DEBUT,  DATE_FORMAT(EP_DATE_FIN, '%d-%m-%Y') EP_DATE_FIN,
			   TIME_FORMAT(EP_DEBUT, '%k:%i') EP_DEBUT,  TIME_FORMAT(EP_FIN, '%k:%i') EP_FIN , EP_DUREE
		from evenement_participation 
		where P_ID=$pid
		and EH_ID=$k
		and E_CODE in (".$evts.")";
		
		$result=mysql_query($query);
		$row=@mysql_fetch_array($result);
		$EPH_ID[$k]=$row["EH_ID"];
		$EP_DATE_DEBUT[$k]=$row["EP_DATE_DEBUT"];
		$EP_DATE_FIN[$k]=$row["EP_DATE_FIN"];
		$EP_DEBUT[$k]=$row["EP_DEBUT"];
		$EP_FIN[$k]=$row["EP_FIN"];
		$EP_DUREE[$k]=$row["EP_DUREE"];	
	}
}

echo "<form name=frm action='evenement_horaires.php' method='POST'>";
echo "<div align=center><table>";
echo "<tr>
<td class='FondMenu'>";


echo "<input type='hidden' name='evenement' value='$evenement'>";
echo "<input type='hidden' name='pid' value='$pid'>";
echo "<table cellspacing=0 border=0>";
echo "<tr>
      	   <td class=TabHeader colspan=2><img src=images/user.png title='personnes'><b> ".$nom_inscrit."</b></td>
      </tr>";


for ($k=1; $k <= $nbmaxsessionsparevenement; $k++) {
  if ( isset($EH_ID[$k])) {
    // si participation enregistree pour la session
	if ( isset($EPH_ID[$k])) {
	    $style='';
 		$antistyle="style='display:none'";
 		// comme evenement
 		if ( $EP_DATE_DEBUT[$k] == "" ) {
 			$checked='checked';
 			$disabled='disabled';
 			$EP_DATE_DEBUT[$k]=$EH_DATE_DEBUT[$k];
			$EP_DATE_FIN[$k]=$EH_DATE_FIN[$k];
			$EP_DEBUT[$k]=$EH_DEBUT[$k];
			$EP_FIN[$k]=$EH_FIN[$k];
			$EP_DUREE[$k]=$EH_DUREE[$k];
		}
		else { // different de l'evenement
		 	$checked='';
 			$disabled='';
		}
	}
	// si pas de participation enregistree pour la session
	else {
 		$checked='';
 		$disabled='disabled';
		$style="style='display:none'";
        $antistyle='';
        $EP_DATE_DEBUT[$k]=$EH_DATE_DEBUT[$k];
		$EP_DATE_FIN[$k]=$EH_DATE_FIN[$k];
		$EP_DEBUT[$k]=$EH_DEBUT[$k];
		$EP_FIN[$k]=$EH_FIN[$k];
		$EP_DUREE[$k]=$EH_DUREE[$k];
    }

	echo "<tr id='identiquerow_".$k."' height=25 $style>
      <td bgcolor=$mylightcolor rowspan=3 align=center width=100><b>Partie n°".$k."</b> ";
    
    if ( $nbsessions > 1 ) 
		echo "<br><img src=images/trash.png border=0 title='Ne participe pas à cette partie.\nOu est absente.' 
	     onclick=\"javascript:hideRow('$k');\">";
	echo "</td>";
	echo "<td bgcolor=$mylightcolor width=300><b>Horaires identiques à ceux de la partie n°$k?</b>
		  	<input type=checkbox id='identique_$k' name='identique_$k' value=1 $checked 
		  	onclick=\"custom('$k','$EH_DATE_DEBUT[$k]','$EH_DATE_FIN[$k]','$EH_DEBUT[$k]','$EH_FIN[$k]','$EH_DUREE[$k]');\"></td>";
	echo "<td bgcolor=$mylightcolor rowspan=3 width=100>durée ";
	
	echo "<input type=\"text\" name=\"duree_$k\" id=\"duree_$k\" value=\"".$EP_DUREE[$k]."\" size=\"3\" length=3
	onfocus=\"EvtCalcDuree(document.frm.dc1_$k,document.frm.dc2_$k,document.frm.debut_$k,document.frm.fin_$k,document.frm.duree_$k);\" 
	title='durée en heures de la partie n°$k' $disabled>h ";
	echo "</td></tr>";

	echo "<tr id='debrow_".$k."' $style>";
	echo " <td bgcolor=$mylightcolor align=left> du";
	echo "<input class=\"plain\" name=\"dc1_$k\" id=\"dc1_$k\" value=\"".$EP_DATE_DEBUT[$k]."\"
	size=\"12\" onchange=\"checkDate2(document.frm.dc1_$k)\" title=\"Date début format jj-mm-yyyy\" $disabled>
	<a href=\"javascript:void(0)\" onclick=\"if(self.gfPop)gfPop.fStartPop(document.frm.dc1_$k,document.frm.dc2_$k);return false;\" HIDEFOCUS>
	<img name=\"popcal\" align=\"absmiddle\" src=\"images/calbtn.gif\" width=\"34\" height=\"22\" border=\"0\" alt=\"\" ></a>";
	
	echo " à <select id='debut_$k' name='debut_$k' 
	onchange=\"EvtCalcDuree(document.frm.dc1_$k,document.frm.dc2_$k,document.frm.debut_$k,document.frm.fin_$k,document.frm.duree_$k);\" $disabled>";
	for ( $i=0; $i <= 24; $i++ ) {
    	$check = $i.":00";
    	if (  $check == $EP_DEBUT[$k] ) $selected="selected";
    	else $selected="";
    	echo "<option value=".$i.":00 ".$selected.">".$i.":00</option>\n";
    	if ( $i.":30" == $EP_DEBUT[$k] ) $selected="selected";
    	else $selected="";
    	if ( $i < 24 )
       		echo "<option value=".$i.":30 ".$selected.">".$i.":30</option>\n";
	}
	echo "</select>";

	echo "<tr id='finrow_".$k."' $style>";
	echo "<td bgcolor=$mylightcolor align=left> au";
	echo "<input class=\"plain\" name=\"dc2_$k\" id=\"dc2_$k\" value=\"".$EP_DATE_FIN[$k]."\"
	size=\"12\" onchange=\"checkDate2(document.frm.dc2_$k)\" title=\"Date fin format jj-mm-yyyy\" $disabled>
	<a href=\"javascript:void(0)\" onclick=\"if(self.gfPop)gfPop.fEndPop(document.frm.dc1_$k,document.frm.dc2_$k);return false;\" HIDEFOCUS>
	<img name=\"popcal\" align=\"absmiddle\" src=\"images/calbtn.gif\" width=\"34\" height=\"22\" border=\"0\" alt=\"\" ></a>";
	echo " à <select id='fin_$k' name='fin_$k' 
	onchange=\"EvtCalcDuree(document.frm.dc1_$k,document.frm.dc2_$k,document.frm.debut_$k,document.frm.fin_$k,document.frm.duree_$k);\" $disabled>";
	for ( $i=0; $i <= 24; $i++ ) {
   		if ( $i.":00" == $EP_FIN[$k] ) $selected="selected";
   		else $selected="";
   		echo "<option value=".$i.":00 $selected>".$i.":00</option>\n";
   		if ( $i.":30" == $EP_FIN[$k] ) $selected="selected";
   		else $selected="";
   		if ( $i < 24 )
      		echo "<option value=".$i.":30 $selected>".$i.":30</option>\n";	  
	}
	echo "</select></td></tr>";
	
	echo "<tr id='plusrow_".$k."' $antistyle>
	   <td bgcolor=$mylightcolor align=center width=100><b>Partie n°".$k."</b><br>
	   <img src=images/plusgreen.png border=0 title='Ajouter participation à cette partie' 
	     onclick=\"javascript:showRow('$k');\">";
	echo "</td>
	      <td bgcolor=$mylightcolor width=400 colspan=2>
		  <i>Pas de participation sur la partie n°$k</i></td></tr>";
  }
}
}

//=====================================================================
// modifier horaires d'un véhicule
//=====================================================================

if ( $vid > 0 ) {
if (check_rights($id, 17, $organisateur) or check_rights($id, 15, $organisateur)) $granted_vehicule=true;
else if (!$granted_vehicule) check_all(17);

for ($k=1; $k <= $nbmaxsessionsparevenement; $k++) {
    if ( isset ($EH_ID[$k])){
		$query="select EH_ID, DATE_FORMAT(EV_DATE_DEBUT,'%d-%m-%Y') EV_DATE_DEBUT,  DATE_FORMAT(EV_DATE_FIN, '%d-%m-%Y') EV_DATE_FIN,
			   TIME_FORMAT(EV_DEBUT, '%k:%i') EV_DEBUT,  TIME_FORMAT(EV_FIN, '%k:%i') EV_FIN , EV_DUREE
		from evenement_vehicule 
		where V_ID=$vid
		and EH_ID=$k
		and E_CODE in (".$evts.")";
		
		$result=mysql_query($query);
		$row=@mysql_fetch_array($result);
		$EVH_ID[$k]=$row["EH_ID"];
		$EV_DATE_DEBUT[$k]=$row["EV_DATE_DEBUT"];
		$EV_DATE_FIN[$k]=$row["EV_DATE_FIN"];
		$EV_DEBUT[$k]=$row["EV_DEBUT"];
		$EV_FIN[$k]=$row["EV_FIN"];
		$EV_DUREE[$k]=$row["EV_DUREE"];	
	}
}

echo "<form name=frm action='evenement_horaires.php' method='POST'>";
echo "<div align=center><table>";
echo "<tr>
<td class='FondMenu'>";


echo "<input type='hidden' name='evenement' value='$evenement'>";
echo "<input type='hidden' name='vid' value='$vid'>";
echo "<table cellspacing=0 border=0>";
echo "<tr>
      	   <td class=TabHeader colspan=2><img src=images/car.png title='vehicule'><b> ".$nom_vehicule."</b></td>
      </tr>";


for ($k=1; $k <= $nbmaxsessionsparevenement; $k++) {
  if ( isset($EH_ID[$k])) {
    // si participation enregistree pour la session
	if ( isset($EVH_ID[$k])) {
	    $style='';
 		$antistyle="style='display:none'";
 		// comme evenement
 		if ( $EV_DATE_DEBUT[$k] == "" ) {
 			$checked='checked';
 			$disabled='disabled';
 			$EV_DATE_DEBUT[$k]=$EH_DATE_DEBUT[$k];
			$EV_DATE_FIN[$k]=$EH_DATE_FIN[$k];
			$EV_DEBUT[$k]=$EH_DEBUT[$k];
			$EV_FIN[$k]=$EH_FIN[$k];
			$EV_DUREE[$k]=$EH_DUREE[$k];
		}
		else { // different de l'evenement
		 	$checked='';
 			$disabled='';
		}
	}
	// si pas de participation enregistree pour la session
	else {
 		$checked='';
 		$disabled='disabled';
		$style="style='display:none'";
        $antistyle='';
        $EV_DATE_DEBUT[$k]=$EH_DATE_DEBUT[$k];
		$EV_DATE_FIN[$k]=$EH_DATE_FIN[$k];
		$EV_DEBUT[$k]=$EH_DEBUT[$k];
		$EV_FIN[$k]=$EH_FIN[$k];
		$EV_DUREE[$k]=$EH_DUREE[$k];
    }

	echo "<tr id='identiquerow_".$k."' height=25 $style>
      <td bgcolor=$mylightcolor rowspan=3 align=center width=100><b>Partie n°".$k."</b> ";
    
    if ( $nbsessions > 1 ) 
		echo "<br><img src=images/trash.png border=0 title='Pas engagé sur cette partie.\nOu est absente.' 
	     onclick=\"javascript:hideRow('$k');\">";
	echo "</td>";
	echo "<td bgcolor=$mylightcolor width=300><b>Horaires identiques à ceux de la partie n°$k?</b>
		  	<input type=checkbox id='identique_$k' name='identique_$k' value=1 $checked 
		  	onclick=\"custom('$k','$EH_DATE_DEBUT[$k]','$EH_DATE_FIN[$k]','$EH_DEBUT[$k]','$EH_FIN[$k]','$EH_DUREE[$k]');\"></td>";
	echo "<td bgcolor=$mylightcolor rowspan=3 width=100>durée ";
	
	echo "<input type=\"text\" name=\"duree_$k\" id=\"duree_$k\" value=\"".$EV_DUREE[$k]."\" size=\"3\" length=3
	onfocus=\"EvtCalcDuree(document.frm.dc1_$k,document.frm.dc2_$k,document.frm.debut_$k,document.frm.fin_$k,document.frm.duree_$k);\" 
	title='durée en heures de la partie n°$k' $disabled>h ";
	echo "</td></tr>";

	echo "<tr id='debrow_".$k."' $style>";
	echo " <td bgcolor=$mylightcolor align=left> du";
	echo "<input class=\"plain\" name=\"dc1_$k\" id=\"dc1_$k\" value=\"".$EV_DATE_DEBUT[$k]."\"
	size=\"12\" onchange=\"checkDate2(document.frm.dc1_$k)\" title=\"Date début format jj-mm-yyyy\" $disabled>
	<a href=\"javascript:void(0)\" onclick=\"if(self.gfPop)gfPop.fStartPop(document.frm.dc1_$k,document.frm.dc2_$k);return false;\" HIDEFOCUS>
	<img name=\"popcal\" align=\"absmiddle\" src=\"images/calbtn.gif\" width=\"34\" height=\"22\" border=\"0\" alt=\"\" ></a>";
	
	echo " à <select id='debut_$k' name='debut_$k' 
	onchange=\"EvtCalcDuree(document.frm.dc1_$k,document.frm.dc2_$k,document.frm.debut_$k,document.frm.fin_$k,document.frm.duree_$k);\" $disabled>";
	for ( $i=0; $i <= 24; $i++ ) {
    	$check = $i.":00";
    	if (  $check == $EV_DEBUT[$k] ) $selected="selected";
    	else $selected="";
    	echo "<option value=".$i.":00 ".$selected.">".$i.":00</option>\n";
    	if ( $i.":30" == $EV_DEBUT[$k] ) $selected="selected";
    	else $selected="";
    	if ( $i < 24 )
       		echo "<option value=".$i.":30 ".$selected.">".$i.":30</option>\n";
	}
	echo "</select>";

	echo "<tr id='finrow_".$k."' $style>";
	echo "<td bgcolor=$mylightcolor align=left> au";
	echo "<input class=\"plain\" name=\"dc2_$k\" id=\"dc2_$k\" value=\"".$EV_DATE_FIN[$k]."\"
	size=\"12\" onchange=\"checkDate2(document.frm.dc2_$k)\" title=\"Date fin format jj-mm-yyyy\" $disabled>
	<a href=\"javascript:void(0)\" onclick=\"if(self.gfPop)gfPop.fEndPop(document.frm.dc1_$k,document.frm.dc2_$k);return false;\" HIDEFOCUS>
	<img name=\"popcal\" align=\"absmiddle\" src=\"images/calbtn.gif\" width=\"34\" height=\"22\" border=\"0\" alt=\"\" ></a>";
	echo " à <select id='fin_$k' name='fin_$k' 
	onchange=\"EvtCalcDuree(document.frm.dc1_$k,document.frm.dc2_$k,document.frm.debut_$k,document.frm.fin_$k,document.frm.duree_$k);\" $disabled>";
	for ( $i=0; $i <= 24; $i++ ) {
   		if ( $i.":00" == $EV_FIN[$k] ) $selected="selected";
   		else $selected="";
   		echo "<option value=".$i.":00 $selected>".$i.":00</option>\n";
   		if ( $i.":30" == $EV_FIN[$k] ) $selected="selected";
   		else $selected="";
   		if ( $i < 24 )
      		echo "<option value=".$i.":30 $selected>".$i.":30</option>\n";	  
	}
	echo "</select></td></tr>";
	
	echo "<tr id='plusrow_".$k."' $antistyle>
	   <td bgcolor=$mylightcolor align=center width=100><b>Partie n°".$k."</b><br>
	   <img src=images/plusgreen.png border=0 title='Ajouter participation à cette partie' 
	     onclick=\"javascript:showRow('$k');\">";
	echo "</td>
	      <td bgcolor=$mylightcolor width=400 colspan=2>
		  <i>Pas de participation sur la partie n°$k</i></td></tr>";
  }
}
}
echo "</table>";// end left table
echo "</td></tr></table>"; // end cadre

echo "<p>
<input type='submit' value='sauver'>
<input type='button' value='fermer cette page' onclick='closeme();'>
</div></form>";
?>

<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
</BODY>
</HTML>