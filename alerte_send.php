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
  
include_once ("config.php");
check_all(43);
$id=$_SESSION['id'];

$poste=intval($_GET["poste"]);
$section=intval($_GET["section"]);
$mode=$_GET["mode"];
$dispo=mysql_real_escape_string($_GET["dispo"]);
if (( $mode == 'sms' ) or ( $mode == 'flash' )) check_all(23);

if ( $dispo == '0' ) {
	if ( $poste <> 0 ) { 
	$query="select distinct a.P_ID from pompier a, poste b, qualification c
		where a.P_ID=c.P_ID
		and b.PS_ID=c.PS_ID
		and a.P_OLD_MEMBER=0
		and a.P_STATUT <> 'EXT'
		and b.PS_ID = $poste 
		and c.Q_VAL > 0
		and a.P_SECTION in (".get_family("$section").")";
	}
	else {
 	$query="select distinct P_ID from pompier
 		where P_OLD_MEMBER=0
 		and P_STATUT <> 'EXT'
		and P_SECTION in (".get_family("$section").")";
	}
}
else {
 	if ( $poste <> 0 ) { 
	$query="select distinct a.P_ID from pompier a, poste b, qualification c, disponibilite d
		where a.P_ID=c.P_ID
		and d.P_ID = a.P_ID
		and b.PS_ID=c.PS_ID
		and a.P_OLD_MEMBER=0
		and a.P_STATUT <> 'EXT'
		and b.PS_ID = $poste 
		and d.D_DATE = '".$dispo."'
		and c.Q_VAL > 0
		and d.D_JOUR + d.D_NUIT >= 1 
		and a.P_SECTION in (".get_family("$section").")";
	}
	else {
 	$query="select distinct p.P_ID from pompier p, disponibilite d
 		where d.P_ID =p.P_ID
 		 and p.P_OLD_MEMBER=0
 		 and p.P_STATUT <> 'EXT'
 		 and d.D_DATE = '".$dispo."'
 		 and d.D_JOUR + d.D_NUIT >= 1 
		 and p.P_SECTION in (".get_family("$section").")";
	}
}

$dest=''; $nb1=0;
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result) ) {
 	$dest .= $row["P_ID"].",";
 	$nb1++;
} 


$message=str_replace("\'","'",$_GET["message"]);
$message=str_replace("«","",$message);
$message=str_replace("«","",$message);
$message=str_replace("’","'",$message);
$message=str_replace("`","'",$message);
$message=str_replace("%u2019","'",$message);
$message=str_replace("%u20AC","euros",$message);
if (( $mode == 'sms' ) or ( $mode == 'flash' )) {
 
	$phonelist = mySmsGet("$dest",'data');
	$phone_numbers=explode(",", $phonelist);
 	$nb_phone_numbers =  count($phone_numbers);
	$nb = mySmsGet("$dest",'nb');
	$comment=" (Envoyé depuis $cisurl)";
	if ( strlen($message) + strlen($comment) < 120 ) $message .= $comment;
	$sent=0;
 	
 	//===================================================================
	// sms.pictures-on-line.net
	//===================================================================
 	if ( $sms_provider == 1 ) {
	   for($i=0; $i < $nb_phone_numbers ; $i++){
		 		$number = $phone_numbers[$i];
		 		$retour = sendSMS_1("$number", "$message", $mode);
				if ( $retour == 'OK' )  {
				 	$sent = $sent +1;
				}
			}
			if ( $sent  <> 0) {
		 		write_msgbox("OK", $star_pic, 
				"Le sms a bien été envoyé à <b>".$sent."</b> numéros de téléphone sur ".$nb."<br>
				<p><font face=courrier-new size=1>Le texte du SMS:<br> ".nl2br($message)."</font>
				<p>en utilisant www.sms.pictures-on-line.net</b>
				<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);	
			}
			else {
				write_msgbox("ERREUR", $error_pic, 
				"Une erreur est survenue lors de l'envoi, en utilisant  sms.pictures-on-line.net.<br>
				Votre référence de crédit (".$sms_password.") est épuisée ou inexistante.<br>
				<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);
			}		
	
    }
    //===================================================================
	// EnvoyerSMS.org 
	//===================================================================
    if ( $sms_provider == 2 ) { 
		/* Messages correspondants aux différents retours possibles de l'API */ 
		$description = array('OK' => 'Message envoyé avec succès',
					 	'ERR_01' => 'Login ou mot de passe incorrect', 
					 	'ERR_02' => 'Manque de paramètres', 
					 	'ERR_03' => 'Crédit insuffisant', 
					 	'ERR_04' => 'Le numéro du destinataire est invalide', 
					 	'ERR_05' => 'Message vide ou trop long (160 caracteres)'); 

		$from = strtoupper(get_nom($id));
		
		$credits = getSMSCredit_2();
		if ( $credits == 0 ) {
		 	write_msgbox("ERREUR", $error_pic, 
			"Vous n'avez plus de crédits chez EnvoyerSMS.org<br>
			<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);
		 
		}
		else if ($credits == 'ERREUR' ) {
		 	write_msgbox("ERREUR", $error_pic, 
			"Impossible de se connecter chez EnvoyerSMS.org<br>
			Vérifiez identifiant et mot de passe dans la configuration
			<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0); 
		}
		else { // on peut envoyer
			for($i=0; $i < $nb_phone_numbers ; $i++){
		 		$number = $phone_numbers[$i];
				$retour=sendSMS_2($number, "$message", "$from" , $mode); 
				if(array_key_exists($retour, $description)) $reponse=$description[$retour]; 
				if ( $retour == 'OK') $sent = $sent +1;
			}
			if ( $sent  > 0) {
				write_msgbox("OK", $star_pic, 
				"Le sms a bien été envoyé à <b>".$sent."</b> numéros de téléphone sur ".$nb."<br>
				<p><font face=courrier-new size=1>Le texte du SMS:<br> ".nl2br($message)."</font>
				<p>Il vous reste:<b> ".getSMSCredit_2()."</b> crédits chez EnvoyerSMS.org</b>
				<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);		
			}
			else {
	 			write_msgbox("ERREUR", $error_pic, 
				"Aucun SMS sur ".$nb." n'a été envoyé.<br>
				Une erreur est survenue lors de l'envoi du SMS via EnvoyerSMS.org:<br>".$reponse."<br>
				<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);
			}
		}
    }
    
    //===================================================================
	// clickatell.com
	//===================================================================
    if ( $sms_provider == 3 ) {   			
		$conn = preg_split("/:/",connectSMS_3());
		if ( $conn[0] == 'KO' ) {
		 		write_msgbox("ERREUR", $error_pic, 
				"Une erreur est survenue lors de la connexion à clickatell.com:<br>".$conn[1]." ".$conn[2]."<br>
				<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);
		 
		}
		else { // on peut envoyer
			for($i=0; $i < $nb_phone_numbers ; $i++){
		 		$number = $phone_numbers[$i];
		 		$retour = sendSMS_3("$conn[1]", "$number", "$message");
				if ( $retour == 'OK' )  {
				 	$sent = $sent +1;
				}
			}
			if ( $sent  <> 0) {
		 		write_msgbox("OK", $star_pic, 
				"Le sms a bien été envoyé à <b>".$sent."</b> numéros de téléphone sur ".$nb."<br>
				<p><font face=courrier-new size=1>Le texte du SMS:<br> ".nl2br($message)."</font>
				<p>Il vous reste:<b> ".getSMSCredit_3($conn[1])."</b> crédits</b>
				<p>en utilisant votre api $sms_api_id de www.clickatell.com</b>
				<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);	
			}
			else {
				write_msgbox("ERREUR", $error_pic, 
				"Une erreur est survenue lors de l'envoi, en utilisant votre api $sms_api_id de www.clickatell.com.<br>
				$retour<br>
				<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);
			}		
		}
	}
	//===================================================================
	// Orange
	//===================================================================
    if ( $sms_provider == 4 ) {   			
		for($i=0; $i < $nb_phone_numbers ; $i++){
		 		$number = $phone_numbers[$i];
		 		$retour = sendSMS_4("$number", "$message");
		 		
				if ( $retour == '200' )  {
				 	$sent = $sent +1;
				}
			}
			if ( $sent  <> 0) {
		 		write_msgbox("OK", $star_pic, 
				"Le sms a bien été envoyé à <b>".$sent."</b> numéros de téléphone sur ".$nb."<br>
				<p><font face=courrier-new size=1>Le texte du SMS:<br> ".nl2br($message)."</font>
				<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);	
			}
			else {
				write_msgbox("ERREUR", $error_pic, 
				"Une erreur est survenue lors de l'envoi, en utilisant votre api $sms_api_id de Orange.<br>
				$retour<br>
				<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);
			}		
		
	}
    //===================================================================
	// save history
	//===================================================================	
	if ( $sent > 0 ) {
		// insérer dans la table smslog
    	$query="insert into smslog (P_ID, S_DATE, S_NB, S_TEXTE) 
	 	select ".$id.", NOW(),'".$sent."',\"".$message."\"";
    	$result=mysql_query($query);
    }
}
else {
	$subject="message de ".ucfirst(get_prenom($id))." ".strtoupper(get_nom($id));

	$nb = mysendmail( "$dest" , $id  , $subject , "$message" );

	write_msgbox("OK", $star_pic, "Le message (de ".get_email($id).") a été envoyé à:
	<br>".$nb." personnes sur ".$nb1."<p><font face=courrier-new size=1>Sujet:[".$cisname."] ".$subject."
	<p>".nl2br($message)."</font><p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);
}

?>
