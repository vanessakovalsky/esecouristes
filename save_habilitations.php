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
check_all(9);

$GP_ID=intval($_GET["GP_ID"]);
$GP_DESCRIPTION=$_GET["GP_DESCRIPTION"];
$GP_USAGE=$_GET["gp_usage"];
$sub_possible=intval($_GET["sub_possible"]);
if ( $GP_DESCRIPTION == "") $GP_DESCRIPTION= "groupe ".$GP_ID;
?>

<html>
<SCRIPT language=JavaScript>

function redirect(category) {
     url="habilitations.php?from=update&category="+category;
     self.location.href=url;
}

</SCRIPT>

<?php
include_once ("config.php");


//=====================================================================
// enregistrer les habilitations saisies
//=====================================================================
if (( $GP_ID <> 4) and ($GP_ID <> 0)) {
	$query="delete from groupe where GP_ID=".$GP_ID;
	$result=mysql_query($query);
	$query="insert into groupe (GP_ID, GP_DESCRIPTION, TR_SUB_POSSIBLE, GP_USAGE) 
			values (".$GP_ID.", \"".$GP_DESCRIPTION."\", ".$sub_possible.",  \"".$GP_USAGE."\")";
	$result=mysql_query($query);
}
$query="select distinct F_ID, F_TYPE from fonctionnalite";
$result=mysql_query($query);

while ($row=@mysql_fetch_array($result)) {
      	    $F_ID=$row["F_ID"];
      	    $F_TYPE=$row["F_TYPE"];
      	    // on ne supprime pas F9 pour admin
      	    if (($GP_ID <> 4) or ( $F_ID <> 9 )) {
      	        if (( $gardes == 1 ) or ( $F_TYPE <> 1 )) {
      	    		$query2="delete from habilitation where F_ID=".$F_ID." and GP_ID=".$GP_ID;
      	    		$result2=mysql_query($query2);
      	    		if (isset($_GET[$F_ID])) {
	       	   			$query2="insert into  habilitation (GP_ID, F_ID)
	      		  			select ".$GP_ID.",".$F_ID;
      	       			$result2=mysql_query($query2);
  	        		}
  	        	}
			}
}

// remettre la fonctionnalite 0
$query2="delete from habilitation where F_ID=0 and GP_ID=".$GP_ID;
$result2=mysql_query($query2);

$query2="insert into habilitation (GP_ID, F_ID) values(".$GP_ID.",0)";
$result2=mysql_query($query2);

if ( $GP_ID >= 100 ) $category = 'R';
else $category = 'G';
echo "<body onload=\"redirect('".$category."')\">";
?>
