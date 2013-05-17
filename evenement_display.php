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
$id=$_SESSION['id'];
$mysection=$_SESSION['SES_SECTION'];
$mysectionparent=get_section_parent($mysection);
if (isset ($_GET["id"])) $evenement=intval($_GET["id"]);
else $evenement=intval($_GET["evenement"]);
if (! is_inscrit($id,$evenement)) {
	if (! check_rights($_SESSION['id'],41))
		if ( get_company_evenement($evenement) == $_SESSION['SES_COMPANY'] )
			check_all(45);
		else
			check_all(41);
}

// from: scroller , inscription , calendar, choice, vehicule, personnel, formation
if (isset ($_GET["from"])) $from=mysql_real_escape_string($_GET["from"]); 
else if (isset ($_SESSION['eventabdoc'])) {
	$from='document';
	unset($_SESSION['eventabdoc']);
}
else if (isset ($_SESSION['from'])) {
	$from=mysql_real_escape_string($_SESSION['from']);
	unset($_SESSION['from']);
}
else $from='default';
if (isset ($_GET["section"])) $section=intval(mysql_real_escape_string($_GET["section"]));
else $section=$mysection;
if (isset ($_GET["type"])) $type=mysql_real_escape_string($_GET["type"]);
else $type="ALL";
if (isset ($_GET["date"])) $date=mysql_real_escape_string($_GET["date"]);
else $date="FUTURE";
if (isset ($_GET["day"])) $day=mysql_real_escape_string($_GET["day"]);
else $day="";
if (isset ($_GET["pid"])) $pid=mysql_real_escape_string($_GET["pid"]);
else $pid="";

$evts=get_event_and_renforts($evenement);

$query="select count(distinct P_ID) as NB from evenement_participation
 	where E_CODE in (".$evts.")";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB1=$row["NB"];

$query="select count(distinct V_ID) as NB from evenement_vehicule
 	where E_CODE in (".$evts.")";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB2=$row["NB"];

$query="select count(distinct MA_ID) as NB from evenement_materiel
 	where E_CODE in (".$evts.")";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB3=$row["NB"];

$NB4=0;
$mypath=$filesdir."/files/".$evenement;
if (is_dir($mypath)) {
   	$dir=opendir($mypath); 
   	while ($file = readdir ($dir)) {
      	if ($file != "." && $file != ".." and (file_extension($file) <> "db")) $NB4++;
	}
}

$query="select e.TE_CODE, e.E_LIBELLE, e.E_LIEU, eh.EH_DATE_DEBUT, eh.EH_DATE_FIN, e.S_ID, e.E_PARENT, e.PS_ID, e.TF_CODE, 
		TIME_FORMAT(eh.EH_DEBUT, '%k:%i') EH_DEBUT, TIME_FORMAT(eh.EH_FIN, '%k:%i') EH_FIN, e.E_CLOSED,
		te.TE_LIBELLE, eh.EH_ID
		from evenement e, evenement_horaire eh, type_evenement te
 	    where e.E_CODE = $evenement
 	    and eh.E_CODE = e.E_CODE
		and e.TE_CODE = te.TE_CODE";
$result=mysql_query($query);
$num=mysql_num_rows($result);
$row=@mysql_fetch_array($result);
$TE_CODE=$row["TE_CODE"];
$E_LIBELLE=stripslashes($row["E_LIBELLE"]);
$E_LIEU=stripslashes($row["E_LIEU"]);
$EH_DATE_DEBUT=$row["EH_DATE_DEBUT"];
$EH_DATE_FIN=$row["EH_DATE_FIN"];
$EH_DEBUT=$row["EH_DEBUT"];
$EH_FIN=$row["EH_FIN"];
$S_ID=$row["S_ID"];
$E_CLOSED=$row["E_CLOSED"];
$E_PARENT=$row["E_PARENT"];
$PS_ID=$row["PS_ID"];
$TF_CODE=$row["TF_CODE"];
$TE_LIBELLE=$row["TE_LIBELLE"];

// afficher la date seulement si une seule session
if ( $num == 1 ) {
	$tmp=explode ( "-",$EH_DATE_DEBUT); $year1=$tmp[0]; $month1=$tmp[1]; $day1=$tmp[2];
	$date1=mktime(0,0,0,$month1,$day1,$year1);
	$year2=$year1;
	$month2=$month1;
	$day2=$day1;

	if ( $EH_DATE_FIN <> '' ) {
		$tmp=explode ( "-",$EH_DATE_FIN); $year2=$tmp[0]; $month2=$tmp[1]; $day2=$tmp[2];
		$date2=mktime(0,0,0,$month2,$day2,$year2);
	}

	if (( $EH_DATE_FIN <> '' ) and ( $EH_DATE_FIN <> $EH_DATE_DEBUT )) {
   		$mydate=" - du ".date_fran($month1, $day1 ,$year1)." ".moislettres($month1)." ".$year1." au 
	   ".date_fran($month2, $day2 ,$year2)." ".moislettres($month2)." ".$year2.", ".$EH_DEBUT."-".$EH_FIN;
	}
	else {
 		$mydate=" - ".date_fran($month1, $day1 ,$year1)." ".moislettres($month1).", ".$year1." ".$EH_DEBUT."-".$EH_FIN;
	}
}
else $mydate="";

if ( $nbsections == 0) {
	$voircompta = check_rights($_SESSION['id'], 29,"$S_ID");
	// le chef de l'événement a toujours accès à ces fonctionnalités
	if ( get_chef_evenement ( $evenement ) == $id ) {
 		$voircompta = true;
	}
	// le cadre de permanence a toujours accès à ces fonctionnalités
	if ( get_cadre (get_section_organisatrice ( $evenement )) == $id ) {
 		$voircompta = true;
	}
}
else {
 $voircompta = false;
}
if (check_rights($id, 15, $S_ID)) $granted_event=true;
else if ( get_chef_evenement ( $evenement ) == $id ) $granted_event=true;
else $granted_event=false;

// ajout documents générés
if ( $granted_event) {
	if ( $TE_CODE == 'FOR'  and $competences == 1) {
	    $NB4 = $NB4 + 1; // évaluation par les stagiaires
		if ( $E_CLOSED == 1 ) {
			$NB4 = $NB4 + 1; // fiche présence
			if ( $PS_ID <> '' and $TF_CODE == 'I') $NB4 = $NB4 + 1; // procès verbal
		}
		$query1="select TYPE from poste where PS_ID=".$PS_ID;
		$result1=mysql_query($query1);
		$row1=@mysql_fetch_array($result1);
		$t=str_replace(" ","",$row1["TYPE"]);
		if ( $t == "SST") $NB4 = $NB4 + 5; // documents SST
		if ( $t == "PSC1") $NB4 = $NB4 + 1; // document PSC1
	}
	if (($TE_CODE == 'MAN' or $TE_CODE == 'EXE' or $TE_CODE == 'REU') and $competences == 1 and $E_CLOSED == 1)
		$NB4 = $NB4 + 1; // fiche présence seulement
	if ( $TE_CODE == 'DPS' or $TE_CODE == 'AIP' or $TE_CODE == 'HEB' or $TE_CODE == 'MET' 
	  or $TE_CODE == 'INS' or $TE_CODE == 'FOR' or $TE_CODE == 'AH' or $TE_CODE == 'GAR') {
		if ( $E_CLOSED == 1 ) 
			$NB4 = $NB4 + 1; // ordre de mission
	}
	if ( $TE_CODE == 'DPS' ) {
			$NB4 = $NB4 + 1; //convention
	}
}

writehead();
echo "
<script type='text/javascript' src='checkForm.js'></script>
<script type='text/javascript' src='popupBoxes.js'></script>
<script type=\"text/javascript\" src=\"js/jquery.js\"></script>
<style type=\"text/css\" >@import url(js/tabs/ui.tabs.css);</style>
<script type=\"text/javascript\" src=\"js/tabs/ui.tabs.js\"></script>
<script type=\"text/javascript\">
$(document).ready(function() {
	$('#TabsTri > ul').tabs();
});
";
?>

function bouton_redirect(cible, action) {
    if ( action == 'delete' ) {
       if ( confirm ("Attention : vous allez supprimer cet événement du calendrier. Voulez vous continuer ?" ))
          confirmed=1;
       else return;
    }
    if ( action == 'copy_old' ) {
       if ( confirm ("Attention : vous allez dupliquer cet événement du calendrier.\nVous pourrez modifier les paramètres (date, heure, lieu ...).\nVoulez vous continuer ?" ))
          confirmed=1;
       else return;
    }
    if ( action == 'renfort' ) {
       if ( confirm ("Attention : vous allez créer un renfort pour cet événement.\nVous pourrez modifier les paramètres (section organisatrice, personnel requis ...).\nVoulez vous continuer ?" ))
          confirmed=1;
       else return;
    }
    if ( action == 'inscription' ){
    		if ( confirm("Vous allez vous inscrire sur cet événement\nContinuer?"))
				confirmed = 1;
	 	    else return;
	}
    self.location.href = cible;
}
function inscrire(evenement,action, pid) {
     if ( action == 'inscription' ){
    		if ( confirm("Vous allez inscrire une personne sur cet événement\nContinuer?"))
				confirmed = 1;
	 	    else return;
	 }
     cible="evenement_inscription.php?evenement="+evenement+"&action="+action+"&P_ID="+pid;
     self.location.href=cible;
}

var fenetreDetail=null;
function horaires(evenement, pid, vid) {
     url="evenement_horaires.php?evenement="+evenement+"&pid="+pid+"&vid="+vid;
	 fenetre=window.open(url,'Horaires','menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=no,resizable=no,' + 'width=700' + ',height=750');
	 fenetreDetail = fenetre;
	 fenetreDetail.focus();

}

function inscrire(evenement,what){
 	 fermerDetail();
	 url="evenement_detail.php?evenement="+evenement+"&what="+what;
	 fenetre=window.open(url,'Personnel','menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=no,resizable=no,' + 'width=700' + ',height=200');
	 fenetreDetail = fenetre;
	 fenetreDetail.focus();
	 return true
}

function choisir_responsable(evenement,responsable){
 	 fermerDetail();
	 url="evenement_detail.php?evenement="+evenement+"&what=responsable&responsable="+responsable;
	 fenetre=window.open(url,'Personnel','menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=no,resizable=no,' + 'width=700' + ',height=200');
	 fenetreDetail = fenetre;
	 fenetreDetail.focus();
	 return true
}

function modifier_competences(evenement,partie){
 	 fermerDetail();
	 url="evenement_competences.php?evenement="+evenement+"&partie="+partie;
	 fenetre=window.open(url,'Compétences','menubar=no,toolbar=no,location=no,directories=no,status=no,' + 'width=700' + ',height=400');
	 fenetreDetail = fenetre;
	 fenetreDetail.focus();
	 return true
}

function modifier_equipes(evenement){
 	 fermerDetail();
	 url="evenement_equipes.php?evenement="+evenement;
	 fenetre=window.open(url,'Compétences','menubar=no,toolbar=no,location=no,directories=no,status=no,' + 'width=700' + ',height=400');
	 fenetreDetail = fenetre;
	 fenetreDetail.focus();
	 return true
}

function fermerDetail() {
	 if (fenetreDetail != null) {
	    fenetreDetail.close( );
	    fenetreDetail = null;
         }
}

var NewDocument=null;
function openNewDocument(evenement,section){
	 url="upd_document.php?section="+section+"&evenement="+evenement;
	 fenetre=window.open(url,'Note','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,' + 'width=450' + ',height=300');
	 NewDocument = fenetre;
	 return true
}

function closeNewDocument() {
	 if (NewDocument != null) {
	    NewDocument.close( );
	    NewDocument = null;
     }
}

function deletefile(evenement, file) {
   if ( confirm ("Voulez vous vraiment supprimer le fichier " + file +  "?" )) {
         self.location = "delete_event_file.php?number=" + evenement + "&file=" + file + "&type=evenement";
   }
}


function savefonction(evenement,fn, pid) {
     cible="evenement_inscription.php?evenement="+evenement+"&action=fonction&fonction="+fn+"&P_ID="+pid;
     self.location.href=cible;
}

function saveequipe(evenement,equipe, pid) {
     cible="evenement_inscription.php?evenement="+evenement+"&action=equipe&equipe="+equipe+"&P_ID="+pid;
     self.location.href=cible;
}

function saveconducteur(evenement,pid, vid) {
     cible="evenement_vehicule.php?evenement="+evenement+"&action=conducteur&conducteur="+pid+"&V_ID="+vid;
     self.location.href=cible;
}

function saveequipemateriel(evenement,eeid, mid) {
     cible="evenement_materiel.php?evenement="+evenement+"&action=equipe_materiel&equipe_materiel="+eeid+"&MA_ID="+mid;
     self.location.href=cible;
}

function cancel_renfort(evenement,renfort) {
    if ( confirm("Vous allez annuler un renfort sur cet événement\nContinuer?"))
				confirmed = 1;
	 	    else return;
     cible="evenement_inscription.php?evenement="+evenement+"&renfort="+renfort+"&action=cancel";
     self.location.href=cible;
}

function updatenumber(element,evenement,number,value,defaultvalue) {
	if(value.length > 0)
   	{
   	 	var obj = document.getElementById(element);
   	 	for (i = 0; i < value.length; i++)
    	{   
       		var c = value.charAt(i);
        	if (((c < "0") || (c > "9"))) {
		 		alert ("Seul des numéros sont attendus: "+ value + " ne convient pas.");
		 		obj.value = defaultvalue;
		 		return false;
			}
    	}
     	cible="evenement_inscription.php?evenement="+evenement+"&action=nb"+number+"&value="+value;
     	self.location.href=cible;
     	return true
   	}
}
function updateformation(evenement,kind,value) {
	if(value.length > 0)
   	{
   		if ( kind == 'ps' )
     		cible="evenement_inscription.php?evenement="+evenement+"&action=poste&value="+value;
     	else {
     		cible="evenement_inscription.php?evenement="+evenement+"&action=tf&value="+value;
     	}
     	self.location.href=cible;
   	}
}
function fermerfenetre(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}


<?php
echo "</script>";
echo "</head>";
echo "<body onload='javascript:fermerDetail();'>";

if ( $E_PARENT <> '' and $E_PARENT > 0)
 	$img = "<img border=0 src=images/renfort.png title='renfort sur un autre événement'> <img border=0 src=images/".$TE_CODE."small.gif >";
else
	$img = "<img border=0 height=48 src=images/".$TE_CODE.".gif title=\"".$TE_LIBELLE."\">";

echo "\n"."<div align=center>
        <table><tr>
		<td>".$img."</td>
		<td><b><font size=3>".$E_LIBELLE."</b> (".$E_LIEU." ".$mydate." )</font>";

if ($voircompta)
		echo "<p style=\"".get_etat_facturation($evenement,"css")."\">".get_etat_facturation($evenement,"txt")."</p>";
echo "</td>
		</tr></table>
		</div><p>";

if ( $TE_CODE == 'DPS' ){
  echo "<a href='dps.php?evenement=$evenement' target='_blank'><span>
  <img src='images/calculette.png' align='right' height='24' border='0' alt='Dimensionnement DPS' title='Dimensionnement DPS' ></span></a>";
}

if ( $voircompta )
  echo "<a href='evenement_facturation.php?evenement=$evenement' ><span>
  <img src='images/money.png' align='right' height='24' border='0' alt='Facturation' title='Facturation' ></span></a> ";	

if (check_rights($id, 41)) {
	echo "<img src='images/xls.jpg' id='StartExcel' height='24' border='0' alt='Excel' title='Excel' onclick=\"window.open('	evenement_xls.php?evenement=$evenement')\" class='noprint' align='right' />";
	if ( $_SESSION['SES_BROWSER'] != "Mozilla unknown" )
		echo "\n"."<a href=\"evenement_display_sub.php?evenement=$evenement&print=1&from=print\" target=_blank><img src=images/printer.gif align=right height=24 alt=imprimer title=imprimer class=\"noprint\" border=0></a>";
	echo "\n"."<a href=\"evenement_ical.php?evenement=$evenement&section=$section\" target=_blank><img src=\"images/ical.png\" align=\"right\" height=\"24\" alt=\"ical\" title=\"Télécharger le fichier ical\" class=\"noprint\" border=\"0\"></a>";
}

echo "\n"."<div align=center id=\"TabsTri\" >"; // dev tabs
echo "\n"."<ul class=\"noprint\">";

// infos generales
echo "\n"."<li><a href=\"evenement_display_sub.php?pid=$pid&from=$from&tab=1&evenement=$evenement\">
<span><img src=images/view_detailed.png height=14 border=0> Informations</span></a></li>";

// personnel
if ( $from == 'inscription' ) $class='ui-tabs-selected';
else $class='';
echo "\n"."<li class=\"$class\"><a href=\"evenement_display_sub.php?pid=$pid&from=$from&tab=2&evenement=$evenement\">
<span><img src=images/user.png height=14 border=0> Personnel ($NB1)</span></a></li>";

//vehicule
if ( $vehicules == 1 ) {
if ( $from == 'vehicule' ) $class='ui-tabs-selected';
else $class='';
echo "\n"."<li class=\"$class\"><a href=\"evenement_display_sub.php?pid=$pid&from=$from&tab=3&evenement=$evenement\">
<span><img src=images/car.png height=14 border=0> Véhicules ($NB2)</span></a></li>";
}
//materiel
if ( $materiel == 1 ) {
if ( $from == 'materiel' ) $class='ui-tabs-selected';
else $class='';
echo "\n"."<li class=\"$class\"><a href=\"evenement_display_sub.php?pid=$pid&from=$from&tab=4&evenement=$evenement\">
<span><img src=images/smallengine.png height=14 border=0> Matériel ($NB3)</span></a></li>";
}
//formation
if (($competences == 1 ) and ( $TE_CODE == 'FOR' ) and ( $PS_ID <> "") and ($TF_CODE <> "")){
if ( $from == 'formation' ) $class='ui-tabs-selected';
else $class='';
echo "\n"."<li class=\"$class\"><a href=\"evenement_display_sub.php?pid=$pid&from=$from&tab=5&evenement=$evenement\">
<span><img src=images/medal.png height=14 border=0> Formation/diplômes </span></a></li>";
}

// documents
if ( $from == 'document' ) $class='ui-tabs-selected';
else $class='';
echo "\n"."<li class=\"$class\"><a href=\"evenement_display_sub.php?pid=$pid&from=$from&tab=6&evenement=$evenement\">
<span><img src=images/smallbook.png height=14 border=0> Documents ($NB4)</span></a></li>";

echo "\n"."</ul>";
echo "\n"."</div>";// fin tabs
echo "\n<div align=center id=\"export\"></div>";
echo "\n"."</body></html>";
?>


