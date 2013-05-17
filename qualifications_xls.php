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
get_session_parameters();

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="competences-du-personnel.xls"');
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

$query1="select distinct P_ID , P_NOM , P_PRENOM, P_GRADE, P_STATUT, S_CODE
         from pompier, grade, section
	 where P_GRADE=G_GRADE
	 and P_SECTION=S_ID
	 and P_NOM <> 'admin' 
	 and P_OLD_MEMBER = 0
	 and P_STATUT <> 'EXT'";

	if ( $subsections == 1 ) {
  	   	$query1 .= "\nand P_SECTION in (".get_family("$filter").")";
	}
	else {
  	   	$query1 .= "\nand P_SECTION =".$filter;
	}
      
   	$query1 .= "\norder by P_NOM";
	$result1=mysql_query($query1);

$result1=mysql_query($query1);
$number=mysql_num_rows($result1);

$query2="select e.EQ_ID, p.PS_ID, p.TYPE, p.DESCRIPTION as COMMENT
         from poste p, equipe e
	 	 where p.EQ_ID=e.EQ_ID";
if ( $typequalif <> 0 ) $query2 .= " and e.EQ_ID=".$typequalif;	
$query2 .= " order by e.EQ_ID, p.PS_ID";
$result2=mysql_query($query2);
$num_postes = mysql_num_rows($result2);

if ( $filter <> 0 ) $cmt=" de ".get_section_name("$filter");
else $cmt=" de ".$cisname;

if ( $filter2 <> 0) {
 	$query="select EQ_NOM from equipe where EQ_ID=".$typequalif;
 	$result=mysql_query($query);
 	$row=@mysql_fetch_array($result);
 	$EQ_NOM="(".$row["EQ_NOM"].") ";
}
else $EQ_NOM="";


echo "<tr>
  <td colspan=10><b>Compétences ".$EQ_NOM."du personnel ".$cmt."</b> ($number personnes)</td>
 </tr>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr>";
if ( $grades == 1 ) echo "<td>Grade</td>";
echo "<td>Prénom</td>
      <td>Nom</td>";      	  
if ( $nbsections == 0 ) {      	  
  echo "<td>Section</td>";
}

while ($row2=@mysql_fetch_array($result2)) {
      $TYPE=$row2["TYPE"];
      $PS_ID=$row2["PS_ID"];
      echo "<td >$TYPE</td>";
}
echo "</tr>";

while ($row=@mysql_fetch_array($result1)) {
    $P_ID=$row["P_ID"];
    $P_NOM=$row["P_NOM"];
    $P_PRENOM=$row["P_PRENOM"];
    $P_GRADE=$row["P_GRADE"];
    $S_CODE=$row["S_CODE"];
      
	echo "<tr>";
	if ( $grades == 1 ) echo "<td>".$P_GRADE."</td>";
	echo "<td>".strtoupper($P_NOM)."</td>
      <td>".my_ucfirst($P_PRENOM)."</td>";
	if ( $nbsections == 0 ) {
	 	if (substr($S_CODE,0,1) == '0') $S_CODE="'".$S_CODE;
		echo "<td>".$S_CODE."</td>";
	}

	$result2=mysql_query($query2);
      
	while ($row2=@mysql_fetch_array($result2)) {
    	$PS_ID=$row2["PS_ID"];
    	$query3="select Q_VAL, date_format(Q_EXPIRATION, '%d/%m/%Y') EXP
			from qualification where PS_ID=".$PS_ID." and P_ID=".$P_ID;
		$result3=mysql_query($query3);
		$row3=@mysql_fetch_array($result3);
		if (mysql_num_rows($result3) > 0) {
			$Q_VAL=$row3["Q_VAL"];
			$EXP=$row3["EXP"];
			if ( $Q_VAL > 0 )  {
		 		if ( $EXP <> '' ) $Q = $EXP;
		 		else $Q='oui';
			}
			else $Q='';
		}
		else $Q='';
		echo "<td>".$Q."</td>";  
	}
	echo "</tr>";
}
echo "</table>";

?>
