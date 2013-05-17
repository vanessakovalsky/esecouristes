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

/*
Export des contacts
format csv
Utilise les noms par défaut du format .wab
A corriger : gestion du champ Nom
au 20090122 - l'utilisateur doit refaire le mappage de ce champ lors de l'import dans le fichier .wab
*/
include_once("config.php");
check_all(0);

$show=1;
$section=$_SESSION['SES_SECTION'];
$pompier = (isset($_GET['pid'])?$_GET['pid']:0);
$category = (isset($_GET['category'])?$_GET['category']:'interne');
$list = (isset($_GET['subsections'])?(($_GET['subsections']==1)?get_family($_GET['section']):$_GET['section']):$_GET['section']);

if ( ! check_rights($_SESSION['id'], 2)) {
	$pompier=0;
	$list="";
}

if ($list==""){
	echo "<p>Pas de sélection... <br>ou droits insuffisants pour exporter la liste des contacts...</p>";
}else{
	$export_name = "Export vcard";
	$select="
		concat( upper(substring(P_PRENOM,1,1)) , substring(P_PRENOM,2)) 'Prénom',
		upper(p.p_nom) 'Nom',	
		null  'Deuxième prénom',
		concat( upper(substring(P_PRENOM,1,1)) , substring(P_PRENOM,2) ,' ',upper(P_NOM)) 'Nom complet',
		p.p_address 'Rue (domicile)',
		p.p_zip_code 'Code postal (domicile)',
		p.p_city 'Ville (domicile)',
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Téléphone mobile',				
		case 
		when p.p_phone2 is null then concat('')
		when p.p_phone2 is not null and p.p_hide = 1 and ".$show."=0 then concat('')
		when p.p_phone2 is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone2) 
		when p.p_phone2 is not null and p.p_hide = 0 then concat(p.p_phone2) 
		end
		as 'Téléphone personnel',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_email) 
		when p.p_email is not null and p.p_hide = 0 then concat(p.p_email) 
		end
		as 'Adresse de messagerie',
		concat(s.s_code,' - ',s.s_description)  'Société'";
	$table="pompier p, section s";
	$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
	$where .= " p.p_section = s.s_id ";
	$where .= " and p.p_old_member = 0 ";//and p.p_id=$pompier";
	if ( $category == 'EXT' ) $where .= " and p.p_statut = 'EXT' ";
	else $where .= " and p.p_statut <> 'EXT' ";
	$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
	$groupby="";
	
	if(isset($table) && isset($select)){
		$sql = "SELECT $select
	FROM $table";
		$sql .= (isset($where)?(($where!="")? "
	WHERE $where ":""):"");
		$sql .= (isset($groupby)?(($groupby!="")? "
	GROUP BY $groupby ":""):"");
		$sql .= (isset($orderby)?(($orderby!="")? "
	ORDER BY $orderby ":""):"");

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
			$nolig++;
		}
	}

	function NettoyerTexte($txt){
		return strip_tags(str_replace("\n"," ",str_replace("\r"," ",$txt)));
	}

	$export_name = str_replace(" ", "_", get_section_name($_GET['section']));
	$export_separateur = ";";	
	$export_extension = "csv";
	
	header('Content-Disposition: attachment; filename="' . $export_name . '.'.$export_extension.'"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: no-cache');

	$lig=0;
	//
	// Titres
	//
	$titres="";
	for($col=0;$col<$numcol;$col++){
		$titres .= "".$tab[$lig][$col]."$export_separateur";
	}
	echo substr($titres,0,strlen($titres)-1)."\r\n";
	//
	// Affichage des lignes
	//
	$no=1;
	for($lig=1;$lig<count($tab);$lig++){
		$ligne="";
		for($col=0;$col<$numcol;$col++){
			$ligne .= "".htmlspecialchars(NettoyerTexte($tab[$lig][$col]))."$export_separateur";
		}
		echo substr($ligne,0,strlen($ligne)-1)."\r\n";
		$no++;
	}
}
?>