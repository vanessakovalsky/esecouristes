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
check_all(0);

$month=intval($_GET["month"]);
$year=intval($_GET["year"]);
$section=intval($_GET["section"]);

$T = array();
$J = array();
$K = array();
$FOR = array();
$P_NOM = array();
$P_SECTION = array();
$P_STATUT = array();
$allJ=0;
$allK=0;
$allFOR=0;


$query_j="select distinct P_ID, P_NOM, P_PRENOM, P_SECTION, P_STATUT
          from pompier, section
 	 where S_ID=P_SECTION
 	 and P_NOM <> 'admin' ";
if ( $section <> 0 ) $query_j .= "\nand P_SECTION in (".get_family($section).")";
$query_j .= " order by P_NOM";

$result_j=mysql_query($query_j);
$maxT=5;
$i=0; $k=0;
while ($row_j=@mysql_fetch_array($result_j)) {
 	$P_ID=$row_j["P_ID"];
    //======================================================
	// get infos
	//======================================================     	  	      	
      	$queryJ1="select count(*) as NB
		   from evenement_participation ep, evenement e, evenement_horaire eh
  		   where ep.E_CODE= e.E_CODE
  		   and ep.E_CODE = eh.E_CODE 
  		   and ep.P_ID=".$P_ID."
  		   and e.E_CANCELED=0
  		   and e.TE_CODE='DPS'
      	   and YEAR(eh.EH_DATE_DEBUT) =".$year;
  	    if ( $month <> 100 ) $queryJ1 .= " and MONTH(eh.EH_DATE_DEBUT)=".$month." ";
	    $resultJ1=mysql_query($queryJ1);
	    $rowJ1=mysql_fetch_array($resultJ1);
	    
	    $queryJ3="select count(*) as NB
		   from evenement_participation ep, evenement e, evenement_horaire eh
  		   where ep.E_CODE= e.E_CODE
  		   and ep.E_CODE = eh.E_CODE 
  		   and ep.P_ID=".$P_ID."
  		   and e.E_CANCELED=0
  		   and e.TE_CODE='FOR'
      	   and YEAR(eh.EH_DATE_DEBUT) =".$year;
  	    if ( $month <> 100 ) $queryJ3 .= " and MONTH(eh.EH_DATE_DEBUT)=".$month." ";
	    $resultJ3=mysql_query($queryJ3);
	    $rowJ3=mysql_fetch_array($resultJ3);
		
		$queryJ2="select count(*) as NB
		   from evenement_participation ep, evenement e, evenement_horaire eh
  		   where ep.E_CODE= e.E_CODE
  		   and ep.E_CODE = eh.E_CODE 
  		   and ep.P_ID=".$P_ID."
  		   and e.E_CANCELED=0
  		   and e.TE_CODE NOT IN('DPS','FOR')
      	   and YEAR(eh.EH_DATE_DEBUT) =".$year;
  	    if ( $month <> 100 ) $queryJ2 .= " and MONTH(eh.EH_DATE_DEBUT)=".$month." ";
	    $resultJ2=mysql_query($queryJ2) or die ("Erreur SQL");
	    $rowJ2=mysql_fetch_array($resultJ2);  
	    
	    $J[$i] = $rowJ1["NB"];
	    $K[$i] = $rowJ2["NB"];
	    $FOR[$i] = $rowJ3["NB"];
	    $T[$i] = $rowJ1["NB"] + $rowJ2["NB"] + $rowJ3["NB"];
	    if ( $T[$i] > 0 ) $k =$k +1;
		$P_NOM[$i] = substr($row_j["P_NOM"],0,18).", ".substr($row_j["P_PRENOM"],0,1);
		$P_SECTION[$i] = $row_j["P_SECTION"];
		$P_STATUT[$i] = $row_j["P_STATUT"];
		$allJ=$allJ+$J[$i];
		$allK=$allK+$K[$i];
		$allFOR=$allFOR+$FOR[$i];
		if ( $T[$i] > $maxT ) $maxT = $T[$i];
		$i = $i +1;

}

array_multisort($T, SORT_DESC,
				$J, SORT_DESC,
				$K, SORT_DESC,
				$FOR, SORT_DESC,
				$P_NOM,
				$P_SECTION,
				$P_STATUT );

// afficher au maximum les 200 premiers et avoir $nb au moins égal à 1
$donotdisplaymorethan=200;
$nb = min($donotdisplaymorethan,max(1,$k));
$max = $maxT +20;

$hauteurmini=500;
$hauteur1 = 20;
$largeur = 500; 
$hauteur = max($hauteurmini,$nb * $hauteur1 + 20); 

$im = @ImageCreate ($largeur, $hauteur) or die ("Erreur lors de la création de l'image");
 
$blanc = ImageColorAllocate ($im, 255, 255, 255);  
$noir = ImageColorAllocate ($im, 0, 0, 0); 
   
$orangedark = ImageColorAllocate ($im, 197 , 89 , 6);
$orange = ImageColorAllocate ($im, 247 , 175 , 4);

$bluedark = ImageColorAllocate ($im, 45,263,59);
$blue = ImageColorAllocate ($im, 45,263,123);

$greendark = ImageColorAllocate ($im, 0,110,0);
$green = ImageColorAllocate ($im, 0,160,0);

$namesSPP = ImageColorAllocate ($im, 255 , 20 , 60);
$namesSPV = ImageColorAllocate ($im, 30 , 30 , 160);
$namesEXT = $greendark;
$counter = ImageColorAllocate ($im, 30 , 30 , 160);

// on dessine un trait horizontal    	
ImageLine ($im, 100, 20, $largeur-12, 20, $noir); 
// graduations
$pas= ceil($maxT/20);
$largeur2 = ceil(2 *($largeur - 100)/$max);
for ($y=0; $y <= $maxT; $y= $y + $pas) {
 	$c= 2 *$y;
	ImageLine ($im, 100 + $y *$largeur2 ,12 ,100 + $y *$largeur2, 20, $noir);
	imagettftext($im, 9, 0, 100 + $y *$largeur2, 9, $noir, 'images/arial.ttf', "$c");
}

// on dessine un trait vertical
ImageLine ($im, 100, 20,100, $hauteur, $noir); 


// légende
if ( $month == 100 ) $str="Année $year";
else  $str=moislettres($month)." ".$year;
if ( $k > $donotdisplaymorethan ) $str.=" (".$donotdisplaymorethan." premiers)";
imagettftext($im, 10, 0, $largeur-220, $hauteur -80, $noir, 'images/arial.ttf', strtoupper("$str"));

// garde 1
ImageFilledRectangle ($im, $largeur-220,$hauteur -65, $largeur-210, $hauteur -55, $noir); 
ImageFilledRectangle ($im, $largeur-219, $hauteur -64, $largeur-211, $hauteur -56, $orangedark); 
ImageFilledRectangle ($im, $largeur-218, $hauteur -63, $largeur-212, $hauteur -57,$orange); 
imagettftext($im, 9, 0, $largeur-198, $hauteur -57, $noir, 'images/arial.ttf', "DPS: ".$allJ);

// Formation
ImageFilledRectangle ($im, $largeur-220,$hauteur -45, $largeur-210, $hauteur -35, $noir); 
ImageFilledRectangle ($im, $largeur-219, $hauteur -44, $largeur-211, $hauteur -36, $bluedark); 
ImageFilledRectangle ($im, $largeur-218, $hauteur -43, $largeur-212, $hauteur -37,$blue); 
imagettftext($im, 9, 0, $largeur-198, $hauteur -37, $noir, 'images/arial.ttf', "Formation: ".$allFOR);

// garde 2
ImageFilledRectangle ($im, $largeur-220,$hauteur -25, $largeur-210, $hauteur -15, $noir); 
ImageFilledRectangle ($im, $largeur-219, $hauteur -24, $largeur-211, $hauteur -16, $greendark); 
ImageFilledRectangle ($im, $largeur-218, $hauteur -23, $largeur-212, $hauteur -17,$green); 
imagettftext($im, 9, 0, $largeur-198, $hauteur -17, $noir, 'images/arial.ttf', "autres: ".$allK);

// on parcourt les noms
if ( $i > 0 ) {
  for ($i=0; $i < $nb; $i++) {
    if ( $T[$i] > 0 ) {
       if (( $P_STATUT[$i] == 'SAL' ) or ( $P_STATUT[$i] == 'SPP' ))
 		  imagettftext($im, 8,0,0, 33+ $i * $hauteur1 , $namesSPP, 'images/arial.ttf', strtoupper($P_NOM[$i]));
 	   else if ( $P_STATUT[$i] == 'EXT' )
 		  imagettftext($im, 8,0,0, 33+ $i * $hauteur1 , $namesEXT, 'images/arial.ttf', strtoupper($P_NOM[$i]));
 	   else
 		  imagettftext($im, 8,0,0, 33+ $i * $hauteur1 , $namesSPV, 'images/arial.ttf', strtoupper($P_NOM[$i]));

	   // on calcule la longueur du baton
       $lT = ceil($T[$i] * $largeur2 / 2); 
       $lJ = ceil($J[$i] * $largeur2 / 2);
       $lK = ceil($K[$i] * $largeur2 / 2);
       $lF = ceil($FOR[$i] * $largeur2 / 2);
     
	   if ( $J[$i] <> 0 ) {   
	   ImageFilledRectangle ($im, 100, $i * $hauteur1 +20, $lJ + 100, ($i +1) * $hauteur1 + 18, $noir); 
       ImageFilledRectangle ($im, 101, $i * $hauteur1 +22, $lJ + 100 -1, ($i +1) * $hauteur1 + 16, $orangedark); 
       ImageFilledRectangle ($im, 102, $i * $hauteur1 +24, $lJ + 100 -2, ($i +1) * $hauteur1 + 14,$orange); 
       imagettftext($im, 7,0, 105, $i * $hauteur1 +32, $noir, 'images/arial.ttf', $J[$i]);
       }

	   if ( $FOR[$i] <> 0 ) {   
	   ImageFilledRectangle ($im, 100 + $lJ, $i * $hauteur1 +20, $lJ+ $lF + 100, ($i +1) * $hauteur1 + 18, $noir); 
       ImageFilledRectangle ($im, 101 + $lJ, $i * $hauteur1 +22, $lJ+ $lF + 100 -1, ($i +1) * $hauteur1 + 16, $bluedark); 
       ImageFilledRectangle ($im, 102 + $lJ, $i * $hauteur1 +24, $lJ+ $lF + 100 -2, ($i +1) * $hauteur1 + 14,$blue); 
       imagettftext($im, 7,0, 105 +$lJ, $i * $hauteur1 +32, $noir, 'images/arial.ttf', $FOR[$i]);
       }

	   if ( $K[$i] <> 0 ) {   
	   ImageFilledRectangle ($im, 100 +$lJ + $lF, $i * $hauteur1 +20, $lJ+ $lF + $lK + 100, ($i +1) * $hauteur1 + 18, $noir); 
       ImageFilledRectangle ($im, 101 +$lJ + $lF , $i * $hauteur1 +22, $lJ+ $lF +$lK + 100 -1, ($i +1) * $hauteur1 + 16, $greendark); 
       ImageFilledRectangle ($im, 102 +$lJ + $lF , $i * $hauteur1 +24, $lJ+ $lF +$lK + 100 -2, ($i +1) * $hauteur1 + 14,$green); 
       imagettftext($im, 7,0, 105 +$lJ +$lF, $i * $hauteur1 +32, $noir, 'images/arial.ttf', $K[$i]);
       }    
	   ImageFilledRectangle ($im, $lT + 100, $i * $hauteur1 +20, $lT + 101, ($i +1) * $hauteur1 + 18, $noir);
	   imagettftext($im, 7,0, $lT + 105, $i * $hauteur1 +32, $noir, 'images/arial.ttf', $T[$i]." participation(s) à un évènement");
	}
  }
}
// on dessine le tout
Imagepng ($im);
?>
