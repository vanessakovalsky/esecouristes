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
check_all(18);
get_session_parameters();
$possibleorders= array('EQ_ID','PS_ID','TYPE','DESCRIPTION','PO_JOUR','PO_NUIT','PS_EXPIRABLE',
						'PS_AUDIT','PS_DIPLOMA','PS_SECOURISME','PS_NATIONAL','PS_PRINTABLE',
						'PS_RECYCLE','PS_USER_MODIFIABLE','F_LIBELLE','EQ_TYPE');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='EQ_ID';
writehead();
?>

<script language="JavaScript">
function orderfilter(p1,p2){
	 self.location.href="poste.php?order="+p1+"&typequalif="+p2;
	 return true
}

function displaymanager(p1){
	 self.location.href="upd_poste.php?pid="+p1;
	 return true
}

function bouton_redirect(cible) {
	 self.location.href = cible;
}

</script>
<?php

echo "<body>";

if ( $typequalif == 'ALL' and $gardes == 0 ) $MEQ_TYPE='COMPETENCE';
else if ( $typequalif == 'ALL' ) $MEQ_TYPE='ALL';
else {
	$query1="select EQ_TYPE from equipe where EQ_ID='".$typequalif."'";
	$result1=mysql_query($query1);
	$row1=@mysql_fetch_array($result1);
	$MEQ_TYPE=$row1["EQ_TYPE"];
}

$query1="select p.PS_ID, p.EQ_ID, p.TYPE, p.DESCRIPTION, p.PO_JOUR, p.PO_NUIT, e.EQ_TYPE,
		 e.EQ_NOM, e.EQ_JOUR, e.EQ_NUIT, p.PS_EXPIRABLE, p.PS_AUDIT, p.PS_DIPLOMA, p.F_ID,
		 p.PS_RECYCLE, p.PS_USER_MODIFIABLE, p.PS_PRINTABLE, p.PS_NATIONAL, p.PS_SECOURISME,
		 case
	        when f.F_ID = 4 then 'zzz'
	        else f.F_LIBELLE
	     end
	     as F_LIBELLE
	     from equipe e, poste p, fonctionnalite f
	     where p.EQ_ID=e.EQ_ID
		 and p.F_ID = f.F_ID";

if ( $typequalif <> 'ALL' ) $query1 .= "\nand p.EQ_ID='".$typequalif."'";
$query1 .="\norder by ". $order;
if ( $order == 'PS_EXPIRABLE' || $order == 'PS_AUDIT' 
	|| $order == 'PS_DIPLOMA'
	|| $order == 'PS_RECYCLE' || $order == 'PS_USER_MODIFIABLE'
	|| $order == 'PS_PRINTABLE' || $order == 'PS_NATIONAL'
	|| $order == 'PS_SECOURISME' ) 
$query1 .= " desc";

$result1=mysql_query($query1);
$number=mysql_num_rows($result1);

echo "<div align=center><font size=4><b>Paramétrage des Compétences</b></font><i> (".$number ." trouvées)</i>";
echo "<p><table cellspacing=0 border=0 >";
echo "<tr>";
echo "<td width=250><select id='typequalif' name='typequalif' onchange=\"orderfilter('".$order."',document.getElementById('typequalif').value)\">
	  <option value='ALL'>toutes types</option>";


$query2="select distinct EQ_ID, EQ_NOM from equipe";
$result2=mysql_query($query2);
while ($row=@mysql_fetch_array($result2)) {
      $EQ_ID=$row["EQ_ID"];
      $EQ_NOM=$row["EQ_NOM"];
      echo "<option value='".$EQ_ID."'";
      if ($EQ_ID == $typequalif ) echo " selected ";
      echo ">".$EQ_NOM."</option>\n";
}
echo "</select></td> ";
if ( check_rights($_SESSION['id'], 18) ) {
   $query="select count(1) as NB from poste";	
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   if ( $row["NB"] < $nbmaxpostes )
   		echo "<td><input type='button' value='Ajouter' name='ajouter' onclick=\"bouton_redirect('ins_poste.php');\"></td>";
   else
   		echo "<td><font color=red><b>Vous ne pouvez plus ajouter de $title ( maximum atteint: $nbmaxpostes)</b></font></td>";
}

echo "</tr><tr><td colspan=2>";
// ====================================
// pagination
// ====================================
require_once('paginator.class.php');
$pages = new Paginator;  
$pages->items_total = $number;  
$pages->mid_range = 9;  
$pages->paginate();  
if ( $number > 10 ) {
	echo $pages->display_pages();
	echo $pages->display_jump_menu(); 
	echo $pages->display_items_per_page(); 
	$query1 .= $pages->limit;
}
$result1=mysql_query($query1);
echo "</td></tr></table>";
echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr height=10 class=TabHeader>
      	  <td width=200><a href=poste.php?order=EQ_ID class=TabHeader>Type</a></td>
      	  <td width=0></td>
      	  <td width=30><a href=poste.php?order=PS_ID class=TabHeader>N°</a></td>
      	  <td width=0></td>
      	  <td width=50><a href=poste.php?order=TYPE class=TabHeader>Code</a></td>
      	  <td width=0></td>
      	  <td width=240><a href=poste.php?order=DESCRIPTION class=TabHeader>Description</a></td>
      	  ";
if ( $MEQ_TYPE == 'GARDE' )      	  
echo "  <td width=0></td>  
		<td width=30 >
			<a href=poste.php?order=PO_JOUR class=TabHeader>Jour</a></td>
      	<td width=0></td>
      	<td width=30>
		  <a href=poste.php?order=PO_NUIT class=TabHeader>Nuit</a></td>";
else if ( $MEQ_TYPE == 'COMPETENCE' ) 
echo "  <td width=0></td>  
		<td width=30 align=center>
			<a href=poste.php?order=PS_EXPIRABLE class=TabHeader title=\"On peut définir une date d'expiration sur cette compétence\">Exp.</a></td>
		<td width=0></td>  
		<td width=30 align=center>
			<a href=poste.php?order=PS_AUDIT class=TabHeader title='Un mail est envoyé au secrétariat en cas de modification'>Audit</a></td>
		<td width=0></td>  
		<td width=30 align=center>
			<a href=poste.php?order=PS_DIPLOMA class=TabHeader title='Un diplôme est délivré après formation' >Diplôme</a></td>
		<td width=0></td>
		<td width=30 align=center>
			<a href=poste.php?order=PS_SECOURISME class=TabHeader title='Compétence officielle de secourisme' >Secourisme</a></td>
		<td width=0></td>  
		<td width=30 align=center>
			<a href=poste.php?order=PS_NATIONAL class=TabHeader title='Le diplôme est délivré au niveau national seulement' >National</a></td>
		<td width=0></td>  
		<td width=30 align=center>
			<a href=poste.php?order=PS_PRINTABLE class=TabHeader title=\"Possibilité d'imprimer un diplôme\">Print.</a></td>
		<td width=0></td>  
		<td width=30 align=center>
			<a href=poste.php?order=PS_RECYCLE class=TabHeader title='Recyclage ou formation continue nécessaire'>Recycl.</a></td>
		<td width=0></td>  
		<td width=30 align=center>
			<a href=poste.php?order=PS_USER_MODIFIABLE class=TabHeader title='Modifiable par chaque utilisateur'>Modif.</a></td>
		<td width=0></td>  
		<td width=30 align=center>
			<a href=poste.php?order=F_LIBELLE class=TabHeader title='Permission spéciale requise pour modifier cette compétence'>Perm.</a></td>
";
else if (( $MEQ_TYPE == 'ALL' ) and ( $gardes == 1 )) 
echo "  <td width=0></td>  
		<td width=100 align=center>
			<a href=poste.php?order=EQ_TYPE class=TabHeader>Type</a></td>";
echo "</tr>";

// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result1)) {
      $PS_ID=$row["PS_ID"];
      $EQ_ID=$row["EQ_ID"];
      $EQ_JOUR=$row["EQ_JOUR"];
      $EQ_NUIT=$row["EQ_NUIT"];
      $EQ_TYPE=$row["EQ_TYPE"];
      $TYPE=$row["TYPE"];
      $DESCRIPTION=strip_tags($row["DESCRIPTION"]);
      $PO_JOUR=$row["PO_JOUR"];
      $PO_NUIT=$row["PO_NUIT"];
      $EQ_NOM=$row["EQ_NOM"];
      $PS_EXPIRABLE=$row["PS_EXPIRABLE"];
	  $F_ID=$row["F_ID"];
	  $F_LIBELLE=$row["F_LIBELLE"];
      $PS_AUDIT=$row["PS_AUDIT"];
      $PS_DIPLOMA=$row["PS_DIPLOMA"];
      $PS_NATIONAL=$row["PS_NATIONAL"];
      $PS_SECOURISME=$row["PS_SECOURISME"];
      $PS_RECYCLE=$row["PS_RECYCLE"];
      $PS_PRINTABLE=$row["PS_PRINTABLE"];
      $PS_USER_MODIFIABLE=$row["PS_USER_MODIFIABLE"];
      
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      if (( $PO_JOUR == 1) and ( $EQ_JOUR == 1)) $jour="<img src=images/green.gif>";
      else $jour="<img src=images/red.gif>";
      if (( $PO_NUIT == 1) and ( $EQ_NUIT == 1)) $nuit="<img src=images/green.gif>";
      else $nuit="<img src=images/red.gif>";
      if ( $PS_EXPIRABLE == 1 ) $expirable="<img src=images/YES.gif 
	  title = 'Expiration possible'>";
      else $expirable="";
      if ( $PS_AUDIT == 1 ) $audit="<img src=images/YES.gif 
	  title = 'Alerter si modifications'>";
      else $audit="";
      if ( $PS_DIPLOMA == 1 ) $diploma="<img src=images/YES.gif 
	  title = 'Diplôme délivré après une formation'>";
      else $diploma="";
      if ( $PS_SECOURISME == 1 ) $secourisme="<img src=images/YES.gif 
	  title = 'Compétence officielle de secourisme'>";
      else $secourisme="";
      if ( $PS_NATIONAL == 1 ) $national="<img src=images/YES.gif 
	  title = 'Diplôme délivré au niveau national seulement'>";
      else $national="";
      if ( $PS_RECYCLE == 1 ) $recycle="<img src=images/YES.gif 
	  title = 'Un recyclage périodique est nécessaire'>";
      else $recycle="";
      if ( $PS_USER_MODIFIABLE == 1 ) $modifiable="<img src=images/YES.gif 
	  title = 'Modifiable par chaque utilisateur'";
      else $modifiable="";
      if ( $PS_PRINTABLE == 1 ) $printable="<img src=images/YES.gif 
	  title = 'Possibilité d''imprimer un diplôme'";
      else $printable="";
	  if ( $F_ID <> 4 ) $permission="<img src=images/YES.gif 
	  title = \"Permission '$F_ID - $F_LIBELLE' requise pour modifier cette compétence\"> $F_ID";
      else $permission="";
      
echo "<tr bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$mycolor'\" onclick=\"this.bgColor='#33FF00'; displaymanager($PS_ID)\" >
      	  <td>$EQ_NOM</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center>$PS_ID</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td>$TYPE</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td >$DESCRIPTION</font></td>
      	  ";
if ( $MEQ_TYPE == 'GARDE' ) 
echo "    <td bgcolor=$mydarkcolor width=0></td> 
		  <td align=center><B>$jour</B></font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><B>$nuit</B></font></td>
      	  ";
else if ( $MEQ_TYPE == 'COMPETENCE' ) 
echo "    <td bgcolor=$mydarkcolor width=0></td> 
		  <td align=center>$expirable</font></td>
		  <td bgcolor=$mydarkcolor width=0></td> 
		  <td align=center>$audit</font></td>
		  <td bgcolor=$mydarkcolor width=0></td> 
		  <td align=center>$diploma</font></td>
		  <td bgcolor=$mydarkcolor width=0></td>
		  <td align=center>$secourisme</font></td>
		  <td bgcolor=$mydarkcolor width=0></td> 
		  <td align=center>$national</font></td>
		  <td bgcolor=$mydarkcolor width=0></td> 
		  <td align=center>$printable</font></td>
		  <td bgcolor=$mydarkcolor width=0></td>
		  <td align=center>$recycle</font></td>
		  <td bgcolor=$mydarkcolor width=0></td> 
		  <td align=center>$modifiable</font></td>
		  <td bgcolor=$mydarkcolor width=0></td> 
		  <td align=center>$permission</font></td>";
else if (( $MEQ_TYPE == 'ALL' ) and ( $gardes == 1 )) 
echo "  <td bgcolor=$mydarkcolor width=0></td>
		<td align=center>".$EQ_TYPE."</td>";
echo "</tr>";
      
}

// ===============================================
// le bas du tableau
// ===============================================
echo "</table>";  
echo "</td></tr></table>";  
?>
