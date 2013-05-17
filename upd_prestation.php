<?php
/**  Esecouristes - Janvier 2013
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Ajout / Modification d'une prestation
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

 // On enregistre notre autoload
    function chargerClasse($classname)
    {
        require './class/'.$classname.'.class.php';
    }
    
    spl_autoload_register('chargerClasse');
    
    $db = new PDO('mysql:host=localhost;dbname=esecouristes', 'root', 'b2Emi0902*');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué
    
    $manager = new PrestationManager($db);
    //echo $_GET['presta_id'];
	if (isset ($_GET['presta_id'])) {
		$id_presta = $_GET['presta_id'];
		//echo "l'id de la presta".$id_presta;
		$presta = $manager->get($id_presta);    
	}
	    
	else // Si on a voulu créer une prestation
	{
        	$presta = new Prestation(array('libelle' => $_POST['libelle'], 'prix' => $_POST['prix'], 'section' => $section, 'id_prestation_parent' => NULL)); // On crée une nouvelle prestation
        }
		
	//print_r($presta);

// On traite le formulaire

	if (isset($_POST['libelle']))
	{        
		//echo $section;
		//print_r($presta);
		
		//echo $presta->prix();
		if (isset($_POST['id_prestation'])) {
			if ($section == $presta->section_id()) {
			$presta_id = $_POST['id_prestation'];
			$presta->setId_prestation($presta_id);
			$presta->setId_prestation_parent($presta_id);
			}
			else {
			$presta_id = $_POST['id_prestation'];
			$presta->setId_prestation_parent($presta_id);
			}
		}			
		$presta->setSection_id($section);
			//echo 'id ajoute';
		

		//print_r($presta);
		if (!$presta->libelleValide())
		{
		    $message = 'Le libellé choisi est invalide.';
		    unset($presta);
		}
		else
		{
		    $manager->savePrestation($presta);
		    
		    $message = $presta->isNew() ? "La prestation a bien été ajoutée !" : "La prestation a bien été modifiée !";
		}
	    
	}	
		
	?>

<!-- On affiche le formulaire -->
<h2>Ajout ou modification d'une prestation</h2>
<?php
if (isset($message)) {// On a un message à afficher ? 
			$message_propre = htmlentities($message);
			echo '<p>', $message_propre, '</p>';// Si oui, on l'affiche
		}

?>

<div id="form-prestation">
        <form action="upd_prestation.php" method="post">
		<fieldset class="bleu-clair">		
		<legend class="TabHeader">Information sur le type de prestation</legend>
        	<p>
        		<label>Libell&eacute; :</label>
			<input type="text" name="libelle" maxlength="50" value="<?php if (isset($presta)) echo $presta->libelle(); ?>" />
		</p>
		<p>
			<label>Prix :</label>
			<input type="text" name="prix" maxlength="10" value="<?php if (isset($presta)) echo $presta->prix(); ?>" />
		</p>	
		</fieldset>		
				
		<?php
			if(isset($presta) && !$presta->isNew())
			{
		?>
				        <input type="hidden" name="id_prestation" value="<?php echo $presta->id_prestation(); ?>" />
				        <input type="submit" value="Enregistrer cette prestation" name="modifier" class="submit" />
		<?php
			}
			else
			{
		?>
				        <input type="submit" value="Ajouter" class="submit" />
		<?php
			}
		?>
        </form>
</div>
