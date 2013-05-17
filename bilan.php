<?php

/** Application esecouristes
Vanessa Kovalsky David - Mars 2012 (vanessa.kovalsky@free.fr)
Page pour générér un bilan annuel pour envoyer au ministère pour obtenir l'agrément MSC.
Licence GNU/GPL V3.

**/
session_start();
include_once ("config.php"); 

check_all(0);
writehead();
$mysection=$_SESSION['SES_SECTION'];
get_session_parameters();
?>
<script>
function redirect(year,month,section) {
url = "bilan.php?month="+month+"&year="+year+"&section="+section;
self.location.href = url;
}
</script>
<!-- Pour éviter les problèmes de CSS 3<-->
<!--[if lt IE 9]> 
<div class="warning"> <strong>Pour profiter d'un affichage correct, merci de mettre Internet Explorer &agrave; jour vers la nouvelle version <a href="http://www.microsoft.com/france/windows/internet-explorer/telecharger-ie9.aspx">http://www.microsoft.com/france/windows/internet-explorer/telecharger-ie9.aspx</a> ou d'utiliser un autre navigateur comme <a href="http://www.mozilla.org/fr/firefox/fx/">Mozilla Firefox</a></strong>
</div>
 <![endif]-->

<?php

echo '<h2>Bilan annuel en nombre de DPS par mois</h2>';

//On récupère l'année et le mois 


if (isset ( $_POST["year"])) $year=intval($_POST["year"]);
else $year=date("Y");

// ON récupère l'info pour les sous-sections

if (isset($_POST["subsections"])) {
	$subsection = intval($_POST["subsections"]);
}
else $subsection == 0;

// On choisit la section concernée

if (isset ($_POST["section"])) {
   $_SESSION['sectionchoice'] = intval($_POST["section"]);
   $section=intval($_POST["section"]);
}
else if ( isset($_SESSION['sectionchoice']) ) {
   $section=$_SESSION['sectionchoice'];
}
else $section=$mysection;

$section_en_cours = $section;
// Si les sous-sections sont incluses
 	if ( $subsection == 1 ) {
 		$section = get_family("$section");
		$section = explode(",", $section);
	}
 	else { 
 		$section = $section;
	}

$_SESSION["section"]=$section;
$mycolor=$textcolor;
//print_r($_SESSION['section']);

include_once ("bilan-annuel.php");

// On affiche les résultats

writehead();
?>
<div align="center">
<?php
// On affiche le choix de l'année et du mois

$year0=$year -1;
$year1=$year +1;
echo "<div id='choix_date'>";
echo "<form action='bilan.php' method='post'>";
echo "P&eacute;riode ";
echo " <select id='year' name='year'>";
echo "<option value='$year0'>".$year0."</option>";
echo "<option value='$year' selected >".$year."</option>";
echo "<option value='$year1' >".$year1."</option>";
echo  "</select>";

echo "</div>";

// On affiche le choix de la section 

if ($nbsections <> 1 ) {
	  echo " <select id='section' name='section'>";
	  display_children2(-1, 0, $section_en_cours, $nbmaxlevels, $sectionorder);
	  echo "</select><br />";
	 if ( get_children("$section") <> '' ) {
	  if ($subsection == 1 ) $checked='checked';
	  else $checked='';
	//echo $subsection;
	  echo "<input type='checkbox' name='subsections' id='subsections' value='1' $checked />
	   <label for='subsections'>inclure les sous sections</label><br />";
	}

}

echo  "<input type='submit' value='Valider' /><br />";
echo "</form>"; 

?>
<a href='bilan_xls.php?year=<?php echo $year;?>&subsection=<?php echo $subsection;?>'><img src='images/xls.jpg' id='StartExcel' height='24' border='0' alt='Excel' title='Excel' /></a>
<?php
// on affiche le tableau bilan
?>

<table class="stats" id="bilan">
	<tr>
		<th class="TabHeader separation" rowspan=2>Mois</th>
		<th class="TabHeader separation" colspan=10>Nombres d'&eacute;v&egrave;nements</th>
		<th class="TabHeader separation" colspan=8>Heures R&eacute;alis&eacute;es</th>
		<th class="TabHeader separation" colspan=8>Nombres d'&eacute;quipiers</th>
		<th class="TabHeader separation" colspan=7>Nombres d'interventions</th>
		<th class="TabHeader separation" colspan=3>Nombre d'&eacute;vacuations</th>
		<th class="TabHeader separation" rowspan=2>Unit&eacute; DSC</th>
	</tr>
	<tr class="TabHeader rotation separation-bas">
		<th class="rotation180" style="filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);">Total Mission A</th>
		<th class="rotation180">Total Mission B</th>
		<th class="rotation180">Total Mission C</th>
		<th class="rotation180">PAPS</th>
		<th class="rotation180">DPS-PE</th>
		<th class="rotation180">DPS-ME</th>
		<th class="rotation180">DPS-GE</th>
		<th class="rotation180">DPS-GR</th>
		<th class="rotation180">GR-PP</th>
		<th class="rotation180 separation-droite">Total Mission D</th>
		<th class="rotation180">FT et ayant droit</th>
		<th class="rotation180">LP Corporate et ayant droit</th>
		<th class="rotation180">LP Colis et ayant droit</th>
		<th class="rotation180">LP Enseigne et ayant droit</th>
		<th class="rotation180">LP SF et ayant droit</th>
		<th class="rotation180">LP Courrier et ayant droit</th>
		<th class="rotation180">Autres </th>
		<th class="rotation180 separation-droite">Total heures</th>
		<th class="rotation180">Total &eacute;quipier</th>
		<th class="rotation180">FT et ayant droit</th>
		<th class="rotation180">LP Corporate et ayant droit</th>
		<th class="rotation180">LP Colis et ayant droit</th>
		<th class="rotation180">LP Enseigne et ayant droit</th>
		<th class="rotation180">LP SF et ayant droit</th>
		<th class="rotation180">LP Courrier et ayant droit</th>
		<th class="rotation180 separation-droite">Autres</th>
		<th class="rotation180">Plaies / Br&ucirc;lures</th>
		<th class="rotation180">Trauma</th>
		<th class="rotation180">Mailaise sans PC</th>
		<th class="rotation180">Pertes de connaissance</th>
		<th class="rotation180">Arr&ecirc;ts Cardio-respiratoire</th>
		<th class="rotation180">Autres</th>
		<th class="rotation180 separation-droite">Nombre total d'interventions</th>
		<th class="rotation180">Evacs VPSP</th>
		<th class="rotation180">Autres moyens</th>
		<th class="rotation180">Nombre total d'&eacute;vacuations</th>
	</tr>

<?php
	$i = 0;
		
	// On appelle la fonction de récupération des données

	$bilan_annuel = bilan_annuel($section,$subsection,$year);
	//print_r($bilan_annuel);
	
	// On affiche les données en faisant une boucle sur le tableau
	
	foreach($bilan_annuel as $month=>$tableau_mois)
		{
	
			if ( $i%2 == 0 ) {
		  		$mycolor="bleu-clair";
		  	}
		  	else {
		  		$mycolor="blanc";
		  	}
		  	
		  	if ($month == 'total') {
		  		$myclass = 'TabHeader separation-haut separation-bas';
				$mycolor = 'TabHeader';
		  	}
		  	
		  	?>
		
			<tr 
			class="<?php echo $myclass; echo $mycolor; ?> ">
				<td class="separation-droite separation-gauche large <?php echo $mycolor; ?>"><?php echo $month;?></td>
				<td><?php echo $tableau_mois['missionA']; ?></td>
				<td><?php echo $tableau_mois['missionB']; ?></td>
				<td><?php echo $tableau_mois['missionC']; ?></td>
				<td><?php echo $tableau_mois['paps']; ?></td>
				<td><?php echo $tableau_mois['dpspe']; ?></td>
				<td><?php echo $tableau_mois['dpsme']; ?></td>
				<td><?php echo $tableau_mois['dpsge']; ?></td>
				<td><?php echo $tableau_mois['dpsgr']; ?></td>
				<td><?php echo $tableau_mois['grpp']; ?></td>
				<td class="separation-droite"><?php echo $tableau_mois['missionD']; ?></td>
				<td><?php echo $tableau_mois['heures_orange']; ?></td>
				<td><?php echo $tableau_mois['heures_lp_corporate']; ?></td>
				<td><?php echo $tableau_mois['heures_lp_colis']; ?></td>
				<td><?php echo $tableau_mois['heures_lp_enseigne']; ?></td>
				<td><?php echo $tableau_mois['heures_lp_sf']; ?></td>
				<td><?php echo $tableau_mois['heures_lp_courrier']; ?></td>
				<td><?php echo $tableau_mois['heures_autres']; ?></td>
				<td class="separation-droite"><?php echo $tableau_mois['heures']; ?></td>		
				<td><?php echo $tableau_mois['equipiers']; ?></td>
				<td><?php echo $tableau_mois['equipiers_orange']; ?></td>
				<td><?php echo $tableau_mois['equipiers_corporate']; ?></td>
				<td><?php echo $tableau_mois['equipiers_colis']; ?></td>
				<td><?php echo $tableau_mois['equipiers_enseigne']; ?></td>
				<td><?php echo $tableau_mois['equipiers_sf']; ?></td>
				<td><?php echo $tableau_mois['equipiers_courrier']; ?></td>
				<td class="separation-droite"><?php echo $tableau_mois['equipiers_autres']; ?></td>
				<td><?php echo $tableau_mois['plaie'];?></td>
				<td><?php echo $tableau_mois['trauma'];?></td>
				<td><?php echo $tableau_mois['malaise'];?></td>
				<td><?php echo $tableau_mois['pci'];?></td>
				<td><?php echo $tableau_mois['acr'];?></td>
				<td><?php echo $tableau_mois['autres_inter'];?></td>
				<td class="separation-droite"><?php echo $tableau_mois['inters'];?></td>
				<td><?php echo $tableau_mois['evac_vpsp'];?></td>
				<td><?php echo $tableau_mois['evac_autres'];?></td>
				<td class="separation-droite"><?php echo $tableau_mois['evacs'];?></td>
				<td><?php echo round($tableau_mois['unite_dsc'], 1); ?></td>
			</tr>
			<?php		
			$i++;
		} // fin du foreach $months
		?>
	</table>
</div>

<?php
?>
