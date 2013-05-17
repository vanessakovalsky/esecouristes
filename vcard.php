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
  
/*
Export des contacts
format VCARD .vcf
un fichier vcard ne permet pas d'exporter tout le carnet d'adresse, mais seulement 1 contact
*/
include_once("config.php");
check_all(0);
$id=$_SESSION['id'];

$show=1;
$pompier = (isset($_GET['pid'])?$_GET['pid']:0);
if ( ! check_rights($id, 2)) {
$pompier=0;
}

if ($pompier==0){
	echo "<p>Pas de s�lection... <br>ou droits insuffisants pour exporter la carte de visite...</p>";
}else{
	$export_name = "Export vcard";
	$select="
		p.p_id,
		concat( upper(substring(P_PRENOM,1,1)) , substring(P_PRENOM,2)) 'Pr�nom',
		upper(p.p_nom) 'Nom',	
		null  'Deuxi�me pr�nom',
		concat( upper(substring(P_PRENOM,1,1)) , substring(P_PRENOM,2) ,' ',upper(P_NOM)) 'Nom complet',
		p.p_birthdate 'Date de Naissance',
		p.p_address 'Rue (domicile)',
		p.p_zip_code 'Code postal (domicile)',
		p.p_city 'Ville (domicile)',
		concat('".$trombidir."', '/' , p.p_photo) 'Photo',
		case 
		when p.p_phone is null then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=0 then concat('')
		when p.p_phone is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone) 
		when p.p_phone is not null and p.p_hide = 0 then concat(p.p_phone) 
		end
		as 'T�l�phone mobile',				
		case 
		when p.p_phone2 is null then concat('')
		when p.p_phone2 is not null and p.p_hide = 1 and ".$show."=0 then concat('')
		when p.p_phone2 is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_phone2) 
		when p.p_phone2 is not null and p.p_hide = 0 then concat(p.p_phone2) 
		end
		as 'T�l�phone personnel',		
		case
		when p.p_email is null then concat('')  
		when p.p_email is not null and p.p_hide = 1 and ".$show."=0 then concat('')
		when p.p_email is not null and p.p_hide = 1 and ".$show."=1 then concat(p.p_email) 
		when p.p_email is not null and p.p_hide = 0 then concat(p.p_email) 
		end
		as 'Adresse de messagerie',
		concat(s.s_code,' - ',s.s_description)  'Soci�t�'";
	$table="pompier p, section s";
	$where = (isset($list)?" p.p_section in(".$list.") AND ":"");
	$where .= " p.p_section = s.s_id ";
	$where .= " and p.p_id=$pompier";
	$orderby="p.p_nom, p.p_prenom, p.p_id, s.s_code, s.s_description";
	$groupby="";
	
	
	$sql = "SELECT $select FROM $table WHERE $where ORDER BY $orderby";
	$res = mysql_query($sql);
	if ($res){
		if(mysql_num_rows($res)>0){
			require_once('vcard_class.php');
			while($row=mysql_fetch_array($res)){
				$vc = new vcard();
				$vc->data['display_name']= $row['Nom'].' '.$row['Pr�nom'];
				$vc->data['first_name']= $row['Pr�nom'];
				$vc->data['last_name']= $row['Nom'];
				$vc->data['additional_name']="";
				$vc->data['name_prefix']="";
				$vc->data['name_suffix']="";
				$vc->data['nickname']="";
				$vc->data['title']="";
				$vc->data['role']="";
				$vc->data['department']="";
				$vc->data['company']=$row['Soci�t�'];
				$vc->data['work_po_box']="";
				$vc->data['work_extended_address']="";
				$vc->data['work_address']="";
				$vc->data['work_city']="";
				$vc->data['work_state']="";
				$vc->data['work_postal_code']="";
				$vc->data['work_country']="";
				$vc->data['home_po_box']="";
				$vc->data['home_extended_address']="";
				$vc->data['home_address']=$row['Rue (domicile)'];
				$vc->data['home_city']=$row['Ville (domicile)'];
				$vc->data['home_state']="";
				$vc->data['home_postal_code']=$row['Code postal (domicile)'];
				$vc->data['home_country']="";
				$vc->data['office_tel']="";
				$vc->data['home_tel']=$row['T�l�phone personnel'];
				$vc->data['cell_tel']=$row['T�l�phone mobile'];
				$vc->data['fax_tel']="";
				$vc->data['pager_tel']="";
				$vc->data['email1']=$row['Adresse de messagerie'];
				$vc->data['email2']="";
				$vc->data['url']="";
				if ( ! is_iphone())
					$vc->data['photo']=$row['Photo'];
				$vc->data['birthday']=($row['Date de Naissance']!=''?$row['Date de Naissance']:'');
				$vc->data['timezone']="";
				$vc->data['sort_string']=$row['Nom'].' '.$row['Pr�nom'];
				$vc->data['note']="";
				
				// send by mail
				mysendmailwithattach(
					$id,
					$id,
					"Vcard pour ".$vc->data['sort_string'],
					$vc->generateMailMessage(),
					"vcard.vcf",
					"text/x-vcard"
				);
				
				// download directly
				$vc->download();
			}
		}else{
			echo "ID '$pompier' inconnu...";
		}
	}else{
		echo "Export vcard impossible...<br>".mysql_error();
	}
}
?>