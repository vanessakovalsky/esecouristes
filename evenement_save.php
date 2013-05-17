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

if ( isset($_GET["action"]) or isset($_POST["action"])) 
	$error=0;
else {
 	write_msgbox("ERREUR", $error_pic, "Une erreur est apparue<br>Veuillez recommencer.<p align=center><a href='evenement_choice.php' target='_self'>$myspecialfont retour</font></a> ",10,0);
 	exit;
}

if (isset($_GET["action"])) $action=mysql_real_escape_string($_GET["action"]);
else $action=mysql_real_escape_string($_POST["action"]);
if (isset($_GET["evenement"])) $evenement=intval($_GET["evenement"]);
else $evenement=intval($_POST["evenement"]);
$evts=get_event_and_renforts($evenement,false);

$query="select E_CHEF from evenement where E_CODE=".$evenement;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$E_CHEF=$row["E_CHEF"];

// check input parameters
if (isset($_POST["copycheffrom"])) $copycheffrom=intval($_POST["copycheffrom"]);
else $copycheffrom=0;
if (isset($_POST["copydetailsfrom"])) $copydetailsfrom=intval($_POST["copydetailsfrom"]);
else $copydetailsfrom=0;
if (isset($_POST["copymode"])) $copymode=$_POST["copymode"];
else $copymode='simple';
if (isset($_POST["closed"])) $closed=intval($_POST["closed"]);
else $closed=0;
if (isset($_POST["open_to_ext"])) $open_to_ext=intval($_POST["open_to_ext"]);
else $open_to_ext=0;
if (isset($_POST["allow_reinforcement"])) $allow_reinforcement=intval($_POST["allow_reinforcement"]);
else $allow_reinforcement=0;
if (isset($_POST["section"])) $section=intval($_POST["section"]);
else $section=$_SESSION['SES_SECTION'];
if (isset($_POST["nb_vpsp"])) $nb_vpsp=intval($_POST["nb_vpsp"]);
else $nb_vpsp="null";
if (isset($_POST["nb_autres_vehicules"])) $nb_autres_vehicules=intval($_POST["nb_autres_vehicules"]);
else $nb_autres_vehicule="null";
if (isset($_POST["canceled"])) $canceled=intval($_POST["canceled"]);
else $canceled=0;
if (isset($_POST["flag1"])) $flag1=intval($_POST["flag1"]);
else $flag1=0;
if (isset($_POST["pp"])) $pp=intval($_POST["pp"]);
else $pp=0;
if (isset($_POST["visible_outside"])) $visible_outside=intval($_POST["visible_outside"]);
else $visible_outside=0;
if (isset($_POST["mail1"])) $mail1=intval($_POST["mail1"]);
else $mail1=0;
if (isset($_POST["mail2"])) $mail2=intval($_POST["mail2"]);
else $mail2=0;
if (isset($_POST["mail3"])) $mail3=intval($_POST["mail3"]);
else $mail3=0;
if (isset($_POST["nb1"])) $nb1=intval($_POST["nb1"]);
else $nb1="null";
if (isset($_POST["nb2"])) $nb2=intval($_POST["nb2"]);
else $nb2="null";
if (isset($_POST["nb3"])) $nb3=intval($_POST["nb3"]);
else $nb3="null";
if (isset($_POST["nb4"])) $nb4=intval($_POST["nb4"]);
else $nb4="null";
if (isset($_POST["nb5"])) $nb5=intval($_POST["nb5"]);
else $nb5="null";
if (isset($_POST["nb6"])) $nb6=intval($_POST["nb6"]);
else $nb6="null";
if (isset($_POST["nb7"])) $nb7=intval($_POST["nb7"]);
else $nb7="null";
if (isset($_POST["nb8"])) $nb8=intval($_POST["nb8"]);
else $nb8="null";
if (isset($_POST["nb9"])) $nb9=intval($_POST["nb9"]);
else $nb9="null";
if (isset($_POST["nb10"])) $nb10=intval($_POST["nb10"]);
else $nb10="null";
if (isset($_POST["nb11"])) $nb11=intval($_POST["nb11"]);
else $nb11="null";
if (isset($_POST["company"])) $company=intval($_POST["company"]);
else $company="null";
if ( $company == 0 ) $company="null";
if (isset($_POST["contact_name"])) $contact_name=mysql_real_escape_string($_POST["contact_name"]);
else $contact_name="";
if (isset($_POST["contact_tel"])) $contact_tel=mysql_real_escape_string($_POST["contact_tel"]);
else $contact_tel="";
if (isset($_POST["parent"])) $parent=intval($_POST["parent"]);
else $parent="null";
if ( $parent == 0 ) $parent="null";
if (isset($_POST["cancel_detail"])) $cancel_detail=mysql_real_escape_string(str_replace("\"","",$_POST["cancel_detail"]));
else $cancel_detail="";
if (isset($_POST["security"])) $security=intval($_POST["security"]);
else $security="1";
if ( $security == 0 ) $security=1;

// calcul du numero du nouvel evenement
function generate_evenement_number() {
   $query="select count(1) from evenement";
   $result=mysql_query($query);
   $row=mysql_fetch_array($result);
   if ( $row[0] == 0 ) $e=1;
   else {
   		$query="select max(E_CODE)+1 as NB from evenement";
   		$result=mysql_query($query);
   		$row=mysql_fetch_array($result);
   		$e=$row["NB"];
   }
   return $e;
}

if ( $evenement == 0 ) $evenement = generate_evenement_number();

if ($canceled == 1 ) {
   if (strlen($cancel_detail) < 6 ) {
 		write_msgbox("ERREUR", $error_pic, "La raison de l'annulation n'a pas été bien précisée<br>Cette information est obligatoire. Veuillez recommencer.<p align=center><a href='javascript:history.back(1);'>$myspecialfont retour</font></a> ",10,0);
 		exit;
 	}	
}

if (( $action <> 'delete' and $action <> 'document') and ( $_POST["type"] == '' )) {
 	write_msgbox("ERREUR", $error_pic, "Le type d'événement n'a pas été bien précisée<br>Cette information est obligatoire. Veuillez recommencer.<p align=center><a href='javascript:history.back(1);'>$myspecialfont retour</font></a> ",10,0);
 	exit;	
}

?>
<SCRIPT>
function redirect(url) {
	 self.location.href = url;
}

function redirect2(evenement) {
	 url="evenement_display.php?evenement="+evenement+"&from=document";
	 opener.document.location.href=url;
	 self.close();
}

function redirect3(evenement) {
	 url="evenement_display.php?evenement="+evenement+"&from=document";
	 self.location.href = url;
}

</SCRIPT>
<?php

// insertion / mise à jour de indisponibilité
if ( $action == "delete" ) {
   check_all(19);
   if (! check_rights($id, 19, get_section_organisatrice($evenement))) check_all(24);
 
   $query="delete from evenement_participation where E_CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="delete from evenement_vehicule where E_CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="delete from evenement_materiel where E_CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="delete from evenement_facturation where E_CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="delete from evenement_competences where E_CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="delete from geolocalisation where TYPE='E' and CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="delete from evenement_horaire where E_CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="delete from document where E_CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="delete from personnel_formation where E_CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="delete from evenement where E_CODE=".$evenement;
   $result=mysql_query($query);
   
   $query="update evenement set E_PARENT=null where E_PARENT=".$evenement;
   $result=mysql_query($query);
   
}
else if ( $id <> $E_CHEF ) 
	check_all(15);

if ( $action == "update" ) {
	if (( $id <> $E_CHEF ) and (! check_rights($id, 15, get_section_organisatrice($evenement))))
		check_all(24);
}

if ( $action == "document") {
  //============================================================
  //   Upload file.
  //============================================================
  //Mmaximum file size. You may increase or decrease.
  $MAX_SIZE = 5000000;
  //Allowable file Mime Types. Add more mime types if you want
  $FILE_MIMES = array('image/jpeg','image/jpg','image/gif','image/png','application/msword');
  //Allowable file ext. names. you may add more extension names.
  $FILE_EXTS  = array('.zip','.jpg','.png','.gif','.doc','.docx','.xls','.xlsx','.pdf','.ppt','.pptx');

  $site_name = $_SERVER['HTTP_HOST'];
  $url_dir = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
  $url_this =  "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

  $upload_dir = $filesdir."/files/".$evenement."/";
  $msgstring ="";

  $file_name='';
  if ( isset ($_FILES['userfile'])) {
     if ($_FILES['userfile']['size'] <> 0) {
      $temp_name = $_FILES['userfile']['tmp_name'];
      $file_type = $_FILES['userfile']['type'];
      $file_name = $_FILES['userfile']['name'];
      $file_ext = strtolower(substr($file_name,strrpos($file_name,".")));
      $file_name = str_replace("\\","",$file_name);
      $file_name = str_replace(" ","_",$file_name);
      $file_name = str_replace("°","",$file_name);
      $file_name = str_replace("#","",$file_name);
      $file_name = str_replace("'","",$file_name);
      $file_name = str_replace("&","",$file_name);
      $file_name = fixcharset($file_name);
      $file_path = $upload_dir.$file_name;
      //File Size Check
      if ( $_FILES['userfile']['size'] > $MAX_SIZE) {
      	 $msgstring = "La taille du fichier attaché ne doit pas dépasser 5 MB.";
     	 $error=1;
      }
      //File Type/Extension Check
      else if (!in_array($file_type, $FILE_MIMES) && !in_array($file_ext, $FILE_EXTS)) {
      	   $msgstring = "Attention, les fichiers du type $file_name($file_type) sont interdits.";
      	   $error=1;
      }
      else {
           if (!is_dir($filesdir."/files")) {
  	           mkdir($filesdir."/files");
           }
  		   // create upload dir
  			if (!is_dir($upload_dir)) {
    			if (!mkdir($upload_dir)) {
  	   				 $msgstring = "Le répertoire d'upload n'existe pas et sa création a échoué.";
  	   				 $error=1;
  	   			}
    			if (!chmod($upload_dir,0755)) {
  	   				$msgstring = "Echec lors de la mise à jour des permissions.";
  	   				$error=1;
  	   			}
   			}
      	   if (! $result  =  move_uploaded_file($temp_name, $file_path)) {
      	      $msgstring ="Une erreur est apparue lors de l'upload du fichier.";
      	      $error=1;
           }
      	   if (!chmod($file_path,0777)) {
   	          $msgstring = "Echec lors de la mise à jour des permissions.";
              $error=1;
      	   }
       }
     }
   }

   if ( $error > 0 ) {
        write_msgbox("ERREUR", $error_pic, "$msgstring<br><p align=center>
					<a onclick=\"window.close();return false;\">$myspecialfont fermer</font></a> ",10,0);
		exit;
   }
   else if ( $file_name <> '' ) {
   		// upload réussi: insérer les informations relatives au document dans la base
       		$query="insert into document(S_ID,D_NAME,E_CODE,TD_CODE,DS_ID,D_CREATED_BY)
       			values (".$section.",\"".$file_name."\",\"".$evenement."\",'DIV',\"".$security."\",".$_SESSION['id'].")";
       		$result=mysql_query($query);
   }
   if ( isset ($_POST['doc'])) {
   		// update doc security info
   		   $doc=mysql_real_escape_string($_POST['doc']);
   		   $query="select count(*) as NB from document where E_CODE=".$evenement." 
			  		and D_NAME=\"".$doc."\"";
		   $result=mysql_query($query);
		   $row=mysql_fetch_array($result);
		   
		   if ( $row["NB"] == 0 ) {		
   		  	    $query="insert into document(S_ID,D_NAME,E_CODE,TD_CODE,DS_ID,D_CREATED_BY)
       					values (".$section.",\"".$doc."\",\"".$evenement."\",'DIV',\"".$security."\",".$_SESSION['id'].")";
       	   		$result=mysql_query($query);
       	   }
       		
   		   $query="update document set DS_ID=".$security." where E_CODE=".$evenement." 
			  		and D_NAME=\"".$doc."\"";
		   $result=mysql_query($query);
   }
   if ( isset ($_FILES['userfile']))
   		echo "<body onload=redirect2('".$evenement."')></body></html>";
   else 
   		echo "<body onload=redirect3('".$evenement."')></body></html>";
   exit;
}

if ( $action <> "delete" and  $action <> "document") {

   //============================================================
   //   Insert or update evenement
   //============================================================
 
   $type=mysql_real_escape_string($_POST["type"]);
   $libelle=mysql_real_escape_string(str_replace("\"","",$_POST["libelle"]));
   $lieu=mysql_real_escape_string(str_replace("\"","",$_POST["lieu"]));
 
   $nombre=intval($_POST["nombre"]);
   $comment=strip_tags(mysql_real_escape_string(str_replace("\"","",$_POST["comment"])));
   $comment2=strip_tags(mysql_real_escape_string(str_replace("\"","",$_POST["comment2"])));
   $address=strip_tags(mysql_real_escape_string(str_replace("\"","",$_POST["address"])));
   $convention=mysql_real_escape_string(str_replace("\"","",$_POST["convention"]));  
   $consignes=strip_tags(mysql_real_escape_string(str_replace("\"","",$_POST["consignes"])));
   $moyens=strip_tags(mysql_real_escape_string(str_replace("\"","",$_POST["moyens"])));
   $clauses=strip_tags(mysql_real_escape_string(str_replace("\"","",$_POST["clauses"])));
   $clauses2=strip_tags(mysql_real_escape_string(str_replace("\"","",$_POST["clauses2"])));
   $contact_name=mysql_real_escape_string(str_replace("\"","",$_POST["contact_name"]));  
   $repas=strip_tags(mysql_real_escape_string(str_replace("\"","",$_POST["repas"])));
   $transport=strip_tags(mysql_real_escape_string(str_replace("\"","",$_POST["transport"])));

   if ( $canceled == 0 ) $cancel_detail="";
   if ( isset ($_FILES["userfile"])) $userfile=$_FILES["userfile"];
   
   $dc1=array();
   $dc2=array();
   $debut=array();
   $fin=array();
	$heure_rdv=array();
	$lieu_rdv=array();
   $duree=array();
   for ($k=1; $k <= $nbmaxsessionsparevenement; $k++) {
      if ( $_POST["dc1_".$k] <> "" ) {
	  	  $dc1[$k]=mysql_real_escape_string($_POST["dc1_".$k]);
          $dc2[$k]=mysql_real_escape_string($_POST["dc2_".$k]);
          if ( $dc2[$k] == "" ) $dc2[$k] = $dc1[$k];
   	  	  $duree[$k]=mysql_real_escape_string($_POST["duree_".$k]);
   	  	  $debut[$k]=mysql_real_escape_string($_POST["debut_".$k]);
      	  $fin[$k]=mysql_real_escape_string($_POST["fin_".$k]);
		  $heure_rdv[$k]=mysql_real_escape_string($_POST["heure_rdv_".$k]);
		  $lieu_rdv[$k]=mysql_real_escape_string($_POST["lieu_rdv_".$k]);
      }
      else  {
       	  $dc1[$k]='';
       	  $dc2[$k]='';
       	  $debut[$k]='';
       	  $fin[$k]='';
       	  $duree[$k]='';
		  $heure_rdv[$k]='';
		  $lieu_rdv[$k]='';
      }
   }	 
   if ( $libelle == "" )   {
 	   write_msgbox("erreur", $error_pic, "Le libelle doit être renseigné<br><p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   echo "";
	   exit;
   }

   if ( $lieu == "" )   {
 	   write_msgbox("erreur", $error_pic, "Le lieu doit être renseigné<br><p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   echo "";
	   exit;
   }

   if ( $dc1[1] == "" )   {
 	   write_msgbox("erreur", $error_pic, "La date de début est incorrecte<br><p align=center><input type=submit value='retour' onclick='javascript:history.back(1);'> ",10,0);
	   echo "";
	   exit;
   }

 	if ( $action =='update') {
 	    // mise à jour 
 	 
 	 	if ( $evenement == 0 ) {
			param_error_msg();
			exit;
		}

	    // mettre à jour l'événement principal
		$query="update evenement set
	    	TE_CODE='".$type."',
			S_ID=".$section.",
			E_LIBELLE=\"".$libelle."\",
			E_LIEU=\"".$lieu."\",
			E_NB=".$nombre.",
			E_COMMENT=\"".$comment."\",
			E_COMMENT2=\"".$comment2."\",
			E_CONSIGNES=\"".$consignes."\",
			E_MOYENS_INSTALLATION=\"".$moyens."\",
			E_NB_VPSP=\"".$nb_vpsp."\",
			E_NB_AUTRES_VEHICULES=\"".$nb_autres_vehicules."\",
			E_CLAUSES_PARTICULIERES=\"".$clauses."\",
			E_CLAUSES_PARTICULIERES2=\"".$clauses2."\",
			E_REPAS=\"".$repas."\",
			E_TRANSPORT=\"".$transport."\",
			E_ADDRESS=\"".$address."\",
			E_VISIBLE_OUTSIDE='".$visible_outside."',
			E_CLOSED='".$closed."',
			E_CANCELED='".$canceled."',
			E_FLAG1='".$flag1."',
			E_PP='".$pp."',
			E_CANCEL_DETAIL=\"".$cancel_detail."\",
			E_MAIL1='".$mail1."',
	     	E_MAIL2='".$mail2."',
			E_MAIL3='".$mail3."',
			E_CONTACT_LOCAL='".$contact_name."',
			E_CONTACT_TEL='".$contact_tel."',
			E_HEURE_RDV='".$heure_rdv."',
			E_LIEU_RDV='".$lieu_rdv."',";
	 	if ( $parent <> 'null' )
        	$query .= "E_PARENT='".$parent."',";
     	else
     		$query .= "E_PARENT=null,";
	 	if ( $company <> 'null' )
        	$query .= "C_ID='".$company."',";
     	else
     		$query .= "C_ID=null,";
	 	if ( $nb1 <> 'null' )
	 		$query .= "E_NB1='".$nb1."',";
	 	if ( $nb2 <> 'null' )
			$query .= "E_NB2='".$nb2."',";
	 	if ( $nb3 <> 'null' )
			$query .= "E_NB3='".$nb3."',";
		if ( $nb4 <> 'null' )
			$query .= "E_NB1_1='".$nb4."',";
		if ( $nb5 <> 'null' )
			$query .= "E_NB1_2='".$nb5."',";
		if ( $nb6 <> 'null' )
			$query .= "E_NB1_3='".$nb6."',";
		if ( $nb7 <> 'null' )
			$query .= "E_NB1_4='".$nb7."',";
		if ( $nb8 <> 'null' )
			$query .= "E_NB1_5='".$nb8."',";
		if ( $nb9 <> 'null' )
			$query .= "E_NB1_6='".$nb9."',";
		if ( $nb10 <> 'null' )
			$query .= "E_NB2_1='".$nb10."',";
		if ( $nb11 <> 'null' )
			$query .= "E_NB2_2='".$nb11."',";
	 	$query .="E_CONVENTION=\"".$convention."\",
			E_OPEN_TO_EXT='".$open_to_ext."',
			E_ALLOW_REINFORCEMENT='".$allow_reinforcement."'
			where E_CODE =".$evenement;
	 	$result=mysql_query($query);
		
		if ( $nombre > 0 ) {
			$query="update evenement_competences set NB=".$nombre." where E_CODE=".$evenement." and PS_ID=0 ";
			$result=mysql_query($query);		
		}
	}
   	else {
   	    // insert evenement
   		$query="insert into  evenement (E_CODE, TE_CODE, S_ID, E_LIBELLE, E_LIEU, E_NB, E_COMMENT, E_COMMENT2, E_CLOSED, E_CANCELED, E_FLAG1, E_PP, E_CANCEL_DETAIL, 
		        E_MAIL1, E_MAIL2, E_MAIL3, E_CONVENTION, E_REPAS, E_TRANSPORT, E_CONSIGNES, E_NB_VPSP, E_NB_AUTRES_VEHICULES, E_MOYENS_INSTALLATION, E_CLAUSES_PARTICULIERES, E_CLAUSES_PARTICULIERES2, E_OPEN_TO_EXT, E_ALLOW_REINFORCEMENT, E_PARENT, E_CREATED_BY, 
				E_CREATE_DATE, C_ID, E_CONTACT_LOCAL, E_CONTACT_TEL, E_ADDRESS, E_VISIBLE_OUTSIDE)
		        values (".$evenement.",\"".$type."\",".$section.",\"".$libelle."\",\"".$lieu."\",".$nombre.",\"".$comment."\",\"".$comment2."\",'".$closed."',
				'".$canceled."','".$flag1."','".$pp."',\"".$cancel_detail."\",
				'".$mail1."','".$mail2."','".$mail3."',\"".$convention."\",\"".$repas."\",\"".$transport."\",\"".$consignes."\",".$nb_vpsp.",".$nb_autres_vehicules.",\"".$moyens."\",\"".$clauses."\",\"".$clauses2."\",'".$open_to_ext."','".$allow_reinforcement."',".$parent.", ".$id.", 
				NOW(),'".$company."',\"".$contact_name."\",\"".$contact_tel."\",\"".$address."\",'".$visible_outside."' )";
   	     $result=mysql_query($query) or die ("Erreur création événement");
    }
    $query="update evenement set E_COMMENT2 = null where E_VISIBLE_OUTSIDE=0 and E_CODE =".$evenement;
    $result=mysql_query($query);
    
	// geolocalisation
    $ret=gelocalize($evenement,'E');

	// insert or update horaires
	for ($k=1; $k <= $nbmaxsessionsparevenement; $k++) {
	  if ( $dc1[$k] <> "" ) {
	 	$tmp=explode ("-",$dc1[$k]); $month1=$tmp[1]; $day1=$tmp[0]; $year1=$tmp[2];
   	    $date1=mktime(0,0,0,$month1,$day1,$year1);
   	    $tmp=explode ("-",$dc2[$k]); $month2=$tmp[1]; $day2=$tmp[0]; $year2=$tmp[2];
   	    $date2=mktime(0,0,0,$month2,$day2,$year2);
   	     
   	    // mettre à jour les dates / heures des renforts
	    $query="select date_format(EH_DATE_DEBUT, '%Y-%m-%d') as EH_DATE_DEBUT, 
				   date_format(EH_DATE_FIN, '%Y-%m-%d') as EH_DATE_FIN, 
				   EH_DEBUT, EH_FIN , EH_DUREE, EH_HEURE_RDV, EH_LIEU_RDV
				   from evenement_horaire 
				   where E_CODE = ". $evenement." 
				   and EH_ID=".$k;
	    $result=mysql_query($query);
		$row=mysql_fetch_array($result);
	   	$cur_EH_DATE_DEBUT=$row["EH_DATE_DEBUT"];
   		$cur_EH_DATE_FIN=$row["EH_DATE_FIN"];
  	 	$cur_EH_DEBUT=$row["EH_DEBUT"];
   		$cur_EH_FIN=$row["EH_FIN"];
		$cur_EH_HEURE_RDV=$row["EH_HEURE_RDV"];
		$cur_EH_LIEU_RDV=$row["EH_LIEU_RDV"];
			
		// changer dates / heures des renforts qui ont les mêmes horaires
   		$query="update evenement_horaire set
   			EH_DATE_DEBUT='".$year1."-".$month1."-".$day1."',
			EH_DATE_FIN='".$year2."-".$month2."-".$day2."',
			EH_DEBUT='".$debut[$k]."',
			EH_FIN='".$fin[$k]."',
			EH_DUREE='".$duree[$k]."',
			EH_HEURE_RDV='".$heure_rdv[$k]."',
			EH_LIEU_RDV='".$lieu_rdv[$k]."'
			where E_CODE in (".$evts.")
			and EH_ID=".$k."
			and EH_DATE_DEBUT = '".$cur_EH_DATE_DEBUT."'
			and EH_DATE_FIN = '".$cur_EH_DATE_FIN."'
			and EH_DEBUT = '".$cur_EH_DEBUT."'
			and EH_FIN = '".$cur_EH_FIN."'
			and EH_HEURE_RDV = '".$cur_EH_HEURE_RDV."'
			and EH_LIEU_RDV = '".$cur_EH_LIEU_RDV."'";
		$result=mysql_query($query);
   	    
   	    // supprimer puis réinsérer sur l'événement principal
   	    $query="delete from evenement_horaire where E_CODE=".$evenement." and EH_ID=".$k;
    	$result=mysql_query($query);
    
   	    $query="insert into evenement_horaire (E_CODE,EH_ID, EH_DATE_DEBUT,EH_DATE_FIN,EH_DEBUT, EH_FIN, EH_DUREE, EH_HEURE_RDV, EH_LIEU_RDV)
   	            values (".$evenement.",".$k.",'".$year1."-".$month1."-".$day1."','".$year2."-".$month2."-".$day2."','".$debut[$k]."','".$fin[$k]."','".$duree[$k]."','".$heure_rdv[$k]."','".$lieu_rdv[$k]."')";
   	    $result=mysql_query($query);
   	    
   	    $query="select count(1) from evenement_competences where E_CODE=".$evenement." and EH_ID= ".$k." and PS_ID=0";
   	    $result=mysql_query($query);
   	    $row=mysql_fetch_array($result);
   	    if ( $row[0] == 0 ) {
   	    	$query="insert into evenement_competences (E_CODE,EH_ID,PS_ID,NB) values (".$evenement.",".$k.",0,".$nombre.")";
   	    	$result=mysql_query($query);
		}
	  }
	  else {
	    $query="delete from evenement_horaire where E_CODE=".$evenement." and EH_ID=".$k;
    	$result=mysql_query($query);
    	
    	$query="delete from evenement_participation where E_CODE=".$evenement." and EH_ID=".$k;
    	$result=mysql_query($query);
    	
    	$query="delete from evenement_competences where E_CODE=".$evenement." and EH_ID=".$k;
    	$result=mysql_query($query);
	  }  	 	
   	}
   	
   	// cas C_ID not null
   	if ( $company <> 'null' ) {
   	 	if ( $contact_name == '' ) {
   	 	 	$query = "update evenement set E_CONTACT_LOCAL = ( select C_CONTACT_NAME from company where C_ID = $company )
					  where E_CODE = $evenement";
			$result=mysql_query($query);
   	 	}
   	 	if ( $contact_tel == '' ) {
   	 	   	$query = "update evenement set E_CONTACT_TEL = ( select C_PHONE from company where C_ID = $company )
					  where E_CODE = $evenement";
			$result=mysql_query($query);
   	 	}
   	}
   	
   	// cas DPS ; mettre à jour le type de DPS
   	if ( $type =='DPS' ) {
   	 	if ( $nombre == '' ) $TAV_ID = 1;
   	 	else if ( $nombre == 0 ) $TAV_ID = 1;
		else if ( $nombre < 3 ) $TAV_ID = 2;
		else if ( $nombre < 13 ) $TAV_ID = 3;
		else if ( $nombre < 37 ) $TAV_ID = 4;
		else $TAV_ID = 5;
   	}
	else $TAV_ID='null';
	
	$query="update evenement set TAV_ID = ".$TAV_ID." where E_CODE = $evenement";
	$result=mysql_query($query);
	
	if ( $canceled == 1) {
			// en cas d'annulation de l'evenement, annuler aussi les renforts
			$query="update evenement set E_CANCELED = 1, E_CANCEL_DETAIL=\"".$cancel_detail."\" 
					where E_PARENT = $evenement
					and E_CANCELED = 0";
			$result=mysql_query($query);
	}
   	
	if (( $action =="create" ) or ( $action =="copy" ) or ( $action =="renfort" )) { 
	       $query="delete from evenement_participation where E_CODE=".$evenement;
		   $result=mysql_query($query);	 
		   $query="delete from evenement_vehicule where E_CODE=".$evenement;
		   $result=mysql_query($query);
		   $query="delete from evenement_materiel where E_CODE=".$evenement;
		   $result=mysql_query($query);		   
		   if (($action == "copy" ) and ( $copydetailsfrom <> 0 )) {
		        if ( $copymode == 'full' or  $copymode == 'perso' ) {
		   	    	$query="insert into evenement_participation (P_ID, E_CODE, EH_ID, EP_DATE, EP_BY,TP_ID, EP_COMMENT, EP_FLAG1) 
				   		select P_ID, ".$evenement.", EH_ID, EP_DATE, EP_BY,TP_ID, EP_COMMENT, EP_FLAG1
				   		from evenement_participation
				   		where E_CODE=".$copydetailsfrom;
		   			$result=mysql_query($query);
				}
				if ( $copymode == 'full' or  $copymode == 'matos' ) {
					$query="insert into evenement_vehicule (V_ID, E_CODE, EV_KM) 
				   		select V_ID, ".$evenement.", EV_KM 
				   		from evenement_vehicule
				   		where E_CODE=".$copydetailsfrom;
		   			$result=mysql_query($query);
		   		
					$query="insert into evenement_materiel (MA_ID, E_CODE, EM_NB) 
				   		select MA_ID, ".$evenement.", EM_NB
				   		from evenement_materiel
				   		where E_CODE=".$copydetailsfrom;
		   			$result=mysql_query($query);
		   		}
		   		
		   		// eventuellement recopier les renforts
		   		$nbr=get_nb_renforts($copydetailsfrom);
		   		if ( $copymode == 'full' and $nbr > 0 ) {
					$queryz="select E_CODE from evenement where E_PARENT=".$copydetailsfrom;
 					$resultz=mysql_query($queryz);
 					while ($rowz=@mysql_fetch_array($resultz)) {
 					 	$oldr=$rowz["E_CODE"];
		   		 		$newr = generate_evenement_number();
		   		 		$queryk="insert into  evenement (E_CODE, TE_CODE, S_ID, E_LIBELLE, E_LIEU, E_NB, E_COMMENT, E_COMMENT2, E_CLOSED, E_CANCELED, E_FLAG1, E_PP, E_CANCEL_DETAIL, 
		        					E_MAIL1, E_MAIL2, E_MAIL3, E_CONVENTION, E_OPEN_TO_EXT, E_ALLOW_REINFORCEMENT, E_PARENT, E_CREATED_BY, 
									E_CREATE_DATE, C_ID, E_CONTACT_LOCAL, E_CONTACT_TEL, E_ADDRESS, E_VISIBLE_OUTSIDE)
		        				 select ".$newr.",TE_CODE, S_ID, E_LIBELLE, E_LIEU, E_NB, E_COMMENT, E_COMMENT2, E_CLOSED, E_CANCELED, E_FLAG1, E_PP, E_CANCEL_DETAIL, 
		        					E_MAIL1, E_MAIL2, E_MAIL3, E_CONVENTION, E_OPEN_TO_EXT, E_ALLOW_REINFORCEMENT, ".$evenement.", $id, 
									NOW(), C_ID, E_CONTACT_LOCAL, E_CONTACT_TEL, E_ADDRESS, E_VISIBLE_OUTSIDE
								 from evenement where E_CODE = ".$oldr;
   	     				$resultk=mysql_query($queryk) or die ("Erreur création événement renfort");
   	     				
   	     				$queryk="insert into evenement_horaire (E_CODE,EH_ID, EH_DATE_DEBUT,EH_DATE_FIN,EH_DEBUT, EH_FIN, EH_DUREE, EH_HEURE_RDV, EH_LIEU_RDV)
   	                             select ".$newr.",EH_ID, EH_DATE_DEBUT,EH_DATE_FIN,EH_DEBUT, EH_FIN, EH_DUREE, EH_HEURE_RDV, EH_LIEU_RDV
								 from evenement_horaire where E_CODE=".$evenement;
   	    			    $resultk=mysql_query($queryk);
		    
						$queryk="insert into geolocalisation (TYPE,CODE,LAT,LNG) select 'E', ".$newr.", LAT, LNG 
						         from geolocalisation where TYPE='E' and CODE=".$oldr;
						$resultk=mysql_query($queryk);

   	    			    $queryk="insert into evenement_participation (P_ID, E_CODE, EH_ID, EP_BY, TP_ID, EP_COMMENT, EP_DATE, EP_FLAG1) 
				   				select distinct ep.P_ID, ".$newr.", eh.EH_ID, ep.EP_BY, ep.TP_ID, ep.EP_COMMENT, ep.EP_DATE, ep.EP_FLAG1
				   				from evenement_horaire eh, evenement_participation ep
				   				where eh.E_CODE = ".$newr."
				   				and ep.E_CODE= ".$oldr;
		   				$resultk=mysql_query($queryk);
		   				
		   				$queryk="insert into evenement_vehicule (V_ID, E_CODE, EV_KM) 
				   				select V_ID, ".$newr.", EV_KM 
				   				from evenement_vehicule
				   				where E_CODE=".$oldr;
		   				$resultk=mysql_query($queryk);

		   				$queryk="insert into evenement_materiel (MA_ID, E_CODE, EM_NB) 
				   				select MA_ID, ".$newr.", EM_NB
				   				from evenement_materiel
				   				where E_CODE=".$oldr;
		   			    $result=mysql_query($queryk);
		   		    }
		   		}
		   }
		   if (($action == "copy" ) and ( $copycheffrom <> 0 )) {
				// recopier le chef
				$query="select E_CHEF from evenement where E_CODE=".$copycheffrom;
				$result=mysql_query($query);
				$row=mysql_fetch_array($result);
   				$E_CHEF=$row["E_CHEF"];
				
				if ( $E_CHEF <> '' ) {
		    		$query="update evenement 
				   	set E_CHEF=".$E_CHEF."
		    	   	where E_CODE=".$evenement;
		   			$result=mysql_query($query); 
				}   

		   }

	       echo "<body onload=redirect('evenement_notify.php?evenement=".$evenement."&action=created');>";
  	   }
  	   else {
		   echo "<body onload=redirect('evenement_display.php?evenement=".$evenement."&from=choice');>";
       }
}
else if  ( $action == 'delete' ) {
      $mypath=$filesdir."/files/".$evenement;
      if(is_dir($mypath)) {
      		full_rmdir($mypath);
      }
      write_msgbox("événement supprimé", $star_pic, " L'événement a été supprimé du calendrier<br>
	  	<p align=center><a href='evenement_choice.php' target='_self'>$myspecialfont retour</font></a> ",10,0);
}
?>
