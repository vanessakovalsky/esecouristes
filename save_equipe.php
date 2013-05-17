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
  
include_once ("config.php");
check_all(18);

?>

<html>
<SCRIPT language=JavaScript>

function redirect() {
     url="equipe.php";
     self.location.href=url;
}

function suppress(p1) {
  if ( confirm("Voulez vous vraiment supprimer ce type de compétence? \nCeci entrainera une suppression des compétences de ce type \net des enregistrements concernés dans le tableau des affectations \net dans le tableau de garde ")) {
     url="del_equipe.php?EQ_ID="+p1;
     self.location.href=url;
  }
  else{
       redirect();
  }
}
</SCRIPT>

<?php

include_once ("config.php");

$NEWEQ_ID=intval($_GET["NEWEQ_ID"]);
$EQ_JOUR=intval($_GET["EQ_JOUR"]);
$EQ_NUIT=intval($_GET["EQ_NUIT"]);
$EQ_ID=intval($_GET["EQ_ID"]);
$EQ_NOM=mysql_real_escape_string($_GET["EQ_NOM"]);
$EQ_TYPE=mysql_real_escape_string($_GET["EQ_TYPE"]);
if ( isset ($_GET["duree"])) $duree=intval($_GET["duree"]);
else $duree=0;
$operation=$_GET["operation"];
if ( isset ($_GET["section"])) $section=intval($_GET["section"]);
else $section=0;

//=====================================================================
// update la fiche
//=====================================================================

if ( $operation == 'update' ) {
   $query="update equipe set
	       EQ_ID='".$NEWEQ_ID."',
	       EQ_NOM=\"".$EQ_NOM."\",
	       EQ_JOUR='".$EQ_JOUR."',
	       EQ_NUIT='".$EQ_NUIT."',
	       EQ_DUREE='".$duree."',
	       EQ_TYPE=\"".$EQ_TYPE."\",
	       S_ID='".$section."',
	       S_ID_DATE = NOW()
		  where EQ_ID='".$EQ_ID."'" ;
   $result=mysql_query($query);

   if ( $NEWEQ_ID <> $EQ_ID ) {
      	$query2="update planning_garde_status set
	       EQ_ID=".$NEWEQ_ID."
	       where EQ_ID=".$EQ_ID ;
   		$result2=mysql_query($query2);

      	$query2="update poste set
	       EQ_ID=".$NEWEQ_ID."
	       where EQ_ID=".$EQ_ID ;
   		$result2=mysql_query($query2);
   		
   		$query2="update planning_garde set
	       EQ_ID=".$NEWEQ_ID."
	       where EQ_ID=".$EQ_ID ;
   		$result2=mysql_query($query2);
   		
   		$query2="update categorie_evenement_affichage set
	       EQ_ID=".$NEWEQ_ID."
	       where EQ_ID=".$EQ_ID ;
   		$result2=mysql_query($query2);
   }
}

//=====================================================================
// insertion nouvelle fiche
//=====================================================================

if ( $operation == 'insert' ) {
   $query="insert into equipe
   ( EQ_ID, EQ_NOM, EQ_JOUR, EQ_NUIT,S_ID,S_ID_DATE, EQ_DUREE, EQ_TYPE)
   values
   ($NEWEQ_ID, \"$EQ_NOM\", $EQ_JOUR, $EQ_NUIT,$section,NOW(),$duree, \"$EQ_TYPE\")";
   $result=mysql_query($query);
}

//=====================================================================
// update categorie_evenement_affichage
//=====================================================================
$query2="select distinct CEV_CODE from categorie_evenement";
$result2=mysql_query($query2);
while ($row=@mysql_fetch_array($result2)) {
	$CEV_CODE=$row["CEV_CODE"];
	$CEV_CODE_VALUE=intval($_GET[$CEV_CODE]);
	$query3="delete from categorie_evenement_affichage 
			where CEV_CODE='".$CEV_CODE."'
			and EQ_ID=".$NEWEQ_ID ;
	$result3=mysql_query($query3);
	$query3="insert into categorie_evenement_affichage (CEV_CODE, EQ_ID, FLAG1)
			 values ('".$CEV_CODE."', ".$NEWEQ_ID.", ".$CEV_CODE_VALUE.")";
	$result3=mysql_query($query3);	
}

if ($operation == 'delete' ) {
   echo "<body onload=suppress('$EQ_ID')>";
}
else {
  echo "<body onload=redirect()>";
}
?>
