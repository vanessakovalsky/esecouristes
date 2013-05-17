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
  
include_once ("./config.php");
check_all(15);
writehead();

if ( isset ($_GET["section"])) $section=intval($_GET["section"]);
else $section=$_SESSION['SES_SECTION'];
if ( isset ($_GET["subsections"])) $subsections=intval($_GET["subsections"]);
else $subsections=1;
if (isset($_GET["type"])) $type=$_GET["type"];
else $type='ALL';
if (isset($_GET["year"])) $year=$_GET["year"];
else $year=date("Y");
if (isset($_GET["report"])) $report=$_GET["report"];
else $report=0;
if (isset($_GET["equipe"])) $equipe=$_GET["equipe"];
else $equipe=1;

?>

<script language="JavaScript">
function orderfilter1(section,sub,type,year,report, equipe){
	 self.location.href="repo_events.php?section="+section+"&subsections="+sub+"&year="+year+"&type="+type+"&report="+report+"&equipe="+equipe;
	 return true
}
function orderfilter2(section,sub,type,year,report){
 	 if (sub.checked) s = 1;
 	 else s = 0;
	 self.location.href="repo_events.php?section="+section+"&subsections="+s+"&year="+year+"&type="+type+"&report="+report;
	 return true
}
</script>
<STYLE type="text/css">
.categorie{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
.type{color:<?php echo $mydarkcolor; ?>; background-color:white; font-size:9pt;}
</STYLE>

<?php
echo "</head>";

echo "<body>";
echo "<div align=center><font size=5><b>Graphiques $cisname</b></font><p>";

echo "<p><table width=450 cellspacing=0 border=0 >";

// ===============================
// choix type de report
// ===============================

echo "<tr><td>statistique</td>
	    <td>
		<select id='report' name='report' 
		onchange=\"orderfilter1('".$section."','".$subsections."','".$type."','".$year."',document.getElementById('report').value ,'".$equipe."')\">";
	
	if (isset($application_title_specific)) $application_title=$application_title_specific;
	
	echo "<OPTGROUP class='categorie' label='Utilisation $application_title'>";
	if (($report  == 0 ) and ($nbsections <> 1))  $selected='selected'; else $selected ='';
	echo "<option value='0' $selected>Connexions par section</option>";	
	
	if ($report  == 23 )  $selected='selected'; else $selected ='';
	echo "<option value='23' $selected>Systèmes d'exploitation utilisés</option>";
	
	if ($report  == 24 )  $selected='selected'; else $selected ='';
	echo "<option value='24' $selected>Navigateurs utilisés</option>";

	echo "<OPTGROUP class='categorie' label='Evénements'>";
	if ($report  == 1 ) $selected='selected'; else $selected ='';
	echo "<option value='1' $selected>Evénements par mois</option>";
	
	if ($report  == 2 )  $selected='selected'; else $selected ='';
	echo "<option value='2' $selected>Evénements par type</option>";
	
	if ($report  == 11 )  $selected='selected'; else $selected ='';
	echo "<option value='11' $selected>Evénements par section</option>";

	if ($report  == 4 )  $selected='selected'; else $selected ='';
	echo "<option value='4' $selected>Evenements annulés</option>";
	
	echo "<OPTGROUP class='categorie' label='DPS'>";	
	if ($report  == 3 )  $selected='selected'; else $selected ='';
	echo "<option value='3' $selected>Dispositifs de secours</option>";
	
	if ($report  == 21 )  $selected='selected'; else $selected ='';
	echo "<option value='21' $selected>DPS par catégorie</option>";
	
	if ($report  == 22 ) $selected='selected'; else $selected ='';
	echo "<option value='22' $selected>DPS par catégorie par mois</option>";

	echo "<OPTGROUP class='categorie' label='Formations'>";		
	if ($report  == 14 )  $selected='selected'; else $selected ='';
	echo "<option value='14' $selected>Formations par mois</option>";
	
	if ($report  == 15 )  $selected='selected'; else $selected ='';
	echo "<option value='15' $selected>Formations initiales/diplômes par mois</option>";
	
	if ($report  == 16 )  $selected='selected'; else $selected ='';
	echo "<option value='16' $selected>Formations complémentaires par mois</option>";
	
	if ($report  == 17 )  $selected='selected'; else $selected ='';
	echo "<option value='17' $selected>Formations continues par mois</option>";
	
	if ($report  == 18 )  $selected='selected'; else $selected ='';
	echo "<option value='18' $selected>Formations / stagiaires / formateurs</option>";
	
	if ($report  == 12 )  $selected='selected'; else $selected ='';
	echo "<option value='12' $selected>Gardes au centre de secours</option>";
	
	echo "<OPTGROUP class='categorie' label='Divers'>";	
	if ($report  == 13 )  $selected='selected'; else $selected ='';
	echo "<option value='13' $selected>Maraudes</option>";
	
	if ($report  == 5 )  $selected='selected'; else $selected ='';
	echo "<option value='5' $selected>Chiffre d'affaire par mois</option>";

	if ($report  == 25 )  $selected='selected'; else $selected ='';
	echo "<option value='25' $selected>Ages des véhicules</option>";


	echo "<OPTGROUP class='categorie' label='Personnel'>";	
	if ($report  == 6 )  $selected='selected'; else $selected ='';
	echo "<option value='6' $selected>Secouristes PSE1 / PSE2</option>";
	
	if ($report  == 7 )  $selected='selected'; else $selected ='';
	echo "<option value='7' $selected>Compétences du personnel</option>";
	
	if ($report  == 8 )  $selected='selected'; else $selected ='';
	echo "<option value='8' $selected>Pyramide des âges</option>";
	
	if ($report  == 9 )  $selected='selected'; else $selected ='';
	echo "<option value='9' $selected>Origine des participants aux DPS</option>";
	
	if ($report  == 19 )  $selected='selected'; else $selected ='';
	echo "<option value='19' $selected>Personnel par catégorie</option>";
	
	if ($report  == 20 )  $selected='selected'; else $selected ='';
	echo "<option value='20' $selected>Flux de personnel (par mois)</option>";
	
	if ($report  == 26 )  $selected='selected'; else $selected ='';
	echo "<option value='26' $selected>Flux de personnel (annuel)</option>";
	
	if ($report  == 27 )  $selected='selected'; else $selected ='';
	echo "<option value='27' $selected>Personnel externe ajouté(par mois)</option>";
	
	if ($report  == 28 )  $selected='selected'; else $selected ='';
	echo "<option value='28' $selected>Personnel externe ajouté (annuel)</option>";
	
	$queryz="select count(*) as NB from evenement where TE_CODE='GRIPA'";
	$resultz=mysql_query($queryz);
    $rowz=@mysql_fetch_array($resultz);
    if ( $rowz["NB"] > 0 ) {
		if ($report  == 10 )  $selected='selected'; else $selected ='';
		echo "<option value='10' $selected>Activité Grippe A par département</option>";
	}
	echo  "</select></td>";

echo "</tr>";		

// ===============================
// choix section
// ===============================
if (($nbsections <> 1 ) and ($report <> 10)) {
echo "<tr>";
	echo "<td>section</td>
	      <td><select id='section' name='section' 
		onchange=\"orderfilter1(document.getElementById('section').value,'".$subsections."','".$type."','".$year."','".$report."','".$equipe."')\">";
	  display_children2(-1, 0, $section, $nbmaxlevels, $defaultsectionorder);
	echo "</select> ";
	echo "</td>";
	if ( get_children($section) <> '' ) {
	  if ( $report <> 0 and $report <> 9 and $report <> 10 and $report <> 11) {
	  		if ($subsections == 1 ) $checked='checked';
	  		else $checked='';
	  		echo "</tr>
	    		<tr><td></td><td><input type='checkbox' name='sub' $checked 
	   		onClick=\"orderfilter2(document.getElementById('section').value, this,'".$type."','".$year."','".$report."')\"/>
	   		<font size=1>inclure les sous sections</td>";
	   		echo "</tr>";
		}
	}
}
// ===============================
// choix type evenement
// ===============================
if (( $report == 1 ) || ( $report == 4 ) || ( $report == 11 )) {
echo "<tr>";
echo "<td>événement</td>
	      <td ><select id='type' name='type' 
		onchange=\"orderfilter1('".$section."','".$subsections."',document.getElementById('type').value,'".$year."','".$report."','".$equipe."')\">";
echo "<option value='ALL' selected>Toutes activités </option>";
$query="select distinct te.CEV_CODE, ce.CEV_DESCRIPTION, te.TE_CODE, te.TE_LIBELLE
        from type_evenement te, categorie_evenement ce
		where te.CEV_CODE=ce.CEV_CODE
		order by te.CEV_CODE desc, te.TE_CODE asc";
$result=mysql_query($query);
$prevCat='';
while ($row=@mysql_fetch_array($result)) {
      $TE_CODE=$row["TE_CODE"];
      $TE_LIBELLE=$row["TE_LIBELLE"];
      $CEV_DESCRIPTION=$row["CEV_DESCRIPTION"];
      $CEV_CODE=$row["CEV_CODE"];
      if ( $prevCat <> $CEV_CODE ){
       	echo "<optgroup class='categorie' label='".$CEV_DESCRIPTION."'";
       	if ($CEV_CODE == $type ) echo " selected ";
        echo ">".$CEV_DESCRIPTION."</option>\n";
      }
      $prevCat=$CEV_CODE;
      echo "<option class='type' value='".$TE_CODE."' title=\"".$TE_LIBELLE."\"";
      if ($TE_CODE == $type ) echo " selected ";
      echo ">".$TE_LIBELLE."</option>\n";
}
echo "</select></td></tr>";
}

// ===============================
// choix type competence
// ===============================
if ( $report == 7 ) {
echo "<tr>";
echo "<td>Type Compétence</td>
	      <td ><select id='equipe' name='equipe' 
		onchange=\"orderfilter1('".$section."','".$subsections."','".$type."','".$year."','".$report."',document.getElementById('equipe').value)\">";
$query="select distinct EQ_ID, EQ_NOM
        from equipe";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $EQ_ID=$row["EQ_ID"];
      $EQ_NOM=$row["EQ_NOM"];
      if ( $equipe == $EQ_ID ) {
      	   echo "<option value='".$EQ_ID."' selected>".$EQ_NOM."</option>";
      }
      else {
      	   echo "<option value='".$EQ_ID."'>".$EQ_NOM."</option>";
      }
}
echo "</select></td></tr>";
}

// ===============================
// choix année
// ===============================
if (($report <> 0 ) and ($report <> 6 ) and ($report <> 7 ) and ($report <> 8 ) 
and ($report <> 10 ) and ($report <> 19) and ($report <> 23 ) and ($report <> 24 )  and ($report <> 25 ) and ($report <> 26 )  and ($report <> 28 ))  { 
		$yearnext=date("Y") +1;
		$yearcurrent=date("Y");
		$yearprevious = date("Y") - 1;

		echo "<tr><td>année</td>
	    <td>
		<select id='year' name='year' 
		onchange=\"orderfilter1('".$section."','".$subsections."','".$type."',document.getElementById('year').value,'".$report."','".$equipe."')\">";
		if ($year > $yearprevious) echo "<option value='$yearprevious'>".$yearprevious."</option>";
		else echo "<option value='$yearprevious' selected>".$yearprevious."</option>";
		if ($year <> $yearcurrent) echo "<option value='$yearcurrent' >".$yearcurrent."</option>";
		else echo "<option value='$yearcurrent' selected>".$yearcurrent."</option>";
		if ($year < $yearnext)  echo "<option value='$yearnext' >".$yearnext."</option>";
		else echo "<option value='$yearnext' selected>".$yearnext."</option>";
		echo  "</select></td>";
	}
echo "</tr></table>";


if ( $report == 0 ) 
echo "<p><img src=repo_audit_pic.php?section=$section&subsections=$subsections><p>" ;

if ( $report == 1 ) 
echo "<p><img src=repo_events_pic.php?year=$year&type=$type&section=$section&subsections=$subsections><p>" ;

if ( $report == 2 ) 
echo "<p><img src=repo_events_type_pic.php?year=$year&type=$type&section=$section&subsections=$subsections><p>" ;

if ( $report == 11 ) 
echo "<p><img src=repo_events_section.php?year=$year&type=$type&section=$section&subsections=$subsections><p>" ;

if ( $report == 3 ) 
echo "<p><img src=repo_dps_pic.php?year=$year&type=DPS&section=$section&subsections=$subsections><p>" ;

if ( $report == 12 ) 
echo "<p><img src=repo_dps_pic.php?year=$year&type=GAR&section=$section&subsections=$subsections><p>" ;

if ( $report == 13 ) 
echo "<p><img src=repo_dps_pic.php?year=$year&type=MAR&section=$section&subsections=$subsections><p>" ;

if ( $report == 4 ) 
echo "<p><img src=repo_canceled_pic.php?year=$year&type=$type&section=$section&subsections=$subsections><p>" ;

if ( $report == 5 ) 
echo "<p><img src=repo_ca_pic.php?year=$year&section=$section&subsections=$subsections><p>" ;

if ( $report == 6 ) 
echo "<p><img src=repo_pse.php?section=$section&subsections=$subsections><p>" ;

if ( $report == 7 ) 
echo "<p><img src=repo_competences.php?section=$section&subsections=$subsections&equipe=$equipe><p>" ;

if ( $report == 8 ) 
echo "<p><img src=repo_age.php?section=$section&subsections=$subsections><p>" ;

if ( $report == 9 ) 
echo "<p><img src=repo_perso_dps.php?section=$section&subsections=$subsections&year=$year><p>" ;

if ( $report == 10 )
echo "<p><img src=repo_grippe.php?section=$section><p>" ;

if ( $report == 14 ) 
echo "<p><img src=repo_formations_pic.php?year=$year&type=$type&section=$section&subsections=$subsections><p>" ;

if ( $report == 15 ) 
echo "<p><img src=repo_formations_pic.php?year=$year&type=$type&section=$section&subsections=$subsections&tf=I><p>" ;

if ( $report == 16 ) 
echo "<p><img src=repo_formations_pic.php?year=$year&type=$type&section=$section&subsections=$subsections&tf=C><p>" ;

if ( $report == 17 ) 
echo "<p><img src=repo_formations_pic.php?year=$year&type=$type&section=$section&subsections=$subsections&tf=R><p>" ;

if ( $report == 18 ) 
echo "<p><img src=repo_personnel_formation_pic.php?year=$year&type=FOR&section=$section&subsections=$subsections><p>" ;

if ( $report == 19 ) 
echo "<p><img src=repo_type_members.php?section=$section&subsections=$subsections><p>" ;

if ( $report == 20 ) 
echo "<p><img src=repo_flux_members.php?year=$year&section=$section&subsections=$subsections&period=month><p>" ;

if ( $report == 21 ) 
echo "<p><img src=repo_dps_type.php?year=$year&section=$section&subsections=$subsections><p>" ;

if ( $report == 22 ) 
echo "<p><img src=repo_dps_type_month.php?year=$year&section=$section&subsections=$subsections><p>" ;

if ( $report == 23 ) 
echo "<p><img src=repo_browser.php?&section=$section&subsections=$subsections&mode=os><p>" ;

if ( $report == 24 ) 
echo "<p><img src=repo_browser.php?&section=$section&subsections=$subsections&mode=browser><p>" ;

if ( $report == 25 ) 
echo "<p><img src=repo_age_vehicules.php?&section=$section&subsections=$subsections><p>" ;

if ( $report == 26 ) 
echo "<p><img src=repo_flux_members.php?section=$section&subsections=$subsections&period=year><p>" ;

if ( $report == 27 ) 
echo "<p><img src=repo_flux_members.php?year=$year&section=$section&subsections=$subsections&period=month&category=ext><p>" ;

if ( $report == 28 ) 
echo "<p><img src=repo_flux_members.php?section=$section&subsections=$subsections&period=year&category=ext><p>" ;

if ( $subsections == 1 ) $list = get_family("$section");
else $list = $section;

$positions="";
$query="select VP_ID from vehicule_position where VP_OPERATIONNEL >=0";
$result = mysql_query($query);
while ($row = @mysql_fetch_array($result)) {
 	$VP_ID=$row["VP_ID"];
 	$positions .= "'".$VP_ID."',";
}
$positions .='NULL';

$YEAR=date('Y');

# The age groups
$lower = array (0,5,10,15,20);
$upper = array (4,9,14,19,100);
$nb_tranches=count($lower); 

for ($i = 0; $i < $nb_tranches; $i++) {
	$labels[$i] = $lower[$i]." - ".$upper[$i];

	$query=" select count(*) as NB 
		 from vehicule 
		 where VP_ID in (".$positions.")
		 and (".$YEAR." >=".$lower[$i]." + V_ANNEE)
		 and (".$YEAR."  <=".$upper[$i]."+ V_ANNEE)
         and S_ID in (".$list.")";
         
	$result = mysql_query($query);
	$row = @mysql_fetch_array($result);
	$data[$i] = $row[0];

}

?>
