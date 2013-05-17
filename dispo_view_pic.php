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
  
header ("Content-type: image/png");  
include_once ("config.php");
check_all(38);

$month=mysql_real_escape_string($_GET["month"]);
$year=mysql_real_escape_string($_GET["year"]);
$type=mysql_real_escape_string($_GET["type"]);
$section=mysql_real_escape_string($_GET["section"]);

//nb de jours du mois
$d=nbjoursdumois($month, $year);
$moislettres=moislettres($month);

for ($i=1; $i<=$d; $i++) { 
	$nb[$i]=count_personnel_dispo($year, $month, $i, $type, $section);
}

$max_nb = max(1,max($nb));

$largeur = 600; 
$hauteur = 300; 

$im = @ImageCreate ($largeur, $hauteur) or die ("Erreur lors de la création de l'image");

$blanc = ImageColorAllocate ($im, 255, 255, 255);  
$noir = ImageColorAllocate ($im, 0, 0, 0); 
$jaune = ImageColorAllocate ($im, 255 , 255 , 128); 

if ( $type == 'J') {
	$comment='Jour';
	$bfs = ImageColorAllocate ($im, 255 , 178 , 10);
	$bcs = ImageColorAllocate ($im, 255 , 244 , 1);
	$bfwe = ImageColorAllocate ($im, 253 , 88 , 1);
	$bcwe = ImageColorAllocate ($im, 255 , 128 , 0);
}
if ( $type == 'N') {
	$comment='Nuit';
	$bfs = ImageColorAllocate ($im, 67 , 138 , 194);
	$bcs = ImageColorAllocate ($im, 90 , 181 , 217);
	$bfwe = ImageColorAllocate ($im, 15 , 51 , 143);
	$bcwe = ImageColorAllocate ($im, 51 , 105 , 178);
}

// on dessine un trait horizontal pour représenter l'axe du temps     
ImageLine ($im, 20, $hauteur-40, $largeur-15, $hauteur-40, $noir); 

// on affiche le numéro des jours
for ($i=1; $i<=$d; $i++) { 
    ImageString ($im, 2, $i * 18 +4, $hauteur-38, $i, $noir);
}

// on dessine un trait vertical
ImageLine ($im, 18, 30, 18, $hauteur-40, $noir); 
// graduations
$hauteur5 = ceil(5 *($hauteur-40-50)/$max_nb);
for ($y=0; $y < ceil($max_nb / 5 ); $y++) {
 	$c= 5 * $y;
	ImageLine ($im, 14, $hauteur-40 - $y *$hauteur5 , 18, $hauteur-40 - $y *$hauteur5, $noir);
	imagettftext($im, 10, 0, 0, $hauteur-40 - $y *$hauteur5, $noir, 'images/arial.ttf', "$c");
}


// on affiche les legendes sur les deux axes ainsi que différents textes.
 imagettftext($im, 10, 0, $largeur-70, $hauteur-10, $noir, 'images/arial.ttf', "$comment");
 imagettftext($im, 10, 0, 10, 20, $noir, 'images/arial.ttf', "Nombre");
 imagettftext($im, 14, 0, $largeur-250, 20, $noir, 'images/arial.ttf', "$moislettres $year");

// on parcourt les jours
for ($i=1; $i <= $d; $i++) {
    if ($nb[$i]!="0") {
        if (is_we($month,$i,$year)) {
			$bf=$bfwe;
			$bc=$bcwe;
		}
		else {
			$bf=$bfs;
			$bc=$bcs;
		}
		$cmt=$bf;

        // on calcule la hauteur du baton
        $hIR = ceil($nb[$i] * $hauteur5 / 5); 
		ImageFilledRectangle ($im, ($i)*18, $hauteur-40 -$hIR -1, ($i)*18+14, $hauteur-41, $noir); 
        ImageFilledRectangle ($im, ($i)*18+2, $hauteur-40 -$hIR+1, ($i)*18+12, $hauteur-41-1, $bf); 
        ImageFilledRectangle ($im, ($i)*18+4, $hauteur-40 -$hIR+3, ($i)*18+10, $hauteur-41-1, $bc); 
        imagettftext($im, 7,30,($i)*18+4, $hauteur-40 -$hIR -3, $cmt, 'images/arial.ttf', "$nb[$i]");
    }
}

// on dessine le tout
Imagepng ($im);
?>