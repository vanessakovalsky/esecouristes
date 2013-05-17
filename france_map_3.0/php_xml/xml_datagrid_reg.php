<?php
include ('settings.php');

$regNum = $_GET['reg'];
//$regNum = 18;

// Function qui renvoie un partie de la requete SQL pour selectionner les departements d'une region
$sqlReqDep = '';

switch ($regNum) {
	case "1":
		$sqlReqDep = " (`$champCodePostal` LIKE '67%'
									OR`$champCodePostal` LIKE '68%')
									";
		break;
		
	case "2":
		$sqlReqDep = "( `$champCodePostal` LIKE '33%'
								OR`$champCodePostal` LIKE '24%'
								OR`$champCodePostal` LIKE '40%'
								OR`$champCodePostal` LIKE '47%'
								OR`$champCodePostal` LIKE '64%')
								";
		break;
		
	case "3":
		$sqlReqDep = "( `$champCodePostal` LIKE '03%'
									OR`$champCodePostal` LIKE '43%'
									OR`$champCodePostal` LIKE '15%'
									OR`$champCodePostal` LIKE '63%')
									";
		break;
		
	case "4":
		$sqlReqDep = " (`$champCodePostal` LIKE '14%'
									OR`$champCodePostal` LIKE '50%'
									OR`$champCodePostal` LIKE '61%')
									";
		break;
		
	case "5":
		$sqlReqDep = "( `$champCodePostal` LIKE '21%'
									OR`$champCodePostal` LIKE '71%'
									OR`$champCodePostal` LIKE '89%'
									OR`$champCodePostal` LIKE '58%')
									";
		break;
		
	case "6":
		$sqlReqDep = " (`$champCodePostal` LIKE '22%'
									OR`$champCodePostal` LIKE '29%'
									OR`$champCodePostal` LIKE '35%'
									OR`$champCodePostal` LIKE '56%')
									";
		break;
		
	case "7":
		$sqlReqDep = " (`$champCodePostal` LIKE '36%'
									OR`$champCodePostal` LIKE '37%'
									OR`$champCodePostal` LIKE '18%'
									OR`$champCodePostal` LIKE '28%'
									OR`$champCodePostal` LIKE '41%'
									OR`$champCodePostal` LIKE '45%')
									";
		break;
		
	case "8":
		$sqlReqDep = "( `$champCodePostal` LIKE '08%'
									OR`$champCodePostal` LIKE '10%'
									OR`$champCodePostal` LIKE '51%'
									OR`$champCodePostal` LIKE '52%')
									";
		break;
		
	case "9":
		$sqlReqDep = " `$champCodePostal` LIKE '20%'
									";
		break;
		
	case "10":
		$sqlReqDep = " `$champCodePostal` LIKE '97%'
									";
		break;
		
	case "11":
		$sqlReqDep = " (`$champCodePostal` LIKE '25%'
									OR`$champCodePostal` LIKE '39%'
									OR`$champCodePostal` LIKE '70%'
									OR`$champCodePostal` LIKE '90%')
									";
		break;
		
	case "12":
		$sqlReqDep = " (`$champCodePostal` LIKE '27%'
									OR`$champCodePostal` LIKE '76%')
									";
		break;
	
	case "13":
		$sqlReqDep = " (`$champCodePostal` LIKE '77%'
									OR`$champCodePostal` LIKE '78%'
									OR`$champCodePostal` LIKE '91%'
									OR`$champCodePostal` LIKE '92%'
									OR`$champCodePostal` LIKE '93%'
									OR`$champCodePostal` LIKE '94%'
									OR`$champCodePostal` LIKE '95%'
									OR`$champCodePostal` LIKE '75%')
									";
		break;
		
	case "14":
		$sqlReqDep = " (`$champCodePostal` LIKE '11%'
									OR`$champCodePostal` LIKE '30%'
									OR`$champCodePostal` LIKE '34%'
									OR`$champCodePostal` LIKE '48%'
									OR`$champCodePostal` LIKE '66%')
									";
		break;
		
	case "15":
		$sqlReqDep = " (`$champCodePostal` LIKE '19%'
									OR`$champCodePostal` LIKE '23%'
									OR`$champCodePostal` LIKE '87%')
									";
		break;
	
	case "16":
		$sqlReqDep = " (`$champCodePostal` LIKE '54%'
									OR`$champCodePostal` LIKE '55%'
									OR`$champCodePostal` LIKE '57%'
									OR`$champCodePostal` LIKE '88%')
									";
		break;
		
	case "17":
		$sqlReqDep = " (`$champCodePostal` LIKE '09%'
									OR`$champCodePostal` LIKE '12%'
									OR`$champCodePostal` LIKE '31%'
									OR`$champCodePostal` LIKE '32%'
									OR`$champCodePostal` LIKE '46%'
									OR`$champCodePostal` LIKE '65%'
									OR`$champCodePostal` LIKE '81%'
									OR`$champCodePostal` LIKE '82%')
									";
		break;
		
	case "18":
		$sqlReqDep = " (`$champCodePostal` LIKE '59%'
									OR`$champCodePostal` LIKE '62%')
									";
			break;						
			
	case "19":
		$sqlReqDep = "( `$champCodePostal` LIKE '44%'
									OR`$champCodePostal` LIKE '49%'
									OR`$champCodePostal` LIKE '53%'
									OR`$champCodePostal` LIKE '72%'
									OR`$champCodePostal` LIKE '85%')
									";
		break;
		
	case "20":
		$sqlReqDep = " (`$champCodePostal` LIKE '02%'
									OR`$champCodePostal` LIKE '60%'
									OR`$champCodePostal` LIKE '80%')
									";
		break;
		
	case "21":
		$sqlReqDep = " (`$champCodePostal` LIKE '16%'
									OR`$champCodePostal` LIKE '17%'
									OR`$champCodePostal` LIKE '79%'
									OR`$champCodePostal` LIKE '86%')
									";
		break;
		
	case "22":
		$sqlReqDep = " (`$champCodePostal` LIKE '04%'
									OR`$champCodePostal` LIKE '05%'
									OR`$champCodePostal` LIKE '06%'
									OR`$champCodePostal` LIKE '13%'
									OR`$champCodePostal` LIKE '83%'
									OR`$champCodePostal` LIKE '84%')
									";
		break;
		
	case "23":
		$sqlReqDep = " (`$champCodePostal` LIKE '01%'
									OR`$champCodePostal` LIKE '07%'
									OR`$champCodePostal` LIKE '26%'
									OR`$champCodePostal` LIKE '38%'
									OR`$champCodePostal` LIKE '42%'
									OR`$champCodePostal` LIKE '69%'
									OR`$champCodePostal` LIKE '73%'
									OR`$champCodePostal` LIKE '74%')
									";
		break;
		
	case "24":
		$sqlReqDep = " `$champCodePostal` LIKE '98%'
									";
		break;
		
	case "25":
		$sqlReqDep = " 1=1 ";
		break;
}


	
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

$sqlDatas = mysql_query($query_membres." and ".$sqlReqDep);
							
if (!$sqlDatas) {
   die('Impossible d\'ex&eacute;cuter la requ&ecirc;te sqlDatas ' . mysql_error());
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

