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
if ( isset($_GET["section"])) $section=intval($_GET["section"]);
else $section="";
if ( isset($_GET["evenement"])) $evenement=mysql_real_escape_string($_GET["evenement"]);
else $evenement="";
if ( isset($_GET["file"])) $file=mysql_real_escape_string($_GET["file"]);
else $file="";
if ( isset($_GET["message"])) $message=intval($_GET["message"]);
else $message="0";
if ( isset($_GET["diplome"])) $diplome=intval($_GET["diplome"]);
else $diplome="0";
if ( isset($_GET["sst"])) $sst=intval($_GET["sst"]);
else $sst="0";

if ( $sst == 1 ) $filepath="images/SST";
else if ( $sst == 2 ) $filepath="images/PSC1";
else if ( $diplome == 1 ) $filepath=$filesdir."/diplomes";
else if ( $message > 0 ) $filepath=$filesdir."/files_message/".$message;
else if ( $evenement <> "" ) $filepath=$filesdir."/files/".$evenement;
else $filepath=$filesdir."/files_section/".$section;

//=====================================================================
// afficher les fichiers stockés même en dehors de la racine web
//=====================================================================
function SendFile($path,$file)
{
  $filefullpath=$path."/".$file;
  //header("Content-Type: " . mime_content_type($file));
  // if you are not allowed to use mime_content_type, then hardcode MIME type
  // use application/octet-stream for any binary file
  // use application/x-executable-file for executables
  // use application/x-zip-compressed for zip files
  header("Content-Type: application/octet-stream");
  header("Content-Length: " . filesize($filefullpath));
  header("Content-Disposition: attachment; filename=\"$file\"");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  if (file_exists($filefullpath)) {
  	  $fp = fopen($filefullpath,"rb");
  	  fpassthru($fp);
  	  fclose($fp);
  }
}

//=====================================================================
// main
//=====================================================================

if ( $diplome == 1 or $message > 0 )
	SendFile($filepath,$file);
else {
	$query="select ds.F_ID, d.D_CREATED_BY from document_security ds, document d
		where d.DS_ID = ds.DS_ID
		and d.S_ID=".$section."
		and d.D_NAME='".$file."'";
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);

	if ( $row["F_ID"] == 0
	 	or check_rights($_SESSION['id'], $row["F_ID"], $section)
	 	or $row["D_CREATED_BY"] == $_SESSION['id']
	 	or get_chef_evenement($evenement) == $_SESSION['id']) 
	 	SendFile($filepath,$file);
	else 
 		write_msgbox("erreur permission",$error_pic,"Vous n'êtes pas autorisés à voir ce fichier <br> 
		<a href=".$basedir."/habilitations.php target=_blank>".$miniquestion_pic."</a> 
		<a href=\"javascript:history.back(1)\">Retour</a>",30,30);
}
?>