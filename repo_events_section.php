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
if (isset($_GET["type"])) $type=$_GET["type"];
else $type='ALL';
if (isset($_GET["year"])) $year=$_GET["year"];
else $year=date("Y");


require_once ("./ChartDirector/lib/phpchartdir.php");
include_once ("./config.php");
check_all(15);

if ( $type <> 'ALL' ) {
	$query1="select TE_CODE, TE_LIBELLE from type_evenement";
 	$query1 .= " where TE_CODE = '".$type."'";
	$result1 = mysql_query($query1);
	$row1 = mysql_fetch_row($result1);
	$type = $row1[0];
    $name = $row1[1];
}
else $name = 'Evénements';

// section 
$query ="select S_DESCRIPTION, S_ID from section where S_ID=".$section;
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$labels[] = $row[0];
$query2="select count(*) as NB from evenement, evenement_horaire
			where S_ID =".$row[1]."
			and evenement.E_CODE = evenement_horaire.E_CODE
			and EH_ID = 1
			and Year(EH_DATE_DEBUT) = ".$year;
if ( $type <> 'ALL' ) $query2 .= " and TE_CODE = '".$type."'";
$result2 = mysql_query($query2);
$row2 = mysql_fetch_row($result2);
$data[] = round($row2[0]);

// sous-sections
if ( get_children($section)  <> '' ) {
	$query ="select S_DESCRIPTION,S_ID from section where S_PARENT=".$section;
	$result = mysql_query($query);

	while ($row = mysql_fetch_row($result)){
		$labels[] = $row[0];
		$query2="select count(*) as NB from evenement, evenement_horaire
			where S_ID in (".get_family($row[1]).")
			and evenement.E_CODE = evenement_horaire.E_CODE
			and EH_ID = 1
			and Year(EH_DATE_DEBUT) = ".$year;
		if ( $type <> 'ALL' ) $query2 .= " and TE_CODE = '".$type."'";
		$result2 = mysql_query($query2);
		$row2 = mysql_fetch_row($result2);
		$data[] = round($row2[0]);
	}
}
# Create a XYChart object of size 600 x 300 pixels, with a light grey (eeeeee)
# background, black border, 1 pixel 3D border effect and rounded corners.
$c = new XYChart(600, 350, 0xB7D8FB, 0x000099, 1);
$c->setRoundedFrame();

$textBoxObj = $c->addTitle($name." par section en ".$year, "arialbd.ttf", 15,0xffffff);
$textBoxObj->setBackground(0x000099, 0x000000, glassEffect());

# Set the plotarea at (60, 60) and of size 520 x 200 pixels. Set background color to
# white (ffffff) and border and grid colors to grey (cccccc)
$c->setPlotArea(60, 60, 520, 180, 0xeeeeee, -1, 0xcccccc, 0xccccccc);

# Add a multi-color bar chart layer using the given data and colors. Use a 1 pixel 3D
# border for the bars.
$barLayerObj = $c->addBarLayer3($data, $colors);
$barLayerObj->setBorderColor(-1, 1);

# Add labels to the top of the bar using 8 pt Arial Bold font.
$barLayerObj->setAggregateLabelStyle("arialbd.ttf", 8, 0x000099);

# Set the labels on the x axis.
$c->xAxis->setLabels($labels);

$textBoxObj = $c->xAxis->setLabelStyle("arialbd.ttf", 9, 0x000099);
$textBoxObj->setFontAngle(36);


# Add a title to the y axis
$c->yAxis->setTitle("nombre d'événements");

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));

?>
