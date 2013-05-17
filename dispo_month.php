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
writehead();
$mysection=$_SESSION['SES_SECTION'];
?>

<script>
function redirect(url) {
	 self.location.href = url;
}
<?php

if ( $nbsections <> 1 ) 
echo "
function fillmenu(frm, menu1,menu2,menu3,menu4) { 
year=frm.menu1.options[frm.menu1.selectedIndex].value;
month=frm.menu2.options[frm.menu2.selectedIndex].value;
type=frm.menu3.options[frm.menu3.selectedIndex].value;
section=frm.menu4.options[frm.menu4.selectedIndex].value;
url = 'dispo_month.php?month='+month+'&year='+year+'&type='+type+'&section='+section;
self.location.href = url;
}
";
else
echo "
function fillmenu(frm, menu1,menu2,menu3) { 
year=frm.menu1.options[frm.menu1.selectedIndex].value;
month=frm.menu2.options[frm.menu2.selectedIndex].value;
type=frm.menu3.options[frm.menu3.selectedIndex].value;
url = 'dispo_month.php?month='+month+'&year='+year+'&type='+type+'&section=0';
self.location.href = url;
}
";

echo "</script>";

if (isset ($_GET["month"])) $month=mysql_real_escape_string($_GET["month"]);
else $month=date("n");
if (isset ( $_GET["year"])) $year=mysql_real_escape_string($_GET["year"]);
else $year=date("Y");
if (isset ($_GET["type"])) $type=mysql_real_escape_string($_GET["type"]);
else $type="J";

if (isset ($_GET["section"])) {
   $_SESSION['sectionchoice'] = intval($_GET["section"]);
   $section=intval($_GET["section"]);
}
else if ( isset($_SESSION['sectionchoice']) ) {
   $section=$_SESSION['sectionchoice'];
}
else $section=$mysection;


include_once ("config.php");
   
//nb de jours du mois
$d=nbjoursdumois($month, $year);
$moislettres=moislettres($month);
$casej=0;$casen=0;

//=====================================================================
// title
//=====================================================================

echo "<body>";
echo "<div align=center><font size=5><b>Personnel disponible </b></font><p>";

//=====================================================================
// choix date
//=====================================================================

$year0=$year -1;
$year1=$year +1;
echo "<form>";
echo "<table><tr><td>année </td><td>
		<select id='menu1' name='menu1'";
if ( $nbsections <> 1) 
		echo "onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3,this.form.menu4)'>";
else
		echo "onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3)'>";
echo "<option value='$year0'>".$year0."</option>";
echo "<option value='$year' selected >".$year."</option>";
echo "<option value='$year1' >".$year1."</option>";
echo  "</select>";

echo " mois <select id='menu2' name='menu2'"; 
if ( $nbsections <> 1) 
		echo "onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3,this.form.menu4)'>";
else
		echo "onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3)'>";
$m=1;
while ($m <=12) {
    $monmois = $mois[$m - 1 ];
    if ( $m == $month ) echo  "<option value='$m' selected >".$monmois."</option>\n";
    else echo  "<option value= $m >".$monmois."</option>\n";
    $m=$m+1;
}
echo  "</select>";
	
$checkedj='';
$checkedn='';
if ($type == 'J') $checkedj='selected';
else $checkedn='selected';
    
echo " Période <select id='menu3' name='menu3'"; 
if ( $nbsections <> 1) 
		echo "onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3,this.form.menu4)'>";
else
		echo "onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3)'>";
echo  "<option value='J' $checkedj >le jour</option>\n";
echo  "<option value='N' $checkedn >la nuit</option>\n";
echo  "</select></td>";
    
echo  "</td>";
echo "";

//=====================================================================
// choix section
//=====================================================================

if ($nbsections <> 1 ) {
	echo "</tr><tr><td>Section</td><td colspan=3>";
	echo " <select id='menu4' name='menu4' 
	onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3,this.form.menu4)'>";
	display_children2(-1, 0, $section, $nbmaxlevels);
	
	echo "</select></td>";
}

echo "</tr></table></form>"; 	


// =====================================================================
// histogram
// =====================================================================   

echo "<img src=dispo_view_pic.php?year=$year&month=$month&type=$type&section=$section><p>" ;

?>
