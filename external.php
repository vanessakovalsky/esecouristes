<?php

// cette page est un exemple d'application externe
// qui peut etre ouverte automatiquement
// lorsque on se connecte sur ebrigade

$expectedsecretkey="1e1f962ead4278a5d52bb8fcc7699918";
$expectedreferrer="http://127.0.0.1/ebrigade/external_open.php";

if ( $_POST["secretkey"] <> $expectedsecretkey ) {
	echo "Incorrect secret key";
	exit;
}

if ( isset($_POST["ident"]))$ident=$_POST["ident"];
else $ident="";
if ( isset($_POST["password"]))$password=$_POST["password"];
else $password="";
if ( isset($_POST["lastname"]))$lastname=$_POST["lastname"];
else $lastname="";
if ( isset($_POST["firstname"]))$firstname=$_POST["firstname"];
else $firstname="";
if ( isset($_POST["phone"]))$phone=$_POST["phone"];
else $phone="";
if ( isset($_POST["mobile"]))$mobile=$_POST["mobile"];
else $mobile="";
if ( isset($_POST["address"]))$address=$_POST["address"];
else $address="";
if ( isset($_POST["email"]))$email=$_POST["email"];
else $email="";
if ( isset($_POST["city"]))$city=$_POST["city"];
else $city="";
if ( isset($_POST["zipcode"]))$zipcode=$_POST["zipcode"];
else $zipcode="";
if ( isset($_POST["departement"]))$departement=$_POST["departement"];
else $departement="";

echo "
<html>
   <head>
   <script language=JavaScript>
   function check_referrer() {
   		if ( document.referrer != \"$expectedreferrer\") {
   			alert('Wrong referrer');
   			self.location.href=document.referrer;
   		}
   }
   </script>
   </head>
   <body onload=\"check_referrer();\">
	  <br>Nom et prénom: ".$firstname." ".$lastname."
	  <br>Téléphone mobile: ".$mobile."
	  <br>Téléphone fixe: ".$phone."
	  <br>email: ".$email."
	  <br>département: ".$departement."
	  <br>mot de passe crypté: ".$password."
   </body>
 </html>";

?>