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

echo "<html>";
include("config.php");
check_all(40);

$title="Compétence(s)";

writehead();
echo "
<script type=\"text/javascript\" src=\"js/jquery.js\"></script>
<style type=\"text/css\" >@import url(js/tabs/ui.tabs.css);</style>
<script type=\"text/javascript\" src=\"js/tabs/ui.tabs.js\"></script>
<script type=\"text/javascript\">
$(document).ready(function() {
	$('#TabsTri > ul').tabs();
});
function impression(){ parent.frames[ \"droite\" ].print(); }

function SendMailTo(formName, checkTab,message,doc){
	var dest = '';
	for (i=0; i<document.forms[formName].elements[checkTab].length; i++) {
		if(document.forms[formName].elements[checkTab][i].checked) {
			dest += ','+document.forms[formName].elements[checkTab][i].value;
		}
	}
	if(dest!=''){
	    if(doc=='listemails'){
			document.forms[formName].action = 'listemails.php';
		}
		document.forms[formName].SelectionMail.value = dest.substr(1,dest.length);
		document.forms[formName].submit();
		return true;
	}
	alert (message);   
	return false;
}

function DirectMailTo(formName, checkTab, message, doc){
	var dest = '';
	var max = 80;
	var m = 0;
	for (i=0; i<document.forms[formName].elements[checkTab].length; i++) {
		if(document.forms[formName].elements[checkTab][i].checked) {
			dest += ','+document.forms[formName].elements[checkTab][i].value;
			m++;
		}
		if (m>max){
			alert ('Maximum '+max+' destinataires par mail avec la fonction mailto');
			return false;
		}
	}
	if(dest!=''){		
		destid=dest.substr(1,dest.length);
		cible='mailto.php?destid='+ destid;
	 	self.location.href=cible;
        return true;
	}
	alert (message);   
	return false;
}


function checkAll(field,checkValue)
{
for (i = 0; i < field.length; i++)
	field[i].checked = ((checkValue!=true)?false:true) ;
}
</script>
";
echo "</head>";
echo "<body>";
/*if ((! substr($_SESSION['SES_BROWSER'],0,2) == 'IE' ) && (! substr($_SESSION['SES_BROWSER'],0,7) == 'Firefox' )) 
   write_msgbox("Erreur navigateur",$warning_pic,"Votre navigateur ne supporte pas cette fonctionnalité.<p align=center><a href=index_d.php >$myspecialfont retour</font></a>",30,30);
else {   */
	echo "\n"."<div ><table><tr><td><img src=\"images/xmag.png\" align=\"left\"></td><td>
	<font size=4><b>Recherche de personnel</b></font></td></tr></table></div><p>";
	echo "\n"."<img src=\"images/printer.gif\" align=\"right\" height=\"24\" alt=\"imprimer\" class=\"noprint\" onclick=\"impression();\">";
	echo "\n"."<script type=\"text/javascript\"></script><noscript style=\"color:red;text-decoration: blink;\"><p>Merci d'activer <b>Javascript</b> pour profiter des toutes les fonctionnalités</p></noscript>";
	echo "\n"."<div id=\"TabsTri\" >"; // dev tabs
	echo "\n"."<ul class=\"noprint\">";
	echo "\n"."<li><a href=\"search_personnel_nom.php\"><span><img src=images/user.png  height=14 border=0> Par NOM</span></a></li>";
	echo "\n"."<li><a href=\"search_ville.php\"><span><img src=images/smallhouse.png  height=14 border=0> Par Ville</span></a></li>";
	echo "\n"."<li><a href=\"search_tel.php\"><span><img src=images/smallphone.png  height=14 border=0> Par Téléphone</span></a></li>";
	if ( $competences == 1 )
		echo "\n"."<li><a href=\"search_personnel_poste.php\"><span><img src=images/medal.png  height=14 border=0> Par $title</span></a></li>";
	echo "\n"."<li><a href=\"search_habilitation.php\"><span><img src=images/miniok.png  height=14 border=0> Par Habilitations</span></a></li>";
	echo "\n"."</ul>";
	echo "\n"."</div>";// fin tabs
	echo "\n<div id=\"export\"></div>";
//}
echo "\n"."</body></html>";
?>
