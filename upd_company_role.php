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
check_all(37);
$C_ID=intval($_GET["C_ID"]);
$TCR_CODE=mysql_real_escape_string($_GET["TCR_CODE"]);
if (isset($_GET["P_ID"])) $P_ID=intval($_GET["P_ID"]);
else $P_ID=-1;

$query="select S_ID from company where C_ID=".$C_ID;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$S_ID=$row["S_ID"];

if (! check_rights($_SESSION['id'], 37, $S_ID)) check_all(24);;

// current
$query3="select p.P_ID, p.P_NOM, p.P_PRENOM, p.P_SECTION
		 from company_role cr, pompier p
		 where p.P_ID = cr.P_ID
		 and cr.C_ID=".$C_ID." 
		 and cr.TCR_CODE = '".$TCR_CODE."'";
$result3=mysql_query($query3);
$row3=@mysql_fetch_array($result3);
$CURPID=$row3["P_ID"];

writehead();

?>
<script type="text/javascript">
function saveresponsable(p1,p2,p3){
	 self.location.href="upd_company_role.php?C_ID="+p1+"&TCR_CODE="+p2+"&P_ID="+p3;
	 return true
}
</script>
</head>
<?php

// ------------------------------------
// enregistrement nouveau responsable
// ------------------------------------
if ( $P_ID >= 0 ) {		
	$query="delete from company_role where C_ID=".$C_ID." and TCR_CODE='".$TCR_CODE."'";
	$result=mysql_query($query);
	if ( $P_ID > 0 ) {
			$query="insert company_role (C_ID,TCR_CODE,P_ID) 
				values (".$C_ID.",'".$TCR_CODE."',".$P_ID.")";
			$result=mysql_query($query);
	}
	echo "<body onload=\"opener.document.location.reload();window.close();\">";
}

// ------------------------------------
// choix nouveau responsable
// ------------------------------------
else {
echo "<div align=center><table cellspacing=0 border=0>
      <tr><td>
      <font size=4><b>".get_company_name("$C_ID")."</b></font></td></tr>
	  </table>";

// infos role
$query2="select TCR_DESCRIPTION, TCR_CODE from type_company_role 
		 where TCR_CODE='".$TCR_CODE."'";
$result2=mysql_query($query2);
$row2=@mysql_fetch_array($result2);
$TCR_DESCRIPTION=$row2["TCR_DESCRIPTION"];

echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	   <td width=400 class=TabHeader>Choix ".$TCR_DESCRIPTION."</td>
      </tr>";

//lisbox
echo "<tr><td bgcolor=$mylightcolor>Nom: 
		<select id='resp' name='resp'
			onchange=\"saveresponsable('".$C_ID."','".$TCR_CODE."',document.getElementById('resp').value);\">
   		<option value='0' selected >--personne--</option>\n
		<OPTGROUP LABEL='Personnel externe'>\n";

// list personnel externe
$query="select p.P_ID, p.P_PRENOM, p.P_NOM, s.S_CODE, p.P_SECTION, c.C_NAME, c.C_ID
		from pompier p, section s, company c
		where p.P_CODE <> '1234'
   		and p.P_SECTION = s.S_ID
		and p.C_ID = c.C_ID
		and P_STATUT = 'EXT'";	
if ( $S_ID <> 0 ) $query .= " and  p.P_SECTION in (".get_family("$S_ID").")";		
$query .= " order by P_NOM";

$result=mysql_query($query);

while ($row=@mysql_fetch_array($result)) {
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_ID=$row["P_ID"];
      $P_SECTION=$row["P_SECTION"];
      $PS_CODE=$row["S_CODE"];
      $C_NAME=$row["C_NAME"];
      $C_ID=$row["C_ID"];
      if ( $C_ID > 0 ) $ent="- ".$C_NAME;
      else $ent="";
      if ( $P_ID == $CURPID ) $selected='selected';
      else $selected=""; 
      	echo "<option value='".$P_ID."' $selected>".strtoupper($P_NOM)." 
		  	".ucfirst($P_PRENOM)." (".$PS_CODE." ".$ent.")</option>\n";
		  		  		  	
}

echo "<OPTGROUP LABEL='Personnel interne'>\n";
// list personnel interne
$query="select p.P_ID, p.P_PRENOM, p.P_NOM, s.S_CODE, p.P_SECTION, c.C_NAME, c.C_ID
		from pompier p, section s, company c
		where p.P_CODE <> '1234'
   		and p.P_SECTION = s.S_ID
		and p.C_ID = c.C_ID
		and P_STATUT <> 'EXT'";	
if ( $S_ID <> 0 ) $query .= " and  p.P_SECTION in (".get_family("$S_ID").")";		
$query .= " order by P_NOM";

$result=mysql_query($query);

while ($row=@mysql_fetch_array($result)) {
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_ID=$row["P_ID"];
      $P_SECTION=$row["P_SECTION"];
      $PS_CODE=$row["S_CODE"];
      $C_NAME=$row["C_NAME"];
      $C_ID=$row["C_ID"];
      if ( $C_ID > 0 ) $ent="- ".$C_NAME;
      else $ent="";
      if ( $P_ID == $CURPID ) $selected='selected';
      else $selected=""; 
      	echo "<option value='".$P_ID."' $selected>".strtoupper($P_NOM)." 
		  	".ucfirst($P_PRENOM)." (".$PS_CODE." ".$ent.")</option>\n";
		  		  		  	
}

echo "</select>";
echo "</td></tr></table>";// end left table
echo "</td></tr></table>"; // end cadre
echo "</div>";
}
?>