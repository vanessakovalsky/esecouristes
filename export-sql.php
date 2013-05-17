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

$OptionsExport = "";
check_all(27);

// check if exists cotisation
$query="select count(*) as NB from poste
	    where DESCRIPTION='Cotisation'";
$result=mysql_query($query);
$row=mysql_fetch_array($result);
if ( $row["NB"] <> 0 ) $cotisation=true;
else $cotisation=false;


// check if veille opérationnelle
$query="select count(*) as NB from groupe
	    where GP_DESCRIPTION='Veille opérationnelle'";
$result=mysql_query($query);
$row=mysql_fetch_array($result);
if ( $row["NB"] <> 0 ) $veille=true;
else $veille=false;

// check if personnelsante
$query="select count(*) as NB from equipe
	    where EQ_NOM='Personnels de Santé'";
$result=mysql_query($query);
$row=mysql_fetch_array($result);
if ( $row["NB"] <> 0 ) $personnelsante=true;
else $personnelsante=false;

switch ($nbsections){
case 0: // Association de sécurité civile

// événements
$OptionsExport .= "\n<OPTGROUP LABEL=\"événements\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"1activite\"".(($exp=="1activite")?" selected":"").">Evénements - participants</option>";
$OptionsExport .= "\n"."<option value=\"1point\"".(($exp=="1point")?" selected":"").">Point de situation</option>";
$OptionsExport .= "\n"."<option value=\"1evenement_annule_liste\"".(($exp=="1evenement_annule_liste")?" selected":"").">Evènements Annulés (justificatifs)</option>";
$OptionsExport .= "\n"."<option value=\"1evenement_annule\"".(($exp=="1evenement_annule")?" selected":"").">Evènements Annulés par type</option>";
$OptionsExport .= "\n"."<option  value=\"1tcd_activite_annee\" ".(($exp=="1tcd_activite_annee")?" selected":"").">Evénements par type et par section</option>";
$OptionsExport .= "\n"."<option value=\"1conventions\"".(($exp=="1conventions")?" selected":"").">Etat des Conventions - COA</option>";
$OptionsExport .= "\n"."<option value=\"1dps\"".(($exp=="1dps")?" selected":"").">DPS réalisés</option>";
$OptionsExport .= "\n"."<option value=\"1maraudes\"".(($exp=="1maraudes")?" selected":"").">Maraudes réalisées</option>";
$OptionsExport .= "\n"."<option value=\"1ogripa\"".(($exp=="1ogripa")?" selected":"").">Grippe A - divers</option>";
$OptionsExport .= "\n"."<option value=\"1vacci\"".(($exp=="1vacci")?" selected":"").">Grippe A - vaccination</option>";
$OptionsExport .= "\n"."<option value=\"1horairesdouteux\"".(($exp=="1horairesdouteux")?" selected":"").">Horaires douteux à corriger</option>";

// formations
$OptionsExport .= "\n<OPTGROUP LABEL=\"formations\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"1formations\"".(($exp=="1formations")?" selected":"").">Formations réalisées</option>";
$OptionsExport .= "\n"."<option value=\"1formationsnontraitees\"".(($exp=="1formationsnontraitees")?" selected":"").">Formations non traitées</option>";
$OptionsExport .= "\n"."<option value=\"1sst\"".(($exp=="1sst")?" selected":"").">Formations SST réalisées</option>";

if(check_rights($_SESSION['id'], 29)){ // autoriser seulement au personnes avec la compétence 29 : comptabilité
$OptionsExport .= "\n<OPTGROUP LABEL=\"facturation\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"1facturation\"".(($exp=="1facturation")?" selected":"").">Suivi commercial</option>";
$OptionsExport .= "\n"."<option value=\"1facturationRecap\"".(($exp=="1facturationRecap")?" selected":"").">Détail du suivi commercial</option>";
$OptionsExport .= "\n"."<option value=\"fafacturer\"".(($exp=="fafacturer")?" selected":"").">A facturer</option>";
$OptionsExport .= "\n"."<option value=\"fnonpaye\"".(($exp=="fnonpaye")?" selected":"").">Facturé non payé</option>";
$OptionsExport .= "\n"."<option value=\"1paye\"".(($exp=="1paye")?" selected":"").">Payé</option>";
$OptionsExport .= "\n"."<option value=\"1facturestoutes\"".(($exp=="1facturestoutes")?" selected":"").">Listes des factures</option>";

//$OptionsExport .= "\n"."<option value=\"devisencours\"".(($exp=="devisencours")?" selected":"").">Devis en cours...</option>";
//$OptionsExport .= "\n"."<option value=\"facturenonpaye\"".(($exp=="facturenonpaye")?" selected":"").">Facturé non payé...</option>";
}

// véhicules / matériel
$OptionsExport .= "\n<OPTGROUP LABEL=\"véhicules / matériel\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"vehicule\"".(($exp=="vehicule")?" selected":"").">Liste des véhicules</option>";
$OptionsExport .= "\n"."<option value=\"1vehicule_km\"".(($exp=="1vehicule_km")?" selected":"").">Kilométrage réalisé par véhicule</option>";
$OptionsExport .= "\n"."<option value=\"1evenement_km\"".(($exp=="1evenement_km")?" selected":"").">Kilométrage par type d'événement</option>";
$OptionsExport .= "\n"."<option value=\"vehicule_a_dispo\"".(($exp=="vehicule_a_dispo")?" selected":"").">Véhicules mis à disposition</option>";
$OptionsExport .= "\n"."<option value=\"materiel_a_dispo\"".(($exp=="materiel_a_dispo")?" selected":"").">Matériel mis à disposition</option>";

// personnel
$OptionsExport .= "\n<OPTGROUP LABEL=\"personnel\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"effectif\"".(($exp=="effectif")?" selected":"").">Liste du personnel</option>";
$OptionsExport .= "\n"."<option value=\"salarie\"".(($exp=="salarie")?" selected":"").">Liste du personnel salarié</option>";
$OptionsExport .= "\n"."<option value=\"adresses\"".(($exp=="adresses")?" selected":"").">Liste des adresses du personnel</option>";
$OptionsExport .= "\n"."<option value=\"1anniversaires\" ".(($exp=="1anniversaires")?" selected":"").">Anniversaires des membres</option>";
$OptionsExport .= "\n"."<option value=\"1heurespersonne\"".(($exp=="1heurespersonne")?" selected":"").">Participations / personne</option>";
$OptionsExport .= "\n"."<option value=\"1heuressections\"".(($exp=="1heuressections")?" selected":"").">Heures réalisées / section</option>";
if ( $cotisation ) {
$OptionsExport .= "\n"."<option value=\"1inactif\"".(($exp=="1inactif")?" selected":"").">Personnel inactif</option>";
$OptionsExport .= "\n"."<option value=\"cotisation\"".(($exp=="cotisation")?" selected":"").">Cotisations en retard</option>";
$OptionsExport .= "\n"."<option value=\"1anciens\"".(($exp=="1anciens")?" selected":"").">Anciens membres</option>";
$OptionsExport .= "\n"."<option value=\"engagement\"".(($exp=="engagement")?" selected":"").">Années d'engagement du personnel </option>";
}
else {
$OptionsExport .= "\n"."<option value=\"1inactif2\"".(($exp=="1inactif2")?" selected":"").">Personnel inactif</option>";
}
if ( $veille ) {
$OptionsExport .= "\n"."<option value=\"veille\"".(($exp=="veille")?" selected":"").">Personnel de veille opérationnelle </option>";
}
$OptionsExport .= "\n"."<option value=\"skype\"".(($exp=="skype")?" selected":"").">Identifiants de contact Skype </option>";
$OptionsExport .= "\n"."<option value=\"sansemail\"".(($exp=="sansemail")?" selected":"").">Personnel sans email valide</option>";
$OptionsExport .= "\n"."<option value=\"sansadresse\"".(($exp=="sansadresse")?" selected":"").">Personnel sans adresse valide</option>";
$OptionsExport .= "\n"."<option value=\"sanstel\"".(($exp=="sanstel")?" selected":"").">Personnel sans numéro de téléphone valide</option>";
$OptionsExport .= "\n"."<option value=\"1perso_km\"".(($exp=="1perso_km")?" selected":"").">Kilométrage réalisé avec véhicule personnel</option>";

// permissions
$OptionsExport .= "\n<OPTGROUP LABEL=\"permissions\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"groupes\"".(($exp=="groupes")?" selected":"").">Permissions du personnel</option>";
$OptionsExport .= "\n"."<option value=\"roles\"".(($exp=="roles")?" selected":"").">Rôles dans l'organigramme du personnel</option>";

// compétences
$OptionsExport .= "\n<OPTGROUP LABEL=\"secourisme\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"secouristes\"".(($exp=="secouristes")?" selected":"").">Liste des secouristes</option>";
$OptionsExport .= "\n"."<option value=\"secouristesparsection\"".(($exp=="secouristesparsection")?" selected":"").">Nombre de secouristes</option>";
$OptionsExport .= "\n"."<option value=\"moniteurs\"".(($exp=="moniteurs")?" selected":"").">Liste des moniteurs</option>";
$OptionsExport .= "\n"."<option value=\"moniteursparsection\"".(($exp=="moniteursparsection")?" selected":"").">Nombre de moniteurs</option>";
$OptionsExport .= "\n"."<option value=\"competence_expire\"".(($exp=="competence_expire")?" selected":"").">Compétences expirées</option>";

// diplômes 
$OptionsExport .= "\n<OPTGROUP LABEL=\"diplômes\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"diplomesPSC1\"".(($exp=="diplomesPSC1")?" selected":"").">Liste des diplômes PSC1</option>";
$OptionsExport .= "\n"."<option value=\"diplomesPSE1\"".(($exp=="diplomesPSE1")?" selected":"").">Liste des diplômes PSE1</option>";
$OptionsExport .= "\n"."<option value=\"diplomesPSE2\"".(($exp=="diplomesPSE2")?" selected":"").">Liste des diplômes PSE2</option>";

// sections
$OptionsExport .= "\n<OPTGROUP LABEL=\"sections\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"sectionannuaire\"".(($exp=="sectionannuaire")?" selected":"").">Annuaire des sections</option>";
$OptionsExport .= "\n"."<option value=\"agrements\"".(($exp=="agrements")?" selected":"").">Liste des agréments</option>";
$OptionsExport .= "\n"."<option value=\"agrements_dps\"".(($exp=="agrements_dps")?" selected":"").">Liste des agréments DPS</option>";

// entreprises clientes
$OptionsExport .= "\n<OPTGROUP LABEL=\"entreprises\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"entreprisesannuaire\"".(($exp=="entreprisesannuaire")?" selected":"").">Annuaire des entreprises</option>";
$OptionsExport .= "\n"."<option value=\"medecinsreferents\"".(($exp=="medecinsreferents")?" selected":"").">Médecins référents</option>";
$OptionsExport .= "\n"."<option value=\"1entreprisesDPS\"".(($exp=="1entreprisesDPS")?" selected":"").">Entreprises DPS</option>";
$OptionsExport .= "\n"."<option value=\"1entreprisesFOR\"".(($exp=="1entreprisesFOR")?" selected":"").">Entreprises Formations</option>";

// bilans
$OptionsExport .= "\n<OPTGROUP LABEL=\"bilans\" style=\"background-color:$background\">";
$OptionsExport .= "\n"."<option value=\"1dps\"".(($exp=="1dps")?" selected":"").">DPS réalisés</option>";
$OptionsExport .= "\n"."<option value=\"secouristes\"".(($exp=="secouristes")?" selected":"").">Liste des secouristes</option>";
$OptionsExport .= "\n"."<option value=\"moniteurs\"".(($exp=="moniteurs")?" selected":"").">Liste des moniteurs</option>";
if ($personnelsante)
$OptionsExport .= "\n"."<option value=\"personnelsante\"".(($exp=="personnelsante")?" selected":"").">Liste du personnel de santé</option>";

break;
case 1:// Caserne SP Volontaires
case 2:// Caserne SP Pro
default:
$OptionsExport .= "\n"."<option value=\"\" ".(($exp=="")?" selected":"").">---- Aucun état de synthèse ----</option>";
}

if($exp!=""){
$ColonnesCss = array();
$RuptureSur = array();
$SommeSur = array();

if((!isset($_POST['dtdb'])) or ($_POST['dtdb']=="")) { 
 	$dtdb=date("d-m-Y");
} else $dtdb=$_POST['dtdb'];
if((!isset($_POST['dtfn'])) or ($_POST['dtfn']=="")) { 
 	$dtfn=$dtdb;
}else $dtfn=$_POST['dtfn'];

$dtdeb = preg_split('/-/',$dtdb,3);
$dtfin =  preg_split('/-/',$dtfn,3);	
$dtdbq = date("Y-m-d",mktime(0,0,0,$dtdeb[1],$dtdeb[0],$dtdeb[2]));
$dtfnq = date("Y-m-d",mktime(0,0,0,$dtfin[1],$dtfin[0],$dtfin[2]));
$dtdbannee = date("Y",mktime(0,0,0,$dtdeb[1],$dtdeb[0],$dtdeb[2]));
$list = (isset($_POST['subsections'])?get_family($_POST['section']):$_POST['section']);
/* Recherche entre deux dates. */ 
$champdatedebut = "date_format(eh_date_debut,'%Y-%m-%d')";
$champdatefin = "date_format(eh_date_fin,'%Y-%m-%d')";
$evenemententredeuxdate = " (
                      ( $champdatedebut >= '$dtdbq' AND
                        $champdatedebut <= '$dtfnq' ) OR
                      ( $champdatefin >= '$dtdbq'  AND
                        $champdatefin <= '$dtfnq' ) OR
                      ( $champdatedebut <= '$dtdbq'  AND
                        $champdatefin >= '$dtfnq' )
                     ) ";
					 
// permissions
$id=$_SESSION['id'];
$mysection=$_SESSION['SES_SECTION'];
$ischef=is_chef($id,$_POST['section']);
$show='0';
if ((is_children($_POST['section'],$mysection)) or (check_rights($_SESSION['id'], 24))) {
 	if ( check_rights($_SESSION['id'], 2)) $show='1';
}
if ( $ischef ) $show='1';
					 
switch($_POST['exp']){

//-------------------
// sections 
//-------------------
      case "sectionannuaire":
		$export_name = "Annuaire des sections";
		$select="concat('<a href=\"upd_section.php?from=export&S_ID=',mys.s_id,'\" target=_blank>',REPLACE(mys.s_code,'ç','c'),'</a>') 'Section',
		mys.s_description 'Nom long',		
		mys.s_email 'Email',
		mys.s_phone 'Téléphone',
		mys.s_address 'Adresse',
		mys.s_zip_code 'Code postal',
		mys.s_city 'Ville'";
		$table=" ( select REPLACE(REPLACE(s_code,'é','e'),'è','e') s_code, s.s_id, substring(s.s_description,1,25) s_description, 
		 s.s_email,substring(s.s_phone,1,10) s_phone,s.s_address,s.s_zip_code,s.s_city
		 from section s
		 ) as mys";
		$where = (isset($list)?" s_id in(".$list.") ":"");
		$orderby="mys.s_code";
		$groupby="";
		break;
		
//-------------------
// entreprises 
//-------------------
      case "entreprisesannuaire":
		$export_name = "Annuaire des entreprises";
		$select="concat('<a href=\"upd_company.php?from=export&C_ID=',mys.c_id,'\" target=_blank>',REPLACE(mys.c_name,'ç','c'),'</a>') 'Entreprise',
		mys.tc_libelle 'Type',
		mys.c_description 'Description',
		mys.c_siret 'SIRET',		
		mys.c_email 'Email',
		mys.c_phone 'Téléphone',
		mys.c_address 'Adresse',
		mys.c_zip_code 'Code postal',
		mys.c_city 'Ville',
		mys.s_code 'Rattachée à'";
		$table=" ( select REPLACE(REPLACE(c.c_name,'é','e'),'è','e') c_name, c.s_id, c.c_siret, tc.tc_libelle, c.c_id, substring(c.c_description,1,35) c_description, 
		 c.c_email,substring(c.c_phone,1,10) c_phone,c.c_address, c.c_zip_code,c.c_city, s.s_code
		 from company c, section s , type_company tc
		 where c.C_ID > 0
		 and c.tc_code = tc.tc_code
		 and c.S_ID=s.S_ID
		 ) as mys";
		$where = (isset($list)?" s_id in(".$list.") ":"");
		$orderby="mys.c_name";
		$groupby="";
		break;
		
//-------------------
// médecins référents 
//-------------------

	case "medecinsreferents":
		$export_name = "Liste des médecins référents";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		concat(s.s_code,' - ',s.s_description)  'Section',
		tcr.tcr_description  ' Rôle',
		c.c_name  'Entreprise'";
		$table="pompier p, section s, company c, company_role cr, type_company_role tcr";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 ";
		$where .= " and p.p_id = cr.p_id ";
		$where .= " and c.c_id = cr.c_id ";
		$where .= " and tcr.tcr_code = cr.tcr_code ";
		$where .= " and cr.tcr_code like 'MED%' ";
		$orderby=" p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
		$groupby="";
		break;
		

//-------------------
// agrements 
//-------------------
      case "agrements":
		$export_name = "Liste des agrements";
		$select="concat('<a href=\"upd_section.php?from=export&status=agrements&S_ID=',mys.s_id,'\" target=_blank>',REPLACE(mys.s_code,'ç','c'),'</a>') 'Code',
		mys.s_description 'Nom',
		mys.ta_code 'Code',
		mys.ta_description 'Description agrément',
		date_format(mys.a_debut,'%d-%m-%Y')	'Début',
		date_format(mys.a_fin,'%d-%m-%Y')	'Fin'
		";
		$table=" ( select REPLACE(REPLACE(s.s_code,'é','e'),'è','e') s_code, s.s_id, substring(s.s_description,1,50) s_description, 
		 a.ta_code, ta.ta_description, a.a_debut, a.a_fin
		 from section s, agrement a, type_agrement ta
		 where ta.ta_code=a.ta_code
		 and a.s_id=s.s_id
		 ) as mys";
		$where = (isset($list)?" s_id in(".$list.") ":"");
		$orderby="mys.s_code, mys.ta_code";
		$groupby="";
		break;		

//-------------------
// agrements DPS
//-------------------
      case "agrements_dps":
		$export_name = "Liste des agrements pour les DPS";
		$select="concat('<a href=\"upd_section.php?from=export&status=agrements&S_ID=',mys.s_id,'\" target=_blank>',REPLACE(mys.s_code,'ç','c'),'</a>') 'Code',
		mys.s_description 'Nom',
		mys.ta_description 'Description agrément',
		date_format(mys.a_debut,'%d-%m-%Y')	'Début',
		date_format(mys.a_fin,'%d-%m-%Y')	'Fin',
		mys.ta_valeur	'DPS autorisés'
		";
		$table=" ( select REPLACE(REPLACE(s.s_code,'é','e'),'è','e') s_code, s.s_id, substring(s.s_description,1,50) s_description, 
		 ta.ta_description, a.a_debut, a.a_fin, tav.ta_valeur
		 from section s, agrement a, type_agrement ta, type_agrement_valeur tav
		 where ta.ta_code=a.ta_code
		 and a.tav_id=tav.tav_id
		 and a.s_id=s.s_id
		 and ta.ta_code='D'
		 ) as mys";
		$where = (isset($list)?" s_id in(".$list.") ":"");
		$orderby="mys.s_code";
		$groupby="";
		break;	

//-------------------
// facturation
//-------------------
case "1facturation":
	$export_name = "Facturation des événements du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	if($show<>1){
	$select = " 'NON AUTORISE' ";
	$table = "dual";
	}else{
	$select = " 
	e.statutFact 'Statut / Date',
	e.te_code 'Evénement', 
	e.e_libelle 'Libellé',	
	e.e_lieu 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y')  'Date début' ,	
	concat('<a href=''evenement_facturation.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'	
	";	
	$table = " (
	select 
	if(ef.paiement_date is not null,concat('<img src=\"images/green.gif\" valign=\"middle\" border=\"0\" alt=\"\" title=\"Payé\"/>Payé : ',date_format(ef.paiement_date,'%d-%m-%Y')),
	if(ef.relance_date is not null,concat('<img src=\"images/red.gif\" valign=\"middle\" border=\"0\" alt=\"\" title=\"Relance\"/>Relance : ',date_format(ef.relance_date,'%d-%m-%Y')),
	if(ef.facture_date is not null,concat('<img src=\"images/red.gif\" valign=\"middle\" border=\"0\" alt=\"\" title=\"Facture\"/>Facture : ',date_format(ef.facture_date,'%d-%m-%Y')),
	if(ef.devis_date is not null,concat('<img src=\"images/yellow.gif\" valign=\"middle\" border=\"0\" alt=\"\" title=\"Devis\"/>Devis : ',date_format(ef.devis_date,'%d-%m-%Y')),
	' ')))) 
	as 'statutFact',
	e.te_code, e.e_libelle, e.e_lieu ,eh.eh_date_debut,e.e_code,
	eh.eh_date_fin,e.s_id, e.e_canceled
	FROM evenement e
	left JOIN evenement_facturation ef ON e.e_code = ef.e_id
	join evenement_horaire eh on eh.e_code = e.e_code
	where ". $evenemententredeuxdate ."
	and e.e_canceled = 0
	and eh.eh_id = 1
	GROUP BY e.e_code, e.e_libelle
	) as e
	";
	
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$where .=" and e.e_canceled = 0"; // exclure les évènements annulés
	$orderby  = " e.eh_date_debut, e.te_code";
	$groupby = " e.te_code, e.e_code";
	}
	//$RuptureSur = array("Evénement");
	//$SommeSur = array("");
	break;
case "1facturationRecap":
	$export_name = "Facturation des événements du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	if($show<>1){
	$select = " 'NON AUTORISE' ";
	$table = "dual";
	}else{	
	$select = " 
	e.te_code 'Evénement', 
	e.e_libelle 'Libellé',	
	e.e_lieu 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y')  'Date début' ,		
	e.Devis 'Devis',
	e.Facture 'Facture',
	e.Relance 'Relance',
	e.Paiement 'Paiement',
	concat('<a href=''evenement_facturation.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = " (
	select 
	if(ef.paiement_date is not null,concat(date_format(ef.paiement_date,'%d-%m-%Y')),'') 'Paiement',
	if(ef.relance_date is not null,concat(date_format(ef.relance_date,'%d-%m-%Y')),'') 'Relance',
	if(ef.facture_date is not null,concat(date_format(ef.facture_date,'%d-%m-%Y')),'') 'Facture',
	if(ef.devis_date  is not null,concat(date_format(ef.devis_date,'%d-%m-%Y')),'') 'Devis',
	e.e_code, e.te_code, e.e_libelle, e.e_lieu ,eh.eh_date_debut,
	eh.eh_date_fin,e.s_id, e.e_canceled
	FROM evenement e 
	left JOIN evenement_facturation ef ON e.e_code = ef.e_id
	join evenement_horaire eh on eh.e_code = e.e_code
	where e.e_canceled = 0
	and eh.eh_id = 1
	GROUP BY e.e_code, e.e_libelle
	) as e
	";
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$where .=" and e.e_canceled = 0"; // exclure les évènements annulés
	$orderby  = " e.eh_date_debut, e.te_code";
	$groupby = " e.te_code, e.e_code";
	}
	//$RuptureSur = array("Evénement");
	//$SommeSur = array("");
	break;	
case "fafacturer":
	$export_name = "Evénements terminés a facturer";	
	if($show<>1){
	$select = " 'NON AUTORISE' ";
	$table = "dual";
	}else{	
	$select = " 
	e.te_code 'Evénement', 
	e.e_libelle 'Libellé',	
	e.e_lieu 'Lieu',
	date_format(eh.eh_date_debut,'%d-%m-%Y')  'Date début' ,		
	if(ef.devis_date is not null,concat(date_format(ef.devis_date,'%d-%m-%Y')),NULL) 'Devis',
	if(ef.devis_date is not null,concat(ef.devis_montant),NULL) 'Montant',
	concat('<a href=''evenement_facturation.php?from=export&tab=3&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = "evenement e, evenement_facturation ef, evenement_horaire eh ";
	$where = " e.e_code = ef.e_id ";
	$where .= (isset($list)?"  AND e.s_id in(".$list.") ":"");
	$where .=" AND e.e_canceled = 0"; // exclure les évènements annulés	
	$where .=" AND ef.facture_date is null "; 
	$where .=" AND ef.paiement_date is null "; 	
	$where .=" AND eh.eh_date_fin <= now() ";	
	$where .= " AND eh.e_code = e.e_code";
	$where .= " AND eh.eh_id = 1";
	$orderby  = " eh.eh_date_debut, e.te_code";
	$groupby = " e.te_code, e.e_code";
	$RuptureSur = array("Evénement");
	$SommeSur = array("Montant");
	}
	break;			
case "fnonpaye":
	$export_name = "Evénements facturés non payés";	
	if($show<>1){
	$select = " 'NON AUTORISE' ";
	$table = "dual";
	}else{	
	$select = " 
	e.te_code 'Evénement', 
	e.e_libelle 'Libellé',	
	e.e_lieu 'Lieu',
	date_format(eh.eh_date_debut,'%d-%m-%Y')  'Date début' ,		
	if(ef.facture_date is not null,concat(date_format(ef.facture_date,'%d-%m-%Y')),NULL) 'Facture',
	if(ef.relance_date is not null,concat(date_format(ef.relance_date,'%d-%m-%Y'),' No:',ef.relance_num),NULL) 'Relance',
	if(ef.devis_date is not null,concat(ef.devis_montant),NULL) 'Montant devis',
	if(ef.facture_date is not null, ef.facture_montant ,NULL) 'Montant facturé',	
	concat(ef.facture_numero) 'Facture No',
	concat('<a href=''evenement_facturation.php?from=export&tab=4&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = "evenement e, evenement_facturation ef, evenement_horaire eh ";
	$where = " e.e_code = ef.e_id ";
	$where .= (isset($list)?"  AND e.s_id in(".$list.") ":"");
	$where .=" AND e.e_canceled = 0"; // exclure les évènements annulés
	$where .=" AND ef.paiement_date is null "; 
	$where .=" AND ef.facture_date is not null ";
	$where .= " AND eh.e_code = e.e_code";
	$where .= " AND eh.eh_id = 1";
	$orderby  = " eh.eh_date_debut, e.te_code";
	$groupby = " e.te_code, e.e_code";
	$RuptureSur = array("Evénement");
	$SommeSur = array("Montant facturé");
	}
	break;			
case "1paye":
/*	
En cas de collaboration, il peut être utile de savoir si une autre section a été payée.
le montant doit cependant rester confidentiel.
*/
	$export_name = "Evénements payés entre le ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = " 
	e.te_code 'Evénement', 
	e.e_libelle 'Libellé',	
	e.e_lieu 'Lieu',
	date_format(eh.eh_date_debut,'%d-%m-%Y')  'Date début' ,		
	concat(ef.facture_numero) 'Facture No',
	if(ef.facture_date is not null,concat(date_format(ef.facture_date,'%d-%m-%Y')),NULL) 'Facture',	
	if(ef.paiement_date is not null,concat(date_format(ef.paiement_date,'%d-%m-%Y')),NULL) 'Paiement',
	if(ef.paiement_date is not null,if(".$show."<>1,'confidentiel',ef.facture_montant),NULL) 'Montant',
	concat('<a href=''evenement_facturation.php?from=export&tab=5&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = "evenement e, evenement_facturation ef, evenement_horaire eh";
	$where = " e.e_code = ef.e_id ";
	//$where = " $evenemententredeuxdate ";
	$where .= " AND ef.paiement_date between '$dtdbq' and '$dtfnq' ";
	$where .= (isset($list)?"  AND e.s_id in(".$list.") ":"");
	//$where .=" AND e.e_canceled = 0"; // exclure les évènements annulés
	$where .=" AND ef.paiement_date is not null ";
	$where .= " AND eh.e_code = e.e_code";
	$where .= " AND eh.eh_id = 1";
	$orderby  = " eh.eh_date_debut, e.te_code";
	$groupby = " e.te_code, e.e_code";
	$RuptureSur = array("Evénement");
	$SommeSur = array("Montant");
	break;				
case "1facturestoutes":
/*	
En cas de collaboration, il peut être utile de savoir si une autre section a été payée.
le montant doit cependant rester confidentiel.
*/
	$export_name = "Factures émises entre le ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = " 	
	if(ef.paiement_date is null,'<img src=\"images/red.gif\" border=\"0\" alt=\"\" title=\"NON PAYE\">','<img src=\"images/green.gif\" border=\"0\" alt=\"\" title=\"Payé\">') 'Statut',
	e.te_code 'Evénement', 
	e.e_libelle 'Libellé',	
	e.e_lieu 'Lieu',
	date_format(eh.eh_date_debut,'%d-%m-%Y')  'Date début' ,		
	if(ef.facture_date is not null,concat(date_format(ef.facture_date,'%d-%m-%Y')),NULL) 'Facture',	
	if(ef.paiement_date is not null,concat(date_format(ef.paiement_date,'%d-%m-%Y')),NULL) 'Paiement',
	if(ef.paiement_date is not null,if(".$show."<>1,'confidentiel',ef.facture_montant),NULL) 'Montant',
	concat('<b>',ef.facture_numero,'</b>') 'Facture No',
	concat('<a href=''evenement_facturation.php?from=export&tab=5&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = "evenement e, evenement_facturation ef, evenement_horaire eh";
	$where = " e.e_code = ef.e_id ";
	$where .= " AND eh.e_code = e.e_code";
	$where .= " AND eh.eh_id = 1";
	//$where = " $evenemententredeuxdate ";
	$where .= " AND ef.facture_date between '$dtdbq' and '$dtfnq' ";
	$where .= (isset($list)?"  AND e.s_id in(".$list.") ":"");
	//$where .=" AND e.e_canceled = 0"; // exclure les évènements annulés
	//$where .=" AND ef.paiement_date is not null "; 
	$orderby  = " ef.facture_date, eh.eh_date_debut";
	$groupby = " e.te_code, e.e_code";
	//$RuptureSur = array("Evénement");
	$SommeSur = array("Montant");
	break;

//-------------------
// événements 
//-------------------	
case ( $exp == "1activite" or $exp == "1point" or $exp == "1maraudes"):
    if ( $exp == '1maraudes' ) {
        $t1="'Rencontrées.'";
     	$t2="'Transportées.'";
     	$t3="''";
    }
    else {
     	$t1="'Inter.'";
     	$t2="'Evac.'";
     	$t3="'Assist.'";
    }
	if ( $exp == "1activite" ) 
		$export_name = "Evénements du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
	else if ( $exp == "1maraudes" )
		$export_name = "Maraudes du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
	else
		$export_name = "Point de situation du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
	$select = " e.te_code 'Type', 
	e.libelle 'Libellé',
	case
	   when e.eh_id=1 then ''
	   else concat('<i>partie ', e.eh_id,'</i> ')
	end
	as 'Partie',
	substring(e.S_CODE,1,2) 'Org.',
	e.e_lieu 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y')  'Date début' ,
	case
	   when e.eh_id = 1 then e.e_nb1
	   else ''
	end
	as $t1,
	case
	   when e.eh_id = 1 then e.e_nb2 
	   else ''
	end
	as $t2,
	case
	   when e.eh_id = 1 then e.e_nb3 
	   else ''
	end
	as $t3,
	sum(personnes) ' Participants.',
	e.eh_duree 'Heures.',
	(sum(personnes)*e.eh_duree) 'Total',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = " (
	select s.S_CODE, e.e_code code, eh.eh_id ,e.e_libelle libelle, count(ep.p_id) personnes, e.*,
	eh.eh_duree, eh.eh_date_debut, eh.eh_date_fin
	FROM evenement_horaire eh
	JOIN evenement e on e.E_CODE = eh.E_CODE
	JOIN section s on s.S_ID=e.S_ID
	left JOIN evenement_participation ep ON (eh.e_code = ep.e_code and eh.eh_id = ep.eh_id)
	where ". $evenemententredeuxdate ."
	and e.e_canceled = 0
	GROUP BY e.e_code, eh.eh_id
	) as e
	";
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$where .=" and e.e_canceled = 0"; // exclure les évènements annulés
	if ( $exp == "1maraudes" ) $where .=" and e.te_code='MAR'";
	$orderby  = " e.te_code, e.eh_date_debut";
	$groupby = " e.te_code, e.e_code, e.eh_id";
	$RuptureSur = array("Evénement");
	$SommeSur = array("Rencontrées.","Transportées.","Participants.","Inter.","Assist.","Evac.","Total");
	break;	

//-------------------
// DPS 
//-------------------
case "1dps":
	$export_name = "DPS du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = "
	e.libelle 'Libellé',
	case
	   when e.eh_id=1 then ''
	   else concat('<i>partie ', e.eh_id,'</i> ')
	end
	as 'Partie',
	substring(e.e_lieu,1,25) 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y')  'Date début.' ,
	e.e_nb1 'Soins.',
	e.e_nb2 'Evac.',
	e.e_nb3 'Assist.',
	case
	   when e.e_flag1 = 1 then 'oui'
	   else 'non'
	   end
	as 'Interassociatif.',
	sum(personnes) 'Participants.',
	e.eh_duree 'h/p.',
	e.eh_duree * sum(personnes) 'Heures',
	case 
		when e.tav_id = 1 then '-'
		when e.tav_id = 2 then 'PAPS'
		when e.tav_id = 3 then 'DPS-PE'
		when e.tav_id = 4 then 'DPS-ME'
		when e.tav_id = 5 then 'DPS-GE'
		end
	as 'DPS',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = " (
	select e.e_code code, eh.eh_id, e.e_libelle libelle, count(ep.p_id) personnes, 0 vehicules, 0 km, e.*, eh.eh_date_debut, eh.eh_date_fin, eh.eh_duree
	FROM evenement_horaire eh
	JOIN evenement e on e.E_CODE = eh.E_CODE
	left JOIN evenement_participation ep ON (eh.e_code = ep.e_code and eh.eh_id = ep.eh_id)
	where e.e_canceled = 0
	and e.te_code='DPS' 
	and ".$evenemententredeuxdate." 
	GROUP BY e.e_code,eh.eh_id, e.e_libelle 
	) as e
	";
	$where = " e.e_canceled = 0";
	$where .= (isset($list)?" and e.s_id in(".$list.") ":"");
	$orderby  = " e.te_code, e.eh_date_debut, e.e_code";
	$groupby = " e.te_code, e.e_code, e.eh_id";
	$SommeSur = array("Heures");

	break;

//-------------------
// Grippe A 
//-------------------
	case "1ogripa":
	$export_name = "Opérations Grippe A du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = "
	e.s_code 'Section',
	e.libelle 'Libellé',	
	substring(e.e_lieu,1,25) 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y')  'Date début.' ,
	sum(personnes) 'Participants.',
	e.eh_duree 'h/p.',
	e.eh_duree * sum(personnes) 'Heures',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = " (
	select e.e_code code, e.e_libelle libelle, s.s_code , count(ep.p_id) personnes, e.*, eh.eh_date_debut,eh.eh_date_fin, eh.eh_duree
	FROM section s, evenement e
	JOIN evenement_horaire eh on eh.e_code = e.e_code
	left JOIN evenement_participation ep ON e.e_code = ep.e_code
	where ". $evenemententredeuxdate ."
	and e.e_canceled = 0
	and e.s_id = s.s_id
	and e.te_code='GRIPA'
	GROUP BY e.e_code, e.e_libelle
	) as e
	";
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$where .=" and e.e_canceled = 0"; // exclure les évènements annulés
	$orderby  = " e.s_code, e.eh_date_debut";
	$groupby = " e.te_code, e.e_code";
	$SommeSur = array("Heures");
	break;
	
//-------------------
// Horaires douteux 
//-------------------
	case "1horairesdouteux":
	$export_name = "Horaires douteux à corriger ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = "
	e.s_code 'Section',
	e.libelle 'Libellé',
	e.te_code 'Type',	
	substring(e.e_lieu,1,25) 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y')  'Date début.' ,
	date_format(e.eh_date_fin,'%d-%m-%Y')  'Date fin.' ,
	sum(personnes) 'Participants.',
	concat('<b>',round(e.eh_duree),'</b>') 'h/p.',
	round(e.eh_duree * sum(personnes)) 'Heures',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = " (
	select e.e_code code, e.e_libelle libelle, s.s_code , count(ep.p_id) personnes, e.*, eh.eh_date_debut, eh.eh_date_fin, eh.eh_duree
	FROM section s, evenement e
	JOIN evenement_horaire eh on eh.e_code = e.e_code
	left JOIN evenement_participation ep ON e.e_code = ep.e_code
	where ". $evenemententredeuxdate ."
	and e.e_canceled = 0
	and e.s_id = s.s_id
	GROUP BY e.e_code, e.e_libelle
	) as e
	";
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$where .=" and e.e_canceled = 0"; // exclure les évènements annulés
	$where .=" and ((e.eh_date_debut=e.eh_date_fin and e.eh_duree > 20) ";
	$where .=" 		or (e.eh_duree > 50)) ";
	$orderby  = " e.eh_date_debut";
	$groupby = " e.te_code, e.e_code";
	$SommeSur = array("Heures");
	break;

//-------------------
// Grippe A - vaccination
//-------------------
	case "1vacci":
	$export_name = "Opérations vaccination Grippe A du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = "
	e.s_code 'Section',
	e.libelle 'Libellé',	
	substring(e.e_lieu,1,25) 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y')  'Date début.' ,
	sum(personnes) 'Participants.',
	e.eh_duree 'h/p.',
	e.eh_duree * sum(personnes) 'Heures',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = " (
	select e.e_code code, e.e_libelle libelle, s.s_code , count(ep.p_id) personnes, e.*, eh.eh_date_debut, eh.eh_date_fin, eh.eh_duree
	FROM section s, evenement e 
	JOIN evenement_horaire eh on eh.e_code = e.e_code
	left JOIN evenement_participation ep ON e.e_code = ep.e_code
	where e.e_canceled = 0
	and e.s_id = s.s_id
	and e.te_code='VACCI'
	GROUP BY e.e_code, e.e_libelle
	) as e
	";
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$where .=" and e.e_canceled = 0"; // exclure les évènements annulés
	$orderby  = " e.s_code, e.eh_date_debut";
	$groupby = " e.te_code, e.e_code";
	$SommeSur = array("Heures");
	break;
//-------------------
// formations 
//-------------------
	case "1formations_old":
	$export_name = "Formations du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
    $select = " 
	e.libelle 'Libellé',
	case
	   when e.eh_id=1 then ''
	   else concat('<i>partie ', e.eh_id,'</i> ')
	end
	as 'Partie',
	e.e_lieu 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y') 'Date',
	e.S_CODE 'Section',
	sum(personnes) 'Stagiaires',
	e.eh_duree 'Heures.',
	(sum(personnes)*e.eh_duree) 'Total',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = " (
	select s.S_CODE, e.e_code code, eh.eh_id ,e.e_libelle libelle, count(ep.p_id) personnes, e.*,
	eh.eh_duree, eh.eh_date_debut, eh.eh_date_fin
	FROM evenement_horaire eh
	JOIN evenement e on e.E_CODE = eh.E_CODE
	JOIN section s on s.S_ID=e.S_ID
	left JOIN evenement_participation ep ON (eh.e_code = ep.e_code and eh.eh_id = ep.eh_id)
	where ". $evenemententredeuxdate ."
    and e.te_code = 'FOR'
	and e.e_canceled = 0
	GROUP BY e.e_code, eh.eh_id
	) as e
	";
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$orderby  = " e.e_code, e.eh_date_debut";
	$groupby = " e.e_code, e.eh_id";
	$SommeSur = array("Stagiaires","Total");
	break;

	case "1formations":
	$export_name = "Formations du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
    $select = " 
	e.libelle 'Libellé',
	case
	   when e.eh_id=1 then ''
	   else concat('<i>partie ', e.eh_id,'</i> ')
	end
	as 'Partie',
	e.e_lieu 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y') 'Date',
	e.S_CODE 'Section',	
	sum(NbStagiaires) 'Stagiaires',
	sum(HrsSta) 'Hrs_Stagiaires',
	sum(NbFormateurs) 'Encadrants',
	sum(HrsFor) 'Hrs_Encadrants',	
	e.eh_duree 'Heures.',
	(sum(personnes)*e.eh_duree) 'Total',
	sum(HrsFor)+sum(HrsSta) 'Réel',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = " (
	select s.S_CODE, e.e_code code, eh.eh_id ,e.e_libelle libelle, 
	count(ep.p_id) as personnes,
	case 
		when ep.tp_id=0 
		then count(ep.p_id) 
		else 0
	end 
	as 'NbStagiaires',
	case
		when (ep.ep_duree is null and ep.tp_id = 0) then eh.eh_duree
		when (ep.ep_duree is not null and ep.tp_id = 0) then ep.ep_duree
	end
	as 'HrsSta',	
	case 
		when ep.tp_id>0 
		then count(ep.p_id) 
		else 0
	end 
	as 'NbFormateurs', 
	case
		when (ep.ep_duree is null and ep.tp_id > 0) then eh.eh_duree
		when (ep.ep_duree is not null  and ep.tp_id > 0) then ep.ep_duree
	end
	as 'HrsFor',
	e.*,

	eh.eh_duree, eh.eh_date_debut, eh.eh_date_fin
	FROM evenement_horaire eh
	JOIN evenement e on e.E_CODE = eh.E_CODE
	JOIN section s on s.S_ID=e.S_ID
	left JOIN evenement_participation ep ON (eh.e_code = ep.e_code and eh.eh_id = ep.eh_id)
	where ". $evenemententredeuxdate ."
    and e.te_code = 'FOR'
	and e.e_canceled = 0
	GROUP BY e.e_code, eh.eh_id, ep.p_id
	) as e
	";
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$orderby  = " e.e_code, e.eh_date_debut";
	$groupby = " e.e_code, e.eh_id";
	$SommeSur = array("Stagiaires","Hrs_Stagiaires","Encadrants","Hrs_Encadrants","Total","Réel");
	break;
	
	case "1sst":
	$export_name = "Formations SST du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = " 
	e.libelle 'Libellé',
	case
	   when e.eh_id=1 then ''
	   else concat('<i>partie ', e.eh_id,'</i> ')
	end
	as 'Partie',
	e.e_lieu 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y') 'Date',
	e.S_CODE 'Section',
	sum(personnes) 'Stagiaires',
	e.eh_duree 'Heures.',
	(sum(personnes)*e.eh_duree) 'Total',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	// exclure les formateurs/encadrants : and ep.tp_id=0 
	$table = " (
	select s.S_CODE, e.e_code code, eh.eh_id ,e.e_libelle libelle, count(ep.p_id) personnes, e.*,
	eh.eh_duree, eh.eh_date_debut, eh.eh_date_fin
	FROM evenement_horaire eh
	JOIN evenement e on e.E_CODE = eh.E_CODE
	JOIN section s on s.S_ID=e.S_ID
	left JOIN evenement_participation ep ON (eh.e_code = ep.e_code and eh.eh_id = ep.eh_id and ep.tp_id=0)
	where ". $evenemententredeuxdate ."
    and e.te_code = 'FOR'
	and e.e_canceled = 0
	and ( e.e_libelle like '%sst%' or e.e_libelle like '%SST%' )
	GROUP BY e.e_code, eh.eh_id
	) as e
	";
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$orderby  = " e.e_code, e.eh_date_debut";
	$groupby = " e.e_code, e.eh_id";
	$SommeSur = array("Stagiaires","Total");
	break;
	
	case "1formationsnontraitees":
	$export_name = "Formations non traitées du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = " e.e_libelle 'Libellé',	
	e.e_lieu 'Lieu',
	date_format(e.eh_date_debut,'%d-%m-%Y')  'Date début' ,
	REPLACE( convert( e.eh_duree, CHAR ) , '.', ',' )  'Durée (h)' ,
	sum(personnes) 'Stagiaires',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	// exclure les formateurs/encadrants : and ep.tp_id=0 
	$table = " (
	select e.e_libelle libelle, count(ep.p_id) personnes, e.e_libelle,e.te_code,
	e.e_lieu, eh.eh_date_debut,eh.eh_date_fin, eh.eh_duree, e.e_code, e.s_id, e.e_canceled
	FROM evenement e 
	JOIN evenement_horaire eh on eh.e_code = e.e_code
	left JOIN evenement_participation ep ON e.e_code = ep.e_code and ep.tp_id=0
	where e.te_code = 'FOR'
	and eh.eh_id = 1
	GROUP BY e.e_code
	) as e
	";
	$where = "";
	$where = " $evenemententredeuxdate ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$where .=" and e.e_canceled = 0"; // exclure les évènements annulés
	$where .=" and not exists (select 1 from personnel_formation pf where pf.e_code = e.e_code)";
	$orderby  = " e.eh_date_debut";
	$groupby = " e.e_code";
	$SommeSur = array("Stagiaires");
	break;
	
//-------------------
// Etat des Conventions - COA
//-------------------
	case "1conventions":
	$export_name = "Etat des Conventions - COA du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = " e.e_convention 'Convention',
	e.e_libelle 'Libellé',	
	date_format(eh.eh_date_debut,'%d-%m-%Y')  'Date',
	s.s_code 'Section',
	case
	when e.e_canceled = 1 then '<img src=images/red.gif title=annulé><font color=red>annulé</font>'
	when e.e_canceled = 0 and e.e_closed = 1  then '<img src=images/yellow.gif title=fermé> <font color=orange>fermé</font>'
	when e.e_canceled = 0 and e.e_closed = 0  then '<img src=images/green.gif title=ouvert><font color=green>ouvert</font>'
	end
	as 'Statut',
	concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = "evenement e, section s, evenement_horaire eh";
	$where = " $evenemententredeuxdate ";
	$where .= " and e.s_id = s.s_id ";
	$where .= " and e.e_code = eh.e_code ";
	$where .= " and eh.eh_id = 1 ";
	$where .= " and e.e_convention is not null and e.e_convention <> ''";
	$where .= " and e.te_code = 'DPS' ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$orderby  = " eh.eh_date_debut";
	break;	
	
//-------------------
// Entreprises DPS
//-------------------
case ( $exp == "1entreprisesDPS" or $exp == "1entreprisesFOR"):
	if (  $exp == "1entreprisesDPS" ) {
		$tecode='DPS';
		$t='DPS';
	}
	else {
		$tecode='FOR';
		$t='formations';	
	}
	$export_name = "Entreprises bénéficiant de ".$t." du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";	
	$select = " c.c_name 'Entrerpise',
	c.c_email 'Email',
	c.c_contact_name 'Contact',
	s.s_code 'Section',
	count(*) 'Nombre de $t',
	concat('<a href=''upd_company.php?from=export&C_ID=',c.c_id,''' class=''noprint'' target=''_blank'' >voir</a>') '&nbsp;'
	";
	$table = "evenement e, section s, company c, evenement_horaire eh";
	$where = " $evenemententredeuxdate ";
	$where .= " and e.s_id = s.s_id ";
	$where .= " and e.e_code = eh.e_code";
	$where .= " and eh.eh_id=1";
	$where .= " and e.c_id = c.c_id ";
	$where .= " and c.c_id > 0 ";
	$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
	$where .= " and e.te_code = '".$tecode."' group by c.c_name";
	$orderby  = " c.c_name";
	break;
	

	
//-------------------
// personnel 
//-------------------	
	case "effectif":
		$export_name = "Liste du personnel";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		concat(s.s_code,' - ',s.s_description)  'Section',
		p.p_abbrege 'N° abrégé Dép'";
		$table="pompier p, section s";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 ";
		$where .= " and p.p_statut <> 'EXT' ";
		$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
		$groupby="";
		break;
		
	case "sansadresse":
		$export_name = "Liste du personnel sans adresse valide";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		concat(s.s_code,' - ',s.s_description)  'Section'";
		$table="pompier p, section s";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 ";
		$where .= " and p.p_statut <> 'EXT' ";
		$where .= " and ( p.p_city is null or p.p_address is null) ";
		$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
		$groupby="";
		break;

	case "sansemail":
		$export_name = "Liste du personnel sans email valide";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',
		concat(s.s_code,' - ',s.s_description)  'Section'";
		$table="pompier p, section s";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 ";
		$where .= " and p.p_statut <> 'EXT' ";
		$where .= " and p.p_email not like '%@%' ";
		$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
		$groupby="";
		break;

	case "skype":
		$export_name = "Liste des identifiants Skype";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		concat('<a href=\"skype:',p.p_skype,'?call\">',p.p_skype,'</a>') as 'Skype',
		concat(s.s_code,' - ',s.s_description)  'Section'";
		$table="pompier p, section s";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 ";
		$where .= " and p.p_statut <> 'EXT' ";
		$where .= " and p.p_skype is not null ";
		$where .= " and p.p_skype <> '' ";
		$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
		$groupby="";
		break;
		
	case "sanstel":
		$export_name = "Liste du personnel sans numéro de téléphone valide";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		concat(s.s_code,' - ',s.s_description)  'Section'";
		$table="pompier p, section s";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 ";
		$where .= " and p.p_statut <> 'EXT' ";
		$where .= " and p.p_phone is null and  p.p_phone2 is null";
		$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
		$groupby="";
		break;
		
	case "groupes":
		$export_name = "Permissions du personnel";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p_id,'\" target=_blank>',upper(p_nom),'</a>')  'NOM',
		p_prenom 'Prénom',		
		concat(s_code,' - ',s_description)  'Section',
		gp_description1 'Permission 1',
		case 
		when gp_flag1 = 1 then '+'
		when gp_flag1 = 0 then ''
		end
		as 'Niv1.',
		gp_description2 'Permission 2',
		case 
		when gp_flag2 = 1 then '+'
		when gp_flag2 = 0 then ''
		end
		as 'Niv2.'
		";
		$table= " (select p.p_id, p.p_nom, p.p_prenom, s.s_code, s.s_description, 
		g1.gp_description gp_description1, g2.gp_description gp_description2,
		p.gp_flag1, p.gp_flag2
		from section s, pompier p
		left join groupe g1 on p.gp_id = g1.gp_id
		left join groupe g2 on p.gp_id2 = g2.gp_id
		where p.p_old_member = 0
		and p.p_section = s.s_id
		and p.p_statut <> 'EXT' ";
		$table .= (isset($list)?" and p.p_section in(".$list.") ":"");
		$table .=") as pompier";
		$orderby="p_nom, p_prenom, s_description";
		$groupby="";
		break;
		
	case "roles":
		$export_name = "Rôles dans l'organigramme du personnel";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		concat(s.s_code,' - ',s.s_description)  'Section appartenance',
		g.gp_description 'Rôle',
		concat(s2.s_code,' - ',s2.s_description)  'Pour la section '
		";
		$table="pompier p, section s, section_role sr, groupe g, section s2";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and sr.gp_id = g.gp_id ";
		$where .= " and sr.s_id = s2.s_id ";
		$where .= " and sr.p_id = p.p_id ";
		$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_description";
		$groupby="";
		break;
		
	case "salarie":
		$export_name = "Liste du personnel salarié";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		concat(s.s_code,' - ',s.s_description)  'Section',
		p.TS_LIBELLE 'type salarié',
		case
		when p.TS_HEURES is null then concat('')
		when p.TS_HEURES =0 then concat('')
		when p.TS_HEURES > 0 then concat(p.TS_HEURES)
		end as 'Heures'";
		$table = " (
		select p.p_id, p.p_nom, p.p_hide, p.p_prenom, p.p_phone, p.p_email, p.TS_CODE, p.TS_HEURES, ts.TS_LIBELLE, p.p_section
		FROM pompier p
		left JOIN type_salarie ts on p.TS_CODE = ts.TS_CODE
		where p.p_old_member = 0 and p.P_STATUT <> 'EXT'
		and p.p_statut = 'SAL'
		) as p, section s 
		";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$orderby=" p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
		$groupby="";
		break;

	case "veille":
		$export_name = "Liste du personnel de Veille opérationnelle";
		$select="
		concat(s.s_code,' - ',s.s_description)  'Veille opérationnelle pour',
		concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		p.p_phone as 'Tél',		
		concat('<a href=mailto:',p.p_email,'>',p.p_email,'</a>') as 'Email'
		";
		$table = " (
		select p.p_id, p.p_nom, p.p_prenom, p.p_phone, p.p_email, sr.s_id
		FROM pompier p, section_role sr, groupe g
		where p.P_ID = sr.P_ID
		and sr.gp_id = g.gp_id
		and g.gp_description='Veille opérationnelle' 
		) as p, section s 
		";
		$where = (isset($list)?" s.s_id in(".$list.") AND ":"");
		$where .= " p.s_id = s.s_id ";
		$orderby=" s.s_code, p.p_nom, p.p_prenom";
		$groupby="";
		break;		
		
	case "engagement":
		$export_name = "Années d'engagement du personnel";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',		
		concat(s.s_code,' - ',s.s_description)  'Section',
		p.p_debut 'Année engagement'
		";
		$table="pompier p, section s";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 and p.P_STATUT <> 'EXT'";
		$where .= " and p.p_debut is not null and p.p_debut <> '' and p.p_debut <> '0000'";
		$orderby="p.p_debut, p.p_nom, p.p_prenom";
		$groupby="";
		break;		
		
//-------------------
// adresses 
//-------------------	
	case "adresses":
		$export_name = "Liste des adresses du personnel";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',
		p.p_address 'Adresse',
		p.p_zip_code 'Code postal',
		p.p_city 'Ville',		
		concat(s.s_code,' - ',s.s_description)  'Section'";
		$table="pompier p, section s";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 and p.P_STATUT <> 'EXT'";
		$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
		$groupby="";
		break;

//-------------------
// anciens 
//-------------------	
	case "1anciens":
		$export_name = "Liste du personnel ne faisant plus partie de $cisname";
		$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a>')  'NOM',
		p.p_prenom 'Prénom',			
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		concat(s.s_code,' - ',s.s_description)  'Section',
		p.p_debut 'Entrée',
		DATE_FORMAT(p.p_fin,'%d-%m-%Y') 'Sortie',
		tm.tm_code 'Raison'";
		$table="pompier p, section s, type_membre tm";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " AND date_format(P_FIN,'%Y-%m-%d')  >=  '".date("Y-m-d",mktime(0,0,0,$dtdeb[1],$dtdeb[0],$dtdeb[2]))."' ";
		$where .= " AND date_format(P_FIN,'%Y-%m-%d')  <=  '".date("Y-m-d",mktime(0,0,0,$dtfin[1],$dtfin[0],$dtfin[2]))."' ";
		$where .= " and p.p_old_member = tm.tm_id ";
		$where .= " and p.p_old_member > 0 and p.P_STATUT <> 'EXT'";
		$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
		$groupby="";
		break;
//-------------------
// vehicules 
//-------------------
	case "vehicule":
		$export_name = "Véhicules";
		$select ="v.TV_CODE  'Code',  v.V_MODELE  'Modèle', v.V_IMMATRICULATION  'Immat.', v.V_ANNEE  'Année' , V_KM  'Km', vp.VP_LIBELLE 'statut', concat(s.s_code,' - ',s.s_description)  'Section'";
		$table ="vehicule v, section s, vehicule_position vp";
		$where = " v.s_id = s.s_id ";
		$where .= " and vp.VP_ID=v.VP_ID";
		$where .= " and vp.VP_OPERATIONNEL >=0";
		$where .= (isset($list)?" AND v.s_id in(".$list.") ":"");
		$orderby ="TV_CODE, V_ANNEE asc";
		$groupby ="";		
		break;
		
//-------------------
// vehicules à dispo
//-------------------
	case "vehicule_a_dispo":
		$export_name = "Véhicules mis à disposition par $cisname";
		$select ="concat('<a href=\"upd_vehicule.php?from=export&vid=',v.v_id,'\" target=_blank>',v.TV_CODE,'</a>')  'Code',
		  v.V_MODELE  'Modèle', v.V_IMMATRICULATION  'Immat.', v.V_ANNEE  'Année' , V_KM  'Km', 
		  vp.VP_LIBELLE 'statut', concat(s.s_code,' - ',s.s_description)  'Section bénéficiaire', V_COMMENT 'Commentaire'";
		$table ="vehicule v, section s, vehicule_position vp";
		$where = " v.s_id = s.s_id ";
		$where .= " and v.v_externe = 1 ";
		$where .= " and vp.VP_ID=v.VP_ID";
		$where .= " and vp.VP_OPERATIONNEL >=0";
		$where .= (isset($list)?" AND v.s_id in(".$list.") ":"");
		$orderby ="TV_CODE, V_ANNEE asc";
		$groupby ="";		
		break;

//-------------------
// materiel à dispo
//-------------------
	case "materiel_a_dispo":
		$export_name = "Matériel mis à disposition par $cisname";
		$select ="concat('<a href=\"upd_materiel.php?from=export&mid=',m.ma_id,'\" target=_blank>',REPLACE(REPLACE(REPLACE(REPLACE(tm.TM_CODE,'é','e'),'è','e'),'à','a'),'ê','e'),'</a>')  'Type',
		  tm.TM_USAGE 'Catégorie', m.MA_MODELE  'Modèle',  m.MA_ANNEE  'Année' , m.MA_NB 'Pièces' ,
		  m.MA_NUMERO_SERIE 'N°série',m.MA_LIEU_STOCKAGE 'Lieu stockage',
		  concat(s.s_code,' - ',s.s_description)  'Section bénéficiaire', m.MA_COMMENT 'Commentaire'";
		$table ="materiel m, section s, type_materiel tm";
		$where = " m.s_id = s.s_id ";
		$where .= " and tm.tm_id = m.tm_id ";
		$where .= " and m.ma_externe = 1 ";
		$where .= (isset($list)?" AND m.s_id in(".$list.") ":"");
		$orderby ="TM_CODE, MA_ANNEE asc";
		$groupby ="";		
		break;

//-------------------
// Kilométrage réalisé par véhicule 
//-------------------
 	case "1vehicule_km":
		$export_name = "Kilométrage réalisé par véhicule du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
		$select ="tv.TV_LIBELLE 'Type', v.V_MODELE  'Modèle', v.V_IMMATRICULATION  'Immat.', sum(ev.ev_km)  'Total Km', concat(s.s_code,' - ',s.s_description)  'Section'";
		$table ="(select ev.e_code, ev.v_id, ev.ev_km, min(ev.eh_id) eh_id
            from vehicule v, evenement_vehicule ev, evenement e, evenement_horaire eh
		    where e.e_code = eh.e_code 
            AND eh.eh_id=ev.eh_id
            AND ev.v_id = v.v_id 
            AND v.v_id = ev.v_id 
            AND ev.ev_km is not null 
            AND e.e_code = ev.e_code 
            AND $evenemententredeuxdate
            AND e.E_CANCELED = 0";
        $table .=(isset($list)?" and v.s_id in(".$list.") ":"");
        $table .=" group by ev.e_code, ev.v_id) as ev, section s, type_vehicule tv, vehicule v";
        $where = " ev.v_id = v.v_id ";
        $where .= " AND tv.tv_code = v.tv_code ";
        $where .= " AND v.s_id = s.s_id ";
		$orderby = "";
		$groupby = "tv.TV_LIBELLE, v.V_IMMATRICULATION ";
		$RuptureSur = array("Immat");
		$SommeSur = array("Total Km");		
		break;
        
//-------------------
// Kilométrage réalisé par type d'événement 
//-------------------
   	case "1evenement_km":
		$export_name = "Kilométrage réalisé par type d'événement du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
		$select ="te.te_libelle 'Type Evénement', sum(ev.ev_km)  'Total Km'";     
  		$table ="(select ev.e_code, ev.v_id, ev.ev_km, min(ev.eh_id) eh_id
            from vehicule v, evenement_vehicule ev, evenement e, evenement_horaire eh
		    where e.e_code = eh.e_code 
            AND eh.eh_id=ev.eh_id
            AND ev.v_id = v.v_id 
            AND v.v_id = ev.v_id 
            AND ev.ev_km is not null 
            AND e.e_code = ev.e_code 
            AND $evenemententredeuxdate
            AND e.E_CANCELED = 0";
        $table .=(isset($list)?" and v.s_id in(".$list.") ":"");
        $table .=" group by ev.e_code, ev.v_id) as ev, evenement e, type_evenement te";
        $where = " e.e_code = ev.e_code ";
        $where .= " AND te.te_code = e.te_code ";
		$orderby = "";
		$groupby = "e.TE_CODE";	
		$SommeSur = array("Total Km");		
		break;
//-------------------
// Kilométrage réalisé en véhicule perso
//-------------------
	case "1perso_km":
		$export_name = "Kilométrage réalisé en véhicule personnel ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
		$select ="
		concat(upper(p.p_nom),' ',p.p_prenom) 'NOM',
		s.S_CODE 'Section',
		e.TE_CODE '.',
		e.E_LIBELLE 'Libellé',
		e.E_LIEU 'Lieu',
		date_format(eh.eh_date_debut,'%d-%m-%Y')  'Début',  
		ep.ep_km 'Km',
		concat('<a href=\"evenement_display.php?from=export&evenement=',e.e_code,'\" target=_blank>voir</a>') 'Lien'";
		$table ="pompier p, evenement e, section s, evenement_horaire eh, evenement_participation ep";
		$where = " p.p_section = s.s_id ";
		$where .= " AND e.e_code = eh.e_code and eh.eh_id=1";
		$where .= " AND ep.e_code = eh.e_code and ep.eh_id=eh.eh_id";
		$where .= " AND p.p_id = ep.p_id AND ep.ep_km is not null and $evenemententredeuxdate";
		$where .= " AND e.E_CANCELED = 0";
		$where .= (isset($list)?" and p.p_section in(".$list.") ":"");
		$orderby = "NOM, eh.eh_date_debut";
		$RuptureSur = array("NOM");
		$SommeSur = array("Km");		
		break;

//-------------------
// Evénements annulés 
//-------------------
	case "1evenement_annule":
		$export_name = "Evénements annulés par type du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
		$select ="te.te_libelle  'Type', sum(e.e_canceled) as 'Annulés', count(e.e_code) as 'Evénements', format((sum(e.e_canceled) / count(e.e_code)) * 100,0) as ' % '";
		$table =" evenement e, type_evenement te, evenement_horaire eh";
		$where =" $evenemententredeuxdate ";
		$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
		$where .= " AND e.te_code = te.te_code";
		$where .= " AND e.e_code = eh.e_code";
		$where .= " AND eh.eh_id = 1";
		$orderby ="";
		$groupby ="e.TE_CODE";
		$SommeSur = array("Annulés","Evénements");
		$colonneCss = array("","","nbr","nbr");
		break;
//-------------------
// Evénements annulés (justificatifs)
//-------------------	
	case "1evenement_annule_liste":
		$export_name = "Evènements Annulés (justificatifs) du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
		$select ="s.s_code 'Section', e.te_code 'Type.', 
		date_format(eh.eh_date_debut,'%d-%m-%Y') 'Date Début.', 
		concat(e.e_libelle ,' - ', e.e_lieu) 'Libellé.', 
		e.E_CANCEL_DETAIL 'Raison de l''annulation.' , 
		concat('<a href=''evenement_display.php?from=export&evenement=',e.e_code,''' class=''noprint'' target=''_blank''>voir</a>') '&nbsp;'";
		$table =" evenement e, section s, evenement_horaire eh";
		$where =" $evenemententredeuxdate ";
		$where .= " AND e.s_id = s.s_id";
		$where .= " AND e.e_code = eh.e_code";
		$where .= " AND e.E_CANCELED = 1";
		$where .= " AND eh.eh_id = 1";
		$where .= (isset($list)?"  and e.s_id in(".$list.") ":"");
		$orderby =" eh.eh_date_debut, e.te_code";
		$groupby ="";
		//$SommeSur = array("Nb Annulé","Nb Evénements");
		//$colonneCss = array("","","nbr","nbr");
		break;
//-------------------
// Anniversaires
//-------------------			
	case "1anniversaires":
		$export_name = "Anniversaires du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
		$cb= " case 
		when p_email !='' then concat('<input type=\"checkbox\"',' name=\"SendEmail\" id=\"SendEmail\"',' value=\"',p_id,'\" />') 
		else ''
		end 
		 as 'cb' ,";
		$cb=""; // en cours de dev pour envois emails
		$select= "distinct $cb upper(p_nom)  'NOM', p_prenom  'Prenom', date_format(P_BIRTHDATE,'%m-%d')  'Mois-Jour', concat(s.s_code,' - ',s.s_description)  'Section' ";
		$table= " pompier p, section s";
		$where = " p.p_section=s.s_id ";
		$where .= " AND date_format(P_BIRTHDATE,'%m-%d')  >=  '".date("m-d",mktime(0,0,0,$dtdeb[1],$dtdeb[0],$dtdeb[2]))."' ";
		$where .= " AND date_format(P_BIRTHDATE,'%m-%d')  <=  '".date("m-d",mktime(0,0,0,$dtfin[1],$dtfin[0],$dtfin[2]))."' ";
		$where .= (isset($list)?" AND p.p_section in (".$list.") ":"");
		$where .= " and p.P_OLD_MEMBER = 0 and p.P_STATUT <> 'EXT'"; // seulement les actifs.
		$orderby=" date_format(P_BIRTHDATE,'%m-%d') asc, p_nom, p_prenom, p_section";
		$groupby="";
		break;	
//-------------------
// evenement / type / section
//-------------------	
	case "1tcd_activite_annee":
		$sqlColonnes = "SELECT DISTINCT te.te_code 'Code', te.te_libelle 'Libelle' ";
		$sqlColonnes .= "FROM type_evenement te ";
		$sqlColonnes .= "ORDER BY te.te_code ";
		$dbCols = mysql_query($sqlColonnes) or die ("Erreur :".mysql_error());		
		// recherche sous sections
		$section = (isset($_POST['section'])?($_POST['section']<>""?$_POST['section']:-1):$section);
		$liste = (isset($_POST['subsections'])?get_family($section):$section);
		$annee = (isset($_POST['annee'])?$_POST['annee']:$dtdbannee);
		
		$sqlLignes = "";
		$sqlLignes = "SELECT concat(s.s_code,' - ',s.s_description) as 'Section', s.niv ";
		while($rowx = mysql_fetch_object($dbCols)){
				$sqlLignes .= ", SUM(IF(e.te_code = '$rowx->Code', 1, 0)) AS Code ";
		}
		$sqlLignes .= ", count(e.te_code) AS 'Total' ";
		$sqlLignes .= " FROM evenement e
		JOIN evenement_horaire eh on eh.e_code = e.e_code
		RIGHT JOIN section_flat s ON e.s_id = s.s_id ";
		$sqlLignes .= " AND $evenemententredeuxdate ";
		$sqlLignes .= " AND eh.eh_id=1 ";
		$sqlLignes .= " WHERE s.s_id in ($liste)";
		$sqlLignes .= " AND e.e_canceled = 0 "; // exclure les évènements annulés
		$sqlLignes .= " GROUP BY s.s_id ";
		$sqlLignes .= " ORDER BY lig ";		
		$export_name = "Evénements par type et par section du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"");
	break;
//-------------------
// heures / section
//-------------------
	case "1heuressections":			
	$export_name = "Heures réalisées sur les activités du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
	$select = "s.S_CODE  'Section', 
	te.te_libelle 'Type événement', 
	sum(eh.eh_duree) 'Heures'";
	$table = "evenement e, evenement_participation ep, type_evenement te, section s, evenement_horaire eh, pompier p";	
	$where = " e.e_code = ep.e_code ";
	$where .= " and e.s_id = s.s_id ";
	$where .= " and ep.e_code = eh.e_code ";
	$where .= " and ep.eh_id = eh.eh_id ";
	$where .= (isset($list)?"  and s.s_id in(".$list.") ":"");
	$where .= " and e.te_code = te.te_code ";
	$where .= " and $evenemententredeuxdate ";	
	$where .= " and e.E_CANCELED = 0";
	$where .= " and ep.P_ID = p.P_ID";
	$orderby  = "s.S_CODE, s.S_DESCRIPTION ,e.te_code";
	$groupby = "s.s_id, te.te_libelle";
	$RuptureSur = array("Section");
	$SommeSur[0] = "Heures";
	
	// Heures pour Orange
	
	$select_orange = "s.S_CODE  'Section', 
	te.te_libelle 'Type événement',
	sum(eh.eh_duree) 'Orange'";
	$table_orange = "evenement e, evenement_participation ep, type_evenement te, section s, evenement_horaire eh, pompier p";
	$where_orange = " e.e_code = ep.e_code";
	$where_orange .= " and e.s_id = s.s_id";
	$where_orange .= " and ep.e_code = eh.e_code";
	$where_orange .= " and ep.eh_id = eh.eh_id";
	$where_orange .= (isset($list)?" and s.s_id in(".$list.")":"");
	$where_orange .= " and e.te_code = te.te_code";
	$where_orange .= " and $evenemententredeuxdate";	
	$where_orange .= " and e.E_CANCELED = 0";
	$where_orange .= " and ep.P_ID = p.P_ID";
	$where_orange .= " and p.C_ID = 109";
	$orderby_orange  = "s.s_id, s.S_DESCRIPTION ,e.te_code";
	$groupby_orange = "s.s_id,te.te_libelle";
	$SommeSur[1] = "Orange";
		
		
	// Heures pour La Poste Corporate
	
	$select_lp_corporate = "s.S_CODE  'Section', 
	te.te_libelle 'Type événement',
	sum(eh.eh_duree) 'La Poste - Corporate'";
	$table_lp_corporate = "evenement e, evenement_participation ep, type_evenement te, section s, evenement_horaire eh, pompier p";
	$where_lp_corporate = " e.e_code = ep.e_code";
	$where_lp_corporate .= " and e.s_id = s.s_id";
	$where_lp_corporate .= " and ep.e_code = eh.e_code";
	$where_lp_corporate .= " and ep.eh_id = eh.eh_id";
	$where_lp_corporate .= (isset($list)?" and s.s_id in(".$list.")":"");
	$where_lp_corporate .= " and e.te_code = te.te_code";
	$where_lp_corporate .= " and $evenemententredeuxdate";	
	$where_lp_corporate .= " and e.E_CANCELED = 0";
	$where_lp_corporate .= " and ep.P_ID = p.P_ID";
	$where_lp_corporate .= " and p.C_ID = 13";
	$orderby_lp_corporate  = "s.s_id, s.S_DESCRIPTION ,e.te_code";
	$groupby_lp_corporate = "s.s_id,te.te_libelle";
	$SommeSur[2] =  "La Poste - Corporate";
	
	// Heures pour La Poste Colis
	
	$select_lp_colis = "s.S_CODE  'Section', 
	te.te_libelle 'Type événement',
	sum(eh.eh_duree) 'La Poste - Colis'";
	$table_lp_colis = "evenement e, evenement_participation ep, type_evenement te, section s, evenement_horaire eh, pompier p";
	$where_lp_colis = " e.e_code = ep.e_code";
	$where_lp_colis .= " and e.s_id = s.s_id";
	$where_lp_colis .= " and ep.e_code = eh.e_code";
	$where_lp_colis .= " and ep.eh_id = eh.eh_id";
	$where_lp_colis .= (isset($list)?" and s.s_id in(".$list.")":"");
	$where_lp_colis .= " and e.te_code = te.te_code";
	$where_lp_colis .= " and $evenemententredeuxdate";	
	$where_lp_colis .= " and e.E_CANCELED = 0";
	$where_lp_colis .= " and ep.P_ID = p.P_ID";
	$where_lp_colis .= " and p.C_ID = 11";
	$orderby_lp_colis  = "s.s_id, s.S_DESCRIPTION ,e.te_code";
	$groupby_lp_colis = "s.s_id,te.te_libelle";
	$SommeSur[3] = "La Poste - Colis";		
	
	// Heures pour La Poste Enseinge
	
	$select_lp_enseigne = "s.S_CODE  'Section', 
	te.te_libelle 'Type événement',
	sum(eh.eh_duree) 'La Poste - Enseigne'";
	$table_lp_enseigne = "evenement e, evenement_participation ep, type_evenement te, section s, evenement_horaire eh, pompier p";
	$where_lp_enseigne = " e.e_code = ep.e_code";
	$where_lp_enseigne .= " and e.s_id = s.s_id";
	$where_lp_enseigne .= " and ep.e_code = eh.e_code";
	$where_lp_enseigne .= " and ep.eh_id = eh.eh_id";
	$where_lp_enseigne .= (isset($list)?" and s.s_id in(".$list.")":"");
	$where_lp_enseigne .= " and e.te_code = te.te_code";
	$where_lp_enseigne .= " and $evenemententredeuxdate";	
	$where_lp_enseigne .= " and e.E_CANCELED = 0";
	$where_lp_enseigne .= " and ep.P_ID = p.P_ID";
	$where_lp_enseigne .= " and p.C_ID = 12";
	$orderby_lp_enseigne  = "s.s_id, s.S_DESCRIPTION ,e.te_code";
	$groupby_lp_enseigne = "s.s_id,te.te_libelle";
	$SommeSur[4] = "La Poste - Enseigne";	
	
	// Heures pour La Poste Services Financiers
	
	$select_lp_financier = "s.S_CODE  'Section', 
	te.te_libelle 'Type événement',
	sum(eh.eh_duree) 'La Poste - Services Financiers'";
	$table_lp_financier = "evenement e, evenement_participation ep, type_evenement te, section s, evenement_horaire eh, pompier p";
	$where_lp_financier = " e.e_code = ep.e_code";
	$where_lp_financier .= " and e.s_id = s.s_id";
	$where_lp_financier .= " and ep.e_code = eh.e_code";
	$where_lp_financier .= " and ep.eh_id = eh.eh_id";
	$where_lp_financier .= (isset($list)?" and s.s_id in(".$list.")":"");
	$where_lp_financier .= " and e.te_code = te.te_code";
	$where_lp_financier .= " and $evenemententredeuxdate";	
	$where_lp_financier .= " and e.E_CANCELED = 0";
	$where_lp_financier .= " and ep.P_ID = p.P_ID";
	$where_lp_financier .= " and p.C_ID = 14";
	$orderby_lp_financier  = "s.s_id, s.S_DESCRIPTION ,e.te_code";
	$groupby_lp_financier = "s.s_id,te.te_libelle";
	$SommeSur[5] = "La Poste - Services Financiers";
	
	// Heures pour La Poste Courrier
	
	$select_lp_courrier = "s.S_CODE  'Section', 
	te.te_libelle 'Type événement',
	sum(eh.eh_duree) 'La Poste - Courrier'";
	$table_lp_courrier = "evenement e, evenement_participation ep, type_evenement te, section s, evenement_horaire eh, pompier p";
	$where_lp_courrier = " e.e_code = ep.e_code";
	$where_lp_courrier .= " and e.s_id = s.s_id";
	$where_lp_courrier .= " and ep.e_code = eh.e_code";
	$where_lp_courrier .= " and ep.eh_id = eh.eh_id";
	$where_lp_courrier .= (isset($list)?" and s.s_id in(".$list.")":"");
	$where_lp_courrier .= " and e.te_code = te.te_code";
	$where_lp_courrier .= " and $evenemententredeuxdate";	
	$where_lp_courrier .= " and e.E_CANCELED = 0";
	$where_lp_courrier .= " and ep.P_ID = p.P_ID";
	$where_lp_courrier .= " and p.C_ID = 10";
	$orderby_lp_courrier  = "s.s_id, s.S_DESCRIPTION ,e.te_code";
	$groupby_lp_courrier = "s.s_id,te.te_libelle";	
	$SommeSur[6] = "La Poste - Courrier";

	// Heures pour les externes
	
	$select_autres = "s.S_CODE  'Section', 
	te.te_libelle 'Type événement',
	sum(eh.eh_duree) 'Autres'";
	$table_autres = "evenement e, evenement_participation ep, type_evenement te, section s, evenement_horaire eh, pompier p";
	$where_autres = " e.e_code = ep.e_code";
	$where_autres .= " and e.s_id = s.s_id";
	$where_autres .= " and ep.e_code = eh.e_code";
	$where_autres .= " and ep.eh_id = eh.eh_id";
	$where_autres .= (isset($list)?" and s.s_id in(".$list.")":"");
	$where_autres .= " and e.te_code = te.te_code";
	$where_autres .= " and $evenemententredeuxdate";	
	$where_autres .= " and e.E_CANCELED = 0";
	$where_autres .= " and ep.P_ID = p.P_ID";
	$where_autres .= " and p.C_ID NOT IN(10,11,12,13,14,109)";
	$orderby_autres  = "s.s_id, s.S_DESCRIPTION ,e.te_code";
	$groupby_autres = "s.s_id,te.te_libelle";
	$SommeSur[7] =  "Autres";
	//$RuptureSur =  array("Autres");

	break;
//-------------------
// heures / personne
//-------------------
	case "1heurespersonne":			
	$export_name = "Participations du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
	$select = "concat(upper(p.p_nom),' ',p.p_prenom) 'Personnel', 
	e.te_code 'Code', e.e_libelle 'Evenement',
	case
	when ep.ep_date_debut is null then date_format(eh.eh_date_debut,'%e-%c-%Y') 
	when ep.ep_date_debut is not null then date_format(ep.ep_date_debut,'%e-%c-%Y') 
	end
	as 'Début',
	case
	when ep.ep_debut is null then date_format(eh.eh_debut,'%H:%i') 
	when ep.ep_debut is not null then date_format(ep.ep_debut,'%H:%i') 
	end
	as 'à',
	case
	when ep.ep_date_fin is null then date_format(eh.eh_date_fin,'%e-%c-%Y')
	when ep.ep_date_fin is not null then date_format(ep.ep_date_fin,'%e-%c-%Y')
	end
	as  'Fin',
	case
	when ep.ep_fin is null then date_format(eh.eh_fin,'%H:%i')
	when ep.ep_fin is not null then date_format(ep.ep_fin,'%H:%i')
	end
	as  'à',
	case
	when ep.ep_duree is null then eh.eh_duree
	when ep.ep_duree is not null then ep.ep_duree
	end
	as 'Heures'
	";
	$table = "evenement e, evenement_participation ep, pompier p, evenement_horaire eh";
	$where = " e.e_code = ep.e_code ";
	$where .= " and ep.p_id = p.p_id ";
	$where .= " and ep.e_code = eh.e_code ";
	$where .= " and ep.eh_id = eh.eh_id ";
	$where .= " and $evenemententredeuxdate ";
	$where .= " and e.E_CANCELED = 0 and p.P_STATUT <> 'EXT'";
	$where .= (isset($list)?"  and p.p_section in(".$list.") ":"");
	$orderby  = "p.p_nom, p.p_prenom ,eh_date_debut";
	$groupby = "";
	$RuptureSur = array("Personnel");
	$SommeSur = array("Heures");	
	break;	
	default:
		echo "<p>Choisissez un format d'affichage...</p>";
		
//-------------------
// personnel inactif
//-------------------
	case "1inactif":			
	$export_name = "Personnel inactif du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
	$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a> ')  'NOM',
		p.p_prenom	'Prénom',	
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		s.s_code  'Section',
		case
		when q.q_expiration <= '".date("Y-m-d")."' then '<img src=images/red.gif title=cotisation-en-retard> <font color=red> en retard</font>'
		when q.q_expiration > '".date("Y-m-d")."' or q.q_expiration is null then '<img src=images/green.gif title=cotisation-à-jour> <font color=green> à jour</font>'
		end
		as 'Cotisation'
		";
		$table="pompier p, section s, qualification q, poste po";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 and p.P_STATUT <> 'EXT'\n";
		$where .= " and q.p_id = p.p_id and q.ps_id = po.ps_id \n";
		$where .= " and po.description = 'Cotisation' \n";
		$where .= " and not exists (select 1 from evenement_participation ep, evenement_horaire eh where ep.p_id = p.p_id \n";
		$where .= " and $evenemententredeuxdate "; 
		$where .= " and ep.e_code = eh.e_code ";
		$where .= " and ep.eh_id = eh.eh_id) ";
		$orderby= " p.p_nom, p.p_prenom";
		$groupby="";
		break;
		
//-------------------
// personnel inactif 2
//-------------------
	case "1inactif2":			
	$export_name = "Personnel inactif du ".str_replace('-','-',$dtdb).(($dtdbq!=$dtfnq)?" au ".str_replace('-','-',$dtfn):"")."";
	$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a> ')  'NOM',
		p.p_prenom	'Prénom',	
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		s.s_code  'Section'
		";
		$table="pompier p, section s";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id ";
		$where .= " and p.p_old_member = 0 and p.P_STATUT <> 'EXT'\n";
		$where .= " and not exists (select 1 from evenement_participation ep, evenement e where ep.p_id = p.p_id \n";
		$where .= " and $evenemententredeuxdate "; 
		$where .= " and ep.e_code = e.e_code) ";
		$orderby= " p.p_nom, p.p_prenom";
		$groupby="";
		break;
//-------------------
// cotisations
//-------------------
	case "cotisation":			
	$export_name = "Cotisations en retard le ".date("d")."-".date("n")."-".date("Y");
	$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a> ')  'NOM',
		p.p_prenom	'Prénom',	
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		s.s_code  'Section',
		concat('<font color=red>',DATE_FORMAT(q.Q_EXPIRATION, '%m / %Y'),'</font>') 'Expiration'
		";
		$table="pompier p, section s, qualification q, poste po";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id \n";
		$where .= " and p.p_old_member = 0 and p.P_STATUT <> 'EXT'\n";
		$where .= " and datediff(q.q_expiration,'".date("Y-m-d")."') <= 0 \n";
		$where .= " and q.p_id = p.p_id and q.ps_id = po.ps_id \n";
		$where .= " and po.description = 'Cotisation' \n";
		$orderby= " p.p_nom, p.p_prenom";
		$groupby="";
		break;
		
//-------------------
// compétences expirées
//-------------------
	case "competence_expire":			
	$export_name = "Compétences expirées le ".date("d")."-".date("n")."-".date("Y");
	$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a> ')  'NOM',
		p.p_prenom	'Prénom',
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',				
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		s.s_code  'Section',
		po.description  'Compétence',
		concat('<font color=red>',DATE_FORMAT(q.Q_EXPIRATION, '%m / %Y'),'</font>') 'Expiration'
		";
		$table="pompier p, section s, qualification q, poste po";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id \n";
		$where .= " and p.p_old_member= 0 and p.P_STATUT <> 'EXT'\n";
		$where .= " and datediff(q.q_expiration,'".date("Y-m-d")."') <= 0 \n";
		$where .= " and q.p_id = p.p_id and q.ps_id = po.ps_id \n";
		$where .= " and po.description <> 'Cotisation' and po.description <> 'Passeport'\n";
		$orderby= " p.p_nom, p.p_prenom";
		$groupby="";
		break;

//-------------------
// cotisations
//-------------------
	case "cotisation":			
	$export_name = "Cotisations en retard le ".date("d")."-".date("n")."-".date("Y");
	$select="concat('<a href=\"upd_personnel.php?from=export&pompier=',p.p_id,'\" target=_blank>',upper(p.p_nom),'</a> ')  'NOM',
		p.p_prenom	'Prénom',	
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'Tél',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('**********')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		when p.p_email is not null and p.p_hide = 0 then concat('<a href=''mailto:',p.p_email,''' target=''_self''>',p.p_email,'</a>') 
		end
		as 'Email',
		concat(s.s_code,' - ',s.s_description)  'Section',
		DATE_FORMAT(q.Q_EXPIRATION, '%m / %Y') 'Expiration'
		";
		$table="pompier p, section s, qualification q, poste po";
		$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
		$where .= " p.p_section = s.s_id \n";
		$where .= " and p.p_old_member = 0 and p.P_STATUT <> 'EXT'\n";
		$where .= " and datediff(q.q_expiration,'".date("Y-m-d")."') <= 0 \n";
		$where .= " and q.p_id = p.p_id and q.ps_id = po.ps_id \n";
		$where .= " and po.description = 'Cotisation' \n";
		$orderby= " p.p_nom, p.p_prenom";
		$groupby="";
		break;
		
//-------------------
// diplômes
//-------------------
	case "diplomesPSC1":			
	$export_name = "Diplômes PSC1 délivrés";
	$select = "pf.PF_DIPLOME ' Diplôme',
	pf.PF_DATE 'Date',
	concat('<a href=\"upd_personnel.php?from=export&pompier=',o.p_id,'\" target=_blank>',upper(o.p_nom),'</a>')  'Délivré à',
	o.p_prenom 'Prénom', 
	concat(s.s_code,' - ',s.s_description)  'Section',
	case 
	when o.p_statut = 'EXT' then '<font color=green>externe</font>'
	when o.p_old_member = 1 then '<font color=black>ancien</font>'
	else 'actif'
	end
	as 'Statut'
	";
	$table = "personnel_formation pf, poste p,  pompier o, section s ";	
	$where = " p.type like 'PSC%1' ";
	$where .= " and p.ps_id = pf.ps_id\n";
	$where .= " and o.p_id = pf.p_id\n";
	$where .= " and o.p_section = s.s_id\n";
	$where .= " and pf.PF_DIPLOME is not null\n";
	$where .= " and pf.PF_DIPLOME <> ''\n";
	$where .= (isset($list)?" and s.s_id in(".$list.") ":"");
	$orderby  = "pf.PF_DIPLOME";	
	break;
	
	case "diplomesPSE1":			
	$export_name = "Diplômes PSE1 délivrés";
	$select = "pf.PF_DIPLOME ' Diplôme',
	pf.PF_DATE 'Date',
	concat('<a href=\"upd_personnel.php?from=export&pompier=',o.p_id,'\" target=_blank>',upper(o.p_nom),'</a>')  'Délivré à',
	o.p_prenom 'Prénom', 
	concat(s.s_code,' - ',s.s_description)  'Section',
	case 
	when o.p_statut = 'EXT' then '<font color=green>externe</font>'
	when o.p_old_member = 1 then '<font color=black>ancien</font>'
	else 'actif'
	end
	as 'Statut'
	";
	$table = "personnel_formation pf, poste p,  pompier o, section s ";	
	$where = " p.type like 'PSE%1' ";
	$where .= " and p.ps_id = pf.ps_id\n";
	$where .= " and o.p_id = pf.p_id\n";
	$where .= " and o.p_section = s.s_id\n";
	$where .= " and pf.PF_DIPLOME is not null\n";
	$where .= " and pf.PF_DIPLOME <> ''\n";
	$where .= (isset($list)?" and s.s_id in(".$list.") ":"");
	$orderby  = "pf.PF_DIPLOME";	
	break;
	
	case "diplomesPSE2":			
	$export_name = "Diplômes PSE2 délivrés";
	$select = "pf.PF_DIPLOME ' Diplôme',
	pf.PF_DATE 'Date',
	concat('<a href=\"upd_personnel.php?from=export&pompier=',o.p_id,'\" target=_blank>',upper(o.p_nom),'</a>')  'Délivré à',
	o.p_prenom 'Prénom', 
	concat(s.s_code,' - ',s.s_description)  'Section',
	case 
	when o.p_statut = 'EXT' then '<font color=green>externe</font>'
	when o.p_old_member = 1 then '<font color=black>ancien</font>'
	else 'actif'
	end
	as 'Statut'
	";
	$table = "personnel_formation pf, poste p,  pompier o, section s ";	
	$where = " p.type like 'PSE%2' ";
	$where .= " and p.ps_id = pf.ps_id\n";
	$where .= " and o.p_id = pf.p_id\n";
	$where .= " and o.p_section = s.s_id\n";
	$where .= " and pf.PF_DIPLOME is not null\n";
	$where .= " and pf.PF_DIPLOME <> ''\n";
	$where .= (isset($list)?" and s.s_id in(".$list.") ":"");
	$orderby  = "pf.PF_DIPLOME";	
	break;
	
	
//-------------------
// secouristes
//-------------------
	case "secouristes":			
	$export_name = "Secouristes PSE -formation à jour- le ".date("d")."-".date("n")."-".date("Y");
	$select = "p.type 'Compétence',
	concat('<a href=\"upd_personnel.php?from=export&pompier=',o.p_id,'\" target=_blank>',upper(o.p_nom),'</a>')  'NOM',
	o.p_prenom 'Prénom', 
	q.q_expiration 'Expiration',
	concat(s.s_code,' - ',s.s_description)  'Section'";
	$table = "qualification q, poste p,  pompier o, section s ";	
	$where = " p.type like 'PSE%'";
	$where .= " and p.ps_id = q.ps_id\n";
	$where .= " and o.p_id = q.p_id\n";
	$where .= " and o.p_old_member = 0\n";
	$where .= " and o.p_statut <> 'EXT'\n";
	$where .= " and( q.q_expiration is null or q.q_expiration >= '".date("Y")."-".date("n")."-".date("d")."')";
	$where .= " and o.p_section = s.s_id\n";
	$where .= (isset($list)?" and s.s_id in(".$list.") ":"");
	$orderby  = "o.p_nom";	
	break;

//-------------------
// personnel de santé
//-------------------
	case "personnelsante":			
	$export_name = "Personnels de santé";
	$select = "p.description 'Compétence',
	concat('<a href=\"upd_personnel.php?from=export&pompier=',o.p_id,'\" target=_blank>',upper(o.p_nom),'</a>')  'NOM',
	o.p_prenom 'Prénom', 
	concat(s.s_code,' - ',s.s_description)  'Section'";
	$table = "qualification q, poste p,  pompier o, section s, equipe e ";	
	$where = " p.ps_id = q.ps_id\n";
	$where .= " and e.EQ_NOM='Personnels de Santé'\n";
	$where .= " and e.EQ_ID =p.EQ_ID\n";
	$where .= " and o.p_id = q.p_id\n";
	$where .= " and o.p_old_member = 0\n";
	$where .= " and o.p_statut <> 'EXT'\n";
	$where .= " and o.p_section = s.s_id\n";
	$where .= (isset($list)?" and s.s_id in(".$list.") ":"");
	$orderby  = "p.type";	
	break;

//-------------------
// Nombre de secouristes
//-------------------	
	case "secouristesparsection":			
	$export_name = "Secouristes -formation à jour- par section le ".date("d")."-".date("n")."-".date("Y");
	$select = "p.type 'Compétence',
	concat(s.s_code,' - ',s.s_description)  'Section',
	count(*) 'Nombre'";
	$table = "qualification q, poste p,  pompier o, section s ";	
	$where = " p.type like 'PSE%' ";
	$where .= " and p.ps_id = q.ps_id\n";
	$where .= " and o.p_id = q.p_id\n";
	$where .= " and o.p_section = s.s_id\n";
	$where .= " and o.p_old_member = 0\n";
	$where .= " and o.p_statut <> 'EXT'\n";
	$where .= " and( q.q_expiration is null or q.q_expiration >= '".date("Y")."-".date("n")."-".date("d")."')\n";
	$where .= (isset($list)?" and s.s_id in(".$list.") ":"");
	$orderby  = "";
	$groupby = "p.type,s.s_code";
	$RuptureSur = array("Compétence");
	$SommeSur = array("Nombre");	
	break;

//-------------------
// moniteurs
//-------------------
	case "moniteurs":			
	$export_name = "Moniteurs - formation à jour- le ".date("d")."-".date("n")."-".date("Y");
	$select = "p.type 'Compétence',
	concat('<a href=\"upd_personnel.php?from=export&pompier=',o.p_id,'\" target=_blank>',upper(o.p_nom),'</a>')  'NOM',
	o.p_prenom 'Prénom', 
	q.q_expiration 'Expiration',
	concat(s.s_code,' - ',s.s_description)  'Section'";
	$table = "qualification q, poste p,  pompier o, section s ";	
	$where = " ( p.type like 'PAE%' or p.type like '%SST%') ";
	$where .= " and p.type <> 'SST'\n";
	$where .= " and p.ps_id = q.ps_id\n";
	$where .= " and o.p_id = q.p_id\n";
	$where .= " and o.p_old_member = 0\n";
	$where .= " and o.p_statut <> 'EXT'\n";
	$where .= " and( q.q_expiration is null or q.q_expiration >= '".date("Y")."-".date("n")."-".date("d")."')";
	$where .= " and o.p_section = s.s_id\n";
	$where .= (isset($list)?" and s.s_id in(".$list.") ":"");
	$orderby  = "p.type, s.s_code, o.p_nom";
	break;


//-------------------
// Nombre de secouristes
//-------------------	
	case "moniteursparsection":			
	$export_name = "Moniteurs par section - formation à jour- le ".date("d")."-".date("n")."-".date("Y");
	$select = "p.type 'Compétence',
	concat(s.s_code,' - ',s.s_description)  'Section',
	count(*) 'Nombre'";
	$table = "qualification q, poste p,  pompier o, section s ";	
	$where = " ( p.type like 'PAE%' or p.type like '%SST%') ";
	$where .= " and p.type <> 'SST'\n";
	$where .= " and p.ps_id = q.ps_id\n";
	$where .= " and o.p_id = q.p_id\n";
	$where .= " and o.p_section = s.s_id\n";
	$where .= " and o.p_old_member = 0\n";
	$where .= " and o.p_statut <> 'EXT'\n";
	$where .= " and( q.q_expiration is null or q.q_expiration >= '".date("Y")."-".date("n")."-".date("d")."')\n";
	$where .= (isset($list)?" and s.s_id in(".$list.") ":"");
	$orderby  = "";
	$groupby = "p.type,s.s_code";
	$RuptureSur = array("Compétence");
	$SommeSur = array("Nombre");	
	break;
	}
}
?>
