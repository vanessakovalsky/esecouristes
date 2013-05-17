<?php
   #Un flux RSS pour afficher les événements publics

  # written by: Nicolas MARCHE, Jean-Pierre KUNTZ
  # contact: nico.marche@free.fr
  # project: eBrigade
  # homepage: http://sourceforge.net/projects/ebrigade/
  # version: 2.5

  # Copyright (C) 2004, 2010 Nicolas MARCHE
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
  
require_once("config.php");
check_all(0);

$sectionname="$cisname";
$mysection=0;

$tmpics=(isset($_GET['ics'])?true:false); // sauvegarde dans répertoire local ou non
if (isset ($_GET["section"])){
   $sectionname=get_section_name(intval($_GET["section"]))." (".get_section_code(intval($_GET["section"])).")";
   $mysection=$_GET["section"];
   $section = $mysection;
   if ( get_level($mysection) >= $nbmaxlevels -1 ){
   		$section=get_section_parent($mysection);
  } 
 }else{	
   if(isset($_SESSION['SES_SECTION'])){
   $mysection=$_SESSION['SES_SECTION'];
   $section = $mysection;
   if ( get_level($mysection) >= $nbmaxlevels -1 ){
   		$section=get_section_parent($mysection);
  }
  }else{
		$section=0;
  }
}
$list=$section;
//echo "<p>$cisurl</p>";
$site = "$cisname $sectionname";//.(isset($_GET['section'])?" - ".get_section_name($_GET['section']):"");
$siteurl = "http://$cisurl";//"http://adpc90.free.fr";
$logorss = "";//http://$cisurl/logorss.gif";
$siteinfo = "Les prochains événements de $site";
$siterss = "http://$cisurl/evenement_display.php?";

/*-------------------------------- */
$p_head = "<?xml version=\"1.0\" encoding=\"ISO-8859-15\" ?>
<rss version=\"2.0\"  xmlns:atom=\"http://www.w3.org/2005/Atom\">
<channel>
<title>$site</title>
<link>$siteurl</link>
<image>
<title>$site</title>
<url>$logorss</url>
<link>$siteurl</link>
</image>
<description>$siteinfo</description>
<language>fr</language>
<atom:link href=\"$siteurl/rss.php\" rel=\"self\" type=\"application/rss+xml\" />
";
$p_foot = "</channel>
</rss>";
$p_item = "";

$sql = "select e.e_code id, 
date_format(e.e_date_debut,'%d-%m-%Y') dtdb, 
e.e_debut, 
date_format(e.e_date_fin,'%d-%m-%Y') dtfn, 
e.e_fin, 
e.e_lieu lieu, 
e.e_comment comment, 
e.te_code code, 
e.e_libelle libelle,
date_format(e.E_CREER_DATE,'%a, %e %b %Y %T GMT') pubdate
from evenement e
where e.e_date_debut >= curdate()-10
AND e.s_id in ($list)
AND e.e_public=1
AND e.e_canceled=0"; // n'afficher que les événements "publics" non annulés

$res = mysql_query($sql);
echo (mysql_errno()>0?"<p>$sql</p>Erreur : ".mysql_error():"");
while($rows = mysql_fetch_array($res)){
$title = substr($rows['libelle'],0,50);
$link = "evenement=".$rows['id'];//."&from=rss";
$permalink = $link;//$rows[2];
$pubdate = $rows['pubdate'];
$evtdtdb=$rows['dtdb'];
$evtlieu=$rows['lieu'];
$evtdtfn=$rows['dtfn'];
$evttype=$rows['code'];
$description = $evttype." du ".($rows['dtdb']!=$rows['dtfn']?$rows['dtdb']." au ".$rows['dtfn']:$rows['dtdb']) ;
$p_item .= "<item>";
$p_item .= "<title>$title...</title>";
$p_item .= "<link>$siterss$link</link>";
$p_item .= "<guid isPermaLink=\"false\">$permalink</guid>";
$p_item .= "<description>$description</description>";
$p_item .= "<pubDate>$pubdate</pubDate>";
$p_item .= "</item>";
$p_item .= "\n";

}
echo $p_head;
echo $p_item;
echo $p_foot;
?>