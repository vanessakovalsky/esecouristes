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
check_all(14);
$confid=$_GET["confid"];
$value=$_GET["value"];
?>

<SCRIPT>
function redirect(url) {
	 parent.gauche.window.location.reload();
	 self.location.href = url;
}
</SCRIPT>
<?php

$query="update configuration set VALUE=\"".$value."\"
		where ID=".$confid;
$result=mysql_query($query);

$query="update configuration set VALUE='1'
		where ID=-1";
$result=mysql_query($query);

if ( $confid == 8 ) {
 	$query="update pompier set P_EMAIL='".$value."'
		where P_CODE='1234'";
	$result=mysql_query($query);
}
# si nbsections == 0, disable gardes and grades
if ( $confid == 2 ) {
 	if ( $value == '0' ) {
	    $query="update configuration set VALUE='0'
		  where ID in ('3','5')";
	    $result=mysql_query($query);
	}
	else {
	 	$query="update configuration set VALUE='1'
		  where ID in ('3','5')";
	    $result=mysql_query($query);
	}
}

# si vehicule desactive, desactiver materiel aussi
if ( $confid == 4 ) {
 	if ( $value == '0' ) {
	    $query="update configuration set VALUE='0'
		  where ID = 18";
	    $result=mysql_query($query);
	}	
}

# si materiel active, activer vehicule aussi
if ( $confid == 18 ) {
 	if ( $value == '1' ) {
	    $query="update configuration set VALUE='1'
		  where ID = 4";
	    $result=mysql_query($query);
	}	
}

if ( $confid == 6 ) {
	 $query="update section set S_CODE='".$value."'
		where S_CODE='eBrigade'";
	 $result=mysql_query($query);
}
if ( $confid == 21 and $value <> "")
	if (!is_dir($value)) {
			mkdir($value, 0777);
			mkdir($value."/save", 0777);
			mkdir($value."/files", 0777);
			mkdir($value."/files_section", 0777);
			mkdir($value."/files_messages", 0777);
			mkdir($value."/diplomes", 0777);
	}

echo "<body onload=\"redirect('configuration.php');\">";

?>
