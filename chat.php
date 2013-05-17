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
check_all(43,'chat');
$id=$_SESSION['id'];

echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'DTD/xhtml1-transitional.dtd'>
<html>";
writehead();
echo "<script type='text/javascript' src='chat.js'></script></head>";
echo "<body onload='UpdateTimer();'>";

if (isset($_GET['del'])) {
 	check_all(14,'chat');
 	$todelete=intval($_GET['del']);
 	$query="delete from chat where C_ID=".$todelete;
 	$result=mysql_query($query);
}

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/chat.png></td><td>
      <font size=4><b>Messagerie instantanée $cisname<br>Aide en direct</b></font></td></tr></table>";

echo "<div id='Chat' align=center >";
echo "<table>
       <tr>
        <td class='FondMenu'>";
echo "
	<table cellspacing=0 border=0 >
  	<tr class=TabHeader>
    	<td>
      	<div align=left>Liste des messages</div>
    	</td>
     	<td>
      	<div align=left >Liste des utilisateurs en ligne 
		  	<img src=images/miniquestion.png  title='En gras les utilisateurs actuellement présents sur la messagerie'></div>
    	</td>   	
  	</tr>
  	<tr>";
echo "<td bgcolor=$mylightcolor align=left>
	<div id='result'></div>
	</td>";
echo "<td bgcolor=$mylightcolor align=left>
	<div id='users'></div>
	</td>";


echo "</tr>
  	<tr><td bgcolor=$mylightcolor colspan=2>
  	<div id='sender'>
  		<i>Votre message:</i> <input type='text' name='msg' size='30' id='msg' />
         <button onclick='doWork();'>Envoyer</button>
      </div>
    </td></tr>
	</table>";
echo "</td>";


echo "</tr></table>";
echo "</div>";
echo "</body>";
   