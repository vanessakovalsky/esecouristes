<?php

include ('settings.php');

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

// query defined in settings
$mysql_query=$query_membres;
			  
// filtre departement			  
$myCp = $_GET['dep'];
if ($myCp == '2A') $mysql_query .= " AND (`$champCodePostal` LIKE '201%' OR  `$champCodePostal` LIKE '200%' )";
else if ($myCp == '2B') $mysql_query .= " AND (`$champCodePostal` LIKE '202%' OR `$champCodePostal` LIKE '206%' )";
else $mysql_query .= " AND `$champCodePostal` LIKE '$myCp%'";

$mysql_query .= $order_membres;
				  		  
$sqlDatas = mysql_query($mysql_query);

if (!$sqlDatas) {
   echo mysql_error();
}
echo '<?xml version="1.0" encoding="UTF-8" ?>'.retour;
echo tab.'<dataProvider>'.retour;

while ($row = mysql_fetch_array($sqlDatas)) {
	
		// Nom
		$nom = htmlspecialchars($row[$champNom], ENT_QUOTES);
		$nom = utf8_encode($nom);
		$nom = str_replace(array("\r", "\n"), array('', ''), $nom);
		$nom = stripslashes($nom);
		
		// Ville
		$ville = htmlspecialchars($row[$champVille], ENT_QUOTES);
		$ville = utf8_encode($ville);
		$ville = str_replace(array("\r", "\n"), array('', ''), $ville);
		$ville = stripslashes($ville);
		$ville = strtoupper($ville);
		
		echo tab.tab.'<data Id="'.intval($row[$champId]).'" Nom="'.$nom.'" CP="'.$row[$champCodePostal].'" Ville="'.$ville.'" />'.retour;
		
}

echo tab.'</dataProvider>'.retour;

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Fermeture connexion
mysql_close($connexSql);
?>

