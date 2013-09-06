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

$dest=$_GET["dest"];
$mode=$_GET["mode"];
if (( $mode == 'sms' ) or ( $mode == 'flash' )) check_all(23);

$message=str_replace("\'","'",$_GET["message"]);
$message=str_replace("�","",$message);
$message=str_replace("�","",$message);
$message=str_replace("�","'",$message);
$message=str_replace("`","'",$message);
$message=str_replace("%u2019","'",$message);
$message=str_replace("%u20AC","euros",$message);

$phpver=phpversion();
if ( $phpver[0] == '5' ) {
  $message=htmlspecialchars_decode($message);
}

if (( $mode == 'sms' ) or ( $mode == 'flash' )) {
 
	$phonelist = mySmsGet("$dest",'data');
	$phone_numbers=explode(",", $phonelist);
 	$nb_phone_numbers =  count($phone_numbers);
	$nb = mySmsGet("$dest",'nb');
	$comment=" (Envoye depuis $cisurl)";
        //$comment_propre = url_encode($comment);
	if ( strlen($message) + strlen($comment) < 160 ) $message .= $comment;
        $message = urlencode($message);
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
				"Le sms a bien �t� envoy� � <b>".$sent."</b> num�ros de t�l�phone sur ".$nb."<br>
				<p><font face=courrier-new size=1>Le texte du SMS:<br> ".nl2br($message)."</font>
				<p>en utilisant www.sms.pictures-on-line.net</b>
				<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);	
			}
			else {
				write_msgbox("ERREUR", $error_pic, 
				"Une erreur est survenue lors de l'envoi, en utilisant  sms.pictures-on-line.net.<br>
				Votre r�f�rence de cr�dit (".$sms_password.") est �puis�e ou inexistante.<br>
				<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);
			}		
	
    }
    //===================================================================
	// EnvoyerSMS.org 
	//===================================================================
    if ( $sms_provider == 2 ) { 
		/* Messages correspondants aux diff�rents retours possibles de l'API */ 
		$description = array('OK' => 'Message envoy� avec succ�s',
					 	'ERR_01' => 'Login ou mot de passe incorrect', 
					 	'ERR_02' => 'Manque de param�tres', 
					 	'ERR_03' => 'Cr�dit insuffisant', 
					 	'ERR_04' => 'Le num�ro du destinataire est invalide', 
					 	'ERR_05' => 'Message vide ou trop long (160 caracteres)'); 

		$from = strtoupper(get_nom($id));
		
		$credits = getSMSCredit_2();
		if ( $credits == 0 ) {
		 	write_msgbox("ERREUR", $error_pic, 
			"Vous n'avez plus de cr�dits chez EnvoyerSMS.org<br>
			<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);
		 
		}
		else if ($credits == 'ERREUR' ) {
		 	write_msgbox("ERREUR", $error_pic, 
			"Impossible de se connecter chez EnvoyerSMS.org<br>
			V�rifiez identifiant et mot de passe dans la configuration
			<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0); 
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
				"Le sms a bien �t� envoy� � <b>".$sent."</b> num�ros de t�l�phone sur ".$nb."<br>
				<p><font face=courrier-new size=1>Le texte du SMS:<br> ".nl2br($message)."</font>
				<p>Il vous reste:<b> ".getSMSCredit_2()."</b> cr�dits chez EnvoyerSMS.org</b>
				<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);		
			}
			else {
	 			write_msgbox("ERREUR", $error_pic, 
				"Aucun SMS sur ".$nb." n'a �t� envoy�.<br>
				Une erreur est survenue lors de l'envoi du SMS via EnvoyerSMS.org:<br>".$reponse."<br>
				<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);
			}
		}
    }
    
    //===================================================================
	// clickatell.com
	//===================================================================
    if ( $sms_provider == 3 ) {   			
		$conn = preg_split('/:/',connectSMS_3());
		if ( $conn[0] == 'KO' ) {
		 		write_msgbox("ERREUR", $error_pic, 
				"Une erreur est survenue lors de la connexion � clickatell.com:<br>".$conn[1]." ".$conn[2]."<br>
				<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);
		 
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
				"Le sms a bien �t� envoy� � <b>".$sent."</b> num�ros de t�l�phone sur ".$nb."<br>
				<p><font face=courrier-new size=1>Le texte du SMS:<br> ".nl2br($message)."</font>
				<p>Il vous reste:<b> ".getSMSCredit_3($conn[1])."</b> cr�dits</b>
				<p>en utilisant votre api $sms_api_id de www.clickatell.com</b>
				<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);	
			}
			else {
				write_msgbox("ERREUR", $error_pic, 
				"Une erreur est survenue lors de l'envoi, en utilisant votre api $sms_api_id de www.clickatell.com.<br>
				$retour<br>
				<p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);
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
				"Le sms a bien �t� envoy� � <b>".$sent."</b> num�ros de t�l�phone sur ".$nb."<br>
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
	// SMS Mode
	//===================================================================
     
    if ( $sms_provider == 5 ) { 
		/* Messages correspondants aux diff�rents retours possibles de l'API */ 
		$description = array('0' => 'Message envoy&eacute; avec succ&egrave;s',
                                     '2' => 'Erreur interne lors de l’envoi du SMS.', 
                                     '11' => 'Reçu : le sms a été reçu par le téléphone portable', 
                                     '13' => 'Délivré opérateur : le sms a été délivré à l’opérateur dont dépend
votre destinataire', 
                                     '31' => 'Erreur interne lors de la requ&ecirc;te', 
                                     '34' => 'Erreur routage : le réseau destinataire n\'a pas été reconnu',
                                     '35' => 'Erreur r&acute;ception : l\op&eacute;rateur n\'a pas r&eaucte;ussi &agrave, d&eacue;livrer le sms sur le t&eacute;l&eacute;hone du destinataire',
                                     '61' => 'Le SMS n\'existe pas ou plus'); 

		$from = strtoupper(get_nom($id));
		$code_ok =  array(0,11,13);
		$credits = getSMSCredit_5();
		if ( $credits == 0 ) {
		 	write_msgbox("ERREUR", $error_pic, 
			"Vous n'avez plus de cr&eacute;dits chez SMS Mode<br>
			<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);
                }
		else { // on peut envoyer
			for($i=0; $i < $nb_phone_numbers ; $i++){
		 		$number = $phone_numbers[$i];
				$retour=sendSMS_5($number, "$message"); 
				if(array_key_exists($retour, $description)) $reponse=$description[$retour]; 
                                if (in_array($retour, $code_ok)) { $sent = $sent +1;};
			}
			if ( $sent  > 0) {
				write_msgbox("OK", $star_pic, 
				"Le sms a bien &eacute;t&eacute; envoy&eacute; &agrave; <b>".$sent."</b> num&eacute;ros de t&eacute;l&eacute;phone sur ".$nb."<br>
				<p><font face=courrier-new size=1>Le texte du SMS:<br> ".nl2br($message)."</font>
				<p>Il vous reste:<b> ".getSMSCredit_5()."</b> cr&eacute;dits chez SMS Mode</b>
				<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);		
			}
			else {
	 			write_msgbox("ERREUR", $error_pic, 
				"Aucun SMS sur ".$nb." n'a &eacute;t&eacute; envoy&eacute;.<br>
				Une erreur est survenue lors de l'envoi du SMS via SMS Mode:<br>".$reponse."<br>
				<p align=center><a href=alerte_create.php> $myspecialfont retour</font></a>",30,0);
			}
		}
    }
        
                

    //===================================================================
	// save history
	//===================================================================	
	if ( $sent > 0 ) {
		// ins�rer dans la table smslog
    	$query="insert into smslog (P_ID, S_DATE, S_NB, S_TEXTE) 
	 	select ".$id.", NOW(),'".$sent."',\"".$message."\"";
    	$result=mysql_query($query);
    }
}
else {
	$subject="message de ".ucfirst(get_prenom($id))." ".strtoupper(get_nom($id));

	$nb = mysendmail( "$dest" , $id  , $subject , "$message" );

	write_msgbox("OK", $star_pic, "Le message (de ".get_email($id).") a �t� envoy� �:
	<br>".$nb." personnes<p><font face=courrier-new size=1>Sujet:[".$cisname."] ".$subject."
	<p>".nl2br($message)."</font><p align=center><a href=mail_create.php> $myspecialfont retour</font></a>",30,0);
}

?>
