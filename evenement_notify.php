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
check_all(0);

$action=mysql_real_escape_string($_GET["action"]);
$evenement=$_GET["evenement"];
// check input parameters
$evenement=intval(mysql_real_escape_string($evenement));
if ( $evenement == 0 ) {
	param_error_msg();
	exit;
}
?>

<SCRIPT>
function redirect(evenement, from) {
	 url = "evenement_display.php?evenement="+evenement+"&from="+from;
	 self.location.href = url;
}
</SCRIPT>
<?php

$query="select E.E_CODE, EH.EH_ID, E.S_ID,E.TE_CODE, TE.TE_LIBELLE, E.E_LIEU, EH.EH_DATE_DEBUT,EH.EH_DATE_FIN,
        TIME_FORMAT(EH.EH_DEBUT, '%k:%i ') as EH_DEBUT, S.S_CODE, E_PARENT,
		TIME_FORMAT(EH.EH_FIN, '%k:%i ') as EH_FIN, S.S_EMAIL, S.S_EMAIL2, E.E_CHEF, E.TAV_ID,
		E.E_NB, E.E_COMMENT, E.E_LIBELLE, S.S_DESCRIPTION, E.E_CLOSED, E.E_CANCELED, E.E_CANCEL_DETAIL
        from evenement E, type_evenement TE, section S, evenement_horaire EH
	where E.TE_CODE=TE.TE_CODE
	and E.E_CODE = EH.E_CODE
	and S.S_ID=E.S_ID
	and E.E_CODE=".$evenement;
$result=mysql_query($query);

$EH_ID= array();
$EH_DEBUT= array();
$EH_DATE_DEBUT= array();
$EH_DATE_FIN= array();
$EH_FIN= array();
$EH_DUREE= array();
$horaire_evt= array();
$date1=array();
$month1=array();
$day1=array();
$year1=array();
$date2=array();
$month2=array();
$day2=array();
$year2=array();
$i=1;

while ( $row=mysql_fetch_array($result)) {
    if ( $i == 1 ) {
       $E_CODE=$row["E_CODE"];
       $E_CHEF=$row["E_CHEF"];
       $S_ID=$row["S_ID"];
       $S_CODE=$row["S_CODE"]; 
       $S_EMAIL=$row["S_EMAIL"];
       $S_EMAIL2=$row["S_EMAIL2"];
       $S_DESCRIPTION=get_section_name($S_ID);
       $TE_CODE=$row["TE_CODE"];
       $E_LIBELLE=$row["E_LIBELLE"];
       $TE_LIBELLE=$row["TE_LIBELLE"];
       $E_LIEU=$row["E_LIEU"];
       $E_PARENT=$row["E_PARENT"];
       $E_NB=$row["E_NB"];
       $E_COMMENT=$row["E_COMMENT"];
       $E_CLOSED=$row["E_CLOSED"];
       $E_CANCELED=$row["E_CANCELED"];
       $E_CANCEL_DETAIL=$row["E_CANCEL_DETAIL"];
       $TAV_ID=$row["TAV_ID"];
    }

	$EH_DEBUT[$i]=$row["EH_DEBUT"];
    $EH_DATE_DEBUT[$i]=$row["EH_DATE_DEBUT"];
    $EH_DATE_FIN[$i]=$row["EH_DATE_FIN"];
    $EH_FIN[$i]=$row["EH_FIN"];

	$tmp=explode ( "-",$EH_DATE_DEBUT[$i]); $year1[$i]=$tmp[0]; $month1[$i]=$tmp[1]; $day1[$i]=$tmp[2];
	$date1[$i]=mktime(0,0,0,$month1[$i],$day1[$i],$year1[$i]);
	if (( $EH_DATE_FIN[$i] <> '' ) and ( $EH_DATE_FIN[$i] <> $EH_DATE_DEBUT[$i] )) {
		$tmp=explode ( "-",$EH_DATE_FIN[$i]); $year2[$i]=$tmp[0]; $month2[$i]=$tmp[1]; $day2[$i]=$tmp[2];
		$date2[$i]=mktime(0,0,0,$month2[$i],$day2[$i],$year2[$i]);
		$infos_dates[$i] = "dates.................: du ".date_fran($month1[$i], $day1[$i] ,$year1[$i])." ".moislettres($month1[$i])." ".$year1[$i]." à ".$EH_DEBUT[$i];
		$infos_dates[$i] .= " au ".date_fran($month2[$i], $day2[$i] ,$year2[$i])." ".moislettres($month2[$i])." ".$year2[$i]." à ".$EH_FIN[$i]."\n";
	}
	else {
		$infos_dates[$i] = "date..................: le ".date_fran($month1[$i], $day1[$i] ,$year1[$i])." ".moislettres($month1[$i])." ".$year1[$i]." de ".$EH_DEBUT[$i]." à ".$EH_FIN[$i]."\n";
	}
	$i++;
}

$nb=0;$nb2=0;
$subject=$TE_LIBELLE.":  ".$E_LIBELLE;

$message_desc  = $TE_LIBELLE." : ".$E_LIBELLE.".\n\n";
$message_desc .= "organisé par..........: ".$S_CODE." - ".$S_DESCRIPTION."\n";

if ( $E_PARENT <> '' ) {
	$S2=get_section_organisatrice("$E_PARENT");
	$message_desc .= "renfort pour..........: ";
	$message_desc .= get_section_code("$S2")." - ".get_section_name("$S2")."\n";
}

for ($i=1; $i <= $nbmaxsessionsparevenement; $i++) {
    if (isset($infos_dates[$i]))
		$message_desc .= $infos_dates[$i]."\n";
}
	
$message_desc .= "lieu..................: ".$E_LIEU.".\n";
if ( $E_NB == 0 ) 
	$message_desc .= "personnes requises....: pas de limite\n";
else
	$message_desc .= "personnes requises....: ".$E_NB.".\n";
$message_desc .= "commentaire...........: ".$E_COMMENT."\n";

$sp=get_section_parent("$S_ID");

$admins=get_granted(21,"$S_ID",'local','yes');
$adminsparent=get_granted(21,"$sp",'local','yes');
if ( $E_CHEF <> '' ) $chef=$E_CHEF;
else $chef='';
$veille=false;
$secretariat=false;

if ( $action == 'created' ) {
 	$COLMAIL='NO';
 	$subject="création - ".$subject;
 	$ttte="";
 	if ( $TE_CODE == 'DPS' and $TAV_ID == 5) $ttt="un nouveau DPS de grande envergure";
 	else if ( $TE_CODE == 'DPS') $ttt="un nouveau DPS";
 	else if ( $TE_CODE == 'MET') {
	  	$ttt="une nouvelle alerte des bénévoles";
	  	$ttte="e";
	}
 	else  $ttt="nouvel événement";
	$message = "Bonjour,\n
Pour information, ".$ttt." vient d'être créé".$ttte.":\n";
	$message .= $message_desc;
	if (( $S_EMAIL <> "" ) and ( $TE_CODE == 'DPS' or $TE_CODE == 'MET' )) $veille=true;
	if ( $S_EMAIL2 <> "" ) $secretariat=true;
	if (( get_formateurs("$S_ID") <> "") and ( $TE_CODE == 'INS' )) 
		$destid = $admins;
	else
		$destid = $chef.",".$admins;
	// si evenement sur antenne locale , prevenir aussi le departement
	if ( get_children("$S_ID") == '' ) $destid .= ",".$adminsparent;
	// si renfort, prevenir les responsables de l'événement principal
	if ( $E_PARENT <> '' ) {
	 	$destid .= ",".get_chef_evenement("$E_PARENT");
		$destid .= ",".get_granted(21,"$S2",'local','yes');
	}
	// si DPS GE, prévenir le niveau national et toujours le niveau parent
	if ( $TAV_ID == 5 or $TE_CODE == 'MET') {
		$destid .= ",".get_granted(21,0,'local','no');
		$destid .= ",".$adminsparent;
	}
}

if ( $action == 'enroll' ) {
 	$COLMAIL='E_MAIL1';
 	$subject="inscriptions ouvertes - ".$subject;
	$message = "Bonjour,\n
Tu peux dès maintenant t'inscrire pour:\n";
	$message .= $message_desc;
	if (( get_formateurs("$S_ID") <> "") and ( $TE_CODE == 'INS' )) 
		$destid =get_formateurs("$S_ID").",".$admins;
	else
		$destid=get_family_members("$S_ID").",".$admins;
}

if ( $action == 'closed' ) {
  	$COLMAIL='E_MAIL2';
 	$subject="validation - ".$subject;
	$message = "Bonjour,\n
Voici la liste des personnes retenues:\n";
	$message .= get_noms_inscrits($evenement);
	$message .="\nPour participer à:\n";
	$message .= $message_desc;
	if (( $S_EMAIL <> "" ) and ( $TE_CODE == 'DPS' )) $veille=true;
	if ( $S_EMAIL2 <> "" ) $secretariat=true;
	if (( get_formateurs("$S_ID") <> "") and ( $TE_CODE == 'INS' ))  
		$destid =get_inscrits($evenement).",".$admins;
	else
		$destid=get_inscrits($evenement).",".$chef.",".$admins;
}


if ( $action == 'canceled' ) {
  	$COLMAIL='E_MAIL3';
 	$subject="annulation - ".$subject;
	$message = "Bonjour,\n
L'événement suivant a été annulé (".$E_CANCEL_DETAIL."):\n";
	$message .= $message_desc;
	if (( get_formateurs("$S_ID") <> "") and ( $TE_CODE == 'INS' ))  
		$destid =get_inscrits($evenement).",".$admins;
	else
		$destid = get_inscrits($evenement).",".$chef.",".$admins;
	if (( $S_EMAIL <> "" ) and ( $TE_CODE == 'DPS' )) $veille=true;
	if ( $S_EMAIL2 <> "" ) $secretariat=true;
}

if (( $action == 'desinscrit' ) and isset ($_GET["P_ID"])) {
  	$COLMAIL='NO';
  	// notifier le chef si une personne est désinscrite alors que l'événement est clôturé
	if ( $E_CLOSED == 1 and $chef <> '') {
	    $P_ID=intval($_GET["P_ID"]);
	 	$subject="Participation annulee - ".$subject;
	 	$query="select P_PRENOM, P_NOM from pompier where P_ID = ".$P_ID;
	 	$result=mysql_query($query);
  		$row=mysql_fetch_array($result);
  		$prenom = my_ucfirst($row["P_PRENOM"]);
  		$nom = strtoupper($row["P_NOM"]);
	 	$message = "Bonjour,\n
La participation de ".$prenom." ".$nom." a été annulée pour:\n";
	 	$message .= $message_desc;
		$nb = mysendmail("$chef" , $_SESSION['id'] , "$subject" , "$message" );
	}
  	// notifier la personne qui est désinscrite
 	$subject="non retenu - ".$subject;
	$message = "Bonjour,\n
Votre inscription n'a pas été retenue pour participer à:\n";
	$message .= $message_desc;
	$destid = intval($_GET["P_ID"]);
}

if (( $action == 'inscription' ) and isset ($_GET["P_ID"])) {
  	$COLMAIL='NO';
  	$destid='';
  	$P_ID=intval($_GET["P_ID"]);
  	$statut_of=get_statut($P_ID);
  	$usersection=get_section_of($P_ID);
  	
  	$sql="select S_EMAIL2 from section where S_ID = (select P_SECTIOn from pompier where P_ID = $P_ID )";
  	$result=mysql_query($sql);
  	$row=mysql_fetch_array($result);
  	$S_EMAIL2 = $row["S_EMAIL2"];

	// cas inscription d'un salarié, notifier ses responsables en indiquant son statut
  	if ( $statut_of == 'SAL' ) {
  		$query="select ep.EP_FLAG1, p.P_PRENOM, p.P_NOM
		  		from evenement_participation ep, pompier p
		  		where ep.E_CODE=".$evenement." 
				and p.P_ID = ep.P_ID 
				and p.P_ID = ".$P_ID;
  		$result=mysql_query($query);
  		$row=mysql_fetch_array($result);
  		$prenom = my_ucfirst($row["P_PRENOM"]);
  		$nom = strtoupper($row["P_NOM"]);
  		$destid=get_granted(13,"$usersection",'parent','no');
    	if ($row["EP_FLAG1"] == 1 ) $as='salarié(e)';
    	else $as='bénévole';
    	$subject="inscription en tant que ".$as." de ".$prenom." ".$nom;
  		$message = "Bonjour,\n
Pour information, ".$prenom." ".$nom."\nvient de s'inscrire en tant que ".$as." pour:\n";
		$message .= $message_desc;
  		if ( $destid <> '' ) $nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );
  		$destid='';
  		if ( $S_EMAIL2 <> "" ) $nb2 = mysendmail2("$S_EMAIL2" , $_SESSION['id'] , "$subject" , "$message" );	
  	}
  	if ( $E_PARENT <> '' ) {
  		// cas inscription sur un renfort alors que evenement principal clôturé
  		// notifier responsable evenement principal
  		$query="select E_CHEF, E_CLOSED from evenement where E_CODE=".$E_PARENT;
  		$result=mysql_query($query);
  		$row=mysql_fetch_array($result);
    	$E_CLOSED=$row["E_CLOSED"];
    	$E_CHEF=$row["E_CHEF"];
  		if ( $E_CLOSED == 1 and $E_CHEF <> "" ) {
  			$subject="inscription - ".$subject;
  			$message = "Bonjour,\n
Pour information,".my_ucfirst(get_prenom($P_ID))." ".strtoupper(get_nom($P_ID))."
		  	\n vient de s'inscrire à un renfort pour un événement principal déjà clôturé:\n";
		  	$message .= $message_desc;
  		  	$destid = $E_CHEF;
  		  	if ( $destid <> '' ) $nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );
  		  	$destid='';
  		}
  	}
  	// si un agent s'inscrit pour un événement extérieur à sa section ou à la section n+1, et qu'il
  	// est plus bas dans la hiérarchie que la section organisatrice 
  	// alors on notifie son chef de section
  	if (( $usersection <> $S_ID ) 
	  and ( get_section_parent($usersection) <> $S_ID ) 
	    and ( get_level($usersection) >= get_level("$S_ID"))) {
 		    $subject="inscription - ".$subject;
		    $message = "Bonjour,\n
Pour information,".my_ucfirst(get_prenom($P_ID))." ".strtoupper(get_nom($P_ID))."
		    \n vient de s'inscrire pour participer à un événement extérieur:\n";
		    $message .= $message_desc;
		    $destid = get_granted(21,"$usersection",'parent','yes');
		    if ( $S_EMAIL2 <> "" ) $nb2 = mysendmail2("$S_EMAIL2" , $_SESSION['id'] , "$subject" , "$message" );
	}
}

//echo "<pre>".$destid."
//".$subject."
//".$message."</pre>";
//exit;

if ( $secretariat )
	$nb2 = mysendmail2("$S_EMAIL2" , $_SESSION['id'] , "$subject" , "$message" );
if ( $veille )
	$nb2 = mysendmail2("$S_EMAIL" , $_SESSION['id'] , "$subject" , "$message" );
if ( $destid <> '' ) 
	$nb = mysendmail("$destid" , $_SESSION['id'] , "$subject" , "$message" );

if ( $COLMAIL <> 'NO' ) {
	$query="update evenement set ".$COLMAIL."=1 where E_CODE=".$evenement ;
	$result=mysql_query($query);
	if ( $nb2 == 1 ) $addthis="<br>Et à cette adresse aussi: ".$S_EMAIL;
	else $addthis="";
	write_msgbox("OK", $star_pic, "Le message suivant a été envoyé à: ".$nb." personnes.".$addthis."<p><font face=courrier size=1>Sujet:[".$cisname."] ".$subject."<p>".nl2br($message)."</font><p align=center><a href=evenement_display.php?evenement=".$evenement."&from=choice>".$myspecialfont." retour</font></a>",30,0);
}
else {
    if ( $action <> 'created' ) $action='inscription';
	echo "<body onload=redirect('".$evenement."','".$action."');>";
}


?>
