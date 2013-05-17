#====================================================
#  REFERENCE DATABASE
#====================================================
# written by: Nicolas MARCHE, Jean-Pierre KUNTZ
# contact: nico.marche@free.fr
# project: ebrigade
# homepage: http://sourceforge.net/projects/ebrigade/
# version: 2.6

# Copyright (C) 2004, 2011 Nicolas MARCHE
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

# ----------------------------------------------------------
# MYSQL Database dump
# Server : localhost
# Database : ebrigade
# Db version : 2.6
# Date : 19 Jul 2011 at 21:45
# Dump Host : HP_NICO
# ----------------------------------------------------------



# ------------------------------------
# structure for table 'agrement'
# ------------------------------------

DROP TABLE IF EXISTS agrement ;
CREATE TABLE agrement (
TA_CODE varchar(5) NOT NULL,
S_ID smallint(6) DEFAULT '0' NOT NULL,
A_DEBUT date,
A_FIN date,
TAV_ID smallint(6),
PRIMARY KEY (S_ID, TA_CODE),
KEY TA_CODE (TA_CODE)
);
# ------------------------------------
# data for table 'agrement'
# ------------------------------------



# ------------------------------------
# structure for table 'audit'
# ------------------------------------

DROP TABLE IF EXISTS audit ;
CREATE TABLE audit (
P_ID int(11) DEFAULT '0' NOT NULL,
A_DEBUT datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
A_FIN datetime,
A_OS varchar(50),
A_BROWSER varchar(50),
A_LAST_PAGE varchar(30),
PRIMARY KEY (P_ID, A_DEBUT),
KEY A_DEBUT (A_DEBUT),
KEY A_FIN (A_FIN)
);
# ------------------------------------
# data for table 'audit'
# ------------------------------------


# ------------------------------------
# structure for table 'badge_list'
# ------------------------------------

DROP TABLE IF EXISTS badge_list ;
CREATE TABLE badge_list (
P_ID int(11) NOT NULL,
DATE date NOT NULL,
S_ID smallint(6) NOT NULL,
P_PHOTO varchar(50),
PRIMARY KEY (P_ID, DATE)
);
# ------------------------------------
# data for table 'badge_list'
# ------------------------------------



# ------------------------------------
# structure for table 'calendrier'
# ------------------------------------

DROP TABLE IF EXISTS calendrier ;
CREATE TABLE calendrier (
CAL_DATE date DEFAULT '0000-00-00' NOT NULL,
CAL_COMMENT varchar(30),
PRIMARY KEY (CAL_DATE)
);
# ------------------------------------
# data for table 'calendrier'
# ------------------------------------

INSERT INTO calendrier VALUES ('2007-01-01','1er janvier');
INSERT INTO calendrier VALUES ('2008-01-01','1er janvier');
INSERT INTO calendrier VALUES ('2009-01-01','1er janvier');
INSERT INTO calendrier VALUES ('2010-01-01','1er janvier');
INSERT INTO calendrier VALUES ('2011-01-01','1er janvier');
INSERT INTO calendrier VALUES ('2012-01-01','1er janvier');
INSERT INTO calendrier VALUES ('2013-01-01','1er janvier');
INSERT INTO calendrier VALUES ('2014-01-01','1er janvier');
INSERT INTO calendrier VALUES ('2015-01-01','1er janvier');
INSERT INTO calendrier VALUES ('2007-05-01','fête du travail');
INSERT INTO calendrier VALUES ('2008-05-01','fête du travail');
INSERT INTO calendrier VALUES ('2009-05-01','fête du travail');
INSERT INTO calendrier VALUES ('2010-05-01','fête du travail');
INSERT INTO calendrier VALUES ('2011-05-01','fête du travail');
INSERT INTO calendrier VALUES ('2012-05-01','fête du travail');
INSERT INTO calendrier VALUES ('2013-05-01','fête du travail');
INSERT INTO calendrier VALUES ('2014-05-01','fête du travail');
INSERT INTO calendrier VALUES ('2015-05-01','fête du travail');
INSERT INTO calendrier VALUES ('2007-05-08','victoire 45');
INSERT INTO calendrier VALUES ('2008-05-08','victoire 45');
INSERT INTO calendrier VALUES ('2009-05-08','victoire 45');
INSERT INTO calendrier VALUES ('2010-05-08','victoire 45');
INSERT INTO calendrier VALUES ('2011-05-08','victoire 45');
INSERT INTO calendrier VALUES ('2012-05-08','victoire 45');
INSERT INTO calendrier VALUES ('2013-05-08','victoire 45');
INSERT INTO calendrier VALUES ('2014-05-08','victoire 45');
INSERT INTO calendrier VALUES ('2015-05-08','victoire 45');
INSERT INTO calendrier VALUES ('2007-08-15','15 août');
INSERT INTO calendrier VALUES ('2008-08-15','15 août');
INSERT INTO calendrier VALUES ('2009-08-15','15 août');
INSERT INTO calendrier VALUES ('2010-08-15','15 août');
INSERT INTO calendrier VALUES ('2011-08-15','15 août');
INSERT INTO calendrier VALUES ('2012-08-15','15 août');
INSERT INTO calendrier VALUES ('2013-08-15','15 août');
INSERT INTO calendrier VALUES ('2014-08-15','15 août');
INSERT INTO calendrier VALUES ('2015-08-15','15 août');
INSERT INTO calendrier VALUES ('2007-11-01','toussaint');
INSERT INTO calendrier VALUES ('2008-11-01','toussaint');
INSERT INTO calendrier VALUES ('2009-11-01','toussaint');
INSERT INTO calendrier VALUES ('2010-11-01','toussaint');
INSERT INTO calendrier VALUES ('2011-11-01','toussaint');
INSERT INTO calendrier VALUES ('2012-11-01','toussaint');
INSERT INTO calendrier VALUES ('2013-11-01','toussaint');
INSERT INTO calendrier VALUES ('2014-11-01','toussaint');
INSERT INTO calendrier VALUES ('2015-11-01','toussaint');
INSERT INTO calendrier VALUES ('2007-11-11','armistice');
INSERT INTO calendrier VALUES ('2008-11-11','armistice');
INSERT INTO calendrier VALUES ('2009-11-11','armistice');
INSERT INTO calendrier VALUES ('2010-11-11','armistice');
INSERT INTO calendrier VALUES ('2011-11-11','armistice');
INSERT INTO calendrier VALUES ('2012-11-11','armistice');
INSERT INTO calendrier VALUES ('2013-11-11','armistice');
INSERT INTO calendrier VALUES ('2014-11-11','armistice');
INSERT INTO calendrier VALUES ('2015-11-11','armistice');
INSERT INTO calendrier VALUES ('2007-12-25','Noël');
INSERT INTO calendrier VALUES ('2008-12-25','Noël');
INSERT INTO calendrier VALUES ('2009-12-25','Noël');
INSERT INTO calendrier VALUES ('2010-12-25','Noël');
INSERT INTO calendrier VALUES ('2011-12-25','Noël');
INSERT INTO calendrier VALUES ('2012-12-25','Noël');
INSERT INTO calendrier VALUES ('2013-12-25','Noël');
INSERT INTO calendrier VALUES ('2014-12-25','Noël');
INSERT INTO calendrier VALUES ('2015-12-25','Noël');


# ------------------------------------
# structure for table 'categorie_agrement'
# ------------------------------------

DROP TABLE IF EXISTS categorie_agrement ;
CREATE TABLE categorie_agrement (
CA_CODE varchar(5) NOT NULL,
CA_DESCRIPTION varchar(40) NOT NULL,
CA_FLAG tinyint(4) DEFAULT '0' NOT NULL,
PRIMARY KEY (CA_CODE)
);
# ------------------------------------
# data for table 'categorie_agrement'
# ------------------------------------

INSERT INTO categorie_agrement VALUES ('SEC','Agréments de sécurité civile','0');
INSERT INTO categorie_agrement VALUES ('FOR','Formations au secourisme','0');
INSERT INTO categorie_agrement VALUES ('CON','Conventions de missions','0');
INSERT INTO categorie_agrement VALUES ('ASS','Informations liées à l\'association','0');


# ------------------------------------
# structure for table 'categorie_evenement'
# ------------------------------------

DROP TABLE IF EXISTS categorie_evenement ;
CREATE TABLE categorie_evenement (
CEV_CODE varchar(5) NOT NULL,
CEV_DESCRIPTION varchar(40) NOT NULL,
PRIMARY KEY (CEV_CODE)
);
# ------------------------------------
# data for table 'categorie_evenement'
# ------------------------------------

INSERT INTO categorie_evenement VALUES ('C_SEC','opérations de secours');
INSERT INTO categorie_evenement VALUES ('C_OPE','autres activités opérationnelles');
INSERT INTO categorie_evenement VALUES ('C_FOR','activités de formation');
INSERT INTO categorie_evenement VALUES ('C_DIV','divers');


# ------------------------------------
# structure for table 'categorie_evenement_affichage'
# ------------------------------------

DROP TABLE IF EXISTS categorie_evenement_affichage ;
CREATE TABLE categorie_evenement_affichage (
CEV_CODE varchar(5) NOT NULL,
EQ_ID smallint(6) NOT NULL,
FLAG1 tinyint(4) DEFAULT '1' NOT NULL,
PRIMARY KEY (CEV_CODE, EQ_ID)
);
# ------------------------------------
# data for table 'categorie_evenement_affichage'
# ------------------------------------

INSERT INTO categorie_evenement_affichage VALUES ('C_DIV','1','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_FOR','1','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_OPE','1','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_SEC','1','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_DIV','2','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_FOR','2','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_OPE','2','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_SEC','2','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_DIV','3','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_FOR','3','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_OPE','3','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_SEC','3','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_DIV','4','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_FOR','4','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_OPE','4','1');
INSERT INTO categorie_evenement_affichage VALUES ('C_SEC','4','1');


# ------------------------------------
# structure for table 'categorie_materiel'
# ------------------------------------

DROP TABLE IF EXISTS categorie_materiel ;
CREATE TABLE categorie_materiel (
TM_USAGE varchar(15) NOT NULL,
CM_DESCRIPTION varchar(50) NOT NULL,
PICTURE_LARGE varchar(50),
PICTURE_SMALL varchar(50),
PRIMARY KEY (TM_USAGE)
);
# ------------------------------------
# data for table 'categorie_materiel'
# ------------------------------------

INSERT INTO categorie_materiel VALUES ('ALL','tous types de matériel','engine.png','smallengine.png');
INSERT INTO categorie_materiel VALUES ('Hébergement','matériel d\'hébergement','house.png','smallhouse.png');
INSERT INTO categorie_materiel VALUES ('Logistique','matériel de logistique','logistic.png','smalllogistic.png');
INSERT INTO categorie_materiel VALUES ('Eclairage','matériel d\'éclairage','light.png','smalllight.png');
INSERT INTO categorie_materiel VALUES ('Eléctrique','matériel éléctrique','energy.png','smallenergy.png');
INSERT INTO categorie_materiel VALUES ('Transmission','matériel d\'émission/transmission','phone.png','smallphone.png');
INSERT INTO categorie_materiel VALUES ('Sanitaire','matériel médical','bag.png','smallbag.png');
INSERT INTO categorie_materiel VALUES ('Formation','matériel de formation','board.png','smallboard.png');
INSERT INTO categorie_materiel VALUES ('Pompage','matériel de pompage','engine.png','smallengine.png');
INSERT INTO categorie_materiel VALUES ('Elagage','matériel d\'élagage','elagage.png','smallelagage.png');
INSERT INTO categorie_materiel VALUES ('Informatique','matériel informatique','mycomputer.png','smallmycomputer.png');
INSERT INTO categorie_materiel VALUES ('Sauvetage','Lots de sauvetage','rescue.png','smallrescue.png');
INSERT INTO categorie_materiel VALUES ('Déblais','matériel de déblaiement','engine.png','smallengine.png');
INSERT INTO categorie_materiel VALUES ('Incendie','matériel d\'incendie','fire.png','smallfire.png');
INSERT INTO categorie_materiel VALUES ('Divers','matériel divers','divers.png','smalldivers.png');
INSERT INTO categorie_materiel VALUES ('Habillement','tenues vestimentaires','uniform.png','smalluniform.png');
INSERT INTO categorie_materiel VALUES ('Aquatique','matériel aquatique','aquatique.png','smallaquatique.png');


# ------------------------------------
# structure for table 'chat'
# ------------------------------------

DROP TABLE IF EXISTS chat ;
CREATE TABLE chat (
C_ID int(11) NOT NULL auto_increment,
P_ID int(11) NOT NULL,
C_MSG varchar(500) NOT NULL,
C_DATE datetime NOT NULL,
C_COLOR varchar(20) NOT NULL,
PRIMARY KEY (C_ID)
);
# ------------------------------------
# data for table 'chat'
# ------------------------------------



# ------------------------------------
# structure for table 'company'
# ------------------------------------

DROP TABLE IF EXISTS company ;
CREATE TABLE company (
C_ID int(11) NOT NULL,
TC_CODE varchar(8) NOT NULL,
C_NAME varchar(30) NOT NULL,
S_ID int(11) NOT NULL,
C_DESCRIPTION varchar(80),
C_ADDRESS varchar(150),
C_ZIP_CODE varchar(150),
C_CITY varchar(30),
C_EMAIL varchar(60),
C_PHONE varchar(60),
C_FAX varchar(20),
C_CONTACT_NAME varchar(50),
C_CREATED_BY int(11),
C_CREATE_DATE date,
C_PARENT int(11),
C_SIRET varchar(20),
PRIMARY KEY (C_ID),
   UNIQUE S_ID (S_ID, C_NAME),
KEY TC_CODE (TC_CODE),
KEY C_PARENT (C_PARENT)
);
# ------------------------------------
# data for table 'company'
# ------------------------------------

INSERT INTO company VALUES ('0','PARTIC','Particulier','0','Ne fait pas partie d\'une entreprise',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);


# ------------------------------------
# structure for table 'company_role'
# ------------------------------------

DROP TABLE IF EXISTS company_role ;
CREATE TABLE company_role (
C_ID int(11) NOT NULL,
TCR_CODE varchar(5) NOT NULL,
P_ID int(11) NOT NULL,
PRIMARY KEY (C_ID, TCR_CODE),
KEY TC_CODE (P_ID)
);
# ------------------------------------
# data for table 'company_role'
# ------------------------------------



# ------------------------------------
# structure for table 'configuration'
# ------------------------------------

DROP TABLE IF EXISTS configuration ;
CREATE TABLE configuration (
ID int(11) DEFAULT '0' NOT NULL,
NAME varchar(30) NOT NULL,
VALUE varchar(150),
DESCRIPTION varchar(255),
ORDERING smallint(6) DEFAULT '100' NOT NULL,
PRIMARY KEY (ID)
);
# ------------------------------------
# data for table 'configuration'
# ------------------------------------

INSERT INTO configuration VALUES ('-1','already_configured','0','Application déjà configurée','100');
INSERT INTO configuration VALUES ('1','version','2.6','version installée','1');
INSERT INTO configuration VALUES ('2','nbsections','1','type d\'organisation','2');
INSERT INTO configuration VALUES ('3','gardes','1','activer une gestion des gardes (pompiers seulement)','3');
INSERT INTO configuration VALUES ('4','vehicules','1','activer la gestion des véhicules','4');
INSERT INTO configuration VALUES ('5','grades','1','activer la notion de grades (pompiers seulement)','5');
INSERT INTO configuration VALUES ('6','cisname','eBrigade','nom du centre ou de l\'association','22');
INSERT INTO configuration VALUES ('7','cisurl','ebrigade.org','adresse du site web','16');
INSERT INTO configuration VALUES ('8','admin_email','admin@ebrigade.org','adresse mail de l\'administrateur','17');
INSERT INTO configuration VALUES ('9','sms_provider','','fournisseur SMS','18');
INSERT INTO configuration VALUES ('10','sms_user','','utilisateur du compte SMS','19');
INSERT INTO configuration VALUES ('11','sms_password','','mot de passe du compte SMS','20');
INSERT INTO configuration VALUES ('12','sms_api_id','','api_id SMS (clickatell seulement)','21');
INSERT INTO configuration VALUES ('13','auto_backup','1','sauvegarde quotidienne (possible pour bases de maximum 3Mo)','11');
INSERT INTO configuration VALUES ('14','auto_optimize','1','optimisation quotidienne des indexes et données de la base','12');
INSERT INTO configuration VALUES ('15','password_quality','0','interdiction des mots de passes trop simples','13');
INSERT INTO configuration VALUES ('16','password_length','0','longueur minimum des mots de passes','14');
INSERT INTO configuration VALUES ('17','password_failure','0','bloquage temporaire du compte après échecs d\'authentification','15');
INSERT INTO configuration VALUES ('18','materiel','1','activer la gestion du matériel (véhicules requis)','6');
INSERT INTO configuration VALUES ('19','chat','1','activer la communication par chat','7');
INSERT INTO configuration VALUES ('20','identpage','index.php','URL de la page d\'identification','23');
INSERT INTO configuration VALUES ('21','filesdir','.','répertoire secret contenant les documents, peut être hors de la racine du site si le chemin est absolu.','24');
INSERT INTO configuration VALUES ('22','evenements','1','activer gestion des événements et calendrier','8');
INSERT INTO configuration VALUES ('23','competences','1','activer gestion des compétences','9');
INSERT INTO configuration VALUES ('24','disponibilites','1','activer gestion des disponibilités','10');
INSERT INTO configuration VALUES ('25','log_actions','1','Garder un historique des actions réalisées','12');


# ------------------------------------
# structure for table 'demande'
# ------------------------------------

DROP TABLE IF EXISTS demande ;
CREATE TABLE demande (
P_ID int(11) NOT NULL,
D_TYPE varchar(20) NOT NULL,
D_DATE datetime NOT NULL,
D_SECRET varchar(30) NOT NULL,
PRIMARY KEY (P_ID, D_TYPE)
);
# ------------------------------------
# data for table 'demande'
# ------------------------------------



# ------------------------------------
# structure for table 'diplome_param'
# ------------------------------------

DROP TABLE IF EXISTS diplome_param ;
CREATE TABLE diplome_param (
PS_ID int(11) NOT NULL,
FIELD tinyint(4) NOT NULL,
AFFICHAGE tinyint(4) NOT NULL,
ACTIF tinyint(4) DEFAULT '0' NOT NULL,
TAILLE tinyint(4) NOT NULL,
STYLE tinyint(4) NOT NULL,
POLICE tinyint(4) NOT NULL,
POS_X float NOT NULL,
POS_Y float NOT NULL,
ANNEXE varchar(50),
PRIMARY KEY (PS_ID, FIELD)
);
# ------------------------------------
# data for table 'diplome_param'
# ------------------------------------

INSERT INTO diplome_param VALUES ('12','1','8','1','4','0','1','70','117','');
INSERT INTO diplome_param VALUES ('13','1','8','1','4','0','1','70','117','');
INSERT INTO diplome_param VALUES ('12','2','10','1','4','0','1','160','117','');
INSERT INTO diplome_param VALUES ('13','2','10','1','4','0','1','160','117','');
INSERT INTO diplome_param VALUES ('12','3','0','1','4','1','1','45','126','');
INSERT INTO diplome_param VALUES ('13','3','0','1','4','1','1','45','126','');
INSERT INTO diplome_param VALUES ('12','4','6','1','4','0','1','150','126','');
INSERT INTO diplome_param VALUES ('13','4','6','1','4','0','1','150','126','');
INSERT INTO diplome_param VALUES ('12','5','5','1','4','0','1','210','126','');
INSERT INTO diplome_param VALUES ('13','5','5','1','4','0','1','210','126','');
INSERT INTO diplome_param VALUES ('12','6','0','1','6','1','1','80','153','');
INSERT INTO diplome_param VALUES ('13','6','0','1','6','1','1','80','153','');
INSERT INTO diplome_param VALUES ('12','7','11','1','4','0','1','175','168','');
INSERT INTO diplome_param VALUES ('13','7','11','1','4','0','1','175','168','');
INSERT INTO diplome_param VALUES ('12','8','3','1','4','0','1','240','168','');
INSERT INTO diplome_param VALUES ('13','8','3','1','4','0','1','240','168','');
INSERT INTO diplome_param VALUES ('12','9','7','1','7','0','0','65','199','');
INSERT INTO diplome_param VALUES ('13','9','7','1','7','0','0','65','199','');


# ------------------------------------
# structure for table 'disponibilite'
# ------------------------------------

DROP TABLE IF EXISTS disponibilite ;
CREATE TABLE disponibilite (
P_ID int(11) DEFAULT '0' NOT NULL,
D_DATE date DEFAULT '0000-00-00' NOT NULL,
D_JOUR tinyint(4) DEFAULT '0',
D_NUIT tinyint(4) DEFAULT '0',
D_STATUT varchar(5) DEFAULT 'SPV' NOT NULL,
PRIMARY KEY (P_ID, D_DATE),
KEY D_DATE (D_DATE)
);
# ------------------------------------
# data for table 'disponibilite'
# ------------------------------------



# ------------------------------------
# structure for table 'document'
# ------------------------------------

DROP TABLE IF EXISTS document ;
CREATE TABLE document (
D_ID int(11) NOT NULL auto_increment,
S_ID int(11) NOT NULL,
E_CODE int(11) DEFAULT '0' NOT NULL,
TD_CODE varchar(5) NOT NULL,
D_NAME varchar(80) NOT NULL,
DS_ID tinyint(4) DEFAULT '1' NOT NULL,
D_CREATED_BY int(11) NOT NULL,
PRIMARY KEY (D_ID),
   UNIQUE S_ID (S_ID, D_NAME, E_CODE),
KEY TD_CODE (TD_CODE),
KEY E_CODE (E_CODE)
);
# ------------------------------------
# data for table 'document'
# ------------------------------------



# ------------------------------------
# structure for table 'document_security'
# ------------------------------------

DROP TABLE IF EXISTS document_security ;
CREATE TABLE document_security (
DS_ID tinyint(4) NOT NULL,
DS_LIBELLE varchar(50) NOT NULL,
F_ID tinyint(4) DEFAULT '0' NOT NULL,
PRIMARY KEY (DS_ID)
);
# ------------------------------------
# data for table 'document_security'
# ------------------------------------

INSERT INTO document_security VALUES ('1','public visible de tous','0');
INSERT INTO document_security VALUES ('2','accès restreint (15 - Gestion des événements)','15');
INSERT INTO document_security VALUES ('3','accès restreint (29 - Comptabilité)','29');
INSERT INTO document_security VALUES ('4','accès restreint (36 - Gestion des agréments)','36');
INSERT INTO document_security VALUES ('5','accès restreint (25 - Sécurité)','25');


# ------------------------------------
# structure for table 'equipage'
# ------------------------------------

DROP TABLE IF EXISTS equipage ;
CREATE TABLE equipage (
V_ID int(11) DEFAULT '0' NOT NULL,
ROLE_ID tinyint(4) DEFAULT '1' NOT NULL,
PS_ID int(11) DEFAULT '0' NOT NULL,
PRIMARY KEY (V_ID, ROLE_ID)
);
# ------------------------------------
# data for table 'equipage'
# ------------------------------------



# ------------------------------------
# structure for table 'equipe'
# ------------------------------------

DROP TABLE IF EXISTS equipe ;
CREATE TABLE equipe (
EQ_ID smallint(6) DEFAULT '0' NOT NULL,
EQ_NOM varchar(30) NOT NULL,
EQ_JOUR tinyint(4) DEFAULT '0' NOT NULL,
EQ_NUIT tinyint(4) DEFAULT '0' NOT NULL,
S_ID int(11) DEFAULT '0' NOT NULL,
S_ID_DATE date,
EQ_DUREE tinyint(4),
EQ_TYPE varchar(10) DEFAULT 'GARDE' NOT NULL,
PRIMARY KEY (S_ID, EQ_ID),
KEY EQ_ID (EQ_ID)
);
# ------------------------------------
# data for table 'equipe'
# ------------------------------------

INSERT INTO equipe VALUES ('1','Garde en caserne','1','1','2','2007-11-19','12','GARDE');
INSERT INTO equipe VALUES ('2','Feux de forêts','1','0','1','2007-11-19','8','GARDE');
INSERT INTO equipe VALUES ('3','Secourisme','0','0','0',NULL,NULL,'COMPETENCE');
INSERT INTO equipe VALUES ('4','Permis','0','0','0',NULL,NULL,'COMPETENCE');


# ------------------------------------
# structure for table 'evenement'
# ------------------------------------

DROP TABLE IF EXISTS evenement ;
CREATE TABLE evenement (
E_CODE int(11) DEFAULT '0' NOT NULL,
TE_CODE varchar(5) NOT NULL,
S_ID smallint(6) DEFAULT '0' NOT NULL,
E_CHEF int(11),
E_LIBELLE varchar(60) NOT NULL,
E_LIEU varchar(50) NOT NULL,
E_NB tinyint(4),
E_NB_DPS smallint(6) DEFAULT '0' NOT NULL,
E_COMMENT text,
E_COMMENT2 varchar(800),
E_CONVENTION varchar(30),
E_OPEN_TO_EXT tinyint(4) DEFAULT '0' NOT NULL,
E_CLOSED tinyint(4) DEFAULT '0' NOT NULL,
E_CANCELED tinyint(4) DEFAULT '0' NOT NULL,
E_CANCEL_DETAIL varchar(50),
E_MAIL1 tinyint(4) DEFAULT '0' NOT NULL,
E_MAIL2 tinyint(4) DEFAULT '0' NOT NULL,
E_MAIL3 tinyint(4) DEFAULT '0' NOT NULL,
E_NB1 smallint(6),
E_NB2 smallint(6),
E_NB3 smallint(6),
E_PARENT int(11),
E_CREATED_BY int(11),
E_CREATE_DATE datetime,
E_ALLOW_REINFORCEMENT tinyint(4) DEFAULT '0' NOT NULL,
TF_CODE varchar(1),
PS_ID smallint(6),
F_COMMENT varchar(100),
C_ID int(11),
E_CONTACT_LOCAL varchar(50),
E_CONTACT_TEL varchar(15),
TAV_ID tinyint(4) DEFAULT '1',
E_FLAG1 tinyint(4) DEFAULT '0' NOT NULL,
E_VISIBLE_OUTSIDE tinyint(4) DEFAULT '0' NOT NULL,
E_ADDRESS varchar(255),
E_REPAS VARCHAR( 10 ) NOT NULL ,
E_CONSIGNES VARCHAR( 800 ) NULL DEFAULT NULL,
E_NB_VPSP INT( 10 ) NULL DEFAULT NULL,
E_NB_AUTRES_VEHICULES INT( 10 ) NULL DEFAULT NULL ,
E_MOYENS_INSTALLATION VARCHAR( 800 ) NULL DEFAULT NULL ,
E_CLAUSES_PARTICULIERES VARCHAR( 800 ) NULL DEFAULT NULL ,
E_CLAUSES_PARTICULIERES2 VARCHAR( 800 ) NULL DEFAULT NULL 
E_TRANSPORT VARCHAR(10) NULL DEFAUlT NULL,
PRIMARY KEY (E_CODE),
KEY S_ID (S_ID),
KEY E_PARENT (E_PARENT),
KEY TE_CODE (TE_CODE),
KEY PS_ID (PS_ID),
KEY C_ID (C_ID),
KEY E_CANCELED (E_CANCELED),
KEY E_CLOSED (E_CLOSED),
KEY E_OPEN_TO_EXT (E_OPEN_TO_EXT),
KEY TAV_ID (TAV_ID)
);
# ------------------------------------
# data for table 'evenement'
# ------------------------------------



# ------------------------------------
# structure for table 'evenement_competences'
# ------------------------------------

DROP TABLE IF EXISTS evenement_competences ;
CREATE TABLE evenement_competences (
E_CODE int(11) NOT NULL,
EH_ID smallint(6) NOT NULL,
PS_ID int(11) DEFAULT '0' NOT NULL,
NB smallint(6) DEFAULT '1' NOT NULL,
PRIMARY KEY (E_CODE, EH_ID, PS_ID),
KEY PS_ID (PS_ID)
);
# ------------------------------------
# data for table 'evenement_competences'
# ------------------------------------



# ------------------------------------
# structure for table 'evenement_equipe'
# ------------------------------------

DROP TABLE IF EXISTS evenement_equipe ;
CREATE TABLE evenement_equipe (
E_CODE int(11) NOT NULL,
EE_ID smallint(6) NOT NULL,
EE_NAME varchar(20) NOT NULL,
EE_DESCRIPTION varchar(300),
PRIMARY KEY (E_CODE, EE_ID)
);
# ------------------------------------
# data for table 'evenement_equipe'
# ------------------------------------



# ------------------------------------
# structure for table 'evenement_facturation'
# ------------------------------------

DROP TABLE IF EXISTS evenement_facturation ;
CREATE TABLE evenement_facturation (
E_ID bigint(11) DEFAULT '0' NOT NULL,
dimP int(5),
dimP1 int(5),
dimP2 float,
dimE1 float,
dimE2 float,
dimNbISActeurs int(5),
dimNbISActeursCom varchar(250),
dimRIS decimal(20,4),
dimRISCalc decimal(20,4),
dimI decimal(20,4),
dimNbIS int(5),
dimTypeDPS varchar(100),
dimTypeDPSComment varchar(250),
dimSecteurs int(5),
dimPostes int(5),
dimEquipes int(5),
dimBinomes int(5),
devis_lieu varchar(50),
devis_date_heure varchar(100),
devis_date date,
devis_numero varchar(20),
devis_montant float,
devis_orga varchar(200),
devis_civilite varchar(20),
devis_contact varchar(200),
devis_adresse varchar(250),
devis_cp varchar(10),
devis_ville varchar(100),
devis_tel1 varchar(20),
devis_tel2 varchar(20),
devis_fax varchar(20),
devis_email varchar(50),
devis_url varchar(250),
devis_comment varchar(250),
devis_accepte int(1),
facture_lieu varchar(50),
facture_date_heure varchar(100),
facture_date date,
facture_numero varchar(20),
facture_montant float,
facture_comment varchar(250),
facture_orga varchar(200),
facture_civilite varchar(20),
facture_contact varchar(200),
facture_adresse varchar(250),
facture_cp varchar(10),
facture_ville varchar(100),
facture_tel1 varchar(20),
facture_tel2 varchar(20),
facture_fax varchar(20),
facture_email varchar(50),
relance_num int(1),
relance_date date,
relance_comment varchar(250),
paiement_date date,
paiement_comment varchar(250),
PRIMARY KEY (E_ID)
);
# ------------------------------------
# data for table 'evenement_facturation'
# ------------------------------------



# ------------------------------------
# structure for table 'evenement_facturation_detail'
# ------------------------------------

DROP TABLE IF EXISTS evenement_facturation_detail ;
CREATE TABLE evenement_facturation_detail (
e_id int(11) NOT NULL,
ef_type varchar(20) NOT NULL,
ef_lig int(11) NOT NULL,
ef_txt varchar(250) NOT NULL,
ef_qte int(11) DEFAULT '0' NOT NULL,
ef_pu float DEFAULT '0' NOT NULL,
ef_rem float DEFAULT '0' NOT NULL,
ef_comment varchar(250),
ef_frais varchar(10) DEFAULT 'PRE' NOT NULL,
PRIMARY KEY (e_id, ef_type, ef_lig)
);
# ------------------------------------
# data for table 'evenement_facturation_detail'
# ------------------------------------



# ------------------------------------
# structure for table 'evenement_horaire'
# ------------------------------------

DROP TABLE IF EXISTS evenement_horaire ;
CREATE TABLE evenement_horaire (
E_CODE int(11) NOT NULL,
EH_ID smallint(6) NOT NULL,
EH_DATE_DEBUT date NOT NULL,
EH_DATE_FIN date NOT NULL,
EH_DEBUT time NOT NULL,
EH_FIN time NOT NULL,
EH_DUREE float NOT NULL,
PRIMARY KEY (E_CODE, EH_ID),
KEY EH_DATE_DEBUT (EH_DATE_DEBUT),
KEY EH_DATE_FIN (EH_DATE_FIN)
);
# ------------------------------------
# data for table 'evenement_horaire'
# ------------------------------------



# ------------------------------------
# structure for table 'evenement_materiel'
# ------------------------------------

DROP TABLE IF EXISTS evenement_materiel ;
CREATE TABLE evenement_materiel (
E_CODE int(11) NOT NULL,
MA_ID int(11) NOT NULL,
EM_NB int(11) DEFAULT '1' NOT NULL,
PRIMARY KEY (E_CODE, MA_ID),
KEY MA_ID (MA_ID)
);
# ------------------------------------
# data for table 'evenement_materiel'
# ------------------------------------



# ------------------------------------
# structure for table 'evenement_participation'
# ------------------------------------

DROP TABLE IF EXISTS evenement_participation ;
CREATE TABLE evenement_participation (
E_CODE int(11) DEFAULT '0' NOT NULL,
EH_ID smallint(6) DEFAULT '1' NOT NULL,
P_ID int(11) DEFAULT '0' NOT NULL,
EP_DATE datetime,
EP_BY int(11),
TP_ID smallint(6) DEFAULT '0' NOT NULL,
EP_COMMENT varchar(150),
EP_DATE_DEBUT date,
EP_DATE_FIN date,
EP_DEBUT time,
EP_FIN time,
EP_DUREE float,
EP_FLAG1 tinyint(4) DEFAULT '0' NOT NULL,
EP_KM smallint(6),
EE_ID smallint(6),
PRIMARY KEY (E_CODE, EH_ID, P_ID),
KEY P_ID (P_ID),
KEY TP_ID (TP_ID)
);
# ------------------------------------
# data for table 'evenement_participation'
# ------------------------------------



# ------------------------------------
# structure for table 'evenement_vehicule'
# ------------------------------------

DROP TABLE IF EXISTS evenement_vehicule ;
CREATE TABLE evenement_vehicule (
E_CODE int(11) DEFAULT '0' NOT NULL,
EH_ID smallint(6) DEFAULT '1' NOT NULL,
V_ID int(11) DEFAULT '0' NOT NULL,
EV_KM smallint(6),
EV_DATE_DEBUT date,
EV_DATE_FIN date,
EV_DEBUT time,
EV_FIN time,
EV_DUREE float,
PRIMARY KEY (E_CODE, EH_ID, V_ID),
KEY V_ID (V_ID)
);
# ------------------------------------
# data for table 'evenement_vehicule'
# ------------------------------------



# ------------------------------------
# structure for table 'fonctionnalite'
# ------------------------------------

DROP TABLE IF EXISTS fonctionnalite ;
CREATE TABLE fonctionnalite (
F_ID int(11) DEFAULT '0' NOT NULL,
F_LIBELLE varchar(30) NOT NULL,
F_TYPE tinyint(4) DEFAULT '0' NOT NULL,
TF_ID smallint(6) DEFAULT '0' NOT NULL,
F_FLAG tinyint(4) DEFAULT '0' NOT NULL,
F_DESCRIPTION varchar(500),
PRIMARY KEY (F_ID)
);
# ------------------------------------
# data for table 'fonctionnalite'
# ------------------------------------

INSERT INTO fonctionnalite VALUES ('1','Ajouter personnel','0','4','0','Ajouter du personnel dans l\'application.<br>Un mot de passe aléatoire est généré et un mail est envoyé au nouvel utilisateur pour lui indiquer que son compte a été créé.<br> Seul le personnel interne est concerné ici. L\'habilitation 37 est requise pour le personnel externe.');
INSERT INTO fonctionnalite VALUES ('2','Modifier le personnel','0','4','0','Modifier les informations du personnel sous sa responsabilité,<br> sauf le mot de passe. Seul le personnel interne est concerné ici. <br>L\'habilitation 37 est requise pour le personnel externe.');
INSERT INTO fonctionnalite VALUES ('3','Supprimer le personnel','0','4','1','Supprimer les fiches personnel.<br><img src=images/miniwarn.png> Attention seuls les administrateurs devraient être habilités pour utiliser cette fonctionnalité.');
INSERT INTO fonctionnalite VALUES ('4','Compétences du personnel','0','5','1','Modifier les compétence et dates d\'expiration des compétences du personnel<br> sous sa responsabilité.');
INSERT INTO fonctionnalite VALUES ('5','Vider le tableau de garde','1','8','0','Supprimer les données du tableau de garde.');
INSERT INTO fonctionnalite VALUES ('6','Modifier le tableau de garde','1','8','0','Modifier la liste de personnel de garde un jour donné.');
INSERT INTO fonctionnalite VALUES ('7','Remplir le tableau de garde','1','8','0','Utiliser la fonction de remplissage automatique du tableau de garde.');
INSERT INTO fonctionnalite VALUES ('8','Ajout/Suppression consignes','1','8','0','Ajouter ou supprimer des consignes pour la garde opérationnelle.');
INSERT INTO fonctionnalite VALUES ('9','Sécurité/habilitations','2','2','1','Changer les mots de passes de tout le personnel.<br>Créer, modifier et supprimer  des groupes de permissions et des rôles dans l\'organigramme.<br><img src=images/miniwarn.png> Attention seuls les administrateurs devraient être habilités pour utiliser cette fonctionnalité.');
INSERT INTO fonctionnalite VALUES ('0','Se connecter','0','0','0','Se connecter à eBrigade.<br> Tous les groupes d\'habilitation doivent avoir cette permission, sauf \'accès interdit\'');
INSERT INTO fonctionnalite VALUES ('10','Modifier les disponibilités','0','4','0','Modifier les disponibilités du personnel sous sa responsabilité.<br>Inscrire le personnel sous sa responsabilité sur des événements.');
INSERT INTO fonctionnalite VALUES ('11','Saisir ses absences','0','0','0','Saisir ses absences personnelles, demandes de congés payés <br>(pour le personnel professionnel ou salarié), absences pour raisons personnelles ou autres.<br>Permet aussi de voir les absences saisies par les autres personnes.<br>Dans le cas d\'une demande de congés une demande de validation est envoyée au responsable du demandeur.');
INSERT INTO fonctionnalite VALUES ('12','Saisie toutes absences','0','4','0','Modifier les disponibilités du personnel sous sa responsabilité.<br>Inscrire le personnel sous sa responsabilité sur des événements.');
INSERT INTO fonctionnalite VALUES ('13','Valider les CP','0','4','0','Valider les demandes de congés payés et de RTT du personnel professionnel ou salarié.<br>Recevoir un mail de notification en cas d\'inscription de personnel salarié, précisant <br>le statut bénévole ou salarié.');
INSERT INTO fonctionnalite VALUES ('14','Admin technique','2','1','1','Configuration de l\'application eBrigade, gestion des sauvegardes <br>de la base de données. Supprimer des sections. Supprimer des messages sur la messagerie instantanée.<br><img src=images/miniwarn.png> Attention seuls les administrateurs devraient être habilités pour utiliser cette fonctionnalité.');
INSERT INTO fonctionnalite VALUES ('15','Gestion des événements','0','6','0','Créer de nouveaux événements, modifier les événements existants, inscrire du personnel et du matériel sur les événements.');
INSERT INTO fonctionnalite VALUES ('16','Ajout infos diverses','0','9','0','Ajouter des informations visibles par les autres utilisateurs sur la pages infos diverses. Ces informations sont aussi visibles sur la page d\'accueil.');
INSERT INTO fonctionnalite VALUES ('17','Gestion véhicules/matériel','0','7','0','Ajouter ou modifier des véhicules ou du matériel. Permet d\'engager des véhicules ou du matériel sur les événements.');
INSERT INTO fonctionnalite VALUES ('18','Paramétrage application','0','3','0','Paramétrage de l\'application: Compétences, Fonctions, Types de matériel<br><img src=images/miniwarn.png> Attention seuls les administrateurs devraient être habilités pour utiliser cette fonctionnalité.');
INSERT INTO fonctionnalite VALUES ('19','Supprimer données','0','7','1','Supprimer des événements, des véhicules, du matériel ou des entreprises clientes.<br><img src=images/miniwarn.png> Attention seuls les administrateurs devraient être habilités pour utiliser cette fonctionnalité.');
INSERT INTO fonctionnalite VALUES ('20','Audit','0','9','0','Voir l\'historique des connexions à l\'application.');
INSERT INTO fonctionnalite VALUES ('21','Notifications événement','0','10','0','Recevoir un email de notification lorsqu\'un événement est créé.');
INSERT INTO fonctionnalite VALUES ('23','Envoyer des SMS','0','9','0','Envoyer des SMS (c\'est un service qui a un coût).');
INSERT INTO fonctionnalite VALUES ('22','Gestion des sections','0','3','1','Ajouter ou modifier des sections dans l\'organigramme.<br> Cette fonctionnalité ne permet pas de supprimer une section (il faut avoir 14 pour cela).');
INSERT INTO fonctionnalite VALUES ('24','Permissions extérieures','2','1','0','Etendre les permissions d\'une personne à toutes les sections ou à toutes les zones géographiques.');
INSERT INTO fonctionnalite VALUES ('25','Sécurité locale','0','2','1','Permissions de modifier les mots de passes ou de modifier les permissions des autres utilisateurs.<br> Ces droits sont cependant limités au personnel sous sa responsabilité,<br> et ne permettent pas de donner les permissions les plus élevées (9, 14 et 24).');
INSERT INTO fonctionnalite VALUES ('26','Gestion des permanences','0','7','0','Permissions du cadre de permanence. Donne aussi des droits de création<br> et de modification sur les événements, d\'inscription du personnel ou d\'engagement des véhicule ste du matériel.<br> Permet aussi de changer le cadre de permanence.');
INSERT INTO fonctionnalite VALUES ('27','Statistiques et reporting','0','9','0','Voir les graphiques montrant les statistiques opérationnelles (si le module complémentaire ChartDirector est installé).<br>Utiliser les fonctionnalités de reporting.<br>Voir les cartes de France (si le module france map est installé).');
INSERT INTO fonctionnalite VALUES ('28','Inscriptions extérieures','0','6','0','S\'inscrire ou inscrire du personnel sur les événements de toutes les sections ou de toutes les zones géographiques.');
INSERT INTO fonctionnalite VALUES ('29','Comptabilité','0','7','0','Utiliser la fonctionnalité de comptabilité permettant de visualiser,<br> de créer ou de modifier des devis ou des factures pour les DPS, les formations ou les autres activités facturables.<br>Modifier les paramétrage des devis et factures sur la page section');
INSERT INTO fonctionnalite VALUES ('30','Gestion des badges','0','7','0','Editer et imprimer des badges pour le personnel.<br>Paramétrer le format des badges sur la page section.');
INSERT INTO fonctionnalite VALUES ('31','Gestion des compétences élevée','0','5','1','Permet d\'attribuer ou de modifier des compétences considérées comme élevées.<br> Dans la page de paramétrage des compétences,<br> on peut définir si une compétence requiret cette habilitation pour pouvoir être attribuée à une personne.');
INSERT INTO fonctionnalite VALUES ('32','Notifications personnel','0','10','0','Recevoir une notification par email lorsque une nouvelle fiche personnel<br> est créée ou lorsque une personne change de statut (actif <-> ancien).');
INSERT INTO fonctionnalite VALUES ('33','Notifications compétences','0','10','0','Recevoir une notification par email lorsque certaines compétences<br> (ayant la propriété \'Alerter si modification\' sont attribuées à du personnel.');
INSERT INTO fonctionnalite VALUES ('34','Notifications véhicules','0','10','0','Recevoir une notification par email lorsque le statut<br> d\'un véhicule est modifié (utilisable <-> réformé).');
INSERT INTO fonctionnalite VALUES ('35','Notifications comptabilité','0','10','0','Recevoir une notification par email lorsque un devis a été créé.');
INSERT INTO fonctionnalite VALUES ('36','Gestion des agréments','0','7','1','Permettre de modifier les agréments des sections.');
INSERT INTO fonctionnalite VALUES ('37','Gestion des externes','0','7','0','Ajouter et modifier le personnel externe.<br> Ajouter, modifier les entreprises ou associations clientes, liées à une section. <br>Attention, la suppression d\'une entreprise requiert en plus l\'habilitation 19');
INSERT INTO fonctionnalite VALUES ('38','Saisir ses disponibilités','0','0','0','Permettre de saisir ses propres disponibilités,<br> et de voir les disponibilités saisies par le personnel.<br> Tous les membres peuvent avoir cette permission.');
INSERT INTO fonctionnalite VALUES ('39','S\'inscrire','0','0','0','Permet à une personne de s\'inscrire sur des événements lorsque<br> ceux ci sont ouverts aux inscriptions pour le personnel de sa section.<br> Tous les membres peuvent avoir cette permission.');
INSERT INTO fonctionnalite VALUES ('40','Voir le personnel','0','0','0','Voir toutes les fiches du personnel interne, à l\'exclusion éventuelle<br> des informations protégées . Tous les membres peuvent avoir cette permission.<br> Attention, pour voir les fiches du peronnel externe, les permissions 37 ou 45 sont requises.');
INSERT INTO fonctionnalite VALUES ('41','Voir les événements','0','0','0','Voir tous les événements qui ont été créés.<br>Sans cette permission on ne peut voir que les événements où l\'on est inscrit.<br>Le personnel externe possédant cette habilitation a une restriction géographique.<br> Tous le personnel interne devrait avoir cette permission.');
INSERT INTO fonctionnalite VALUES ('42','Voir véhicules/matériel','0','0','0','Accès en lecture aux menus véhicules et matériel,<br> permet d\'afficher l\'inventaire et l\'état de chaque véhicule ou pièce de matériel.');
INSERT INTO fonctionnalite VALUES ('43','Messagerie','0','0','0','Utiliser les outils de messagerie: mails, alertes et messagerie instantanée<br> - aide en ligne. Tous les membres peuvent avoir cette permission.');
INSERT INTO fonctionnalite VALUES ('44','Voir les infos','0','0','0','Permet à une personne de voir les messages d\'information et l\'organigramme.<br> Tous les membres peuvent avoir cette permission.');
INSERT INTO fonctionnalite VALUES ('45','Voir mon entreprise','3','0','0','Permet à un utilisateur faisant partie du personnel d\'une entreprise<br>de voir les informations relatives à cette entreprise, le personnel externe attaché à une entreprise<br>et aussi les événements organisés pour le compte de cette entreprise.<br>Cette fonctionnalité n\'a aucun effet sur les utilisateurs qui ne font pas partie d\'une entreprise.');
INSERT INTO fonctionnalite VALUES ('46','Habilitations des externes','2','2','1','Permettre de donner un accès étendu à l\'application au personnel externe.<br> Les permissions donnant les droits sur la fonctionnalité 45 sont concernées.<br>L\'accès à cette fonctionnalité doit être restreint.');
INSERT INTO fonctionnalite VALUES ('47','Gestion des documents','0','7','0','Ajouter des documents sur la page section.<br>Définir des restrictions d\'accès à ces documents.');
INSERT INTO fonctionnalite VALUES ('48','Imprimer les diplômes','0','5','0','Imprimer les diplômes à l\'issue des formations.');
INSERT INTO fonctionnalite VALUES ('50','Notification coordonnées','0','10','0','Recevoir une notification en cas de <br>changement de coordonnées du personnel');
INSERT INTO fonctionnalite VALUES ('49','Historique','0','2','0','Voir l\'historique des modifications faites sur les fiches personnels<br>les véhicules ou matériels et les événements');


# ------------------------------------
# structure for table 'geolocalisation'
# ------------------------------------

DROP TABLE IF EXISTS geolocalisation ;
CREATE TABLE geolocalisation (
ID int(11) NOT NULL auto_increment,
TYPE char(1) DEFAULT 'E' NOT NULL,
CODE int(11) NOT NULL,
LAT float(10,6) NOT NULL,
LNG float(10,6) NOT NULL,
PRIMARY KEY (ID),
   UNIQUE TYPE (TYPE, CODE)
);
# ------------------------------------
# data for table 'geolocalisation'
# ------------------------------------



# ------------------------------------
# structure for table 'grade'
# ------------------------------------

DROP TABLE IF EXISTS grade ;
CREATE TABLE grade (
G_GRADE varchar(5) NOT NULL,
G_DESCRIPTION varchar(50) NOT NULL,
G_LEVEL smallint(6) DEFAULT '0' NOT NULL,
G_TYPE varchar(25) NOT NULL,
PRIMARY KEY (G_GRADE)
);
# ------------------------------------
# data for table 'grade'
# ------------------------------------

INSERT INTO grade VALUES ('SAP','Sapeur de 2ème classe','1','caporaux et sapeurs');
INSERT INTO grade VALUES ('1CL','sapeur de 1ère classe','2','caporaux et sapeurs');
INSERT INTO grade VALUES ('CPL','caporal','3','caporaux et sapeurs');
INSERT INTO grade VALUES ('CCH','caporal-chef','4','caporaux et sapeurs');
INSERT INTO grade VALUES ('SGT','sergent','5','sous-officiers');
INSERT INTO grade VALUES ('SCH','sergent-chef','6','sous-officiers');
INSERT INTO grade VALUES ('ADJ','adjudant','7','sous-officiers');
INSERT INTO grade VALUES ('ADC','adjudant-chef','8','sous-officiers');
INSERT INTO grade VALUES ('MAJ','major','9','officiers');
INSERT INTO grade VALUES ('LTN','lieutenant','10','officiers');
INSERT INTO grade VALUES ('CPT','capitaine','11','officiers');
INSERT INTO grade VALUES ('CDT','commandant','12','officiers');
INSERT INTO grade VALUES ('LCL','lieutenant-colonel','13','officiers');
INSERT INTO grade VALUES ('COL','colonel','14','officiers');


# ------------------------------------
# structure for table 'groupe'
# ------------------------------------

DROP TABLE IF EXISTS groupe ;
CREATE TABLE groupe (
GP_ID smallint(6) DEFAULT '0' NOT NULL,
GP_DESCRIPTION varchar(30) NOT NULL,
TR_CONFIG tinyint(4),
TR_SUB_POSSIBLE tinyint(4) DEFAULT '0' NOT NULL,
GP_USAGE varchar(10) DEFAULT 'internes' NOT NULL,
PRIMARY KEY (GP_ID)
);
# ------------------------------------
# data for table 'groupe'
# ------------------------------------

INSERT INTO groupe VALUES ('-1','accès interdit',NULL,'0','all');
INSERT INTO groupe VALUES ('0','public',NULL,'0','internes');
INSERT INTO groupe VALUES ('1','bureau opérations',NULL,'0','internes');
INSERT INTO groupe VALUES ('2','chef de section',NULL,'0','internes');
INSERT INTO groupe VALUES ('3','chef de centre',NULL,'0','internes');
INSERT INTO groupe VALUES ('4','admin',NULL,'0','internes');
INSERT INTO groupe VALUES ('102','Chef',NULL,'0','internes');
INSERT INTO groupe VALUES ('103','Adjoint',NULL,'0','internes');
INSERT INTO groupe VALUES ('104','Trésorier',NULL,'1','internes');
INSERT INTO groupe VALUES ('105','Secrétaire général',NULL,'0','internes');
INSERT INTO groupe VALUES ('106','Directeur',NULL,'0','internes');
INSERT INTO groupe VALUES ('107','Responsable opérationnel',NULL,'1','internes');
INSERT INTO groupe VALUES ('108','Webmaster',NULL,'1','internes');
INSERT INTO groupe VALUES ('109','Responsable véhicules/matériel',NULL,'1','internes');
INSERT INTO groupe VALUES ('110','Secrétariat',NULL,'1','internes');
INSERT INTO groupe VALUES ('5','Externe','2','0','externes');


# ------------------------------------
# structure for table 'habilitation'
# ------------------------------------

DROP TABLE IF EXISTS habilitation ;
CREATE TABLE habilitation (
GP_ID smallint(6) DEFAULT '0' NOT NULL,
F_ID int(11) DEFAULT '0' NOT NULL,
PRIMARY KEY (GP_ID, F_ID),
KEY F_ID (F_ID)
);
# ------------------------------------
# data for table 'habilitation'
# ------------------------------------

INSERT INTO habilitation VALUES ('0','0');
INSERT INTO habilitation VALUES ('0','11');
INSERT INTO habilitation VALUES ('0','16');
INSERT INTO habilitation VALUES ('0','38');
INSERT INTO habilitation VALUES ('0','39');
INSERT INTO habilitation VALUES ('0','40');
INSERT INTO habilitation VALUES ('0','41');
INSERT INTO habilitation VALUES ('0','42');
INSERT INTO habilitation VALUES ('0','43');
INSERT INTO habilitation VALUES ('0','44');
INSERT INTO habilitation VALUES ('1','0');
INSERT INTO habilitation VALUES ('1','1');
INSERT INTO habilitation VALUES ('1','2');
INSERT INTO habilitation VALUES ('1','3');
INSERT INTO habilitation VALUES ('1','4');
INSERT INTO habilitation VALUES ('1','5');
INSERT INTO habilitation VALUES ('1','6');
INSERT INTO habilitation VALUES ('1','7');
INSERT INTO habilitation VALUES ('1','8');
INSERT INTO habilitation VALUES ('1','10');
INSERT INTO habilitation VALUES ('1','11');
INSERT INTO habilitation VALUES ('1','12');
INSERT INTO habilitation VALUES ('1','13');
INSERT INTO habilitation VALUES ('1','15');
INSERT INTO habilitation VALUES ('1','16');
INSERT INTO habilitation VALUES ('1','17');
INSERT INTO habilitation VALUES ('1','18');
INSERT INTO habilitation VALUES ('1','26');
INSERT INTO habilitation VALUES ('1','27');
INSERT INTO habilitation VALUES ('1','38');
INSERT INTO habilitation VALUES ('1','39');
INSERT INTO habilitation VALUES ('1','40');
INSERT INTO habilitation VALUES ('1','41');
INSERT INTO habilitation VALUES ('1','42');
INSERT INTO habilitation VALUES ('1','43');
INSERT INTO habilitation VALUES ('1','44');
INSERT INTO habilitation VALUES ('2','0');
INSERT INTO habilitation VALUES ('2','4');
INSERT INTO habilitation VALUES ('2','6');
INSERT INTO habilitation VALUES ('2','8');
INSERT INTO habilitation VALUES ('2','10');
INSERT INTO habilitation VALUES ('2','11');
INSERT INTO habilitation VALUES ('2','16');
INSERT INTO habilitation VALUES ('2','17');
INSERT INTO habilitation VALUES ('2','27');
INSERT INTO habilitation VALUES ('2','38');
INSERT INTO habilitation VALUES ('2','39');
INSERT INTO habilitation VALUES ('2','40');
INSERT INTO habilitation VALUES ('2','41');
INSERT INTO habilitation VALUES ('2','42');
INSERT INTO habilitation VALUES ('2','43');
INSERT INTO habilitation VALUES ('2','44');
INSERT INTO habilitation VALUES ('3','0');
INSERT INTO habilitation VALUES ('3','1');
INSERT INTO habilitation VALUES ('3','2');
INSERT INTO habilitation VALUES ('3','3');
INSERT INTO habilitation VALUES ('3','4');
INSERT INTO habilitation VALUES ('3','8');
INSERT INTO habilitation VALUES ('3','11');
INSERT INTO habilitation VALUES ('3','12');
INSERT INTO habilitation VALUES ('3','13');
INSERT INTO habilitation VALUES ('3','16');
INSERT INTO habilitation VALUES ('3','17');
INSERT INTO habilitation VALUES ('3','27');
INSERT INTO habilitation VALUES ('3','38');
INSERT INTO habilitation VALUES ('3','39');
INSERT INTO habilitation VALUES ('3','40');
INSERT INTO habilitation VALUES ('3','41');
INSERT INTO habilitation VALUES ('3','42');
INSERT INTO habilitation VALUES ('3','43');
INSERT INTO habilitation VALUES ('3','44');
INSERT INTO habilitation VALUES ('4','0');
INSERT INTO habilitation VALUES ('4','1');
INSERT INTO habilitation VALUES ('4','2');
INSERT INTO habilitation VALUES ('4','3');
INSERT INTO habilitation VALUES ('4','4');
INSERT INTO habilitation VALUES ('4','5');
INSERT INTO habilitation VALUES ('4','6');
INSERT INTO habilitation VALUES ('4','7');
INSERT INTO habilitation VALUES ('4','8');
INSERT INTO habilitation VALUES ('4','9');
INSERT INTO habilitation VALUES ('4','10');
INSERT INTO habilitation VALUES ('4','11');
INSERT INTO habilitation VALUES ('4','12');
INSERT INTO habilitation VALUES ('4','13');
INSERT INTO habilitation VALUES ('4','14');
INSERT INTO habilitation VALUES ('4','15');
INSERT INTO habilitation VALUES ('4','16');
INSERT INTO habilitation VALUES ('4','17');
INSERT INTO habilitation VALUES ('4','18');
INSERT INTO habilitation VALUES ('4','19');
INSERT INTO habilitation VALUES ('4','20');
INSERT INTO habilitation VALUES ('4','21');
INSERT INTO habilitation VALUES ('4','22');
INSERT INTO habilitation VALUES ('4','23');
INSERT INTO habilitation VALUES ('4','24');
INSERT INTO habilitation VALUES ('4','25');
INSERT INTO habilitation VALUES ('4','26');
INSERT INTO habilitation VALUES ('4','27');
INSERT INTO habilitation VALUES ('4','28');
INSERT INTO habilitation VALUES ('4','29');
INSERT INTO habilitation VALUES ('4','30');
INSERT INTO habilitation VALUES ('4','31');
INSERT INTO habilitation VALUES ('4','36');
INSERT INTO habilitation VALUES ('4','37');
INSERT INTO habilitation VALUES ('4','38');
INSERT INTO habilitation VALUES ('4','39');
INSERT INTO habilitation VALUES ('4','40');
INSERT INTO habilitation VALUES ('4','41');
INSERT INTO habilitation VALUES ('4','42');
INSERT INTO habilitation VALUES ('4','43');
INSERT INTO habilitation VALUES ('4','44');
INSERT INTO habilitation VALUES ('4','46');
INSERT INTO habilitation VALUES ('4','47');
INSERT INTO habilitation VALUES ('4','48');
INSERT INTO habilitation VALUES ('4','49');
INSERT INTO habilitation VALUES ('5','0');
INSERT INTO habilitation VALUES ('5','45');
INSERT INTO habilitation VALUES ('102','38');
INSERT INTO habilitation VALUES ('102','39');
INSERT INTO habilitation VALUES ('102','40');
INSERT INTO habilitation VALUES ('102','41');
INSERT INTO habilitation VALUES ('102','42');
INSERT INTO habilitation VALUES ('102','43');
INSERT INTO habilitation VALUES ('102','44');
INSERT INTO habilitation VALUES ('103','38');
INSERT INTO habilitation VALUES ('103','39');
INSERT INTO habilitation VALUES ('103','40');
INSERT INTO habilitation VALUES ('103','41');
INSERT INTO habilitation VALUES ('103','42');
INSERT INTO habilitation VALUES ('103','43');
INSERT INTO habilitation VALUES ('103','44');
INSERT INTO habilitation VALUES ('104','38');
INSERT INTO habilitation VALUES ('104','39');
INSERT INTO habilitation VALUES ('104','40');
INSERT INTO habilitation VALUES ('104','41');
INSERT INTO habilitation VALUES ('104','42');
INSERT INTO habilitation VALUES ('104','43');
INSERT INTO habilitation VALUES ('104','44');
INSERT INTO habilitation VALUES ('105','38');
INSERT INTO habilitation VALUES ('105','39');
INSERT INTO habilitation VALUES ('105','40');
INSERT INTO habilitation VALUES ('105','41');
INSERT INTO habilitation VALUES ('105','42');
INSERT INTO habilitation VALUES ('105','43');
INSERT INTO habilitation VALUES ('105','44');
INSERT INTO habilitation VALUES ('106','38');
INSERT INTO habilitation VALUES ('106','39');
INSERT INTO habilitation VALUES ('106','40');
INSERT INTO habilitation VALUES ('106','41');
INSERT INTO habilitation VALUES ('106','42');
INSERT INTO habilitation VALUES ('106','43');
INSERT INTO habilitation VALUES ('106','44');
INSERT INTO habilitation VALUES ('107','38');
INSERT INTO habilitation VALUES ('107','39');
INSERT INTO habilitation VALUES ('107','40');
INSERT INTO habilitation VALUES ('107','41');
INSERT INTO habilitation VALUES ('107','42');
INSERT INTO habilitation VALUES ('107','43');
INSERT INTO habilitation VALUES ('107','44');
INSERT INTO habilitation VALUES ('108','38');
INSERT INTO habilitation VALUES ('108','39');
INSERT INTO habilitation VALUES ('108','40');
INSERT INTO habilitation VALUES ('108','41');
INSERT INTO habilitation VALUES ('108','42');
INSERT INTO habilitation VALUES ('108','43');
INSERT INTO habilitation VALUES ('108','44');
INSERT INTO habilitation VALUES ('109','38');
INSERT INTO habilitation VALUES ('109','39');
INSERT INTO habilitation VALUES ('109','40');
INSERT INTO habilitation VALUES ('109','41');
INSERT INTO habilitation VALUES ('109','42');
INSERT INTO habilitation VALUES ('109','43');
INSERT INTO habilitation VALUES ('109','44');
INSERT INTO habilitation VALUES ('110','38');
INSERT INTO habilitation VALUES ('110','39');
INSERT INTO habilitation VALUES ('110','40');
INSERT INTO habilitation VALUES ('110','41');
INSERT INTO habilitation VALUES ('110','42');
INSERT INTO habilitation VALUES ('110','43');
INSERT INTO habilitation VALUES ('110','44');


# ------------------------------------
# structure for table 'indisponibilite'
# ------------------------------------

DROP TABLE IF EXISTS indisponibilite ;
CREATE TABLE indisponibilite (
I_CODE int(11) NOT NULL auto_increment,
P_ID int(11) DEFAULT '0' NOT NULL,
TI_CODE varchar(5) DEFAULT 'CP' NOT NULL,
I_STATUS varchar(5) DEFAULT 'ATT' NOT NULL,
I_DEBUT date DEFAULT '0000-00-00' NOT NULL,
I_FIN date,
IH_DEBUT time DEFAULT '08:00:00' NOT NULL,
IH_FIN time DEFAULT '19:00:00' NOT NULL,
I_JOUR_COMPLET tinyint(4) DEFAULT '1' NOT NULL,
I_ACCEPT datetime,
I_CANCEL datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
I_COMMENT varchar(50) NOT NULL,
PRIMARY KEY (I_CODE),
KEY P_ID (P_ID),
KEY TI_CODE (TI_CODE),
KEY I_STATUS (I_STATUS),
KEY I_DEBUT (I_DEBUT)
);
# ------------------------------------
# data for table 'indisponibilite'
# ------------------------------------



# ------------------------------------
# structure for table 'indisponibilite_status'
# ------------------------------------

DROP TABLE IF EXISTS indisponibilite_status ;
CREATE TABLE indisponibilite_status (
I_STATUS varchar(5) NOT NULL,
I_STATUS_LIBELLE varchar(20) NOT NULL,
PRIMARY KEY (I_STATUS)
);
# ------------------------------------
# data for table 'indisponibilite_status'
# ------------------------------------

INSERT INTO indisponibilite_status VALUES ('VAL','Validée');
INSERT INTO indisponibilite_status VALUES ('ATT','Attente');
INSERT INTO indisponibilite_status VALUES ('ANN','Annulée');
INSERT INTO indisponibilite_status VALUES ('REF','Refusée');
INSERT INTO indisponibilite_status VALUES ('PRE','prévisionnel');


# ------------------------------------
# structure for table 'log_category'
# ------------------------------------

DROP TABLE IF EXISTS log_category ;
CREATE TABLE log_category (
LC_CODE varchar(2) NOT NULL,
LC_DESCRIPTION varchar(30) NOT NULL,
PRIMARY KEY (LC_CODE)
);
# ------------------------------------
# data for table 'log_category'
# ------------------------------------

INSERT INTO log_category VALUES ('P','personnel');
INSERT INTO log_category VALUES ('V','véhicule');
INSERT INTO log_category VALUES ('M','matériel');
INSERT INTO log_category VALUES ('E','événement');


# ------------------------------------
# structure for table 'log_history'
# ------------------------------------

DROP TABLE IF EXISTS log_history ;
CREATE TABLE log_history (
LH_ID int(11) NOT NULL auto_increment,
P_ID int(11) NOT NULL,
LH_STAMP datetime NOT NULL,
LT_CODE varchar(8) NOT NULL,
LH_WHAT int(11) NOT NULL,
LH_COMPLEMENT varchar(150),
COMPLEMENT_CODE int(11),
PRIMARY KEY (LH_ID),
KEY P_ID (P_ID),
KEY LH_STAMP (LH_STAMP),
KEY LT_CODE (LT_CODE),
KEY LH_WHAT (LH_WHAT),
KEY COMPLEMENT_CODE (COMPLEMENT_CODE)
);
# ------------------------------------
# data for table 'log_history'
# ------------------------------------



# ------------------------------------
# structure for table 'log_type'
# ------------------------------------

DROP TABLE IF EXISTS log_type ;
CREATE TABLE log_type (
LT_CODE varchar(8) NOT NULL,
LC_CODE varchar(2) NOT NULL,
LT_DESCRIPTION varchar(50) NOT NULL,
PRIMARY KEY (LT_CODE),
KEY LC_CODE (LC_CODE)
);
# ------------------------------------
# data for table 'log_type'
# ------------------------------------

INSERT INTO log_type VALUES ('INSP','P','Ajout d\'une fiche personnel');
INSERT INTO log_type VALUES ('UPDP','P','Modification de fiche personnel');
INSERT INTO log_type VALUES ('DROPP','P','Suppression d\'une fiche personnel');
INSERT INTO log_type VALUES ('UPDMDP','P','Modification de mot de passe');
INSERT INTO log_type VALUES ('REGENMDP','P','Regénération de mot de passe');
INSERT INTO log_type VALUES ('UPDSEC','P','Changement de section');
INSERT INTO log_type VALUES ('UPDSTP','P','Changement de position');
INSERT INTO log_type VALUES ('INSV','V','Ajout d\'une fiche véhicule');
INSERT INTO log_type VALUES ('UPDV','V','Modification de fiche véhicule');
INSERT INTO log_type VALUES ('DROPV','V','Suppression d\'une fiche véhicule');
INSERT INTO log_type VALUES ('UPDSTV','V','Changement de position véhicule');
INSERT INTO log_type VALUES ('INSCP','P','Inscription');
INSERT INTO log_type VALUES ('DESINSCP','P','Désinscription');
INSERT INTO log_type VALUES ('DETINSCP','P','Commentaire sur inscription');
INSERT INTO log_type VALUES ('FNINSCP','P','Modification Fonction');
INSERT INTO log_type VALUES ('EEINSCP','P','Modification Equipe');
INSERT INTO log_type VALUES ('ADQ','P','Ajout compétence');
INSERT INTO log_type VALUES ('UPDQ','P','Modification compétence');
INSERT INTO log_type VALUES ('DELQ','P','Suppression compétence');
INSERT INTO log_type VALUES ('INSABS','P','Saisie absence');
INSERT INTO log_type VALUES ('VALABS','P','Validation congés ou RTT');
INSERT INTO log_type VALUES ('REFABS','P','Refus congés ou RTT');
INSERT INTO log_type VALUES ('DELABS','P','Suppression demande absence');
INSERT INTO log_type VALUES ('UPDDISPO','P','Modification des disponibilités');
INSERT INTO log_type VALUES ('UPDPHOTO','P','Modification de photo');
INSERT INTO log_type VALUES ('DELPHOTO','P','Suppression de photo');
INSERT INTO log_type VALUES ('IMPBADGE','P','Impression de badge');
INSERT INTO log_type VALUES ('DEMBADGE','P','Demande de nouveau badge');


# ------------------------------------
# structure for table 'materiel'
# ------------------------------------

DROP TABLE IF EXISTS materiel ;
CREATE TABLE materiel (
MA_ID int(11) NOT NULL auto_increment,
TM_ID int(11) NOT NULL,
MA_NUMERO_SERIE varchar(15),
MA_COMMENT varchar(60),
MA_LIEU_STOCKAGE varchar(60),
MA_MODELE varchar(20),
MA_ANNEE year(4),
MA_NB int(11) DEFAULT '1',
S_ID smallint(6) DEFAULT '0' NOT NULL,
VP_ID varchar(5) DEFAULT 'OP' NOT NULL,
MA_EXTERNE tinyint(4),
MA_INVENTAIRE varchar(40),
MA_UPDATE_DATE date,
MA_UPDATE_BY int(11),
AFFECTED_TO int(11),
MA_REV_DATE date,
V_ID int(11),
MA_PARENT int(11),
PRIMARY KEY (MA_ID),
KEY TM_ID (TM_ID),
KEY S_ID (S_ID),
KEY AFFECTED_TO (AFFECTED_TO),
KEY V_ID (V_ID),
KEY MA_PARENT (MA_PARENT),
KEY VP_ID (VP_ID)
);
# ------------------------------------
# data for table 'materiel'
# ------------------------------------



# ------------------------------------
# structure for table 'message'
# ------------------------------------

DROP TABLE IF EXISTS message ;
CREATE TABLE message (
M_ID int(11) NOT NULL auto_increment,
S_ID smallint(6) DEFAULT '0' NOT NULL,
M_DATE datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
P_ID int(11) DEFAULT '0' NOT NULL,
M_TEXTE varchar(2000),
M_OBJET varchar(50) NOT NULL,
M_DUREE smallint(6),
M_TYPE varchar(10) DEFAULT 'consigne' NOT NULL,
M_FILE varchar(100),
TM_ID tinyint(4) DEFAULT '0' NOT NULL,
PRIMARY KEY (M_ID),
KEY M_DATE (M_DATE),
KEY P_ID (P_ID),
KEY S_ID (S_ID),
KEY M_TYPE (M_TYPE),
KEY TM_ID (TM_ID)
);
# ------------------------------------
# data for table 'message'
# ------------------------------------

INSERT INTO message VALUES ('1','0','2010-10-27 22:51:04','1','Voici le site de votre Organisation. Commencer par choisir la Configuration.','Bienvenue','127','consigne','','0');
INSERT INTO message VALUES ('2','0','2010-10-27 22:51:04','1','En cas de problèmes techniques, demander Nicolas MARCHE (nmarche@users.sourceforge.net)','Questions','127','consigne','','0');


# ------------------------------------
# structure for table 'personnel_formation'
# ------------------------------------

DROP TABLE IF EXISTS personnel_formation ;
CREATE TABLE personnel_formation (
PF_ID int(11) NOT NULL auto_increment,
P_ID int(11) NOT NULL,
PS_ID smallint(6) NOT NULL,
TF_CODE varchar(1) NOT NULL,
PF_DIPLOME varchar(20),
PF_COMMENT varchar(100),
PF_ADMIS tinyint(4) DEFAULT '1' NOT NULL,
PF_DATE date,
PF_RESPONSABLE varchar(60),
PF_LIEU varchar(40),
E_CODE int(11),
PF_UPDATE_BY int(11),
PF_UPDATE_DATE datetime,
PF_PRINT_BY int(11),
PF_PRINT_DATE datetime,
PF_EXPIRATION date,
PRIMARY KEY (PF_ID),
KEY P_ID (P_ID, PS_ID),
KEY E_CODE (E_CODE)
);
# ------------------------------------
# data for table 'personnel_formation'
# ------------------------------------



# ------------------------------------
# structure for table 'planning_garde'
# ------------------------------------

DROP TABLE IF EXISTS planning_garde ;
CREATE TABLE planning_garde (
S_ID smallint(6) DEFAULT '0' NOT NULL,
PG_DATE date DEFAULT '0000-00-00' NOT NULL,
TYPE char(2) DEFAULT 'J' NOT NULL,
PS_ID int(11) DEFAULT '1' NOT NULL,
EQ_ID tinyint(4) DEFAULT '1' NOT NULL,
P_ID int(11),
PG_STATUT varchar(5) DEFAULT 'SPV' NOT NULL,
PRIMARY KEY (S_ID, PG_DATE, TYPE, PS_ID),
KEY P_ID (P_ID),
KEY PS_ID (PS_ID)
);
# ------------------------------------
# data for table 'planning_garde'
# ------------------------------------



# ------------------------------------
# structure for table 'planning_garde_status'
# ------------------------------------

DROP TABLE IF EXISTS planning_garde_status ;
CREATE TABLE planning_garde_status (
S_ID smallint(6) DEFAULT '2' NOT NULL,
PGS_YEAR smallint(6) DEFAULT '0' NOT NULL,
PGS_MONTH tinyint(4) DEFAULT '0' NOT NULL,
EQ_ID tinyint(4) DEFAULT '1' NOT NULL,
PGS_STATUS varchar(5) NOT NULL,
PRIMARY KEY (S_ID, PGS_YEAR, PGS_MONTH, EQ_ID)
);
# ------------------------------------
# data for table 'planning_garde_status'
# ------------------------------------



# ------------------------------------
# structure for table 'pompier'
# ------------------------------------

DROP TABLE IF EXISTS pompier ;
CREATE TABLE pompier (
P_ID int(11) NOT NULL auto_increment,
P_CODE varchar(20) NOT NULL,
P_PRENOM varchar(20) NOT NULL,
P_NOM varchar(30) NOT NULL,
P_SEXE varchar(1) DEFAULT 'M' NOT NULL,
P_OLD_MEMBER tinyint(1) DEFAULT '0' NOT NULL,
P_UPDATED_BY int(11),
P_GRADE varchar(5) DEFAULT 'SAP' NOT NULL,
P_STATUT varchar(5) DEFAULT 'SPV' NOT NULL,
P_MDP varchar(50) NOT NULL,
P_PASSWORD_FAILURE tinyint(4),
P_DEBUT varchar(4),
P_FIN date,
P_SECTION smallint(6),
C_ID int(11) DEFAULT '0' NOT NULL,
GP_ID smallint(6) DEFAULT '0' NOT NULL,
GP_ID2 smallint(6),
P_BIRTHDATE date,
P_BIRTHPLACE varchar(40),
P_EMAIL varchar(60) NOT NULL,
P_HORAIRE smallint(6),
P_PHONE varchar(20),
P_PHONE2 varchar(20),
P_ABBREGE varchar(5),
P_ADDRESS varchar(150),
P_ZIP_CODE varchar(6),
P_CITY varchar(30),
P_RELATION_PRENOM varchar(20),
P_RELATION_NOM varchar(30),
P_RELATION_PHONE varchar(20),
P_HIDE tinyint(4) DEFAULT '0' NOT NULL,
P_PHOTO varchar(50),
P_LAST_CONNECT datetime,
P_NB_CONNECT int(11) DEFAULT '0' NOT NULL,
GP_FLAG1 tinyint(4) DEFAULT '0' NOT NULL,
GP_FLAG2 tinyint(4) DEFAULT '0' NOT NULL,
TS_CODE varchar(5),
TS_HEURES int(11),
P_NOSPAM tinyint(4) DEFAULT '0' NOT NULL,
P_CREATED_BY int(11),
P_CREATE_DATE date,
P_SKYPE varchar(40),
PRIMARY KEY (P_ID),
   UNIQUE P_CODE (P_CODE),
   UNIQUE P_HOMONYM (P_SECTION, P_NOM, P_PRENOM),
KEY GP_ID (GP_ID),
KEY P_OLD_MEMBER (P_OLD_MEMBER),
KEY P_STATUT (P_STATUT),
KEY C_ID (C_ID),
KEY GP_ID2 (GP_ID2),
KEY P_ZIP_CODE (P_ZIP_CODE),
KEY P_NOM (P_NOM),
KEY P_CITY (P_CITY)
);
# ------------------------------------
# data for table 'pompier'
# ------------------------------------

INSERT INTO pompier VALUES ('1','1234','admin','admin','M','0',NULL,'SAP','SPV','81dc9bdb52d04dc20036dbd8313ed055',NULL,'2006',NULL,'0','0','4',NULL,NULL,NULL,'admin@mybrigade.org',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0',NULL,'2011-07-19 21:45:05','3','0','0',NULL,NULL,'0',NULL,NULL,NULL);


# ------------------------------------
# structure for table 'poste'
# ------------------------------------

DROP TABLE IF EXISTS poste ;
CREATE TABLE poste (
S_ID smallint(6) DEFAULT '0' NOT NULL,
PS_ID int(11) DEFAULT '0' NOT NULL,
EQ_ID smallint(6) DEFAULT '1' NOT NULL,
TYPE varchar(8) NOT NULL,
DESCRIPTION varchar(80) NOT NULL,
PO_JOUR tinyint(4) DEFAULT '1' NOT NULL,
PO_NUIT tinyint(4) DEFAULT '1' NOT NULL,
PS_EXPIRABLE tinyint(4) DEFAULT '0' NOT NULL,
PS_AUDIT tinyint(4) DEFAULT '0' NOT NULL,
PS_DIPLOMA tinyint(4) DEFAULT '0' NOT NULL,
PS_RECYCLE tinyint(4) DEFAULT '0' NOT NULL,
PS_USER_MODIFIABLE tinyint(4) DEFAULT '0' NOT NULL,
PS_PRINTABLE tinyint(4) DEFAULT '0' NOT NULL,
PS_NATIONAL tinyint(4) DEFAULT '0' NOT NULL,
PS_SECOURISME tinyint(4) DEFAULT '0' NOT NULL,
F_ID int(11) DEFAULT '4' NOT NULL,
PRIMARY KEY (S_ID, PS_ID),
KEY PS_ID (PS_ID),
KEY EQ_ID (EQ_ID)
);
# ------------------------------------
# data for table 'poste'
# ------------------------------------

INSERT INTO poste VALUES ('0','1','1','CDS','Chef de garde','1','1','0','0','0','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','2','1','COD','Conducteur PL','1','1','0','0','0','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','3','1','EQ1','Equipier 1','1','1','0','0','0','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','4','1','EQ2','Equipier 2','1','1','0','0','0','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','8','2','CA','Chef d\'agrès','1','1','0','0','0','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','9','2','COD','Conducteur','1','1','0','0','0','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','10','2','EQ1','Equipier 1','1','1','0','0','0','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','11','2','EQ2','Equipier 2','1','1','0','0','0','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','12','3','PSE1','Equipier secouriste PSE1','0','0','1','0','1','1','0','1','0','1','4');
INSERT INTO poste VALUES ('0','13','3','PSE2','Equipier secouriste PSE2','0','0','1','0','1','1','0','1','0','1','4');
INSERT INTO poste VALUES ('0','14','3','PAE1','Formateur secourisme PAE1','0','0','1','0','1','1','0','0','0','1','4');
INSERT INTO poste VALUES ('0','15','3','PAE2','Formateur secourisme PAE2','0','0','1','0','1','1','0','0','0','1','4');
INSERT INTO poste VALUES ('0','16','3','PAE3','Formateur secourisme PAE3','0','0','1','0','1','1','0','0','0','1','4');
INSERT INTO poste VALUES ('0','17','4','VL','Permis voiture','0','0','0','0','1','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','18','4','PB','Permis blanc','0','0','0','0','1','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','19','4','PL','Permis poids lourd','0','0','0','0','1','0','0','0','0','0','4');
INSERT INTO poste VALUES ('0','20','3','PSC1','Premiers secours civique','0','0','0','0','1','0','0','1','0','1','4');


# ------------------------------------
# structure for table 'priorite'
# ------------------------------------

DROP TABLE IF EXISTS priorite ;
CREATE TABLE priorite (
P_ID int(11) DEFAULT '0' NOT NULL,
PS_ID int(11) DEFAULT '0' NOT NULL,
SCORE int(11) DEFAULT '0' NOT NULL,
PRIMARY KEY (P_ID, PS_ID)
);
# ------------------------------------
# data for table 'priorite'
# ------------------------------------



# ------------------------------------
# structure for table 'qualification'
# ------------------------------------

DROP TABLE IF EXISTS qualification ;
CREATE TABLE qualification (
P_ID int(11) DEFAULT '0' NOT NULL,
PS_ID int(11) DEFAULT '0' NOT NULL,
Q_VAL tinyint(4) DEFAULT '1' NOT NULL,
Q_EXPIRATION date,
Q_UPDATED_BY int(11),
Q_UPDATE_DATE datetime,
PRIMARY KEY (P_ID, PS_ID),
KEY PS_ID (PS_ID),
KEY Q_EXPIRATION (Q_EXPIRATION)
);
# ------------------------------------
# data for table 'qualification'
# ------------------------------------



# ------------------------------------
# structure for table 'section'
# ------------------------------------

DROP TABLE IF EXISTS section ;
CREATE TABLE section (
S_ID smallint(6) DEFAULT '0' NOT NULL,
S_PARENT smallint(6) DEFAULT '0' NOT NULL,
S_CODE varchar(12) DEFAULT 'MON CODE' NOT NULL,
S_DESCRIPTION varchar(50),
S_URL varchar(40),
S_PHONE varchar(20),
S_PHONE2 varchar(20),
S_FAX varchar(20),
S_ADDRESS varchar(150),
S_ZIP_CODE varchar(6),
S_CITY varchar(30),
S_EMAIL varchar(60),
S_EMAIL2 varchar(60),
S_PDF_PAGE varchar(250),
S_PDF_MARGE_TOP float DEFAULT '15',
S_PDF_MARGE_LEFT float DEFAULT '15',
S_PDF_TEXTE_TOP float DEFAULT '40',
S_PDF_TEXTE_BOTTOM float DEFAULT '25',
S_PDF_BADGE varchar(250),
S_PDF_SIGNATURE varchar(250),
s_devis_debut tinytext,
s_devis_fin tinytext,
s_facture_debut text,
s_facture_fin text,
S_FRAIS_ANNULATION VARCHAR(5) NOT NULL DEFAULT '0',
DPS_MAX_TYPE tinyint(4),
PRIMARY KEY (S_ID),
   UNIQUE S_CODE (S_CODE),
KEY S_PARENT (S_PARENT)
);
# ------------------------------------
# data for table 'section'
# ------------------------------------

INSERT INTO section VALUES ('1','0','section 1','section 1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'15','15','40','25',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO section VALUES ('2','0','section 2','section 2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'15','15','40','25',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO section VALUES ('3','0','section 3','section 3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'15','15','40','25',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO section VALUES ('4','0','hors section','hors sections',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'15','15','40','25',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO section VALUES ('0','-1','eBrigade','Nom de l\'organisation',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'15','15','40','25',NULL,NULL,NULL,NULL,NULL,NULL,NULL);


# ------------------------------------
# structure for table 'section_flat'
# ------------------------------------

DROP TABLE IF EXISTS section_flat ;
CREATE TABLE section_flat (
LIG int(11) NOT NULL auto_increment,
NIV tinyint(4) NOT NULL,
S_ID int(11) NOT NULL,
S_PARENT int(11) NOT NULL,
S_CODE varchar(12),
S_DESCRIPTION varchar(50),
NB_P int(11),
NB_V int(11),
PRIMARY KEY (LIG)
);
# ------------------------------------
# data for table 'section_flat'
# ------------------------------------

INSERT INTO section_flat VALUES ('1','0','0','-1','eBrigade','Nom de l\'organisation','0','0');
INSERT INTO section_flat VALUES ('2','1','4','0','hors section','hors sections','0','0');
INSERT INTO section_flat VALUES ('3','1','1','0','section 1','section 1','0','0');
INSERT INTO section_flat VALUES ('4','1','2','0','section 2','section 2','0','0');
INSERT INTO section_flat VALUES ('5','1','3','0','section 3','section 3','0','0');


# ------------------------------------
# structure for table 'section_role'
# ------------------------------------

DROP TABLE IF EXISTS section_role ;
CREATE TABLE section_role (
S_ID int(11) NOT NULL,
GP_ID smallint(6) DEFAULT '0' NOT NULL,
P_ID int(11) NOT NULL,
PRIMARY KEY (S_ID, GP_ID),
KEY P_ID (P_ID),
KEY GP_ID (GP_ID)
);
# ------------------------------------
# data for table 'section_role'
# ------------------------------------



# ------------------------------------
# structure for table 'smslog'
# ------------------------------------

DROP TABLE IF EXISTS smslog ;
CREATE TABLE smslog (
P_ID int(11) DEFAULT '0' NOT NULL,
S_DATE datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
S_TEXTE varchar(200) NOT NULL,
S_NB int(11) DEFAULT '0' NOT NULL,
PRIMARY KEY (P_ID, S_DATE)
);
# ------------------------------------
# data for table 'smslog'
# ------------------------------------



# ------------------------------------
# structure for table 'statut'
# ------------------------------------

DROP TABLE IF EXISTS statut ;
CREATE TABLE statut (
S_STATUT varchar(5) NOT NULL,
S_DESCRIPTION varchar(50) NOT NULL,
S_CONTEXT tinyint(4) NOT NULL,
PRIMARY KEY (S_CONTEXT, S_STATUT)
);
# ------------------------------------
# data for table 'statut'
# ------------------------------------

INSERT INTO statut VALUES ('SPV','Sapeur Pompier Volontaire','3');
INSERT INTO statut VALUES ('SPP','Sapeur Pompier Professionnel','3');
INSERT INTO statut VALUES ('PATS','Agent Territorial','3');
INSERT INTO statut VALUES ('BEN','Personnel bénévole','0');
INSERT INTO statut VALUES ('SAL','Personnel salarié','0');
INSERT INTO statut VALUES ('SPV','Sapeur Pompier Volontaire','1');
INSERT INTO statut VALUES ('EXT','Personnel externe','0');


# ------------------------------------
# structure for table 'type_agrement'
# ------------------------------------

DROP TABLE IF EXISTS type_agrement ;
CREATE TABLE type_agrement (
TA_CODE varchar(5) NOT NULL,
CA_CODE varchar(5) NOT NULL,
TA_DESCRIPTION varchar(60) NOT NULL,
TA_FLAG tinyint(4) DEFAULT '0' NOT NULL,
PRIMARY KEY (TA_CODE)
);
# ------------------------------------
# data for table 'type_agrement'
# ------------------------------------

INSERT INTO type_agrement VALUES ('A1','SEC','Opérations de secours à personnes et sauvetage','0');
INSERT INTO type_agrement VALUES ('A2','SEC','Recherche cynophile','0');
INSERT INTO type_agrement VALUES ('A3','SEC','Sécurité des activités aquatiques','0');
INSERT INTO type_agrement VALUES ('B','SEC','Actions de soutien aux populations sinistrées','0');
INSERT INTO type_agrement VALUES ('C','SEC','Encadrement des bénévoles lors des actions de soutien','0');
INSERT INTO type_agrement VALUES ('D','SEC','Dispositif prévisionnel de secours','0');
INSERT INTO type_agrement VALUES ('PSE','FOR','Formations aux premiers secours','0');
INSERT INTO type_agrement VALUES ('PAE','FOR','Formations au monitorat de premiers secours','0');
INSERT INTO type_agrement VALUES ('BNSSA','FOR','Formations au B.N.S.S.A','0');
INSERT INTO type_agrement VALUES ('37','CON','Missions de secours d’urgence aux personnes','0');
INSERT INTO type_agrement VALUES ('38','CON','Actions de soutien aux populations et de formation','0');
INSERT INTO type_agrement VALUES ('COTIS','ASS','Cotisation fédérale','0');
INSERT INTO type_agrement VALUES ('CONTR','ASS','Contribution fédérale','0');
INSERT INTO type_agrement VALUES ('AUT','ASS','Autorisation d\'exercice','0');


# ------------------------------------
# structure for table 'type_agrement_valeur'
# ------------------------------------

DROP TABLE IF EXISTS type_agrement_valeur ;
CREATE TABLE type_agrement_valeur (
TAV_ID smallint(6) NOT NULL,
TA_CODE varchar(5) NOT NULL,
TA_SHORT varchar(8),
TA_VALEUR varchar(40) NOT NULL,
TA_FLAG smallint(6) NOT NULL,
PRIMARY KEY (TAV_ID),
KEY TA_CODE (TA_CODE)
);
# ------------------------------------
# data for table 'type_agrement_valeur'
# ------------------------------------

INSERT INTO type_agrement_valeur VALUES ('1','D','-','Aucun DPS possible','0');
INSERT INTO type_agrement_valeur VALUES ('2','D','PAPS','Point alerte et premiers secours (max 2)','2');
INSERT INTO type_agrement_valeur VALUES ('3','D','DPS-PE','Petite envergure (max 12)','12');
INSERT INTO type_agrement_valeur VALUES ('4','D','DPS-ME','Moyenne envergure (max 36)','36');
INSERT INTO type_agrement_valeur VALUES ('5','D','DPS-GE','Grande envergure (plus de 36)','999');


# ------------------------------------
# structure for table 'type_bilan'
# ------------------------------------

DROP TABLE IF EXISTS type_bilan ;
CREATE TABLE type_bilan (
TB_ID smallint(6) NOT NULL,
TE_CODE varchar(5) NOT NULL,
TB_NUM tinyint(4) NOT NULL,
TB_LIBELLE varchar(40) NOT NULL,
PRIMARY KEY (TB_ID),
KEY TE_CODE (TE_CODE)
);
# ------------------------------------
# data for table 'type_bilan'
# ------------------------------------

INSERT INTO type_bilan VALUES ('1','DPS','1','soins réalisés (hors évac.)');
INSERT INTO type_bilan VALUES ('2','DPS','2','évacuations réalisées');
INSERT INTO type_bilan VALUES ('3','GAR','1','interventions');
INSERT INTO type_bilan VALUES ('4','GAR','2','évacuations réalisées');
INSERT INTO type_bilan VALUES ('5','MAR','1','personnes rencontrées');
INSERT INTO type_bilan VALUES ('6','MAR','2','transports en centre d\'hébergement');
INSERT INTO type_bilan VALUES ('7','DPS','3','personnes assistées');
INSERT INTO type_bilan VALUES ('8','VACCI','1','soins réalisés (hors évac.)');
INSERT INTO type_bilan VALUES ('9','VACCI','2','évacuations réalisées');


# ------------------------------------
# structure for table 'type_company'
# ------------------------------------

DROP TABLE IF EXISTS type_company ;
CREATE TABLE type_company (
TC_CODE varchar(8) NOT NULL,
TC_LIBELLE varchar(30) NOT NULL,
PRIMARY KEY (TC_CODE)
);
# ------------------------------------
# data for table 'type_company'
# ------------------------------------

INSERT INTO type_company VALUES ('ASSOC','Association');
INSERT INTO type_company VALUES ('ECOLE','Ecole');
INSERT INTO type_company VALUES ('COLLEGE','Collège');
INSERT INTO type_company VALUES ('LYCEE','Lycée');
INSERT INTO type_company VALUES ('ENTPRIV','Entreprise privée');
INSERT INTO type_company VALUES ('ENTPUB','Entreprise publique');
INSERT INTO type_company VALUES ('MAIRIE','Mairie');


# ------------------------------------
# structure for table 'type_company_role'
# ------------------------------------

DROP TABLE IF EXISTS type_company_role ;
CREATE TABLE type_company_role (
TCR_CODE varchar(5) NOT NULL,
TCR_DESCRIPTION varchar(40) NOT NULL,
TCR_FLAG tinyint(4),
PRIMARY KEY (TCR_CODE)
);
# ------------------------------------
# data for table 'type_company_role'
# ------------------------------------

INSERT INTO type_company_role VALUES ('MED','Médecin référent',NULL);
INSERT INTO type_company_role VALUES ('RF','Responsable formations',NULL);
INSERT INTO type_company_role VALUES ('MED2','Médecin supplémentaire','0');
INSERT INTO type_company_role VALUES ('MED3','Médecin supplémentaire','0');
INSERT INTO type_company_role VALUES ('RO','Responsable opérationnel',NULL);


# ------------------------------------
# structure for table 'type_document'
# ------------------------------------

DROP TABLE IF EXISTS type_document ;
CREATE TABLE type_document (
TD_CODE varchar(5) NOT NULL,
TD_LIBELLE varchar(50) NOT NULL,
PRIMARY KEY (TD_CODE)
);
# ------------------------------------
# data for table 'type_document'
# ------------------------------------

INSERT INTO type_document VALUES ('CRAG','Compte rendu assemblée générale');
INSERT INTO type_document VALUES ('CRR','Compte rendu de réunion');
INSERT INTO type_document VALUES ('NS','Note de service');
INSERT INTO type_document VALUES ('DOCOP','Procédures opérationnelles');
INSERT INTO type_document VALUES ('DOCAD','Documentation administrative');
INSERT INTO type_document VALUES ('MODEL','Modèle de document');
INSERT INTO type_document VALUES ('DIV','Documents divers');
INSERT INTO type_document VALUES ('FOR','Formation');
INSERT INTO type_document VALUES ('DPS','D.P.S.');
INSERT INTO type_document VALUES ('TRANS','Transmission');
INSERT INTO type_document VALUES ('MAT','Matériel');
INSERT INTO type_document VALUES ('VEHI','Véhicules');
INSERT INTO type_document VALUES ('CACH','Centrale d\'achat');


# ------------------------------------
# structure for table 'type_evenement'
# ------------------------------------

DROP TABLE IF EXISTS type_evenement ;
CREATE TABLE type_evenement (
TE_CODE varchar(5) NOT NULL,
TE_LIBELLE varchar(40) NOT NULL,
CEV_CODE varchar(5) DEFAULT 'C_DIV' NOT NULL,
PRIMARY KEY (TE_CODE)
);
# ------------------------------------
# data for table 'type_evenement'
# ------------------------------------

INSERT INTO type_evenement VALUES ('CER','Cérémonie','C_DIV');
INSERT INTO type_evenement VALUES ('MAN','Manoeuvre','C_FOR');
INSERT INTO type_evenement VALUES ('FOR','Formation','C_FOR');
INSERT INTO type_evenement VALUES ('DPS','Dispositif Prévisionnel de Secours','C_SEC');
INSERT INTO type_evenement VALUES ('REU','Réunion','C_DIV');
INSERT INTO type_evenement VALUES ('SPO','Compétition sportive','C_DIV');
INSERT INTO type_evenement VALUES ('MET','Alerte des bénévoles','C_OPE');
INSERT INTO type_evenement VALUES ('DIV','Evénement divers','C_DIV');
INSERT INTO type_evenement VALUES ('GAR','Garde','C_SEC');
INSERT INTO type_evenement VALUES ('MAR','Maraude','C_SEC');
INSERT INTO type_evenement VALUES ('TEC','Entretien, opérations techniques','C_DIV');
INSERT INTO type_evenement VALUES ('HEB','Hébergement d\'urgence','C_OPE');
INSERT INTO type_evenement VALUES ('AIP','Aide aux populations','C_OPE');
INSERT INTO type_evenement VALUES ('GRIPA','OGRIPA opérations diverses','C_OPE');
INSERT INTO type_evenement VALUES ('VACCI','OGRIPA centre de vaccination','C_OPE');
INSERT INTO type_evenement VALUES ('AH','Autres actions humanitaires','C_OPE');
INSERT INTO type_evenement VALUES ('EXE','Participation à exercice état-sdis-samu','C_FOR');
INSERT INTO type_evenement VALUES ('MLA','Mission Logistique et Administrative','C_DIV');


# ------------------------------------
# structure for table 'type_fonctionnalite'
# ------------------------------------

DROP TABLE IF EXISTS type_fonctionnalite ;
CREATE TABLE type_fonctionnalite (
TF_ID tinyint(4) NOT NULL,
TF_DESCRIPTION varchar(40) NOT NULL,
PRIMARY KEY (TF_ID)
);
# ------------------------------------
# data for table 'type_fonctionnalite'
# ------------------------------------

INSERT INTO type_fonctionnalite VALUES ('0','général');
INSERT INTO type_fonctionnalite VALUES ('1','configuration');
INSERT INTO type_fonctionnalite VALUES ('2','sécurité');
INSERT INTO type_fonctionnalite VALUES ('3','paramétrage');
INSERT INTO type_fonctionnalite VALUES ('4','personnel');
INSERT INTO type_fonctionnalite VALUES ('5','compétences');
INSERT INTO type_fonctionnalite VALUES ('6','événements');
INSERT INTO type_fonctionnalite VALUES ('7','administratif');
INSERT INTO type_fonctionnalite VALUES ('8','gardes');
INSERT INTO type_fonctionnalite VALUES ('9','information');
INSERT INTO type_fonctionnalite VALUES ('10','notifications');


# ------------------------------------
# structure for table 'type_formation'
# ------------------------------------

DROP TABLE IF EXISTS type_formation ;
CREATE TABLE type_formation (
TF_CODE varchar(1) NOT NULL,
TF_LIBELLE varchar(40) NOT NULL,
PRIMARY KEY (TF_CODE)
);
# ------------------------------------
# data for table 'type_formation'
# ------------------------------------

INSERT INTO type_formation VALUES ('P','prérequis à une formation');
INSERT INTO type_formation VALUES ('I','formation initiale/diplôme');
INSERT INTO type_formation VALUES ('C','formation complémentaire');
INSERT INTO type_formation VALUES ('R','formation continue');
INSERT INTO type_formation VALUES ('T','initiation');


# ------------------------------------
# structure for table 'type_indisponibilite'
# ------------------------------------

DROP TABLE IF EXISTS type_indisponibilite ;
CREATE TABLE type_indisponibilite (
TI_CODE varchar(5) NOT NULL,
TI_LIBELLE varchar(30) NOT NULL,
TI_FLAG tinyint(4) DEFAULT '0' NOT NULL,
PRIMARY KEY (TI_CODE)
);
# ------------------------------------
# data for table 'type_indisponibilite'
# ------------------------------------

INSERT INTO type_indisponibilite VALUES ('CP','Congés payés','1');
INSERT INTO type_indisponibilite VALUES ('FOR','formation','0');
INSERT INTO type_indisponibilite VALUES ('MAL','maladie / blessure','0');
INSERT INTO type_indisponibilite VALUES ('PRO','raison professionnelle','0');
INSERT INTO type_indisponibilite VALUES ('FAM','raison familiale','0');
INSERT INTO type_indisponibilite VALUES ('DIV','autre raison','0');
INSERT INTO type_indisponibilite VALUES ('RTT','Réduction du temps de travail','1');


# ------------------------------------
# structure for table 'type_materiel'
# ------------------------------------

DROP TABLE IF EXISTS type_materiel ;
CREATE TABLE type_materiel (
TM_ID int(11) NOT NULL auto_increment,
TM_CODE varchar(25) NOT NULL,
TM_DESCRIPTION varchar(60) NOT NULL,
TM_USAGE varchar(15) DEFAULT 'DIVERS' NOT NULL,
TM_LOT tinyint(4) DEFAULT '0' NOT NULL,
PRIMARY KEY (TM_ID),
   UNIQUE TM_CODE (TM_USAGE, TM_CODE)
);
# ------------------------------------
# data for table 'type_materiel'
# ------------------------------------

INSERT INTO type_materiel VALUES ('2','LOT A','Sac de secours avec équipement lot A','Sanitaire','0');
INSERT INTO type_materiel VALUES ('3','LOT B','Sac de secours avec équipement lot B','Sanitaire','0');
INSERT INTO type_materiel VALUES ('4','LOT C','Sac de secours avec équipement lot C (Hors VPS)','Sanitaire','0');
INSERT INTO type_materiel VALUES ('5','Lits Picots','','Hébergement','0');
INSERT INTO type_materiel VALUES ('6','DAE','Défibrillateur automatique externe','Sanitaire','0');
INSERT INTO type_materiel VALUES ('7','Oxygène','','Sanitaire','0');
INSERT INTO type_materiel VALUES ('8','Radios 450 Mhz','','Transmission','0');
INSERT INTO type_materiel VALUES ('10','Radios 150 MHz','','Transmission','0');
INSERT INTO type_materiel VALUES ('37','Valise P.C','450 MHz','Transmission','0');
INSERT INTO type_materiel VALUES ('21','Immobilisateurs de tête','','Sanitaire','0');
INSERT INTO type_materiel VALUES ('13','Valise P.C.','150 MHz','Transmission','0');
INSERT INTO type_materiel VALUES ('14','Pantalons','','Habillement','0');
INSERT INTO type_materiel VALUES ('15','Mannequins','','Formation','0');
INSERT INTO type_materiel VALUES ('16','Groupes électogènes','','Eléctrique','0');
INSERT INTO type_materiel VALUES ('17','D.A.E.','','Formation','0');
INSERT INTO type_materiel VALUES ('18','Portables','','Informatique','0');
INSERT INTO type_materiel VALUES ('19','Fixes','','Informatique','0');
INSERT INTO type_materiel VALUES ('20','Tentes','','Hébergement','0');
INSERT INTO type_materiel VALUES ('24','Vestes','','Habillement','0');
INSERT INTO type_materiel VALUES ('25','Parkas','','Habillement','0');
INSERT INTO type_materiel VALUES ('26','Polos','','Habillement','0');
INSERT INTO type_materiel VALUES ('27','Polaires','','Habillement','0');
INSERT INTO type_materiel VALUES ('28','Eclairages','','Eléctrique','0');
INSERT INTO type_materiel VALUES ('29','Rallonges','','Eléctrique','0');
INSERT INTO type_materiel VALUES ('30','Classeurs','','Formation','0');
INSERT INTO type_materiel VALUES ('31','CD ROM','','Formation','0');
INSERT INTO type_materiel VALUES ('34','Vidéos Projecteurs','','Informatique','0');
INSERT INTO type_materiel VALUES ('32','Couvertures','','Hébergement','0');
INSERT INTO type_materiel VALUES ('33','Sacs de Couchage','','Hébergement','0');
INSERT INTO type_materiel VALUES ('35','Imprimantes','','Informatique','0');
INSERT INTO type_materiel VALUES ('36','tee-shirts','','Habillement','0');
INSERT INTO type_materiel VALUES ('38','Antennes','','Transmission','0');
INSERT INTO type_materiel VALUES ('39','Tronçonneuses','','Elagage','0');
INSERT INTO type_materiel VALUES ('40','Thermos','','Logistique','0');
INSERT INTO type_materiel VALUES ('41','Jerricanes Alimentaires','','Logistique','0');
INSERT INTO type_materiel VALUES ('42','Claies de Portage','','Logistique','0');
INSERT INTO type_materiel VALUES ('43','Néons','','Eclairage','0');
INSERT INTO type_materiel VALUES ('44','Trépieds Hallogènes','','Eclairage','0');
INSERT INTO type_materiel VALUES ('45','Brancards','','Hébergement','0');
INSERT INTO type_materiel VALUES ('46','Jerricanes','','Divers','0');
INSERT INTO type_materiel VALUES ('47','Brancards Pliants','','Sanitaire','0');
INSERT INTO type_materiel VALUES ('48','Chaises Porteurs','','Sanitaire','0');
INSERT INTO type_materiel VALUES ('49','Brancards Cuillères','','Sanitaire','0');
INSERT INTO type_materiel VALUES ('50','Chauffages Electriques','','Hébergement','0');
INSERT INTO type_materiel VALUES ('51','Aspirateurs à eau','','Pompage','0');
INSERT INTO type_materiel VALUES ('52','Motos Pompes','','Pompage','0');
INSERT INTO type_materiel VALUES ('53','Seaux','','Pompage','0');
INSERT INTO type_materiel VALUES ('54','Raclettes','','Pompage','0');
INSERT INTO type_materiel VALUES ('55','Serpillières','','Pompage','0');
INSERT INTO type_materiel VALUES ('56','Vides Caves','','Pompage','0');
INSERT INTO type_materiel VALUES ('57','Téléphones Portables','','Transmission','0');
INSERT INTO type_materiel VALUES ('58','Extincteur à poudre','','Incendie','0');
INSERT INTO type_materiel VALUES ('59','Extincteur à eau','','Incendie','0');


# ------------------------------------
# structure for table 'type_membre'
# ------------------------------------

DROP TABLE IF EXISTS type_membre ;
CREATE TABLE type_membre (
TM_ID tinyint(4) NOT NULL,
TM_CODE varchar(30) NOT NULL,
PRIMARY KEY (TM_ID)
);
# ------------------------------------
# data for table 'type_membre'
# ------------------------------------

INSERT INTO type_membre VALUES ('0','actif');
INSERT INTO type_membre VALUES ('1','n\'a plus d\'activité');
INSERT INTO type_membre VALUES ('2','a démissionné');
INSERT INTO type_membre VALUES ('3','décédé');
INSERT INTO type_membre VALUES ('4','radié');
INSERT INTO type_membre VALUES ('5','suspendu(e)');


# ------------------------------------
# structure for table 'type_message'
# ------------------------------------

DROP TABLE IF EXISTS type_message ;
CREATE TABLE type_message (
TM_ID tinyint(4) NOT NULL,
TM_LIBELLE varchar(30) NOT NULL,
TM_COLOR varchar(20) NOT NULL,
TM_ICON varchar(20),
PRIMARY KEY (TM_ID)
);
# ------------------------------------
# data for table 'type_message'
# ------------------------------------

INSERT INTO type_message VALUES ('0','normal','#000099','bullet.gif');
INSERT INTO type_message VALUES ('1','informatique','#04520E','smallmycomputer.png');
INSERT INTO type_message VALUES ('2','urgent','#BC0803','miniwarn.png');


# ------------------------------------
# structure for table 'type_participation'
# ------------------------------------

DROP TABLE IF EXISTS type_participation ;
CREATE TABLE type_participation (
TP_ID smallint(6) NOT NULL auto_increment,
TE_CODE varchar(5) NOT NULL,
TP_NUM tinyint(4) NOT NULL,
TP_LIBELLE varchar(40) NOT NULL,
PS_ID int(11) DEFAULT '0' NOT NULL,
PS_ID2 int(11) DEFAULT '0' NOT NULL,
INSTRUCTOR tinyint(4) DEFAULT '0' NOT NULL,
PRIMARY KEY (TP_ID),
KEY TE_CODE (TE_CODE)
);
# ------------------------------------
# data for table 'type_participation'
# ------------------------------------

INSERT INTO type_participation VALUES ('1','FOR','1','Responsable pédagogique','0','0','1');
INSERT INTO type_participation VALUES ('2','FOR','2','Instructeur','0','0','1');
INSERT INTO type_participation VALUES ('3','FOR','3','Aide moniteur','0','0','1');
INSERT INTO type_participation VALUES ('4','FOR','4','Plastron','0','0','0');
INSERT INTO type_participation VALUES ('5','DPS','1','Chef de dispositif','0','0','0');
INSERT INTO type_participation VALUES ('6','DPS','2','Chef de secteur','0','0','0');
INSERT INTO type_participation VALUES ('7','DPS','3','Chef de poste','0','0','0');
INSERT INTO type_participation VALUES ('8','DPS','4','Conducteur','0','0','0');


# ------------------------------------
# structure for table 'type_salarie'
# ------------------------------------

DROP TABLE IF EXISTS type_salarie ;
CREATE TABLE type_salarie (
TS_CODE varchar(5) NOT NULL,
TS_LIBELLE varchar(40) NOT NULL,
PRIMARY KEY (TS_CODE)
);
# ------------------------------------
# data for table 'type_salarie'
# ------------------------------------

INSERT INTO type_salarie VALUES ('TC','temps complet');
INSERT INTO type_salarie VALUES ('TP','temps partiel');
INSERT INTO type_salarie VALUES ('VNP','vacataire non permanent');


# ------------------------------------
# structure for table 'type_vehicule'
# ------------------------------------

DROP TABLE IF EXISTS type_vehicule ;
CREATE TABLE type_vehicule (
TV_CODE varchar(10) NOT NULL,
TV_LIBELLE varchar(60) NOT NULL,
TV_NB tinyint(4) DEFAULT '3' NOT NULL,
TV_USAGE varchar(12) DEFAULT 'SECOURS' NOT NULL,
PRIMARY KEY (TV_CODE)
);
# ------------------------------------
# data for table 'type_vehicule'
# ------------------------------------

INSERT INTO type_vehicule VALUES ('VSAV','Véhicule de secours aux blessés','3','SECOURS');
INSERT INTO type_vehicule VALUES ('FPTL','Fourgon pompe tonne léger','6','FEU');
INSERT INTO type_vehicule VALUES ('EPA','Echelle pivotante automatique','3','FEU');
INSERT INTO type_vehicule VALUES ('FPT','Fourgon pompe tonne','8','FEU');
INSERT INTO type_vehicule VALUES ('FPTLHR','Fourgon pompe tonne léger hors route','6','FEU');
INSERT INTO type_vehicule VALUES ('CTU','Camionnette tous usages','3','DIVERS');
INSERT INTO type_vehicule VALUES ('CCFL','Camion citerne Forêt léger','2','FEU');
INSERT INTO type_vehicule VALUES ('CCFM','Camion citerne Forêt moyen','4','FEU');
INSERT INTO type_vehicule VALUES ('CCFS','Camion citerne Forêt super','4','FEU');
INSERT INTO type_vehicule VALUES ('CCGC','Camion citerne grande capacité','3','FEU');
INSERT INTO type_vehicule VALUES ('VTU','Véhicule tous usages','2','DIVERS');
INSERT INTO type_vehicule VALUES ('VL','Véhicule léger','3','DIVERS');
INSERT INTO type_vehicule VALUES ('VLHR','Véhicule léger hors route','2','DIVERS');
INSERT INTO type_vehicule VALUES ('VSR','Véhicule de secours routier','3','SECOURS');
INSERT INTO type_vehicule VALUES ('VPS','Véhicule de premier secours','3','SECOURS');
INSERT INTO type_vehicule VALUES ('VPI','Véhicule polyvalent d\'intervention','3','DIVERS');
INSERT INTO type_vehicule VALUES ('ERS','Embarcation de Reconnaissance et de Sauvetage','3','SECOURS');
INSERT INTO type_vehicule VALUES ('GER','Groupe Electrogène Remorquable','0','DIVERS');
INSERT INTO type_vehicule VALUES ('PCM','Poste de Commandement Mobile','2','DIVERS');
INSERT INTO type_vehicule VALUES ('VLC','Véhicule Léger de Commandement','2','DIVERS');
INSERT INTO type_vehicule VALUES ('QUAD','Véhicule quad','1','DIVERS');
INSERT INTO type_vehicule VALUES ('VCYN','Véhicule Cynotechnique','1','DIVERS');
INSERT INTO type_vehicule VALUES ('VTI','Véhicule technique soutien intendance','2','LOGISTIQUE');
INSERT INTO type_vehicule VALUES ('VTH','Véhicule technique hébergement','2','LOGISTIQUE');
INSERT INTO type_vehicule VALUES ('VTD','Véhicule technique déblaiement','2','DIVERS');
INSERT INTO type_vehicule VALUES ('ASSU','Ambulance de secours et de soins d\'urgence','3','SECOURS');
INSERT INTO type_vehicule VALUES ('VTP','Véhicule de transport de personnel','9','DIVERS');
INSERT INTO type_vehicule VALUES ('REM','Remorque','0','DIVERS');
INSERT INTO type_vehicule VALUES ('MPS','Moto de premiers secours','1','SECOURS');
INSERT INTO type_vehicule VALUES ('MOTO','Motocyclette','1','DIVERS');
INSERT INTO type_vehicule VALUES ('VELO','Vélo tout terrain','1','DIVERS');


# ------------------------------------
# structure for table 'type_vehicule_role'
# ------------------------------------

DROP TABLE IF EXISTS type_vehicule_role ;
CREATE TABLE type_vehicule_role (
TV_CODE varchar(10) NOT NULL,
ROLE_ID tinyint(4) DEFAULT '0' NOT NULL,
ROLE_NAME varchar(25) NOT NULL,
PRIMARY KEY (TV_CODE, ROLE_ID)
);
# ------------------------------------
# data for table 'type_vehicule_role'
# ------------------------------------

INSERT INTO type_vehicule_role VALUES ('FPTLHR','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('FPTLHR','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('FPTLHR','3','chef BAT');
INSERT INTO type_vehicule_role VALUES ('FPTLHR','4','équipier BAT');
INSERT INTO type_vehicule_role VALUES ('FPTLHR','5','chef BAL');
INSERT INTO type_vehicule_role VALUES ('FPTLHR','6','équipier BAL');
INSERT INTO type_vehicule_role VALUES ('FPTL','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('FPTL','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('FPTL','3','chef BAT');
INSERT INTO type_vehicule_role VALUES ('FPTL','4','équipier BAT');
INSERT INTO type_vehicule_role VALUES ('FPTL','5','chef BAL');
INSERT INTO type_vehicule_role VALUES ('FPTL','6','équipier BAL');
INSERT INTO type_vehicule_role VALUES ('FPT','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('FPT','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('FPT','3','chef BAT');
INSERT INTO type_vehicule_role VALUES ('FPT','4','équipier BAT');
INSERT INTO type_vehicule_role VALUES ('FPT','5','chef BAL');
INSERT INTO type_vehicule_role VALUES ('FPT','6','équipier BAL');
INSERT INTO type_vehicule_role VALUES ('FPT','7','chef ATT');
INSERT INTO type_vehicule_role VALUES ('FPT','8','équipier ATT');
INSERT INTO type_vehicule_role VALUES ('EPA','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('EPA','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('EPA','3','équipier');
INSERT INTO type_vehicule_role VALUES ('VPI','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VPI','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('VPI','3','équipier');
INSERT INTO type_vehicule_role VALUES ('VL','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VL','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('CCFL','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('CCFL','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('CCGC','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('CCGC','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('VSAV','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VSAV','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('VSAV','3','équipier');
INSERT INTO type_vehicule_role VALUES ('VTU','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VTU','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('VTU','3','équipier');
INSERT INTO type_vehicule_role VALUES ('CCFM','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('CCFM','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('CCFM','3','équipier 1');
INSERT INTO type_vehicule_role VALUES ('CCFM','4','équipier 2');
INSERT INTO type_vehicule_role VALUES ('CCFS','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('CCFS','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('CCFS','3','équipier 1');
INSERT INTO type_vehicule_role VALUES ('CCFS','4','équipier 2');
INSERT INTO type_vehicule_role VALUES ('VLHR','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VLHR','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('VSR','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VSR','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('VSR','3','équipier');
INSERT INTO type_vehicule_role VALUES ('ERS','1','pilote');
INSERT INTO type_vehicule_role VALUES ('ERS','2','plongeur 1');
INSERT INTO type_vehicule_role VALUES ('ERS','3','plongeur 2');
INSERT INTO type_vehicule_role VALUES ('PCM','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('PCM','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('QUAD','1','conducteur');
INSERT INTO type_vehicule_role VALUES ('VCYN','1','conducteur');
INSERT INTO type_vehicule_role VALUES ('VTI','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VTI','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('VTH','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VTH','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('VTD','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VTD','2','conducteur');
INSERT INTO type_vehicule_role VALUES ('VLC','1','chef d\'agrès');
INSERT INTO type_vehicule_role VALUES ('VLC','2','conducteur');


# ------------------------------------
# structure for table 'vehicule'
# ------------------------------------

DROP TABLE IF EXISTS vehicule ;
CREATE TABLE vehicule (
V_ID int(11) DEFAULT '0' NOT NULL,
TV_CODE varchar(10) NOT NULL,
V_IMMATRICULATION varchar(15),
V_COMMENT varchar(60),
VP_ID varchar(5) DEFAULT 'OP' NOT NULL,
V_MODELE varchar(20),
V_KM int(11),
EQ_ID tinyint(4) DEFAULT '1' NOT NULL,
V_ANNEE year(4),
S_ID smallint(6) DEFAULT '4' NOT NULL,
V_ASS_DATE date,
V_CT_DATE date,
V_REV_DATE date,
V_EXTERNE tinyint(4),
V_INVENTAIRE varchar(40),
V_UPDATE_DATE date,
V_UPDATE_BY int(11),
V_INDICATIF varchar(20),
V_FLAG1 tinyint(4) DEFAULT '0' NOT NULL,
V_FLAG2 tinyint(4) DEFAULT '0' NOT NULL,
AFFECTED_TO int(11),
PRIMARY KEY (V_ID),
KEY S_ID (S_ID),
KEY AFFECTED_TO (AFFECTED_TO),
KEY VP_ID (VP_ID),
KEY V_ANNEE (V_ANNEE)
);
# ------------------------------------
# data for table 'vehicule'
# ------------------------------------



# ------------------------------------
# structure for table 'vehicule_position'
# ------------------------------------

DROP TABLE IF EXISTS vehicule_position ;
CREATE TABLE vehicule_position (
VP_ID varchar(5) NOT NULL,
VP_LIBELLE varchar(40) NOT NULL,
VP_OPERATIONNEL tinyint(4) DEFAULT '0' NOT NULL,
PRIMARY KEY (VP_ID)
);
# ------------------------------------
# data for table 'vehicule_position'
# ------------------------------------

INSERT INTO vehicule_position VALUES ('OP','opérationnel','3');
INSERT INTO vehicule_position VALUES ('REV','en révision','1');
INSERT INTO vehicule_position VALUES ('REP','en réparation','1');
INSERT INTO vehicule_position VALUES ('PRE','en prêt','1');
INSERT INTO vehicule_position VALUES ('CAR','plein de carburant','0');
INSERT INTO vehicule_position VALUES ('LIM','usage limité','2');
INSERT INTO vehicule_position VALUES ('HUI','niveau d\'huile','0');
INSERT INTO vehicule_position VALUES ('PNE','pression des pneumatiques','0');
INSERT INTO vehicule_position VALUES ('EAU','remplissage tonne','0');
INSERT INTO vehicule_position VALUES ('ARM','armement à compléter','0');
INSERT INTO vehicule_position VALUES ('IND','autre indisponibilité','1');
INSERT INTO vehicule_position VALUES ('PAN','en panne','1');
INSERT INTO vehicule_position VALUES ('REF','réformé','-1');
INSERT INTO vehicule_position VALUES ('VEN','vendu','-1');
INSERT INTO vehicule_position VALUES ('DET','détruit','-1');
