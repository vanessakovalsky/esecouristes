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
check_all(37);
$section=$_SESSION['SES_SECTION'];
$id=$_SESSION['id'];
?>

<html>
<SCRIPT language=JavaScript>

function redirect(url) {
     self.location.href=url;
}

function suppress(id) {
  if ( confirm("Voulez vous vraiment supprimer cette entreprise?\n")) {
     url="del_company.php?C_ID="+id;
     self.location.href=url;
  }
  else{
       redirect();
  }
}
</SCRIPT>

<?php

include_once ("config.php");
$operation=$_GET["operation"];

if ($operation == 'delete' ) {
   $C_ID=intval($_GET["C_ID"]);
   echo "<body onload=suppress('".$C_ID."')>";
}
else {

$groupe=intval($_GET["groupe"]);
$TC_CODE=mysql_real_escape_string($_GET["TC_CODE"]);
$C_NAME=$_GET["C_NAME"];
$parent=intval($_GET["parent"]);
if ( $parent == 0 ) $parent='null';
$C_DESCRIPTION=mysql_real_escape_string($_GET["C_DESCRIPTION"]);
$C_SIRET=mysql_real_escape_string($_GET["C_SIRET"]);
$C_ADDRESS=mysql_real_escape_string($_GET["address"]);
$C_ZIP_CODE=mysql_real_escape_string($_GET["zipcode"]);
$C_CITY=mysql_real_escape_string($_GET["city"]);
$C_EMAIL=mysql_real_escape_string($_GET["email"]);
$C_PHONE=mysql_real_escape_string($_GET["phone"]);
$C_FAX=mysql_real_escape_string($_GET["fax"]);
$C_CONTACT_NAME=mysql_real_escape_string($_GET["relation_nom"]);

$C_NAME=STR_replace("\"","",$C_NAME);
$C_DESCRIPTION=STR_replace("\"","",$C_DESCRIPTION);
$C_ADDRESS=STR_replace("\"","",$C_ADDRESS);
$C_ADDRESS=STR_replace("\\","",$C_ADDRESS);
$C_ZIP_CODE=STR_replace("\"","",$C_ZIP_CODE);
$C_CITY=STR_replace("\"","",$C_CITY);
$C_CONTACT_NAME=STR_replace("\"","",$C_CONTACT_NAME);


//=====================================================================
// update la fiche
//=====================================================================

if ( $operation == 'update' ) {
	$C_ID=intval($_GET["C_ID"]);
    $query="update company set
	       TC_CODE=\"".$TC_CODE."\",
	       C_NAME=\"".$C_NAME."\",
	       S_ID=".$groupe.",
	       C_PARENT=".$parent.",
	       C_ADDRESS=\"".$C_ADDRESS."\",
	       C_ZIP_CODE=\"".$C_ZIP_CODE."\",
	       C_CITY=\"".$C_CITY."\",
	       C_EMAIL=\"".$C_EMAIL."\",
	       C_PHONE=\"".$C_PHONE."\",
	       C_FAX=\"".$C_FAX."\",
	       C_SIRET=\"".$C_SIRET."\",
	       C_CONTACT_NAME=\"".$C_CONTACT_NAME."\",
	       C_DESCRIPTION=\"".$C_DESCRIPTION."\"
		   where C_ID =".$C_ID;
    $result=mysql_query($query);
}

//=====================================================================
// insertion nouvelle fiche
//=====================================================================

if ( $operation == 'insert' ) {
   $query = "select max(C_ID) +1 'NEWID' from company";
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   $NEWID=$row["NEWID"];
   if ( $NEWID == '' ) $NEWID=1;
 
   $query="insert into company 
   (C_ID,TC_CODE, C_NAME, S_ID, C_PARENT, C_DESCRIPTION, C_ADDRESS, C_ZIP_CODE, C_CITY, C_EMAIL, C_PHONE, C_FAX, C_CONTACT_NAME, C_CREATED_BY, C_CREATE_DATE, C_SIRET )
   values
   ($NEWID, \"$TC_CODE\",\"$C_NAME\", $groupe, $parent,\"$C_DESCRIPTION\", \"$C_ADDRESS\", \"$C_ZIP_CODE\", 
   \"$C_CITY\", \"$C_EMAIL\", \"$C_PHONE\", \"$C_FAX\", \"$C_CONTACT_NAME\",".$id.", NOW(), \"$C_SIRET\" )";
   $result=mysql_query($query);

}

echo "<body onload=redirect('company.php')>";
}
?>
