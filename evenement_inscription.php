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
check_all(39);
$id=$_SESSION['id'];
$evenement=intval($_GET["evenement"]);
$action=$_GET["action"];

?>

<SCRIPT>
function redirect(url) {
	 self.location.href = url;
}
</SCRIPT>
<?php

$query="select E_CHEF, S_ID from evenement where E_CODE=".$evenement;
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$E_CHEF= $row["E_CHEF"];
$S_ID=$row["S_ID"];

if (isset ($_GET["P_ID"])) $P_ID=intval($_GET["P_ID"]);
else $P_ID=$id;
$section_of=get_section_of($P_ID);
$statut_of=get_statut($P_ID); 

$statut='';
if (isset ($_GET["statut"])) {
	if ( $_GET["statut"] == 'SAL' ) $statut='SAL';
	if ( $_GET["statut"] == 'BEN' ) $statut='BEN';
}

if (isset ($_GET["value"])) $value=mysql_real_escape_string($_GET["value"]);
else $value=0;

if (isset ($_GET["EP_FLAG1"])) $EP_FLAG1=intval($_GET["EP_FLAG1"]);
else $EP_FLAG1=0;

$granted= false;
if ( check_rights($_SESSION['id'], 15, "$section_of") 
	or  check_rights($_SESSION['id'], 15, "$S_ID")
	or ( $id == $E_CHEF ))
$granted = true;

// cas inscription d'un salarié, préciser sous quel statu, bénévole ou salarié
if ( $statut_of == 'SAL' and $statut == '' and $action == 'inscription') {
	$link="evenement_inscription.php?evenement=".$evenement."&action=inscription&P_ID=".$P_ID;
	$message=ucfirst(get_prenom($P_ID))." ".strtoupper(get_nom($P_ID))." <br>fait partie du personnel salarié.<br>";
	$message .="Souhaitez vous l'inscrire en tant que:<br>";
	$message .="<a href=".$link."&statut=SAL>$myspecialfont Salarié(e)</font></a> ou ";	
	$message .="<a href=".$link."&statut=BEN>$myspecialfont Bénévole</font></a><br>";
	write_msgbox("Choix statut",$question_pic,$message,30,30);
	exit;
}

if ( $action == 'desinscription') {
   // on peut toujours se desinscrire le jour de l'inscription
   if ( $id == $P_ID ) {
        $query="select DATEDIFF(NOW(), ep.EP_DATE) as NB_DAYS 
   			from evenement_participation ep, evenement e
   			where ep.E_CODE = e.E_CODE
			and ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")
			and ep.P_ID=".$id;
   		$r1=mysql_query($query);
   		$num=mysql_num_rows($r1);
   		if ( $num > 0 ) {
  	 		$row=mysql_fetch_array($r1);
     		if ( $row["NB_DAYS"] < 1 ) $granted=true;
   		}
   }
   if (( check_rights($_SESSION['id'], 10)) or ( $granted )) {
      if ( isset($_GET['EC'])) $EC=$_GET['EC'];
      else $EC=$evenement;
      
      $query = "delete from personnel_formation where (E_CODE =".$evenement." or E_CODE=".$EC.")
			and P_ID=".$P_ID;
      $result=mysql_query($query);
      
   	  $query="delete from evenement_participation
			where (E_CODE =".$evenement." or E_CODE=".$EC.")
			and P_ID=".$P_ID;
	  insert_log('DESINSCP', $P_ID, "", $evenement);
   }
   else
   	  $query="select 'exception raised'";
}
elseif ( $action == 'inscription') {
    if ( check_rights($_SESSION['id'], 10, $section_of) or ( $granted ) or ( $id == $P_ID)) {
		if ( $statut_of == 'SAL' and $statut == 'SAL') $flag1=1;
		else $flag1=0;
		insert_log('INSCP', $P_ID, "", $evenement);
    	$query="insert into evenement_participation (E_CODE, EH_ID, P_ID, EP_DATE, EP_BY, EP_FLAG1)
		select E_CODE,EH_ID, ".$P_ID.", now() ,".$id.",".$flag1."
		from evenement_horaire
		where E_CODE=".$evenement;
	}
	else
   	  $query="select 'exception raised'";
}
else {
	if (( $action == "close" ) and ( $granted )) {
   		$query="update evenement set E_CLOSED=1 where E_CODE=".$evenement." or E_PARENT=".$evenement;
	}
	elseif (( $action == "open" ) and ( $granted )) {
   		$query="update evenement set E_CLOSED=0 where E_CODE=".$evenement." or E_PARENT=".$evenement;
	}
	elseif (( $action == "nb1" ) and ( $granted )) {
   		$query="update evenement set E_NB1=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb2" ) and ( $granted )) {
   		$query="update evenement set E_NB2=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb3" ) and ( $granted )) {
   		$query="update evenement set E_NB3=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb4" ) and ( $granted )) {
   		$query="update evenement set E_NB1_1=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb5" ) and ( $granted )) {
   		$query="update evenement set E_NB1_2=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb6" ) and ( $granted )) {
   		$query="update evenement set E_NB1_3=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb7" ) and ( $granted )) {
   		$query="update evenement set E_NB1_4=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb8" ) and ( $granted )) {
   		$query="update evenement set E_NB1_5=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb9" ) and ( $granted )) {
   		$query="update evenement set E_NB1_6=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb10" ) and ( $granted )) {
   		$query="update evenement set E_NB2_1=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "nb11" ) and ( $granted )) {
   		$query="update evenement set E_NB2_2=$value where E_CODE=".$evenement;
	}
	elseif (( $action == "poste" ) and ( $granted )) {
   		$query="update evenement set PS_ID=".$value." where E_CODE=".$evenement;
	}
	elseif (( $action == "detail" ) and ( $granted or $id == $P_ID  or check_rights($_SESSION['id'], 10, $section_of))) {
		if ( isset($_GET["detail"])) 
			$detail=strip_tags(mysql_real_escape_string(str_replace("\"","",$_GET["detail"])));
		else 
			$detail='';
		$km='';
		$updkm="EP_KM = null";
		$auditkm="km non renseigné";
		if ( isset($_GET["km"])) {
		   $km=$_GET["km"];
		   if ($km <> '') {
		    	$updkm="EP_KM = ".intval($km);
		    	$auditkm=intval($km)." km";
		   }
		}
   		$query="update evenement_participation set EP_COMMENT=\"".$detail."\", ".$updkm." , EP_FLAG1=".$EP_FLAG1." 
		   		where P_ID=".$P_ID."
				and E_CODE in (select E_CODE from evenement 
								where  E_CODE=".$evenement." or E_PARENT = ".$evenement.")";
		insert_log('DETINSCP', $P_ID, $detail." ".$auditkm, $evenement);
	}
	elseif (( $action == "fonction" ) and ( $granted )) {
		if ( isset($_GET["fonction"])) $fonction=intval($_GET["fonction"]);
		else $fonction=0;
		
		$queryZ="select E_CODE from evenement 
				where E_PARENT = ".$evenement;
		$resultZ=mysql_query($queryZ);
		$evts=$evenement;
		while ($rowZ=@mysql_fetch_array($resultZ)) {
		 	$evts .= ",".$rowZ["E_CODE"];
		}
		
		if ( $fonction == 0 ) 
   			$query="update evenement_participation set TP_ID=null
		   			where P_ID=".$P_ID." and E_CODE in (".$evts.")";
		else {		
   			$query="update evenement_participation set TP_ID=".$fonction."
		   			where P_ID=".$P_ID." and E_CODE in (".$evts.")";
			if ( $log_actions == 1 ) {					
				$queryf="select TP_LIBELLE from type_participation where TP_ID=".$fonction;
				$resultf=mysql_query($queryf);
				$rowf=@mysql_fetch_array($resultf);
				$TP_LIBELLE=$rowf["TP_LIBELLE"];
				if ( $TP_LIBELLE == "" ) $TP_LIBELLE="Pas de fonction";
				insert_log('FNINSCP', $P_ID, $TP_LIBELLE, $evenement);
			}
		}
	}
	elseif (( $action == "equipe" ) and ( $granted )) {
		if ( isset($_GET["equipe"])) $equipe=intval($_GET["equipe"]);
		else $equipe=0;
		
		$queryZ="select E_CODE from evenement 
				where E_PARENT = ".$evenement;
		$resultZ=mysql_query($queryZ);
		$evts=$evenement;
		while ($rowZ=@mysql_fetch_array($resultZ)) {
		 	$evts .= ",".$rowZ["E_CODE"];
		}
		
		if ( $equipe == 0 ) 
   			$query="update evenement_participation set EE_ID=null
		   			where P_ID=".$P_ID." and E_CODE in (".$evts.")";
		else {		
   			$query="update evenement_participation set EE_ID=".$equipe."
		   			where P_ID=".$P_ID." and E_CODE in (".$evts.")";
			if ( $log_actions == 1 ) {					
				$queryf="select EE_NAME from evenement_equipe where E_CODE =".$evenement." and  EE_ID=".$equipe;
				$resultf=mysql_query($queryf);
				$rowf=@mysql_fetch_array($resultf);
				$EE_NAME=$rowf["EE_NAME"];
				if ( $EE_NAME == "" ) $EE_NAME="Pas d'équipe";
				insert_log('EEINSCP', $P_ID, $EE_NAME, $evenement);
			}
		}
	}
	elseif (( $action == "tf" ) and ( $granted )) {
		if ( $value == 'NULL') $query="update evenement set TF_CODE=null where E_CODE=".$evenement;
   		else $query="update evenement set TF_CODE='".$value."' where E_CODE=".$evenement;
	}
	elseif (( $action == "cancel" ) and ( $granted )) {
	 	$renfort = intval($_GET["renfort"]);
   		$query = "update evenement set E_CANCELED=1, E_CANCEL_DETAIL='Renfort refusé', E_PARENT=null  
		where E_CODE=".$renfort;
	}
	elseif (( $action == "responsable" ) and ( $granted )) {
		if ( $P_ID == 0 ) $P_ID='null';
   		$query = "update evenement set E_CHEF=".$P_ID." where E_CODE=".$evenement;
	}
	// add security
	if ($id <> $P_ID and $action <> "detail")
		if (! $granted ) 
   	  		$query="select 'exception raised'";
}

$result=mysql_query($query);

// cleanup: nobody with P_ID = 0 can be enrolled
$query="delete from evenement_participation where P_ID = '0'";
$result=mysql_query($query);

if (( $action == 'desinscription' ) and (isset ($_GET["P_ID"]))) {
	echo "<body onload=redirect('evenement_notify.php?evenement=".$evenement."&action=desinscrit&P_ID=".$_GET["P_ID"]."');>";
}
elseif ( $action == 'cancel' ) {
 	echo "<body onload=redirect('evenement_notify.php?evenement=".$renfort."&action=canceled');>";
}
elseif ( $action == 'inscription' ) {
	echo "<body onload=redirect('evenement_notify.php?evenement=".$evenement."&action=inscription&P_ID=".$P_ID."');>";
}
elseif ( $action == 'poste' or $action == 'tf' or $action == 'responsable') {
	echo "<body onload=redirect('evenement_display.php?evenement=".$evenement."');>";
}
elseif ( $action == 'diplomes' ) {
	echo "<body onload=redirect('evenement_display.php?evenement=".$evenement."&from=formation');>";
}
elseif ( $action == 'detail' or $action == 'fonction' or $action == 'equipe') {
	echo "<body onload=redirect('evenement_display.php?evenement=".$evenement."&from=inscription');>";
}
else {
	echo "<body onload=redirect('evenement_display.php?evenement=".$evenement."');>";
}
?>
