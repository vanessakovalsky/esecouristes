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

  //version
  $version="2.6";
  $application_title="eBrigade";
   
  // use http://www.coolarchive.com/logogenerator.php
  // police Romeo
  
  // BLUE ENVIRONMENT 
  $mydarkcolor="#000099";
  $mylightcolor="#B7D8FB";
  $my2darkcolor="#FFCC33";
  $my2lightcolor="#FFFF99";
  $myothercolor='#FFC0C0';
  
  //grey
  $mygreycolor='#C3C3C3';
  
  //green
  $mygreencolor='#A5E7A5';

  // Déclaration des variables de configuration
  $background = "#FFFFFF" ;                                 // Couleur de fond
  $textcolor = "#00006B" ;                                  // Couleur du texte
  $fontfamily = "Arial" ;                                   // Police d'écran
  
  // colors
  $purple="purple";
  $red="red";
  $green="green";
  $brown="#996633";
  $orange="orange";
  $blue="#0000CC";
  
  //identification repertoire  
  $basedir = dirname(__FILE__);
  $curdir = getcwd();
  if ( $basedir <> $curdir ) {
  	$reldir = str_replace ( $basedir,'',$curdir);
  	$reldir = str_replace ( "\\","/",$reldir );
  	$subcnt = substr_count($reldir, "/"); 
  	if ( $subcnt == 1 ) $basedir = '..';
  	if ( $subcnt == 2 ) $basedir = '../..';
  }
  else $basedir='.';
  
  //messages d'erreur
  $error_pic="<img src='".$basedir."/images/no.png'>";
  $question_pic="<img src='".$basedir."/images/question.png'>";
  $warning_pic="<img src='".$basedir."/images/warn.png'>";
  $star_pic="<img src='".$basedir."/images/ok.png'>";
  $miniquestion_pic="<img src='".$basedir."/images/miniquestion.png' border=0 alt='voir les habilitations des groupes' title='voir les habilitations des groupes'>";
  
  $error_1="Vous devez saisir le matricule";
  $error_2="Vous devez saisir le mot de passe";
  $error_3="Le matricule ou le mot de passe saisis ne sont pas reconnus.";
  $error_4="Votre session a expiré, vous devez vous identifier.";
  $error_5="Identification obligatoire";
  $error_6="Vous n'êtes pas habilités à utiliser cette fonctionnalité de l'application";
  $error_7="Le matricule ou l'adresse email saisis ne sont pas reconnus.";
  $question_1="Etes vous certain de vouloir supprimer ";

  $mois=array("janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
  
  $myspecialfont="<font size=2 face=$fontfamily color=$textcolor 
  	onMouseOver=\"this.style.fontWeight='bold';this.style.color='yellow';this.style.textDecoration='None'; \"
    onMouseOut=\"this.style.fontWeight='normal';this.style.color='$mydarkcolor';this.style.textDecoration='underline';\">";
  
 //nombre maximum de backups.
  $sql=$basedir."/sql/";
  $nbfiles=15;

 // Phone prefix
  $phone_prefix="33";


 // fichiers evenements
  $supported_ext = array("doc","docx","zip","ppt","pptx","xls","xlsx","pdf","pdf","jpg","png"); 

 // config
  $config_file=$basedir."/conf/sql.php";
  $config_file_optional=$basedir."/conf/optional.php";

 // optional user guides
  $userguide=$basedir."/doc/userguide.doc";
  $adminguide=$basedir."/doc/adminguide.pdf";
  
 // photos uidentite
  $trombidir=$basedir."/images/user-specific/trombi";
 
 // durée de conservation des données en purge glissante.
  $days_audit=2;
  $days_log=100;
  $days_smslog=1825;
  $days_disponibilite=60;
  $days_planning_garde=200;
  
  // nombre maxi de personnes
  $nbmaxpersonnes=30000; 
  $nbmaxpersonnesparsection=200; 
  
 // nombre maxi de postes à la garde ou de compétences 
  $nbmaxpostes=200; 

 // nombre maxi d'équipes à la garde ou de types de compétences
  $nbmaxequipes=15; 
  
  // nombre maxi de sections
  $nbmaxsections=1000; 
  
  // nombre maxi de niveaux hiérarchiques 
  $nbmaxlevels=5;
  // ordre par défaut (pages personnel, evenements ...)
  $defaultsectionorder='alphabetique'; 
  
  // nombre maxi de groupes utilisateurs ou rôles dans organigramme
  $nbmaxgroupes=40; 
  
  // envoi des notifications en mode test seulement
  //$testmail="only.for.me@mycis.org";
  
  // extended time limit (used in backup / restore), tableau garde
  $mytimelimit=180;
  
  // mail limit
  $mymailmaxdest=1;

  // mail limit
  $nbmaxsessionsparevenement=8;
  
  // output max number of rows
  $maxnumrows=200;
  
  // nombre max de destinataires dans la page message
  $maxdestmessage=400;
  
  // nombre maxi de messages dans le chat
  $maxchatmessages=20;
  
  // password failed block time in minutes (default 30 minutes)
  $passwordblocktime=30;
  
  // impression diplomes
  $numfields_org=10;
  $aff_org=array("NOM Prénom","NOM PRENOM","Nom Prénom","Date diplôme","Période formation","Lieu naissance",
				 "Date de naissance","N° diplôme","Date fin de cours","Personnalisé",
				 "Organisateur formation","Ville organisateur");
  // permettre impression totale du diplôme y compris le fond, sur du papier blanc.
  $printfulldiplome=false;
  
  // colors report
  $colors = array(0xff0000, 0x3300CC, 0x00cc00,
  				  0xff9900, 0xFF99FF, 0x00CC99, 
				  0x996699, 0xFFCC33, 0x666666,  
				  0xa0bdc4, 0x999966, 0x333366, 
				  0xc3c3e6, 0xc3c3e5, 0xc3c3e3, 
				  0xFF3366, 0x5c88c4, 0xf488c4,
				  0xba4a4a, 0x97ba99, 0x972399,
				  0x653851, 0x133851, 0x51fa13,
				  0xfa1337, 0x1e0207, 0xd1df07,
				  0xd1dfb9
				  );

  if ( file_exists($config_file)) {
        include_once ($config_file);
  }
  if ( file_exists($config_file_optional)) {
        include_once ($config_file_optional);
  }
  include_once ($basedir."/fonctions.php");
  include_once ($basedir."/fonctions_gardes.php");
  include_once ($basedir."/fonctions_specific.php");

  global $noconnect;
  if ( ! isset($noconnect)) {
  	connect();
  	$already_configured=get_conf(-1);
  	$dbversion=get_conf(1);
  	$nbsections=get_conf(2);
  	$gardes=get_conf(3);
  	$vehicules=get_conf(4);
  	$grades=get_conf(5);
  	$cisname=get_conf(6);
  	$cisurl=get_conf(7);
  	$admin_email=get_conf(8);
  	$sms_provider=get_conf(9);
  	$sms_user=get_conf(10);
  	$sms_password=get_conf(11);
  	$sms_api_id=get_conf(12);
  	$auto_backup=get_conf(13);
  	$auto_optimize=get_conf(14);
  	$password_quality=get_conf(15);
  	$password_length=get_conf(16);
  	$password_failure=get_conf(17);
  	$materiel=get_conf(18);
  	$chat=get_conf(19);
  	$identpage=get_conf(20);
  	$filesdir=get_conf(21);
  	$evenements=get_conf(22);
  	$competences=get_conf(23);
  	$disponibilites=get_conf(24);
  	$log_actions=get_conf(25);
	$badges_equipes="1,2"; 
  }
  if ( isset ($cisname)) $title="$cisname";
  else $title="ebrigade";
  
  // dénominations
  $niv0="national";
  $niv1="zone";
  $niv2="région";
  $niv3="département";
  $niv4="antenne";
  if ( isset ($nbsections ))
  	if ( $nbsections > 0 ) $niv3="section";
  	
  // spécifique pour attestations
  $attestation_dept_name="l'Association Départementale de Protection Civile";
  $attestation_complement1="Cette attestation autorise l’autorité d’emploi de";
  $attestation_complement2="à l’inscrire sur une liste d’aptitude permettant l’emploi en qualité de";

  //utilise uniquement  pour une compétence officielle de secourisme
  $attestation_complement3="à condition que les autres modalités de l’arrêté du 24/05/2000 soient satisfaites.";
  $attestation_arretes="Vu la loi n° 2004-811 du 13/08/04 de la modernisation de la Sécurité Civile ;\n
Vu le décret n° 91-834 du 30/08/91 modifié, relatif à la formation aux premiers secours ;\n
Vu le décret n°97-48 du 20/01/97 portant diverses mesures relatives au secourisme ;\n
Vu le décret n° 2006-237 du 27/02/06 relatif à la procédure d’agrément de sécurité civile, notamment ses articles 1 et 3 ;\n 
Vu l’arrêté du 08/07/92modifié, relatif aux conditions d’habilitation ou d’agrément pour les formations aux premiers secours ;\n
Vu l’arrêté du 24/05/00 portant organisation de la formation continue dans le domaine des premiers secours ;\n";

function connexion_bdd() {
	// Configuration pour la base de données 

try
{
	$bdd = new PDO('mysql:host=localhost;dbname=esecouristes', 'root', 'b2Emi0902*');
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}
return $bdd;
}
?>
