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
writehead();
echo "<script type='text/javascript' src='checkForm.js'></script>";
?>

<script language="JavaScript">

function modify( form, confid, value, defaultvalue ) {
 	var ok=1;
	if (value.indexOf(' ') >= 0){
      	alert("Ce paramètre de configuration ne doit pas contenir d'espaces.");
      	form.value = defaultvalue;
      	ok=0;
    }
   	else {
   	 	/*if ( confid == 12 ) {
			for (i = 0; i < value.length; i++)
    		{   
       			var c = value.charAt(i);
        		if (((c < "0") || (c > "9"))) {
		 		alert ("Seul des numéros sont attendus: "+ value + " ne convient pas.");
		 		form.value = defaultvalue;
		 		ok=0;
				}
    		}
    	}
    	else*/ if ( confid == 8 ) {
    	  if (! mailCheck(config.f8, defaultvalue)) {
    	  	form.value=defaultvalue;
    	  	ok=0;
    	  }
    	}
    	if (ok==1) {
     		url = "save_configuration.php?confid="+confid+"&value="+value;
     		self.location.href = url;
     	}
    }
}

function redirect() {
     cible="index_d.php";
     self.location.href=cible;
}
function dbcfg() {
	 self.location.href = "configuration_db.php" ;
}


</script>
</head>

<?php
echo "<body>";

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/configure.png></td><td>
      <font size=4><b>Configuration de l'application</b></font></td></tr></table>";

echo "<form name='config'>";
  	
echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr height=10 class=TabHeader>
      <td width=100>Paramètre</td>
      <td width=0></td>
	  <td width=220>Valeur</td>
      <td width=0></td>
      <td width=300>Description</td>
	  </tr>";

// ===============================================
// le corps du tableau
// ===============================================

$query="select ID, NAME, VALUE,DESCRIPTION from configuration where ID > 0 order by ORDERING";
$result=mysql_query($query);
$i=0;
while ($row=@mysql_fetch_array($result)) {
    $ID=$row["ID"];
    $NAME=$row["NAME"];
    $VALUE=$row["VALUE"];
    $DESCRIPTION=$row["DESCRIPTION"];
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }

   echo "<tr height=10>
      <td bgcolor=$mycolor width=100>$NAME</td>
      <td bgcolor=$mydarkcolor width=0></td>
	  <td bgcolor=$mycolor width=220 align=left valign=middle>";
  if ( $ID == 1 ) echo "$VALUE";
  elseif ( $ID == 2 ) {
  	echo "<select id='f2' onchange='modify(config.f2,\"".$ID."\", this.value, \"".$VALUE."\")'>";
  	if ( $VALUE == '0' ) {
	   $selected="selected"; 
	   $disabled='disabled';
	}
	else {
	   $selected="";
	   $disabled='';
	}  
  	echo "<option value='0' $selected>association de sécurité civile</option>";
  	if ( $VALUE == '1' ) $selected="selected"; 
	  else $selected="";
  	echo "<option value='1' $selected>caserne pompiers volontaires</option>";
  	if ( $VALUE == '3' ) $selected="selected";
	  else $selected="";
  	echo "<option value='3' $selected>caserne pompiers professionnels</option>";
  }
  elseif (( $ID ==3 ) or ( $ID == 5 )){
  	echo "<select id='f$ID' $disabled  onchange='modify(config.f".$ID.", \"".$ID."\", this.value, \"".$VALUE."\")'>";
  	if ( $VALUE == '0' ) {
	  		echo "<option value='0' selected >non</option>";
  			echo "<option value='1'>oui</option>";
  	}
  	else {
	  		echo "<option value='0'>non</option>";
  			echo "<option value='1' selected>oui</option>";  	
  	}
  }
  elseif (( $ID == 4 ) or ( $ID == 13) or ( $ID == 14) 
  		or ( $ID == 18) or ( $ID == 19) or ( $ID == 22) 
  		or ( $ID == 23) or ( $ID == 24) or ( $ID == 25)){
  	echo "<select id='f$ID' onchange='modify(config.f".$ID.", \"".$ID."\", this.value, \"".$VALUE."\")'>";
  	if ( $VALUE == '0' ) {
	  		echo "<option value='0' selected >non</option>";
  			echo "<option value='1'>oui</option>";
  	}
  	else {
	  		echo "<option value='0'>non</option>";
  			echo "<option value='1' selected>oui</option>";  	
  	}
  }
  elseif ( $ID == 8 ) {
  	echo "<input id='f8' type=text value='$VALUE' size=25 
	  onchange='modify(config.f".$ID.",\"".$ID."\", this.value, \"".$VALUE."\")'>";
  }
  elseif ( $ID == 9 ) {
  	echo "<select id='f9' onchange='modify(config.f".$ID.",\"".$ID."\", this.value, \"".$VALUE."\")'>";
  	if ( $VALUE == '0' ) $selected="selected"; 
	  else $selected="";
  	echo "<option value='0' $selected>SMS désactivés</option>";
  	if ( $VALUE == '2' ) $selected="selected";
	  else $selected="";
  	echo "<option value='2' $selected>www.envoyersms.org</option>";
  	if ( $VALUE == '3' ) $selected="selected";
	  else $selected="";
  	echo "<option value='3' $selected>www.clickatell.com</option>";
  	if ( $VALUE == '1' ) $selected="selected"; 
	  else $selected="";
  	echo "<option value='1' $selected>www.sms.pictures-on-line.net</option>";
  	if ( $VALUE == '4' ) $selected="selected";
	  else $selected="";
  	echo "<option value='4' $selected>Orange</option>";
  	echo "</select>";
  }
  elseif ( $ID == 11 ) {
    if ( $sms_provider == 0 ) $disabled='disabled';
    else $disabled='';
  	echo "<input id='f$ID' type=password value='$VALUE' $disabled size=25 onBlur='modify(config.f".$ID.",\"".$ID."\", this.value, \"".$VALUE."\")'>"; 
  }
  elseif ( $ID == 12 ) {
    if (($sms_provider == 3) || ($sms_provider == 4)) $disabled='';
    else $disabled='disabled';
  	echo "<input id='f$ID' type=text value='$VALUE' $disabled size=25 
	  onBlur='modify(config.f".$ID.",\"".$ID."\", this.value, \"".$VALUE."\")'>"; 
  }
  elseif ( $ID == 10 ) {
    if (( $sms_provider == 0 ) or ( $sms_provider == 1 )) $disabled='disabled';
    else $disabled='';
  	echo "<input id='f$ID' type=text value='$VALUE' $disabled size=25 onBlur='modify(config.f".$ID.",\"".$ID."\", this.value, \"".$VALUE."\")'>"; 
  }
  elseif ( $ID == 15 ) {
  	echo "<select id='f15' onchange='modify(config.f".$ID.",\"".$ID."\", this.value, \"".$VALUE."\")'>";
  	if ( $VALUE == '0' ) $selected="selected"; 
	  else $selected="";
  	echo "<option value='0' $selected>désactivé</option>";
  	if ( $VALUE == '1' ) $selected="selected";
	  else $selected="";
  	echo "<option value='1' $selected>activé</option>";
  	echo "</select>";
  }
  elseif ( $ID == 16 ) {
  	echo "<select id='f16' onchange='modify(config.f".$ID.",\"".$ID."\", this.value, \"".$VALUE."\")'>";
  	if ( $VALUE == '0' ) $selected="selected"; 
	  else $selected="";
	echo "<option value='0' $selected>pas de longueur minimum</option>";
	for ( $k=1 ; $k<=20 ; $k++) {
	  	if ( $VALUE == $k ) $selected="selected";
	  	else $selected='';
  		echo "<option value='$k' $selected>$k</option>";
  	}
  	echo "</select>";
  }
  elseif ( $ID == 17 ) {
  	echo "<select id='f17' onchange='modify(config.f".$ID.",\"".$ID."\", this.value, \"".$VALUE."\")'>";
  	if ( $VALUE == '0' ) $selected="selected"; 
	  else $selected="";
	echo "<option value='0' $selected>jamais de bloquage</option>";
	for ( $k=3 ; $k<=10 ; $k++) {
	  	if ( $VALUE == $k ) $selected="selected";
	  	else $selected='';
  		echo "<option value='$k' $selected>$k échecs</option>";
  	}
  	echo "</select>";
  }
  elseif ( $ID >= 6 ) {
  	echo "<input id='f$ID' type=text value='$VALUE' size=25 onBlur='modify(config.f".$ID.",\"".$ID."\", this.value, \"".$VALUE."\")'>"; 
  }
   echo "</td><td bgcolor=$mydarkcolor width=0></td>
      <td bgcolor=$mycolor width=300><font size=1>$DESCRIPTION</font></td>
	  </tr>";
   $i++;
}
echo "</table>";
echo "</td></tr></table>";

echo "</form>";
echo "</div>";

echo "<div align=center>";
echo " <input type=submit value='retour accueil' onclick='redirect();'>";
echo " <input type=submit value='base de données' onclick='dbcfg();'>";
echo " <input type=submit value='phpinfo' onclick='window.open(\"phpinfo.php\");'>";
echo "</div>";

if ( is_file('images/user-specific/logo.jpg'))
 	$logo='images/user-specific/logo.jpg';
else 
 	$logo='images/logo.jpg';
 	
echo "<div align=left>
<ul>
<li>Le logo peut être personnalisé: placer un fichier <b>logo.jpg</b>dans le répertoire images/user-specific. <img src=".$logo." height=25 title='logo actuel'>
<li>De même la bannière de la page d'accueil peut être personnalisée. Placer un fichier <b>banniere.jpg</b> dans le répertoire images/user-specific.
<li>Le logo iPhone peut être personnalisé aussi (à la racine du site) <b>apple-touch-icon.png</b> <img src=apple-touch-icon.png width=16 title='icone iPhone actuelle'>
<li>De même que le logo web (à la racine du site) <b>favicon.ico</b> <img src=favicon.ico width=12 title='icone actuelle'>
</font>
</ul>";
echo "</div>";

?>
