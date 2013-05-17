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
$evenement=intval($_POST["evenement"]);
if ( isset($_POST["expiration"])) $expiration=mysql_real_escape_string($_POST["expiration"]);
else $expiration="";
if ( isset($_POST["comment"])) $comment=mysql_real_escape_string(str_replace("\"","",$_POST["comment"]));
else $comment="";
writehead();
?>
<SCRIPT language=JavaScript>

function redirect1(url) {
	 self.location.href = url;
}

</SCRIPT>
</head>
<?php

$query="select e.S_ID, e.PS_ID, p.TYPE, DATE_FORMAT(eh.EH_DATE_DEBUT, '%d-%m-%Y' ) EH_DATE_DEBUT, 
		DATE_FORMAT(eh.EH_DATE_FIN, '%d-%m-%Y' ) EH_DATE_FIN, e.TF_CODE, e.E_LIEU, e.E_CHEF, tf.TF_LIBELLE,
		p.PS_NATIONAL, p.PS_PRINTABLE, eh.EH_ID
        from evenement e, poste p, type_formation tf, evenement_horaire eh
	    where p.PS_ID=e.PS_ID
	    and e.E_CODE = eh.E_CODE
	    and tf.TF_CODE = e.TF_CODE
		and e.E_CODE=".$evenement." order by eh.EH_ID";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
    $PS_ID=$row["PS_ID"];
    $PS_NATIONAL=$row["PS_NATIONAL"];
    $PS_PRINTABLE=$row["PS_PRINTABLE"];
    $S_ID=$row["S_ID"];
    $TYPE=$row["TYPE"];
    $TF_CODE=$row["TF_CODE"];
    $TF_LIBELLE=$row["TF_LIBELLE"];
    if ($row["EH_ID"] == 1) $EH_DATE_DEBUT=$row["EH_DATE_DEBUT"];
    $EH_DATE_FIN=$row["EH_DATE_FIN"];
    $E_LIEU=$row["E_LIEU"];
    $E_CHEF=$row["E_CHEF"];
    if ( $EH_DATE_FIN == "" ) $EH_DATE_FIN=$EH_DATE_DEBUT;
}

if (( $E_CHEF <> $id ) and ( ! check_rights($id, 4, "$S_ID"))) check_all(24);

// cas pas de responsable désigné, choisir un formateur
if ( $E_CHEF == '' ) {
	$query2="select ep.P_ID, ep.TP_ID, tp.TP_NUM
		 from evenement_participation ep, type_participation tp
		 where ep.TP_ID=tp.TP_ID
		 and tp.INSTRUCTOR = 1
		 and ep.TP_ID > 0
		 and ep.EH_ID=1
		 and ep.E_CODE=".$evenement."
		 order by tp.TP_NUM desc, ep.P_ID desc";
	$result2=mysql_query($query2);
	$row2=@mysql_fetch_array($result2);
	$E_CHEF=$row2["P_ID"];
}

//=====================================================================
// enregistrer les diplomes saisis
//=====================================================================

// d'abord on efface les formations 
$query="delete from personnel_formation where E_CODE=".$evenement;
$result=mysql_query($query);

// ensuite on réenregistre
$listpersonnel=""; $destid="";
if ($E_CHEF <> "" ) $resp=ucfirst(get_prenom($E_CHEF))." ".strtoupper(get_nom($E_CHEF));
else $resp="";

$ignore="";
while (list($result_nme, $result_val) = each($_POST)) {
 	$k=false;
	if ( substr($result_nme,0,4) == 'dipl' ) {
		save_personnel_formation($result_val, $PS_ID, "$TF_CODE", "$EH_DATE_FIN", "$E_LIEU", 
								 "$resp", "$comment", $evenement,"");
		$listpersonnel .= strtoupper(get_nom($result_val))." ".ucfirst(get_prenom($result_val));
		$destid .= $result_val.",";
		$ignore .= " and P_ID <> ".$result_val;
		$k=true;
	}
	if ( substr($result_nme,0,4) == 'num_' ) {
		$query="update personnel_formation set PF_DIPLOME=\"".$result_val."\" 
   	  	where P_ID = ".substr($result_nme,4,12)."
		and E_CODE = ".$evenement;
		$result=mysql_query($query);
		$query="select PF_DIPLOME from personnel_formation
		where P_ID = ".substr($result_nme,4,12)."
		and PF_DIPLOME is not null
		and PF_DIPLOME <> ''
		and E_CODE = ".$evenement;
		$result=mysql_query($query);
		if ( mysql_num_rows($result) > 0 ) {
			$listpersonnel .= " diplôme n° ".$result_val." ";
			$k=true;
		}
	}
	if ( substr($result_nme,0,4) == 'exp_' ) {
	    if (  $result_val <> '' ) {
			$tmp=explode ( "/",$result_val); $year=$tmp[2]; $month=$tmp[1]; $day=$tmp[0];
			$expiration=$year.'-'.$month.'-'.$day;
			$query="update personnel_formation set PF_EXPIRATION=\"".$expiration."\" 
			where P_ID = ".substr($result_nme,4,12)."
			and E_CODE = ".$evenement;
			$result=mysql_query($query);
			
			$listpersonnel .= " Compétence valide jusqu'au: ".$result_val." ";
			$k=true;
			
			// mettre à jour prolongation sur la fiche formation
			$query="update personnel_formation set Q_EXPIRATION='".$expiration."',
				    where P_ID=".substr($result_nme,4,12)." and E_CODE = ".$evenement;
			$result=mysql_query($query);
			
			// mettre à jour competence
			$query="update qualification set Q_EXPIRATION='".$expiration."',
					   	     	Q_UPDATED_BY=".$_SESSION['id'].", Q_UPDATE_DATE=NOW()
			   	  where ( Q_EXPIRATION < '".$expiration."' or Q_EXPIRATION is null )
				  and P_ID=".substr($result_nme,4,12)." and PS_ID=".$PS_ID;
			$result=mysql_query($query);
			if ($log_actions == 1) {
				$query1="select TYPE from poste where PS_ID=".$PS_ID;
				$result1=mysql_query($query1);
				$row1=@mysql_fetch_array($result1);
				insert_log("UPDQ",substr($result_nme,4,12), $row1["TYPE"]." ".$expiration);
			}
	    }
		else {
			// mettre à jour prolongation
			$query="update personnel_formation set Q_EXPIRATION=null,
				    where P_ID=".substr($result_nme,4,12)." and E_CODE = ".$evenement;
			$result=mysql_query($query);
		}
	}
	if ( $k ) $listpersonnel .= "\n";
}

// et supprimer les diplômes en cas de formation initiale, pour ceux qui ne sont pas cochés
if ( $TF_CODE == 'I' ) {
   	$query = "delete from qualification where PS_ID=".$PS_ID."
	   		 and P_ID in (select P_ID from evenement_participation where E_CODE=".$evenement." and TP_ID=0)";
	$query .= $ignore;
   	$result=mysql_query($query);
}

$query="update personnel_formation set PF_UPDATE_BY=".$id.", PF_UPDATE_DATE=NOW()
	    where E_CODE=".$evenement;
$result=mysql_query($query);

$query="update evenement 
		set F_COMMENT=\"".$comment."\"
   	  	where E_CODE=".$evenement;
$result=mysql_query($query);

// envoyer notification si formation initiale ou continue
$S_ID = get_section_organisatrice($evenement);
$destid .= get_granted(33,"$S_ID",'local','yes');
if ($E_CHEF <> "" ) $destid .= ",".$E_CHEF;

if (( $TF_CODE == 'I' ) or ( $TF_CODE == 'R' )) {
   $query="select count(*) as NB from personnel_formation where E_CODE=".$evenement;
   $result=mysql_query($query);
   $row=@mysql_fetch_array($result);
   if ( $row["NB"] > 0) {
    	$datesheures=get_dates_heures($evenement);	       
   		$subject  = "Résultats de la formation - ".$TYPE." de ".$E_LIEU;
   		$message  = "Bonjour,\n";
   		$message .= "Les personnes suivantes ont suivi avec succès\nla ".$TF_LIBELLE." ".$TYPE;
		$message .= "\ndates: ".$datesheures."\norganisée à ".$E_LIEU." par ".get_section_code("$S_ID").":\n\n";
   		$message .= $listpersonnel;	
   		$nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );
   		
   		// notifier ceux qui doivent imprimer le diplôme national
   		if ( $TF_CODE == 'I' and $PS_NATIONAL == 1 and $PS_PRINTABLE == 1 ) {
   		 	$destid = get_granted(48,0,'local','yes');
   		 	$subject  = "Diplomes nationaux à imprimer - ".$TYPE." (".get_section_code("$S_ID").")";
    		$message .= "\nMerci de procéder à l'impression des diplômes nationaux ".$TYPE;
    		$nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );
    	}
   }
	
}

echo "<body onload=redirect1('evenement_display.php?evenement=".$evenement."&from=formation');>";

?>
