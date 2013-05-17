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
  

if ( isset ($_GET["section"])) $section=intval($_GET["section"]);
else $section=$_SESSION['SES_SECTION'];

require_once ("./ChartDirector/lib/phpchartdir.php");
include_once ("./config.php");

check_all(15);

$i=1;
$query ="select S_DESCRIPTION, S_ID from section_flat where NIV=3 order by S_DESCRIPTION";
$result = mysql_query($query);
while (($row = mysql_fetch_row($result)) and ( $i < 50)){
 	$query2="select count(*) as NB from evenement e, section s 
 			where e.TE_CODE in ('GRIPA','VACCI')
 			and s.S_ID = e.S_ID
 			and ( s.S_ID = ".$row[1]." or s.S_PARENT  = ".$row[1]." )";
 	$result2 = mysql_query($query2);
 	$row2 = mysql_fetch_row($result2);

 	if ( $row2[0] > 0 ) {
 	 	$i++;
 		$labels[] = $row[0];
 		$data[] = $row2[0];
 	}
}

array_multisort($data, SORT_ASC,
				$labels);

$height = $i * 25;

$queryz="select count(*) as NB from evenement where TE_CODE='GRIPA'";
$resultz=mysql_query($queryz);
$rowz=@mysql_fetch_array($resultz);
$total=$rowz["NB"];

# Create a XYChart object of size 700 x 250 pixels
$c = new XYChart(700, $height, 0xB7D8FB, 0x000099, 1);
$c->setRoundedFrame();

# Set the plotarea at (100, 30) and of size 400 x 200 pixels. Set the plotarea
# border, background and grid lines to Transparent
$c->setPlotArea(200, 35, 500, $height - 50 , 0xeeeeee, -1, 0xcccccc,
    0xcccccc, 0xcccccc);

# Add a title to the chart using Arial Bold Italic font
$textBoxObj =$c->addTitle("Activités liées à la grippe par département (total ".$total.")", "arialbd.ttf", 15,0xffffff);
$textBoxObj->setBackground(0x000099, 0x000000, glassEffect());

# Add a bar chart layer using the given data. Use a gradient color for the bars,
# where the gradient is from dark blue (0x008000) to white (0xffffff)
$layer = $c->addBarLayer($data, $c->gradientColor(200, 0, 700, 0, 0xff3333, 0xffffff)
    );

# Swap the axis so that the bars are drawn horizontally
$c->swapXY(true);

# Set the bar gap to 10%
$layer->setBarGap(0.1);

# Use the format "US$ xxx millions" as the bar label
$layer->setAggregateLabelFormat("{value}");

# Set the bar label font to 10 pts Times Bold Italic/dark red (0x663300)
$layer->setAggregateLabelStyle("arialbd.ttf", 10, 0x000099);

# Set the labels on the x axis
$textbox = $c->xAxis->setLabels($labels);

# Set the x axis label font to 10pt Arial Bold Italic
$textbox->setFontStyle("arialbi.ttf");
$textbox->setFontSize(9);

# Set the x axis to Transparent, with labels in dark red (0x663300)
$c->xAxis->setColors(Transparent, 0x000099);

# Set the y axis and labels to Transparent
$c->yAxis->setColors(Transparent, Transparent);


# Output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));

?>
