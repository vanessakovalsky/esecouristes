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
$SES_NOM=$_SESSION['SES_NOM'];
$SES_PRENOM=$_SESSION['SES_PRENOM'];
$SES_GRADE=$_SESSION['SES_GRADE'];
$groupe=$_SESSION['groupe'];
$groupe2=$_SESSION['groupe2'];
if ( $nbsections == 1) $section = 0; 
else $section=$_SESSION['SES_SECTION'];
$parent=$_SESSION['SES_PARENT'];

writehead();

$iphone=is_iphone();

if ($chat) {

echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<script type='text/javascript' src='prototype.js'></script>
<script type='text/javascript' src='updater.js'></script>
<script type='text/javascript'>
// <![CDATA[
document.observe('dom:loaded', function() {
	/*
	first arg	: div to update
	second arg	: interval to poll in seconds
	third arg	: file to get data
	*/
var visitorCounter = new updater('counter', 10, 'chat_message.php?counter=1');
visitorCounter.getUpdate();
});
// ]]>
</script>
</head>";	
}

if (isset($application_title_specific)) $application_title=$application_title_specific;

echo "<div style='position:absolute;  left: 15px; top: 5px'>";
echo "<div align=center>";
if ( is_file('images/user-specific/logo.jpg'))
 	$logo='images/user-specific/logo.jpg';
else 
 	$logo='images/logo.jpg';
echo "<a href=index_d.php target='droite'><img src=".$logo."  border=0 height='70' title='Accueil $application_title'></a>"; 
echo "</div>";

echo "
<TABLE>
<TR>
<TD class='FondMenu' valign='top'><TABLE BORDER='0' CELLPADDING='0' CELLSPACING='0' WIDTH=150></TD>";

// ==================================
// v�hicules/mat�riel
// ==================================

if (( $vehicules == 1 or  $materiel == 1 ) and ( check_rights($_SESSION['id'], 42) )) {
	echo"	
	<TR><TD CLASS='MenuRub'>Inventaire</TD></TR>
	<TR><TD class='charte'></TD></TR>";
	
	if ( $vehicules == 1 ) {
		echo "<TR><TD CLASS='Menu' nowrap>";
		echo "<a href='vehicule.php' target='droite' class=s>V&eacute;hicules</a>";
		if ( check_rights($_SESSION['id'], 17) ) {
		echo " / <a href='evenement_vehicule.php' 
			target='droite' class=s>Engagements</a>";
		}
		echo "</TD></TR>";
	}
	if ( $materiel == 1 ) {
		echo "<TR><TD CLASS='Menu' nowrap>";
		echo "<a href='materiel.php' target='droite' class=s>Mat&eacute;riel</a>";
		if ( check_rights($_SESSION['id'], 17) ) {
		echo " / <a href='evenement_materiel.php' 
			target='droite' class=s>Engagements</a>";
		}
		echo "</TD></TR>";
	}
	echo "<TR><TD class='charte'></TD>
	</TR>";
}

// ==================================
// personnel
// ==================================

if ( check_rights($_SESSION['id'], 40)) {
//if (( substr($_SESSION['SES_BROWSER'],0,2) == 'IE' ) || ( substr($_SESSION['SES_BROWSER'],0,7) == 'Firefox' ))
$searchpage='search_personnel2.php';
//else $searchpage='search_personnel.php';

echo "
<TR><TD CLASS='MenuRub'>Personnel</TD></TR>
<TR><TD class='charte'></TD></TR>
<TR>
<TD CLASS='Menu' nowrap>
<a href='personnel.php?category=interne&position=actif' target='droite' class=s>Personnel</a>
 / <a href='personnel.php?category=interne&position=ancien' target='droite' class=s>Anciens</a> <BR>";
echo "<a href='".$searchpage."' target='droite' class=s>Recherche</a>";
if ( $gardes == 1 ) $title="Affectations";
else $title="Comp&eacute;tences";
if ( $competences == 1 )
echo" / <a href='qualifications.php?pompier=0' target=droite class=s>$title</a><BR>
</TD>
</TR>";
}
// ==================================
// externes
// ==================================

if ( $nbsections == 0  and $competences == 1 and 
(check_rights($_SESSION['id'], 37) or 
(check_rights($_SESSION['id'], 45) and $_SESSION['SES_COMPANY'] > 0))) {
echo "
<TR><TD CLASS='MenuRub'>Externes</TD></TR>
<TR><TD class='charte'></TD></TR>
<TR>
<TD CLASS='Menu' nowrap>";

echo "<a href='personnel.php?category=EXT&position=actif' target='droite' class=s>Personnel externe</a>";

if (check_rights($_SESSION['id'], 37))
	echo "<BR><a href='company.php' target='droite' class=s>Entreprises clientes</a>";
if (check_rights($_SESSION['id'], 45) && $_SESSION['SES_COMPANY'] <> 0)
	echo "<BR><a href='upd_company.php?C_ID=".$_SESSION['SES_COMPANY']."' target='droite' class=s>Mon Entreprise</a>";
echo "
</TD>
</TR>"; 
}

// ==================================
// dispos
// ==================================
if ( $disponibilites == 1) {
	if ( check_rights($_SESSION['id'], 38) or check_rights($_SESSION['id'], 11)) {
	echo "
	<TR><TD CLASS='MenuRub'>Disponibilit&eacutes;s</TD></TR>
	<TR><TD class='charte'></TD></TR>
	<TR>
	<TD CLASS='Menu' nowrap>";

	if ( check_rights($_SESSION['id'], 38))
	echo "<a href='dispo.php?person=$id' target='droite' class=s>Saisie dispos</a><BR>
	<a href='dispo_view.php' target='droite' class=s>Personnel disponible</a><BR>
	<a href='dispo_month.php' target='droite' class=s>Bilan mois</a> / <a href='dispo_homme.php' target='droite' class=s>homme</a><br>";
	if ( check_rights($_SESSION['id'], 11))
	echo "<a href='indispo_choice.php' target='droite' class=s>Absences</a>  / <a href='indispo.php' target='droite' class=s>Saisie</a><BR>";
	echo "</TD>
	</TR>";
	}
}
// ==================================
// gardes
// ==================================

if ( $gardes == 1 ) {
	echo"
	<TR><TD CLASS='MenuRub'>Gardes</TD></TR>
	<TR><TD class='charte'></TD></TR>
	<TR>
	<TD CLASS='Menu'>";

	if ( check_rights($_SESSION['id'], 8)) {
   		echo "<a href=\"message.php?catmessage=consigne\" target=\"droite\" class=s>Consignes</a><BR>";
	}

	echo "<a href='tableau_garde.php' target='droite' class=s>Tableau</a>";
	if ( $vehicules == 1 ) 
	 echo " / <a href='grille_depart.php' target='droite' class=s>Grille d&eacute;part</a>";
	echo "<BR><a href='bilan_garde.php' target='droite' class=s>R&eacute;partition</a> / 
	<a href='garde_jour.php' target='droite' class=s>Garde jour</a>
	</TD>
	</TR>";
}

// ==================================
// reporting
// ==================================
if (check_rights($_SESSION['id'], 27) and ( $evenements == 1 ) and
		(($nbsections == 0) or (@is_dir("./ChartDirector")))) {
   echo "
	   <TR><TD CLASS='MenuRub'>Statistiques</TD></TR>
	   <TR><TD class='charte'></TD></TR>";
   if ( @is_dir("./ChartDirector")) {
	 echo "
	   <TR>
	   <TD CLASS='Menu'>
	   <a href='repo_events.php' target='droite' class=s>Graphiques</a>";
	   if ( $SES_NOM == 'admin' ) {
		   echo " / <a href='ChartDirector/phpdemo' target='_blank' class=s>D�mo</a>"; 
	       echo "
	       ";
	   }
	   echo "</TD></TR>";
	}
	if ( $nbsections == 0 ){
	  if (!$iphone) {
    	if ( @is_dir("./france_map_3.0")){
    		echo "<TR>
   			<TD CLASS='Menu'>
   			<a href='france_map_3.0' target='droite' class=s title='Cartes de france de membres et des activit�s'>
			   Cartes de France</a>
   			</TD>
   			</tr>";
    	}
      }
      echo "<tr><td CLASS='Menu'><a href='bilan_participation.php' target='droite' class=s>Participations
	      </a>";
      echo " / <a href='export.php' target='droite' class=s>Reporting<br />
		  </a></td></tr>";
	// On affiche le bilan
	echo "<tr><td CLASS='Menu'><a href='bilan.php' target='droite' class=s>Bilan Annuel
	      </a>";
	echo " / <a href='bilan-national-vue.php' target='droite' class=s>National</a>
		</td></tr>";
	}
}
// ==================================
// infos
// ==================================

echo "
<TR><TD CLASS='MenuRub'>Informations</TD></TR>
<TR><TD class='charte'></TD></TR>
<TR>
<TD CLASS='Menu'>";
if ( check_rights($_SESSION['id'], 41) or 
	(check_rights($_SESSION['id'], 45) and $_SESSION['SES_COMPANY'] > 0))
if ( $evenements == 1 ){	
	echo "<a href='evenement_choice.php' target='droite' class=s>Ev&egrave;nements</a> / ";
	echo "<a href='calendar.php' target='droite' class=s title='Voir mon calendrier'>Calendrier</a><BR>";
}
if ( check_rights($_SESSION['id'], 44) ) {
	echo "<a href='message.php?catmessage=amicale' target='droite' class=s>Infos</a>";
	if ( $nbsections <> 1 ) {
		echo " / <a href='section.php' target=\"droite\" class=s 
			title=\"Voir l'organigramme de ".$cisname."\">Organigramme</a><BR>";
	}
	else echo " / ";
	
    if ( get_level("$section") >= $nbmaxlevels -1 or $nbsections > 0 ) {
		if ( $parent < 0 ) $loc=0;
		else $loc=$parent;
	}
	else $loc=$section;
	$tit="Voir documents attach&eacute;s &agrave; mon ".$niv3;
	
    echo "<a href='upd_section.php?S_ID=".$loc."#documents' target=\"droite\" class=s 
			title=\"".$tit."\">Documents</a>";
}
echo "</TD>
</TR>";


// ==================================
//Comptabilit�
// ==================================

echo "
<TR><TD CLASS='MenuRub'>Comptabilit&eacute;</TD></TR>
<TR><TD class='charte'></TD></TR>
<TR>
<TD CLASS='Menu'>";
if ( check_rights($_SESSION['id'], 41) or 
	(check_rights($_SESSION['id'], 45) and $_SESSION['SES_COMPANY'] > 0))
if ( $evenements == 1 ){	
	echo "<a href='devis.php' target='droite' class=s>Devis</a> / ";
}
echo "</TD>
</TR>";

// ==================================
// Sessions
// ==================================

echo "<TR><TD CLASS='MenuRub'>Session</TD></TR>
<TR><TD class='charte'></TD></TR>
<TR>
<TD CLASS='Menu'>
<a href='deconnexion.php' target='droite' class=s title=\"se d&eacute;connecter de l'application\">D&eacute;connexion</a>";
if ( check_rights($_SESSION['id'], 20) ) { 
	echo " / <a href='audit.php' target=\"droite\" class=s title=\"Voir l'historique des connexions\">Audit</a>";
}

$query="SELECT substr( @@version , 1, 1 ) as V";
$result=mysql_query($query);
$row = mysql_fetch_array($result);
$V=$row["V"];

if (check_rights($_SESSION['id'], 43) ) {
if ($chat) {
// compteur si Mysql > 4
if ( $V > 4 ) $cnt="<div id='counter'></div>";
else $cnt="Online";

echo "<a href='chat.php' target='droite' class=s 
	title=\"Communication par messagerie instantann&eacute;e avec les autres personnes connect&eacute;es\">".$cnt."</a>";
}
else echo "<br>";

if ( @is_dir("./spgm") and check_rights($_SESSION['id'], 44) ) {
    echo "<a href='spgm/index.php' target='droite' class=s 
	title=\"Album photos\">Album photos</a><br>";
}

if ( ( check_rights($_SESSION['id'], 43) and $competences == 1 )
  or ( check_rights($_SESSION['id'], 14) and $competences == 0))
echo "<a href='mail_create.php' target='droite' class=s title='envoyer un message'>Message</a> / 
<a href='alerte_create.php' target='droite' class=s title='alerter une partie du personnel'>Alerte</a>";

}
echo "
<BR><a href='change_password.php?meonly=true' target='droite' class=s title='Modifier mon mot de passe'>Mot de passe</a> / 
<a href='upd_personnel.php?pompier=$id' target='droite' class=s title='Voir et modifier ma fiche personnel'>Mes infos</a><br>";
echo "</TD>
</TR>";

// ==================================
// param�trage
// ==================================
if ($competences == 1 or $evenements == 1 or $materiel == 1)
 if ( check_rights($_SESSION['id'], 9)  or  check_rights($_SESSION['id'], 18)) {
   echo "<TR><TD class=\"charte\"></TD></TR>
         <TR><TD CLASS=\"MenuRub\">Param&eacute;trage</TD></TR>
	 <TR><TD class=\"charte\"></TD></TR>
	 <TR>
	 <TD CLASS=\"Menu\">";
   	 if ( $competences )
	    echo "<a href='equipe.php' target=\"droite\" class=s>Types de comp&eacute;tences</a><BR>";
	 if ( $competences )
	 	echo "<a href='poste.php?order=PS_ID&filter=ALL' target=\"droite\" class=s>Comp&eacute;tences</a>";
	 if ( $evenements ){
	 	if ( $competences ) echo " / ";
	 	echo "<a href='paramfn.php' target=\"droite\" class=s>Fonctions</a>";
	 }
	 if ( $materiel or $competences) echo "<BR>";
	 if ( $materiel ) 
	 	echo "<a href='type_materiel.php' target=\"droite\" class=s>Mat&eacute;riel</a>";
	 if ( $competences ) {
	 	if ( $materiel ) echo " / ";
     	echo "<a href='diplome_edit.php' target=\"droite\" class=s>Dipl&ocirc;mes</a>";
	 }
	 echo " / ";
	 echo "<a href='prestation.php' target=\"droite\">Prestations</a>";
         echo " / ";
         echo "<a href='type_evenement.php' target=\"droite\">Type &eacute;v&egrave;nement</a>";
	 echo "</TD></TR>";
}

// ==================================
// administration
// ==================================

if (( check_rights($_SESSION['id'], 14) ) 
	or ( check_rights($_SESSION['id'], 9) )
	or ( check_rights($_SESSION['id'], 25) ))
	  {
   echo "<TR><TD class=\"charte\"></TD></TR>
         <TR><TD CLASS=\"MenuRub\">Administration</TD></TR>
	 <TR><TD class=\"charte\"></TD></TR>
	 <TR>
	 <TD CLASS=\"Menu\">";
	 if ( check_rights($_SESSION['id'], 14) )
	 	echo "<a href='configuration.php' target=\"droite\" class=s>Configuration</a> / <a href='restore.php?file=' target=\"droite\" class=s>Backup</a><BR>";
	 if (( check_rights($_SESSION['id'], 9) ) or ( check_rights($_SESSION['id'], 25) ))
	 	echo "<a href='habilitations.php' target=\"droite\" class=s title='Voir les permissions pqr groupe ou par r�le'>Habilitations</a> / ";
	 	echo "<a href='change_password.php' target=\"droite\" class=s title='Modifier les mots de passe du personnel'>Passwords</a><BR>";
	 echo "</TD>
	 <TR><TD class='charte'></TD></TR>
	 </TR>";
}

// get webmaster email
$query="select P_EMAIL , NIV from pompier p, section_role sr, section_flat sf
	    where sr.P_ID = p.P_ID
	    and sr.S_ID = sf.S_ID
	    and sr.GP_ID = 108
	    and ( sr.S_ID = ".$section." or sr.S_ID = ".$parent.")
		order by NIV asc";
$result= mysql_query($query);
$row=mysql_fetch_array($result);
if ( $row["P_EMAIL"] <> "" ) $display_mail = $row["P_EMAIL"];	    
else $display_mail = $admin_email;

if (isset($application_title_specific)) $cmt = $application_title_specific;
else $cmt = "$application_title $version";
echo "<TR><TD >
	   <a href=about.php target=\"droite\" class=Bottom><font size=1 face=arial><i>$cmt</i></font></a> - ";
echo " <a href=doc.php target=\"droite\" class=Bottom ><font size=1 face=arial><i>aide</i></font></a> -";
echo " <a href=mailto:$display_mail class=Bottom><font size=1 face=arial><i>contact</i></font></a></TD></TR>";

echo "</TABLE>";
echo "</td></tr></table>";
echo "</div>";
?>
