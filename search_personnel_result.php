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
header("Cache-Control: no-cache");
echo "<html>";
$ok=false;
$show_num=false;
$show_address=false;
include_once ("config.php");
check_all(40);
$id=$_SESSION['id'];

$query="";
$response="";
// DEB EMAIL
if ( check_rights($id, 43))
$envoisEmail=true;//(($nbsections==0)?true:false);
else
$envoisEmail=false;

$frmEmailDeb="";
$frmEmailFin="";
if ($envoisEmail) {   
$frmEmailDeb = "
";
$frmEmailDeb .= "<form name=\"frmPersonnel\" id=\"frmPersonnel\" method=\"post\" action=\"mail_create.php\">";
$frmEmailDeb .= "<input type=hidden name=SendMail id=SendMail value=\"0\" />";
$frmEmailDeb .= "<input type=\"button\" onclick=\"SendMailTo('frmPersonnel','SendMail','Vous devez sélectionner au moins un destinataire !');\" value=\"Message\" title=\"envoyer un message à partir de cette application\">";

if ( check_rights($_SESSION['id'], 2)) {	
	$frmEmailDeb .= " <input type=\"button\" onclick=\"DirectMailTo('frmPersonnel','SendMail','Vous devez sélectionner au moins un destinataire !','mail');\" 
						value=\"Mailto\" title=\"envoyer un 	message avec votre logiciel de messagerie\">";	
	$frmEmailDeb .= " <input type=\"button\" onclick=\"SendMailTo('frmPersonnel','SendMail','Vous devez sélectionner au moins un destinataire !','listemails');\" 
						value=\"Listemails\" title=\"Récupérer la liste des adresses email\">";	
}
$frmEmailDeb .= "<input type=\"hidden\" name=\"SelectionMail\" id=\"SelectionMail\">";
$frmEmailDeb .= " Tout cocher <input type=\"checkbox\" name=\"CheckAll\" id=\"CheckAll\" onclick=\"checkAll(document.frmPersonnel.SendMail,this.checked);\" >";
$frmEmailFin = "</form>";
}
// FIN EMAIL

$badletters  = array("é","è","ê","ë","à","ç","ï","ü");
$goodletters = array("e","e","e","e","a","c","i","u"); 

$section=(isset($_POST['section'])?$_POST['section']:"0");
if ( $section == "undefined" ) $section = 0;
// type de recherche
$q="0";
$h="";
$critere=(isset($_POST['typetri'])?$_POST['typetri']:"et");

// recherche  autre
$numero="";
$trouve = (isset($_POST['trouve'])?$_POST['trouve']:"");

// DEBUT PAGE
writehead();
echo "
<script type=\"text/javascript\">
function displaymanager(p1){
	 self.location.href=\"upd_personnel.php?pompier=\"+p1;
	 return true
}
</script>
<style type=\"text/css\" media=\"screen,projection\" >
.tablesorter th{
color:white;
}</style>
<style type=\"text/css\" media=\"print\" >@import url('export-print.css');</style>";
echo "</head>
<body>";

// permission de voir les externes?
if ( check_rights($_SESSION['id'], 37)) $externe=true;
else  $externe=false;

switch($critere){
 
//======================
// tel
//======================
case "tel":
	$show_num=true;
    $envoisEmail=false;
	$ok=(strlen($trouve)>=3?true:false);
	$query ="select distinct p_id 'ID', p_nom 'NOM', P_PRENOM 'prenom', S_CODE 'section', 
			 numero 'numero', ancien 'ancien', p_statut ";
	$query .= "FROM (
select p_id, upper(p.p_nom) p_nom , p_prenom , concat(s.s_code,' - ',s.s_description) 'S_CODE', p.p_phone 'numero', 
p.p_old_member 'ancien', p.p_statut
from pompier p, section s
where p_phone like '$trouve%'
and p_hide!=1
and p.P_SECTION = s.S_ID
union
select p_id, upper(p.p_nom) p_nom , p_prenom , concat(s.s_code,' - ',s.s_description) 'S_CODE', p.p_phone2 'numero', 
p.p_old_member 'ancien', p.p_statut
from pompier p, section s
where p_phone2 like '$trouve%'
and p.P_SECTION = s.S_ID
and p_hide!=1
union
select '', s_code, s_description, concat(s_code,' - ',s_description) 'S_CODE', s_phone, 0 'ancien', 'BEN' 'p_statut'
from section 
where s_phone like '$trouve%'
) listetel";
if ( !$externe ) $query .= " where p_statut <> 'EXT'";
$query .= " order by numero, p_nom, p_prenom";	
	break;
	
//======================
// Nom
//======================
case "nom":
	if (check_rights($_SESSION['id'],2)) $show_num=true;
	$envoisEmail=false;
	$ok=(strlen($trouve)>0?true:false);
	$query ="select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_NOM 'NOM', p.p_statut,
			p.P_PRENOM 'prenom', concat(s_code,' - ',s_description) 'section' ,
			p.P_EMAIL 'Email', p.P_PHONE 'numero',
			p.p_old_member 'ancien'";
	$query .="	from pompier p, section s
		where p.P_SECTION=s.S_ID
		and p.p_nom like '$trouve%'
		";	
	if ( !$externe ) $query .= " and p.p_statut <> 'EXT'";
	$query.=" order by p.P_NOM, p.P_PRENOM asc ";		
	break;
	
//======================
// Ville
//======================
case "ville":
	if (check_rights($_SESSION['id'],25)) $show_num=true;
	$show_address=true;
	$ok=(strlen($trouve)>0?true:false);
	$query ="select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_PHONE 'numero', p.P_NOM 'NOM', p.P_ZIP_CODE 'Code', p.P_CITY 'Ville',
			p.P_PRENOM 'prenom', concat(s_code,' - ',s_description) 'section' ,p.p_statut,
			p.p_old_member 'ancien'";
	$query .="	from pompier p, section s
		where p.P_SECTION=s.S_ID
		and ( lower(p.p_city) like lower('$trouve%') or p.p_zip_code like '$trouve%' )
		";	
	if (! check_rights($_SESSION['id'], 25))
	   $query .=" and p.P_HIDE !=1";
	if ( !$externe ) $query .= " and p.p_statut <> 'EXT'";
	$query .=" and p.P_OLD_MEMBER=0";
	$query.=" order by p.p_zip_code, p.P_NOM asc ";		
	break;

//======================
// Et
//======================
case "et":
	if (check_rights($_SESSION['id'],25,"$section")) $show_num=true;
	$q = (isset($_POST['qualif'])?explode(",",$_POST['qualif']):false);
	$ok = ((count($q)>0)?true:false);
	if ($q){
	   $qualif=$_POST['qualif'];
	   $query ="select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_PHONE 'numero', p.P_NOM 'NOM', p.P_PRENOM 'prenom', p.p_statut,
			 concat(s_code,' - ',s_description) 'section'";
	   $query .=" from pompier p, section s";
	   $query .=" where p.P_OLD_MEMBER=0";
	   for ($i=0;$i<count($q);$i++){		
		   $query .=" and p.p_id in ( 
				  select q.p_id from qualification q
				  where q.ps_id = ".$q[$i]."
				  and ( date_format(q.q_expiration,'%Y%m%d') > date_format(now(),'%Y%m%d')
				      or q.q_expiration is null
				    )
				)";
	   }
	   $query .=" AND p.P_SECTION=s.S_ID ";
	   $query .=" AND p.P_SECTION in (".get_family("$section").")";
	   $query .=" AND p.P_OLD_MEMBER=0 ";
	   if ( !$externe ) $query .= " and p.p_statut <> 'EXT'";
	   $query.=" order by NOM, PRENOM asc ";
	   $query.=" Limit ".$maxnumrows;
	}
	break;

//======================
// ou
//======================
case "ou":
	if (check_rights($_SESSION['id'],25,"$section")) $show_num=true;
	$q = (isset($_POST['qualif'])?explode(",",$_POST['qualif']):false);
	$ok = ((count($q)>0)?true:false);
	if($q){
	 	$qualif=$_POST['qualif'];
	    for ($i=0;$i<count($q);$i++){
	 	    $query .=" select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_PHONE 'numero', p.P_NOM 'NOM', p.p_statut, po.TYPE, po.DESCRIPTION,
		 		p.P_PRENOM 'prenom', concat(s_code,' - ',s_description) 'section'
			    from pompier p, section s, poste po, qualification q
				where p.P_SECTION=s.S_ID
				and p.P_OLD_MEMBER=0
				and ( date_format(q.q_expiration,'%Y%m%d') > date_format(now(),'%Y%m%d')
				      or q.q_expiration is null
				    )
				and q.ps_id = ".$q[$i]."
				and q.p_id = p.p_id
				and q.ps_id=po.ps_id";
			$query .=" and p.P_SECTION in (".get_family("$section").")";
		    $query .=" and p.P_OLD_MEMBER=0 ";
			if ( !$externe ) $query .= " and p.p_statut <> 'EXT'";
		    if ($i < count($q) -1) $query .=" union ";
        }
	$query.=" order by NOM, PRENOM asc ";	
	$query.=" Limit ".$maxnumrows;	
	}
	break;

//========================
// not
//========================

case "not":
	if (check_rights($_SESSION['id'],25,"$section")) $show_num=true;
	$q = (isset($_POST['qualif'])?explode(",",$_POST['qualif']):false);
	$ok = ((count($q)>0)?true:false);
	if($q){
	 	$qualif=$_POST['qualif'];
	    for ($i=0;$i<count($q);$i++){
	 	    $query .=" select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_PHONE 'numero', p.P_NOM 'NOM', p.p_statut,
		 		p.P_PRENOM 'prenom', concat(s_code,' - ',s_description) 'section'
			    from pompier p, section s, poste po, qualification q
				where p.P_SECTION=s.S_ID
				and p.P_OLD_MEMBER=0
				and ( date_format(q.q_expiration,'%Y%m%d') > date_format(now(),'%Y%m%d')
				      or q.q_expiration is null
				    )
				and q.ps_id != ".$q[$i]."
				and q.p_id = q.p_id
				and  p.p_id NOT
				IN (
				SELECT DISTINCT q.p_id
				FROM qualification q
				WHERE q.ps_id =".$q[$i]."
				)
				and q.ps_id=po.ps_id";
			$query .=" and p.P_SECTION in (".get_family("$section").")";
		    $query .=" and p.P_OLD_MEMBER=0 ";
			if ( !$externe ) $query .= " and p.p_statut <> 'EXT'";
		    if ($i < count($q) -1) $query .=" union ";
        }
	$query.=" order by NOM, PRENOM asc ";	
	$query.=" Limit ".$maxnumrows;	
	}
	break;
//======================
// habilitation
//======================
case "habilitation":
	$qualif="0";
	if (check_rights($_SESSION['id'],25,"$section")) $show_num=true;
	$ok=(strlen($trouve)>=1?true:false);
	if ( $trouve < 100){
		$query .=" select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_PHONE 'numero', p.P_NOM 'NOM', p.p_statut, 
		 		p.P_PRENOM 'prenom', concat(s_code,' - ',s_description) 'section', 
				case 
				when g1.gp_description is not null then g1.gp_description
				else '-'
				end
				as 'groupe1',
				p.gp_flag1 'flag1',
				case 
				when g2.gp_description is not null then g2.gp_description
				else ' '
				end
				as 'groupe2',
				p.gp_flag2 'flag2'
			    from section s, groupe g1, pompier p
				left join groupe g2 on (g2.gp_id = p.gp_id2)
				where p.P_SECTION=s.S_ID
				and p.P_OLD_MEMBER=0
				and ( p.gp_id = ".$trouve." or p.gp_id2 = ".$trouve." )
				and g1.gp_id = p.gp_id";	
	}
	else {
		$query .=" select distinct p.P_ID 'ID', p.P_EMAIL 'Email', p.P_PHONE 'numero', p.P_NOM 'NOM', p.p_statut, 
		 		p.P_PRENOM 'prenom', concat(s_code,' - ',s_description) 'section', 
				g.gp_description 'groupe1', niv
			    from pompier p, section_flat s,  section_role sr, groupe g
				where sr.S_ID = s.S_ID
				and g.GP_ID = sr.GP_ID
				and p.P_ID = sr.P_ID
				and p.P_OLD_MEMBER=0
				and sr.gp_id = ".$trouve;
	}
	$query .=" and s.S_ID in (".get_family("$section").")";
	if ( !$externe ) $query .= " and p.p_statut <> 'EXT'";
	$query.=" order by NOM, PRENOM asc ";
	$query.=" Limit ".$maxnumrows;		
	break;
default:
}

if($ok && $query!=""){
	$result=mysql_query($query);
}else{
	$result= false;
}

if ($result){
$number=mysql_num_rows($result);
if ( $number == $maxnumrows ) 
	  echo "<img src=images/miniwarn.png> Affinez votre recherche, seules les $maxnumrows premières lignes sont affichées";
else if ($number > 1)
      echo $number." personne".(($number>1)?"s":"")." trouvée".(($number>1)?"s":"");
$i=0;
$prevPID=0;

if ( $number > 0 ) {
	$hint="";
	while ($row=@mysql_fetch_array($result)) {
	 	$P_ID=(isset($row['ID'])?$row["ID"]:"");
	 	if ( $P_ID <> $prevPID ) {
	 	 	$new=true;
			$P_PRENOM=(isset($row['prenom'])?str_replace($badletters, $goodletters, $row["prenom"]):"");
			$P_NOM=(isset($row['NOM'])?str_replace($badletters, $goodletters, $row["NOM"]):"");
			$S_CODE=(isset($row['section'])?str_replace($badletters, $goodletters, $row["section"]):"");
			$prevPID=$P_ID;
			$numero=(isset($row['numero'])?$row["numero"]:"");
		}
		else {
		 	$new=false;
		 	$P_PRENOM="";
		 	$P_NOM="<div align=center>-</div>";
		 	$S_CODE="-";
		 	$numero="<div align=center>-</div>";
		}
		$ancien=(isset($row['ancien'])?$row['ancien']:"0");
		$statut=(isset($row['p_statut'])?$row['p_statut']:"0");
		$email=(isset($row['Email'])?$row['Email']:"");
		$ville=(isset($row['Ville'])?$row['Ville']:"");
		$zipcode=(isset($row['Code'])?$row['Code']:"");
		$groupe1=(isset($row['groupe1'])?$row['groupe1']:"");
		$groupe2=(isset($row['groupe2'])?$row['groupe2']:"");
		$flag1=(isset($row['flag1'])?$row['flag1']:"");
		$flag2=(isset($row['flag2'])?$row['flag2']:"");
		$poste=(isset($row['DESCRIPTION'])?$row['DESCRIPTION']:"");
		
		if ( $critere == 'habilitation' ) {
		 	if ( $groupe1 ==  'Président (e)' ) {
		 	 // vrai président ou responsable d'antenne
		 	 	if ( $row['niv'] == 4 ) $groupe1 =  "Responsable d'antenne";
		 	}
		}

		$i=$i+1;
		if ( $i%2 == 0 ) {
			$mycolor=$mylightcolor;
			if ($ancien >= 1 ) $mycolor=$mygreycolor;
			else if ($statut == 'EXT' ) $mycolor=$mygreencolor;
		}
		else {
			$mycolor="#FFFFFF";
			if ($ancien >= 1 ) $mycolor='#E1E1E1';
			else if ($statut == 'EXT' ) $mycolor='#F0FBF0';
		}
	    if ($ancien >= 1 ) $ft="<font color=black>";
	    else if ($statut == 'EXT' ) $ft="<font color='#0b660b'>";
	    else $ft="";
	    if ( $flag1 == 1 ) $flag1=" (+)";
	    else $flag1="";
	    if ( $flag2 == 1 ) $flag2=" (+)";
	    else $flag2="";
	    
	  if($P_ID>0){
		$hint = $hint ."\n"."<tr height=10 bgcolor=$mycolor 
	      onMouseover=\"this.bgColor='yellow'\" 
	      onMouseout=\"this.bgColor='$mycolor'\"   
		  onclick=\"this.bgColor='#33FF00'\">";
		if ($envoisEmail) {
			if ( $new ) 
				$hint .=(($email)?"<td><input type=\"checkbox\" name=\"SendMail\" value=\"$P_ID\" /></td>":"<td></td>");
			else
				$hint .="<td></td>";
		}
		$hint .="<td onclick=\"displaymanager($P_ID);\">".$ft.strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</td>";
		$hint .="<td onclick=\"displaymanager($P_ID);\">".$ft.$S_CODE."</td>";
		if ($show_address) {
			$hint .="<td onclick=\"displaymanager($P_ID);\">".$ft.$zipcode."</td>";
			$hint .="<td onclick=\"displaymanager($P_ID);\">".$ft.$ville."</td>";
		}
		if ($show_num) $hint .= "<td>".$numero."</td>";
		if ($groupe1 <> "") $hint .="<td>".$groupe1.$flag1."</td>";
		if ($groupe2 <> "") $hint .="<td>".$groupe2.$flag2."</td>";
		if ($poste <> "") $hint .="<td>".$poste."</td>";
		$hint .= "</tr>";
		}		
	}
	
	$response ="<div id=\"overlay\" class=\"noprint\" style=\"display:none;\"><img src=\"images/loading.gif\" border=\"0\" align=\"left\">Recherche en cours...</div>";
	$response .= (($envoisEmail)?$frmEmailDeb:"");
	$response .= "<table><tr><td class='FondMenu'>";	
	$response .= "<table id=\"exportTable\" class=\"tablesorter\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
	<thead>
	<tr>"
	.(($envoisEmail)?"<th witdh=\"10\">Message</th>":"")
	."<th>NOM Prénom</th>
	  <th align=left>Section</th>"
	.(($show_address)?"<th>Code Postal </th>":"")
	.(($show_address)?"<th>Ville </th>":"")
	.(($show_num)?"<th align=left>Numéro</th>":"")
	.(($groupe1!='')?"<th>Principal</th>":"")
	.(($groupe2!='')?"<th>Secondaire</th>":"")
	.(($poste!='')?"<th>Compétence</th>":"")
	."</tr>
	</thead>
	
	<tbody>	
	$hint
	</tbody>
	<tfoot></tfoot>
	</table>";
	$response .="</td></tr></table>";
	$response .=(($envoisEmail)?"</form>":"");
	
} // $number > 0 
} // $result
else{
	$response = "<p>Aucune suggestion...</p>"; 
}

if ( $critere =='tel' or $critere =='ville' )
echo "<p><img src=images/miniwarn.png> Attention les personnes qui ont choisi de masquer leurs informations n'apparaissent pas.";

if ( $critere =='habilitation' || $critere =='et' || $critere=='ou' || $critere=='not') {
	echo "<p><table border=0>
			<tr>";
	if ( $critere =='habilitation') $msg="Attention les anciens membres n'apparaissent pas.";
	else $msg="Attention les anciens membres et les personnes dont les compétences sont expirées n'apparaissent pas.";
	echo "<td><img src=images/miniwarn.png> ".$msg."</td>
				<td><img src='images/xls.jpg' id='StartExcel' height='24' border='0' 
					alt='Excel' title='Exporter au format Excel' 
					onclick=\"window.open('habilitations_xls.php?s=$section&groupe=$trouve&critere=$critere&q=$qualif')\" class='noprint' /></td>
				</tr></table>";
}
mysql_close();

// DEBUG
/*
echo "<p>";
echo "<br />  Trouve = $trouve";
echo "<br />       q = ";
echo "<pre>";
echo print_r($q);
echo "</pre>";
echo "<br />Type tri = $critere";
echo "<br />sql = $query";
echo "</p>";
*/
echo "<script type=\"text/javascript\"></script>
<noscript style=\"color:red;text-decoration: blink;\" class=\"noprint\">
<p>Merci d'activer <b>Javascript</b> pour profiter des toutes les fonctionnalités
".(isset($_POST['retour'])?"<br /><a href=\"".$_POST['retour']."\" target=\"_self\">Retour</a>":"")."</p>
</noscript>";
echo $response;
?>
</body>
</html>
