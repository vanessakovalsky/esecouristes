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
  
header('Content-Type: text/html; charset=ISO-8859-1');
include_once ("config.php");
check_all(0);
$id=$_SESSION['id'];
$evenement=intval($_GET["evenement"]);
$evts=get_event_and_renforts($evenement,false);

if (! is_inscrit($id,$evenement)) {
	if (! check_rights($_SESSION['id'],41))
		if ( get_company_evenement($evenement) == $_SESSION['SES_COMPANY'] )
			check_all(45);
		else
			check_all(41);
}

$mysection=$_SESSION['SES_SECTION'];
$mysectionparent=get_section_parent($mysection);

if (isset ($_GET["from"])) $from=mysql_real_escape_string($_GET["from"]); // scroller , inscription , calendar, choice, vehicule, personnel
else $from="null";
if (isset ($_GET["section"])) $section=intval($_GET["section"]);
else $section=$mysection;
if (isset ($_GET["type"])) $type=mysql_real_escape_string($_GET["type"]);
else $type="ALL";
if (isset ($_GET["date"])) $date=mysql_real_escape_string($_GET["date"]);
else $date="FUTURE";
if (isset ($_GET["day"])) $day=mysql_real_escape_string($_GET["day"]);
else $day="";
if ( isset($_GET["tab"]))$tab=mysql_real_escape_string($_GET["tab"]);
else $tab="1";
if (isset ($_GET["pid"])) $pid=mysql_real_escape_string($_GET["pid"]);
else $pid="";
if ( isset($_GET["print"]))$print=true;
else $print=false;

writehead();
$iphone=is_iphone();
 
?>
<STYLE type="text/css">
.section{color:<?php echo $mydarkcolor; ?>;background-color:<?php echo $mylightcolor; ?>;font-size:10pt;}
.categorie{color:black; background-color:white; font-size:10pt;}
.materiel{color:<?php echo $mydarkcolor; ?>; background-color:white; font-size:9pt;}
</STYLE>
<script type=text/javascript>
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

$query="select E.E_CODE, E.S_ID,E.TE_CODE, TE.TE_LIBELLE, E.E_LIEU, EH.EH_DATE_DEBUT,EH.EH_DATE_FIN,
        TIME_FORMAT(EH.EH_DEBUT, '%k:%i') as EH_DEBUT, E.E_NB1, E.E_NB2, E.E_NB3, E.E_NB1_1, E.E_NB1_2, E.E_NB1_3, E.E_NB1_4, E.E_NB1_5, E.E_NB1_6, E.E_NB2_1, E.E_NB2_2, S.S_CODE, E.E_CHEF,
		TIME_FORMAT(EH.EH_FIN, '%k:%i') as EH_FIN, E.E_MAIL1, E.E_MAIL2, E.E_MAIL3, E.E_OPEN_TO_EXT,
		E.E_NB, E.E_COMMENT, E.E_COMMENT2, E.E_LIBELLE, S.S_DESCRIPTION, E.E_CLOSED, E.E_CANCELED, E.E_CANCEL_DETAIL,
		E.E_CONVENTION, E.E_PARENT, E.E_CREATED_BY, E_ALLOW_REINFORCEMENT, E.TF_CODE, E.PS_ID,
		date_format(E.E_CREATE_DATE,'%d-%m-%Y %H:%i') E_CREATE_DATE, E.C_ID, E.E_CONTACT_LOCAL, E.E_CONTACT_TEL,
		S.DPS_MAX_TYPE, E.TAV_ID, EH.EH_ID, EH.EH_DUREE, E.E_FLAG1, E.E_VISIBLE_OUTSIDE, E.E_ADDRESS
        from evenement E, evenement_horaire EH, type_evenement TE, section S
		where E.TE_CODE=TE.TE_CODE
		and E.E_CODE=EH.E_CODE
		and S.S_ID=E.S_ID
		and E.E_CODE=".$evenement."
		order by EH.EH_ID";
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
$E_DUREE_TOTALE = 0;
$i=1;
while ( $row=mysql_fetch_array($result)) {
    if ( $i == 1 ) {
       $E_CODE=$row["E_CODE"];
       $S_ID=$row["S_ID"];
       $E_CHEF=$row["E_CHEF"];
       $S_CODE=$row["S_CODE"];
       $S_DESCRIPTION=stripslashes($row["S_DESCRIPTION"]);
       $TE_CODE=$row["TE_CODE"];
       $E_LIBELLE=stripslashes($row["E_LIBELLE"]);
       $TE_LIBELLE=$row["TE_LIBELLE"];
       $E_LIEU=stripslashes($row["E_LIEU"]);
       $E_MAIL1=$row["E_MAIL1"];
       $E_MAIL2=$row["E_MAIL2"];
       $E_MAIL3=$row["E_MAIL3"];
       $E_NB=$row["E_NB"];
       $E_NB1=$row["E_NB1"];
       $E_NB2=$row["E_NB2"];
       $E_NB3=$row["E_NB3"];
       $E_NB4=$row["E_NB1_1"];
       $E_NB5=$row["E_NB1_2"];
       $E_NB6=$row["E_NB1_3"];
       $E_NB7=$row["E_NB1_4"];
       $E_NB8=$row["E_NB1_5"];
       $E_NB9=$row["E_NB1_6"];
       $E_NB10=$row["E_NB2_1"];
       $E_NB11=$row["E_NB2_2"];
       $E_COMMENT=stripslashes($row["E_COMMENT"]);
       $E_COMMENT2=stripslashes($row["E_COMMENT2"]);
	   $E_ADDRESS=stripslashes($row["E_ADDRESS"]);
	   $E_VISIBLE_OUTSIDE=$row["E_VISIBLE_OUTSIDE"];
       $E_CLOSED=$row["E_CLOSED"];
       $E_CANCELED=$row["E_CANCELED"];
       $E_FLAG1=$row["E_FLAG1"];
       $E_CANCEL_DETAIL=$row["E_CANCEL_DETAIL"];
       $E_CONVENTION=$row["E_CONVENTION"];
       $E_OPEN_TO_EXT=$row["E_OPEN_TO_EXT"];
       $E_ALLOW_REINFORCEMENT=$row["E_ALLOW_REINFORCEMENT"];
	   $E_PARENT=$row["E_PARENT"];
	   $E_CREATED_BY=$row["E_CREATED_BY"];
	   $E_CREATE_DATE=$row["E_CREATE_DATE"];
	   $TF_CODE=$row["TF_CODE"];
	   $PS_ID=$row["PS_ID"];
	   $C_ID=$row["C_ID"];
	   $TAV_ID=$row["TAV_ID"];
	   $E_CONTACT_LOCAL=$row["E_CONTACT_LOCAL"];
	   $E_CONTACT_TEL=$row["E_CONTACT_TEL"];
	   $DPS_MAX_TYPE=$row["DPS_MAX_TYPE"];
    }
    
    // tableau des sessions
    $EH_ID[$i]=$row["EH_ID"];
    $EH_DEBUT[$i]=$row["EH_DEBUT"];
    $EH_DATE_DEBUT[$i]=$row["EH_DATE_DEBUT"];
	if ( $row["EH_DATE_FIN"] == '' ) 
		$EH_DATE_FIN[$i]=$row["EH_DATE_DEBUT"];
    else 
	    $EH_DATE_FIN[$i]=$row["EH_DATE_FIN"];
    $EH_FIN[$i]=$row["EH_FIN"];
    $EH_DUREE[$i]=$row["EH_DUREE"];
    if ( $EH_DUREE[$i] == "") $EH_DUREE[$i]=0;
    $E_DUREE_TOTALE = $E_DUREE_TOTALE + $EH_DUREE[$i];
	$tmp=explode ( "-",$EH_DATE_DEBUT[$i]); $year1[$i]=$tmp[0]; $month1[$i]=$tmp[1]; $day1[$i]=$tmp[2];
	$date1[$i]=mktime(0,0,0,$month1[$i],$day1[$i],$year1[$i]);
    $tmp=explode ( "-",$EH_DATE_FIN[$i]); $year2[$i]=$tmp[0]; $month2[$i]=$tmp[1]; $day2[$i]=$tmp[2];
	$date2[$i]=mktime(0,0,0,$month2[$i],$day2[$i],$year2[$i]);

	if ( $EH_DATE_DEBUT[$i] == $EH_DATE_FIN[$i])
		$horaire_evt[$i]=date_fran($month1[$i], $day1[$i] ,$year1[$i])." ".moislettres($month1[$i])." ".$year1[$i]." de ".$EH_DEBUT[$i]." à ".$EH_FIN[$i];
	else
		$horaire_evt[$i]="\ndu ".date_fran($month1[$i], $day1[$i] ,$year1[$i])." ".moislettres($month1[$i])." ".$EH_DEBUT[$i]." au "
		                 .date_fran($month2[$i], $day2[$i] ,$year2[$i])." ".moislettres($month2[$i])." ".$year2[$i]." ".$EH_FIN[$i];
	$i++;
}

$nbsessions=sizeof($EH_ID);
$organisateur= $S_ID;
if (get_level("$organisateur") > $nbmaxlevels - 2 ) $departement=get_family(get_section_parent("$organisateur"));
else $departement=get_family("$organisateur");
	
$query="select count(distinct P_ID) as NB from evenement_participation
 	where E_CODE in (".$evts.")";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NP=$row["NB"];

$ischef=is_chef($id,$S_ID);

$OPEN_TO_ME = 1;
if (( $nbsections <> 1) and ( $E_OPEN_TO_EXT == 0 ) and ( $mysection <> $S_ID )) {
	  if ( get_section_parent("$mysection") <> get_section_parent("$S_ID")) {
	  	 	$list = preg_split('/,/' , get_family_up("$S_ID"));
	  	 	if (! in_array($mysection,$list)) {
			   	$list = preg_split('/,/' , get_family("$S_ID"));  
			   	if (! in_array($mysection,$list)) 
				   $OPEN_TO_ME = 0;
	  	 	}
	  }
	  else {
	  		// je peux inscrire sur les antennes voisines mais pas les départements voisins
	  		// si je suis à un niveau supérieur à antenne -> je ne peux pas m'inscrire
	  		if ( get_level("$mysection") + 2 <= $nbmaxlevels )
	  			$OPEN_TO_ME = 0;
	  }
}
// événement national,régional
$list = preg_split('/,/'  , get_family_up("$mysection"));
if (( $nbsections == 0) and ( $mysection <> $S_ID ) and ( in_array($S_ID,$list))) {
  if (( get_level($S_ID) < $nbmaxlevels - 2 ) and ( ! check_rights($id, 26)))
      $OPEN_TO_ME = -2;
}
// cas particulier un agent lambda ne doit pas s'inscrire lui même sur un événement extérieur
elseif (( $nbsections == 0) and ( $E_OPEN_TO_EXT == 1 ) and ( $mysection <> $S_ID )) {
	if (( get_section_parent("$mysection") <> $S_ID )
	  and ( get_section_parent("$mysection") <> get_section_parent("$S_ID"))) {
		if ( ! check_rights($id, 26))
			$OPEN_TO_ME = -1;
	}
	elseif (get_section_parent("$mysection") == get_section_parent("$S_ID")
		and get_level("$mysection") + 2 <= $nbmaxlevels) {
		if ( ! check_rights($id, 26))
			$OPEN_TO_ME = -1;	 
	}
}

// definition des permissions
if (check_rights($id, 15, $organisateur)) $granted_event=true;
else $granted_event=false;
if (check_rights($id, 10, $organisateur)) $granted_personnel=true;
else $granted_personnel=false;
if (check_rights($id, 17, $organisateur) or $granted_event) $granted_vehicule=true;
else $granted_vehicule=false;
if (check_rights($id, 19, $organisateur)) $granted_delete=true;
else $granted_delete=false;
if (check_rights($id, 26, $organisateur)) { 
 	$veille=true;
	$SECTION_CADRE=get_highest_section_where_granted($id,26);
}
else $veille=false;
if (($OPEN_TO_ME == 1 ) and (check_rights($id, 28) or check_rights($id, 10))) 
   $granted_inscription=true;
else $granted_inscription=false;

// cas particulier
if (check_rights($id, 17) and (! $granted_vehicule))  {
 	if ( $E_OPEN_TO_EXT == 1 ) $granted_vehicule=true;
}

$chef=false;
if ( $id == $E_CHEF ) {
 $granted_event=true;
 $granted_personnel=true;
 $granted_vehicule=true;
 $chef=true;
}

if ((check_rights($_SESSION['id'], 47, "$organisateur")) or $chef or $granted_event)
$documentation=true;
else $documentation=false;

echo "</script>
     <title>$TE_LIBELLE </title></head>";	
if ( $print ) 
   echo "<body onload='javascript:window.print();'>";
else echo "<body>";

//=====================================================================
// titre si impression
//=====================================================================
if ( $print) {

 	if ( is_file('images/user-specific/logo.jpg'))
 		$logo='images/user-specific/logo.jpg';
	else 
 		$logo='images/t_ebrigade.gif';
 	
	$queryz="select S_DESCRIPTION from section where S_ID = 0";;
	$resultz=mysql_query($queryz);
	$rowz=mysql_fetch_array($resultz);
	$S_DESCRIPTION0 =  $rowz["S_DESCRIPTION"];	
	echo "<table><tr><td><img src=".$logo."></td><td><font size=5>".$S_DESCRIPTION0."</font><br><font size=4>Section : ".$S_CODE." - ".$S_DESCRIPTION."</font></td></tr></table>
		<p>Bonjour, veuillez trouver ci-dessous les éléments relatifs à la mise en place de :<br>
		".$TE_LIBELLE." - ".$E_LIBELLE." (".$E_LIEU.")";
	
	for ($i=1; $i <= $nbmaxsessionsparevenement; $i++) {
       if (isset($horaire_evt[$i]))
		   echo "<br>".$horaire_evt[$i];
    }
		
	// info demandeur DPS
	if ( $C_ID <> "" and $C_ID > 0)
		echo "<br>Pour le compte de <b>".get_company_name($C_ID)."</b>";
	
}

//=====================================================================
// informations générales
//=====================================================================
if (( $tab == 1 ) or ($print)){
echo "<p>";
echo "<TABLE>
<TR>
<TD class='FondMenu'>";

if ( $E_CREATED_BY <> '' ) 
	$author = "<font size=1><i> - créé par ".my_ucfirst(get_prenom($E_CREATED_BY))." ".strtoupper(get_nom($E_CREATED_BY))."
			   le ". $E_CREATE_DATE."
				</i></font>";
else 
	$author='';

echo "<table cellpading=0 cellspacing=0 border=0 width=700>";
echo "<tr><td CLASS='MenuRub'>Description".$author."</td></tr>";
echo "<tr><TD CLASS='Menu'>";

if ( $E_CANCELED == 1 ) {
   if ( $E_CANCEL_DETAIL <> '' ) $pr="( ".$E_CANCEL_DETAIL." )";
   else $pr='';
      echo "<font size=3 color=red><b>Evénement annulé ".$pr."</b></font>";
}
else if ( $E_CLOSED == 1 ) echo "<font size=3 color=orange><b>Inscriptions fermées</b></font>";
else if ( $OPEN_TO_ME == 0 ) echo "<font size=3 color=orange><b>Inscriptions interdites pour les personnes des autres ".$niv3."s</b></font>";
else if ( $OPEN_TO_ME == -1 ) echo "<font size=3 color=green><b>Inscriptions possibles pour les personnes des autres ".$niv3."s par leur responsable</b></font>";
else echo "<font size=3 color=green><b>Inscriptions ouvertes</b></font>";

echo "<table width=600 cellspacing=0 border=0>";

if (( $E_PARENT <> '' ) and ( $E_PARENT > 0) and ( $nbsections == 0)) {
	echo "<tr><td width=30%><b>Renfort pour: </b></td>";
	$queryR="select e.TE_CODE, e.E_LIBELLE, s.S_CODE, s.S_DESCRIPTION 
			from evenement e, section s 
			where s.S_ID = e.S_ID
			and e.E_CODE=".$E_PARENT;
	$resultR=mysql_query($queryR);
	$rowR=@mysql_fetch_array($resultR);
	$ER_LIBELLE=stripslashes($rowR["E_LIBELLE"]);
	$SR_CODE=$rowR["S_CODE"];
	$SR_DESCRIPTION=$rowR["S_DESCRIPTION"];
	echo "<td width=70%><a href=evenement_display.php?evenement=".$E_PARENT.">
	".$ER_LIBELLE." organisé par ".$SR_CODE." - ".$SR_DESCRIPTION."</a></td></tr>";	
}

for ($i=1; $i <= $nbmaxsessionsparevenement; $i++) {
    if ( $nbsessions == 1 ) $t="Dates et heures";
    else if (isset($EH_ID[$i])) $t="Date Partie ".$EH_ID[$i];
	if ( isset($horaire_evt[$i]))
		echo "<tr><td ><b>".$t.": </b></td>
   	 	<td> ".$horaire_evt[$i]."
	 	</td></tr>";
}
if($E_DUREE_TOTALE!=''){	 
echo "<tr><td ><b>Durée totale: </b></td>
   	 <td > ".$E_DUREE_TOTALE." heures</td></tr>";
}

if ( $nbsections <> 1 ) {
echo "<tr><td ><b>Organisateur: </b></td>
   	 <td ><a href=upd_section.php?S_ID=".$S_ID.">".$S_CODE." - ".get_section_name($organisateur)."</a></td></tr>";
}

if ( isset ($organisation_name)) $org=$organisation_name;
else $org=$cisname;
 
echo "<tr><td title=\"Donne tous les droits d'accès sur cet évenement\"><b>Responsable $org: </b></td>
   	 <td>";
if ( $E_CHEF <> '' ) {
	$queryz="select P_PHONE, P_HIDE from pompier where P_ID=".$E_CHEF;
	$resultz=mysql_query($queryz);
	$rowz=mysql_fetch_array($resultz);
	$phone =  $rowz["P_PHONE"];
	$P_HIDE = $rowz["P_HIDE"];
	if ($phone <> '') {
		if ($iphone)
 				$P_PHONE=" (<a href='tel:".$row["P_PHONE"]."'>".$row["P_PHONE"]."</a>)";
 		else 
			$phone = " (".$phone.")";
		if (( ($P_HIDE == 1) ) and ( $nbsections == 0 )) {
	  		if (( ! $ischef ) 
				and ( $E_CHEF <> $id )
				and (! check_rights($id, 2))
				and (! check_rights($id, 12)))
	  			$phone=" (**********)";
		} 
	}
	echo "<a href=upd_personnel.php?pompier=".$E_CHEF." title=\"A tous les droits d'accès sur cet évenement\"> 
		".my_ucfirst(get_prenom($E_CHEF))." ".strtoupper(get_nom($E_CHEF))."</a>".$phone;
}

if ( $granted_event and (!$print)) echo " <img src=images/user.png border=0 height=14 title='choisir le responsable'
		   onclick=\"choisir_responsable('".$evenement."','".$E_CHEF."')\">";		
echo "</td></tr>";

echo "<tr><td width=30%><b>Lieu: </b></td>
   	 <td width=70%> ".$E_LIEU."</td></tr>";

if ( $E_ADDRESS <> "" ) {
	$querym="select count(*) as NB from geolocalisation where TYPE='E' and CODE=".$evenement;
	$resultm=mysql_query($querym);
	$rowm=mysql_fetch_array($resultm);
	if ( $rowm["NB"] == 1 and (! $print)) $map="<a href=map.php?type=E&code=".$evenement." target=_blank><img src=images/mapsmall.png height=14 title='Voir la carte Google Maps' border=0></a>";
    else $map="";
	
	echo "<tr><td width=30%><b>Adresse exacte: </b></td>
   	 <td width=70%>".$E_ADDRESS." ".$map."</td></tr>";
}

// compétences requises
$querym="select ec.EH_ID, ec.PS_ID, ec.NB, p.TYPE, p.DESCRIPTION, p.EQ_ID 
		from evenement_competences ec
        left join poste p on ec.PS_ID = p.PS_ID
		where ec.E_CODE=".$evenement."
		order by ec.EH_ID, p.EQ_ID, p.PS_ID";
$resultm=mysql_query($querym);
$nbm=mysql_num_rows($resultm);

if ( $nbsessions == 1 ) {
 	$showcpt = "<tr><td width=30%><b>Personnel demandé: </b></td><td>";
 	$prevEH_ID=1;
}
else {
 	$showcpt = "<tr><td colspan=2><b>Personnel demandé: </b>";
	$prevEH_ID=0;
}
while ( $rowm=mysql_fetch_array($resultm) ) {
 	$i=$rowm["EH_ID"];
 	$poste=$rowm["PS_ID"];
 	$type=$rowm["TYPE"];
 	$nb=$rowm["NB"];
 	$desc=$nb." ".$rowm["DESCRIPTION"]." requis, ";
 	if ( $i <> $prevEH_ID ) {
 	 	if ( $i > 1 ) {
 	 	 	$showcpt = rtrim($showcpt,',');
			if ( $granted_event and (!$print))
				$showcpt .= " <img src=images/page_white_text.png
				  	title='Modifier les compétences demandées' 
				  	border=0 
				  	height=14 
				  	onclick=\"modifier_competences('".$evenement."',".$prevEH_ID.")\">";
			$showcpt .= "</td></tr>";
 	 	 
 	 	}
 	 	$showcpt .= "</td></tr><tr><td align=right><font size=1>partie ". $i.":</font></td><td>";
 	 	$prevEH_ID=$i;
 	}
    if ( $poste == 0 ){
        $type='TOTAL';
        $inscrits=get_nb_competences($evenement,$i);
        if ($inscrits >= $nb ) $col=$green;
     	else $col=$red;
     	$desc = $inscrits." inscrits.";
        $showcpt .= " <a title=\"$nb personnes requises\n".$desc."\"><font color=$col>TOTAL</font></a><font color=$col> $nb </font>";
		if ( $nbm > 1 ) $showcpt .= " <font size=1><i>détail: </i></font>";
    }
    else {
     	$inscrits=get_nb_competences($evenement,$i,$poste);
     	if ($inscrits >= $nb ) $col=$green;
     	else $col=$red;
     	if ( $inscrits < 2 ) $desc .= "\n".$inscrits." inscrit ayant cette compétence valide.";
     	else $desc .= "\n".$inscrits." inscrits ayant cette compétence valide.";
 	  	$showcpt .= " <font color=$col>".$nb."</font> <a title=\"".$desc."\"><font color=$col>".$type."</font></a>,";
 	}
}
$showcpt = rtrim($showcpt,',');

if ( $granted_event and (!$print))
	$showcpt .= " <img src=images/page_white_text.png
				  title='Modifier les compétences demandées' 
				  border=0 
				  height=14 
				  onclick=\"modifier_competences('".$evenement."',".$i.")\">  ";
                  
$showcpt .= "</td></tr>";
print $showcpt;

// cas du DPS
if ( $TE_CODE == 'DPS' ) {
	$warn="";
	if ( $TAV_ID == 1  or  $TAV_ID == '' ) $tdps='Non défini';
	else {
		// type de DPS choisi
		$querydps="select TAV_ID, TA_VALEUR from type_agrement_valeur
			   where TA_CODE = 'D'
			   and TAV_ID=".$TAV_ID;
		$resultdps=mysql_query($querydps);
		$rowdps=mysql_fetch_array($resultdps);
		$tdps = $rowdps["TA_VALEUR"];
		
		//comparer avec agrément
		$queryag="select a.S_ID, a.A_DEBUT, a.A_FIN, tav.TAV_ID, tav.TA_VALEUR,
					DATEDIFF(NOW(), a.A_FIN) as NB_DAYS
					from agrement a, type_agrement_valeur tav
					where a.TA_CODE=tav.TA_CODE
					and a.TAV_ID= tav.TAV_ID
					and a.TA_CODE='D'
					and a.S_ID in (".$S_ID.",".get_section_parent("$S_ID").")";
		$resultag=mysql_query($queryag);
		$rowag=mysql_fetch_array($resultag);
		$debut = $rowag["A_DEBUT"];
		$tag = $rowag["TA_VALEUR"];
		$tagid = $rowag["TAV_ID"];
		$nbd = $rowag["NB_DAYS"];
		$sectionag = $rowag["S_ID"];

		if ( $tagid <> "" and ( !$print)) {
			if ( $TAV_ID > $tagid or $debut == '') {
				$title="ATTENTION Il n'y a pas d'agrément ou l'agrément est insuffisant pour ce type de DPS.";
				if ( $tagid > 1 and $debut <> '') 
					$title .=" L'agrément permet seulement l'organisation de DPS de type $tag.";
				$warn_img="<img src=images/minino.png height=14
					title=\"$title\" border=0>";
			}
			else if  ( $nbd > 0  ) 
				$warn_img="<img src=images/minino.png height=14
					title=\"ATTENTION agrément pour les DPS périmé\" border=0>";
			else if ( $DPS_MAX_TYPE <> '' and $DPS_MAX_TYPE < $TAV_ID ) {
				$warn_img="<img src=images/miniwarn.png height=14
					title=\"ATTENTION le $niv3 ne permet pas à cette $niv4 d'organiser ce type de DPS\" border=0>";
				$warn="<a href=upd_section.php?S_ID=".$S_ID.">".$warn_img."</a>";
			}		
			else
				$warn_img="<img src=images/miniok.png height=14
					title=\"Agrément valide pour ce type de DPS\" border=0>";
					
			if ( $warn == '')	
				$warn="<a href=upd_section.php?S_ID=".$sectionag."&status=agrements>".$warn_img."</a>";
		}
	}
    if ( $E_FLAG1 == 1 ) $interassociatif='<b>Inter-associatif</b>, ';
    else $interassociatif='';
	echo "<tr><td width=30%><b>Type de DPS: </b></td>
   	 <td width=70%> ".$interassociatif." ".$tdps." ".$warn."</td></tr>";

}

if ( $E_CONVENTION <> "" ) {
	echo "<tr><td width=30%><b>Numéro de convention: </b></td>
   	 <td width=70%> ".$E_CONVENTION."</td></tr>";
}	
if (( $E_CLOSED == 0 ) && ( $nbsections == 0 )) { 
   if ( $E_OPEN_TO_EXT == 1 && $E_ALLOW_REINFORCEMENT == 1 ) 
   		$cmt="Possibles pour les personnes des autres ".$niv3."s et pour les renforts.";
   elseif ( $E_OPEN_TO_EXT == 1 && $E_ALLOW_REINFORCEMENT == 0 ) 
   		$cmt="Possibles pour les personnes extérieures.";
   elseif ( $E_OPEN_TO_EXT == 0 && $E_ALLOW_REINFORCEMENT == 1 ) 
   		$cmt="Impossibles pour les personnes des autres ".$niv3."s, mais possible pour les renforts.";
   else 
    	$cmt="Impossibles pour les personnes des autres ".$niv3."s et pour les renforts."; 
}
else  {
 	if ( $E_OPEN_TO_EXT == 1) 
   		$cmt="Possibles pour les personnes des autres ".$niv3."s.";
   	else 
   		$cmt="Impossibles pour les personnes des autres ".$niv3."s";
}
if ( $nbsections <> 1 )
echo "<tr><td width=30%><b>Inscriptions:</b></td> 
             <td width=70%>".$cmt."</td></tr>"; 
if ( $E_COMMENT <> "" ) {
	echo "<tr><td width=30%><b>Détails: </b></td>
   	 <td width=70%> ".$E_COMMENT."</td></tr>";	    
}
if ( $E_VISIBLE_OUTSIDE == 1 ) {
	echo "<tr><td width=30%><b>Visible de l'extérieur: </b></td>
   	 <td width=70%>Peut être vu dans un site externe sans identification<img src=images/miniwarn.png title=\"Visible de l'extérieur\"> </td></tr>";	    
}
if ( $E_COMMENT2 <> "" ) {
	echo "<tr><td width=30%><b>Commentaire extérieur: </b></td>
   	 <td width=70%> ".$E_COMMENT2."</td></tr>";	    
}

if ( $C_ID <> '' and $C_ID > 0) {
 	echo "<tr><td width=25%><b>Pour le compte de: </b></td>";
 	if (check_rights($_SESSION['id'], 37)) $company="<a href=upd_company.php?C_ID=".$C_ID.">".get_company_name($C_ID)."</a>";
 	else $company=get_company_name($C_ID);
 	echo "<td width=75%>".$company."</td></tr>";	
 
 	// responsable formation ou opérationnel
	$queryr="select p.P_ID, p.P_NOM, p.P_PRENOM, p.P_PHONE , tcr.TCR_DESCRIPTION
				from pompier p, company_role cr, type_company_role tcr 
				where p.P_ID=cr.P_ID
				and tcr.TCR_CODE = cr.TCR_CODE
				and cr.C_ID=".$C_ID;
	if ( $TE_CODE == 'FOR' ) $queryr .=" and cr.TCR_CODE='RF'";
	else $queryr .=" and cr.TCR_CODE='RO'";
	$resultr=mysql_query($queryr);
	$rowr=mysql_fetch_array($resultr);
	$TCR_DESCRIPTIONr =  $rowr["TCR_DESCRIPTION"];
	$P_IDr 		=  $rowr["P_ID"];
	$P_NOMr 	=  $rowr["P_NOM"];
	$P_PRENOMr 	=  $rowr["P_PRENOM"];
	$P_PHONEr 	=  $rowr["P_PHONE"];
	if 	( $P_IDr <> "" ) {
		if ($P_PHONEr <> '') {
			if ($iphone)
 				$phone=" (<a href='tel:".$P_PHONEr."'>".$P_PHONEr."</a>)";
 			else 
				$phone = " (".$P_PHONEr.")";
		}
		echo "<tr><td width=25% align=right><font size=1>".$TCR_DESCRIPTIONr.": </font></td>";
		echo "<td width=75%><font size=1>
		<a href=upd_personnel.php?pompier=".$P_IDr.">".my_ucfirst($P_PRENOMr)." ".strtoupper($P_NOMr)."</a>".$phone."</font></td></tr>";
		
	}
 
}

if ( $E_CONTACT_LOCAL <> '' or $E_CONTACT_TEL <> '') {
 	echo "<tr><td width=25% align=right><font size=1>Contact sur place: </font></td>";
 	if ( $E_CONTACT_TEL <> '') {
 		if ($iphone) $E_CONTACT_TEL="( <a href='tel:".$E_CONTACT_TEL."'>".$E_CONTACT_TEL."</a>) ";
 		else $E_CONTACT_TEL="( ".$E_CONTACT_TEL." )";
 	}
 	echo "<td width=75%><font size=1>".$E_CONTACT_LOCAL." ".$E_CONTACT_TEL."</font></td></tr>";	
}

// équipes, groupes (seulement pour événement principal)
if ($E_PARENT == '' and  $nbsections == 0) {
	$querym="select EE_NAME, EE_DESCRIPTION from evenement_equipe
		where E_CODE=".$evenement."
		order by EE_NAME";
	$resultm=mysql_query($querym);
	$nbm=mysql_num_rows($resultm);

	$showcpt = "<tr><td width=30%><b>Equipes: </b></td><td>";

	while ( $rowm=mysql_fetch_array($resultm) ) {
 		$type=$rowm["EE_NAME"];
 		$desc=$rowm["EE_DESCRIPTION"];
 		$showcpt .= " <a title=\"".$desc."\">".$type."</a>,";
	}
	$showcpt = rtrim($showcpt,',');

	if ( $granted_event and (!$print))
	$showcpt .= " <img src=images/page_white_text.png
				  title=\"Modifier l'organisation en équipes\"
				  border=0 
				  height=14 
				  onclick=\"modifier_equipes('".$evenement."',".$i.")\">  ";           
	$showcpt .= "</td></tr>";
	print $showcpt;
}

if ($nbsections == 0 ) {
//------------------------
// Renforts
//------------------------
	$queryR="select e.E_CODE as CE_CODE, e.E_CANCELED as CE_CANCELED, e.E_CLOSED as CE_CLOSED,
			s.S_CODE CS_CODE, s.S_DESCRIPTION CS_DESCRIPTION,
			DATE_FORMAT(eh.EH_DATE_DEBUT, '%d-%m') as EH_DATE_DEBUT0,
			DATE_FORMAT(eh.EH_DATE_FIN, '%d-%m') as EH_DATE_FIN0,
			TIME_FORMAT(eh.EH_DEBUT, '%k:%i') EH_DEBUT0,  
			TIME_FORMAT(eh.EH_FIN, '%k:%i') EH_FIN0
	        from evenement e, section s, evenement_horaire eh
			where e.S_ID = s.S_ID
			and eh.E_CODE = e.E_CODE
			and e.E_PARENT=".$evenement."
			order by e.E_CODE, eh.EH_ID";
	$resultR=mysql_query($queryR);
	if ( mysql_num_rows($resultR) > 0 ) {
		echo "<tr><td colspan=2><b>Evénements renforts:</b></td></tr>";
		$prevEC=""; $first=true;
	    // affiche les infos pour ce renfort
		while ( $rowR=@mysql_fetch_array($resultR)) {
	 		$CE_CODE=$rowR["CE_CODE"];
	 		$CE_CANCELED=$rowR["CE_CANCELED"];
	 		$CE_CLOSED=$rowR["CE_CLOSED"];
			$CS_CODE=$rowR["CS_CODE"];
			$CS_DESCRIPTION=$rowR["CS_DESCRIPTION"];
			$EH_DATE_DEBUT0=$rowR["EH_DATE_DEBUT0"];
	  		$EH_DATE_FIN0=$rowR["EH_DATE_FIN0"];
	  		$EH_DEBUT0=$rowR["EH_DEBUT0"];
	  		$EH_FIN0=$rowR["EH_FIN0"];
	  		
	  		if ( $EH_DATE_DEBUT0 <> $EH_DATE_FIN0 ) $dates_renfort=$EH_DATE_DEBUT0 ." au ".$EH_DATE_FIN0;
			else $dates_renfort=$EH_DATE_DEBUT0;
			$detail_renfort=$dates_renfort." - ".$EH_DEBUT0."-".$EH_FIN0;
			
			if ( $CE_CANCELED == 1 ) {
			 	$color="red";
			 	$info="événement annulé";
			}
	  		elseif ( $CE_CLOSED == 1 ) {
			   	$color="orange";
			   	$info="événement clôturé";
			}
	  		else {
			   	$color="green";
			   	$info="événement ouvert";
			}
			if (($granted_event) and (! $print))
      			$cancelbtn = "<a href=\"javascript:cancel_renfort('".$evenement."','".$CE_CODE."')\"><img src=images/trash.png title='annuler ce renfort' border=0></a>";
      		else $cancelbtn ='';
			
			if ( $CE_CODE <> $prevEC ) {
			    if ($first) $first=false;
			    else echo "</td></tr>";
				echo "<td colspan=2><a href=evenement_display.php?evenement=".$CE_CODE.">
				<img src=images/renfort_".$color.".png border=0 title='$info' > 
				Renfort de ".$CS_CODE." - ".$CS_DESCRIPTION."</a>";
				if ( mysql_num_rows($resultR) == 1 ) echo " <font size=1>".$detail_renfort."</font>";
				echo $cancelbtn." ";
			}
			if ( $CE_CANCELED == 0 ) $clock="clock_green.png";
			else {
			 	$clock="clock_red.png";
			 	$detail_renfort = "ANNULE : ".$detail_renfort;
			}
			echo "<img border=0 src=images/".$clock." title=\"$detail_renfort\">";
			
			$prevEC=$CE_CODE;
		}
		echo "</td></tr>";
		echo "<p>";
	}
}


//------------------------
// Soins / Evac
//------------------------


$queryN="select TB_NUM,TB_LIBELLE from type_bilan where TE_CODE='".$TE_CODE."'";
$resultN=mysql_query($queryN);
if (( mysql_num_rows($resultN) > 0 ) and ( $E_CLOSED == 1 )) {
	while ( $rowR=@mysql_fetch_array($resultN)) {
		$TB_NUM=$rowR["TB_NUM"];
		$TB_LIBELLE=$rowR["TB_LIBELLE"]; 
		switch ($TB_NUM) {
			case 1:
        		$cpt=$E_NB1;
        		break;
			case 2:
        		$cpt=$E_NB2;
        		break;
        	case 3:
        		$cpt=$E_NB3;
        		break;
        	case 4:
        		$cpt=$E_NB4;
        		break;
        	case 5:
        		$cpt=$E_NB5;
        		break;
        	case 6:
        		$cpt=$E_NB6;
        		break;
        	case 7:
        		$cpt=$E_NB7;
        		break;
        	case 8:
        		$cpt=$E_NB8;
        		break;
        	case 9:
        		$cpt=$E_NB9;
        		break;
        	case 10:
        		$cpt=$E_NB10;
        		break;
        	case 11:
        		$cpt=$E_NB11;
        		break;
		}
		if ((! $print and $granted_event) or $cpt > 0 ) {
			echo "<tr>";
			echo "<td width=25%><b>".str_replace('(hors évac.)','</b><font size=1>(hors évac.)</font>',$TB_LIBELLE)."</td>
	  	 	<td width=75%>";
	  		if ( $print or (! $granted_event))  echo $cpt;
	  		else echo "<input type='text' size=3 id='nombre".$TB_NUM."' name='nombre".$TB_NUM."' value='$cpt' 
		  	title=\"saisir ici le nombre de ".$TB_LIBELLE." réalisés sur cet événement\"
		   	onchange='updatenumber(\"nombre".$TB_NUM."\",\"".$evenement."\",\"".$TB_NUM."\",this.value,\"$cpt\")'>";
			echo "</select></td>";	   
    		echo "</tr>";
    	}
    }
}

//------------------------
// type de formation
//------------------------

if ( $TE_CODE == 'FOR' ){ 
if ($granted_event) $readonly="";
else $readonly="disabled";

// si des diplômes ont été données sur cette formation, interdire de changer  ces paramètres
$queryf="select count(*) as NB from personnel_formation where E_CODE=".$evenement;
$resultf=mysql_query($queryf);
$rowf=@mysql_fetch_array($resultf);
if  ( $rowf["NB"] > 0 ) $readonly="disabled";

echo "<tr>";
echo "<td width=25%><b>formation pour</td>
	  	 <td width=75%>";
echo "<select id='ps' name='ps' 
		  title='saisir ici le type de compétence ou le diplôme obtenu grâce à cette formation' $readonly
		  onchange='updateformation(\"".$evenement."\",\"ps\",this.value)'>";
$selected=( is_null($PS_ID) ? "selected":"");
echo "<option value=\"NULL\" $selected>non renseigné</option>\n"; // NULL par défaut		  
$query2="select PS_ID, TYPE, DESCRIPTION from poste 
		 where PS_DIPLOMA=1
      	 order by TYPE asc";
$result2=mysql_query($query2);
while ($row2=@mysql_fetch_array($result2)) {
		$_PS_ID=$row2["PS_ID"];
		$_TYPE=$row2["TYPE"];
		$_DESCRIPTION=$row2["DESCRIPTION"];		 
		$selected = (($PS_ID == $_PS_ID && !is_null($PS_ID)) ?"selected":"");
		echo "<option value=".$_PS_ID." $selected>".$_TYPE." - ".$_DESCRIPTION."</option>\n";
}
echo "</select></td>";	   
echo "</tr>";

if (is_null($PS_ID)) $disabled_tf='disabled';
else $disabled_tf='';
echo "<td width=25%><b>type de formation</td>
	  	 <td width=75%>";
echo "<select id='tf' name='tf' 
		  title='saisir ici le type de formation' $readonly $disabled_tf
		  onchange='updateformation(\"".$evenement."\",\"tf\",this.value)'>";
$selected=( is_null($TF_CODE) ? "selected":"");
echo "<option value=\"NULL\" $selected>non renseigné</option>\n"; // NULL par défaut		  
$query2="select TF_CODE, TF_LIBELLE from type_formation";
$result2=mysql_query($query2);
while ($row2=@mysql_fetch_array($result2)) {
		$_TF_CODE=$row2["TF_CODE"];
		$_TF_LIBELLE=$row2["TF_LIBELLE"];		 
		$selected = (($TF_CODE == $_TF_CODE && !is_null($TF_CODE)) ?"selected":"");
		echo "<option value=".$_TF_CODE." $selected>".$_TF_LIBELLE."</option>\n";
}
echo "</select></td>";	   
echo "</tr>";
}

echo "</table>";

echo "</tr>";

//=====================================================================
// email notifications
//=====================================================================

if (( $granted_event ) and (! $print))  {

   echo "<tr><td CLASS='MenuRub'>Notifications par email</td></tr>";
   echo "<tr><td CLASS='Menu'>";

   echo "<table cellspacing=0 border=0 width=550>";
 	
 	// email ouverture
 	if (( $E_CLOSED == 0 ) and ( $E_CANCELED == 0 )) {
		echo "<tr><td width=50><img src=images/email.png></td>";
 		echo "<td width=400><font size=1 >
	 	Avertir tout le personnel que le nouvel événement a été créé. </td>";
 		if ( $E_MAIL1 == 0 ) {
 			echo "<td width=100><input type=submit value='envoyer' onclick='bouton_redirect(
			 \"evenement_notify.php?evenement=".$evenement."&action=enroll\",\"notify\");'></td></tr>";
 		}
 		else echo "<td width=100><img src=images/button_ok.png alt='déjà envoyé'></td></tr>";
 	}
 	
 	// email cloture
 	if (( $E_CLOSED == 1 ) and ( $E_CANCELED == 0 )) {
		echo "<tr><td width=50><img src=images/email.png></td>";
 		echo "<td width=400><font size=1 >
	 		Envoyer au personnel inscrit (liste validée) les informations relatives à l'événement.</td>";
 		if ( $E_MAIL2 == 0 ) {
 			echo "<td width=100><input type=submit value='envoyer' onclick='bouton_redirect(
			 \"evenement_notify.php?evenement=".$evenement."&action=closed\",\"notify\");'></td></tr>";
 		}
 		else echo "<td width=100><img src=images/button_ok.png alt='déjà envoyé'></td></tr>";
 	}
 		
 	// email annulation
 	if ($E_CANCELED == 1 ) {
		echo "<tr><td width=50><img src=images/email.png></td>";
 		echo "<td width=400><font size=1>
	 	Avertir le personnel inscrit que l'événement a été annulé.</td>";
 		if ( $E_MAIL3 == 0 ) {
 			echo "<td width=100><input type=submit value='envoyer' 
		 	onclick='bouton_redirect(\"evenement_notify.php?evenement=".$evenement."&action=canceled\",\"notify\");'></td></tr>";
 		}
 		else echo "<td width=100><img src=images/button_ok.png alt='déjà envoyé'></td></tr>";
 	}	
	
 echo "</table>";
 
 echo "</tr>";
 
}

//=====================================================================
// bouton de retour et de modification
//=====================================================================
echo "</table></td>";

if (! $print) {
   echo "<tr height=40><td align=center>";
   if ( $from == "export" ) {
 	   echo "<input type=submit value='fermer cette page' onclick='fermerfenetre();'> ";
   }
   elseif ( $from == "personnel" ) {
 	   echo "<input type=submit value='retour' 
      onclick='bouton_redirect(\"upd_personnel.php?from=inscriptions&pompier=".$pid."\");'> ";
   }
   elseif ( $from == "diplomes" or $from == "history" ) {
       if ( ! $iphone)
 	   		echo "<input type=submit value='retour' onclick='javascript:history.back(1);'> ";
   }
   else {
      echo "<input type=submit value='retour' 
      onclick='bouton_redirect(\"evenement_choice.php\");'> ";
   }

   if ( $granted_event ) {
      echo " <input type=submit value='modifier' 
      onclick='bouton_redirect(\"evenement_edit.php?evenement=".$evenement."&action=update\",\"update\");'> ";
      echo " <input type=submit value='dupliquer' title='dupliquer cet événement'
      onclick='bouton_redirect(\"evenement_edit.php?evenement=".$evenement."&action=copy\",\"copy\");'> ";
   }
   if (($nbsections == 0 ) 
   		and  check_rights($id, 15) 
	  	and ( $E_ALLOW_REINFORCEMENT == 1 ) 
		and ( $E_PARENT == '' )) {
			if ( $E_CLOSED == 1 or $E_CANCELED == 1 ) $disabled='disabled';
			else $disabled="";
      		echo " <input type=submit value='créer renfort' title='créer un événement en renfort de celui-ci' $disabled
      		onclick='bouton_redirect(\"evenement_edit.php?evenement=".$evenement."&action=renfort\",\"renfort\");'> ";
      }
   if ( $granted_delete ) {
      echo " <input type=submit value='supprimer' 
      onclick='bouton_redirect(\"evenement_save.php?action=delete&evenement=".$evenement."\",\"delete\");'> ";
   }
}

//=====================================================================
// boutons d'inscription /désinscription
//=====================================================================
if (( $E_CLOSED == 0 ) and ( $E_CANCELED == 0 ) and (! $print )){
   if ( my_date_diff(date('d')."-".date('m')."-".date('Y'),$day1[1]."-".$month1[1]."-".$year1[1]) >= 0 ) {
   $query="select DATEDIFF(NOW(), ep.EP_DATE) as NB_DAYS 
   			from evenement_participation ep, evenement e
   			where ep.E_CODE = e.E_CODE
			and ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")
			and ep.P_ID=".$id;
   $r1=mysql_query($query);
   $num=mysql_num_rows($r1);
   if ( $num == 0 ) {
     if (( $OPEN_TO_ME == 1 ) and check_rights($id, 39))
      echo " <input type=submit value=\"s'inscrire\" 
	   onclick='bouton_redirect(\"evenement_inscription.php?evenement=".$evenement."&action=inscription\",\"inscription\");'> ";
	 if ( $OPEN_TO_ME == -1 )
      echo " <input type=submit value=\"s'inscrire\" 
	   onclick=\"alert('Votre inscription sur cet événement extérieur ne peut être faite que par votre responsable.');\"> ";
	if (( $OPEN_TO_ME == -2 ) or ( $OPEN_TO_ME == -3 ))
      echo " <input type=submit value=\"s'inscrire\" 
	   onclick=\"alert('Votre inscription sur cet événement national ou régional ne peut être faite que par votre responsable.');\"> ";
   }
   else {
     $row=mysql_fetch_array($r1);
     if ( $row["NB_DAYS"] < 1 ) $disabled='';
     else if ( $granted_event ) $disabled='';
     else $disabled="disabled";

	 if (check_rights($id, 39))
     	echo " <input type=submit value=\"se désinscrire\" $disabled onclick='bouton_redirect(\"evenement_inscription.php?evenement=".$evenement."&action=desinscription\",\"desinscription\");'>";
     }
  }
   if ( $granted_event ) {
   echo " <input type=submit value='clôturer' title='fermer les inscriptions pour cet événement et ses renforts'
   onclick='bouton_redirect(\"evenement_inscription.php?evenement=".$evenement."&action=close\",\"close\");' > ";
  }
  
}
if (( $E_CLOSED == 1 ) and ( $E_CANCELED == 0 ) and (! $print )) {
   if ( $granted_event ) {
   echo " <input type=submit value='ouvrir' title='ouvrir les inscriptions pour cet événement et ses renforts' 
   onclick='bouton_redirect(\"evenement_inscription.php?evenement=".$evenement."&action=open\",\"open\");' >";
  } 
}
echo "</tr></TABLE>";

}

//=====================================================================
// participants
//=====================================================================
if (( $tab == 2 ) or ($print)){
  
if ( $E_NB == 0 ) $cmt = "Pas de limite sur le nombre de participants";
else $cmt = "requis ".$E_NB;

$queryf="select tp.TP_ID, tp.TP_LIBELLE, tp.TP_NUM, tp.PS_ID, p.TYPE
	  	from type_participation tp
	  	left join poste p
	  	on p.PS_ID=tp.PS_ID
	  	where tp.TE_CODE='".$TE_CODE."'
		order by tp.TP_NUM";

$queryd="select count(*) as NB from type_participation 
	  	where TE_CODE='".$TE_CODE."'"; 
$resultd=mysql_query($queryd);
$rowd=@mysql_fetch_array($resultd);
$nbfn=$rowd["NB"];

$querye="select E_CODE,	EE_ID, EE_NAME, EE_DESCRIPTION
		 from evenement_equipe 
		 where E_CODE=".$evenement."
		 order by EE_NAME";
$queryenb="select count(*) as NB
		 from evenement_equipe
		 where E_CODE=".$evenement;
$resultenb=mysql_query($queryenb);
$rowenb=@mysql_fetch_array($resultenb);
$nbe=$rowenb["NB"];

echo "<tr CLASS='Menu'><td><table cellspacing=0>";

// trouver tous les participants
$query="select distinct ep.E_CODE as EC, p.P_ID, p.P_NOM, p.P_PHONE, p.P_PRENOM, p.P_GRADE, s.S_ID, 
		p.P_HIDE, p.P_STATUT, p.P_OLD_MEMBER, s.S_CODE, p.P_EMAIL, p.C_ID,
		EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),p.P_BIRTHDATE))))+0 AS age
		from evenement_participation ep, pompier p, section s
        where ep.E_CODE in (".$evts.")
		and p.P_ID=ep.P_ID
		and p.P_SECTION=s.S_ID
		order by ep.E_CODE asc, ep.EP_DATE asc, p.P_NOM";
$result=mysql_query($query);

$listePompiers = "";
$mailist = "";

if ( mysql_num_rows($result) > 0 ) {
 	echo "<table>";
	echo "<tr>
	<td class='FondMenu'>";
	echo "<table cellspacing=0 border=0>";
	echo "<tr CLASS='TabHeader'><td colspan=5>Participants: ".$cmt." , inscrits $NP</td></tr>";
	echo "<tr class='Menu'><td width=220><i><b>Personnel inscrit</b><font size=1> (par ordre d'inscription):</font></i><br>";
	if ( ! $print) echo "<td width=100><font size=1><i>Horaires</i></font></td>";
	if ( $nbfn > 0 ){
	 	if ($granted_event and (!$print)) echo "<td width=100 colspan=2><font size=1><i>Fonction</i></font></td>";
	 	else echo "<td width=100><font size=1><i>Fonction</i></font></td>";
	}
	else echo "<td></td>";
	if ($E_PARENT == '' ) echo "<td width=100><font size=1><i>Equipe</i></font></td>";
	else echo "<td></td>";
	if (! $print) echo "<td><font size=1><i>Infos</i></font></td>";
	echo "<td colspan=3></td>";

	$prevEC='';
	while ($row=@mysql_fetch_array($result)) {
	  $EC=$row["EC"];
	  // affiche les infos pour ce renfort
	  if ( $EC <> $prevEC ) {
	    $queryR="select e.E_CANCELED as CE_CANCELED, e.E_CLOSED as CE_CLOSED, eh.EH_ID,
			s.S_CODE CS_CODE, s.S_DESCRIPTION CS_DESCRIPTION,
			DATE_FORMAT(eh.EH_DATE_DEBUT, '%d-%m') as EH_DATE_DEBUT0,
			DATE_FORMAT(eh.EH_DATE_FIN, '%d-%m') as EH_DATE_FIN0,
			TIME_FORMAT(eh.EH_DEBUT, '%k:%i') EH_DEBUT0,  
			TIME_FORMAT(eh.EH_FIN, '%k:%i') EH_FIN0
	        from evenement e, section s, evenement_horaire eh
			where e.S_ID = s.S_ID
			and eh.E_CODE = e.E_CODE
			and e.E_CODE=".$EC;
		$resultR=mysql_query($queryR);
		$EH_DATE_DEBUT0 = Array();
		$EH_DATE_DEBUT0 = Array();
		$EH_DEBUT0 = Array();
		$EH_FIN0 = Array();
		$horaire_renfort = Array();
		
		while ( $rowR=@mysql_fetch_array($resultR)) {
		    $n=$rowR["EH_ID"];
			$EH_DATE_DEBUT0[$n]=$rowR["EH_DATE_DEBUT0"];
	    	$EH_DATE_FIN0[$n]=$rowR["EH_DATE_FIN0"];
	    	$EH_DEBUT0[$n]=$rowR["EH_DEBUT0"];
	    	$EH_FIN0[$n]=$rowR["EH_FIN0"];
	 		$CE_CANCELED=$rowR["CE_CANCELED"];
	 		$CE_CLOSED=$rowR["CE_CLOSED"];
			$CS_CODE=$rowR["CS_CODE"];
			$CS_DESCRIPTION=$rowR["CS_DESCRIPTION"];
			if ( $CE_CANCELED == 1 ) {
			 	$color="red";
			 	$info="événement annulé";
			}
	  		elseif ( $CE_CLOSED == 1 ) {
			   	$color="orange";
			   	$info="événement clôturé";
			}
	  		else {
			   	$color="green";
			   	$info="événement ouvert";
			}
			if ( $EH_DATE_DEBUT0[$n] <> $EH_DATE_FIN0[$n] ) $dates_renfort=$EH_DATE_DEBUT0[$n] ." au ".$EH_DATE_FIN0[$n];
			else $dates_renfort=$EH_DATE_DEBUT0[$n];
			$horaire_renfort[$n]=$dates_renfort." - ".$EH_DEBUT0[$n]."-".$EH_FIN0[$n];
		}
		if ( $EC <> $evenement ) {
			if ( mysql_num_rows($resultR) == 1 ) $dt=$horaire_evt[1];
			else $dt="";
	  		echo "<tr class='Menu'><td>
		  		<b><i><a href=evenement_display.php?evenement=$EC&from=inscription>
		 	 	<img src=images/renfort_".$color.".png border=0 title='$info' >
		  		Renfort de ".$CS_CODE."</i></b></a>
		  		</td>
		  		<td colspan=8><b><i>".$dt."</i></b></td></tr>";  	
		}
		
		$prevEC = $EC;
	  }

      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_STATUT=$row["P_STATUT"];
      $P_OLD_MEMBER=$row["P_OLD_MEMBER"];
      $C_ID=$row["C_ID"];
      $P_EMAIL=$row["P_EMAIL"];
      $P_ID=$row["P_ID"];
      $S_ID=$row["S_ID"];
      $AGE=$row["age"];
      $P_HIDE=$row["P_HIDE"]; 
      $S_CODE=$row["S_CODE"];
      if ( $P_EMAIL <> "" ) $mailist .= $P_EMAIL.";";
      if ( $row["P_PHONE"] <> '' ) {
			if ($iphone)
 				$P_PHONE=" (<a href='tel:".$row["P_PHONE"]."'>".$row["P_PHONE"]."</a>)";
	   		else $P_PHONE=" (".$row["P_PHONE"].")";	
			if (( ($P_HIDE == 1) ) and ( $nbsections == 0 )) {
	  			if (( ! $ischef ) 
				and ( $E_CHEF <> $id )
				and (! check_rights($id, 2))
				and (! check_rights($id, 12)))
	  				$P_PHONE=" (**********)";
	  		}
	  }
	  else $P_PHONE="";
	  $listePompiers .= $P_ID.","; 	  
	  
	  if ( $nbsections <> 1 ) { 
          if ( is_children($S_ID,$organisateur)) $prio=true; 
          else $prio=false; 
      } 
      else $prio=true; 
      $P_GRADE=$row["P_GRADE"];
      if ( check_rights($id, 10,"$S_ID")) $granted_update=true;
      else $granted_update=false;
      
      // récupérer horaires de la personne
      $clock="";
      for ($i=1; $i <= $nbmaxsessionsparevenement; $i++) {
        if ( isset ($horaire_renfort[$i])) {
          $query_horaires="select  EH_ID,
		   DATE_FORMAT(EP_DATE, '%d/%m %H:%i') as EP_DATE, 
		   DATE_FORMAT(EP_DATE_DEBUT,'%d-%m-%Y') EP_DATE_DEBUT, 
		   DATE_FORMAT(EP_DATE_FIN,'%d-%m-%Y') EP_DATE_FIN,
		   TIME_FORMAT(EP_DEBUT, '%k:%i') EP_DEBUT,  
		   TIME_FORMAT(EP_FIN, '%k:%i') EP_FIN,
		   DATE_FORMAT(EP_DATE_DEBUT,'%Y-%m-%d') EP_DATE_DEBUT1,
		   DATE_FORMAT(EP_DATE_FIN,'%Y-%m-%d') EP_DATE_FIN1
		   from evenement_participation ep
           where E_CODE=".$EC."
           and EH_ID = ".$i."
		   and P_ID=".$P_ID;
	      $resultH=mysql_query($query_horaires);
	      $rowH=@mysql_fetch_array($resultH);
	      $EH_ID=$rowH["EH_ID"];
	      if ( $EH_ID <> "" ) {
      		$EP_DATE_DEBUT=$rowH["EP_DATE_DEBUT"];    // DD-MM-YYYY
      		$EP_DATE_FIN=$rowH["EP_DATE_FIN"];		  
      		$EP_DATE_DEBUT1=$rowH["EP_DATE_DEBUT1"];  // YYYY-MM-DD
      		$EP_DATE_FIN1=$rowH["EP_DATE_FIN1"];
      		$EP_DEBUT=$rowH["EP_DEBUT"];
      		$EP_FIN=$rowH["EP_FIN"];
      
	  		if ($nbsessions == 1 ) $t=" de l'événement";
           	else $t=" de la partie n°$EH_ID";			
      		if ( $EP_DATE_DEBUT <> "" ) {
           		if ( $EP_DATE_DEBUT1 == $EH_DATE_DEBUT0[$i] and $EP_DATE_FIN1 == $EH_DATE_FIN0[$i] ) $horaire_p=$EP_DEBUT."-".$EP_FIN;
           		else if ( $EP_DATE_DEBUT == $EP_DATE_FIN ) $horaire_p= substr($EP_DATE_DEBUT,0,5).", ".$EP_DEBUT."-".$EP_FIN;
           		else $horaire_p= substr($EP_DATE_DEBUT,0,5)." au ".substr($EP_DATE_FIN,0,5).", ".$EP_DEBUT."-".$EP_FIN;

           		$clock .="<img border=0 src=images/clock_yellow.png title=\"horaires différents de ceux $t \n$horaire_p \ncliquer pour modifier\">";
      		}
	  		else $clock .="<img border=0 src=images/clock_green.png title=\"horaires identiques à ceux $t \n".$horaire_renfort[$i]." \ncliquer pour modifier\">";
        }
        else $clock .="<img border=0 src=images/clock_red.png title=\"pas inscrit à cette partie \ncliquer pour modifier\">";
       }
     }
      
      // récupérer détails de la personne
      $subq="select min(EH_ID) as EH_ID from evenement_participation where E_CODE=".$EC." and P_ID=".$P_ID;
      $resultq=mysql_query($subq);
	  $rowq=@mysql_fetch_array($resultq);
      
      // les équipes ne peuvent être définies que sur l'événeù
      $queryD="select ep.EP_FLAG1, ep.TP_ID, ep.EE_ID, ep.EP_COMMENT, ep.EP_KM, ep.EP_BY, ep.EP_DATE , ee.EE_NAME, ee.EE_DESCRIPTION
	  	   from evenement_participation ep
	  	   left join evenement_equipe ee on (ee.E_CODE=".$evenement." and ee.EE_ID=ep.EE_ID)
           where ep.E_CODE=".$EC."
		   and ep.P_ID=".$P_ID."
		   and ep.EH_ID=".$rowq["EH_ID"];
	  $resultD=mysql_query($queryD);
	  $rowD=@mysql_fetch_array($resultD);

      $EP_FLAG1=$rowD["EP_FLAG1"];
      $EP_KM=$rowD["EP_KM"];
      $EP_COMMENT=$rowD["EP_COMMENT"];
      $EP_DATE=$rowD["EP_DATE"];
      $EE_ID=$rowD["EE_ID"];
      $EE_NAME=$rowD["EE_NAME"];
      $EE_DESCRIPTION=$rowD["EE_DESCRIPTION"];
      
      if ( $EP_FLAG1 == 1 ) $txtimg="texte3.png";
	  else if ( $EP_COMMENT <> '' ) $txtimg="texte2.png";
	  else $txtimg="texte.png";
      $TP_ID=$rowD["TP_ID"];
      $EP_BY=$rowD["EP_BY"];
      if ( $TP_ID <> "" ) {
      		$queryTP="select tp.TP_LIBELLE, tp.PS_ID, tp.PS_ID2, p.TYPE
			  			from type_participation tp
						left join poste p
						on p.PS_ID=tp.PS_ID
						where tp.TP_ID=".$TP_ID;
      		$resultTP=mysql_query($queryTP);
			$rowTP=@mysql_fetch_array($resultTP);
      		$TP_LIBELLE=$rowTP["TP_LIBELLE"];
      		$F_PS_ID=$rowTP["PS_ID"];
			$F_PS_ID2=$rowTP["PS_ID2"];
      		$F_TYPE=$rowTP["TYPE"];
      }
      else { $F_PS_ID=0; $F_PS_ID2=0; }
      if ( $EP_BY <> "" and $EP_BY <> $P_ID) {
      		$queryBy="select P_NOM, P_PRENOM from pompier where P_ID=".$EP_BY;
      		$resultBy=mysql_query($queryBy);
			$rowBy=@mysql_fetch_array($resultBy);
      		$P_NOM_By=$rowBy["P_NOM"];
      		$P_PRENOM_By=$rowBy["P_PRENOM"];
      		$inscritPar="par ".strtoupper($P_NOM_By)." ".my_ucfirst($P_PRENOM_By);
      }
      else $inscritPar="";
      $popup="Inscrit le: ".$EP_DATE;
	  if ( $EP_FLAG1 == 1 ) $popup .= "
Participation en tant que salarié(e)";
	  if ( $EP_COMMENT <> "" ) $popup .= "
Commentaire: ".$EP_COMMENT;
	  if ( $EP_KM <> "" ) $popup .= "
Kilomètres: ".$EP_KM;
      
      // récupérer compétences de la personne
      $postes=""; $myimg="";
      $querys="select p.PS_ID, p.TYPE, p.DESCRIPTION , q.Q_VAL, cea.FLAG1,
	  			q.Q_EXPIRATION,  DATEDIFF(q.Q_EXPIRATION,NOW()) as NB
	  			from poste p, qualification q, equipe e, categorie_evenement_affichage cea, type_evenement te
      			where q.PS_ID=p.PS_ID
      			and cea.EQ_ID = e.EQ_ID
      			and cea.CEV_CODE = te.CEV_CODE
				and te.TE_CODE='".$TE_CODE."'
      			and e.EQ_ID = p.EQ_ID
      			and e.EQ_TYPE='COMPETENCE'
      			and q.P_ID=".$P_ID;
      $results=mysql_query($querys);
      
      $querycnt="select count(*) as COUNT
	  			from poste p, qualification q, equipe e, categorie_evenement_affichage cea, type_evenement te
      			where q.PS_ID=p.PS_ID
				and cea.EQ_ID = e.EQ_ID
      			and cea.CEV_CODE = te.CEV_CODE
				and te.TE_CODE='".$TE_CODE."'
      			and cea.FLAG1 = 1
      			and e.EQ_ID = p.EQ_ID
      			and e.EQ_TYPE='COMPETENCE'
      			and q.P_ID=".$P_ID;
      $resultcnt=mysql_query($querycnt);
      $rowcnt=@mysql_fetch_array($resultcnt);
      $max=$rowcnt["COUNT"];	

	  $nb=1; $found=false;
      while ($rows=@mysql_fetch_array($results)) {
           $DESCRIPTION=$rows["DESCRIPTION"];
           $FLAG1=$rows["FLAG1"];
           $PS_ID=$rows["PS_ID"];
           if (( $F_PS_ID + $F_PS_ID2 > 0 ) and (! $found)) {
           		if ( $F_PS_ID == $PS_ID ) $found=true;
				else if ( $F_PS_ID2 == $PS_ID ) $found=true;
           }
           $TYPE=$rows["TYPE"];
           $Q_VAL=$rows["Q_VAL"];
           $Q_EXPIRATION=$rows["Q_EXPIRATION"];
           $NB=$rows["NB"];
           if ( $Q_VAL == 1 ) $mycolor='green';
 		   else $mycolor='darkblue';
		   if ( $Q_EXPIRATION <> '') {
			   if ($NB < 61) $mycolor='orange';
 			   if ($NB <= 0) $mycolor='red';
 		   }
 		   if (( $TYPE == 'PSE1' ) and ($nbsections == 0 )) $TYPE='<span style="background:#FFFF00">PSE1</span>';
 		   if ( $FLAG1 == 1) {
           		$postes .="<a href=upd_personnel.php?pompier=".$P_ID." title=\"".$DESCRIPTION."\")>
			   		<font size=1 color=$mycolor>".$TYPE."</font></a>"; 
		   		if ( $nb <  $max )  $postes .= "<font size=1> , </font>";
		   		$nb++;
		  } 
      } 
      
      if ( $nbsessions == 1 ) {
	  	$nb = get_nb_inscriptions($P_ID, $year1[1], $month1[1], $day1[1], $year2[1], $month2[1], $day2[1]) - 1 ;
	  	$dispo = is_dispo($P_ID,$year1[1], $month1[1], $day1[1], $year2[1], $month2[1], $day2[1]);
	  	if ( $nbsections > 0 )
	  		$nbgardes = get_nb_gardes ($P_ID,$year1[1], $month1[1], $day1[1], $year2[1], $month2[1], $day2[1]);
	  	else 
	  		$nbgardes=0;
	  	if ( $nbgardes > 0 ) 
	  		$myimg="<img src=images/red.gif title='attention cet agent est parallèlement inscrit $nbgardes fois sur le tableau de garde'>";
	  	else if ( $nb > 1 ) 
	   		$myimg="<img src=images/red.gif title='attention cet agent est parallèlement inscrit sur $nb autres événements'>";
	  	else if ( $nb == 1 )
	  		$myimg="<img src=images/yellow.gif title='attention cet agent est parallèlement inscrit sur 1 autre événement'>";
	  	else if ( $dispo > 0 )
	  		$myimg="<img src=images/blue.gif title='remarque: cet agent est aussi marqué disponible sur la période de cet événement'>";
	  }
	  else $myimg="";
	  
	  $cmt=""; 
	  if ( $P_OLD_MEMBER > 0 ) {
	  	  $altcolor="<font color=black>";
	  	  $extcmt="ATTENTION: Ancien membre";	  
	  }
	  else if ( $P_STATUT=='EXT') {
	  	  $altcolor="<font color=green>";
	  	  $extcmt="Personnel externe ".get_company_name("$C_ID");	
	  }
	  else {
	  	  $altcolor=(($prio)?"":"<font color=purple>");
	  	  $extcmt=$S_CODE;
	  }
	  if ( $AGE <> '' )
	  	if ($AGE < 18 ) $cmt="<font size=1 color=red>(-18)</font>";
	
	 // nouvelle ligne
      echo "<tr class='Menu'><td><font size=1>
      <a href=upd_personnel.php?pompier=$P_ID title=\"$extcmt\">".$altcolor.strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM)."</a> ".$cmt." ".$P_PHONE."</font></td>";
      


	 // affiche horaires
	  if ( ! $print) {
		if ($granted_event or ($P_ID == $id and $E_CLOSED == 0) or ($granted_update and $E_CLOSED == 0))
          		echo "<td><a href=\"javascript:horaires('$EC','$P_ID',0);\">".$clock."</a></td>";			
		else 
				echo "<td>".$clock."</td>";
      }

	  // affiche fonctions / équipes
      if (($granted_personnel or $granted_event or $granted_update) 
	  and (! $print)) {
          if ( $nbfn > 0 ) {
            $warnflag="";
		//echo $F_PS_ID;
		//if (! $found) { echo "found est daux";}
            if (($F_PS_ID + $F_PS_ID2 ) > 0 and (! $found)) 
					$warnflag="<img src=images/miniwarn.png 
					title=\"Attention cette personne n'est pas qualifiée pour assurer cette fonction\">";
            if (! $granted_event) {
             	if ( $TP_ID == "" or $TP_ID == 0 ) $TP_LIBELLE="";
			 	echo  "<td><font size=1>".$TP_LIBELLE." </font></a> ".$warnflag."</td>";
			}
            else {
             	// choix fonction
      			if ( $TP_ID == "" or $TP_ID == 0 ) $TP_LIBELLE="<font size=1 color=grey><i>choisir</i></font>";
      			echo "<td><a href=\"javascript:ReverseContentDisplay('r".$P_ID."');\">
			  		<font size=1>".$TP_LIBELLE."</font></a><td>".$warnflag."</td>";
      			echo  "<div id='r".$P_ID."' 
					   style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width: 350px;
					   height: 70px;
					   padding: 5px;'>
					<img src=images/user.png> <b>".strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM)."</b>
					<br>
	  				<select name='fn".$P_ID."' id='fn".$P_ID."'
				  	onchange=\"savefonction(".$evenement.", document.getElementById('fn".$P_ID."').value,".$P_ID.");\">
	  				<option value='0'>aucune fonction</option>";
	  				$resultf=mysql_query($queryf);
					while ($rowf=@mysql_fetch_array($resultf)) {
						if ( $rowf["TP_ID"] == $TP_ID) $selected='selected';
						else $selected='';
						if ( $rowf["TYPE"] <> '' ) $require=" (".$rowf["TYPE"]." requis)";
						else $require="";
						echo "<option value='".$rowf["TP_ID"]."' $selected>".$rowf["TP_LIBELLE"].$require."</option>";
					}
					echo "</select>
					<div align=center><a onmouseover=\"HideContent('r".$P_ID."'); return true;\"
   					href=\"javascript:HideContent('r".$P_ID."')\"><i>fermer</i></a>
   					</div>
   					</form>
			 		</div>"; 
				 echo "</td>";
				}
          }
		  else echo "<td></td>";
		
		
		  // choix équipe
		  if ( $nbe > 0 ) {
			 	if (! $granted_event) {
			 	 	if ( $EE_ID == "" or $EE_ID == 0 ) $EE_NAME="";
			 		echo  "<td><font size=1>".$EE_NAME." </font></a></td>";
			 	}
			 	else {
				  	if ( $EE_ID == "" or $EE_ID == 0 ) $EE_NAME="<font size=1 color=grey><i>choisir</i></font>";
      				echo "<td><a href=\"javascript:ReverseContentDisplay('s".$P_ID."');\">
			  				<font size=1>".$EE_NAME."</font></a>";
      				echo  "<div id='s".$P_ID."' 
					   		style='display: none;
					   		position: absolute; 
					   		border-style: solid;
					   		border-width: 2px;
					   		background-color: $mylightcolor; 
					   		border-color: $mydarkcolor;
					   		width: 350px;
					   		height: 70px;
					   		padding: 5px;'>
							<img src=images/user.png> <b>".strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM)."</b>
							<br>
				            <select name='e".$P_ID."' id='e".$P_ID."'
				  			onchange=\"saveequipe(".$evenement.", document.getElementById('e".$P_ID."').value,".$P_ID.");\">
	  						<option value='0'>aucune équipe</option>";
	  						$resulte=mysql_query($querye);
					while ($rowe=@mysql_fetch_array($resulte)) {
							if ( $rowe["EE_ID"] == $EE_ID) $selected='selected';
							else $selected='';
							echo "<option value='".$rowe["EE_ID"]."' $selected>".$rowe["EE_NAME"]." - ".substr($rowe["EE_DESCRIPTION"],0,30).".</option>";
					}
					echo "</select>
					<div align=center><a onmouseover=\"HideContent('s".$P_ID."'); return true;\"
   					href=\"javascript:HideContent('s".$P_ID."')\"><i>fermer</i></a>
   					</div>
   					</form>
			 		</div>"; 
				 echo "</td>";
				}
		  }
		  else echo "<td></td>";	 
      }
      else { // impression ou personne sans habilitations
      	  if ( $nbfn > 0 ) {
      			if ( $TP_ID == "" ) $TP_LIBELLE="-";
      			echo "<td><font size=1>".$TP_LIBELLE."</font></td>";
      	  }
      	  if ( $nbe > 0 ) {
      	   		if ( $EE_ID == "" ) $EE_NAME="-";
      			echo "<td><font size=1>".$EE_NAME."</font></td>";
      	  }
      	  else echo "<td></td>";
      }
      	  
      // infos
      if (! $print) {
        if (($granted_personnel or $granted_event or $granted_update or $id == $P_ID)) {
      	  echo "<td><a href=\"javascript:ReverseContentDisplay('p".$P_ID."');\">
	  		<img border=0 src=images/$txtimg title=\"".$popup."\"></a> <font size=1>$EP_KM</font>";
      	  echo  "<div id='p".$P_ID."' 
				style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width: 310px;
					   height: 175px;
					   padding: 5px;'>
				<form name='pform".$P_ID."' action='evenement_inscription.php'>
				<img src=images/user.png> <b>".strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM)."</b>
				<br><font size=1>Inscrit le: ".$EP_DATE." ".$inscritPar."</font><br>
        		<input type=hidden name='P_ID' value='".$P_ID."' />
        		<input type=hidden name='action' value='detail' />
        		<input type=hidden name='from' value='evenement' />
        		<input type=hidden name='evenement' value='".$evenement."' />";
          if ( $P_STATUT == 'SAL' ) {	
		    echo "<i>Participation en tant que:<br>";
		    if ( $EP_FLAG1 == 1 ) $checked='checked'; else  $checked='';
		  	echo "<label for='0'>Salarié</label><input type='radio' name='EP_FLAG1' id='EP_FLAG1' 
			  	value='1' $checked/>";
		  	if ( $EP_FLAG1 == 0 ) $checked='checked'; else  $checked='';
		  	echo "<label for='1'>Bénévole</label><input type='radio' name='EP_FLAG1' id='EP_FLAG1' 
			  	value='0' $checked/></i><br>";
		  }	
	  	 echo "<i>Commentaire:</i><br> 
				<textarea style='font-size:10pt; font-family:Arial;' cols=40 rows=3
				  	name='detail' id='detail' value='$EP_COMMENT' 
		  			title='saisir ici le commentaire lié à cette inscription'>".$EP_COMMENT."</textarea>";
		  echo "<br><i>km réalisés en véhicule personnel: </i> 
				<input type=text size=3
				  	name='km' id='km' value='$EP_KM' 
		  			title='saisir ici le nombre de km réalisés avec véhicule personnel'>";
		    	
		  echo  "<br><input type=submit name='p".$P_ID."' value='OK'\"
		    		title='cliquer pour valider'><br>
		  		<div align=center><a onmouseover=\"HideContent('p".$P_ID."'); return true;\"
   					href=\"javascript:HideContent('p".$P_ID."')\"><i>fermer</i></a>
				</div>";
		  echo "</td>";
		}
		else {
	  		echo "<td><img src=images/$txtimg title=\"".$popup."\"></td>";
	  		
	    }
	  }

	echo "</div></form>";	

	    
      // autres engagements?
      echo "<td>$myimg</td>";
      
      // suppression
      if (($granted_event or ($granted_inscription and $granted_update)) 
	  		and (! $print)) {
      		echo "<td >
		  	<a href=evenement_inscription.php?evenement=".$evenement."&EC=".$EC."&action=desinscription&P_ID=".$P_ID." title='désinscrire' >
		  	<img src=images/trash.png height=12 border=0></a></td>";
	  }
	  else echo "<td ></td>";
	  
	  echo "<td>$postes</td>";
      echo "</tr>";
	}
	echo "</table></td></tr>";
	echo "</table>";
	echo "</tr></td></table>";
}
else echo "Aucun personnel inscrit. (".$cmt.").<br>";

//=====================================================================
// inscrire d'autres personnes
//=====================================================================

if (( $E_CLOSED == 0 ) and ( $E_CANCELED == 0 ) and (! $print)){
   if (( $granted_personnel ) or ( $granted_inscription )) {
    echo "<p>";   
	echo "<input type='button' name='ajouter' value='inscrire du personnel' title='inscrire du personnel'
		   onclick=\"inscrire(".$evenement.",'personnel')\">";
	if ( $nbsections == 0 )
		echo " <input type='button' name='ajouter' value='inscrire des externes' title='inscrire des externes'
		   onclick=\"inscrire(".$evenement.",'personnelexterne')\">";  
  }
}


if((strlen($listePompiers)-1)>1 &&  $granted_event && (! $print)){
  	echo  "<form name='FrmEmail' method='post' action='mail_create.php'>";
    echo  "<input type='hidden' name='Messagebody' value=\"".str_replace("'","",$E_LIBELLE)."\">
      <input type='hidden' name='SelectionMail'
	  		value=\"".rtrim($listePompiers,',')."\" />";
      echo "Contacter les inscrits: ";
	  if ( check_rights($id, 43))
      	  echo "<input type='submit' value='message' title=\"envoi de message à partir de l'application web\"/>"; 
	  if  ( $mailist <> "" ) 
	  	    echo " <input type='button' value='mailto' 
		    onclick='parent.location=\"mailto:".rtrim($mailist,',')."?subject=".str_replace("'","",$E_LIBELLE)."\"' 
			title=\"envoi de message à partir de votre logiciel de messagerie\"/>";
	echo "</form>";
   }

}
//=====================================================================
// véhicules demandés
//=====================================================================
if ( (( $tab == 3 ) or ($print)) and ( $vehicules == 1 )) {
$query="select distinct ev.E_CODE as EC,v.V_ID,v.V_IMMATRICULATION,v.TV_CODE, vp.VP_LIBELLE, v.V_MODELE, v.V_INDICATIF,
	    vp.VP_ID, vp.VP_OPERATIONNEL, s.S_DESCRIPTION, s.S_ID, s.S_CODE, ev.EV_KM, ev.COND_ID,
		DATE_FORMAT(v.V_ASS_DATE, '%d-%m-%Y') as V_ASS_DATE,
		DATE_FORMAT(v.V_CT_DATE, '%d-%m-%Y') as V_CT_DATE,
		DATE_FORMAT(v.V_REV_DATE, '%d-%m-%Y') as V_REV_DATE
        from evenement_vehicule ev, vehicule v, vehicule_position vp, section s
        where v.V_ID=ev.V_ID
        and s.S_ID=v.S_ID
        and vp.VP_ID=v.VP_ID
        and ev.E_CODE=".$evenement."
		order by ev.E_CODE asc";
$result=mysql_query($query) or die (mysql_error());

$nbvehic=mysql_num_rows($result);
if ( $nbvehic > 0 ) {
   echo "<table>";
   echo "<tr>
         <td class='FondMenu'>";
   echo "<table cellspacing=0 border=0 width=700>";

   echo "<tr><td CLASS='MenuRub'>Véhicules</td></tr>";
   echo "<tr><td CLASS='Menu'>";
   echo "<table>";
   $prevEC='';
   while ($row=mysql_fetch_array($result)) {
      $V_ID=$row["V_ID"];
      $S_ID=$row["S_ID"];
      $S_CODE=$row["S_CODE"];
      $S_DESCRIPTION=$row["S_DESCRIPTION"];
      $V_MODELE=$row["V_MODELE"];  
      $EV_KM=$row["EV_KM"];
      $V_IMMATRICULATION=$row["V_IMMATRICULATION"]; 
      $V_INDICATIF=$row["V_INDICATIF"]; 
      $TV_CODE=$row["TV_CODE"];
	  $V_ASS_DATE=$row["V_ASS_DATE"];
	  $V_CT_DATE=$row["V_CT_DATE"];
	  $V_REV_DATE=$row["V_REV_DATE"];
      $VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
      $VP_ID=$row["VP_ID"];  
      $VP_LIBELLE=$row["VP_LIBELLE"]; 
      $EC=$row["EC"];
	  $COND_ID=$row["COND_ID"];
      
      if ( $V_INDICATIF <> '' ) $V_IDENT = $V_INDICATIF;
      else $V_IDENT = $V_IMMATRICULATION;
	  
	  // affiche d'où vient le renfort
	  if ( $EC <> $prevEC ) {
	    $queryR="select e.E_CANCELED as CE_CANCELED, e.E_CLOSED as CE_CLOSED, eh.EH_ID,
			s.S_CODE CS_CODE, s.S_DESCRIPTION CS_DESCRIPTION,
			DATE_FORMAT(eh.EH_DATE_DEBUT, '%d-%m') as EH_DATE_DEBUT0,
			DATE_FORMAT(eh.EH_DATE_FIN, '%d-%m') as EH_DATE_FIN0,
			TIME_FORMAT(eh.EH_DEBUT, '%k:%i') EH_DEBUT0,  
			TIME_FORMAT(eh.EH_FIN, '%k:%i') EH_FIN0
	        from evenement e, section s, evenement_horaire eh
			where e.S_ID = s.S_ID
			and e.E_CODE = eh.E_CODE
			and e.E_CODE=".$EC."";
		$resultR=mysql_query($queryR) or die (mysql_error());
		$EH_DATE_DEBUT0 = Array();
		$EH_DATE_DEBUT0 = Array();
		$EH_DEBUT0 = Array();
		$EH_FIN0 = Array();
		$horaire_renfort = Array();
		
		while ( $rowR=@mysql_fetch_array($resultR)) {
		    $n=$rowR["EH_ID"];
			$EH_DATE_DEBUT0[$n]=$rowR["EH_DATE_DEBUT0"];
	    	$EH_DATE_FIN0[$n]=$rowR["EH_DATE_FIN0"];
	    	$EH_DEBUT0[$n]=$rowR["EH_DEBUT0"];
	    	$EH_FIN0[$n]=$rowR["EH_FIN0"];
	 		$CE_CANCELED=$rowR["CE_CANCELED"];
	 		$CE_CLOSED=$rowR["CE_CLOSED"];
			$CS_CODE=$rowR["CS_CODE"];
			$CS_DESCRIPTION=$rowR["CS_DESCRIPTION"];
			if ( $CE_CANCELED == 1 ) {
			 	$color="red";
			 	$info="événement annulé";
			}
	  		elseif ( $CE_CLOSED == 1 ) {
			   	$color="orange";
			   	$info="événement clôturé";
			}
	  		else {
			   	$color="green";
			   	$info="événement ouvert";
			}
			if ( $EH_DATE_DEBUT0[$n] <> $EH_DATE_FIN0[$n] ) $dates_renfort=$EH_DATE_DEBUT0[$n] ." au ".$EH_DATE_FIN0[$n];
			else $dates_renfort=$EH_DATE_DEBUT0[$n];
			$horaire_renfort[$n]=$dates_renfort." - ".$EH_DEBUT0[$n]."-".$EH_FIN0[$n];
		}
		if ( $EC <> $evenement ) {
	  		echo "<tr CLASS='Menu'><td width=300 colspan=6>
		  		<b><i><a href=evenement_display.php?evenement=$EC&from=inscription>
		  		<img src=images/renfort_".$color.".png border=0 title='$info' >
		  		Renfort de ".$CS_CODE."</i></b></a>
			  	</td></tr>";
		}
	  	$prevEC = $EC;
	  }
      
      if ( $VP_OPERATIONNEL == -1) $mytxtcolor='black';
      else if ( $VP_OPERATIONNEL == 1) $mytxtcolor=$red;      
	  else if ( my_date_diff(getnow(),$V_ASS_DATE) < 0 ) {
	  		$mytxtcolor=$red;
	  		$VP_LIBELLE = "assurance périmée";
	  }
	  else if ( my_date_diff(getnow(),$V_CT_DATE) < 0 ) {
	  		$mytxtcolor=$red;
	  		$VP_LIBELLE = "CT périmé";	  
	  }
	  else if ( $VP_OPERATIONNEL == 2) {
	  	$mytxtcolor=$orange;
	  }
	  else if (( my_date_diff(getnow(),$V_REV_DATE) < 0 ) and ( $VP_OPERATIONNEL <> 1)) {
	  	$mytxtcolor=$orange;
		$VP_LIBELLE = "révision à faire";
	  }  
      else $mytxtcolor=$green;
      
      // récupérer horaires du véhicule
      $clock="";
      for ($i=1; $i <= $nbmaxsessionsparevenement; $i++) {
        if ( isset ($horaire_renfort[$i])) {
          $query_horaires="select EH_ID,
		   DATE_FORMAT(EV_DATE_DEBUT,'%d-%m-%Y') EV_DATE_DEBUT, 
		   DATE_FORMAT(EV_DATE_FIN,'%d-%m-%Y') EV_DATE_FIN,
		   TIME_FORMAT(EV_DEBUT, '%k:%i') EV_DEBUT,  
		   TIME_FORMAT(EV_FIN, '%k:%i') EV_FIN,
		   DATE_FORMAT(EV_DATE_DEBUT,'%Y-%m-%d') EV_DATE_DEBUT1,
		   DATE_FORMAT(EV_DATE_FIN,'%Y-%m-%d') EV_DATE_FIN1
		   from evenement_vehicule
           where E_CODE=".$EC."
           and EH_ID = ".$i."
		   and V_ID=".$V_ID."";
	       $resultH=mysql_query($query_horaires) or die (mysql_error());
	       $rowH=@mysql_fetch_array($resultH);
	       $EH_ID=$rowH["EH_ID"];
	       if ( $EH_ID <> "" ) {
      		  $EV_DATE_DEBUT=$rowH["EV_DATE_DEBUT"];    // DD-MM-YYYY
      		  $EV_DATE_FIN=$rowH["EV_DATE_FIN"];		  
      		  $EV_DATE_DEBUT1=$rowH["EV_DATE_DEBUT1"];  // YYYY-MM-DD
      		  $EV_DATE_FIN1=$rowH["EV_DATE_FIN1"];
      		  $EV_DEBUT=$rowH["EV_DEBUT"];
      		  $EV_FIN=$rowH["EV_FIN"];
	  		  if ($nbsessions == 1 ) $t=" de l'événement";
           	  else $t=" de la partie n°$EH_ID";			
      		  if ( $EV_DATE_DEBUT <> "" ) {
           		  if ( $EV_DATE_DEBUT1 == $EH_DATE_DEBUT0[$i] and $EV_DATE_FIN1 == $EH_DATE_FIN0[$i] ) $horaire_v=$EV_DEBUT."-".$EV_FIN;
           		  else if ( $EV_DATE_DEBUT == $EV_DATE_FIN ) $horaire_v= substr($EV_DATE_DEBUT,0,5).", ".$EV_DEBUT."-".$EV_FIN;
           		  else $horaire_v= substr($EV_DATE_DEBUT,0,5)." au ".substr($EV_DATE_FIN,0,5).", ".$EV_DEBUT."-".$EV_FIN;
           		  $clock .="<img border=0 src=images/clock_yellow.png title=\"horaires différents de ceux $t \n$horaire_v \ncliquer pour modifier\">";
      		  }
	  		  else $clock .="<img border=0 src=images/clock_green.png title=\"horaires identiques à ceux $t \n".$horaire_renfort[$i]." \ncliquer pour modifier\">";
           }
	       else $clock .="<img border=0 src=images/clock_red.png title=\"pas engagé sur cette partie \ncliquer pour modifier\">";
        }
      }
      
      
      $nb = get_nb_engagements('V', $V_ID, $year1, $month1, $day1, $year2, $month2, $day2) - 1 ;
	  if ( $nb > 1 ) 
	   		$myimg="<img src=images/red.gif title='attention ce véhicule est parallèlement engagé sur $nb autres événements'>";
	  else if ( $nb == 1 )
	  		$myimg="<img src=images/yellow.gif title='attention ce véhicule est parallèlement engagé sur 1 autre événement'>";
	  else $myimg="";
	  
      $altcolor=(($S_ID==$organisateur)?"":"<font color=purple>");
      echo "<tr><td width=400><font size=1><a href=upd_vehicule.php?vid=$V_ID 
	  title=\"$S_CODE - $S_DESCRIPTION\">".$altcolor.$TV_CODE." - ".$V_MODELE." - ".$V_IDENT."</a>	
	  <font color=$mytxtcolor>".$VP_LIBELLE."</font></td>";
	  
	  // affiche horaires
	  if ( ! $print) {
		if ($granted_event or ($granted_vehicule and $E_CLOSED == 0))
          		echo "<td><a href=\"javascript:horaires('$EC',0,'$V_ID');\">".$clock."</a></td>";			
		else 
				echo "<td>".$clock."</td>";
      }
	  
	  echo "<td width=20>$myimg</td>";
	  if ( $granted_vehicule and (! $print)) {
      	echo "<td width=20>
		  <a href=evenement_vehicule_add.php?evenement=".$evenement."&EC=".$EC."&action=remove&V_ID=".$V_ID."&from=evenement title='désengager ce véhicule' >
		  <img src=images/trash.png height=12 border=0></a>";	 
		echo "</td>";
		$readonly="";	
      }
      else {
	  		$readonly="readonly";
	  		echo "<td width=20></td>";
	  }
      echo "<td width=150>";
      if ( $EV_KM == '' )  {
			$showEV_KM = 'renseigner ';
			$EV_KM = 0;
	  }
	  else $showEV_KM  = $EV_KM;
      
      if ( $readonly == ''){
        echo "<a href=\"javascript:ReverseContentDisplay('v".$V_ID."');javascript:document.forms['vform".$V_ID."'].elements['km'].focus();\">$showEV_KM km</a>";
        echo  "<div id='v".$V_ID."' 
				style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width:300px;
					   height:80px;
					   padding: 5px;'>
				<form name='vform".$V_ID."' action='evenement_vehicule_add.php' method=POST>
				<img src=images/car.png> <b>Saisie du kilométrage réalisé par</b>
				<br><i> ".$TV_CODE." - ".$V_MODELE." - ".$V_IDENT."</i><br>
        		<input type=hidden name='V_ID' value='".$V_ID."' />
        		<input type=hidden name='action' value='km' />
        		<input type=hidden name='from' value='evenement' />
        		<input type=hidden name='evenement' value='".$evenement."' />
				<input type=hidden name='EC' value='".$EC."' />
	  			<i>km</i> <input type=text size=5 maxlength=5 name='km' value='$EV_KM' 
		  			title='saisir ici le kilométrage réalisé sur cet événement'>
	       		<input type=submit name='s".$V_ID."' value='OK'\"
		    		title='cliquer pour valider le kilométrage'><br>
		  		<div align=center><a onmouseover=\"HideContent('v".$V_ID."'); return true;\"
   					href=\"javascript:HideContent('v".$V_ID."')\"><i>fermer</i></a>
   				</form></div>
			 </div>"; 
	   }
	   else {
	        echo $showEV_KM." km";
	   }
	   echo "</td>";
//---------------------
// Conducteur du véhicule
//---------------------
//On sélectionne les participants
$query_participants = "SELECT DISTINCT p.P_NOM, p.P_PRENOM, p.P_ID
					FROM pompier p, evenement_participation ep
					WHERE ep.E_CODE=".$evenement."
					AND p.P_ID = ep.P_ID";

$result_participants = mysql_query($query_participants) or die (mysql_error());

      if ( $nbsections == 0 ) echo "<td width=250><font size=1>$S_CODE</td>";
	  else echo "<td width=250></td>";
	  
	$query_conducteur_nom = "SELECT DISTINCT P_NOM, P_PRENOM FROM pompier p, evenement_vehicule ev WHERE p.P_ID=ev.COND_ID AND ev.E_CODE=".$evenement." AND ev.V_ID=".$V_ID."";
	$result_conducteur_nom = mysql_query($query_conducteur_nom) or die (mysql_error());
	$row_conducteur_nom = mysql_fetch_array($result_conducteur_nom);
	$conducteur_nom = $row_conducteur_nom["P_NOM"];
	//echo "le nom du conducteur".$conducteur_nom;
	$conducteur_prenom = $row_conducteur_nom["P_PRENOM"];
	
	if ($COND_ID == 0) {
      				echo "<td><a href=\"javascript:ReverseContentDisplay('cond".$V_ID."');\">
			  				<font size=1>choisir</font></a>";
	}
	else {	
	echo "<td><a href=\"javascript:ReverseContentDisplay('cond".$V_ID."');\">
			  				<font size=1>".$conducteur_nom." ".$conducteur_prenom."</font></a>";
	}
      				echo  "<div id='cond".$V_ID."' 
					   		style='display: none;
					   		position: absolute; 
					   		border-style: solid;
					   		border-width: 2px;
					   		background-color: $mylightcolor; 
					   		border-color: $mydarkcolor;
					   		width: 350px;
					   		height: 70px;
					   		padding: 5px;'>
							<br>
				            <select name='c".$V_ID."' id='c".$V_ID."'
				  			onchange=\"saveconducteur(".$evenement.", document.getElementById('c".$V_ID."').value,".$V_ID.");\">
	  						<option value='0'>aucun conducteur</option>";
					while($row_participants=mysql_fetch_array($result_participants)) {
						$PID=$row_participants["P_ID"];
						$PNOM=$row_participants["P_NOM"];
						$PPRENOM=$row_participants["P_PRENOM"];
						
						$query_conducteur = "SELECT COND_ID FROM evenement_vehicule WHERE E_CODE=".$evenement." AND V_ID=".$V_ID."";
						$result_conducteur = mysql_query($query_conducteur) or die (mysql_error());
						$row_conducteur = mysql_fetch_array($result_conducteur);
						$condid=$row_conducteur["COND_ID"];
						//echo $condid;
						//echo $PID;
						if ($condid==$PID) $selected="selected";
						else $selected='';
						echo "<option value='".$PID."' ".$selected.">".$PNOM.' '.$PPRENOM."</option>\n";
					}
					echo "</select>";
					echo "<div align=center><a onmouseover=\"HideContent('cond".$V_ID."'); return true;\"
   					href=\"javascript:HideContent('cond".$V_ID."')\"><i>fermer</i></a>
   					</div>
   					</form>
			 		</div>"; 
				 echo "</td>";
	echo "</td>";	
	echo "</tr>";
	}
	echo "</table>";
}	

else {
echo "Aucun véhicule engagé.<br>";
}
echo "</table></table>";
//=====================================================================
// ajouter un véhicule
//=====================================================================

if (( $E_CANCELED == 0 ) and (! $print)) {
  if ( $granted_vehicule ) {
    echo "<p>";   
	echo "<input type='button' name='ajouter' value='engager des véhicules' title='engager des véhicules'
		   onclick=\"inscrire(".$evenement.",'vehicule')\">";  
  }
}
}


//=====================================================================
// matériel demandés
//=====================================================================
if ( (( $tab == 4 ) or ($print)) and ( $materiel == 1 )) {

$query_materiel="select em.E_CODE as EC, m.MA_ID, tm.TM_CODE, m.TM_ID, vp.VP_LIBELLE, m.MA_MODELE, m.MA_NUMERO_SERIE,
	    vp.VP_ID, vp.VP_OPERATIONNEL, s.S_DESCRIPTION, s.S_ID, s.S_CODE, em.EM_NB, m.MA_NB, m.MA_PARENT, tm.TM_LOT,
	    cm.TM_USAGE, cm.PICTURE_SMALL, cm.CM_DESCRIPTION, em.EE_ID,
	    DATE_FORMAT(m.MA_REV_DATE, '%d-%m-%Y') as MA_REV_DATE
        from evenement_materiel em, materiel m, vehicule_position vp, section s, 
		type_materiel tm, categorie_materiel cm
        where m.MA_ID=em.MA_ID
        and cm.TM_USAGE=tm.TM_USAGE
        and tm.TM_ID = m.TM_ID
        and s.S_ID=m.S_ID
        and vp.VP_ID=m.VP_ID
        and em.E_CODE=".$evenement."
		order by em.E_CODE asc, cm.TM_USAGE, tm.TM_LOT desc, m.MA_PARENT desc, tm.TM_CODE, m.MA_MODELE";

$result_materiel=mysql_query($query_materiel) or die (mysql_error());
$nbmat = 0;
$nbmat=mysql_num_rows($result_materiel);
//echo "le nombre de materiel engagé".$nbmat;
if ( $nbmat > 0 ) {

   echo "<table>";
   echo "<tr>
         <td class='FondMenu'>";
   echo "<table cellspacing=0 border=0 width=700>";

   echo "<tr><td CLASS='MenuRub'>Matériel</td></tr>";
   echo "<tr><td CLASS='Menu'>";
   echo "<table>";
   $prevTM_USAGE='';
   $prevEC=$evenement;
   while ($row=mysql_fetch_array($result_materiel)) {
      $EC=$row["EC"];
      $MA_ID=$row["MA_ID"];
      $MA_PARENT=$row["MA_PARENT"];
      $TM_LOT=$row["TM_LOT"];
      $MA_NB=$row["MA_NB"];
      $EM_NB=$row["EM_NB"];
      $S_ID=$row["S_ID"];
      $MA_REV_DATE=$row["MA_REV_DATE"];
      $S_CODE=$row["S_CODE"];
      $TM_USAGE=$row["TM_USAGE"];
      $CM_DESCRIPTION=$row["CM_DESCRIPTION"];
      $PICTURE_SMALL=$row["PICTURE_SMALL"];
      $S_DESCRIPTION=$row["S_DESCRIPTION"];
      $MA_MODELE=$row["MA_MODELE"];  
      $MA_NUMERO_SERIE=$row["MA_NUMERO_SERIE"]; 
      $TM_CODE=$row["TM_CODE"];
      $VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
      $VP_ID=$row["VP_ID"];  
      $VP_LIBELLE=$row["VP_LIBELLE"]; 
	  $EE_ID=$row["EE_ID"];
      
      if ( $VP_OPERATIONNEL == -1) $mytxtcolor='black'; 
      else if ( $VP_OPERATIONNEL == 1) $mytxtcolor=$red;      
	  else if ( $VP_OPERATIONNEL == 2) $mytxtcolor=$orange;
      else $mytxtcolor=$green;
      
      $nb = get_nb_engagements('M', $MA_ID, $year1, $month1, $day1, $year2, $month2, $day2) ;
	  if ( $nb > $MA_NB ) {
	  		$alreadyused=$nb - $MA_NB;
	   		$myimg="<img src=images/red.gif 
		title='attention ce matériel est parallèlement engagé un ou des autres événements ($alreadyused pièces)'>";
	  }
	  else $myimg="";
    
      // affiche d'où vient le renfort
	  if ( $EC <> $prevEC ) {
	    $queryR="select e.E_CANCELED as CE_CANCELED, e.E_CLOSED as CE_CLOSED,
			s.S_CODE CS_CODE, s.S_DESCRIPTION CS_DESCRIPTION
	        from evenement e, section s
			where e.S_ID = s.S_ID
			and e.E_CODE=".$EC."";
		$resultR=mysql_query($queryR);
		$rowR=@mysql_fetch_array($resultR);
	 	$CE_CANCELED=$rowR["CE_CANCELED"];
	 	$CE_CLOSED=$rowR["CE_CLOSED"];
		$CS_CODE=$rowR["CS_CODE"];
		$CS_DESCRIPTION=$rowR["CS_DESCRIPTION"];
		if ( $CE_CANCELED == 1 ) {
			 	$color="red";
			 	$info="événement annulé";
		}
	  	elseif ( $CE_CLOSED == 1 ) {
			   	$color="orange";
			   	$info="événement clôturé";
		}
	  	else {
			   	$color="green";
			   	$info="événement ouvert";
		}
	  	echo "<tr><td width=300 colspan=2 >
		  <b><i><a href=evenement_display.php?evenement=$EC&from=inscription>
		  <img src=images/renfort_".$color.".png border=0 title='$info' >
		  Renfort de ".$CS_CODE."</i></b></a>
		  </td></tr>";
	  	$prevEC = $EC;
	  	$prevTM_USAGE='';
	  }
	  
	  // affiche catégorie
	  if ( $TM_USAGE <> $prevTM_USAGE) {
       echo "<tr><td colspan=5><img src=images/$PICTURE_SMALL><b> $CM_DESCRIPTION</b></td></tr>";
      }
      $prevTM_USAGE=$TM_USAGE;
      
      if ( $VP_OPERATIONNEL == -1) $mytxtcolor='black';
      else if ( $VP_OPERATIONNEL == 1) $mytxtcolor=$red;      
	  else if ( my_date_diff(getnow(),$MA_REV_DATE) < 0 ) {
	  		$mytxtcolor=$orange;
	  		$VP_LIBELLE = "date dépassée";
	  }
	  else if ( $VP_OPERATIONNEL == 2) {
	  	$mytxtcolor=$orange;
	  }
      else $mytxtcolor=$green;
      
	  
	  $element="<font color=$mylightcolor>.....";
	  if ( $TM_LOT == 1 ) $element .="</font><img height='12' src=images/arrow_black.png title=\"Ceci est un lot de matériel\"> ";
	  elseif ( $MA_PARENT > 0  ) $element .="...</font><img height='12' src=images/bullet_black_small.png title=\"élément d'un lot de matériel\">";
	  else $element .="</font><img height='12' src=images/bullet_black.png title=\"Ne fait pas partie d'un lot\"> ";
	  
      $altcolor=(($S_ID==$organisateur)?"":"<font color=purple>");
      echo "<tr valign=baseline><td width=400>".$element."<font size=1><a href=upd_materiel.php?mid=$MA_ID 
	  title=\"$S_CODE - $S_DESCRIPTION\">".$altcolor.$TM_CODE." - ".$MA_MODELE." - ".$MA_NUMERO_SERIE."</a>	
	  <font color=$mytxtcolor>".$VP_LIBELLE."</font></td>";
	  echo "<td width=20>$myimg</td>";
	  if ( $granted_vehicule  and (! $print) ) {
      	echo "<td width=20>
		  <a href=evenement_materiel_add.php?evenement=".$evenement."&EC=".$EC."&action=remove&MA_ID=".$MA_ID."&from=evenement title='désengager ce matériel'>
		  <img src=images/trash.png height=12 border=0></a>";	 
		echo "</td>";
		$readonly="";	
      }
      else {
	  		$readonly="readonly";
	  		echo "<td width=20></td>";
	  }
      echo "<td width=150>";
      //echo "le nombre de materiel" .$MA_NB;
      if ( $MA_NB > 1 ) {
      	if ( $EM_NB == '' )  $EM_NB = 0;
      	if ( $readonly == ''){
        	echo "<a href=\"javascript:ReverseContentDisplay('m".$MA_ID."');javascript:document.forms['mform".$MA_ID."'].elements['nb'].focus()\">$EM_NB unités</a>";
        	echo  "<div id='m".$MA_ID."' 
				style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width:300px;
					   height:80px;
					   padding: 5px;'>
				<form name='mform".$MA_ID."' action='evenement_materiel_add.php' method=POST>
				<img src=images/smallengine.png> <b>Saisie du nombre d'unités (maximum $MA_NB)</b>
				<br><i> ".$TM_CODE." - ".$MA_MODELE." - ".$MA_NUMERO_SERIE."</i><br>
        		<input type=hidden name='MA_ID' value='".$MA_ID."' />
        		<input type=hidden name='action' value='nb' />
        		<input type=hidden name='from' value='evenement' />
        		<input type=hidden name='evenement' value='".$evenement."' />
				<input type=hidden name='EC' value='".$EC."' />
	  			<i>nb</i> <input type=text size=5 maxlength=5 name='nb' value='$EM_NB' 
		  			title=\"saisir ici le nombre d'unités à engager\">
	       		<input type=submit name='s".$MA_ID."' value='OK'\"
		    		title='cliquer pour valider le nombre'><br>
		  		<div align=center><a onmouseover=\"HideContent('m".$MA_ID."'); return true;\"
   					href=\"javascript:HideContent('m".$MA_ID."')\"><i>fermer</i></a>
   				</form></div>
			 	</div>"; 
	   		}
	   		else {
	        	echo "$EM_NB pièces";
	   		}
	   	}
	   echo "</td>";
      if ( $nbsections == 0 ) echo "<td width=200><font size=1>$S_CODE</td>";
	  else echo "<td width=200></td>";
      
   
   
//----------------------------------------
// Affectation du matériel à une équipes
//----------------------------------------
	//On sélectionne les équipes
	//echo "l'id du matériel".$EE_ID;
	$query_equipeid="select ee.EE_ID, ee.EE_NAME, ee.EE_DESCRIPTION
	  	   from evenement_equipe ee 
	  	   WHERE ee.E_CODE=".$evenement."";
	  $result_equipeid=mysql_query($query_equipeid);
	
      if ( $nbsections == 0 ) echo "<td width=250><font size=1>$S_CODE</td>";
	  else echo "<td width=250></td>";
	  //echo "l'id enregistrée de l'équipe est la".$EE_ID;
	$query_equipe = "SELECT DISTINCT ee.EE_ID, EE_NAME, EE_DESCRIPTION 
	FROM evenement_equipe ee, evenement_materiel em
	WHERE ee.E_CODE = ".$evenement."
	AND em.E_CODE = ".$evenement." 
	AND em.MA_ID = ".$MA_ID."
	AND ee.EE_ID = em.EE_ID";
	$result_equipe = mysql_query($query_equipe) or die (mysql_error());
	$row_equipe = mysql_fetch_array($result_equipe);
	$equipe_nom = $row_equipe["EE_NAME"];
	//echo $equipe_nom;
	$equipe_description = $row_equipe["EE_DESCRIPTION"];	
	
	if (empty($EE_ID)) {
      				echo "<td><a href=\"javascript:ReverseContentDisplay('equipe_materiel".$MA_ID."');\">
			  				<font size=1>choisir</font></a></td>";
	}
	else {
	echo "<td><a href=\"javascript:ReverseContentDisplay('equipe_materiel".$MA_ID."');\">
			  				<font size=1>".$equipe_nom." ".$equipe_description."</font></a>";
	}
      	echo  "<div id='equipe_materiel".$MA_ID."' 
		   		style='display: none;
		   		position: absolute; 
		   		border-style: solid;
		   		border-width: 2px;
		   		background-color: $mylightcolor; 
		   		border-color: $mydarkcolor;
				width: 350px;
				height: 70px;
				padding: 5px;'>
				<br>
			<select name='em".$MA_ID."' id='em".$MA_ID."'
				onchange=\"saveequipemateriel(".$evenement.", document.getElementById('em".$MA_ID."').value,".$MA_ID.");\">
	  		<option value='0'>aucune équipe</option>";
	while($row_equipeid=mysql_fetch_array($result_equipeid)) {
		$equipe_id=$row_equipeid["EE_ID"];
		$equipe_nom2=$row_equipeid["EE_NAME"];
		$equipe_description2=$row_equipeid["EE_DESCRIPTION"];
						
		if ($equipe_id==$EE_ID) $selected="selected";
		else $selected='';
		echo "<option value='".$equipe_id."' ".$selected.">".$equipe_nom2.' '.$equipe_description2."</option>\n";
	}
	echo "</select>";
	echo "<div align=center><a onmouseover=\"HideContent('equipe_materiel".$MA_ID."'); return true;\"
   		href=\"javascript:HideContent('equipe_materiel".$MA_ID."')\"><i>fermer</i></a>
   		</div>
   		</form>
		</div>"; 
		 echo "</td>";
	echo "</tr>";
		
	echo "</td>";	
	echo "</tr>";
	
	}
	echo "</table>";
}	


else {
echo "Aucun matériel engagé.<br>";
}
echo "</table></table>";
	

//=====================================================================
// ajouter du matériel
//=====================================================================

if (( $E_CANCELED == 0 ) and (! $print)) {
    echo "<p>";   
	echo "<input type='button' name='ajouter' value='engager du matériel' title='engager du matériel'
		   onclick=\"inscrire(".$evenement.",'materiel')\">"; 
	echo "</p>"; 
  }
}
//=====================================================================
// formation / diplômes
//=====================================================================
if (( $tab == 5 ) and (! $print) and ( $TE_CODE == 'FOR' ) and ( $PS_ID <> "") and ($TF_CODE <> "")){

if ( $granted_personnel && ( check_rights($id, 4) or $chef)) $disabledtf="";
else $disabledtf="disabled";

echo "<p>";
echo "<TABLE>
<TR>
<TD class='FondMenu'>";

$query="select p.PS_EXPIRABLE, p.PS_NATIONAL, p.PS_SECOURISME, p.PS_PRINTABLE, p.PS_ID, p.TYPE, tf.TF_LIBELLE, e.F_COMMENT
        from type_formation tf, poste p, evenement e
	    where e.PS_ID=p.PS_ID
		and e.TF_CODE=tf.TF_CODE
		and e.E_CODE=".$evenement."";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$_TYPE=$row["TYPE"];
$_PS_ID=$row["PS_ID"]; 
$_TF_LIBELLE=$row["TF_LIBELLE"];
$_PS_EXPIRABLE=$row["PS_EXPIRABLE"];
$_F_COMMENT=$row["F_COMMENT"];
$_PS_PRINTABLE=$row["PS_PRINTABLE"];
$_PS_NATIONAL=$row["PS_NATIONAL"];
$_PS_SECOURISME=$row["PS_SECOURISME"];

$printdiplomes=false;
if ($_PS_PRINTABLE == 1 ){
	if ( $_PS_NATIONAL == 1 ) {
		if (check_rights($_SESSION['id'], 48, "0" )) $printdiplomes=true;
	}
	else if (check_rights($_SESSION['id'], 48, "$S_ID")) $printdiplomes=true;
}
	
if ($_TYPE <> "") {
    if ($_TF_LIBELLE <> "") $tt=$_TF_LIBELLE." pour ".$_TYPE;
 	else $tt ="formation pour ".$_TYPE;
}
else $tt="formation";

echo "<table cellpading=0 cellspacing=0 border=0 width=700 bgcolor=$mylightcolor>";
echo "<tr><td CLASS='MenuRub' colspan=2>Résultats de: ".$tt."</td></tr>";

if($E_DUREE_TOTALE!=''){	 
echo "<tr><td width=40%><b>Durée effective: </b></td>
   	 <td width=60%> ".$E_DUREE_TOTALE." heures</td></tr>";
}
//instructeurs
$queryi="select distinct ep.E_CODE as EC, p.P_ID,p.P_NOM,p.P_PHONE, p.P_PRENOM, p.P_GRADE, s.S_ID, s.S_CODE, p.P_STATUT, c.C_NAME,
		EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),p.P_BIRTHDATE))))+0 AS age, ep.TP_ID, tp.TP_NUM, tp.TP_LIBELLE
		from evenement_participation ep, pompier p, section s, type_participation tp, company c
        where ep.E_CODE in (".$evts.")
        and tp.TP_ID = ep.TP_ID
        and p.C_ID = c.C_ID
		and p.P_ID=ep.P_ID
		and p.P_SECTION=s.S_ID
		and ep.TP_ID > 0
		order by tp.TP_NUM, p.P_NOM";
$resulti=mysql_query($queryi);

//stagiaires
$query="select distinct ep.E_CODE as EC, p.P_ID,p.P_NOM,p.P_PHONE, p.P_PRENOM, p.P_GRADE, s.S_ID, s.S_CODE, p.P_STATUT, c.C_NAME,
		EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),p.P_BIRTHDATE))))+0 AS age, ep.TP_ID
		from evenement_participation ep, pompier p, section s, company c
        where ep.E_CODE in (".$evts.")
        and p.C_ID = c.C_ID
		and p.P_ID=ep.P_ID
		and p.P_SECTION=s.S_ID
		and ep.TP_ID=0
		order by p.P_NOM, p.P_PRENOM";
$result=mysql_query($query);
$nbstagiaires=mysql_num_rows($result);

if ( mysql_num_rows($resulti) > 0 ) {
	while ($rowi=@mysql_fetch_array($resulti)) {
      $P_NOM=$rowi["P_NOM"]; 
      $P_PRENOM=$rowi["P_PRENOM"];
      $P_ID=$rowi["P_ID"];
      $TP_ID=$rowi["TP_ID"];
      $S_ID=$rowi["S_ID"];
      $P_STATUT=$rowi["P_STATUT"];
      $AGE=$rowi["age"]; 
      $S_CODE=$rowi["S_CODE"];
      $C_NAME=$rowi["C_NAME"];
	  $TP_LIBELLE=$rowi["TP_LIBELLE"];
	  
	  if ( $P_STATUT == 'EXT' ) {
	  	$colorbegin="<font color=green>";
		$colorend="</font>";
		$title="Personnel externe ".$C_NAME." (".$S_CODE.")";
	  }
	  else {
	   	$colorbegin="";
		$colorend="";
		$title=$S_CODE;
      }
	  
	  echo "<tr><td width=40%>";
	  echo "<b>".$TP_LIBELLE.":</b>";
	  echo "</td><td width=60%>";
	  echo " <a href=upd_personnel.php?pompier=$P_ID title=\"$title\">".
	  $colorbegin.strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM).$colorend."</a>";
	  echo "<td></tr>";
	}
}
else if ( $E_CHEF <> '' ) {
	echo "<tr><td width=40%><b>Responsable: </b></td>
   	 <td width=60%>
		<a href=upd_personnel.php?pompier=".$E_CHEF."> 
		".my_ucfirst(get_prenom($E_CHEF))." ".strtoupper(get_nom($E_CHEF))."</a></td></tr>";
}

$nbadmis=0;
$nbdiplomes=0;

if ( mysql_num_rows($result) > 0 ) {
	echo "<tr height=25><td colspan=2 valign=bottom><b>Réussite des stagiaires à la formation";
	if ( $TF_CODE == 'I' ) echo " / numéro de diplôme";
	if ( $_PS_EXPIRABLE == 1 ) echo " / compétence valide jusqu'au";
	echo"</b></td></tr>";
	
	echo "<tr><td colspan=2 bgcolor=$mylightcolor>
		  <form name='diplomes' action='evenement_diplome.php' method='POST'>";
	echo "<input type=hidden name='evenement' value='".$evenement."'>";
	while ($row=@mysql_fetch_array($result)) {
      $P_NOM=$row["P_NOM"]; 
      $P_PRENOM=$row["P_PRENOM"];
      $P_ID=$row["P_ID"];
      $P_STATUT=$row["P_STATUT"];
      $S_ID=$row["S_ID"];
      $AGE=$row["age"]; 
      $S_CODE=$row["S_CODE"];
	  $C_NAME=$row["C_NAME"];
	  
	  
	  $query1="select count(1) as NB from evenement_participation
	           where P_ID =".$P_ID." 
			   and E_CODE in (".$evts.")";
	  $result1=mysql_query($query1);
	  $row1=@mysql_fetch_array($result1);
	  $n1=$row1["NB"];
	  
	  $query1="select count(1) as NB from evenement_participation 
	           where P_ID=".$P_ID." 
			   and E_CODE in (".$evts.") 
			   and EP_DATE_DEBUT is not null";
	  $result1=mysql_query($query1);
	  $row1=@mysql_fetch_array($result1);
	  $n2=$row1["NB"];

	  
	  if ( check_rights($id, 10,"$S_ID")) $granted_update=true;
      else $granted_update=false;
	  
	  if ($granted_event or ($P_ID == $id and $E_CLOSED == 0) or ($granted_update and $E_CLOSED == 0)) {
		if ($n1 < $nbsessions) $warn="<a href=\"javascript:horaires('$evenement','$P_ID');\">
	  					<img src=images/clock_red.png title=\"Attention n'est pas présent à toutes les parties de la formation\" border=0></a>";
	  	else if ($n2 > 0) $warn="<a href=\"javascript:horaires('$evenement','$P_ID');\">
	  					<img src=images/clock_yellow.png title='Attention horaires différents de ceux de la formation' border=0></a>";
	  	else $warn="<a href=\"javascript:horaires('$evenement','$P_ID');\">
	  					<img src=images/clock_green.png title='Présence totale sur la formation' border=0></a>";
	  }
	  else {
	   	if ($n1 < $nbsessions) $warn="<img src=images/clock_red.png title=\"Attention n'est pas présent à toutes les parties de la formation\" border=0>";
	  	else if ($n2 > 0) $warn="<img src=images/clock_yellow.png title='Attention horaires différents de ceux de la formation' border=0>";
	  	else $warn="<img src=images/clock_green.png title='Présence totale sur la formation' border=0>";
      }
	  
	  $query1="select PF_ADMIS, PF_DIPLOME, date_format(PF_EXPIRATION,'%d/%m/%Y') PF_EXPIRATION  from personnel_formation pf
	  	       where pf.P_ID=".$P_ID." and pf.E_CODE=".$evenement;
	  $result1=mysql_query($query1);
	  $row1=@mysql_fetch_array($result1);
	  $PF_DIPLOME=$row1["PF_DIPLOME"];
	  $PF_EXPIRATION=$row1["PF_EXPIRATION"];
	  if ($row1["PF_ADMIS"] == 1) {
	  		$checked="checked"; 
	  		$nbadmis++;
	  }
	  else $checked="";
	  if ( $PF_DIPLOME <> "" ) $nbdiplomes++;
	  $cmt1=""; $cmt2="";
	  if ( $AGE <> '' )
	  	if ($AGE < 18 ) 
		  $cmt1="<font color=red>(-18)</font>";
		  
	  $for=strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM);
	  echo "<input type=checkbox id='dipl_".$P_ID."' name='dipl_".$P_ID."' 
	  		title=\"cochez cette case si ".$for." a réussi la formation\"
	  		value='".$P_ID."' $checked $disabledtf>";
	  		
	  if ( $TF_CODE == 'I') {
	  	  echo "<input type=text id='num_".$P_ID."' name='num_".$P_ID."' size='10'
	  		title=\"saisissez le numéro de diplôme décerné à ".$for."\"
	  		value='".$PF_DIPLOME."' $disabledtf>";
	  }
	  if ($_PS_EXPIRABLE == 1) {
		echo " <input type='text' size='10' id='exp_".$P_ID."' name='exp_".$P_ID."' $disabledtf  
		value='".$PF_EXPIRATION."'
		title =\"saisissez ici la date de validité de la compétence pour ".$for." au format JJ/MM/AAAA\"
		onchange='checkDate(diplomes.exp_".$P_ID.")'>
		";
    }
	  
	  if ( $P_STATUT == 'EXT' ) {
	  	$colorbegin="<font color=green>";
		$colorend="</font>";
		$title="Personnel externe ".$C_NAME." (".$S_CODE.")";
	  }
	  else {
	   	$colorbegin="";
		$colorend="";
		$title=$S_CODE;
      }
	  
	  $query1="select Q_VAL, DATE_FORMAT(Q_EXPIRATION, '%d-%m-%Y') as Q_EXPIRATION, 
	  			DATEDIFF(Q_EXPIRATION,NOW()) as NB
	  			from qualification
				where P_ID=".$P_ID." 
				and PS_ID=".$PS_ID;
	  $result1=mysql_query($query1);
	  $row1=@mysql_fetch_array($result1);	 
	  $Q_VAL=$row1["Q_VAL"];
	  $Q_EXPIRATION=$row1["Q_EXPIRATION"];
 	  $NB=$row1["NB"];
	  if ( $Q_VAL <> '' ) {
		  if ( $Q_EXPIRATION <> '') {
		     if ($NB <= 0) 
			 	$cmt2="<font size=1 color=red>Compétence $_TYPE expirée depuis $Q_EXPIRATION</font>";
		     else if ($NB < 61) 
			 	$cmt2="<font size=1 color=orange>Compétence $_TYPE expire le $Q_EXPIRATION</font>";
		     else if ( $Q_VAL == 2 ) 
			 	$cmt2="<font size=1 color=blue>Compétence secondaire $_TYPE expire le $Q_EXPIRATION</font>"; 
 		     else if ( $Q_VAL == 1 ) 
			  	$cmt2="<font size=1 color=green>Compétence principale $_TYPE expire le $Q_EXPIRATION</font>";
	     }
	     else if ( $Q_VAL == 2 ) 
		 	$cmt2="<font size=1 color=blue>Compétence secondaire $_TYPE valide</font>";
 	     else if ( $Q_VAL == 1 ) 
		  	$cmt2="<font size=1 color=green>Compétence principale $_TYPE valide</font>";
      }
      else {
            $cmt2="<font size=1 color=black>En formation pour obtenir la compétence $_TYPE</font>";
	   	 	// cas particulier: ne pas montrer PSE1 si PSE2 valide
	 		if ( $_TYPE == 'PSE1') {
	 	 		$query3="select count(*) as NB from qualification q, poste p
		 		where q.P_ID=".$P_ID." and p.PS_ID=q.PS_ID and p.TYPE='PSE2'";
		 		$result3=mysql_query($query3);
		 		$row3=@mysql_fetch_array($result3);
		 		$NB=$row3["NB"];
		  		if ( $NB == 1 ) $cmt2="<font size=1 color=blue>Possède la compétence supérieure PSE2</font>";
			}
	  }
	   			
      echo " <a href=upd_personnel.php?pompier=$P_ID title=\"$title\">".$colorbegin.
	  strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM).$colorend."</a> ".$cmt1." ".$cmt2." ".$warn;
	  
      echo "<br>";
	}
	echo "<table><tr><td><b>Commentaire:</b></td>";
	echo "</tr><tr><td><input type=text size=30 name=comment value =\"".$_F_COMMENT."\" $disabledtf></td>";
	
    echo "</tr></table>";
    if ( $disabledtf == "" ) 
    	echo "<br><input type='submit' value='sauver'>";
    	
	echo "</form></td></tr>";
}

echo "</table>";
echo "</td></tr></table>";

if (( $printdiplomes or $granted_event ) and $nbstagiaires > 0 ) {
	echo "<table><tr>";
	echo "<td><img src=images/printer.gif height=32 
		title='imprimer diplômes ou attestations, choisissez un des différents modes proposés' border=0></a></td>";
	   
	if ( $printdiplomes and $TF_CODE == 'I' and $nbdiplomes > 0) {
	   if ( $_PS_SECOURISME == 1 )
	   echo "
		<td  align=center><a href=pdf_diplome.php?evenement=".$evenement."&mode=1 target=_blank
		title=\"Choisissez cette option si vous disposez de feuilles de diplômes pre-imprimées, ayant chacune un numéro unique. Les n° de diplômes doivent être saisis ci-dessus avant de lancer l'impression. ATTENTION: Les feuilles doivent être introduites dans le bon ordre dans l'imprimante.\"> 
		<font size=1>Diplôme sur<br>papier<br>pré-imprimé<br>numéroté</a></td>";
	   
	   echo "<td  align=center><a href=pdf_diplome.php?evenement=".$evenement."&mode=2 target=_blank
		title=\"Choisissez cette option si vous disposez de feuilles de diplômes pre-imprimées, sans numéro unique. Les n° de diplômes doivent être saisis ci-dessus avant de lancer l'impression, ils seront imprimés.\"> 
		<font size=1>Diplôme sur<br>papier<br>pré-imprimé<br>non numéroté</a></td>";
	   if ( $printfulldiplome ) // autorisable dans config.php
	   echo "<td align=center><a href=pdf_diplome.php?evenement=".$evenement."&mode=3 target=_blank
	   title=\"Choisissez cette option si vous utilisez de feuilles de papier vierges, l'image du diplôme sera imprimée en même temps que les informations du stagiaire diplômé, y compris son numéro de diplôme. Les n° de diplômes doivent être saisis ci-dessus avant de lancer l'impression.\">
	   <font size=1>Diplôme sur<br>papier blanc</a></td>";
	   echo "<td align=center><a href=pdf_diplome.php?evenement=".$evenement."&mode=4 target=_blank
		title=\"Choisissez cette option si vous utilisez de feuilles de papier vierges, le duplicata du diplôme sera imprimé. Les n° de diplômes doivent être saisis ci-dessus avant de lancer l'impression.\">
		<font size=1>Duplicata<br>de<br>diplôme</a></td>";
	}
	if ( $granted_event ){
	   if ( $E_CLOSED == 1 ) 
	     echo "<td align=center><a href=pdf_document.php?evenement=".$evenement."&section=$S_ID&mode=2 target=_blank
		  title=\"Imprimer des attestations sur papier vierge, possible pour tous les stagiaires ayant réussi ou échoué.\">
		  <font size=1>Attestations<br>de<br>formation</a></td>";
	   else  echo "<td align=center><a 
		  title=\"Fermez l'événement pour pouvoir imprimer les attestations.\">
		  <font size=1>Attestations<br>de<br>formation</a></td>";
	}
	echo "</tr></table>";
}

}

//=====================================================================
// tab 6 documents
//=====================================================================

if (( $tab == 6 ) and (! $print)){
echo "<div id='documents'>";


function show_auto_doc($docname, $mode, $secured=true) {
		global $granted_event, $evenement, $S_ID, $mylightcolor;
		$link="pdf_document.php?section=".$S_ID."&evenement=".$evenement."&mode=".$mode;
		$myimg="<img border=0 src=images/smallerpdf.jpg>"; 
		$filedate="";
    	if ( $granted_event or (! $secured)) {
    		$img="<img src=images/unlocksmall.png border=0 title=\"Vous pouvez voir et imprimer ces documents\" height=16>";
			echo "<tr><td bgcolor=$mylightcolor ><a href=".$link.">".$myimg."</a></td>
		  	<td bgcolor=$mylightcolor ><a href=".$link." target=_blank><font size=1>".$docname."</font></a></td>";
		}
		else {
			$img="<img src=images/locksmall.png border=0 title=\"Vous n'avez pas le droit de voir ces documents\" height=14>";
			echo "<tr><td bgcolor=$mylightcolor >".$myimg."</td>
		  	<td bgcolor=$mylightcolor ><font size=1 color=red> ".$docname."</font></a></td>";
		}
		echo "<td bgcolor=$mylightcolor align=center>".$img."</td>
		<td bgcolor=$mylightcolor align=center>-</td>
		<td bgcolor=$mylightcolor align=center>-</td>
		<td bgcolor=$mylightcolor ></td>
		</tr>";

}

function show_hardcoded_doc($number, $docname, $file, $type) {
    // $number = 1 : SST or 2: PSC1
	// $type pdf, or xls
	global $evenement, $S_ID, $mylightcolor;
	$myimg="<img border=0 src=images/smaller".$type.".jpg>";
	$img="<img src=images/unlocksmall.png border=0 title=\"Vous pouvez voir et imprimer ces documents\" height=16>";
	echo "<tr><td bgcolor=$mylightcolor >
		<a href=showfile.php?sst=$number&section=".$S_ID."&evenement=".$evenement."&file=".$file.">".$myimg."</a></td>
		<td bgcolor=$mylightcolor ><a href=showfile.php?sst=$number&section=".$S_ID."&evenement=".$evenement."&file=".$file.">
		<font size=1>".$docname."</font></a></td>
		<td bgcolor=$mylightcolor align=center>".$img."</td>
		<td bgcolor=$mylightcolor align=center>-</td>
		<td bgcolor=$mylightcolor align=center>-</td>
		<td bgcolor=$mylightcolor ></td>
		</tr>";
}

$query="select TD_CODE, TD_LIBELLE from type_document order by TD_LIBELLE";
$result=mysql_query($query);

echo "<p><table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";
echo "<tr>
		  <td width=20 class=TabHeader></td>
      	  <td width=300 class=TabHeader>
			 	Documents attachés</td>
		  <td width=50 class=TabHeader align=center>
			 	Secu.</td>
      	  <td width=120 class=TabHeader align=center>
			 	Auteur</td>
      	  <td width=100 class=TabHeader align=center>
			 	Date</td>
      	  <td width=20 class=TabHeader>Suppr.</td>
      </tr>";

// DOCUMENTS GENERES
if ($granted_event) {
	if ( $TE_CODE == 'FOR' or $TE_CODE == 'MAN' or $TE_CODE == 'EXE' or $TE_CODE == 'REU') {
		if ( $E_CLOSED == 1 ) {
			show_auto_doc("Fiche de présence", "1", true);
			if ( $TE_CODE == 'FOR' and $PS_ID <> '' and $TF_CODE == 'I') show_auto_doc("Procès verbal", "5", true);
		}
	}
	if ( $TE_CODE == 'FOR')
		show_auto_doc("Fiche d'évaluation de la formation", "3", false);
	if ( $TE_CODE == 'DPS' or $TE_CODE == 'AIP' or $TE_CODE == 'HEB' or $TE_CODE == 'MET'
	  or $TE_CODE == 'INS' or $TE_CODE == 'FOR' or $TE_CODE == 'AH' or $TE_CODE == 'GAR') {
		if ( $E_CLOSED == 1 ) 
			show_auto_doc("Ordre de mission", "4", false);	
			
		}
	if ( $TE_CODE == 'DPS' ) 
			show_auto_doc("Convention", "6", false);
	


	// documents spécifiques SST
	$query1="select TYPE from poste where PS_ID=".$PS_ID;
	$result1=mysql_query($query1);
	$row1=@mysql_fetch_array($result1);
	$t=str_replace(" ","",$row1["TYPE"]);
	if ( $t == 'SST' ) {
		show_hardcoded_doc(1,"SST Ouverture de session" , "Notification_Ouverture_Session.pdf","pdf");
		show_hardcoded_doc(1,"SST Fiche Evaluation Individuelle" , "Evaluation_Individuelle.pdf","pdf");
		show_hardcoded_doc(1,"SST Notice Evaluation Individuelle" , "fiche_individuelle_eval_.pdf","pdf");
		show_hardcoded_doc(1,"SST PV de Session" , "PV_Session.pdf","pdf");
		show_hardcoded_doc(1,"SST Procédures administratives" , "procedures_administratives.pdf","pdf");
	}
	if ( $t == 'PSC1' ) {
		show_hardcoded_doc(2,"PSC1 fiche d'évaluation participant" , "eval_participants_psc1.xls","xls");
	}
}

// DOCUMENTS ATTACHES
$mypath=$filesdir."/files/".$evenement;
if (is_dir($mypath)) {
   	$dir=opendir($mypath); 
   	$querys="select DS_ID, DS_LIBELLE,F_ID from document_security";
   	
   	while ($file = readdir ($dir)) {
   		$securityid = "1";
		$securitylabel ="Public";
		$fonctionnalite = "0";
		$author = "";
		
      	if ($file != "." && $file != ".." and (file_extension($file) <> "db")) {
      	    $query="select d.D_ID,d.S_ID,d.D_NAME,d.TD_CODE,d.DS_ID, td.TD_LIBELLE, 
			  		ds.DS_LIBELLE, ds.F_ID, d.D_CREATED_BY
					from document d, document_security ds, type_document td
					where td.TD_CODE=d.TD_CODE
					and d.DS_ID=ds.DS_ID
					and d.E_CODE=".$evenement."
					and d.D_NAME=\"".$file."\"";
					
			$result=mysql_query($query);
			$nb=mysql_num_rows($result);
			$row=@mysql_fetch_array($result);
			
			if ($row["F_ID"] == 0 
				or check_rights($_SESSION['id'], $row["F_ID"], "$S_ID")
				or $documentation
				or $row["D_CREATED_BY"] == $_SESSION['id']) {
				$visible=true;			
			}
			else $visible=false;
			
			if ( in_array(strtolower(file_extension($file)), $supported_ext)) {
		     	$myimg="<img border=0 src=images/smaller".strtolower(file_extension($file)).".jpg>"; 	
		    } 
		    else {
		     	$myimg="<img border=0 src=images/miniquestion.png>";
			}
      	    $filedate = date("Y-m-d H:i",filemtime($mypath."/".$file));
					
			if ( $nb > 0 ) {
				$securityid = $row["DS_ID"];
				$securitylabel =$row["DS_LIBELLE"];
				$fonctionnalite = $row["F_ID"];
				$author = $row["D_CREATED_BY"];
			}
			if ( $visible ) 
				echo "<tr><td bgcolor=$mylightcolor >
				   <a href=showfile.php?section=".$S_ID."&evenement=".$evenement."&file=".$file.">".$myimg."</a></td>
				  	<td bgcolor=$mylightcolor ><a href=showfile.php?section=".$S_ID."&evenement=".$evenement."&file=".$file.">
					  	<font size=1>".$file."</font></a></td>";
			else
				echo "<tr><td bgcolor=$mylightcolor >".$myimg."</td>
					  <td bgcolor=$mylightcolor ><font size=1 color=red> ".$file."</font></td>";
				
			echo "<td bgcolor=$mylightcolor align=center>";
			
			if ( $securityid > 1 ) $img="<img src=images/locksmall.png border=0 title=\"".$securitylabel."\" height=14>";
			else $img="<img src=images/unlocksmall.png border=0 title=\"".$securitylabel."\" height=16>";
	
			if ($documentation) 
					echo "<a href=\"javascript:ReverseContentDisplay('".$file."');\">".$img."</a>";
		    else echo $img;
	
      		echo  "<div id='".$file."' 
					style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width: 430px;
					   height: 150px;
					   padding: 5px;'>
				<form name='form".$file."' action='evenement_save.php' method=POST>
				<input type='hidden' name='action' value='document'>
				<input type='hidden' name='S_ID' value='".$S_ID."'>
				<input type='hidden' name='evenement' value='".$evenement."'>
				<input type='hidden' name='doc' value='".$file."'>
				<table border=0>
				<tr><td colspan=2><b>Informations liées au document:</b></td></tr>
				<tr><td colspan=2>".$myimg."<i> ".$file."</i></td></tr>";
			echo "<tr><td align=right><i>Sécurité</i></td>
				<td align=left><select name='security' id='security'>";
	  			$results=mysql_query($querys);
			while ($rows=@mysql_fetch_array($results)) {
				if ( $rows["DS_ID"] == $securityid) $selected='selected';
				else $selected='';
				echo "<option value='".$rows["DS_ID"]."' $selected>".$rows["DS_LIBELLE"]."</option>";
			}
			echo "</select></td></tr>	
				<tr><td colspan=2 align=center><input type=submit name='s".$file."' value='OK'\"
		    		title='cliquer pour valider les changements'></td></tr></table>
		  		<div align=center><a onmouseover=\"HideContent('".$file."'); return true;\"
   					href=\"javascript:HideContent('".$file."')\"><i>fermer</i></a>
   				</div>
   				</form>
			 	</div>"; 
			echo "</td>";	
			
			if ( $author <> "" ) $author = "<a href=upd_personnel.php?pompier=".$author.">".
				  my_ucfirst(get_prenom($author))." ".strtoupper(get_nom($author))."</a>";

			echo "<td bgcolor=$mylightcolor align=center><font size=1>".$author."</a></font></td>";
				
			echo "<td bgcolor=$mylightcolor align=center>
				<font size=1>".$filedate."</td>";
				
      	  	if ( $documentation)
			      echo "<td bgcolor=$mylightcolor align=center><a href=\"javascript:deletefile('".$evenement."','".$file."')\">
				  <img src=images/trash.png alt='supprimer' border=0></a></td>";
			else echo "<td bgcolor=$mylightcolor></td>";
			echo "</tr>";
      	}
	 }
   }
   if ($documentation) {
	  echo "<tr>
      	  <td colspan=6 bgcolor=$mylightcolor colspan=2 align=left ><b>Attacher un nouveau fichier :</b>";
	  echo "<input type='button' id='userfile' name='userfile' value='Ajouter'
			onclick=\"closeNewDocument(); openNewDocument('".$evenement."','".$S_ID."');\" ></td>";
	  echo " </tr>";
  }   
    
  echo "</table>";// end left table
  echo "</td></tr></table>"; // end cadre

  echo "</div>";
}

if ( $print )
 echo "<p><div align=center><input type=submit value='fermer cette page' onclick='fermerfenetre();'></div> ";

?>

