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
if ( isset ($_GET["subsections"])) $subsections=mysql_real_escape_string($_GET["subsections"]);
else $subsections=1;
if (isset($_GET["type"])) $type=mysql_real_escape_string($_GET["type"]);
else $type='DPS';
if (isset($_GET["year"])) $year=mysql_real_escape_string($_GET["year"]);
else $year=date("Y");

if ( $subsections == 1 ) {
  	 $list = get_family("$section");
}
else {
  	$list = $section;
}

# The data for the line chart
// month loop
for ( $m = 1; $m <= 12 ; $m++ ) {
    $query ="select count(*) as nb0
			from evenement e, evenement_horaire eh
			where Year(eh.EH_DATE_DEBUT) = ".$year."
			and month(eh.EH_DATE_DEBUT) = ".$m."
			and eh.E_CODE = e.E_CODE
			and e.E_CANCELED = 0 
			and e.TE_CODE='".$type."'
			and e.S_ID in (".$list.")";
	$result = mysql_query($query);
	while ($row = mysql_fetch_row($result)) {
	 	$data0[$m - 1] = $row[0];
	}

	$query ="select count(*) as nb0
			from evenement e, evenement_participation ep, evenement_horaire eh
			where Year(eh.EH_DATE_DEBUT) = ".$year."
			and month(eh.EH_DATE_DEBUT) = ".$m."
			and ep.E_CODE =e.E_CODE
			and eh.E_CODE = ep.E_CODE
			and eh.EH_ID = ep.EH_ID
			and e.E_CANCELED = 0
			and ep.TP_ID = 0
			and e.TE_CODE='".$type."'
			and e.S_ID in (".$list.")";
	$result = mysql_query($query);
	while ($row = mysql_fetch_row($result)) {
	 	$data1[$m - 1] = $row[0];
	}

    $query ="select count(*) as nb0
			from evenement e, evenement_participation ep, evenement_horaire eh
			where Year(eh.EH_DATE_DEBUT) = ".$year."
			and month(eh.EH_DATE_DEBUT) = ".$m."
			and ep.E_CODE =e.E_CODE
			and eh.E_CODE = ep.E_CODE
			and eh.EH_ID = ep.EH_ID
			and e.E_CANCELED = 0
			and ep.TP_ID > 0
			and e.TE_CODE='".$type."'
			and e.S_ID in (".$list.")";
	$result = mysql_query($query);
	while ($row = mysql_fetch_row($result)) {
	 	$data2[$m - 1] = $row[0];
	}

}

$labels = array("Jan", "Fev", "Mar", "Avr", "Mai", "Jun", "Jul", "Aou", "Sept",
    "Oct", "Nov", "Dec");

# Create a XYChart object of size 600 x 375 pixels
$c = new XYChart(600, 400, 0xB7D8FB, 0x000099);
$c->setRoundedFrame();

$textBoxObj = $c->addTitle("Formations / stagiaires / formateurs ".$year, "arialbd.ttf", 15,0xffffff);
$textBoxObj->setBackground(0x000099, 0x000000, glassEffect());

$c->setPlotArea(50, 75, 500, 300, 0xeeeeee, -1, 0xcccccc, 0xccccccc);

# Add a legend box at (50, 28) using horizontal layout. Use 10pts Arial Bold as font,
# with transparent background.
$legendObj = $c->addLegend(50, 28, false, "arialbd.ttf", 10);
$legendObj->setBackground(Transparent);

# Set the x axis labels
$c->xAxis->setLabels($labels);

# Set y-axis tick density to 30 pixels. ChartDirector auto-scaling will use this as
# the guideline when putting ticks on the y-axis.
$c->yAxis->setTickDensity(30);

# Set axis label style to 8pts Arial Bold
$c->xAxis->setLabelStyle("arialbd.ttf", 8);
$c->yAxis->setLabelStyle("arialbd.ttf", 8);

# Set axis line width to 2 pixels
$c->xAxis->setWidth(2);
$c->yAxis->setWidth(2);

# Add axis title using 10pts Arial Bold Italic font
$c->yAxis->setTitle("Nombre par mois", "arialbi.ttf", 10);

# Add a line layer to the chart
$layer = $c->addLineLayer2();

# Set the line width to 3 pixels
$layer->setLineWidth(3);

# Add the three data sets to the line layer, using circles, diamands and X shapes as
# symbols
$dataSetObj = $layer->addDataSet($data0, $colors[5], "Formations");
$dataSetObj->setDataSymbol(DiamondSymbol, 8);
$dataSetObj = $layer->addDataSet($data1, $colors[6], "Stagiaires");
$dataSetObj->setDataSymbol(CircleSymbol, 8);
$dataSetObj = $layer->addDataSet($data2, $colors[7], "Formateurs");
$dataSetObj->setDataSymbol(CircleSymbol, 8);
$layer->setDataLabelFormat("{value|0}");


# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>

