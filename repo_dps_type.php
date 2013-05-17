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

if ( isset ($_GET["section"])) $section=$_GET["section"];
else $section=$_SESSION['SES_SECTION'];
if ( isset ($_GET["subsections"])) $subsections=$_GET["subsections"];
else $subsections=1;
if (isset($_GET["year"])) $year=$_GET["year"];
else $year=date("Y");

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

$query=" select tav.TA_SHORT, count(*) as NB 
		 from evenement e, evenement_horaire eh, type_agrement_valeur tav
		 where e.TE_CODE='DPS'
		 and tav.TAV_ID = e.TAV_ID
		 and Year(eh.EH_DATE_DEBUT) = ".$year."
		 and e.E_CODE = eh.E_CODE
		 and eh.EH_ID=1
         and e.S_ID in (".$list.")
         and tav.TA_CODE='D'
         group by tav.TA_SHORT
		 order by NB desc";
$result = mysql_query($query);
$d1=50;
while ($row = mysql_fetch_row($result)) {
 	if ( $row[0] == '-' ) $labels[] = "Non défini";
 	else $labels[] = $row[0];
 	$data[] = $row[1];
 	$d1 = $d1 -5;
 	if ( $d1 < 10 ) $d1=5;
 	$depths[] = $d1;
}

# The data for the pie chart
# $data = array(72, 18, 15, 12);

# The depths for the sectors
# $depths = array(30, 20, 10, 10);

# The labels for the pie chart
# $labels = array("Sunny", "Cloudy", "Rainy", "Snowy");

# The icons for the sectors
# $icons = array("sun.png", "cloud.png", "rain.png", "snowy.png");

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
$textBoxObj = $c->addTitle("DPS par catégorie en $year", "arialbd.ttf", 15,0xffffff);
$textBoxObj->setBackground(0x000099, 0x000000, glassEffect());

# Add a legend box at (70, 32) (top of the plotarea) with 9pts Arial Bold font
#$legendObj = $c->addLegend(426, 30, false, "arialbd.ttf", 9);
#$legendObj->setBackground(Transparent);

# Set the pie data and the pie labels
$c->setLabelLayout(SideLayout);

# Set the label box background color the same as the sector color, with glass effect,
# and with 5 pixels rounded corners
$t = $c->setLabelStyle();
$t->setBackground(SameAsMainColor, Transparent, glassEffect());
$t->setRoundedCorners(5);

# Set the border color of the sector the same color as the fill color. Set the line
# color of the join line to black (0x0)
$c->setLineColor(SameAsMainColor, 0x000000);

# Add icons to the chart as a custom field
$c->addExtraField($icons);
$c->addExtraField($labels);
$c->addExtraField($data);

$c->setData($data, $labels);

# Configure the sector labels using CDML to include the icon images
$c->setLabelFormat(
    "<*block,align=center,valign=absmiddle*><*img={field0}*> <*block*>{field1} - <*block*>{field2} ({percent}%)<*/*>".
    "<*/*>");

# Draw the pie in 3D with variable 3D depths
$c->set3D(20);

# Set the start angle to 225 degrees may improve layout when the depths of the sector
# are sorted in descending order, because it ensures the tallest sector is at the
# back.
$c->setStartAngle(135);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));

	
?>
