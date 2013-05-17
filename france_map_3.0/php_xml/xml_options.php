<?php
include ('settings.php');

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Envoi XML Datas
echo '<?xml version="1.0" encoding="UTF-8" ?>'.retour;
echo tab.'<options>'.retour;

echo tab.tab.'<couleurDep>'.retour;
echo tab.tab.tab.'<color0>0x'.$couleur0.'</color0>'.retour;
echo tab.tab.tab.'<color1>0x'.$couleur1.'</color1>'.retour;
echo tab.tab.tab.'<color2>0x'.$couleur2.'</color2>'.retour;
echo tab.tab.tab.'<color3>0x'.$couleur3.'</color3>'.retour;
echo tab.tab.tab.'<color4>0x'.$couleur4.'</color4>'.retour;
echo tab.tab.tab.'<color5>0x'.$couleur5.'</color5>'.retour;
echo tab.tab.tab.'<color6>0x'.$couleur6.'</color6>'.retour;
echo tab.tab.tab.'<color7>0x'.$couleur7.'</color7>'.retour;
echo tab.tab.tab.'<color8>0x'.$couleur8.'</color8>'.retour;
echo tab.tab.tab.'<color9>0x'.$couleur9.'</color9>'.retour;
echo tab.tab.tab.'<color10>0x'.$couleur10.'</color10>'.retour;
echo tab.tab.tab.'<color11>0x'.$couleur11.'</color11>'.retour;
echo tab.tab.'</couleurDep>'.retour;

echo tab.tab.'<couleurBackground>0x'.$backgroundCouleur.'</couleurBackground>'.retour;
echo tab.tab.'<unLabel>'.$unLabel.'</unLabel>'.retour;

if ($utiliserPageCible == true) {
	echo tab.tab.'<utiliserPageCible>true</utiliserPageCible>'.retour;
} else {
	echo tab.tab.'<utiliserPageCible>false</utiliserPageCible>'.retour;
}

if ($showOmbre == true) {
	echo tab.tab.'<showOmbre>true</showOmbre>'.retour;
} else {
	echo tab.tab.'<showOmbre>false</showOmbre>'.retour;
}


echo tab.tab.'<urlPageCible>'.$urlPageCible.'</urlPageCible>'.retour;
echo tab.tab.'<variableCible>'.$variableCible.'</variableCible>'.retour;
echo tab.tab.'<couleurInfobulle>0x'.$couleurInfobulle.'</couleurInfobulle>'.retour;
echo tab.tab.'<couleurLegendes>0x'.$couleurLegendes.'</couleurLegendes>'.retour;
echo tab.tab.'<couleurTexteInfobulle>0x'.$couleurTexteInfobulle.'</couleurTexteInfobulle>'.retour;
echo tab.tab.'<couleurContourDepartements>0x'.$couleurContourDepartements.'</couleurContourDepartements>'.retour;
echo tab.tab.'<couleurContourRegions>0x'.$couleurContourRegions.'</couleurContourRegions>'.retour;
echo tab.tab.'<afficheFirst>'.$afficheFirst.'</afficheFirst>'.retour;


if ($tranchesAuto == true) {
	echo tab.tab.'<tranchesAuto>true</tranchesAuto>'.retour;
} else {
	echo tab.tab.'<tranchesAuto>false</tranchesAuto>'.retour;
}
echo tab.tab.'<tranche1>'.$tranche1.'</tranche1>'.retour;
echo tab.tab.'<tranche2>'.$tranche2.'</tranche2>'.retour;
echo tab.tab.'<tranche3>'.$tranche3.'</tranche3>'.retour;
echo tab.tab.'<tranche4>'.$tranche4.'</tranche4>'.retour;
echo tab.tab.'<tranche5>'.$tranche5.'</tranche5>'.retour;
echo tab.tab.'<tranche6>'.$tranche6.'</tranche6>'.retour;
echo tab.tab.'<tranche7>'.$tranche7.'</tranche7>'.retour;
echo tab.tab.'<tranche8>'.$tranche8.'</tranche8>'.retour;
echo tab.tab.'<tranche9>'.$tranche9.'</tranche9>'.retour;
echo tab.tab.'<tranche10>'.$tranche10.'</tranche10>'.retour;

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Connexion à MySql
$connexSql = mysql_connect($hote , $utilisateur, $motDePasse);
if (!$connexSql) {
    die('Non connect&eacute; : ' . mysql_error());
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Connexion à la base de données
$dbConnect = mysql_select_db($baseDeDonnees, $connexSql);
if (!$dbConnect) {
    die ('Impossible d\'utiliser la base : ' . mysql_error());
}


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Liste des villes
$sqlVilles = mysql_query($query_membres." GROUP BY `$champVille`");
if (!$sqlVilles) {
	die('Impossible d\'ex&eacute;cuter la requ&ecirc;te : ' . mysql_error());
}

echo tab.tab.'<villes>'.retour;

while ($datasVilles = mysql_fetch_assoc($sqlVilles)) {

	$ville = htmlspecialchars($datasVilles[$champVille], ENT_QUOTES);
	$ville = utf8_encode($ville);
	$ville = str_replace(array("\r", "\n"), array('', ''), $ville);
	$ville = stripslashes($ville);
	
	if (trim($ville) != '') {
		echo tab.tab.tab.'<ville>' . strtoupper($ville) . '</ville>'.retour;
		
		
	}
}

echo tab.tab.'</villes>'.retour;


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Liste des membres
// requete definie dans settings.php

$sqlMembres = mysql_query($query_membres.$order_membres);
if (!$sqlMembres) {
	die('Impossible d\'ex&eacute;cuter la requ&ecirc;te : ' . mysql_error());
}

echo tab.tab.'<membres>'.retour;

while ($datasMembres = mysql_fetch_assoc($sqlMembres)) {
	echo tab.tab.tab.'<membre>'.retour;
	
	// Id
	echo tab.tab.tab.tab.'<id>' . intval($datasMembres[$champId]) . '</id>'.retour;

	// Nom
	$nom = htmlspecialchars($datasMembres[$champNom], ENT_QUOTES);
	$nom = utf8_encode($nom);
	$nom = str_replace(array("\r", "\n"), array('', ''), $nom);
	$nom = stripslashes($nom);
	
	if (trim($nom) != '') {
		echo tab.tab.tab.tab.'<nom>' . $nom . '</nom>'.retour;
	}

	// Cp
	if (is_numeric($datasMembres[$champCodePostal])) {
	
		if (trim(strtolower($datasMembres[$champPays])) == 'france') {
			
			// Corse
			if (substr($datasMembres[$champCodePostal], 0, 2) == '20') {
				if (substr($datasMembres[$champCodePostal], 0, 3) == '201') {
					echo tab.tab.tab.tab.'<cp>2A</cp>'.retour;
				} else if (substr($datasMembres[$champCodePostal], 0, 3) == '202' || substr($datasMembres[$champCodePostal], 0, 3) == '206') {
					echo tab.tab.tab.tab.'<cp>2B</cp>'.retour;
				}
			} else {
			
				if (strlen($datasMembres[$champCodePostal]) == 5) { // Code Postal à 5 chiffres
					//echo intval(substr($row[$champCodePostal], 0, 2)).'<br />';
					echo tab.tab.tab.tab.'<cp>' . intval(substr($datasMembres[$champCodePostal], 0, 2)) . '</cp>'.retour;
				}
				else if (strlen($datasMembres[$champCodePostal]) == 4) { // Code Postal à 4 chiffres
					//echo intval(substr($row[$champCodePostal], 0, 1)).'<br />';
					echo tab.tab.tab.tab.'<cp>' . intval(substr($datasMembres[$champCodePostal], 0, 1)) . '</cp>'.retour;
				}
				else if (strlen($datasMembres[$champCodePostal]) == 3) { // Code Postal à 3 chiffres
					//echo intval(substr($row[$champCodePostal], 0, 2)).'<br />';
					echo tab.tab.tab.tab.'<cp>' . intval(substr($datasMembres[$champCodePostal], 0, 2)) . '</cp>'.retour;
				}
				else if (strlen($datasMembres[$champCodePostal]) == 2) { // Code Postal à 2 chiffres
					//echo intval($row[$champCodePostal]).'<br />';
					echo tab.tab.tab.tab.'<cp>' . intval($datasMembres[$champCodePostal]) . '</cp>'.retour;
				}
				else if (strlen($datasMembres[$champCodePostal]) == 1) { // Code Postal à 1 chiffre
					//echo intval($row[$champCodePostal]).'<br />';
					echo tab.tab.tab.tab.'<cp>' . intval($datasMembres[$champCodePostal]) . '</cp>'.retour;
				}
				
			}
			
		} else {
			// pays étanger
			echo tab.tab.tab.tab.'<cp>99</cp>'.retour;
		}
		
	} else { // Hors France

		// Gestion des DOM TOM 97// 98
		if ($datasMembres[$champCodePostal] != '') {
			// Gestion des DOM TOM
			if (intval(substr($datasMembres[$champCodePostal], 0, 2)) == 97) {
				echo tab.tab.tab.tab.'<cp>97</cp>'.retour;
			}
			else if (intval(substr($datasMembres[$champCodePostal], 0, 2)) == 98) {
				echo tab.tab.tab.tab.'<cp>98</cp>'.retour;
			}
			else {
				// pays étanger
				echo tab.tab.tab.tab.'<cp>99</cp>'.retour;
			}
		}
	}
	echo tab.tab.tab.'</membre>'.retour;
}
echo tab.tab.'</membres>'.retour;

echo tab.'</options>'.retour; 
?>