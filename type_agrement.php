<?php
/**  Esecouristes - Juin 2013
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Affichage des types d'agréments disponibles et de la possibilité d'en ajouter/modifier
**/

include_once ("config.php"); 
check_all(0);
writehead();

    require 'lib/autoload.inc.php';
 
    $db = DBFactory::getMysqlConnexionWithPDO();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On �met une alerte � chaque fois qu'une requ�te a �chou�
    
    $manager = new TypeAgrementManager($db);
    
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Configurer les types d'agr&eacute;ments disponibles</title>
        <meta charset="utf-8">
    </head>
    <body>
    <div align="center">
        <h2>Param&eacute;trage des types d'agr&eacute;nements</h2>
	<!-- On affiche le choix de la section  -->

	<form id="ajout_type_agrement" action="upd_type_agrement.php" method="post"> 
        <input type="hidden" name="ta_new" value="1" />
	<input type="submit" value="Ajouter" />	
	</form>

	<table id="type_agrement">
            <tr class="TabHeader"><th>TA_CODE</th><th>Description</th><th>CA_CODE</th></tr>
<?php
	$i == 1;

	//print_r($manager->listerPrestations());
    foreach ($manager->listerTypeAgrement() as $type_agrement)
    	{
    	//print_r($type_agrement);
    	//echo $textcolor;
    	 if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
		  }
		  else {
		  	 $mycolor="#FFFFFF";
		  }	
	?>	
            <tr bgcolor='<?php echo $mycolor ?>' onclick="document.location='upd_type_agrement.php?ta_id=<?php echo $type_agrement->ta_code();?>'"><td><?php echo $type_agrement->ta_code(); ?><td><a href="upd_type_agrement.php?ta_id=<?php echo $type_agrement->ta_code();?>"><?php echo $type_agrement->ta_description(); ?></a></td><td><?php echo $type_agrement->ca_code(); ?></td></tr>
	<?php        
	$i ++;
        //echo $i;
        }
?>
        </table>
	</div>
    </body>
</html>
