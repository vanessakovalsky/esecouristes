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
?>

<html>
<head>

<SCRIPT type="text/javascript">

function redirect(status) {
     url="section.php?from=save&status="+status;
     self.location.href=url;
}

function suppress(p1,p2) {
  if ( confirm("Voulez vous vraiment supprimer la section "+ p2 +" "+ p1+ "?")) {
     url="del_section.php?S_ID="+p1+"&S_CODE="+p2;
     self.location.href=url;
  }
  else{
       redirect();
  }
}

function retour(p1,p2,p3) {
     url="upd_section.php?S_ID="+p1+"&status="+p3+"&from=save&msg='"+p2+"'";
     if ( p2 == 'updated') {
     	self.location.href=url;
     }
     else if ( p3 == 'documents') {
	 	opener.document.location.href="upd_section.php?S_ID="+p1+"&status="+p3;
	 	self.close();
	 }
	 else {
     	self.location.href=url;
     }
}
</SCRIPT>
</head>
<?php
$msg="";
$S_ID=intval($_POST["S_ID"]);
$operation=$_POST["operation"];

if ( isset($_POST['nom'])) $nom=mysql_real_escape_string($_POST["nom"]);
if ( isset($_POST['code'])) $code=mysql_real_escape_string($_POST["code"]); else $code='';
if ( isset($_POST['parent'])) $parent=mysql_real_escape_string($_POST["parent"]);
if ( isset($_POST['address'])) $address=mysql_real_escape_string($_POST["address"]);
if ( isset($_POST['zipcode'])) $zipcode=mysql_real_escape_string($_POST["zipcode"]);
if ( isset($_POST['city'])) $city=mysql_real_escape_string($_POST["city"]);
if ( isset($_POST['cedex'])) $cedex=mysql_real_escape_string($_POST["cedex"]);

if (isset ($_POST["phone"])) $phone=mysql_real_escape_string($_POST["phone"]); 
else $phone='';
if (isset ($_POST["phone2"])) $phone2=mysql_real_escape_string($_POST["phone2"]); 
else $phone2='';
if (isset ($_POST["fax"])) $fax=mysql_real_escape_string($_POST["fax"]); 
else $fax='';
if (isset ($_POST["email"])) $email=mysql_real_escape_string($_POST["email"]); 
else $email='';
if (isset ($_POST["email2"])) $email2=mysql_real_escape_string($_POST["email2"]); 
else $email2='';
if (isset ($_POST["type"])) $TD_CODE=mysql_real_escape_string($_POST["type"]); 
else $TD_CODE='';
if (isset ($_POST["url"])) $URL=mysql_real_escape_string($_POST["url"]); 
else $URL='';
if (isset ($_POST["security"])) $DS_ID=intval($_POST["security"]); 
else $DS_ID='';

if ( isset($_POST['status']) ) $status=$_POST['status'];
else $status = 'infos';

if ( isset($_POST['dps']) ) $dps=intval($_POST['dps']);
else $dps = 'null';

$S_PDF_MARGE_TOP=(isset($_POST["pdf_marge_top"])?$_POST["pdf_marge_top"]:15);
$S_PDF_MARGE_LEFT=(isset($_POST["pdf_marge_left"])?$_POST["pdf_marge_left"]:15);
$S_PDF_TEXTE_TOP=(isset($_POST["pdf_texte_top"])?$_POST["pdf_texte_top"]:40);
$S_PDF_TEXTE_BOTTOM=(isset($_POST["pdf_texte_bottom"])?$_POST["pdf_texte_bottom"]:25);
$S_PDF_SIGNATURE = addslashes((isset($_POST["pdf_signature"])?$_POST["pdf_signature"]:""));
$S_DEVIS_DEBUT = addslashes((isset($_POST["devis_debut"])?$_POST["devis_debut"]:""));
$S_DEVIS_FIN = addslashes((isset($_POST["devis_fin"])?$_POST["devis_fin"]:""));
$S_FACTURE_DEBUT = addslashes((isset($_POST["facture_debut"])?$_POST["facture_debut"]:""));
$S_FACTURE_FIN = addslashes((isset($_POST["facture_fin"])?$_POST["facture_fin"]:""));
$S_FRAIS_ANNULATION = addslashes((isset($_POST["frais_annulation"])?$_POST["frais_annulation"]:""));

if ( $operation == 'delete' ) check_all(14);
else if ( $operation == 'insert' ) check_all(22);
else if ( isset ($_FILES['userfile']) or $operation == 'updatedoc' ) check_all(47);
else if ((! check_rights($_SESSION['id'], 29, "$S_ID")) and (! check_rights($_SESSION['id'], 30, "$S_ID")) and (! check_rights($_SESSION['id'], 36, "$S_ID")))
check_all(22);

$_SESSION['status'] = "infos";

//============================================================
//   Upload file
//============================================================
$error = 0;
if ( isset ($_FILES['userfile'])) {
   if (check_rights($_SESSION['id'], 47, "$S_ID")) {
   //Maximum file size. You may increase or decrease.
   $MAX_SIZE = 5000000;
   //Allowable file Mime Types. Add more mime types if you want
   $FILE_MIMES = array('image/jpeg','image/jpg','image/gif','image/png','application/msword');
   //Allowable file ext. names. you may add more extension names.
   $FILE_EXTS  = array('.zip','.jpg','.png','.gif','.doc','.xls','.pdf','.ppt');

   $site_name = $_SERVER['HTTP_HOST'];
   $url_dir = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
   $url_this =  "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

   $upload_dir = $filesdir."/files_section/".$S_ID."/";
   $msgstring ="";

   if ($_FILES['userfile']['size'] <> 0) {
      $_SESSION['status'] = "documents";
      $temp_name = $_FILES['userfile']['tmp_name'];
      $file_type = $_FILES['userfile']['type'];
      $file_name = $_FILES['userfile']['name'];
      $file_ext = strtolower(substr($file_name,strrpos($file_name,".")));
      $file_name = str_replace("\\","",$file_name);
      $file_name = str_replace(" ","_",$file_name);
      $file_name = str_replace("°","",$file_name);
      $file_name = str_replace("#","",$file_name);
      $file_name = str_replace("'","",$file_name);
      $file_name = str_replace("&","",$file_name);
      $file_name = fixcharset($file_name);
      $file_path = $upload_dir.$file_name;
      //File Size Check
      if ( $_FILES['userfile']['size'] > $MAX_SIZE) {
      	 $msgstring = "La taille du fichier attaché ne doit pas dépasser 5 MB.";
     	 $error=1;
      }
      //File Type/Extension Check
      else if (!in_array($file_type, $FILE_MIMES) && !in_array($file_ext, $FILE_EXTS)) {
      	   $msgstring = "Attention, les fichiers du type $file_name($file_type) sont interdits.";
      	   $error=1;
      }
      else {
  		   // create upload dir
  			if (!is_dir($upload_dir)) {
    			if (!mkdir($upload_dir)) {
  	   				 $msgstring = "Le répertoire d'upload n'existe pas et sa création a échoué.";
  	   				 $error=1;
  	   			}
    			if (!chmod($upload_dir,0755)) {
  	   				$msgstring = "Echec lors de la mise à jour des permissions.";
  	   				$error=1;
  	   			}
   			}
      	   if (! $result  =  move_uploaded_file($temp_name, $file_path)) {
      	      $msgstring ="Une erreur est apparue lors de l'upload du fichier.";
      	      $error=1;
           }
      	   if (!chmod($file_path,0777)) {
   	          $msgstring = "Echec lors de la mise à jour des permissions.";
              $error=1;
      	   }
       }
       if ( $error == 0 ) {
       		// upload réussi: insérer les informations relatives au document dans la base
       		$query="insert into document(S_ID,D_NAME,TD_CODE,DS_ID,D_CREATED_BY)
       			values (".$S_ID.",\"".$file_name."\",\"".$TD_CODE."\",\"".$DS_ID."\",".$_SESSION['id'].")";
       		$result=mysql_query($query);
       		
       		if ( isset($_SESSION['td'])) {
				if ( $TD_CODE <> $_SESSION['td'] and $_SESSION['td'] <> 'ALL') $_SESSION['td']=$TD_CODE;
			}
       }
    }
    else $file_name="";
    $operation="retour";
  }
  else $file_name="";
  $_SESSION['status'] = "infos";
}
else {
	$file_name="";
	$_SESSION['status'] = "infos";
}

if ( $error == 1 )  {
         write_msgbox("ERREUR", $error_pic, "$msgstring<br><p align=center>
		 <a href='upd_section.php?S_ID=".$S_ID."' target='_self'>$myspecialfont retour</font></a> ",10,0);
		 exit;
}

// vérifier le code section choisi
if ( $code <> '' ) {
	$query="select count(*) as NB from section where S_CODE=\"".$code."\" and S_ID <> ".$S_ID;
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);


	if (( $operation <> 'delete' ) and ( $row["NB"] <> 0 )) {	 
 	   write_msgbox("erreur", $error_pic, "Le code choisi (".$code.") est déjà utilisé pour une autre section.<br><p align=center>
		<input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   exit;
	}
}

//=====================================================================
// changer infos d'un document
//=====================================================================
if ( $operation == 'updatedoc' ) {
	$doc=mysql_real_escape_string($_POST["doc"]);
	$S_ID=intval($_POST["S_ID"]);
	$TD_CODE=mysql_real_escape_string($_POST["type"]); 
	$DS_ID=intval($_POST["security"]); 

	$query="select count(*) as NB from document where S_ID=".$S_ID." and D_NAME='".$doc."'";
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	if ( $row["NB"] == 0 )
		$query="insert into document (S_ID,D_NAME,TD_CODE,DS_ID,D_CREATED_BY)
			values(".$S_ID.",'".$doc."','".$TD_CODE."',".$DS_ID.",".$_SESSION['id'].")";
	else 
		$query="update document set TD_CODE='".$TD_CODE."', DS_ID=".$DS_ID."
			where S_ID=".$S_ID." and D_NAME='".$doc."'";

	$result=mysql_query($query);
	$status='documents';
	$operation='retour2';
	
	if ( isset($_SESSION['td'])) {
		if ( $TD_CODE <> $_SESSION['td'] and $_SESSION['td'] <> 'ALL') $_SESSION['td']=$TD_CODE;
	}
}

//=====================================================================
// update la fiche
//=====================================================================
if (( $operation == 'update' ) and ( $status == 'infos')) {

    // interdire les dependences circulaires
 	$list = preg_split('/,/' , get_family("$S_ID").",".$S_ID); 
 	if (in_array("$parent", $list)) {
 	 write_msgbox("erreur", $error_pic, 
	  "La section ne peut pas avoir comme section parente une de ses sections filles.<br><p align=center>
	  <input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   exit;
 	}

	if ( $dps == '' ) $dps = 'null';
	if (check_rights($_SESSION['id'], 22, "$S_ID")){
		$query="update section set
	       S_CODE=(\"".$code."\"),
	       S_DESCRIPTION=(\"".$nom."\"),
	       S_PARENT='".$parent."',
	       S_PHONE=\"".$phone."\",
	       S_PHONE2=\"".$phone2."\",
	       S_FAX=\"".$fax."\",
	       S_ADDRESS=\"".$address."\",
	       S_CITY=\"".strtoupper($city)."\",
	       S_CEDEX=\"".strtoupper($cedex)."\",
	       S_ZIP_CODE=\"".$zipcode."\",
	       S_EMAIL=\"".$email."\",
	       S_EMAIL2=\"".$email2."\",
	       S_URL=\"".$URL."\",
	       DPS_MAX_TYPE=".$dps."
		   where S_ID=".$S_ID ;
		$result=mysql_query($query);
		if ( $address <> "" ) gelocalize($S_ID, 'S');
		rebuild_section_flat(-1,0,6);
	}
	$operation="retour";		
}	
		
//=====================================================================
// sauver les agréments
//=====================================================================			

if (( $operation == 'update' ) and ( $status == 'agrements')) {	
	if (check_rights($_SESSION['id'], 36, "$S_ID")) {
		$query="select TA_CODE from type_agrement";
		$result=mysql_query($query);
    	while ($row=@mysql_fetch_array($result)){
    		$TA_CODE=$row["TA_CODE"];
    		$debut='';$fin='';$val='';
    		if (isset($_POST["deb_".$TA_CODE])) {
    		    if ( $_POST["deb_".$TA_CODE] <> '' ) {
    		 		$special = preg_split('/\-/',mysql_real_escape_string($_POST["deb_".$TA_CODE]));
  	            	$day=$special[0];
  	            	$month=$special[1];
  	            	$year=$special[2];
			 		$debut=$year.'-'.$month.'-'.$day;
			 	}
    		}
			if (isset($_POST["fin_".$TA_CODE])) {
			   if ( $_POST["fin_".$TA_CODE] <> '' ) {
			    	$special = preg_split('/\-/',mysql_real_escape_string($_POST["fin_".$TA_CODE]));
  	            	$day=$special[0];
  	            	$month=$special[1];
  	            	$year=$special[2];
			 		$fin=$year.'-'.$month.'-'.$day;
			 	}
			}
			if (isset($_POST["val_".$TA_CODE])) {
			 	if ( $_POST["val_".$TA_CODE] <> '' )
			 		$val=mysql_real_escape_string($_POST["val_".$TA_CODE]);
			}
			
			$query2="delete from agrement where TA_CODE='".$TA_CODE."' and S_ID=".$S_ID;
			$result2=mysql_query($query2);
			if (( $debut <> '' ) or ( $fin <> '' ) or ( $val <> '' )){
				$query2="insert into agrement (TA_CODE,S_ID,A_DEBUT,A_FIN,TAV_ID) 
					values ( '".$TA_CODE."',".$S_ID.",";
				if ( $debut <> '' ) $query2 .= "'".$debut."',";
				else $query2 .= "NULL,";
				if ( $fin <> '' ) $query2 .= "'".$fin."',";
				else $query2 .= "NULL,";
				if ( $val <> '' ) $query2 .= "'".$val."')";
				else $query2 .= "NULL)";		
				$result2=mysql_query($query2);
			}	
		}
	}
	$operation="retour";
}	
		
// ===================================================
// Modèles de documents
// ===================================================

if (( $operation == 'update' ) and ( $status == 'parametrage')) {	
$_SESSION['status'] = "parametrage";
// MODELE PDF
if (check_rights($_SESSION['id'], 29, "$S_ID")) {
	if(isset($_FILES['pdf_page'])) {
	  if($_FILES['pdf_page']['name']!=""){
		$dossier = $basedir.'/images/user-specific/';
		$fichier = basename($_FILES['pdf_page']['name']);
		$taille_maxi = 2000000;
		$taille = filesize($_FILES['pdf_page']['tmp_name']);
		$extensions_page = array('.pdf');
		$extension = strrchr($_FILES['pdf_page']['name'], '.'); 
		//Début des vérifications de sécurité...
		if(!in_array($extension, $extensions_page)) //Si l'extension n'est pas dans le tableau
     		$erreur = 'Vous devez uploader un fichier de type pdf.';
		if($taille>$taille_maxi)
			$erreur = 'Le fichier est trop gros...';
		if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
		{
	 		$fichier = $S_ID."_pdf_page.pdf";
     		if(! move_uploaded_file($_FILES['pdf_page']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
     			$msg .= 'Echec de l\'upload !';
			else {
			 	$sql = "UPDATE section set S_PDF_PAGE = '$fichier' WHERE s_id = ".$S_ID;
			 	$result=mysql_query($sql);
			}
		}
		else $msg .= $erreur;
	  }
	}
	$sql = "UPDATE section set 
		   S_PDF_MARGE_TOP = $S_PDF_MARGE_TOP,
		   S_PDF_MARGE_LEFT = $S_PDF_MARGE_LEFT,
		   S_PDF_TEXTE_TOP = $S_PDF_TEXTE_TOP,
		   S_PDF_TEXTE_BOTTOM = $S_PDF_TEXTE_BOTTOM,		   
		   S_PDF_SIGNATURE = \"$S_PDF_SIGNATURE\",
		   S_DEVIS_DEBUT = \"$S_DEVIS_DEBUT\",
		   S_DEVIS_FIN = \"$S_DEVIS_FIN\",
		   S_FACTURE_DEBUT = \"$S_FACTURE_DEBUT\",
		   S_FACTURE_FIN = \"$S_FACTURE_FIN\",	
		   S_FRAIS_ANNULATION =\"$S_FRAIS_ANNULATION\"	  
	WHERE s_id =".$S_ID;
	$result=mysql_query($sql);
	if(isset($_POST['delpage'])){
		$sql = "update section set S_PDF_PAGE = NULL where S_ID='$S_ID'";
		$result=mysql_query($sql);
	}
}
	
// MODELE BADGE
if (check_rights($_SESSION['id'], 30, "$S_ID")) {
	if(isset($_FILES['pdf_badge'])) {
		if($_FILES['pdf_badge']['name']!="") {
		$dossier = $basedir.'/images/user-specific/';
		$fichier = basename($_FILES['pdf_badge']['name']);
		$taille_maxi = 200000; // 200 Ko
		$taille = filesize($_FILES['pdf_badge']['tmp_name']);
		$extensions_badge = array('.gif');
		$extension = strrchr($_FILES['pdf_badge']['name'], '.'); 
		//Début des vérifications de sécurité...
		if(!in_array($extension, $extensions_badge)) //Si l'extension n'est pas dans le tableau
			$erreur = 'Vous devez uploader un fichier de type png ou gif.';
		if($taille>$taille_maxi)
			$erreur = 'Le fichier est trop gros...';
		if(!isset($erreur)) {
     		//On formate le nom du fichier ici...
     		$fichier = fixcharset($fichier);
     		$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
	 		$fichier = $S_ID."_badge.".substr($fichier,strlen($fichier)-3,3);
     		if( !move_uploaded_file($_FILES['pdf_badge']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
     			$msg .= 'Echec de l\'upload !';
     		else {
    			$msg .= 'Upload effectué avec succès !';
				$sql = "UPDATE section set 
		  		S_PDF_BADGE = '$fichier'
		  		WHERE s_id = $S_ID;
		  		";
				$result=mysql_query($sql); 
     		}
		}
		else $msg .= $erreur;
		}
	}
	if(isset($_POST['delbadge'])){
		$sql = "update section set S_PDF_BADGE = NULL where S_ID='$S_ID'";
		$result=mysql_query($sql);	
	}
}
echo $msg;
$operation="retour";
}

//=====================================================================
// insertion nouvelle fiche
//=====================================================================

if ( $operation == 'insert' ) {
   $query="select max(S_ID) as NB from section";
   $result=mysql_query($query);
   $row=mysql_fetch_array($result);
   $newsid = $row["NB"] + 1 ;	
 
   $query="INSERT INTO section ( S_ID, S_CODE, S_DESCRIPTION , S_PARENT , 
   			 S_PHONE, S_PHONE2, S_FAX , S_ADDRESS , S_ZIP_CODE , S_CITY , S_CEDEX ,
			 S_EMAIL , S_EMAIL2, S_URL)
   	values ( $newsid, \"".$code."\",\"".$nom."\",".$parent.",
	   		'".$phone."','".$phone2."','".$fax."',\"".$address."\",'".$zipcode."',\"".$city."\",\"".$cedex."\",
			\"".$email."\",\"".$email2."\",\"".$URL."\")";
	$result=mysql_query($query);
	rebuild_section_flat(-1,0,6);
}
switch ($operation){
case "delete":
	echo "<body onload=suppress('".$S_ID."','".$code."')></body></html>";
	break;
case "retour":
	echo "<body onload=retour('".$S_ID."','".urlencode($msg)."','".$status."')></body></html>";
	break;
case "retour2":
	echo "<body onload=retour('".$S_ID."','updated','".$status."')></body></html>";
	break;
default:
	echo "<body onload=redirect('".$status."')></body></html>";
}
//echo "$operation";
?>
