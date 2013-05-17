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
  
session_start();
include_once ("config.php");
require_once('browscap.php');
$b=get_browser_ebrigade();

?>
<html>
<SCRIPT language=JavaScript>

function redirect(url) {
     self.location.href=url;
}

function open_popup(pgname, windowname)
{
window.open (pgname, windowname, config='height=250, width=400, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no, address=no')
return true;
}

</SCRIPT>

<?php
$name=str_replace("_","",strtolower($cisname));
$name=str_replace(".","",$name);
$name=str_replace(" ","",$name);
$name=str_replace("/","",$name);
$dbversion=get_conf(1);
$filesdir=get_conf(21);
if ( $filesdir == "" ) $filesdir=".";
$url=$identpage;

if ( isset($_POST["id"])) $id=mysql_real_escape_string($_POST["id"]); 
else $id="";
if ( isset($_POST["pwd"])) $pwd=mysql_real_escape_string($_POST["pwd"]);
else $pwd="";

$path=$filesdir."/save";

// ==================================
// upgrade database if needed
// ==================================
if (( check_ebrigade() == 1 ) and ( $version <> $dbversion )) {

   $filename='./sql/upgrade_'.$dbversion.'_'.$version.'.sql';
   $logname ='./sql/upgrade_log_'.$dbversion.'_'.$version.'.txt'; 
   $myupgrade = './sql/upgrade_'.$dbversion.'_'.$version.'.tmp';
   $tmpdbversion=$dbversion;
   $no_start=true;
   $no_end=true;
   
   $path='./sql';
   $sqldir = opendir($path);

   if (! @is_file($filename)) {
     if ( @is_file($myupgrade)) unlink($myupgrade);
	    $fh = fopen($myupgrade, 'w');

	 $i = 0;
     while ($f1 = readdir($sqldir)){
        if ($f1 != "." && $f1 != ".." && $f1 != "reference.sql" ) {
		 if (!is_dir($path.$f1)) {
		  		$path_parts = pathinfo("$f1");
		  		if ( $path_parts["extension"] == "sql" ) {
				   $filearray[$i] = $f1;
				   $i++;
				}	
		  }
        }
     }
     sort($filearray);

     for ($i=0; $i<sizeof($filearray); $i++){
	  		  $f1 = $filearray[$i];  	  
      	      $start = get_file_from_version($f1);
      	      $end = get_file_to_version($f1);
      	      if ( $tmpdbversion == $start ) {
      	       	$no_start=false;
      	       	$file=fread(fopen($path.'/'.$f1, "r"), 10485760);
				$query=explode(";",$file);
				for ($k=0;$k < count($query)-1;$k++) {
		           fwrite($fh, $query[$k].';'); 
      	           $tmpdbversion = $end;
      	         } 
      	        fwrite($fh, $query[$k].'');
			  } 
			  if ( $version == $end) $no_end=false;
     }
     closedir($sqldir);
     fclose($fh);
     if ( $no_start || $no_end ) unlink($myupgrade);
   }
    $upgerr=0;
    if ((! @is_file($filename)) && (@is_file($myupgrade)))
    	$filename = $myupgrade;
	if (@is_file($filename)) {
	 	if ( @is_file($logname)) unlink($logname);
	    $fh = fopen($logname, 'w');
        fwrite($fh,'upgrade de la base '.$database.' de la version '.$dbversion.' vers '.$version.'
');
	fwrite($fh, 'START :'.date("D M j G:i:s T Y").'
'); 
		@set_time_limit($mytimelimit);
		$file=fread(fopen($filename, "r"), 10485760);
		$query=explode(";
",$file);
		for ($i=0;$i < count($query)-1;$i++) {
		   fwrite($fh, $query[$i].'
'); 
		   if (! mysql_query($query[$i])) {
		 	   fwrite($fh, '***********************************
ERROR - '.mysql_error().'
***********************************
');
		 	   $upgerr=1;
	      }
	      else if ( mysql_affected_rows() <> 0 )
	      	fwrite($fh,'--> Lignes modifiées : '.mysql_affected_rows().'
');
	   }
	fwrite($fh, 'END :'.date("D M j G:i:s T Y").'
'); 
	fclose($fh);
	if (@is_file($myupgrade)) unlink($myupgrade);
	echo "<p>";
	if ( $upgerr == 0 ) 
	    write_msgbox("upgrade réussi", $star_pic, 
		"<p><font face=arial>La base de données à été upgradée<br> 
		 de la version <b>$dbversion</b><br>
		 à la version <b>$version</b><br>
		 sans erreurs. <a href=$logname target=_blank>voir le log d'upgrade</a><br>
		<p align=center><a href=index.php target=_top> $myspecialfont Se connecter</font>",10,0);
	else 
	     write_msgbox("erreur sql", $error_pic, 
		"<p><font face=arial>L'upgrade de la base de données <br> 
		 de la version <b>$dbversion</b><br>
		 à la version <b>$version</b><br>
		 à généré des erreurs. 
		 <a href=$logname target=_blank>voir le log d'upgrade</a><br>
		 corriger les erreurs rencontrées dans la base de données<br>
		 avant de vous connecter.
		 <p align=center><a href=index.php target=_top> $myspecialfont Se connecter</font>",10,0);
  }
  else {
	    write_msgbox("version des composants incompatible", $error_pic, 
		"<p><font face=arial>La base de données est incompatible avec le code de l'application web<br> 
		 version de la base de données:<b>$dbversion</b><br>
		 version de l'application web:<b>$version</b><br>
		 Vous devez manuellement exécuter les fichiers d'upgrade sur la base(voir répertoire sql)<br>
		<p align=center><a href=index.php target=_top> $myspecialfont Se connecter</font>",10,0);
  }
  echo "<p>";
  exit; 
}


      
// ==================================
// check parameters: try to connect
// ==================================
if ($id == "" ){
   write_msgbox("erreur connexion", $error_pic, $error_1."<br><p align=center><a href=$url>$myspecialfont retour</font></a> ",10,0);
}
elseif ($pwd == "" ){
   write_msgbox("erreur connexion", $error_pic, $error_2."<br><p align=center><a href=$url>$myspecialfont retour</font></a> ",10,0);
}
else {
     
     connect();
     
     // ==================================
	 // load reference schema if needed
     // ==================================
     if ( check_ebrigade() == 0 ) {
	  	$filename = "reference.sql";
		@set_time_limit($mytimelimit);
		$file=fread(fopen($sql.'/reference.sql', "r"), 10485760);
		$query=explode(";
",$file);
		for ($i=0;$i < count($query)-1;$i++) {
			mysql_query($query[$i]) or die(mysql_error());
		}
		echo "<p>";
		write_msgbox("initialisation réussie", $star_pic, 
			"<p><font face=arial>Schéma de base de données importé avec succès!<br> 
		 	Tu peux maintenant te connecter en utilisant le compte admin <br>
		 	<b>identifiant:</b> 1234<br>
		 	<b>password:</b> 1234<br>
			<p align=center><a href=index.php target=_top> $myspecialfont Se connecter</font>",10,0);
		echo "<p>";
		exit;
	}
    // ==================================
    // vérifier qu'un utilisateur existe
    // ==================================
     $query="select count(*) as 'NB'
        from pompier 
		where P_CODE='".$id."'
	 	and ( P_MDP=password('".$pwd."') 
			or P_MDP=old_password('".$pwd."') 
			or P_MDP=md5('".$pwd."'))";
     $result=mysql_query($query);
     $row=mysql_fetch_array($result);
     $NB=$row['NB'];
     if ( $NB != 1 ) {
        if ( $password_failure > 0 ) {
        	$query="update pompier set P_PASSWORD_FAILURE=P_PASSWORD_FAILURE + 1, P_LAST_CONNECT=NOW() where P_CODE='".$id."'
             	and P_PASSWORD_FAILURE is not null";
        	$result=mysql_query($query);
        	$query="update pompier set P_PASSWORD_FAILURE=1, P_LAST_CONNECT=NOW() where P_CODE='".$id."'
             	and P_PASSWORD_FAILURE is null";
        	$result=mysql_query($query);
        }
     	write_msgbox("erreur connexion", $error_pic, $error_3."<p align=center><a href=$url>$myspecialfont retour</font></a> ",10,0);
     	
     }
     else
     {

     // récupérer les infos
     $query="select P_ID,P_NOM,P_PRENOM,P_GRADE,P_STATUT, P_EMAIL, GP_ID, GP_ID2, C_ID,
	 		 P_SECTION, P_PASSWORD_FAILURE, round((UNIX_TIMESTAMP() - UNIX_TIMESTAMP(P_LAST_CONNECT)) / 60) 'LAST',
	 		 LENGTH(P_MDP) 'MDP_SIZE'
        	 from pompier where P_CODE='".$id."'";
     $result=mysql_query($query);

     $row=@mysql_fetch_array($result);
     $P_ID=$row['P_ID'];
     $C_ID=$row['C_ID'];
     $GP_ID=$row['GP_ID'];
     $GP_ID2=$row['GP_ID2']; if ( $GP_ID2 == '' ) $GP_ID2=$GP_ID;
     $P_NOM=$row['P_NOM'];	
     $P_GRADE=$row['P_GRADE'];
     $P_PRENOM=$row['P_PRENOM'];
     $P_STATUT=$row['P_STATUT'];
     $P_EMAIL=$row['P_EMAIL'];
     $P_SECTION=$row['P_SECTION'];
     $LAST=$row['LAST'];
     $P_PASSWORD_FAILURE=$row['P_PASSWORD_FAILURE'];
     $MDP_SIZE=$row['MDP_SIZE'];
     
     // passer les vieux mots de passe en MD5
     if ( $MDP_SIZE == 16  or $MDP_SIZE == 41 ) {
     	$query="update pompier set P_MDP=md5('".$pwd."') where P_ID=".$P_ID;
     	$result=mysql_query($query);
     }

	 if ( $password_failure > 0 ) {
     		if (( $P_PASSWORD_FAILURE >= $password_failure ) and ( $LAST <= $passwordblocktime )) {
     			write_msgbox("erreur connexion",$error_pic,"Le compte ".$id." est temporairement bloqué.\n Veuillez vous reconnecter dans 30 minutes ou contacter votre administrateur en lui demandant de changer votre mot de passe.<p align=center><a href=$url>$myspecialfont retour</font></a>",30,30);
     			session_destroy();
     			exit;
     		}
     		else {
     		    $query="update pompier set P_PASSWORD_FAILURE=null where P_CODE='".$id."'
             	and P_PASSWORD_FAILURE is not null";
     			$result=mysql_query($query);
     		}
     }
     
     $query=" select NOW() as DEBUT";
     $result=mysql_query($query);
     $row=@mysql_fetch_array($result);
     $DEBUT=$row['DEBUT'];
     
	 $A_OS = $b -> platform;
	 $A_BROWSER = $b -> parent;
     
     $_SESSION['id']=$P_ID;
     $_SESSION['groupe']=$GP_ID;
     $_SESSION['groupe2']=$GP_ID2;
     $_SESSION['SES_NOM']=$P_NOM;
     $_SESSION['SES_EMAIL']=$P_EMAIL;
     $_SESSION['SES_GRADE']=$P_GRADE;
     $_SESSION['SES_PRENOM']=$P_PRENOM;
     $_SESSION['SES_STATUT']=$P_STATUT;
     $_SESSION['SES_DEBUT']=$DEBUT;
     $_SESSION['SES_COMPANY']=$C_ID;
     $_SESSION['SES_BROWSER']=$A_BROWSER;
     $_SESSION['SES_SECTION']=$P_SECTION;
     $_SESSION['SES_PARENT']=get_section_parent($P_SECTION);
    
     if ( $auto_optimize == 1 ) {
     	$query=" select P_ID from audit
		         where TO_DAYS(NOW()) = TO_DAYS(A_DEBUT)";
     	$result=mysql_query($query);
     	if ( mysql_num_rows($result) == 0 ) { 
     		database_maintenance();
     		specific_maintenance();
     	}
     }
     
     // insérer dans la table d'audit
     $query="insert into audit (P_ID, A_DEBUT, A_OS, A_BROWSER) 
	 select ".$P_ID.",NOW(),'".$A_OS."','".$A_BROWSER."' ";
     $result=mysql_query($query);
     
     $query="update pompier set P_LAST_CONNECT=NOW(), P_NB_CONNECT= P_NB_CONNECT + 1 
	         where P_ID=".$P_ID;
	 $result=mysql_query($query);
     
     if ( $auto_backup == 1 ) {
     	//  backup de la base
     	if (!is_dir($path)) mkdir($path, 0777);
     	$cur_datetime=date("Y-m-d");
     	$backupfile=$path."/".$name."_".$cur_datetime."_".$dbversion.".sql";
     	if (! is_file($backupfile)) {
	 		include_once ("backup.php");
	 	}
	 }
    
	 if ( $GP_ID >= 0 and $GP_ID2 >= 0) {
	 	echo "<body onload=redirect('index.php')></body>";
	 }
     else {
		write_msgbox("erreur connexion",$error_pic,"Ce compte est interdit d'accès.<p align=center><a href=$url>$myspecialfont retour</font></a>",30,30);
		session_destroy();
	   }
     }
}
     
?>
</html>
