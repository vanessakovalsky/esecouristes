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

include_once ("config.php");
check_all(0);
writehead();

if ( isset($_GET["id"])) $pompier=$_GET["id"];
else $pompier=$_GET["pompier"];
if ( isset($_GET["from"]))$from=$_GET["from"]; //qualif, inscriptions
else $from="default";

$id=$_SESSION['id'];
$mycompany=$_SESSION['SES_COMPANY'];
if ($id == $pompier) $allowed=true;
else if ( $mycompany == get_company($pompier) and check_rights($_SESSION['id'], 45) and $mycompany > 0) {
	$allowed=true;
}
else check_all(40);

if ( isset ( $_GET['order'])) {
	$order = mysql_real_escape_string($_GET['order']);
	$from = 'formations';
}
else $order='PS_ID';

// check input parameters
$pompier=intval(mysql_real_escape_string($pompier));
if ( $pompier == 0 ) {
	param_error_msg();
	exit;
}

$query="select count(*) as NB from qualification where P_ID=".$pompier;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB1=$row["NB"];

$query="select count(*) as NB from personnel_formation where P_ID=".$pompier;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB2=$row["NB"];

$query="select count(*) as NB from evenement_participation ep, evenement e, evenement_horaire eh
 	where ep.P_ID=".$pompier."
 	and eh.e_code = e.e_code
 	and ep.eh_id=eh.eh_id
	and ep.E_CODE=e.E_CODE
	and e.E_CANCELED = 0
	and ( date_format(eh.eh_date_debut,'%Y%m%d') >= date_format(now(),'%Y%m%d') or 
        	  ( date_format(eh.eh_date_debut,'%Y%m%d') < date_format(now(),'%Y%m%d') and date_format(eh.eh_date_fin,'%Y%m%d') >= date_format(now(),'%Y%m%d'))
        	 )";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB3=$row["NB"];

$query="select count(*) as NB from planning_garde pg
 	where pg.P_ID=".$pompier."
	and date_format(pg.PG_DATE,'%Y%m%d') >= date_format(now(),'%Y%m%d')";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB3=$NB3+$row["NB"];

$query="select count(*) as NB from vehicule
 	where AFFECTED_TO=".$pompier;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB4=$row["NB"];

$query="select count(*) as NB from materiel
 	where AFFECTED_TO=".$pompier;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB4=$NB4+$row["NB"];


echo "
<script type='text/javascript' src='checkForm.js'></script>
<script type=\"text/javascript\" src=\"js/jquery.js\"></script>
<style type=\"text/css\" >@import url(js/tabs/ui.tabs.css);</style>
<script type=\"text/javascript\" src=\"js/tabs/ui.tabs.js\"></script>
<script type=\"text/javascript\" src=\"http://download.skype.com/share/skypebuttons/js/skypeCheck.js\"></script>
<script type=\"text/javascript\">
$(document).ready(function() {
	$('#TabsTri > ul').tabs();
});
function impression(){ 
    parent.frames[ \"droite\" ].print(); 
}

function fermerfenetre(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}

function bouton_redirect(cible) {
    self.location.href = cible;
}

function send_id(cible) {
    self.location.href = cible;
}


function update(pid,psid,pfid) {
     url='personnel_formation.php?P_ID='+pid+'&PS_ID='+psid+'&PF_ID='+pfid+'&action=update';
     self.location.href=url;
}


function changedType() {
 	var type = document.getElementById('statut');
 	var ts=document.getElementById('type_salarie');
 	var h=document.getElementById('heures');
 	var tsRow = document.getElementById('tsRow');
 	var gRow = document.getElementById('gRow');
 	var gRow2 = document.getElementById('gRow2');
 	var cRow2 = document.getElementById('cRow2');
 	var iRow = document.getElementById('iRow');
 	var pRow = document.getElementById('pRow');
 	var sRow2 = document.getElementById('sRow2');
 	var uRow0 = document.getElementById('uRow0');
 	var uRow1 = document.getElementById('uRow1');
 	var uRow2 = document.getElementById('uRow2');
 	var uRow3 = document.getElementById('uRow3');
 	var uRow4 = document.getElementById('uRow4');
 	var aRow = document.getElementById('aRow');
 	var yRow = document.getElementById('yRow');
 	var flag1 = document.getElementById('flag1');
    if (type.value == 'SAL') {
		tsRow.style.display = '';

	} else {
		ts.value='0';
		h.value='';
		tsRow.style.display = 'none';
	}
	if (type.value == 'EXT') {
	 	gRow.style.display = '';
	 	gRow2.style.display = 'none';
	 	cRow2.style.display = 'none';
	 	iRow.style.display = '';
	 	pRow.style.display = 'none';
	 	sRow2.style.display = 'none';
	 	uRow0.style.display = 'none';
	 	uRow1.style.display = 'none';
	 	uRow2.style.display = 'none';
	 	uRow3.style.display = 'none';
	 	uRow4.style.display = 'none';
	 	aRow.style.display = 'none';
	 	yRow.style.display = '';
	 	flag1.style.display = 'none';
	}
	else {
	 	gRow.style.display = '';
	 	gRow2.style.display = '';
	 	cRow2.style.display = '';
		iRow.style.display = '';
		pRow.style.display = '';
	 	sRow2.style.display = '';
		uRow0.style.display = '';
		uRow1.style.display = '';
	 	uRow2.style.display = '';
	 	uRow3.style.display = '';
	 	uRow4.style.display = '';
		aRow.style.display = '';
		yRow.style.display = '';
		flag1.style.display = '';	 	
	}
}
function changedSalarie() {
 	var ts=document.getElementById('type_salarie');
 	var h=document.getElementById('heures');
    if (ts.value == 'TC') {
		h.value='35';
	}
}

</script>
";
echo "</head>";
echo "<body>";

// lien tel iphone
if (stristr($_SERVER['HTTP_USER_AGENT'], "iPhone")  || strpos($_SERVER['HTTP_USER_AGENT'], "iPod")) {  
 	$num=get_phone($pompier);
        // On empêche d'afficher le lien si la case Masquer au public est cochée.
        $confidentialite = get_confidentialite($pompier);
 	if ( $num <> "" && $confidentialite == '0' )
	 	$iphonelink="<a href='tel:".$num."'><img src=images/it_phone.png border=0></a> <a href='sms:".$num."'><img src=images/sms2.png border=0></a>";
}
else $iphonelink="";

$sexe=get_sexe($pompier);
if ( $sexe == 'M') $pic='male.png';
else $pic='female.png';
echo "\n"."<div align=center><table border=0><tr><td><img src=images/$pic ></td><td>
<font size=4><b>".my_ucfirst(get_prenom($pompier))." ".strtoupper(get_nom($pompier))."</b></font> ".$iphonelink."</td></tr></table>
</div><p>";

if ( check_rights($_SESSION['id'], 49) and $log_actions == 1 ) {
echo "\n"." <a href=\"history.php?lccode=P&lcid=$pompier&order=LH_STAMP&ltcode=ALL\"><img src=\"images/zoom.png\" align=\"right\" height=\"24\" title=\"Historique des modifications\" class=\"noprint\" border=\"0\" ></a>";
}

echo "\n"." <img src=\"images/printer.gif\" align=\"right\" height=\"24\" alt=\"imprimer\" title=\"imprimer\" class=\"noprint\" onclick=\"impression();\">";

if ( check_rights($_SESSION['id'], 2)) {
echo "\n"." <a href=\"vcard.php?pid=$pompier\"><img src=\"images/vcard.png\" align=\"right\" height=\"24\" alt=\"Carte de visite\" title=\"Carte de visite\" class=\"noprint\" border=\"0\" ></a>";
}

$hissection=get_section($pompier);

if ( check_rights($_SESSION['id'], 40,$hissection) and $evenements == 1) {
echo "\n"." <a href=\"calendar.php?pompier=$pompier\"><img src=\"images/calendar.png\" align=\"right\" height=\"22\" alt=\"Calendrier\" title=\"Calendrier\" class=\"noprint\" border=\"0\" ></a>";
}
if ( check_rights($_SESSION['id'], 25,$hissection) or check_rights($_SESSION['id'], 9 ,$hissection) or $pompier == $_SESSION['id']) {
   	echo " <a href='change_password.php?pid=$pompier'>
		<img src='images/key.png' align='right' height='24' title=\"Changer le mot de passe de ".my_ucfirst(get_prenom($pompier))." ".strtoupper(get_nom($pompier))."\" 
		class='noprint' border='0' ></a>";
}

$skype=get_skype($pompier);
if ( $skype <> "" ) {
   	echo " <a href='skype:".$skype."?call'>
		<img src='images/skype.png' align='right' height='24' title=\"Contacter ".my_ucfirst(get_prenom($pompier))." ".strtoupper(get_nom($pompier))." (".$skype.") avec Skype\" 
		class='noprint' border='0' ></a>";
}

echo "\n"."<script type=\"text/javascript\"></script><noscript style=\"color:red;text-decoration: blink;\"><p>Merci d'activer <b>Javascript</b> pour profiter des toutes les fonctionnalit�s</p></noscript>";
echo "\n"."<div align=center id=\"TabsTri\" >"; // dev tabs
echo "\n"."<ul class=\"noprint\">";

// infos personnel
echo "\n"."<li><a href=\"upd_personnel_sub.php?from=$from&tab=1&pompier=$pompier\"><span><img src=images/user.png height=14 border=0> Infos</span></a></li>";

// competences
if ( $competences == 1 ) {
	if ( $from == 'qualif' ) $class='ui-tabs-selected';
	else $class='';
	echo "\n"."<li class=$class><a href=\"upd_personnel_sub.php?from=$from&tab=2&pompier=$pompier\"><span><img src=images/medal.png height=14 border=0> Comp&eacute;tences ($NB1)</span></a></li>";

	// formations
	if ( $from == 'formations' ) $class='ui-tabs-selected';
	else $class='';
	echo "\n"."<li class=$class><a href=\"upd_personnel_sub.php?from=$from&tab=3&pompier=$pompier&order=$order\"><span><img src=images/smallbook.png height=14 border=0> Formations ($NB2)</span></a></li>";
}
// inscriptions
if ( $evenements == 1 ){
	if ( $from == 'inscriptions' ) $class='ui-tabs-selected';
	else $class='';
	echo "\n"."<li class=$class><a href=\"upd_personnel_sub.php?from=$from&tab=4&pompier=$pompier\"><span><img src=images/date.png height=14 border=0> Participations ($NB3)</span></a></li>";
}
// vehicules/materiel
if ( $vehicules == 1 or $materiel == 1) {
	if ( $from == 'vehicules' ) $class='ui-tabs-selected';
	else $class='';
	echo "\n"."<li class=$class><a href=\"upd_personnel_sub.php?from=$from&tab=5&pompier=$pompier\"><span><img src=images/car.png height=14 border=0> V�hicules/Mat�riel ($NB4)</span></a></li>";
}
echo "\n"."</ul>";
echo "\n"."</div>";// fin tabs
echo "\n<div align=center id=\"export\"></div>";
echo "\n"."</body></html>";
?>