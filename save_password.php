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

$new1=mysql_real_escape_string($_GET["new1"]);
$new2=mysql_real_escape_string($_GET["new2"]);
$person=intval($_GET["person"]);
$section=get_section_of($person);
$matricule=get_matricule($person);

if ( $person <> $id ) {
   if (! check_rights($id, 25,"$section"))
	  check_all(9);
}
if ($new1 =="" ) {
    write_msgbox("erreur mot de passe",$error_pic,"le nouveau mot de passe doit �tre renseign� <br><p align=center><a href=change_password.php>$myspecialfont retour</font></a>",30,30);
    exit;
}

//======================
// check duplicate
//======================

elseif ($new1 <> $new2) {
    write_msgbox("erreur mot de passe",$error_pic,"les 2 valeurs saisies pour le nouveau mot de passe sont diff�rentes<br><p align=center><a href=change_password.php>$myspecialfont retour</font></a>",30,30);
    exit;
}

//======================
// check quality
//======================

if ( $password_quality == 1 ){
  $pos = strpos($new1, $matricule);
  if (($pos == true ) or ( substr($new1,0,2) == substr($matricule,0,2)))  { 
    	write_msgbox("erreur mot de passe",$error_pic,"le mot de passe ne doit pas �tre bas� sur votre identifiant.<br><p align=center><a href=change_password.php>$myspecialfont retour</font></a>",30,30);
    	    exit;
  }
  if (! preg_match("/.*[0-9].*/","$new1" )){
      	write_msgbox("erreur mot de passe",$error_pic,"le mot de passe doit aussi contenir des chiffres.<br><p align=center><a href=change_password.php>$myspecialfont retour</font></a>",30,30);
    	    exit;
  }
  if (! preg_match("/.*[a-zA-Z].*/","$new1" )){
      	write_msgbox("erreur mot de passe",$error_pic,"le mot de passe doit aussi contenir des lettres.<br><p align=center><a href=change_password.php>$myspecialfont retour</font></a>",30,30);
    	    exit;
  }
  if ( preg_match("/\"|\'/","$new1" )){
      	write_msgbox("erreur mot de passe",$error_pic,"le mot de passe ne doit pas contenir d'apostrophes ou guillemets.<br><p align=center><a href=change_password.php>$myspecialfont retour</font></a>",30,30);
    	    exit;
  }
}

//======================
// check length
//======================

if ( $password_length > 0 ){
	if (strlen("$new1") < $password_length ) {
      	write_msgbox("erreur mot de passe",$error_pic,"le mot de passe est trop court. Il doit avoir au moins $password_length caract�res.<br><p align=center><a href=change_password.php>$myspecialfont retour</font></a>",30,30);
        exit;
    }
}

$query="update pompier set P_MDP=md5('".$new1."'), P_PASSWORD_FAILURE=null
     	where P_ID=convert('".$person."',UNSIGNED)";
$result=mysql_query($query);

insert_log('UPDMDP', $person);

write_msgbox("changement r�ussi",$star_pic,"le mot de passe a �t� modifi� avec succ�s<br><p align=center><a href=index_d.php >$myspecialfont retour</font></a>",30,30);

 
?>
