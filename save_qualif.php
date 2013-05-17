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
$P_ID=intval($_GET["pompier"]);
check_all(0);
$id=$_SESSION['id'];

if ( $id <> $P_ID ) {
	// permission de modifier les compétences?
	$competence_allowed=false;
	$query="select distinct F_ID from poste order by F_ID";
	$result=mysql_query($query);
	while ($row=@mysql_fetch_array($result)) {
		if (check_rights($_SESSION['id'], $row['F_ID'], get_section_of("$P_ID")) ) {
			$competence_allowed=true;
			break;
		}
	}
	if ( ! $competence_allowed ) {
		check_all(4);
		if (!  check_rights($_SESSION['id'], 4, get_section_of("$P_ID"))) check_all(24);
	}
}

if (isset ($_GET["from"])) $from=$_GET["from"];
else $from="personnel";
?>

<html>
<SCRIPT language=JavaScript>

function redirect1(pid) {
     url="upd_personnel.php?pompier="+pid+"&from=qualif";
     self.location.href=url;
}

function redirect2() {
     url="qualifications.php?pompier=0";
     self.location.href=url;
}

</SCRIPT>

<?php
include_once ("config.php");

$S_ID = get_section($P_ID);
$query="select s.S_EMAIL, s.S_EMAIL2, sf.NIV
		from section_flat sf, section s
		where s.S_ID = sf.S_ID
		and sf.NIV < 4
		and s.S_ID in (".$S_ID.",".get_section_parent($S_ID).")
		order by sf.NIV ";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$S_EMAIL=$row["S_EMAIL"];
$S_EMAIL2=$row["S_EMAIL2"];
if ( $S_EMAIL2 <> "" ) $secretariat=true;
else $secretariat=false;


$query="select p.P_NOM, p.P_PRENOM, p.P_STATUT, p.C_ID , c.C_NAME
		from pompier p
		left join company c on p.C_ID = c.C_ID
		where P_ID=".$P_ID;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$P_NOM=$row["P_NOM"];
$C_ID=$row["C_ID"];
$C_NAME=$row["C_NAME"];
$P_PRENOM=$row["P_PRENOM"];
$P_STATUT=$row["P_STATUT"];


//=====================================================================
// enregistrer les qualifications saisies
//=====================================================================

$query="select distinct PS_ID, DESCRIPTION, PS_AUDIT, PS_USER_MODIFIABLE from poste";
$result=mysql_query($query);

while ($row=@mysql_fetch_array($result)) {
      	 $PS_ID=$row["PS_ID"];
      	 $PS_AUDIT=$row["PS_AUDIT"];
      	 $DESCRIPTION=$row["DESCRIPTION"];
      	 $PS_USER_MODIFIABLE=$row["PS_USER_MODIFIABLE"];
      	 $exp="null";

		 $month=''; $year='';
		 if (isset($_GET[$PS_ID])) {
      	    if ($_GET[$PS_ID] >= "1" ) {		 	
  	             	if (isset($_GET["exp_".$PS_ID])) { 
	    		 	 	$exp=$_GET["exp_".$PS_ID];
  	             		if (( $exp <> "null" ) and ( $exp <> '' )){  	             			
  	             			$special = preg_split('/\//',$exp);
  	             			$day=$special[0];
  	             			$month=$special[1];
  	             			$year=$special[2];
  	             		}
  	             	}
  	             	
					$query2="select count(*) as NB from qualification
					 		where PS_ID=".$PS_ID." and P_ID=".$P_ID;
	    		 	$result2=mysql_query($query2);
	    		 	$row2=@mysql_fetch_array($result2);
	    		 	$NB=$row2["NB"];

	    		 	// new qualification
	    		 	if ( $NB == 0 ) {
	       	   			if (( $exp <> '') and ( $exp <> "null")) {
							$expdate=$year."-".$month."-".$day;
					  		$query2="insert into  qualification (P_ID, PS_ID, Q_VAL, Q_EXPIRATION, 
							  		Q_UPDATED_BY, Q_UPDATE_DATE)
	      		  			values (".$P_ID.",".$PS_ID.",". $_GET[$PS_ID].",'".$expdate."',
									".$id.", NOW())";
								
						}
	      		  		else {
	      		  			$query2="insert into  qualification (P_ID, PS_ID, Q_VAL,
									Q_UPDATED_BY, Q_UPDATE_DATE)
	      		  			values (".$P_ID.",".$PS_ID.",". $_GET[$PS_ID].",
									".$id.", NOW())";
							$expdate="";
						}
	      		  		$result2=mysql_query($query2);
						
						if ($log_actions == 1) {
							$query1="select TYPE from poste where PS_ID=".$PS_ID;
							$result1=mysql_query($query1);
							$row1=@mysql_fetch_array($result1);
							insert_log("ADQ",$P_ID, $row1["TYPE"]." ".$expdate);
						}
      	            	// audit notification
      	            	if ( $PS_AUDIT == 1 ) {
      	               	  $destid=get_granted(33,"$S_ID",'parent','yes');
      	               	  $n=ucfirst($P_PRENOM)." ".strtoupper($P_NOM);
      	               
      	                  $subject  = "Nouvelle qualification pour - ".$n." : ".$DESCRIPTION;
      	                  $message  = "Bonjour,\n";
      	                  $message .= "Pour information, ";
      	                  if ( $P_STATUT == 'EXT' ) {
      	                     $message .= $n." personnel externe";
      	                     if ( $C_ID > 0 ) $message .=" de ".$C_NAME.",";
							 $message .= "\nrattaché à la section ".get_section_code($S_ID)."\n";
      	                  }
      	                  else
					         $message .= $n." de la section ".get_section_code($S_ID)."\n";
					      $message .= "est maintenant qualifié(e) pour la compétence ".$DESCRIPTION."\n";
      	                  $message .= "à partir du ".date("d/m/Y")." à ".date("H:i")."\n";
      	                  if ($month <> '')
      	                  	$message .= "jusqu'au ".$day."/".$month."/".$year."\n";
      	                  else 
      	                  	$message .= "sans limitation de durée.\n";
		
      	                  $nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );
      	                  
      	                  if ( $secretariat ) {
							$nb2 = mysendmail2("$S_EMAIL2" , $_SESSION['id'] , "$subject" , "$message" );
						  }
      	               }
  	               }
  	               // only update on a qualification
  	               else {
  	                    // change 1 or 2 if needed
  	                	$query2="update qualification
  	                			set Q_VAL=".$_GET[$PS_ID]."
  	                			where P_ID = ".$P_ID."
							 	and PS_ID = ".$PS_ID;
						$result2=mysql_query($query2);
						$updated=mysql_affected_rows();
						if ($updated){
							// audit change
							$query2="update qualification
  	                			set Q_UPDATED_BY=".$id.", Q_UPDATE_DATE=NOW()
  	                			where P_ID = ".$P_ID."
							 	and PS_ID = ".$PS_ID;
							$result2=mysql_query($query2);
						}
  	                 
  	                 	if (( $exp <> "null" ) and (isset($_GET["exp_".$PS_ID]))) {
  	               			// change expiration on existing qualification
  	               			if ($_GET["exp_".$PS_ID] == '') {
  	               				$query2="update qualification
					   	     	set Q_EXPIRATION = null
							 	where P_ID = ".$P_ID." and PS_ID = ".$PS_ID;
								$expdate="";
							}
  	               			else {
							    $expdate=$year."-".$month."-".$day;
  	                			$query2="update qualification
					   	     	set Q_EXPIRATION = '".$expdate."'
							 	where P_ID = ".$P_ID." and PS_ID = ".$PS_ID;
							}
							$result2=mysql_query($query2);				
			
							$updated=mysql_affected_rows();
							
							if ($updated){
								// audit change
								$query2="update qualification
  	                			set Q_UPDATED_BY=".$id.", Q_UPDATE_DATE=NOW()
  	                			where P_ID = ".$P_ID."
							 	and PS_ID = ".$PS_ID;
								$result2=mysql_query($query2);
								if ($log_actions == 1) {
									$query1="select TYPE from poste where PS_ID=".$PS_ID;
									$result1=mysql_query($query1);
									$row1=@mysql_fetch_array($result1);
									insert_log("UPDQ",$P_ID, $row1["TYPE"]." ".$expdate);
								}
							}
							// audit notification
							if (( $updated == 1 ) and ( $PS_AUDIT == 1 )) {
      	               	  		$destid=get_granted(33,"$S_ID",'parent','yes');
      	               	  		$n=ucfirst($P_PRENOM)." ".strtoupper($P_NOM);
      	               
      	                  		$subject  = "Nouvelle date d'expiration d'une qualification pour - ".$n." : ".$DESCRIPTION;
      	                  		$message  = "Bonjour,\n";
					      		$message .= "Pour information, ";
      	                  		if ( $P_STATUT == 'EXT' ) {
      	                     		$message .= $n." personnel externe";
      	                     		if ( $C_ID > 0 ) $message .=" de ".$C_NAME.",";
							 		$message .= "\nrattaché à la section ".get_section_code($S_ID)."\n";
      	                  		}
      	                  		else
					         		$message .= $n." de la section ".get_section_code($S_ID)."\n";
					      		$message .= "était déjà qualifié(e) pour la compétence ".$DESCRIPTION.".\n";
      	                  		$message .= "La date d'expiration de cette qualification a été modifiée.\n";
      	                  		if ($month <> '')
      	                  			$message .= "La nouvelle date d'expiration est le ".$day."/".$month."/".$year.".\n"; 
      	                  		else
      	                  			$message .= "Il n'y a plus de limitation de durée.\n";
		
      	                  		$nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );
      	                  		
      	                  		if ( $secretariat ) {
									$nb2 = mysendmail2("$S_EMAIL2" , $_SESSION['id'] , "$subject" , "$message" );
								}
							}
						}
				   }
	    }
	    else {
	     	$query2="delete from qualification where PS_ID=".$PS_ID." and P_ID=".$P_ID;
      	    $result2=mysql_query($query2);
	    }
	  }
}

specific_post_update($P_ID);

if ( $from == 'personnel' )
echo "<body onload='redirect1(\"".$P_ID."\")'>";
else
echo "<body onload='redirect2()'>";
?>
