
//////////////////// Agenda file for CalendarXP 9.0 /////////////////
// This file is totally configurable. You may remove all the comments in this file to minimize the download size.
/////////////////////////////////////////////////////////////////////

//////////////////// Define agenda events ///////////////////////////
// Usage -- fAddEvent(year, month, day, message, action, bgcolor, fgcolor, bgimg, boxit, html);
// Notice:
// 1. The (year,month,day) identifies the date of the agenda.
// 2. In the action part you can use any javascript statement, or use " " for doing nothing.
// 3. Assign "null" value to action will result in a line-through effect(can't be selected).
// 4. html is the HTML string to be shown inside the agenda cell, usually an <img> tag.
// 5. fgcolor is the font color for the specific date. Setting it to ""(empty string) will make the fonts invisible and the date unselectable.
// 6. bgimg is the url of the background image file for the specific date.
// 7. boxit is a boolean that enables the box effect using the bgcolor when set to true.
// ** REMEMBER to enable respective flags of the gAgendaMask option in the theme, or it won't work.
/////////////////////////////////////////////////////////////////////
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

//------------------------
// les événements
//------------------------

include_once ("config.php");
check_all(0);
if (isset($_GET['pompier'])) $pompier=intval($_GET['pompier']);
else $pompier=$_SESSION['id'];
$section=get_section_of($pompier);

$query="select  te.TE_LIBELLE,e.E_CODE,e.TE_CODE,e.E_LIBELLE,e.E_LIEU, s.S_CODE,s.S_DESCRIPTION,
		TIME_FORMAT(eh.EH_DEBUT, '%k:%i') as EH_DEBUT, TIME_FORMAT(eh.EH_FIN, '%k:%i') as EH_FIN,
        DATE_FORMAT(eh.EH_DATE_DEBUT, '%d-%c-%Y') as EH_DATE_DEBUT,
        DATE_FORMAT(eh.EH_DATE_FIN, '%d-%c-%Y') as EH_DATE_FIN,
        E_CANCELED, E_CLOSED,
        TIME_FORMAT(ep.EP_DEBUT, '%k:%i') as EP_DEBUT, TIME_FORMAT(ep.EP_FIN, '%k:%i') as EP_FIN,
        DATE_FORMAT(ep.EP_DATE_DEBUT, '%d-%c-%Y') as EP_DATE_DEBUT,
        DATE_FORMAT(ep.EP_DATE_FIN, '%d-%c-%Y') as EP_DATE_FIN
        from evenement e, type_evenement te, section s, evenement_participation ep, evenement_horaire eh
        where e.TE_CODE=te.TE_CODE
        and eh.E_CODE = ep.E_CODE
        and eh.EH_ID = ep.EH_ID
        and e.E_CANCELED <> 1
        and ep.P_ID=$pompier
        and ep.E_CODE=e.E_CODE
		and e.S_ID=s.S_ID";      

$result=mysql_query($query);

while ($row=@mysql_fetch_array($result)) {
      $TE_LIBELLE=str_replace("'","",$row["TE_LIBELLE"]);
      $E_LIBELLE=str_replace("'","",$row["E_LIBELLE"]);
      $S_DESCRIPTION=str_replace("'","",$row["S_DESCRIPTION"]);
      $E_CODE=$row["E_CODE"];
      $S_CODE=$row["S_CODE"];
      $TE_CODE=$row["TE_CODE"];
      $E_LIEU=str_replace("'"," ",$row["E_LIEU"]);

      $E_CANCELED=$row["E_CANCELED"];
      $E_CLOSED=$row["E_CLOSED"];
      
      if ( $row["EP_DATE_DEBUT"] <> "" ) {
        $EH_DEBUT=$row["EP_DEBUT"];
        $EH_FIN=$row["EP_FIN"];
        $EH_DATE_DEBUT=$row["EP_DATE_DEBUT"];
        $EH_DATE_FIN=$row["EP_DATE_FIN"];      
      }
      else {
        $EH_DEBUT=$row["EH_DEBUT"];
        $EH_FIN=$row["EH_FIN"];
        $EH_DATE_DEBUT=$row["EH_DATE_DEBUT"];
        $EH_DATE_FIN=$row["EH_DATE_FIN"];
      }
      
      $P1=explode("-",$EH_DATE_DEBUT); 
      if ($EH_DATE_FIN == '' ) $P2=explode("-",$EH_DATE_DEBUT);
      else $P2=explode("-",$EH_DATE_FIN);
      $DATE1=mktime(0,0,0,$P1[1],$P1[0],$P1[2]);
      $DATE2=mktime(0,0,0,$P2[1],$P2[0],$P2[2]);
      
	  if ( $E_CANCELED == 1 ) {
	   	$theclass = 'MsgRed';
	   	$theinfo= 'ANNULE';
	  }
	  else {
	   	$theclass = 'MsgBlue';
		$theinfo= 'INSCRIT';	   	
	  }      
      $N=0;
	  while ($DATE2 >= $DATE1) {
	    $YEAR=date("Y",$DATE1);
      	$MONTH=date("n",$DATE1);
      	$DAY=date("j",$DATE1);
        $onmousedown="parent.location=\\\"evenement_display.php?evenement=".$E_CODE."&from=scroller\\\"";
        //$onmousedown="parent.location=evenement_display.php?evenement=1";
        //echo "fAppendEvent(".$YEAR.",".$MONTH.",".$DAY.",'','',null,null,'',null,'<div align=center class=MsgBoard  title='".$TE_LIBELLE." ".$E_LIBELLE." à ".$E_LIEU." à ".$E_DEBUT."'><a href='#' onmousedown='".$onmousedown."'><img src=images/".$TE_CODE."small.gif border=0>".substr($E_LIBELLE,0,6).".</a></div>\");";
        
	    echo "fAppendEvent(".$YEAR.",".$MONTH.",".$DAY.",'','',null,null,'',null,'<div align=left class=".$theclass." title=\'".$theinfo.": ".$TE_LIBELLE." ".$E_LIBELLE." à ".$E_LIEU." de ".$EH_DEBUT." à ".$EH_FIN." organisé par ".$S_DESCRIPTION."\'><table><tr><td><img src=images/".$TE_CODE."small.gif border=0></td><td> <font face=$fontfamily size=1><a href=\'#\' onmousedown=\'".$onmousedown."\' >".$TE_CODE."</font></a></td></tr></table></div>');";
	     $N++;
	     $DATE1=mktime(0,0,0,$P1[1],$P1[0]+$N,$P1[2]);
      }
}

//------------------------
// les gardes attribuées
//------------------------

$query="select distinct DATE_FORMAT(pg.PG_DATE, '%Y') as YEAR, DATE_FORMAT(pg.PG_DATE, '%c') as MONTH,
        DATE_FORMAT(pg.PG_DATE, '%e') as DAY,pg.TYPE, p.EQ_ID, e.EQ_NOM
        from planning_garde pg, planning_garde_status pgs, poste p, equipe e
        where pgs.PGS_STATUS='OK'
        and p.PS_ID=pg.PS_ID
        and p.EQ_ID=e.EQ_ID
        and pgs.PGS_YEAR=DATE_FORMAT(pg.PG_DATE, '%Y')
        and pgs.PGS_MONTH=DATE_FORMAT(pg.PG_DATE, '%c')
	and pg.P_ID=".$pompier."
	order by pg.TYPE";
$result=mysql_query($query);

while ($row=@mysql_fetch_array($result)) {
      $YEAR=$row["YEAR"];
      $MONTH=$row["MONTH"];
      $DAY=$row["DAY"];
      $TYPE=$row["TYPE"];
      $EQ_ID=$row["EQ_ID"];
      $EQ_NOM=str_replace("'","",$row["EQ_NOM"]);
      $onmousedown="parent.location=\\\"garde_jour.php?month=".$MONTH."&year=".$YEAR."&day=".$DAY."\\\"";
      if ( $EQ_ID == 1 ) $theclass='MsgPurple';
      elseif ( $EQ_ID == 2 ) $theclass='MsgGold';
      elseif ( $EQ_ID == 3 ) $theclass='MsgIvory';
      else $theclass='MsgBlue';
        if (( $TYPE == 'J' ) or ( $TYPE == 'N' )) {
      	 $query2="select count(*) as NB from planning_garde where P_ID=".$pompier."
	        and PG_DATE='".$YEAR."-".$MONTH."-".$DAY."' and TYPE in ('N','J')";
         $result2=mysql_query($query2);
      	 $row2=mysql_fetch_array($result2);
      	 $NB=$row2["NB"];
      	 if ( $NB == 2 ){
      	    if ( $TYPE == 'J' )
      	    echo "fAppendEvent(".$YEAR.",".$MONTH.",".$DAY.",\"\",\"\",null,null,\"\",null,\"<div align=left class=".$theclass." title='".$EQ_NOM." ".$DAY."-".$MONTH."-".$YEAR."'><a href='#' onmousedown='".$onmousedown."'>".$EQ_NOM." 24h</a></div>\");
     		";
         }
         else {
	    	if ( $TYPE == 'J' ) $info='Jour';
	    	else  $info='Nuit';
	    	$descriptif=$EQ_NOM." ".$info;
	    	echo "fAppendEvent(".$YEAR.",".$MONTH.",".$DAY.",\"\",\"\",null,null,\"\",null,\"<div align=left class=".$theclass." title='".$EQ_NOM." ".$DAY."-".$MONTH."-".$YEAR."'><a href='#' onmousedown='".$onmousedown."'>".$descriptif."</a></div>\");
     		";
        }
      }
}

//------------------------
// les absences enregistrées
//------------------------
$query="select ti.TI_LIBELLE as TYPE, DATE_FORMAT(i.I_DEBUT, '%d-%c-%Y') as DEBUT, DATE_FORMAT(i.I_FIN, '%d-%c-%Y') as FIN
        from  indisponibilite i, type_indisponibilite ti
        where i.P_ID=".$pompier."
	and i.TI_CODE=ti.TI_CODE";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
       $P1=explode("-",$row["DEBUT"]);
       $P2=explode("-",$row["FIN"]);
       $TYPE=$row["TYPE"];
       $DATE1=mktime(0,0,0,$P1[1],$P1[0],$P1[2]);
       $DATE2=mktime(0,0,0,$P2[1],$P2[0],$P2[2]);
       $N=0;
	while ($DATE2 >= $DATE1) {
	    $YEAR=date("Y",$DATE1);
      	$MONTH=date("n",$DATE1);
      	$DAY=date("j",$DATE1);
        echo "fAppendEvent(".$YEAR.",".$MONTH.",".$DAY.",'','',null,null,'',null,'<div align=left class=MsgAbsence>Absence ".$TYPE."</div>');
	";
	$N++;
	$DATE1=mktime(0,0,0,$P1[1],$P1[0]+$N,$P1[2]);
        }
}


?>

///////////// Dynamic holiday calculations /////////////////////////
function fHoliday(y,m,d) {
	var rE=fGetEvent(y,m,d), r=null;
	// you may have sophisticated holiday calculation set here, following are only simple examples.
	if (m==1&&d==1)
		r=[" Jan 1, "+y+" \n Bonne Année! ",gsAction,"orange","red"];
	else if (m==12&&d==25)
		r=[" Dec 25, "+y+" \n Joyeux Noël! ",gsAction,"orange","red"];
	else if (m==5&&d==1)
		r=[" Mai 1, "+y+" \n Fête du travail! ",gsAction,"orange","red"];
	else if (m==5&&d==8)
		r=[" Mai 1, "+y+" \n 8 Mai 1945! ",gsAction,"orange","red"];
	else if (m==7&&d==14)
		r=[" Jul 14, "+y+" \n Fête nationale ",gsAction,"orange","red"];
	else if (m==11&&d==11)
		r=[" Nov 11, "+y+" \n Armistice 1918 ",gsAction,"orange","red"];	
	return rE?rE:r;
}


