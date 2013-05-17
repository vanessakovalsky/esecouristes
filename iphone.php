<html>
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
writehead('iphone');

if ( strpos($_SERVER['HTTP_USER_AGENT'], "iPad"))
	echo "<meta name='viewport' content='width=800'/>";
else 
	echo "<meta name='viewport' content='width=250'/>";


if ( $grades == 1) $str="Matricule";
else  $str="Identifiant";

echo "<body>
<div id='Layer1' align=center>";
echo "<form name='form' action='check_login.php' method=post>";

echo "
  <table cellspacing='0' border='0'>
    <tr class=TabHeader>
      <td align=right colspan=3>$cisname - identifiez vous</td>
    </tr>
    <tr bgcolor='$mylightcolor'> 
      <td><b>$str</b></td>
      <td><input type='text' name='id'>
    </tr>
    <tr bgcolor='$mylightcolor'> 
      <td><b>Mot de passe</b></td>
      <td><input type='password' name='pwd'>
    </tr>
    <tr bgcolor='$mylightcolor'> 
      <td colspan=2 align=center>
      <input type='submit' value='envoyer' onClick=\"this.disabled=true;this.value='attendez';document.form.submit()\">
      </td>
    </tr>
  </table>
  <p><i><font size=1 color='#3333FF'><a href=lost_password.php>mot de passe perdu</a></font></i></p>";

echo "</form>
</div>
</body>
</html>";
?>
