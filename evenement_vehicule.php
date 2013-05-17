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
get_session_parameters();
$id=$_SESSION['id'];
$evenement=intval($_GET["evenement"]);
$vid=intval($_GET["V_ID"]);
$action=$_GET["action"];
$possibleorders= array('evenement','vehicule','dtdb');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='evenement';

/**$granted= false;
if ( check_rights($_SESSION['id'], 15, "$section_of") 
	or  check_rights($_SESSION['id'], 15, "$S_ID")
	or ( $id == $E_CHEF ))
$granted = true;**/

writehead();
?>
<STYLE type="text/css">
.section{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
.categorie{color:black; background-color:white; font-size:9pt;}
.vehicule{color:<?php echo $mydarkcolor; ?>; background-color:white; font-size:9pt;}
</STYLE>
<script type='text/javascript' src='popupBoxes.js'></script>
<SCRIPT>
function redirect(vehicule, section, dtdb, dtfn, order, subsections) {
	 url = "evenement_vehicule.php?vehicule="+vehicule+"&dtdb="+dtdb+"&order="+order+"&dtfn="+dtfn+"&filter="+section+"&subsections="+subsections;
	 self.location.href = url;
}
function redirect2(vehicule, section, dtdb, dtfn, order, sub) {
	 if (sub.checked) subsections = 1;
	 else subsections = 0;
	 url = "evenement_vehicule.php?vehicule="+vehicule+"&dtdb="+dtdb+"&order="+order+"&dtfn="+dtfn+"&filter="+section+"&subsections="+subsections;
	 self.location.href = url;
}
</SCRIPT>
<?php
echo "</head>";
include_once ("config.php");
echo "<body>";

$query="select distinct v.TV_CODE, v.V_ID, v.V_IMMATRICULATION, v.V_MODELE, 
		DATE_FORMAT(eh.EH_DATE_DEBUT, '%d-%m-%Y') as EH_DATE_DEBUT,
		DATE_FORMAT(eh.EH_DATE_FIN, '%d-%m-%Y') as EH_DATE_FIN, e.E_CODE,
		e.TE_CODE, e.E_LIBELLE, v.S_ID, s.S_DESCRIPTION,
		vp.VP_OPERATIONNEL, vp.VP_LIBELLE,ev.EV_KM,
		DATE_FORMAT(v.V_ASS_DATE, '%d-%m-%Y') as V_ASS_DATE,
		DATE_FORMAT(v.V_CT_DATE, '%d-%m-%Y') as V_CT_DATE,
		DATE_FORMAT(v.V_REV_DATE, '%d-%m-%Y') as V_REV_DATE,
		e.E_CANCELED, e.E_CLOSED,
		TIME_FORMAT(eh.EH_DEBUT, '%k:%i') as EH_DEBUT, 
		TIME_FORMAT(eh.EH_FIN, '%k:%i') as  EH_FIN,
		eh.EH_ID
        from evenement e, vehicule v, evenement_vehicule ev, section s, vehicule_position vp, evenement_horaire eh
        where v.V_ID=ev.V_ID
        and e.E_CODE = eh.E_CODE
        and s.S_ID=v.S_ID
        and vp.VP_ID = v.VP_ID
        and e.E_CODE=ev.E_CODE
		and eh.E_CODE=ev.E_CODE
		and eh.EH_ID=ev.EH_ID";
	
if ( $vehicule > 0 ) $query .= "\nand  v.V_ID = '".$vehicule."'";

$tmp=explode ( "-",$dtdb); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2]; 
$tmp=explode ( "-",$dtfn); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];

$query .="\n and eh.EH_DATE_DEBUT <= '$year2-$month2-$day2' 
			 and eh.EH_DATE_FIN   >= '$year1-$month1-$day1'";

if ( $nbsections <> 1 ) {
 	if ( $subsections == 1 )
 		$query .= "\n and v.S_ID in (".get_family("$filter").")";
 	else 
 		$query .= "\n and v.S_ID =".$filter;
}

if ( $order == 'vehicule') 	$query .="\n order by TV_CODE, V_ID";
if ( $order == 'dtdb') 	$query .="\norder by eh.EH_DATE_DEBUT, e.E_CODE";
if ( $order == 'evenement') $query .="\norder by e.E_CODE, eh.EH_DATE_DEBUT";
	
//echo "$query<br>";
$result=mysql_query($query);
$number=mysql_num_rows($result);



echo "<div align=center><font size=4><b>Engagements des véhicules </b></font><i>(".$number." touvés)</i><br>";
echo "<form name=formf>";
echo "<table width=400 cellspacing=0 border=0>";

//---------------------
// choix section
//---------------------
if ($nbsections == 0 ) {
  echo "<tr><td align=right width=40%>".choice_section_order('evenement_vehicule.php')."</td>";
  echo "<td align=left width=60% >
		<select id='filter' name='filter' 
	 title=\"cliquer sur Organisateur pour choisir le mode d'affichage de la liste\"
     onchange=\"redirect('0', this.form.filter.options[this.form.filter.selectedIndex].value,'$dtdb', '$dtfn', '$order', '$subsections')\">";
     display_children2(-1, 0, $filter, $nbmaxlevels, $sectionorder);
	echo "</select>";
	  
	if ( get_children("$filter") <> '' ) {
	  if ($subsections == 1 ) $checked='checked';
	  else $checked='';
	  echo "<br><input type='checkbox' name='subsections' id='subsections' value='1' $checked 
	   onClick=\"redirect2('0', '$filter','$dtdb', '$dtfn', '$order', this)\"/>
	   <label for='subsections'>inclure les sous sections</label>";
	}
	echo "</td></tr>";
}
//---------------------
// choix véhicule
//---------------------
echo "<tr><td align=right> Véhicule </td>";
echo "<td align=left>
      <select id='vehicule' name='vehicule' 
	  onchange=\"redirect( this.form.vehicule.options[this.form.vehicule.selectedIndex].value ,'$filter', '$dtdb', '$dtfn', '$order', '$subsections')\">";
echo "<option value='0' selected>Tous véhicules</option>\n";
$query2="select distinct v.V_ID, v.TV_CODE, v.V_MODELE, v.V_IMMATRICULATION, s.S_DESCRIPTION, s.S_ID, s.S_CODE
	    from vehicule v, section s
	    where s.S_ID = v.S_ID";
if ( $nbsections == 0 ) {
	if ( $subsections == 1 ) $list=get_children("$filter");
	else $list='';
 	if ( $list == '' ) $list=$filter;
 	else $list=$filter.",".$list;
	$query2 .= " and v.S_ID in (".$list.")
	 			order by s.S_ID, v.TV_CODE";
}
else $query2 .= " order by v.TV_CODE";

$result2=mysql_query($query2);
$prevS_ID=-1;
while ($row2=@mysql_fetch_array($result2)) {
      $V_ID=$row2["V_ID"];
      $S_ID=$row2["S_ID"];
      $S_CODE=$row2["S_CODE"];
      $TV_CODE=$row2["TV_CODE"];
      $S_DESCRIPTION=$row2["S_DESCRIPTION"];
      $V_MODELE=$row2["V_MODELE"];
	  if (( $prevS_ID <> $S_ID ) and ( $nbsections == 0 )) echo "<OPTGROUP LABEL='".$S_CODE." - ".$S_DESCRIPTION."' class='section'>";
      $prevS_ID=$S_ID;
      $V_IMMATRICULATION=$row2["V_IMMATRICULATION"];
      if ( $vehicule == $V_ID ) $selected='selected';
      else $selected='';
      echo "<option value='".$V_ID."' $selected class='vehicule'>".$TV_CODE." - ".$V_MODELE." - ".$V_IMMATRICULATION."</option>\n";
}
echo "</select></td></tr>";

//---------------------
// choix date
//---------------------
echo "<tr><td align=right >Début:</td><td align=left>";
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
echo " <input type='submit' value='go'>";
echo "</td></tr>";

echo "</table></form>";


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

if ( $number > 0 ) {
echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";
   echo "<table cellspacing=0 border=0>";
   echo "<tr height=10 >
      	  <td width=230 align=center>
			<a href=evenement_vehicule.php?order=evenement class=TabHeader>Evénement
		  </td>
      	  <td width=15 class=TabHeader></td>
      	  <td width=0 class=TabHeader></td>
      	  <td width=350 align=center>
			<a href=evenement_vehicule.php?order=vehicule class=TabHeader>Véhicule
		  </td>
      	  <td width=15 class=TabHeader></td>
      	  <td width=15 class=TabHeader></td>
      	  <td width=0 class=TabHeader></td>
      	  <td width=130 align=center><a href=evenement_vehicule.php?order=dtdb class=TabHeader>Date
		  </td>
      	  <td width=0 class=TabHeader></td>
      	  <td width=80 align=center class=TabHeader>Horaire</td>
      	  <td width=0 class=TabHeader></td>
      	  <td width=50 align=center class=TabHeader>Km</td>
      </tr>
      ";

   $i=0;
   $k=0;
   while ($row=@mysql_fetch_array($result)) {
       $TV_CODE=$row["TV_CODE"];
       $V_ID=$row["V_ID"];
       $EH_ID=$row["EH_ID"];
       $V_IMMATRICULATION=$row["V_IMMATRICULATION"];
       $V_MODELE=$row["V_MODELE"];
       $TE_CODE=$row["TE_CODE"];
       $E_LIBELLE=$row["E_LIBELLE"];
       $E_CODE=$row["E_CODE"];
       $EH_DATE_DEBUT=$row["EH_DATE_DEBUT"];
       $EH_DATE_FIN=$row["EH_DATE_FIN"];
       $EH_DEBUT=$row["EH_DEBUT"];
       $EH_FIN=$row["EH_FIN"];
       $E_CANCELED=$row["E_CANCELED"];
       $E_CLOSED=$row["E_CLOSED"];
       $S_ID=$row["S_ID"];
       $VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
       $VP_LIBELLE=$row["VP_LIBELLE"];
	   $V_ASS_DATE=$row["V_ASS_DATE"];
       $V_CT_DATE=$row["V_CT_DATE"];
       if ($row["EV_KM"] <> "" ) {
            if ( $EH_ID == 1) $EV_KM=$row["EV_KM"];
            else $EV_KM="-";
       }
       else $EV_KM="";
       $V_REV_DATE=$row["V_REV_DATE"];
       $S_DESCRIPTION=$row["S_DESCRIPTION"];
	   if ( $EH_DATE_FIN == '') $EH_DATE_FIN = $EH_DATE_DEBUT;
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
	  if ( $E_CANCELED == 1 ) $myimg='<img border=0 src=images/red.gif title=événement-annulé>';
	  elseif ( $E_CLOSED == 1 ) $myimg='<img border=0 src=images/yellow.gif title=inscriptions-fermées>';
	  else $myimg='<img border=0 src=images/green.gif title=inscriptions-ouvertes>';
	  
	  $tmp=explode ( "-",$EH_DATE_DEBUT); $day1=$tmp[0]; $month1=$tmp[1]; $year1=$tmp[2];
      $date1=mktime(0,0,0,$month1,$day1,$year1);
      $ladate=date_fran($month1, $day1 ,$year1)." ".moislettres($month1);
	
	  $year2=$year1;
	  $month2=$month1;
	  $day2=$day1;
	  
      if ( $EH_DATE_FIN <> '' and $EH_DATE_FIN <> $EH_DATE_DEBUT) {
	  	$tmp=explode ( "-",$EH_DATE_FIN); $day1=$tmp[0]; $month1=$tmp[1]; $year1=$tmp[2];
      	$date1=mktime(0,0,0,$month1,$day1,$year1);
      	$ladate=$ladate." au<br> ".date_fran($month1, $day1 ,$year1)." ".moislettres($month1)." ".$year1;
      }
      else $ladate=$ladate." ".$year1;
	  
	 $removelink="";
	 if (( check_rights($_SESSION['id'], 15)) and ( is_children($S_ID,$mysection))) {
      	$removelink="<a href=evenement_vehicule_add.php?evenement=".$E_CODE."&action=remove&V_ID=".$V_ID."&from=vehicule&dtdb=$dtdb&dtfn=$dtfn&order=$order&vehicule=$vehicule>
                    <img src=images/trash.png alt='désengager ce véhicule' border=0></a>";
     }

     if ( $nbsections == 0 ) $sectioninfo="(".$S_DESCRIPTION.")";
     else $sectioninfo="";
     if ( $E_CODE <> $k ) {
     	$evenementinfo="<td><table><tr>
			  <td><a href=evenement_display.php?evenement=".$E_CODE."&from=vehicule>
			   <img src=images/".$TE_CODE."small.gif height=14 border=0>
			 </td>
			 <td><a href=evenement_display.php?evenement=".$E_CODE."&from=vehicule>
			     <font size=1>".$E_LIBELLE."</a></font>
			 </td>
			 </tr></table></td>
			 <td >".$myimg."</td>";
			 $k = $E_CODE;	
	 }
	 else $evenementinfo="<td ></td>
	 					  <td >".$myimg."</td>";
	 	 
     echo "<tr bgcolor=$mycolor >";
     echo $evenementinfo;

	 if ( $VP_OPERATIONNEL == -1 ) $mytxtcolor="black";
     else if ( $VP_OPERATIONNEL == 1) $mytxtcolor=$red;      
	 else if ( my_date_diff(getnow(),$V_ASS_DATE) < 0 ) {
	  		$mytxtcolor=$red;
	  		$VP_LIBELLE = "assurance périmée";
	 }
	 else if ( my_date_diff(getnow(),$V_CT_DATE) < 0 ) {
	  		$mytxtcolor=$red;
	  		$VP_LIBELLE = "CT périmé";	  
	 }
	 else if ( $VP_OPERATIONNEL == 2) {
	  	$mytxtcolor=$orange;
	 }
	 else if (( my_date_diff(getnow(),$V_REV_DATE) < 0 ) and ( $VP_OPERATIONNEL <> 1)) {
	  	$mytxtcolor=$orange;
		$VP_LIBELLE = "révision à faire";
	 }  
     else $mytxtcolor=$green;


	 $nb = get_nb_engagements('V', $V_ID, $year1, $month1, $day1, $year2, $month2, $day2) - 1 ;
	 //$nb=1;
	 if ( $nb > 1 ) 
	   		$myimg="<img src=images/red.gif title='attention ce véhicule est parallèlement engagé sur $nb autres événements'>";
	 else if ( $nb == 1 )
	  		$myimg="<img src=images/yellow.gif title='attention ce véhicule est parallèlement engagé sur 1 autre événement'>";
	 else $myimg="";
	 
     echo "<td bgcolor=$mydarkcolor width=0 ></td>
      	  <td>
      	    <a href=upd_vehicule.php?vid=".$V_ID.">
			<b> ".$TV_CODE."</b> - <font size=1>".$V_MODELE." - ".$V_IMMATRICULATION."</a> ".$sectioninfo."</font><font color=$mytxtcolor size=1> $VP_LIBELLE</font></td>
      	  <td>".$myimg."</td>
      	  <td>".$removelink."</td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  
      	  <td align=center>
				<font size=1>".$ladate."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center>
				<font size=1>".$EH_DEBUT."-".$EH_FIN."</font></td>
		  <td bgcolor=$mydarkcolor width=0></td>
		  <td align=center>
				<font fsize=1>".$EV_KM."</font></td>
         </tr>";
   }
}
else {
     echo "<p><b>Aucune engagement ne correspond aux critères choisis</b>";
}

echo "</td></tr></table></table></div>";

//---------------------
// choix du conducteur
//---------------------

//if (( $action == "conducteur" ) and ( $granted )) {
		if ( isset($_GET["conducteur"])) $conducteur=intval($_GET["conducteur"]);
		else $conducteur=0;
		echo "le numéro du conducteur est le :".$conducteur;
		echo "le numéro du véhicule est le :" .$V_ID;
		//print_r($evts);
		//echo $V_ID;
		if ( $conducteur == 0) {
   			$query_cond="update evenement_vehicule set COND_ID=null
		   			where V_ID=".$vid." and E_CODE = ".$evenement."";		
		}			
		else {		
   			$query_cond="UPDATE evenement_vehicule SET COND_ID = ".$conducteur."
		   			WHERE E_CODE = ".$evenement." and V_ID=".$vid."";		
			
		}
//	}
$result_cond = mysql_query($query_cond) or die (mysql_error());	
?>
<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
</BODY>
</HTML>
