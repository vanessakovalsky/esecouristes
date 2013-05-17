<?php

/** Application esecouristes
Vanessa Kovalsky David - Mars 2012 (vanessa.kovalsky@free.fr)
Page pour générér un export excel du bilan annuel pour envoyer au ministère pour obtenir l'agrément MSC.
Licence GNU/GPL V3.

**/
session_start();
include_once ("config.php"); 


//On récupère l'année et le mois 


if (isset ( $_GET["year"])) $year=intval($_GET["year"]);
else $year=date("Y");

// On choisit la section concernée

if (isset ($_SESSION['section'])) {
   $section=$_SESSION['section'];
}

//print_r($section);

// ON récupère l'info pour les sous-sections

if (isset($_POST["subsections"])) {
	$subsection = intval($_POST["subsections"]);
}
else $subsection == 0;

include_once ("bilan-annuel.php");

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="bilan_annuel_'.$section.'_'.$year.'.xls"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header("Expires: 0");

//======================
$charset="ISO-8859-15";
//$charset="UTF8";
//=====================
?>
<table>
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

	$bilan_annuel = bilan_annuel($section,$subsection,$year);
	//print_r($bilan_annuel);
	
	// On affiche les données en faisant une boucle sur le tableau
	
	foreach($bilan_annuel as $month=>$tableau_mois)
		{
	
			if ( $i%2 == 0 ) {
		  		$mycolor="$mylightcolor";
		  	}
		  	else {
		  		$mycolor="#FFFFFF";
		  	}
		  	
		  	if ($month == 'total') {
		  		$myclass = 'TabHeader separation-haut separation-bas';
		  	}
		  	
		  	?>
		
			<tr bgcolor="<?php echo $mycolor;?>" 
			class="<?php echo $myclass;?>">
				<td class="separation-droite separation-gauche large"><?php echo $month;?></td>
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
