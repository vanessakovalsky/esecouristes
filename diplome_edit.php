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
check_all(18);

if (isset ($_GET["psid"])) $psid=intval($_GET["psid"]);
else $psid=0;

writehead();

?>
<script>
function redirect(id){
	 url="diplome_edit.php?psid="+id;
	 self.location.href=url;
	 return true
}
function checkNumber(element,defaultvalue,max)
{   
	var e = document.getElementById(element);
 	var s = element.value;
 	var re = /^([0-9]+)$/;
 	if (! re.test(s) || s > max ) {
	  	alert ("Saisissez un nombre inférieur à "+ max+ ": '"+ s + "' ne convient pas.");
 		element.value = defaultvalue;
 		return false;
 	}
 	// All characters are numbers.
    return true;
}

</script>
</head>
<?php

echo "<body>";	
echo "<div align=center>
	<table><tr><td><img src=images/certificate.png></td>
	<td><font size=4><b>Paramétrage de l'impression des diplômes</b></td></tr></table>";

$actif=array();
$affichage=array();
$taille=array();
$style=array();
$police=array();
$pos_x=array();
$pos_y =array();
$annexe=array();
$style_org=array("Normal","Gras","Italique","Gras et Italique");
$taille_org=array(8,9,10,11,12,14,16,18);
$police_org=array("Courrier","Arial","Times");
extract($_POST); 

if (isset($_POST["action"])) $action=$_POST["action"];
else $action="show";

//============================================================
//   Save info and upload file.
//============================================================
//Mmaximum file size. You may increase or decrease.
$MAX_SIZE = 5000000;
//Allowable file Mime Types. Add more mime types if you want
$FILE_MIMES = array('image/jpeg','image/jpg','image/x-png','image/pjpeg','image/gif','image/png');
//Allowable file ext. names. you may add more extension names.
$FILE_EXTS  = array('.jpg','.png','.gif');
$upload_dir=$filesdir."/diplomes";
$msgstring="";

// enregistrement dans la table
if ($action == "save") {
	$psid=intval($_POST["psid"]);
	$query="select TYPE from poste where PS_ID=".$psid;
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	$type=str_replace(" ", "",$row["TYPE"]);
	$file_name="";
	
	if ( isset ($_FILES['userfile'])) {
     if ($_FILES['userfile']['size'] <> 0) {
       $error=0;
       $temp_name = $_FILES['userfile']['tmp_name'];
       $file_type = $_FILES['userfile']['type'];
       $file_ext = strtolower(substr($file_name,strrpos($file_name,".")));
       $file_path = $upload_dir."/".$type.".jpg";
       
      //File Size Check
      if ( $_FILES['userfile']['size'] > $MAX_SIZE) {
      	 $msgstring = "La taille du fichier attaché ne doit pas dépasser 5 MB.";
     	 $error=1;
      }
      //File Type/Extension Check
      else if (!in_array($file_type, $FILE_MIMES) && !in_array($file_ext, $FILE_EXTS)) {
      	   $msgstring = "Attention, les fichiers du type ($file_type) sont interdits.";
      	   $error=1;
      }
      else {
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
    }
    echo "<font color=red>".$msgstring."</font>";
	
	for($i=1; $i <= $numfields_org ; $i++) { 
		if (isset($actif[$i])) $actif[$i]=1;
		else $actif[$i]=0;
		
		$q1="select count(*) as NB from diplome_param where PS_ID=".$psid." and FIELD=".$i;
		$r1=mysql_query($q1);
		$row=mysql_fetch_array($r1);
		if ( $row["NB"] == 0 )
			$query="insert into diplome_param 
			    (PS_ID,FIELD,ACTIF,AFFICHAGE,TAILLE,STYLE,
			     POLICE,POS_X,POS_Y,
				 ANNEXE) values (".
				$psid.",".$i.",".$actif[$i].",".intval($affichage[$i]).",".intval($aff_taille[$i]).",".intval($aff_style[$i]).","
				.intval($aff_police[$i]).",".intval($pos_x[$i]).",".intval($pos_y[$i]).", 
				\"".mysql_real_escape_string(str_replace("\"","'",$annexe[$i]))."\")";
		else 	
			$query="UPDATE diplome_param SET ACTIF=".$actif[$i].", 
				AFFICHAGE =".intval($affichage[$i]).",
				TAILLE =".intval($aff_taille[$i]).", 
				STYLE=".intval($aff_style[$i]).", 
				POLICE =".intval($aff_police[$i]).", 
				POS_X =".intval($pos_x[$i]).", 
				POS_Y=".intval($pos_y[$i]).", 
				ANNEXE=\"".mysql_real_escape_string(str_replace("\"","'",$annexe[$i]))."\"
				WHERE PS_ID=".$psid."
				and FIELD=".$i;
		mysql_query($query);
	}
}

echo "<table><tr>";
echo "<td>Diplôme pour la compétence</td>
	<td><select id='selectdiplome' name='selectdiplome' 
	  onchange=\"redirect(document.getElementById('selectdiplome').value)\">";
$query="select PS_ID, TYPE, DESCRIPTION from poste 
	  		  where PS_PRINTABLE=1
			  and PS_DIPLOMA=1";
$result=mysql_query($query);

while ($row=@mysql_fetch_array($result)) {
	$PS_ID=$row["PS_ID"];
	$TYPE=$row["TYPE"];
	$DESCRIPTION=$row["DESCRIPTION"];
	if ( $psid == 0 ) $psid = $PS_ID;
	if ( $psid == $PS_ID ) {
		$selected='selected';
		$curtype=$TYPE;
	}
	else $selected='';
 	echo "<option value='".$PS_ID."' $selected>".$TYPE." - ".$DESCRIPTION."</option>";
}
echo "</select></td></tr></table> ";

$default=$filesdir."/diplomes/diplome.jpg";
$file=$filesdir."/diplomes/".str_replace(" ", "",$curtype).".jpg";
if ( file_exists($file))
	$link="<a href=showfile.php?diplome=1&file=".str_replace(" ", "",$curtype).".jpg&section=0&evenement=0&message=0 
			title='Voir image du diplome'>".str_replace(" ", "",$curtype)."</a>";
else if ( file_exists($default))
	$link="<a href=showfile.php?diplome=1&file=diplome.jpg&section=0&evenement=0&message=0 
			title='Voir image du diplome'>".str_replace(" ", "",$curtype)."</a>";
else
	$link=$curtype;

echo "\n<form enctype='multipart/form-data' action='diplome_edit.php' method='POST' >";	
echo "<p><table><tr><td>Changer image diplôme ".$link."</td>
	  <td><input id='userfile' name='userfile' type='file'></td>
	  </tr></table>";

for($i=1; $i <= $numfields_org; $i++) { 
	$query="select PS_ID,FIELD,ACTIF,AFFICHAGE,TAILLE,STYLE,
			     POLICE,POS_X,POS_Y, ANNEXE from diplome_param 
			where PS_ID=".$psid." and FIELD=".$i;
	$result=mysql_query($query);
	$data = @mysql_fetch_array($result); 

	echo "<p><table>";
	echo "<tr>
	  	 <td class='FondMenu'>
	     <table cellspacing=0 border=0>
	     <tr class=TabHeader >
		 <td colspan=5>Impression champ N°:".$i."</td></tr>
	     <tr bgcolor=".$mylightcolor.">";
	     
	if ($data["ACTIF"]=='1') $checked='checked'; else $checked='';
	echo "<td width='120' >
		<input type='checkbox' name='actif[".$i."]' id='actif[".$i."]' value=1 $checked /> Actif </td>";

	echo "<td width='120'>Taille : "; 
	echo "<select id='aff_taille[".$i."]' name='aff_taille[".$i."]'>";
	for($j=0; $j != 8; $j++) { 
		echo "<option value='".$j."'";
 		if ($data["TAILLE"]==$j) {echo " selected='selected'";};
		echo'>'.$taille_org[$j].'</option>';
	};
	echo "</select></td>";
	
	echo "<td width='278' > Affichage : ";
	echo "<select name='affichage[".$i."]' id='affichage[".$i."]' >";
	for($j=0; $j != 12; $j++) { 
	echo "<option value=".$j;
 		if ($data["AFFICHAGE"]==$j) {echo " selected='selected'";};
		echo ">".$aff_org[$j]."</option>";
	};
	echo "</select></td>";
	
	echo "<td width='160'> Style :<select name='aff_style[".$i."]' id='aff_style[".$i."]'>";
	for($j=0; $j != 4; $j++) { 
		echo '<option value="'.$j.'"';
 		if ($data["STYLE"]==$j) {echo " selected='selected'";};
		echo ">".$style_org[$j]."</option>";
	};
	echo "</select></td>";
	
	echo "<td width='160'>Police :  <select name='aff_police[".$i."]' id='aff_police[".$i."]'>";
	for($j=0; $j != 3; $j++) { 
	echo "<option value=".$j;
 		if ($data["POLICE"]==$j) {echo " selected='selected'";};
	echo ">".$police_org[$j]."</option>";
	};
	echo"</select>   
	</td>
	</tr>
	<tr bgcolor=".$mylightcolor.">";
	
	echo "<td>Position X : <input name=pos_x[".$i."] id=pos_x_".$i." type='text' size='5' maxlength='5'
	title='Choisir une valeur comprise entre 0 et 297'
	onchange=\"checkNumber(pos_x_".$i.",'".$data["POS_X"]."',297);\"
	value='".$data["POS_X"]."'/></td>";
	
	echo "<td >Position Y : <input name=pos_y[".$i."] id=pos_y_".$i." type='text' size='5' maxlength='5'
	title='Choisir une valeur comprise entre 0 et 210'   
	onchange=\"checkNumber(pos_y_".$i.",'".$data["POS_Y"]."',210);\" 
	value='".$data["POS_Y"]."'/></td>";
	
	echo "<td colspan='3' ><span name='aff_perso' id='aff_perso' >Personnalisation:
	<input name='annexe[".$i."]' id='annexe[".$i."]' type='text' size='50' maxlength='50'  
	value=\"".$data["ANNEXE"]."\"/></span></td>";

	echo "</tr></table></td></tr></table>";
   };
 
echo "<input type='hidden' name='action' value='save'>
	  <input type='hidden' name='psid' value='".$psid."'>
	  <input type='submit' value='Valider'>
</form>
</div>
</body>";


