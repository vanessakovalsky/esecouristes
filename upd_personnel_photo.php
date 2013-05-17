<?php

  # written by: Nicolas MARCHE <nico.marche@free.fr> & Jean-Pierre KUNTZ
  # project: eBrigade
  # homepage: http://sourceforge.net/projects/ebrigade/
  # version: 2.6

  # Copyright (C) 2004, 2009 Nicolas MARCHE
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
<!-- 
* Copyright (c) 2008 http://www.webmotionuk.com / http://www.webmotionuk.co.uk
* Date: 2008-11-21
* "PHP & Jquery image upload & crop"
* Ver 1.2
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
* IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
* STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF 
* THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
<ul>
	<li><a href="http://www.webmotionuk.co.uk/php-jquery-image-upload-and-crop/">Back to project page</a></li>
	<li><a href="http://www.webmotionuk.co.uk/jquery_upload_crop.zip">Download Files</a></li>
</ul>

** http://odyniec.net/projects/imgareaselect/
-->
*/
error_reporting (E_ALL ^ E_NOTICE);
session_start(); //Do not remove this

include_once ("config.php");
check_all(0);

$id=$_SESSION['id'];
$error=(isset($_GET['error'])?$_GET['error']:(isset($_POST['error'])?$_POST['error']:""));
$msg=(isset($_GET['msg'])?$_GET['msg']:(isset($_POST['msg'])?$_POST['msg']:""));

$P_PHOTO=(isset($_GET['photo'])?$_GET['photo']:"");
$P_ID=(isset($_GET['pompier'])?intval($_GET['pompier']):(isset($_POST['P_ID'])?intval($_POST['P_ID']):0));
$section=get_section_of($P_ID);

if ( $id <> $P_ID ) {
   check_all(2);
   if (! check_rights($id,2,"$section")) check_all(24);
}

$pompier=intval(mysql_real_escape_string($P_ID));
$pompierNomPrenomId=get_prenom($pompier)." ".get_nom($pompier)." (".get_matricule($pompier).")";

//only assign a new timestamp if the session variable is empty
if (!isset($_SESSION['random_key']) || strlen($_SESSION['random_key'])==0){
    $_SESSION['random_key'] = strtotime(date('Y-m-d H:i:s')); //assign the timestamp to the session variable
	$_SESSION['user_file_ext']= "";
}
#########################################################################################################
# CONSTANTS																								#
# You can alter the options below																		#
#########################################################################################################
$upload_dir = "upload_pic"; 				// The directory for the images to be saved in
$upload_path = $upload_dir."/";	// The path to where the image will be saved	

$upload_dir = "trombi"; // JPK - spécifique ebrigade
$upload_path = $trombidir."/";// JPK - spécifique ebrigade

$large_image_prefix = "resize_"; 			// The prefix name to large image
$thumb_image_prefix = "thumbnail_";			// The prefix name to the thumb image
$large_image_name = $large_image_prefix.$_SESSION['random_key'];     // New name of the large image (append the timestamp to the filename)
$thumb_image_name = $thumb_image_prefix.$_SESSION['random_key'];     // New name of the thumbnail image (append the timestamp to the filename)
$thumb_image_name = $pompier;// JPK - spécifique ebrigade
$max_file = "1"; 					// Maximum file size in MB
$min_file = "6"; 					// Minimum file size in kB
$max_pixels="2";					//2 Mega Pixels
$min_pixels="6";					//4 Kilo Pixels
$max_width = "500";					// Max width allowed for the large image
$min_width = "148";					// Min width allowed for the small image
$min_height = "177";					// Min width allowed for the small image
// Taille photo 25*30 mm
// 300 dpi - px/pouce = 295 * 354 mm
// 150 dpi - px/pouce = 148 * 177 px   <<<< choix optimum pour une retouche automatique
//  72 dpi - px/pouce = 71 * 85 px
$thumb_width = "148";				// Width of thumbnail image
$thumb_height = "177";				// Height of thumbnail image
// Only one of these image types should be allowed for upload
//$allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
$allowed_image_types = array('image/jpeg'=>"jpg",'image/pjpeg'=>"jpg",'image/jpg'=>"jpg");
$allowed_image_ext = array_unique($allowed_image_types); // do not change this
$image_ext = "";	// initialise variable, do not change this.
foreach ($allowed_image_ext as $mime_type => $ext) {
    $image_ext.= strtoupper($ext)." ";
}

##########################################################################################################
# IMAGE FUNCTIONS																						 #
# You do not need to alter these functions																 #
##########################################################################################################
function resizeImage($image,$width,$height,$scale) {
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
	
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$image); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$image,90); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$image);  
			break;
    }
	chmod($image, 0777);
	return $image;
}
//You do not need to alter these functions
function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$thumb_image_name); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$thumb_image_name,90); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$thumb_image_name);  
			break;
    }
	chmod($thumb_image_name, 0777);
	return $thumb_image_name;
}
//You do not need to alter these functions
function getHeight($image) {
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
}
//You do not need to alter these functions
function getWidth($image) {
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
}

//Image Locations
$large_image_location = $upload_path.$large_image_name.$_SESSION['user_file_ext'];
$thumb_image_location = $upload_path.$thumb_image_name.$_SESSION['user_file_ext'];

//Create the upload directory with the right permissions if it doesn't exist
if(!is_dir($upload_dir)){
	mkdir($upload_dir, 0777);
	chmod($upload_dir, 0777);
}

//Check to see if any images with the same name already exist
if (file_exists($large_image_location)){
	if(file_exists($thumb_image_location)){
		$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name.$_SESSION['user_file_ext']."\" alt=\"Thumbnail Image\"/>";
	}else{
		$thumb_photo_exists = "";
	}
   	$large_photo_exists = "<img src=\"".$upload_path.$large_image_name.$_SESSION['user_file_ext']."\" alt=\"Large Image\"/>";
} else {
   	$large_photo_exists = "";
	$thumb_photo_exists = "";
}

if (isset($_POST["upload"])) { 
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
	$userfile_size = $_FILES['image']['size'];
	$userfile_type = $_FILES['image']['type'];
	if ($userfile_type == 'image/pjpeg' ) $userfile_type = 'image/jpeg';
	$filename = basename($_FILES['image']['name']);
	$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

	$taille = filesize($_FILES['image']['tmp_name']);
	$img_info = @getimagesize($_FILES['image']['tmp_name']);
	
	//Only process if the file is a JPG, PNG or GIF and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
		
		foreach ($allowed_image_types as $mime_type => $ext) {
			//loop through the specified image types and if they match the extension then break out
			//everything is ok so go and check file size
			if($file_ext==$ext && $userfile_type==$mime_type){
				$error .= "";
				break;
			}else{
				$error .= "<br />Seules les images <strong>".$image_ext." ".$userfile_type." ".$mime_type." ".$file_ext." ".$ext."</strong> sont accept&eacute;es<br />";
			}
		}
		//check if the file size is above the allowed limit
		
		if ($userfile_size > ($max_file*1048576)) {
			$error.= "<br />L'image doit faire moins de ".$max_file." MB ";
		}
		if ($userfile_size < (($min_file -1) *1024)) {
			$error.= "<br />L'image doit faire au moins ".$min_file." kB ";
		}	
	
	}else{
		$error .= "<br />S&eacute;lectionnez une image a t&eacute;l&eacute;charger";
	}
// DEBUG
/*
	echo "<pre>taille = $taille";
	echo print_r($img_info);
	echo "</pre>";
*/
	if(($img_info[0]*$img_info[1])>($max_pixels*1000000)){
		$imgtropgrande=true;
		$error .= "<br />Diminuez les dimensions de l'image d'origine<br />MAXI = $max_pixels Mega pixels et $max_file MB";
		$error .= "<br /><a href=\"".$_SERVER['PHP_SELF']."?a=delete&t=".$_SESSION['random_key'].$_SESSION['user_file_ext']."&pompier=$pompier\">retour</a>";
	}else{
		$imgtropgrande=false;
	}
	if(($img_info[0]*$img_info[1])<(($min_pixels -1)*1000)){
		$imgtroppetite=true;
		$error .= "<br />Augmentez les dimensions de l'image d'origine<br />MINI = $min_pixels kilo pixels et $min_file kB";
		$error .= "<br /><a href=\"".$_SERVER['PHP_SELF']."?a=delete&t=".$_SESSION['random_key'].$_SESSION['user_file_ext']."&pompier=$pompier\">retour</a>";
	}else{
		$imgtroppetite=false;
	}		
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){
		if (isset($_FILES['image']['name'])){
			//this file could now has an unknown file extension (we hope it's one of the ones set above!)
			$large_image_location = $large_image_location.".".$file_ext;
			$thumb_image_location = $thumb_image_location.".".$file_ext;
			
			//put the file ext in the session so we know what file to look for once its uploaded
			$_SESSION['user_file_ext']=".".$file_ext;
			
			move_uploaded_file($userfile_tmp, $large_image_location);
			chmod($large_image_location, 0777);
			
			$width = getWidth($large_image_location);
			$height = getHeight($large_image_location);
			//Scale the image if it is greater than the width set above
			if ($width > $max_width){
				$scale = $max_width/$width;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}
			elseif ($width < $min_width or $height < $min_height){
				$scale = $min_width/$width;
				if ( $scale < 1 ) $scale = $min_height/$height;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}
			else{
				$scale = 1;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}
			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}
		//Refresh the page to show the new uploaded image
		header("location:".$_SERVER["PHP_SELF"]."?pompier=$pompier");
		exit();
	}
}

if (isset($_POST["upload_thumbnail"]) && strlen($large_photo_exists)>0) {
	//Get the new coordinates to crop the image.
	$x1 = $_POST["x1"];
	$y1 = $_POST["y1"];
	$x2 = $_POST["x2"];
	$y2 = $_POST["y2"];
	$w = $_POST["w"];
	$h = $_POST["h"];
	//Scale the image to the thumb_width set above
	$scale = $thumb_width/$w;
	$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
	$msg.= '<br />Upload effectué avec succès !';
				$sql = "UPDATE pompier SET
					p_photo = '".str_replace($trombidir,"",$thumb_image_location)."'
					WHERE p_id = $pompier;
					";
				$result=mysql_query($sql);
				insert_log('UPDPHOTO', $pompier);	
	$large_image_location = $upload_path.$large_image_prefix.$_GET['t'];
	if (file_exists($large_image_location)) {
		unlink($large_image_location);
	}	
	//Reload the page again to view the thumbnail
	header("location:".$_SERVER["PHP_SELF"]."?pompier=$pompier&msg=$msg");
	exit();
}


if ($_GET['a']=="delete" && strlen($_GET['t'])>0){
//get the file locations 
	$large_image_location = $upload_path.$large_image_prefix.$_GET['t'];
	$thumb_image_location = $upload_path.$thumb_image_prefix.$_GET['t'];
	if (file_exists($large_image_location)) {
		unlink($large_image_location);
	}
	if (file_exists($thumb_image_location)) {
		unlink($thumb_image_location);
	}
	header("location:".$_SERVER["PHP_SELF"]."?pompier=$pompier");
	exit(); 
}

if ($_GET['a']=="suppr" && strlen($_GET['t'])>0 && isset($_GET['del_photo'])){
//get the file locations 
	$pompier=$_GET['P_ID'];
	$image_location = $upload_path.$_GET['t'].$_SESSION['user_file_ext'];
	if (file_exists($image_location)) {
		unlink($image_location);
		$sql = "UPDATE pompier SET
		p_photo = NULL
		WHERE p_id = $pompier;
		";
		$result=mysql_query($sql);
		$msg .= "<p>La photo a été supprimée</p>";	
		insert_log('DELPHOTO', $pompier);	
	}
	header("location:".$_SERVER["PHP_SELF"]."?pompier=$pompier&msg=$msg");
	exit(); 
}
?>
<?php
$title="$cisname - Votre photo";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
writehead();
?>
<script type="text/javascript" src="js/jquery.js"></script>
<script>
function changeStatus() 
{ 
	what=document.getElementById('image');
	if  (what.value == '' ) {
		document.getElementById("upload").disabled=true;
	}
	else {
		document.getElementById("upload").disabled=false;
	}
}
</script>
</head>
<body onload="opener.document.location.reload();">
<?php
echo EbDeb("Photo de $pompierNomPrenomId");
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
	$current_large_image_width = getWidth($large_image_location);
	$current_large_image_height = getHeight($large_image_location);?>
<script type="text/javascript" src="js/jquery.imgareaselect.min.js"></script>	
<script type="text/javascript">
function preview(img, selection) { 
	var scaleX = <?php echo $thumb_width;?> / selection.width; 
	var scaleY = <?php echo $thumb_height;?> / selection.height; 
	
	$('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
}

$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("Vous devez faire une sélection");
			return false;
		}else{
			return true;
		}
	});
}); 

$(window).load(function () { 
	$('#thumbnail').imgAreaSelect({ x1: 0, y1: 0, x2: 148, y2: 177, handle: true, aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', onSelectChange: preview }); 
	/* $('#thumbnail').imgAreaSelect({ x1: 120, y1: 90, x2: 280, y2: 210 }); */
});

</script>
<?php } // FIN large_photo_exists ?>
<?php
//Display error message if there are any
if(strlen($error)>0){
	echo "\n"."<div id=\"msgError\" style=\"color:red;font-size:1.2em;\"><strong>Erreur !</strong>".$error."</div>";
}
if(strlen($msg)>0){
	echo "\n"."<div id=\"msgInfo\"><strong>Info :</strong>".$msg."</div>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){
	echo "\n"."<img src=\"".$thumb_image_location."\" border=\"0\" />";
	echo "<p><a href=\"".$_SERVER["PHP_SELF"]."?a=delete&t=".$_SESSION['random_key'].$_SESSION['user_file_ext']."&pompier=$pompier&msg=$msg\" >Confirmer l'enregistrement</a></p>";
	//Clear the time stamp session and user file extension
	$_SESSION['random_key']= "";
	$_SESSION['user_file_ext']= "";
}else{
	// Affiche la photo si elle existe
	echo "\n"."<div id=\"AffichePhoto\" style=\"float:right;margin:10px;\">";
	if(file_exists($thumb_image_location.".jpg")){	
		echo "\n"."<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"get\">";
		echo "\n"."<img src=\"".$thumb_image_location.".jpg"."\" border=\"0\" />";
		echo "<br /><label>Cocher pour supprimer</label><input input type='checkbox' name='del_photo'>";
		echo "<input type=\"hidden\" name=\"a\" value=\"suppr\" />";
		echo "<input type=\"hidden\" name=\"t\" value=\"".$thumb_image_name.".jpg\" />";
		echo "<input type=\"hidden\" name=\"pompier\" id=\"pompier\" value=\"$pompier\" />";
		echo "<input type=\"hidden\" name=\"P_ID\" id=\"P_ID\" value=\"$pompier\" />";
		echo "<br /><input type=\"submit\"  value=\"Confirmer\" onclick=\"return confirm('Etes vous sur de vouloir supprimer cette photo ?');\">";
//		echo "\n"."<br /><a href=\"".$_SERVER["PHP_SELF"]."?a=suppr&t=".$thumb_image_name.".jpg&pompier=$pompier&msg=$msg\" onclick=\"return confirm('Etes vous sur de vouloir supprimer cette photo ?');\">Supprimer la photo</a>";		
		echo "\n"."</form>";
	}else{
		echo "\n"."<p>Aucune photo</p>";
	}
	echo "\n"."</div>";
	
	if ( $imgtropgrande or $imgtroppetite) $st="display:none;";
	else $st="";
	
	// DEB Affiche les images pour créer la miniature
	if(strlen($large_photo_exists)>0){?>
		<h2>Cr&eacute;er une miniature</h2>
		<div align="center">
			<img src="<?php echo $upload_path.$large_image_name.$_SESSION['user_file_ext'];?>" 
				style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" style="<?php echo $st ?>"/>
			<div style="border:1px #e5e5e5 solid; float:left; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
				<img src="<?php echo $upload_path.$large_image_name.$_SESSION['user_file_ext'];?>" style="position: relative;" alt="Thumbnail Preview" />
			</div>
			<br style="clear:both;"/>
			<form name="thumbnail" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
				<input type="hidden" name="x1" value="" id="x1" />
				<input type="hidden" name="y1" value="" id="y1" />
				<input type="hidden" name="x2" value="" id="x2" />
				<input type="hidden" name="y2" value="" id="y2" />
				<input type="hidden" name="w" value="" id="w" />
				<input type="hidden" name="h" value="" id="h" />
				<input type="hidden" name="P_ID" id="P_ID" value="<?php echo $pompier; ?>" />
				<input type="submit" name="upload_thumbnail" value="Cr&eacute;er la photo" id="save_thumb" />
			</form>
		</div>
	<hr />
	<?php 	} 
	// FIN Affiche les images pour créer la miniature
	?>
	
	<ol><h3>5 Etapes</h3>
	<li>Chercher la photo sur votre ordinateur avec le bouton parcourir</li>
	<li>Cliquez : Envoyer vers le site</li>
	<li>Sélectionnez une zone sur l'image agrandie avec votre souris</li>
	<li>Cliquez : Créer la photo</li>
	<li>Cliquez sur le lien "Confirmer la création"</li>
	</ol>
	<p style="font-size=-1;">La photo générée fera 148px * 178 px. (taille d'une photo d'identité)</p>
	<form name="photo" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">	
	Photo <input type="file" id="image" name="image" size="30" onchange="javascript:changeStatus();"/>
	<input type="submit" id="upload" name="upload" value="Envoyer vers le site" disabled/>
	<input type="hidden" name="P_ID" id="P_ID" value="<?php echo $pompier; ?>" />
	</form>
<?php 

} 
echo EbFin();
?>
</body>
</html>
