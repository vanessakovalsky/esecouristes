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

$myId = $_GET['id'];

$query = $query_membres;
if ( $map_mode < 5 ) $query .= " and p.`$champId` = '$myId'";
else if ( $map_mode < 7 ) $query .= " and e.`$champId` = '$myId'";
else $query .= " and `$champId` = '$myId'";
$sqlDatas = mysql_query($query);

if (!$sqlDatas) {
   die('Impossible d\'ex&eacute;cuter la requ&ecirc;te sqlDatas ' . mysql_error());
}


while ($row = mysql_fetch_array($sqlDatas)) {
	
		// Nom
		$nom = htmlspecialchars($row[$champNom], ENT_QUOTES);
		$nom = utf8_encode($nom);
		$nom = str_replace(array("\r", "\n"), array('', ''), $nom);
		$nom = stripslashes($nom);
		
		echo '<span class="titre">'.$nom.'</span>';
		echo '<br /> ';
		
		// Ville
		$ville = htmlspecialchars($row[$champVille], ENT_QUOTES);
		$ville = utf8_encode($ville);
		$ville = str_replace(array("\r", "\n"), array('', ''), $ville);
		$ville = stripslashes($ville);
		$ville = strtoupper($ville);
		
		echo '<span class="sousTitre">'.$row[$champCodePostal].' '.$ville.'</span>';
		echo '<br /><br />';
		
		// Champs facultatifs
		
		foreach($champFacultatifs as $cle=>$valeur) 
		{ 
			// varFac
			$varFac = htmlspecialchars($row[$cle], ENT_QUOTES);
			$varFac = utf8_encode($varFac);
			$varFac = str_replace(array("\r", "\n"), array('', ''), $varFac);
			$varFac = stripslashes($varFac);
			
			// valeur
			$valeur = htmlspecialchars($valeur, ENT_QUOTES);
			$valeur = utf8_encode($valeur);

			echo '<span class="texteBold">'.$valeur.' :</span> <span class="texte">'.$varFac.'</span><br />';
		} 
		
}



//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Fermeture connexion
mysql_close($connexSql);
?>