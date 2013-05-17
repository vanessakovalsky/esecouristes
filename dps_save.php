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

header('Content-Type: text/html; charset=ISO-8859-1');
header("Cache-Control: no-cache");
 
include_once ("config.php");
check_all(15);

$evenement=$_POST['evenement'];
if(isset($_POST['P1'])){
$P1 = (isset($_POST['P1'])?$_POST['P1']:0);
$P2 = (isset($_POST['P2'])?$_POST['P2']:0.25);
$E1 = (isset($_POST['E1'])?$_POST['E1']:0.25);
$E2 = (isset($_POST['E2'])?$_POST['E2']:0.25);
$nbisacteurs=(isset($_POST['dimNbISActeurs'])?$_POST['dimNbISActeurs']:0);
$nbisacteurscom=(isset($_POST['dimNbISActeursCom'])?$_POST['dimNbISActeursCom']:"");
//$binomes = (isset($_POST['binomes'])?$_POST['binomes']:0);
//$equipes = (isset($_POST['equipes'])?$_POST['equipes']:0);
//$postes = (isset($_POST['postes'])?$_POST['postes']:0);
EvenementSave($_POST);

if( check_rights($_SESSION['id'], 15) ){
echo "
<p>Vous pouvez <a href=\"pdf.php?pdf=DPS&id=$evenement\" target=\"_blank\">imprimer la grille</a> &agrave; joindre &agrave; la convention
</p>
";
}

}else{
echo "<p>Aucune donnée envoyée...</p>";
}
?>
