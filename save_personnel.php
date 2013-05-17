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
check_all(0);
$id=$_SESSION['id'];
if ( $nbsections == 1) $mysection=4;
else $mysection=$_SESSION['SES_SECTION'];

?>

<html>
<SCRIPT language=JavaScript>

function redirect(section,category) {
     url="personnel.php?order=P_NOM&filter="+section+"&category="+category;
     self.location.href=url;
}

function redirect2(pid) {
     url="index_d.php";
     self.location.href=url;
}

function suppress(p1,p2,p3,p4) {
  if ( confirm("Voulez vous vraiment supprimer la fiche de "+ p2 +" "+ p1+ "?")) {
     url="del_personnel.php?P_ID="+p4+"&P_CODE="+p3;
     self.location.href=url;
  }
  else{
       redirect();
  }
}
</SCRIPT>

<?php

include_once ("config.php");
$P_ID=intval(mysql_real_escape_string($_GET["P_ID"]));
if ( $P_ID == 0 ) {
	param_error_msg();
	exit;
}

if (isset ($_GET["grade"])) $grade=mysql_real_escape_string($_GET["grade"]);
else $grade="SAP";
if (isset ($_GET["statut"])) $statut=mysql_real_escape_string($_GET["statut"]);
else $statut="SPV";
if (isset ($_GET["debut"])) $debut=mysql_real_escape_string($_GET["debut"]);
else $debut=date("Y");
if (isset ($_GET["company"])) $company=intval($_GET["company"]);
else $company="null";

if (isset ($_GET["skype"])) $skype=mysql_real_escape_string($_GET["skype"]);
else $skype="";


if ( $statut == 'EXT' ) $mylightcolor=$mygreencolor;

if (isset ($_GET["prenom"])) $prenom=STR_replace("\"","",$_GET["prenom"]);
else $prenom="";
if (isset ($_GET["nom"])) $nom=STR_replace("\"","",$_GET["nom"]);
else $nom="";

$matricule=STR_replace("\"","",$_GET["matricule"]);
$city=STR_replace("\"","",$_GET["city"]);
$address=STR_replace("\"","",$_GET["address"]);
$skype=STR_replace("\"","",$skype);
$birthplace=STR_replace("\"","",$_GET["birthplace"]);

$nom=mysql_real_escape_string($nom);
$prenom=mysql_real_escape_string($prenom);
$matricule=mysql_real_escape_string($matricule);
$birth=mysql_real_escape_string($_GET["birth"]);
if ( $birth <> '') {
	$tmp=explode ( "/",$birth); $year=$tmp[2]; $month=$tmp[1]; $day=$tmp[0];
	$birth=$year.'-'.$month.'-'.$day;
}
$birthplace=mysql_real_escape_string($birthplace);
$hissection=mysql_real_escape_string($_GET["groupe"]);
$habilitation=intval(mysql_real_escape_string($_GET["habilitation"]));
$habilitation2=intval(mysql_real_escape_string($_GET["habilitation2"]));
$operation=mysql_real_escape_string($_GET["operation"]);
$email=mysql_real_escape_string($_GET["email"]);
$phone=mysql_real_escape_string($_GET["phone"]);
$phone2=mysql_real_escape_string($_GET["phone2"]);
$abbrege=mysql_real_escape_string($_GET["abbrege"]);
$address=mysql_real_escape_string($address);
$zipcode=mysql_real_escape_string($_GET["zipcode"]);
$city=mysql_real_escape_string($city);
$skype=mysql_real_escape_string($skype);
$sexe=mysql_real_escape_string($_GET["sexe"]);
$relation_nom=mysql_real_escape_string($_GET["relation_nom"]);
$relation_prenom=mysql_real_escape_string($_GET["relation_prenom"]);
$relation_phone=mysql_real_escape_string($_GET["relation_phone"]);
if ( isset($_GET["type_salarie"]))
	$type_salarie=mysql_real_escape_string($_GET["type_salarie"]);
else $type_salarie="";
if ( isset($_GET["heures"]))
	$heures=intval(($_GET["heures"]));
else $heures="0";
$hide=(isset($_GET["hide"])?intval($_GET["hide"]):0);
$nospam=(isset($_GET["nospam"])?intval($_GET["nospam"]):0);
$flag1=(isset($_GET["flag1"])?intval($_GET["flag1"]):0);
if ( $habilitation < 0 ) $flag1=0;
$flag2=(isset($_GET["flag2"])?intval($_GET["flag2"]):0);
if ( $habilitation2 < 0 ) $flag2=0;
if (isset ($_GET["activite"])) $activite=mysql_real_escape_string($_GET["activite"]);
else $activite="1";

$phone=STR_replace("-",".",$phone);
$phone=STR_replace(" ",".",$phone);
$phone=STR_replace(".","",$phone);
$phone2=STR_replace("-",".",$phone2);
$phone2=STR_replace(" ",".",$phone2);
$phone2=STR_replace(".","",$phone2);
$relation_phone=STR_replace("-",".",$relation_phone);
$relation_phone=STR_replace(" ",".",$relation_phone);
$relation_phone=STR_replace(".","",$relation_phone);



// avoid homonymes in the same section
if ( $operation == 'insert') {
	$query=" select count(*) as NB from pompier
		 where ( P_SECTION ='$hissection' or P_SECTION =".get_section_parent("'".$hissection."'").")
		 and P_NOM='".$nom."'
		 and P_PRENOM='".$prenom."'";
	$result=mysql_query($query);
	$row=@mysql_fetch_array($result);
	if ( $row["NB"] == 1 ) {
 		write_msgbox("erreur", $error_pic, ucfirst($prenom)." ".strtoupper($nom)." existe déjà dans ".get_section_name($hissection)." ou dans la section supérieure.<br><p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   exit;
   }
}
if ( $operation == 'update') {
	$query=" select count(*) as NB from pompier
		 where ( P_SECTION ='$hissection' or P_SECTION =".get_section_parent("'".$hissection."'").")
		 and P_NOM='".$nom."'
		 and P_PRENOM='".$prenom."'
		 and P_ID <> ".$P_ID;
	$result=mysql_query($query);
	$row=@mysql_fetch_array($result);
	if ( $row["NB"] == 1 ) {
 		write_msgbox("erreur", $error_pic, ucfirst($prenom)." ".strtoupper($nom)." existe déjà dans ".get_section_name($hissection)." ou dans la section supérieure.<br> Veuillez vérifier la présence d'une autre fiche avec les mêmes noms et prénoms.<p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   exit;
   }
}


if ( $operation <> "delete" ) {
   if (( $matricule == "") or ( $matricule == "0" )) {
       if (( $operation <> "insert" ) or ($statut <> 'EXT' )) {
 	   		write_msgbox("erreur", $error_pic, "L'identifiant doit être renseigné et ne doit pas être '0'.<br><p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   		exit;
	   }
   }
   if ( isset($_GET["nom"]) and isset($_GET["prenom"])) {
   		if (( $nom == "") or ( $prenom == "" )) {
    		write_msgbox("erreur", $error_pic, "Le nom et le prénom doivent être renseignés<br><p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   		exit;
    	}
   }
   if (( get_code($matricule) <> '' ) and ( get_code($matricule) <> $P_ID  ))   {
 	   write_msgbox("erreur", $error_pic, "L'identifiant choisi (".$matricule.") est déjà utilisé pour un autre utilisateur.<br><p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   exit;
   }
}
else check_all(3);


//=====================================================================
// update la fiche
//=====================================================================

if ( $operation == 'update' ) {
   // vérifier permissions
   if ($id <> $P_ID ) {
      if (get_statut($P_ID) == 'EXT' ) check_all(37);
      else check_all(2);
   }
   	
   $query="select P_CODE as OLDM, P_STATUT, P_SECTION as OLD_SECTION, GP_ID as OLDG, P_OLD_MEMBER as PREVIOUSPOLDMEMBER,
   		   P_EMAIL as OLDMAIL, P_PHONE as OLDPHONE, P_PHONE2 as OLDPHONE2,
   		   P_ADDRESS as OLDADDRESS, P_CITY as OLDCITY, P_ZIP_CODE as OLDZIPCODE
   		   from pompier where P_ID=".$P_ID;
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   $OLD_MATRICULE=$row["OLDM"];
   $OLD_GROUPE=$row["OLDG"];
   $OLD_STATUT=$row["P_STATUT"];
   $OLD_SECTION=$row["OLD_SECTION"];
   $PREVIOUSPOLDMEMBER=$row["PREVIOUSPOLDMEMBER"];
   $OLDMAIL=$row["OLDMAIL"];
   $OLDPHONE=$row["OLDPHONE"];
   $OLDPHONE2=$row["OLDPHONE2"];
   $OLDADDRESS=$row["OLDADDRESS"];
   $OLDCITY=$row["OLDCITY"];
   $OLDZIPCODE=$row["OLDZIPCODE"];
	
   // cas du changement de groupe
   if ( $OLD_GROUPE <> $habilitation ) {
    if ((! check_rights($_SESSION['id'], 25)) or ( $habilitation == 4 ))
      @check_all(9);
   }
   // cas ancien membre
   if ( $activite > 0 ) $habilitation = -1;
   if ((  $PREVIOUSPOLDMEMBER > 0 ) and ( $activite == 0 )) $habilitation = 0; 
 
   // cas passage de externe à membre, mettre groupe public
   if ( $OLD_STATUT == 'EXT'  and  $statut <> 'EXT' ) {
   		$habilitation=min(0,$habilitation);
   		if ($habilitation < 0 ) $habilitation=0;
   }
   // je modifie ma fiche, droits limités, seules quelques colonnnes
   if (($id == $P_ID ) and (! check_rights($_SESSION['id'], 2)))
 		$query="update pompier set 
		   P_CODE=\"".$matricule."\",
		   P_SEXE=\"".$sexe."\",
	       P_BIRTHDATE='".$birth."',
	       P_BIRTHPLACE=\"".$birthplace."\",
	       P_EMAIL=\"".$email."\",
	       P_PHONE=\"".$phone."\",
	       P_PHONE2=\"".$phone2."\",
	       P_ABBREGE=\"".$abbrege."\",
	       P_ADDRESS=\"".$address."\",
		   P_SKYPE=\"".$skype."\",
	       P_CITY=\"".strtoupper($city)."\",
	       P_ZIP_CODE=\"".$zipcode."\",
	       P_RELATION_NOM=\"".$relation_nom."\",
	       P_RELATION_PRENOM=\"".$relation_prenom."\",
	       P_RELATION_PHONE=\"".$relation_phone."\"	,
		   P_HIDE=\"".$hide."\",
		   P_NOSPAM=\"".$nospam."\"
		   where P_ID=".$P_ID ;	   
   else {
   		// je modifie n'importe quelle fiche
   		$query="update pompier set P_CODE=\"".$matricule."\",
   		   P_SEXE=\"".$sexe."\",
	       P_NOM=LOWER(\"".$nom."\"),
	       P_PRENOM=LOWER(\"".$prenom."\"),
	       P_DEBUT='".$debut."',
	       P_OLD_MEMBER='".$activite."',
	       P_STATUT='".$statut."',
	       P_BIRTHDATE='".$birth."',
	       P_BIRTHPLACE=\"".$birthplace."\",
	       P_GRADE='".$grade."',
	       P_SECTION=".$hissection.",
	       C_ID=".$company.",
	       GP_ID=".$habilitation.",
	       GP_ID2=".$habilitation2.",
	       P_EMAIL=\"".$email."\",
	       P_PHONE=\"".$phone."\",
		   P_PHONE2=\"".$phone2."\",
	       P_ABBREGE=\"".$abbrege."\",
	       P_ADDRESS=\"".$address."\",
		   P_SKYPE=\"".$skype."\",
	       P_CITY=\"".strtoupper($city)."\",
	       P_ZIP_CODE=\"".$zipcode."\",
	       P_RELATION_NOM=\"".$relation_nom."\",
	       P_RELATION_PRENOM=\"".$relation_prenom."\",
	       P_RELATION_PHONE=\"".$relation_phone."\"	,
		   P_HIDE=\"".$hide."\",
		   P_NOSPAM=\"".$nospam."\",
		   GP_FLAG1=\"".$flag1."\",
		   GP_FLAG2=\"".$flag2."\"			   
		where P_ID=".$P_ID ;
   }
   $result=mysql_query($query);
   insert_log('UPDP', $P_ID);
   
   // cas changement coordonnées mail, phone, adresse
   // notification secrétariat (fonctionnalité 50)
   $changed="";
   
   $queryt="select P_ADDRESS from pompier where P_ID=".$P_ID ;
   $resultt=mysql_query($queryt);
   $rowt=@mysql_fetch_array($resultt);
   $address=$rowt["P_ADDRESS"];
   
   if ( "$OLDMAIL" <> "$email" ) $changed .=" - Email";
   if ( "$OLDPHONE" <> "$phone") $changed .=" - Téléphone portable";
   if ( "$OLDPHONE2" <> "$phone2") $changed .=" - Autre Téléphone";
   if ( "$OLDADDRESS" <> "$address") $changed .=" - Adresse";
   if ( "$OLDCITY" <> "$city") $changed .=" - Ville";
   if ( "$OLDZIPCODE" <> "$zipcode") $changed .=" - Code postal";
   if ( $changed <> "" ) {
		$changed .= " -";
   		if ((get_level("$hissection")  >= $nbmaxlevels -1) or ($nbsections > 0 )) { // antenne locale, pompiers
   	  		$destid=get_granted(50,"$hissection",'parent');
   		}
   		else { // département, région
      		$destid=get_granted(50,"$hissection",'local');
      	}
		if ( $destid <> "" ) {
      			$message  = "Bonjour,\n";
      			$m=get_section_name("$hissection");
      			$n=ucfirst($prenom)." ".strtoupper($nom);
      			$subject = "Changement de coordonnees personnelles pour - ".$n;	               
      			$message = "La informations suivantes ont été modifiées pour ".$n;
				$message .= "\n".$changed;
				$message .= "\n\nVoici ses nouvelles coordonnées personnelles:";
				$message .= "\nEmail: ".$email;
				$message .= "\nTéléphone portable: ".$phone;
				$message .= "\nAutre Téléphone: ".$phone2;
				$message .= "\nAdresse: ".$address;
				$message .= "\nVille: ".$city;
				$message .= "\nCode postal: ".$zipcode;
				
      			$nb = mysendmail("$destid" , $id , "$subject" , "$message" ,"yes" );
   		}
		gelocalize($P_ID, 'P');
   }
   
   if ( $hissection <> $OLD_SECTION ) {
    	$query="update vehicule set S_ID=".$hissection." where S_ID in (".get_family("$OLD_SECTION").") and AFFECTED_TO=".$P_ID;
   		$result=mysql_query($query);	
    	$query="update materiel set S_ID=".$hissection." where S_ID in (".get_family("$OLD_SECTION").") and AFFECTED_TO=".$P_ID;
   		$result=mysql_query($query);
   		
		// remove permissions on higher level
		if ( get_section_parent("$hissection") <> get_section_parent("$OLD_SECTION")) {  
			$query="update pompier set GP_FLAG1=0, GP_FLAG2=0 where P_ID=".$P_ID;
			$result=mysql_query($query);
		}
		if ($log_actions == 1)
			insert_log('UPDSEC', $P_ID, get_section_code("$OLD_SECTION")." -> ".get_section_code("$hissection"));
   }
   
   if ( $activite > 0 ) {
   		$query="delete from section_role where P_ID=".$P_ID;
   		$result=mysql_query($query);
   }
   if (( $birth == '') or ( $birth == '0000-00-00')){
    	$query="update pompier set P_BIRTHDATE=null
    			where P_ID=".$P_ID ;
     	$result=mysql_query($query);  
   }
   
   // particularité salariés
   if ( $statut == 'SAL' ) {
    	$query="update pompier set TS_CODE='".$type_salarie."', TS_HEURES=".$heures." where P_ID=".$P_ID;
   }
   else {
    	$query="update pompier set TS_CODE=null, TS_HEURES=null where P_ID=".$P_ID;
   }
   $result=mysql_query($query);
   
   // cas de changement de statut activité : sauver date et auteur
   // et envoyer un mail au responsable(s) d'association
   if ( $PREVIOUSPOLDMEMBER <> $activite ) {
    	if ($log_actions == 1)
			insert_log('UPDSTP', $P_ID, ($activite == 0)?"de nouveau actif":"ancien membre");
        if ( $PREVIOUSPOLDMEMBER == 0 ) {
        	$query="update pompier set P_UPDATED_BY=$id, P_FIN=NOW() where P_ID=".$P_ID;
        	$result=mysql_query($query);
        	
        	$query="delete from indisponibilite where P_ID=".$P_ID." and (I_DEBUT >= CURDATE() or I_FIN > CURDATE())";
        	$result=mysql_query($query);
        }
        if ( $activite == 0 ){
            $query="update pompier set P_UPDATED_BY=null, P_FIN=null where P_ID=".$P_ID;
        	$result=mysql_query($query);
        }
        if ( $nbsections == 0 ) {
   			if (get_level("$hissection")  >= $nbmaxlevels -1) { // antenne locale
   	  			$destid=get_granted(32,"$hissection",'parent','yes');
   			}
   			else { // département, région
      			$destid=get_granted(32,"$hissection",'local','yes');
      		}
      		$message  = "Bonjour,\n";
      		$m=get_section_name("$hissection");
      		$n=ucfirst($prenom)." ".strtoupper($nom);
      		$subject = "Changement de situation pour - ".$n;	               
      		$message = "La situation d'activité a été modifiée pour ".$n;
      		$message .= "\ndans la section: ".$m;
      		if ( $activite == 0 ) $message .= "\n$n est de nouveau un membre actif.";
      		else $message .= "\n$n est maintenant un ancien membre.";
      		if ( $destid <> "" ) 
      			$nb = mysendmail("$destid" , $id , "$subject" , "$message" );
      			
			$query="select s.S_EMAIL2, sf.NIV
			from section_flat sf, section s
			where s.S_ID = sf.S_ID
			and sf.NIV < 4
			and s.S_ID in (".$hissection.",".get_section_parent("$hissection").")
			order by sf.NIV ";
	  		$result=mysql_query($query);
	  		$row=@mysql_fetch_array($result);
	  		$S_EMAIL2=$row["S_EMAIL2"];
	  		if ( $S_EMAIL2 <> "" )
				$nb2 = mysendmail2("$S_EMAIL2" , $_SESSION['id'] , "$subject" , "$message" );	
	   	}
	   	if ( $PREVIOUSPOLDMEMBER == 0 ) {
        	$query="select count(*) as NB from vehicule where AFFECTED_TO=".$P_ID;
        	$result=mysql_query($query);
        	$row=@mysql_fetch_array($result);
   			$NB1=$row["NB"];
   			$query="select count(*) as NB from materiel where AFFECTED_TO=".$P_ID;
        	$result=mysql_query($query);
        	$row=@mysql_fetch_array($result);
   			$NB2=$row["NB"];
   			if ( $NB1 > 0 or $NB2 > 0 ) {
   				write_msgbox("WARNING", $warning_pic, "Attention ".ucfirst($prenom)." ".strtoupper($nom)." n'est plus un membre actif, mais des véhicules ou du matériel lui sont toujours affectés.<p><a href=upd_personnel.php?from=created&pompier=$P_ID > $myspecialfont retour</font></a></p>",30,0);
	   exit;
   			}
        }
   }
}

//=====================================================================
// insertion nouvelle fiche
//=====================================================================

if ( $operation == 'insert' ) {
   if ( $statut == 'EXT' ) {
       	check_all(37);
   		if (! check_rights($_SESSION['id'], 37, "$hissection")) check_all(24);
   }
   else {
   		check_all(1);
   		if (! check_rights($_SESSION['id'], 1, "$hissection")) check_all(24);
   }
   if ( $habilitation <> 0 ) {
      if ((! check_rights($_SESSION['id'], 25)) or ( $habilitation == 4 ))
      @check_all(9);
   }
   $mylength=max($password_length , 6);
   $mypass=generatePassword($mylength);
   
   // pour externes generer un matricule bidon mais unique
   if ($statut == 'EXT' ) $matricule=generatePassword(12);
   // les externes ne doivent pas pouvoir se connecter
   if ( $statut == 'EXT' ) $habilitation=-1;
   
   if ($birth == "") $birth = '0000-00-00';
   $query="insert into pompier 
   			(P_CODE,P_PRENOM,P_NOM,P_SEXE,P_GRADE,P_STATUT,P_MDP, P_DEBUT,P_BIRTHDATE, 
			 P_BIRTHPLACE, P_SECTION, GP_ID, P_EMAIL, P_PHONE, P_PHONE2, 
			 P_ABBREGE,P_ADDRESS,P_CITY,P_ZIP_CODE,C_ID, P_SKYPE,
			 P_RELATION_NOM,P_RELATION_PRENOM,P_RELATION_PHONE,P_HIDE,P_NOSPAM,TS_CODE,TS_HEURES, 
			 P_CREATED_BY, P_CREATE_DATE)
	   values (\"".$matricule."\",LOWER(\"".$prenom."\"),LOWER(\"".$nom."\"),'".$sexe."','".$grade."',
	           '".$statut."',md5(\"".$mypass."\"),'".$debut."',\"".$birth."\",
			   \"".$birthplace."\",".$hissection.",".$habilitation.",\"".$email."\",\"".$phone."\",\"".$phone2."\",
			   \"".$abbrege."\",\"".$address."\",\"".strtoupper($city)."\",\"".$zipcode."\",".$company.",\"".$skype."\",
			   \"".$relation_nom."\",\"".$relation_prenom."\",\"".$relation_phone."\",'".$hide."','".$nospam."','".$type_salarie."',".$heures.",
			   ".$id.",NOW()
			   )";
   $result=mysql_query($query);
   
   if (( $birth == '') or ( $birth == '0000-00-00')){
    	$query="update pompier set P_BIRTHDATE=null
    			where P_CODE='".$matricule."'" ;
     	$result=mysql_query($query);  
   }

   // run specific actions
   $P_ID=get_code($matricule);
   specific_insert ($P_ID);
   
   if ($log_actions == 1) 
   		insert_log('INSP', $P_ID);
   
   // send notifications
   if (get_level("$hissection")  >= $nbmaxlevels -1) { // antenne locale
   	  $destid=get_granted(32,"$hissection",'parent','yes');
   }
   else { // département, région
      	$destid=get_granted(32,"$hissection",'local','yes');
   }
   if ($statut <> 'EXT' ) {
      $message  = "Bonjour,\n";
      $m=get_section_name("$hissection");
      $n=ucfirst($prenom)." ".strtoupper($nom);
      $subject = "Nouveau compte utilisateur - ".$m;	               
      $message = "Un nouveau compte utilisateur a été créé pour:\n ".$n;
      $message .= "\ndans la section: ".$m;
      if ( $destid <> "" )
      	$nb = mysendmail("$destid" , $id , "$subject" , "$message" );
      
	  $query="select s.S_EMAIL2, sf.NIV
		from section_flat sf, section s
		where s.S_ID = sf.S_ID
		and sf.NIV < 4
		and s.S_ID in (".$hissection.",".get_section_parent($hissection).")
		order by sf.NIV ";
	  $result=mysql_query($query);
	  $row=@mysql_fetch_array($result);
	  $S_EMAIL2=$row["S_EMAIL2"];
	  if ( $S_EMAIL2 <> "" )
			$nb2 = mysendmail2("$S_EMAIL2" , $_SESSION['id'] , "$subject" , "$message" );
   	} 
   
   // send login / password information
   if (( $email <> "" ) and ($statut <> 'EXT' ))  {
   	  $destid=get_code($matricule);
      $message  = "Bonjour ".ucfirst($prenom).",\n";
      $n=ucfirst($prenom)." ".strtoupper($nom);
      	               
      $subject  = "Nouveau compte utilisateur pour - ".$n;

      $message .= "Je viens de créer votre compte personnel eBrigade\n";
      $message .= "identifiant: ".$matricule."\n";
      $message .= "mot de passe: ".$mypass."\n";
      if ( $cisname == 'F.N.P.C.' )
      	$message .= "\nPour accéder à votre nouveau compte eBrigade, prenez soin de sélectionner 'eBrigade' avec le bouton radio avant de saisir vos identifiants\n";
		
      $nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );
      if ($nb == 0 ) $email ='';
      else {
       		write_msgbox("OK", $star_pic, "Le compte de ".ucfirst($prenom)." ".strtoupper($nom)."a été créé.<br>Un email contenant ses informations de connexion lui a été envoyé<p>à cette adresse: <b> $email</b><p align=center><a href=upd_personnel.php?from=created&pompier=$P_ID > $myspecialfont retour</font></a>",30,0);
	   exit;
	  }
   }
   else if (( $email == "" ) and ($statut <> 'EXT' )) {
      write_msgbox("OK", $star_pic, "Le compte de ".ucfirst($prenom)." ".strtoupper($nom)."a été créé.<br>Merci de lui communiquer ces infos:<p>identifiant: <b>$matricule</b><br>mot de passe: <b>$mypass</b><p align=center><a href=upd_personnel.php?from=created&pompier=$P_ID > $myspecialfont retour</font></a>",30,0);
	   exit;
   }
   else {
    	write_msgbox("OK", $star_pic, "Le compte de ".ucfirst($prenom)." ".strtoupper($nom)." a été créé.<br>En tant que personnel extérieur, ".($sexe=='M'?'il':'elle')." ne peut pas se connecter.<p align=center><a href=upd_personnel.php?from=created&pompier=$P_ID > $myspecialfont retour</font></a>",30,0);
	   exit;
   }
}

if ($operation == 'delete' ) {
   check_all(3);
   $nom=STR_replace(" ","",get_nom($P_ID));
   $prenom=STR_replace(" ","",get_prenom($P_ID));
   echo "<body onload=suppress('".$nom."','".$prenom."','$matricule','$P_ID')>";
}
else {
   // cas externe qui a modifié ses infos
   if ( ! check_rights($id,40) and $id == $P_ID) echo "<body onload=redirect2()>";
   // cas général
   else echo "<body onload=redirect('".$hissection."','".$statut."')>";
}
?>
