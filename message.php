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
get_session_parameters();


if (isset($_POST['sectionmessage'])) $sectionmessage=intval($_POST['sectionmessage']);
else $sectionmessage=$mysection;

if (isset($_POST['TM_ID'])) $TM_ID=intval($_POST['TM_ID']);
else $TM_ID=0;

if ( isset($catmessage) or isset($_POST["catmessage"])) 
	$error=0;
else { 
	write_msgbox("ERREUR", $error_pic, "Une erreur est apparue<br>Veuillez recommencer.<br><p align=center><a href='index_d.php' target='_self'>$myspecialfont retour</font></a> ",10,0);
	exit;
}
writehead();
?>

<script language="JavaScript">
function displaymanager(p1,p2){
	 self.location.href="message.php?filter="+p1+"&catmessage="+p2;
	 return true
}
</script>
<?php


//============================================================
//   Upload settings.
//============================================================
//Mmaximum file size. You may increase or decrease.
$MAX_SIZE = 5000000;
//Allowable file Mime Types. Add more mime types if you want
$FILE_MIMES = array('image/jpeg','image/jpg','image/gif','image/png','application/msword');
//Allowable file ext. names. you may add more extension names.
$FILE_EXTS  = array('.zip','.jpg','.png','.gif','.doc','.xls','.pdf','.ppt');

$query="select max(M_ID)+1 as NB from message";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB=$row["NB"];
if ( $NB == '') $NB = 1; 

$messages=$filesdir."/files_message";
$upload_dir = $messages."/".$NB."/";
$msgstring ="";


//============================================================
//   Upload file
//============================================================
if ( isset ($_FILES['userfile'])) {
   if ($_FILES['userfile']['size'] <> 0) {
      $error=0;
      $temp_name = $_FILES['userfile']['tmp_name'];
      $file_type = $_FILES['userfile']['type'];
      $file_name = $_FILES['userfile']['name'];
      $file_ext = strtolower(substr($file_name,strrpos($file_name,".")));
      $file_name = str_replace("\\","",$file_name);
      $file_name = str_replace(" ","_",$file_name);
      $file_name = str_replace("°","",$file_name);
      $file_name = str_replace("#","",$file_name);
      $file_name = str_replace("'","",$file_name);
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
       
           // create upload dir
			if (!is_dir($messages)) {
  				if (!mkdir($messages))
  					die ("Le répertoire d'upload n'existe pas et sa création a échoué.");
  				if (!chmod($messages,0755))
  					die ("Echec lors de la mise à jour des permissions.");
			}

			// create upload subdir
			if (!is_dir($upload_dir)) {
  				if (!mkdir($upload_dir))
  					die ("Le répertoire d'upload n'existe pas et sa création a échoué.");
  				if (!chmod($upload_dir,0755))
  					die ("Echec lors de la mise à jour des permissions.");
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
   else $file_name="";
}
else $file_name="";


//============================================================
//   Save message
//============================================================
if (isset($_POST['catmessage'])) $catmessage=$_POST['catmessage'];
else if (isset ($_GET['catmessage'])) $catmessage=$_GET['catmessage'];
else $catmessage='amicale';

if ( $error == 0 ) {
  if (isset($_POST['objet']) AND isset($_POST['message'])) // Si les variables existent
  {
    if ($_POST['objet'] != NULL AND $_POST['message'] != NULL) // Si on a quelque chose à enregistrer
    {
        $message = strip_tags(mysql_real_escape_string($_POST["message"]));
        $message = str_replace("\\r\\n"," ",$message);
        $message = str_replace(";","",$message);
        $message = str_replace("\"","'",$message);
        $message = str_replace("<","",$message);
        $message = str_replace(">","",$message);
        $message = str_replace("\\","",$message);
        $objet = strip_tags(mysql_real_escape_string($_POST['objet']));
        $objet = str_replace(";","",$objet);
        $objet = str_replace("\"","",$objet);
        $objet = str_replace("\\","",$objet);
        $duree = mysql_real_escape_string($_POST['duree']);

        // Ensuite on enregistre le message
        $query="INSERT INTO message (M_ID,S_ID,M_TYPE, M_DATE, P_ID, M_TEXTE, M_OBJET, M_DUREE, M_FILE, TM_ID)
	       values ( $NB,'$sectionmessage', '$catmessage', NOW() , $id, \"$message\", \"$objet\", $duree, \"$file_name\", $TM_ID)" ;
		$result=mysql_query($query);
    }
  }
}
else {
 	if (( $gardes == 1 ) and ( check_rights($_SESSION['id'], 8) )) $mycatmessage='consignes';
 	else $mycatmessage='amicale';
	write_msgbox("ERREUR", $error_pic, "$msgstring<a href='message.php?type=$mycatmessage' target='_self'>$myspecialfont retour</font></a> ",10,0);
	exit;
}
//============================================================
//   formulaire
//============================================================

echo "<body>";

if ( $catmessage ==  'amicale' or $nbsections == 0 )  {
	$numfonction=16;
    $mytxt="Ajouter une information";
}
else {
     $numfonction=8;
     $mytxt="Ajouter une consigne pour la garde";
}


if ( check_rights($id, $numfonction) ) {
echo "<div align=center><table cellspacing=0 border=0>
      <tr><td ><img src=images/desktop.png> </td><td>
      <font size=4><b>$mytxt</b></font><br>";
	  
echo "</td></tr></table>"; 
 
echo "<form action='message.php' method='POST' enctype='multipart/form-data'>";
echo "<input type='hidden' name='catmessage' value='$catmessage' size='20'>";

echo "<table>";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0 >";

echo "<tr class=TabHeader>
      <td ></td>
      <td  align=right>Nouvelle information</div></td>
      </tr>";
echo "<tr height=90>
     	  <td bgcolor=$mylightcolor align=center ></td>
          <td bgcolor=$mylightcolor >
	      <table cellspacing=0 border=0>
	      	     <tr  height=20>
		     	 <td bgcolor=$mylightcolor >Objet</td>
			 <td bgcolor=$mylightcolor><input type='text' name='objet' size=20 ></td>
		     </tr>";

//=====================================================================
// ligne section
//=====================================================================

$highestsection=get_highest_section_where_granted($id,$numfonction);
if ( $highestsection == '' ) $highestsection=$mysection;
if (( $highestsection <> '' ) and  check_rights($_SESSION['id'], 24 )) $highestsection=0;

if (($nbsections == 0 ) and check_rights($id, $numfonction)) {
 	echo "<tr>
      	  <td bgcolor=$mylightcolor >Destinataires</td>
      	  <td bgcolor=$mylightcolor align=left>";
 	echo "<select id='section' name='sectionmessage'>"; 
   
    $level=get_level("$highestsection");
   	if ( $level == 0 ) $mycolor=$myothercolor;
   	elseif ( $level == 1 ) $mycolor=$my2darkcolor;
   	elseif ( $level == 2 ) $mycolor=$my2lightcolor;
   	elseif ( $level == 3 ) $mycolor=$mylightcolor;
   	else $mycolor='white';
   	$class="style='background: $mycolor;'";
   	echo "<option value='$highestsection' $class >".str_repeat(". ",$level)." ".
      		get_section_code("$highestsection")." - ".get_section_name("$highestsection")."</option>";
   		    display_children2("$highestsection", $level +1, $mysection, $nbmaxlevels);
    echo "</select></td> ";
    echo "</tr>";
}	
else
	echo "<input type='hidden' name='section' value='$mysection'>";	     


$query="select TM_ID, TM_LIBELLE, TM_COLOR, TM_ICON from type_message order by TM_ID";
$result=mysql_query($query);		     
		     
echo         "<tr>
		     	 <td bgcolor=$mylightcolor >Message</td>
		     	 <td bgcolor=$mylightcolor ><textarea name='message' cols='40' rows='10' onFocus=\"if (this.value=='Votre message') {this.value=''}\">Votre message</textarea>
			      </td>
	      	     </tr>
	      	     <tr>
	             	 <td bgcolor=$mylightcolor >Attacher un fichier</td>
	             	 <td bgcolor=$mylightcolor ><input type='file' id='userfile' name='userfile'></td>
            	     </tr>";

echo 		"<tr><td bgcolor=$mylightcolor>Type de message</td>
				<td bgcolor=$mylightcolor><select name='TM_ID'>";
while ($row = mysql_fetch_array($result) )	{
		if ($row["TM_ID"] == 0) $selected='selected';
		else $selected='';
		echo "<option value=".$row["TM_ID"]." $selected>".$row["TM_LIBELLE"]."</option>";	
		
}
echo "</select></td></tr>";           	     
            	     
echo         "<tr>
	      	     	<td bgcolor=$mylightcolor>Durée</td>
			<td bgcolor=$mylightcolor><select name='duree'>
				    <option value=1>1 jours</option>
				    <option value=1>2 jours</option>				    			
				    <option value=3>3 jours</option>
				    <option value=7 selected >7 jours</option>
				    <option value=10>10 jours</option>
				    <option value=15>15 jours</option>
				    <option value=20>20 jours</option>
				    <option value=30>30 jours</option>
				    <option value=60>60 jours</option>
                   </select> <input type='submit' value='OK'></td>
	             </tr>";
        
echo " </table>
	  </td>
     </tr>";

echo "</table>";
echo "</td></tr></table>";
echo "</form></div><p>";
}

//============================================================
//   messages en cours
//============================================================

$query="SELECT p.P_ID, P_NOM, P_PRENOM, P_GRADE, M_DUREE, M_ID, s.S_DESCRIPTION, s.S_ID,
        DATE_FORMAT(M_DATE,'%d/%m/%Y %H:%i') as FORMDATE1,
        DATE_FORMAT(M_DATE, '%m%d%Y%T') as FORMDATE2,
		DATE_FORMAT(M_DATE,'%d-%m-%Y') as FORMDATE3,
		p.P_ID, m.M_TEXTE, m.M_OBJET, m.M_FILE,
		tm.TM_COLOR, tm.TM_ICON, tm.TM_LIBELLE
        FROM message m, pompier p, section s, type_message tm
        where m.P_ID=p.P_ID
        and m.TM_ID = tm.TM_ID
        and s.S_ID = m.S_ID";
if ( $nbsections == 0 )         
	$query .= " and s.S_ID in (".get_family_up("$filter").")";
$query .= " and m.M_TYPE='".$catmessage."'";
// personne non habilitée ne voit pas les messages périmés
if (! check_rights($id, $numfonction) ) {
$query .= "\nand ((DAYOFYEAR(M_DATE) + M_DUREE  >=  DAYOFYEAR(CURDATE())
	       and YEAR(M_DATE) = YEAR(CURDATE()))
	       or ( DAYOFYEAR(M_DATE) + M_DUREE  >=  DAYOFYEAR(CURDATE()) +365
	       and YEAR(M_DATE)+1  = YEAR(CURDATE())))";
}
$query .= "\norder by M_DATE desc";
$result=mysql_query($query);
$number=mysql_num_rows($result);

echo "<body >";
echo "<div align=center><table cellspacing=0 border=0>
      <tr><td  ><img src=images/info.png> </td><td>
      <font size=4><b>Historique des informations </b></font><i> (".$number." trouvés)</i><br>";
      
if (  $nbsections <> 0 ) {
	echo "<input type='hidden' name='filter' value='$filter'>";
}
else {
 	echo "pour les membres de <select id='filter' name='filter'
	 onchange=\"displaymanager(document.getElementById('filter').value,'".$catmessage."')\">"; 
   
   $level=get_level($filter);
   if ( $level == 0 ) $mycolor=$myothercolor;
   elseif ( $level == 1 ) $mycolor=$my2darkcolor;
   elseif ( $level == 2 ) $mycolor=$my2lightcolor;
   elseif ( $level == 3 ) $mycolor=$mylightcolor;
   else $mycolor='white';
   $class="style='background: $mycolor;'";
   display_children2(-1, 0, $filter, $nbmaxlevels,$sectionorder);
   echo "</select>";	  
}		           

echo "</td></tr></table>";

// ====================================
// pagination
// ====================================
require_once('paginator.class.php');
$pages = new Paginator;  
$pages->items_total = $number;  
$pages->mid_range = 9;  
$pages->paginate();  
if ( $number > 10 ) {
	echo $pages->display_pages();
	echo $pages->display_jump_menu(); 
	echo $pages->display_items_per_page(); 
	$query .= $pages->limit;
}
$result=mysql_query($query);

if ( $number > 0 ) {
echo "<p><table width=950 cellspacing=0 border=0 >";
while ($row = mysql_fetch_array($result) )
{
 $duree=$row["M_DUREE"];
 $date1=$row["FORMDATE1"];
 $date3=$row["FORMDATE3"];
 $S_ID=$row["S_ID"];
 $grade=$row["P_GRADE"];
 $nom=$row["P_NOM"];
 $prenom=$row["P_PRENOM"];
 $objet=$row["M_OBJET"];
 $mid=$row["M_ID"];
 $file=$row["M_FILE"];
 $color=$row["TM_COLOR"];
 $icon=$row["TM_ICON"];
 $category=$row["TM_LIBELLE"];
 $MYDATEDIFF = $duree - my_date_diff($date3, date('d-m-Y'));
 if ( $MYDATEDIFF  < 0 ) {
    $mycolor="#990099";
    $perim_info=" - Message périmé";
 }
 else {
    $mycolor=$textcolor;
    $perim_info=" - encore $MYDATEDIFF j";
 }
 if ($grades == 1) $mygrade=$grade;
 else $mygrade="";

 echo "<tr height=50><td width=10><img src=images/".$icon." title=\"message ".$category."\"></td>
           <td ><font size=3 color=".$color."><b>".$objet." </font></b> -<i> 
		   <font color=".$color.">".$mygrade." ".ucfirst($prenom)." ".strtoupper($nom)."</i>";
 
 if ( check_rights($id, $numfonction, $S_ID) ) {
    echo "<i> - ".$date1." - ".$duree."j $perim_info</i>";
    echo " <a href=delete_message.php?catmessage=".$catmessage."&M_ID=".$mid.">
			   	<img src=images/trash.png title='supprimer ce message' border=0></a>";
 }
 else 
 	echo "<i> - ".$date1." </i>";
 
 echo "      <br>".$row["M_TEXTE"];
 if ( $row["M_FILE"] <> "") echo " <i> fichier joint - 
    <a href=showfile.php?section=".$S_ID."&evenement=0&message=".$mid."&file=".$file.">".$file."</a></i>";

 echo "</font></td></tr>";
}
echo "</table>";
}
?>
