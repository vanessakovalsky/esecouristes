<?php
/**  Esecouristes - Mars 2013
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Affichage les devis
**/

include_once ("config.php"); 
check_all(0);
writehead();
$mysection=$_SESSION['SES_SECTION'];
get_session_parameters();

// On choisit la section concernée

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

 // On enregistre notre autoload
    function chargerClasse($classname)
    {
        require './class/'.$classname.'.class.php';
    }
    
    spl_autoload_register('chargerClasse');
    
    $db = new PDO('mysql:host=localhost;dbname=esecouristes', 'root', 'b2Emi0902*');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué
    
    $manager = new DevisManager($db);
    
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Les devis</title>
        <meta charset="utf-8">
    </head>
    <body>
    <div align="center">
	<h2>Liste des devis</h2>
	<!-- On affiche le choix de la section  -->
	<form action="devis.php" method="post">
	<?php
	if ($nbsections <> 1 ) {
		  echo " <select id='section' name='section' onchange='this.form.submit()'>";
		  display_children2(-1, 0, $section, $nbmaxlevels, $sectionorder);
		 echo "</select>";
	}
	?>
	<input type='submit' value='Valider' />
	</form>

	<table id="devis">
            <tr class="TabHeader"><th>id</th><th>Nom de l'&eacute;v&egrave;nement</th><th>Prix</th><th>Commentaire</th><th>Date du devis</th><th>Statut</th></tr>
<?php
	$i == 1;

	//print_r($manager->listerDevis());
    foreach ($manager->listerDevis($section) as $devis)
    	{
    	//print_r($devis);
    	//echo $devis->_id_devis;
    	//echo $textcolor;
    	 if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
		  }
		  else {
		  	 $mycolor="#FFFFFF";
		  }	
	?>	
        <tr bgcolor='<?php echo $mycolor ?>' onclick="document.location='upd_devis.php?devis_id=<?php echo $devis->id_devis();?>'"><td><?php echo $devis->id_devis(); ?><td><a href="upd_devis.php?devis_id=<?php echo $devis->id_devis();?>"><?php echo $devis->evenement_nom(); ?></a></td><td><?php echo $devis->montant(); ?></td><td><?php echo $devis->commentaire(); ?></td><td><?php echo $devis->date_devis(); ?><td><?php echo $devis->statut_devis(); ?></td></tr>
	<?php        
	$i ++;
        //echo $i;
        }
?>
        </table>
	</div>
    </body>
</html>
