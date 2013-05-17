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

if ( isset($_GET["file"])) $file= $_GET["file"];
else $file="";
if (isset($_GET["folder"])) $folder = $_GET["folder"];
else $folder="";
include_once ("config.php");
writehead();

?>
<script language="JavaScript">

function restore(where, what) {
   if ( confirm ("Vous allez appliquer sur la base de données avec le contenu du fichier " + where + "/" + what +  "?" )) {
         self.location = "restore.php?folder=" + where + "&file=" + what;
         
   }
}
function deletefile(what) {
   if ( confirm ("Etes vous certain de vouloir supprimer ce fichier " + what +  "?" )) {
         self.location = "delete_file.php?file=" + what;
   }
}
function redirect(url) {
     self.location.href=url;
}
</script>
</head>
<?php

echo "<body>";



//====================================================
// restore
//====================================================

if ($file!="") {
	$filename = $file;
	@set_time_limit($mytimelimit);
	if ( $folder == 'sql' ) 
		$file=fread(fopen('sql/'.$file, "r"), 10485760);
	else 
		$file=fread(fopen($filesdir."/".$folder.'/'.$filename, "r"), 10485760);

	$query=explode(";
",$file);
	for ($i=0;$i < count($query)-1;$i++) {
		mysql_query($query[$i]) or die(mysql_error());
	}
	echo "<p>";
	write_msgbox("opération réussie", $star_pic, "<p align=center>$filename rechargé avec succès! <br><p align=center><a href=index_d.php>$myspecialfont retour</font></a>",10,0);
	echo "<p>";
}

//====================================================
// backups
//====================================================
$path=$filesdir."/save/";

if ( ! is_dir ($path)) $path="./save/";

if ($file=="") {
echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/database.png></td><td>
      <font size=4><b>Sauvegardes de la base de données</b></font></td></tr></table>";
echo "<br><input type=submit value='nouvelle sauvegarde' onclick='redirect(\"backup.php?mode=interactif\");'><p>";

$f_arr = array(); $f = 0;
$dir=opendir($path); 
while ($file = readdir ($dir)) { 
      if ($file != "." && $file != ".." && file_extension($file) == 'save' ) {
      	        $f_arr[$f++] = $file;
      }
}
closedir($dir);

$f2_arr = array(); $f = 0;
$dir=opendir($path);
while ($file = readdir ($dir)) {
      if ($file != "." && $file != ".." && file_extension($file) == 'sql') {
            	$f2_arr[$f++] = $file;
	             }
}
closedir($dir);

if ( count( $f_arr )+count( $f2_arr )  > 0 ) {
	echo "<p><table>";
	echo "<tr>
	  <td class='FondMenu'>";
	echo "<table cellspacing=0 border=0>";
}

if ( count( $f_arr ) > 0 ) { 
	echo "<tr class=TabHeader >
      <td width=250>Fin de mois</td>
      <td width=80>Version</td>
      <td width=100>Size (kB)</td>
      <td width=130>Date</td>
      <td width=100>Actions</td>
    </tr>";

	sort( $f_arr ); reset( $f_arr );
	for( $i=0; $i < count( $f_arr ); $i++ ) {
      	if ( $i%2 == 0 ) {
      	 	$mycolor="$mylightcolor";
      	}
      	else {
      	 	$mycolor="#FFFFFF";
      	}
     	echo "<tr bgcolor=$mycolor>
	 		<td>".$f_arr[$i]."</td>
	 		<td align=center>".get_file_version($f_arr[$i])."</td>
	    	<td align=center>
	   	   ".round(filesize($path.$f_arr[$i])/1024,1)."	
			</td>
	   	<td align=center>
	   		".date("Y-m-d H:i",filemtime($path.$f_arr[$i]))."
	   		</td>
	   	<td align=center>
	   		<a href=\"javascript:restore('save','".$f_arr[$i]."')\"> 
			   <img src=images/reload.gif border=0 title='recharger la base'></font></a> 
	   		<a href=\"javascript:deletefile('".$f_arr[$i]."')\"> 
			   <img src=images/trash.png border=0 title='supprimer ce fichier'></a>
	   	</td>
	   	</tr>";
	}
}

if ( count( $f2_arr ) > 0 ) {
	echo "<tr class=TabHeader >
      <td width=250>Récentes</td>
      <td width=80>Version</td>
      <td width=100>Size (kB)</td>
      <td width=130>Date</td>
      <td width=100>Actions</td>
    </tr>";

	sort( $f2_arr ); reset( $f2_arr );
	for( $i=0; $i < count( $f2_arr ); $i++ ) {
     	if ( $i%2 == 0 ) {
      	 	$mycolor="$mylightcolor";
      	}
      	else {
      	 	$mycolor="#FFFFFF";
      	}
      	if (date("d-m-Y",filemtime($path.$f2_arr[$i])) == getnow()) $bold="<b>"; 
	  	else  $bold="";
     	echo "<tr bgcolor=$mycolor>
	 	<td>$bold ".$f2_arr[$i]."</td>
	 	<td align=center>$bold ".get_file_version($f2_arr[$i])."</td>
	    <td align=center>
	   	   ". round(filesize($path.$f2_arr[$i])/1024,1)."	
			</td>
	   	<td align=center>
	   		".date("Y-m-d H:i",filemtime($path.$f2_arr[$i]))."
	   		</td>
	   	<td align=center>
	   		<a href=\"javascript:restore('save','".$f2_arr[$i]."')\"> 
			   <img src=images/reload.gif border=0 title='recharger la base'></font></a> 
	   		<a href=\"javascript:deletefile('".$f2_arr[$i]."')\"> 
			   <img src=images/trash.png border=0 title='supprimer ce fichier'></a>
	   	</td>
	   	</tr>";
	}
}
echo "  </table>";
echo "</td></tr></table>";

//====================================================
// upgrades
//====================================================
$dbversion=get_conf(1);
if ($dbversion == '') $dbversion  = 'version inconnue';
echo "<p><div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/upgrade.gif></td><td>
      <font size=4><b>Upgrades la base de données<br>$dbversion</b></font></td></tr></table>";
 
echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<tr class=TabHeader>
      <td width=250>Référence</td>
      <td width=80>Version</td>
      <td width=100>Size (kB)</td>
      <td width=130>Date</td>
      <td width=100>Actions</td>
    </tr>";

$f_arr = array(); $f = 0;
$dir=opendir($sql); 
while ($file = readdir ($dir)) { 
      if ($file != "." && $file == "reference.sql") {
      	        $f_arr[$f++] = $file;
      }
}
closedir($dir);

sort( $f_arr ); reset( $f_arr );
for( $i=0; $i < count( $f_arr ); $i++ ) {
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
     echo "<tr bgcolor=$mycolor>
	 	<td >".$f_arr[$i]."</td>
	 	<td align=center>$version</td>
	    <td align=center >". round(filesize($sql.$f_arr[$i]) / 1024,1) ."	
			</td>
	   <td align=center>".date("Y-m-d H:i",filemtime($sql.$f_arr[$i]))."
	   		</td>
	   <td align=center>
	   		<a href=\"javascript:restore('sql','".$f_arr[$i]."')\"> 
			   <img src=images/reload.gif border=0 title='réinitialiser la base'></font></a> 
	   </td>
	   </tr>";

}

echo "<tr class=TabHeader>
      <td width=250>Upgrades</td>
      <td width=80>Version</td>
      <td width=100>Size (kB)</td>
      <td width=130>Date</td>
      <td width=100>Actions</td>
    </tr>";

$f_arr = array(); $f = 0;
$dir=opendir($sql);
while ($file = readdir ($dir)) {
      if ($file != "." && $file != ".." && $file != "reference.sql" && file_extension($file) == 'sql') {
            	$f_arr[$f++] = $file;
	             }
}
closedir($dir);

sort( $f_arr ); reset( $f_arr );
for( $i=0; $i < count( $f_arr ); $i++ ) {
     if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      echo "<tr bgcolor=$mycolor>
	 	<td >".$f_arr[$i]."</td>
	 	<td align=center>".get_file_version($f_arr[$i])."</td>
	    <td align=center>".round(filesize($sql.$f_arr[$i]) / 1024,1) ."	
			</td>
	   <td align=center >".date("Y-m-d H:i",filemtime($sql.$f_arr[$i]))."
	   		</td>
	   <td align=center>";
	   if (( $dbversion == get_file_from_version ($f_arr[$i])) or (get_conf(1) == '')) {
	   		echo "<a href=\"javascript:restore('sql','".$f_arr[$i]."')\">
			   <img src=images/reload.gif border=0 title='upgrader la base'></font></a> ";
			}
			else {
			  echo " <img src=images/minino.png border=0 
			  	title='upgrade vers ".get_file_version($f_arr[$i])." impossible '> ";
			}
	   		echo "
	   </td>
	   </tr>";
}
echo "  </table>";
echo "</td></tr></table>";
}
?>
