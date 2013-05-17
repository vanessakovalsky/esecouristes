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
check_all(41);
$evenement=intval($_GET["evenement"]);
$id=$_SESSION['id'];

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="fiche-de-poste.xls"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header("Expires: 0");
$charset="ISO-8859-15";



echo  "<html>";
echo  "<head>
<meta http-equiv=\"Content-type\" content=\"text/html;charset=".$charset."\" />
<style id=\"Classeur1_16681_Styles\"></style>
<style type=\"text/css\">";
echo  "</style>
</head>
<body>
<div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\">";
echo  "\n"."<table x:num border=1 cellpadding=0 cellspacing=0 width=100% style=\"border-collapse: collapse\">";

//-----------------------------
// infos générales
//-----------------------------
$query="select EH.EH_ID, E.E_CODE, E.S_ID,E.TE_CODE, TE.TE_LIBELLE, E.E_LIEU, EH.EH_DATE_DEBUT,EH.EH_DATE_FIN,
        TIME_FORMAT(EH.EH_DEBUT, '%k:%i') as EH_DEBUT, E.E_NB1, E.E_NB2, S.S_CODE, E.E_CHEF,
		TIME_FORMAT(EH.EH_FIN, '%k:%i') as EH_FIN, E.E_MAIL1, E.E_MAIL2, E.E_MAIL3, E.E_OPEN_TO_EXT,
		E.E_NB, E.E_COMMENT, E.E_LIBELLE, S.S_DESCRIPTION, E.E_CLOSED, E.E_CANCELED, E.E_CANCEL_DETAIL,
		E.E_CONVENTION, E.C_ID, E.E_CONTACT_LOCAL, E.E_CONTACT_TEL, EH.EH_DUREE
        from evenement E, type_evenement TE, section S, evenement_horaire EH
		where E.TE_CODE=TE.TE_CODE
		and EH.E_CODE = E.E_CODE
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
$E_DUREE_TOTALE = 0;
$i=1;
while ($row=mysql_fetch_array($result)) {
    if ( $i == 1 ) {
       $E_CODE=$row["E_CODE"];
       $S_ID=$row["S_ID"];
       $E_CHEF=$row["E_CHEF"];
       $S_CODE=$row["S_CODE"];
       $S_DESCRIPTION=$row["S_DESCRIPTION"];
       $TE_CODE=$row["TE_CODE"];
       $E_LIBELLE=$row["E_LIBELLE"];
       $TE_LIBELLE=$row["TE_LIBELLE"];
       $E_LIEU=$row["E_LIEU"];
       $E_MAIL1=$row["E_MAIL1"];
       $E_MAIL2=$row["E_MAIL2"];
       $E_MAIL3=$row["E_MAIL3"];
       $E_NB=$row["E_NB"];
       $E_NB1=$row["E_NB1"];
       $E_NB2=$row["E_NB2"];
       $C_ID=$row["C_ID"];
       $E_CONTACT_LOCAL=$row["E_CONTACT_LOCAL"];
       $E_CONTACT_TEL=$row["E_CONTACT_TEL"];
       $E_COMMENT=$row["E_COMMENT"];
       $E_CLOSED=$row["E_CLOSED"];
       $E_CANCELED=$row["E_CANCELED"];
       $E_CANCEL_DETAIL=$row["E_CANCEL_DETAIL"];
       $E_CONVENTION=$row["E_CONVENTION"];
       $E_OPEN_TO_EXT=$row["E_OPEN_TO_EXT"];
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
$last=$i-1;
$nbsessions=sizeof($EH_ID);
	   
$organisateur= $S_ID;     
$query="select count(distinct P_ID) as NP from evenement_participation where E_CODE=".$evenement;
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$NP=$row["NP"];
$ischef=is_chef($id,$S_ID);
 
echo "<tr>
		<td colspan=8><font size=4><b>".$E_LIBELLE."</font> </b></td>
		</tr>";
     
if ( $nbsections <> 1 ) {
echo "<tr><td><b>Organisateur: </b></td>
   	 <td colspan=7>".$S_CODE." - ".get_section_name("$organisateur")."</td></tr>";
}
if ( $E_CHEF <> '' ) {
	echo "<tr><td><b>Responsable: </b></td>
   	 <td colspan=7>".ucfirst(get_prenom($E_CHEF))." ".strtoupper(get_nom($E_CHEF))." ".get_phone($E_CHEF)."</td></tr>";
}
if ( $C_ID <> '' ) {
	echo "<tr><td><b>Pour le compte de: </b></td>
   	 <td colspan=7>".get_company_name("$C_ID")."</td></tr>";
}
if ( $E_CONTACT_LOCAL <> '' or  $E_CONTACT_TEL <> '' ) {
	echo "<tr><td><b>Contact local: </b></td>
   	 <td colspan=7>".$E_CONTACT_LOCAL." ".$E_CONTACT_TEL."</td></tr>";
}
echo "<tr><td><b>Lieu: </b></td>
   	 <td colspan=7> ".$E_LIEU."</td></tr>";
echo "<tr><td><b>Début: </b></td>
   	 <td colspan=7> '".$EH_DEBUT[1]."</td></tr>";	
echo "<tr><td><b>Fin: </b></td>
   	 <td colspan=7> '".$EH_FIN[$last]."</td></tr>";
if($E_DUREE_TOTALE!=''){	 
echo "<tr><td><b>Durée effective: </b></td>
   	 <td colspan=7> ".$E_DUREE_TOTALE." heures</td></tr>";
}	 

for ($i=1; $i <= $nbmaxsessionsparevenement; $i++) {
    if ( $nbsessions == 1 ) $t="Dates et heures";
    else if (isset($EH_ID[$i])) $t="Date Partie ".$EH_ID[$i];
	if ( isset($horaire_evt[$i]))
		echo "<tr><td><b>".$t.": </b></td>
   	 	<td colspan=7> ".$horaire_evt[$i]."
	 	</td></tr>";
}

if ( $E_CONVENTION <> "" ) {
	echo "<tr><td><b>Numéro de convention: </b></td>
   	 <td colspan=7> ".$E_CONVENTION."</td></tr>";
}	

if ( $E_COMMENT <> "" ) {
	echo "<tr><td ><b>Détails: </b></td>
   	 <td colspan=7> ".$E_COMMENT."</td></tr>";	    
}	

//------------------------------
// personnel
//------------------------------

$query="select e.E_CODE as EC, p.P_ID,p.P_NOM,p.P_PHONE, p.P_PRENOM, p.P_GRADE, s.S_ID, p.P_HIDE,
		DATE_FORMAT(ep.EP_DATE, '%d/%m %H:%i') as EP_DATE , s.S_CODE,
		EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),p.P_BIRTHDATE))))+0 AS age,
		TIME_FORMAT(eh.EH_DEBUT, '%k:%i') as EH_DEBUT, 
		TIME_FORMAT(eh.EH_FIN, '%k:%i') as EH_FIN,
        DATE_FORMAT(eh.EH_DATE_DEBUT, '%d/%m') as EH_DATE_DEBUT,
        DATE_FORMAT(eh.EH_DATE_FIN, '%d/%m') as EH_DATE_FIN,
        TIME_FORMAT(ep.EP_DEBUT, '%k:%i') as EP_DEBUT, TIME_FORMAT(ep.EP_FIN, '%k:%i') as EP_FIN,
        DATE_FORMAT(ep.EP_DATE_DEBUT, '%d/%m') as EP_DATE_DEBUT,
        DATE_FORMAT(ep.EP_DATE_FIN, '%d/%m') as EP_DATE_FIN,
        ep.EH_ID
		from evenement_participation ep, evenement e, pompier p, section s, evenement_horaire eh
        where ( e.E_CODE=".$evenement." or e.E_PARENT = ".$evenement.")
        and e.E_CODE = ep.E_CODE
        and ep.E_CODE = eh.E_CODE
        and ep.EH_ID = eh.EH_ID
		and p.P_ID=ep.P_ID
		and p.P_SECTION=s.S_ID
		order by eh.E_CODE asc, p.P_NOM, eh.EH_ID";
$result=mysql_query($query);
$listePompiers = "";
$k=0;$prevpid=0;
if ( mysql_num_rows($result) > 0 ) {
	$prevEC=$evenement;
	echo "<tr><td><b>Personnel inscrit:</b>";
	echo "<td><font size=1 ><i>téléphone</i></font></td>";
	echo "<td><font size=1 ><i>section</i></font></td>";
	echo "<td><font size=1 ><i>fonction</i></font></td>";
	echo "<td><font size=1 ><i>équipe</i></font></td>";
	echo "<td><font size=1 ><i>commentaire</i></font></td>";
	echo "<td><font size=1 ><i>compétences valides</i></font></td>";
	echo "<td><font size=1 ><i>horaires</i></font></td>";
	while ($row=@mysql_fetch_array($result)) {
	  $EC=$row["EC"]; 
	  // affiche d'où vient le renfort
	  if ( $EC <> $prevEC ) {
	    $prevpid=0;
	    $queryR="select e.E_CANCELED as CE_CANCELED, e.E_CLOSED as CE_CLOSED,
			s.S_CODE CS_CODE, s.S_DESCRIPTION CS_DESCRIPTION
	        from evenement e, section s
			where e.S_ID = s.S_ID
			and e.E_CODE=".$EC;
		$resultR=mysql_query($queryR);
		$rowR=@mysql_fetch_array($resultR);
		$CS_CODE=$rowR["CS_CODE"];
		$CS_DESCRIPTION=$rowR["CS_DESCRIPTION"];
	  	echo "<tr><td><i>Renfort de ".$CS_CODE." - ".$CS_DESCRIPTION."</i></td>";
	  	if ( $k > 0 ) {
			echo "<td><font size=1 ><i>téléphone</i></font></td>";
			echo "<td><font size=1 ><i>section</i></font></td>";
			echo "<td><font size=1 ><i>fonction</i></font></td>";
			echo "<td><font size=1 ><i>équipe</i></font></td>";
			echo "<td><font size=1 ><i>commentaire</i></font></td>";
			echo "<td><font size=1 ><i>compétences valides</i></font></td>";
			echo "<td><font size=1 ><i>horaires</i></font></td>";
		}
	  	$prevEC = $EC;
	  }
	  
	  $k++;
      $P_NOM=$row["P_NOM"]; 
      $P_PRENOM=$row["P_PRENOM"]; 
      $P_ID=$row["P_ID"];
      $S_CODE=$row["S_CODE"];
      $S_ID=$row["S_ID"]; 
      $AGE=$row["age"];
      $P_HIDE=$row["P_HIDE"]; 
      $S_CODE=$row["S_CODE"];
 
      $EH_DATE_DEBUT=$row["EH_DATE_DEBUT"];
      $EH_DATE_FIN=$row["EH_DATE_FIN"];
      $EH_DEBUT=$row["EH_DEBUT"];
      $EH_FIN=$row["EH_FIN"];
	  if ( $EH_FIN == '' ) $EH_FIN=$EH_DEBUT;    
      $EP_DATE_DEBUT=$row["EP_DATE_DEBUT"];
      $EP_DATE_FIN=$row["EP_DATE_FIN"];
      $EP_DEBUT=$row["EP_DEBUT"];
      $EP_FIN=$row["EP_FIN"];
      
      if ( $row["P_PHONE"] <> '' ) {
	   		$P_PHONE=$row["P_PHONE"];	
			if (( ($P_HIDE == 1) ) and ( $nbsections == 0 )) {
	  			if (( ! $ischef ) 
				and ( $E_CHEF <> $id )
				and (! check_rights($_SESSION['id'], 2)))
	  				$P_PHONE="**********";
	  		}
	  }
	  else $P_PHONE="";
	  $listePompiers .= $P_ID.","; 	  
	  
	  if ( $EP_DATE_DEBUT <> "" ) {
           	if ( $EP_DATE_DEBUT == $EP_DATE_FIN ) {
           			$horaire= $EP_DATE_DEBUT.", ".$EP_DEBUT."-".$EP_FIN;
           	}
           	else
           		$horaire= $EP_DATE_DEBUT." au ".$EP_DATE_FIN.", ".$EP_DEBUT."-".$EP_FIN;
	  }
	  else {
	  		if ( $EH_DATE_DEBUT == $EH_DATE_FIN )
           		$horaire= $EH_DATE_DEBUT.", ".$EH_DEBUT."-".$EH_FIN;
           	else
           		$horaire= $EH_DATE_DEBUT." au ".$EH_DATE_FIN.", ".$EH_DEBUT."-".$EH_FIN;       		
	  }
	  
     if ( $P_ID <> $prevpid) {
	  $NewPID=true;
	  $query1="select count(*) as NB from evenement_participation 
	           where P_ID=".$P_ID." and E_CODE=".$EC;
	  $result1=mysql_query($query1);
	  $row1=@mysql_fetch_array($result1);
	  $n1=$row1["NB"];
	  
      $P_GRADE=$row["P_GRADE"];
      $EP_DATE=$row["EP_DATE"];	  
      $nb=1;
      $queryf="select tp.TP_ID, tp.TP_LIBELLE from evenement_participation ep, type_participation tp
      		   where ep.TP_ID= tp.TP_ID
      		   and ep.P_ID=".$P_ID."
      		   and ep.E_CODE=".$EC;
      $resultf=mysql_query($queryf);
      $rowf=@mysql_fetch_array($resultf);
      $fn = $rowf["TP_LIBELLE"];

      $queryc="select EP_COMMENT from evenement_participation
      		   where P_ID=".$P_ID."
      		   and E_CODE=".$EC;
      $resultc=mysql_query($queryc);
      $rowc=@mysql_fetch_array($resultc);
      $comment = $rowc["EP_COMMENT"];
      
      $querye="select ee.EE_NAME, ee.EE_DESCRIPTION 
	           from evenement_equipe ee, evenement_participation ep
      		   where ep.P_ID=".$P_ID."
      		   and ep.E_CODE=".$EC."
			   and ee.E_CODE = ".$evenement."
			   and ep.EE_ID = ee.EE_ID";
      $resulte=mysql_query($querye);
      $rowe=@mysql_fetch_array($resulte);
      $eename = $rowe["EE_NAME"];
      $eedesc = substr($rowe["EE_DESCRIPTION"],0,45);
      
      $postes="";
      $querys="select p.TYPE
	  			from poste p, qualification q, equipe e, categorie_evenement_affichage cea
      			where q.PS_ID=p.PS_ID
      			and cea.EQ_ID = e.EQ_ID
      			and cea.CEV_CODE = (select CEV_CODE from type_evenement where TE_CODE='".$TE_CODE."')
      			and cea.FLAG1 = 1
      			and e.EQ_ID = p.EQ_ID
      			and e.EQ_TYPE='COMPETENCE'
      			and ( DATEDIFF(q.Q_EXPIRATION,NOW()) >= 0  or q.Q_EXPIRATION is null ) 
      			and q.P_ID=".$P_ID;
      $results=mysql_query($querys);
      $max=mysql_num_rows($results);
      while ($rows=@mysql_fetch_array($results)) {
		   $postes .= $rows["TYPE"]; 
		   if ( $nb <  $max )  $postes .= " , ";
		   $nb++;
      } 
      $cmt='';
	  if ( $AGE <> '' )
	  	if ($AGE < 18 ) 
		  $cmt="<font color=red>(-18)</font>";
	
	  $prevpid=$P_ID;	  
	  }
	  else $NewPID=false;
      echo "<tr>";
      if ( $NewPID ) {
	  	echo "<td rowspan=$n1>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)." ".$cmt."</td>
      	<td rowspan=$n1>'".$P_PHONE."</td>
      	<td rowspan=$n1>'".$S_CODE."</td>
		<td rowspan=$n1>".$fn."</td>
		<td rowspan=$n1>".$eename." ".$eedesc."</td>
		<td rowspan=$n1>".$comment."</td>
		<td rowspan=$n1>".$postes."</td>
		<td>".$horaire."</td>";
      }
      else 	
	  	echo "<td>".$horaire."</td>";
      echo "</tr>";
  }
}

//------------------------------
// véhicules
//------------------------------

if ( $vehicules == 1 ) {
$query="select distinct e.E_CODE as EC,v.V_ID,v.V_IMMATRICULATION,v.TV_CODE, vp.VP_LIBELLE, v.V_MODELE, 
	    vp.VP_ID, vp.VP_OPERATIONNEL, s.S_DESCRIPTION, s.S_ID, s.S_CODE, ev.EV_KM, v.V_INDICATIF,
		DATE_FORMAT(v.V_ASS_DATE, '%d-%m-%Y') as V_ASS_DATE,
		DATE_FORMAT(v.V_CT_DATE, '%d-%m-%Y') as V_CT_DATE,
		DATE_FORMAT(v.V_REV_DATE, '%d-%m-%Y') as V_REV_DATE,
		v.V_INDICATIF,v.V_COMMENT
        from evenement_vehicule ev, vehicule v, vehicule_position vp, section s, evenement e
        where v.V_ID=ev.V_ID
        and ev.E_CODE = e.E_CODE
        and s.S_ID=v.S_ID
        and vp.VP_ID=v.VP_ID
        and ( e.E_CODE=".$evenement." or e.E_PARENT = ".$evenement.")";


$result=mysql_query($query);
$nbvehic=mysql_num_rows($result);
$prevEC=$evenement; $k=0;
if ( $nbvehic > 0 ) {
	echo "<tr><td><b>Véhicules engagés:</b>";
	echo "<td><font size=1 ><i>indicatif</i></font></td>";
	echo "<td><font size=1 ><i>section</i></font></td>";
	echo "<td><font size=1 ><i>immatriculation</i></font></td>";
	echo "<td><font size=1 ><i>position</i></font></td>";
	echo "<td><font size=1 ><i>km</i></font></td>";
	echo "<td colspan=2><font size=1 ><i>commentaire</i></font></td>";
	
   while ($row=@mysql_fetch_array($result)) {
   
      $V_ID=$row["V_ID"];
      $S_ID=$row["S_ID"];
      $S_CODE=$row["S_CODE"];
      $S_DESCRIPTION=$row["S_DESCRIPTION"];
      $V_MODELE=$row["V_MODELE"];  
      $EV_KM=$row["EV_KM"];
      $V_IMMATRICULATION=$row["V_IMMATRICULATION"]; 
      $TV_CODE=$row["TV_CODE"];
	  $V_ASS_DATE=$row["V_ASS_DATE"];
	  $V_CT_DATE=$row["V_CT_DATE"];
	  $V_REV_DATE=$row["V_REV_DATE"];
      $VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
      $VP_ID=$row["VP_ID"];  
      $VP_LIBELLE=$row["VP_LIBELLE"];
      $V_INDICATIF=$row["V_INDICATIF"];
      $V_COMMENT=$row["V_COMMENT"];
      $EC=$row["EC"];
       
	   // affiche d'où vient le renfort
	  if ( $EC <> $prevEC ) {
	    $queryR="select e.E_CANCELED as CE_CANCELED, e.E_CLOSED as CE_CLOSED,
			s.S_CODE CS_CODE, s.S_DESCRIPTION CS_DESCRIPTION
	        from evenement e, section s
			where e.S_ID = s.S_ID
			and e.E_CODE=".$EC;
		$resultR=mysql_query($queryR);
		$rowR=@mysql_fetch_array($resultR);
		$CS_CODE=$rowR["CS_CODE"];
		$CS_DESCRIPTION=$rowR["CS_DESCRIPTION"];
	  	echo "<tr><td><i>Renfort de ".$CS_CODE." - ".$CS_DESCRIPTION."</i></td>";
	  	if ( $k > 0 ) {
	  		echo "<td><font size=1><i>indicatif</i></font></td>";
	  		echo "<td><font size=1><i>section</i></font></td>";
	  		echo "<td><font size=1><i>immatriculation</i></font></td>";
	  		echo "<td><font size=1><i>position</i></font></td>";
	  		echo "<td><font size=1><i>km</i></font></td>";
	  		echo "<td colspan=2><font size=1><i>commentaire</i></font></td>";
	  	}
	  	$prevEC = $EC;
	  }   
	  if ( my_date_diff(getnow(),$V_ASS_DATE) < 0 ) {
	  		$VP_LIBELLE = "assurance périmée";
	  }
	  else if ( my_date_diff(getnow(),$V_CT_DATE) < 0 ) {
	  		$VP_LIBELLE = "CT périmé";	  
	  }
	  else if (( my_date_diff(getnow(),$V_REV_DATE) < 0 ) and ( $VP_OPERATIONNEL <> 1)) {
		$VP_LIBELLE = "révision à faire";
	  }
      $k++;
      echo "<tr><td>".$TV_CODE." - ".$V_MODELE."</td>
      <td>".$V_INDICATIF."</td>
      <td>'".$S_CODE."</td>
	  <td>'".$V_IMMATRICULATION."</td>
	  <td>".$VP_LIBELLE."</td>
	  ";
      if ( $EV_KM == '' )  $EV_KM = 0;
	  echo "<td>'$EV_KM</td>";
	  echo "<td colspan=2>".$V_COMMENT."</td>";
	}
  }
}


//------------------------------
// matériel
//------------------------------

if ( $materiel == 1 ) {
$query="select e.E_CODE as EC, m.MA_ID, tm.TM_CODE, m.TM_ID, vp.VP_LIBELLE, m.MA_MODELE, m.MA_NUMERO_SERIE,
	    vp.VP_ID, vp.VP_OPERATIONNEL, s.S_DESCRIPTION, s.S_ID, s.S_CODE, em.EM_NB, m.MA_NB,
	    cm.TM_USAGE, cm.PICTURE_SMALL, cm.CM_DESCRIPTION,
	    DATE_FORMAT(m.MA_REV_DATE, '%d-%m-%Y') as MA_REV_DATE
        from evenement_materiel em, materiel m, vehicule_position vp, section s, 
		type_materiel tm, categorie_materiel cm, evenement e
        where m.MA_ID=em.MA_ID
        and e.E_CODE=em.E_CODE
        and cm.TM_USAGE=tm.TM_USAGE
        and tm.TM_ID = m.TM_ID
        and s.S_ID=m.S_ID
        and vp.VP_ID=m.VP_ID
        and ( e.E_CODE=".$evenement." or e.E_PARENT = ".$evenement.")
		order by e.E_CODE, cm.TM_USAGE";

  $result=mysql_query($query);
  $nbmat=mysql_num_rows($result);
  $prevEC=$evenement; $k=0;
  if ( $nbmat > 0 ) {
   	echo "<tr><td><b>Matériel engagés:</b><br>";
	echo "<td><font size=1 ><i>modèle</i></font></td>";
	echo "<td><font size=1 ><i>section</i></font></td>";
	echo "<td><font size=1 ><i>position</i></font></td>";
	echo "<td><font size=1 ><i>nombre</i></font></td>";
	echo "<td><font size=1 ></font></td>";
	echo "<td><font size=1 ></font></td>";
	   
   while ($row=@mysql_fetch_array($result)) {
      $EC=$row["EC"];
      $MA_ID=$row["MA_ID"];
      $MA_NB=$row["MA_NB"];
      $EM_NB=$row["EM_NB"];
      $S_ID=$row["S_ID"];
      $S_CODE=$row["S_CODE"];
      $TM_USAGE=$row["TM_USAGE"];
      $MA_REV_DATE=$row["MA_REV_DATE"];
      $CM_DESCRIPTION=$row["CM_DESCRIPTION"];
      $PICTURE_SMALL=$row["PICTURE_SMALL"];
      $S_DESCRIPTION=$row["S_DESCRIPTION"];
      $MA_MODELE=$row["MA_MODELE"];  
      $MA_NUMERO_SERIE=$row["MA_NUMERO_SERIE"]; 
      $TM_CODE=$row["TM_CODE"];
      $VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
      $VP_ID=$row["VP_ID"];  
      $VP_LIBELLE=$row["VP_LIBELLE"]; 
	  $CM_DESCRIPTION;
      
      // affiche d'où vient le renfort
	  if ( $EC <> $prevEC ) {
	    $queryR="select e.E_CANCELED as CE_CANCELED, e.E_CLOSED as CE_CLOSED,
			s.S_CODE CS_CODE, s.S_DESCRIPTION CS_DESCRIPTION
	        from evenement e, section s
			where e.S_ID = s.S_ID
			and e.E_CODE=".$EC;
		$resultR=mysql_query($queryR);
		$rowR=@mysql_fetch_array($resultR);
		$CS_CODE=$rowR["CS_CODE"];
		$CS_DESCRIPTION=$rowR["CS_DESCRIPTION"];
	  	echo "<tr><td><i>Renfort de ".$CS_CODE." - ".$CS_DESCRIPTION."</i></td>";
	  	if ( $k > 0 ) {
	  		echo "<td><font size=1 ><i>modèle</i></font></td>";
	  		echo "<td><font size=1 ><i>section</i></font></td>";
	  		echo "<td><font size=1 ><i>position</i></font></td>";
	  		echo "<td><font size=1 ><i>nombre</i></font></td>";
	  		echo "<td><font size=1 ></font></td>";
	  		echo "<td><font size=1 ></font></td>";
	  	}
	  	$prevEC = $EC;
	  	$prevTM_USAGE='';
	  }
      $k++;
      
      if (( my_date_diff(getnow(),$MA_REV_DATE) < 0 ) and ( $VP_OPERATIONNEL <> 1)) {
		$VP_LIBELLE = "date dépassée";
	  }
      
	  echo "<tr><td>".$TM_USAGE." - ".$TM_CODE."</td>
	  <td>'".$MA_MODELE." - ".$MA_NUMERO_SERIE."</td>
      <td>'".$S_CODE."</td>
	  <td>".$VP_LIBELLE."</td>
	  <td>'".$EM_NB."</td>
	  <td></td>
	  <td></td>
	  </tr>";
   }
 }
}
echo "</table>";
?>
