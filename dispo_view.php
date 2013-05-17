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
check_all(38);
$mysection=$_SESSION['SES_SECTION'];
$id=$_SESSION['id'];

writehead();
?>

<script>
function myalert(year,month,day,section,poste) {
	self.location.href = "alerte_create.php?section="+section+"&poste="+poste+"&dispo="+year+"-"+month+"-"+day;
}
function redirect(url) {
	 self.location.href = url;
}
function fillmenu(frm, menu1,menu2,menu3,menu4,menu5) {
year=frm.menu1.options[frm.menu1.selectedIndex].value;
month=frm.menu2.options[frm.menu2.selectedIndex].value;
day=frm.menu3.options[frm.menu3.selectedIndex].value;
<?php
if ($nbsections <> 1 ) 
	echo "section=frm.menu4.options[frm.menu4.selectedIndex].value;";
else 
	echo "section=0;";
?>
poste=frm.menu5.options[frm.menu5.selectedIndex].value;
url = "dispo_view.php?month="+month+"&year="+year+"&day="+day+"&poste="+poste+"&section="+section+"&print=NO";
self.location.href = url;
}
</script>
<?php

if (isset ($_GET["month"])) $month=intval($_GET["month"]);
else $month=date("m");
if (isset ( $_GET["year"])) $year=intval($_GET["year"]);
else $year=date("Y");
if (isset ($_GET["day"])) $day=intval($_GET["day"]);
else $day=date("d");
if (isset ($_GET["poste"])) $poste=intval($_GET["poste"]);
else $poste=0;
if (isset ($_GET["section"])) {
 	$section=intval($_GET["section"]);
 	$_SESSION['sectionchoice'] = $_GET["section"];
}
else if ( isset($_SESSION['sectionchoice']) ) {
   $section=$_SESSION['sectionchoice'];
}
else $section=$mysection;

include_once ("config.php");
   
$mycolor=$textcolor;
//nb de jours du mois
$d=nbjoursdumois($month, $year);
$moislettres=moislettres($month);
$casej=0;$casen=0;

//=====================================================================
// title
//=====================================================================


echo "<body>";
echo "<div align=center><font size=5><b>Personnel disponible le <br>".date_fran($month, $day, $year)." $moislettres $year </b></font><p>";



//=====================================================================
// choix date
//=====================================================================

$yearnext=date("Y") +1;
$yearcurrent=date("Y");
$yearprevious = date("Y") - 1;

echo "<form>";

if ($nbsections <> 1 ) $number4='this.form.menu4';
else $number4=0;

echo "<table>
	<tr><td>année </td><td>
		<select id='menu1' name='menu1' 
		onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3,$number4,this.form.menu5)'>";
	if ($year > $yearprevious) echo "<option value='$yearprevious'>".$yearprevious."</option>";
	else echo "<option value='$yearprevious' selected>".$yearprevious."</option>";
	if ($year <> $yearcurrent) echo "<option value='$yearcurrent' >".$yearcurrent."</option>";
	else echo "<option value='$yearcurrent' selected>".$yearcurrent."</option>";
	if ($year < $yearnext)  echo "<option value='$yearnext' >".$yearnext."</option>";
	else echo "<option value='$yearnext' selected>".$yearnext."</option>";
	echo  "</select>";

	echo " mois <select id='menu2' name='menu2' 
	onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3,$number4,this.form.menu5)'>";
	$m=1;
	while ($m <=12) {
      $monmois = $mois[$m - 1 ];
      if ( $m < 10 ) $M = "0".$m ; 
      else $M=$m;
      if ( $M == $month ) echo  "<option value='$M' selected >".$monmois."</option>\n";
      else echo  "<option value= '$M' >".$monmois."</option>\n";
      $m=$m+1;
	}
	echo  "</select>";

	echo " jour <select id='menu3' name='menu3' 
	onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3,$number4,this.form.menu5)'>";
	$d=1;
	while ($d <= 31) {
	  if ( $d < 10 ) $D = "0".$d ; 
      else $D=$d;
      if ( $D == $day ) echo  "<option value='$D' selected >".$d."</option>\n";
      else echo  "<option value= '$D' >".$d."</option>\n";
      $d=$d+1;
	}
	echo  "</select></td></tr>";
	echo "<tr><td>";
	
	echo "Filtre </td><td><select id='menu5' name='menu5' 
	onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3,$number4,this.form.menu5)'>
	  <option value='0'>toutes qualifications</option>";
		$query2="select p.PS_ID, p.DESCRIPTION, e.EQ_NOM, e.EQ_ID from poste p, equipe e 
		   where p.EQ_ID=e.EQ_ID
		   order by p.EQ_ID, p.PS_ID";
		$result2=mysql_query($query2);
		$prevEQ_ID=0;
		while ($row=@mysql_fetch_array($result2)) {
      		$PS_ID=$row["PS_ID"];
      		$EQ_ID=$row["EQ_ID"];
      		$EQ_NOM=$row["EQ_NOM"];
      		if ( $prevEQ_ID <> $EQ_ID ) echo "<OPTGROUP LABEL='".$EQ_NOM."'>";
      		$prevEQ_ID=$EQ_ID;
      		$DESCRIPTION=$row["DESCRIPTION"];
      		echo "<option value='".$PS_ID."'";
      		if ($PS_ID == $poste ) echo " selected ";
      		echo ">".$DESCRIPTION."</option>\n";
		}
	echo "</select></td></tr><tr><td>";
	
   if ($nbsections <> 1 ) {
 	  echo "Choix section</td><td><select id='menu4' name='menu4' 
	   onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3,$number4,this.form.menu5)'>";
	  display_children2(-1, 0, $section, $nbmaxlevels);
	  echo "</select>";
   }
   else echo "</td><td>";
	
echo "</td></tr></table></form>"; 	


// ===============================================
// personnel disponible
// ===============================================

echo "<table >";
echo "<tr>
<td class='FondMenu'>";
echo "<table border=0 cellspacing=0 cellpadding=0>
      <tr class=TabHeader>";
echo "<td width=220>24h</td>";
echo "<td width=1></td>";
echo "<td width=220>Jour seul</td>";
echo "<td width=1></td>";
echo "<td width=220>Nuit seule</td>";
echo "</tr>";

echo "<tr>";
echo "<td width=220 bgcolor=$mylightcolor>";
personnel_dispo($year, $month, $day, 'A', $poste, $section);
echo "</td>";
echo "<td bgcolor=$mydarkcolor width=1></td>";
echo "<td width=220 bgcolor=#FFFFFF>";
personnel_dispo($year, $month, $day, 'J', $poste, $section);
echo "</td>";
echo "<td bgcolor=$mydarkcolor width=1></td>";
echo "<td width=220 bgcolor=$mylightcolor>";
personnel_dispo($year, $month, $day, 'N', $poste, $section);
echo "</td>";
echo "</tr>";

echo "</table>";
echo "</td></tr></table>";

if ( check_rights ($id,43,"$section"))
	echo "<p><input type=button value=\"alerter\" 
		onclick=\"myalert('".$year."','".$month."','".$day."','".$section."','".$poste."');\">";

?>
