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
check_all(11);
$id=$_SESSION['id'];
get_session_parameters();

$possibleorders= array('P_NOM','TI_CODE','I_STATUS','I_DEBUT','I_FIN','I_COMMENT');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='I_DEBUT';

writehead();
?>

<html>
<script type='text/javascript' src='popupBoxes.js'></script>
<SCRIPT>
function redirect(statut, type, person, dtdb, dtfn, validation,section) {
	 url = "indispo_choice.php?statut="+statut+"&type_indispo="+type+"&person="+person+"&dtdb="+dtdb+"&dtfn="+dtfn+"&validation="+validation+"&filter="+section;
	 self.location.href = url;
}

function redirect2(statut, type, person, dtdb, dtfn, validation,section, subsection){
 	 if (subsection.checked) s = 1;
 	 else s = 0;
	 url = "indispo_choice.php?statut="+statut+"&type_indispo="+type+"&person="+person+"&dtdb="+dtdb+"&dtfn="+dtfn+"&validation="+validation+"&filter="+section+"&subsections="+s;
	 self.location.href = url;
	 return true
}

var fenetreDetail=null;
function displaymanager(p1){
	 url="indispo_display.php?code="+p1;
	 fenetre=window.open(url,'Note','toolbar=no,location=no,directories=no,status=no,scrollbars=no,resizable=yes,copyhistory=no,' + 'width=450' + ',height=550');
	 fenetreDetail = fenetre;
	 return true
	 //self.location.href = url;
}

function fermerDetail() {
	 if (fenetreDetail != null) {
	    fenetreDetail.close( );
	    fenetreDetail = null;
         }
}
</SCRIPT>

<?php
include_once ("config.php");

echo "<body>";
echo "<div align=center>";

$query1="select distinct i.I_CODE, p.P_ID, p.P_NOM, p.P_PRENOM, p.P_OLD_MEMBER, DATE_FORMAT(i.I_DEBUT, '%d-%m-%Y') as I_DEBUT, DATE_FORMAT(i.I_FIN, '%d-%m-%Y') as I_FIN, i.TI_CODE,
        ti.TI_LIBELLE, i.I_COMMENT, ist.I_STATUS_LIBELLE, i.I_STATUS, date_format(i.IH_DEBUT,'%H:%i') IH_DEBUT, date_format(i.IH_FIN,'%H:%i') IH_FIN, i.I_JOUR_COMPLET
        from pompier p, indisponibilite i, type_indisponibilite ti, indisponibilite_status ist
        where p.P_ID=i.P_ID
	and i.TI_CODE=ti.TI_CODE
	and i.I_STATUS=ist.I_STATUS";

if ( $nbsections <> 1 ) {
	if ( $subsections == 1 ) 
		$query1 .= "\nand P_SECTION in (".get_family("$filter").")";
	else 
		$query1 .= "\nand  P_SECTION = ".$filter;
}	
if ( $statut <> "ALL") $query1 .= "\nand  p.P_STATUT = '".$statut."'";
if ( $type_indispo <> "ALL") $query1 .= "\nand  ti.TI_CODE = '".$type_indispo."'";
if ( intval($person) > 0 ) $query1 .= "\nand  p.P_ID = ".$person;
if ( $validation <> "ALL") $query1 .= "\nand  ist.I_STATUS = '".$validation."'";

$tmp=explode ( "-",$dtdb); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2]; 
$tmp=explode ( "-",$dtfn); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];
$query1 .="\n and i.I_DEBUT <= '$year2-$month2-$day2' 
			 and i.I_FIN   >= '$year1-$month1-$day1'";


if ( $order == 'P_NOM' ) $query1 .="\norder by p.P_NOM, p.P_PRENOM, i.I_DEBUT";
else $query1 .="\norder by i.".$order;

if ( $order == 'I_DEBUT' or $order == 'I_FIN' or $order == 'I_COMMENT' ) $query1 .=" desc";

$result1=mysql_query($query1);
$number=mysql_num_rows($result1);


echo "<p><table><tr><td><font size=4><b>Absences du personnel</b></font><i> (".$number." trouvées)</td>
<td><img src=\"images/xls.jpg\" id=\"StartExcel\" height=\"24\" border=\"0\" alt=\"Excel\" 
title=\"Extraire ces données dans un fichier Excel\" onclick=\"window.open('indispo_list_xls.php');\" class=\"noprint\" /></td></tr></table>";

echo "<form name=formf>";
echo "<table width=600 cellspacing=0 border=0>";

//filtre section
if ($nbsections <> 1 ) {
 	echo "<tr><td width=50% align=right> Section </td>";
	echo "<td align=left><select id='filter' name='filter' 
		onchange=\"redirect( '$statut' ,'$type_indispo', '$person', '$dtdb','$dtfn', '$validation',document.formf.filter.options[document.formf.filter.selectedIndex].value)\">";
	  display_children2(-1, 0, $filter, $nbmaxlevels);
	  echo "</select></td> ";
	  
	if ( get_children("$filter") <> '' ) {
	  if ($subsections == 1 ) $checked='checked';
	  else $checked='';
	  echo "<tr><td width=50% align=right> Inclure les sous sections </td>";
	  echo "<td align=left><input type='checkbox' name='sub' $checked 
	   onClick=\"redirect2('$statut' ,'$type_indispo', '$person', '$dtdb','$dtfn', '$validation', '$filter',this)\"/></td></tr>";
	}  
	  
}

// choix catégorie personnel
echo "<tr><td width=50% align=right> Catégorie de personnel </td>";
echo "<td width=50% align=left><select id='menu1' name='menu1' 
onchange=\"redirect(document.formf.menu1.options[document.formf.menu1.selectedIndex].value, '$type_indispo', '$person', '$dtdb','$dtfn', '$validation','$filter')\">";
echo "<option value='ALL'>Toutes catégories de personnel </option>\n";
$query="select S_STATUT, S_DESCRIPTION from statut 
         where S_STATUT <> 'EXT' and S_CONTEXT =".$nbsections;
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $S_STATUT=$row["S_STATUT"];
      $S_DESCRIPTION=$row["S_DESCRIPTION"];
      if ( $statut == $S_STATUT ) {
      	   echo "<option value='".$S_STATUT."' selected>".$S_DESCRIPTION."</option>\n";
      }
      else {
      	   echo "<option value='".$S_STATUT."'>".$S_DESCRIPTION."</option>\n";
      }
}
echo "</select><td></tr>";


// choix type absence
echo "<tr><td width=50% align=right> Type d'absence </td>";
echo "<td width=50% align=left><select id='menu2' name='menu2' 
onchange=\"redirect( '$statut' ,document.formf.menu2.options[document.formf.menu2.selectedIndex].value, '$person', '$dtdb','$dtfn', '$validation','$filter')\">";
echo "<option value='ALL' selected>Toutes absences </option>\n";
$query="select distinct TI_CODE, TI_LIBELLE
        from type_indisponibilite";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $TI_CODE=$row["TI_CODE"];
      $TI_LIBELLE=$row["TI_LIBELLE"];
      if ( $type_indispo == $TI_CODE ) {
      	   echo "<option value='".$TI_CODE."' selected>".$TI_LIBELLE."</option>\n";
      }
      else {
      	   echo "<option value='".$TI_CODE."'>".$TI_LIBELLE."</option>\n";
      }
}
echo "</select></td></tr>";

// choix personne
echo "<tr><td width=50% align=right> Nom </td>";
echo "<td width=50% align=left><select id='menu3' name='menu3' 
onchange=\"redirect( '$statut','$type_indispo' ,document.formf.menu3.options[document.formf.menu3.selectedIndex].value, '$dtdb','$dtfn', '$validation','$filter')\">";
echo "<option value='ALL' selected>Toutes les personnes </option>\n";
$query="select distinct P_ID, P_NOM, P_PRENOM, P_OLD_MEMBER from pompier";
if ( $subsections == 1 ) 
	$query .= " where  P_SECTION in (".get_family("$filter").")";
else $query .= " where  P_SECTION = ".$filter;
$query .=" and P_STATUT <> 'EXT' and P_OLD_MEMBER= 0";
if ( $statut <> "ALL" ) $query .=" and P_STATUT ='".$statut."'";
$query .=" order by P_NOM";
echo "\n<OPTGROUP LABEL=\"personnel actif\" style=\"background-color:$mylightcolor\">";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $P_ID=$row["P_ID"];
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_OLD_MEMBER=$row["P_OLD_MEMBER"];
      if ( $person == $P_ID ) {
      	   echo "<option value='".$P_ID."' selected>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</option>\n";
      }
      else {
      	   echo "<option value='".$P_ID."'>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</option>\n";
      }
}
$query="select distinct P_ID, P_NOM, P_PRENOM, P_OLD_MEMBER from pompier";
if ( $subsections == 1 ) 
	$query .= " where  P_SECTION in (".get_family("$filter").")";
else $query .= " where  P_SECTION = ".$filter;
$query .=" and P_STATUT <> 'EXT' and P_OLD_MEMBER> 0";
if ( $statut <> "ALL" ) $query .=" and P_STATUT ='".$statut."'";
$query .=" order by P_NOM";
echo "\n<OPTGROUP LABEL=\"anciens membres\" style=\"background-color:$mygreycolor\">";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $P_ID=$row["P_ID"];
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_OLD_MEMBER=$row["P_OLD_MEMBER"];
      if ( $person == $P_ID ) {
      	   echo "<option value='".$P_ID."' selected>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</option>\n";
      }
      else {
      	   echo "<option value='".$P_ID."'>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</option>\n";
      }
}
echo "</select></td></tr>";

// choix etat de la demande
echo "<tr><td width=50% align=right> Etat de la demande </td>";
echo "<td width=50% align=left><select id='menu5' name='menu5' 
onchange=\"redirect( '$statut' ,'$type_indispo', '$person', '$dtdb','$dtfn', document.formf.menu5.options[document.formf.menu5.selectedIndex].value,'$filter')\">";
echo "<option value='ALL' selected>Tous </option>\n";
$query="select distinct I_STATUS, I_STATUS_LIBELLE
        from indisponibilite_status";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $I_STATUS=$row["I_STATUS"];
      $I_STATUS_LIBELLE=$row["I_STATUS_LIBELLE"];
      if ( $validation == $I_STATUS ) {
      	   echo "<option value='".$I_STATUS."' selected>".$I_STATUS_LIBELLE."</option>\n";
      }
      else {
      	   echo "<option value='".$I_STATUS."'>".$I_STATUS_LIBELLE."</option>\n";
      }
}
echo "</select></td></tr>";


// Choix Dates
echo "<tr><td align=right >Début:</td><td align=left>";
?>
<input class="plain" name="dtdb" id="dtdb" value=
<?php
echo "\"".$dtdb."\"";
?>
size="12" onchange="checkDate2(document.formf.dtdb)">
<a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fStartPop(document.formf.dtdb,document.formf.dtfn);return false;" HIDEFOCUS>
<img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt="" ></a>
<?php
echo "</td></tr>";


echo "<tr><td align=right >Fin :</td><td align=left>";
?>
<input class="plain" name="dtfn" id="dtfn" value=
<?php
echo "\"".$dtfn."\"";
?>
size="12" onchange="checkDate2(document.formf.dtfn)">
<a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.formf.dtdb,document.formf.dtfn);return false;" HIDEFOCUS>
<img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt="" ></a>
<?php

echo " <input type='submit' value='go'>";
echo "</td></tr></table></form>";


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
$result=mysql_query($query1);

if ( $number > 0 ) {
 echo "<p><table>";
 echo "<tr>
	  <td class='FondMenu'>";
   echo "<table cellspacing=0 border=0>";
   echo "<tr class=TabHeader>
      	  <td width=150><a href=indispo_choice.php?order=P_NOM class=TabHeader>Nom</a></td>
      	  <td width=0></td>
      	  <td width=120><a href=indispo_choice.php?order=TI_CODE class=TabHeader>Absence</a></td>
      	  <td width=0></td>
      	  <td width=120><a href=indispo_choice.php?order=I_DEBUT class=TabHeader>début</a></td>
      	  <td width=0></td>
      	  <td width=120><a href=indispo_choice.php?order=I_FIN class=TabHeader>fin</a></td>
      	  <td width=0></td>
      	  <td width=60>Durée</td>
      	  <td width=0></td>
      	  <td width=100><a href=indispo_choice.php?order=I_STATUS class=TabHeader>Etat demande</a></td>
      	  <td width=0></td>
      	  <td width=160><a href=indispo_choice.php?order=I_COMMENT class=TabHeader>Commentaire</a></td>
      </tr>
      ";

   $i=0;
   while ($row=@mysql_fetch_array($result)) {
       $I_CODE=$row["I_CODE"];
	   $I_JOUR_COMPLET=$row["I_JOUR_COMPLET"];
       $P_ID=$row["P_ID"];
       $P_NOM=$row["P_NOM"];
       $P_PRENOM=$row["P_PRENOM"];
       $I_DEBUT=$row["I_DEBUT"];
       $I_FIN=$row["I_FIN"];
       $TI_CODE=$row["TI_CODE"];
       $TI_LIBELLE=$row["TI_LIBELLE"];
       $I_COMMENT=$row["I_COMMENT"];
       $I_STATUS=$row["I_STATUS"];
       $IH_DEBUT=$row["IH_DEBUT"];
       $IH_FIN=$row["IH_FIN"];
       $I_STATUS_LIBELLE=$row["I_STATUS_LIBELLE"];
       $P_OLD_MEMBER=$row["P_OLD_MEMBER"];
       
      if ( $P_OLD_MEMBER > 0 ) {
       	  $cmt="<font color=black title='Attention: Ancien membre'>";
      }
      else $cmt="";

      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      if ( $I_STATUS == 'VAL' ) $mytxtcolor='green';
      if (( $I_STATUS == 'ANN' ) or ( $I_STATUS == 'REF' )) $mytxtcolor='red';
      if (( $I_STATUS == 'ATT' )or ( $I_STATUS == 'PRE' ))  $mytxtcolor='orange';
      $abs=my_date_diff($I_DEBUT,$I_FIN) + 1;
      
      if ( $I_JOUR_COMPLET == 0 ) {
      		if ( $abs == 1 ) {
      		 	if ( substr($IH_FIN,0,1) == '0' ) $fin = substr($IH_FIN,1,1);
      		 	else  $fin = substr($IH_FIN,0,2);
      		 	if ( substr($IH_DEBUT,0,1) == '0' ) $debut = substr($IH_DEBUT,1,1);
      		 	else  $debut = substr($IH_DEBUT,0,2);      		 	
      		 	$abs = $fin - $debut;
      		 	$abs .= ' heures';
      		}
      		else $abs .= ' jours';
      		
      		$I_DEBUT=$I_DEBUT." ".$IH_DEBUT;
        	$I_FIN=$I_FIN." ".$IH_FIN;
      }
      else $abs .= ' jours';
      
      echo "<tr bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$mycolor'\" 
	  onclick=\"this.bgColor='#33FF00';\" >
      	  <td><a href='upd_personnel.php?pompier=".$P_ID."'>".$cmt.strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</a></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td onclick=\"fermerDetail();displaymanager('$I_CODE')\"><font size=1>".$TI_LIBELLE."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td onclick=\"fermerDetail();displaymanager('$I_CODE')\"><font size=1>".$I_DEBUT."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td onclick=\"fermerDetail();displaymanager('$I_CODE')\"><font size=1>".$I_FIN."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td onclick=\"fermerDetail();displaymanager('$I_CODE')\"><font size=1>".$abs."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td onclick=\"fermerDetail();displaymanager('$I_CODE')\"><font color=$mytxtcolor><b><u title='voir détail'>".$I_STATUS_LIBELLE."</u></b></font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td onclick=\"fermerDetail();displaymanager('$I_CODE')\"><font size=1>".$I_COMMENT."</font></td>
         </tr>";
   }
}
else {
     echo "<p><b>Aucune absence ne correspond aux critères choisis</b>";
}

echo "</table>";
echo "</td></tr></table>"; 
echo "<iframe width=132 height=142 name=\"gToday:contrast:agenda.js\" id=\"gToday:contrast:agenda.js\" src=\"ipopeng.htm\" scrolling=\"no\" frameborder=\"0\" style=\"visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;\"></iframe>";
echo "</BODY>
</HTML>";
?>
