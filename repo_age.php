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

require_once ("./ChartDirector/lib/phpchartdir.php");
include_once ("./config.php");
check_all(15);

if ( $subsections == 1 ) {
  	 $list = get_family("$section");
}
else {
  	$list = $section;
}

# The age groups
$lower = array ( 0,19,25,30,35,40,45,50,55,60,65,70,75);
$upper = array (18,24,29,34,39,44,49,54,59,64,69,74,99);
$nb_tranches=count($lower);

$labels = array();
$male = array();
$female = array();
for ($i = 0; $i < $nb_tranches; $i++) {
	$labels[$i] = $lower[$i]." - ".$upper[$i];
	
	$query ="select P_SEXE, count(*) as NB from pompier
			 where EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),P_BIRTHDATE))))+0 >=".$lower[$i]."
			 and EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),P_BIRTHDATE))))+0 <=".$upper[$i]."
			 and P_BIRTHDATE <> '0000-00-00'
			 and P_BIRTHDATE is not null
			 and P_OLD_MEMBER = 0
			 and P_STATUT <> 'EXT'
			 and P_SEXE is not null
			 and P_SECTION in (".$list.")
			 group by P_SEXE";
	$result = mysql_query($query);
	while ($row = mysql_fetch_row($result)){
		if ( $row[0] == 'M' ) $male[$i] = $row[1];
		if ( $row[0] == 'F' ) $female[$i] = $row[1];
	}
}

$query="select P_SEXE, round(avg(EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),P_BIRTHDATE))))+0))
        from pompier
        where P_BIRTHDATE <> '0000-00-00'
		and P_BIRTHDATE is not null
		and P_OLD_MEMBER = 0
		and P_STATUT <> 'EXT'
		and P_SEXE is not null
		and P_SECTION in (".$list.")
		group by P_SEXE";
$result = mysql_query($query);
while ($row = mysql_fetch_row($result)){
		if ( $row[0] == 'M' ) $avgM = $row[1];
		if ( $row[0] == 'F' ) $avgF = $row[1];		
}

$query="select count(*)
        from pompier
        where P_BIRTHDATE <> '0000-00-00'
		and P_BIRTHDATE is not null
		and P_OLD_MEMBER = 0
		and P_STATUT <> 'EXT'
		and P_SECTION in (".$list.")";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$total = $row[0];

#=============================================================
#    Draw the right bar chart
#=============================================================

# Create a XYChart object of size 320 x 300 pixels
$c = new XYChart(320, 300);

# Set the plotarea at (50, 0) and of size 250 x 255 pixels. Use pink (0xffdddd) as
# the background.
$c->setPlotArea(50, 0, 250, 255, 0xffdddd);

# Add a custom text label at the top right corner of the right bar chart
$textBoxObj = $c->addText(300, 0, "Femmes (moyenne ".$avgF." ans)", "arialbd.ttf", 10, 0xa07070);
$textBoxObj->setAlignment(TopRight);

# Add the pink (0xf0c0c0) bar chart layer using the female data
$femaleLayer = $c->addBarLayer($female, 0xf0c0c0);

# Swap the axis so that the bars are drawn horizontally
$c->swapXY(true);

# Set the bar to touch each others
$femaleLayer->setBarGap(TouchBar);

# Set the border style of the bars to 1 pixel 3D border
$femaleLayer->setBorderColor(-1, 1);

# Add labels to the top of the bar using 8 pt Arial Bold font.
$femaleLayer->setAggregateLabelStyle("arialbd.ttf", 8, 0xa07070);

# Add a Transparent line layer to the chart using the male data. As it is
# Transparent, only the female bar chart can be seen. We need to put both male and
# female data in both left and right charts, because we want auto-scaling to produce
# the same scale for both chart.
$c->addLineLayer($male, Transparent);

# Set the y axis label font to Arial Bold
$c->yAxis->setLabelStyle("arialbd.ttf");

# Set the labels between the two bar charts, which can be considered as the x-axis
# labels for the right chart
$tb = $c->xAxis->setLabels($labels);

# Use a fix width of 50 for the labels (height = automatic) with center alignment
$tb->setSize(50, 0);
$tb->setAlignment(Center);

# Set the label font to Arial Bold
$tb->setFontStyle("arialbd.ttf");

# Disable ticks on the x-axis by setting the tick length to 0
$c->xAxis->setTickLength(0);

#=============================================================
#    Draw the left bar chart
#=============================================================

# Create a XYChart object of size 280 x 300 pixels with a transparent background.
$c2 = new XYChart(280, 300, Transparent);

# Set the plotarea at (20, 0) and of size 250 x 255 pixels. Use pale blue (0xddddff)
# as the background.
$c2->setPlotArea(20, 0, 250, 255, 0xddddff);

# Add a custom text label at the top left corner of the left bar chart
$c2->addText(20, 0, "Hommes (moyenne ".$avgM." ans)", "arialbd.ttf", 10, 0x7070a0);

# Add the pale blue (0xaaaaff) bar chart layer using the male data
$maleLayer = $c2->addBarLayer($male, 0xaaaaff);

# Add labels to the top of the bar using 8 pt Arial Bold font.
$maleLayer->setAggregateLabelStyle("arialbd.ttf", 8, 0x7070a0);

# Swap the axis so that the bars are drawn horizontally
$c2->swapXY(true);

# Reverse the direction of the y-axis so it runs from right to left
$c2->yAxis->setReverse();

# Set the bar to touch each others
$maleLayer->setBarGap(TouchBar);

# Set the border style of the bars to 1 pixel 3D border
$maleLayer->setBorderColor(-1, 1);

# Add a Transparent line layer to the chart using the female data. As it is
# Transparent, only the male bar chart can be seen. We need to put both male and
# female data in both left and right charts, because we want auto-scaling to produce
# the same scale for both chart.
$c2->addLineLayer($female, Transparent);

# Set the y axis label font to Arial Bold
$c2->yAxis->setLabelStyle("arialbd.ttf");

#=============================================================
#    Use a MultiChart to contain both bar charts
#=============================================================

# Create a MultiChart object of size 590 x 320 pixels.
$m = new MultiChart(590, 320);

# Add a title to the chart using Arial Bold Italic font
$m->addTitle("Pyramide des âges", "arialbi.ttf",12,0x000099);

# Add another title at the bottom using Arial Bold Italic font
$m->addTitle2(Bottom, "Nombre de membres comptabilisés: $total", "arialbi.ttf", 10,0x000099);

# Put the right chatr at (270, 25)
$m->addChart(270, 25, $c);

# Put the left char at (0, 25)
$m->addChart(0, 25, $c2);

# output the chart
header("Content-type: image/png");
print($m->makeChart2(PNG));
?>
