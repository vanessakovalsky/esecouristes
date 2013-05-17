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
  
//header ("Content-type: image/png");
include_once ("config.php");
check_all(38);

$month=intval($_GET["month"]);
$year=intval($_GET["year"]);
$section=intval($_GET["section"]);
$moislettres=moislettres($month);

$T = array();
$J = array();
$N = array();
$P_NOM = array();
$P_GRADE = array();
$P_SECTION = array();
$P_STATUT = array();
$allN=0;
$allJ=0;

$query_j="select distinct P_GRADE, P_ID, P_NOM, P_PRENOM, P_SECTION, P_STATUT
          from pompier, grade, section
 	 where P_GRADE=G_GRADE
 	 and S_ID=P_SECTION
 	 and P_OLD_MEMBER=0
 	 and P_NOM <> 'admin' ";
if ( $section <> 0 ) $query_j .= "\nand P_SECTION in (".get_family($section).")";
$query_j .= " order by P_NOM";

$result_j=mysql_query($query_j);
$i=0;
while ($row_j=@mysql_fetch_array($result_j)) {
 	$P_ID=$row_j["P_ID"];
    //======================================================
	// get infos
	//======================================================     	  	      	
      	$query="select sum(D_JOUR) as DJ , sum(D_NUIT) as DN
		   from disponibilite
  		   where P_ID=".$P_ID."
      	   and YEAR(D_DATE) =".$year;
  	    if ( $month <> 100 ) $query .= " and MONTH(D_DATE)=".$month." ";

	    $result=mysql_query($query);
	    $row=mysql_fetch_array($result);
	    if (($nbsections <> 3 ) or ( $row_j["P_SECTION"] <= 4)) {
	     	if (( $nbsections <> 0 ) or ( $row["DJ"] > 0 ) or ( $row["DN"] > 0)) {
	    	   $T[$i] = $row["DN"] + $row["DJ"];
	    	   $J[$i] = $row["DJ"];
	    	   $N[$i] = $row["DN"];
			   $P_NOM[$i] = substr($row_j["P_NOM"],0,18).", ".substr($row_j["P_PRENOM"],0,1);
			   $P_GRADE[$i] = $row_j["P_GRADE"];
			   $P_SECTION[$i] = $row_j["P_SECTION"];
			   $P_STATUT[$i] = $row_j["P_STATUT"];
			   $allN=$allN+$N[$i];
			   $allJ=$allJ+$J[$i];
			   $i = $i +1;
			}
		}

}

array_multisort($T, SORT_DESC,
				$J, SORT_DESC,
				$N, SORT_DESC,
				$P_NOM,
				$P_GRADE,
				$P_SECTION,
				$P_STATUT );


$nb = max(1,$i);
$max = 68;

$hauteurmini=500;
$hauteur1 = 20;
$largeur = 400; 
$hauteur = max($hauteurmini,$nb * $hauteur1 + 20); 

$im = @ImageCreate ($largeur, $hauteur) or die ("Erreur lors de la création de l'image");
 
$blanc = ImageColorAllocate ($im, 255, 255, 255);  
$noir = ImageColorAllocate ($im, 0, 0, 0); 
$jaune = ImageColorAllocate ($im, 255 , 255 , 128);   
$bfs = ImageColorAllocate ($im, 75, 130, 195);
$bcs = ImageColorAllocate ($im, 95, 160, 240);
$bfwe = ImageColorAllocate ($im, 197 , 89 , 6);
$bcwe = ImageColorAllocate ($im, 247 , 175 , 4);

$namesSPP = ImageColorAllocate ($im, 255 , 20 , 60);
$namesSPV = ImageColorAllocate ($im, 30 , 30 , 160);
$counter = ImageColorAllocate ($im, 30 , 30 , 160);

// on dessine un trait horizontal    	
ImageLine ($im, 100, 20, $largeur-15, 20, $noir); 
// graduations
$largeur5 = ceil(5 *($largeur - 100)/$max);
for ($y=0; $y <= 12 ; $y++) {
 	$c= 5 * $y;
	ImageLine ($im, 100 + $y *$largeur5 ,12 ,100 + $y *$largeur5, 20, $noir);
	imagettftext($im, 9, 0, 100 + $y *$largeur5, 9, $noir, 'images/arial.ttf', "$c");
}

// on dessine un trait vertical
ImageLine ($im, 100, 20,100, $hauteur, $noir); 

// légende
imagettftext($im, 12, 0, $largeur-180, $hauteur -80, $noir, 'images/arial.ttf', "dispos de 12h");
imagettftext($im, 12, 0, $largeur-180, $hauteur -100, $noir, 'images/arial.ttf', strtoupper("$moislettres $year"));

ImageFilledRectangle ($im, $largeur-180,$hauteur -70, $largeur-170, $hauteur -60, $noir); 
ImageFilledRectangle ($im, $largeur-179, $hauteur -69, $largeur-171, $hauteur -61, $bfwe); 
ImageFilledRectangle ($im, $largeur-178, $hauteur -68, $largeur-172, $hauteur -62,$bcwe); 
imagettftext($im, 10, 0, $largeur-158, $hauteur -62, $noir, 'images/arial.ttf', "jours: $allJ");
        
ImageFilledRectangle ($im, $largeur-180,$hauteur -50, $largeur-170, $hauteur -40, $noir); 
ImageFilledRectangle ($im, $largeur-179, $hauteur -49, $largeur-171, $hauteur -41, $bfs); 
ImageFilledRectangle ($im, $largeur-178, $hauteur -48, $largeur-172, $hauteur -42,$bcs); 
imagettftext($im, 10, 0, $largeur-158, $hauteur -42, $noir, 'images/arial.ttf', "nuits: $allN");

// on parcourt les noms
if ( $i > 0 ) {
  for ($i=0; $i < $nb; $i++) {
    if ( $P_STATUT[$i] == 'SPP' )
 		imagettftext($im, 8,0,0, 33+ $i * $hauteur1 , $namesSPP, 'images/arial.ttf', strtoupper($P_NOM[$i]));
 	else
 		imagettftext($im, 8,0,0, 33+ $i * $hauteur1 , $namesSPV, 'images/arial.ttf', strtoupper($P_NOM[$i]));
	$bf=$bfs;
	$bc=$bcs;
	// on calcule la longueur du baton
    $lT = ceil($T[$i] * $largeur5 / 5); 
    $lJ = ceil($J[$i] * $largeur5 / 5); 
     
	if ( $J[$i] <> 0 ) {   
	ImageFilledRectangle ($im, 100, $i * $hauteur1 +20, $lJ + 100, ($i +1) * $hauteur1 + 18, $noir); 
    ImageFilledRectangle ($im, 101, $i * $hauteur1 +22, $lJ + 100 -1, ($i +1) * $hauteur1 + 16, $bfwe); 
    ImageFilledRectangle ($im, 102, $i * $hauteur1 +24, $lJ + 100 -2, ($i +1) * $hauteur1 + 14,$bcwe); 
    }
	if ( $N[$i] <> 0 ) {     
	ImageFilledRectangle ($im, 100 +$lJ, $i * $hauteur1 +20, $lT + 100 , ($i +1) * $hauteur1 + 18, $noir);
    ImageFilledRectangle ($im, 101 +$lJ, $i * $hauteur1 +22, $lT + 100 -2, ($i +1) * $hauteur1 + 16, $bf); 
    ImageFilledRectangle ($im, 102 +$lJ, $i * $hauteur1 +24, $lT + 100 -3 , ($i +1) * $hauteur1 + 14,$bc); 
    }    
    imagettftext($im, 7,0, $lT + 105, $i * $hauteur1 +32, $counter, 'images/arial.ttf', $T[$i]);
  }
}
// on dessine le tout
 Imagepng ($im);
?>
