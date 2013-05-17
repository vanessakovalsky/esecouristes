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
?>
<script>
function redirect(url) {
	 self.location.href = url;
}
function fillmenu(frm, menu1,menu2,menu3) {
year=frm.menu1.options[frm.menu1.selectedIndex].value;
month=frm.menu2.options[frm.menu2.selectedIndex].value;
day=frm.menu3.options[frm.menu3.selectedIndex].value;
url = "dispo_view.php?month="+month+"&year="+year+"&day="+day+"&print=NO";
self.location.href = url;
}
</script>
<?php

if (isset ($_GET["month"])) $month=$_GET["month"];
else $month=date("n");
if (isset ( $_GET["year"])) $year=$_GET["year"];
else $year=date("Y");
if (isset ($_GET["day"])) $day=$_GET["day"];
else $day=date("d");
if (isset ($_GET["print"])) $print=$_GET["print"];
else $print="NO";

include_once ("config.php");
   
$mycolor=$textcolor;
//nb de jours du mois
$d=nbjoursdumois($month, $year);
$moislettres=moislettres($month);
$casej=0;$casen=0;

//=====================================================================
// choix date
//=====================================================================
if ( $print == "YES" ) {
   echo "<body onload='javascript:window.print()'>";
}
else {
   echo "<body>";
}
echo "<div align=center><font size=5><b>Garde du ".date_fran($month, $day, $year)." $moislettres $year</b></font><p>";
if ( $print == "NO" ) {
	$year0=$year -1;
	$year1=$year +1;
	echo "<form>";
	echo "<table><tr><td>année 
		<select id='menu1' name='menu1' onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3)'>";
	echo "<option value='$year0'>".$year0."</option>";
	echo "<option value='$year' selected >".$year."</option>";
	echo "<option value='$year1' >".$year1."</option>";
	echo  "</select>";

	echo " mois <select id='menu2' name='menu2' onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3)'>";
	$m=1;
	while ($m <=12) {
      $monmois = $mois[$m - 1 ];
      if ( $m == $month ) echo  "<option value='$m' selected >".$monmois."</option>\n";
      else echo  "<option value= $m >".$monmois."</option>\n";
      $m=$m+1;
	}
	echo  "</select>";

	echo " jour <select id='menu3' name='menu3' onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3)'>";
	$d=1;
	while ($d <= 31) {
      if ( $d == $day ) echo  "<option value='$d' selected >".$d."</option>\n";
      else echo  "<option value= $d >".$d."</option>\n";
      $d=$d+1;
	}
	echo  "</select></td>";
   	echo "<td><a href=garde_jour.php?month=$month&year=$year&day=$day&print=YES target=_blank><img src=images/printer.gif border=0 alt='imprimer la feuille de garde'></a></td>";
	echo "</tr></table></form>"; 	
}


// ===============================================
// personnel disponible
// ===============================================
echo "<p><font size=4><b>Personnel de réserve</b></font><hr><br>";

echo "<p><table border=0>";
echo "<tr><td width=500 align=left valign=top><font size=4>24h</font><p>";
personnel_dispo($year, $month, $day, 'A');
echo "</td></tr>";
echo "<tr><td width=250 align=left valign=top><font size=4>Jour</font><p>";
personnel_dispo($year, $month, $day, 'J');
echo "</td>";
echo "<td width=250 align=left valign=top><font size=4>Nuit</font><p>";
personnel_dispo($year, $month, $day, 'N');
echo "</td>";
echo "</tr></table>";

?>
