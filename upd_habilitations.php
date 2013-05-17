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

if ( isset($_GET["order"])) $order=$_GET["order"];
else $order='TF_ID';

// check input parameters
if ( $order <> mysql_real_escape_string($order)){
	param_error_msg();
	exit;
}

?>

<script type='text/javascript' src='popupBoxes.js'></script>
<script>

function order(p1,p2){
	 self.location.href="upd_habilitations.php?gpid="+p1+"order="+p2;
	 return true;
}

function suppr_groupe(groupe) {
    if (groupe < 100 )
      msg="Attention : vous allez supprimer ce groupe.\nLes membres de ce groupe seront réaffectés\ndans le groupe public.\nVoulez vous continuer ?"
    else msg="Attention : vous allez supprimer un type de rôle dans l'organigramme.\nLes personnes qui ont ce rôles perdront leur titres et les habilitations correspondantes.\nVoulez vous vraiment continuer ?"
    if ( confirm (msg )){
     	cible = "del_groupe.php?GP_ID=" + groupe;
     	self.location.href = cible;
    }
}

var fenetreDetail=null;
function voirDetail(groupe){
	 url="membres.php?groupe="+groupe;
	 fenetre=window.open(url,'Note','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,' + 'width=500' + ',height=550');
	 fenetreDetail = fenetre;
	 return true
	 //self.location.href = url;
}

function fermerDetail() {
	 if (fenetreDetail) {
	    fenetreDetail.close( );
	    fenetreDetail = null;
         }
}

function redirect(category) {
     url="habilitations.php?from=update&category="+category;
     self.location.href=url;
}

</script>
</head>

<?php

$GP_ID=$_GET["gpid"];

//=====================================================================
// affiche la fiche groupe
//=====================================================================

$query="select GP_DESCRIPTION, TR_SUB_POSSIBLE, GP_USAGE
 	    from groupe where GP_ID=".$GP_ID;	
$result=mysql_query($query);
$row=mysql_fetch_array($result);

$GP_DESCRIPTION=$row["GP_DESCRIPTION"];
$TR_SUB_POSSIBLE=$row["TR_SUB_POSSIBLE"];
$GP_USAGE=$row["GP_USAGE"];

if ( $GP_ID < 100 ) 
echo "<div align=center><font size=4><b>Groupe utilisateur n° $GP_ID - $GP_DESCRIPTION<br></b></font>";
else 
echo "<div align=center><font size=4><b>Rôle de l'organigramme - $GP_DESCRIPTION<br></b></font>";

echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<form name='habilitations' action='save_habilitations.php'>";
echo "<input type='hidden' name='GP_ID' value='$GP_ID'>";
echo "<input type='hidden' name='GP_DESCRIPTION' value=\"$GP_DESCRIPTION\">";
echo "<input type='hidden' name='sub_possible' value='0'>";
echo "<input type='hidden' name='gp_usage' value=\"$GP_USAGE\">";

//=====================================================================
// ligne 1
//=====================================================================

echo "<tr>
      	<td bgcolor=$mydarkcolor width=520 align=right colspan=4 class=TabHeader>habilitations</td>
      </tr>";


//=====================================================================
// ligne description
//=====================================================================
$disabled="";
if ($GP_ID == 4) $disabled="disabled";
 
echo "<tr>
		  <td bgcolor=$mylightcolor width=320 colspan=3><b>Nom du groupe</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>";
	echo"<input type='text' name='GP_DESCRIPTION' size='25' value=\"$GP_DESCRIPTION\" $disabled>";		
echo "</tr>";

if ( $GP_ID >= 100 ) {
 	if ( $TR_SUB_POSSIBLE == 1 ) $checked="checked";
	else $checked="";
	echo "<tr>
      	  <td bgcolor=$mylightcolor width=320 colspan=3 ><b>Membre d'une sous-section possible</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<input type='checkbox' name='sub_possible'  value='1' $checked title=\"Si cette case est cochée, alors un membre d'une sous-section peut avoir le rôle\">
		  </td>";		
	echo "</tr>";
}
else {
	// attribuable à certaines catégories de personnel seulement
	echo "<tr>
      	  <td bgcolor=$mylightcolor width=320 colspan=3><b>Utilisable pour le personnel</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<select name='gp_usage'>";
	if ( $GP_USAGE == 'internes') $selected ='selected'; else $selected='';
	echo 	"<option value='internes' style='background:white;' $selected>interne seulement</option>";
	if ( $GP_USAGE == 'externes') $selected ='selected'; else $selected='';
	echo	"<option value='externes' style='background:".$mygreencolor.";' $selected>externe seulement</option>";
	if ( $GP_USAGE == 'all') $selected ='selected'; else $selected='';
	echo	"<option value='all' style='background:yellow;' $selected>interne et externe</option>";
	echo "		</select>
		  </td>";		
	echo "</tr>";
}

//=====================================================================
// nombre de membres
//=====================================================================

if ( $GP_ID >= 100 ) 
$query="select count(*) as NB
	    from pompier p , section s, section_role  sr
	    where sr.S_ID= s.S_ID
	    and sr.GP_ID=".$GP_ID."
	    and sr.P_ID = p.P_ID";
else 
$query="select count(*) as NB from pompier where GP_ID=$GP_ID or GP_ID2=$GP_ID";	
$result=mysql_query($query);     
$row=@mysql_fetch_array($result);
$NB=$row["NB"];
	
echo "<tr>
      	  <td bgcolor=$mylightcolor width=320 colspan=3><b>Nombre de membres</b></td>
      	  <td bgcolor=$mylightcolor width=150 align=left>
			<input type='button' value='$NB' title='cliquer pour voir la liste du personnel' onclick=\"fermerDetail(); voirDetail('".$GP_ID."');\">";		
echo "</tr>";
      
//=====================================================================
// ligne fonctionnalités
//=====================================================================
$query="select distinct f.F_ID , f.F_TYPE, f.F_LIBELLE, tf.TF_ID, tf.TF_DESCRIPTION, f.F_FLAG,f.F_DESCRIPTION
         from fonctionnalite f, type_fonctionnalite tf
         where f.TF_ID = tf.TF_ID
	 order by ".$order.",F_ID";	
$result=mysql_query($query);

echo "<tr>
      	<td bgcolor=$mydarkcolor width=20 align=left><a href=upd_habilitations.php?gpid=".$GP_ID."&order=F_ID class=TabHeader>N°</a></td>
      	<td bgcolor=$mydarkcolor width=250 align=left><a href=upd_habilitations.php?gpid=".$GP_ID."&order=F_LIBELLE class=TabHeader>Fonctionnalité</a></td>
      	<td bgcolor=$mydarkcolor width=100 align=left><a href=upd_habilitations.php?gpid=".$GP_ID."&order=TF_ID class=TabHeader>Catégorie</a></td>
      	<td bgcolor=$mydarkcolor width=150 align=left class=TabHeader>Permission</td>
      </tr>";


$i=0;$prevtype=0;
while ($row=@mysql_fetch_array($result)) {
    $F_ID=$row["F_ID"];
    $TF_ID=$row["TF_ID"];
    $F_FLAG=$row["F_FLAG"];
    $TF_DESCRIPTION=$row["TF_DESCRIPTION"];
    $F_DESCRIPTION=$row["F_DESCRIPTION"];
    $F_TYPE=$row["F_TYPE"];
    $F_LIBELLE=$row["F_LIBELLE"];
    
      if (( $gardes == 1 ) or ( $F_TYPE <> 1 )) {
			$query2="select count(*) as NB from habilitation where F_ID=$F_ID and GP_ID=$GP_ID";	
			$result2=mysql_query($query2);     
			$row2=@mysql_fetch_array($result2);
			$NB=$row2["NB"];
			if ( $NB > 0 ) $checked="checked";
			else $checked="";
	
			if (( $prevtype <> $TF_ID) and ( $TF_ID <> 0 ) and ( $order=='TF_ID'))  {      
				echo "<tr class=tabHeader height=2><td colspan=4></td></tr>";
      		}
      		$prevtype=$TF_ID;
	  
	  		if (( $F_FLAG == 1 ) and ( $nbsections == 0 ))  $cmt="<font color=red><b>*</b></font>";
      		else $cmt="";
      		
			$disabled="";
			if ($GP_ID == 4){
	 			if (($F_ID == 9) and ( $NB > 0)) $disabled="disabled";
			}
			if  (($F_ID == 0) and ( $NB > 0)) $disabled="disabled";
			if ($GP_ID == -1)  $disabled="disabled";
			echo "<tr>
				<td bgcolor=$mylightcolor width=20 align=right>$F_ID</td>
				<td bgcolor=$mylightcolor width=250>- ";	
      	  		
      	  	echo "<a onmouseover=\"javascript:ReverseContentDisplay('f".$F_ID."');\" 
					 onmouseout=\"javascript:ReverseContentDisplay('f".$F_ID."');\">".$F_LIBELLE."</a> ".$cmt;
      		echo  "<div id='f".$F_ID."' 
					   style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width:300px;
					   height:90px;
					   padding: 5px;'>
				<img src=images/smallengine.png><font size=1><b>".$F_ID." - ".$F_LIBELLE."</b>
	  			<br>".$F_DESCRIPTION."
			 	</div>"; 
      	  		
			echo "</td><td bgcolor=$mylightcolor width=100><font size=1><i>$TF_DESCRIPTION</i></font></td>
      	  		<td bgcolor=$mylightcolor width=150 align=left><input type='checkbox' name='$F_ID'  value='1' $checked $disabled>";		
			echo "</tr>";
    }
}

//=====================================================================
// bas de tableau
//=====================================================================
echo "</table>";
echo "</td></tr></table>";
if ( check_rights($_SESSION['id'], 9)) {
   echo "<p><input type='submit' value='sauver' onclick=\"fermerDetail()\"> ";
}
echo "</form>";
if ( check_rights($_SESSION['id'], 9)) {
   // on ne peut pas supprimer les groupes admin, public et acces interdit
   if (( $GP_ID <> 4 ) and ( $GP_ID > 0 )) 
      echo "<input type='button' value='supprimer'
          onclick=\"fermerDetail();suppr_groupe('".$GP_ID."')\">";
}
if ( $GP_ID >= 100 ) $category = 'R';
else $category = 'G';

echo "<input type='button' value='Retour' name='annuler' 
      onclick=\"redirect('".$category."');fermerDetail();\">";

if ( $nbsections == 0 ) 
	echo "<p><font color=red><b>*</b></font><i> : ces fonctionnalités ne sont pas accessibles aux personnes habilitées seulement au niveau antenne</i>";	  
	  
echo "</div>";

?>
