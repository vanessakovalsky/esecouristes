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

header('Content-Type: text/html; charset=ISO-8859-1');
header("Cache-Control: no-cache");
 
include_once ("config.php");
check_all(0);

$evenement = (isset($_POST['evenement'])?intval($_POST['evenement']):(isset($_GET['evenement'])?intval($_GET['evenement']):""));

writehead();
$msgerr="";

if(isset($_POST['action'])){
$dimNbISActeurs = (isset($_POST['dimNbISActeurs'])?mysql_real_escape_string($_POST['dimNbISActeurs']):0);
$dimNbISActeursCom = (isset($_POST['dimNbISActeursCom'])?mysql_real_escape_string($_POST['dimNbISActeursCom']):"");
$dimP=(isset($_POST['P'])?mysql_real_escape_string($_POST['P']):0);
$dimP1=(isset($_POST['P1'])?mysql_real_escape_string($_POST['P1']):0);
$dimP2=(isset($_POST['P2'])?mysql_real_escape_string($_POST['P2']):0.25);
$dimE1=(isset($_POST['E1'])?mysql_real_escape_string($_POST['E1']):0.25);
$dimE2=(isset($_POST['E2'])?mysql_real_escape_string($_POST['E2']):0.25);
$dimI=(isset($_POST['i'])?mysql_real_escape_string($_POST['i']):0);
$dimRIS=(isset($_POST['RIS'])?mysql_real_escape_string($_POST['RIS']):0);
$dimRISCalc=(isset($_POST['RISCalc'])?mysql_real_escape_string($_POST['RISCalc']):0);
$dimNbIS=(isset($_POST['NbIS'])?mysql_real_escape_string($_POST['NbIS']):0);
$dimTypeDPS=(isset($_POST['type'])?mysql_real_escape_string($_POST['type']):0);
$dimTypeDPSCom=(isset($_POST['commentaire'])?mysql_real_escape_string($_POST['commentaire']):"");
$dimSecteurs=(isset($_POST['secteurs'])?mysql_real_escape_string($_POST['secteurs']):0);
$dimPostes=(isset($_POST['postes'])?mysql_real_escape_string($_POST['postes']):0);
$dimEquipes=(isset($_POST['equipes'])?mysql_real_escape_string($_POST['equipes']):0);
$dimBinomes=(isset($_POST['binomes'])?mysql_real_escape_string($_POST['binomes']):0);
EvenementSave($_POST);
}

$row=EvenementDPS($evenement,'data');
$dimNbISActeurs=$row['dimNbISActeurs'];
$dimNbISActeursCom=stripslashes($row['dimNbISActeursCom']);
$dimI=$row['i'];
$dimP=$row['P'];
$dimP1=$row['P1'];
$dimP2=$row['P2'];
$dimE1=$row['E1'];
$dimE2=$row['E2'];
$dimRIS=$row['RIS'];
$dimRISCalc=$row['RISCalc'];
$dimNbIS=$row['NbIS'];
$dimTypeDPS=stripslashes($row['type']);
$dimTypeDPSCom=stripslashes($row['commentaire']);
$dimSecteurs=$row['secteurs'];
$dimPostes=$row['postes'];
$dimEquipes=$row['equipes'];
$dimBinomes=$row['binomes'];
$effectif=$row['effectif'];
$action=(($dimRISCalc>0)?"Modifier":"Enregistrer");// si Modifier >> affiche lien vers impression
?>
<script type="text/javascript" src="js/jquery.js"></script> 
<script type="text/javascript" src="js/jquery_forms.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
calcRIS();
var options = { 
    target:     '#resultat', 
    url:        'dps_save.php', 	
    success:    function() { 
        //alert("Dimensionnement enregistré.");
    } 
};
$('form#frmDPS').ajaxForm(options);
$('input#btGrille').submit(options);
$('input').keyup(function(){
	calcRIS();
});
$('input[@type=radio]').change(function(){
	calcRIS();
});
});
function calcRIS(){
	$.post('dps_calc.php',
	{
	evenement:$('input[@name=evenement]').fieldValue()[0],
	P1:$('input[@name=P1]').fieldValue()[0],
	P2:$('input[@name=P2]').fieldValue()[0],
	E1:$('input[@name=E1]').fieldValue()[0],
	E2:$('input[@name=E2]').fieldValue()[0],
	dimNbISActeurs:$('input[@name=dimNbISActeurs]').fieldValue()[0],
	dimNbISActeursCom:$('textarea[@name=dimNbISActeursCom]').fieldValue()[0],
	actionPrint:'<?php echo $action;?>'
	},
	function(data){
		$("#resultat").html(data);
   }
   );
}
function fermerfenetre(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}
</script>
<style type='text/css'>
div#formulaire{
width:72%;
margin-right:28%;
}
div#resultat{
float:right;
width:27%;
}
div#frmDPSretour{
float:right;
width:27%;
}
#frmDPS table td{
background-color:#ffffff;
}
#resultat input,#resultat textarea{
background-color:transparent;
border:none;
FONT-SIZE: 10pt; 
FONT-FAMILY: Arial;
}
input:focus, textarea:focus, select:focus{
	background-color:#ffffcc;
}
.TabHeader2{
background-color:orange;
}
</style>
</head>
<body>
<?php

echo "<p style=\"color:red;\">$msgerr</p>";
echo EbDeb("RNMSC-DPS - Dimensionnement");
?>
<p  style="text-align:justify; padding:0 1em 0 1em;">Le dimensionnement du dispositif de secours pour les <b>acteurs</b> est de la seule responsabilité du demandeur et/ou de l'autorité de police compétente. 
<br />Le dimensionnement du dispositif de secours pour le <b>public</b> est régit par le Référentiel National des Missions de Sécurité Civile - Dispositifs Prévisionnels de Secours</p>
<p  style="text-align:justify; padding:0 1em 0 1em;">Ce calcul de dimensionnement minimal est mis a disposition pour "information".
<br />Seule une étude personnalisée de votre manifestation avec une association de sécurité civile permettra de dimensionner <b>votre</b> dispositif prévisionnel de secours</p>
<p style="text-align:justify; padding:0 1em 0 1em;">Le nombre d'intervenant correspond au nombre de Secouriste, Equipier Secouriste, Chef d'Equipe, Chef de Poste. <br />Sont exclus: l'encadrement et la logistique.</p>
<!--
<h2><blink>A tester...</blink></h2>
<p>merci d'envoyer vos commentaire à  <a href="mailto:adpc90@free.fr?subject=Pour Jean-Pierre : RNMSC-DPS">Jean-Pierre</a></p>
-->
<form action="dps_save.php?tab=2" method="POST" name="dps" id="frmDPS">

<!-- deb resultat -->
<div id="resultat"></div>
<!-- fin resultat -->

<div id="formulaire">
<table border="1">
<tr><th class="TabHeader2" colspan="2">Demande pour les acteurs : </th></tr>
<tr>
<td style="background-color:#ffffcc;">
<p>Descriptif de la demande pour les acteurs</p>
<textarea name="dimNbISActeursCom" style="width:90%;FONT-SIZE: 10pt; FONT-FAMILY: Arial;"><?php echo $dimNbISActeursCom; ?></textarea>
<br />Equivalence en nombre d'intervenants secouristes pour les acteurs :<input type="text" name="dimNbISActeurs" id="dimNbISActeurs"  value="<?php echo $dimNbISActeurs; ?>"  style="background-color:Yellow;"> <br >(Minimum = 4, si un dispositif est demandé pour les acteurs)  
</td>
<td style="background-color:#ffffcc;">&nbsp;</td>
</tr>

<tr><th colspan="2">&nbsp;</th></tr>

<tr><th colspan="2"><b>Dimensionnement pour le public : </b></th></tr>
<tr>
<td colspan="2">
<p><b>Nota :</b><br />Dans le cas où les acteurs présenteraient un risque différent du public, et en absence d'un dispositif spécifique pour les acteurs, le PAPS n'est pas un dispositif de secours suffisant.</p>
</td>
</tr>
<tr><th><b>Effectif déclaré du public</b></th>
<th class="TabHeader">Indicateur P1</th></tr>
<tr>
<td><input type="text" name="P1" value="<?php echo ($dimP1); ?>" style="background-color:Yellow;"></td>
<td><input type="text" name="P" value="" readonly class="result"></td>
</tr>
<tr>
<tr><th class="TabHeader">Activité du rassemblement</th>
<th class="TabHeader">Indicateur P2</th></tr>
<tr>
<td><input type="radio" name="P2" value="0.25"  <?php echo ($dimP2==0.25?"checked=\"yes\"":""); ?>>- Public assis : spectacle, cérémonie cultuelle, réunion publique, restauration, rendez-vous sportif...</td>
<td>0,25</td>
</tr>
<tr>
<td><input type="radio" name="P2" value="0.30" <?php echo ($dimP2==0.30?"checked=\"yes\"":""); ?>>- Public debout : cérémonie cultuelle, réunion publique, restauration, exposition, foire, salon, comice agricole...</td>
<td>0,30</td>
</tr>
<tr>
<td><input type="radio" name="P2" value="0.35" <?php echo ($dimP2==0.35?"checked=\"yes\"":""); ?>>- Public debout : spectacle avec public statique, fête foraine, rendez-vous sportif avec protection du public par rapport à  l'événement...</td>
<td>0,35</td>
<tr>
<td><input type="radio" name="P2" value="0.40" <?php echo ($dimP2==0.40?"checked=\"yes\"":""); ?>>- Public debout : spectacle avec public dynamique, danse, feria, fête votive, carnaval, spectacle de rue, grande parade, rendez-vous sportif sans protection du public par rapport à  l'événement ...
<br />- Evénement se déroulant sur plusieurs jours avec présence permanente du public : hébergement sur site ou à 
proximité. </td>
<td>0,40</td>
</tr>
<tr>
<th class="TabHeader">Caractéristiques de l'environnement ou<br/>de l'accessibilité du site</th>
<th class="TabHeader">Indicateur E1</th>
</tr>
<tr>
<td><input type="radio" name="E1" value="0.25"  <?php echo ($dimE1==0.25?"checked=\"yes\"":""); ?>>- Structures permanentes : Bâtiment, salle « en dur »,...
<br />- Voies publiques, rues,...avec accès dégagés 
<br />- Conditions d'accès aisés </td>
<td>0.25</td>
</tr>
<tr>
<td><input type="radio" name="E1" value="0.30"  <?php echo ($dimE1==0.30?"checked=\"yes\"":""); ?>>- Structures non permanentes : gradins, tribunes, chapiteaux,...
<br />- Espaces naturels : surface = 2 hectares
<br />- Brancardage : 150 m < longueur = 300 m
<br />- Terrain en pente sur plus de 100 mètres 0,30</td>
<td>0.30</td>
</tr>
<tr>
<td><input type="radio" name="E1" value="0.35"  <?php echo ($dimE1==0.35?"checked=\"yes\"":""); ?>>- Espaces naturels : 2 ha < surface = 5 ha 
<br />- Brancardage : 300 m < longueur = 600 m
<br />- Terrain en pente sur plus de 150 mètres
<br />- Autres conditions d'accès difficiles</td>
<td>0.35</td>
</tr>
<tr>
<td><input type="radio" name="E1" value="0.40"  <?php echo ($dimE1==0.40?"checked=\"yes\"":""); ?>>- Espaces naturels : surface > 5 hectares
<br />- Brancardage : longueur > 600 mètres
<br />- Terrain en pente sur plus de 300 mètres
<br />- Autres conditions d'accès difficiles : Talus, escaliers, voies d'accès non carrossables,...
<br />- Progression des secours rendue difficile par la présence du public </td>
<td>0.40</td>
</tr>
<tr>
<th class="TabHeader">Délai d'intervention des secours publics </th>
<th class="TabHeader">Indicateur E2</th>
</tr>
<tr>
<td><input type="radio" name="E2" value="0.25"  <?php echo ($dimE2==0.25?"checked=\"yes\"":""); ?>> <= 10 minutes </td>
<td>0.25</td>
</tr>
<tr>
<td><input type="radio" name="E2" value="0.30"  <?php echo ($dimE2==0.30?"checked=\"yes\"":""); ?>> > 10 minutes et <=
<br />20 minutes </td>
<td>0.30</td>
</tr>
<tr>
<td><input type="radio" name="E2" value="0.35"  <?php echo ($dimE2==0.35?"checked=\"yes\"":""); ?>> > 20 minutes et 
<br /><= 30 minutes</td>
<td>0.35</td>
</tr>
<tr>
<td><input type="radio" name="E2" value="0.40"  <?php echo ($dimE2==0.40?"checked=\"yes\"":""); ?>> > 30 minutes</td>
<td>0.40</td>
</tr>
</table>
<input type="hidden" name="evenement" value="<?php echo $evenement; ?>">
</form>
</div>
<h2>Rappel du RNMSC-DPS</h2><p style="text-align:justify; font-size:0.9em;">
Les DPS font partie des missions de sécurité civile dévolues uniquement aux associations agréées de sécurité civile.
<br />
En tout état de cause, il incombe à  l'autorité de police compétente, si elle le juge nécessaire ou approprié, de prendre toute disposition en matière de secours à  personnes pour assurer la sécurité lors d'un rassemblement de personnes, sur son territoire de compétences. A ce titre, elle peut imposer à  l'organisateur un DPS dimensionné selon les modalités du présent référentiel national.
<br />
En outre, l'organisateur est libre de faire appel, en complément du DPS à  personnes prescrit, à  tout autre moyen humain ou matériel, destiné à  augmenter le niveau de sécurité de la manifestation.</p>
</p>
<p style="text-decoration:none;font-size:0.8em;">Arrêté du 7 novembre 2006 fixant le référentiel national relatif aux dispositifs prévisionnels de secours<br><a href="http://www.legifrance.gouv.fr/WAspad/UnTexteDeJorf?numjo=INTE0600910A" target="_blank">NOR: INTE0600910A</a>
</p><a href="http://www.interieur.gouv.fr/sections/a_l_interieur/defense_et_securite_civiles/autres_acteurs/associations-securite-civile/missions-securite-civile/d-dps/cpsdocument_view" target="_blank" style="text-decoration:none;font-size:0.8em;">
<img src="images/rnmsc_dps.gif" style="float:right;margin:1.5em;" border="0">
Réferentiel National - Missions de Sécurité Civile : Dispositifs Prévisionnels de Secours</a>
<br />
<?php
echo EbFin();

echo "<input type=submit value='fermer cette page' onclick='fermerfenetre();'> ";
?>
