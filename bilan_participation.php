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
writehead();
$mysection=$_SESSION['SES_SECTION'];
get_session_parameters();
?>

<script>
function redirect(year,month,section) {
url = "bilan_participation.php?month="+month+"&year="+year+"&section="+section;
self.location.href = url;
}
</script>
<?php

if (isset ($_GET["month"])) $month=intval($_GET["month"]);
else {
	$month=date("n");
	if ( $gardes == 1 ) {
		// afficher le mois suivant
		if ( $month == 12 )  {
      		$month = 1;
      		$year= $year +1;
		}
		else  $month = $month +1 ;
	}
}
if (isset ( $_GET["year"])) $year=intval($_GET["year"]);
else $year=date("Y");

if (isset ($_GET["section"])) {
   $_SESSION['sectionchoice'] = intval($_GET["section"]);
   $section=intval($_GET["section"]);
}
else if ( isset($_SESSION['sectionchoice']) ) {
   $section=$_SESSION['sectionchoice'];
}
else $section=$mysection;

include_once ("config.php");
   
$mycolor=$textcolor;
//nb de jours du mois
$d=nbjoursdumois($month, $year);
$casej=0;$casen=0;

//=====================================================================
// title
//=====================================================================

echo "<body>";
echo "<div align=center><font size=5><b>Participations aux événements</b></font><p>";

//=====================================================================
// choix date
//=====================================================================
$year0=$year -1;
$year1=$year +1;
echo "<form>";
echo "<table><tr><td>Période ";
echo " <select id='year' name='year' onchange='redirect(document.getElementById(\"year\").value,".$month.",".$section.")'>";
echo "<option value='$year0'>".$year0."</option>";
echo "<option value='$year' selected >".$year."</option>";
echo "<option value='$year1' >".$year1."</option>";
echo  "</select>";
echo " <select id='month' name='month' onchange='redirect(".$year.",document.getElementById(\"month\").value,".$section.")'>";
$m=1;
while ($m <=12) {
      $monmois = $mois[$m - 1 ];
      if ( $m == $month ) echo  "<option value='$m' selected >".$monmois."</option>\n";
      else echo  "<option value= $m >".$monmois."</option>\n";
      $m=$m+1;
}
if ( $month == 100 ) echo  "<option value='100' selected >bilan annuel</option>\n";
      else echo  "<option value='100'>bilan annuel</option>\n";
echo  "</select>";

//=====================================================================
// choix section
//=====================================================================
if ($nbsections <> 1 ) {
	  echo " <select id='section' name='section' 
	  onchange='redirect(".$year.",".$month.",document.getElementById(\"section\").value)'>";
	  display_children2(-1, 0, $section, $nbmaxlevels, $sectionorder);
	  echo "</select></td>";
}

echo "</tr></table></form>"; 	



// =====================================================================
// histogram
// =====================================================================   
echo "<img src=bilan_participation_pic.php?year=$year&month=$month&section=$section><p>" ;


?>
