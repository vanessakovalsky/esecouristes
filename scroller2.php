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

if ( $evenements == 1) {
// affichage des événements en cours
$query="select E.E_CODE, EH.EH_ID, E.TE_CODE, TE.TE_LIBELLE, 
	E.E_LIEU, EH.EH_DEBUT, EH.EH_FIN, E.E_NB, E.E_LIBELLE, E.E_CODE,
	DATE_FORMAT(EH.EH_DATE_DEBUT,'%d-%m-%Y') as FORMDATE1, 
	E.S_ID, S.S_DESCRIPTION, E.E_CLOSED,E.E_CANCELED
    from evenement E, type_evenement TE, section S, evenement_horaire EH
	where E.TE_CODE=TE.TE_CODE
	and E.E_CODE = EH.E_CODE
	and E.S_ID = S.S_ID";
if ( $nbsections == 0 ) {
	if ( $_SESSION['SES_STATUT'] == 'EXT' )
		$query .= " and E.S_ID in (".get_family(get_section($id)).")";
	else
		$query .= " and E.S_ID in (".get_family_up(get_section($id)).")";
}
$query .= " and ( EH.EH_DATE_DEBUT >= CURDATE()
				  or ( EH.EH_DATE_DEBUT < CURDATE() and EH.EH_DATE_FIN >= CURDATE() )
				)";
	
if (( is_formateur($id) == 0 ) and (! check_rights($_SESSION['id'], 15))) 
		$query .= " and E.TE_CODE <> 'INS'";
		
$query .= " order by EH.EH_DATE_DEBUT";
$result=mysql_query($query);


echo "<font face=$fontfamily color=$mydarkcolor size=3><b>Calendrier des activités<hr></b></font>";
echo "<table width=95% cellspacing=0 border=0 >";
if ( check_rights($id, 41)) {
 while ($row = mysql_fetch_array($result) )
 {
  $E_CODE=$row["E_CODE"];
  $EH_ID=$row["EH_ID"];
  $TE_CODE=$row["TE_CODE"];
  $TE_LIBELLE=$row["TE_LIBELLE"];
  $E_LIBELLE=$row["E_LIBELLE"];
  $E_LIEU=$row["E_LIEU"];
  $E_CODE=$row["E_CODE"];
  $E_CLOSED=$row["E_CLOSED"];
  $E_CANCELED=$row["E_CANCELED"];
  $S_ID=$row["S_ID"];
  $E_NB=$row["E_NB"];
  $EH_DEBUT=$row["EH_DEBUT"];
  $FORMDATE1=$row["FORMDATE1"];
  $EH_FIN=$row["EH_FIN"];
  $E_NB=$row["E_NB"];
  
  $S_DESCRIPTION=get_section_name($S_ID);

  $mycolor=$textcolor;
  if ( $E_CANCELED == 1 ) $myimg='<img src=../images/red.gif title=événement-annulé>';
  elseif ( $E_CLOSED == 1 ) $myimg='<img src=../images/yellow.gif title=inscriptions-fermées>';
  else $myimg='<img src=../images/green.gif title=inscriptions-ouvertes>';
  
  if ( $EH_ID > 1 ) $sess=' session n°'.$EH_ID;
  else $sess='';
  
  echo "<tr><td width=30><img src=../images/".$TE_CODE."small.gif height=20> </td>
           <td><font face=$fontfamily><font color=$mycolor size=2><b>
		   <a href=./../evenement_display.php?evenement=$E_CODE&from=scroller target=_parent>".$FORMDATE1." : ".$E_LIBELLE.$sess;
  if ( $nbsections <> 1)  echo " <font face=$fontfamily size=1><i>(".$S_DESCRIPTION.") </i></font>";
  echo "</a></b>".$myimg."<br>";
  echo "<font face=$fontfamily size=2>".$TE_LIBELLE." - lieu: ".$E_LIEU." </font>";
   
  echo "</td></tr>";
 }
}
else echo "<tr><td><font color=$mydarkcolor size=2>Vous n'avez pas les permissions suffisantes pour voir la liste des événements.</font></td></tr>";
echo "</table>";
}
else 
echo "<font face=$fontfamily color=$mydarkcolor size=3><b>Pas d'activités.</b></font>";
?>
