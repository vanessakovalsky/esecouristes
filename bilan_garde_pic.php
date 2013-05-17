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

$month=$_GET["month"];
$year=$_GET["year"];
$section=$_GET["section"];
$moislettres=moislettres($month);

$T = array();
$J = array();
$N = array();
$K = array();
$L = array();
$P_NOM = array();
$P_GRADE = array();
$P_SECTION = array();
$P_STATUT = array();
$DISPOS = array();
$allN=0;
$allJ=0;
$allK=0;
$allL=0;

$query_e="select EQ_NOM, EQ_JOUR, EQ_NUIT, EQ_DUREE from equipe where EQ_ID=1";
$result_e=mysql_query($query_e);
$row_e=@mysql_fetch_array($result_e);
$EQ_NOM1=$row_e["EQ_NOM"];
$EQ_JOUR1=$row_e["EQ_JOUR"];
$EQ_NUIT1=$row_e["EQ_NUIT"];
$EQ_DUREE1=$row_e["EQ_DUREE"];
if ( $EQ_DUREE1 == "" ) $EQ_DUREE1=12;


$query_e="select EQ_NOM, EQ_JOUR, EQ_NUIT, EQ_DUREE from equipe where EQ_ID=2";
$result_e=mysql_query($query_e);
$row_e=@mysql_fetch_array($result_e);
$EQ_NOM2=$row_e["EQ_NOM"];
$EQ_JOUR2=$row_e["EQ_JOUR"];
$EQ_NUIT2=$row_e["EQ_NUIT"];
$EQ_DUREE2=$row_e["EQ_DUREE"];
if ( $EQ_DUREE2 == "" ) $EQ_DUREE2=8;

$query_j="select distinct P_GRADE, P_ID, P_NOM, P_SECTION, P_STATUT
          from pompier, grade, section
 	 where P_GRADE=G_GRADE
 	 and P_OLD_MEMBER=0
 	 and S_ID=P_SECTION
 	 and P_NOM <> 'admin' 
	 and P_OLD_MEMBER = 0 ";
if ( $section <> 0 ) $query_j .= "\nand P_SECTION in (".get_family($section).")";
$query_j .= " order by P_NOM";

$result_j=mysql_query($query_j);
$i=0;
while ($row_j=@mysql_fetch_array($result_j)) {
 	$P_ID=$row_j["P_ID"];
    //======================================================
	// get infos
	//======================================================     	  	      	
      	$queryJ1="select count(*) as NB
		   from planning_garde
  		   where P_ID=".$P_ID."
  		   and TYPE='J'
  		   and EQ_ID=1
      	   and YEAR(PG_DATE) =".$year;
  	    if ( $month <> 100 ) $queryJ1 .= " and MONTH(PG_DATE)=".$month." ";
	    $resultJ1=mysql_query($queryJ1);
	    $rowJ1=mysql_fetch_array($resultJ1);
	      
      	$queryN1="select count(*) as NB
		   from planning_garde
  		   where P_ID=".$P_ID."
  		   and TYPE='N'
  		   and EQ_ID=1
      	   and YEAR(PG_DATE) =".$year;
  	    if ( $month <> 100 ) $queryN1 .= " and MONTH(PG_DATE)=".$month." ";
	    $resultN1=mysql_query($queryN1);
	    $rowN1=mysql_fetch_array($resultN1);	
		
		$queryJ2="select count(*) as NB
		   from planning_garde
  		   where P_ID=".$P_ID."
  		   and TYPE='J'
  		   and EQ_ID=2
      	   and YEAR(PG_DATE) =".$year;
  	    if ( $month <> 100 ) $queryJ2 .= " and MONTH(PG_DATE)=".$month." ";
	    $resultJ2=mysql_query($queryJ2);
	    $rowJ2=mysql_fetch_array($resultJ2);
	      
      	$queryN2="select count(*) as NB
		   from planning_garde
  		   where P_ID=".$P_ID."
  		   and TYPE='N'
  		   and EQ_ID=2
      	   and YEAR(PG_DATE) =".$year;
  	    if ( $month <> 100 ) $queryN2 .= " and MONTH(PG_DATE)=".$month." ";
	    $resultN2=mysql_query($queryN2);
	    $rowN2=mysql_fetch_array($resultN2);    
	    
	   $queryD="select sum(D_JOUR) as DJ , sum(D_NUIT) as DN
		   from disponibilite
  		   where P_ID=".$P_ID."
      	   and YEAR(D_DATE) =".$year;
  	   if ( $month <> 100 ) $queryD .= " and MONTH(D_DATE)=".$month." "; 
  	   $resultD=mysql_query($queryD);
	   $rowD=mysql_fetch_array($resultD); 
	    
	   if ( $nbsections <> 0 ) { 
	      //if (( $rowJ1["NB"] > 0 ) or ( $rowN1["NB"] > 0) or ( $rowJ2["NB"] > 0) or ( $rowN2["NB"] > 0)) {
	    	   $T[$i] = $rowN1["NB"] + $rowJ1["NB"] + $rowN2["NB"] + $rowJ2["NB"];
	    	   $J[$i] = $rowJ1["NB"];
	    	   $N[$i] = $rowN1["NB"];
	    	   $K[$i] = $rowJ2["NB"];
	    	   $L[$i] = $rowN2["NB"];
	    	   $DISPOS[$i] = ($rowD["DJ"]+$rowD["DN"]) * 12;
			   $P_NOM[$i] = substr($row_j["P_NOM"],0,18);
			   $P_GRADE[$i] = $row_j["P_GRADE"];
			   $P_SECTION[$i] = $row_j["P_SECTION"];
			   $P_STATUT[$i] = $row_j["P_STATUT"];
			   $allN=$allN+$N[$i];
			   $allJ=$allJ+$J[$i];
			   $allK=$allK+$K[$i];
			   $allL=$allL+$L[$i];
			   $i = $i +1;
		 //}
	  }

}

array_multisort($T, SORT_DESC,
				$J, SORT_DESC,
				$N, SORT_DESC,
				$K, SORT_DESC,
				$L, SORT_DESC,
				$P_NOM,
				$P_GRADE,
				$P_SECTION,
				$P_STATUT,
				$DISPOS );


$nb = max(1,$i);
$max = 30;

$hauteurmini=500;
$hauteur1 = 20;
$largeur = 400; 
$hauteur = max($hauteurmini,$nb * $hauteur1 + 20); 

$im = @ImageCreate ($largeur, $hauteur) or die ("Erreur lors de la création de l'image");
 
$blanc = ImageColorAllocate ($im, 255, 255, 255);  
$noir = ImageColorAllocate ($im, 0, 0, 0); 
$jaune = ImageColorAllocate ($im, 255 , 255 , 128);
   
$purpledark = ImageColorAllocate ($im, 120, 12, 60);
$purple = ImageColorAllocate ($im, 180, 20, 90);
$orangedark = ImageColorAllocate ($im, 160 , 80 , 80);
$orange = ImageColorAllocate ($im, 255 , 100 , 100);

$kaki = ImageColorAllocate ($im, 90,120,90);
$kakidark = ImageColorAllocate ($im, 60,90,60);
$greendark = ImageColorAllocate ($im, 0,110,0);
$green = ImageColorAllocate ($im, 0,160,0);

$namesSPP = ImageColorAllocate ($im, 255 , 20 , 60);
$namesSPV = ImageColorAllocate ($im, 30 , 30 , 160);
$counter = ImageColorAllocate ($im, 30 , 30 , 160);

// on dessine un trait horizontal    	
ImageLine ($im, 100, 20, $largeur-15, 20, $noir); 
// graduations
$largeur2 = ceil(2 *($largeur - 100)/$max);
for ($y=0; $y <= 12 ; $y++) {
 	$c= 2 * $y;
	ImageLine ($im, 100 + $y *$largeur2 ,12 ,100 + $y *$largeur2, 20, $noir);
	imagettftext($im, 9, 0, 100 + $y *$largeur2, 9, $noir, 'images/arial.ttf', "$c");
}

// on dessine un trait vertical
ImageLine ($im, 100, 20,100, $hauteur, $noir); 


// légende
imagettftext($im, 10, 0, $largeur-130, $hauteur -120, $noir, 'images/arial.ttf', strtoupper("$moislettres $year"));
imagettftext($im, 10, 0, $largeur-120, $hauteur -95, $noir, 'images/arial.ttf', $EQ_NOM1);

// garde 1
ImageFilledRectangle ($im, $largeur-120,$hauteur -90, $largeur-110, $hauteur -80, $noir); 
ImageFilledRectangle ($im, $largeur-119, $hauteur -89, $largeur-111, $hauteur -81, $orangedark); 
ImageFilledRectangle ($im, $largeur-118, $hauteur -88, $largeur-112, $hauteur -82,$orange); 
imagettftext($im, 9, 0, $largeur-88, $hauteur -82, $noir, 'images/arial.ttf', "jour: $EQ_DUREE1 h");
        
ImageFilledRectangle ($im, $largeur-120,$hauteur -75, $largeur-110, $hauteur -65, $noir); 
ImageFilledRectangle ($im, $largeur-119, $hauteur -74, $largeur-111, $hauteur -66, $purpledark); 
ImageFilledRectangle ($im, $largeur-118, $hauteur -73, $largeur-112, $hauteur -67,$purple); 
imagettftext($im, 9, 0, $largeur-88, $hauteur -67, $noir, 'images/arial.ttf', "nuit: $EQ_DUREE1 h");

// garde 2
imagettftext($im, 10, 0, $largeur-120, $hauteur -50, $noir, 'images/arial.ttf', $EQ_NOM2);

ImageFilledRectangle ($im, $largeur-120,$hauteur -45, $largeur-110, $hauteur -35, $noir); 
ImageFilledRectangle ($im, $largeur-119, $hauteur -44, $largeur-111, $hauteur -36, $greendark); 
ImageFilledRectangle ($im, $largeur-118, $hauteur -43, $largeur-112, $hauteur -37,$green); 
imagettftext($im, 9, 0, $largeur-88, $hauteur -37, $noir, 'images/arial.ttf', "jour: $EQ_DUREE2 h");
 
if ( $EQ_NUIT2 == 1 ) {    
ImageFilledRectangle ($im, $largeur-120,$hauteur -30, $largeur-110, $hauteur -20, $noir); 
ImageFilledRectangle ($im, $largeur-119, $hauteur -29, $largeur-111, $hauteur -21, $kakidark); 
ImageFilledRectangle ($im, $largeur-118, $hauteur -28, $largeur-112, $hauteur -22, $kaki); 
imagettftext($im, 9, 0, $largeur-88, $hauteur -22, $noir, 'images/arial.ttf', "nuit: $EQ_DUREE2 h");
}


// on parcourt les noms
if ( $i > 0 ) {
  for ($i=0; $i < $nb; $i++) {
    if ( $P_STATUT[$i] == 'SPP' )
 		imagettftext($im, 8,0,0, 33+ $i * $hauteur1 , $namesSPP, 'images/arial.ttf', strtoupper($P_NOM[$i]));
 	else
 		imagettftext($im, 8,0,0, 33+ $i * $hauteur1 , $namesSPV, 'images/arial.ttf', strtoupper($P_NOM[$i]));

  if ( $T[$i] > 0 ) {
	   // on calcule la longueur du baton
       $lT = ceil($T[$i] * $largeur2 / 2); 
       $lJ = ceil($J[$i] * $largeur2 / 2);
	   $lN = ceil($N[$i] * $largeur2 / 2);  
       $lK = ceil($K[$i] * $largeur2 / 2);
     
	   if ( $J[$i] <> 0 ) {   
	   ImageFilledRectangle ($im, 100, $i * $hauteur1 +20, $lJ + 100, ($i +1) * $hauteur1 + 18, $noir); 
       ImageFilledRectangle ($im, 101, $i * $hauteur1 +22, $lJ + 100 -1, ($i +1) * $hauteur1 + 16, $orangedark); 
       ImageFilledRectangle ($im, 102, $i * $hauteur1 +24, $lJ + 100 -2, ($i +1) * $hauteur1 + 14,$orange); 
       }
	   if ( $N[$i] <> 0 ) {     
	   ImageFilledRectangle ($im, 100 +$lJ, $i * $hauteur1 +20, $lJ+$lN + 100 , ($i +1) * $hauteur1 + 18, $noir);
       ImageFilledRectangle ($im, 101 +$lJ, $i * $hauteur1 +22, $lJ+$lN + 100 -1, ($i +1) * $hauteur1 + 16, $purpledark); 
       ImageFilledRectangle ($im, 102 +$lJ, $i * $hauteur1 +24, $lJ+$lN + 100 -2 , ($i +1) * $hauteur1 + 14,$purple); 
       }
	   if ( $K[$i] <> 0 ) {   
	   ImageFilledRectangle ($im, 100 +$lJ+$lN, $i * $hauteur1 +20, $lJ+$lN+$lK + 100, ($i +1) * $hauteur1 + 18, $noir); 
       ImageFilledRectangle ($im, 101 +$lJ+$lN, $i * $hauteur1 +22, $lJ+$lN+$lK + 100 -1, ($i +1) * $hauteur1 + 16, $greendark); 
       ImageFilledRectangle ($im, 102 +$lJ+$lN, $i * $hauteur1 +24, $lJ+$lN+$lK + 100 -2, ($i +1) * $hauteur1 + 14,$green); 
       }    
	   if ( $L[$i] <> 0 ) {   
	   ImageFilledRectangle ($im, 100 +$lJ+$lN+$lK, $i * $hauteur1 +20, $lT + 100, ($i +1) * $hauteur1 + 18, $noir); 
       ImageFilledRectangle ($im, 101 +$lJ+$lN+$lK, $i * $hauteur1 +22, $lT + 100 -1, ($i +1) * $hauteur1 + 16, $kakidark); 
       ImageFilledRectangle ($im, 102 +$lJ+$lN+$lK, $i * $hauteur1 +24, $lT + 100 -2, ($i +1) * $hauteur1 + 14,$kaki); 
       }

	   ImageFilledRectangle ($im, $lT + 100, $i * $hauteur1 +20, $lT + 101, ($i +1) * $hauteur1 + 18, $noir);
	
       $heures=$EQ_DUREE1*$J[$i]+$EQ_DUREE1*$N[$i]+$EQ_DUREE2*$K[$i]+$EQ_DUREE2*$L[$i];
	   if ($DISPOS[$i] > 0 ) $ratio= round(100 * $heures / $DISPOS[$i],0);
       if ( $heures > 0 ) imagettftext($im, 9,0, $lT + 107, $i * $hauteur1 +35, $noir, 'images/arial.ttf', $heures."h");
       if (($DISPOS[$i] > 0 ) and ( $P_STATUT[$i] <> 'SPP' ))
		imagettftext($im, 7,0, $lT + 137, $i * $hauteur1 +35, $noir, 'images/arial.ttf', "(".$ratio."%)");
	}
  }
}
// on dessine le tout
 Imagepng ($im);
?>
