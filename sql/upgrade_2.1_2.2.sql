#====================================================;
#  Upgrade v2.2;
#
#====================================================;
  # written by: Nicolas MARCHE <nico.marche@free.fr>;
  # project: ebrigade;
  # homepage: http://sourceforge.net/projects/ebrigade/;
  # version: 2.2;
  # Copyright (C) 2004, 2007 Nicolas MARCHE;
  # This program is free software; you can redistribute it and/or modify;
  # it under the terms of the GNU General Public License as published by;
  # the Free Software Foundation; either version 2 of the License, or;
  # (at your option) any later version.;
  #
  # This program is distributed in the hope that it will be useful,;
  # but WITHOUT ANY WARRANTY; without even the implied warranty of;
  # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the;
  # GNU General Public License for more details.;
  # You should have received a copy of the GNU General Public License;
  # along with this program; if not, write to the Free Software;
  # Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA;
  

# ------------------------------------;
# configuration sms;
# ------------------------------------ ;
INSERT INTO configuration VALUES (9,'sms_provider','','fournisseur SMS');
INSERT INTO configuration VALUES (10,'sms_user','','utilisateur du compte SMS');
INSERT INTO configuration VALUES (11,'sms_password','','mot de passe du compte SMS');
INSERT INTO configuration VALUES (12,'sms_api_id','','api_id SMS (clickatell seulement)');

INSERT INTO fonctionnalite VALUES ('23','Envoyer des SMS','0');
INSERT INTO habilitation VALUES ('4','23');

# ------------------------------------;
# ajout table smslog;
# ------------------------------------; 

DROP TABLE IF EXISTS smslog ;
CREATE TABLE smslog (
P_ID int(11) DEFAULT '0' NOT NULL,
S_DATE datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
S_TEXTE varchar(200) NOT NULL,
S_NB int(11) NOT NULL default '0',
PRIMARY KEY  (P_ID,S_DATE)
);

# ------------------------------------;
# change version;
# ------------------------------------; 
update configuration set VALUE='2.2' where ID=1;

