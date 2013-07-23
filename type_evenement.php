<?php
/**  Esecouristes - Juin 2013
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Affichage des types d'évèeemtn disponibles et de la possibilit� d'en ajouter/modifier
**/

include_once ("config.php"); 
check_all(0);
writehead();

    require 'lib/autoload.inc.php';
 
    $db = DBFactory::getMysqlConnexionWithPDO();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On �met une alerte � chaque fois qu'une requ�te a �chou�
    
    $manager = new TypeEvenementManager($db);
    
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Configurer les types d'&eacute;v&eagrave;nements disponibles</title>
        <meta charset="utf-8">
    </head>
    <body>
    <div align="center">
        <h2>Param&eacute;trage des types d'&eacute;v&egrave;nements</h2>
	<!-- On affiche le choix de la section  -->

	<form id="ajout_type_evenement" action="upd_type_evenement.php" method="post"> 
        <input type="hidden" name="te_new" value="1" />
	<input type="submit" value="Ajouter" />	
	</form>

	<table id="type_evenement">
            <tr class="TabHeader"><th>id</th><th>Libelle</th><th>CEV_CODE</th><th>TA_CODE</th></tr>
<?php
	$i == 1;

	//print_r($manager->listerPrestations());
    foreach ($manager->listerTypeEvenement() as $type_evenement)
    	{
    	//print_r($type_evenement);
    	//echo $textcolor;
    	 if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
		  }
		  else {
		  	 $mycolor="#FFFFFF";
		  }	
	?>	
            <tr bgcolor='<?php echo $mycolor ?>' onclick="document.location='upd_type_evenement.php?te_id=<?php echo $type_evenement->te_code();?>'"><td><?php echo $type_evenement->te_code(); ?><td><a href="upd_type_evenement.php?te_id=<?php echo $type_evenement->te_code();?>"><?php echo $type_evenement->te_libelle(); ?></a></td><td><?php echo $type_evenement->cev_code(); ?></td><td><?php echo $type_evenement->ta_code(); ?></td></tr>
	<?php        
	$i ++;
        //echo $i;
        }
?>
        </table>
	</div>
    </body>
</html>
