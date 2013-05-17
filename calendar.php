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
if (isset($_GET['pompier'])) $pompier=intval($_GET['pompier']);
else $pompier=$_SESSION['id'];
?>

<style>
.Nav {text-decoration:none; color:$mydarkcolor; font:bold 9pt arial;}
</style>
<SCRIPT>
function redirect(p1) {
     url="calendar.php?pompier="+p1;
     self.location.href=url;
}

</SCRIPT>
</HEAD>
<BODY>

<?php
include_once ("config.php");
$month=date("n");
$year=date("Y");
$mydate="[".$year.",".$month."]";


echo "<div align=center><table cellspacing=0 border=0>
      <tr><td width = 60 ><img src=images/date2.png></td><td>
      <font size=4><b>Calendrier de</b></font><br>";

echo "<select id='filtre' name='filtre' onchange=\"redirect(document.getElementById('filtre').value)\">";

$query = "select P_ID, P_PRENOM, P_NOM , S_CODE from pompier, section 
			 where P_SECTION = S_ID
			 and P_ID=".$_SESSION['id'];

if ( check_rights($_SESSION['id'], 40)) {
	$query="select p.P_ID, p.P_PRENOM, p.P_NOM , s.S_CODE 
		from pompier p, section s
		where p.P_SECTION = s.S_ID
		and p.P_OLD_MEMBER = 0 and p.P_STATUT <> 'EXT'";
	if (( $nbsections == 0 ) and (! check_rights($_SESSION['id'], 24))) {
	 	$section=get_highest_section_where_granted($_SESSION['id'],40);
		$query .= " and P_SECTION in (".get_family($section).")";
	}
}

if 	( check_rights($_SESSION['id'], 45) and $_SESSION['SES_COMPANY'] > 0 ) {
	if ( check_rights($_SESSION['id'], 40)) $query .= " union";
	else $query="";
	$query .= " select p.P_ID, p.P_PRENOM, p.P_NOM , s.S_CODE 
		from pompier p, section s
		where p.P_SECTION = s.S_ID
		and p.P_OLD_MEMBER = 0 
		and p.C_ID in (select C_ID from company 
						where C_PARENT = ".$_SESSION['SES_COMPANY']." 
						or C_ID = ".$_SESSION['SES_COMPANY']." )";
}

$query .= " order by P_NOM";

$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_ID=$row["P_ID"];
      $S_CODE=$row["S_CODE"];
      echo "<option value='".$P_ID."'";
      if ($P_ID == $pompier ) echo " selected ";
      if ( $nbsections <> 1 ) $cmt=' ('.$S_CODE.')';
      else $cmt ='';
      echo ">".strtoupper($P_NOM)." ".ucfirst($P_PRENOM).$cmt."</option>\n";
}
echo "</select>";

//echo $query;
if ( $nbsections == 0 )     
	echo "<br><font size=1>visualiser les inscriptions.</font>";
echo "</td></tr></table>";
echo "
<p><table border=1 cellspacing=0 cellpadding=4 bgcolor=$mylightcolor width='400' bordercolor=$mydarkcolor>
<tr>
  <td valign=top align=center bgcolor= $mylightcolor >
";
?>
    <table border=0 cellspacing=2 cellpadding=0>
    <tr>
    <td align=center>
    	<table border=0 cellspacing=0 cellpadding=0 width="100%">
    	<tr>
    	    <td align=left><a class="Nav" href="javascript:void(0)" onmousedown="gfFlat_s.showPrevMon();return false" onmouseout="gfFlat_s.stopShowMon();if(this.blur)this.blur()" onmouseup="gfFlat_s.stopShowMon()">&laquo;</a></td>
	    <td align=center><span id="smallCaption" class="Nav"></span></td>
	    <td align=right><a class="Nav" href="javascript:void(0)" onmousedown="gfFlat_s.showNextMon();return false" onmouseout="gfFlat_s.stopShowMon();if(this.blur)this.blur()" onmouseup="gfFlat_s.stopShowMon()">&raquo;</a></td>
        </tr>
        </table>
   </td>
   </tr>
   <tr>
       <td align=center>
       	   <iframe width=156 height=133 name="<?php echo $mydate;?>:msmall:agenda.php?pompier=<?php echo $pompier;?>:gfFlat_s:plugins_s.js" id="[2002,12]:msmall:agenda.php:gfFlat_s:plugins_s.js" src="iflateng.htm" scrolling="no" frameborder="0">
       	   </iframe>
       </td>
   </tr>
   <tr>
   <td align=center>
       <input type="Button" value="Aujourdhui" onclick="if(this.blur)this.blur();with(gfFlat_s)if(!fSetDate(gToday[0],gToday[1],gToday[2]))alert('You cannot select today!');return false;">
   </td>
   </tr>
   </table>
   </td>
   <td>
    <!--  FlatCalendar Tags (tag name and id must match) -->
    <iframe width=600 height=540 name="<?php echo $mydate;?>:mockup:share[gfFlat_s]:gfFlat_b:plugins_b.js" id="[2005,3]:mockup:share[gfFlat_s]:gfFlat_b:plugins_b.js" src="iflateng.htm" scrolling="no" frameborder="0">
    </iframe>
   </td>
</tr>
</table>

</BODY>
</HTML>
