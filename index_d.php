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
check_all(0);
$id=$_SESSION['id'];
$SES_NOM=$_SESSION['SES_NOM'];
$SES_PRENOM=$_SESSION['SES_PRENOM'];
$SES_GRADE=$_SESSION['SES_GRADE'];
writehead();
?>

<script language="javascript" src="ts_files/scroll.js"></script>
<script>
function redirect() {
     url="configuration.php";
     self.location.href=url;
}
</script>
</head>	
<?php

if (($already_configured == 0) and ( check_rights($_SESSION['id'], 14))) {
	echo "<body onload=redirect();></body>";
	exit;
}
else echo "<body>";

$name=str_replace(" ","",ucfirst($SES_PRENOM));
$madate=date_fran(date('m'), date('j'), date('Y')) ."/".date('m/Y H:i');
if ( is_file('images/user-specific/banniere.jpg')) {
 	echo "<center><img src=images/user-specific/banniere.jpg border=0>";
 	echo "<div id=Layer1 align=center>";
}
else 
 echo "<div id=Layer1 align=center style='position:relative; top: 30px'>";
 
echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing='0' border='0'>
   <tr>
       <td bgcolor='$mydarkcolor' class=TabHeader>informations utiles - ".ucfirst($madate);

echo "   </tr>
   <tr>
       <td ><SCRIPT LANGUAGE='JavaScript'>Tscroll_init (0)</SCRIPT></td>
   </tr>

</table>";
echo "</td></tr></table>";   

if ( $evenements == 1 or $disponibilites == 1 ) {     
if ($gardes == 0) {
 if ( check_rights($_SESSION['id'], 38) and check_rights($_SESSION['id'], 41)){
 echo "
 <p><table>
	  <tr>
	  <td class='FondMenu'>
	  <table cellspacing='0' border='0'>    
    <tr height=60 valign=bottom>
      <td bgcolor='$mylightcolor' width='34%'><div align= center><a href='evenement_choice.php'><img src='images/info.png' border=0></a> </div></td>";
  if ( $disponibilites == 1 ) 
   echo "<td bgcolor='$mylightcolor' width='33%'><div align= center><a href='dispo.php?person=$id' ><img src='images/korganizer.png' border=0 ></a> </div></td>";
   echo "<td bgcolor='$mylightcolor' width='33%'><div align= center><a href='calendar.php'><img src='images/date2.png' border=0></a> </div></td>
    </tr>";
   echo "<tr height=30 valign=baseline>
      <td bgcolor='$mylightcolor' width='34%' align=center>
      	  <a HREF='evenement_choice.php' onMouseOver=\"img1.src='images/r_evenements.gif'\" onMouseOut=\"img1.src='images/t_evenements.gif'\" >
      	  <img NAME='img1' BORDER='0' SRC='images/t_evenements.gif' alt='événements'></a>
      </td>";
  if ( $disponibilites == 1 ) 
  echo "<td bgcolor='$mylightcolor' width='33%' align=center>
      	  <a HREF='dispo.php?person=$id' onMouseOver=\"img3.src='images/r_disponibilites.gif'\" onMouseOut=\"img3.src='images/t_disponibilites.gif'\" >
      	  <img NAME='img3' BORDER='0' SRC='images/t_disponibilites.gif' alt='saisir ses disponibilités'></a>
      </td>";
  echo "<td bgcolor='$mylightcolor' width='33%' align=center>
      	  <a HREF='calendar.php' onMouseOver=\"img2.src='images/r_calendrier.gif'\" onMouseOut=\"img2.src='images/t_calendrier.gif'\" >
      	  <img NAME='img2' BORDER='0' SRC='images/t_calendrier.gif' alt='voir mon calendrier'></a>
      </td>
    </tr>";
  }
}
else {
  echo "<p><table>
	  <tr>
	  <td class='FondMenu'>
	  <table width='770' cellspacing='0' border='0'>     
    <tr height=60 valign=bottom>
      <td bgcolor='$mylightcolor' width='34%'><div align= center><a href='tableau_garde.php'><img src='images/vcalendar.png' border=0></a> </div></td>
      <td bgcolor='$mylightcolor' width='33%'><div align= center><a href='dispo.php?person=$id' ><img src='images/korganizer.png' border=0 ></a> </div></td>
      <td bgcolor='$mylightcolor' width='33%'><div align= center><a href='calendar.php'><img src='images/date2.png' border=0></a> </div></td>
    </tr>
    <tr height=30 valign=baseline>
      <td bgcolor='$mylightcolor' width='34%' align=center>
      	  <a HREF='tableau_garde.php' onMouseOver=\"img1.src='images/r_tableau_garde.gif'\" onMouseOut=\"img1.src='images/t_tableau_garde.gif'\" >
      	  <img NAME='img1' BORDER='0' SRC='images/t_tableau_garde.gif' alt='tableau de garde'></a>
      </td>
      <td bgcolor='$mylightcolor' width='33%' align=center>
      	  <a HREF='dispo.php?person=$id' onMouseOver=\"img3.src='images/r_disponibilites.gif'\" onMouseOut=\"img3.src='images/t_disponibilites.gif'\" >
      	  <img NAME='img3' BORDER='0' SRC='images/t_disponibilites.gif' alt='saisir ses disponibilités pour le mois suivant'></a>
      </td>
      <td bgcolor='$mylightcolor' width='33%' align=center>
      	  <a HREF='calendar.php' onMouseOver=\"img2.src='images/r_calendrier.gif'\" onMouseOut=\"img2.src='images/t_calendrier.gif'\" >
      	  <img NAME='img2' BORDER='0' SRC='images/t_calendrier.gif' alt='voir mon calendrier'></a>
      </td>
    </tr>
";
}
echo "</table>";
echo "</td></tr></table>";
}   
echo "</div>";
?>


