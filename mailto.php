<?php

  # written by: Nicolas MARCHE, Jean-Pierre KUNTZ
  # contact: nico.marche@free.fr
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
check_all(2);
writehead();
$destid=$_GET["destid"];

$MailTo="";
$destinataires=explode(",", $destid);
$m =  count($destinataires);
for($i=0; $i < $m ; $i++){
    $matricule = $destinataires[$i];
    if ( $matricule <> "" ) {
        $query="select P_EMAIL 
			        from pompier 
				    where P_EMAIL <>'' 
					and P_OLD_MEMBER = 0
					and P_ID='".$matricule."'";
       	$result=mysql_query($query);
       	if ( mysql_num_rows($result) > 0 ) {
       			$row=@mysql_fetch_array($result);
       			$MailTo .= $row['P_EMAIL'].";";
       	}
    }
}

echo "Ouverture de votre logiciel de messagerie";
if ( $MailTo <> "" ) {
	$MailTo=substr($MailTo,0,strlen($MailTo) - 1);
	$Subject = "[".$cisname."] message au personnel";
	echo "<body onload='parent.location=\"mailto:".$MailTo."?subject=".$Subject."\";javascript:history.back(1);'>";
}
else 
    echo "<body onload='javascript:history.back(1);'>";
?>
    