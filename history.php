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
check_all(49);
get_session_parameters();

$possibleorders= array('LH_STAMP','LT_DESCRIPTION','P_ID','LH_COMPLEMENT','P_NOM','COMPLEMENT_CODE','P_NOM2');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='LH_STAMP';

writehead();
?>

<script language="JavaScript">
function orderfilter(p1,p2){
	 self.location.href="history.php?ltcode="+p1+"&lccode=P&lcid="+p2+"&order=LH_STAMP";
	 return true
}
</script>
<?php

echo "</head>";
echo "<body>";

if ( check_rights($_SESSION['id'], 9)) $granted_for_all=true;
else $granted_for_all=false;

$query ="select lh.LH_ID,lh.P_ID, date_format(lh.LH_STAMP, '%d-%m-%Y %k:%i:%s') DATE, LH_STAMP, lh.LT_CODE,lh.LH_WHAT,lh.LH_COMPLEMENT,
		lt.LT_CODE,lt.LC_CODE,lt.LT_DESCRIPTION,p.P_NOM, p.P_PRENOM, e.E_CODE, e.E_LIBELLE, p2.P_NOM P_NOM2, p2.P_PRENOM P_PRENOM2
		from log_type lt, pompier p, log_history lh
		left join evenement e on ( e.E_CODE = lh.COMPLEMENT_CODE)
		left join pompier p2 on ( p2.P_ID = lh.LH_WHAT)
		where p.P_ID = lh.P_ID
		and lh.LT_CODE=lt.LT_CODE
		and lt.LC_CODE='".$lccode."'";
if ( $ltcode <> 'ALL' ) 
		$query .= " and lt.LT_CODE='".$ltcode."'";

$what="";$what2="";
if ( $lcid > 0) {
 	$_SESSION["lcid2"]=$lcid;
	$query .= " and lh.LH_WHAT='".$lcid."'";
	if ( $lccode = 'P' ) {
	 	$what="<br>pour ".my_ucfirst(get_prenom("$lcid"))." ".strtoupper(get_nom("$lcid"));
		if ( $granted_for_all )  $what2 ="<a href=history.php?ltcode=".$ltcode."&lccode=P&lcid=0 title='historique pour tout le personnel'>Voir tout</a>";
	}
}
else { // $lcid=0
 	if (! $granted_for_all) check_all(9);
 	if ( $lccode = 'P' )
	  	$what="<br>pour tout le personnel";
}
$query .= " order by ".$order ;

if ( $order == 'LH_STAMP' or $order == 'COMPLEMENT_CODE') $query .= " desc";

$result=mysql_query($query);
$number=mysql_num_rows($result);

echo "<div align=center>
<table><tr>
<td>
<img src=images/zoom.png>
</td>
<td>
<font size=4>Historique des modifications sur les ".$days_log." derniers jours ".$what."<font size=2><i> ($number modifications)</i> ".$what2."</font>
</td></tr>
<tr>
<td>Type d'historique</td>
<td>";

//filtre LT_CODE
echo "<select id='ltcode' name='ltcode' 
	onchange=\"orderfilter(document.getElementById('ltcode').value,'$lcid')\">
	  <option value='ALL'>tous types</option>";
$query2="select lt.LT_CODE, lt.LT_DESCRIPTION, count(*) as NB
		 from log_type lt, log_history lh
         where lt.LT_CODE = lh.LT_CODE
		 and lt.LC_CODE='".$lccode."'";
if ($lcid > 0) 
	$query2 .= " and lh.LH_WHAT = '".$lcid."'";
$query2 .=" group by lt.LT_CODE, lt.LT_DESCRIPTION
		 	order by lt.LT_DESCRIPTION";
$result2=mysql_query($query2);


while ($row=@mysql_fetch_array($result2)) {
      $_LT_CODE=$row["LT_CODE"];
      $_LT_DESCRIPTION=$row["LT_DESCRIPTION"];
	  $_NB=$row["NB"];
      echo "<option value='".$_LT_CODE."' title=\"".$_LT_DESCRIPTION."\"";
      if ($_LT_CODE == $ltcode ) echo " selected ";
      echo ">".$_LT_DESCRIPTION." (".$_NB.")</option>\n";
}
echo "</select></td></tr>";

echo "</table><p>";

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
	$query .= $pages->limit;
}
$result=mysql_query($query);


echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr >
		  <td width=120 align=center><a href=history.php?order=LH_STAMP class=TabHeader>Date</a></td>
      	  <td width=0></td>
		  <td width=150 align=center><a href=history.php?order=P_NOM class=TabHeader>Modifié par</a></td>
      	  <td width=0></td>
      	  <td width=200 align=center><a href=history.php?order=LT_DESCRIPTION class=TabHeader>Action</a></td>
      	  <td width=0></td>
      	  <td width=150 align=center><a href=history.php?order=P_NOM2 class=TabHeader>Pour</a></td>
      	  <td width=0></td>
      	  <td width=150 align=center><a href=history.php?order=COMPLEMENT_CODE class=TabHeader>référence</a></td>
      	  <td width=0></td>
      	  <td width=150 align=center><a href=history.php?order=LH_COMPLEMENT class=TabHeader>Complément</a></td>
      </tr>
      ";

// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result)) {
      $DATE=$row["DATE"];
	  $LT_DESCRIPTION=$row["LT_DESCRIPTION"];
	  $P_ID=$row["P_ID"];
	  $LH_WHAT=$row["LH_WHAT"];
	  $LH_COMPLEMENT=$row["LH_COMPLEMENT"];
	  $P_NOM=strtoupper($row["P_NOM"]);
	  $P_PRENOM=my_ucfirst($row["P_PRENOM"]);
	  $P_NOM2=strtoupper($row["P_NOM2"]);
	  $P_PRENOM2=my_ucfirst($row["P_PRENOM2"]);
	  $E_LIBELLE=$row["E_LIBELLE"];
	  $E_CODE=$row["E_CODE"];
	  if ( $E_LIBELLE <> "" ) {
			$COMPLEMENT = "<a href=evenement_display.php?evenement=$E_CODE&from=history title=\"".$LH_COMPLEMENT."\">".$E_LIBELLE."</a>";
	  }
	  else $COMPLEMENT="";
	  
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      
	echo "<tr bgcolor=$mycolor>";
	echo "<td align=center>".$DATE."</td>
      	  <td bgcolor=$mydarkcolor width=0></td>
		  <td align=center><a href=upd_personnel.php?pompier=".$P_ID.">".$P_PRENOM." ".$P_NOM."</a></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=left>".$LT_DESCRIPTION."</td>
      	  <td bgcolor=$mydarkcolor width=0></td>
		  <td align=center><a href=upd_personnel.php?pompier=".$LH_WHAT.">".$P_PRENOM2." ".$P_NOM2."</a></td>
		  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=center><font size=1>".$COMPLEMENT."</font></td>  	
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td align=left ><font size=1>".substr($LH_COMPLEMENT,0,40)."</font></td>  	  
      </tr>"; 
}
echo "</table>";
echo "</td></tr></table>";   

if ( $lccode = 'P' )
	echo "<p><input type=button value='retour' onclick='javascript:self.location.href=\"upd_personnel.php?pompier=".$_SESSION["lcid2"]."\";'> ";
?>
