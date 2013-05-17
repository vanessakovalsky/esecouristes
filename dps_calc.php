<?php
header('Content-Type: text/html; charset=ISO-8859-1');
header("Cache-Control: no-cache");
 
include_once ("config.php");
check_all(0);

if(isset($_POST['P1'])){
$P1 = (isset($_POST['P1'])?$_POST['P1']:0);
$P2 = (isset($_POST['P2'])?$_POST['P2']:0.25);
$E1 = (isset($_POST['E1'])?$_POST['E1']:0.25);
$E2 = (isset($_POST['E2'])?$_POST['E2']:0.25);
$nbisacteurs=(isset($_POST['dimNbISActeurs'])?$_POST['dimNbISActeurs']:0);
$nbisacteurscom=(isset($_POST['dimNbISActeursCom'])?$_POST['dimNbISActeursCom']:"");
CalcRIS($P1,$P2,$E1,$E2,$nbisacteurs,$nbisacteurscom);
//echo "<pre>";
//echo print_r($_POST);
//echo "</pre>";
//EvenementSave($_POST);
}

if(isset($_GET['evenement'])){
$evenement=$_GET['evenement'];
$organisation=get_section_organisatrice($evenement);
}
if(isset($_POST['evenement'])){
$evenement=$_POST['evenement'];
$organisation=get_section_organisatrice($evenement);
}
else $organisation=0;

if( check_rights($_SESSION['id'], 15, "$organisation")){
   echo "<br><input type=\"submit\" name=\"action\" id=\"btGrille\" value=\"Enregistrer\" 
   style=\"   width:90%;border:thin groove black;background-color:orange;color:blue;\">";

   $actionPrint = (isset($_POST['actionPrint'])?$_POST['actionPrint']:"");
   if($actionPrint=="Modifier"){
      echo "<p>Vous pouvez <a href=\"pdf.php?pdf=DPS&id=$evenement\" target=\"_blank\">
      imprimer la grille</a> &agrave; joindre &agrave; la convention
      </p>
      ";
   }
}
?>