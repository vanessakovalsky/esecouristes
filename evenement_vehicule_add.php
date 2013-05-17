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

// from evenement, vehicule
if ( isset ($_GET["from"])) $from=$_GET["from"];
else $from=$_POST["from"];
if ( isset ($_GET["evenement"])) $evenement=intval($_GET["evenement"]);
else $evenement=intval($_POST["evenement"]);
if ( isset ($_GET["action"])) $action=$_GET["action"];
else $action=$_POST["action"];
if ( isset ($_GET["V_ID"])) $vehicule=intval($_GET["V_ID"]);
else $vehicule=intval($_POST["V_ID"]);
if ( isset ($_POST["km"])) $km=intval($_POST["km"]);
else $km='0';

// used for evenement_vehicule link
if ( isset ($_GET["order"])) $order=$_GET["order"];
else $order='date';
if ( isset ($_GET["date"])) $date=$_GET["date"];
else $date='FUTURE';
if ( isset ($_GET["filtervehicule"])) $filtervehicule=$_GET["filtervehicule"];
else $filtervehicule='ALL';

if ( isset($_GET['EC'])) $EC=intval($_GET['EC']);
else if ( isset($_POST['EC'])) $EC=intval($_POST['EC']);
else $EC=$evenement;

$query="select E_CHEF, E_OPEN_TO_EXT, S_ID from evenement where E_CODE=".$evenement;
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$S_ID=$row["S_ID"];
$E_CHEF=$row["E_CHEF"];
$E_OPEN_TO_EXT=$row["E_OPEN_TO_EXT"];

if ( $id <> $E_CHEF ) {
   check_all(17);
   if (($E_OPEN_TO_EXT == 0 ) and (! check_rights($id, 17, "$S_ID")) and (! check_rights($id, 15, "$S_ID"))) check_all(24);
}

?>

<SCRIPT>
function redirect(url) {
	 self.location.href = url;
}
</SCRIPT>
<?php

$query="select EV_KM as OLD_KM from evenement_vehicule 
	    where (E_CODE =".$evenement." or E_CODE=".$EC.") 
		and V_ID=".$vehicule;
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$OLD_KM=intval($row["OLD_KM"]);

if ( $action == 'km') {
   //incrémenter le kilométrage global véhicule
   $ajouter= $km - $OLD_KM;
   $query="update vehicule set V_KM = V_KM + ".$ajouter." where V_ID=".$vehicule;
   $result=mysql_query($query);
   
   $query="update vehicule set V_KM = ".$km." where V_KM < 0 and V_ID=".$vehicule;
   $result=mysql_query($query);
   
   // mettre à jour le km du véhicule
   $query="update evenement_vehicule set EV_KM=$km
	where (E_CODE =".$evenement."  or E_CODE=".$EC.")
	and V_ID=".$vehicule;
   $result=mysql_query($query);
}
elseif ( $action == 'remove') {
   //décrémenter le kilométrage global véhicule
   $query="update vehicule set V_KM = V_KM - ".$OLD_KM." where V_ID=".$vehicule;
   $result=mysql_query($query);
   
   $query="update vehicule set V_KM = 0 where V_KM < 0 and V_ID=".$vehicule;
   $result=mysql_query($query);
 
   $query="delete from evenement_vehicule
	where (E_CODE =".$evenement."  or E_CODE=".$EC.")
	and V_ID=".$vehicule;
   $result=mysql_query($query);
   
   // enlever le matériel embarqué
   $query="delete from evenement_materiel
	where (E_CODE =".$evenement."  or E_CODE=".$EC.")
	and MA_ID in (select MA_ID from materiel 
				  where V_ID=".$vehicule."
				  or MA_PARENT in (select MA_ID from materiel where V_ID=".$vehicule.")
				  )";
   $result=mysql_query($query);
}
elseif ( $action == 'demande') {
       $query="insert into evenement_vehicule (E_CODE, EH_ID, V_ID)
			select E_CODE,EH_ID, ".$vehicule."
			from evenement_horaire
			where E_CODE=".$evenement;
       $result=mysql_query($query);
       
       // engager le matériel embarqué
       $query="insert into evenement_materiel (E_CODE,MA_ID,EM_NB)
			select ".$evenement.",m.MA_ID,m.MA_NB
			from materiel m
			where MA_ID in (select MA_ID from materiel 
				  where V_ID=".$vehicule."
				  or MA_PARENT in (select MA_ID from materiel where V_ID=".$vehicule.")
				  )
            and not exists (select 1 from evenement_materiel em1 where em1.E_CODE=".$evenement." and em1.MA_ID=m.MA_ID)";
       $result=mysql_query($query);      
}

if ( $from == 'evenement' ) 
	echo "<body onload=redirect('evenement_display.php?evenement=".$evenement."&from=vehicule');>";
else
	echo "<body onload=redirect('evenement_vehicule.php?vehicule=".$filtervehicule."&date=".$date."&order=".$order."');>";
?>
