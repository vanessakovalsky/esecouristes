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
if (isset ($_POST["partie"])) $partie=intval($_POST["partie"]);
else $partie=intval($_GET["partie"]);

writehead();

?>
<STYLE type="text/css">
.categorie{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
</STYLE>
<script type='text/javascript' src='checkForm.js'></script>
<script type='text/javascript'>
function closeme(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}
function updateGlobal(newvalue,field)
{   
    var nv = parseInt(newvalue);
 	var cur = parseInt(field.value);
 	if ( nv > cur) field.value = nv;
    return true;
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

echo "<form name='evenement_competence' method='post' action='evenement_competences.php'>";
echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

//=====================================================================
// sauver informations globales ou nouvelles
//=====================================================================
if (isset($_POST["new_competence"])) {
    $nc=intval($_POST["new_competence"]);
    if ( $nc > 0) {
        $query="insert into evenement_competences(E_CODE, EH_ID, PS_ID, NB)
            values (".$evenement.",".$partie.",".$nc.",".intval($_POST["new_nb"]).")";
        $result=mysql_query($query);
    }
}

if (isset($_POST["global"])) {
    $query="insert into evenement_competences(E_CODE, EH_ID, PS_ID, NB)
            values (".$evenement.",".$partie.",'0',".intval($_POST["global"]).")
            where not exists (select 1 from evenement_competences t
                        where t.PS_ID=0
                        and t.E_CODE=".$evenement."
                        and t.EH_ID=".$partie.")";
    $result=mysql_query($query);
    $query="update evenement_competences set NB=".intval($_POST["global"])."
            where E_CODE=".$evenement."
            and EH_ID=".$partie."
            and PS_ID=0";
    $result=mysql_query($query);
    $query="update evenement set E_NB=(select max(NB) from evenement_competences where E_CODE=".$evenement." and PS_ID=0)
            where E_CODE=".$evenement;
    $result=mysql_query($query);
}

//=====================================================================
// afficher  compétences
//=====================================================================
echo  "<tr class=tabheader><td>Global</td><td align=center>Inscrits</td><td align=center>Demandés</td></tr>";

$querym="select ec.EH_ID, ec.PS_ID, ec.NB, p.TYPE, p.DESCRIPTION, p.EQ_ID 
		from evenement_competences ec
        left join poste p on ec.PS_ID = p.PS_ID
		where ec.E_CODE=".$evenement."
        and ec.EH_ID=".$partie."
		order by ec.EH_ID, p.EQ_ID, p.TYPE";
$resultm=mysql_query($querym);
while ( $rowm=mysql_fetch_array($resultm) ) {
 	$poste=$rowm["PS_ID"];
 	$type=$rowm["TYPE"];
 	$nb=$rowm["NB"];
 	$desc=$rowm["DESCRIPTION"];
    
    // GLOBAL
    if ( $poste == 0 ) {
     	$inscrits=get_nb_competences($evenement,$partie);
     	if ( $nb == 0 ) $pic="<img src=images/miniok.png title='Pas de limite sur le nombre de personnel inscrit'>";
     	else if ( $inscrits > $nb ) $pic="<img src=images/miniwarn.png title='Trop de personnel inscrit'>";
     	else if ( $inscrits == $nb ) $pic="<img src=images/miniok.png title='Nombre suffisant de personnel inscrit'>";
     	else $pic="<img src=images/minino.png title='Pas assez de personnel inscrit'>";
        echo  "<tr bgcolor=$mylightcolor>
        <td width=300><b>Nombre total</b></td>
        <td align=center><b>".$inscrits."</b> ".$pic."</td>
        <td align=center>
        <input name='global'
            type=text 
            title='Nombre global de personnes' 
            size=1 
            value='$nb' 
            onchange='checkNumber(form.global,\"$nb\");'
        >
       </td></tr>";

        echo  "<tr class=tabheader ><td>Détail par compétence</td><td align=center>Inscrits</td><td align=center>Demandés</td></tr>";
    }
    // DETAIL PAR COMPETENCE
    else {
      if ( isset($_POST["P".$poste])){
        $nb=intval($_POST["P".$poste]);
        if ($nb == 0) {
            $query="delete from evenement_competences
                where PS_ID=".$poste."
                and E_CODE=".$evenement."
                and EH_ID=".$partie;
            $result=mysql_query($query);
        }
        else {
            $query="insert into evenement_competences(E_CODE, EH_ID, PS_ID, NB)
                values (".$evenement.",".$partie.",'0',".$nb.")
                where not exists (select 1 from evenement_competences t
                        where t.PS_ID=".$poste."
                        and t.E_CODE=".$evenement."
                        and t.EH_ID=".$partie.")";
            $result=mysql_query($query);
            $query="update evenement_competences set NB=".$nb."
                where E_CODE=".$evenement."
                and EH_ID=".$partie."
                and PS_ID=".$poste;
            $result=mysql_query($query);
        }
      }
      if ( $nb > 0 ) {
       	  $inscrits=get_nb_competences($evenement,$partie,$poste);
       	  if ( $inscrits > $nb + 2 ) $pic="<img src=images/miniwarn.png title='Trop de personnel inscrit pour cette compétence'>";
     	  else if ( $inscrits >= $nb ) $pic="<img src=images/miniok.png title='Nombre suffisant de personnel inscrit pour cette compétence'>";
     	  else $pic="<img src=images/minino.png title='Pas assez de personnel inscrit pour cette compétence'>";
 	      echo  "<tr bgcolor=$mylightcolor><td><b>".$type." - ".$desc."</b></td>
 	      	<td align=center><b>".$inscrits."</b> ".$pic."</td>
            <td align=center>
            <input name='P".$poste."'
                type=text 
                title='Nombre requis' 
                size=1 
                value='$nb' 
                onchange='checkNumber(form.P".$poste.",\"$nb\");updateGlobal(form.P".$poste.".value,form.global);'
            >
            </td></tr>";
       }
     }
}

echo "<tr class=tabheader height=1><td colspan=3></td></tr>";
echo "<tr bgcolor=$mylightcolor><td colspan=3><i>Ajouter une compétence requise</i></td></tr>";
echo "<tr bgcolor=$mylightcolor><td colspan=2>";
echo "<select name=new_competence>";
echo "<option value='-1'>Choix compétence</option>";

$querym="select e.EQ_NOM, e.EQ_ID, p.TYPE, p.DESCRIPTION, p.PS_ID 
        from poste p, equipe e
        where e.EQ_ID=p.EQ_ID
        and e.EQ_TYPE='COMPETENCE'
        and not exists (select 1 from evenement_competences t
                        where t.PS_ID=p.PS_ID
                        and t.E_CODE=".$evenement."
                        and t.EH_ID=".$partie.")
        order by e.EQ_ID, p.TYPE";
$resultm=mysql_query($querym);
$prevEQ=0;
while ( $rowm=mysql_fetch_array($resultm) ) {
    $EQ_NOM=$rowm["EQ_NOM"];
    $EQ_ID=$rowm["EQ_ID"];
    $TYPE=$rowm["TYPE"];
    $DESCRIPTION=$rowm["DESCRIPTION"];
    $PS_ID=$rowm["PS_ID"];
    if ( $prevEQ <> $EQ_ID ){
       	echo "<option class='categorie' value='".$EQ_ID."'>".$EQ_NOM."</option>\n";
        $prevEQ =$EQ_ID;
    }
    echo "<option value='".$PS_ID."'>".$TYPE." - ".$DESCRIPTION."</option>";
}
echo "</select></td>";
echo  "<td align=center>
    <input name='new_nb'
    type=text 
    title='Nombre requis' 
    size=1 
    value='1' 
    onchange='checkNumber(form.new_nb,\"1\");updateGlobal(form.new_nb.value);'
    >
</td></tr>";

echo "</table></td></tr></table>";

echo "<div align=center><p>
<input type=hidden name='evenement' value='".$evenement."'>
<input type=hidden name='partie' value='".$partie."'>
<input type=submit value='sauver' onclick='submit();'>";
if (isset ($_POST["evenement"]))
    echo "<input type=button value='terminé' onclick=\"opener.document.location.reload();closeme();\">";
else
    echo "<input type=button value='annuler' onclick='closeme();'>";
echo "</form></div>";
?>