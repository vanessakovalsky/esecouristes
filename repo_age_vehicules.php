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
if (isset($_GET["equipe"])) $equipe=$_GET["equipe"];
else $equipe=1;

require_once ("./ChartDirector/lib/phpchartdir.php");
include_once ("./config.php");
check_all(15);

if ( $subsections == 1 ) $list = get_family("$section");
else $list = $section;

$positions="";
$query="select VP_ID from vehicule_position where VP_OPERATIONNEL >=0";
$result = mysql_query($query);
while ($row = @mysql_fetch_array($result)) {
 	$VP_ID=$row["VP_ID"];
 	$positions .= "'".$VP_ID."',";
}
$positions .='NULL';

$YEAR=date('Y');

# The age groups
$lower = array (0,5,10,15,20);
$upper = array (4,9,14,19,100);
$nb_tranches=count($lower); 

for ($i = 0; $i < $nb_tranches; $i++) {
 	if ( $lower[$i] == 20 ) $labels[$i] = '+ de 20 ans';
	else $labels[$i] = $lower[$i]." - ".$upper[$i]. " ans";

	$query=" select count(*) as NB 
		 from vehicule 
		 where VP_ID in (".$positions.")
		 and (".$YEAR." >=".$lower[$i]." + V_ANNEE)
		 and (".$YEAR."  <=".$upper[$i]."+ V_ANNEE)
         and S_ID in (".$list.")";
         
	$result = mysql_query($query);
	$row = @mysql_fetch_array($result);
	$data[$i] = $row[0];
}
	
# Create a XYChart object of size 600 x 300 pixels, with a light grey (eeeeee)
# background, black border, 1 pixel 3D border effect and rounded corners.
$c = new XYChart(600, 350, 0xB7D8FB, 0x000099, 1);
$c->setRoundedFrame();

$textBoxObj = $c->addTitle("Nombre de v�hicules par age", "arialbd.ttf", 15,0xffffff);
$textBoxObj->setBackground(0x000099, 0x000000, glassEffect());

# Set the plotarea at (60, 60) and of size 520 x 200 pixels. Set background color to
# white (ffffff) and border and grid colors to grey (cccccc)
$c->setPlotArea(60, 60, 520, 180, 0xeeeeee, -1, 0xcccccc, 0xccccccc);

# Add a multi-color bar chart layer using the given data and colors. Use a 1 pixel 3D
# border for the bars.
//if ( $nbsections == 0 ) 
	$colors=array(0x000099, 0x057c24, 0xf90101, 0xFF3366, 0x5c88c4, 0xf488c4,
				  0xba4a4a, 0x97ba99, 0x972399);
	
$barLayerObj = $c->addBarLayer3($data, $colors);
$barLayerObj->setBorderColor(-1, 1);

# Add labels to the top of the bar using 8 pt Arial Bold font.
$barLayerObj->setAggregateLabelStyle("arialbd.ttf", 8, 0x000099);

# Set the labels on the x axis.
$c->xAxis->setLabels($labels);

$textBoxObj = $c->xAxis->setLabelStyle("arialbd.ttf", 9, 0x000099);
$textBoxObj->setFontAngle(36);


# Add a title to the y axis
$c->yAxis->setTitle("Nombre de personnes");

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));

	
?>