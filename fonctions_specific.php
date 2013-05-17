<?php
  # written by: Nicolas MARCHE <nico.marche@free.fr>
  # project: eBrigade
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

//=====================================================================
// fonctions spécifiques de l'application: 
// peuvent être modifiés par les administarteurs
//=====================================================================

// cette fonction est exécutée lors de la sauvegarde des compétences
function specific_post_update ($P_ID) {
 	 global $cisname;
 	 // this is specific FNPC. This function
 	 if ( $cisname == 'F.N.P.C.' ) {
 	  	// supprimer PSE1 si la personne est PSE2
 		$query="delete from qualification
		where P_ID=".$P_ID." 
		and PS_ID=(select PS_ID from poste where TYPE='PSE1')
		and P_ID in (select * from 
					  ( select q.P_ID from qualification q 
			  			where P_ID =".$P_ID."
			  			and q.PS_ID = (select PS_ID from poste where TYPE='PSE2')
			 		   )
			 		   tmp
			         )";
		$result=mysql_query($query);

		// supprimer PSC* si la personne est PSE*
		$query="delete from qualification
		where P_ID=".$P_ID." 
		and PS_ID=(select PS_ID from poste where TYPE like 'PSC%')
		and P_ID in (select * from 
					  ( select q.P_ID from qualification q 
			  			where P_ID =".$P_ID."
			  			and q.PS_ID = (select PS_ID from poste where TYPE like 'PSE%')
			 		   )
			 		   tmp
			         )";
		$result=mysql_query($query);
		
		// supprimer PAE1 (11) si la personne est PAE1 (14)
		$query="delete from qualification
		where P_ID=".$P_ID." 
		and PS_ID=(select PS_ID from poste where TYPE like 'PAE1%' and PS_ID = 11)
		and P_ID in (select * from 
					  ( select q.P_ID from qualification q 
			  			where P_ID =".$P_ID."
			  			and q.PS_ID = (select PS_ID from poste where TYPE like 'PAE1%' and PS_ID = 14)
			 		   )
			 		   tmp
			         )";
		$result=mysql_query($query);		
   }
}

// cette fonction est exécutée chaque jour lors de la première connexion au serveur 
function specific_maintenance () {
	 // this is specific FNPC
	 global $cisname;
	 if ( $cisname == 'F.N.P.C.' ) {
	 	$query="update pompier p, section s
	 	set p.P_ZIP_CODE = substring(s.S_CODE,1,2)
     	where ( p.P_ZIP_CODE = '' or  p.P_ZIP_CODE is null )
     	and p.P_SECTION=s.S_ID
	 	and s.S_CODE not like 'S%'
	 	and s.S_CODE not like 'R%'
	 	and s.S_ID <> 0";
	 	$result=mysql_query($query);
		
		$query="update section
	 	set S_ZIP_CODE = substring(S_CODE,1,2)
     	where ( S_ZIP_CODE = '' or  S_ZIP_CODE is null )
	 	and S_CODE not like 'S%'
	 	and S_CODE not like 'R%'
	 	and S_ID <> 0";
	 	$result=mysql_query($query);
	 }
}

// cette fonction est exécutée pour interdire les messages contenant certains mots
function specific_chat_cleanup () {
	$query="delete from chat where C_MSG like '%ant.virtuelle%' or C_MSG like '%apcv.users%' or C_MSG like '%proteccivilevirtuel%'";
	$result=mysql_query($query);
}

// cette fonction est exécutée lors de l'insertion d'une fiche personnel
// on ajoute automatiquement:
// - une compétence Cotisation qui exire le 1er jour du mois courant
// - une compétence L.A.T
function specific_insert ($P_ID) {
 	global $cisname;
	 // this is specific FNPC
	 if ( $cisname == 'F.N.P.C.' ) {
	  if (get_statut($P_ID) <> 'EXT' ) {
	  	$mydate=date("Y")."-".date("n")."-01";
	  	
	  	$query="insert into qualification (P_ID, PS_ID, Q_VAL, Q_EXPIRATION, Q_UPDATED_BY, Q_UPDATE_DATE)
	          select $P_ID,PS_ID,1,'".$mydate."',".$_SESSION['id'].",NOW()
	          from poste where DESCRIPTION='Cotisation'";
	  	$result=mysql_query($query);
	  
	  	$query="insert into qualification (P_ID, PS_ID, Q_VAL, Q_UPDATED_BY, Q_UPDATE_DATE)
	          select $P_ID,PS_ID,1,".$_SESSION['id'].",NOW()
	          from poste where TYPE='L.A.T'";
	  	$result=mysql_query($query);
	  }
	 }
}

// cette fonction est exécutée lors de la connexion a ebrigade
// a condition que la variable $extpage existe avec une valeur non nulle (dans config.php)
// ceci permet d'ouvrir une connexion sur une application externe
// ou de déclencher des traitements de synchronisation des fiches personnel entre applications

function external_open () {
	global $extserver,$extpage,$extsecretkey;
	
	$query="select p.P_CODE, p.P_NOM, p.P_PRENOM, p.P_PHONE, p.P_PHONE2, p.P_EMAIL, p.P_ADDRESS, 
				   p.P_CITY, p.P_ZIP_CODE, p.P_MDP, s.S_CODE
				from pompier p, section s
				where p.P_SECTION = s.S_ID
				and p.P_ID=".$_SESSION['id'];
	$result=mysql_query($query);
	$row=@mysql_fetch_array($result);
	$P_NOM=$row["P_NOM"];
	$P_PRENOM=$row["P_PRENOM"];
	$P_PHONE=$row["P_PHONE"];
	$P_PHONE2=$row["P_PHONE2"];
	$P_EMAIL=$row["P_EMAIL"];
	$P_ADDRESS=$row["P_ADDRESS"];
	$P_CITY=$row["P_CITY"];
	$P_ZIP_CODE=$row["P_ZIP_CODE"];
	$P_CODE=$row["P_CODE"];
	$P_MDP=$row["P_MDP"];
	$S_CODE=intval($row["S_CODE"]);
	if ( strlen($S_CODE) == 1 and $S_CODE > 0 ) $S_CODE="0".$S_CODE;
		
	$page = $extserver."/".$extpage;

	if ( isset($extpage))
	return "
  		<form name='f' method='post' action=".$page." target='_parent'>
    	<input id='lastname' 	name='lastname' 	type='hidden' 	value=\"".$P_NOM."\"'>
    	<input id='firstname' 	name='firstname' 	type='hidden' 	value=\"".$P_PRENOM."\">
    	<input id='mobile' 		name='mobile' 		type='hidden' 	value=\"".$P_PHONE."\">
    	<input id='phone' 		name='phone' 		type='hidden' 	value=\"".$P_PHONE2."\">
    	<input id='email' 		name='email' 		type='hidden' 	value=\"".$P_EMAIL."\">
    	<input id='address' 	name='address' 		type='hidden' 	value=\"".$P_ADDRESS."\">
    	<input id='city' 		name='city' 		type='hidden' 	value=\"".$P_CITY."\">
    	<input id='zipcode' 	name='zipcode' 		type='hidden' 	value=\"".$P_ZIP_CODE."\">
    	<input id='ident' 		name='ident' 		type='hidden' 	value=\"".$P_CODE."\">
    	<input id='password' 	name='password' 	type='hidden' 	value=\"".$P_MDP."\">
    	<input id='departement' name='departement' 	type='hidden' 	value=\"".$S_CODE."\">
    	<input id='secretkey' 	name='secretkey' 	type='hidden' 	value=\"".$extsecretkey."\">
  		</form>
  		";
}

?>