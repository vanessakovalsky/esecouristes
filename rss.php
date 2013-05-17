<?php

  # written by: Nicolas MARCHE <nico.marche@free.fr>, Jean-Pierre KUNTZ
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
/*
http://sebsauvage.net/comprendre/rss/creer.html
*/

/*
a ajouter dans le header
<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"http://www.protection-civile.org/rss.php\" />
*/
  
include_once ("config.php");


$section=(isset($_GET['id'])?intval($_GET['id']):-1);
$sections=get_family("$section");
$site = $cisname.(($section>0)?" - ".get_section_name($section):"") ;
$url=str_replace("www.", "" , $cisurl);
$url=str_replace("https://", "" , $cisurl);
$url=str_replace("http://", "" , $cisurl);
$siteurl = "http://".$url;
$siteinfo = $cisname;
$siterss = "http://".$url."/evenement_display.php?";

$flux=strtoupper(isset($_GET['f'])?"'".ereg_replace(",","','",$_GET['f'])."'":''); // Récupère le flux selon type_evenement, séparé par des virgules

if ( is_file('images/user-specific/logo.jpg'))
 	$logo=$siteurl.'/images/user-specific/logo.jpg';
else 
 	$logo=$siteurl.'/images/logo.jpg';
/*
format de date :
Tue, 9 Aug 2005 16:20:00 GMT
*/

$sql="select distinct
        e.E_CODE,
		te.TE_LIBELLE,
		concat('evenement=',e.E_CODE) rsslink, 
		date_format(e.E_CREATE_DATE,'%a, %e %b %Y %T GMT') rsspubdate,
		e.E_COMMENT2,
		e.TE_CODE,
		e.E_LIBELLE,
		e.E_LIEU rsslieu,
		e.S_ID,
		e.E_PARENT,
		e.PS_ID,
		e.TF_CODE,
		e.E_CLOSED,
		e.E_NB,
		s.S_CODE,
		s.S_DESCRIPTION
		from evenement e, evenement_horaire eh, type_evenement te, section s
 	    where e.S_ID in( $sections )
 	    and e.S_ID = s.S_ID
 	    and eh.E_CODE = e.E_CODE
		and e.TE_CODE = te.TE_CODE
		and ( TO_DAYS(NOW()) - TO_DAYS(eh.EH_DATE_DEBUT) <= 5 or eh.EH_DATE_DEBUT > NOW())
		and e.E_VISIBLE_OUTSIDE=1
		and e.E_CANCELED=0 ";
$sql.=	(($flux!="")?" and e.TE_CODE in($flux) ":"");	// Choix d'un flux particulier
$sql.=	" order by eh.EH_DEBUT asc";

$p_head = "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>
<?xml-stylesheet type=\"text/xsl\" href=\"rss.xsl\" ?>
<rss version=\"2.0\">
<channel>
<title>$site</title>
<link>$siteurl</link>
<image>
<title>$site</title>
<link>$siteurl</link>
<url>$logo</url>
</image>
<description>$siteinfo</description>
<language>fr</language>
";
$p_foot = "</channel>
</rss>";
$p_item = "";

$show_organisateur=false;
$res = mysql_query($sql);
while($rows = mysql_fetch_array($res)){
	$title = html_entity_decode(substr(strtoupper($rows['TE_LIBELLE']." - ".$rows['E_LIBELLE']),0,50));
	$link = $rows['rsslink'];
	$permalink = $link;
	$pubdate = $rows['rsspubdate'];
	$evtlieu=html_entity_decode($rows['rsslieu']);
	$comment=$rows['E_COMMENT2'];
	$organisateur=html_entity_decode($rows['S_CODE']." - ".$rows['S_DESCRIPTION']);
	$S_ID = $rows['S_ID'];
	if ( $S_ID <> $section ) $show_organisateur=true;	
	$datesheures=get_dates_heures($rows['E_CODE']);	
	$p_item .= "<item>
<title>".$title."</title>
<link>".$siterss.$link."</link>
<guid isPermaLink=\"false\">".$permalink."</guid>
<description>";
if ( $show_organisateur ) $p_item .="- organisé par: ".$organisateur;
$p_item .="\n- dates: ".$datesheures;
$p_item .="\n- lieu: ".$evtlieu;
$p_item .= "\n".$comment;
$p_item .="</description>
<pubDate>".$pubdate."</pubDate>
</item>
";
}
echo $p_head;
echo $p_item;
echo $p_foot;

?>