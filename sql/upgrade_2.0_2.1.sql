#====================================================;
#  Upgrade v2.1;
#
#====================================================;
  # written by: Nicolas MARCHE <nico.marche@free.fr>;
  # project: ebrigade;
  # homepage: http://sourceforge.net/projects/ebrigade/;
  # version: 2.1;
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
# ajout événement divers;
# ------------------------------------; 
DELETE from type_evenement where TE_CODE='DIV';
INSERT INTO type_evenement VALUES ('DIV','Evénement divers');

DELETE from type_evenement where TE_CODE='INS';
INSERT INTO type_evenement VALUES ('INS','Instructeur pour une formation');

ALTER TABLE evenement ADD E_NB1 SMALLINT DEFAULT '0' NOT NULL;
ALTER TABLE evenement ADD E_NB2 SMALLINT DEFAULT '0' NOT NULL;

# ------------------------------------;
# change version;
# ------------------------------------; 
update configuration set VALUE='2.1' where ID=1;

