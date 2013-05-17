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

if ( $gardes == 1 ) $title="Affectations";
else $title="compétence(s)";
if ( $nbsections == 1) $section = 0; 
else $section=$_SESSION['SES_SECTION'];

writehead();

?>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$("input").click(function(){	
		var dest;
		var Tri;
		var choixSection;
		dest='';
		for (i=0; i<$("input").length; i++) {
			if($("input")[i].checked && $("input")[i].name == 'qualif' ) {
				dest += ','+$("input")[i].value;
			}
		}			
		$("input + label").css("color", "red");
		$("input:not(:checked) + label").css("color", "black");
		
		dest = dest.substr(1,dest.length);						
		Tri = $("select#typeTri option:selected").val(); 
		choixSection = $("select#choixSection option:selected").val();
		//alert('Tri:'+Tri+'');
		if(dest!=''){
		$.post("search_personnel_result.php",{qualif:dest,typetri:Tri,section:choixSection},		
		function (data){		
			$("#export").empty();
			$("#export").html(" ").append(data);
		});
		}else{
			$("#export").empty();
		}
		
});
$("select").change(function(){
    $("select#typeTri option:selected").each(function () {
	   Tri = $(this).val();
    } );
    $("select#choixSection option:selected").each(function () {
	   choixSection = $(this).val();
    } );
	var dest;
	var Tri;
	var choixSection;
	dest='';
		
	for (i=0; i<$("input").length; i++) {
			if($("input")[i].checked && $("input")[i].name == 'qualif' ) {
				dest += ','+$("input")[i].value;
		}
	}			
	$("input + label").css("color", "red");
	$("input:not(:checked) + label").css("color", "black");
		
	dest = dest.substr(1,dest.length);					
	if(dest!=''){
	$.post("search_personnel_result.php",{qualif:dest,typetri:Tri,section:choixSection},		
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
	echo "<tr><td> section </td><td><select id='choixSection' name='choixSection' onchange=\"\">";
	  display_children2(-1, 0, $section, $nbmaxlevels,$sectionorder);
	echo "</select> 
	</td></tr>";
}
//========================
// toutes /au moins 1 / seuleument
//========================
$CurTri = (isset($_POST['typetri'])?$_POST['typetri']:"ET");
echo  "<td> avec </td>
    <td><select name=\"typeTri\" id=\"typeTri\" onchange=\"\">
	  <option value=\"et\" ".(($CurTri=="ET")?" selected":"").">Toutes les ".$title."</option>
	  <option value=\"ou\"".(($CurTri=="OU")?" selected":"").">au moins une des ".$title." sélectionnée(s)</option>
	  <option value=\"not\"".(($CurTri=="NOT")?" selected":"").">N'a pas la ".$title." sélectionnée(s)</option>
	  </select>
	</td></tr>";	
echo "</table>";

//========================
// compétences 
//========================
$sql = "select e.eq_nom, p.eq_id, p.ps_id, type, p.description 
from poste p, equipe e
where e.eq_id = p.eq_id
order by p.eq_id, p.type";
$res = mysql_query($sql) or die ("Erreur : ".mysql_error());
if (mysql_num_rows($res)>0){
		$curEq="-1";
		echo "<table border=0>\n";
		while($row=mysql_fetch_array($res)){			
			if($curEq!=$row['eq_id']){
			    if ( $curEq > 0) echo "</td></tr>\n";
				echo "\n"."<tr><td width=150><b>\n";
				echo $row['eq_nom'];
				echo "\n</b></td><td>\n";
				$curEq=$row['eq_id'];
				$nb=1;
			}
			else $nb++;
			if ( $nb%12 == 0 )echo "<br>";
			echo "\n<input type=\"checkbox\" 
			name=\"qualif\" 
			value=\"".$row['ps_id']."\" 
			id=\"cb".$row['ps_id']."\" 
			alt=\"".$row['description']."\" 
			title=\"".$row['description']."\" ".(in_array($row['ps_id'],(isset($_GET['qualif'])?array($_GET['qualif']):array()))?" checked":"")."/>
			<label 
			for=\"cb".$row['ps_id']."\" 
			title=\"".$row['description']."\"
			id=\"cb".$row['ps_id']."\" 
			>".$row['type']."</label>";
		}
		echo "</td></tr></table>\n";
}
else {
		echo "Aucune qualification";
}	


echo "<script type=\"text/javascript\"></script>
<noscript>
<input type=\"hidden\" name=\"retour\" id=\"retour\" value=\"search_personnel_poste.php\">
<input type=\"submit\">
</noscript>";
echo "</form>";
echo "</body></html>";
?>
