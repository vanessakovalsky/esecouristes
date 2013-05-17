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

if ($id == $pompier) $allowed=true;
else if ( $mycompany == get_company($pompier) and check_rights($_SESSION['id'], 45) and $mycompany > 0) {
	$allowed=true;
}
else check_all(40);

$pompier=intval($_GET["pompier"]);
if ( isset ( $_GET['order'])) {
	$order = mysql_real_escape_string($_GET['order']);
}
else $order='PS_ID';

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="liste-des-formations.xls"');
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

$query="select pf.PS_ID, p.TYPE, pf.PF_ID, pf.PF_COMMENT, pf.PF_ADMIS, DATE_FORMAT(pf.PF_DATE, '%d-%m-%Y') as PF_DATE, 
		pf.PF_RESPONSABLE, pf.PF_LIEU, pf.E_CODE, tf.TF_LIBELLE, pf.PF_DIPLOME
	    from personnel_formation pf, type_formation tf, poste p
	    where tf.TF_CODE=pf.TF_CODE
	    and p.PS_ID = pf.PS_ID
        and pf.P_ID=".$pompier."
		order by pf.".$order;
$result=mysql_query($query);
$num=mysql_num_rows($result);

$colspan=7;
echo "<tr>
  <td colspan= $colspan ><b>Formations suivies par 
  ".ucfirst(get_prenom($pompier))." ".strtoupper(get_nom($pompier))."</b> ($num formations)</td>
 </tr>";

// ===============================================
// premiere ligne du tableau
// ===============================================


echo "<tr>
    <td>Type</td>
    <td>date</td>      	      	  
    <td>Type de formation</td>
    <td>N°diplôme</td>
    <td>Lieu</td>
    <td>Délivré par</td>
    <td>Commentaire</td>
</tr>";

while ($row=@mysql_fetch_array($result)) {
   	   $PS_ID=$row["PS_ID"];
   	   $TYPE=$row["TYPE"];
	   $PF_ID=$row["PF_ID"];
	   $PF_COMMENT=$row["PF_COMMENT"];
	   $PF_ADMIS=$row["PF_ADMIS"];
	   $PF_DATE=$row["PF_DATE"];
	   $PF_RESPONSABLE=$row["PF_RESPONSABLE"];
	   $PF_LIEU=$row["PF_LIEU"];
	   $PF_DIPLOME=$row["PF_DIPLOME"];
	   $E_CODE=$row["E_CODE"];
	   $TF_LIBELLE=$row["TF_LIBELLE"];
	   
	   echo "<tr>";
	   echo "<td width=50><font size=1><b>".$TYPE."</b></font></td>";
	   echo "<td width=80><font size=1>".$PF_DATE."</font></td>";
	   echo "<td width=150><font size=1>".$TF_LIBELLE."</font></td>";
	   echo "<td width=130><font size=1><b>".$PF_DIPLOME."</b></font></td>";
	   echo "<td width=100><font size=1>".$PF_LIEU."</font></td>";
	   echo "<td width=150><font size=1>".$PF_RESPONSABLE."</font></td>";
	   echo "<td width=100><font size=1>".$PF_COMMENT."</font></td>";
	   echo "</tr>";
}
echo "</table>";

?>
