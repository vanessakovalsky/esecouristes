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

$printed_by="imprimé par ".my_ucfirst(get_prenom($id))." ".strtoupper(get_nom($id)). " le ".date("d-m-Y � H:i");
$mode=intval($_GET["mode"]);

if ( isset($_GET["P_ID"])) $pid=intval($_GET["P_ID"]);
else $pid=0;

require_once('fpdf/fpdf.php');
require_once("fpdf/fpdi.php");
require_once("fpdf/ebrigade.php");

$evenement=intval($_GET["evenement"]);
//echo "id de l'evenement :".$evenement;
$section=intval($_GET["section"]);
// dates et infos �v�nement
$query = "SELECT e.PS_ID, eh.EH_ID, DATE_FORMAT(eh.EH_DATE_DEBUT, '%d-%m-%Y') as EH_DATE_DEBUT,
		  DATE_FORMAT(eh.EH_DATE_FIN, '%d-%m-%Y') as EH_DATE_FIN, e.E_LIEU, sf.NIV, s.S_PARENT,
		  s.S_DESCRIPTION, s.S_ID, s.S_CODE, s.S_CITY, s.S_PDF_PAGE, e.E_LIBELLE,
		  s.S_URL, s.S_PHONE, s.S_EMAIL2, s.S_FAX, s.S_EMAIL, s.S_ADDRESS, s.S_CITY, s.S_ZIP_CODE, e.E_NB_VPSP, e.E_NB_AUTRES_VEHICULES, e.E_CONSIGNES, e.E_REPAS, e.E_TRANSPORT, e.E_MOYENS_INSTALLATION, e.E_CLAUSES_PARTICULIERES, e.E_CLAUSES_PARTICULIERES2, e.E_NB,
		  TIME_FORMAT(eh.EH_DEBUT, '%k:%i') as HEURE_DEB, e.E_CHEF, eh.EH_DUREE,
		  TIME_FORMAT(eh.EH_FIN, '%k:%i') as HEURE_FIN, TIME_FORMAT(eh.EH_HEURE_RDV, '%k:%i') as HEURE_RDV, eh.EH_LIEU_RDV, te.TE_LIBELLE, e.TE_CODE,
		  e.C_ID, e.E_CONTACT_LOCAL, e.E_CONTACT_TEL, c.C_ADDRESS, c.C_ZIP_CODE, c.C_CITY, c.C_EMAIL, c.C_PHONE, c.C_CONTACT_NAME, s.S_FRAIS_ANNULATION
		  FROM evenement e, section s, section_flat sf, type_evenement te, evenement_horaire eh, company c
		  WHERE e.E_CODE=".$evenement."
		  and e.E_CODE = eh.E_CODE
		  and te.TE_CODE = e.TE_CODE
		  and s.S_ID = ".$section."
		  and sf.S_ID = s.S_ID
		  and e.C_ID = c.C_ID
		  order by eh.EH_ID" ;
$result=mysql_query($query) or die (mysql_error());

$EH_ID= array();
$EH_DEBUT= array();
$EH_DATE_DEBUT= array();
$EH_DATE_FIN= array();
$EH_FIN= array();
$EH_DUREE= array();
$EH_HEURE_RDV=array();
$EH_LIEU_RDV=array();
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
while ($row = mysql_fetch_array($result)) {
	$te_code=$row["TE_CODE"];
	$lieu=stripslashes($row["E_LIEU"]);
	$type_evenement=$row["TE_LIBELLE"];
	//echo $type_evenement;
	$responsable=$row["E_CHEF"];
if ( $responsable <> "" ) {
	$responsable_phone = get_phone($responsable);
	$responsable_phone_affichage_propre = clean_display_phone($responsable_phone, ' ');
	$responsable_email = get_email($responsable);
	$responsable = my_ucfirst(get_prenom($responsable))." ".strtoupper(get_nom($responsable));}
	$organisateur=$row["S_DESCRIPTION"];
	$organisateur_city=$row["S_CITY"];
	//if ( $organisateur_city <> "" ) $organisateur_city = "� ".$organisateur_city;
	$company=$row["C_ID"];
	if ( intval($company) > 0 )  $company=get_company_name($company);
	$company_address=stripslashes($row["C_ADDRESS"]);
	$company_cp=$row["C_ZIP_CODE"];
	$company_city=stripslashes($row["C_CITY"]);
	$company_phone=$row["C_PHONE"];
	$company_email=$row["C_EMAIL"];
	$company_representant=$row["C_CONTACT_NAME"];
	$contact=$row["E_CONTACT_LOCAL"];
	$contact_tel=clean_display_phone($row["E_CONTACT_TEL"], ' ');
	if ($contact_tel <> "" ) $contact = "".$row["E_CONTACT_LOCAL"]." (t�l. ".$contact_tel.")";
	$section=$row["S_ID"];
	$description=stripslashes($row["E_LIBELLE"]);
	$S_URL=$row["S_URL"];
	if ( $row["S_EMAIL"] <> "" ) $S_EMAIL=$row["S_EMAIL"];
	else $S_EMAIL=$row["S_EMAIL2"];
	$S_PHONE=$row["S_PHONE"];
	$S_FAX=$row["S_FAX"];
	$S_ADDRESS=stripslashes($row["S_ADDRESS"]);
	$S_CITY=stripslashes($row["S_CITY"]);
	$S_PARENT=$row["S_PARENT"];
	$S_ZIP_CODE=$row["S_ZIP_CODE"];
	$niv=$row['NIV'];
	$s_description=$row['S_DESCRIPTION'];
	$psid=$row["PS_ID"];
	$nb_vpsp=$row["E_NB_VPSP"];
	$nb_autres_vehicules=$row["E_NB_AUTRES_VEHICULES"];
	$moyen_installation_1=stripslashes($row["E_MOYENS_INSTALLATION"]);
	$repas=$row["E_REPAS"];
	$transport=$row["E_TRANSPORT"];
	$clause_particuliere_1=stripslashes($row["E_CLAUSES_PARTICULIERES"]);
	$clause_particuliere_2=stripslashes($row["E_CLAUSES_PARTICULIERES2"]);
	$consignes=stripslashes($row["E_CONSIGNES"]);
	$nb_is=$row["E_NB"];
	$frais_annulation=$row["S_FRAIS_ANNULATION"];
	$dps_interasso=$row["E_FLAG1"];	

	// tableau des sessions
	$EH_ID[$i]=$row["EH_ID"];
    $EH_DEBUT[$i]=$row["HEURE_DEB"];
    $EH_DATE_DEBUT[$i]=$row["EH_DATE_DEBUT"];
	if ( $row["EH_DATE_FIN"] == '' ) 
		$EH_DATE_FIN[$i]=$row["EH_DATE_DEBUT"];
    else 
	    $EH_DATE_FIN[$i]=$row["EH_DATE_FIN"];
    $EH_FIN[$i]=$row["HEURE_FIN"];
    $EH_DUREE[$i]=$row["EH_DUREE"];
	$EH_HEURE_RDV[$i]=$row["HEURE_RDV"];
	$EH_LIEU_RDV[$i]=stripslashes($row["EH_LIEU_RDV"]);
    if ( $EH_DUREE[$i] == "") $EH_DUREE[$i]=0;
    $E_DUREE_TOTALE = $E_DUREE_TOTALE + $EH_DUREE[$i];
	$tmp=explode ( "-",$EH_DATE_DEBUT[$i]); $year1[$i]=$tmp[2]; $month1[$i]=$tmp[1]; $day1[$i]=$tmp[0];
	$date1[$i]=mktime(0,0,0,$month1[$i],$day1[$i],$year1[$i]);
    $tmp=explode ( "-",$EH_DATE_FIN[$i]); $year2[$i]=$tmp[2]; $month2[$i]=$tmp[1]; $day2[$i]=$tmp[0];
	$date2[$i]=mktime(0,0,0,$month2[$i],$day2[$i],$year2[$i]);

	$rdv_evt[$i]=" Heure de RDV : <B>".$EH_HEURE_RDV[$i]."</B> Lieu de RDV : <B>".$EH_LIEU_RDV[$i]."</B>";
	if ( $EH_DATE_DEBUT[$i] == $EH_DATE_FIN[$i])
		$horaire_evt[$i]=date_fran($month1[$i], $day1[$i] ,$year1[$i])." ".moislettres($month1[$i])." ".$year1[$i]." de ".$EH_DEBUT[$i]." � ".$EH_FIN[$i];
	else
		$horaire_evt[$i]="du ".date_fran($month1[$i], $day1[$i] ,$year1[$i])." ".moislettres($month1[$i])." ".$EH_DEBUT[$i]." au "
		                 .date_fran($month2[$i], $day2[$i] ,$year2[$i])." ".moislettres($month2[$i])." ".$year2[$i]." ".$EH_FIN[$i]."";
	$i++;
	//echo $EH_ID[$i];
}
//print_r($EH_ID);
$nbsessions=count($EH_ID);
//echo $nbsessions;
$last=$i-1;

$periode='';
for ($i=1; $i <= $nbmaxsessionsparevenement; $i++) {
       if (isset($horaire_evt[$i]))
		   $periode .=$horaire_evt[$i]."<BR>";

}
$rdv='';
for ($i=1; $i <= $nbmaxsessionsparevenement; $i++) {
		if (isset($rdv_evt[$i]))
		   $rdv .=$rdv_evt[$i];
}

$periode_rdv='';
for ($i=1; $i <= $nbmaxsessionsparevenement; $i++) {
		if (isset($rdv_evt[$i]))
		   $periode_rdv .="Date : ".$horaire_evt[$i]. " - ".$rdv_evt[$i]."<BR>";
}

//$periode = substr($periode,0,strlen($periode) -2);

//if ( $last > 1 ) 
//$periode="Du ".$EH_DATE_DEBUT[1]." au ".$EH_DATE_FIN[$last];

$query2="select tf.TF_CODE, tf.TF_LIBELLE from type_formation tf, evenement e
where e.TF_CODE= tf.TF_CODE
and e.E_CODE=".$evenement;
$result2 = mysql_query($query2);
$row2=mysql_fetch_array($result2);
$TF_LIBELLE=$row2['TF_LIBELLE'];
$TF_CODE=$row2['TF_CODE'];

if ( $psid <> "" ){
	$query2="select TYPE, DESCRIPTION, PS_NATIONAL,PS_SECOURISME 
			from poste where PS_ID=".$psid;
	$result2=mysql_query($query2); 
	$row2 = mysql_fetch_array($result2);
	$national=$row2["PS_NATIONAL"];
	$secourisme=$row2["PS_SECOURISME"];
	$description=$row2["DESCRIPTION"];
	$type=$row2["TYPE"];
}
else {
	$national=0;
	$secourisme=0;
	$type="";
}

// mode imprimer seulement 1 attestation pour une personne
if ( $pid > 0 and $mode==2 ) {
 	check_all(4);
	if (! check_rights($id,4,get_section_of($pid))) check_all(24);
}
// mode g�n�ral imprimer tous les documents
else if ($id <> $responsable ) {
	check_all(15);
	if ((! check_rights($id, 15, "$section"))) check_all(24);
}


// informations organisateur
if ( $niv == $nbmaxlevels -1 ) {
	// cas antenne locale, on donne les infos du d�partement
	$query2="select S_ID, S_CODE, S_DESCRIPTION from section where S_ID=".$S_PARENT;
	$res2 = mysql_query($query2);
	$row2 = mysql_fetch_array($res2);
	$section_affiche = $row2['S_DESCRIPTION'];
	$antenne_affiche = " ".$s_description;
	$tmpS=$row2["S_ID"];
}

else {
	// cas d�partement ou plus haut dans l'organigramme
	$section_affiche = $s_description;
	$antenne_affiche = "";
	$tmpS=$section;
}

// chercher le chef ou pr�sident d�partemental
$queryy="select p.P_ID, p.P_PRENOM, p.P_NOM, g.GP_DESCRIPTION, p.P_SEXE
		from pompier p, groupe g, section_role sr
		where sr.GP_ID = g.GP_ID
		and sr.P_ID = p.P_ID
		and sr.S_ID = ".$tmpS."
		and sr.GP_ID = 101
		order by sr.GP_ID asc";

$resulty = mysql_query($queryy);
$num_resulty = mysql_num_rows($resulty);
if ( $num_resulty == 0) {
	$chef = my_ucfirst(get_prenom($id))." ".strtoupper(get_nom($id));
 	$chef_long = $chef.", de l'association ";
 	$titre_prefix = "";
 	$titre = "";
}
else {
	$data2 = mysql_fetch_array($resulty);
	if ( $data2["P_SEXE"] == 'F' ) {
		$titre = rtrim(str_replace(" (e)","e", $data2["GP_DESCRIPTION"]));
		$titre = rtrim(str_replace("(e)","e", $titre));
		$titre_prefix = "La ";
	}
	else {
		$titre = rtrim(str_replace(" (e)", "", $data2["GP_DESCRIPTION"]));
		$titre = rtrim(str_replace("(e)", "", $titre));
		$titre_prefix = "Le "; 
	}
	$chef = my_ucfirst($data2["P_PRENOM"])." ".strtoupper($data2["P_NOM"]);
	$chef_long = $chef.", ".$titre." de ";
}
if ( $data2["P_SEXE"] == 'F' ) $soussigne="soussign�e";
else $soussigne="soussign�";

if ( substr($section_affiche,0,4) == 'F�d�') $chef_long .= "la ".$section_affiche;
else if ( substr($section_affiche,0,5) == 'Prote') $chef_long .= "la ".$section_affiche;
else if ( substr($section_affiche,0,4) == 'D�l�') $chef_long .= "la ".$section_affiche;
else if ( $nbsections == 0 ) {
//	$chef_long .= $attestation_dept_name;
	$voyels = array('A','E','I','O','U','Y','H','a','e','i','o','u','y','h');
	$short2=substr($section_affiche,0,2);
	$short1=substr($section_affiche,0,1);
	$short5=substr($section_affiche,0,5);
	$last1=substr($section_affiche, -1);
	$last2=substr($section_affiche, -2);
	
/**	if ($short5 == 'Alpes' or $short5 == 'Hauts' or $short5 == 'Arden' or $last2 == 'es' or $short2 == 'Bo' or $last2 == 'or' ) $chef_long .= " des ";
	else if ( $last2 == 'et') $r = " du ";
	else if ( $short5 == 'Loire' or $short5 == 'Sarth' or $short5 == 'Somme') $chef_long .= " de la ";
	else if ( $short5 == 'Haute' or $short5 == 'Paris') $chef_long .= " de ";
	else if ( $short2 == 'Ai' ) $chef_long .= " de l'";
	else if ( $last2 == 'in' or $short5 == 'Rh�ne') $r = " du ";
	else if ( in_array($short1 , $voyels) ) $chef_long .= " de l'";
	else if ( $short5 == 'Maine' or  $short2 == 'Fi' or  $short2 == 'Pu' or $short2 == 'Pa'  or $short2 == 'Va' or  $short5 == 'Lot e' or  $short2 == 'Ta') $chef_long .= " du ";
	else if ( $short2 == 'Ma' or $short2 == 'Me' or $short2 == 'R�' or $short2 == 'C�' or $short2 == 'Ni' or $short2 == 'Cr') $chef_long .= " de la ";
	else if ( $last1 == 'e' or $last2 == 'is') $chef_long .= " de ";
	else $chef_long .= " du ";
	$chef_long .= $section_affiche;**/
	
} 
//else
 $chef_long .= $section_affiche;

$customlocal=$basedir."/images/user-specific/".$row['S_PDF_PAGE'];
$customdefault=$basedir."/images/user-specific/pdf_page.pdf";
$generic=$basedir."/fpdf/pdf_page.pdf";
$fondpdf=((file_exists($customlocal) && $row['S_PDF_PAGE']!="")?$customlocal:(file_exists($customdefault)?$customdefault:$generic));

if ( $S_URL <> "" or $S_EMAIL <> "" ) {
	if ( $S_EMAIL <> "" ) $mailinfos = "Email : ".$S_EMAIL;
	if ( $S_URL <> "" and $S_EMAIL <> "" )  $mailinfos .= " - ";
	if ( $S_URL <> "" ) $mailinfos .= "Site : ".$S_URL;
}
else $mailinfos="";

if ( $S_PHONE <> "" or $S_FAX <> "" ) {
	if ( $S_PHONE <> "" ) $phoneinfos = "T�l�phone : ".$S_PHONE;
	if ( $S_FAX <> "" and $S_PHONE <> "" )  $phoneinfos .= " - ";
	if ( $S_FAX <> "" ) $phoneinfos .= "T�l�copie : ".$S_FAX;
}
else $phoneinfos="";

$adr = $S_ADDRESS."\n".$S_ZIP_CODE." ".$S_CITY;

// On r�cup�re le type d'�v�nement

		$querydps="SELECT dimTypeDPS, TA_VALEUR, ef.dimNbISActeurs, ef.dimP1, ef.dimP2, ef.dimE1, ef.dimEquipes, ef.dimBinomes
					FROM evenement_facturation ef, evenement e, type_agrement_valeur tav
					WHERE ef.E_ID = ".$evenement."
					AND ef.dimTypeDPS = tav.TA_SHORT";
		$resultdps=mysql_query($querydps) or die ("Veuillez remplir la grille de dimensionnement DPS");
		$rowdps=mysql_fetch_array($resultdps);
		$tdps = $rowdps["TA_VALEUR"];
		$nb_public=$rowdps["dimP1"];
		$comportement_public=$rowdps["dimP2"];
		//print_r($comportement_public);
		$structure=$rowdps["dimE1"];
		$nb_equipes=$rowdps["dimEquipes"];
		$nb_binomes=$rowdps["dimBinomes"];
		$is_acteurs=$rowdps["dimNbISActeurs"];

// Cas du DPS interasso

	if ($dps_interasso == '1') {
		$dps_interasso = 'oui';
	}
	else {
		$dps_interasso = 'non';
	}

//=============================
// ordre de mission
//=============================

if ( $mode == 4 ) {
	
$pdf=new PDFEB();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetCreator($organisateur);
$pdf->SetAuthor($organisateur);
$pdf->SetDisplayMode('fullpage','single');
$pdf->SetTitle("Ordre de Mission");
$pdf->SetAutoPageBreak(0);	
$pdf->AddPage();
$pdf->SetFont('Arial','B',25); 
$pdf->SetXY(40,48);
$pdf->MultiCell(120,14,"Ordre de Mission".
"\n".$description,"1","C");
	
$pdf->SetFont('Arial','',11);
//$pdf->SetXY(25,90);
$pdf->MultiCell(180,6,"
Je ".$soussigne.", ".$chef_long.", autorise les personnes d�sign�es ci-dessous � participer � la mission suivante.","","J");			
$pdf->SetFont('Arial','',11);
//$pdf->SetXY(25,110);

// On d�termine le comportement du public en fonction de l'indice enregistr� 

switch ($comportement_public) {
	case 0.25 : 
		$comportement_public_libelle = "Public assis";
		break;
	case 0.30 : 
		$comportement_public_libelle = "Public debout : c�r�monie cultuelle, r�union publique, restauration, exposition, foire, salon, comice agricole...";
		break;
	case 0.35 : 
		$comportement_public_libelle = "Public debout : smpectacle avec public statique, f�te foraine, rendez-vous sportif avec protection du public par rapport � l'�v�nement...";
		break;
	case 0.40 :
		$comportement_public_libelle = "Public debout : spectacle avec public dynamique, danse, feria, f�te votive, carnaval, spectacle de rue, grande parade, rendez-vous sportif sans protection du public par rapport � l'�v�nement ...";
		break;	
	}

// On d�termine le type de structure en fonction de l'indice
switch ($structure) {
	case 0.25 : 
		$structure_libelle = "Structures permanentes : B�timent, salle � en dur �,...";
		break;
	case 0.30 : 
		$structure_libelle = "Structures non permanentes : gradins, tribunes, chapiteaux,...";
		break;
	case 0.35 : 
		$structure_libelle = "Espaces naturels : 2 ha < surface = 5 ha ";
		break;
	case 0.40 :
		$structure_libelle = "Espaces naturels : surface > 5 hectares";
		break;	
	}

$info_om = "<BR>Type de mission: 		<B>".$type_evenement."</B><BR>Mission:				<B>".$description."</B><BR>Lieu:				<B>".$lieu.
"</B><BR>Dates:				<B>".$periode_rdv.
"<BR>Pour le compte de:	<B>".$company.
"</B><BR>Contact sur place:	<B>".$contact."</B><BR><BR>";
$pdf->WriteHTML( $info_om );
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(180,10,"Descriptif de la manifestation","","J");
$pdf->SetFont('Arial','',11);
$descriptif_om ="Nombre de public : <B>".$nb_public.
"</B><BR>Comportement du public : <B>".$comportement_public_libelle.
"</B><BR>Type de structure : <B>".$structure_libelle.
"</B><BR>Nature du Dispositif : <B>".$tdps."</B> (<B>".$nb_is."</B> intervenants secouristes)".
"<BR>Nombre d'�quipes : <B>".$nb_equipes.
"</B> Nombre de bin�mes : <B>" .$nb_binomes.
"</B><BR>DPS Interassociatif : <B>".$dps_interasso."</B><BR>";
$pdf->WriteHTML( $descriptif_om );
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(180,10,"Repas","","J");
$pdf->SetFont('Arial','',11);
//$pdf->SetXY(25,185);
if ($repas == "oui"){
$pdf->MultiCell(180,6,"Les repas et les boissons des secouristes pr�sents seront pris en charge par l'Organisateur.","","J"); }
if ($repas =="non"){
$pdf->MultiCell(180,6,"Les repas et les boissons des secouristes pr�sents ne seront pas pris en charge par l'Organisateur.","","J"); }
//$pdf->SetXY(25,190);
if ($transport == "oui"){
$pdf->MultiCell(180,8,"L'association ".$organisateur." pourra assurer l'�vacuation des victimes vers un centre hospitalier apr�s r�gulation du SAMU.","","J"); }
if ($transport =="non"){
$pdf->MultiCell(180,6,"L'association ".$organisateur." n'assurera pas le transport des victimes vers un centre hospitalier.","","J"); }
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(180,10,"Consignes particuli�res de l'�v�nement'","","J");
$pdf->SetFont('Arial','',11);
if (!empty($consignes)){
$pdf->MultiCell(180,6,"".$consignes."","","J");
}
else{
$pdf->MultiCell(180,8,"N�ant","","J");
}
// On ajoute une page pour les autres informations
$pdf->addPage();
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(180,10,"Composition des �quipes","","J");
$pdf->SetFont('Arial','',11);
$pdf->SetAutoPageBreak(true, 30);

// On teste le nb de session, car affichage diff�rent si �v�nement en plusieurs parties
//echo $nbsessions;
if ($nbsessions==1) {

// On va s�l�ctionner les �quipiers en affichant leur �quipes 
		
		// trouver tous les participants
/*$query_participants="select distinct ep.E_CODE as EC, p.P_ID, p.P_NOM, p.P_PHONE, p.P_PRENOM, p.P_GRADE, s.S_ID, 
		p.P_HIDE, s.S_CODE,  p.C_ID
		from evenement_participation ep, pompier p, section s
        where ep.E_CODE = ".$evenement."
		and p.P_ID=ep.P_ID
		and p.P_SECTION=s.S_ID";
		//order by ep.E_CODE asc, p.P_NOM";
$result=mysql_query($query_participants) or die (mysql_error());
$nb_participants = mysql_num_rows($result);*/
//echo $nb_participants;

//on Fait un tableau pour mettre les participants 

		$header=array('Nom','Pr�nom','T�l�phone','Fonction');

		$pdf->SetFont('Arial','',10);

	 //On rajoute les participants sans �quipes
	 	$query_participants_2="SELECT DISTINCT p.P_NOM, p.P_PRENOM, p.P_PHONE, tp.TP_LIBELLE, 
		EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),p.P_BIRTHDATE))))+0 AS age
		FROM pompier p, evenement e, section s, evenement_participation ep,  type_participation tp
		WHERE ep.P_ID = p.P_ID
		AND e.E_CODE = ep.E_CODE
		AND ep.EE_ID IS NULL 
		AND e.S_ID = s.S_ID
		AND ep.TP_ID = tp.TP_ID
		AND ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")";
	$query_participants_2 .= " ORDER BY TP_LIBELLE";
	$result_participants=mysql_query($query_participants_2) or die (mysql_error()); 
	$i=0; 

//on Fait un tableau pour mettre les participants 

		$header=array('Nom','Pr�nom','T�l�phone','Fonction');
		for($i=0;$i<sizeof($header);$i++)
		$pdf->cell(40,5,$header[$i],1,0,'C',0);
				$pdf->SetFont('Arial','',10);
		$pdf->SetXY(15,$pdf->GetY()+5);
		
		while($data_array_participants = mysql_fetch_array($result_participants))
		{
		$nom_participant = strtoupper($data_array_participants['P_NOM']);
		$prenom_participant = my_ucfirst($data_array_participants['P_PRENOM']);
		$AGE=$data_array_participants["age"];
		if ($AGE < 18 ) 
		{$pdf->SetTextColor(200,0,0);}
		$pdf->cell(40,5,$nom_participant,1,0,'C',0);
		$pdf->cell(40,5,$prenom_participant,1,0,'C',0);
		$pdf->cell(40,5,$data_array_participants['P_PHONE'],1,0,'C',0);
		$pdf->cell(40,5,$data_array_participants['TP_LIBELLE'],1,0,'C',0);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(15,$pdf->GetY()+5);
		} 


	// On va chercher les �quipes 
	
	$select_equipe="SELECT DISTINCT EE_ID, EE_NAME FROM evenement_equipe WHERE E_CODE=".$evenement."";
	$query_equipe = mysql_query($select_equipe);
	//$result_equipe = mysql_fetch_array($query_equipe);
	//$result_equipe_id = $result_equipe["EE_ID"];
	//print_r($result_equipe_id);
	while ($equipe_id = mysql_fetch_array($query_equipe)) {
	//print_r($equipe_id);
		$pdf->SetXY(15,$pdf->GetY()+5);
		$pdf->MultiCell(150,5,"Intervenants secouristes de l'�quipe ".$equipe_id["EE_NAME"],"","L");
		$pdf->SetXY(15,$pdf->GetY()+3);
		for($i=0;$i<sizeof($header);$i++)
		$pdf->cell(40,5,$header[$i],1,0,'C',0);
		//$pdf->SetFont('Arial','',10);
		$pdf->SetXY(15,$pdf->GetY()+5);
		
		// On s�lectionne les �quipiers de cette �quipe
		$select_intervenant="SELECT DISTINCT p.P_NOM, p.P_PRENOM, p.P_PHONE, tp.TP_LIBELLE,
		EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),p.P_BIRTHDATE))))+0 AS age
	FROM pompier p, evenement e, evenement_participation ep, type_participation tp
	WHERE p.P_ID = ep.P_ID
	AND ep.E_CODE = e.E_CODE
	AND ep.EE_ID = ".$equipe_id["EE_ID"]."
	AND ep.TP_ID = tp.TP_ID
	AND ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")
	ORDER BY tp.TP_NUM";
	$result_liste=mysql_query($select_intervenant);
			while ($row_liste=mysql_fetch_array($result_liste)) {
    			  $P_NOM=$row_liste["P_NOM"];
    			  $P_PRENOM=$row_liste["P_PRENOM"];
    			  $P_PHONE=$row_liste["P_PHONE"];
    			  $TP_LIBELLE=$row_liste["TP_LIBELLE"];
    			  $AGE=$row_liste["age"];

	$pdf->SetFont('Arial','',11);
	//$pdf->SetTextColor(0,0,200);
	$i=0; 
		$nom_participant = strtoupper($P_NOM);
		$prenom_participant = my_ucfirst($P_PRENOM);
		if ($AGE < 18 ) 
		{$pdf->SetTextColor(200,0,0);}
		$pdf->cell(40,5,$nom_participant,1,0,'C',0);
		$pdf->cell(40,5,$prenom_participant,1,0,'C',0);
		$pdf->cell(40,5,$P_PHONE,1,0,'C',0);
		$pdf->cell(40,5,$TP_LIBELLE,1,0,'C',0);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(15,$pdf->GetY()+5);
		} 
	}//fin du 2eme while 
	
} // On ferme le if testant si il n'y a qu'une seule parties

/************* Si l'�v�nement est en plusieurs parties on s�pare les parties *********************/

else {

foreach ($EH_ID as $id_EH_ID)

{
// On va s�l�ctionner les �quipiers en affichant leur �quipes si il y a plus de 5 intervenants secouristes
		
		// trouver tous les participants
$query_participants="select distinct ep.E_CODE as EC, ep.TP_ID, p.P_ID, p.P_NOM, p.P_PHONE, p.P_PRENOM, p.P_GRADE, s.S_ID, 
		p.P_HIDE, s.S_CODE,  p.C_ID
		from evenement_participation ep, pompier p, section s
        where ep.E_CODE = ".$evenement."
        AND ep.EH_ID = ".$EH_ID[$id_EH_ID]."
		and p.P_ID=ep.P_ID
		and p.P_SECTION=s.S_ID";
		//order by ep.E_CODE asc, p.P_NOM";
$result=mysql_query($query_participants) or die (mysql_error());
$nb_participants = mysql_num_rows($result);
//echo $nb_participants;
$nb_ses=0;
		$pdf->MultiCell(35,5,"Partie : ".$id_EH_ID,"","L");
//$pdf->cell(150,10,$EH_DATE_DEBUT[$nb_ses].$EH_DATE_FIN[$nb_ses], 1,0,'C',0);
if ($nb_participants > 5) {
//on Fait un tableau pour mettre les participants 

		$header=array('Nom','Pr�nom','T�l�phone','�quipe','Fonction');
		//print_r($header);	
		//print_r($data_participants);	

	//$pdf->SetXY(15,50);
		for($i=0;$i<sizeof($header);$i++)
		$pdf->cell(35,5,$header[$i],1,0,'C',0);
		//$pdf->SetFillColor(0xdd,0xdd,0xdd);
		//$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(15,$pdf->GetY()+5);
	
// On r�cup�re la liste des participants avec fontion et �quipes

$query_liste="SELECT DISTINCT p.P_NOM, p.P_PRENOM, p.P_PHONE, tp.TP_LIBELLE, ee.EE_NAME,
EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),p.P_BIRTHDATE))))+0 AS age
FROM pompier p, evenement_participation ep, type_participation tp, evenement_equipe ee
WHERE p.P_ID = ep.P_ID
AND ep.E_CODE = ".$evenement."
AND ep.EE_ID = ee.EE_ID
AND ep.TP_ID = tp.TP_ID
AND ee.E_CODE = ".$evenement."
ORDER BY ee.EE_NAME, tp.TP_NUM";
$result_liste=mysql_query($query_liste);

	while ($row_liste=mysql_fetch_array($result_liste)) {
      $P_NOM=$row_liste["P_NOM"];
      $P_PRENOM=$row_liste["P_PRENOM"];
      $P_PHONE=$row_liste["P_PHONE"];
      $EE_NAME=$row_liste["EE_NAME"];
      $TP_LIBELLE=$row_liste["TP_LIBELLE"];
      $AGE=$row_liste["age"];
      

	$pdf->SetFont('Arial','',11);
	$pdf->SetTextColor(0,0,200);
	//$nom_prenom=""; 
	$i=0; 
	//$y=132;

		//$fond=0;
		//print_r($P_NOM);
		$nom_participant = strtoupper($P_NOM);
		//echo $nom_participant;
		$prenom_participant = my_ucfirst($P_PRENOM);
		if ($AGE < 18 ) 
		$pdf->SetTextColor(200,0,0);
		$pdf->cell(35,5,$nom_participant,1,0,'C',0);
		$pdf->cell(35,5,$prenom_participant,1,0,'C',0);
		$pdf->cell(35,5,$P_PHONE,1,0,'C',0);
		$pdf->cell(35,5,$EE_NAME,1,0,'C',0);
		$pdf->cell(35,5,$TP_LIBELLE,1,0,'C',0);
		$pdf->SetXY(15,$pdf->GetY()+5);
		//$fond=!$fond;
		} 
	//On rajoute de la place entre les deux tableaux	
	$pdf->SetXY(15,$pdf->GetY()+10);
	}

// Sinon on n'affiche la liste des secouristes sans �quipes
	else 
	{
			$query_participants_2="SELECT DISTINCT p.P_NOM, p.P_PRENOM, p.P_PHONE, tp.TP_LIBELLE,
			EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),p.P_BIRTHDATE))))+0 AS age 
		FROM pompier p, evenement e, section s, evenement_participation ep,  type_participation tp
		WHERE ep.P_ID = p.P_ID
		AND ep.EH_ID = ".$EH_ID[$id_EH_ID]."
		AND e.E_CODE = ep.E_CODE
		AND e.S_ID = s.S_ID
		AND ep.TP_ID = tp.TP_ID
		AND ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")";
	if ( $te_code == 'FOR' ) $query_participants_2 .= " and p.P_STATUT <> 'EXT' ";
	$query_participants_2 .= " ORDER BY TP_LIBELLE";
	$result_participants=mysql_query($query_participants_2) or die (mysql_error()); 
	//$pdf->SetFont('Arial','',11);
	//$pdf->SetTextColor(0,0,200);
	//$nom_prenom=""; 
	$i=0; 
	//$y=132;

//on Fait un tableau pour mettre les participants 

		$header=array('Nom','Pr�nom','T�l�phone','Fonction');
		//print_r($header);	
		//print_r($data_participants);	
		//$pdf->ImprovedTable($header,$data_array_participants);

	//$pdf->SetXY(15,50);
		for($i=0;$i<sizeof($header);$i++)
		$pdf->cell(40,5,$header[$i],1,0,'C',0);
		//$pdf->SetFillColor(0xdd,0xdd,0xdd);
		//$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(15,$pdf->GetY()+5);
		//$fond=0;
		//my_ucfirst(get_prenom($responsable))." ".strtoupper(get_nom($responsable))
		while($data_array_participants = mysql_fetch_array($result_participants))
		{
		$nom_participant = strtoupper($data_array_participants['P_NOM']);
		//echo $nom_participant;
		$prenom_participant = my_ucfirst($data_array_participants['P_PRENOM']);
		$AGE = $data_array_participants["age"];
		if ($AGE < 18 ) 
		$prenom_participant .= "-18";
		$pdf->cell(40,5,$nom_participant,1,0,'C',0);
		$pdf->cell(40,5,$prenom_participant,1,0,'C',0);
		$pdf->cell(40,5,$data_array_participants['P_PHONE'],1,0,'C',0);
		$pdf->cell(40,5,$data_array_participants['TP_LIBELLE'],1,0,'C',0);
		$pdf->SetXY(15,$pdf->GetY()+5);
		//$fond=!$fond;
		} 
	//On rajoute de la place entre les deux tableaux	
	$pdf->SetXY(15,$pdf->GetY()+10);
	}
	$nb_ses ++;
	}//fin du foreach

} // fin du else

//	$pdf->SetXY(30,300); P_NOM, p.P_PRENOM, ee.EE_NAME, tp.TP_LIBELLE
	//$pdf->MultiCell(170,6,$nom_prenom.$fonction.$renfort,"","L");
	
	$pdf->SetTextColor(0,0,0);

	//mat�riel engag�

/* On distingue le mat�riel sans �quipe du mat�riel avec �quipe */

//On rajoute le mat�riel sans �quipes
	 	$query_materiel_sans_equipe="SELECT DISTINCT em.E_CODE, s.S_ID, em.MA_ID, m.MA_ID, m.TM_ID, m.MA_MODELE, m.MA_INVENTAIRE, em.EM_NB, tm.TM_CODE, m.MA_PARENT
		FROM evenement e, evenement_materiel em, type_materiel tm, section s, materiel m
		WHERE m.MA_ID = em.MA_ID
		AND tm.TM_ID = m.TM_ID
		AND e.E_CODE = em.E_CODE
		AND e.S_ID = s.S_ID
		AND ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")
		AND em.EE_ID IS NULL
		AND m.MA_PARENT IS NULL";
	$result_materiel_sans_equipe=mysql_query($query_materiel_sans_equipe) or die (mysql_error()); 
	$nbmatos_sans_equipe=mysql_num_rows($result_materiel_sans_equipe);
	$pdf->SetFont('Arial','B',12);
	$pdf->MultiCell(180,5,"\nLe mat�riel suivant sera utilis� :","","L");
	$pdf->SetXY(15,$pdf->GetY()+3);
	$pdf->SetFont('Arial','',11);

// On affiche la liste du materiel non affect� � une �quipe
	if ( $nbmatos_sans_equipe > 0) {
	$header=array('Type','Modele','Inventaire','Nombre');
		for($i=0;$i<sizeof($header);$i++)
		$pdf->cell(40,5,$header[$i],1,0,'C',0);
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(15,$pdf->GetY()+5);
		while ($data_materiel = mysql_fetch_array($result_materiel_sans_equipe)) {
			$materiel=$data_materiel["TM_CODE"];
			$materiel_modele=$data_materiel["MA_MODELE"];
			$materiel_inventaire=$data_materiel["MA_INVENTAIRE"];
			$materiel_nombre=$data_materiel["EM_NB"];
			$pdf->cell(40,5,$materiel,1,0,'C',0);
			$pdf->cell(40,5,$materiel_modele,1,0,'C',0);
			$pdf->cell(40,5,$materiel_inventaire,1,0,'C',0);
			$pdf->cell(40,5,$materiel_nombre,1,0,'C',0);
			$pdf->SetXY(15,$pdf->GetY()+5);
				//if ( $data["E_CODE"] <> $evenement ) $materiel .= "(renfort ".$data['S_CODE'].")";
				//$pdf->MultiCell(170,7,$materiel."-".$materiel_modele."-".$materiel_inventaire."-".$materiel_nombre."","L");
		}
	}

//On rajoute de la place entre les deux tableaux	
	//$pdf->SetXY(15,$pdf->GetY()+10);

// On affiche la liste du materiel par �quipe
	
	$query_matos="SELECT DISTINCT em.E_CODE, s.S_ID, em.MA_ID, em.EE_ID, ee.EE_NAME, m.MA_ID, m.TM_ID, m.MA_MODELE, m.MA_INVENTAIRE, em.EM_NB, tm.TM_CODE, m.MA_PARENT
	FROM evenement e, evenement_materiel em, evenement_equipe ee, type_materiel tm, section s, materiel m
	WHERE m.MA_ID = em.MA_ID
	AND tm.TM_ID = m.TM_ID
	AND e.E_CODE = em.E_CODE
	AND e.S_ID = s.S_ID
	AND em.EE_ID=ee.EE_ID
	AND ee.E_CODE=".$evenement."
	AND ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")
	AND m.MA_PARENT IS NULL
	ORDER BY ee.EE_NAME";
	$result_matos=mysql_query($query_matos);
	$nbmatos=mysql_num_rows($result_matos);
	if ( $nbmatos > 0) {
	$pdf->MultiCell(180,5,"\nLe mat�riel suivant est affect� aux �quipes ci-dessous :","","L");
	$pdf->SetXY(15,$pdf->GetY()+3);
	$header=array('Type','Modele','Inventaire','Nombre','�quipe');
		for($i=0;$i<sizeof($header);$i++)
		$pdf->cell(38,5,$header[$i],1,0,'C',0);
		//$pdf->SetFillColor(0xdd,0xdd,0xdd);
		//$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(15,$pdf->GetY()+5);
		while ($data = mysql_fetch_array($result_matos)) {
				$materiel=$data["TM_CODE"];
				$materiel_modele=$data["MA_MODELE"];
				$materiel_inventaire=$data["MA_INVENTAIRE"];
				$materiel_nombre=$data["EM_NB"];
				$materiel_equipe=$data["EE_NAME"];
				$pdf->cell(38,5,$materiel,1,0,'C',0);
				$pdf->cell(38,5,$materiel_modele,1,0,'C',0);
				$pdf->cell(38,5,$materiel_inventaire,1,0,'C',0);
				$pdf->cell(38,5,$materiel_nombre,1,0,'C',0);
				$pdf->cell(38,5,$materiel_equipe,1,0,'C',0);
				$pdf->SetXY(15,$pdf->GetY()+5);
				//if ( $data["E_CODE"] <> $evenement ) $materiel .= "(renfort ".$data['S_CODE'].")";
				//$pdf->MultiCell(170,7,$materiel."-".$materiel_modele."-".$materiel_inventaire."-".$materiel_nombre."-".$materiel_equipe,"","L");
		}
	}

		
	
	// v�hicules engag�s
	$query="select distinct ev.E_CODE, s.S_CODE, v.V_ID, v.V_IMMATRICULATION, v.TV_CODE, v.V_MODELE, v.V_INDICATIF
        from evenement_vehicule ev, vehicule v, evenement e, section s
        where v.V_ID = ev.V_ID
		and e.E_CODE = ev.E_CODE
		and e.S_ID = s.S_ID
        and ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")";
	$result=mysql_query($query);
	$nbvehic=mysql_num_rows($result);
	if ( $nbvehic > 0 ) {
		$pdf->SetFont('Arial','B',12);
		$pdf->SetTextColor(0,0,0);
		$pdf->MultiCell(180,10,"Les v�hicules suivants seront utilis�s:","","L");
		$pdf->SetXY(15,$pdf->GetY()+3);
		$pdf->SetFont('Arial','',11);
		$header=array('Type','Modele','Immatriculation','Conducteur');
		for($i=0;$i<sizeof($header);$i++)
		$pdf->cell(40,5,$header[$i],1,0,'C',0);
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(15,$pdf->GetY()+5);
		while ($data = mysql_fetch_array($result)) {
			$vehicule=$data["TV_CODE"];
			$vehicule_modele = $data["V_MODELE"];
			$vehicule_immat =  $data["V_IMMATRICULATION"];
			//if ( $data["E_CODE"] <> $evenement ) $vehicule .= "(renfort ".$data['S_CODE'].")";

			//On va chercher le conducteur du vehicule
			$query_conducteur="SELECT p.P_NOM, p.P_PRENOM FROM pompier p, evenement_vehicule ev WHERE p.P_ID=ev.COND_ID AND ev.V_ID=".$data["V_ID"]." AND ev.E_CODE=".$evenement."";
			$result_conducteur=mysql_query($query_conducteur);
			$data_conducteur = mysql_fetch_array($result_conducteur);
			$conducteur = "".$data_conducteur["P_NOM"]." ".$data_conducteur["P_PRENOM"];
			$pdf->cell(40,5,$vehicule,1,0,'C',0);
			$pdf->cell(40,5,$vehicule_modele,1,0,'C',0);
			$pdf->cell(40,5,$vehicule_immat,1,0,'C',0);
			$pdf->cell(40,5,$conducteur,1,0,'C',0);
			$pdf->SetXY(15,$pdf->GetY()+5);
		}
	}
	$pdf->SetTextColor(0,0,0);
	

	$pdf->Multicell(180,6,"\nTenue secouriste et badge obligatoires
\nL'absence d'un secouriste compromet l'ensemble du dispositif.
\nSi malgr� votre engagement vous deviez pour une raison importante vous d�sister, nous vous invitons � trouver un rempla�ant et � pr�venir l'association et le responsable du dispositif ","","L");
	
	$pdf->SetFont('Arial','',12);
	$pdf->MultiCell(200,8,"\n Fait le ".date('d-m-Y')." ".$organisateur_city,"","C");
	$pdf->MultiCell(100,8,$titre_prefix." ".$titre.", ".$chef,"","L");
	
	$pdf->SetXY(10,245);
	$pdf->SetFont('Arial','',6);
	$pdf->Output();
}

//=============================
// Convention
//=============================

if ( $mode == 6 ) {

// On r�cup�re le montant du devis dans la table evenement_facturation

$select_devis = "SELECT devis_montant, E_ID FROM evenement_facturation WHERE E_ID=".$evenement."";
$query_devis = mysql_query($select_devis);
while ($data_devis=mysql_fetch_array($query_devis)) {
$montant_devis = $data_devis['devis_montant'];
}


$pdf=new PDFEB();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetDisplayMode('fullpage','single');
$pdf->SetTitle("Convention");
$pdf->SetAutoPageBreak(0);	
$pdf->AddPage();
$pdf->SetFont('Arial','',11);
$pdf->SetXY(15,67);
$pdf->MultiCell(180,6, "".$company."".
"\n ".$company_address."".
"\n ".$company_cp."  ". "".$company_city."","","R");
$pdf->MultiCell(180,6,"Objet : Dispositif secouriste ".$description." \n".
"\n Affaire suivie par : ".$responsable."".
"\n T�l. ".$responsable_phone_affichage_propre." ".
"\n Email : ".$responsable_email."","","J");			
$pdf->MultiCell(180,10,"".$organisateur_city." le ".date('d-m-Y')."","","R");
$pdf->MultiCell(180,10,"Madame, Monsieur,".
"\n Suite � votre demande de mise en place d'un dispositif pr�ventif de secours, vous trouverez ci-joint :","","J");
$pdf->MultiCell(180,8,"Deux exemplaires de la convention pr�cisant les modalit�s de notre accord. Vous voudrez bien les compl�ter et nous retourner un exemplaire sign�.","","J");
$pdf->MultiCell(180,8,"Dans l'attente, veuillez, Madame, Monsieur, accepter nos salutations les meilleures.".
"\n ".
"\n Le pr�sident".
"\n ".
"\n ".
"\n ".$chef."","","J"); 
$pdf->AddPage();
$pdf->SetFont('Arial','B',14); 
$pdf->MultiCell(180,10,"CONVENTION".
"\n Pour la mise en place d'un Dispositif Pr�visionnel de Secours \n","1","C");
$pdf->MultiCell(180,10,"".$description."\n le ".$EH_DATE_DEBUT[1]."","1","C");
$pdf->MultiCell(180,10," ","","C");
$pdf->MultiCell(180,10,"1. Association Prestataire","1","C");
$pdf->SetFont('Arial','',11);
$pdf->MultiCell(180,8," ".$organisateur."".
"\n Adresse : ".$S_ADDRESS."".
"\n Code Postal : ".$S_ZIP_CODE."".
"\n Commune : ".$S_CITY."".
"\n T�l�phone : ".$S_PHONE."".
"\n Courriel : ".$S_EMAIL."".	
"\n Ci-apr�s d�sign�e ".$organisateur."".
"\n Repr�sent� par (Pr�nom, Nom, Qualit�) : ".$chef.", pr�sident".
"\n ","","J");
$pdf->SetFont('Arial','B',14); 
$pdf->MultiCell(180,10,"2. Organisateur de l'�v�nement","1","C");
$pdf->SetFont('Arial','',11);
$pdf->MultiCell(180,8,"Raison sociale de l'Organisateur : ".$company."".
"\n Adresse : ".$company_address."".
"\n Code Postal : ".$company_cp."".
"\n Commune : ".$company_city."".
"\n T�l�phone : ".$company_phone."".
"\n Courriel : ".$company_email."".	
"\n Ci-apr�s d�sign�e l'Organisateur".
"\n Repr�sent� par (Pr�nom, Nom, Qualit�) : ".$company_representant."","","J");

$pdf->AddPage();
$pdf->SetFont('Arial','B',14); 
$pdf->MultiCell(180,10,"3. Objet de la convention","1","C");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"3.1 Objet","","J");
$pdf->SetFont('Arial','',11);
$pdf->MultiCell(180,8,"Mise en place d'un Dispositif Pr�ventif de Secours pour","","J");
if ($is_acteurs > 0) { 
$pdf->MultiCell(180,5,"\n - Les acteurs de la manifestation (joueurs, comp�titeurs, com�diens, ...)","","J"); }
$pdf->MultiCell(180,5,"\n - Le public","","J");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"3.2 Descriptif de l'�v�nement","","J");
$pdf->SetFont('Arial','',11);
$evenement_description = " Nom de l'�v�nement : <B>".$description."</B> <BR> Date : <B>".$periode."</B> <BR> Lieu : <B>".$lieu."</B><BR>";
$pdf->WriteHTML( $evenement_description ) ;
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"3.3 Grille d'�valuation des risques","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"Cet �v�nement a fait l'objet par l'Organisateur d'une �valuation des risques dont la grille figure en annexe de la pr�sente convention.","","J");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"3.4 Autorisations","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"L'Organisateur reconnait poss�der toutes les autorisations n�cessaires au d�roulement de la dite manifestation et avoir souscrit une assurance responsabilit� civile Organisateur.","","J");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"3.5 Responsabilit�s","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"Conform�ment aux textes r�glementaires, l'Organisateur est responsable de l'ensemble de l'organisation et des mesures prises en liaison avec l'autorit� de police comp�tente (maire, pr�fet).".
"\nLa mise en place d'un dispositif de secours ne peut avoir pour cons�quence un transfert de responsabilit� vers l'association ".$organisateur."","","J");
$pdf->AddPage();
$pdf->SetFont('Arial','B',14); 
$pdf->MultiCell(180,10,"4. Prestations fournies par ".$organisateur."","1","C");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"4.1 Type du dispositif mis en place ","","J");
$pdf->SetFont('Arial','',11); 

$pdf->MultiCell(180,8,"".$type_evenement." : ".$tdps."","","J");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"4.2 : Composition du dispositif ","","J");
$pdf->SetFont('Arial','',11); 
$evenement_intervenants = "Nombre d'intervenants secouriste : <B>".$nb_is."</B>".
"<BR>V�hicule(s) de Premiers Secours � Personnes : <B>".$nb_vpsp."</B>".
"<BR>Autre(s) v�hicule(s) : <B>".$nb_autres_vehicules."</B><BR>";
$pdf->WriteHTML( $evenement_intervenants ) ;
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"4.3 Missions ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,6,"Les moyens mis en place par l'association ".$organisateur." sont destin�s � assurer une pr�sence pr�ventive pendant la manifestation objet de cette convention :","","J");
$pdf->SetFont('Arial','B',11); 
$pdf->MultiCell(180,6,"Dans le cas d'un Point d'alertes et de premiers secours (PAPS):","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,6,"- Reconna�tre et analyser la situation accidentelle".
"\n - Prendre les premi�res mesures adapt�es de s�curit� et de protection".
"\n - Alerter les secours publics".
"\n - Prodiguer � la victime des gestes de premier secours r�alisables � 2 intervenants".
"\n - Accueillir les secours et faciliter leur intervention", "", "J");
$pdf->SetFont('Arial','B',11); 
$pdf->MultiCell(180,6,"Dans le cas d'un Dispositif Pr�visionnel de Secours : Poste de secours ", "", "J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,6,"- Reconna�tre et analyser la situation accidentelle".
"\n - Prendre les premi�res mesures adapt�es de s�curit� et de protection".
"\n - Faire un bilan et porter les premiers secours n�cessaires � une victime".
"\n - Prodiguer des conseils adapt�s � une victime qui pourrait partir par ses propres moyens".
"\n - Contribuer � la mise en place de la cha�ne des secours allant de l'alerte jusqu'� la prise en charge de la victime par les pouvoirs publics".
"\n - Accueillir les secours et faciliter leur intervention", "","J");
$pdf->SetFont('Arial','B',11); 
$pdf->SetX(20);
$pdf->MultiCell(180,6,"Une �quipe de secours peut prendre en charge : ", "", "J");
$pdf->SetX(20);
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,6,"- Une seule victime atteinte d'une d�tresse vitale".
"\n- Un nombre de victimes sans gravit�s, �quivalent � celui des intervenants qui la composent","","J");

$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"4.4 Transport des victimes ","","J");
$pdf->SetFont('Arial','',11); 
if ($transport == "oui"){
$pdf->MultiCell(180,8,"L'association ".$organisateur." pourra assurer l'�vacuation des victimes vers un centre hospitalier apr�s r�gulation du SAMU.","","J"); }
if ($transport =="non"){
$pdf->MultiCell(180,8,"L'association ".$organisateur." n'assurera pas le transport des victimes vers un centre hospitalier.","","J"); }

$pdf->AddPage();
$pdf->SetFont('Arial','B',14); 
$pdf->MultiCell(180,10,"5. Engagements de l'Organisateur","1","C");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"5.1 Aspects logistique ","","J");
$pdf->MultiCell(180,10,"5.1.1 Locaux, mat�riels, moyens de communication ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"L'Organisateur s'engage � mettre � la disposition des �quipes de secours, afin que celles ci puissent travailler dans des conditions optimales�:","","J");
$moyens_instal = "<B>- ".$moyen_installation_1."</B><BR>";
$pdf->WriteHTML( $moyens_instal );
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"5.1.2 Dispositf d'alerte des secours publics ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"L'Organisateur s'engage � mettre � la disposition des �quipes de secours, un moyen d'appel des secours publics","","J");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"5.1.3 Conditions de vie ","","J");
$pdf->SetFont('Arial','',11); 
if ($repas == "oui"){
$pdf->MultiCell(180,8,"Les repas et les boissons des secouristes pr�sents seront pris en charge par l'Organisateur.","","J"); }
if ($repas =="non"){
$pdf->MultiCell(180,8,"Les repas et les boissons des secouristes pr�sents ne seront pas pris en charge par l'Organisateur.","","J"); }
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"5.2 Modalit�s op�rationnelles ","","J");
$pdf->MultiCell(180,10,"5.2.1 Correspondant de l'organisateur ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"".$contact." membre de l'Organisateur, est d�sign� comme interlocuteur de l'association ".$organisateur." le jour de la manifestation.","","J");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"5.2.2 Cha�ne de commandement du DPS ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"Le commandement du dispositif sera assur� par�l'association ".$organisateur."","","J");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"5.2.3 Cas particulier d'un DPS Inter associatif ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"D'un commun accord entre les associations participantes aux dispositifs son commandement sera assur� par�l'association ".$organisateur."","","J");

$pdf->AddPage();
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"5.3 Modalit�s financi�res ","","J");
$pdf->MultiCell(180,10,"5.3.1 Montant de la participation ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"L'intervention des secouristes demeure b�n�vole et l'action de l'association ".$organisateur." est � but non lucratif. ".
"\nToutefois, l'Organisateur d�dommage l'association des frais engendr�s (d�placements, mat�riel, oxyg�ne, produits pharmaceutiques...), estim�s � ".$montant_devis." euros.","","J");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"5.3.2 Participation financi�re en cas d'annulation ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"En cas d'annulation de l'�v�nement l'Organisateur d�dommage l'association des frais administratifs engendr�s, estim�s � ".$frais_annulation." euros. ","","J");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"5.3.3 Conditions de paiement ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"Cette somme sera r�gl�e par ch�que libell� � l'ordre de�:".$organisateur."","","J");	
$pdf->MultiCell(180,8," ","","J");	
$pdf->SetFont('Arial','B',14); 
$pdf->MultiCell(180,10,"6. Engagement des deux parties","1","C");
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"6.1 Dur�e de la convention ","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"Cette convention est sign�e pour la dur�e de l'�v�nement objet de la pr�sente.","","J");	
$pdf->SetFont('Arial','B',12); 
$pdf->MultiCell(180,10,"6.2 Condition de r�alisation","","J");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"L'engagement de l'association ".$organisateur." est li�:".
"\n- � l'acceptation de la pr�sente convention par l'organisateur ".
"\n- � l'autorisation de l'�v�nement par les pouvoirs publics","","J");

$pdf->MultiCell(180,8," ","","J");	
$pdf->SetFont('Arial','B',14); 
$pdf->MultiCell(180,10,"7. Grille d'�valution des risques","1","C");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"Cette grille remplie sous la responsabilit� de l'Organisateur figure en annexe de la pr�sente convention","","J");	

$pdf->AddPage();
$pdf->SetFont('Arial','B',14); 
$pdf->MultiCell(180,10,"8. Clauses particuli�res","1","C");
$pdf->SetFont('Arial','',11); 
if (empty($clause_particuliere_1)){
$pdf->MultiCell(180,8,"N�ant","","J");
};
if (!empty($clause_particuliere_1)){
$pdf->MultiCell(180,8,"".$clause_particuliere_1."".
"\n ".$clause_particuliere_2."","","J");
};
$pdf->MultiCell(180,8," ","","J");
$pdf->SetFont('Arial','B',14); 
$pdf->MultiCell(180,10,"9. Litiges","1","C");
$pdf->SetFont('Arial','',11); 
$pdf->MultiCell(180,8,"Toute contestation n�e de l'interpr�tation ou de l'ex�cution de la pr�sente convention devra trouver un r�glement amiable.
Si une contestation ou un diff�rend n'ont pu �tre r�gl�s � l'amiable, le tribunal de ".$organisateur_city." sera seul comp�tent pour r�gler le litige.","","J");

$pdf->MultiCell(180,8,"\n \nConvention �tablie en double exemplaire � ".$organisateur_city.", le ".date('d-m-Y')."","","J");
$pdf->MultiCell(100,8,"Pour l'Organisateur".
"\n (Cachet, nom et pr�nom,fonction du signataire)".
"\n  ".
"\n  ".
"\n  ","","J");
$pdf->MultiCell(100,8,"Pour l'association ".$organisateur."".
"\n Le pr�sident ".$chef."".
"\n  ".
"\n  ".
"\n  ","","J");
$pdf->Output();
}

//=============================
// attestations de formation
//=============================

if ( $mode == 2 ) {

$pdf= new PDFEB();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetCreator($organisateur);
$pdf->SetAuthor($organisateur);
$pdf->SetDisplayMode('fullpage','single');
$pdf->SetTitle("Liste Stagiaire formation");
$pdf->SetAutoPageBreak(0);

// liste des stagiaires
$query="SELECT distinct p.P_ID, p.P_SEXE, p.P_NOM, p.P_PRENOM, date_format(p.P_BIRTHDATE, '%d-%m-%Y') P_BIRTHDATE, p.P_BIRTHPLACE  
		 from evenement_participation ep, pompier p, evenement e
		 where ep.P_ID = p.P_ID
		 and e.E_CODE = ep.E_CODE
		 and ep.TP_ID = 0 
		 and ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")";

if ( $pid > 0 ) $query .= " and p.P_ID = ".$pid;

$query .= " order by p.P_NOM, p.P_PRENOM";
$result = mysql_query($query);

while ($data = mysql_fetch_array($result)) { 
    $expcomplement="";
	$nom_prenom=my_ucfirst($data['P_PRENOM'])." ".strtoupper($data['P_NOM']);
	$date_nai=$data['P_BIRTHDATE'];
	$lieu_nai=$data['P_BIRTHPLACE'];
	$P_ID=$data['P_ID'];
	if ( $date_nai <> ""  or $lieu_nai <> "" ) {
		if ( $data['P_SEXE'] == 'M' ) $birthinfo="N�";
		else $birthinfo="N�e";
		if ( $date_nai <> "" ) $birthinfo .= " le ".$date_nai;
		if ( $lieu_nai <> "" ) $birthinfo .= " � ".$lieu_nai;
	}
	else $birthinfo="";	

	$type_formation="une formation";
	if ( $TF_CODE == 'P' ) $type_formation = "un ".$TF_LIBELLE;	
	elseif ( $TF_CODE == 'I' ) $type_formation = "une formation initiale";
	elseif ( $TF_CODE <> "" ) $type_formation = "une ".$TF_LIBELLE;
	
	$query2="select PF_ADMIS, PF_DIPLOME, DATE_FORMAT(PF_DATE, '%Y') PF_DATE , DATE_FORMAT(PF_EXPIRATION, '%d/%m/%Y') PF_EXPIRATION
			 from personnel_formation where P_ID=".$P_ID." and E_CODE=".$evenement;
	$result2 = mysql_query($query2);
	$row2=mysql_fetch_array($result2);
	$PF_ADMIS=$row2['PF_ADMIS'];
	$PF_DIPLOME=$row2['PF_DIPLOME'];
	$PF_EXPIRATION=$row2['PF_EXPIRATION'];
	$PF_DATE=intval($row2['PF_DATE']);
	if ( $PF_EXPIRATION <> '' ) $expcomplement=" jusqu'au ".$PF_EXPIRATION.",";
	else if ( $PF_DATE <> '' ) {
		$n2 = $PF_DATE + 1;
		$expcomplement=" pour l'ann�e ".$n2.",";
	}
	
	$diplome="";
	if ( $PF_ADMIS == 1 ) {
	    if ( $type == 'PSC1')  $reussite="A suivi avec succ�s ".$type_formation;
		else $reussite="A fait l'objet d'un bilan favorable suite � ".$type_formation;
		if ( $PF_DIPLOME <> "" )  $diplome = " et a obtenu le dipl�me n�".$PF_DIPLOME;
	}
	else $reussite="A particip� � ".$type_formation;
	
	if ( substr($description,0,21) == "Pr�vention et Secours") $fonction = "secouriste qualifi� ";
	else $fonction = "";
	
	$complement  = $attestation_complement1;
	$complement .= " ".$nom_prenom;
	$complement .= " ".$attestation_complement2;
	$complement .= " \"".$fonction.$description."\"";
	$n2=date('Y')+1;
	$complement .= $expcomplement;
	if ( $secourisme ) $complement .= " ".$attestation_complement3;
	else $complement .= ".";
	
	$pdf->AddPage();
	$pdf->SetTextColor(0,0,0);
	if ( $secourisme ) {
		$pdf->SetXY(33,52);      
		$pdf->SetFont('Times','',8);
		$pdf->MultiCell(180,1.5,$attestation_arretes,"","L");
	}
	$pdf->SetFont('Arial','B',36); 
	$pdf->SetXY(55,80);
	$pdf->MultiCell(100,18,"ATTESTATION","1","C");
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(25,105);
	$pdf->MultiCell(160,8,"Je soussign�, ".$chef_long.", atteste que:","","J");			
	$pdf->SetFont('Arial','B',24);
	$pdf->SetXY(15,127);
	$pdf->MultiCell(180,10,$nom_prenom,"","C");
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(15,135);
	$pdf->MultiCell(180,10,$birthinfo,"","C");
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(25,156);
	$pdf->MultiCell(160,8, $reussite." \"".$description."\", ".$periode." � ".$lieu.$diplome.".","","J");	
	if ( $PF_ADMIS == 1 and $type  <> 'PSC1' ) {
	   $pdf->SetXY(25,185);
	   $pdf->MultiCell(160,8,$complement);
	}
	$pdf->SetXY(25,225);
	$pdf->SetFont('Arial','',12);
	$pdf->MultiCell(200,8,"Fait le ".date('d-m-Y')." ".$organisateur_city,"","J");
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(100,240);
	$pdf->MultiCell(100,8,$titre_prefix." ".$titre.", ".$chef,"","L");
	
	$pdf->SetXY(10,265);
	$pdf->SetFont('Arial','',6);
	$pdf->MultiCell(100,5,$printed_by,"","L");
};
$pdf->SetDisplayMode('fullpage','single');
$pdf->Output();
}

//=============================
// Fiche �valuation formation
//=============================

else if ( $mode == 3 ) {

class PDF_Ellipse extends PDFEB
{

	function Circle($x, $y, $r, $style='D')
	{
		$this->Ellipse($x,$y,$r,$r,$style);
	}

	function Ellipse($x, $y, $rx, $ry, $style='D')
	{
		if($style=='F')
			$op='f';
		elseif($style=='FD' || $style=='DF')
			$op='B';
		else
			$op='S';
		$lx=4/3*(M_SQRT2-1)*$rx;
		$ly=4/3*(M_SQRT2-1)*$ry;
		$k=$this->k;
		$h=$this->h;
		$this->_out(sprintf('%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
			($x+$rx)*$k,($h-$y)*$k,
			($x+$rx)*$k,($h-($y-$ly))*$k,
			($x+$lx)*$k,($h-($y-$ry))*$k,
			$x*$k,($h-($y-$ry))*$k));
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
			($x-$lx)*$k,($h-($y-$ry))*$k,
			($x-$rx)*$k,($h-($y-$ly))*$k,
			($x-$rx)*$k,($h-$y)*$k));
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
			($x-$rx)*$k,($h-($y+$ly))*$k,
			($x-$lx)*$k,($h-($y+$ry))*$k,
			$x*$k,($h-($y+$ry))*$k));
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c %s',
			($x+$lx)*$k,($h-($y+$ry))*$k,
			($x+$rx)*$k,($h-($y+$ly))*$k,
			($x+$rx)*$k,($h-$y)*$k,
			$op));
	}
}
$pdf=new PDF_Ellipse();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetCreator($organisateur);
$pdf->SetAuthor($organisateur);
$pdf->SetDisplayMode('fullpage','single');
$pdf->SetTitle("Evaluation formation");
$pdf->SetAutoPageBreak(0);		
$pdf->AddPage();
$pdf->SetFont('Arial','B',13);
$libelle=$description." ".$periode;
if ( $lieu <> "" and strlen($libelle) < 60) $libelle .= " (".substr($lieu,0, 75 - strlen($libelle) ).".)";
$pdf->Text(5,50,$libelle);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',9);
$pdf->SetXY(5,54);
$pdf->Write(5,"Nom du stagiaire: ............... ");
$pdf->SetDrawColor(0,0,0);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','B',14);
$pdf->SetXY(10,200);
$pdf->Write(5,"- Quelle est votre satisfaction globale vis � vis du stage ?");
$pdf->SetXY(10,215);
$pdf->Write(5,"- Quels sont les points positifs de cette formation ?");
$pdf->SetXY(10,230);
$pdf->Write(5,"- Quels sont les points n�gatifs de cette formation ?");
$pdf->SetXY(10,245);
if ( $type == 'PSC1' ) 
$pdf->Write(5,"- Etes-vous pr�t � r�aliser une activit� de citoyen de s�curit� civile ? Justifier.");
else
$pdf->Write(5,"- Autres commentaires.");					
$pdf->Circle(105,120,14,'D');
$pdf->Circle(105,120,28,'D');
$pdf->Circle(105,120,42,'D');
$pdf->Circle(105,120,56,'D');
$pdf->Line(49,120, 160.9,120);
$pdf->Line(105,64, 105,176);
$pdf->Line(65.4,80.4, 145,159.6);
$pdf->Line(65.4,159.6, 145,80.4);
$pdf->SetXY(105-48,120-18.8);
$pdf->Write(5,"1");
$pdf->SetXY(105+44,120-19);
$pdf->Write(5,"1");
$pdf->SetXY(105-48,120+18.8);
$pdf->Write(5,"1");
$pdf->SetXY(105+42,120+18);
$pdf->Write(5,"1");
$pdf->SetXY(105-18.8,120-48);
$pdf->Write(5,"1");
$pdf->SetXY(105+15.8,120-48);
$pdf->Write(5,"1");
$pdf->SetXY(105-18.8,120+44);
$pdf->Write(5,"1");
$pdf->SetXY(105+18.8,120+42.5);
$pdf->Write(5,"1");
$pdf->SetXY(105-33.6,120-13.4);
$pdf->Write(5,"2");
$pdf->SetXY(105+30.4,120-13.4);
$pdf->Write(5,"2");
$pdf->SetXY(105-33.6,120+13);
$pdf->Write(5,"2");
$pdf->SetXY(105+30.4,120+13);
$pdf->Write(5,"2");
$pdf->SetXY(105-14,120-34);
$pdf->Write(5,"2");
$pdf->SetXY(105+13.4,120-33.5);
$pdf->Write(5,"2");
$pdf->SetXY(105-13.4,120+30.5);
$pdf->Write(5,"2");
$pdf->SetXY(105+12.5,120+30.5);
$pdf->Write(5,"2");
$pdf->SetXY(105-20.3,120-8.0);
$pdf->Write(5,"3");
$pdf->SetXY(105+18.5,120-8.0);
$pdf->Write(5,"3");
$pdf->SetXY(105-20.3,120+8.0);
$pdf->Write(5,"3");
$pdf->SetXY(105+18.5,120+8.0);
$pdf->Write(5,"3");
$pdf->SetXY(105-9.9,120-19.9);
$pdf->Write(5,"3");
$pdf->SetXY(105+6.8,120-19.9);
$pdf->Write(5,"3");
$pdf->SetXY(105-9.9,120+18.8);
$pdf->Write(5,"3");
$pdf->SetXY(105+6.8,120+18.8);
$pdf->Write(5,"3");
$pdf->SetXY(105-10.4,120-6);
$pdf->Write(5,"4");
$pdf->SetXY(105+6.4,120-6);
$pdf->Write(5,"4");
$pdf->SetXY(105-10.4,120+2.5);
$pdf->Write(5,"4");
$pdf->SetXY(105+6.4,120+2.5);
$pdf->Write(5,"4");
$pdf->SetXY(105-6.7,120-11);
$pdf->Write(5,"4");
$pdf->SetXY(105+1.8,120-11);
$pdf->Write(5,"4");
$pdf->SetXY(105-5.7,120+6.4);
$pdf->Write(5,"4");
$pdf->SetXY(105+1.8,120+6.4);
$pdf->Write(5,"4");
$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(136.5,57);
$pdf->MultiCell(55,6,"Pertinence des m�thodes p�dagogiques","1","C");
$pdf->SetXY(159.5,80);
$pdf->MultiCell(47,6,"Conditions d�emploi
et qualit� des outils p�dagogiques","1","C");
$pdf->SetXY(163.5,128);
$pdf->MultiCell(42,6,"Niveau d�acquisition des savoirs","1","C");
$pdf->SetXY(124.5,175);
$pdf->MultiCell(30,6,"Niveau de la logistique","1","C");
$pdf->SetXY(39.5,168);
$pdf->MultiCell(30,6,"Int�r�t des contenus","1","C");
$pdf->SetXY(19.5,138);
$pdf->MultiCell(28,6,"Qualit� des formateurs","1","C");
$pdf->SetXY(26,80);
$pdf->MultiCell(27,6,"Clart� des objectifs","1","C");
$pdf->SetXY(42,62);
$pdf->MultiCell(30,6,"Qualit� de l�organisation","1","C");
$pdf->SetFillColor(192,192,192);
$pdf->SetDrawColor(0,0,0);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY(153,153);
$pdf->MultiCell(52,5,"Veuillez hachurer les cases
qui correspondent � votre
appr�ciation, s�il vous plait !
Merci de votre collaboration !","1","C","True");
$pdf->SetXY(3,105);
$pdf->MultiCell(42,5,"1 = Pas du tout satisfait
2 = Peu satisfait
3 = Satisfait
4 = Tr�s satisfait","1","L","True");

$pdf->SetXY(10,265);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell(100,5,$printed_by,"","L");

$pdf->Output();

}
//=============================
// fiche de pr�sence
//=============================

else if ( $mode == 1 ) {

if ( $responsable <> "" ) 
	$responsable = my_ucfirst(get_prenom($responsable))." ".strtoupper(get_nom($responsable));
if ( is_file('images/user-specific/logo.jpg'))
 	$logo='images/user-specific/logo.jpg';
else 
 	$logo='images/logo.jpg';

$i=0;
if ( $nbsessions > 1 ) $nb_cours=$nbsessions;
else if ($EH_DATE_DEBUT[1] == $EH_DATE_FIN[1] ) $nb_cours=2;
else $nb_cours = intval(max(min($EH_DUREE[1]/3, 6),2)) ; // 6 colonnes � signer
$largeur = 155 / $nb_cours;
$hauteur=11;
 	
$pdf= new FPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetCreator("$cisname - $organisateur");
$pdf->SetAuthor("$cisname");
$pdf->SetDisplayMode('fullpage','single');
$pdf->SetTitle("Fiche de pr�sence");
$pdf->SetAutoPageBreak(0);
$pdf->AliasNbPages();

// liste des moniteurs
$query="SELECT distinct p.P_ID, p.P_NOM, p.P_PRENOM, tp.TP_LIBELLE, tp.INSTRUCTOR
		 from evenement_participation ep, pompier p, type_participation tp, evenement e
		 where ep.P_ID = p.P_ID
		 and e.E_CODE = ep.E_CODE
		 and ep.TP_ID > 0 
		 and ep.TP_ID = tp.TP_ID
		 and tp.INSTRUCTOR = 1
		 and ( e.E_CODE=".$evenement."  or e.E_PARENT=".$evenement.")
		 order by p.P_NOM, p.P_PRENOM asc";
$result = mysql_query($query);
$moniteurs="";
while ($data = mysql_fetch_array($result)) {
	if ( $moniteurs <> "" ) $moniteurs .= ", ";
	$moniteurs .= my_ucfirst($data['P_PRENOM'])." ".strtoupper($data['P_NOM'])." (".$data['TP_LIBELLE'].")";
}

// liste des stagiaires
$query="SELECT distinct e.E_CODE, s.S_CODE, p.P_ID, p.P_NOM, p.P_PRENOM, date_format(p.P_BIRTHDATE, '%d-%m-%Y') P_BIRTHDATE  
		 from evenement_participation ep, pompier p, evenement e, section s
		 where ep.P_ID = p.P_ID
		 and s.S_ID = e.S_ID
		 and e.E_CODE = ep.E_CODE";
if ( $te_code <> 'REU' ) $query .= " and ep.TP_ID = 0 ";
$query .= " and ( e.E_CODE=".$evenement." or e.E_PARENT=".$evenement.")
		 order by p.P_NOM, p.P_PRENOM asc";
$result = mysql_query($query);
$nbstagiaires=mysql_num_rows($result);

if ( $nbstagiaires == 0 ) {
	$empty=true;
	$query="select null as P_ID, ' ' as P_NOM, ' ' as p_PRENOM, null as P_BIRTHDATE";
	$result = mysql_query($query);
}
else $empty=false;

if ( $te_code == 'REU' ) $value='R�union';
else $value="Formation";

while ($data = mysql_fetch_array($result)) {
	if ( $i % 12 == 0) { // nouvelle page
		$y=54;	
		$pdf->AddPage();
		$pdf->Image($logo,5,5);
		$pdf->SetTextColor(13,53,148);
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(60,10);	
		$pdf->MultiCell(120,6,$section_affiche."\n".$antenne_affiche,0,"L",0);
		$pdf->Text(60,30,"$value: ".$description);
		$pdf->SetXY(0,10);			
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(0,4,$adr."\n".$phoneinfos."\n".$mailinfos,0,"R",0);
		$pdf->SetFont('Arial','B',12);

		$pdf->Text(10,42,"Date:");
		$pdf->Text(10,47,"Lieu:");
		$pdf->Text(130,42,"Responsable administratif:");
		if ( $te_code <> 'REU' ) $pdf->Text(130,47,"Moniteurs:");
		$pdf->SetFont('Arial','I',11);
		$pdf->Text(25,42,$periode);
		$pdf->Text(25,47,$lieu);
		$pdf->Text(190,42,$responsable);
		//$pdf->Text(150,47,$moniteurs);
		$pdf->SetXY(155,43.5);
		if ( $te_code <> 'REU' ) $pdf->MultiCell(150,5,$moniteurs,0,"L",0);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(200);
		$pdf->SetXY(10,$y);
		$pdf->SetFont('Arial','B',16);
		$pdf->MultiCell(75,$hauteur,"Nom Pr�nom",1,"L",true);
		$pdf->SetFont('Arial','B',10);	
		$pdf->SetXY(85,$y);
		$pdf->MultiCell(35,$hauteur,"Date de naissance",1,"C",true);
		if ($nbsessions > 6) $pdf->SetFont('Arial','B',8);
		for($k=1; $k != $nb_cours+1; $k++) { 
			$pos=120+($k-1)*$largeur;
			$pdf->SetXY($pos,$y);
			if ( $k == 1 or $nbsessions > 1) {
			 	if ( $EH_DATE_DEBUT[$k] == $EH_DATE_FIN[$k]) $day=$EH_DATE_DEBUT[$k];
			 	else $day=$EH_DATE_DEBUT[$k];	
			}
			else $day=".......";
			$pdf->MultiCell($largeur,$hauteur,"$day",1,"C",true);
		};
  	}
	$i=$i+1;$y=$y+$hauteur;
	if (! $empty){
		$n=strtoupper($data['P_NOM'])." ".my_ucfirst($data['P_PRENOM']);
		if ( $data['E_CODE'] <> $evenement ) $n .= " - ".$data['S_CODE'];
		$nom_prenom=substr($n,0,28);
		if (strlen($n) > 28 )  $nom_prenom .= ".";
		$date_nai=$data['P_BIRTHDATE'];
	}
	else {
		$nom_prenom="";
		$date_nai="";
	}
	$pdf->SetXY(10,$y);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetFillColor(240);
	$pdf->MultiCell(75,$hauteur,"$nom_prenom",1,"L",true);
	$pdf->SetFont('Arial','B',10);	
	$pdf->SetXY(85,$y);
	$pdf->MultiCell(35,$hauteur,"$date_nai",1,"C",true);
	for($k=1; $k != $nb_cours+1; $k++) { 
		$pos=120+($k-1)*$largeur;
		$pdf->SetXY($pos,$y);
		$pdf->MultiCell($largeur,$hauteur,"",1,"C");
	};
}
$y=min(196,$y+20);
$pdf->SetXY(120,$y);
$pdf->MultiCell(60,10,"Signatures des responsables:",0,"L");
$pdf->SetXY(10,200);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell(100,5,$printed_by,"","L");
$pdf->Output();	
}

//=============================
// proc�s verbal
//=============================

else if ( $mode == 5 ) {

if ( $responsable <> "" ) 
	$responsable = my_ucfirst(get_prenom($responsable))." ".strtoupper(get_nom($responsable));
if ( is_file('images/user-specific/logo.jpg'))
 	$logo='images/user-specific/logo.jpg';
else 
 	$logo='images/logo.jpg';

$i=0;
$hauteur=9;
 	
$pdf= new FPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetCreator("$cisname - $organisateur");
$pdf->SetAuthor("$cisname");
$pdf->SetDisplayMode('fullpage','single');
$pdf->SetTitle("Fiche de pr�sence");
$pdf->SetAutoPageBreak(0);
$pdf->AliasNbPages();

// liste des moniteurs
$query="SELECT distinct p.P_ID, p.P_NOM, p.P_PRENOM, tp.TP_LIBELLE
		 from evenement_participation ep, pompier p, type_participation tp, evenement e
		 where ep.P_ID = p.P_ID
		 and e.E_CODE = ep.E_CODE
		 and ep.TP_ID > 0 
		 and ep.TP_ID = tp.TP_ID
		 and ( e.E_CODE=".$evenement."  or e.E_PARENT=".$evenement.")
		 and tp.INSTRUCTOR = 1
		 order by p.P_NOM, p.P_PRENOM asc";
		 
$result = mysql_query($query);
$moniteurs="";
while ($data = mysql_fetch_array($result)) {
	if ( $moniteurs <> "" ) $moniteurs .= ", ";
	$moniteurs .= my_ucfirst($data['P_PRENOM'])." ".strtoupper($data['P_NOM'])." (".$data['TP_LIBELLE'].")";
}

// liste des stagiaires
$query="SELECT distinct s.S_CODE, e.E_CODE, p.P_ID, p.P_NOM, p.P_PRENOM, date_format(p.P_BIRTHDATE, '%d-%m-%Y') P_BIRTHDATE, P_BIRTHPLACE 
		 from evenement_participation ep, pompier p, evenement e, section s
		 where ep.P_ID = p.P_ID
		 and s.S_ID = e.S_ID
		 and e.E_CODE = ep.E_CODE
		 and ep.TP_ID = 0 
		 and ( e.E_CODE=".$evenement."  or e.E_PARENT=".$evenement.")
		 order by p.P_NOM, p.P_PRENOM asc";
$result = mysql_query($query);
$nbstagiaires=mysql_num_rows($result);

if ( $nbstagiaires == 0 ) {
	$empty=true;
	$query="select null as P_ID, ' ' as P_NOM, ' ' as p_PRENOM, null as P_BIRTHDATE, null as P_BIRTHPLACE";
	$result = mysql_query($query);
}
else $empty=false;

if ( ! $empty ) {
  while ($data = mysql_fetch_array($result)) {
    $query2="select PF_ADMIS, PF_DIPLOME from personnel_formation where P_ID=".$data['P_ID']." and E_CODE=".$evenement;
    $result2 = mysql_query($query2);
    $row2 = mysql_fetch_array($result2);
    if ( $row2['PF_ADMIS'] == 1 ) $admis='OUI'; else $admis='NON';
    $diplome=$row2['PF_DIPLOME'];
    if ( $diplome == '')  $diplome ='-';
	if ( $i % 12 == 0) { // nouvelle page
		$y=54;	
		$pdf->AddPage();
		//$pdf->Image($logo,5,5);
		$pdf->SetTextColor(13,53,148);
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(60,10);	
		//$pdf->MultiCell(120,6,$section_affiche."\n".$antenne_affiche,0,"L",0);
		$pdf->Text(10,10,"Proc�s verbal de la formation ".$description);
		$pdf->SetFont('Arial','',11);
		
		$txt="� la formation initiale";
		if (substr($type,0,3) == 'PSE') $txt .=" aux premiers secours en �quipe (".$type.")";
		$pdf->Text(10,20,"Suite ".$txt.",");
		$pdf->Text(10,26,"qui s'est d�roul�e ".$periode);
		$pdf->Text(10,32,"� ".$lieu);
		$pdf->SetXY(0,10);			
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(0,4,$adr."\n".$phoneinfos."\n".$mailinfos,0,"R",0);
		$pdf->SetFont('Arial','B',11);

		$pdf->Text(10,40,"Responsable administratif:");
		$pdf->Text(10,45,"Moniteurs:");
		$pdf->SetFont('Arial','I',10);
		$pdf->Text(70,40,$responsable);
		$pdf->SetXY(69,41);
		$pdf->MultiCell(190,5,$moniteurs,0,"L",0);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(200);
		$pdf->SetXY(10,$y);
		$pdf->SetFont('Arial','B',14);
		$pdf->MultiCell(75,$hauteur,"Nom Pr�nom",1,"L",true);
		$pdf->SetFont('Arial','B',10);	
		$pdf->SetXY(85,$y);
		$pdf->MultiCell(35,$hauteur,"Date de naissance",1,"C",true);
		$pdf->SetXY(120,$y);
		$pdf->MultiCell(60,$hauteur,"Lieu de naissance",1,"C",true);
		$pdf->SetXY(180,$y);
		$pdf->MultiCell(30,$hauteur,"Apte",1,"C",true);
		$pdf->SetXY(210,$y);
		$pdf->MultiCell(70,$hauteur,"N�dipl�me",1,"C",true);
		
  	}
	$i=$i+1;$y=$y+$hauteur;
	if (! $empty){
		$n=strtoupper($data['P_NOM'])." ".my_ucfirst($data['P_PRENOM']);
		if ( $data['E_CODE'] <> $evenement ) $n .= " - ".$data['S_CODE'];
		$nom_prenom=substr($n,0,30);
		if (strlen($n) > 30 )  $nom_prenom .= ".";
		$date_nai=$data['P_BIRTHDATE'];
		$lieu_nai=$data['P_BIRTHPLACE'];
	}
	else {
		$nom_prenom="";
		$date_nai="";
	}
	$pdf->SetXY(10,$y);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetFillColor(240);
	$pdf->MultiCell(75,$hauteur,"$nom_prenom",1,"L",true);
	$pdf->SetFont('Arial','B',10);	
	$pdf->SetXY(85,$y);
	$pdf->MultiCell(35,$hauteur,"$date_nai",1,"C",true);
	$pdf->SetXY(120,$y);
	$pdf->MultiCell(60,$hauteur,"$lieu_nai",1,"C",true);
	$pdf->SetXY(180,$y);
	$pdf->MultiCell(30,$hauteur,$admis,1,"C",true);
	$pdf->SetXY(210,$y);
	$pdf->MultiCell(70,$hauteur,$diplome,1,"C",true);
  }
}
else $y=180;
$pdf->SetFont('Arial','B',10);
$pdf->SetTextColor(0,0,0);
$y=min(183,$y+8);
$pdf->SetXY(10,$y);
$pdf->MultiCell(50,10,"Fait � ",0,"L");
$pdf->SetXY(150,$y);
$pdf->MultiCell(80,10,"le ",0,"L");
$y = $y + 8;
$pdf->SetXY(10,$y);
$pdf->MultiCell(80,10,"Signatures du responsable p�dagogique:",0,"L");
$pdf->SetXY(120,$y);
$pdf->MultiCell(80,10,"Signatures formateurs:",0,"L");
$pdf->SetXY(220,$y);
$pdf->MultiCell(80,10,"Signatures du ".$titre.":",0,"L");
$pdf->SetXY(10,200);
$pdf->SetFont('Arial','',6);
$pdf->MultiCell(100,5,$printed_by,"","L");
$pdf->Output();	
}


?>
