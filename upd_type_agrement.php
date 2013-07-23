<?php
/**  Esecouristes - Juin 2013
  Vanessa Kovalsky vanessa.kovalsky@free.fr
  Licence GNU/GPL V3

  Ajout / Modification d'un type d'évènement
 * */
include_once ("config.php");
check_all(0);
writehead();
$mysection = $_SESSION['SES_SECTION'];
get_session_parameters();

require 'lib/autoload.inc.php';
 
$db = DBFactory::getMysqlConnexionWithPDO();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué

$manager = new TypeAgrementManager($db);
$manager_categorie = new CategorieAgrementManager($db);
//echo $_GET['ta_id'];
// On affiche le type d'agrément ou on l'enregistre ?
if (isset($_GET['ta_id'])) {
    $id_ta = $_GET['ta_id'];
    //echo "l'id du type d'agrément".$id_ta;
    $type_agrement = $manager->get($id_ta);
    //print_r($type_agrement)
} else { // Si on a voulu enregistrer un type d'agrément
    $type_agrement = new TypeAgrement(array('ta_code' => $_POST['ta_code'], 'ta_description' => $_POST['ta_description'], 'ca_code' => $_POST['categorie'], 'ta_new' => $_POST['ta_new'])); // On crée un nouvel objet TypeAgrement
}

//print_r($type_agrement);
// On traite le formulaire

if (isset($_POST['ta_code'])) {
    //print_r($type_evenement);

    if (!$type_agrement->libelleValide()) {
        $message = 'La description choisie est invalide.';
        unset($type_agrement);
    } else {
        $manager->saveTypeAgrement($type_agrement);
echo $_POST['ta_new'];
        $message = $type_agrement->isNew($_POST['ta_new']) ? "Le type d'agr&eacute;ment a bien &eacute;t&eacute; ajout&eacute; !" : "Le type d'agr&eacute;ment a bien &eacute;t&eacute; modifi&eacute; !";
    }
}
?>

<!-- On affiche le formulaire -->
<h2>Ajout ou modification d'un type d'agr&eacute;ment</h2>
<?php
if (isset($message)) {// On a un message à afficher ? 
    $message_propre = htmlentities($message);
    echo '<p>', $message_propre, '</p>'; // Si oui, on l'affiche
}
?>

<div id="form-type-agrement">
    <form action="upd_type_agrement.php" method="post">
        <fieldset class="bleu-clair">		
            <legend class="TabHeader">Information sur le type d'agr&eacute;ment</legend>
            <p>
                <label>Code :</label>
                <input type="text" name="ta_code" maxlength="10" value="<?php if (isset($type_agrement)) echo $type_agrement->ta_code(); ?>" />
            </p>
            <p>
                <label>Description :</label>
                <input type="text" name="ta_description" maxlength="40" value="<?php if (isset($type_agrement)) echo $type_agrement->ta_description(); ?>" />
            </p>
            <p>
                <label>Cat&eacute;gorie d'agr&eacute;ment :</label>
<?php
//print_r($type_agrement); 
$ca_code = $type_agrement->ca_code();
//echo $ca_code;
?>
                <select class="categorie" id="categorie1" name="categorie">
<?php
$i = 1;
foreach ($manager_categorie->listerCategorieAgrement() as $categorie) {
    $ca_categorie = $categorie->ca_code();
    //echo $ca_categorie;
    //echo $ca_code;
    ?><option <?php if ($ca_categorie == $ca_code) {
        echo 'selected';
    } ?> value="<?php echo $categorie->ca_code(); ?>"><?php echo $categorie->ca_description(); ?></option>
                    <?php
                    $i++;
                }
                ?>
                </select>
            </p>
        </fieldset>		

                    <?php
        if (isset($type_agrement) && !$type_agrement->isNew($_POST['ta_new'])) {
                        ?>
            <input type="hidden" name="ta_new" value="0" />
            <input type="submit" value="Enregistrer ce type d'agr&eacute;ment" name="modifier" class="submit" />
            <?php
        } else {
            ?>
                        <input type="hidden" name="ta_new" value="1" />
            <input type="submit" value="Ajouter" class="submit" />
            <?php
        }
        ?>
    </form>
</div>
