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
check_all(23);
writehead();

?>
<script>

function fermerfenetre(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}
</script>
</head>
<?php

if (! isset ($_SESSION['prev'])) $_SESSION['prev'] = 1;

if ($_SESSION['prev'] == 1 ) $defaulttype='histo';
else $defaulttype='compta';	

if (isset($_GET['type'])) $type=$_GET['type']; //histo,compta
else $type=$defaulttype;
if (isset($_GET['order'])) $order=$_GET['order']; //histo,compta
else $order='s.S_DATE';

if ( $type == 'histo' ) $_SESSION['prev'] = 1;
if ( $type == 'compta' ) $_SESSION['prev'] = 0;

// get dat parameters, else use default dates
if (isset($_GET['dtdb'])) {
 	$dtdb = mysql_real_escape_string($_GET['dtdb']);	
}
else 
	$dtdb = date("d-m-Y",mktime(0,0,0,1,1,date("Y")-1));

if (isset($_GET['dtfn'])) {
 	$dtfn = mysql_real_escape_string($_GET['dtfn']);	
}
else {
 	$d =  date ("d");
 	$m =  date ("m");
 	$y =  date ("Y");
 	if ( $d < 29 ) $d = $d + 1;
 	else if ( $m < 12 ) $m = $m + 1;
 	else $y = $y + 1;
	$dtfn = date("d-m-Y",mktime(0,0,0,$m,$d,$y));
}
$tmp=explode ( "-",$dtdb); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2]; 
$tmp=explode ( "-",$dtfn); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];

echo "<body>";

if ( $type == 'compta' ) {
	$h='SMS envoyés par département ';
	$other='histo';
	$otheri='sms2.png';
	$othert='Voir la liste des sms envoyés';
}
else {
 	$h='SMS envoyés (historique) ';
	$other='compta';
	$otheri='calculette.png';
	$othert='Voir le nombre de sms envoyés par département';
}
echo "<div align=center>
<table cellspacing=0 border=0>
<tr><td align=center><font size=4><b>".$h."</b></td> 
<td><a href=histo_sms.php?type=".$other."&dtdb=".$dtdb."&dtfn=".$dtfn." >
<img height=24 border=0 src=images/".$otheri." title=\"".$othert."\"></a></font></td>";
echo "<tr></table>";

echo "<p><form name='formf' action='histo_sms.php'>";
echo "<table cellspacing=0 border=0><tr><td align=center>";
// Choix Dates

echo "<tr>
<td align=right >Début:</td>
<td align=left>";
?>
<input class="plain" name="dtdb" id="dtdb" value=
<?php
echo "\"".$dtdb."\"";
?>
size="12" onchange="checkDate2(document.formf.dtdb)"><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fStartPop(document.formf.dtdb,document.formf.dtfn);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt="" ></a>
<?php
echo "</td></tr>";


echo "<tr><td align=right >Fin :</td><td align=left>";
?>
<input class="plain" name="dtfn" id="dtfn" value=
<?php
echo "\"".$dtfn."\"";
?>
size="12" onchange="checkDate2(document.formf.dtfn)"><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.formf.dtdb,document.formf.dtfn);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt="" ></a>
<?php
echo " <input type='submit' value='go'>";
echo "</td></tr>";
echo "</table>";

// ===============================================
// compta
// ===============================================

if ( $type == 'compta' ) {
$SID = array();
$SCODE = array();
$SDESCRIPTION = array();
$SNB = array();
$i=0; 
$total=0;

$query="select S_ID, S_CODE, S_DESCRIPTION 
		from section_flat where NIV=3";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
	$S_ID=$row["S_ID"];
	$query2="select sum(sm.S_NB) as S_NB 
	     from smslog sm, pompier p
		 where p.P_ID = sm.P_ID
		 and sm.S_DATE <= '$year2-$month2-$day2' 
		 and sm.S_DATE   >= '$year1-$month1-$day1'
		 and p.P_SECTION in (".get_family("$S_ID").")";
	$result2=mysql_query($query2);
	$row2=@mysql_fetch_array($result2);
	if ( $row2["S_NB"] > 0 && $row2["S_NB"] <> "" ) {
		$SID[$i]=$S_ID;
		$SCODE[$i]=$row["S_CODE"];
		$SDESCRIPTION[$i]=$row["S_DESCRIPTION"];
		$SNB[$i]=$row2["S_NB"];
		$total++;
		$i++;
	}
	
}
array_multisort($SNB, SORT_DESC,
				$SCODE,
				$SID,
				$SDESCRIPTION
);

if ( $total > 0 ) {
   echo "<p><table>";
   echo "<tr>
	  <td class='FondMenu'>";
   echo "<table cellspacing=0 border=0>";

   echo "<tr>
      	  <td class=TabHeader align=center>Section</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td class=TabHeader align=center>Nombre SMS</font></td>
      </tr>
      ";

   for ($i=0; $i < $total; $i++) {
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      
      echo "<tr bgcolor=$mycolor >
      	  <td align=left>
			<a href=upd_section.php?S_ID=".$SID[$i].">".$SCODE[$i]." ".$SDESCRIPTION[$i]."</a></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
          <td align=center><font size=1>".$SNB[$i]."</font></td>	  
      </tr>"; 
   }
   echo "</table>";
   echo "</td></tr></table>";
}
}
else {

// ===============================================
// historique
// ===============================================

$query="select p.P_ID, p.P_NOM, p.P_PRENOM, s.S_DATE, s.S_NB, s.S_TEXTE , se.S_CODE
         from pompier p, smslog s, section se
         where s.P_ID=p.P_ID
         and p.P_SECTION=se.S_ID
         and s.S_DATE <= '$year2-$month2-$day2' 
		 and s.S_DATE   >= '$year1-$month1-$day1'
	 order by ".$order;
if ( $order == 's.S_NB' || $order == 's.S_DATE' ) $query .=" desc";

$result=mysql_query($query);
$number=mysql_num_rows($result);

echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

echo "<tr>
      	  <td width=150 align=center>
      	  	<a href=histo_sms.php?order=p.P_NOM&type=histo&dtdb=".$dtdb."&dtfn=".$dtfn." class=TabHeader >
			Nom</a></font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=100 class=TabHeader align=center>
			<a href=histo_sms.php?order=se.S_CODE&type=histo&dtdb=".$dtdb."&dtfn=".$dtfn." class=TabHeader >
			Section</a></font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
    	  <td width=120 class=TabHeader align=center>
		  	<a href=histo_sms.php?order=s.S_DATE&type=histo&dtdb=".$dtdb."&dtfn=".$dtfn." class=TabHeader >
			  Date</a></font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=20 class=TabHeader align=center>
			<a href=histo_sms.php?order=s.S_NB&type=histo&dtdb=".$dtdb."&dtfn=".$dtfn." class=TabHeader >
			Nb</a></font></td>
          <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=300 class=TabHeader align=left>Texte du message</font></td>
      </tr>
      ";

$i=0;
while ($row=@mysql_fetch_array($result)) {
      $P_ID=$row["P_ID"];
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $S_DATE=$row["S_DATE"];
      $S_NB=$row["S_NB"];
      $S_TEXTE=$row["S_TEXTE"];
      $S_CODE=$row["S_CODE"];

      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      
echo "<tr bgcolor=$mycolor >
      	  <td width=150 align=center>
			<a href=upd_personnel.php?pompier=".$P_ID.">".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</a></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
          <td width=100 align=center><font size=1>".$S_CODE."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
          <td width=120 align=center><font size=1>".$S_DATE."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=20 align=center><font size=1>".$S_NB."</font></td>
      	  <td bgcolor=$mydarkcolor width=0></td>
      	  <td width=300 align=left><font size=1>".$S_TEXTE."</font></td>	  
      </tr>"; 
}
echo "</table>";
echo "</td></tr></table>";  
}

echo " <input type=submit value='fermer' onclick=\"fermerfenetre();\">";
echo "<iframe width=132 height=142 name=\"gToday:contrast:agenda.js\" id=\"gToday:contrast:agenda.js\" src=\"ipopeng.htm\" scrolling=\"no\" frameborder=\"0\" style=\"visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;\"></iframe>";
echo "</BODY>
</HTML>";
?>
