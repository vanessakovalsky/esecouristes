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
// Qui est à un poste donné un jour donné
//=====================================================================
function who_is_there($year, $month, $day, $poste, $period) {

	 $query="select P_ID from planning_garde 
	 		  where PS_ID=$poste
              and PG_DATE = '".$year."-".$month."-".$day."'
              and TYPE='".$period."'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);	  
	 return $row["P_ID"];

}
//=====================================================================
// est ce qu'un pompier donné est absent un jour donné ?
//=====================================================================

function is_out($P_ID, $year, $month, $day) {
	 // absence enregistrée ?
	 $query="select count(*) as NB from indisponibilite where P_ID =".$P_ID."
                 and I_DEBUT <= '".$year."-".$month."-".$day."'
		 and I_FIN >= '".$year."-".$month."-".$day."'
		 and I_STATUS in ('ATT','VAL')";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];
}

//=====================================================================
// est ce qu'un pompier donné est de garde un jour donné ?
//=====================================================================
function is_de_garde($P_ID,$year, $month, $day, $type) {
	 $query="select count(*) as NB from planning_garde where P_ID =".$P_ID."
                 and PG_DATE= '".$year."-".$month."-".$day."'
		 and TYPE='".$type."'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];

}

//=====================================================================
// combien de gardes pour un agent sur une période donnée ?
//=====================================================================
function get_nb_gardes($P_ID,$year1, $month1, $day1, $year2, $month2, $day2) {
	 $query="select count(*) as NB from planning_garde where P_ID =".$P_ID."
                 and PG_DATE >= '".$year1."-".$month1."-".$day1."'
                 and PG_DATE <= '".$year2."-".$month2."-".$day2."'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 return $row["NB"];

}


//=====================================================================
// a quel poste un pompier donné est-il de garde un jour donné ?
//=====================================================================
function which_poste($P_ID,$year, $month, $day, $type, $equipe) {
	 $query="select pg.PS_ID, po.TYPE
	 		 from planning_garde pg, poste po
			 where po.PS_ID=pg.PS_ID
			 and pg.P_ID =$P_ID
			 and pg.EQ_ID=$equipe
             and pg.PG_DATE= '".$year."-".$month."-".$day."'
		     and pg.TYPE='".$type."'";
	 $result=mysql_query($query);
	 if ( mysql_num_rows($result) ==0 ) return "NULL";
	 else {
	 	$row=mysql_fetch_array($result);
		return $row["TYPE"];
	}
}


//=====================================================================
// affiche le personnel disponible qui n'est pas de garde pour la période J, N ou A (J+N)
//=====================================================================
function personnel_reserve($year, $month, $day, $type) {
	global $nbsections;
         $query="select p.P_ID, p.P_NOM, p.P_SECTION, p.P_ABBREGE from pompier p, disponibilite d
		 where p.P_ID=d.P_ID
		 and d.D_DATE='".$year."-".$month."-".$day."'";
         if ( $type == 'J') $query =$query." and d.D_JOUR=1 and d.D_NUIT=0";
		 else if ( $type == 'N') $query =$query." and d.D_NUIT=1 and d.D_JOUR=0";
		 else if ( $type == 'A') $query =$query." and d.D_JOUR=1 and d.D_NUIT=1";
		 $query =$query."\norder by p.P_NOM";
	 $result=mysql_query($query);	
	 
	 while ($row=@mysql_fetch_array($result)) {
	       $P_NOM=$row["P_NOM"];
	       $P_ID=$row["P_ID"];
	       $P_SECTION=$row["P_SECTION"];
	       $P_ABBREGE=$row["P_ABBREGE"];
	       if ( $type == 'A') $test = is_de_garde($P_ID, $year, $month, $day, 'J') + is_de_garde($P_ID, $year, $month, $day, 'N');
	       else $test = is_de_garde($P_ID, $year, $month, $day, $type);
	       if ( $test == 0 ) {
	       	  if ( is_out ($P_ID, $year, $month, $day) == 0 ) {
	       	  	 if ($nbsections == 3 ) $commentaire=" (S".$P_SECTION.")";
				 else $commentaire="";
	       	     echo strtoupper($P_NOM).$commentaire." <i>".$P_ABBREGE."</i><br>";
     	          }
   	       }
	 }
}

//=====================================================================
// est ce qu'un SPP donné devrait travailler un jour donné ?
//=====================================================================

function should_be_working($P_ID, $year, $month, $day) {
 	 $numsemaine=date("W",mktime( 0,0,0,$month,$day,$year));
	 $jour=date("w", mktime(0, 0, 0, $month, $day,  $year)); //0 = dimanche
	 $query="select S_ID, P_STATUT from section, pompier  where P_SECTION=S_ID and P_ID=".$P_ID;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 $section=$row["S_ID"];
	 $statut=$row["P_STATUT"];	
	 if ( $statut == 'SPP' ) {
	 		 $query2="select EQ_ID from equipe where EQ_JOUR = 1 or EQ_NUIT=1)";
	 		 $result2=mysql_query($query2);
	 		 $test=0;
	 		 while ($row2=@mysql_fetch_array($result2)) {
	       	 	$EQ_ID=$row2["EQ_ID"];
	     	 	if ( get_section_pro_jour($EQ_ID,$year, $month, $day) == $section) $test=1;
			 }	  
			 return $test;
	 }
	 else return 0;
}

//=====================================================================
// quelle est la section pour un jour donné ?
//=====================================================================

function get_section_pro_jour($equipe,$year, $month, $day) {
	 global $nbsections;
	 
	 $query="select S_ID, DATE_FORMAT(S_ID_DATE, '%d-%c-%Y') as S_ID_DATE
	 		 from equipe where EQ_ID='".$equipe."'";
	 		 
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 $S_ID=$row["S_ID"];
	 $S_ID_DATE=$row["S_ID_DATE"];
	 
	 if (($nbsections <> 3 ) or ( $S_ID == '0' )) return 0;
	 else {
	 	$section = 1;
	 	$num = my_date_diff($S_ID_DATE, $day."-".$month."-".$year);
	 	if ( $num >= 0 ) {
	 		if ( $num % 3  == 0) $section = $S_ID;
 	 		if ( $num % 3  == 1) $section = ($S_ID + 1 ) % 3;
 	 		if ( $num % 3  == 2) $section = ($S_ID + 2 ) % 3; 
 	 		if ( $section == 0 ) $section = 3;

	 	}
	 	else {
	 		$num = my_date_diff($day."-".$month."-".$year , $S_ID_DATE);
	 		if ( $num % 3  == 0) $section = $S_ID;
 	 		if ( $num % 3  == 1) $section = ($S_ID + 2 ) % 3;
 	 		if ( $num % 3  == 2) $section = ($S_ID + 1 ) % 3; 
 	 		if ( $section == 0 ) $section = 3;
	 	}
	 	return $section;
	 }	
}
//=====================================================================
// remplir 1 case du tableau de garde
//=====================================================================

function fill_1_poste_garde ($equipe, $year, $month, $day, $period, $poste,$statut) {
	 
	 // insertion dans le planning si la case est vide
	 $query="select count(1) as NB from planning_garde where PS_ID=".$poste."
		 and PG_DATE='".$year."-".$month."-".$day."'
		 and  TYPE='".$period."'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 $NB1=$row["NB"];
	 
	 // insertion dans le planning si il existe une personne qui convient
	 $query="select count(1) as NB from priorite where PS_ID=".$poste;
	 $result=mysql_query($query);
	 $row=mysql_fetch_array($result);
	 $NB2=$row["NB"];
	 
         if (( $NB1 == 0 ) && ( $NB2 <> 0 )) {
             // choix de la plus haute priorité
             $query="select max(SCORE) as VALUE from priorite where PS_ID=".$poste;
             $result=mysql_query($query);	
             $row=mysql_fetch_array($result);
             $value=$row["VALUE"];
	
	     $query="select P_ID from priorite where PS_ID=".$poste." and SCORE=".$value;
             $result=mysql_query($query);	
             $row=mysql_fetch_array($result);
             $P_ID=$row["P_ID"];
	     $query="insert planning_garde (EQ_ID, PG_DATE, TYPE, PS_ID, P_ID,PG_STATUT)
		     select '".$equipe."','".$year."-".$month."-".$day."','".$period."',".$poste.", ".$P_ID.",'".$statut."'";
	     $result=mysql_query($query);
	     // la personne ne doit plus apparaître dans la liste des priorités
             del_1_priorite($P_ID,'P_ID');
       	 // le poste est affecté
             del_1_priorite($poste,'PS_ID');
	 }

}

//=====================================================================
// remplir 1 ligne du tableau de garde
//=====================================================================
function fill_1_garde ($equipe,$year, $month, $day, $period,$statut) {
	 //echo "<br>Remplissage garde $period du $day avec $statut<br>";
	 $query="select distinct PS_ID from poste where EQ_ID=".$equipe;
 	 $result=mysql_query($query);	
 	 
 	 if ( $statut == 'SPV') {
	    while ($row=mysql_fetch_array($result)) {
       	       $PS_ID=$row["PS_ID"];
       	       fill_1_poste_garde($equipe,$year, $month, $day, $period, $PS_ID,$statut);
            }
	 }
	 else {
	     if ( $period == 'J') {
	         // pour chaque SPP on affecte le poste le plus prioritaire
		 $query2="select distinct po.P_ID from priorite pr, pompier po where pr.P_ID=po.P_ID and po.P_STATUT='SPP'
		          order by pr.SCORE desc";
	     	 $result2=mysql_query($query2);
	         while ( $row2=mysql_fetch_array($result2)) {
       	      	       $P_ID=$row2["P_ID"];
       	      	       // choix de la plus haute priorité
             	       $query="select max(SCORE) as VALUE from priorite where P_ID=".$P_ID;
             	       $result=mysql_query($query);	
             	       $row=mysql_fetch_array($result);
             	       $value=$row["VALUE"];

	     	       $query="select PS_ID from priorite where P_ID=".$P_ID." and SCORE=".$value;
             	       $result=mysql_query($query);	
             	       $row=mysql_fetch_array($result);
             	       $PS_ID=$row["PS_ID"];
    		       // insertion
    	               $query="insert planning_garde (EQ_ID, PG_DATE, TYPE, PS_ID, P_ID,PG_STATUT)
		       select ".$equipe.",'".$year."-".$month."-".$day."','".$period."',".$PS_ID.", ".$P_ID.",'SPP'";
 	     	       $result=mysql_query($query);
	               // la personne ne doit plus apparaître dans la liste des priorités
                       del_1_priorite($P_ID, 'P_ID');
		       // le poste ne doit plus apparaître dans la liste des priorités
		       del_1_priorite($PS_ID,'PS_ID');
	         }
	     }
	     else  {  // period= 'N'
	     	 // cas spécifique des SPP pour la nuit : on remet pareil
	     	 $query="insert planning_garde (PG_DATE, TYPE, PS_ID, P_ID,PG_STATUT)
  	         		select '".$year."-".$month."-".$day."','N',PS_ID, P_ID,'SPP'
                 		from  planning_garde
		 		where PG_DATE='".$year."-".$month."-".$day."'
		 		and TYPE='J'
		 		and PG_STATUT='SPP'";
 	     	 $result=mysql_query($query);
 	     	 // sauf si le poste est inactif
 	     	 $query2="select distinct PS_ID from poste where TYPE='".$period."' and PO_NUIT=0";
	 		 $result2=mysql_query($query2);	
				 while ($row2=@mysql_fetch_array($result2)) {
	 				$query= "delete from planning_garde where PS_ID=".$row2["PS_ID"]." 
					 and PG_DATE='".$year."-".$month."-".$day."' and TYPE='".$period."'" ;
	 				$result=mysql_query($query);
	 			}
 	     }
	 }
}


//=====================================================================
// remplir la table de travail priorite pour SPP pour une date donnée
//=====================================================================
function fill_priorite_spp ($equipe,$year, $month, $day, $period) {
	 global $textcolor,$nb1,$nb2;
	 $sectionjour=get_section_pro_jour($equipe,$year, $month, $day);
	 //echo "<br>Calcul des priorités SPP pour le $day, section ".$sectionjour." de garde<br>";
	 if ( $period == 'J') $mycol='PO_JOUR';
	 if ( $period == 'N') $mycol='PO_NUIT';
	 // ------------------------------
	 // suppression des indisponibles
	 // ------------------------------
	 $query2="delete from priorite";
	 $result2=mysql_query($query2);
	
	 // ------------------------------
	 //insertion des SPP
	 // ------------------------------
	 $query= "insert into priorite (P_ID, PS_ID, SCORE)
                  select distinct p.P_ID, q.PS_ID, 0
	 	  from pompier p, qualification q, poste c
	 	  where p.P_SECTION=".$sectionjour."
	 	  and p.P_OLD_MEMBER = 0
	 	  and c.PS_ID=q.PS_ID
	 	  and c.EQ_ID=".$equipe."
	 	  and p.P_STATUT = 'SPP'
	 	  and p.P_ID=q.P_ID
		  and ".$mycol."=1";
	 $result=mysql_query($query);
	 
	 // ------------------------------
	 // purge de la table
 	 // ------------------------------
	 $query="delete from priorite
	 	 where P_ID in (select P_ID from indisponibilite
		 				where I_DEBUT <= '".$year."-".$month."-".$day."'
		 				and I_FIN >= '".$year."-".$month."-".$day."')";
	 $result=mysql_query($query);
	 
	 // ------------------------------
	 // pompiers déjà affectés
 	 // ------------------------------
	 $query="delete from priorite
	 	 where P_ID in (select P_ID from planning_garde
		 				where PG_DATE = '".$year."-".$month."-".$day."'
		 				and TYPE ='".$period."'";
	 $result=mysql_query($query);
	 
	 // ------------------------------
	 // scoring
	 // ------------------------------
	 $query="select distinct P_ID from priorite";
	 $result=mysql_query($query);
	 while ($row=@mysql_fetch_array($result)) {
       	       $P_ID=$row["P_ID"];
	       //mise à jour des scores par rapport à la date de dernière garde au poste indiqué
	       $query2="select distinct p.PS_ID, q.Q_VAL from priorite p, qualification q
	                where p.P_ID=".$P_ID."
			and p.P_ID=q.P_ID
			and p.PS_ID=q.PS_ID";
	       $result2=mysql_query($query2);
	       while ($row2=@mysql_fetch_array($result2)) {
       	       	        $PS_ID=$row2["PS_ID"];
       			$Q_VAL=$row2["Q_VAL"];
       			if ( $Q_VAL == 1 ) {
	       	           $query3="select DATE_FORMAT(max(PG_DATE), '%d-%m-%Y') as DATE1
   	           		    from planning_garde
       	                	    where P_ID=".$P_ID."
       	                	    and PS_ID=".$PS_ID."
       	                	    and PG_DATE < '".$year."-".$month."-".$day."'";
               		   $result3=mysql_query($query3);
	       		   $row3=mysql_fetch_array($result3);
	       		   $DATE1=$row3["DATE1"];
	       		   if ($DATE1 == "" ) $SCORE=10; else $SCORE=my_date_diff($DATE1, $day."-".$month."-".$year);
	
	     		   if ( $SCORE >= 10 ) $SCORE = 10;
       	       		         $query3="update priorite set SCORE=SCORE+".$SCORE."
               		         where P_ID=".$P_ID." and PS_ID=".$PS_ID;
                                 $result3=mysql_query($query3);
		        }
		        else {
			   $query3="update priorite
     		       	            set SCORE=1
                       	   	    where P_ID=".$P_ID."
		       	   	    and PS_ID=".$PS_ID;
		       	   $result3=mysql_query($query3);
			    }
	       }
	 }
}
//=====================================================================
// remplir la table de travail priorite pour SPV pour une date donnée
//=====================================================================

function fill_priorite_spv ($equipe,$year, $month, $day, $period) {

	 //echo "<br>Calcul des priorités SPV pour $period du $day<br>";
	 if ( $period == 'J') $mycol='PO_JOUR';
	 if ( $period == 'N') $mycol='PO_NUIT';
	 $sectionjour=get_section_pro_jour($equipe,$year, $month, $day);
	 // ------------------------------
	 // purge de la table
 	 // ------------------------------
	 $query="delete from priorite";
	 $result=mysql_query($query);
	
 	 // ------------------------------
	 //insertion
	 // ------------------------------
	 if ( $period == 'J' ) $P_COL="D_JOUR" ; else $P_COL="D_NUIT";
	 $query= "insert into priorite (P_ID, PS_ID, SCORE)
                  select d.P_ID, q.PS_ID, 0
	 	  from disponibilite d, qualification q, pompier p, poste po
	 	  where d.P_ID=q.P_ID
	 	  and p.P_ID=d.P_ID
	 	  and q.PS_ID=po.PS_ID
	 	  and p.P_OLD_MEMBER = 0
	 	  and po.EQ_ID=$equipe
	 	  and d.".$P_COL."=1
	 	  and p.P_SECTION in (".$sectionjour.",0)
		  and D_DATE= '".$year."-".$month."-".$day."'
		  and D_STATUT='SPV'
		  and ".$mycol."=1";
	 $result=mysql_query($query);
	 
	 // ---------------------------------------------
	 // suppression priorités si garde déjà attribuée
	 // ---------------------------------------------
   	 $query="delete from priorite 
		  	 where PS_ID in ( select PS_ID from planning_garde
   		 			where PG_DATE='".$year."-".$month."-".$day."'
   		 			and TYPE = '".$period."')";
	 $result=mysql_query($query);
	 
	 // ---------------------------------------------
	 // suppression priorités personnes dèjà de garde
	 // ---------------------------------------------
   	 $query="delete from priorite 
		  	 where P_ID in ( select P_ID from planning_garde
   		 			where PG_DATE='".$year."-".$month."-".$day."'
   		 			and TYPE = '".$period."')";
	 $result=mysql_query($query);
	 // ------------------------------
	 // delete si indisponible
	 // ------------------------------
	 $query="delete priorite from priorite p, indisponibilite i
		 where i.P_ID=p.P_ID
		 and i.I_DEBUT <= '".$year."-".$month."-".$day."'
		 and i.I_FIN >= '".$year."-".$month."-".$day."'";
	 $result=mysql_query($query);
	 // ------------------------------
	 // update selon plusieurs règles
 	 // ------------------------------
	 $query="select distinct p.P_ID, p.P_SECTION from priorite r, pompier p where p.P_ID=r.P_ID";
	 $result=mysql_query($query);
	 while ($row=@mysql_fetch_array($result)) {
       	       $P_ID=$row["P_ID"];
       	       $P_SECTION=$row["P_SECTION"];
	       $NB=0;
	       //mise à jour des scores par rapport à la somme des dispos du mois (sauf pour les section 4)
       	       $query2="select  sum(D_JOUR)+sum(D_NUIT) as SCORE
                        from disponibilite
       			where P_ID=".$P_ID."
       			and MONTH(D_DATE)=".$month."
       			and YEAR(D_DATE)=".$year."
       			and D_STATUT='SPV'";
               $result2=mysql_query($query2);
	       $row2=mysql_fetch_array($result2);
	       $SCORE=$row2["SCORE"];
       
       	       $query2="update priorite set SCORE=".$SCORE."
                        where P_ID=".$P_ID;
               $result2=mysql_query($query2);
               
	       //mise à jour des scores par rapport au nombre de gardes attribuées ce mois
       	       $query2="select count(*) as SCORE from planning_garde
       			where P_ID=".$P_ID."
       			and MONTH(PG_DATE)=".$month."
       			and YEAR(PG_DATE)=".$year."
       			and PG_STATUT='SPV'
	       		and EQ_ID=".$equipe;
               $result2=mysql_query($query2);
	       $row2=mysql_fetch_array($result2);
	       $SCORE=$row2["SCORE"];
	       
	       // suppression des pros déjà affectés + de 48h en SPPV
	       if (get_statut($P_ID) <> 'SPV' ) {
	       	  if ( $SCORE >= 4 ) {
	       	     $query2="delete from priorite where P_ID=".$P_ID;
                     $result2=mysql_query($query2);
        	  }
	       }
       	       else {
	       	    $SCORE= $SCORE * 12;
       	       	    $query2="update priorite set SCORE =SCORE -".$SCORE."
                        where P_ID=".$P_ID;
                    $result2=mysql_query($query2);
	       }

               //mise à jour des scores par rapport à la date de dernière garde au poste indiqué
	       $query2="select distinct p.PS_ID, q.Q_VAL from priorite p, qualification q
	                where p.P_ID=".$P_ID."
			and p.P_ID=q.P_ID
			and p.PS_ID=q.PS_ID";
	       $result2=mysql_query($query2);
	       while ($row2=@mysql_fetch_array($result2)) {
       	       	        $PS_ID=$row2["PS_ID"];
       			$Q_VAL=$row2["Q_VAL"];
       			if ( $Q_VAL == 1 ) {
	       	           $query3="select DATE_FORMAT(max(PG_DATE), '%d-%m-%Y') as DATE1
   	           		    from planning_garde
       	                	    where P_ID=".$P_ID."
       	                	    and PS_ID=".$PS_ID."
       	                	    and PG_DATE < '".$year."-".$month."-".$day."'";
               		    $result3=mysql_query($query3);
	       		    $row3=mysql_fetch_array($result3);
	       		    $DATE1=$row3["DATE1"];
	       		    if ($DATE1 == "" ) $SCORE=10; else $SCORE=my_date_diff($DATE1, $day."-".$month."-".$year);
	
	     		    if ( $SCORE >= 10 ) $SCORE = 10;
       	       		         $query3="update priorite set SCORE=SCORE+".$SCORE."
    		 	 		  where P_ID=".$P_ID." and PS_ID=".$PS_ID;
                                 $result3=mysql_query($query3);
		        }
		        else {
		             $query3="update priorite
     		       	            set SCORE=1
                       	   	    where P_ID=".$P_ID."
		       	   	    and PS_ID=".$PS_ID;
		       	     $result3=mysql_query($query3);
    	      		}
		        
	      }
	     //mise à jour des scores par rapport à la date de dernière garde
       	       $query2="select DATE_FORMAT(max(PG_DATE), '%d-%m-%Y') as DATE1
	                from planning_garde
       	                where P_ID=".$P_ID."
       	                and PG_STATUT='SPV'
       	                and EQ_ID=".$equipe."
       	                and PG_DATE <= '".$year."-".$month."-".$day."'";
               $result2=mysql_query($query2);
	       $row2=mysql_fetch_array($result2);
	       $DATE1=$row2["DATE1"];
	      
	       if ($DATE1 == "" ) $SCORE=10;
	       else $SCORE=my_date_diff($DATE1, $day."-".$month."-".$year);
	       if ( $SCORE >= 10 ) $SCORE=10;
	
	       // si dernière garde il y a moins de 3 jours on réduit la priorité
	       if ( $SCORE == 1 )  $SCORE= -100 ;
	       if ( $SCORE == 2 )  $SCORE= -20 ;
	       if ( $SCORE == 3 )  $SCORE= -10 ;
	       // sinon on l'augmente
	       if ( $SCORE >=5 ) $SCORE= $SCORE * 2;
               
               $query2="update priorite set SCORE=SCORE +".$SCORE."
                       where P_ID=".$P_ID;
               $result2=mysql_query($query2);
               
	          // de préférence on remet de nuit la personne affectée le jour
	          if ( $period == 'N') {
		         $query2="select PS_ID
		              from planning_garde pg
			      where P_ID=".$P_ID."
			      and PG_DATE='".$year."-".$month."-".$day."'
			      and TYPE = 'J'";
	             $result2=mysql_query($query2);
	             $row2=mysql_fetch_array($result2);
	             $MYPS_ID=$row2["PS_ID"];
	             if ( $MYPS_ID > 1 ) {
	  		        if ( is_we($month,$day ,$year) >= 1 )
	  		        	$query2="update priorite set SCORE=SCORE+100
                                 where P_ID=".$P_ID." and PS_ID=".$MYPS_ID;
					else
	             	    $query2="update priorite set SCORE=SCORE+50
                                 where P_ID=".$P_ID." and PS_ID=".$MYPS_ID;
	             	$result2=mysql_query($query2);
             	 }
	          }
	    //}
	 }
}

//=====================================================================
// delete 1 case du tableau de garde
//=====================================================================

function del_1_poste_garde ($year, $month, $day, $period, $poste) {
	 $query="delete from planning_garde
	 where PS_ID=".$poste."
	 and PG_DATE='".$year."-".$month."-".$day."'
	 and TYPE ='".$period."'";
	 $result=mysql_query($query);
}

//=====================================================================
// delete 1 mois du tableau de garde
//=====================================================================

function del_1_mois_garde ($year, $month, $equipe) {
	 if ( $month < 12 ) {
     	      $nextmonth= $month + 1; $nextyear = $year;
         }
	 else {
     	      $nextmonth= "01"; $nextyear = $year +1;
         }
	 $query="delete from planning_garde where PG_DATE < '".$nextyear."-".$nextmonth."-01'
	         and PG_DATE >= '".$year."-".$month."-01'
		 and EQ_ID=".$equipe;
	 $result=mysql_query($query);

}

//=====================================================================
// delete 1 personne de la table priorite ( sous fonction)
//=====================================================================

function del_1_priorite ($id, $type) {
	 $query="delete from priorite where ".$type."=".$id;
	 $result=mysql_query($query);	
}

//=====================================================================
// diplay priorite for debug
//=====================================================================
function debug_display() {
echo "<br>-----------------------------------------------------<br>";
       $query="select PS_ID, P_NOM, SCORE
	from priorite, pompier
	where pompier.P_ID=priorite.P_ID
	order by PS_ID asc, SCORE desc";
	$result=mysql_query($query);

	while ($row=mysql_fetch_array($result)) {
       	      $P_NOM=$row["P_NOM"];
	      $PS_ID=$row["PS_ID"];
 	      $SCORE=$row["SCORE"];
  	
   	      echo "--- $PS_ID $P_NOM : $SCORE <br>";
        }
echo "<br>-----------------------------------------------------<br>";
}

//=====================================================================
// manual tableau de garde : display subgroup
//=====================================================================

function display_subgroup($status,$comment,$background,$poste, $pompier, $pg_status, $year,$month,$day,$sectionjour,$type){
    //$type = D_JOUR ou D_NUIT
    //$comment= section ou autres
    //$status=SPP ou SPV
	global $nbsections;
	if ( $nbsections == 3 ) echo "\n<OPTGROUP LABEL=\"$status $comment\" style=\"background-color:$background\">";
	else echo "\n<OPTGROUP LABEL=\"personnel disponible\" style=\"background-color:$background\">";
	if ($status =='SPP') {
	         $query="select distinct p.P_ID, p.P_NOM, p.P_STATUT
	         from pompier p, qualification q
		     where p.P_STATUT ='SPP'
		     and p.P_OLD_MEMBER = 0 
		     and q.P_ID=p.P_ID
		     and q.PS_ID=".$poste;
	    	 if ($comment== 'section') { $query=$query." and p.P_SECTION=".$sectionjour;}
	    	 else { $query=$query." and p.P_SECTION <>".$sectionjour;}
	}
	else {
	$query="select distinct p.P_ID, p.P_NOM, d.D_STATUT as P_STATUT
	         from pompier p, disponibilite d, qualification q
		     where q.P_ID=p.P_ID
		     and p.P_OLD_MEMBER = 0
		     and q.PS_ID=".$poste."
			 and d.".$type."='1'
	 		 and d.P_ID=p.P_ID
		     and d.D_STATUT='SPV'
		     and d.D_DATE='".$year."-".$month."-".$day."'";
		     if ( $nbsections == 3 ){
			 	if ($comment== 'section')  $query=$query." and p.P_SECTION=".$sectionjour;
	    	 	else $query=$query." and p.P_SECTION <>".$sectionjour;
	    	}
	}
	$query=$query." order by p.P_NOM";
 	$result=mysql_query($query);
	while ($row=@mysql_fetch_array($result) ) {
	        $P_ID=$row["P_ID"];
       	    $P_NOM=$row["P_NOM"];
       	    $D_STATUT=$row["P_STATUT"];	
       	    if ( is_out($P_ID, $year, $month, $day) == 0 ) {
       	      	if (( $P_ID == $pompier) and ( $pg_status == $status)) {
					 $selected="selected";
				}
       	      	else $selected="";
		       	echo "<option value='".$P_ID."_".$D_STATUT."' class=\"".$D_STATUT."\" 
				   style=\"background-color:$background\" $selected>$P_NOM</option>";
		    }
		    // cas des SPP qui prennent des gardes SPPV pendant leur vacances
		    else if (( get_statut($P_ID) == 'SPP') and ($D_STATUT = 'SPV' ) and ( $status == 'SPV')) {
		     	if (( $P_ID == $pompier) and ( $pg_status == $status)) {
					 $selected="selected";
				}
       	      	else $selected="";
		       	echo "<option value='".$P_ID."_SPV' class=\"SPPV\" 
				   style=\"background-color:$background\" $selected>$P_NOM</option>";
		    }
	}
	// afficher aussi la personne de garde si elle n'est plus disponible
	if (( $status == 'SPV') and ( $pompier <> "" ) and (get_statut($pompier) == 'SPV' )){
		$query="select count(*) as NB from disponibilite
				    where P_ID=".$pompier."
				    and D_DATE='".$year."-".$month."-".$day."'
			 		and ".$type."='1'";
		$result=mysql_query($query);
		$row=@mysql_fetch_array($result);
		$NB=$row["NB"];
		if ( $NB == 0 ) {
			if (( $nbsections == 3 ) and (get_section($pompier) == $sectionjour) and ($comment== 'section')) {
			 	echo "<option value='".$pompier."_SPV' class=\"SPV\" style=\"background-color:$background\" selected>".get_nom($pompier)."</option>";
			} 	
			else if  (( $nbsections == 3 ) and (get_section($pompier) <> $sectionjour) and ($comment== 'autres')) {
			 	echo "<option value='".$pompier."_SPV' class=\"SPV\" style=\"background-color:$background\" selected>".get_nom($pompier)."</option>";
			}
			else if  ( $nbsections == 1 ){
				echo "<option value='".$pompier."_SPV' class=\"SPV\" style=\"background-color:$background\" selected>".get_nom($pompier)."</option>";
			}
		}
	}
}

?>
