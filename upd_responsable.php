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
$S_ID=intval($_GET["S_ID"]);
$GP_ID=intval($_GET["GP_ID"]);
if (isset($_GET["P_ID"])) $P_ID=intval($_GET["P_ID"]);
else $P_ID=-1;

if ( check_rights($_SESSION['id'], 24)) $mysection='0';
else $mysection=$_SESSION['SES_SECTION'];

$disabled="disabled";
if ( check_rights($_SESSION['id'], 22, "$S_ID"))
$disabled="";

// current
$query3="select p.P_ID, p.P_NOM, p.P_PRENOM, p.P_SECTION
		 from section_role sr, pompier p
		 where p.P_ID = sr.P_ID
		 and sr.S_ID=".$S_ID." 
		 and sr.GP_ID = ".$GP_ID;
$result3=mysql_query($query3);
$row3=@mysql_fetch_array($result3);
$CURPID=$row3["P_ID"];

if ( $GP_ID == 107 ) {
   	$disabled='disabled';
	// cas particulier type cadre de permanence, modifiable par le cadre de permanence actuel ou par 
	// une personne habilitée 26
	// ce responsable peut etre membre d'une sous-section
	if ( check_rights($_SESSION['id'], 26, "$S_ID")) $disabled="";
}

if ( $disabled == 'disabled' ) 
	check_all(22);

writehead();

?>
<script type="text/javascript">
function saveresponsable(p1,p2,p3){
	 self.location.href="upd_responsable.php?S_ID="+p1+"&GP_ID="+p2+"&P_ID="+p3;
	 return true
}
</script>
</head>
<?php

// ------------------------------------
// enregistrement nouveau responsable
// ------------------------------------
if ( $P_ID >= 0 ) {
	if ( $disabled == "" ) {
		$previous_cadre=get_cadre("$S_ID");
		
		$query="delete from section_role where S_ID=".$S_ID." and GP_ID=".$GP_ID;
		$result=mysql_query($query);
		if ( $P_ID > 0 ) {
			$query="insert section_role (S_ID,GP_ID,P_ID) 
				values (".$S_ID.",".$GP_ID.",".$P_ID.")";
			$result=mysql_query($query);
		}
		$cadre=get_cadre("$S_ID");
		
		// changement de cadre de permanence: notifier les personnes.
		if (empty($previous_cadre)) $p="personne";
		else $p=my_ucfirst(get_prenom("$previous_cadre"))." ".strtoupper(get_nom("$previous_cadre"));
		if (empty($cadre)) $n="personne";
		else $n=my_ucfirst(get_prenom("$cadre"))." ".strtoupper(get_nom("$cadre"));
		if ( "$p" <> "$n") {
	 		$destid=get_granted(21,"$S_ID",'parent','yes').",".$previous_cadre.",".$cadre;
	 		$sname=get_section_name("$S_ID");
	 		$subject="cadre de permanence - ".$sname;
			$message = "Bonjour,\n
			Pour information, le cadre de permanence a changé\npour : ".$sname."\n";
			$message .= "le ".date("d/m/Y")." à ".date("H:i")."\n\n";
			$message .= "Jusqu'ici c'était: ".$p."\n";
			$message .= "maintenant c'est: ".$n."\n";
		
			$nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );
		
			$query3="select S_EMAIL
		 	from section
		 	where S_ID=".$S_ID; 
			$result3=mysql_query($query3);
			$row3=@mysql_fetch_array($result3);
			if ( $row3["S_EMAIL"] <> "" ) 
				$nb2 = mysendmail2( $row3["S_EMAIL"] , $_SESSION['id'] , "$subject" , "$message" );
			
		}
		echo "<body onload=\"opener.document.location='upd_section.php?S_ID=$S_ID&status=responsables';window.close();\">";
	}
	else check_all(24);
}

// ------------------------------------
// choix nouveau responsable
// ------------------------------------
else {
echo "<div align=center><table cellspacing=0 border=0>
      <tr><td>
      <font size=4><b>".get_section_code("$S_ID")." - ".get_section_name("$S_ID")."</b></font></td></tr>
	  </table>";

// infos role
$query2="select GP_DESCRIPTION, TR_SUB_POSSIBLE from groupe 
		 where GP_ID=".$GP_ID;
$result2=mysql_query($query2);
$row2=@mysql_fetch_array($result2);
$GP_DESCRIPTION=$row2["GP_DESCRIPTION"];
$TR_SUB_POSSIBLE=$row2["TR_SUB_POSSIBLE"];  

// list personnel
if ( $TR_SUB_POSSIBLE == 1 ) {
	$query="select p.P_ID, p.P_PRENOM, p.P_NOM, s.S_CODE, p.P_SECTION
		from pompier p, section s
		where p.P_CODE <> '1234'
		and p.P_STATUT <> 'EXT'
		and p.P_OLD_MEMBER = 0
   		and p.P_SECTION = s.S_ID";	
	if ( $S_ID <> 0 ) $query .= " and  p.P_SECTION in (".get_family("$S_ID").")";	
	$query .= " union select p.P_ID, p.P_PRENOM, p.P_NOM, s.S_CODE, p.P_SECTION
		from pompier p, section_role sr, section s
		where sr.S_ID=".$S_ID."
   		and p.P_SECTION = s.S_ID 
		and sr.P_ID = p.P_ID
		and p.P_CODE <> '1234'
		and p.P_STATUT <> 'EXT'
		and p.P_OLD_MEMBER = 0";	
	$query .= " order by P_NOM";
}
else {
	$query="select p.P_ID, p.P_PRENOM, p.P_NOM, s.S_CODE, p.P_SECTION
		from pompier p, section s
   		where p.P_SECTION = ".$S_ID."
   		and p.P_SECTION = s.S_ID
   		and p.P_CODE <> '1234'	
		union select p.P_ID, p.P_PRENOM, p.P_NOM, s.S_CODE, p.P_SECTION
		from pompier p, section_role sr, section s
		where sr.S_ID=".$S_ID." 
		and sr.P_ID = p.P_ID
   		and p.P_SECTION = s.S_ID
		and p.P_CODE <> '1234'
		and p.P_STATUT <> 'EXT'
		and p.P_OLD_MEMBER = 0
		order by P_NOM";
}

echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	   <td width=400 class=TabHeader>Choix ".$GP_DESCRIPTION."</td>
      </tr>";

//lisbox
echo "<tr><td bgcolor=$mylightcolor>Nom: 
		<select id='resp' name='resp' $disabled 
			onchange=\"saveresponsable(".$S_ID.",".$GP_ID.",document.getElementById('resp').value);\">
   		<option value='0' selected >--personne--</option>\n";

$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_ID=$row["P_ID"];
      $P_SECTION=$row["P_SECTION"];
      $PS_CODE=$row["S_CODE"];
      if ( $P_ID == $CURPID ) $selected='selected';
      else $selected="";
      if (($TR_SUB_POSSIBLE == 1 ) or ( $P_ID == $CURPID ) or ($P_SECTION == $S_ID)) 
      	echo "<option value='".$P_ID."' $selected>".strtoupper($P_NOM)." 
		  	".my_ucfirst($P_PRENOM)." (".$PS_CODE.")</option>\n";
		  	
		  		  	
}
echo "</select>";
echo "</td></tr></table>";// end left table
echo "</td></tr></table>"; // end cadre
echo "</div>";
}
?>