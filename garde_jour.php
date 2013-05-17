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
check_all(0);
writehead();

?>
<script>
function redirect(url) {
	 self.location.href = url;
}
function fillmenu(frm, menu1,menu2,menu3) {
year=frm.menu1.options[frm.menu1.selectedIndex].value;
month=frm.menu2.options[frm.menu2.selectedIndex].value;
day=frm.menu3.options[frm.menu3.selectedIndex].value;
url = "garde_jour.php?month="+month+"&year="+year+"&day="+day+"&print=NO";;
self.location.href = url;
}
</script>
<?php

if (isset ($_GET["month"])) $month=mysql_real_escape_string($_GET["month"]);
else $month=date("n");
if (isset ( $_GET["year"])) $year=mysql_real_escape_string($_GET["year"]);
else $year=date("Y");
if (isset ($_GET["day"])) $day=mysql_real_escape_string($_GET["day"]);
else $day=date("d");
if (isset ($_GET["print"])) $print=mysql_real_escape_string($_GET["print"]);
else $print="NO";

if (isset ($_GET["P1"])) $P1=mysql_real_escape_string($_GET["P1"]);
else $P1=1;
if (isset ($_GET["P2"])) $P2=mysql_real_escape_string($_GET["P2"]);
else $P2=0;
if (isset ($_GET["P3"])) $P3=mysql_real_escape_string($_GET["P3"]);
else $P3=0;
if (isset ($_GET["P4"])) $P4=mysql_real_escape_string($_GET["P4"]);
else $P4=1;

include_once ("config.php");
   
$mycolor=$textcolor;
//nb de jours du mois
$d=nbjoursdumois($month, $year);
$moislettres=moislettres($month);
$casej=0;$casen=0;

//=====================================================================
// choix date
//=====================================================================
if ( $print == "YES" ) {
   echo "<body  background=$background text=$textcolor onload='javascript:window.print()'>";
}
else {
   echo "<body>";
}
echo "<div align=center><font size=5><b>Garde du ".date_fran($month, $day, $year)." $moislettres $year</b></font><p>";
if ( $print == "NO" ) {
	$year0=$year -1;
	$year1=$year +1;
	echo "<form>";
	echo "<table><tr><td>année 
		<select id='menu1' name='menu1' onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3)'>";
	echo "<option value='$year0'>".$year0."</option>";
	echo "<option value='$year' selected >".$year."</option>";
	echo "<option value='$year1' >".$year1."</option>";
	echo  "</select>";

	echo " mois <select id='menu2' name='menu2' onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3)'>";
	$m=1;
	while ($m <=12) {
      $monmois = $mois[$m - 1 ];
      if ( $m == $month ) echo  "<option value='$m' selected >".$monmois."</option>\n";
      else echo  "<option value= $m >".$monmois."</option>\n";
      $m=$m+1;
	}
	echo  "</select>";

	echo " jour <select id='menu3' name='menu3' onchange='fillmenu(this.form,this.form.menu1,this.form.menu2,this.form.menu3)'>";
	$d=1;
	while ($d <= 31) {
      if ( $d == $day ) echo  "<option value='$d' selected >".$d."</option>\n";
      else echo  "<option value= $d >".$d."</option>\n";
      $d=$d+1;
	}
	echo  "</select></td>";
   	echo "<td><a href=garde_jour.php?month=$month&year=$year&day=$day&print=YES&P1=$P1&P2=$P2&P3=$P3&P4=$P4 target=_blank><img src=images/printer.gif width=22 border=0 alt='imprimer la feuille de garde'></a></td>";
	echo "</tr></table></form>"; 	
}

if ( is_we($month,$day,$year) ) {
    $daycolor="#FFFF99";
    $nightcolor="#FFCC33"; }
else {
	$daycolor="#FFFFFF";
    $nightcolor="$mylightcolor";
}


$query="select PGS_STATUS from planning_garde_status
        where PGS_YEAR=$year and PGS_MONTH=$month and EQ_ID=1";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$PGS_STATUS=$row["PGS_STATUS"];
if (( $PGS_STATUS <> "OK" ) and (! check_rights($_SESSION['id'], 7) )){
   echo "<body background=$background text=$textcolor>";
   write_msgbox("Attention",$error_pic,"Le tableau de garde pour $moislettres $year n'est pas disponible. Vous devez attendre qu'il soit créé par le bureau opérations.",30,30);
}
else {
//=====================================================================
// affiche le tableau du personnel
//=====================================================================
echo "<div align=left>";
if ( $P1 == 1 ) {
echo "<a href=garde_jour.php?month=$month&year=$year&day=$day&print=NO&P1=0&P2=$P2&P3=$P3&P4=$P4>
		<img src=images/collapse.gif alt='cacher le détail' border=0></a>
		<font size=4><b>Composition de la garde</b></font>";


$query1="select PS_ID, TYPE, DESCRIPTION +' '+EQ_NOM  as COMMENT
         from poste, equipe
	 where poste.EQ_ID=equipe.EQ_ID
	 and equipe.EQ_ID=1
	 order by PS_ID";
$result1=mysql_query($query1);
$num_postes = mysql_num_rows($result1);

 if ( check_rights($_SESSION['id'], 6)  and $print == "NO") {
      	echo " <input type='button' value='modifier' name='modifier' onclick='redirect(\"manual.php?month=$month&year=$year&day=$day&section=0&origine=gardejour\");'>";
   }
echo "<br>";
echo "<table><tr><td width=30></td><td>"; // retrait
// ===============================================
// Personnel de garde
// ===============================================

$query = "select EQ_ID, EQ_NOM, EQ_JOUR, EQ_NUIT 
		  from equipe where EQ_TYPE='GARDE' order by EQ_ID";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result) ) {
	    $EQ_ID=$row["EQ_ID"];
	    $EQ_NOM=$row["EQ_NOM"];
	    $EQ_JOUR=$row["EQ_JOUR"];	
	    $EQ_NUIT=$row["EQ_NUIT"];
	
      $sectionjour = get_section_pro_jour($EQ_ID,$year, $month, $day);
	  if (($nbsections == 3 ) and ($sectionjour <> 0)) $commentaire=" (".get_section_name($sectionjour).")";
	  else $commentaire="";
	  echo "<p><font size=3><b>$EQ_NOM $commentaire</b></font><br>";	    

	  echo "<table>";
	  echo "<tr>
	  	<td class='FondMenu'>";
	  echo "<table border=0 cellspacing=0 cellpadding=0>";
	  echo "<tr height=10>";
	  echo "<td width=60 class=TabHeader>Poste</td>";
	  echo "<td bgcolor=$mydarkcolor width=1></td>";
	  echo "<td width=300 class=TabHeader>Descriptif</td>";
	  if ( $EQ_JOUR == 1 ) {
	    echo "<td bgcolor=$mydarkcolor width=1></td>";
	  	echo "<td width=150 class=TabHeader ><div align=center>Jour</div></td>";	
	  }
	  if ( $EQ_NUIT == 1 ) {
	  	echo "<td bgcolor=$mydarkcolor width=1></td>";
	  	echo "<td width=150 class=TabHeader ><div align=center>Nuit</div></td>";
	  }
	  echo "</tr>";

      $query1="select PS_ID, TYPE, DESCRIPTION, PO_JOUR, PO_NUIT 
		  from poste 
		  where EQ_ID = $EQ_ID
		  order by PS_ID";
	  $result1=mysql_query($query1);
	  $i=0;
	  while ($row1=@mysql_fetch_array($result1) ) {
	      $PS_ID=$row1["PS_ID"];
	      $TYPE=$row1["TYPE"];
	      $DESCRIPTION=strip_tags($row1["DESCRIPTION"]);	
	      $PO_JOUR=$row1["PO_JOUR"];
	      $PO_NUIT=$row1["PO_NUIT"];	
	
     // jour
	 $query2="select pg.P_ID, p.P_NOM, p.P_PRENOM, p.P_GRADE, pg.PG_STATUT
	         from pompier p, planning_garde pg
	         where p.P_ID = pg.P_ID
	         and pg.EQ_ID = $EQ_ID
	         and pg.TYPE='J'
	         and pg.PS_ID = $PS_ID
	         and pg.PG_DATE='".$year."-".$month."-".$day."'";
     $result2=mysql_query($query2);
	 $row2=@mysql_fetch_array($result2);
	 $P_NOM_J=$row2["P_NOM"];
	 $P_GRADE_J=$row2["P_GRADE"];
	 $P_PRENOM_J=$row2["P_PRENOM"];
	 $P_ID_J=$row2["P_ID"];     	         	
	 $P_STATUT_J=$row2["PG_STATUT"];	
	 if ( $P_STATUT_J == 'SPP' ) $mycolor_j='red';
	 if ( $P_STATUT_J == 'SPV' ) $mycolor_j=$textcolor;

     // nuit
	 $query2="select pg.P_ID, p.P_NOM, p.P_PRENOM, p.P_GRADE, pg.PG_STATUT
	         from pompier p, planning_garde pg
	         where p.P_ID = pg.P_ID
	         and pg.EQ_ID = $EQ_ID
	         and pg.TYPE='N'
	         and pg.PS_ID = $PS_ID
	         and pg.PG_DATE='".$year."-".$month."-".$day."'";

     $result2=mysql_query($query2);
	 $row2=@mysql_fetch_array($result2);
	 $P_NOM_N=$row2["P_NOM"];
	 $P_GRADE_N=$row2["P_GRADE"];
	 $P_PRENOM_N=$row2["P_PRENOM"];
	 $P_ID_N=$row2["P_ID"];     	         	
	 $P_STATUT_N=$row2["PG_STATUT"];	
	 if ( $P_STATUT_N == 'SPP' ) $mycolor_n='red';
	 if ( $P_STATUT_N == 'SPV' ) $mycolor_n=$textcolor;

	  $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }

	 echo "<tr>";
	 echo "<td bgcolor=$mycolor>
	 		$TYPE</td>";
	 echo "<td bgcolor=$mydarkcolor width=1></td>";
	 echo "<td bgcolor=$mycolor>
	 		$DESCRIPTION</td>";
	 
	 if ( $P_ID_N <> $P_ID_J ) {
	    if ( $EQ_JOUR == 1 ) {
	        echo "<td bgcolor=$mydarkcolor width=1></td>";
	 		echo "<td bgcolor=$mycolor><div align=center><a href=upd_personnel.php?pompier=".$P_ID_J.">".
	 		strtoupper($P_GRADE_J." ".$P_NOM_J)."</a></div></td>";
	 	}
	 	if ( $EQ_NUIT == 1 ) {
	 		echo "<td bgcolor=$mydarkcolor width=1></td>";
	 		echo "<td bgcolor=$mycolor><div align=center><a href=upd_personnel.php?pompier=".$P_ID_N.">".
	 		strtoupper($P_GRADE_N." ".$P_NOM_N)."</a></div></td>";
	 	}
	 }
	 else {
	    echo "<td bgcolor=$mydarkcolor width=1></td>";
	    if ( $EQ_JOUR  + $EQ_NUIT == 2 ) {
	  		echo "<td bgcolor=$mycolor colspan=3>";
	  	}
	  	else if ( $EQ_JOUR + $EQ_NUIT == 1 ) {
	  	 	echo "<td bgcolor=$mycolor>";
	  	}
		echo "  <div align=center><a href=upd_personnel.php?pompier=".$P_ID_J.">".
	 		strtoupper($P_GRADE_J." ".$P_NOM_J)."</a></div></td>";
	 }
	 echo "</tr>";
  }
  echo "</table>";
  echo "</td></tr></table>";  
}
echo "</td></tr></table>"; // retrait
}
else {
   echo "<a href=garde_jour.php?month=$month&year=$year&day=$day&print=NO&P1=1&P2=$P2&P3=$P3&P4=$P4>
		<img src=images/expand.gif alt='voir le détail' border=0></a>
		<font size=4><b>Composition de la garde<br></b></font>";
 
}

// ===============================================
// personnel disponible
// ===============================================
if ( $P4 == 1 ) {
echo "<p><a href=garde_jour.php?month=$month&year=$year&day=$day&print=NO&P1=$P1&P2=$P2&P3=$P3&P4=0>
		<img src=images/collapse.gif alt='cacher le détail' border=0></a>
		<font size=4><b>Personnel en réserve<br></b></font>";

echo "<table><tr><td width=30></td><td>"; // retrait		
echo "<table >";
echo "<tr>
<td class='FondMenu'>";
echo "<table border=0 cellspacing=0 cellpadding=0>
      <tr height=8>";
echo "<td width=150 class=TabHeader>24h</td>";
echo "<td bgcolor=$mydarkcolor width=1></td>";
echo "<td width=150 class=TabHeader>Jour seul</td>";
echo "<td bgcolor=$mydarkcolor width=1></td>";
echo "<td width=150 class=TabHeader>Nuit seule</td>";
echo "</tr>";

echo "<tr>";
echo "<td width=150 bgcolor=$mylightcolor>";
personnel_reserve($year, $month, $day, 'A');
echo "</td>";
echo "<td bgcolor=$mydarkcolor width=1></td>";
echo "<td width=150 bgcolor=#FFFFFF>";
personnel_reserve($year, $month, $day, 'J');
echo "</td>";
echo "<td bgcolor=$mydarkcolor width=1></td>";
echo "<td width=150 bgcolor=$mylightcolor>";
personnel_reserve($year, $month, $day, 'N');
echo "</td>";
echo "</tr>";

echo "</table>";
echo "</td></tr></table>";
echo "</td></tr></table>"; // retrait
}
else {
 echo "<p><a href=garde_jour.php?month=$month&year=$year&day=$day&print=NO&P1=$P1&P2=$P2&P3=$P3&P4=1>
		<img src=images/expand.gif alt='voir le détail' border=0></a>
		<font size=4><b>Personnel en réserve<br></b></font>";
}

// ===============================================
// affiche les tableaux des départs / engin
// ===============================================
if ( $vehicules <> 0 ) {

if ( $P2 == 1 ) {
 echo "<p><a href=garde_jour.php?month=$month&year=$year&day=$day&print=NO&P1=$P1&P2=0&P3=$P3&P4=$P4>
		<img src=images/collapse.gif alt='cacher le détail' border=0></a>
		<font size=4><b>Départs par engin<br></b></font>";

echo "<table><tr><td width=30></td><td>"; // retrait

$queryx="select distinct e.EQ_ID,e.EQ_JOUR,e.EQ_NUIT from equipe e, vehicule v
		 where v.EQ_ID=e.EQ_ID";		
$resultx=mysql_query($queryx);
while ($rowx=@mysql_fetch_array($resultx)) { // équipe loop
	$equipe=$rowx["EQ_ID"];
	$EQ_JOUR=$rowx["EQ_JOUR"];
	$EQ_NUIT=$rowx["EQ_NUIT"];

	$query1="select distinct v.V_ID, v.TV_CODE, tv.TV_LIBELLE,
		v.V_COMMENT, tv.TV_NB, vp.VP_LIBELLE, vp.VP_OPERATIONNEL,
		 v.V_KM, v.V_IMMATRICULATION
        from vehicule v, type_vehicule tv, equipage e, vehicule_position vp
	where tv.TV_CODE=v.TV_CODE
	and vp.VP_ID = v.VP_ID
	and e.V_ID=v.V_ID
	and v.EQ_ID=$equipe
	order by vp.VP_OPERATIONNEL desc, tv.TV_NB desc, v.V_ID asc ";
	$result1=mysql_query($query1);


	while ($row1=@mysql_fetch_array($result1)) {
      $TV_CODE=$row1["TV_CODE"];
      $V_COMMENT=$row1["V_COMMENT"];
      $V_ID=$row1["V_ID"];
      $VP_LIBELLE=$row1["VP_LIBELLE"];
      $VP_OPERATIONNEL=$row1["VP_OPERATIONNEL"];
      $TV_NB=$row1["TV_NB"];
      $TV_LIBELLE=$row1["TV_LIBELLE"];
      $V_IMMATRICULATION=$row1["V_IMMATRICULATION"];
      $V_KM=$row1["V_KM"];
      
      
      switch ($VP_OPERATIONNEL) {
		case 1:// indispo
   			$img="<img src=images/red.gif border=0>";
   			break;
		case 2: //limite
   			$img="<img src=images/yellow.gif border=0>";
   			break;
		case 3: //operationnel
   			$img="<img src=images/green.gif border=0>";
   			break;
		}
      echo "<p><table><tr><td width=10>$img</td>";
      echo "<td width=300>
      		<a href=upd_vehicule.php?vid=$V_ID&from=garde>
	  		<b>".$TV_CODE." ".$V_IMMATRICULATION." : ".$VP_LIBELLE."</b></a>";
	  if ( $V_COMMENT <> "") echo "<i> ( ".$V_COMMENT." ) </i>";
	  echo "</td></tr></table>";

      
      if ( $VP_OPERATIONNEL >= 2 ) {
      echo "<table>";
	  echo "<tr>
	  	<td class='FondMenu'>";
      echo "<table border=0 cellspacing=0 cellpadding=0>";
	  echo "<tr height=10>";
	  echo "<td width=100 class=TabHeader>Piquet</td>";
	  if ( $EQ_JOUR == 1 ) {
	  	echo "<td bgcolor=$mydarkcolor width=1></td>";
	  	echo "<td width=150 class=TabHeader align=center>Jour</td>";
	  }
	  if ( $EQ_NUIT == 1 ) {
	  	  	echo "<td bgcolor=$mydarkcolor width=1></td>";
	  		echo "<td width=150 class=TabHeader align=center>Nuit</td>";
	  }
	  echo "</tr>";
		
	  $query0="select ROLE_ID, ROLE_NAME from type_vehicule_role tvr 
	  			where TV_CODE='".$TV_CODE."'
				order by ROLE_ID";
	  $i=0;
	  $result0=mysql_query($query0);
	  while ($row0=@mysql_fetch_array($result0)) {
      	$ROLE_ID=$row0["ROLE_ID"];
      	$ROLE_NAME=$row0["ROLE_NAME"];
	    if ( $EQ_JOUR == 1 ) {
	    	//jour 
	    	$query="select pg.P_ID, p.P_NOM, p.P_GRADE, e.ROLE_ID 
			from pompier p, planning_garde pg, equipage e
			where p.P_ID = pg.P_ID 
			and e.PS_ID = pg.PS_ID 
			and e.V_ID = $V_ID 
			and pg.EQ_ID = $equipe 
			and e.ROLE_ID = $ROLE_ID
			and pg.PG_DATE = '".$year."-".$month."-".$day."' 
			and pg.TYPE = 'J'";

	    	$result=mysql_query($query);
	    	$row=@mysql_fetch_array($result);
	    	$P_NOM_J=$row["P_NOM"];
	    	$P_GRADE_J=$row["P_GRADE"];
	    	$P_ID_J=$row["P_ID"];	
		}
	    if ( $EQ_NUIT == 1 ) {
	    	//nuit 
	    	$query="select pg.P_ID, p.P_NOM, p.P_GRADE, e.ROLE_ID 
			from pompier p, planning_garde pg, equipage e
			where p.P_ID = pg.P_ID 
			and e.PS_ID = pg.PS_ID 
			and e.V_ID = $V_ID 
			and pg.EQ_ID = $equipe 
			and e.ROLE_ID = $ROLE_ID
			and pg.PG_DATE = '".$year."-".$month."-".$day."' 
			and pg.TYPE = 'N'";

	    	$result=mysql_query($query);
	    	$row=@mysql_fetch_array($result);
	    	$P_NOM_N=$row["P_NOM"];
	    	$P_GRADE_N=$row["P_GRADE"];
	    	$P_ID_N=$row["P_ID"];	
		}
	
	   	$i=$i+1;
        if ( $i%2 == 0 ) {
      	 	$mycolor=$mylightcolor;
      	}
      	else {
      	 	$mycolor="#FFFFFF";
      	}
      	$EQ_JOUR = get_equipe_status_jour($equipe);
      	$EQ_NUIT = get_equipe_status_nuit($equipe);
	    echo "<tr height=10>";
	    echo "<td width=100 bgcolor=$mycolor>$ROLE_NAME</td>";
	 		
	    if ( $P_ID_N <> $P_ID_J ) {
	    	if ( $EQ_JOUR == 1 ) {
	        	echo "<td bgcolor=$mydarkcolor width=1></td>";
	 			echo "<td width=150 bgcolor=$mycolor><div align=center>".
	 			strtoupper($P_GRADE_J." ".$P_NOM_J)."</div></td>";
	 		}
	 		if ( $EQ_NUIT == 1 ) {
	 			echo "<td bgcolor=$mydarkcolor width=1></td>";
	 			echo "<td width=150 bgcolor=$mycolor><div align=center>".
	 			strtoupper($P_GRADE_N." ".$P_NOM_N)."</div></td>";
	 		}
	 	}
	 	else {
	    	echo "<td bgcolor=$mydarkcolor width=1></td>";
	    	if ( $EQ_JOUR  + $EQ_NUIT == 2 ) {
	  			echo "<td width=301 bgcolor=$mycolor colspan=3>";
	  		}
	  		else if ( $EQ_JOUR + $EQ_NUIT == 1 ) {
	  	 		echo "<td width=150 bgcolor=$mycolor>";
	  		}
			echo "  <div align=center>".
	 		strtoupper($P_GRADE_J." ".$P_NOM_J)."</div></td>";
	    }
	   echo "</tr>";
	  }
	}
	echo "</table>";
	echo "</td></tr></table>"; 
   }  
  }
  echo "</td></tr></table>"; // retrait
}

else {
 echo "<p><a href=garde_jour.php?month=$month&year=$year&day=$day&print=NO&P1=$P1&P2=1&P3=$P3&P4=$P4>
		<img src=images/expand.gif alt='voir le détail' border=0></a>
		<font size=4><b>Départs par engin<br></b></font>";
}
}
// ===============================================
// affichage des messages en cours
// ===============================================
if ( $P3 == 1) {
echo "<p><a href=garde_jour.php?month=$month&year=$year&day=$day&print=NO&P1=$P1&P2=$P2&P3=0&P4=$P4>
		<img src=images/collapse.gif alt='cacher le détail' border=0></a>
		<font size=4><b>Consignes pour la garde<br></b></font>"; 

echo "<table><tr><td width=30></td><td>"; // retrait
$query="SELECT pompier.P_ID, P_NOM, P_GRADE, M_DUREE, DATE_FORMAT(M_DATE,'%d/%m/%Y %H:%i') as FORMDATE1,
        DATE_FORMAT(M_DATE, '%m%d%Y%T') as FORMDATE2, pompier.P_ID, M_TEXTE, M_OBJET, M_FILE
	FROM message, pompier
        where  ((DAYOFYEAR(M_DATE) + M_DUREE  >=  DAYOFYEAR(CURDATE())
	       and YEAR(M_DATE) = YEAR(CURDATE()))
	       or ( DAYOFYEAR(M_DATE) + M_DUREE  >=  DAYOFYEAR(CURDATE()) +365
	       and YEAR(M_DATE)+1  = YEAR(CURDATE())))
	and message.P_ID=pompier.P_ID
	and message.M_TYPE='consigne'
	order by M_DATE desc";
$result=mysql_query($query);

echo "<table width=650 cellspacing=0 border=0 >";
while ($row = mysql_fetch_array($result) )
{
 echo "<tr><td width=10><img src=images/bullet.gif ></td>
           <td width=490><font size=3><b>".$row["M_OBJET"]." </font></b><font size=1> -<i> (".$row["P_GRADE"]." ".strtoupper($row["P_NOM"])." - ".$row["FORMDATE1"]." - ".$row["M_DUREE"]."j )</i></font><br>
           ".$row["M_TEXTE"];
 if ( $row["M_FILE"] <> "") echo " <i> fichier joint - <a href=files/".$row["M_FILE"]." target=_blank>".$row["M_FILE"]."</a></i>";
 echo "</font></td></tr>";
}
echo "</table>";
echo "</td></tr></table>"; // retrait
}
else {
 echo "<p><a href=garde_jour.php?month=$month&year=$year&day=$day&print=NO&P1=$P1&P2=$P2&P3=1&P4=$P4>
		<img src=images/expand.gif alt='voir le détail' border=0></a>
		<font size=4><b>Consignes pour la garde<br></b></font>";
 
}
}
?>
