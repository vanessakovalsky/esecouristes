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
     url="poste.php?order=PS_ID";
     self.location.href=url;
}

function suppress(p1, p2, p3) {
  if ( confirm("Voulez vous vraiment supprimer le poste / la compétence n°"+ p1 +"? \nCeci entrainera une suppression des enregistrements concernés\n dans le tableau des affectations et dans le tableau de garde ")) {
     url="del_poste.php?PS_ID="+p1;
     self.location.href=url;
  }
  else{
       redirect();
  }
}
</SCRIPT>

<?php

include_once ("config.php");


$PS_ID=$_GET["PS_ID"];
$operation=$_GET["operation"];
if (isset ($_GET["NEWPS_ID"])) $NEWPS_ID=intval($_GET["NEWPS_ID"]);
if (isset ($_GET["TYPE"])) $TYPE=mysql_real_escape_string($_GET["TYPE"]);
if (isset ($_GET["DESCRIPTION"])) $DESCRIPTION=mysql_real_escape_string($_GET["DESCRIPTION"]);
if (isset ($_GET["PO_JOUR"])) $PO_JOUR=intval($_GET["PO_JOUR"]);
if (isset ($_GET["PO_NUIT"])) $PO_NUIT=intval($_GET["PO_NUIT"]);
if (isset ($_GET["PS_EXPIRABLE"])) $PS_EXPIRABLE=intval($_GET["PS_EXPIRABLE"]);
if (isset ($_GET["PS_AUDIT"])) $PS_AUDIT=intval($_GET["PS_AUDIT"]);
if (isset ($_GET["PS_DIPLOMA"])) $PS_DIPLOMA=intval($_GET["PS_DIPLOMA"]);
if (isset ($_GET["PS_SECOURISME"])) $PS_SECOURISME=intval($_GET["PS_SECOURISME"]);
if (isset ($_GET["PS_NATIONAL"])) $PS_NATIONAL=intval($_GET["PS_NATIONAL"]);
if (isset ($_GET["PS_PRINTABLE"])) $PS_PRINTABLE=intval($_GET["PS_PRINTABLE"]);
if (isset ($_GET["PS_RECYCLE"])) $PS_RECYCLE=intval($_GET["PS_RECYCLE"]);
if (isset ($_GET["PS_USER_MODIFIABLE"])) $PS_USER_MODIFIABLE=intval($_GET["PS_USER_MODIFIABLE"]);
if (isset ($_GET["EQ_ID"])) $EQ_ID=intval($_GET["EQ_ID"]);
if (isset ($_GET["F_ID"])) $F_ID=intval($_GET["F_ID"]);

//=====================================================================
// controle sur jour / nuit
//=====================================================================
if ($operation <> 'delete' ) {
	$query="select EQ_JOUR, EQ_NUIT from equipe where EQ_ID=".$EQ_ID;
	$result=mysql_query($query);
	$row=@mysql_fetch_array($result);
	$EQ_JOUR=$row["EQ_JOUR"];
	$EQ_NUIT=$row["EQ_NUIT"];
	if ( $EQ_JOUR == 0 ) $PO_JOUR = 0;
	if ( $EQ_NUIT == 0 ) $PO_NUIT = 0;
}
//=====================================================================
// update la fiche
//=====================================================================

if ( $operation == 'update' ) {
   $query="update poste set
	       PS_ID=".$NEWPS_ID.",
	       TYPE=\"".$TYPE."\",
	       DESCRIPTION=\"".$DESCRIPTION."\",
	       PO_JOUR=".$PO_JOUR.",
		   F_ID=".$F_ID.",
	       PO_NUIT=".$PO_NUIT.",
	       EQ_ID=".$EQ_ID.",
	       PS_EXPIRABLE=".$PS_EXPIRABLE.",
		   PS_AUDIT=".$PS_AUDIT.",
		   PS_DIPLOMA=".$PS_DIPLOMA.",
		   PS_SECOURISME=".$PS_SECOURISME.",
		   PS_NATIONAL=".$PS_NATIONAL.",
		   PS_PRINTABLE=".$PS_PRINTABLE.",
		   PS_RECYCLE=".$PS_RECYCLE.",
		   PS_USER_MODIFIABLE=".$PS_USER_MODIFIABLE."
	where PS_ID=".$PS_ID ;
   $result=mysql_query($query);
   
   if ( $NEWPS_ID <> $PS_ID ) {
   		$query1="update qualification set
	       PS_ID=".$NEWPS_ID."
	       where PS_ID=".$PS_ID ;
   		$result1=mysql_query($query1);
   		
   		$query1="update personnel_formation set
	       PS_ID=".$NEWPS_ID."
	       where PS_ID=".$PS_ID ;
   		$result1=mysql_query($query1);
   		
   		$query1="update evenement set
	       PS_ID=".$NEWPS_ID."
	       where PS_ID=".$PS_ID ;
   		$result1=mysql_query($query1);
   
   		$query2="update planning_garde set
	       PS_ID=".$NEWPS_ID."
	       where PS_ID=".$PS_ID ;
   		$result2=mysql_query($query2);
   		
   		$query3="update equipage set
	       PS_ID=".$NEWPS_ID."
	       where PS_ID=".$PS_ID ;
   		$result3=mysql_query($query3);
   		
   		$query3="update type_participation set
	       PS_ID=".$NEWPS_ID."
	       where PS_ID=".$PS_ID ;
   		$result3=mysql_query($query3);
   		
   		$query3="update diplome_param set
	       PS_ID=".$NEWPS_ID."
	       where PS_ID=".$PS_ID ;
   		$result3=mysql_query($query3);
   		
   		$query3="update evenement_competences set
	       PS_ID=".$NEWPS_ID."
	       where PS_ID=".$PS_ID ;
   		$result3=mysql_query($query3);
   }
}

//=====================================================================
// insertion nouvelle fiche
//=====================================================================

if ( $operation == 'insert' ) {
   $query="insert into poste
   (PS_ID, EQ_ID, TYPE, DESCRIPTION, PO_JOUR, PO_NUIT, PS_EXPIRABLE, PS_AUDIT, 
   	PS_DIPLOMA, PS_SECOURISME, PS_NATIONAL, PS_PRINTABLE, PS_RECYCLE, PS_USER_MODIFIABLE, F_ID)
   values
   ($NEWPS_ID, $EQ_ID,\"$TYPE\",\"$DESCRIPTION\", $PO_JOUR, $PO_NUIT, $PS_EXPIRABLE, $PS_AUDIT, 
   $PS_DIPLOMA, $PS_SECOURISME, $PS_NATIONAL, $PS_PRINTABLE, $PS_RECYCLE, $PS_USER_MODIFIABLE, $F_ID)";
   $result=mysql_query($query);
}

if ($operation == 'delete' ) {
   echo "<body onload=suppress('$PS_ID')>";
}
else {
   echo "<body onload=redirect()>";
}
?>
