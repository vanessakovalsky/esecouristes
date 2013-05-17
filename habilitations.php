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

if ( isset($_GET["order"])) $order=mysql_real_escape_string($_GET["order"]);
else $order='TF_ID';

if ( isset ($_GET["from"])) $from=$_GET["from"];
else $from ='default';

// type
if (isset ($_GET["domain"])) {
   $domain= $_GET["domain"];
   if ( $domain <> -1 ) $domain=intval($domain);
   $_SESSION['domain'] = $domain;
}
else if ( isset($_SESSION['domain']) ) {
   $domain=$_SESSION['domain'];
}
else $domain=-1;
if ( $domain >= 0 ) $order='TF_ID';

// 2 possible categories: group habilitation (GP_ID < 100), role habilitation ( GP_ID >= 100)
if ( isset ($_GET["category"])) $category=$_GET["category"];
else $category='G';
if ($category <> 'R') $category='G';

$query="select count(*) as NB from fonctionnalite";
if ( $domain <> -1  ) $query .= " where TF_ID = ".$domain;
else $query .= " where TF_ID is not null";
if ( $gardes == 0  ) $query .= " and F_TYPE <> 1";

$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB=$row["NB"];
$height= 67 + $row["NB"] * 18;


if ( $domain == -1 ) $height += 8;
else if ( $domain == 0 ) $height -= 2;
$width=650;

$query="select count(*) as NB from type_fonctionnalite";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB=$row["NB"] -1;

if ($order == 'TF_ID') $height = $height + $NB;

echo "<script type='text/javascript' src='popupBoxes.js'></script>";
echo "<script>
function redirect(domain, order, category, from) {
	 url = 'habilitations.php?domain='+domain+'&order='+order+'&category='+category+'&from='+from;
	 self.location.href = url;
}
</script>";
echo "<style type='text/css'>
    <!--
    .centre
        {
            width:".$width."px;
            height: ".$height."px;
            overflow:auto;
            border: 0px;
			bgcolor: ".$mylightcolor.";			
        }
    //-->
  </style>";

echo "</head>";
echo "<body>";

?>

<script language="JavaScript">
function bouton_redirect(cible) {
	 self.location.href = cible;
}

function displaymanager(category,order,from){
	self.location.href="habilitations.php?from="+from+"order="+order+"&category="+category.value;
	return true
}

</script>
<?php
echo "<body>";

echo "<div align=center><font size=4><b>Habilitations</b></font>";

$checked1='';$checked2='';
if ( $category == 'G' ) $checked1='checked';
if ( $category == 'R' ) $checked2='checked';
echo "<form name='formf' action='habilitations.php'>";
echo "<table><tr>
      	  <td>
      	  <label for='G'>Permissions</label>
			<input type='radio' name='category' id='G' value='G' $checked1 onclick='this.form.submit();'/>
		  </td><td> 
		  <label for='R'>Rôles de l'organigramme</label>
		    <input type='radio' name='category' id='R' value='R' $checked2 onclick='this.form.submit();'/>
		  </td></tr>
		  <td colspan=2> Domaine
		  <select id='domain' name='domain' 
   			onchange=\"redirect(document.formf.domain.options[document.formf.domain.selectedIndex].value, '$order', '$category', '$from')\">";
echo "<option value='-1' selected>Tous les domaines</option>\n";
$query="select TF_ID, TF_DESCRIPTION
        from type_fonctionnalite";
if ( $gardes == 0  ) $query .= " where TF_DESCRIPTION <> 'gardes'";
$query .= "		order by TF_ID";

$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $TF_ID=$row["TF_ID"];
      $TF_DESCRIPTION=$row["TF_DESCRIPTION"];
      if ( $domain == $TF_ID ) {
      	   echo "<option value='".$TF_ID."' selected>".$TF_DESCRIPTION."</option>\n";
      }
      else {
      	   echo "<option value='".$TF_ID."'>".$TF_DESCRIPTION."</option>\n";
      }
}
echo "</select></td>";	
echo "</tr></table>";
echo "</form>";

$query1="select distinct f.F_ID , f.F_TYPE, f.F_LIBELLE, f.F_DESCRIPTION, tf.TF_ID, tf.TF_DESCRIPTION, f.F_FLAG
         from fonctionnalite f, type_fonctionnalite tf
         where f.TF_ID = tf.TF_ID";
if ( $domain <> -1  ) $query1 .= " and tf.TF_ID = ".$domain;
else $query1 .= " and tf.TF_ID is not null";
$query1 .=" order by f.".$order.",f.F_ID";
$result1=mysql_query($query1);

$query2="select GP_ID, GP_DESCRIPTION, GP_USAGE from groupe ";
if ( $category == 'R') $query2 .="where GP_ID >= 100";
else $query2 .="where GP_ID >= 0 and GP_ID < 100";
$query2 .=" order by GP_ID";
$result2=mysql_query($query2);
$nb=mysql_num_rows($result2);

if ( $nb == 0 ) echo "<b>Aucun rôle trouvé</b><p>";
else  {
 
 
 
echo "<table cellspacing=0 border=2 bordercolor=$mydarkcolor bgcolor=$mylightcolor style='border-collapse:collapse;'>";

// ===============================================
// premiere ligne du tableau de gauche
// ===============================================

echo "<tr >
	   <td>
	   	  <table cellspacing=0 border=0>
		  <tr height=50 bgcolor=$mydarkcolor>
		  		 <td nowrap><font size=1><a href=habilitations.php?category=".$category."&order=F_ID class=TabHeader>N°</a></td>
				 <td nowrap><font size=1><a href=habilitations.php?category=".$category."&order=F_LIBELLE class=TabHeader >Fonctionnalité</a></td> 
				 <td nowrap><font size=1><a href=habilitations.php?category=".$category."&order=TF_ID class=TabHeader>Catégorie</a></td>
		  </tr>";
		  
	
// ===============================================
// le corps du tableau de gauche
// ===============================================

	$i=0; $prevtf=0;
	while ($row=@mysql_fetch_array($result1)) {
    $F_ID=$row["F_ID"];
    $F_FLAG=$row["F_FLAG"];
    $TF_ID=$row["TF_ID"];
    $TF_DESCRIPTION=$row["TF_DESCRIPTION"];
    $F_DESCRIPTION=$row["F_DESCRIPTION"];
    $F_TYPE=$row["F_TYPE"];
    $F_LIBELLE=$row["F_LIBELLE"];

    if (( $gardes == 1 ) or ( $F_TYPE <> 1 )) {
	  $prevtype=$TF_ID;
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }

      if (( $prevtf <> $TF_ID) and ( $TF_ID <> 0 ) and ( $order=='TF_ID'))  {      
      		$nbcol=2*$nb;
			echo "<tr class=tabHeader height=1><td colspan=$nbcol></td></tr>";
      }
      $prevtf=$TF_ID;
      
      if (( $F_FLAG == 1 ) and ( $nbsections == 0 ))  $cmt="<font color=red><b>*</b></font>";
      else $cmt="";
      echo "<tr height=18 bgcolor=$mycolor>";
      echo "<td align=center nowrap><font size=1>".$F_ID." 
	  		<td nowrap> - <font size=1><a onmouseover=\"javascript:ReverseContentDisplay('f".$F_ID."');\" 
	  	       onmouseout=\"javascript:ReverseContentDisplay('f".$F_ID."');\" >".$F_LIBELLE."</a>";
      echo  "<div id='f".$F_ID."' 
					   style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width:400px;
					   height:90px;
					   padding: 5px;'>
				<img src=images/smallengine.png> <b>".$F_ID." - ".$F_LIBELLE."</b>
	  			<br>".$F_DESCRIPTION."
			 	</div>"; 
	  echo $cmt." <i></td><td nowrap><font size=1>".$TF_DESCRIPTION."</i></font></td>";
   	}
   }
	// plus une ligne vide au cas ou un scroller apparait sur la partie droite
	echo "<tr height=25 bgcolor=$mylightcolor><td nowrap></td></tr>
	 </table>
	</td>";

// ===============================================
// premiere ligne du tableau de droite
// ===============================================

echo "<td valign=top><div class='centre' >
	  	  <table cellspacing=0 border=0 width=$width>";

echo "<tr height=50 bgcolor=$mydarkcolor>";
    
while ($row2=@mysql_fetch_array($result2)) {
      $GP_ID=$row2["GP_ID"];
      $GP_DESCRIPTION=$row2["GP_DESCRIPTION"];
      $GP_USAGE=$row2["GP_USAGE"];
      
      if ( $GP_DESCRIPTION == "Président (e)" ) $title=$GP_DESCRIPTION." ou responsable d'antenne";
	  else if ( $GP_DESCRIPTION == "Vice président (e)" ) $title=$GP_DESCRIPTION." ou responsable adjoint d'antenne";
	  else $title="";
	  
	  if ( $GP_USAGE  == 'externes' ) $usagecolor="<font color=".$mygreencolor.">";
	  else if ( $GP_USAGE  == 'all') $usagecolor="<font color=yellow>";
	  else $usagecolor="<font color=white>";
	  
      echo "<td bgcolor=$mydarkcolor width=75 class=TabHeader>";
      if ( check_rights($_SESSION['id'], 9) ) 
      echo "<a href=upd_habilitations.php?gpid=$GP_ID class=TabHeader title=\"$title\">
	  		<div align=center><font size=1>".$usagecolor.$GP_DESCRIPTION."</font></div></td>";
      else {
       		if ( $title <> '' ) $GP_DESCRIPTION="<a class=TabHeader title=\"$title\">".$GP_DESCRIPTION."</a>";
	   		echo "<div align=center><font size=1>".$usagecolor.$GP_DESCRIPTION."</font></div></td>";
	  }
}
echo "</tr>";

// ===============================================
// le corps du tableau de droite
// ===============================================
$result1=mysql_query($query1);
$i=0; $prevtf=0;
while ($row=@mysql_fetch_array($result1)) {
    $F_ID=$row["F_ID"];
    $F_FLAG=$row["F_FLAG"];
    $TF_ID=$row["TF_ID"];
    $TF_DESCRIPTION=$row["TF_DESCRIPTION"];
    $F_DESCRIPTION=$row["F_DESCRIPTION"];
    $F_TYPE=$row["F_TYPE"];
    $F_LIBELLE=$row["F_LIBELLE"];

    if (( $gardes == 1 ) or ( $F_TYPE <> 1 )) {
	  $prevtype=$TF_ID;
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }

      if (( $prevtf <> $TF_ID) and ( $TF_ID <> 0 ) and ( $order=='TF_ID'))  {      
      		$nbcol=2*$nb;
			echo "<tr class=tabHeader height=1><td colspan=$nbcol></td></tr>";
      }
      $prevtf=$TF_ID;
      
      if (( $F_FLAG == 1 ) and ( $nbsections == 0 ))  $cmt="<font color=red><b>*</b></font>";
      else $cmt="";
      echo "<tr height=18 bgcolor=$mycolor>";
      $result2=mysql_query($query2);

      while ($row2=@mysql_fetch_array($result2)) {
      	    $GP_ID=$row2["GP_ID"];
      	    $query3="select count(1) as NB from habilitation where GP_ID=".$GP_ID." and F_ID=".$F_ID;	
	    $result3=mysql_query($query3);
	    $row3=@mysql_fetch_array($result3);
            $NB=$row3["NB"];
	    if ( $NB >= 1 ) {
	       $mypic="<img src=images/YES.gif border=0>";
	    }
            else {
	       $mypic="" ;
	    }
	    echo "<td align=center>".$mypic."</td>";
      }
      echo "</tr>";
   }
}
echo "</table>
		</div>
	  </td>
	  </tr>
	  ";
echo "</table>";


if ( check_rights($_SESSION['id'], 9)) {
   if ($category == 'G') {
   	  $query="select count(1) as NB from groupe where GP_ID < 100";
   	  $label="Ajouter un groupe";
   	  }
   else {
   	  $query="select count(1) as NB from groupe where GP_ID >=100";	
   	  $label="Ajouter un rôle";
   }
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   if ( $row["NB"] < $nbmaxgroupes )
   		echo "<p><input type='button' value='".$label."' name='ajouter' onclick=\"bouton_redirect('ins_groupe.php?category=$category');\">";
   else
   		echo "<font color=red ><b>Vous ne pouvez plus ajouter de groupes de cette catégorie( maximum atteint: $nbmaxgroupes)</b></font>";
}

}
if ( $from == 'update' )
echo " <input type=submit value='retour' onclick=\"bouton_redirect('index_d.php');\"> ";
else
echo " <input type=submit value='retour' onclick='javascript:history.back(1);'> ";

if ( $nbsections == 0 ) 
	echo "<p><font color=red><b>*</b></font><i> : ces fonctionnalités ne sont pas accessibles aux personnes habilitées seulement au niveau antenne</i>";
echo "</div>";

?>
