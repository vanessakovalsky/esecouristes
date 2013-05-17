<?php

/** Application esecouristes
Vanessa Kovalsky David - Janvier 2013 (vanessa.kovalsky@free.fr)
Page pour générér un bilan annuel national par section.
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
url = "bilan-national-vue.php?year="+year;
self.location.href = url;
}
</script>
<?php

echo '<h2>Bilan annuel en nombre de DPS par section</h2>';

//On récupère l'année et le mois 


if (isset ( $_POST["year"])) $year=intval($_POST["year"]);
else $year=date("Y");

$mycolor=$textcolor;

include_once ("bilan-national.php");

// On affiche les résultats

writehead();
?>
<div align="center">
<?php
// On affiche le choix de l'année et du mois

$year0=$year -1;
$year1=$year +1;
echo "<div id='choix_date'>";
echo "<form action='bilan-national-vue.php' method='post'>";
echo "P&eacute;riode ";
echo " <select id='year' name='year'>";
echo "<option value='$year0'>".$year0."</option>";
echo "<option value='$year' selected >".$year."</option>";
echo "<option value='$year1' >".$year1."</option>";
echo  "</select>";

echo "</div>";

echo  "<input type='submit' value='Valider' /><br />";
echo "</form>"; 

?>
<a href='bilan-national_xls.php?year=<?php echo $year;?>'><img src='images/xls.jpg' id='StartExcel' height='24' border='0' alt='Excel' title='Excel' /></a>
<?php
// on affiche le tableau bilan
?>
<table class="stats" id="bilan">
	<tr>
		<th class="TabHeader separation" rowspan=2>Section</th>
		<th class="TabHeader separation" colspan=10>Nombres d'&eacute;v&egrave;nements</th>
		<th class="TabHeader separation" colspan=8>Heures R&eacute;alis&eacute;es</th>
		<th class="TabHeader separation" colspan=8>Nombres d'&eacute;quipiers</th>
		<th class="TabHeader separation" colspan=7>Nombres d'interventions</th>
		<th class="TabHeader separation" colspan=3>Nombre d'&eacute;vacuations</th>
		<th class="TabHeader separation" rowspan=2>Unit&eacute; DSC</th>
	</tr>
	<tr class="TabHeader rotation separation-bas">
		<th class="rotation180">Total Mission A</th>
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

	$bilan_national = bilan_national($year);
	//print_r($bilan_annuel);
	
	// On affiche les données en faisant une boucle sur le tableau
	
	foreach($bilan_national as $section=>$tableau_section)
		{
	
			if ( $i%2 == 0 ) {
		  		$mycolor="bleu-clair";
		  	}
		  	else {
		  		$mycolor="blanc";
		  	}
		  	
		  	if ($section == 'total') {
		  		$myclass = 'TabHeader separation-haut separation-bas';
				$mycolor = 'TabHeader';
		  	}
		  	
		  	?>
		
			<tr 
			class="<?php echo $myclass; echo $mycolor; ?> ">
				<td class="separation-droite separation-gauche large <?php echo $mycolor; ?>"><?php echo $section;?></td>
				<td><?php echo $tableau_section['missionA']; ?></td>
				<td><?php echo $tableau_section['missionB']; ?></td>
				<td><?php echo $tableau_section['missionC']; ?></td>
				<td><?php echo $tableau_section['paps']; ?></td>
				<td><?php echo $tableau_section['dpspe']; ?></td>
				<td><?php echo $tableau_section['dpsme']; ?></td>
				<td><?php echo $tableau_section['dpsge']; ?></td>
				<td><?php echo $tableau_section['dpsgr']; ?></td>
				<td><?php echo $tableau_section['grpp']; ?></td>
				<td class="separation-droite"><?php echo $tableau_section['missionD']; ?></td>
				<td><?php echo $tableau_section['heures_orange']; ?></td>
				<td><?php echo $tableau_section['heures_lp_corporate']; ?></td>
				<td><?php echo $tableau_section['heures_lp_colis']; ?></td>
				<td><?php echo $tableau_section['heures_lp_enseigne']; ?></td>
				<td><?php echo $tableau_section['heures_lp_sf']; ?></td>
				<td><?php echo $tableau_section['heures_lp_courrier']; ?></td>
				<td><?php echo $tableau_section['heures_autres']; ?></td>
				<td class="separation-droite"><?php echo $tableau_section['heures']; ?></td>		
				<td><?php echo $tableau_section['equipiers']; ?></td>
				<td><?php echo $tableau_section['equipiers_orange']; ?></td>
				<td><?php echo $tableau_section['equipiers_corporate']; ?></td>
				<td><?php echo $tableau_section['equipiers_colis']; ?></td>
				<td><?php echo $tableau_section['equipiers_enseigne']; ?></td>
				<td><?php echo $tableau_section['equipiers_sf']; ?></td>
				<td><?php echo $tableau_section['equipiers_courrier']; ?></td>
				<td class="separation-droite"><?php echo $tableau_section['equipiers_autres']; ?></td>
				<td><?php echo $tableau_section['plaie'];?></td>
				<td><?php echo $tableau_section['trauma'];?></td>
				<td><?php echo $tableau_section['malaise'];?></td>
				<td><?php echo $tableau_section['pci'];?></td>
				<td><?php echo $tableau_section['acr'];?></td>
				<td><?php echo $tableau_section['autres_inter'];?></td>
				<td class="separation-droite"><?php echo $tableau_section['inters'];?></td>
				<td><?php echo $tableau_section['evac_vpsp'];?></td>
				<td><?php echo $tableau_section['evac_autres'];?></td>
				<td class="separation-droite"><?php echo $tableau_section['evacs'];?></td>
				<td><?php echo round($tableau_section['unite_dsc'], 1); ?></td>
			</tr>
			<?php		
			$i++;
		} // fin du foreach $section
		?>
	</table>
</div>

<?php
?>
