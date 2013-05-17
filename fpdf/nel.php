<?php

error_reporting(E_ALL);

#
# Fichier : nel.php (Nombres En Lettres)
#
# Auteur : Olivier Miakinen
# Cr�ation : mercredi 2 avril 2003
# Derni�re modification : dimanche 11 novembre 2007
#
# La fonction enlettres($nombre) retourne une cha�ne de caract�res
# repr�sentant le nombre $nombre �crit en toutes lettres, en fran�ais.
#
# Toute la documentation sur trouve sur :
#  http://www.miakinen.net/vrac/nombres
# Un programme de test existe sur :
#  http://www.miakinen.net/vrac/nombres2
#
######################################################################
#
# Je tiens � remercier tout particuli�rement Nicolas Graner pour
# m'avoir fourni le code source de son propre programme d'�criture
# des nombres en lettres.
#
# En effet, c'est � lui que je dois la superbe impl�mentation du
# syst�me de John Horton Conway et Allan Wechsler sous la forme de
# deux ou trois preg_replace().
#
# Voir <http://www.graner.net/nicolas/nombres/nom.php>
# et <http://www.graner.net/nicolas/nombres/nom-exp.php>
#
######################################################################
#
# Correction de dimanche 11 novembre 2007
#	Dans la version 5.2.2 de PHP, le comportement de la fonction
#	substr() a chang�. Auparavant substr($nombre, -6) retournait
#	$nombre dans le cas o� sa longueur �tait inf�rieure � 6, mais
#	maintenant cela retourne false. Merci � Benjamin (de la soci�t�
#	Dreamnex) et � David Duret pour m'avoir signal� le probl�me et
#	sa solution.
#
######################################################################

define('NEL_SEPTANTE',       0x0001);
define('NEL_HUITANTE',       0x0002);
define('NEL_OCTANTE',        0x0004);
define('NEL_NONANTE',        0x0008);
define('NEL_BELGIQUE',       NEL_SEPTANTE|NEL_NONANTE);
define('NEL_VVF',            NEL_SEPTANTE|NEL_HUITANTE|NEL_NONANTE);
define('NEL_ARCHAIQUE',      NEL_SEPTANTE|NEL_OCTANTE|NEL_NONANTE);
define('NEL_SANS_MILLIARD',  0x0010);
define('NEL_AVEC_ZILLIARD',  0x0020);
define('NEL_TOUS_ZILLIONS',  0x0040);
define('NEL_RECTIF_1990',    0x0100);
define('NEL_ORDINAL',        0x0200);
define('NEL_NIEME',          0x0400);

# Le tableau associatif $NEL contient toutes les variables utilis�es
# de fa�on globale dans ce module. ATTENTION : ce nom est assez court,
# et cela pourrait poser des probl�mes de collision avec une autre
# variable si plusieurs modules sont inclus dans le m�me programme.

$NEL = array(
  '1-99' => array(
    # 0-19
    '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept',
    'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze',
    'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf',
    # 20-29
    'vingt', 'vingt et un', 'vingt-deux', 'vingt-trois',
    'vingt-quatre', 'vingt-cinq', 'vingt-six',
    'vingt-sept', 'vingt-huit', 'vingt-neuf',
    # 30-39
    'trente', 'trente et un', 'trente-deux', 'trente-trois',
    'trente-quatre', 'trente-cinq', 'trente-six',
    'trente-sept', 'trente-huit', 'trente-neuf',
    # 40-49
    'quarante', 'quarante et un', 'quarante-deux', 'quarante-trois',
    'quarante-quatre', 'quarante-cinq', 'quarante-six',
    'quarante-sept', 'quarante-huit', 'quarante-neuf',
    # 50-59
    'cinquante', 'cinquante et un', 'cinquante-deux', 'cinquante-trois',
    'cinquante-quatre', 'cinquante-cinq', 'cinquante-six',
    'cinquante-sept', 'cinquante-huit', 'cinquante-neuf',
    # 60-69
    'soixante', 'soixante et un', 'soixante-deux', 'soixante-trois',
    'soixante-quatre', 'soixante-cinq', 'soixante-six',
    'soixante-sept', 'soixante-huit', 'soixante-neuf',
    # 70-79
    'septante', 'septante et un', 'septante-deux', 'septante-trois',
    'septante-quatre', 'septante-cinq', 'septante-six',
    'septante-sept', 'septante-huit', 'septante-neuf',
    # 80-89
    'huitante', 'huitante et un', 'huitante-deux', 'huitante-trois',
    'huitante-quatre', 'huitante-cinq', 'huitante-six',
    'huitante-sept', 'huitante-huit', 'huitante-neuf',
    # 90-99
    'nonante', 'nonante et un', 'nonante-deux', 'nonante-trois',
    'nonante-quatre', 'nonante-cinq', 'nonante-six',
    'nonante-sept', 'nonante-huit', 'nonante-neuf'
  ),

  'illi' => array('', 'm', 'b', 'tr', 'quatr', 'quint', 'sext'),
  'maxilli' => 0,           # voir plus loin
  'de_maxillions' => '',    # voir plus loin

  'septante' => false,  # valeurs possibles : (false|true)
  'huitante' => false,  # valeurs possibles : (false|true|'octante')
  'nonante' => false,   # valeurs possibles : (false|true)
  'zillions' => false,  # valeurs possibles : (false|true)
  'zilliard' => 1,      # valeurs possibles : (0|1|2)
  'rectif' => false,    # valeurs possibles : (false|true)
  'ordinal' => false,   # valeurs possibles : (false|true|'nieme')

  'separateur' => ' '
);

# Si le tableau $NEL['illi'] s'arr�te � 'sext', alors les deux valeurs
# suivantes sont respectivement '6' et ' de sextillions'.
$NEL['maxilli'] = count($NEL['illi']) - 1;
$NEL['de_maxillions'] = " de {$NEL['illi'][$NEL['maxilli']]}illions";

function enlettres_options($options, $separateur=NULL)
{
 global $NEL;

 if ($options !== NULL) {
  $NEL['septante'] = ($options & NEL_SEPTANTE) ? true : false;
  $NEL['huitante'] =
    ($options & NEL_OCTANTE) ? 'octante' :
    (($options & NEL_HUITANTE) ? true : false);
  $NEL['nonante'] = ($options & NEL_NONANTE) ? true : false;
  $NEL['zillions'] = ($options & NEL_TOUS_ZILLIONS) ? true : false;
  $NEL['zilliard'] =
    ($options & NEL_AVEC_ZILLIARD) ? 2 :
    (($options & NEL_SANS_MILLIARD) ? 0 : 1);
  $NEL['rectif'] = ($options & NEL_RECTIF_1990) ? true : false;
  $NEL['ordinal'] =
    ($options & NEL_NIEME) ? 'nieme' :
    (($options & NEL_ORDINAL) ? true : false);
 }

 if ($separateur !== NULL) {
  $NEL['separateur'] = $separateur;
 }
}

function enlettres_par3($par3)
{
 global $NEL;

 if ($par3 == 0) return '';

 $centaine = floor($par3 / 100);
 $par2 = $par3 % 100;
 $dizaine = floor($par2 / 10);

 # On traite � part les particularit�s du fran�ais de r�f�rence
 # 'soixante-dix', 'quatre-vingts' et 'quatre-vingt-dix'.
 $nom_par2 = NULL;
 switch ($dizaine) {
 case 7:
  if ($NEL['septante'] === false) {
   if ($par2 == 71) $nom_par2 = 'soixante et onze';
   else $nom_par2 = 'soixante-' . $NEL['1-99'][$par2 - 60];
  }
  break;
 case 8:
  if ($NEL['huitante'] === false) {
   if ($par2 == 80) $nom_par2 = 'quatre-vingts';
   else $nom_par2 = 'quatre-vingt-' . $NEL['1-99'][$par2 - 80];
  }
  break;
 case 9:
  if ($NEL['nonante'] === false) {
   $nom_par2 = 'quatre-vingt-' . $NEL['1-99'][$par2 - 80];
  }
  break;
 }
 if ($nom_par2 === NULL) {
  $nom_par2 = $NEL['1-99'][$par2];
  if (($dizaine == 8) and ($NEL['huitante'] === 'octante')) {
   $nom_par2 = str_replace('huitante', 'octante', $nom_par2);
  }
 }

 # Apr�s les dizaines et les unit�s, il reste � voir les centaines
 switch ($centaine) {
 case 0: return $nom_par2;
 case 1: return rtrim("cent {$nom_par2}");
 }

 # Assertion : $centaine = 2 .. 9
 $nom_centaine = $NEL['1-99'][$centaine];
 if ($par2 == 0) return "{$nom_centaine} cents";
 return "{$nom_centaine} cent {$nom_par2}";
}

function enlettres_zilli($idx)
{
 # Noms des 0�me � 9�me zillions
 static $petit = array(
    'n', 'm', 'b', 'tr', 'quatr', 'quint', 'sext', 'sept', 'oct', 'non'
 );
 # Composantes des 10�me � 999�me zillions
 static $unite = array(
    '<', 'un<', 'duo<', 'tre<s�',
    'quattuor<', 'quin<', 'se<xs�',
    'septe<mn�', 'octo<', 'nove<mn�'
 );
 static $dizaine = array(
    '', 'n�>d�ci<', 'ms>viginti<', 'ns>triginta<',
    'ns>quadraginta<', 'ns>quinquaginta<', 'n�>sexaginta<',
    'n�>septuaginta<', 'mxs>octoginta<', '�>nonaginta<'
 );
 static $centaine = array(
    '>', 'nxs>cent', 'n�>ducent', 'ns>tr�cent',
    'ns>quadringent', 'ns>quingent', 'n�>sescent',
    'n�>septingent', 'mxs>octingent', '�>nongent'
 );

 # R�gles d'assimilation aux pr�fixes latins, modifi�es pour accentuer
 # un �ventuel '�' de fin de pr�fixe.
 # (1) Si on trouve une lettre deux fois entre < > on la garde.
 #     S'il y a plusieurs lettres dans ce cas, on garde la premi�re.
 # (2) Sinon on efface tout ce qui est entre < >.
 # (3) On remplace "tre�" par "tr�", "se�" par "s�", "septe�" par "sept�"
 #     et "nove�" par "nov�".
 # (4) En cas de dizaine sans centaine, on supprime la voyelle en trop.
 #     Par exemple "d�ciilli" devient "d�cilli" et "trigintailli" devient
 #     "trigintilli".
 #
 # Il est � noter que ces r�gles PERL (en particulier la premi�re qui
 # est la plus complexe) sont *tr�s* fortement inspir�es du programme
 # de Nicolas Graner. On pourrait m�me parler de plagiat s'il n'avait
 # pas �t� au courant que je reprenais son code.
 # Voir <http://www.graner.net/nicolas/nombres/nom.php>
 # et <http://www.graner.net/nicolas/nombres/nom-exp.php>
 #
 static $recherche = array(
  '/<[a-z�]*?([a-z�])[a-z�]*\1[a-z�]*>/',       # (1)
  '/<[a-z�]*>/',                                # (2)
  '/e�/',                                       # (3)
  '/[ai]illi/'                                  # (4)
 );
 static $remplace = array(
  '\\1',                                        # (1)
  '',                                           # (2)
  '�',                                          # (3)
  'illi'                                        # (4)
 );

 $nom = '';
 while ($idx > 0) {
  $p = $idx % 1000;
  $idx = floor($idx/1000);

  if ($p < 10) {
   $nom = $petit[$p] . 'illi' . $nom;
  } else {
   $nom = $unite[$p % 10] . $dizaine[floor($p/10) % 10]
        . $centaine[floor($p/100)] . 'illi' . $nom;
  }
 }
 return preg_replace($recherche, $remplace, $nom);
}

function enlettres_illions($idx)
{
 global $NEL;

 if ($idx == 0) {
  return '';
 }

 if ($NEL['zillions']) {
  return enlettres_zilli($idx) . 'ons';
 }

 $suffixe = '';
 while ($idx > $NEL['maxilli']) {
  $idx -= $NEL['maxilli'];
  $suffixe .= $NEL['de_maxillions'];
 }
 return "{$NEL['illi'][$idx]}illions{$suffixe}";
}

function enlettres_avec_illiards($idx)
{
 global $NEL;

 if ($idx == 0) return false;
 switch ($NEL['zilliard']) {
 case 0: return false;
 case 2: return true;
 }
 return ($idx == 1);
}

function enlettres($nombre, $options=NULL, $separateur=NULL)
{
 global $NEL;

 if ($options !== NULL or $separateur !== NULL) {
  $NELsave = $NEL;
  enlettres_options($options, $separateur);
  $nom = enlettres($nombre);
  $NEL = $NELsave;
  return $nom;
 }

 # On ne garde que les chiffres, puis on supprime les 0 du d�but
 $nombre = preg_replace('/[^0-9]/', '', $nombre);
 $nombre = ltrim($nombre, '0');

 if ($nombre == '') {
  if ($NEL['ordinal'] === 'nieme') return 'z�ro��me';
  else return 'z�ro';
 }

 $table_noms = array();
 for ($idx = 0; $nombre != ''; $idx++) {
  $par6 = (int)((strlen($nombre) < 6) ? $nombre : substr($nombre, -6));
  $nombre = substr($nombre, 0, -6);

  if ($par6 == 0) continue;

  $nom_par3_sup = enlettres_par3(floor($par6 / 1000));
  $nom_par3_inf = enlettres_par3($par6 % 1000);

  $illions = enlettres_illions($idx);
  if (enlettres_avec_illiards($idx)) {
   if ($nom_par3_inf != '') {
    $table_noms[$illions] = $nom_par3_inf;
   }
   if ($nom_par3_sup != '') {
    $illiards = preg_replace('/illion/', 'illiard', $illions, 1);
    $table_noms[$illiards] = $nom_par3_sup;
   }
  } else {
   switch($nom_par3_sup) {
   case '':
    $nom_par6 = $nom_par3_inf;
    break;
   case 'un':
    $nom_par6 = rtrim("mille {$nom_par3_inf}");
    break;
   default:
    $nom_par3_sup = preg_replace('/(vingt|cent)s/', '\\1', $nom_par3_sup);
    $nom_par6 = rtrim("{$nom_par3_sup} mille {$nom_par3_inf}");
    break;
   }
   $table_noms[$illions] = $nom_par6;
  }
 }

 $nom_enlettres = '';
 foreach ($table_noms as $nom => $nombre) {
  ##
  # $nombre est compris entre 'un' et
  # 'neuf cent nonante-neuf mille neuf cent nonante-neuf'
  # (ou variante avec 'quatre-vingt-dix-neuf')
  ##
  # $nom peut valoir '', 'millions', 'milliards', 'billions', ...
  # 'sextillions', 'sextilliards', 'millions de sextillions',
  # 'millions de sextilliards', etc.
  ##

  # Rectifications orthographiques de 1990
  if ($NEL['rectif']) {
   $nombre = str_replace(' ', '-', $nombre);
  }

  # Nom (�ventuel) et accord (�ventuel) des substantifs
  $nom = rtrim("{$nombre} {$nom}");
  if ($nombre == 'un') {
   # Un seul million, milliard, etc., donc au singulier
   # noter la limite de 1 remplacement, pour ne supprimer que le premier 's'
   # dans 'billions de sextillions de sextillions'
   $nom = preg_replace('/(illion|illiard)s/', '\\1', $nom, 1);
  }

  # Ajout d'un s�parateur entre chaque partie
  if ($nom_enlettres == '') {
   $nom_enlettres = $nom;
  } else {
   $nom_enlettres = $nom . $NEL['separateur'] . $nom_enlettres;
  }
 }

 if ($NEL['ordinal'] === false) {
  # Nombre cardinal : le traitement est fini
  return $nom_enlettres;
 }

 # Aucun pluriel dans les ordinaux
 $nom_enlettres =
   preg_replace('/(cent|vingt|illion|illiard)s/', '\\1', $nom_enlettres);

 if ($NEL['ordinal'] !== 'nieme') {
  # Nombre ordinal simple (sans '-i�me')
  return $nom_enlettres;
 }

 if ($nom_enlettres === 'un') {
  # Le f�minin n'est pas trait� ici. On fait la supposition
  # qu'il est plus facile de traiter ce cas � part plut�t
  # que de rajouter une option rien que pour �a.
  return 'premier';
 }

 switch (substr($nom_enlettres, -1)) {
 case 'e':
  # quatre, onze � seize, trente � nonante, mille
  # exemple : quatre -> quatri�me
  return substr($nom_enlettres, 0, -1) . 'i�me';
 case 'f':
  # neuf -> neuvi�me
  return substr($nom_enlettres, 0, -1) . 'vi�me';
 case 'q':
  # cinq -> cinqui�me
  return $nom_enlettres . 'ui�me';
 }

 # Tous les autres cas.
 # Exemples: deuxi�me, troisi�me, vingti�me, trente et uni�me,
 #           neuf centi�me, un millioni�me, quatre-vingt milliardi�me.
 return $nom_enlettres . 'i�me';
}

?>
