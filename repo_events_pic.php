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
  

if ( isset ($_GET["section"])) $section=$_GET["section"];
else $section=$_SESSION['SES_SECTION'];
if ( isset ($_GET["subsections"])) $subsections=$_GET["subsections"];
else $subsections=1;
if (isset($_GET["type"])) $type=$_GET["type"];
else $type='ALL';
if (isset($_GET["year"])) $year=$_GET["year"];
else $year=date("Y");

require_once ("./ChartDirector/lib/phpchartdir.php");
include_once ("./config.php");
check_all(15);

if ( $subsections == 1 ) {
  	 $list = get_family("$section");
}
else {
  	$list = $section;
}

#
# Displays the monthly revenue for the selected year. The selected year should be
# passed in as a query parameter called "year"
#

# Create a XYChart object of size 600 x 400 pixels, with a light grey (eeeeee)
# background, black border, 1 pixel 3D border effect and rounded corners.
$c = new XYChart(600, 420, 0xB7D8FB, 0x000099, 1);
$c->setRoundedFrame();

# Set the plotarea at (60, 60) and of size 520 x 200 pixels. Set background color to
# white (ffffff) and border and grid colors to grey (cccccc)
$c->setPlotArea(60, 60, 520, 230, 0xeeeeee, -1, 0xcccccc, 0xccccccc);

# Add a title to the chart using 15pts Times Bold Italic font, with a light blue
# (ccccff) background and with glass lighting effects.
$textBoxObj = $c->addTitle("Evénements pour l'année $year", "arialbd.ttf", 15,0xffffff);
$textBoxObj->setBackground(0x000099, 0x000000, glassEffect());

# Add a legend box at (70, 32) (top of the plotarea) with 9pts Arial Bold font
$legendObj = $c->addLegend(70, 310, false, "arialbd.ttf", 9);
$legendObj->setBackground(Transparent);

# Add a stacked bar chart layer using the supplied data
$layer = $c->addBarLayer2(Stack);

# Add labels to the top of the bar using 8 pt Arial Bold font.
$layer->setAggregateLabelStyle("arialbd.ttf", 8, 0x000000);


$query1="select TE_CODE, TE_LIBELLE from type_evenement";
if ( $type <> 'ALL' ) $query1 .= " where TE_CODE = '".$type."'";
$result1 = mysql_query($query1);

// event type loop
$i=0; 
while ($row1 = mysql_fetch_row($result1)) {
    $type = $row1[0];
    $name = $row1[1];
    $number=array();
    // month loop
    for ( $m = 1; $m <= 12 ; $m++ ) {
    	$query ="select count(*) as NB 
			from evenement, evenement_horaire
			where Year(EH_DATE_DEBUT) = ".$year."
			and month(EH_DATE_DEBUT) = ".$m."
			and evenement.E_CODE = evenement_horaire.E_CODE
			and EH_ID = 1
			and E_CANCELED = 0
			and TE_CODE='".$type."'
			and S_ID in (".$list.")";
		$result = mysql_query($query);
		while ($row = mysql_fetch_row($result)) {
	 		$number[$m - 1] = $row[0];
		}
	}
	$layer->addDataSet($number, $colors[$i] , $name);
	$i++;
}

# Use soft lighting effect with light direction from the left
$layer->setBorderColor(Transparent, softLighting(Left));

# Set the x axis labels. In this example, the labels must be Jan - Dec.
$labels = array("Jan", "Fev", "Mar", "Avr", "Mai", "Jun", "Jul", "Aou", "Sept",
    "Oct", "Nov", "Dec");
$c->xAxis->setLabels($labels);

# Draw the ticks between label positions (instead of at label positions)
$c->xAxis->setTickOffset(0.5);

# Set the y axis title
$c->yAxis->setTitle("Nombre d'événements (non annulés)");

# Set axes width to 2 pixels
$c->xAxis->setWidth(2);
$c->yAxis->setWidth(2);

# output the chart in PNG format
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
