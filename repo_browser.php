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
  
require_once ("./ChartDirector/lib/phpchartdir.php");
include_once ("./config.php");
check_all(15);

if ( isset ($_GET["section"])) $section=intval($_GET["section"]);
else $section=$_SESSION['SES_SECTION'];
if ( isset ($_GET["subsections"])) $subsections=intval($_GET["subsections"]);
else $subsections=1;
$mode=$_GET["mode"];

if ( $subsections == 1 ) {
  	 $list = get_family("$section");
}
else {
  	$list = $section;
}

$icons = array();
$labels = array();
$data = array();
$d1 = array();
$depths = array();

$query="SELECT count(*) 
	from audit, pompier
	where pompier.P_ID = audit.P_ID
	and P_SECTION in (".$list.")";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$nb = $row[0] ;

if ( $mode == 'browser'){
	$title="Navigateurs";
	$query="SELECT SUBSTRING_INDEX( A_BROWSER, ' ', 1), count(*) 
	from audit, pompier
	where pompier.P_ID = audit.P_ID
	and P_SECTION in (".$list.")
	group by SUBSTRING_INDEX( A_BROWSER, ' ', 1)
	having count(*) > $nb / 50
	order by A_BROWSER desc
	";
}
else {
	$title="Systèmes d'exploitation";
	$query="SELECT SUBSTRING_INDEX( A_OS, ' ', 1), count(*) 
	from audit, pompier
	where pompier.P_ID = audit.P_ID
	and P_SECTION in (".$list.")
	group by SUBSTRING_INDEX( A_OS, ' ', 1)
	having count(*) > $nb / 40
	order by A_OS desc
	";
}	
$result = mysql_query($query);
$d1=50;
while ($row = mysql_fetch_row($result)) {
 	$labels[] = $row[0];
 	$data[] = $row[1];
 	//$d1 = $d1 -3;
 	//if ( $d1 < 10 ) $d1=5;
 	$depths[] = $d1;
}


# Create a PieChart object of size 400 x 300 pixels
$c = new PieChart(600, 400);
$c->setRoundedFrame();

# Use the semi-transparent palette for this chart
$c->setColors($transparentPalette);

# Set the background to metallic light blue (CCFFFF), with a black border and 1 pixel
# 3D border effect,
$c->setBackground(0xB7D8FB, 0x000099,1);

#Set directory for loading images to current script directory
#Need when running under Microsoft IIS
$c->setSearchPath(dirname(__FILE__));

# Set donut center at (200, 175), and outer/inner radii as 100/50 pixels
$c->setPieSize(300, 210, 110);

# Add a title box using 15 pts Times Bold Italic font and metallic blue (8888FF)
# background color

$textBoxObj = $c->addTitle("Répartition des principaux ".$title, "arialbd.ttf", 15,0xffffff);
$textBoxObj->setBackground(0x000099, 0x000000, glassEffect());

# Set the pie data and the pie labels
$c->setData($data, $labels);

# Draw the pie in 3D with variable 3D depths
$c->set3D2($depths);

# Set the label box background color the same as the sector color, with reduced-glare
# glass effect and rounded corners
$t = $c->setLabelStyle();
$t->setBackground(SameAsMainColor, Transparent, glassEffect(ReducedGlare));
$t->setRoundedCorners();

# Set the sector label format. The label consists of two lines. The first line is the
# sector name in Times Bold Italic font and is underlined. The second line shows the
# data value and percentage.
$c->setLabelFormat(
    "<*block,halign=left*><*font=arialbd.ttf,size=10,underline=1*>{label}<*/font*>".
    "<*br*> {value} ({percent}%)");



# Set the start angle to 280 degrees may improve layout when the depths of the sector
# are sorted in descending order, because it ensures the tallest sector is at the
# back.
$c->setStartAngle(280);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));

	
?>
