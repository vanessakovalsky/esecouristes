<?php
  # written by: Nicolas MARCHE <nico.marche@free.fr>
  # project: eBrigade
  # homepage: http://sourceforge.net/projects/ebrigade/
  # version: 2.5

  # Copyright (C) 2004, 2010 Nicolas MARCHE
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

//=====================================================================
// configurations spécifiques de l'application: 
// ces variables sont facultatives
//=====================================================================


// si la variable $extpage existe avec une valeur non nulle
// alors un bouton de redirection apparait sous le menu de gauche
// ceci permet d'ouvrir une connexion sur une application externe
// ou de déclencher des traitements de synchronisation des fiches personnel entre applications
 
//$extpage="external.php";
//$extserver="http://127.0.0.1/ebrigade";
//$extsecretkey="1e1f962ead4278a5d52bb8fcc7699918";

// nom personnalisé de l'application
// remplace le nom par défaut défini par la variable $application_title dans config.php
//$application_title_specific="e-protec";

// nom personnalisé de l'organisation
// apparait sur la page evenement_display_sub
//$organisation_name="Protection civile";

// activer une trame de frame supplémentaire en bandeau
// avec un menu personnalisable pour accéder à d'autres applications
//$linkframepage="link.php";

// avoir une page de redirection après déconnexion différente de la valeur configurée
// dans la base de données
//$deconnect_redirect="http://protection-civile.org";
?>