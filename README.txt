eBrigade 2.6 : application pour la gestion des disponibilites
des sapeurs pompiers et du personnel de secours
developp�e par Nicolas MARCHE (nico.marche@free.fr) et Jean-Pierre KUNTZ
Copyright 2004-2011 Nicolas MARCHE 

==============================================
=   INSTALLATION INITIALE
==============================================

1 - installer APACHE 2 / MYSQL 5 / PHP 5
	exemple utiliser easyPHP 5.3 sur windows
	ou LAMP sur linux. 
	Les dernieres versions des composants sont recommandees.
	PHP 5.2 et 5.3 sont support�s.
	Attention, PHP 4 et MySQL 4 ne sont plus support�s.
	

2 - dezipper le package sur le disque local 
	exemple C:\www 
	ou /var/www

3 - creer la base de donnees MYSQL 
	* utiliser par exemple phpmyadmin (fourni avec easyPhp)
	* definir un user et un password autre que root avec tous les droits
	* creer la base de donnees avec un character set supportant le fran�ais: latin1_general_cs
	* CREATE DATABASE `ebrigade` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_cs;

4 - definir un alias dans le fichier de configuration de apache httpd.conf
    pointant sur C:\www dont le nom peut etre par exemple "ebrigade"
	#alias
	Alias "/ebrigade" "C:/www/ebrigade"
	<Directory "D:/ebrigade/dev">
	Options Indexes FollowSymLinks Includes
	AllowOverride All
	#Order allow,deny
	Allow from all
	</Directory>

5 - se connecter via l'interface web http://127.0.0.1/ebrigade

6 - renseigner les informations permettant d'acceder a la base de donnees.
	* si les informations sont correctes, alors les tables sont automatiquement creees
	* remarque: sur free.fr seul le password est requis.

7 - s'identifier avec 1234 / 1234 
	* changer tout de suite le mot de passe (Session / Mot de passe)
	* changer l'adresse email de admin (Session / Mes infos)

8 - configurer dans l'ordre:
	* les parametres de configuration (Administration / Configuration)
	** pour pouvoir utiliser un service SMS, il faut creer un compte (de preference clickatell central API -  SMS Gateway )
	* les groupes et leurs habilitations ( Parametrage / Habilitations )
	* les sections (si la configuration choisie pour nbsections le necessite) (Informations / Sections )
	* les types de gardes ou types de competences ( Parametrage / Types de garde )
	* les postes de gardes ou competences ( Parametrage / Postes de garde )
	* les vehicules ( Inventaire /Vehicules)
	* le materile ( Parametrage / Types de materiel)
	* le personnel (le mot de passe par defaut est le meme que l'identifiant) (Personnel / Personnel)
	* les qualifications du personnel ( Parametrage / Affectations )

==============================================
=   MISE A JOUR DE L'APPLICATION
==============================================

Par exemple pour migrer l'application eBrigade de 2.4 � 2.6.
Suivre les etapes suivantes:

1 - Se connecter avec le compte admin via l'interface web
2 - Faire une nouvelle sauvegarde de la Base (menu Administration / Base de donnees)
3 - se deconnecter
4 - dezipper le package sur le disque local (d'abord renommer le repertoire precedent). 
5 - Se connecter en admin, la base de donnees sera automatiquement mise a jour.
7 - En cas d'erreurs, recharger la sauvegarde et executer le fichier pas a pas dans phpMyadmin.
	Il y a certainement des incoherences dans vos donnees, qui doivent etre corrigees.
8 - La nouvelle version est installee.


==============================================
=   release note 2.6 - juillet 2011
==============================================

principales nouveaut�s
----------------------
1 - Cartes de France montrant l'activit� op�rationnelle, utilisation France Map (v3.0). 
2 - Optimisation des performances de l'application, stockage des permissions dans la session
3 - Pagination
4 - D�coupage des �v�nements � jours multiples
  Ceci permet notamment de cr�er un �v�nement et de le d�couper en plusieurs parties. ( Formation, DPS, R�union etc....) 
  Il est possible d' inscrire le personnel, en partie ou en totalit� sur les parties.('Petite horloge' de couleur modifiable)
  Pour chaque b�n�vole il est possible de personnaliser les horaires. 
  Les documents g�n�r�s y compris la facturation et les reports prennent en compte le d�coupage. 
5 - Propri�t� 'DPS inter-associatif :' 'Coche' � activer dans la page de cr�ation du DPS ainsi que 
6 - Deux possibilit�s suppl�mentaires de duplications d'�v�nements (personnel seulement, et mat�riel/v�hicules seulement)
7 - Lien Google Maps sur les �v�nements et les fiches personnel
8 - Date d'expiration des comp�tences
  - sur l'onglet dipl�mes de la page �v�nement formation, ajout de dates d'expirations pour chaque personne 
  - ceci permet de mettre des dates de validit� diff�rentes selon les participants 
  - ces dates sont affich�es sur l'attestation de formation 
  - la validit� de la comp�tence est prolong�e si la nouvelle date est post�rieure � la date de fin de validit� actuelle
9 - Ev�nement public et flux RSS
  Il est possible de cocher un �v�nement et de le rendre 'public'. 
  Cette case a cocher permet d'alimenter un flux rss auquel il est possible de s'abonner avec un lecteur de flux rss, 
  mais aussi que chaque webmestre peut r�cup�rer et int�grer dans son site internet. Voir documentation
  http://sourceforge.net/apps/mediawiki/ebrigade
10 - Historique des actions r�alis�es
  - pour les personnes habilit�es, une nouvelle ic�ne apparait � droite en forme de loupe sur les fiches du personnel 
  - en cliquant sur l'ic�ne, on ouvre la page historique pour la personne concern�e 
  - On peut ainsi savoir qui a fait les modifications suivantes et quand: 
   . Ajout d'une fiche personnel 
   � Modification de fiche personnel 
   � Suppression d'une fiche personnel 
   � Modification de mot de passe 
   � Reg�n�ration de mot de passe 
   � Changement de section 
   � Changement de position 
   � Inscription 
   � D�sinscription 
   � Commentaire sur inscription 
   � Modification Fonction 
   � Ajout comp�tence 
   � Modification comp�tence 
   � Suppression comp�tence
   . Modification de disponibilit�s
11 - Lien Skype
12 - Km v�hicules personnels 
13 - D�finition des comp�tences demand�es sur l'�v�nement.
  Sur la page �v�nement, on peut maintenant d�finir le nombre de personnes demand�es au global et pour chaque comp�tence. 
  Et ceci pour chaque partie de l'�v�nement. Il est possible de modifier les informations en cliquant sur l�ic�ne blanche en fin de ligne.
  Lorsqu'il y a assez de personnes inscrites, alors l'info apparait en vert. Sinon en rouge.
14 - Gestion des �quipes
  On peut maintenant d�finir sur chaque �v�nement (ou renfort) des �quipes, puis affecter le personnel inscrit sur ces �quipes. 
  En cliquant sur l'onglet �quipes de la page information d'un �v�nement, une nouvelle fen�tre s'ouvre qui permet d'ajouter, modifier ou supprimer des �quipes. 
  Le nombre indique combien de personnes ont �t� affect�es � chaque �quipe. 
  Puis sur l'onglet personnel on peut maintenant affecter le personnel � une des �quipes d�finies. 
  Vous pouvez utiliser cette fonctionnalit� seulement sur l'�v�nement principal ou renfort ou sont inscrits les personnels.
15 - Lot de mat�riel
  Cette fonctionnalit� permet, avec votre mat�riel existant, de l'associer dans un lot unique, lui m�me pouvant �tre associ� � un v�hicule, 
  et enfin l'ensemble �tant engag� sur l'�v�nement : soit en engageant le lot, soit en engageant le v�hicule

bugs r�solus
--------------------
- 65 bugs corrig�s 


==============================================
=   release note 2.5 - d�cembre 2010
==============================================

principales nouveaut�s
----------------------
1 - gestion d'une pr�sence partielle du personnel sur les �v�nements
2 - impression des dipl�mes et duplicata de dipl�mes
3 - impression des attestations de formations
4 - impression des ordres de mission
5 - Messagerie instantan�e (chat) + liste des utilisateurs connect�s
6 - Support IPhone / IPad am�lior�
7 - gestion du personnel externe (qui peut suivre des formations de secourisme)
8 - gestion des entreprise clientes
9 - attribution de fonctions au personnel sur les �v�nements et param�trage des fonctions possibles
10 - tra�abilit� accrue des actions importantes des utilisateurs
11 - performances am�lior�es sur les pages �v�nement, sections
12 - v�hicule et mat�riel affect�s � une personne
13 - nombreuses statistiques dans les graphiques Chartdirector, dont l'installation est document�e ici
http://sourceforge.net/apps/mediawiki/ebrigade/index.php?title=Graphiques
14 - certaines comp�tences peuvent �tre modifiables par chaque utilisateur (exemple vaccinations)
15 - ajout de description sur les fonctionnalit�s
16 - Nouveau d�tecteur de navigateurs et OS
17 - gestion des absences possible par heures
18 - duplication des �v�nements (simple ou compl�te avec le personnel, les v�hicules et le mat�riel)
19 - choix de type de contrat et horaire pour le personnel salari�
20 - participation du personnel salari� en tant que b�n�vole ou salari� sur les �v�nements
21 - gestion s�curis�e des documents avec cat�gories et permissions d'acc�s
22 - possibilit� de bloquer la saisie des disponibilit�s pour un mois donn�
23 - cat�gories de messages (normal, informatique, urgent)
24 - gestion des photos d'identit�, fonction de recadrage
25 - possibilit� d'acc�s en lecture seule � l'application ebrigade
26 - gestion du mail secr�tariat pour chaque section, qui re�oit toutes les notoifications
27 - choix des comp�tences devant �tre affich�es sur les �v�nements
28 - cat�gories d'�v�nement (classification)
29 - D�finition du type de DPS (PAPS, PE,ME,GE) Contr�le des agr�ments DPS
30 - am�lioration de l'impression des badges.
31 - possibilit� d'activer/d�sactiver presque toutes les fonctionnalit�s dans le menu Configuration
32 - cartes de france interactive pour localiser le personnel et l'activit� en cours
33 - Nouvelle documentation en ligne
http://sourceforge.net/apps/mediawiki/ebrigade

bugs r�solus
--------------------
- 192 bugs corrig�s 


==============================================
=   release note 2.4 - d�cembre 2009
==============================================

principales nouveautes
----------------------
1- changements graphiques divers, remplacement d'icones et utilisation d'onglets sur les pages personnel, evenement et section.
2- nouvelle fonction de recherche (par nom, par ville, par tel, par competences, par habilitation)
3- gestion du materiel 
 * configurer les types de materiel (parametrage / type de materiel)
 * saisir le materiel dans l'inventaire (inventaire / materiel)
 * ajouter du materiel sur les evenements
4 - comptabilite / facturation
 * cliquer sur l'icone en forme de calculatrice sur la page de chaque evenement pour acceder � ces fonctions
 * edition des devis et factures en format pdf
5 - Gestion des badges
6 - Ajout des membres dans les contacts Windows (cliquer sur l'icone carte de visite sur la fiche personnel)
7 - Export du personnel vers fichier CSV
8 - Export de la fiche evenement sous Excel
9 - Gestion des formations du personnel
10 - Evenements renforts
11 - Export des calendriers au format ical (clique sur l'icone calendrier de la page d'un evenement)
12 - Organigramme de la section modifie avec de nouveaux roles
13 - ajout information sur le sexe masculin/feminin des membres
14 - Photos d'identite
15 - securite accrue (contre injection sql, meilleure confidentialite des informations techniques du systeme)
16 - Gestion des agr�ments de s�curit� civile
17 - Gestion des competences dans le cadre des casernes de sapeurs pompiers
18 - Gestion des roles dans l'organigramme, avec des permissions associees

changements mineurs
------------------
- plus de distinction prioritaire/non prioritaire pour le personnel inscrit a un evenement, mais couleur specifique pour les externes.
- affichage de la documentation a partir du menu aide, date des documents
- support de PHP 5.3.0

principaux bugs resolus
--------------------
- permissions parfois incorrectes sur la gestion des evenements

==============================================
=   release note 2.3 - d�cembre 2008
==============================================

principales nouveautes
----------------------
1 - gestion de plusieurs niveaux hierarchiques de sections dans la meme base de donnees. 
   * exemple: plusieurs centres de secours, plusieurs ADPC.
   * L'application est maintenant utilisable pour un nombre de membres tres important.
   * Gestion d'un organigramme de sections (exemple: national, zonal, regional, departemental, local)
   * les droits d'un utilisateurs sur l'application sont maintenant restreints a sa section (+ ses sous-sections)
   * exemple: un utilisateur ayant le droit de modifier le personnel peut le faire pour le personnel de sa section
	et de ses sous-sections mais pas des autres.
   * exemple 2: un utilisateur ayant le droit 'gestion des vehicules' peut ajouter nu vehicule dans sa section mais 
	pas dans une section d'un niveau superieur.
   * un evenement est visible dans le calendrier par les membres de la section organisatrice et ceux des sous-sections
   * la fonctionnalite "evenement exterieur" a ete ajoutee et permet a celui qui est authorise de creer 
	des evenements en dehors de sa section.
   * un message cree par un agent est visible par les membres de sa section et de ses sous-sections
   * les messages sont tries en fonction des destinataires

2 - l'identifiant ou matricule peut etre maintenant une combinaison de chiffres et lettres au lieu de numerique seulement.

3 - chaque utilisateur peut soi-meme modifier son identifiant (menu Session / Mes Infos).

4 - un responsable peut etre designe pour chaque evenement, il a alors tous les droits sur l'evenement.

5 - securite: les informations personnelles ne sont visibles que par les chefs de sections
	ou par les personnes habilitees a modifier le personnel.

6 - notion de cadre de permanence pour chaque section. Il recoit des notifications pour son secteur. 
	Des droits particuliers et temporaires peuvent etre automatiquement ajoutes.

7 - ajout d'une interface de configuration de la connexion a la base de donnees

8 - possibilite de definir des dates d'expiration des competences et un audit des changements de competences.

9 - fonctionnalite 'Securite locale' permettant de changer les groupes des utilisateurs (sauf admin) et de changer les mots de passe pour sa section.

10 - installation simplifiee: chargement automatique du schema de reference.

11 - upgrade automatique de la base de donnees.

12 - fonction mot de passe perdu, avec regeneration et envoi d'un mail.

13 - securite accrue configurable (longueur et qualite des mots de passes, lock apres X erreurs)

14 - Reporting et statistiques

14 - Gestion des heures de participation effective.
Si un evenement se deroule sur plusieurs jours, il faut pouvoir indiquer le nombre d'heures effectives de presence / activite.
La fiche evenement permet de definir la valeur par defaut pour l'evenement.
(ex : la formation PSC1 est proposee du Samedi 08h00 au Dimanche 12h00, mais le temps reel de formation et de 10 heures.)
La fiche participation permet de modifier le temps de presence des participants

changements mineurs
------------------
- ajout d'information (section et groupe) sur la page 'Mes Infos'
- stockage des fichiers attaches aux messages dans des sous repertoires specifiques.
- ajout d'un identifiant unique M_ID dans la table message.
- ne pas creer un repertoire pour les fichiers attaches des evenements tant qu'il n'y a pas de pieces jointes
- suppression d'une personne, les messages sont reaffectes a ADMIN au lieu d'etre perdus.
- ajout d'un lien vers chaque personne ou chaque vehicule dans la fiche d'un evenement.
- jusqu'a 99 soins / evacuations par DPS
- ajout des parametres de configuration auto_backup et auto_optimize
- sur la page configuration, grades et gardes sont incompatibles avec nbsections 0
- affichage de la liste des membres d'un groupe d'habilitations
- ajout de confirmations avant envoi de mais, d'alertes ou d'inscriptions a des evenements.
- affichage des destinataires d'un mail d'alerte.
- boutons d'envois d'email a partir des fiches personnel ou evenements
- affichage des prochaines inscriptions dans la fiche personnel
- selection de tous les jours ou toutes les nuits dansla page dispos
- calendrier: seules les inscriptions sont affichees
- calendrier: affichage du calendrier des autres utilisateurs possible

bugs resolus
------------
- bug sur PHP 5 (definition du type de fichier avec la balise <?php )
- bug MySQL 5 ( changements lies a la longueur de la cha�ne encrytee pour le mot de passe)
- suppression des personnes avec un espace dans le nom ou le prenom
- format urlencoded dans les emails ( en particulier mauvais affichage des apostrophes)
- identifiant=0 ne doit pas etre autorise, ajout d'un contr�le sur la valeur identifiant ou matricule
- modification d'un evenement : perte des infos sur le nombre de soins et d'evacuations
- permissions 'modifier' et 'supprimer' inversees dans la gestion du personnel
- ajouts de checks sur les adresses email
- impossible de supprimer un vehicule si il y a des espaces dans l'immatruculation
- limitation du nombre de destinataires des emails pour eviter les erreurs sur la fonction mail limitee.
- support des apostrophes dans certains champs de saisie.

==============================================
=   release note 2.2
==============================================

liste des nouveautes
----------------------
1 - Envois de sms ( utilisation d'un compte au choix )
    * parametrage du numero de compte dans la page configuration
    * habilitation configurable pour "envoyer des SMS"
    * 3 fournisseurs de SMS sont utilisables ( clickatell est vivement recommande, le meilleur et le moins cher).

2 - Envoi d'alertes a une categorie de personnel selon qualification et / ou section d'appartenance.


changements mineurs
------------------
- affichage page personnel

bugs resolus
------------
- affichage evenement si habilite 17 mais pas 15
- ajout de contr�les sur l'identifiant (ou matricule)
- filtrage du texte des evenements ( suppression des balises HTML et des ")

==============================================
=   release note 2.1
==============================================

liste des nouveautes
----------------------
1 - ajout type d'evenement: Divers
2 - ajout du type d'evenement "Instructeur pour une formation"
	ces evenements ne sont visibles que par  le personnel 
	ayant une qualification de type 'PAE*'
3 - ajout d'un filtre par jour sur Vehicules/Engagements


==============================================
=   release note 2.0
==============================================

liste des nouveautes
----------------------

1 - evenements
-- possibilite pour l'administrateur d'ajouter et enlever du personnel
-- on ne peut plus se desinscrire
-- ajouter des vehicules sur des evenements
-- possibilite de cloturer les inscriptions ( ou d'ouvrir les inscriptions)
-- ajout d'informations (section organisatrice, numero) 
-- gestion de la riorite du personnel et de l'order d'inscription
-- filtre par section sur les evenements
-- ajout d'une date de fin dans le cas d'un evenement sur plusieurs jours (date de fin facultative)

2 - disponibilites
-- disponibilites par jour: ajout d'un filtre de tri par section et par qualification
-- disponibilites par jour: amelioration ergonomie du tableau
-- disponibilites jour/mois: split dispos mois et jour en 2 pages			
-- disponibilites du mois : ajout liste deroulante choix le jour ou la nuit.

3 - grille de depart par defaut modifiable. Cette grille conditionne les piquets dans "Garde du jour".
4 - configuration possible des sections (ajout, modif, suppression)
5 - parametrage possible des habilitations
6 - notifications par email ( lies aux evenements et aux demandes de CP)
7 - amelioration de l'affichage du calendrier
8 - remplacement des titres de la page d'accueil
9 - ajout de l'ecran engagement des vehicules
10 - purge glissante disponibilite et planning_garde
11 - lien dans mon calendrier vers garde du jour
12 - possibilite pour un agent de modifier son email et son numero de telephone
13 - masquage des grades parametrable
14 - Au lieu de poste de garde, on utilisera dans le cas ou il n'y a pas de garde le terme competence.
16 - configuration dans la base de donnees ( suppression du fichier config_param)


liste des principaux bugs resolus
---------------------------------
1 - crash si ajout de pieces jointes de plus de 5M 
2 - corrections des bugs firefox ( envoi email et graphiques)
3 - correction d'un bug dans le texte des emails pour les apostrophes ou apparaissait "\'" au lieu de '
4 - impossible de creer le premier evenement 

==============================================
=   release note 1.7
==============================================
1 - affichage des parametres de configuration
2 - configuration des equipes


==============================================
=   release note 1.6
==============================================
1 - possibilite de desactiver les menus vehicules, gardes.
2 - configuration des postes

==============================================
=   release note 1.5
==============================================
1 - histogrammes des disponibilites

==============================================
=   release note 1.4
==============================================
1 - possibilite de gestion sans sections ou en 3 sections (parametrable)
2 - sauvegarde automatique declenchee a la premiere connexion du jour
3 - correction de bug : page modification manuelle, lorsque la personne de garde supprime sa disponibilite.
4 - generation d'un script pour creer une nouvelle base (configuration par defaut)
5 - possibilite de decouper la journee en matin et apres midi ou pas (parametrable)

