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
$id=$_SESSION['id'];
$section=$_SESSION['SES_SECTION'];

// affichage pompier
if (date('n') == 12 and date('j') > 15 ) {
	$img='xmas.png';
	$msg='Joyeux Noël';
}
else if (date('n') == 1 and date('j') < 20 ) {
	$img='happy-new-year.png';
	$msg='Bonne année '.date('Y').',';
}
else {
	$img='keditbookmarks.png';
	$msg='Bonjour';
}

// affichage association
$query="select P_PHOTO,P_SEXE from pompier where P_ID=".$id;
$result=mysql_query($query);
$row = mysql_fetch_array($result);
if ( $row["P_PHOTO"] == '' ) {
 	if ( $row["P_SEXE"] == 'M') $img2='../images/male.png';
 	else $img2='../images/female.png';
 	$txt="Veuillez enregistrer votre photo en cliquant sur <a href=../upd_personnel.php?pompier=$id target=_parent>Mes infos</a>"; 	
}
else if (! file_exists($trombidir."/".$row["P_PHOTO"])) {
  	if ( $row["P_SEXE"] == 'M') $img2='../images/male.png';
 	else $img2='../images/female.png';
 	$txt="Photo enregistrée mais non trouvée sur le serveur";
}
else {
 	$img2='../'.$trombidir.'/'.$row["P_PHOTO"];
	$txt="Vous pouvez modifier votre photo en cliquant sur <a href=../upd_personnel.php?pompier=$id target=_parent>Mes infos</a>";
}


echo "<table><tr><td>";
if ( $nbsections > 0 ) 
	echo "<img src=../images/".$img." title='".$msg."'></td>";
echo "<td><font face=$fontfamily color=$purple size=3><b>".$msg." ".ucfirst(get_prenom($id))." ".strtoupper(get_nom($id))."</b></font></td>";
if ( $nbsections == 0 ) 
	echo "<td> <img src=".$img2." height=32></td><td> <font size=1><i>".$txt."</i></font></td>";
echo "</tr></table>";
	
if ( $nbsections == 0 and check_rights($id,41)) {
    if ( check_rights($id,40)) $_40=true;
    else $_40=false;
    if ( check_rights($id,44)) $_44=true;
    else $_44=false;
    $query= "select p.P_PRENOM, p.P_NOM, p.P_CODE, p.P_PHOTO, p.P_SEXE, p.P_ID, s.S_ID, s.S_DESCRIPTION, p.P_PHONE
			from pompier p, section s, section_role sr
			where p.P_ID = sr.P_ID
			and s.S_ID = sr.S_ID
			and sr.GP_ID=107
			and s.S_ID in (".get_family_up("$section").")";
    $result=mysql_query($query);
    $num=mysql_num_rows($result);
	if ( $num <> 0 ) {
	  echo "<p><font face=$fontfamily color=$mydarkcolor size=3><b>
	  	La veille Opérationnelle est assurée par:<hr></b></font>";
	  echo "<table>";
	   while ($row = mysql_fetch_array($result)) {
      		$P_ID=$row["P_ID"];
      		$S_ID=$row["S_ID"];
      		$S_DESCRIPTION=$row["S_DESCRIPTION"];
      		$P_PRENOM=$row["P_PRENOM"];
      		$P_NOM=$row["P_NOM"];
      		$P_PHONE=$row["P_PHONE"];
      		$P_PHONE=$row["P_PHONE"];
      		if ( $P_PHONE <> '' ) $phone = "(".$P_PHONE.")";
      		else $phone="";
      		if ( $row["P_SEXE"] == 'M') $img2='../images/male.png';
 			else $img2='../images/female.png';
      		$P_PHOTO=$row["P_PHOTO"];
      		if ( $P_PHOTO <> '' and file_exists($trombidir."/".$row["P_PHOTO"])) {
      			$img2='../'.$trombidir.'/'.$row["P_PHOTO"];
			}
      		
			echo "<tr><td>";
			echo "<a href=../upd_personnel.php?pompier=$P_ID target=_parent><img height=32 src=".$img2." border=0></a>
			</td>
			<td>
			<font face=$fontfamily color=$mydarkcolor size=2>";
			if ( $_40 ) echo "<a href=../upd_personnel.php?pompier=$P_ID target=_parent>";
		    echo my_ucfirst($P_PRENOM)." ".strtoupper($P_NOM)."</a> ".$phone."</td>
		    <td><font face=$fontfamily color=$mydarkcolor size=2> pour ";
			if ( $_44 ) echo "<a href=../upd_section.php?S_ID=$S_ID target=_parent>";
			echo $S_DESCRIPTION."</a></td></tr>";
	   }
	   echo "</table>";	
    }
}

if ( $gardes == 1 ) {
// affichage des consignes, sans possibilité de les supprimer

$query="SELECT m.M_DUREE, DATE_FORMAT(m.M_DATE,'%d/%m/%Y %H:%i') as FORMDATE1,
        DATE_FORMAT(m.M_DATE, '%m%d%Y%T') as FORMDATE2, DATE_FORMAT(m.M_DATE,'%d-%m-%Y') as FORMDATE3,
	    m.M_TEXTE, m.M_OBJET, m.M_FILE, m.M_ID, tm.TM_ID, tm.TM_LIBELLE, tm.TM_COLOR, tm.TM_ICON, m.S_ID
	    FROM message m, type_message tm
        where m.M_TYPE='consigne'
		and m.TM_ID=tm.TM_ID";
if ( $nbsections == 0 )
	$query .= " and S_ID in (".get_family_up($section).")";
$query .= "	order by M_DATE desc";
$result=mysql_query($query);


echo "<font face=$fontfamily color=$mydarkcolor size=3><b>Consignes opérationnelles<hr></b></font>";
echo "<table cellspacing=0 border=0 >";
while ($row = mysql_fetch_array($result) )
{
 $MYDATEDIFF = $row["M_DUREE"] - my_date_diff($row["FORMDATE3"], date('d-m-Y'));
 if ( $MYDATEDIFF  >= 0 ) {
  	$img ="<img src=../images/".$row["TM_ICON"]." title=\"message ".$row["TM_LIBELLE"]."\">";
    echo "<tr><td width=30>".$img."</td>
           <td><font face=$fontfamily color=".$row["TM_COLOR"]." size=2>
		   	<b>".$row["M_OBJET"]." </font></b><br><font face=$fontfamily color=".$row["TM_COLOR"]." size=2>".$row["M_TEXTE"];
    if ( $row["M_FILE"] <> "") 
		echo " <i> fichier joint - 
		<a href=showfile.php?section=".$row["S_ID"]."&evenement=0&message=".$row["M_ID"]."&file=".$row["M_FILE"]."</a></i>";
    echo "</font></td></tr>";
 }
}
echo "</table><p>";
}

echo "<p><font face=$fontfamily color=$mydarkcolor size=3><b>Infos générales<hr></b></font>";
echo "<table cellspacing=0 border=0 >";

if ( check_rights($id,44)) {

	// affichage des infos diverses, sans possibilité de les supprimer
	$query="SELECT m.M_DUREE, DATE_FORMAT(m.M_DATE,'%d/%m/%Y %H:%i') as FORMDATE1,
        DATE_FORMAT(m.M_DATE, '%m%d%Y%T') as FORMDATE2, DATE_FORMAT(m.M_DATE,'%d-%m-%Y') as FORMDATE3,
	    m.M_TEXTE, m.M_OBJET, m.M_FILE, m.M_ID, tm.TM_ID, tm.TM_LIBELLE, tm.TM_COLOR, tm.TM_ICON, m.S_ID
	    FROM message m, type_message tm
        where m.M_TYPE='amicale'
		and m.TM_ID=tm.TM_ID";
	if ( $nbsections == 0 )
	$query .= " and S_ID in (".get_family_up($section).")";
	$query .= "	order by M_DATE desc";
	$result=mysql_query($query);
	
	while ($row = mysql_fetch_array($result) )
	{
 		$MYDATEDIFF = $row["M_DUREE"] - my_date_diff($row["FORMDATE3"], date('d-m-Y'));
 		if ( $MYDATEDIFF  >= 0 ) {
  			$img ="<img src=../images/".$row["TM_ICON"]." title=\"message ".$row["TM_LIBELLE"]."\">";
    		echo "<tr><td width=30>".$img."</td>
           		<td><font face=$fontfamily color=".$row["TM_COLOR"]." size=2>
		   		<b>".$row["M_OBJET"]." </font></b><br>
				   <font face=$fontfamily color=".$row["TM_COLOR"]." size=2>".$row["M_TEXTE"];
    		if ( $row["M_FILE"] <> "") echo " <i> fichier joint - 
				<a href=../showfile.php?section=".$row["S_ID"]."&evenement=0&message=".$row["M_ID"]."&file=".$row["M_FILE"].">"
				.$row["M_FILE"]."</a></i>";
    		echo "</font></td></tr>";
 		}
	}
} else {
// affichage générique pour les externes qui n'ont pas le droit de voir les infos
	echo "<tr><td><font face=$fontfamily color=$mydarkcolor size=2>
		Vous pouvez visualiser votre calendrier en cliquant sur <b>'Calendrier'</b> dans le menu de gauche,
		ou voir vos informations personnelles, y compris les formations suivies en cliquant sur <b>'Mes infos'
		</b>.
	</font></td></tr>";
}
	echo "</table>";

?>
