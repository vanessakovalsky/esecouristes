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
check_all(27);

// parameters
$affichage=(isset($_POST['affichage'])?$_POST['affichage']:'ecran');
$exp=(isset($_POST['exp'])?$_POST['exp']:"");
if (isset ($_POST["section"])) {
   $_SESSION['sectionchoice'] = intval($_POST["section"]);
   $section=intval($_POST["section"]);
}
else if ( isset($_SESSION['sectionchoice']) ) {
   $section=$_SESSION['sectionchoice'];
}
else $section=$_SESSION['SES_SECTION'];
$subsections=(isset($_POST['subsections'])?1:((isset($_POST['exp']))?0:1));
$dtdb = (isset($_POST['dtdb'])? $_POST['dtdb']:date("d-m-Y",mktime(0,0,0,date("m"),1,date("Y"))));
$dtfn = (isset($_POST['dtfn'])? $_POST['dtfn']:date("d-m-Y",mktime(0,0,0,date("m"),date("d"),date("Y"))));
$code = (isset($_POST['code'])? $_POST['code']:"");

$dateJ = date("d-m-Y",mktime(0,0,0,date("m"),date("d"),date("Y")));

// functions
function NettoyerTexte($txt){
	return strip_tags(str_replace("\n"," ",str_replace("\r"," ",$txt)));
}

include_once ("export-sql.php");

// process query

if(isset($table) && isset($select)){
	$sql = "SELECT $select
	FROM $table";
	$sql .= (isset($where)?(($where!="")? "
	WHERE $where ":""):"");
	$sql .= (isset($groupby)?(($groupby!="")? "
	GROUP BY $groupby ":""):"");
	$sql .= (isset($orderby)?(($orderby!="")? "
	ORDER BY $orderby ":""):"");

	//print($sql);
	$result = mysql_query($sql) or die("<pre>$sql</pre><br />Erreur : ".mysql_error());
	$numlig = mysql_num_rows($result);
	$numcol = mysql_num_fields($result);
	$tab = array();

// Titres
	for($col = 0;$col<$numcol;$col++){
		$tab[0][$col]= (mysql_field_name($result, $col));			
	}	

// Données
	$nolig=1;
	for($lig = 0;$lig<$numlig;$lig++){
		for($col = 0;$col<$numcol;$col++){		
			$tab[$nolig][$col] = mysql_result($result, $lig, $col);
		
		}
		//print_r($tab);
		
		$nolig++;
		
	}

// On ajoute la colonne pour Orange

if(isset($table_orange) && isset($select_orange)){
	$sql_orange .= "
	SELECT $select_orange
	FROM $table_orange";
	$sql_orange .= (isset($where_orange)?(($where_orange!="")? "
	WHERE $where_orange ":""):"");
	$sql_orange .= (isset($groupby_orange)?(($groupby_orange!="")? "
	GROUP BY $groupby_orange ":""):"");
	$sql_orange .= (isset($orderby_orange)?(($orderby_orange!="")? "
	ORDER BY $orderby_orange ":""):"");
	//print($sql_orange);
	$result_orange = mysql_query($sql_orange) or die("<pre>$sql_orange</pre><br />Erreur : ".mysql_error());
	
	//Ajout de la colonne Orange
	$tab[0][3] = "Orange";

// Ajout des données Orange dans le tableau :
	$ligne = 0; 
	//print_r($tab);
	
	while ($resultat_orange = mysql_fetch_assoc($result_orange))
		{
		$resultat_section = $resultat_orange['Section'];
		$resultat_type = $resultat_orange['Type événement'];

		$nolig_orange=1;
		for($lig = 0;$lig<$numlig;$lig++){
		
		if ($tab[$nolig_orange][0] == $resultat_section && $tab[$nolig_orange][1] == $resultat_type) 
			{
				$tab[$nolig_orange][3] = $resultat_orange['Orange'];
			}
		$nolig_orange ++;
		}
		}// fin du while
	$numcol ++;		
} // Fin du if $table_orange

// On ajoute la colonne pour La Poste - Corporate

if(isset($table_lp_corporate) && isset($select_lp_corporate)){
	$sql_lp_corporate .= "
	SELECT $select_lp_corporate
	FROM $table_lp_corporate";
	$sql_lp_corporate .= (isset($where_lp_corporate)?(($where_lp_corporate!="")? "
	WHERE $where_lp_corporate ":""):"");
	$sql_lp_corporate .= (isset($groupby_lp_corporate)?(($groupby_lp_corporate!="")? "
	GROUP BY $groupby_lp_corporate ":""):"");
	$sql_lp_corporate .= (isset($orderby_lp_corporate)?(($orderby_lp_corporate!="")? "
	ORDER BY $orderby_lp_corporate ":""):"");
	//print($sql_lp_corporate);
	$result_lp_corporate = mysql_query($sql_lp_corporate) or die("<pre>$sql_lp_corporate</pre><br />Erreur : ".mysql_error());
	
	//Ajout de la colonne La Poste - Corporate
	$tab[0][4] = "La Poste - Corporate";

// Ajout des données La Poste - Corporate dans le tableau :
	$ligne = 0; 
	//print_r($tab);
	
	while ($resultat_lp_corporate = mysql_fetch_assoc($result_lp_corporate))
		{
		$resultat_section = $resultat_lp_corporate['Section'];
		$resultat_type = $resultat_lp_corporate['Type événement'];

		$nolig_lp_corporate=1;
		for($lig = 0;$lig<$numlig;$lig++){
		
		if ($tab[$nolig_lp_corporate][0] == $resultat_section && $tab[$nolig_lp_corporate][1] == $resultat_type) 
			{
				$tab[$nolig_lp_corporate][4] = $resultat_lp_corporate['La Poste - Corporate'];
			}
		$nolig_lp_corporate ++;
		}
		}// fin du while
		
	$numcol ++;		
} // Fin du if $table_lp_corporate

// On ajoute la colonne pour La Poste - Colis

if(isset($table_lp_colis) && isset($select_lp_colis)){
	$sql_lp_colis .= "
	SELECT $select_lp_colis
	FROM $table_lp_colis";
	$sql_lp_colis .= (isset($where_lp_colis)?(($where_lp_colis!="")? "
	WHERE $where_lp_colis ":""):"");
	$sql_lp_colis .= (isset($groupby_lp_colis)?(($groupby_lp_colis!="")? "
	GROUP BY $groupby_lp_colis ":""):"");
	$sql_lp_colis .= (isset($orderby_lp_colis)?(($orderby_lp_colis!="")? "
	ORDER BY $orderby_lp_colis ":""):"");
	//print($sql_lp_colis);
	$result_lp_colis = mysql_query($sql_lp_colis) or die("<pre>$sql_lp_colis</pre><br />Erreur : ".mysql_error());
	
	//Ajout de la colonne La Poste - Colis
	$tab[0][5] = "La Poste - Colis";

// Ajout des données La Poste - Colis dans le tableau :
	$ligne = 0; 
	//print_r($tab);
	
	while ($resultat_lp_colis = mysql_fetch_assoc($result_lp_colis))
		{
		$resultat_section = $resultat_lp_colis['Section'];
		$resultat_type = $resultat_lp_colis['Type événement'];

		$nolig_lp_colis=1;
		for($lig = 0;$lig<$numlig;$lig++){
		
		if ($tab[$nolig_lp_colis][0] == $resultat_section && $tab[$nolig_lp_colis][1] == $resultat_type) 
			{
				$tab[$nolig_lp_colis][5] = $resultat_lp_colis['La Poste - Colis'];
			}
		$nolig_lp_colis ++;
		}
		}// fin du while
	$numcol ++;		
} // Fin du if $table_lp_colis

// On ajoute la colonne pour La Poste - Enseigne

if(isset($table_lp_enseigne) && isset($select_lp_enseigne)){
	$sql_lp_enseigne .= "
	SELECT $select_lp_enseigne
	FROM $table_lp_enseigne";
	$sql_lp_enseigne .= (isset($where_lp_enseigne)?(($where_lp_enseigne!="")? "
	WHERE $where_lp_enseigne ":""):"");
	$sql_lp_enseigne .= (isset($groupby_lp_enseigne)?(($groupby_lp_enseigne!="")? "
	GROUP BY $groupby_lp_enseigne ":""):"");
	$sql_lp_enseigne .= (isset($orderby_lp_enseigne)?(($orderby_lp_enseigne!="")? "
	ORDER BY $orderby_lp_enseigne ":""):"");
	//print($sql_lp_enseigne);
	$result_lp_enseigne = mysql_query($sql_lp_enseigne) or die("<pre>$sql_lp_enseigne</pre><br />Erreur : ".mysql_error());
	
	//Ajout de la colonne La Poste - Enseigne
	$tab[0][6] = "La Poste - Enseigne";

// Ajout des données La Poste - Enseigne dans le tableau :
	$ligne = 0; 
	//print_r($tab);
	
	while ($resultat_lp_enseigne = mysql_fetch_assoc($result_lp_enseigne))
		{
		$resultat_section = $resultat_lp_enseigne['Section'];
		$resultat_type = $resultat_lp_enseigne['Type événement'];

		$nolig_lp_enseigne=1;
		for($lig = 0;$lig<$numlig;$lig++){
		
		if ($tab[$nolig_lp_enseigne][0] == $resultat_section && $tab[$nolig_lp_enseigne][1] == $resultat_type) 
			{
				$tab[$nolig_lp_enseigne][6] = $resultat_lp_enseigne['La Poste - Enseigne'];
			}
		$nolig_lp_enseigne ++;
		}
		}// fin du while
	$numcol ++;		
} // Fin du if $table_lp_enseigne

// On ajoute la colonne pour La Poste - Services Financiers

if(isset($table_lp_financier) && isset($select_lp_financier)){
	$sql_lp_financier .= "
	SELECT $select_lp_financier
	FROM $table_lp_financier";
	$sql_lp_financier .= (isset($where_lp_financier)?(($where_lp_financier!="")? "
	WHERE $where_lp_financier ":""):"");
	$sql_lp_financier .= (isset($groupby_lp_financier)?(($groupby_lp_financier!="")? "
	GROUP BY $groupby_lp_financier ":""):"");
	$sql_lp_financier .= (isset($orderby_lp_financier)?(($orderby_lp_financier!="")? "
	ORDER BY $orderby_lp_financier ":""):"");
	//print($sql_lp_financier);
	$result_lp_financier = mysql_query($sql_lp_financier) or die("<pre>$sql_lp_financier</pre><br />Erreur : ".mysql_error());
	
	//Ajout de la colonne La Poste - Services Financiers
	$tab[0][7] = "La Poste - Services Financiers";

// Ajout des données La Poste - Services Financiers dans le tableau :
	$ligne = 0; 
	//print_r($tab);
	
	while ($resultat_lp_financier = mysql_fetch_assoc($result_lp_financier))
		{
		$resultat_section = $resultat_lp_financier['Section'];
		$resultat_type = $resultat_lp_financier['Type événement'];

		$nolig_lp_financier=1;
		for($lig = 0;$lig<$numlig;$lig++){
		
		if ($tab[$nolig_lp_financier][0] == $resultat_section && $tab[$nolig_lp_financier][1] == $resultat_type) 
			{
				$tab[$nolig_lp_financier][7] = $resultat_lp_financier['La Poste - Services Financiers'];
			}
		$nolig_lp_financier ++;
		}
		}// fin du while
	$numcol ++;		
} // Fin du if $table_lp_financier


// On ajoute la colonne pour La Poste - Courrier

if(isset($table_lp_courrier) && isset($select_lp_courrier)){
	$sql_lp_courrier .= "
	SELECT $select_lp_courrier
	FROM $table_lp_courrier";
	$sql_lp_courrier .= (isset($where_lp_courrier)?(($where_lp_courrier!="")? "
	WHERE $where_lp_courrier ":""):"");
	$sql_lp_courrier .= (isset($groupby_lp_courrier)?(($groupby_lp_courrier!="")? "
	GROUP BY $groupby_lp_courrier ":""):"");
	$sql_lp_courrier .= (isset($orderby_lp_courrier)?(($orderby_lp_courrier!="")? "
	ORDER BY $orderby_lp_courrier ":""):"");
	//print($sql_lp_courrier);
	$result_lp_courrier = mysql_query($sql_lp_courrier) or die("<pre>$sql_lp_courrier</pre><br />Erreur : ".mysql_error());
	
	//Ajout de la colonne La Poste - Colis
	$tab[0][8] = "La Poste - Courrier";

// Ajout des données La Poste - Courrier dans le tableau :
	$ligne = 0; 
	//print_r($tab);
	
	while ($resultat_lp_courrier = mysql_fetch_assoc($result_lp_courrier))
		{
		$resultat_section = $resultat_lp_courrier['Section'];
		$resultat_type = $resultat_lp_courrier['Type événement'];

		$nolig_lp_courrier=1;
		for($lig = 0;$lig<$numlig;$lig++){
		
		if ($tab[$nolig_lp_courrier][0] == $resultat_section && $tab[$nolig_lp_courrier][1] == $resultat_type) 
			{
				$tab[$nolig_lp_courrier][8] = $resultat_lp_courrier['La Poste - Courrier'];
			}
		$nolig_lp_courrier ++;
		}
		}// fin du while
	$numcol ++;		
} // Fin du if $table_lp_courrier

// On ajoute la colonne pour les autres

if(isset($table_autres) && isset($select_autres)){
	$sql_autres .= "
	SELECT $select_autres
	FROM $table_autres";
	$sql_autres .= (isset($where_autres)?(($where_autres!="")? "
	WHERE $where_autres ":""):"");
	$sql_autres .= (isset($groupby_autres)?(($groupby_autres!="")? "
	GROUP BY $groupby_autres ":""):"");
	$sql_autres .= (isset($orderby_autres)?(($orderby_autres!="")? "
	ORDER BY $orderby_autres ":""):"");
	//print($sql_autres);
	$result_autres = mysql_query($sql_autres) or die("<pre>$sql_autres</pre><br />Erreur : ".mysql_error());
	
	//Ajout de la colonne Autre
	$tab[0][9] = "Autres";


// Ajout des données Autres dans le tableau :
	$ligne = 0; 
	//print_r($tab);
	
	while ($resultat_autres = mysql_fetch_assoc($result_autres))
		{
		$resultat_section = $resultat_autres['Section'];
		$resultat_type = $resultat_autres['Type événement'];

		$nolig_autres=1;
		for($lig = 0;$lig<$numlig;$lig++){
		
		if ($tab[$nolig_autres][0] == $resultat_section && $tab[$nolig_autres][1] == $resultat_type) 
			{
				$tab[$nolig_autres][9] = $resultat_autres['Autres'];
			}
		$nolig_autres ++;
		}
		}// fin du while
		

	$numcol ++;
} // Fin du if $table_autres


} // Fin du if $table


// includes
if(substr($exp,0,4)=="tcd_" && in_array($affichage,array('xls')))
	include("export-tcd.php");		
elseif ($affichage == "xls") 
	include("export-xls.php");
elseif ($affichage == "txt")
	include("export-txt.php");

// display
if ( $affichage == 'ecran' ) {
writehead();
echo "<script type='text/javascript'>
function impression()
{
parent.frames[ 'droite' ].print();
}
function showdates(reportid,divid) {
  	var obj = document.getElementById(divid);
 	var status = reportid.substring(0,1);
 	if (status == '1' ) obj.style.display = ''
	else obj.style.display = 'none';
	if ( reportid == '1point' ) {
		document.frmExport.dtdb.value='".$dateJ."';
		document.frmExport.dtfn.value='".$dateJ."';
	}
}

</script></head>";
echo "<body>";

// form
echo "<div align=center><font size=4><b>Reporting et export</b></font><p>";
echo "<form name='frmExport' action='' method='post'>";
echo "<table border=0 cellpadding=0 cellspacing=1 width=500>";
echo "<tr>";

if ( $nbsections <> 1 ) {
	echo "<td width=100>Section :</td>
	<td width=400><select name='section' id='section'>";
	display_children2(-1, 0, $section, $nbmaxlevels);
	echo "</select>";
	"</td></tr>";
	if ($subsections==1) $checked='checked'; 
	else $checked='';
	echo "<tr><td width=100><font size=1>inclure sous-sections</font></td><td width=400>
		<input type='checkbox' name='subsections' id='subsections' $checked></td></tr>";
}
echo "<tr><td width=100>Report :</td><td width=400>
	 <select name='exp' id='exp' onchange='showdates(document.frmExport.exp.value,\"info\")';>";
echo (isset($OptionsExport)?$OptionsExport:"<option value=''>--- Aucun état de synthèse disponible ---</option>");
echo "</select>";
echo "</td></tr>";

//-----------------
//dates
//-----------------

echo "<tr><td colspan=2>";
if ((substr($exp,0,1) == '1') or ($exp == ""))
	echo "<div id='info' style='display:'>";
else
	echo "<div id='info' style='display: none'>";
echo "<table border=0 cellpadding=0 cellspacing=1 width=500>";
echo "<tr><td width=100>Début:</td><td align=left width=400>";
?>
<input class="plain" name="dtdb" id="dtdb" value=
<?php
echo "\"".$dtdb."\"";
?>
size="12"><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fStartPop(document.frmExport.dtdb,document.frmExport.dtfn);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt="" ></a>
<?php
echo "</td></tr>";


echo "<tr><td width=100>Fin :</td><td align=left width=400>";
?>
<input class="plain" name="dtfn" id="dtfn" value=
<?php
echo "\"".$dtfn."\"";
?>
size="12"><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.frmExport.dtdb,document.frmExport.dtfn);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt="" ></a>
<?php
echo "</td></tr></table></td></tr>";
echo "</div>";

echo "<p><input type='hidden' name='affichage' value='ecran'>";
echo "<tr><td colspan=2><input type='submit' value='Afficher'
onclick=\"document.frmExport.affichage.value='ecran';document.frmExport.submit();\"></td></tr></table>";

echo "</form>";


// output
if ( $exp <> "" ) {
	echo "<img src='images/printer.gif' id='StartPrint' height='24' border='0' alt='Imprimer' title='Imprimante' onclick='impression();' class='noprint' align='right' />";
	echo "<img src='images/xls.jpg' id='StartExcel' height='24' border='0' alt='Excel' title='Excel' onclick=\"document.frmExport.affichage.value='xls';document.frmExport.submit();\" class='noprint'  align='right' />";
	if (substr($exp,0,5) <> "1tcd_")
		echo "<img src='images/page_white_text.png' id='StartTxt' height='24' border='0' alt='Fichier texte' title='Fichier texte' onclick=\"document.frmExport.affichage.value='txt';document.frmExport.submit();\" class='noprint'  align='right' />";

	echo "<h2>$export_name</h2>";
	if(substr($exp,0,5)=="1tcd_") include("export-tcd.php");		
	else include("export-html.php");
}

echo "<iframe width=132 height=142 name=\"gToday:contrast:agenda.js\" id=\"gToday:contrast:agenda.js\" src=\"ipopeng.htm\" scrolling=\"no\" frameborder=\"0\" style=\"visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;\"></iframe>";
echo "\n"."</body>
</html>";
}
?>
