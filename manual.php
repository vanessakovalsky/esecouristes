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
  
include_once ("config.php");
check_all(6);
$id=$_SESSION['id'];
if (isset ($_POST["month"])) $month=mysql_real_escape_string($_POST["month"]);
else $month=mysql_real_escape_string($_GET["month"]);
if (isset ($_POST["year"])) $year=mysql_real_escape_string($_POST["year"]);
else $year=mysql_real_escape_string($_GET["year"]);
if (isset ($_POST["day"])) $day=mysql_real_escape_string($_POST["day"]);
else $day=mysql_real_escape_string($_GET["day"]);
if (isset ($_POST["origine"])) $origine=mysql_real_escape_string($_POST["origine"]);
else $origine=mysql_real_escape_string($_GET["origine"]);
if (isset ($_POST["section"])) $section=mysql_real_escape_string($_POST["section"]);
else $section=mysql_real_escape_string($_GET["section"]);
if (isset ($_GET["equipe"])) $equipe=mysql_real_escape_string($_GET["equipe"]);
else $equipe=1;
if (isset ($_GET["period"])) $period=mysql_real_escape_string($_GET["period"]);
else $period="";
if (isset ($_GET["poste"])) $poste=mysql_real_escape_string($_GET["poste"]);
else $poste="";
if (isset ($_POST["change"])) $change=mysql_real_escape_string($_POST["change"]);
else $change="";
writehead();

?>

<STYLE type="text/css">
.SPP{color:red;font-size:10pt;}
.SPV{color:blue;font-size:10pt;}
.SPPV{color:purple;font-size:10pt;}
.TITLE {color:black;  font-weight: bold;font-size:10pt;}
</STYLE>

<script language="JavaScript">
function modify( month, year, day, poste, period, choix, section, origine,equipe) {
     url = "manual.php?info="+choix+"&month="+month+"&year="+year+"&day="+day+"&poste="+poste+"&period="+period+"&section="+section+"&origine="+origine+"&equipe="+equipe;
     self.location.href = url;
}

function redirect(url) {
	 self.location.href = url;
}
</script>

</head>

<?php
include_once ("config.php");
if ( $change == 'YES' ) {
	$queryz="select PS_ID from poste order by PS_ID";
	$resultz=mysql_query($queryz);
	while ($rowz=@mysql_fetch_array($resultz)) {
       $poste=$rowz["PS_ID"];
	   $query="delete from planning_garde 
			where PS_ID = $poste
			and PG_DATE='".$year."-".$month."-".$day."'";
	   $result=mysql_query($query);
	   $current=who_is_there($year, $month, $day, $poste, 'J');
	   if ( isset ($_POST["J_".$poste])) {
	   		$info= $_POST["J_".$poste];
	   		$info= explode('_',$info);
			$new=$info[0];
			$statut=$info[1];
			$query="insert into planning_garde 
			(PG_DATE, TYPE, PS_ID, EQ_ID, P_ID, PG_STATUT)
			values ( '".$year."-".$month."-".$day."', 'J', $poste ,
				".get_equipe($poste)." , $new, '".$statut."')";
			$result=mysql_query($query);
			
			$query="delete from planning_garde 
			where P_ID = $new
			and PG_DATE='".$year."-".$month."-".$day."'
			and type='J'
			and PS_ID <> $poste";
			$result=mysql_query($query);
	  }
	  $current=who_is_there($year, $month, $day, $poste, 'N');
	  if ( isset ($_POST["N_".$poste])) {
	   		$info= $_POST["N_".$poste];
	   		$info= explode('_',$info);
			$new=$info[0];
			$statut=$info[1];
			$query="insert into planning_garde 
			(PG_DATE, TYPE, PS_ID, EQ_ID, P_ID, PG_STATUT)
			values ( '".$year."-".$month."-".$day."', 'N', $poste ,
				".get_equipe($poste)." , $new, '".$statut."')";
			$result=mysql_query($query);
			$query="delete from planning_garde 
			where P_ID = $new
			and PG_DATE='".$year."-".$month."-".$day."'
			and type='N'
			and PS_ID <> $poste";
			$result=mysql_query($query);
	  }
	}
}

// calculs préliminaires
$d=nbjoursdumois($month, $year);
$moislettres=moislettres($month);
$jj=date("w", mktime(0, 0, 0, $month, $day,  $year));
if ( $jj == 0  or $jj == 6 ) {
      	 $daycolor="#FFFF99";
      	 $nightcolor="#FFCC33"; }
else {
      	 $daycolor="#FFFFFF";
      	 $nightcolor="$mylightcolor";
}

// y a t il des pros en sections
$query="select count(*) as 'NB'
        from pompier where P_STATUT='SPP'
	    and P_SECTION in (1,2,3)";
$result=mysql_query($query);
$row=mysql_fetch_array($result);
$NB=$row['NB'];
if ( $NB > 0 ) $withspp = 1;
else $withspp = 0;

if ( $change == 'YES') {
 if ( $origine == 'tableau' ) 
 	$url="tableau_garde.php?month=".$month."&year=".$year."&day=".$day."&equipe=".$equipe."&print=NO";
 else if ( $origine == 'gardejour' ) 
 	$url="garde_jour.php?month=".$month."&year=".$year."&day=".$day."&print=NO";
 	echo "<body onload=redirect(\"".$url."\")>";
}
else {
echo "<body>";
echo "<div align=center><font size=4><b>Composition de la garde pour ".date_fran($month, $day, $year)." $moislettres $year";
echo "<br></b></font><p>";

echo "<form id='manual' name='manual'  action='manual.php' method=post>";
// common parameters
echo "<input type='hidden' name='month' value='$month'>";
echo "<input type='hidden' name='year' value='$year'>";
echo "<input type='hidden' name='section' value='$section'>";
echo "<input type='hidden' name='day' value='$day'>";
echo "<input type='hidden' name='origine' value='$origine'>";
echo "<input type='hidden' name='change' value='YES'>";
// back button
if ( $origine == "tableau" ) {
	echo " <input type='button' value='retour' name='retour' onclick='redirect(\"tableau_garde.php?month=$month&year=$year&person=0&section=$section&print=NO&equipe=$equipe\")'>";
}
else {
        echo " <input type='button' value='retour' name='retour' onclick='redirect(\"garde_jour.php?month=$month&year=$year&day=$day&print=NO\")'>";
}	
// save button
echo " <input type='submit' value='sauver'>";



//======================================================
// POUR CHAQUE EQUIPE UN TABLEAU
//======================================================
$queryG="select distinct EQ_ID,EQ_JOUR, EQ_NUIT,EQ_NOM from equipe
		 where EQ_TYPE='GARDE'
         order by EQ_ID";
$resultG=mysql_query($queryG);

while ($rowG=@mysql_fetch_array($resultG)) {
      $EQ_ID=$rowG["EQ_ID"];
      $EQ_JOUR=$rowG["EQ_JOUR"];
      $EQ_NUIT=$rowG["EQ_NUIT"];
      $EQ_NOM=strip_tags($rowG["EQ_NOM"]);
      $sectionjour=get_section_pro_jour($EQ_ID,$year, $month, $day);

      $queryP="select PS_ID, TYPE, DESCRIPTION
         from poste
	  where EQ_ID=".$EQ_ID."
	  order by PS_ID";
      $resultP=mysql_query($queryP);
	  if ($nbsections == 3 ) $commentaire=" (section ".$sectionjour.")";
	  else $commentaire=" ";
      echo "<p><font size=4><b>".$EQ_NOM.$commentaire."</b></font>";
      echo "<p><table>
       <tr>
        <td class='FondMenu'>";
      echo "<table cellspacing=0 border=0>";

      // ===============================================
      // premiere ligne du tableau
      // ===============================================

      echo "<tr height=10 class=TabHeader>
      	   <td bgcolor=$mydarkcolor width=200>Poste</td>";
      if ( $EQ_JOUR == 1 ) {
         echo "<td bgcolor=$mydarkcolor width=120>Jour</font></td>";
      }
      if ( $EQ_NUIT == 1 ) {
	     echo "<td bgcolor=$mydarkcolor width=120>Nuit</td>";
      }
      echo "</tr>";

      
      // ===============================================
      // 1 ligne pour chaque poste
      // ===============================================
      while ($rowP=@mysql_fetch_array($resultP)) {
      	    $PS_ID=$rowP["PS_ID"];
      	    $TYPE=$rowP["TYPE"];
      	    $DESCRIPTION=strip_tags($rowP["DESCRIPTION"]);
      	    $queryF="select p.P_ID, p.P_NOM, pg.PG_STATUT from planning_garde pg, pompier p
                      where pg.PS_ID=".$PS_ID."
		      and pg.P_ID=p.P_ID
		      and pg.PG_DATE='".$year."-".$month."-".$day."'";
		      
      	    echo "<tr height=10>
      	   	 <td bgcolor=$daycolor width=200><font color=#0000CC>".$DESCRIPTION."</font></td>";
           if ( $EQ_JOUR == 1 ) {
      	          // le jour
	          $query2 =$queryF."\nand pg.TYPE = 'J'";
	          $result2=mysql_query($query2);
	      	  $affected=mysql_num_rows($result2);
   	      	  $P_ID1="";
      	      	  $P_NOM1="";
      	      	  $PG_STATUT="";
	      	  if ( $affected == 1 ) {
	      	     $row2=@mysql_fetch_array($result2);
     	 	     $P_ID1=$row2["P_ID"];
       	      	     $P_NOM1=$row2["P_NOM"];
       	      	     $PG_STATUT=$row2["PG_STATUT"];
	          }	
      	    	  $fname="J_".$PS_ID; 	
      	      	  echo "\n<td  bgcolor=$daycolor width=120>
	        	<select id='$fname' name='$fname'>";
	
	         if ($nbsections == 3 ){
	         	if ( $withspp == 1 ) {
      	     		display_subgroup('SPP','section',$daycolor,$PS_ID, $P_ID1, 
					   	$PG_STATUT,$year,$month,$day,$sectionjour,'D_JOUR');
	         		display_subgroup('SPP','autres' ,$daycolor,$PS_ID, $P_ID1, 
					 	$PG_STATUT,$year,$month,$day,$sectionjour,'D_JOUR');
      	     	}
				display_subgroup('SPV','section',$daycolor,$PS_ID, $P_ID1, 
					$PG_STATUT,$year,$month,$day,$sectionjour,'D_JOUR');
	      	 	display_subgroup('SPV','autres' ,$daycolor,$PS_ID, $P_ID1, 
				   	$PG_STATUT,$year,$month,$day,$sectionjour,'D_JOUR');
	      	 }
	      	 else display_subgroup('SPV','',$daycolor,$PS_ID, $P_ID1, 
			   	$PG_STATUT,$year,$month,$day,$sectionjour,'D_JOUR');
			 if ( $affected == 0) $selected="selected";
	      	 else $selected="";
	      	 echo "<option value='0_0' $selected>==== personne ===</option></select></td>";
            }  // if EQ_JOUR == 1
      	    if ( $EQ_NUIT == 1 ) {
      	          // la nuit
	          $query2 =$queryF."\nand pg.TYPE = 'N'";
	          $result2=mysql_query($query2);
	      	  $affected=mysql_num_rows($result2);
   	      	  $P_ID1="";
      	      	  $P_NOM1="";
      	      	  $PG_STATUT="";
	      	  if ( $affected == 1 ) {
	      	     $row2=@mysql_fetch_array($result2);
     	 	     $P_ID1=$row2["P_ID"];
       	      	     $P_NOM1=$row2["P_NOM"];
       	      	     $PG_STATUT=$row2["PG_STATUT"];
	          }	
      	    	  $fname="N_".$PS_ID; 	
      	      	  echo "\n<td  bgcolor=$nightcolor width=120>
	        	<select id='$fname' name='$fname'>";
	
	         if ($nbsections == 3 ){
	         	if ( $withspp == 1 ) {
	         		display_subgroup('SPP','section',$nightcolor,$PS_ID, $P_ID1, 
					 	$PG_STATUT,$year,$month,$day,$sectionjour,'D_NUIT');
	         		display_subgroup('SPP','autres' ,$nightcolor,$PS_ID, $P_ID1, 
					 	$PG_STATUT,$year,$month,$day,$sectionjour,'D_NUIT');
      	    	}
				display_subgroup('SPV','section',$nightcolor,$PS_ID, $P_ID1, 
					$PG_STATUT,$year,$month,$day,$sectionjour,'D_NUIT');
	      	 	display_subgroup('SPV','autres' ,$nightcolor,$PS_ID, $P_ID1, 
				   	$PG_STATUT,$year,$month,$day,$sectionjour,'D_NUIT');
	      	 }
	      	 else display_subgroup('SPV','',$nightcolor,$PS_ID, $P_ID1, 
			   	$PG_STATUT,$year,$month,$day,$sectionjour,'D_NUIT');
	      	 
	      	 if ( $affected == 0) $selected="selected";
	      	 else $selected="";
	      	 echo "<option value='0_0' $selected>==== personne ===</option></select></td>";
      	    }
      	    echo "</td>";
      	    echo "</tr>";
      }
      echo "</table>";
      echo "</td></tr></table>";
 }

// ===============================================
// le bas du tableau
// ===============================================

echo "</form></div></body>";
} 
?>
