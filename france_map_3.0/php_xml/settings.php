<?php
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
/*
FRANCE-MAP V3.0 - 18/09/2008
Copyright (C) 2008 PAYROUSE NICOLAS - FRANCE-MAP.FR
Pour toutes questions : http://www.france-map/forum/
Merci.

INFORMATIONS SUR CE FICHIER :
Vous trouverez dansz ce fichier tous les paramètres à definir pour personnaliser et configurer votre carte
*/

include_once("../../config.php");
check_all(27);

if ( isset($_SESSION["map_mode"])) $map_mode=$_SESSION["map_mode"];
else $map_mode=0;

define ('retour', "\r\n"); // Ne pas modifier
define ('tab', "\t"); // Ne pas modifier
$champFacultatifs = array(); // Ne pas modifier
$variableCible = 'id' ; // Ne pas modifier

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Paramètres de la base de données

include_once("../../conf/sql.php");
$hote	=  $server;	// Nom de votre serveur SQL
$utilisateur	= $user;	// Nom d'utilisateur SQL
$motDePasse = $password;	// Mot de passe SQL
$baseDeDonnees = $database;	// Nom de la base de données

$tableUtilisee = 'pompier' ;	// Nom de la table utilisée

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Paramètres de champs

// Nom des champs de votre base // CHAMPS OBLIGATOIRES !
// Vous devez avoir AU MOINS ces champs dans votre base Mysql pour utiliser ce script.
// Modifier les valeurs des varaibles ci-dessous avec les noms des champs de votre base Mysql.

$champId = 'P_ID';
$champNom = 'P_NOM';	
$champCodePostal = 'P_ZIP_CODE';
$champVille = 'P_CITY';
$champPays = 'P_COUNTRY';

if ( $map_mode > 4 ) {
  if ( $map_mode == 8 ) $champId = 'V_ID';
  else if ( $map_mode == 7 ) $champId = 'MA_ID';
  else $champId = 'E_CODE';
  $champCodePostal = 'S_ZIP_CODE';
  $champVille = 'S_CITY';
}

// Champs facultatifs
// Vous pouvez désormais rajouter autant de champ que nécéssaire.
// Il suffit de remplir le tableau $champFacultatifs, en mettant :
// en Key : le nom du champ dans votre base mysql
// en Value : le nom qui apparaitra dans le module Flash.
//
// Exemple : vous voulez ajouter le champ 'CLI_Adresse' dans le descriptif d'un membre, ajouter ci dessous la ligne :
// $champFacultatifs['adresse'] = 'Adresse du Client';
//
// Vous pouvez ainsi ajouter tous les champs que vous voulez !

//$champFacultatifs['prenom'] = 'P_PRENOM';
//$champFacultatifs['sexe'] = 'P_SEXE';	 


if ( $map_mode==0 ) { // opérations de secours
	$query_membres = "SELECT p.".$champId.", p.".$champCodePostal.", concat(p.P_NOM,' ',p.P_PRENOM) as ".$champNom.", 
				p.".$champVille.", 'france' as `$champPays`
				FROM pompier p, evenement e, evenement_participation ep, type_evenement te, evenement_horaire eh
				where e.E_CODE = ep.E_CODE
				and ep.E_CODE = eh.E_CODE
				and ep.EH_ID = eh.EH_ID
				and e.E_CANCELED=0
				and e.TE_CODE = te.TE_CODE
				and te.CEV_CODE = 'C_SEC'
				and p.P_ID = ep.P_ID
				and ( date_format(eh.EH_DATE_DEBUT,'%Y%m%d') = date_format(now(),'%Y%m%d') 
				or date_format(eh.EH_DATE_FIN,'%Y%m%d') = date_format(now(),'%Y%m%d'))";
	$order_membres = " ORDER BY ".$champCodePostal;
}
else if ( $map_mode==1 ) { // autres opérations
	$query_membres = "SELECT p.".$champId.", p.".$champCodePostal.", concat(p.P_NOM,' ',p.P_PRENOM) as ".$champNom.", 
				p.".$champVille.", 'france' as `$champPays`
				FROM pompier p, evenement e, evenement_participation ep, type_evenement te, evenement_horaire eh
				where e.E_CODE = ep.E_CODE
				and ep.E_CODE = eh.E_CODE
				and ep.EH_ID = eh.EH_ID
				and e.E_CANCELED=0
				and e.TE_CODE = te.TE_CODE
				and te.CEV_CODE = 'C_OPE'
				and p.P_ID = ep.P_ID
				and ( date_format(eh.EH_DATE_DEBUT,'%Y%m%d') = date_format(now(),'%Y%m%d') 
				or date_format(eh.EH_DATE_FIN,'%Y%m%d') = date_format(now(),'%Y%m%d'))";
	$order_membres = " ORDER BY ".$champCodePostal;
}
else if ( $map_mode==2 ) { // formations
	$query_membres = "SELECT p.".$champId.", p.".$champCodePostal.", concat(p.P_NOM,' ',p.P_PRENOM) as ".$champNom.", 
				p.".$champVille.", 'france' as `$champPays`
				FROM pompier p, evenement e, evenement_participation ep, type_evenement te, evenement_horaire eh
				where e.E_CODE = ep.E_CODE
				and ep.E_CODE = eh.E_CODE
				and ep.EH_ID = eh.EH_ID
				and e.E_CANCELED=0
				and e.TE_CODE = te.TE_CODE
				and te.CEV_CODE = 'C_FOR'
				and p.P_ID = ep.P_ID
				and ( date_format(eh.EH_DATE_DEBUT,'%Y%m%d') = date_format(now(),'%Y%m%d') 
				or date_format(eh.EH_DATE_FIN,'%Y%m%d') = date_format(now(),'%Y%m%d'))";
	$order_membres = " ORDER BY ".$champCodePostal;
}
else if ( $map_mode==3 ) { // membres
	$query_membres="SELECT
               concat(P_NOM,' ',P_PRENOM) as ".$champNom.",
               `$champCodePostal`,
               `$champVille`,
               'france' as `$champPays`,
			   `$champId`
			    FROM `$tableUtilisee`
			    where P_OLD_MEMBER=0
				and P_STATUT <> 'EXT'";
	$order_membres = " ORDER BY `$champNom`";
}
else if ( $map_mode==4 ) { // cadres de veille opérationnelle
	$query_membres="SELECT
               concat(p.P_NOM,' ',p.P_PRENOM, ' (', s.S_CODE,')') as ".$champNom.",
               p.`$champCodePostal`,
               p.`$champVille`,
               'france' as `$champPays`,
			   p.`$champId`
			   FROM `$tableUtilisee` p, section_role sr, groupe g, section s
			   where p.P_OLD_MEMBER=0
			   and s.S_ID = sr.S_ID
			   and sr.P_ID = p.P_ID
			   and sr.GP_ID = g.GP_ID
			   and g.GP_ID=107
			   and p.P_STATUT <> 'EXT'";
	$order_membres = " ORDER BY `$champNom`";
}
else if ( $map_mode==5 ) { //tous événements
	$query_membres="select  e.E_CODE as ".$champId.",
		s.S_ZIP_CODE as ".$champCodePostal.",
		concat(e.TE_CODE, ' - ', e.E_LIBELLE,' ', e.E_LIEU) as ".$champNom.", 
		s.S_CITY as ".$champVille.",
		'france' as `$champPays`
		from evenement e, section s, evenement_horaire eh
 	    where e.S_ID = s.S_ID
 	    and e.E_CODE = eh.E_CODE
		and e.E_CANCELED = 0 
		and ( date_format(eh.EH_DATE_DEBUT,'%Y%m%d') = date_format(now(),'%Y%m%d') 
				or date_format(eh.EH_DATE_FIN,'%Y%m%d') = date_format(now(),'%Y%m%d'))";
	$order_membres = " ORDER BY `$champNom`";
}
else if ( $map_mode==6 ) { // DPS
	$query_membres="select  e.E_CODE as ".$champId.",
		s.S_ZIP_CODE as ".$champCodePostal.",
		concat(e.E_LIBELLE,' ', e.E_LIEU) as ".$champNom.", 
		s.S_CITY as ".$champVille.",
		'france' as `$champPays`
		from evenement e, section s, evenement_horaire eh
 	    where e.S_ID = s.S_ID
 	    and e.E_CODE = eh.E_CODE
		and e.E_CANCELED = 0
		and e.TE_CODE = 'DPS'
		and ( date_format(eh.EH_DATE_DEBUT,'%Y%m%d') = date_format(now(),'%Y%m%d') 
				or date_format(eh.EH_DATE_FIN,'%Y%m%d') = date_format(now(),'%Y%m%d'))";
	$order_membres = " ORDER BY `$champNom`";
}
else if ( $map_mode==7 ) { // matériel national
	$query_membres="select m.MA_ID as ".$champId.",
		s.S_ZIP_CODE as ".$champCodePostal.",
		concat(tm.TM_CODE, ' ', m.MA_MODELE, ' ', m.MA_NUMERO_SERIE ) as ".$champNom.", 
		s.S_CITY as ".$champVille.", 
		'france' as `$champPays`
		from  materiel m
		join type_materiel tm on tm.TM_ID = m.TM_ID
		join section s on m.S_ID = s.S_ID
		where m.MA_EXTERNE = 1";
	$order_membres = " ORDER BY `$champNom`";
}
else if ( $map_mode==8 ) { // véhicules
	$query_membres="select v.V_ID as ".$champId.",
		s.S_ZIP_CODE as ".$champCodePostal.",
		concat(v.TV_CODE, ' ', v.V_MODELE, ' ', v.V_IMMATRICULATION ) as ".$champNom.", 
		s.S_CITY as ".$champVille.", 
		'france' as `$champPays`
		from  vehicule v
		join section s on v.S_ID = s.S_ID
		where v.VP_ID not in ('REF','VEN','DET')";
	$order_membres = " ORDER BY `$champNom`";
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Paramètres de couleur de la carte

// Couleur de fond de la carte
// Utiliser le format héxadécimal
$backgroundCouleur = 'EAEAEA';

// Couleur de l'infobulle
// Elle apparait au survol d'un département ou d'une région
// Utiliser le format héxadécimal
$couleurInfobulle = '313131';

// Couleur du texte de l'infobulle
// Utiliser le format héxadécimal
$couleurTexteInfobulle = 'DCDCDC';

// Couleur des légendes
// DOM TOM Région parisienne...
// Utiliser le format héxadécimal
$couleurLegendes = '353535';

// Couleur du contour des départements
// Utiliser le format héxadécimal
$couleurContourDepartements = '595959';

// Couleur du contour des régions
// Utiliser le format héxadécimal
$couleurContourRegions = '3C3C3C';

// Couleurs des départements
// Utiliser le format héxadécimal
// pour vous aider à choisir vos couleurs :
$couleur0 = 'FFFFFF';
$couleur1 = 'FFFF9B';
$couleur2 = 'FFFA20';
$couleur3 = 'FFD700';
$couleur4 = 'FF9C00';
$couleur5 = 'FF6200';
$couleur6 = 'FF2F00';
$couleur7 = 'FF0500';
$couleur8 = 'FF004A';
$couleur9 = 'FE00C9';
$couleur10 = 'EB00FF';
$couleur11 = 'BC00FF';


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Paramètres des tranches de population

// Si vous voulez que les tranches soient calculés automatiquement : $tranchesAuto = true;
// Si vous voulez que les tranches NE SOIENT PAS calculés automatiquement : $tranchesAuto = false;
$tranchesAuto = true;

// Si vous avez défini $tranchesAuto = false, remplissez les tranches ci dessous avec les valeurs que vous voulez.
$tranche1 = 1;
$tranche2 = 3;
$tranche3 = 6;
$tranche4 = 9;
$tranche5 = 15;
$tranche6 = 25;
$tranche7 = 50;
$tranche8 = 150;
$tranche9 = 300;
$tranche10 = 600;

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// Options 

// Si vous voulez qu'il y ai un lien vers une fiche de votre site
// Si cette option est TRUE, un bouton apparait en haut du descriptif membre.
$utiliserPageCible = true;

// Si l'option utiliserPageCible est TRUE, veuillez renseigner l'url de cette page // URL relative au fichier map.php
if ( $map_mode == 8 ) $urlPageCible = '../upd_vehicule.php' ;
else if ( $map_mode == 7 ) $urlPageCible = '../upd_materiel.php' ;
else if ( $map_mode > 4 ) $urlPageCible = '../evenement_display.php' ;
else $urlPageCible = '../upd_personnel.php' ;

// Le terme que vous voulez voir apparaitre sur la carte
// par exemple : club, client, prospect, magasin...
// Laisser ce terme au singulier
if ( $map_mode == 7 ) $unLabel = 'article' ;
else if ( $map_mode == 8 ) $unLabel = 'véhicule' ;
else if ( $map_mode > 4 ) $unLabel = 'événement';
else $unLabel = 'secouriste' ;


// Si vous voulez afficher à l'ouverture de la carte les régions ou les départements
// Si vous voulez voir en premier les départements : $afficheFirst = 'dep';
// Si vous voulez voir en premier les régions : $afficheFirst = 'reg';
$afficheFirst = 'dep';

// Afficher ou non l'ombre qui entoure la carte
$showOmbre = true;



?>