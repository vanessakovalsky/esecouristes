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

$manager = new TypeEvenementManager($db);
$manager_categorie = new CategorieEvenementManager($db);
$manager_cat_agre = new TypeAgrementManager($db);
//echo $_GET['te_id'];
// On affiche le type d'évènement ou on l'enregistre ?
if (isset($_GET['te_id'])) {
    $id_te = $_GET['te_id'];
    //echo "l'id du type d'évènement".$id_te;
    $type_evenement = $manager->get($id_te);
    //print_r($type_evenement);
} else { // Si on a voulu enregistrer un type d'évènement
    $type_evenement = new TypeEvenement(array('te_code' => $_POST['te_code'], 'te_libelle' => $_POST['te_libelle'], 'cev_code' => $_POST['categorie'], 'ta_code' => $_POST['categorie-agre'])); // On crée un nouvel objet TypeEvenement
}

//print_r($type_evenement);
// On traite le formulaire

if (isset($_POST['te_code'])) {
    //print_r($type_evenement);

    if (!$type_evenement->libelleValide()) {
        $message = 'La description choisie est invalide.';
        unset($type_evenement);
    } else {
        $manager->saveTypeEvenement($type_evenement);

        $message = $type_evenement->isNew() ? "Le type d'évènement a bien été ajouté !" : "Le type d'évènement a bien été modifié !";
    }
}
?>

<!-- On affiche le formulaire -->
<h2>Ajout ou modification d'un type d'&eacute;v&egrave;nement</h2>
<?php
if (isset($message)) {// On a un message à afficher ? 
    $message_propre = htmlentities($message);
    echo '<p>', $message_propre, '</p>'; // Si oui, on l'affiche
}
?>

<div id="form-type-evenement">
    <form action="upd_type_evenement.php" method="post">
        <fieldset class="bleu-clair">		
            <legend class="TabHeader">Information sur le type d'&eacute;v&egrave;nement</legend>
            <p>
                <label>Code :</label>
                <input type="text" name="te_code" maxlength="50" value="<?php if (isset($type_evenement)) echo $type_evenement->te_code(); ?>" />
            </p>
            <p>
                <label>Libell&eacute; :</label>
                <input type="text" name="te_libelle" maxlength="10" value="<?php if (isset($type_evenement)) echo $type_evenement->te_libelle(); ?>" />
            </p>
            <p>
                <label>Cat&eacute;gorie d'&eacute;v&egrave;nement :</label>
<?php
//print_r($type_evenement); 
$cev_code = $type_evenement->cev_code();
//echo $cev_code;
?>
                <select class="categorie" id="categorie1" name="categorie">
<?php
$i = 1;
foreach ($manager_categorie->listerCategorieEvenement() as $categorie) {
    $cev_categorie = $categorie->cev_code();
    //echo $cev_categorie;
    //echo $cev_code;
    ?><option <?php if ($cev_categorie == $cev_code) {
        echo 'selected';
    } ?> value="<?php echo $categorie->cev_code(); ?>"><?php echo $categorie->cev_description(); ?></option>
                    <?php
                    $i++;
                }
                ?>
                </select>
            </p>
            <p>
                <label>Type d'agr&eacute;ment :</label>
                    <?php $ta_code = $type_evenement->ta_code();
                    //echo $ta_code;
                    ?>
                <select class="categorie" id="categorie-agre" name="categorie-agre">
                    <?php
                    $i = 1;
                    foreach ($manager_cat_agre->listerTypeAgrement() as $categorie) {
                        $ca_categorie = $categorie->ta_code();
                        //echo $ca_categorie;
                        //echo $ta_code;
                        ?><option <?php if ($ca_categorie == $ta_code) {
                    echo 'selected';
                } ?> value="<?php echo $categorie->ta_code(); ?>"><?php echo $categorie->ta_description(); ?></option>
                        <?php
                        $i++;
                    }
                    ?>
                </select>
            </p>
        </fieldset>		

                    <?php
                    if (isset($type_evenement) && !$type_evenement->isNew()) {
                        ?>
            <input type="hidden" name="id_te" value="<?php echo $type_evenement->te_code(); ?>" />
            <input type="submit" value="Enregistrer ce type d'&eacute;v&eagrave;nement" name="modifier" class="submit" />
            <?php
        } else {
            ?>
            <input type="submit" value="Ajouter" class="submit" />
            <?php
        }
        ?>
    </form>
</div>
