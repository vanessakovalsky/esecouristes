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
	
if ( isset ($_GET["ask"]) or  isset ($_POST["save"]) or  isset ($_GET["err"])) {
	$identpage='index.php';
	$noconnect=1;
}

include_once ("config.php");

writehead();
?>
<script type='text/javascript' src='checkForm.js'></script>
<script>
function redirect() {
     cible="index.php";
     parent.location.href=cible;
}
</script>
<?php
echo "</head>";

if ( isset ($_GET["err"])) {
	echo "<font size=3 color=red>Erreur de connection à la base de données avec les paramètres choisis<br>
	      Vos changements ont été effacés.<br></font>";
}

if ( isset ($_POST["save"])) {
	$server=$_POST["server"];
	// avoid bug with easyPHP 5.3.x
	if ( $server == 'localhost') $server='127.0.0.1';
	$user=$_POST["user"];
	$password=$_POST["password"];
	$database=$_POST["database"];
}
else {
    if ( file_exists($config_file)) 
        include_once ($config_file);
    // compatibility with eBrigade 2.2
	else if ( file_exists('./config_sql.php')) {
		 include_once ('./config_sql.php');
		 $server=$mysqlserver;
  		 $user=$mysqllogin;
  		 $password=$mysqlpassword;
  	}
	else {
	 	$server='';
	 	$user='';
	 	$password='';
	 	$database='';
	}
}

$err=0;
if (isset($_GET["ask"])) $err=1;
elseif ($server == "") $err=1;
else {
	$conn=mysql_connect("$server","$user", "$password") or $err=1;
	@mysql_selectdb("$database") or $err += 1;
}
if ( isset($_POST["save"])) { 
	if ( $err > 0 ) {
  		echo "<body onload='window.location=\"configuration_db.php?err=1\";'>";
	}
	else {
	    $ret = write_db_config($server,$user,$password,$database);
	    if ( $ret == 1 ) {
   		   echo "cannot write db config file<br>";
   		   exit;
   	    }
	    if ( file_exists("./config_sql.php"))
	 	    unlink("./config_sql.php");
	}
}

// load reference schema if needed
if (( $err == 0 ) and ( check_ebrigade() == 0 )) {
	  	$filename = "reference.sql";
		@set_time_limit($mytimelimit);
		$file=fread(fopen($sql.'/reference.sql', "r"), 10485760);
		$query=explode(";
",$file);
		for ($i=0;$i < count($query)-1;$i++) {
		mysql_query($query[$i]) or die(mysql_error());
	}
	echo "<p>";
	write_msgbox("initialisation réussie", $star_pic, 
		"<p>Schéma de base de données importé avec succès!<br> 
		 Tu peux maintenant te connecter en utilisant le compte admin <br>
		 <b>identifiant:</b> 1234<br>
		 <b>password:</b> 1234<br>
		<p align=center><a href=index.php target=_top> $myspecialfont Se connecter</font>",10,0);
	echo "<p>";
	exit;
}

if ( $err == 0 ) {
    	unset ($noconnect);
   		include_once ("config.php");
   		check_all(14);
}

echo "<body>";

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/configure.png></td><td>
      <font size=4><b>Configuration MySQL</b></font></td></tr></table>";

if (! file_exists($config_file)) {

echo "<form method='POST' name='config' action='configuration_db.php' >";
echo "<p><table>";
echo "<tr>
   <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr class=TabHeader>
      <td colspan=2>Paramètres de connexion à la base de données</td>
	  </tr>";

// ===============================================
// le corps du tableau
// ===============================================

$m=$mylightcolor;
echo "<tr>
      <td bgcolor=$m align=right>Server</td>
	  <td bgcolor=$m align=left valign=middle>
	  <input name='server' type=text value='$server' size=25  
		onchange='isValid2(config.server,\"$server\")'>"; 
$m="#FFFFFF";
echo "</tr><tr>
      <td bgcolor=$m align=right>User</td>
	  <td bgcolor=$m align=left valign=middle> 
	  <input name='user' type=text value='$user' size=25 
	  onchange='isValid2(config.user,\"$user\")'>"; 
$m=$mylightcolor;
echo "</tr><tr>
      <td bgcolor=$m align=right>Password</td>
	  <td bgcolor=$m align=left valign=middle 
	  onchange='isValid2(config.password,\"$password\")'>
      <input name='password' type=password value='$password' size=25>"; 
$m="#FFFFFF";
echo "</tr><tr>
      <td bgcolor=$m align=right>Database name</td>
	  <td bgcolor=$m align=left valign=middle>
	  <input name='database' type=text value='$database' size=25 
	  onchange='isValid2(config.database,\"$database\")' 
	  onMouseOut='isValid2(config.database,\"$database\")'>
</table>
</td></tr></table>
<input type='hidden' name='save' value='yes'><p>
<input type='button' value='retour accueil' onclick='redirect();'>
<input type=submit value='valider'/>
</form></div>";
}
else {
   write_msgbox("Configuration base de données",$warning_pic,"<p>Les paramètres d'accès à la base de données ne peuvent pas être modifiés avec votre navigateur au delà de l'installation initiale.</p><p>Vérifiez sur le serveur la configuration du fichier ".$config_file."<br><input type=submit value='retour' onclick='javascript:history.back(1);'></p>",30,30);
 
}

?>
