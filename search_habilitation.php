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
  
header('Content-Type: text/html; charset=ISO-8859-1');
echo "<html>";

include("config.php");
check_all(40);
get_session_parameters();

if ( $nbsections == 1) $section = 0; 
else $section=$_SESSION['SES_SECTION'];

writehead();

?>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">

$("input").click(function(){		
		var trouve;
		var choixSection;
		choixSection = $("select#choixSection2 option:selected").val();
		trouve='';
		for (i=0; i<$("input").length; i++) {
			if($("input")[i].checked) {
				trouve = $("input")[i].value;
			}
		}			
		$("input + label").css("color", "red");
		$("input:not(:checked) + label").css("color", "black");
								 
		if(trouve!=''){
		$.post("search_personnel_result.php",{trouve:trouve,section:choixSection,typetri:'habilitation'},		
		function (data){		
			$("#export").empty();
			$("#export").html(" ").append(data);
		});
		}else{
			$("#export").empty();
		}
		
});
$("select#choixSection2").change(function(){
    $("select#choixSection2 option:selected").each(function () {
	   choixSection = $(this).val();
    } );

	var trouve;
	var choixSection;
	trouve='';
		
	for (i=0; i<$("input").length; i++) {
			if($("input")[i].checked) {
				trouve = $("input")[i].value;
		}
	}			
	$("input + label").css("color", "red");
	$("input:not(:checked) + label").css("color", "black");
							
	if(trouve!=''){
	$.post("search_personnel_result.php",{trouve:trouve,section:choixSection,typetri:'habilitation'},		
	function (data){		
		$("#export").empty();
		$("#export").html(" ").append(data);
	});
	}else{
		$("#export").empty();
	}
});
</script>
<?php
echo "</head>";
echo "<body>";

echo "<form name=\"frmPers\" method=\"post\" action=\"search_personnel_result.php\">";

//========================
// choix section 
//========================
echo "<table border=0><tr>";
if ($nbsections <> 1 ) {
	echo "<tr><td> section </td><td><select id='choixSection2' name='choixSection2' onchange=\"\">";
	  display_children2(-1, 0, $section, $nbmaxlevels,$sectionorder);
	echo "</select> 
	</td></tr>";
}

//========================
// habilitations 
//========================
$sql1 = "select gp_id, gp_description 
       from groupe where gp_id < 100 order by gp_id ";
$res1 = mysql_query($sql1);

$sql2 = "select gp_id, gp_description 
       from groupe where gp_id >= 100 order by gp_id ";
$res2 = mysql_query($sql2);

$nb=0;
if (mysql_num_rows($res1)>0){
		echo "<table border=0 width=700>\n";
		echo "<tr><td><b>Permissions</b></td></tr>"; 
		echo "\n"."<tr><td >\n";
		while($row=mysql_fetch_array($res1)){			
			$nb++;
			if ( $nb%5 == 0 )echo "<br>";
			echo "\n <input type=\"radio\" 
			name=\"habilitation\" 
			value=\"".$row['gp_id']."\" 
			id=\"r".$row['gp_id']."\" 
			alt=\"".$row['gp_description']."\" 
			title=\"".$row['gp_description']."\" ".(in_array($row['gp_id'],(isset($_GET['habilitation'])?array($_GET['habilitation']):array()))?" checked":"")."/>
			<label for=\"r".$row['gp_id']."\">".$row['gp_description']." </label>";
		}
		echo "</td></tr></table>\n";
}
$nb=0;
if (mysql_num_rows($res2)>0){
		echo "<table border=0 width=700>\n";
		echo "<tr><td><b>Rôles de l'organigramme</b></td></tr>"; 
		echo "\n"."<tr><td >\n";
		while($row=mysql_fetch_array($res2)){			
			$nb++;
			if ( $row['gp_description'] == 'Président (e)') $gp_description = "Président / Responsable d'antenne";
			else $gp_description=$row['gp_description'];
			if ( $nb%5 == 0 )echo "<br>";
			echo "\n <input type=\"radio\" 
			name=\"habilitation\" 
			value=\"".$row['gp_id']."\" 
			id=\"r".$row['gp_id']."\" 
			alt=\"".$gp_description."\" 
			title=\"".$gp_description."\" ".(in_array($row['gp_id'],(isset($_GET['habilitation'])?array($_GET['habilitation']):array()))?" checked":"")."/>
			<label for=\"r".$row['gp_id']."\">".$gp_description." </label>";
		}
		echo "</td></tr></table>\n";
}

if ((mysql_num_rows($res1)==0) and (mysql_num_rows($res2)==0)) {
		echo "Aucune habilitation ou rôles trouvés";
}	


echo "<script type=\"text/javascript\"></script>
<noscript>
<input type=\"hidden\" name=\"retour\" id=\"retour\" value=\"search_habilitation.php\">
<input type=\"submit\">
</noscript>";
echo "</form>";
echo "</body></html>";
?>