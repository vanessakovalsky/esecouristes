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
check_all(0);
$id=$_SESSION['id'];
$S_ID=intval($_GET["section"]);
if ( isset($_GET["evenement"])) $evenement=intval($_GET["evenement"]);
else $evenement=0;

if ($evenement > 0 ) {
	if (! check_rights($_SESSION['id'], 47, "$S_ID") and get_chef_evenement($evenement) <> $_SESSION['id'])
	check_all(15);
}
else {
	if (! check_rights($_SESSION['id'], 47, "$S_ID"))
	check_all(22);
}

writehead();
echo "</head>";

if ( $evenement == 0  ) {
   	// section
   	echo "<div align=center><table cellspacing=0 border=0>
      <tr><td>
      <font size=4><b>".get_section_code("$S_ID")." - ".get_section_name("$S_ID")."</b></font></td></tr>
	  </table>";
   	echo "<form action='save_section.php' method='post' enctype='multipart/form-data'>";
	echo "<input type='hidden' name='operation' value='update'>";
	echo "<input type='hidden' name='S_ID' value='$S_ID'>";
	echo "<input type='hidden' name='status' value='documents'>";
}
else { 
  	// evenement
  	$query="select TE_CODE, E_LIBELLE from evenement where E_CODE=".$evenement;
  	$result=mysql_query($query);
  	$row=@mysql_fetch_array($result);
  	$event_name=$row["E_LIBELLE"];
  	$type=$row["TE_CODE"];
  	echo "<div align=center><table cellspacing=0 border=0>
      <tr><td>
      <font size=4><b>".$event_name."</b></font></td></tr>
	  </table>";
    echo "<form action='evenement_save.php' method='post' enctype='multipart/form-data'>";
	echo "<input type='hidden' name='action' value='document'>";
	echo "<input type='hidden' name='section' value='$S_ID'>";
	echo "<input type='hidden' name='evenement' value='$evenement'>";
	echo "<input type='hidden' name='status' value='documents'>";
}



echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	   <td colspan=2 class=TabHeader>Ajout de document</td>
      </tr>";

if ( $evenement == 0  ) {
	//type
	$query="select TD_CODE, TD_LIBELLE from type_document order by TD_LIBELLE";
	echo "<tr><td bgcolor=$mylightcolor align=right>Type:</td>
		<td bgcolor=$mylightcolor> 
		<select id='type' name='type'>\n";
	$result=mysql_query($query);
	while ($row=@mysql_fetch_array($result)) {
      $TD_CODE=$row["TD_CODE"];
      $TD_LIBELLE=$row["TD_LIBELLE"];
      $selected='';
      if ( isset($_SESSION['td'])) {
			if ($_SESSION['td'] == $TD_CODE) $selected='selected';
	  }
      echo "<option value='".$TD_CODE."' $selected>".$TD_LIBELLE."</option>\n";	  		  	
	}
	echo "</select></td></tr>";
}

//security
$query="select DS_ID, DS_LIBELLE,F_ID from document_security";
echo "<tr><td bgcolor=$mylightcolor align=right>Sécurité: </td>
		<td bgcolor=$mylightcolor>
		<select id='security' name='security'>\n";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $DS_ID=$row["DS_ID"];
      $DS_LIBELLE=$row["DS_LIBELLE"]; 
      echo "<option value='".$DS_ID."' $selected>".$DS_LIBELLE."</option>\n";	  		  	
}
echo "</select></td></tr>";

// Document
echo "<tr><td bgcolor=$mylightcolor align=right>Document (max 5M):</td>
	<td bgcolor=$mylightcolor>
	<input type='file' name='userfile' id='userfile'></td></tr>";

echo "</table>";// end left table
echo "</td></tr></table>"; // end cadre
echo "<input type='submit' value='Envoyer'>";
echo "</form>";
echo "<p><a onclick=\"window.close();return false;\">fermer</a>";
echo "</div>";

?>