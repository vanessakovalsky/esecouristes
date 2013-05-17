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
check_all(41);
$id=$_SESSION['id'];
$mysection=$_SESSION['SES_SECTION'];

if (isset ($_POST["evenement"]))  $evenement=intval($_POST["evenement"]);
else $evenement=intval($_GET["evenement"]);
if (isset ($_POST["equipe"]))  $equipe=intval($_POST["equipe"]);
else if (isset ($_GET["equipe"])) $equipe=intval($_GET["equipe"]);
else $equipe=0;
if (isset ($_GET["action"])) $action=mysql_real_escape_string($_GET["action"]);
else $action='display';

if (isset ($_POST["EE_NAME"])) $EE_NAME=mysql_real_escape_string($_POST["EE_NAME"]);
if (isset ($_POST["EE_DESCRIPTION"])) $EE_DESCRIPTION=mysql_real_escape_string($_POST["EE_DESCRIPTION"]);

writehead();

?>
<STYLE type="text/css">
.categorie{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
</STYLE>
<script type='text/javascript'>

function closeme(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}

<?php
echo "</script>";
echo "</head>";
echo "<body>";

//=====================================================================
// recupérer infos evenement
//=====================================================================
$query="select TE_CODE, E_LIBELLE, E_CLOSED, E_CANCELED, E_OPEN_TO_EXT, S_ID, E_CHEF
		from evenement 
        where E_CODE=".$evenement;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$TE_CODE=$row["TE_CODE"];
$E_LIBELLE=$row["E_LIBELLE"];
$E_CLOSED=$row["E_CLOSED"];
$E_CANCELED=$row["E_CANCELED"];
$E_OPEN_TO_EXT=$row["E_OPEN_TO_EXT"];
$S_ID=$row["S_ID"];
$E_CHEF=$row["E_CHEF"];

echo "<div align=center><table cellspacing=0 border=0>
      <tr><td>
      <font size=4><b><img src=images/".$TE_CODE."small.gif> ".$E_LIBELLE."</b></font></td></tr>
	  </table>";

if ( $id <> $E_CHEF ) {
	check_all(15);
	if (! check_rights($id, 15, "$S_ID")) check_all(24);
}

//=====================================================================
// sauver informations globales ou nouvelles
//=====================================================================
if (isset($_POST["equipe"])) {
     $query="update evenement_equipe
             set EE_NAME=\"".$EE_NAME."\",
             EE_DESCRIPTION=\"".$EE_DESCRIPTION."\"
             where E_CODE=".$evenement."
             and EE_ID=".$equipe;
    $result=mysql_query($query);
    $action='display';
}
else if (isset($_POST["EE_NAME"])) {
 	$query="select max(EE_ID) + 1 from evenement_equipe where E_CODE=".$evenement;
 	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
 	if ( $row[0] == '' ) $NEWID=1;
 	else $NEWID=$row[0];
    $query="insert into evenement_equipe(E_CODE, EE_ID, EE_NAME, EE_DESCRIPTION)
            values (".$evenement.",".$NEWID.",\"".$EE_NAME."\",\"".$EE_DESCRIPTION."\")";
    $result=mysql_query($query);
    $action='display';
}

//=====================================================================
// suppression
//=====================================================================
if ( $equipe > 0 and $action == 'delete') {
 	$query="delete from evenement_equipe 
 			where E_CODE=".$evenement."
 			and EE_ID=".$equipe;
 	$result=mysql_query($query);
 	
 	$evts=get_event_and_renforts($evenement,$exclude_canceled_r=false);
 	$query="update evenement_participation
 			set EE_ID=null
 			where E_CODE in (".$evts.")
 			and EE_ID=".$equipe;
 	$result=mysql_query($query);
 	$action='display';
}
//=====================================================================
// afficher une ou toutes les équipes
//=====================================================================

echo "<p><form action='evenement_equipes.php' method='POST'><table>
<tr>
<td class='FondMenu'>
<table cellspacing=0 border=0>";
$num=0;

//modifier
if ( $action == 'update') {
 	$querym="select EE_NAME, EE_DESCRIPTION from evenement_equipe
		where E_CODE=".$evenement."
		and EE_ID=".$equipe;
	$resultm=mysql_query($querym);
    $rowm=mysql_fetch_array($resultm);
 	$type=$rowm["EE_NAME"];
 	$desc=$rowm["EE_DESCRIPTION"];
  	echo "<input type=hidden name='evenement' value='".$evenement."'>
  		  <input type=hidden name='equipe' value='".$equipe."'>
		  <tr class=tabheader><td colspan=2>Modification</td></tr>
  	      <tr bgcolor=$mylightcolor><td width=100>Nom équipe</td><td width=200><input name=EE_NAME type=text size=20 value=\"".$type."\"></td>
	      <tr bgcolor=$mylightcolor><td>Description/Mission</td><td><textarea cols=25 rows=3 name=EE_DESCRIPTION>".$desc."</textarea></td>
 	      <tr bgcolor=$mylightcolor><td colspan=2 align=center><input type=submit name='OK' value='sauver'></td>";
}
// ajouter
else if ( $action == 'insert') {
 	echo "<input type=hidden name='evenement' value='".$evenement."'>
   	      <tr class=tabheader><td colspan=2>Ajouter</td></tr>
  	      <tr bgcolor=$mylightcolor><td width=100>Nom équipe</td><td width=200><input name=EE_NAME type=text size=20 value=''></td>
	      <tr bgcolor=$mylightcolor><td>Description/Mission</td><td><textarea cols=25 rows=3 name=EE_DESCRIPTION></textarea></td>
 	      <tr bgcolor=$mylightcolor><td colspan=2 align=center><input type=submit name='OK' value='sauver'></td>";

 
}
//lister
else if ( $action == 'display') {
 	$evts=get_event_and_renforts($evenement,$exclude_canceled_r=true);
	echo  "<tr class=tabheader>
		<td width=150>Nom</td>
		<td width=150 align=center>Description</td>
		<td width=50>Nombre</td>
		<td width=10></td>
		<td width=10></td>
		</tr>";

	$querym="select EE_ID, EE_NAME, EE_DESCRIPTION 
		from evenement_equipe
		where E_CODE=".$evenement."
		order by EE_NAME";
	$resultm=mysql_query($querym);
	$num=mysql_num_rows($resultm);
	while ( $rowm=mysql_fetch_array($resultm) ) {
 		$eeid=$rowm["EE_ID"];
 		$type=$rowm["EE_NAME"];
 		$desc=$rowm["EE_DESCRIPTION"];
 		
 		
 		$q2="select count(distinct P_ID) from evenement_participation
 			 where E_CODE in (".$evts.")
 			 and EE_ID=".$eeid;
 		$r2=mysql_query($q2);
		$row2=mysql_fetch_array($r2);
		$nb=$row2[0];
 		
    	echo "<tr bgcolor=$mylightcolor>
			<td><b>".$type."</b></td>
			<td align=center><font size=1><i>".$desc."</i></font></td>
			<td align=center><font size=1><i>".$nb."</i></font></td>
			<td><a href=evenement_equipes.php?evenement=".$evenement."&equipe=".$eeid."&action=update>
				<img src=images/smallbook.png title=\"modifier cette équipe\" border=0></a></td>
			<td><a href=evenement_equipes.php?evenement=".$evenement."&equipe=".$eeid."&action=delete>
				<img src=images/trash.png title=\"supprimer cette équipe\" border=0></a></td>
		";
	}
	echo "<tr bgcolor=$mylightcolor><td colspan=5><b>Ajouter une équipe</b>
	<a href=evenement_equipes.php?evenement=".$evenement."&equipe=0&action=insert>
	<img src=images/renfortverysmall.png height=14 title=\"Ajouter une équipe\" border=0></a></td>";
	echo "</tr>";
}
echo "</table></td></tr></table>";
$_SESSION['from']='infos';
if ( $action == 'display') {
	echo "<div align=center><p>";
	if ($equipe > 0 or isset($_POST["EE_NAME"]))
    	echo "<input type=button value='terminé' onclick=\"opener.document.location.reload();closeme();\">";
	else
    	echo "<input type=button value='annuler' onclick='closeme();'>";
	echo "</div>";
}
?>