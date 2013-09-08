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

//=====================================================================
// affiche les diff�rents messages
//=====================================================================

function write_msgbox($type, $image, $message, $top, $left, $width=400) {
global $mydarkcolor,$mylightcolor;
writehead();
echo "<div id='Layer1' align=center >";
echo "<table>
       <tr>
        <td class='FondMenu'>";
echo "
<table width=$width cellspacing=0 border=0>
  <tr class=TabHeader>
    <td width=10></td>
    <td width=140></td>
    <td align=right>".$type."</td>
  </tr>
  <tr height=36 >
    <td bgcolor=$mylightcolor></td>
    <td bgcolor=$mylightcolor >".$image."</td>
    <td bgcolor=$mylightcolor>".$message."</font>
  </tr>
</table>";
echo "</td></tr></table>";
echo "</div>";
}

function param_error_msg(){
 	global $error_pic;
 	write_msgbox("Erreur de param�tres",$error_pic,"<p>Les param�tres fournis � la page sont incorrects<p><input type=submit value='retour' onclick='javascript:history.back(1);'></p>",30,30);
 
}

//=====================================================================
// check session parameters
//=====================================================================

function get_session_parameters(){
 
    global $nbsections, $nbmaxlevels, $mysection, $defaultsectionorder;
	global $order, $category, $filter, $filter2, $company, $subsections, $typecompany, 
	       $old, $type, $position, $typequalif, $type_evenement, $sectionorder, $canceled,
		   $dtdb, $dtfn, $td, $vehicule, $matos, $catmessage, $catmateriel, $lccode, $lcid, $ltcode,
		   $statut, $type_indispo, $person, $validation;

 	$mysection=$_SESSION['SES_SECTION'];
 	
 	// statut personnel (absences)
	if (isset ($_GET["statut"])) {
	    $statut=mysql_real_escape_string($_GET["statut"]);
   		$_SESSION['statut'] = $statut;
	}
	else if ( isset($_SESSION['statut']) ) {
   		$statut=$_SESSION['statut'];
	}
	else {
   		$statut='ALL';
	}
 	// type indisponibilite (absences)
	if (isset ($_GET["type_indispo"])) {
	    $type_indispo=mysql_real_escape_string($_GET["type_indispo"]);
   		$_SESSION['type_indispo'] = $type_indispo;
	}
	else if ( isset($_SESSION['type_indispo']) ) {
   		$type_indispo=$_SESSION['type_indispo'];
	}
	else {
   		$type_indispo='ALL';
	}
	
 	// personne (absences)
	if (isset ($_GET["person"])) {
	    $person=mysql_real_escape_string($_GET["person"]);
   		$_SESSION['person'] = $person;
	}
	else if ( isset($_SESSION['person']) ) {
   		$person=$_SESSION['person'];
	}
	else {
   		$person='ALL';
	}
	
 	// validation (absences)
	if (isset ($_GET["validation"])) {
	    $validation=mysql_real_escape_string($_GET["validation"]);
   		$_SESSION['validation'] = $validation;
	}
	else if ( isset($_SESSION['validation']) ) {
   		$validation=$_SESSION['validation'];
	}
	else {
   		$validation='ALL';
	}
 	
	// order
	if (isset ($_GET["order"])) {
  		$order=mysql_real_escape_string($_GET["order"]);
 		$_SESSION['order'] = $order;
	}
	else if ( isset($_SESSION['order']) ) {
   		$order=$_SESSION['order'];
	}
	else {
 		$_SESSION['order'] = '';
 		$order='';
	}

	// section
	if ( $nbsections == 1 ) $filter=0;
	else if (isset ($_GET["filter"])) {
	    $filter=intval($_GET["filter"]);
   		$_SESSION['sectionchoice'] = $filter;
	}
	else if ( isset($_SESSION['sectionchoice']) ) {
   		$filter=$_SESSION['sectionchoice'];
	}
	else {
   		$filter=$_SESSION['SES_SECTION'];
   		if ( get_level($mysection) >= $nbmaxlevels -1 )
   			$filter=get_section_parent($filter);
	}

	// category
	if ( isset($_GET["category"])) {
 		$category=$_GET["category"];
	}
	else if ( isset($_SESSION['category']) ) {
		$category=$_SESSION['category'];
	}
	else $category='interne';
	if ( $category <> 'EXT' ) $category='interne';
	$_SESSION['category'] = $category;

	// company
	if ( isset($_GET["company"])) {
 		$company=$_GET["company"];
 		if ( $company <> '-1' ) $company=intval($_GET["company"]);
	}
	else if ( isset($_SESSION['company']) ) {
		$company=$_SESSION['company'];
	}
	else $company=-1;
	$_SESSION['company'] = $company;

	// subsections
	if ( isset ($_GET["subsections"])) {
 		$_SESSION['subsections'] = intval($_GET["subsections"]);
 		$subsections=intval($_GET["subsections"]);
	}
	else if ( isset($_SESSION['subsections']) ) {
    	$subsections=$_SESSION["subsections"];
	}
	else { 
 		$subsections=1;
	}
	
	// sectionorder
	if (isset ($_GET["sectionorder"])) {
   		$_SESSION['sectionorder'] = $_GET["sectionorder"];
   		$sectionorder=$_GET["sectionorder"];
	}
	else if ( isset($_SESSION['sectionorder']) ) {
   		$sectionorder=$_SESSION['sectionorder'];
	}
	else {
   		$sectionorder=$defaultsectionorder;
	}
	
	// show old
	if (isset ($_GET["old"])) {
	    $old=intval($_GET["old"]);
   		$_SESSION['old'] = $old;
   		
	}
	else if ( isset($_SESSION['old']) ) {
   		$old=$_SESSION['old'];
	}
	else {
   		$old=0;
	}
	
	// evenements annules
	if (isset ($_GET["canceled"])) {
   		$_SESSION['canceled'] = intval($_GET["canceled"]);
   		$canceled=intval($_GET["canceled"]);
	}
	else if ( isset($_SESSION['canceled']) ) {
   		$canceled=$_SESSION['canceled'];
	}
	else $canceled='0';
	
	// filter2
	if (isset ($_GET["filter2"])) {
	    $filter2=mysql_real_escape_string($_GET["filter2"]);
   		$_SESSION['filter2'] = $filter2;
	}
	else if ( isset($_SESSION['filter2']) ) {
   		$filter2=$_SESSION['filter2'];
	}
	else {
   		$filter2='ALL';
	}
	
	// type evenement
	if (isset ($_GET["type_evenement"])) {
	    $type_evenement=mysql_real_escape_string($_GET["type_evenement"]);
   		$_SESSION['type_evenement'] = $type_evenement;
	}
	else if ( isset($_SESSION['type_evenement']) ) {
   		$type_evenement=$_SESSION['type_evenement'];
	}
	else {
   		$type_evenement='ALL';
	}
	
	// vehicule - used in page engagement
	if (isset ($_GET["vehicule"])) {
	    $vehicule=intval($_GET["vehicule"]);
   		$_SESSION['vehicule'] = $vehicule;
	}
	else if ( isset($_SESSION['vehicule']) ) {
   		$vehicule=$_SESSION['vehicule'];
	}
	else {
   		$vehicule=0;
	}
	
	// matos - used in page engagement
	if (isset ($_GET["matos"])) {
	    $matos=intval($_GET["matos"]);
   		$_SESSION['matos'] = $matos;
	}
	else if ( isset($_SESSION['matos']) ) {
   		$matos=$_SESSION['matos'];
	}
	else {
   		$matos=0;
	}
	
	// type company
	if (isset ($_GET["typecompany"])) {
	    $typecompany=mysql_real_escape_string($_GET["typecompany"]);
   		$_SESSION['typecompany'] = $typecompany;
	}
	else if ( isset($_SESSION['typecompany']) ) {
   		$typecompany=$_SESSION['typecompany'];
	}
	else {
   		$typecompany='ALL';
	}
	
	// type mat�riel
	if (isset ($_GET["type"])) {
	    $type=mysql_real_escape_string($_GET["type"]);
   		$_SESSION['type'] = $type;
	}
	else if ( isset($_SESSION['type']) ) {
   		$type=$_SESSION['type'];
	}
	else {
   		$type='ALL';
	}
	
	// categorie message , infos ou amicale
	if (isset ($_GET["catmessage"])) {
	    $catmessage=mysql_real_escape_string($_GET["catmessage"]);
   		$_SESSION['catmessage'] = $catmessage;
	}
	else if ( isset($_SESSION['catmessage']) ) {
   		$catmessage=$_SESSION['catmessage'];
	}
	else {
   		$catmessage='amicale';
	}
	
	// categorie mat�riel, utilis� dans page param�trage
	if (isset ($_GET["catmateriel"])) {
	    $catmateriel=mysql_real_escape_string($_GET["catmateriel"]);
   		$_SESSION['catmateriel'] = $catmateriel;
	}
	else if ( isset($_SESSION['catmateriel']) ) {
   		$catmateriel=$_SESSION['catmateriel'];
	}
	else {
   		$catmateriel='ALL';
	}
	
	// position du personnel
	if ( isset($_GET["position"])) {
	 	$position=mysql_real_escape_string($_GET["position"]);
	 	$_SESSION['position'] = $position;
	}
	else if ( isset($_SESSION['position']) ) {
   		$position=$_SESSION['position'];
	}
	else {
   		$position='actif';
	}
	
	// type qualif
	if ( isset ($_GET["typequalif"])) {
		$typequalif=intval($_GET["typequalif"]);
		$_SESSION['typequalif'] = $typequalif;
	}
	else if (isset($_SESSION['typequalif'])) {
		$typequalif=$_SESSION['typequalif'];
	}
	else $typequalif=1;
	
	// type document
	if ( isset ($_GET["td"])) {
		$td=mysql_real_escape_string($_GET["td"]);
		$_SESSION['td'] = $td;
	}
	else if (isset($_SESSION['td'])) {
		$td=$_SESSION['td'];
	}
	else $td='ALL';
	
	// categorie historique
	if ( isset ($_GET["lccode"])) {
		$lccode=mysql_real_escape_string($_GET["lccode"]);
		$_SESSION['lccode'] = $lccode;
	}
	else if (isset($_SESSION['lccode'])) {
		$lccode=$_SESSION['lccode'];
	}
	else $lccode='P';
	
	// type historique
	if ( isset ($_GET["ltcode"])) {
		$ltcode=mysql_real_escape_string($_GET["ltcode"]);
		$_SESSION['ltcode'] = $ltcode;
	}
	else if (isset($_SESSION['ltcode'])) {
		$ltcode=$_SESSION['ltcode'];
	}
	else $ltcode='ALL';
	
	// historique pour quoi
	if ( isset ($_GET["lcid"])) {
		$lcid=intval($_GET["lcid"]);
		$_SESSION['lcid'] = $lcid;
	}
	else if (isset($_SESSION['lcid'])) {
		$lcid=$_SESSION['lcid'];
	}
	else $lcid=0;
	
	// default dates
	if ( isset($_SESSION['dtdb'])) 
		$default_dtdb = $_SESSION['dtdb'];
	else
		$default_dtdb=date("d-m-Y",mktime(0,0,0,date("m"),date("d"),date("Y")));

	if ( isset($_SESSION['dtfn'])) 
		$default_dtfn = $_SESSION['dtfn'];
	else if ( date("m") <= 9 ) {
 		$D = array(1,3,5,7,8,10,12);
		if ( in_array( date("m")+3, $D)) $n=31;
		else $n=40;
		$default_dtfn=date("d-m-Y",mktime(0,0,0,date("m")+3,$n,date("Y")));
	}
	else if ( date("m") == 10 )
    	$default_dtfn=date("d-m-Y",mktime(0,0,0,1,31,date("Y")+1));
	else if ( date("m") == 11 )
 	  	$default_dtfn=date("d-m-Y",mktime(0,0,0,2,28,date("Y")+1));
	else if ( date("m") == 12 )
		$default_dtfn=date("d-m-Y",mktime(0,0,0,3,31,date("Y")+1));

	// get date parameters, else use default dates
	if (isset($_GET['dtdb'])) {
 		$dtdb = mysql_real_escape_string($_GET['dtdb']);	
 		$_SESSION['dtdb'] = $dtdb;
	}
	else 
		$dtdb = $default_dtdb;

	if (isset($_GET['dtfn'])) {
 		$dtfn = mysql_real_escape_string($_GET['dtfn']);	
 		$_SESSION['dtfn'] = $dtfn;
	}
	else
		$dtfn = $default_dtfn;
}
//=====================================================================
// inserer dans Log History
//=====================================================================

function insert_log($logtype, $what, $complement="", $code="") {
 	global $log_actions;
 	if ($log_actions == 1) {
 		$query="insert into log_history (P_ID, LT_CODE, LH_WHAT, LH_COMPLEMENT, COMPLEMENT_CODE, LH_STAMP)
 			values (".intval($_SESSION['id']).", '".$logtype."', ".intval($what).",\"".$complement."\", ".intval($code).", NOW())";
 		$res = mysql_query($query);
 	}
 	return 0;
}

//=====================================================================
// choix sectionorder
//=====================================================================

function choice_section_order($page) {
 	global $mylightcolor, $mydarkcolor, $sectionorder;
    $html= "<a href=\"javascript:ReverseContentDisplay('divsectionorder');\" title=\"cliquer pour choisir le mode d'affichage de la liste\">Section</a>";
    $html .= "<div id='divsectionorder' align=left 
				style='display: none;
					   position: absolute; 
					   border-style: solid;
					   border-width: 2px;
					   background-color: $mylightcolor; 
					   border-color: $mydarkcolor;
					   width:220px;
					   height:85px;
					   padding: 5px;'>
				<b>Choix de l'ordre des sections<br>dans la liste d�roulante:</b><br>";
	if ( $sectionorder == 'alphabetique') $checked1='checked';
	else $checked1='';
	$html .= "<label>Alphab�tique <input id='sectionorder' name='sectionorder' type='radio' value='alphabetique' 
				onclick=\"changeSectionOrder('".$page."','alphabetique')\"; $checked1 /></label>"; 
	if ( $sectionorder == 'hierarchique') $checked1='checked';
	else $checked1='';
	$html .= "<label>Hi�rarchique <input id='sectionorder' name='sectionorder' type='radio' value='hierarchique' 
				onclick=\"changeSectionOrder('".$page."','hierarchique')\"; $checked1 /></label>
				<br>
		  		<div align=center><a onmouseover=\"HideContent('divsectionorder'); return true;\"
   					href=\"javascript:HideContent('divsectionorder')\"><i>fermer</i></a>
   				</div>
			 </div>";
	return $html;
}
//=====================================================================
// dates et heures evenement
//=====================================================================
function get_dates_heures($evenement) {
	$datesheures="";
	$sql2="select TIME_FORMAT(EH_DEBUT, '%k:%i') as EH_DEBUT,
	TIME_FORMAT(EH_FIN, '%k:%i') as EH_FIN,
	date_format(EH_DATE_DEBUT,'%d-%m-%Y') as EH_DATE_DEBUT,
	date_format(EH_DATE_FIN,'%d-%m-%Y') as EH_DATE_FIN
	from evenement_horaire
	where E_CODE=".$evenement."
	order by EH_ID";
	$res2 = mysql_query($sql2);
	while($rows2 = mysql_fetch_array($res2)){
	 	$EH_DEBUT=$rows2['EH_DEBUT'];
	 	$EH_FIN=$rows2['EH_FIN'];
	 	$EH_DATE_DEBUT=$rows2['EH_DATE_DEBUT'];
	 	$EH_DATE_FIN=$rows2['EH_DATE_FIN'];
	 	if ($EH_DATE_DEBUT == $EH_DATE_FIN ) $datesheures .= "
le ".$EH_DATE_DEBUT." (".$EH_DEBUT."-".$EH_FIN."), ";
	 	else $datesheures .= "
du ".$EH_DATE_DEBUT." au ".$EH_DATE_FIN." (".$EH_DEBUT."-".$EH_FIN."), ";
	}
	$datesheures=substr($datesheures,0,strlen($datesheures) - 2);
	return $datesheures;
}

//=====================================================================
// nombre d'inscrits avec une comp�tence valide
//=====================================================================
function get_nb_competences($evenement,$partie,$poste=0) {
 	$evts=get_event_and_renforts($evenement,$exclude_canceled_r=true);
 	if ( $poste == 0 ) {
 		$sql="select count(1) as NB 
	 	  from evenement_participation ep
		  where ep.E_CODE in (".$evts.")
		  and ep.EH_ID=".$partie;
	}
 	else
 		$sql="select count(1) as NB 
	 	  from evenement_participation ep, qualification q
		  where ep.E_CODE in (".$evts.")
		  and ep.EH_ID=".$partie."
		  and ep.P_ID = q.P_ID
		  and q.PS_ID = ".$poste."
		  and (q.Q_EXPIRATION > NOW() or q.Q_EXPIRATION is null)";
 	$res = mysql_query($sql);
 	$rows = mysql_fetch_array($res);
 	return $rows["NB"];
}

//=====================================================================
// afficher la liste des codes �v�nements
//=====================================================================

function get_event_and_renforts($evenement,$exclude_canceled_r=true) {
 	$sql="select E_CODE from evenement 
	 		where E_CODE=$evenement 
		  union select E_CODE from evenement 
		  	where E_PARENT=".$evenement;
	if ( $exclude_canceled_r ) $sql .= " and E_CANCELED=0";
 	$res = mysql_query($sql);
 	$A="";
 	while ($rows = mysql_fetch_array($res)){
 	 	$A .= $rows["E_CODE"].",";
 	}
 	return rtrim($A,',');
}
//=====================================================================
// fix charset
//=====================================================================
function fixcharset($string) {
    return strtr($string, 
          '����������������������������������������������������', 
          'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
}
//=====================================================================
// write head
//=====================================================================
function writehead($style='') {
 	global $title,$basedir;
 	if ( $style == 'iphone' ) $css='iphone.css';
 	else $css='main.css';
	echo "<head>
	<title>$title</title>
	<meta http-equiv=Content-Type content='text/html; charset=iso-8859-1'>
	<LINK TITLE='$title' REL='STYLESHEET' TYPE='text/css' HREF='".$basedir."/".$css."'>";
}
//=====================================================================
// detect iphone
//=====================================================================
function is_iphone() {
	if (stristr($_SERVER['HTTP_USER_AGENT'], "iPhone")  || 
		strpos($_SERVER['HTTP_USER_AGENT'], "iPod")     ||
		strpos($_SERVER['HTTP_USER_AGENT'], "iPad")
	)
		return true; 
	else
		return false;
}

//=====================================================================
// dates d'expiration des comp�tences
//=====================================================================
function datesExpiration($nbmonthes,$default,$yearstart=null) {
 	$month=1;
 	if ( $yearstart == "") $year=date("Y") -2;
 	else $year=$yearstart;
 	for ($i=0; $i < $nbmonthes ; $i++) {
 	 	$m = $month + $i;
 	 	if ( $m > 12 ) $m = $m%12;
 	 	if ( $m == 0 ) $m =12;
		if (( $m == 1 ) and ( $i > 0 )) $year = $year +1;
 	 	if ( $m <= 9 ) $MM ='0'.$m;
 	 	else $MM = $m;
 	 	$value =$year."-".$MM."-01";
 	 	if ( "$default" == "$value" ) $selected = 'selected';
 	 	else $selected = '';
		echo "<option value='".$value."' $selected>".$MM." / ".$year."</option>
		";
	}
}

//=====================================================================
// Geolocalisation Google
//=====================================================================
function gelocalize($code, $type='E'){
	
	$query = "delete from geolocalisation where type ='".$type."' and CODE=".intval($code);
	$result=mysql_query($query);
	
	$base_url = 'http://maps.google.com/maps/geo?output=xml';
	
	if ( $type == 'E' ) {
	    $query="select E_ADDRESS from evenement where E_CODE=".intval($code);
		$result=mysql_query($query);
       	$row=mysql_fetch_array($result);
		$address=$row["E_ADDRESS"];
	}
	else if ( $type == 'P' ) {
	    $query="select P_ADDRESS, P_ZIP_CODE, P_CITY from pompier where P_ID=".intval($code);
		$result=mysql_query($query);
       	$row=mysql_fetch_array($result);
		$address=$row["P_ADDRESS"]." ".$row["P_ZIP_CODE"]." ".$row["P_CITY"];
	}
	else if ( $type == 'S' ) {
	    $query="select S_ADDRESS, S_ZIP_CODE, S_CITY from section where S_ID=".intval($code);
		$result=mysql_query($query);
       	$row=mysql_fetch_array($result);
		$address=$row["S_ADDRESS"]." ".$row["S_ZIP_CODE"]." ".$row["S_CITY"];
	}
	else return 2;
	
	$geocode_pending = true;
	$request_url = $base_url.'&q='.urlencode($address);
	$xml = @simplexml_load_file($request_url);
	$status = @$xml->Response->Status->code;
    if (strcmp($status, "200") == 0) {
       // Successful geocode
       $geocode_pending = false;
       $coordinates = $xml->Response->Placemark->Point->coordinates;
       $coordinatesSplit = explode(",", $coordinates);
       // Format: Longitude, Latitude, Altitude
       $lat = $coordinatesSplit[1];
       $lng = $coordinatesSplit[0];
	   $query = "insert geolocalisation (TYPE,CODE,LAT,LNG) values ('".$type."', ".intval($code).", $lat, $lng )";
       $result = mysql_query($query);
	   return 0;
    }
	else return 4;
}
//=====================================================================
// get infos in order to send SMS
//=====================================================================

function mySmsGet($ids,$mode){
 global $sms_account,$phone_prefix;
 $SmsTo="";
 $destinataires=explode(",", $ids);
 $d = 0;
 $m =  count($destinataires);
 $T = array();
 for($i=0; $i < $m ; $i++){
       $matricule = $destinataires[$i];
       if ( $matricule <> "" ) {
        	$query="select P_NOM, P_PHONE, P_PRENOM from pompier 
				where P_PHONE <>'' 
				and (P_PHONE  like '06%'
				OR P_PHONE like '07%')
				and P_ID=".$matricule;
       		$result=mysql_query($query);
       		if ( mysql_num_rows($result) > 0 ) {
       			$row=@mysql_fetch_array($result);
       			$P_PHONE=$row['P_PHONE'];
       			$P_NOM=$row['P_NOM'];
       			$P_PRENOM=$row['P_PRENOM'];
       			if (!in_array($P_PHONE, $T)) {
       			 		$T[$d] = $P_PHONE;
       					$d++;
       			}
       		}
       	}
 }
 $m =  count($T);
 for($i=0; $i < $m ; $i++){
    $cur=$phone_prefix.substr($T[$i], 1, 9);
	if ( $i == $m - 1 ) $SmsTo .= $cur;
	else $SmsTo .= $cur.",";
 }
 if ( $mode == 'data' ) return $SmsTo;
 else return $m;
}

//=====================================================================
// send email
//=====================================================================

function mysendmail($ids,$fromid,$Subject,$Mailcontent){

   global $cisname, $cisurl, $testmail, $mymailmaxdest;
   $SenderName = my_ucfirst(get_prenom($fromid))." ".strtoupper(get_nom($fromid));
   $SenderMail = get_email($fromid);
   $Subject = fixcharset("[".$cisname."] ".$Subject);
   $ids = str_replace(",,",",",$ids);
   $Mailcontent=str_replace("\'","'",urldecode($Mailcontent));
   $Mailcontent .="\n\n".$cisname." - http://".$cisurl;

   $MailTo="";
   $destinataires=explode(",", $ids);
   $d = 0;
   $m =  count($destinataires);
   $T = array();
   for($i=0; $i < $m ; $i++){
       $matricule = $destinataires[$i];
       if ( $matricule <> "" ) {
        	$query="select P_NOM, P_EMAIL, P_PRENOM 
			        from pompier 
				    where P_EMAIL <>'' 
					and P_OLD_MEMBER = 0
					and P_ID='".$matricule."'";
       		$result=mysql_query($query);
       		if ( mysql_num_rows($result) > 0 ) {
       			$row=@mysql_fetch_array($result);
       			$P_EMAIL=$row['P_EMAIL'];
       			$P_NOM=$row['P_NOM'];
       			$P_PRENOM=$row['P_PRENOM'];
       			if (!in_array($P_EMAIL, $T)) {
       			 		$T[$d] = $P_EMAIL;
       					$d++;
       			}
       		}
       	}
   }
   $m =  count($T);
   if ( $mymailmaxdest == 1 ) {
      $Mailcontent .= "\n(Cet email a �t� envoy� � ".$m." destinataire[s].)";
   }
   $j = 0; $ret = 0;
   for($i=0; $i < $m ; $i++){
   		$j++;
		if (( $i == $m - 1 ) or ( $j == $mymailmaxdest )) $MailTo .= $T[$i];
		else $MailTo .= $T[$i].", ";
		if (( $i == $m - 1 ) or ( $j == $mymailmaxdest )) {
			$r = mysubsendmail("$MailTo","$Subject","$Mailcontent","$SenderName","$SenderMail",$j);
			$ret = $ret + $r;
			$MailTo = "";
			$j = 0;
		}
   }
   return $ret;
}

function mysendmail2($MailTo,$fromid,$Subject,$Mailcontent){

   global $cisname, $cisurl, $testmail, $mymailmaxdest;
   $SenderName = my_ucfirst(get_prenom($fromid))." ".strtoupper(get_nom($fromid));
   $SenderMail = get_email($fromid);
   $Subject = fixcharset("[".$cisname."] ".$Subject);
   $Mailcontent=str_replace("\'","'",urldecode($Mailcontent));
   $Mailcontent .="\n\n".$cisname." - http://".$cisurl;

   $ret = mysubsendmail("$MailTo","$Subject","$Mailcontent","$SenderName","$SenderMail",1);
   return $ret;
}

function mysubsendmail($MailTo,$Subject,$Mailcontent,$SenderName,$SenderMail,$nb){
	//if localhost configured to 127.0.0.1, mail is probably not available
	if ($_SERVER["HTTP_HOST"] == '127.0.0.1' ) $error = true;
	if(!isset($error)){
		$header = "From: $SenderName <$SenderMail>\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/plain; charset=iso-8859-1\r\n";
    	if( mail("$MailTo", "$Subject", "$Mailcontent", "$header") ){
            return $nb; // nb emails envoy�s  
    	}else{
            return 0;
    	}
	}
	else return 0;
}

function mysendmailwithattach($destid,$fromid,$Subject,$attachment,$filename,$contenttype){
 
 		global $cisname;
   		$SenderName = my_ucfirst(get_prenom($fromid))." ".strtoupper(get_nom($fromid));
   		$SenderMail = get_email($fromid);
   		$MailTo = get_email($destid);
   		$Subject = fixcharset("[".$cisname."] ".$Subject);
   		$boundary = md5(uniqid(time()));

   		$header = "From: $SenderName <$SenderMail>\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";
		$header .= "This is a multi-part message in MIME format.\r\n";
		$header .= "--".$boundary."\r\n";

		$header .= "Content-Type: ".$contenttype."; name=\"".$filename."\"\r\n";
		$header .= "Content-Transfer-Encoding: base64\r\n";
		$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
		$header .= chunk_split(base64_encode($attachment))."\r\n";
		$header .= "--".$boundary."--";

    	@mail("$MailTo", "$Subject", "", "$header");	
}

//=====================================================================
// Display section tree 0
//=====================================================================

function display_children0($parent, $level, $max, $expand, $order='hierarchique') { 
   global $End;
   global $cisname, $mylightcolor,$mydarkcolor,$my2darkcolor,$my2lightcolor,$myothercolor;
   // order peut prendre les valeurs hierarchique (par defaut) ou alphabetique
   if ( $order == 'hierarchique') {
    if ( $level < $max ) {
     $query="select distinct S_ID, S_CODE, S_DESCRIPTION
		from section 
		where S_PARENT=".$parent."
        order by S_CODE";
     $result = mysql_query($query); 
     $j=mysql_num_rows($result);
     $i=1;
     while (($row = mysql_fetch_array($result)) and ($i < 40)){ 
   	    $S_ID=$row["S_ID"];
        $S_CODE=$row["S_CODE"];
	    $S_DESCRIPTION=$row["S_DESCRIPTION"];
	    if ( get_children($S_ID) <> '' ) {
	   	  $p="<b>".get_section_tree_nb_person("$S_ID")."</b>";
	   	  $v="<b>".get_section_tree_nb_vehicule("$S_ID")."</b>";
	    }
	    else {
	  	  $p="<b>".get_section_nb_person("$S_ID")."</b>";
	  	  $v="<b>".get_section_nb_vehicule("$S_ID")."</b>";
	    }	
	
      	if ( $level == 0 ) $mycolor=$myothercolor;
		elseif ( $level == 1 ) $mycolor=$my2darkcolor;
      	elseif ( $level == 2 ) $mycolor=$my2lightcolor;
      	elseif ( $level == 3 ) $mycolor=$mylightcolor;
      	else $mycolor='white';

	    for ( $n = 1; $n <= $level ; $n++) {
	      if ( $n == $level) {
	         if ( $i == $j ) {
	          if ( get_subsections_nb("$S_ID") == 0 )
		      	 $img='<img src=images/tree_corner.png border=0>';
		       else {
		         if ( $expand == 'false')
		    	 	$img="<a href=\"javascript:changeImage('i".$S_ID."');javascript:appear('d".$S_ID."')\">
				  		<img src=images/tree_expand_corner.png border=0 id='i".$S_ID."'></a> ";
				 else
				 	$img="<a href=\"javascript:changeImage('i".$S_ID."');javascript:appear('d".$S_ID."')\">
				  		<img src=images/tree_collapse_corner.png border=0 id='i".$S_ID."'></a> ";
				}
		    	$End[$n] = 1;
		     }
		     else {
		       if ( get_subsections_nb("$S_ID") == 0 )
		      	 $img='<img src=images/tree_split.png border=0>';
		       else {
		          if ( $expand == 'false')
		      	 	$img="<a href=\"javascript:changeImage('i".$S_ID."');javascript:appear('d".$S_ID."')\">
				  	 <img src=images/tree_expand.png border=0 id='i".$S_ID."'></a> ";
				  else
				  	$img="<a href=\"javascript:changeImage('i".$S_ID."');javascript:appear('d".$S_ID."')\">
				  	 <img src=images/tree_collapse.png border=0 id='i".$S_ID."'></a> ";
				}
		    	$End[$n] = 0;
		     }
		  }
		  else {
			if ( $End[$n] == 0 ) $img='<img src=images/tree_vertline.png border=0> ';	
			else $img='<img src=images/tree_empty.png border=0> ';	
		  }
		  echo $img;   
	    }

	   echo "<a href='javascript:displaymanager($S_ID)' style='background-color:$mycolor'>
	      <b>$S_CODE</b></font></a>
		  <font size=1> - <i>$S_DESCRIPTION</i></font>
		     ( <a href=personnel.php?category=interne&order=P_NOM&filter=".$S_ID."&subsections=1 
			  title=\"personnel de la section ".$S_DESCRIPTION."\">".$p."</a> - 
			  <a href=vehicule.php?order=TV_USAGE&filter=".$S_ID."&filter2=ALL&subsections=1 
			  title=\"v�hicules de la section ".$S_DESCRIPTION."\">".$v."</a> )<br>";
       // call this function again to display this 
       // child's children 
       $i++;
       if ( $expand == 'true') $mystyle='';
       else  $mystyle="style ='display:none;'";
       if ( $level > 0 ) echo "<div id='d".$S_ID."' $mystyle>";
       display_children0("$S_ID", $level+1,$max,$expand);
	   if ( $level > 0 ) echo "</div>";
	   echo "
	   "; 
     } 
   }
  }
  else {
  $query="select distinct S_ID, S_CODE, S_DESCRIPTION, NIV, NB_P, NB_V
		from section_flat
        order by S_CODE";
     $result = mysql_query($query); 
     $j=mysql_num_rows($result);
     $i=1;
     while ($row = mysql_fetch_array($result)){ 
   	    $S_ID=$row["S_ID"];
        $S_CODE=$row["S_CODE"];
	echo "le code est".$S_CODE;
	    $S_DESCRIPTION=$row["S_DESCRIPTION"];
	    $level=$row["NIV"];
	    $NB_P=$row["NB_P"];
		$NB_V=$row["NB_V"];
	    if ( get_children($S_ID) <> '' ) {
	   	  $p="<b>".get_section_tree_nb_person("$S_ID")."</b>";
	   	  $v="<b>".get_section_tree_nb_vehicule("$S_ID")."</b>";
	    }
	    else {
	  	  $p="<b>".get_section_nb_person("$S_ID")."</b>";
	  	  $v="<b>".get_section_nb_vehicule("$S_ID")."</b>";
	    }	
	
      	if ( $level == 0 ) $mycolor=$myothercolor;
		elseif ( $level == 1 ) $mycolor=$my2darkcolor;
      	elseif ( $level == 2 ) $mycolor=$my2lightcolor;
      	elseif ( $level == 3 ) $mycolor=$mylightcolor;
      	else $mycolor='white';
  
  		$prefix='';
	  	for ( $n = 1; $n <= $level ; $n++) {
	    	$prefix .= " .";	
	  	}
  		echo "$prefix <a href='javascript:displaymanager($S_ID)' style='background-color:$mycolor'>
	      <b>$S_CODE</b></font></a>
		  <font size=1> - <i>$S_DESCRIPTION</i></font>
		     ( <a href=personnel.php?category=interne&order=P_NOM&filter=".$S_ID."&subsections=1 
			  title=\"personnel de la section ".$S_DESCRIPTION."\">".$p."</a> - 
			  <a href=vehicule.php?order=TV_USAGE&filter=".$S_ID."&filter2=ALL&subsections=1 
			  title=\"v�hicules de la section ".$S_DESCRIPTION."\">".$v."</a> )<br>";
     }
  }
} 

//=====================================================================
// Display section tree 2
//=====================================================================

function display_children2($parent, $level, $section, $max, $order='hierarchique') { 
   global $mylightcolor,$mydarkcolor,$my2darkcolor,$my2lightcolor,$myothercolor;
   // order peut prendre les valeurs hierarchique (par defaut) ou alphabetique
   if ( $order == 'hierarchique') {
      if ( $level < $max ) {
   		$query="select distinct S_ID, S_CODE, S_DESCRIPTION
			from section 
			where S_PARENT=".$parent."
        	order by S_ID";
   		$result = mysql_query($query); 
   		$i=0;
   		while (($row = mysql_fetch_array($result)) and ( $i < 40)) { 
   	  		$S_ID=$row["S_ID"];
      		$S_CODE=$row["S_CODE"];
      		$S_DESCRIPTION=$row["S_DESCRIPTION"];
      		if ( strlen($S_DESCRIPTION) > 21 )
	  			$S_DESCRIPTION=substr($row["S_DESCRIPTION"],0,21)."..";
      		$prefix='';
	  		for ( $n = 1; $n <= $level ; $n++) {
	    		$prefix .= " .";	
	  		}
	  		if ( $S_ID == $section ) $selected='selected';
	  		else $selected='';
	  		
      		if ( $level == 0 ) $mycolor=$myothercolor;
			elseif ( $level == 1 ) $mycolor=$my2darkcolor;
      		elseif ( $level == 2 ) $mycolor=$my2lightcolor;
      		elseif ( $level == 3 ) $mycolor=$mylightcolor;
      		else $mycolor='white';
      		
      		$class="style='background: $mycolor;'";
	  		echo "<option value='$S_ID' $class $selected>".$prefix." ".$S_CODE." - ".$S_DESCRIPTION."</option>";
      		display_children2($S_ID, $level+1, $section, $max); 
      		$i++;
   		} 
   	   }
   	}
   	else {
   	 	$query="select distinct S_ID, S_CODE, S_DESCRIPTION, NIV
			from section_flat
        	order by S_CODE asc";
   		$result = mysql_query($query); 
   		while ($row = mysql_fetch_array($result)) { 
   	  		$S_ID=$row["S_ID"];
      		$S_CODE=$row["S_CODE"];
      		$S_DESCRIPTION=$row["S_DESCRIPTION"];
      		$level=$row["NIV"];
      		if ( strlen($S_DESCRIPTION) > 21 )
	  			$S_DESCRIPTION=substr($row["S_DESCRIPTION"],0,21)."...";
      		$prefix='';
	  		for ( $n = 1; $n <= $level ; $n++) {
	    		$prefix .= " .";	
	  		}
	  		if ( $S_ID == $section ) $selected='selected';
	  		else $selected='';
	  		
      		if ( $level == 0 ) $mycolor=$myothercolor;
			elseif ( $level == 1 ) $mycolor=$my2darkcolor;
      		elseif ( $level == 2 ) $mycolor=$my2lightcolor;
      		elseif ( $level == 3 ) $mycolor=$mylightcolor;
      		else $mycolor='white';
      		
      		$class="style='background: $mycolor;'";
	  		echo "<option value='$S_ID' $class $selected>".$prefix." ".$S_CODE." - ".$S_DESCRIPTION."</option>";
   		} 
   	}
} 

//=====================================================================
// rebuild section flat
//=====================================================================
function rebuild_section_flat($parent, $level, $max) { 
   global $cisname, $mylightcolor,$mydarkcolor,$my2darkcolor,$my2lightcolor,$myothercolor;
   if ( $parent == -1 )
   	 mysql_query("truncate table section_flat;") or die ("Truncate Erreur : ".mysql_error());  
   if ( $level < $max ) {
     $query="select distinct S_ID, S_CODE, S_DESCRIPTION
		from section 
		where S_PARENT=".$parent."
        order by s_parent, S_CODE";
     $result = mysql_query($query); 
     $j=mysql_num_rows($result);
     $i=1;
     while (($row = mysql_fetch_array($result)) and ($i < 40)){ 
   	    $S_ID=$row["S_ID"];
        $S_CODE=$row["S_CODE"];
	    $S_DESCRIPTION=addslashes(strip_tags($row["S_DESCRIPTION"]));
		$p=get_section_nb_person("$S_ID");
	  	$v=get_section_nb_vehicule("$S_ID");	
		$sql = "insert into section_flat(NIV,S_ID,S_PARENT,S_CODE,S_DESCRIPTION,NB_P,NB_V) 
		        values(".get_level($S_ID).",$S_ID,$parent,\"$S_CODE\",\"$S_DESCRIPTION\",$p,$v)";
	    mysql_query($sql) or die ("<pre>$sql</pre>Insert  $S_ID Erreur : ".mysql_error());
        $i++;
		rebuild_section_flat("$S_ID", $level+1,$max);
     } 
   }
}

//=====================================================================
// companychoice
//=====================================================================

function companychoice($section,$suggestedcompany,$includeparticulier=true,$category='EXT') {
    $family = get_family("$section");
    $familyup =get_family_up("$section");

	$selectbox='';
	$query="select c.TC_CODE, tc.TC_LIBELLE, c.C_ID, c.C_NAME , s.S_CODE
		from company c, type_company tc , section s
		where tc.TC_CODE = c.TC_CODE
		and s.S_ID = c.S_ID";
	if ( $section <> 0 ) 
		$query .= " and c.S_ID in (".$family.",".$familyup.")" ;
	$query .= " order by c.TC_CODE asc, c.C_NAME";
	$result=mysql_query($query);
	$prevTC_CODE='';
	if ( $includeparticulier) {
		if ( $suggestedcompany == 0 ) $selected='selected';
		else $selected ='';
		if ( $category  == 'EXT' ) $u='Particulier';
		else $u='Non pr�cis�';
		$selectbox .= "<option value='0' $selected >... ".$u." ...</option>";
	}
	while ($row=@mysql_fetch_array($result)) {
 		if ( $prevTC_CODE <> $row["TC_CODE"] ) $selectbox .= "<OPTGROUP LABEL='".$row["TC_LIBELLE"]."'>";
 		if ( $suggestedcompany == $row["C_ID"] ) $selected='selected';
		else $selected ='';
		$selectbox .= "<option value='".$row["C_ID"]."' $selected>".substr($row["C_NAME"],0,22)." (".$row["S_CODE"].")</option>";
		$prevTC_CODE=$row["TC_CODE"];
	}
	return $selectbox;
}

//=====================================================================
// sous sections
//=====================================================================

function get_children($parent) {
 	// afficher les sous-section
    if ( $parent == '') return '';
 	$children=""; 
 	$query="select S_ID
			from section 
			where S_PARENT='".$parent."'
        	order by S_ID";
   	$result = mysql_query($query); 
   	$i=0;
   	while (($row = mysql_fetch_array($result)) and ($i < 40)) {
   	 	$children .= $row["S_ID"].",".get_children($row["S_ID"]).",";
   	 	$i++;
	}
	$children=STR_replace(",,",",",trim($children));
	return substr($children, 0, -1);
}

function get_family($section) {
   // afficher la section et ses descendants
    if ( $section == '') return '';
	$list=get_children("$section");
 	if ( $list == '' ) return $section;
	else  return $section.",".$list;
}

function get_family_up($section) {
 	// afficher la section et ses ascendants
    if ( $section == '') return '';
    $list=$section;
    $i=0;
    while (($section <> 0) and ($i < 10)) {
     	$section = get_section_parent("$section");
     	$list = $section.",".$list;
     	$i++;
 	}
	return $list;
}

function is_children($section,$parent) {
    // est ce qu'un section est fille d'une autre
 	$list = preg_split('/,/' , get_children("$parent").",".$parent); 
 	if (in_array($section, $list)) return true;
	else return false; 
}

//=====================================================================
// section level
//=====================================================================

function get_level($section) {
 	$level=0; 
	$parent=get_section_parent($section);
	$i=0;
	while (( $parent <> -1 ) and ( $parent <> '' ) and ( $i < 10)) {
	 	$level++;
	 	$parent=get_section_parent($parent);
	 	$i++;
	}
	return $level;
}

//=====================================================================
// est-il inscrit a un evenement?
//=====================================================================
 function is_inscrit($pid,$evenement) {
 	$query="select count(1) as NB from evenement_participation
	 		where P_ID=".intval($pid)." and E_CODE=".intval($evenement);
	$result=mysql_query($query);
 	$row=mysql_fetch_array($result);
 	if ( $row["NB"] > 0 ) return true;
 	else return false;
 }
//=====================================================================
// Mois en lettres
//=====================================================================

function moislettres($month){
 $mois=array("janvier","f�vrier","mars","avril","mai","juin","juillet","ao�t","septembre","octobre","novembre","d�cembre");
 return $mois[$month - 1];
}

//=====================================================================
// Equipe active
//=====================================================================

function equipeactive($equipe,$periode){
 if ( $periode == "J" ) $query="select EQ_JOUR as VALUE from equipe where EQ_ID=".$equipe;
 else $query="select EQ_NUIT as VALUE from equipe where EQ_ID=".$equipe;
 $result=mysql_query($query);
 $row=mysql_fetch_array($result);
 return $row["VALUE"];
}

//=====================================================================
// Nombre de jours du mois
//=====================================================================

function nbjoursdumois($month, $year){
	 $d=27;
	 while ( checkdate( $month, $d+1, $year) ) {
      	       $d=$d+1;
	 }
	 return $d;
}


function get_nb_equipes() {
	$query="select count(*) as NB from equipe";
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
    return $row["NB"];		
}

//=====================================================================
// combien y a t'il d'engagements pour la p�riode
//=====================================================================
function get_nb_inscriptions($P_ID, $year1, $month1, $day1,$year2, $month2, $day2 ) {
	// retourne le nombre d'inscriptions de la personne sur la plage de dates
	$query="select count(*) as NB from 
			evenement_participation ep, evenement e, evenement_horaire eh
			where e.E_CANCELED = 0
			and e.E_CODE= ep.E_CODE
			and eh.E_CODE = ep.E_CODE
			and eh.EH_ID = ep.EH_ID
			and ep.P_ID = ".$P_ID."
			and ((eh.EH_DATE_DEBUT <= '".$year2."-".$month2."-".$day2."' 
					and eh.EH_DATE_DEBUT >= '".$year1."-".$month1."-".$day1."' ) or
	     		(eh.EH_DATE_FIN <= '".$year2."-".$month2."-".$day2."' 
				 	and eh.EH_DATE_FIN >= '".$year1."-".$month1."-".$day1."'   ) or
	     		(eh.EH_DATE_FIN >= '".$year2."-".$month2."-".$day2."' 
				 	and eh.EH_DATE_DEBUT <= '".$year1."-".$month1."-".$day1."' ) or
	     		(eh.EH_DATE_FIN <= '".$year2."-".$month2."-".$day2."' 
				 	and eh.EH_DATE_DEBUT >= '".$year1."-".$month1."-".$day1."' )
	     )";	
				
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	

}

function get_nb_engagements( $type, $ID, $year1, $month1, $day1,$year2, $month2, $day2 ) {
	// retourne le nombre d'engagements du v�hicule (V) ou mat�riel (M) sur la plage de dates
	if ( $type == 'V' ) 
	$query="select count(1) as NB from 
			evenement_vehicule ev, evenement e, evenement_horaire eh
			where e.E_CANCELED = 0
			and ev.E_CODE = eh.E_CODE
			and e.E_CODE= ev.E_CODE
			and e.E_CODE= eh.E_CODE
			and eh.EH_ID=1
			and ev.V_ID = ".$ID;
	else 	
		$query="select sum(em.EM_NB) as NB from 
			evenement_materiel em, evenement e, evenement_horaire eh
			where e.E_CANCELED = 0
			and em.E_CODE = eh.E_CODE
			and e.E_CODE= em.E_CODE
			and em.MA_ID = ".$ID;	
			
	$query .= " and ((eh.EH_DATE_DEBUT <= '".$year2."-".$month2."-".$day2."' 
					and eh.EH_DATE_DEBUT >= '".$year1."-".$month1."-".$day1."' ) or
	     		(eh.EH_DATE_FIN <= '".$year2."-".$month2."-".$day2."' 
				 	and eh.EH_DATE_FIN >= '".$year1."-".$month1."-".$day1."'   ) or
	     		(eh.EH_DATE_FIN >= '".$year2."-".$month2."-".$day2."' 
				 	and eh.EH_DATE_DEBUT <= '".$year1."-".$month1."-".$day1."' ) or
	     		(eh.EH_DATE_FIN <= '".$year2."-".$month2."-".$day2."' 
				 	and eh.EH_DATE_DEBUT >= '".$year1."-".$month1."-".$day1."' )
	     )";	
				
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	

}

//=====================================================================
// est ce qu'un pompier donn� a des disponibilit�s sur un p�riode?
//=====================================================================
function is_dispo($P_ID,$year1, $month1, $day1,$year2, $month2, $day2) {
	 $query="select count(*) as NB from disponibilite where P_ID =".$P_ID."
              and D_DATE >= '".$year1."-".$month1."-".$day1."'
              and D_DATE <= '".$year2."-".$month2."-".$day2."'
		 	  and D_JOUR= 1";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 $NB=$row["NB"];
	 $query="select count(*) as NB from disponibilite where P_ID =".$P_ID."
              and D_DATE >= '".$year1."-".$month1."-".$day1."'
              and D_DATE <= '".$year2."-".$month2."-".$day2."'
		 	  and D_NUIT=1= 1";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);	 
	 $NB = $NB + $row["NB"];
	 
	 return $NB;

}

//=====================================================================
// affiche le personnel disponible pour la p�riode J, N ou A (J+N)
//=====================================================================
function personnel_dispo($year, $month, $day, $type, $poste, $section) {
	global $nbsections;
	global $mylightcolor;
         $query="select distinct p.P_ID, p.P_NOM, p.P_PRENOM, p.P_SECTION, s.S_CODE, p.P_EMAIL, p.P_PHONE
		 from pompier p, disponibilite d, qualification q, section s
		 where p.P_ID=d.P_ID
		 and p.P_SECTION=s.S_ID
		 and p.P_OLD_MEMBER=0
		 and p.P_STATUT <> 'EXT'
		 and q.P_ID=p.P_ID
		 and d.D_DATE='".$year."-".$month."-".$day."'";
         if ( $type == 'J') $query =$query." and d.D_JOUR=1 and d.D_NUIT=0";
		 else if ( $type == 'N') $query =$query." and d.D_NUIT=1 and d.D_JOUR=0";
		 else if ( $type == 'A') $query =$query." and d.D_JOUR=1 and d.D_NUIT=1";
		 else if ( $type == 'O') $query =$query." and (d.D_JOUR=1 or d.D_NUIT=1)";
		 if ( $poste <> 0) $query =$query." and q.PS_ID=$poste";
		 if ( $section <> 0) $query =$query." and p.P_SECTION in (".get_family("$section").")";
	 $query =$query."\norder by p.P_NOM";
	 $result=mysql_query($query);	
	 
	 while ($row=@mysql_fetch_array($result)) {
	       $P_NOM=$row["P_NOM"];
	       $P_PRENOM=$row["P_PRENOM"];
	       $P_ID=$row["P_ID"];
	       $P_EMAIL=$row["P_EMAIL"];
		   $P_PHONE=$row["P_PHONE"];
	       $S_CODE=$row["S_CODE"];
	       if ( $nbsections <> 1 ) $cmt = ' ('.$S_CODE.')';
	       else $cmt='';
	       if ( $type == 'O') {
				$cmt = " ".my_ucfirst($P_PRENOM).$cmt;
	        	echo "<tr bgcolor=$mylightcolor><td>".strtoupper($P_NOM).$cmt."</td>";
	        	if ( $P_PHONE <>  '') $p="o";
	        	else $p="-";
	        	if ( $P_EMAIL <>  '') $m="o";
	        	else $m="-";
	        	echo "<td>$m</td>";
	        	echo "<td>$p</td>";
	        	echo "</tr>";
	        }
	        else
	        if ( is_out ($P_ID, $year, $month, $day) == 0 )
	       		echo "<a href=upd_personnel.php?pompier=".$P_ID.">".strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM).$cmt."</a><br>";
	 }

}	

function personnel_dispo_ou_non($poste, $section) {
	global $nbsections;
	global $mylightcolor;
	 if ( $poste <> 0)
         $query="select distinct p.P_ID, p.P_NOM, p.P_PRENOM, p.P_SECTION, s.S_CODE, p.P_EMAIL, p.P_PHONE
		 from pompier p, qualification q, section s
		 where p.P_SECTION=s.S_ID
		 and p.P_OLD_MEMBER=0
		 and p.P_STATUT <> 'EXT'
		 and q.P_ID=p.P_ID";
	 else 
	 	$query="select distinct p.P_ID, p.P_NOM, p.P_PRENOM, p.P_SECTION, s.S_CODE, p.P_EMAIL, p.P_PHONE
		 from pompier p, section s
		 where p.P_SECTION=s.S_ID
		 and p.P_OLD_MEMBER=0
		 and p.P_STATUT <> 'EXT'";
	 if ( $poste <> 0) $query =$query." and q.PS_ID=$poste";
	 if ( $section <> 0) $query =$query." and p.P_SECTION in (".get_family("$section").")";
	 $query =$query."\norder by p.P_NOM";
	 $result=mysql_query($query);	
	 while ($row=@mysql_fetch_array($result)) {
	       $P_NOM=$row["P_NOM"];
	       $P_PRENOM=$row["P_PRENOM"];
	       $P_ID=$row["P_ID"];
	       $P_EMAIL=$row["P_EMAIL"];
		   $P_PHONE=$row["P_PHONE"];
	       $S_CODE=$row["S_CODE"];
	       if ( $nbsections <> 1 ) $cmt = ' ('.$S_CODE.')';
	       else $cmt='';
		   $cmt = " ".my_ucfirst($P_PRENOM).$cmt;
	       echo "<tr bgcolor=$mylightcolor><td>".strtoupper($P_NOM).$cmt."</td>";
	       if ( $P_PHONE <>  '') $p="o";
	       else $p="-";
	       if ( $P_EMAIL <>  '') $m="o";
	       else $m="-";
	       echo "<td>$m</td>";
	       echo "<td>$p</td>";
	       echo "</tr>";
	}
}

//=====================================================================
// compte le personnel disponible pour la p�riode J, N 
//=====================================================================
function count_personnel_dispo($year, $month, $day, $type, $section) {
    $query="select count(*) as NB from pompier p, disponibilite d
	where p.P_ID=d.P_ID
	and d.D_DATE='".$year."-".$month."-".$day."'
	and p.P_SECTION in (".get_family("$section").")";
    if ( $type == 'J') $query =$query." and d.D_JOUR=1";
	else if ( $type == 'N') $query =$query." and d.D_NUIT=1";
	//print $query;
	$result=mysql_query($query);	
	 
	$row=@mysql_fetch_array($result);
	return $row["NB"];
}	

	
//=====================================================================
// affiche date fran�aise au format "lundi 1er" ...
//=====================================================================

function date_fran($month, $i ,$year) {
	 $jours=array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi");
	 $num1=date("w", mktime(0,0,0,$month,$i,$year));
	 $num2=date("j", mktime(0,0,0,$month,$i,$year));
	 if ( $num2 == "1" ) { $num2 = "1er" ;}
	 $num3=date("n", mktime(0,0,0,$month,$i,$year))-1;
	 return $jours[$num1]." ".$num2;
	
}
function date_fran_mois($month){
$mois=array("janvier","f�vrier","mars","avril","mai","juin","juillet","ao�t","septembre","octobre","novembre","decembre");
$moisnum=array("01","02","03","04","05","06","07","08","09","10","11","12");
return str_replace($moisnum,$mois,$month);
}
//=====================================================================
// datediff (n'existe pas en mysql 4.0 retourne la diff�rence en jours
//=====================================================================

function getnow() {
	 $query="select DATE_FORMAT(NOW(), '%d-%m-%Y') as NOW";
	 $result=mysql_query($query);	
	 $row=@mysql_fetch_array($result);
	 return $row["NOW"];
}

//=====================================================================
// get version from database
//=====================================================================

function get_conf($id) {
	 $query="select VALUE from configuration where ID=".$id;
	 $result=mysql_query($query);	
	 $row=@mysql_fetch_array($result);
	 return $row["VALUE"];
}

//=====================================================================
// check if ebrigade database
//=====================================================================

function check_ebrigade() {
	 $query="show tables like 'pompier'";
	 $result=@mysql_query($query);	
	 return @mysql_num_rows($result);
}

//=====================================================================
// datediff (n'existe pas en mysql 4.0 retourne la diff�rence en jours
//=====================================================================

function my_date_diff($date1,$date2) {
	 // format des dates dd-mm-yyyy
	 	if ( $date2 == '' ) $date2=$date1;
        $P1=explode("-",$date1);
        $P2=explode("-",$date2);
        return (round((mktime(0,0,0,$P2[1],$P2[0],$P2[2]) - mktime(0,0,0,$P1[1],$P1[0],$P1[2]))/86400));
}

//=====================================================================
//pr�noms, premi�res lettres en majuscule
//=====================================================================

function my_ucfirst($str) {
	$prev="";
	if (  strlen($str) == 0 ) return "";
	for($i = 0; $i < strlen($str); $i++) {
		if ( $i == 0 ) $output=ucfirst($str[$i]);
		else if ( $prev == " " | $prev == "-") $output .= strtoupper($str[$i]);
		else $output .=$str[$i];
		$prev=$str[$i];
	}
	return $output;
}

//=====================================================================
// retourne une liste de P_ID
//=====================================================================
function get_all_section() {
 	 $liste="";
 	 $query="select S_ID from section";
	 $result=mysql_query($query);
	 while ($row=@mysql_fetch_array($result)) {
	    $liste .= $row["S_ID"].",";
	 }
	 $liste .= "0";
	 return $liste;	
}

function get_family_members($section) {
 	 $liste="";
 	 $query="select distinct P_ID from pompier 
	  	where P_OLD_MEMBER = 0
	  	and P_STATUT <> 'EXT'
		and P_SECTION in (".get_family("$section").")";
	 $result=mysql_query($query);
     while ($row=@mysql_fetch_array($result)) {
	    	$liste .= $row["P_ID"].",";
	 }
	 return "$liste";	
}

function get_inscrits($evenement) {
  	 $liste="";
 	 $query="select distinct p.P_ID 
	  from evenement_participation ep, pompier p, evenement e
	  where p.P_OLD_MEMBER = 0
	  and p.P_STATUT <> 'EXT'
	  and p.P_ID = ep.P_ID
	  and ( ep.E_CODE = e.E_CODE or ep.E_CODE = e.E_PARENT)
	  and ep.E_CODE=".$evenement;
	 $result=mysql_query($query);
	 while ($row=@mysql_fetch_array($result)) {
	    $liste .= $row["P_ID"].",";
	 }
	 return $liste;	
}
function get_noms_inscrits($evenement) {
  	 $liste="";
 	 $query="select distinct ep.P_ID, p.P_NOM, p.P_PRENOM, ep.TP_ID
	         from evenement_participation ep, pompier p, evenement e
			 where ( ep.E_CODE = e.E_CODE or ep.E_CODE = e.E_PARENT)
			 and ep.E_CODE=".$evenement."
	         and p.P_ID=ep.P_ID
			 order by p.P_NOM";
	 //echo $query."<br>";
	 $result=mysql_query($query);
	 while ($row=@mysql_fetch_array($result)) {
	  	$TP_LIBELLE="";
	  	if ( $row["TP_ID"] > 0 ) {
	  	 	$query_tp="select TP_LIBELLE from type_participation where TP_ID=".$row["TP_ID"];
	  	 	$result_tp=mysql_query($query_tp);
	  	 	$row_tp=@mysql_fetch_array($result_tp);
	  	 	if ( $row_tp["TP_LIBELLE"] <> "" ) $TP_LIBELLE= "(".$row_tp["TP_LIBELLE"].")";
	  	}
	    $liste .= strtoupper($row["P_NOM"])." ".ucfirst($row["P_PRENOM"])." ".$TP_LIBELLE."\n";
	 }
	 return $liste;	
}

function get_nb_renforts($evenement) {
 	$query="select count(1) as NB from evenement where E_PARENT=".$evenement;
 	$result=mysql_query($query);
 	$row=@mysql_fetch_array($result);
 	return $row["NB"];
}

function get_granted($fonctionnalite,$section, $level = 'parent', $couldbespam = 'no') {
  	 $liste="";
  	 if ( $level == 'local') $sectionlist = "$section";
  	 else $sectionlist = "$section".','.get_section_parent("$section");
 	 $query="select distinct p.P_ID 
	  		 from pompier p, groupe g, habilitation h
	  	     where h.GP_ID=g.GP_ID
	  	     and p.P_OLD_MEMBER = 0
	  	     and p.P_SECTION in (".$sectionlist.")
	  	     and p.GP_ID=g.GP_ID
			 and h.F_ID=".$fonctionnalite."
			 
			 union select distinct p.P_ID 
	  		 from pompier p, groupe g, habilitation h
	  	     where h.GP_ID=g.GP_ID
	  	     and p.P_OLD_MEMBER = 0
	  	     and p.P_SECTION in (".$sectionlist.")
	  	     and p.GP_ID2=g.GP_ID
			 and h.F_ID=".$fonctionnalite."
			 
			 union select distinct p.P_ID
			 from pompier p, groupe g, habilitation h
	  	     where h.GP_ID=g.GP_ID
	  	     and p.GP_FLAG1=1
	  	     and p.P_OLD_MEMBER = 0
	  	     and p.P_SECTION in (select S_ID from section where S_PARENT='".$section."')
	  	     and p.GP_ID=g.GP_ID
			 and h.F_ID=".$fonctionnalite."
			 
			 union select distinct p.P_ID
			 from pompier p, groupe g, habilitation h
	  	     where h.GP_ID=g.GP_ID
	  	     and p.GP_FLAG2=1
	  	     and p.P_OLD_MEMBER = 0
	  	     and p.P_SECTION in (select S_ID from section where S_PARENT='".$section."')
	  	     and p.GP_ID2=g.GP_ID
			 and h.F_ID=".$fonctionnalite."
			 
			 union select distinct p.P_ID
			 from pompier p, section_role sr, habilitation h
			 where p.P_ID = sr.P_ID
			 and sr.S_ID in (".$sectionlist.")
			 and sr.GP_ID = h.GP_ID
			 and h.F_ID=".$fonctionnalite;	
		 
	 $result=mysql_query($query);
	 while ($row=@mysql_fetch_array($result)) {
	    if ( $couldbespam == 'yes' ) {
	     	$query9="select P_NOSPAM from pompier where P_ID=".$row["P_ID"];
	     	$result9=mysql_query($query9);
	     	$row9=@mysql_fetch_array($result9);
	     	if ( $row9["P_NOSPAM"] == 0 ) $liste .= $row["P_ID"].",";
	    }
	    else 
	    	$liste .= $row["P_ID"].",";
	 }
	 return $liste;	
}

function is_chef($pid, $section) {
 	 $query="select S_ID from section_role
	  	     where P_ID='".$pid."'
	  	     and GP_ID in (100,101,102,103,104,106,107)";		 
	 $result=mysql_query($query);
	 while ($row=@mysql_fetch_array($result)) { 
	 	$parent=$row["S_ID"];
	 	if (is_children($section,$parent))
		  return true;
	 }
	 return false;
}

// fonction utilis�e pour protection civile
function is_formateur($pid) {
 	 $query="select count(*) as NB from qualification q, poste p
	  	     where q.P_ID=$pid
	  	     and p.PS_ID = q.PS_ID
			 and p.TYPE like 'PAE%'";		 
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];
}

// fonction utilis�e pour protection civile
function get_formateurs($section) {
  	 $liste="";
 	 $query="select distinct q.P_ID
		from poste p, qualification q, pompier po
		where q.PS_ID=p.PS_ID
		and po.P_SECTION in (".get_family("$section").")
		and po.P_ID = q.P_ID
		and p.TYPE like 'PAE%'
		order by q.P_ID";		 
	 $result=mysql_query($query);
	 while ($row=@mysql_fetch_array($result)) {
	    $liste .= $row["P_ID"].",";
	 }
	 return $liste;	
}

//=====================================================================
// classe gestion des entit�s
//=====================================================================

function get_section_code($id) {
 	 if ( $id == '' ) return '';
	 $query="select S_CODE from section where S_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["S_CODE"];	
}

function get_section_parent($id) {
 	 if ( $id == "" ) return NULL;
	 $query="select S_PARENT from section where S_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["S_PARENT"];	
}

function get_section_nb_person($id) {
 	 if ( $id == "" ) return "";
	 $query="select count(*) as NB from pompier where P_SECTION=".$id."
	 and P_CODE <> '1234'
	 and P_OLD_MEMBER = 0
	 and P_STATUT <> 'EXT'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	
}

function get_section_nb_vehicule($id) {
 	 if ( $id == "" ) return "";
	 $query="select count(*) as NB from vehicule where S_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	
}

function get_section_tree_nb_person($id) {
 	 if ( $id == "" ) return "";
	 $sub=get_children($id);
	 if ( $sub <> '' ) $list=$id.",".$sub;
	 else $list=$id;
	 $query="select count(*) as NB from pompier where P_SECTION in ($list)
	 and P_CODE <> '1234'
	 and P_OLD_MEMBER = 0
	 and P_STATUT <> 'EXT'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	
}

function get_section_tree_nb_vehicule($id) {
 	 if ( $id == "" ) return "";
	 $sub=get_children($id);
	 if ( $sub <> '' ) $list=$id.",".$sub;
	 else $list=$id;
	 $query="select count(*) as NB from vehicule where S_ID in ($list)";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	
}

function get_section_tree_nb_materiel($id) {
 	 if ( $id == "" ) return "";
	 $sub=get_children($id);
	 if ( $sub <> '' ) $list=$id.",".$sub;
	 else $list=$id;
	 $query="select count(*) as NB from materiel where S_ID in ($list)";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	
}

function get_section_nb() {
	 $query="select count(*) as NB from section";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	
}

function get_subsections_nb($parent) {
	 $query="select count(*) as NB from section where S_PARENT='".$parent."'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	
}

function get_section_of($id) {
	 $query="select P_SECTION from pompier where P_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_SECTION"];	
}

//=====================================================================
// get_infos retourne infos sur les personnes
//=====================================================================

function get_cadre($id) {
 	 if ( $id == "" ) return "";
	 $query="select P_ID from section_role 
	 		 where S_ID=".$id."
			 and GP_ID = 107";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_ID"];	
}

function get_nom($id) {
 	 if ( $id == "" ) return "";
	 $query="select P_NOM from pompier where P_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_NOM"];	
}

function get_code($matricule) {
 	 if ( $matricule == "" ) return "";
	 $query="select P_ID from pompier where P_CODE='".$matricule."'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_ID"];	
}

function get_matricule($id) {
 	 if ( $id == "" ) return "";
	 $query="select P_CODE from pompier where P_ID='".$id."'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_CODE"];	
}

function get_phone($id) {
 	 if ( $id == "" ) return "";
	 $query="select P_PHONE from pompier where P_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_PHONE"];	
}

function get_confidentialite($id) {
        if ($id == '') return '';
        $query = "SELECT P_HIDE FROM pompier WHERE P_ID=".$id;
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
        return $row["P_HIDE"];
}

function get_sexe($id) {
  	 if ( $id == "" ) return "M";
	 $query="select P_SEXE from pompier where P_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_SEXE"];	
}

function get_skype($id) {
  	 if ( $id == "" ) return "";
	 $query="select P_SKYPE from pompier where P_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_SKYPE"];	
}
function get_prenom($id) {
  	 if ( $id == "" ) return "";
	 $query="select P_PRENOM from pompier where P_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_PRENOM"];	
}
function get_email($id) {
 	 global $cisname;
	 $query="select P_EMAIL from pompier where P_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 if ( $row["P_EMAIL"] == "" ) return $cisname;
	 else return $row["P_EMAIL"];
}
function get_section($id) {
	 $query="select P_SECTION from pompier where P_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["P_SECTION"];	
}
function get_section_name($id) {
	 global $cisname;
	 if ( $id == '' ) return '';
	 $query="select S_DESCRIPTION from section where S_ID=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["S_DESCRIPTION"];
}
function get_equipe($PS_ID) {
	 $query="select EQ_ID from poste where PS_ID=".$PS_ID;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["EQ_ID"];	
}
function get_equipe_status_jour($equipe) { 
	 $query="select EQ_JOUR from equipe where EQ_ID=".$equipe;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["EQ_JOUR"];	
}
function get_equipe_status_nuit($equipe) { 
	 $query="select EQ_NUIT from equipe where EQ_ID=".$equipe;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["EQ_NUIT"];	
}
function get_poste($vehicule,$role) { 
	 $query="select PS_ID from equipage 
	 	where V_ID=".$vehicule." and ROLE_ID=".$role;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["PS_ID"];	
}

function get_chef_evenement($id) {
 	 if ( $id == "" ) return "";
	 $query="select E_CHEF from evenement where E_CODE=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["E_CHEF"];	
}

function get_section_organisatrice($id) {
 	 if ( $id == "" ) return "";
	 $query="select S_ID from evenement where E_CODE=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["S_ID"];
}

function get_company_evenement($id) {
 	 if ( $id == "" ) return "";
	 $query="select C_ID from evenement where E_CODE=".$id;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["C_ID"];
}

function get_company($pid) {
 	 if ( $pid == "" ) return "";
	 $query="select C_ID from pompier where P_ID=".$pid;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["C_ID"];	
}

function get_company_parent($cid) {
 	 if ( $cid == "" ) return "";
	 $query="select C_PARENT from company where C_ID=".$cid;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["C_PARENT"];	
}

function get_company_name($cid) {
 	 if ( $cid == "" ) return "";
	 $query="select C_NAME from company where C_ID=".$cid;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["C_NAME"];	
}
//=====================================================================
// is_we ( est ce un we ou un ferie )
//=====================================================================
function is_we($month,$day,$year) {
	if ( (date("w", mktime(0,0,0,$month,$day ,$year)) == 0) or (date("w", mktime(0,0,0,$month,$day ,$year)) == 6)) {
		return 1;
	}
	else {
	 $query="select count(*) as NB from calendrier where CAL_DATE='".$year."-".$month."-".$day."'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];	
	}
}	

//=====================================================================
// get fonction
//=====================================================================

function get_fonction($id) {
    if ( $id <> "") {
	    $query="select TP_LIBELLE from type_participation where TP_ID=".$id;
	    $result=mysql_query($query);
	    $row=mysql_fetch_array($result);
            return $row["TP_LIBELLE"];
	}
	else return '';
}

//=====================================================================
// get statut SPP ou SPV
//=====================================================================

function get_statut ($id) {
         if ( $id <> "") {
	    $query="select P_STATUT from pompier where P_ID=".$id;
	    $result=mysql_query($query);
	    $row=mysql_fetch_array($result);
            return $row["P_STATUT"];
	}
	else return 'SPP';
}

//=====================================================================
// write config base de donn�es
//=====================================================================

function write_db_config ($mysqlserver,$mysqluser,$mysqlpassword,$database) {
  global $config_file;
  if ( is_file ($config_file)) unlink($config_file);
  $fh = fopen($config_file, 'w') or die (
	"<font color=red><b>Impossible d'�crire le fichier $config_file.
	<br> V�rifier les permissions sur le filesystem<br>
	<a href=\"javascript:history.back(1)\">Retour</a>");

  fwrite($fh,"<?php".chr(10));
  fwrite($fh, "\$server = '".$mysqlserver."';".chr(10));
  fwrite($fh, "\$database = '".$database."';".chr(10));
  fwrite($fh, "\$user = '".$mysqluser."';".chr(10));
  fwrite($fh, "\$password = '".$mysqlpassword."';".chr(10));
  fwrite($fh,"?>".chr(10));
  fclose($fh);

  chmod($config_file,0700); 
  return 0;
}

//=====================================================================
// connexion base de donn�es
//=====================================================================

function connect () {
	 global $config_file, $server, $database, $password, $user;
	 $diemessage= "<body onload='window.location=\"configuration_db.php?ask=yes\";'>";
	 if (! is_file ($config_file)) die ($diemessage);
	 elseif ($server == "") die ($diemessage);
	 else {
	 	$conn=@mysql_connect("$server", "$user", "$password") or die ($diemessage);
	 	@mysql_selectdb("$database") or die ($diemessage);
	 }
}

//=====================================================================
// maintenance de la base de donn�es
//=====================================================================

function database_maintenance () {
 	 global $days_audit,$days_log,$days_smslog,$days_disponibilite,$days_planning_garde;
     // nettoyage de audit
     $query="delete from audit where DATE_SUB(CURDATE(),INTERVAL ".$days_audit." DAY) >= A_DEBUT ";
     $result=mysql_query($query);
     
     // nettoyage de log_history sauf les UPDQ et UPDPHOTO
     $query="delete from log_history where DATE_SUB(CURDATE(),INTERVAL ".$days_log." DAY) >= LH_STAMP 
	 		and LT_CODE <> 'UPDQ' 
			and LT_CODE <> 'UPDPHOTO'
			and LT_CODE <> 'IMPBADGE'
			and LT_CODE <> 'DEMBADGE'";
     $result=mysql_query($query);
     
     // nettoyage de logsms
     $query="delete from smslog where DATE_SUB(CURDATE(),INTERVAL ".$days_smslog." DAY) >= S_DATE ";
     $result=mysql_query($query);
     
     // nettoyage de demande
     $query="delete from demande";
     $result=mysql_query($query);
     
     // nettoyage de disponibilite
     $query="delete from disponibilite 
		 	where DATE_SUB(CURDATE(),INTERVAL ".$days_disponibilite." DAY) >= D_DATE ";
     $result=mysql_query($query);
     
     // nettoyage de planning_garde
     $query="delete from planning_garde 
		 	where DATE_SUB(CURDATE(),INTERVAL ".$days_planning_garde." DAY) >= PG_DATE ";
     $result=mysql_query($query);
     $query="delete from planning_garde_status 
		 	where DATE_SUB(CURDATE(),INTERVAL ".$days_planning_garde." DAY) >= PG_DATE ";
	 $result=mysql_query($query);

	 $tables = array ('pompier','vehicule','evenement','evenement_participation','evenement_horaire',
				 'planning_garde', 'evenement_vehicule',
				 'planning_garde_status','disponibilite','indisponibilite',
				 'audit','message','qualification','smslog','materiel','type_materiel',
				 'personnel_formation','evenement_facturation','evenement_facturation_detail',
				 'section_role','document','habilitation','log_history');
				 
     for ( $n = 0; $n < sizeof($tables); $n++ ) {
			$query="OPTIMIZE TABLE ".$tables[$n];
			$result=mysql_query($query);
	 }
	 rebuild_section_flat(-1,0,6);
}



//=====================================================================
// d�connexion base de donn�es
//=====================================================================

function disconnect () {
	 mysql_close();
}

//=====================================================================
// trouver le nombre de sessions d'un �v�nement
//=====================================================================

function get_nb_sessions($event) {
 	$query="select count(*) as NB from evenement_horaire
 	        where E_CODE=".intval($event);
 	$result=mysql_query($query);
    $row=mysql_fetch_array($result);
    return $row["NB"];
}

//=====================================================================
// check rights
//=====================================================================

function check_rights($id, $fonctionnalite, $section="undef"){
  global $nbmaxlevels, $nbsections;
  $granted=0;

  // super optimisation, stocker permissions en session
  if (isset($_SESSION['P_'.$fonctionnalite."_".$section])) {
   	if ( $_SESSION['P_'.$fonctionnalite."_".$section] == 1)  return true;
   	else return false;
  }
  
  $_i=intval($id);
  $_f=intval($fonctionnalite);
  $query="select count(*) as NB from
	 		 habilitation h, pompier p
			 where (h.GP_ID = p.GP_ID or h.GP_ID = p.GP_ID2)
			 and p.P_ID = ".$_i."
	 		 and h.F_ID='".$_f."' 
			";
  $result=mysql_query($query);	 
  $row=mysql_fetch_array($result);  
  $_nb = $row["NB"];
  
  // CAS 1 : parametre $section non fourni
  if ( $section == "undef" ) {
     // if not granted check role
     if ( $_nb > 0 ) $granted++;
     else {
	 	$query="select count(*) as NB from
	         habilitation h, section_role sr
	         where sr.GP_ID = h.GP_ID
	         and h.F_ID = ".$_f."
	         and sr.P_ID = ".$_i;
	    $result=mysql_query($query);	
     	$row=mysql_fetch_array($result);
     	if ( $row["NB"] > 0 ) $granted++;
	 }
  }
  // CAS 2 : parametre $section fourni
  else {
     if ( $_nb > 0 ) {
        // check level
        $_s = get_section_of("$_i");
        $query="select 1
			from pompier p, habilitation q 
			where q.F_ID=".$_f."
			and p.GP_FLAG1 = 1
			and p.P_ID=".$_i."
			and p.GP_ID = q.GP_ID
			union
			select 1
			from pompier p, habilitation q 
			where q.F_ID=".$_f."
			and p.GP_FLAG2 = 1
			and p.P_ID=".$_i."
			and p.GP_ID2 = q.GP_ID
			";
        $result=mysql_query($query);
        $num=mysql_num_rows($result);
        if ( $num > 0 ) $_s = get_section_parent("$_s");
     	if ((is_children("$section",$_s)) and ( $_nb > 0 )) $granted++;
     }
     // if not granted check role
     if ( $granted == 0 ) {
	 	$query="select sr.S_ID from
	         habilitation h, section_role sr
	         where sr.GP_ID = h.GP_ID
	         and h.F_ID = ".$_f."
	         and sr.P_ID = ".$_i;
	    $result=mysql_query($query);	
     	while ($row=@mysql_fetch_array($result)) {
     	 	$_s2 = $row["S_ID"];
     	 	if (is_children("$section",$_s2)) $granted++;
		}
	 }
  }
  // CAS 3 : restriction des permissions pour fonctionnalit�s avec F_FLAG = 1
  if (( $nbsections == 0 ) and ( $granted > 0 )) {
      if (get_func_flag($_f) == 1 ) {
         $_g=get_highest_section_where_granted($_i, $_f);
         if  (( get_level(get_section_of($_i))  >= $nbmaxlevels -1 )
         	and (( get_level($_g) >= $nbmaxlevels -1 ) or ( $_g == '')))
         	$granted =  0;	
      }
  }
  if (( $granted == 0 ) && ( $_nb > 0 )) {
  	// CAS 4 : habilitation 24 (permissions ext�rieures)+ $_f : return true
  	$query="select count(*) as NB from
	 		 habilitation h, pompier p
			 where (h.GP_ID = p.GP_ID or h.GP_ID = p.GP_ID2)
			 and p.P_ID = ".$_i."
	 		 and h.F_ID='24' 
			";
  	$result=mysql_query($query);	 
  	$row=mysql_fetch_array($result);  
  	if (( $_nb > 0 ) && ($row["NB"] > 0 )) $granted++;
  }
  
  if ( $granted > 0 ) $_SESSION['P_'.$fonctionnalite."_".$section] = 1;
  else $_SESSION['P_'.$fonctionnalite."_".$section] = 0;
  
  if ( $granted > 0 ) return true;
  else return false;
}

function get_func_flag($fonctionnalite){
 	$query="select F_FLAG from fonctionnalite where F_ID=".$fonctionnalite;
 	$result=mysql_query($query);	
    $row=@mysql_fetch_array($result);
    return $row["F_FLAG"];
}

//=====================================================================
// get permission location
//=====================================================================

// retourne la section la plus �lev�e dans l'organigramme o� 
// les permissions sur une fonctionnalit� sont donn�es � une personne
function get_highest_section_where_granted($id, $fonctionnalite){
 		$_i=intval($id);
  		$_f=intval($fonctionnalite);
  		$_n=99;$_n2=99;$_s='';$_s2='';
  		$query="select p.P_SECTION, p.GP_FLAG1 as NB from
	 		 habilitation h, pompier p
			 where h.GP_ID = p.GP_ID
			 and p.P_ID = ".$_i."
	 		 and h.F_ID='".$_f."'
	 		 union
	 		 select p.P_SECTION, p.GP_FLAG2 as NB from
	 		 habilitation h, pompier p
			 where h.GP_ID = p.GP_ID2
			 and p.P_ID = ".$_i."
	 		 and h.F_ID='".$_f."'
			";
		$result=mysql_query($query);	   
  		while ($row=@mysql_fetch_array($result)) {
     	 	if ( $row["NB"] >= 0 ) $_s=$row[0];
     	 	if ( $row["NB"] == 1 ) {
				$_s = get_section_parent("$_s");
				break;
			}
     	}
     	if ( $_s <> '' ) {
     		$query="select NIV from section_flat where S_ID=".$_s;
     		$result=mysql_query($query);
     		$row=@mysql_fetch_array($result);
     		$_n=$row["NIV"];
     	}
     	
 		$query="select sr.S_ID, sf.NIV from
	         habilitation h, section_role sr, section_flat sf
	         where sr.GP_ID = h.GP_ID
	         and sf.S_ID = sr.S_ID
	         and h.F_ID = ".$_f."
	         and sr.P_ID = ".$_i;
	    $result=mysql_query($query);	
     		while ($row=@mysql_fetch_array($result)) {
     	 		if ( $row["NIV"] < $_n2 ) {
     	 			$_s2 = $row["S_ID"];
     	 			$_n2 = $row["NIV"];
     	 		}
		}
		if ( $_n2 < $_n ) $_s=$_s2;
		return $_s;
}

//=====================================================================
// file extension
//=====================================================================

function file_extension($myfile)
{
	$tmp=explode(".", $myfile);
    return end($tmp);
}

//=====================================================================
// generate password (length = 8 unless other lenght specified
//=====================================================================

function generatePassword ($length = 8)
{

  // start with a blank password
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXZ"; 
    
  $i = 0;  
  // add random characters to $password until $length is reached
  while ($i < $length) { 
    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    // we don't want this character if it's already in the password
    if (!strstr($password, $char)) { 
      $password .= $char;
      $i++;
    }
  }
  return $password;

}


function generateSecretString ()
{
	$mystring = generatePassword();
	$mySecretString = substr(md5($mystring),0,30);
	return  $mySecretString;
}

//=====================================================================
// rmdir full
//=====================================================================
function full_rmdir($dirname){
        if ($dirHandle = opendir($dirname)){
            $old_cwd = getcwd();
            chdir($dirname);

            while ($file = readdir($dirHandle)){
                if ($file == '.' || $file == '..') continue;

                if (is_dir($file)){
                    if (!full_rmdir($file)) return false;
                }else{
                    if (!unlink($file)) return false;
                }
            }

            closedir($dirHandle);
            chdir($old_cwd);
            if (!rmdir($dirname)) return false;

            return true;
        }else{
            return false;
        }
    }


//=====================================================================
// SMS http://sms.pictures-on-line.net
//=====================================================================

function sendSMS_1($to, $message, $mode){ 
 	global $sms_password;
	if ( $mode == 'sms' ) $typesms = "2";
	if ( $mode == 'flash' ) $typesms = "5";
	$text = rawurlencode($message);
 	$request="/sms/sms_send_sms.htm?noauth=$sms_password&dest=$to&msg=$text&typesms=$typesms";
	$baseurl ="http://sms.pictures-on-line.net";
	$url = $baseurl.$request;
	$ret = @file($url);
	//print_r ($ret);
	$out = preg_split('/=/',$ret[0]);
	if ( $out[0] == 'RETOUR') return 'OK';
	else return 'KO';
} 

function getSMSCredit_1(){ 
	global $sms_password;
	$path="http://sms.pictures-on-line.net/sms/sms_get_status.htm";
	$request = "?noauth=".$sms_password;
    $ret = @file($path.$request); 
    //print_r ($ret);
    if ( empty($ret) ) return 'ERREUR';
    return $ret[0]; 
}


//=====================================================================
// SMS envoyersms.org
//=====================================================================
function sendSMS_2($to, $message, $from, $mode){ 
	global $sms_user,$sms_password;
	if ( $mode == 'sms' ) $typesms = "0";
	if ( $mode == 'flash' ) $typesms = "1";
	$path="http://www.envoyersms.org/exe/api.php";
	$request = "?login=".$sms_user;
	$request .= "&pass=".$sms_password;
	$request .= "&msg=".rawurlencode($message);
	$request .= "&dest=".$to;
	$request .= "&exp=".rawurlencode($from);
	$request .= "&mode=".$typesms;
    return @file_get_contents($path.$request); 
} 

function getSMSCredit_2(){ 
	$path="http://www.envoyersms.org/exe/api.php";
	global $sms_user, $sms_password;
	$request = "?login=".$sms_user."&pass=".$sms_password."&action=credit";
    $buffer = @file_get_contents($path.$request); 
    return (substr($buffer, 0, 7)==='CREDIT ')? (int)substr($buffer, 7) : 'ERREUR'; 
}

//=====================================================================
// SMS clickatell.com
//=====================================================================

function connectSMS_3() {
	global $sms_user,$sms_password, $sms_api_id;
	$baseurl ="http://api.clickatell.com";
	$url = "$baseurl/http/auth?user=$sms_user&password=$sms_password&api_id=$sms_api_id";
	$ret = file($url);
	$sess = preg_split('/:/',$ret[0]);
	if ($sess[0] == "OK") {
		$sess_id = trim($sess[1]);
		return "OK:$sess_id";
	}
	else return "KO:$ret[0]";
}

function sendSMS_3($sess_id,$to, $message){ 
	$baseurl ="http://api.clickatell.com";
	$text = rawurlencode($message);
	$url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text";
	$ret = file($url);
	$send = preg_split('/:/',$ret[0]);
	//print_r ($ret);
	if ($send[0] == "ID") return "OK";
	else return "$send[0]:$send[1]";
    	
} 

function getSMSCredit_3($sess_id){ 
 	$baseurl ="http://api.clickatell.com";
	$url = "$baseurl/http/getbalance?session_id=$sess_id";
	$ret = file($url);
	$send = preg_split('/:/',$ret[0]);
	//print_r ($ret);
	//Credit: 250.0 SMS.
	if ($send[0] == "Credit") {
	 	$credit =  intval($send[1] / 2.5);
	 	return $credit;
	}
	else return "ERREUR";
}

//=====================================================================
// SMS Orange	
//=====================================================================

function sendSMS_4($to, $message){ 
	global $sms_api_id;
	$baseurl ="http://run.orangeapi.com/sms";
	$text = rawurlencode($message);
	$url = "$baseurl/sendSMS.xml?id=$sms_api_id&from=38100&to=$to&content=$text&content_encoding=gsm7";
	$response = file_get_contents($url);
	$xml = simplexml_load_string($response);
//return $response;
//echo $xml->status->status_code;
//return print_r($xml->status->status_code);
	$status_code = $xml->status->status_code;
	return $status_code;
} 

//=====================================================================
// SMS SMSMode	
//=====================================================================

function sendSMS_5($to,$message){
    global $sms_user, $sms_password;
    $ch = curl_init();
    $url = 'https://api.smsmode.com/http/1.6/sendSMS.do';
    $url = $url . '?pseudo='.$sms_user.'&pass='.$sms_password.'&message='.$message.'&numero='.$to;
    //echo $url;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response_string = curl_exec($ch);
    curl_close($ch);
    //echo $response_string;
    return $response_string;
} 

function getSMSCredit_5(){ 
	$url="https://api.smsmode.com/http/1.6/credit.do";
	global $sms_user, $sms_password;
        //echo $sms_password;
        $ch = curl_init();
	$url = $url.'?pseudo='.$sms_user.'&pass='.$sms_password;
        //echo $url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response_string = curl_exec($ch);
        curl_close($ch);
        
        return $response_string;
}

//=====================================================================
// fonction d'enregistrement personnel_fromation
//=====================================================================
function save_personnel_formation($pid, $psid, $tfcode, $date, $lieu, $resp, $comment, $evenement, $numdiplome)
{
    global $log_actions;
    if ( $date == "" ) return;
    $tmp=explode ("-",$date);
    $month=$tmp[1]; $day=$tmp[0]; $year=$tmp[2];
   
    $query="insert into personnel_formation 
					( P_ID,
					  PS_ID,
					  TF_CODE,
					  PF_DIPLOME,
					  PF_DATE,
					  PF_RESPONSABLE,
					  PF_LIEU,
					  PF_COMMENT,
					  E_CODE)
		    values (".$pid.",
					".$psid.",
					'".$tfcode."',
					\"".$numdiplome."\",
					'".$year."-".$month."-".$day."',
					\"".$resp."\",
					\"".$lieu."\",
					\"".$comment."\",
					\"".$evenement."\"
					)";
    $result=mysql_query($query);
   
    if ( $tfcode == 'I' ) {
    	$query="select 1 as NB from qualification where P_ID=".$pid." and PS_ID=".$psid;
    	$result=mysql_query($query);
    	$row=@mysql_fetch_array($result);
   		if ( $row["NB"] == 0) {
   			$query="insert into qualification (P_ID, PS_ID, Q_VAL, Q_UPDATED_BY, Q_UPDATE_DATE)
   	  		  	select ".$pid.",PS_ID,1, ".$_SESSION['id'].",NOW()
   	  		  	from poste where PS_ID=".$psid;
   	  		$result=mysql_query($query);
			if ($log_actions == 1) {
				$query1="select TYPE from poste where PS_ID=".$psid;
				$result1=mysql_query($query1);
				$row1=@mysql_fetch_array($result1);
				insert_log("ADQ",$pid, $row1["TYPE"]);
			}
   	  		specific_post_update ($pid);
   		}
    }
}


//=====================================================================
// get infos on upgrade scripts
//=====================================================================
function get_file_version($file) {
    $file = str_replace(".sql","",$file);
    $file = str_replace(".save","",$file); 
 	$chunks = explode("_", $file);
 	$n = count( $chunks ) - 1;
    $count=substr_count($chunks[$n],"-");
    if ($count == 0) {
		return $chunks[$n];
	}
	else return "?";
}

function get_file_from_version($file) {
    $file = str_replace(".sql","",$file); 
 	$chunks = explode("_", $file);
 	$n = count( $chunks ) - 2;
    $count=substr_count($chunks[$n],"-");
	return $chunks[$n];
}

function get_file_to_version($file) {
    $file = str_replace(".sql","",$file); 
 	$chunks = explode("_", $file);
 	$n = count( $chunks ) - 1;
    $count=substr_count($chunks[$n],"-");
	return $chunks[$n];
}

//=====================================================================
// r�cup�rer les informations de session et tester la s�cu
//=====================================================================
function check_all($fonctionnalite, $page="") {
	 global $error_pic,$error_4,$error_pic,$miniquestion_pic,$error_6,$basedir,$identpage;
	 @session_start();
	 connect();
	 if ( ! isset($_SESSION['id']) ) {
   	    write_msgbox("erreur connexion",$error_pic,$error_4."<p align=center><a href=".$identpage." target=_top>s'identifier</a>",30,30);
   	    exit;
	 }
	 if ( ! isset($_SESSION['groupe']) ) {
   	    write_msgbox("erreur connexion",$error_pic,$error_4."<p align=center><a href=".$identpage." target=_top>s'identifier</a>",30,30);
   	    exit;
	 }
	 
	 if (! check_rights($_SESSION['id'], $fonctionnalite)) {
	    	$query="select F_LIBELLE from fonctionnalite where F_ID='".$fonctionnalite."'";
	    	$result=mysql_query($query);
	    	$row=mysql_fetch_array($result);
	    	$FONC=$fonctionnalite." - ".$row["F_LIBELLE"];
	    	$query="select GP_DESCRIPTION from groupe where GP_ID='".$_SESSION['groupe']."'";
	    	$result=mysql_query($query);
	    	$row=mysql_fetch_array($result);
	    	$GROUP=$row["GP_DESCRIPTION"];
            write_msgbox("erreur permission",$error_pic,$error_6 ." <br><i>".$FONC."</i> <a href=".$basedir."/habilitations.php target=_blank>".$miniquestion_pic."</a><p> <a href=\"javascript:history.back(1)\">Retour</a>",30,30);
   	    	exit;
	 }
	 // mise � jour table audit
	 $query="update audit set A_FIN =NOW(), A_LAST_PAGE=\"".$page."\" where P_ID=".$_SESSION['id']." and A_DEBUT >='".$_SESSION['SES_DEBUT']."'";
	 $result=mysql_query($query);	
}

//=====================================================================
//DEB  mise en page par d�faut des tableaux
//=====================================================================
function EbDeb($titre=""){
$out = "<table>
<tr>
<td class='FondMenu'>";
$out .= "<table cellpading=0 cellspacing=0 border=0>";
$out .= "<tr><td class='MenuRub'>$titre</td></tr>";
$out .= "<tr><Td class='Menu'>";
return $out;
}
function EbFin(){
$out = "</td></tr></table>";
$out .= "</td></tr></table>";
return $out;
}
//FIN  mise en page des tableaux
function get_etat_facturation($id,$afficher="txt"){
    $factStatutCode = "";
	$factureStatut = "";
	$factureStatutIco = "";
	$styleEvt = "";
	$sql = "select devis_date, devis_accepte, facture_date, relance_date, paiement_date
from evenement_facturation 
where e_id = '$id'";
	$res = mysql_query($sql);
	if (mysql_num_rows($res)>0){
		while($row = mysql_fetch_array($res)){
			$devisDate = $row['devis_date'];
			$devisAccepte=$row['devis_accepte'];
			$factDate = $row['facture_date'];
			$relanceDate = $row['relance_date'];
			$paiementDate = $row['paiement_date'];
			if($devisDate!=""){
				$tmp=explode ( "-",$devisDate); $year=$tmp[0]; $month=$tmp[1]; $day=$tmp[2];
				$devisDate=$day.'/'.$month.'/'.$year;
				if(checkdate($month,$day,$year)){
				 	$factStatutCode = 'devis';
					$factureStatut = "Devis transmis le $devisDate";					
					$styleEvt=(($devisAccepte==0)?"background-color:grey;color:white;":"background-color:green;color:white;");
					$factureStatutIco=(($devisAccepte==0)?"<img src=\"images/f_grey.gif\" border=\"0\" alt=\"\" title=\"$factureStatut\">":"<img src=\"images/f_green.gif\" border=\"0\" alt=\"\" title=\"$factureStatut\">");
				}else{
					$devisDate="";
				}
			}else{
				$devisDate="";
			}
			if($factDate!=""){
				$tmp=explode ( "-",$factDate); $year=$tmp[0]; $month=$tmp[1]; $day=$tmp[2];
				$factDate=$day.'/'.$month.'/'.$year;
				if(checkdate($month,$day,$year)){
				    $factStatutCode = 'facture';
					$factureStatut = "Facture �mise le $factDate";
					$styleEvt="background-color:orange;color:black;";
					$factureStatutIco="<img src=\"images/f_orange.gif\" border=\"0\" alt=\"\" title=\"$factureStatut\">";
				}else{
					$factDate="";
				}	
			}else{
				$factDate="";
			}		
			if($relanceDate!=""){
				$tmp=explode ( "-",$relanceDate); $year=$tmp[0]; $month=$tmp[1]; $day=$tmp[2];
				$relanceDate=$day.'/'.$month.'/'.$year;
				if(checkdate($month,$day,$year)){
				    $factStatutCode = 'relance';
					$factureStatut = "Relance en date du $relanceDate...";
					$styleEvt="background-color:red;color:white;";
					$factureStatutIco="<img src=\"images/f_red.gif\" border=\"0\" alt=\"\" title=\"$factureStatut\">";
				}else{
					$relanceDate="";
				}	
			}else{
				$relanceDate="";
			}
			if($paiementDate!=""){
				$tmp=explode ( "-",$paiementDate); $year=$tmp[0]; $month=$tmp[1]; $day=$tmp[2];
				$paiementDate=$day.'/'.$month.'/'.$year;
				if(checkdate($month,$day,$year)){
				    $factStatutCode = 'paiement';
					$factureStatut = "Paiement enregistr�...";
					$styleEvt="background-color:white;color:grey;";
					$factureStatutIco="<img src=\"images/f_white.gif\" border=\"0\" alt=\"\" title=\"$factureStatut\">";
				}else{
					$paiementDate="";
				}	
			}else{
				$paiementDate="";
			}		
		}
	}
	switch ($afficher){
	case "code":
	$retour = $factStatutCode;
	break;
	case "css":
	$retour = $styleEvt;
	break;
	case "ico":
	$retour = $factureStatutIco;
	break;
	case "txt":
	default:
	$retour = $factureStatut;
	}
	return $retour;
}

# =======================================
# Utilis� dans :
# dps.php / dps_calc.php / dps_save.php
# =======================================
function pair($nombre){
return (($nombre-1)%2);
};

function CalcRIS($P1=1500,$P2=0.25,$E1=0.25,$E2=0.25,$NbISActeurs=0,$NbISActeursCom="Pas de demande sp�cifique pour les acteurs",$sortie='echo'){
$out = array();
$out['dimNbISActeursCom']=$NbISActeursCom;
$out['dimNbISActeurs']=$NbISActeurs;

$out['NbIS']=0;
$out['effectif'] = 0;
$nbPersEncadrement = 0;

if ($P1 <= 100000){
  $P = $P1;
}else{
  $P = (100000 + (($P1-100000)/2));
}

$i = ($P2 + $E1 + $E2);
$RIS = $i * ($P / 1000);
$RISCalc = ceil($RIS);
if(pair($RISCalc)==0 && $RIS > 4){
  $RISCalc = $RISCalc+1;
}

$out['P1']=$P1;
$out['P2']=$P2;
$out['E1']=$E1;
$out['E2']=$E2;

$out['P']=$P;
$out['i'] = $i;
$out['RIS'] = $RIS;
$out['RISCalc'] = $RISCalc;
$out['NbIS'] = ceil($RISCalc);
$out['commentaire'] = "Aucun";
$out['type'] = "DPS-GE";

if ($RIS<=36){
$out['type'] = "DPS-ME";
$out['commentaire'] ="Compos� de 2 � 3 postes de secours au maximum\nAjouter aux intervenants: \n1 chef de secteur \n2 LAT au minimum.";
$nbPersEncadrement  = 3;
}
if ($RIS<=12){
$out['type'] = "DPS-PE";
$nbPersEncadrement  = 0;
$out['commentaire'] ="";
if($RIS<=4){
  if(pair(ceil($out['NbIS']))==0 && $RIS > 4){
  $out['NbIS'] = $out['NbIS'] + 1;
  }
  if($RIS>1.125){
    $out['NbIS'] = 4;
	$out['commentaire'] ="1.125 < RIS < 4 \n4 intervenants secouristes \n(Nb mini pour 1 �quipe)";
  }
}
}
if ($RIS<=1.125){
$out['type'] = "PAPS";
$out['NbIS'] = 2;
$out['commentaire'] ="Rq : Dans le cas o� les acteurs pr�senteraient un risque diff�rent du public, et en absence d'un dispositif sp�cifique pour les acteurs, le PAPS n'est pas un dispositif de secours suffisant.";
if($E2==0.4){
$out['type'] = "DPS-PE";
$out['NbIS'] = 4;
$out['commentaire'] ="Secours publics � plus de 30 mn > DPS-PE";
}
}
$out['RIS'] = $RIS;
//<!-- Nombre de postes -->
if ($out['NbIS'] > 2){
$out['postes'] = ceil($out['NbIS']/12);
}else{
$out['postes'] = 0 ;
}
//<!-- Nombre de secteurs -->
if($out['postes']>=3){
$out['secteurs'] = ceil($out['postes']/3);
$out['commentaire'] ="\nAjouter aux intervenants: \n1 chef de dispositif, \n".$out['secteurs']." chefs de secteur \n2 LAT au minimum";
$nbPersEncadrement =  1 + floor($out['secteurs']) + 2;
}else{
$out['secteurs'] = 0;
}
//<!-- Nombre d''Equipes -->
$ISPoste = ($out['NbIS'] - ($out['postes'] * 4) ) / 4;
if($ISPoste > $out['postes']){
$out['equipes'] = ceil($out['postes']);
}else{
$out['equipes'] = 0;
}
//<!-- Nombre de binômes -->
$out['binomes'] = floor(($out['NbIS'] - ($out['postes'] * 4) - ($out['equipes'] * 4))/2);
if($out['binomes']<0){
$out['binomes']=0;
}

$out['effectif']= $nbPersEncadrement + floor($out['NbIS']) +  + floor($out['dimNbISActeurs']);

if ($RIS<=0.25){
$out['type'] = "cf. autorit� comp�tente";
$out['commentaire'] ="Pas de dispositif minimal pr�vu \n\nVoir l'autorit� comp�tente";
$out['NbIS'] = 0;
$out['postes'] = 0 ;
$out['equipes'] = 0;
$out['binomes'] = 0;
$out['effectif'] = 0;
}

$out['param'] = implode('|',$out);
if($sortie!='echo'){
return $out;
}else{
$retour = "<div style=\"width:90%;\">";
$retour .= "<b>Dimensionnement pour les acteurs:</b>";
$retour .= "".$out['dimNbISActeursCom'];
$retour .= "<br />Equivalent en intervenants secouristes : ".$out['dimNbISActeurs'];
$retour .= "</div>";
$retour .= "<div style=\"width:90%;\">";
$retour .= "<b>Dimensionnement pour le public: </b><br>";
$retour .= $out['type'];
$retour .= "<p>".preg_replace("/\n/","<br>",$out['commentaire'])."</p>";
$retour .= "<p>Nombre d'intervenants secouristes = ".$out['NbIS']."</p>";
$retour .= "<p><b>Effectif global = ".$out['effectif']."</b></p>";
$retour .= "<fieldset>";
$retour .= "<legend>Exemple de r�partition pour le public</legend>";
//$retour .= ($out['secteurs']>1?"<br />1 Chef de poste":"");
$retour .= "<ul>";
$retour .= ($out['secteurs']>0?"<li>".$out['secteurs']." secteur".($out['secteurs']>1?"s":"")."</li>":"");
$retour .= ($out['postes']>0?"<li>".$out['postes']." poste".($out['postes']>1?"s":"")."</li>":"");
$retour .= ($out['equipes']>0?"<li>".$out['equipes']." &eacute;quipe".($out['equipes']>1?"s":"")."</li>":"");
$retour .= ($out['binomes']>0?"<li>".$out['binomes']." bin&ocirc;me".($out['binomes']>1?"s":"")."</li>":"");
$retour .= "</ul>";
$retour .= "</fieldset>";
//$retour .= $out['param'];
$retour .= "</div>";
echo $retour;
}
}

function EvenementDPS($evenement,$sortie='echo'){
$sql = "select dimP1,dimP2,dimE1,dimE2,dimNbISActeurs,dimNbISActeursCom 
from evenement_facturation 
where e_id='$evenement'";
$res = mysql_query($sql);
//echo "Nb de r�ponses ".mysql_num_rows($res);
if(mysql_num_rows($res)>0){
while($row= mysql_fetch_array($res)){
$P1 = $row['dimP1'];
$P2 = $row['dimP2'];
$E1 = $row['dimE1'];
$E2 = $row['dimE2'];
$IsActeurs = $row['dimNbISActeurs'];
$IsActeursCom = $row['dimNbISActeursCom'];
}
return CalcRIS($P1,$P2,$E1,$E2,$IsActeurs,$IsActeursCom,$sortie);
}
}

function EvenementSave($post){
$msgerr="";
$P1 = (isset($post['P1'])?$post['P1']:0);
$P2 = (isset($post['P2'])?$post['P2']:0.25);
$E1 = (isset($post['E1'])?$post['E1']:0.25);
$E2 = (isset($post['E2'])?$post['E2']:0.25);
$dimNbISActeurs = (isset($post['dimNbISActeurs'])?$post['dimNbISActeurs']:0);
$dimNbISActeursCom = (isset($post['dimNbISActeursCom'])?$post['dimNbISActeursCom']:"");
$evt = CalcRIS($P1,$P2,$E1,$E2,$dimNbISActeurs,$dimNbISActeursCom,'data');
//echo "<pre>";
//echo print_r($evt);
//echo "binomes =" .$evt['binomes'];
//echo "</pre>";
if($post['evenement']>0){
$evenement=$post['evenement'];
$sql = "select count(e_id) NbEvt from evenement_facturation where e_id='$evenement'";
$res = mysql_query($sql);
//echo "<p>$sql<br />".mysql_error()."</p>";
$msgerr .= (mysql_error()>0?"<p>$sql<br />".mysql_error()."</p>":"");
if (mysql_result($res,0)>0){
$sql = "update evenement_facturation
SET 
dimP1 = '".$evt['P1']."'
,dimP2 = '".$evt['P2']."'
,dimE1 = '".$evt['E1']."'
,dimE2 = '".$evt['E2']."'
,dimRIS = '".$evt['RIS']."'
,dimNbISActeurs = '".$evt['dimNbISActeurs']."'
,dimNbISActeursCom = '".addslashes($evt['dimNbISActeursCom'])."'
,dimTypeDPS='".addslashes($evt['type'])."'
,dimTypeDPSComment='".addslashes($evt['commentaire'])."'
,dimPostes='".$evt['postes']."'
,dimEquipes='".$evt['equipes']."'
,dimBinomes='".$evt['binomes']."'
WHERE e_id='$evenement'";
$res = mysql_query($sql);
}else{
$sql = "INSERT into evenement_facturation 
(e_id,dimP1,dimP2,dimE1,dimE2,dimRIS,dimNbISActeurs,dimNbISActeursCom,dimTypeDPS,dimTypeDPSComment,dimPostes,dimEquipes,dimBinomes)
VALUES('$evenement','".$evt['P1']."','".$evt['P2']."','".$evt['E1']."','".$evt['E2']."','".$evt['RIS']."','".$evt['dimNbISActeurs']."','".addslashes($evt['dimNbISActeursCom'])."','".addslashes($evt['type'])."','".addslashes($evt['commentaire'])."','".$evt['postes']."','".$evt['equipes']."','".$evt['binomes']."')";
$res = mysql_query($sql);

$queryC="select c.C_NAME, c.C_ADDRESS, c.C_ZIP_CODE, c.C_CITY, c.C_EMAIL, c.C_FAX, c.C_PHONE, c.C_CONTACT_NAME
				from evenement e, company c
				where e.C_ID=c.C_ID
				and e.E_CODE=".$evenement;
$resultC=mysql_query($queryC);
$rowC=mysql_fetch_array($resultC);
$evtOrga=$rowC['C_NAME'];
$evtAdresse=$rowC['C_ADDRESS'];
$evtCP=$rowC['C_ZIP_CODE'];
$evtVille=$rowC['C_CITY'];
$evtMobile="";
$evtTel="";
if (substr($rowC['C_PHONE'],0,2)=='06' ) $evtMobile=$rowC['C_PHONE'];
else $evtTel=$rowC['C_PHONE'];
$evtFax=$rowC['C_FAX'];
$evtEmail=$rowC['C_EMAIL'];
$evtContact=$rowC['C_CONTACT_NAME'];
if ( $evtOrga <> "") {
 	$queryC="update evenement_facturation set 
 			 devis_orga=\"".$evtOrga."\",
 			 devis_contact=\"".$evtContact."\",
 			 devis_adresse=\"".$evtAdresse."\",
 			 devis_cp=\"".$evtCP."\",
 			 devis_ville=\"".$evtVille."\",
 			 devis_tel1=\"".$evtMobile."\",
 			 devis_tel2=\"".$evtTel."\",
 			 devis_fax=\"".$evtFax."\",
 			 devis_civilite=\"Madame, Monsieur\",
 			 devis_email=\"".$evtEmail."\"
 	         where e_id=".$evenement;
 	$resultC=mysql_query($queryC);
  }
}
//echo "<p>$sql<br />".mysql_error()."</p>";
$msgerr .= (mysql_error()>0?"<p>$sql<br />".mysql_error()."</p>":"");
// maj evenement

$sql = "update evenement set E_NB_DPS = ".$evt['effectif']." where e_code='$evenement'";
$res = mysql_query($sql);
$msgerr .= (mysql_error()>0?"<p>$sql<br />".mysql_error()."</p>":"");
}
echo(($msgerr!="")?$msgerr:"<p class=\"commentaire\">Dimensionnement enregistr&eacute;
<br>Il faut pr�voir ".$evt['effectif']." personnes au total.
<ul>
<li>Pour les acteurs : ".$evt['dimNbISActeurs']."</li>
<li>Pour le public : ".$evt['NbIS']."</li>
</ul>
Commentaire : ".$evt['type']."<pre>".$evt['commentaire']."</pre>
</p>"
);
}

//=====================================================================
// afficher parametres (utile en debug)
//=====================================================================
function display_post_get() { 
   if ($_POST) { 
      echo "Displaying POST Variables: <br> \n"; 
      echo "<table border=1> \n"; 
      echo " <tr> \n"; 
      echo "  <td><b>result_name </b></td> \n "; 
      echo "  <td><b>result_val  </b></td> \n "; 
      echo " </tr> \n"; 
      while (list($result_nme, $result_val) = each($_POST)) { 
         echo " <tr> \n"; 
         echo "  <td> $result_nme </td> \n"; 
         echo "  <td> $result_val </td> \n"; 
         echo " </tr> \n"; 
      } 
      echo "</table> \n"; 
   } 
   if ($_GET) { 
      echo "Displaying GET Variables: <br> \n"; 
      echo "<table border=1> \n"; 
      echo " <tr> \n"; 
      echo "  <td><b>result_name </b></td> \n "; 
      echo "  <td><b>result_val  </b></td> \n "; 
      echo " </tr> \n"; 
      while (list($result_nme, $result_val) = each($_GET)) { 
         echo " <tr> \n"; 
         echo "  <td> $result_nme </td> \n"; 
         echo "  <td> $result_val </td> \n"; 
         echo " </tr> \n"; 
      } 
      echo "</table> \n"; 
   } 
} 
// End of display_post_get

//=====================================================================
// afficher les num�ros de t�l�phone de mani�re lisible
//=====================================================================
function clean_display_phone($nbr, $sep=false)
{
	$nbr = preg_replace('[^0-9]', '', $nbr);
 
	if(strlen($nbr) != 10)
		return false;
	else
	{
		if($sep)
		{
			for($i=0;$i<5;$i++)
				$nbr_array[] = substr($nbr, $i*2, 2);
			$nbr = implode($sep, $nbr_array);
 
			return $nbr;
		}
			else
				return $nbr;
	}
}
// End of clean_display_phone
?>
