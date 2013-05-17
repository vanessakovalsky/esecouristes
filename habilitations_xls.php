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
check_all(40);
$s=intval($_GET["s"]);
$groupe=intval($_GET["groupe"]);
$q=mysql_real_escape_string($_GET["q"]);
$critere=mysql_real_escape_string($_GET["critere"]);

// permission de voir les externes?
if ( check_rights($_SESSION['id'], 37)) $externe=true;
else  $externe=false;

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="personnel_habilitations.xls"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header("Expires: 0");
$charset="ISO-8859-15";

echo  "<html>";
echo  "<head>
<meta http-equiv=\"Content-type\" content=\"text/html;charset=".$charset."\" />
<style id=\"Classeur1_16681_Styles\"></style>
<style type=\"text/css\">";
echo  "</style>
</head>
<body>
<div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\">";
echo  "\n"."<table x:num border=1 cellpadding=0 cellspacing=0 width=100% style=\"border-collapse: collapse\">";

$query="select GP_DESCRIPTION from groupe where GP_ID=".$groupe;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$GP_DESCRIPTION=$row["GP_DESCRIPTION"];

// ===============================================
// habilitations
// ===============================================
if ( $critere == 'habilitation' ) {
 
if ( $groupe < 100){
	$query =" select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_NOM 'NOM', p.p_statut, 
		 		p.P_PRENOM 'prenom', concat(s_code,' - ',s_description) 'section', 
				g1.gp_description 'groupe1', p.gp_flag1 'flag1', g2.gp_description 'groupe2', p.gp_flag2 'flag2'
			    from pompier p, section s, groupe g1, groupe g2
				where p.P_SECTION=s.S_ID
				and p.P_OLD_MEMBER=0
				and ( p.gp_id = ".$groupe." or p.gp_id2 = ".$groupe." )
				and g1.gp_id = p.gp_id
				and g2.gp_id = coalesce(p.gp_id2,0)";	
}
else {
	$query =" select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_NOM 'NOM', p.p_statut, 
		 		p.P_PRENOM 'prenom', concat(s_code,' - ',s_description) 'section', 
				g.gp_description 'groupe1', niv
			    from pompier p, section_flat s,  section_role sr, groupe g
				where sr.S_ID = s.S_ID
				and g.GP_ID = sr.GP_ID
				and p.P_ID = sr.P_ID
				and p.P_OLD_MEMBER=0
				and sr.gp_id = ".$groupe;
}
if ( $s > 0 ) $query .=" and s.S_ID in (".get_family($s).")";
if ( !$externe ) $query .= " and p_statut <> 'EXT' ";
$query.=" order by NOM, prenom asc ";	

$result=mysql_query($query);
$number=mysql_num_rows($result);

if ( $s <> 0 ) $cmt=" de ".get_section_name("$s");
else $cmt=" de ".$cisname;

if ( $nbsections == 0 ) $colspan=5;
else $colspan=4;
if ( $groupe < 100 ) $colspan++;
echo "<tr>
  <td colspan= $colspan ><b>Personnel".$cmt." avec permission $GP_DESCRIPTION</b> ($number personnes)</td>
 </tr>";


echo "<tr>
    <td>Nom</td>
    <td>Prénom</td>";
if ( check_rights($_SESSION['id'], 43))
	echo "<td>Email</td>";      	  
if ( $nbsections == 0 ) {      	  
  echo "<td>Section</td>";
}     	  
echo "<td>Principal</td>";
if ( $groupe < 100 ) 
   echo "   <td>Secondaire</td>";
echo "</tr>";

while ($row=@mysql_fetch_array($result)) {
	$P_EMAIL=$row["Email"];
	$P_NOM=$row["NOM"];
	$P_PRENOM=$row["prenom"];
	$p_statut=$row["p_statut"];
	$section=$row["section"];
	$flag1=(isset($row['flag1'])?$row['flag1']:"");
	$flag2=(isset($row['flag2'])?$row['flag2']:"");
	if ( $flag1 == 1 ) $flag1=" (+)";
	else $flag1="";
	if ( $flag2 == 1 ) $flag2=" (+)";
	else $flag2="";
	$groupe1=(isset($row['groupe1'])?$row['groupe1']:"");
	$groupe2=(isset($row['groupe2'])?$row['groupe2']:"");
	
	if ( $groupe1 ==  'Président (e)' ) {
		// vrai président ou responsable d'antenne
		if ( $row['niv'] == 4 ) $groupe1 =  "Responsable d'antenne";
	}
	
	if ($groupe1 <> "") $groupe1 .=$flag1;
	if ($groupe2 <> "") $groupe2 .=$flag2;

	echo "\n<tr>
			<td>".strtoupper($P_NOM)."</td>
			<td>".ucfirst($P_PRENOM)."</td>";
	if ( check_rights($_SESSION['id'], 43))
		echo "<td>".$P_EMAIL."</td>";
	if ( $nbsections == 0 ) {      	  
  		echo "<td>".$section."</td>";
  	}
	echo "  <td>".$groupe1."</td>";
	if ( $groupe < 100 ) 
		echo "  <td>".$groupe2."</td>";
	echo "  </tr>";
			
}
echo "</table>";
}

// ===============================================
// competence
// ===============================================

else {
 $q = explode(",",$q);
 switch($critere){
   case "et":
	   $query ="select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_NOM 'NOM', p.P_PRENOM 'prenom', p.p_statut,
			 concat(s_code,' - ',s_description) 'section'";
	   $query .=" from pompier p, section s";
	   $query .=" where p.P_OLD_MEMBER=0";
	   for ($i=0;$i<count($q);$i++){		
		   $query .=" and p.p_id in ( 
				  select q.p_id from qualification q
				  where q.ps_id = ".$q[$i]."
				  and ( date_format(q.q_expiration,'%Y%m%d') > date_format(now(),'%Y%m%d')
				      or q.q_expiration is null
				    )
				)";
	   }
	   $query .=" AND p.P_SECTION=s.S_ID ";
	   if ( !$externe ) $query .= " and p.p_statut <> 'EXT'";
	   if ($s) $query .=" AND p.P_SECTION in (".get_family($s).")";
	   $query.=" order by NOM, PRENOM asc ";
	   break;
   case "ou":
   		$query="";
	    for ($i=0;$i<count($q);$i++){
	 	    $query .=" select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_NOM 'NOM', p.p_statut,
		 		p.P_PRENOM 'prenom', concat(s_code,' - ',s_description) 'section'
			    from pompier p, section s
				where p.P_SECTION=s.S_ID
				and p.P_OLD_MEMBER=0
				and p_id in ( 
					select q.p_id from qualification q
					where q.ps_id = ".$q[$i]."
					and ( date_format(q.q_expiration,'%Y%m%d') > date_format(now(),'%Y%m%d')
				           or q.q_expiration is null
				         )
				    )";
				if ($s) $query .=" and p.P_SECTION in (".get_family($s).")";
				if ( !$externe ) $query .= " and p.p_statut <> 'EXT'";
		    if ($i < count($q) -1) $query .=" union ";
	   }
	   $query.=" order by NOM, PRENOM asc ";	
	   break;
  }
  $result=mysql_query($query);
  $number=mysql_num_rows($result);


  if ( $s <> 0 ) $cmt=" de ".get_section_name("$s");
  else $cmt=" de ".$cisname;

  if ( $nbsections == 0 ) $colspan=4;
  else $colspan=3;

  echo "<tr>
  <td colspan= $colspan ><b>Recherche de personnel".$cmt." par compétence </b> ($number personnes)</td>
   </tr>";


  echo "<tr>
    <td>Nom</td>
    <td>Prénom</td>";
  if ( check_rights($_SESSION['id'], 43))
	echo "<td>Email</td>";      	  
  if ( $nbsections == 0 ) {      	  
    echo "<td>Section</td>";
  } 
  echo "</tr>";

  while ($row=@mysql_fetch_array($result)) {
	$P_EMAIL=$row["Email"];
	$P_NOM=$row["NOM"];
	$P_PRENOM=$row["prenom"];
	$p_statut=$row["p_statut"];
	$section=$row["section"];

	echo "\n<tr>
			<td>".strtoupper($P_NOM)."</td>
			<td>".ucfirst($P_PRENOM)."</td>";
	if ( check_rights($_SESSION['id'], 43))
			echo "<td>".$P_EMAIL."</td>";
	if ( $nbsections == 0 ) {      	  
  		echo "<td>".$section."</td>";
  	}
	echo "  </tr>";
			
  }
  echo "</table>"; 
}

?>
