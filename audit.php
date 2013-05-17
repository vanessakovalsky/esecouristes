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
check_all(20);
get_session_parameters();

writehead();

?>
<script type='text/javascript' src='popupBoxes.js'></script>
<script language="JavaScript">
function orderfilter1(section,sub){
	 self.location.href="audit.php?filter="+section+"&subsections="+sub;
	 return true
}
function orderfilter2(section,sub){
 	 if (sub.checked) s = 1;
 	 else s = 0;
	 self.location.href="audit.php?filter="+section+"&subsections="+s;
	 return true
}
</script>
<?php
echo "</head>";
echo "<body>";


$query="select p.P_SECTION , p.P_NOM , p.P_PRENOM, a.A_DEBUT, a.A_FIN, p.P_ID,
		a.A_OS, a.A_BROWSER, g.GP_ID, p.GP_ID2, g.GP_DESCRIPTION, s.S_CODE, s.S_ID
        from audit a, pompier p, groupe g, section s
        where p.P_ID=a.P_ID
        and p.P_SECTION=s.S_ID
        and p.GP_ID=g.GP_ID";

if ( $subsections == 1 ) {
  	   $query .= "\nand p.P_SECTION in (".get_family("$filter").")";
}
else {
  	   $query .= "\nand p.P_SECTION =".$filter;
}
$query .= "\nand time_to_sec(timediff(now(),a.A_DEBUT)) < (24 * 3600 * ".$days_audit.")";
$query .= "\norder by a.A_DEBUT desc";

$result=mysql_query($query);
$number=mysql_num_rows($result);
$moyenne= round($number / $days_audit, 0);

echo "<div align=center><font size=4><b>Dernières connexions</b><br>";
echo "<font size=2> En moyenne <b>$moyenne</b> connexions par jour.<br>";
echo "<font size=2> Total <b>$number</b> connexions sur les $days_audit derniers jours.";
if ($nbsections <> 1 ) {
echo "<p><table cellspacing=0 border=0 >";
echo "<tr><td>";
	echo choice_section_order('audit.php');
	echo "</td><td><select id='filter' name='filter' 
		onchange=\"orderfilter1(document.getElementById('filter').value,'".$subsections."')\">";
	  display_children2(-1, 0, $filter, $nbmaxlevels, $sectionorder);
	echo "</select> ";
	echo "</td>";
	if ( get_children("$filter") <> '' ) {
	  if ($subsections == 1 ) $checked='checked';
	  else $checked='';
	  echo "<td align=center><input type='checkbox' name='sub' $checked 
	   onClick=\"orderfilter2(document.getElementById('filter').value, this)\"/>
	   <font size=1>inclure les<br>sous sections</td>";
	}
echo "</td></tr><tr><td colspan=2>";
}
else echo "<p>";

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


if ($nbsections <> 1 ) echo "</td></tr></table>";
echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr height=10 class=TabHeader >
      	  <td width=120>Nom</td>
      	  <td  width=0></td>";
if ( $nbsections <> 1 ) {
    echo "<td width=100 align=center>Section</td>
      	  <td width=0></td>";
}
    echo "<td width=120 align=center>Début</td>
      	  <td width=0></td>
      	  <td width=120 align=center>Dernière action</td>
      	  <td width=0></td>
      	  <td width=120 align=center>OS</td>
      	  <td width=0></td>
      	  <td width=120 align=center>Browser</td>
      	  <td width=0></td>
      	  <td width=120 align=center>Permission</td>
      </tr>
      ";

// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result)) {
      $S_CODE=$row["S_CODE"];
      $S_ID=$row["S_ID"];
      $P_ID=$row["P_ID"];
      $GP_ID=$row["GP_ID"];
      $GP_ID2=$row["GP_ID2"];
      $A_DEBUT=$row["A_DEBUT"];
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $A_FIN=$row["A_FIN"];
      $A_OS=$row["A_OS"];
      $GP_DESCRIPTION=$row["GP_DESCRIPTION"];
      $A_BROWSER=$row["A_BROWSER"];

	  if (( $GP_ID2 <> "" ) and ( $GP_ID == 0 )){
	  	$query2="select GP_DESCRIPTION from groupe
		 	where GP_ID =".$GP_ID2;
	  	$result2=mysql_query($query2);
	  	$row2=mysql_fetch_array($result2);
	  	$GP_DESCRIPTION=$row2["GP_DESCRIPTION"];
      }  	 

      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      
echo "<tr height=10 bgcolor=$mycolor>
      	  <td width=120 align=left><font size=1><a href=upd_personnel.php?pompier=".$P_ID.">".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
if ( $nbsections <> 1 ) {
    echo "<td width=100 align=center><font size=1><a href=upd_section.php?S_ID=".$S_ID.">".$S_CODE."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>";
}
echo "    <td width=120 align=center><font size=1>".$A_DEBUT."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=120 align=center><font size=1>".$A_FIN."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=120 align=center><font size=1>".$A_OS."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=120 align=center><font size=1>".$A_BROWSER."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=120 align=center><font size=1>".$GP_DESCRIPTION."</font></td>  	  
      </tr>"; 
}
echo "</table>";
echo "</td></tr></table>";   
?>
