<?php

include_once ("config.php");
check_all(14);

//$photo_condition="and p.p_photo is not null";
$photo_condition="";
$full_condition=" p.p_old_member=0 ".$photo_condition." and p.p_statut <> 'EXT' and p.P_NOM <> 'ADMIN' and p.P_NOM not like '% REX'";

// type: badges, adresses, departements
if (isset ($_GET["type"])) {
	    $type=mysql_real_escape_string($_GET["type"]);
}
else $type='choice';

// local functions __________________________________________________________
function get_nb_rows() {
	$sql="select count(1) as NB from badge_list";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	return $row['NB'];
}

function get_long_name($s) {
	if ( substr($s,0,4) == 'Fédé') $r = $s;
	else if ( substr($s,0,5) == 'Prote') $r = $s;
	else if ( substr($s,0,4) == 'Délé') $r = $s;
	else {
		$voyels = array('A','E','I','O','U','Y','H','a','e','i','o','u','y','h');
		$short2=substr($s,0,2);
		$short1=substr($s,0,1);
		$short5=substr($s,0,5);
		$last1=substr($s, -1);
		$last2=substr($s, -2);
		if ($short5 == 'Alpes' or $short5 == 'Hauts' or $short5 == 'Arden' or $last2 == 'es' or $short2 == 'Bo' or $last2 == 'or' ) $r = " des ";
	    else if ( $last2 == 'et') $r = " du ";
		else if ( $short5 == 'Loire' or $short5 == 'Sarth' or $short5 == 'Somme') $r = " de la ";
		else if ( $short5 == 'Haute' or $short5 == 'Paris') $r = " de ";
		else if ( $short2 == 'Ai' ) $r = " de l'";
		else if ( $last2 == 'in' or $short5 == 'Rhône') $r = " du ";
		else if ( in_array($short1 , $voyels) ) $r = " de l'";
		else if ( $short5 == 'Maine' or  $short2 == 'Fi' or  $short2 == 'Pu' or $short2 == 'Pa'  or $short2 == 'Va' or  $short5 == 'Lot e' or  $short2 == 'Ta') $r = " du ";
		else if ( $short2 == 'Ma' or $short2 == 'Me' or $short2 == 'Ré' or $short2 == 'Cô' or $short2 == 'Ni' or $short2 == 'Cr') $r = " de la ";
		else if ( $last1 == 'e' or $last2 == 'is') $r = " de ";
		else $r = " du ";
		$r = "Protection Civile".$r.$s;
	}
	return $r;
}

// main __________________________________________________________

if ( $type == 'choice' ||  $type == 'save' ) {
 	writehead();
 	echo "<body><div align=center>";
	echo "<h2>Export des informations pour badges</h2>";
	echo "<p>Cette page permet de générer les informations nécessaires à l'impression des badges";
 	echo "<p><a href=export_badges.php?type=badges>Fichier des badges</a>";
 	echo "<p><a href=export_badges.php?type=adresses>Fichier des adresses</a>";
 	echo "<p><a href=export_badges.php?type=departements>Fichier des départements</a>";
	echo "<p><a href=export_badges.php?type=save>Sauver la liste</a>";
	if ( $type=='save'){
	 
	 	// initial cleanup
	 	//$sql="delete from badge_list";
		//$res = mysql_query($sql);
	 	//$sql="delete from log_history where LT_CODE='IMPBADGE'";
		//$res = mysql_query($sql);
		// en of initial cleanup
	 
		$nb1=get_nb_rows();
		// supprimer les données enregistrées aujourd'hui
		$sql="delete from badge_list where DATE = CURDATE()";
		$res = mysql_query($sql);			
		$sql="insert into badge_list ( P_ID, S_ID, P_PHOTO, DATE) 
		select p.p_id, p.p_section, p.p_photo, NOW()
		from pompier p
		where  ".$full_condition;
		$res = mysql_query($sql);
		$nb2 = get_nb_rows() - $nb1;
		
		$id=intval($_SESSION['id']);
		$query="insert into log_history (P_ID, LT_CODE, LH_WHAT, LH_COMPLEMENT)
 		select $id, 'IMPBADGE', p.P_ID, concat('avec photo ',p.P_PHOTO)
		from pompier p
		where  ".$full_condition;
 		$res = mysql_query($query);
		
		echo "<p><font color=red>".$nb2." demandes de badges enregistrées.</font>";
	}
 	echo "</body></html>";
	exit;
}

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="'.$type.'.xls"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header("Expires: 0");
$charset="ISO-8859-15";

echo  "<html>";
echo  "<head>";

echo "<meta http-equiv=\"Content-type\" content=\"text/html;charset=".$charset."\" />
<style id=\"Classeur1_16681_Styles\"></style>
<style type=\"text/css\">";
echo  "</style>
</head>
<body>
<div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\">";
echo  "\n"."<table x:num border=1 cellpadding=0 cellspacing=0 width=100% style=\"border-collapse: collapse\">";


// badges ou adresses
if ( $type == 'badges' or $type == 'adresses') {
$sql = "select distinct p.p_id, p.p_nom, p.p_prenom, p.p_address, p.p_zip_code, p.p_city,
		s.S_ID ,s.s_code, s.s_description, p.p_photo, 
		s.S_PARENT, sf.NIV, 
		s2.s_code code_parent, s2.s_description description_parent
		from pompier p, section_flat sf, section s
		left join section s2 on s2.s_id = s.s_parent
		where p.p_section = s.s_id
		and sf.S_ID = s.S_ID
		and ".$full_condition."
		order by s.s_code, p.p_nom, p.p_prenom";

$res = mysql_query($sql);
while($row = mysql_fetch_array($res)){
	$P_ID = $row['p_id'];
	$nom = strtoupper($row['p_nom']);
	$prenom = my_ucfirst($row['p_prenom']);
	$address = $row['p_address'];
	$city = strtoupper($row['p_city']);
	$zip_code = strtoupper($row['p_zip_code']);
	if ( strlen($zip_code) < 3 ) $zip_code="";
	else $zip_code ="'".$zip_code;

	$photo = str_replace('/','',$row['p_photo']);
	if ( $row['NIV'] == $nbmaxlevels -1 ) {
		//$section = $row['code_parent']." - ".$row['description_parent'];
		$section = get_long_name($row['description_parent']);
		$antenne = $row['s_description'];
	}
	else {
		//$section = $row['s_code']." - ".$row['s_description'];
		$section = get_long_name($row['s_description']);
		$antenne = "";
	}
	
	if ( $type == 'badges')
		echo "<tr>
		<td>".$P_ID."</td>
		<td>".$nom."</td>
		<td>".$prenom."</td>
		<td>".$section."</td>
		<td>".$antenne."</td>
		<td>".$photo."</td>
		</tr>";
		
	if ( $type == 'adresses')
		echo "<tr>
		<td>".$P_ID."</td>
		<td>".$nom."</td>
		<td>".$prenom."</td>
		<td>".$section."</td>
		<td>".$address."</td>
		<td>".$city."</td>
		<td>".$zip_code."</td>
		</tr>";
}
}

// departements

if ( $type == 'departements' ) {
		$sql="select distinct s.s_id, s.s_code, s.s_description, s.s_address, s.s_zip_code, s.s_city, sf.NIV 
		from section s, section_flat sf
		where s.S_ID = sf.S_ID
		and sf.NIV in (0,3)
		order by NIV, s.s_code asc
		";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res)){
		 	$q2="select count(1) as NB
				from pompier p
				where ".$full_condition;
			if ( $row['NIV'] == 3 )
				$q2 .= " and p.P_SECTION in (".get_family($row["s_id"]).")";
			else
				$q2 .= " and p.P_SECTION=".$row["s_id"];
			$r2 = mysql_query($q2);
			$row2 = mysql_fetch_array($r2);
			$NB=$row2['NB'];
		 
		 	if ( $NB > 0 ) {
				//$section = $row['s_code']." - ".$row['s_description'];
				$section = get_long_name($row['s_description']);
				$address = $row['s_address'];
				$city = strtoupper($row['s_city']);
				$zip_code = strtoupper($row['s_zip_code']);			
				echo "<tr>
				<td>".$section."</td>
				<td>".$address."</td>
				<td>".$city."</td>
				<td>".$zip_code."</td>
				</tr>";
			}
		}
}





echo "</table></body>
</html>";
?>