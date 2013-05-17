<?php
include ('settings.php');

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Tableau des d�partements
$tabDep = array();
for ($i=0; $i<100; $i++) {
	$tabDep[$i] = 0;
}
$tabDep['2A']= 0;
$tabDep['2B']= 0;

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Connexion � MySql
$connexSql = mysql_connect($hote , $utilisateur, $motDePasse);
if (!$connexSql) {
    die('Non connect&eacute; : ' . mysql_error());
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Connexion � la base de donn�es
$dbConnect = mysql_select_db($baseDeDonnees, $connexSql);
if (!$dbConnect) {
    die ('Impossible d\'utiliser la base : ' . mysql_error());
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
//Requete d�finie dans settings.php
$sqlDatas = mysql_query($query_membres.$order_membres);
if (!$sqlDatas) {
   die('Impossible d\'ex&eacute;cuter la requ&ecirc;te sqlDatas ' . mysql_error());
}

while ($row = mysql_fetch_array($sqlDatas)) {

	if (is_numeric($row[$champCodePostal])) {
	
		if (trim(strtolower($row[$champPays])) == 'france') {
			
			// Corse
			if (substr($row[$champCodePostal], 0, 2) == '20') {
				if (substr($row[$champCodePostal], 0, 3) == '201' || substr($row[$champCodePostal], 0, 3) == '200') {
					$tabDep['2A']++;
				} else if (substr($row[$champCodePostal], 0, 3) == '202' || substr($row[$champCodePostal], 0, 3) == '206') {
					$tabDep['2B']++;
				}
			} else {
				if (strlen($row[$champCodePostal]) == 5) { // Code Postal � 5 chiffres
					//echo intval(substr($row[$champCodePostal], 0, 2)).'<br />';
					$tabDep[ intval(substr($row[$champCodePostal], 0, 2)) ]++;
				}
				else if (strlen($row[$champCodePostal]) == 4) { // Code Postal � 4 chiffres
					//echo intval(substr($row[$champCodePostal], 0, 1)).'<br />';
					$tabDep[ intval(substr($row[$champCodePostal], 0, 1)) ]++;
				}
				else if (strlen($row[$champCodePostal]) == 3) { // Code Postal � 3 chiffres
					//echo intval(substr($row[$champCodePostal], 0, 2)).'<br />';
					$tabDep[ intval(substr($row[$champCodePostal], 0, 2)) ]++;
				}
				else if (strlen($row[$champCodePostal]) == 2) { // Code Postal � 2 chiffres
					//echo intval($row[$champCodePostal]).'<br />';
					$tabDep[ intval($row[$champCodePostal]) ]++;
				}
				else if (strlen($row[$champCodePostal]) == 1) { // Code Postal � 1 chiffre
					//echo intval($row[$champCodePostal]).'<br />';
					$tabDep[ intval($row[$champCodePostal]) ]++;
				}
			}
			
		} else {
			// pays �tanger
			$tabDep[99]++;
		}
		
	} else { // Hors France

		// Gestion des DOM TOM 97// 98
		if ($row[$champCodePostal] != '') {
			// Gestion des DOM TOM
			if (intval(substr($row[$champCodePostal], 0, 2)) == 97) {
				$tabDep[97]++;
			}
			else if (intval(substr($row[$champCodePostal], 0, 2)) == 98) {
				$tabDep[98]++;
			}
			else {
				// pays �tanger
				$tabDep[99]++;
			}
		}
	}
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Fermeture connexion
mysql_close($connexSql);

// Supprime les entr�es inutiles du tableau
unset($tabDep[0]);
unset($tabDep[20]);
unset($tabDep[96]);

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Envoi XML Datas
echo '<?xml version="1.0" encoding="UTF-8" ?>'.retour;
echo tab.'<departements>'.retour;

foreach( $tabDep as $key => $value )  {
	//if ($key != 0) {
		echo tab.tab.'<dep'.$key.' name="dep'.$key.'">'.$value.'</dep'.$key.'>'.retour;
	//}
}

echo tab.'</departements>'.retour; 
?>

