<?php
/**  Esecouristes - Aout 2012 
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Affichage des prestations disponibles et de la possibilit� d'en ajouter/modifier
**/

include_once ("config.php"); 
check_all(0);
writehead();
$mysection=$_SESSION['SES_SECTION'];
get_session_parameters();

// On choisit la section concern�e

if (isset ($_POST["section"])) {
   $_SESSION['sectionchoice'] = intval($_POST["section"]);
   $section=intval($_POST["section"]);
}
else if ( isset($_SESSION['sectionchoice']) ) {
   $section=$_SESSION['sectionchoice'];
}
else {$section=$mysection;}

$sectionparent = get_section_parent($section);

//echo $sectionparent;

    require 'lib/autoload.inc.php';
 
    $db = DBFactory::getMysqlConnexionWithPDO();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On �met une alerte � chaque fois qu'une requ�te a �chou�
    
    $manager = new PrestationManager($db);
    
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Configurer les prestations disponibles</title>
        <meta charset="utf-8">
    </head>
    <body>
    <div align="center">
	<h2>Param�trage des prestations</h2>
	<!-- On affiche le choix de la section  -->
	<form action="prestation.php" method="post">
	<?php
	if ($nbsections <> 1 ) {
		  echo " <select id='section' name='section' onchange='this.form.submit()'>";
		  display_children2(-1, 0, $section, $nbmaxlevels, $sectionorder);
		 echo "</select>";
	}
	?>
	<input type='submit' value='Valider' />
	</form>
	<form id="ajout_prestation" action="upd_prestation.php" method="post"> 	
	<input type="submit" value="Ajouter" />	
	</form>

	<table id="prestation">
            <tr class="TabHeader"><th>id</th><th>Libelle</th><th>Prix</th><th>Modifi�?</th></tr>
<?php
	$i == 1;

	//print_r($manager->listerPrestations());
    foreach ($manager->listerPrestations($section, $sectionparent) as $prestation)
    	{
    	//print_r($prestation);
    	//echo $prestation->_id_prestation;
    	//echo $textcolor;
    	 if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
		  }
		  else {
		  	 $mycolor="#FFFFFF";
		  }	
	?>	
        <tr bgcolor='<?php echo $mycolor ?>' onclick="document.location='upd_prestation.php?presta_id=<?php echo $prestation->id_prestation();?>'"><td><?php echo $prestation->id_prestation(); ?><td><a href="upd_prestation.php?presta_id=<?php echo $prestation->id_prestation();?>"><?php echo $prestation->libelle(); ?></a></td><td><?php echo $prestation->prix(); ?></td><td></td></tr>
	<?php        
	$i ++;
        //echo $i;
        }
?>
        </table>
	</div>
    </body>
</html>
