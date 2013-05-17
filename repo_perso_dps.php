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
else $section=intval($_SESSION['SES_SECTION']);
if (isset($_GET["year"])) $year=$_GET["year"];
else $year=date("Y");

$labels = array();
$data = array();
$parent = get_section_parent("$section");
$nbsub = get_subsections_nb("$section");

$query="select count(1) from evenement, evenement_horaire 
		where Year(EH_DATE_DEBUT) = ".$year."
		and evenement.E_CODE = evenement_horaire.E_CODE
		and EH_ID=1
		and S_ID = ".$section."
		and E_CANCELED = 0
		and TE_CODE='DPS'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$nbev = $row[0];

if ( $nbsub > 0 ) {
    // cas département ou plus gros
	$query="select S_ID, S_CODE
		from section 
		where S_PARENT='".$section."'
        order by S_CODE asc";
    // autres
	$query2=" select count(*) as NB 
		 from evenement e, pompier p , evenement_participation ep, evenement_horaire eh
		 where Year(eh.EH_DATE_DEBUT) = ".$year."
		 and p.P_SECTION not in (".get_family("$section").")
		 and e.S_ID = ".$section."
		 and ep.P_ID = p.P_ID
		 and ep.E_CODE = eh.E_CODE
		 and ep.EH_ID = eh.EH_ID
		 and e.E_CANCELED = 0
		 and e.E_CODE = ep.E_CODE
		 and e.TE_CODE='DPS'
		 order by NB desc";
}
else { 
    // cas d'une antenne locale
	$query="select S_ID, S_CODE
		from section 
		where S_PARENT='".$parent."'
        order by S_CODE asc";
    // autres
    $query2=" select count(*) as NB 
		 from evenement e, pompier p , evenement_participation ep, evenement_horaire eh
		 where Year(eh.EH_DATE_DEBUT) = ".$year."
		 and p.P_SECTION not in (select S_ID from section where S_PARENT='".$parent."')
		 and e.S_ID = ".$section."
		 and ep.P_ID = p.P_ID
		 and e.E_CANCELED = 0
		 and ep.E_CODE = eh.E_CODE
		 and ep.EH_ID = eh.EH_ID
		 and e.E_CODE = ep.E_CODE
		 and e.TE_CODE='DPS'
		 order by NB desc";
} 

$result = mysql_query($query);

while ($row = mysql_fetch_row($result)) {
    if ( $nbsub > 0 )
    $query3=" select count(*) as NB 
		 from evenement e, pompier p , evenement_participation ep, evenement_horaire eh
		 where Year(eh.EH_DATE_DEBUT) = ".$year."
		 and p.P_SECTION in  (".get_family("$row[0]").")
		 and ep.P_ID = p.P_ID
		 and e.E_CANCELED = 0
		 and ep.E_CODE = eh.E_CODE
		 and ep.EH_ID = eh.EH_ID
		 and e.S_ID = ".$section."
		 and e.E_CODE = ep.E_CODE
		 and e.TE_CODE='DPS'
		 order by NB desc";
    else
 	$query3=" select count(*) as NB 
		 from evenement e, pompier p , evenement_participation ep, evenement_horaire eh
		 where Year(eh.EH_DATE_DEBUT) = ".$year."
		 and p.P_SECTION = ".$row[0]."
		 and ep.P_ID = p.P_ID
		 and ep.E_CODE = eh.E_CODE
		 and ep.EH_ID = eh.EH_ID
		 and e.S_ID = ".$section."
		 and e.E_CODE = ep.E_CODE
		 and e.E_CANCELED = 0
		 and e.TE_CODE='DPS'
		 order by NB desc";
 	$result3 = mysql_query($query3);
    $row3 = mysql_fetch_row($result3);
    if ( $row3[0] > 0 ) {
     	    $labels[] = $row[1];
 			$data[] = $row3[0];
 	}
}		 


$result2 = mysql_query($query2);
$row2 = mysql_fetch_row($result2);
if ( $row2[0] > 0 ) {
	$labels[] = 'autres';
	$data[] = $row2[0];
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
$textBoxObj = $c->addTitle("Origine des participants aux DPS", "arialbd.ttf", 15,0xffffff);
$textBoxObj->setBackground(0x000099, 0x000000, glassEffect());

# Add a legend box at (70, 32) (top of the plotarea) with 9pts Arial Bold font
#$legendObj = $c->addLegend(426, 30, false, "arialbd.ttf", 9);
#$legendObj->setBackground(Transparent);

# Set the pie data and the pie labels
$c->setLabelLayout(SideLayout);

# Set the border color of the sector the same color as the fill color. Set the line
# color of the join line to black (0x0)
$c->setLineColor(SameAsMainColor, 0x000000);

# Set the pie data and the pie labels
$c->setData($data, $labels);

# Draw the pie in 3D with variable 3D depths
$c->set3D(20);

# Set the start angle to 225 degrees may improve layout when the depths of the sector
# are sorted in descending order, because it ensures the tallest sector is at the
# back.
$c->setStartAngle(135);

# Add a logo to the chart written in CDML as the bottom title aligned to the bottom
# right
$c->addTitle2(BottomRight,
    "<*font=arialbd.ttf,size=9*>".
	"$nbev DPS organisé(s) par ".get_section_name("$section")." en $year");


# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));

	
?>
