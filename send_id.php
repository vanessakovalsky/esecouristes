<?php

  # written by: Nicolas MARCHE, Jean-Pierre KUNTZ
  # contact: nico.marche@free.fr
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

include("config.php");
check_all(0);

$id=$_SESSION['id'];
$pid=intval($_GET["pid"]);

$section=get_section_of($pid);

if (! check_rights($id, 25,"$section"))
check_all(9);

$query="select p.P_ID, p.P_NOM, p.P_CODE, p.P_PRENOM, p.P_EMAIL
        from pompier p
		where p.P_ID = ".$pid;
$result=mysql_query($query);
     
$row=mysql_fetch_array($result);
$P_ID=$row['P_ID'];
$P_PRENOM=$row['P_PRENOM'];
$P_NOM=$row['P_NOM'];
$P_CODE=$row['P_CODE'];
$email=$row['P_EMAIL'];
if ($password_length == 0) $password_length=6;
$newpass = generatePassword($password_length);
	   
$query="update pompier set P_MDP=md5('".$newpass."'), P_PASSWORD_FAILURE=null
     	     where P_ID=".$P_ID;
$result=mysql_query($query);

if (isset($application_title_specific)) $application_title = $application_title_specific;

$Mailcontent = "Bonjour ".ucfirst($P_PRENOM).",\n\n";	   
$Mailcontent .= "Voici vos informations de connexion $application_title.\n\n";
$Mailcontent .= "Identifiant: $P_CODE\n\n";
$Mailcontent .= "Mot de passe: $newpass\n\n";
$Mailcontent .= "Vous pourrez les changer une fois connecté(e).\n";
$Mailcontent .= $title." - http://".$cisurl;
$Subject = "[".$cisname."] identifiants $application_title pour ".ucfirst($P_PRENOM)." ".strtoupper($P_NOM);
	   	   
mysubsendmail("$email","$Subject","$Mailcontent","Admin $cisname","$email",1);

insert_log('REGENMDP', $P_ID);
	   
write_msgbox("identifiants renvoyés", $star_pic, "Un email contenant l'identifiant et un nouveau mot de passe ont été envoyés à ".ucfirst($P_PRENOM)." ".strtoupper($P_NOM)."<p align=center><a href='javascript:history.back(1)'>$myspecialfont retour</font></a> ",10,0); 

?>
	   
	   