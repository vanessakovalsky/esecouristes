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
check_all(9);
writehead();

echo "<script type='text/javascript' src='popupBoxes.js'>
</script>
</head>
<body>";

if ( isset ($_GET["category"])) $category=$_GET["category"];
else $category='G';
if ($category <> 'R') $category='G';

echo "<div align=center><font size=4><b>Ajout Groupe Utilisateur<br></b></font>";

echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<form name='habilitations' action='save_habilitations.php'>";
echo "<input type='hidden' name='GP_ID' value=''>";
echo "<input type='hidden' name='GP_DESCRIPTION' value=''>";
echo "<input type='hidden' name='sub_possible' value='0'>";
echo "<input type='hidden' name='gp_usage' value='internes'>";

//=====================================================================
// ligne 1
//=====================================================================

echo "<tr height=25 class=TabHeader>
      	   <td width=300 ></td>
      	  <td width=150 >habilitations</td>
      </tr>";

//=====================================================================
// ligne numero
//=====================================================================

if ($category == 'G') $k=0;
else $k=100;
for ($i=0 ; $i<=$nbmaxgroupes+$k ; $i++) $t[$i]=$i;

$query2="select distinct GP_ID, GP_DESCRIPTION from groupe
		 order by GP_ID";
$result2=mysql_query($query2);

while ($row2=@mysql_fetch_array($result2)) {
		 $GP_ID=$row2["GP_ID"];
		 $t[$GP_ID]=0;
}

echo "<tr height=25>
      	  <td bgcolor=$mylightcolor width=150><b>Numéro</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
		  <select name='GP_ID'>";
		     for ($i=$k+1 ; $i<=$nbmaxgroupes+$k ; $i++) {
		     	  if ($t[$i] <> 0) {
				  	if ($i == $GP_ID) $selected="selected";
				  	else $selected="";
		          	echo "<option value='$i'>$i</option>";
	     	     }
	    	}
 	        echo "</select>";
echo "</tr>";


//=====================================================================
// ligne description
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor width=300 ><b>Nom du groupe</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left><input type='text' name='GP_DESCRIPTION' size='25' value=''>";		
echo "</tr>";

if ( $category == 'R' ) {
	echo "<tr>
      	  <td bgcolor=$mylightcolor width=300 align=left><b>Membre d'une sous-section possible</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<input type='checkbox' name='sub_possible'  value='1'  title=\"Si cette case est cochée, alors un membre d'une sous-section peut avoir le rôle\">
		  </td>";		
	echo "</tr>";
}
else {
	// attribuable à certaines catégories de personnel seulement
	echo "<tr>
      	  <td bgcolor=$mylightcolor width=300 align=left><b>Utilisable pour le personnel</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<select name='gp_usage'>
			<option value='internes' style='background:white;' >interne seulement</option>
			<option value='externes' style='background:".$mygreencolor.";'>externe seulement</option>
			<option value='all' style='background:yellow;' >interne et externe</option>
			</select>
		  </td>";		
	echo "</tr>";
}
echo "<tr height=2>
      	   <td bgcolor=$mydarkcolor width=300 ></td>
      	  <td bgcolor=$mydarkcolor width=150 ></td>
      </tr>";
      

//=====================================================================
// ligne fonctionnalités
//=====================================================================
$query="select  F_ID, F_TYPE, F_LIBELLE , F_DESCRIPTION
 	    from fonctionnalite order by F_ID";	
$result=mysql_query($query);

while ($row=@mysql_fetch_array($result)) {
      $F_ID=$row["F_ID"];
      $F_TYPE=$row["F_TYPE"];
      $F_LIBELLE=$row["F_LIBELLE"];
      $F_DESCRIPTION=$row["F_DESCRIPTION"];
	if ( $F_ID == 0 ) {
		$checked="checked";
		$disabled="disabled";
	}
	else {
		$checked="";
		$disabled="";
	}
	if (( $gardes == 1 ) or ( $F_TYPE <> 1 )) {
		echo "<tr>
      	  <td bgcolor=$mylightcolor width=300>$F_ID - ";
		
	    echo "<a onmouseover=\"javascript:ReverseContentDisplay('f".$F_ID."');\" 
				 onmouseout=\"javascript:ReverseContentDisplay('f".$F_ID."');\">".$F_LIBELLE."</a>";
      		echo  "<div id='f".$F_ID."' 
					   style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width:300px;
					   height:100px;
					   padding: 5px;'>
				<img src=images/smallengine.png><font size=1><b>".$F_ID." - ".$F_LIBELLE."</b>
	  			<br>".$F_DESCRIPTION."
		  		<div align=center><a onmouseover=\"HideContent('f".$F_ID."'); return true;\"
   					href=\"javascript:HideContent('f".$F_ID."')\"><i>fermer</i></a>
   				</div></font>
			 	</div>"; 
			
		echo "</td>
      	  <td bgcolor=$mylightcolor width=150 align=left><input type='checkbox' name='$F_ID' value='1' $checked 
			$disabled >";		
		echo "</tr>";
    }
}

echo "</table>";
echo "</td></tr></table>";
echo "<p><input type='submit' value='sauver'></form>";
echo "<input type='button' value='Annuler' name='annuler' onclick=\"javascript:history.back(1);\"></div>";

?>
