<?php
/**  Esecouristes - Janvier 2013
Vanessa Kovalsky vanessa.kovalsky@free.fr
Licence GNU/GPL V3

Ajout / Modification d'un devis
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

  require 'lib/autoload.inc.php';
 
    $db = DBFactory::getMysqlConnexionWithPDO();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué
    
	$manager_prestation = new PrestationManager($db);
        $manager = new DevisManager($db);
        $manager_prestation_devis = new PrestationDevisManager($db);
    
    //echo $_GET['devis_id'];
	if (isset ($_GET['devis_id'])) {
		//echo "on affiche le devis en formulaire";
		$id_devis = $_GET['devis_id'];
		//echo "l'id du devis".$id_devis;
		$devis = $manager->voirDevis($id_devis);  
		//echo "la section est ".$devis->section_id();
                $prestas = $manager_prestation_devis->listerPrestationsDevis($id_devis);
	}
        
        else if (isset($_POST['id_devis2'])) {
            //echo "on affiche le formulaire des prestations";
		$id_devis = $_POST['id_devis2'];
		//echo "l'id du devis".$id_devis;
		$devis = $manager->voirDevis($id_devis);  
		//echo "la section est ".$devis->section_id(); 
                $prestas = $manager_prestation_devis->listerPrestationsDevis($id_devis);
        }
	
        else // Si on a voulu créer un devis
	{
		//echo "on ajoute un devis";
        	$devis = new Devis(array('evenement_id' => $_POST['evenement_id'], 'montant' => $_POST['montant'], 'section' => $section)); // On crée un nouveau devis
        }
        
	//print_r($devis);

// On traite le formulaire

	if (isset($_POST['montant']) )
	{        
		//echo "on traite le formulaire";
		//echo $section;
		//print_r($devis);
		$id_devis = $_POST['id_devis'];
		//echo "l'id du devis".$id_devis;
		$devis = $manager->voirDevis($id_devis); 
		$devis->setId_devis($id_devis);
			
		$commentaire = $_POST['commentaire'];
		$devis->setCommentaire($commentaire);
				
		$remise_globale = $_POST['remise_globale'];
		$devis->setRemise_globale($remise_globale);

		$date_devis = $_POST['date_devis'];
		$devis->setDate_devis($date_devis);

		$montant = $_POST['montant'];
		$devis->setMontant($montant);

		$statut_devis = $_POST['statut_devis'];
		$devis->setStatut_devis($statut_devis);

		$devis->setSection_id($section);
			//echo 'id ajoute';
		
		//print_r($devis);
		if (!$devis->montantValide())
		{
		    $message = 'Le montant entr&eacute; est invalide.';
		    unset($devis);
		}
		else
		{
		    $manager->saveDevis($devis);
		    
		    $message = $devis->isNew() ? "Le devis a bien &eacute;t&eacute; ajouté !" : "Le devis a bien &eacute;t&eacute; modifi&eacute; !";
		}
	    
	}
        
        if (isset($_POST['total'])) {
            
            //echo "l'id du devis".$_POST['id_devis2'];
            //echo "des prestations arrivent!";
            
            // On créer les objets prestations
           
            //print_r($_POST);
            foreach ($_POST['prestation_devis'] as $prestadevis) {
                
                //echo "passe dans le foreach";
                //print_r($prestadevis);
                
                $presta = new PrestationDevis(array('id_devis' => $_POST['id_devis2'])); // On crée une nouvelle prestation 
                
                $id_devis = $_POST['id_devis2'];
		//echo "l'id du devis".$id_devis;
		$presta->setId_devis($id_devis);
			
		$id_prestation = $prestadevis['id_prestation'];
                //echo $id_prestation;
                //echo $prestadevis['id_prestation'];
		$presta->setId_prestation($id_prestation);
				
		$quantite = $prestadevis['quantite'];
		$presta->setQuantite($quantite);

		$sous_total = $prestadevis['total_ligne'];
		$presta->setSous_total($sous_total);

		//On enregistre les prestations;
                //print_r($presta);
		    $manager_prestation_devis->enregistrerPrestationDevis($presta);
		    
		    $message = "Les prestations ont bien &eacute;t&eacute; enregistr&eacute;es.";
                //unset($presta);    
            }
                            
        }	
		
	?>

<!-- On affiche le formulaire -->
<h2>Ajout ou modification d'un devis</h2>
<?php
if (isset($message)) {// On a un message à afficher ? 
			$message_propre = htmlentities($message);
			echo '<p>', $message_propre, '</p>';// Si oui, on l'affiche
		}
?>


<div id="form-prestation">
        <form action="upd_devis.php" method="post">
		<fieldset class="bleu-clair">		
		<legend class="TabHeader">Information sur le devis pour l'&eacute;v&egrave;nement <?php if (isset($devis)) echo $devis->evenement_nom(); ?></legend>
        	<p>
			<input type="hidden" name="evenement" maxlength="50" readonly value="<?php if (isset($devis)) echo $devis->evenement_id(); ?>" />
		</p>
		<p>
			<label>Commentaire : </label>
			<input type="text" name="commentaire" maxlength="256" value="<?php if (isset($devis)) echo $devis->commentaire(); ?>" />
		</p>
		<p>
			<label>Date du devis : </label>
			<input type="date" name="date_devis" value="<?php if(isset($devis->date_devis)) echo $devis->date_devis(); else echo date('Y-m-d'); ?>"/>
		</p>
		<p>
			<label>Remise globale : </label>
			<input type="text" name="remise_globale" maxlength="50" value="<?php if (isset($devis)) echo $devis->remise_globale(); ?>" />
		<p>
			<label>Montant total :</label>
			<input type="text" name="montant" maxlength="10" value="<?php if (isset($devis)) echo $devis->montant(); ?>" />
		</p>	
		
		<p>
			<label>Statut du devis</label>
			<select name="statut_devis">
				<option value="0">Cr&eacute;&eacute;</option>
				<option value="1">Envoy&eacute;</option>
				<option value="2">Accept&eacute;</option>
				<option value="3">Refus&eacute;</option>
			</select>
		</fieldset>
            
            		<?php
			if(isset($devis) && !$devis->isNew())
			{
		?>
				        <input type="hidden" name="id_devis" value="<?php echo $devis->id_devis(); ?>" />
				        <input type="submit" value="Enregistrer ce devis" name="modifier" class="submit" />
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
	
        <form id="ligne-prestation" action="upd_devis.php" method="post">
            <fieldset class="bleu-clair">
		<legend class="TabHeader">Prestations pour l'&eacute;v&egrave;nement <?php if (isset($devis)) echo $devis->evenement_nom(); ?></legend>		<table>
                
                    <table id="prestation">
                        <tbody>
                        <tr>
				<th>Prestation</th>
				<th>Prix unitaire</th>
				<th>Quantit&eacute;</th>
				<th>Sous-total</th>
			</tr>
                        <?php
                        // On teste pour savoir si des prestations existent pour ce devis
                    if (!empty($prestas)) {
                        //print_r($prestas);
                        //echo "il n'y a pas de prestations pour ce devis";
                        // On boucle sur les prestations pour les afficher
                        $n = 1; //pour incrementer les id des boutton pour le js
                        foreach($prestas as $prestationdevis) {
                            //print_r($prestationdevis);
                            $id_prestation_devis = $prestationdevis->id_prestation();
                            //echo $id_prestation_devis."<br />";
                            $presta_devis = $manager_prestation->get($id_prestation_devis);
                            //print_r($presta_devis);
                            ?>
                            <tr class="ligne-presta">
                            
				<td><select class="presta-devis" id="prestation_devis<?php echo $n; ?>" name="prestation_devis[<?php echo $n; ?>][id_prestation]">
						<option data-prix="<?php echo $presta_devis->prix(); ?>" value="<?php echo $presta_devis->id_prestation();?>"><?php echo $presta_devis->libelle(); ?></option>
				</select></td>
				<td><input type="text" id="prix_unitaire<?php echo $n; ?>" name="prestation_devis[<?php echo $n; ?>][prix_unitaire]" class="prix_unitaire" size="5" readonly value="<?php echo $presta_devis->prix(); ?>" /></td>
                                <td><input type="text" id="quantite<?php echo $n; ?>" name="prestation_devis[<?php echo $n; ?>][quantite]" class="quantite" size="5" maxlenght="10" value="<?php echo $prestationdevis->quantite(); ?>" /></td>
                                <td><input type="text" id="total_ligne<?php echo $n; ?>" name="prestation_devis[<?php echo $n; ?>][total_ligne]" class="total_ligne" size="5" readonly value="<?php echo $prestationdevis->sous_total(); ?>" /></td>
			</tr>
                        <?php
                        $n ++;
                        }
                        //echo "fin du foreach";
                    }    
                        
                    else {    
                    ?>    
			<tr class="ligne-presta">
                            
				<td><select class="presta-devis" id="prestation_devis1" name="prestation_devis[1][id_prestation]">
				<?php
                                    $i = 1;
				    foreach ($manager_prestation->listerPrestations($section, $sectionparent) as $prestation) {
						?><option data-prix="<?php echo $prestation->prix(); ?>" value="<?php echo $prestation->id_prestation();?>"><?php echo $prestation->libelle(); ?></option>
					<?php 
                                        $i ++;
                                    }
					
				?>
				</select></td>
				<td><input type="text" id="prix_unitaire1" name="prestation_devis[1][prix_unitaire]" class="prix_unitaire" size="5" readonly value="0" /></td>
				<td><input type="text" id="quantite1" name="prestation_devis[1][quantite]" class="quantite" size="5" maxlenght="10" value="<?php ?>" /></td>
				<td><input type="text" id="total_ligne1" name="prestation_devis[1][total_ligne]" class="total_ligne" size="5" readonly value="<?php ?>" /></td>
			</tr>
                    <?php
                     }
                        ?>    
                    
                    </tbody>
                </table>
                
                        <button class="ajouter-ligne">+</button>
    Total : <input type="text" class="total" name="total" id="total" size="20" readonly value="<?php ?>" />
    
        <input type="hidden" name="id_devis2" value="<?php echo $devis->id_devis(); ?>" />
        <input type="submit" value="Enregistrer les prestations" name="modifierprestation" class="submit" />
                   
                </fieldset>		
            
       </form>      
</div>
	
<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>

<script type="text/javascript">

$(document).ready(function($)
{
    
    $("button").click(function()
    {
        // add new row to table using addTableRow function
        //alert('rentré dans la fonction onclick');
        addTableRow($("#prestation"));
        //alert('ajoute une ligne');
        
     // prevent button redirecting to new page
    return false;
    });

// function to add a new row to a table by cloning the last row and
// incrementing the name and id values by 1 to make them unique
function addTableRow(table)
{
    // clone the last row in the table
    var $tr = $(table).find("tbody tr:last").clone();
    // get the name attribute for the input and select fields
    $tr.find("input,select").attr("name", function()
    {
        //alert('arrive jusqu au find');
        // break the field name and it's number into two parts
        var parts = this.name.split(/(\D+)(\d+)/);
        //alert(parts);
        
    // create a unique name for the new field by incrementing
        // the number for the previous field by 1
        return parts[1] + ++parts[2] + parts[3];
        // repeat for id attributes
    }).attr("id", function()
    {
    var parts = this.id.match(/(\D+)(\d+)$/);
    return parts[1] + ++parts[2];
    });
    
    $tr.find('[id^="prix_unitaire"]').val('');
    $tr.find('[id^="quantite"]').val('1');
    $tr.find('[id^="total_ligne"]').val('0');
    // append the new row to the table
    $(table).find("tbody tr:last").after($tr);
 };

// On change le input du prix en fonction de la selection dans le select.

    $('.presta-devis').live('change', function(){
       //console.log(this);
       var id = $(this).find(':selected')[0];
       //console.log(id);
       var prix = id.getAttribute('data-prix');
       //console.log(prix);
       
       // On récupère le numéro de la ligne pour les prestas, pour mettre le bon prix sur la bon ligne
       var spresta = this.id.match(/(\D+)(\d+)$/);
       //console.log(spresta[2]);
       $('#prix_unitaire'+ spresta[2]).val(prix);
    
    });
       // On calcule le sous-total de la ligne
       
       //$('.quantite').live('change',function(){
       //console.log(this);
       
       function sousTotal(ligne) {
       
       var id = $(ligne).attr('id');
       //console.log('l id est' + id);
       
       var spresta = ligne.id.match(/(\D+)(\d+)$/);
       //console.log(spresta);
       //console.log(spresta[2])
       var prix = $('#prestation_devis' + spresta[2]).find(':selected')[0].getAttribute('data-prix');
       //console.log(prix);

       var quantite = $(ligne).val();
       //console.log(quantite);
        
       var total = prix * quantite;
       //console.log(total);
        
        $('#total_ligne' + spresta[2]).val(total);
    
        };
    
    // On calcule le total global
    
    function total(calcTotal) {
        var total = 0; 
        //console.log(calcTotal);
        $(".total_ligne").each(function() {
        //console.log(this);
        total += Number($(this).val());
        //console.log(this);
        }); 
        //console.log(total);
        $(".total").val(total);
    };
    
    // On déclenche les fonctions de calculs à chaque modifications sur une quantité
    
    $('.quantite, .presta-devis').live('change',function(){
        //console.log('entre dans la fonction');
        sousTotal(this);
        total(this);
        //console.log('le total a ete calcule');
    })  ;
});

</script>