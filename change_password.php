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
writehead();
?>
<script>
function bouton_redirect() {
	 self.location.href = "index_d.php"	;
    }
</script>
</head>
<?php
$pid=0;
$id=$_SESSION['id'];
$SES_NOM=$_SESSION['SES_NOM'];
$SES_PRENOM=$_SESSION['SES_PRENOM'];
$SES_GRADE=$_SESSION['SES_GRADE'];
$mysection=intval($_SESSION['SES_SECTION']);

if (isset($_GET["meonly"])) {
 	 $meonly=true;
 	 $pid=$id;
}
else if (isset($_GET["pid"])) $pid=intval($_GET["pid"]);

echo "<body>";
echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/key.png></td><td>
      <font size=4><b>Changement de mot de passe</b></font></td></tr></table>";
      
echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<form name='change_pwd' action='save_password.php'>";

if ($pid > 0 ) $msg = "Pour ".ucfirst(get_prenom("$pid"))." ".strtoupper(get_nom("$pid"));
else $msg="Choix mot de passe";

echo "<tr>
      	  <td width=300 colspan=2 class=TabHeader>$msg</td>
      </tr>";
//=====================================================================
// ligne 1
//=====================================================================

if (( check_rights($_SESSION['id'], 25) or check_rights($_SESSION['id'], 9 )) and ( $pid == 0)) {
	echo "<tr height=30>
      	  <td bgcolor=$mylightcolor width=150 align=right>Personne</font></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>";
   		$query="select p.P_ID, p.P_PRENOM, p.P_NOM , s.S_CODE
		   from pompier p, section s
		   where s.S_ID=p.P_SECTION
		   and p.P_OLD_MEMBER = 0
		   and p.P_STATUT <> 'EXT'";
   		if (! check_rights($_SESSION['id'], 9)){
   			$g=get_highest_section_where_granted($_SESSION['id'],25);
   			if ( $g <> '' )
   				$query .="	and p.P_SECTION in (".get_family("$g").")";
   			else 
   				$query .="	and p.P_SECTION in (".get_family("$mysection").")";
   		}
   		$query .="	order by p.P_NOM";

	echo "<select id='person' name='person'>";
   		$result=mysql_query($query);
   		while ($row=@mysql_fetch_array($result)) {
      		$P_NOM=$row["P_NOM"];
      		$P_PRENOM=$row["P_PRENOM"];
      		$P_ID=$row["P_ID"];
      		$S_CODE=$row["S_CODE"];
      		if ( $P_ID == $id ) $selected = 'selected';
      		else $selected ='';
      		if ( $nbsections <> 1 ) $cmt=" (".$S_CODE.")";
      		else $cmt="";
      		echo "<option value='".$P_ID."' $selected>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM).$cmt."</option>\n";
   		}
   		echo "</select>
	 		</td>
 			</tr>";
}
elseif ( $pid == $id) {
	echo "<input type='hidden' name='person' id ='person' value='$pid'>";
}
else if ( check_rights($_SESSION['id'], 25) or check_rights($_SESSION['id'], 9 ) and  $pid > 0) {
	echo "<input type='hidden' name='person' id ='person' value='$pid'>";
}

//=====================================================================
// ligne nouveau password
//=====================================================================

echo "<tr height=30>
      	  <td bgcolor=$mylightcolor width=200  align=right>
			Nouveau mot de passe</b></font></td>
      	  <td bgcolor=$mylightcolor width=150 align=left height=25>
			<input type='password' name='new1' size='20' ></td>";		
echo "</tr>";
echo "<tr height=30>
      	  <td bgcolor=$mylightcolor width=200 align=right >
			Répétez le nouveau mot de passe</font></td>
      	  <td bgcolor=$mylightcolor width=150 align=left height=25>
			<input type='password' name='new2' size='20' ></td>";		
echo "</tr>";

echo "</table>";
echo "</td></tr></table><p>"; 
echo "<input type='submit' value='sauver'>";
echo "<input type=button value='retour' onclick='bouton_redirect();'> </form>";


?>
