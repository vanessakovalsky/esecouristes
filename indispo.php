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
check_all(11);

if ( isset ($_GET["person"])) $person=intval($_GET["person"]);
else $person=$_SESSION['id'];

//section
if (isset ($_GET["section"])) {
   $_SESSION['sectionchoice1'] = intval($_GET["section"]);
   $section=intval($_GET["section"]);
}
else if ( isset($_SESSION['sectionchoice1']) ) {
   $section=$_SESSION['sectionchoice1'];
}
else $section=$_SESSION['SES_SECTION'];

$mysection=get_highest_section_where_granted($_SESSION['id'],12);
if ( check_rights($_SESSION['id'], 24) ) $mysection='0';
else if ( $mysection == '' ) $mysection=$_SESSION['SES_SECTION'];

writehead();
?>
<script type='text/javascript' src='checkForm.js'></script>
<script type='text/javascript' src='dateFunctions.js'></script>
<script>

function redirect(section) {
	 url = "indispo.php?section="+section;
	 self.location.href = url;
}


function changeDisplay() {
 	if ( document.getElementById('full_day').checked ) {
 	 	document.getElementById("debut").style.visibility='hidden';
 	 	document.getElementById("fin").style.visibility='hidden';
 	}
 	else {
		document.getElementById("debut").style.visibility='visible';
 	 	document.getElementById("fin").style.visibility='visible'; 
 	}
}


function changedType() {
 	var type = document.getElementById('type');
    if (type.value == '') {
		document.getElementById("save").disabled=true;
	} else {
		document.getElementById("save").disabled=false;
	}
}
</script>
</head>
<?php
//=====================================================================
// debut tableau
//=====================================================================


echo "<body onload='changeDisplay();'>";
echo "<div align=center><font size=4><b>Saisie absence</b></font><br>";

echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<tr>
      	  <td class=TabHeader>Informations</td>
      </tr>";

echo "<form name=demoform action='indispo_save.php'>";

//=====================================================================
// choix section
//=====================================================================

if (($nbsections <> 1 ) and  ( check_rights($_SESSION['id'], 12) )) {
	
	$level=get_level($mysection);
 	if ( $level == 0 ) $mycolor=$myothercolor;
	elseif ( $level == 1 ) $mycolor=$my2darkcolor;
    elseif ( $level == 2 ) $mycolor=$my2lightcolor;
    elseif ( $level == 3 ) $mycolor=$mylightcolor;
    else $mycolor='white';
    $class="style='background: $mycolor;'";
    
 	echo "<tr><td bgcolor=$mylightcolor width=100><b>Section</b> <font color=red>*</font></td>";
	echo "<td bgcolor=$mylightcolor width=200 align=left>
		<select name='s1' id='s1' title='filtrer le personnel' onChange=\"redirect(document.getElementById('s1').value);\">";
	echo "<option value='$mysection' $class >".
		str_repeat(". ",$level)." ".get_section_code($mysection)." - ".get_section_name($mysection)."</option>";
	display_children2($mysection, $level +1, $section, $nbmaxlevels);
	echo "</select></td></tr>";

}

//=====================================================================
// choix personne
//=====================================================================


echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Personne</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>";

//cas personnel habilités sur F 12
if ( check_rights($_SESSION['id'], 12)  ) {
   echo "<select id='person' name='person'>";
   $query="select P_ID, P_PRENOM, P_NOM , S_CODE
   		   from pompier, section
		   where P_SECTION = S_ID
		   and P_OLD_MEMBER = 0
		   and P_STATUT <> 'EXT'";
    if ( $nbsections <> 1 ) {
      //if ( $section == 0 ) $query .= " and P_SECTION = ".$section;
	  //else $query .= " and P_SECTION in (".get_family("$section").")"; 		  
	  $query .= " and P_SECTION = ".$section;
   }			  
   $query .= " order by P_NOM";
   
   echo $query;
   $result=mysql_query($query);
   while ($row=@mysql_fetch_array($result)) {
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_ID=$row["P_ID"];
      $S_CODE=$row["S_CODE"];
      echo "<option value='".$P_ID."'";
      if ($P_ID == $person ) echo " selected ";
      if ( $nbsections == 1 )
      	echo ">".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</option>\n";
      else
      	echo ">".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)." (".$S_CODE.")</option>\n";
   }
   echo "</select>";
}
else {
     echo "<input type=hidden id='person' name='person' value=".$person.">";
     echo strtoupper($_SESSION['SES_NOM'])." ".ucfirst($_SESSION['SES_PRENOM'])."</font>";
}

echo " </td>
   </tr>";

//=====================================================================
// type indispo
//=====================================================================

echo "<tr height=20>
      	  <td bgcolor=$mylightcolor><b>Raison</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>";

echo "<select id='type' name='type' onchange='changedType()'>";
echo "<option value=''>Type d'indisponibilité </option>\n";
$query="select distinct TI_CODE, TI_LIBELLE
        from type_indisponibilite";

// cas un agent ne doit pas pouvoir poser de CP / RTT
if (! check_rights($_SESSION['id'], 12) ) {
	if (( $_SESSION['SES_STATUT'] == 'BEN' ) or ($_SESSION['SES_STATUT'] == 'SPV'))
	$query .= " where TI_FLAG = 0 ";
}
$query .= " order by TI_CODE asc";

$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $TI_CODE=$row["TI_CODE"];
      $TI_LIBELLE=$row["TI_LIBELLE"];
      echo "<option value='".$TI_CODE."'>".$TI_CODE." - ".$TI_LIBELLE."</option>\n";
}
echo "</select></td>";
echo "</tr>";

//=====================================================================
// début et fin
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Jour(s) complet(s)</b></td>
      	  <td bgcolor=$mylightcolor align=left>
			<input type='checkbox' name='full_day' id='full_day' value='1' checked onclick='changeDisplay();'
			title=\"cochez cette case si l'absence concerne une ou plusieurs journées complètes\"></td>";		
echo "</tr>";

echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Début</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>";
?>
<input class="plain" name="dc1" value="" size="12" onchange="checkDate2(document.demoform.dc1)" onchange="changedType()"><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fStartPop(document.demoform.dc1,document.demoform.dc2);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php


echo "<select id='debut' name='debut' title=\"heure de début de l'absence\" onchange=\"EvtCalcDuree(document.demoform.duree);\" hidden>";
for ( $i=0; $i <= 24; $i++ ) {
    $check = $i.":00";
    if (  $i == 8 ) $selected="selected";
    else $selected="";
    echo "<option value=".$i.":00 ".$selected.">".$i.":00</option>\n";
}
echo "</select>";
echo "</tr>";


echo "<tr>
      	  <td bgcolor=$mylightcolor><b>Fin</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor align=left>";
?>
<input class="plain" name="dc2" value="" size="12" onchange="changedDate2();checkDate2(document.demoform.dc2)"><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.demoform.dc1,document.demoform.dc2);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
<?php

echo "<select id='fin' name='fin' title=\"heure de fin de l'absence\" onchange=\"EvtCalcDuree(document.demoform.duree);\" hidden>";
for ( $i=0; $i <= 24; $i++ ) {
    $check = $i.":00";
    if (  $i == 19 ) $selected="selected";
    else $selected="";
    echo "<option value=".$i.":00 ".$selected.">".$i.":00</option>\n";
}
echo "</select>";
echo "<input type='hidden' name='duree' id='duree' value='999999'>";
echo "</tr>";


//=====================================================================
// commentaire facultatif
//=====================================================================

echo "<tr height=30>
      	  <td bgcolor=$mylightcolor><b>Commentaire </b></td>
      	  <td bgcolor=$mylightcolor align=left>";
   echo "<input type='text' name='comment' size='20' value=''>";
   echo " </tr>";


echo "</table>";
echo "</td></tr></table>"; 

//=====================================================================
// boutons enregistrement
//=====================================================================

echo "<p><input id='save' type='submit' value='enregistrer' disabled>";
echo "</form></div>";

?>

<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
</BODY>
</HTML>
