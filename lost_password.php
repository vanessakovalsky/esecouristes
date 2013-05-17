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
writehead();

// iPhone specific
echo "<meta name='viewport' content='width=380'/>";
// end iPhone specific

$url=$identpage;
if (isset($application_title_specific)) $application_title = $application_title_specific;

if (isset($_POST["matricule"])) $matricule = $_POST["matricule"];
else $matricule = "";

if (isset($_POST["email"])) $email = $_POST["email"];
else $email = "";
//print_r($_GET)
if (isset($_GET["session"])) 
$numdemande = $_GET["session"];
//else $session = "";

// =====================================
// Verify parameters 
// =====================================
if ( ($matricule <> "") and ( $email <> "") ) {
 
 	// check input parameters
	$matricule=mysql_real_escape_string($matricule);
 	$email=mysql_real_escape_string($email);
 
	$query="select P_ID, P_NOM, P_PRENOM
        from pompier 
		where P_CODE='".$matricule."'
	 	and P_EMAIL =  '".$email."'
		and GP_ID >= 0 ";
     $result=mysql_query($query);
     
     if ( mysql_num_rows($result) == 1 ) {
       $row=mysql_fetch_array($result);
       $P_ID=$row['P_ID'];
       $P_PRENOM=$row['P_PRENOM'];
       $P_NOM=$row['P_NOM'];
	   $secret = generateSecretString();
	   $query="delete from demande
	   	       where P_ID = '".$P_ID."'
	   	       and D_TYPE = 'password'";
	   $result=mysql_query($query);	
	   
	   $query="insert into demande ( P_ID, D_TYPE, D_SECRET , D_DATE )
	   	       values ( '".$P_ID."' , 'password', '".$secret."', NOW() )";
	   $result=mysql_query($query);	
	
	   $Mailcontent = "Bonjour ".ucfirst($P_PRENOM).",\n\n";	
	   $Mailcontent .= "Vous avez demandé un renouvellement de votre mot de passe.\n";
	   $Mailcontent .= "Veuillez confirmer cette demande en cliquant sur le lien suivant:\n";
	   $Mailcontent .= "http://".$cisurl."/lost_password.php?session=".$secret;
	   $Subject = "[".$cisname."] Confirmation $application_title pour ".ucfirst($P_PRENOM)." ".strtoupper($P_NOM);
	   
	   $nb = mysubsendmail("$email","$Subject","$Mailcontent","Admin $cisname","$email",1);
	   
	   write_msgbox("demande prise en compte", $star_pic, "Vous allez recevoir un email contenant un lien URL. En cliquant dessus vous confirmerez la demande de renouvellement de mot de passe.<p align=center><a href=$url>$myspecialfont retour</font></a> ",10,0); 

	  }
	  else {
	       	write_msgbox("erreur de paramètres", $error_pic, $error_7." Ou encore le compte est interdit d'accès.<p align=center>
			   <a href=$url>$myspecialfont retour</font></a> ",10,0);
      }
}

// =====================================
// confirmation
// =====================================
else if ($numdemande <> "") {
	//echo $numdemande; 
 	 $session=mysql_real_escape_string($numdemande);
	 $query="select d.P_ID, p.P_PRENOM, p.P_NOM, p.P_EMAIL
        from demande d, pompier p
		where d.D_TYPE='password'
		and p.P_ID = d.P_ID 
	 	and d.D_SECRET = '".$session."'";
	 //echo $query;
     $result=mysql_query($query);
     //echo $session;
	 if ( mysql_num_rows($result) > 0 ) {
       $row=mysql_fetch_array($result);
       $P_ID=$row['P_ID'];
       $P_PRENOM=$row['P_PRENOM'];
       $P_NOM=$row['P_NOM'];
       $email=$row['P_EMAIL'];
	   if ($password_length == 0) $password_length=6;
	   $newpass = generatePassword($password_length);
	   
	   $query="update pompier set P_MDP=md5('".$newpass."'), P_PASSWORD_FAILURE=null
     	     where P_ID=".$P_ID;
       $result=mysql_query($query);
       
       $query="delete from demande
	   	       where P_ID = '".$P_ID."'
	   	       and D_TYPE = 'password'";
	   $result=mysql_query($query);

	   $Mailcontent = "Bonjour ".ucfirst($P_PRENOM).",\n\n";	   
	   $Mailcontent .= "Votre mot de passe a été changé.\n\n";
	   $Mailcontent .= "$newpass\n\n";
	   $Mailcontent .= "Vous pourrez le changer une fois connecté(e).\n";
	   $Mailcontent .= $title." - http://".$cisurl;
	   $Subject = "[".$cisname."] nouveau de mot de passe $application_title pour ".ucfirst($P_PRENOM)." ".strtoupper($P_NOM);
	   
	   //echo $Subject."<br>";
	   //echo nl2br($Mailcontent);
	   
	   mysubsendmail("$email","$Subject","$Mailcontent","Admin $cisname","$email",1);
	   
	   write_msgbox("nouveau mot de passe généré", $star_pic, "Vous allez recevoir un email contenant votre nouveau mot de passe.<p align=center><a href=$url>$myspecialfont retour</font></a> ",10,0); 
	   
	}
	else {
	
	    write_msgbox("erreur de paramètres", $error_pic, "Aucune demande de renouvellement de mot de passe correspondant à votre session n'a été enregistrée aujourd'hui<p align=center>
			   <a href=$url>$myspecialfont retour</font></a> ",10,0);
	
	}
}

// =====================================
// Demande 
// =====================================
else {

if ( $grades == 1) $str="Matricule";
else  $str="Identifiant";

echo "<body >
<div id='Layer1' align=center>
<form name='form' action='lost_password.php' method=post>
<TABLE>
<TR>
<TD class='FondMenu'>
  <table width='343' cellspacing='0' border='0' height='147'>
    <tr height=28 class=TabHeader>
      <td width='20' >&nbsp;</td>
      <td width='60'></td>
      <td width='264' align=right colspan=2>$cisname - mot de passe perdu</td>
    </tr>
	<tr height=37 bgcolor='$mylightcolor'> 
      <td width='20'></td>
      <td width='60'></td>
      <td width='363' colspan=2>
	    <i>Vous pouvez demander un nouveau mot de passe, en indiquant votre $str et votre <br>
		adresse email (qui doit déjà être enregistrée).
		<p>Par contre si votre identifiant est perdu, alors vous devrez vous adresser à votre responsable.
		</i>
	  </b></td>

    </tr>
    <tr height=40 bgcolor='$mylightcolor'> 
      <td width='20' ></td>
      <td width='60' ></td>
      <td width='111' ><b> $str</b></td>
      <td width='152' ><input type='text' name='matricule'>
    </tr>
    <tr height=37 bgcolor='$mylightcolor' > 
      <td width='20'></td>
      <td width='60'><img src=images/mail.gif></td>
      <td width='211'><b>adresse email</b></td>
      <td width='152'><input type='text' name='email'>
    </tr>
    <tr height=40 bgcolor='$mylightcolor' > 
      <td width='20'></td>
      <td width='60'></td>
      <td width='211'></td>
      <td width='152'>
      <input type='submit' value='envoyer' onClick=\"this.disabled=true;this.value='attendez';document.form.submit()\">
      </td>
    </tr>
  </table>
</td></tr></table></form>";
echo "<p align=center><input type=button value='retour' onclick='javascript:history.back(1);'>";
}
?>
    
