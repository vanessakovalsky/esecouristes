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

if (isset($_GET["person"])) $person=$_GET["person"];
else $person=0;
if (isset($_GET["section"])) $section=$_GET["section"];
else $section=0;
if (isset($_GET["equipe"])) $equipe=$_GET["equipe"];
else $equipe=1;
if (isset($_GET["print"])) $print=$_GET["print"];
else $print='NO';

if (isset($_GET["month"])) {
 	$month=$_GET["month"];
 	$year=$_GET["year"];
}
else {
	$month=date("n");
	$year=date("Y");
	// afficher le mois suivant
	if ( $month == 12 )  {
      		$month = 1;
      		$year= $year +1;
	}
	else  $month = $month +1 ;
}
$moislettres=moislettres($month);
writehead();

//=====================================================================
// javascripts
//=====================================================================
?>
<SCRIPT language=JavaScript>

function fillmenu(frm, menu1,menu2,person,section,equipe) {
year=frm.menu1.options[frm.menu1.selectedIndex].value;
month=frm.menu2.options[frm.menu2.selectedIndex].value;
url = "tableau_garde.php?month="+month+"&year="+year+"&person="+person+"&section="+section+"&equipe="+equipe+"&print=NO";
self.location.href = url;
}

function redirect(p1,p2,p3,p4,p5) {
     url="tableau_garde.php?year="+p2+"&month="+p1+"&person="+p3+"&section="+p4+"&equipe="+p5+"&print=NO";
     self.location.href=url;
}
function bouton_redirect(cible, action, nom_equipe) {
 if ( action == 'vider' ) {
    if ( confirm ("Attention : vous êtes sur le point de vider le tableau de '"+nom_equipe+"'.\nLes données seront perdues. Voulez vous continuer ?" )) {
	 self.location.href = cible;
    }
 }
 else if ( action == 'remplir' ) {
    if ( confirm ("Attention : vous êtes sur le point de recalculer automatiquement le tableau de '"+nom_equipe+"'.\nLes données seront perdues. Voulez vous continuer ?" )) {
	 self.location.href = cible;
    }
 }
 else if ( action == 'montrer' ) {
    if ( confirm ("Attention : Vous allez rendre le tableau de '"+nom_equipe+"' accessible par tout le personnel.\nLes agents ne pourront plus modifier leur disponibilités.\nLe tableau est-il vraiment terminé ?" )) {
	 self.location.href = cible;
    }
 }
 else if ( action == 'masquer' ) {
    if ( confirm ("Attention : Le tableau de '"+nom_equipe+"' ne sera plus visible par le personnel.\nLes agents pourront de nouveau modifier leur disponibilités.\nVoulez vous vraiment le masquer ?" )) {
	 self.location.href = cible;
    }
 }
 else {
      self.location.href = cible;
 }
}

function displaymanager(url){
	 self.location.href=url;
	 return true
}
</SCRIPT>

<?php

//=====================================================================
// formulaire
//=====================================================================
$yearnext=date("Y") +1;
$yearcurrent=date("Y");
$yearprevious = date("Y") - 1;

echo "<form>";
if ( $print == 'NO' ) {
	echo "<p><table border=0><tr><td>";
	echo "année<select name='menu1' 
	onchange=\"fillmenu(this.form,this.form.menu1,this.form.menu2,'".$person."','".$section."','".$equipe."')\">";
	if ($year > $yearprevious) echo "<option value='$yearprevious'>".$yearprevious."</option>";
	else echo "<option value='$yearprevious' selected>".$yearprevious."</option>";
	if ($year <> $yearcurrent) echo "<option value='$yearcurrent' >".$yearcurrent."</option>";
	else echo "<option value='$yearcurrent' selected>".$yearcurrent."</option>";
	if ($year < $yearnext)  echo "<option value='$yearnext' >".$yearnext."</option>";
	else echo "<option value='$yearnext' selected>".$yearnext."</option>";
	echo  "</select></td>";

	echo "<td>mois<select name='menu2' 
	onchange=\"fillmenu(this.form,this.form.menu1,this.form.menu2,'".$person."','".$section."','".$equipe."')\">";
	$m=1;
	while ($m <=12) {
      $monmois = $mois[$m - 1 ];
      if ( $m == $month ) echo  "<option value='$m' selected >".$monmois."</option>\n";
      else echo  "<option value= $m >".$monmois."</option>\n";
      $m=$m+1;
	}
	echo  "</select>";
	echo "</td></tr></table>";
	echo "</form>";
}

function intercase() {
global $print, $mydarkcolor;
	 if ( $print == "NO" ) {
	    echo "<td bgcolor=$mydarkcolor width=0></td>";
         }
}
   
$mycolor=$textcolor;
//nb de jours du mois
$d=nbjoursdumois($month, $year);

$queryg="select EQ_NOM, EQ_JOUR, EQ_NUIT, S_ID from equipe where EQ_ID=".$equipe;
$resultg=mysql_query($queryg);
$rowg=@mysql_fetch_array($resultg);
$EQ_NOM=$rowg["EQ_NOM"];
$EQ_JOUR=$rowg["EQ_JOUR"];
$EQ_NUIT=$rowg["EQ_NUIT"];
$EQS_ID=$rowg["S_ID"];

//=====================================================================
// le tableau est il terminé ? sinon seuls certains peuvent le voir
//=====================================================================
$query="select PGS_STATUS from planning_garde_status
        where PGS_YEAR=$year and PGS_MONTH=$month and EQ_ID=$equipe";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$PGS_STATUS=$row["PGS_STATUS"];
if (( $PGS_STATUS <> "OK" ) and (! check_rights($_SESSION['id'], 6)) and ($print == "NO")){
   echo "<body background=$background text=$textcolor>";
   write_msgbox("Attention",$error_pic,"Le tableau de $EQ_NOM pour $moislettres $year n'est pas disponible. Vous devez attendre qu'il soit créé par le bureau opérations.",30,30);
}
else {

//=====================================================================
// affiche le tableau
//=====================================================================

$query="select P_ID, P_PRENOM, P_NOM from pompier order by P_NOM";
$result=mysql_query($query);

if ( $print == "YES" ) {
   echo "<body onload='javascript:window.print()'>";
}
else {
   echo "<body>";
}

echo "<div align=center><font size=4><b>Tableau de ".$EQ_NOM." pour $moislettres $year";

if ( $section <> 0 ) echo " (section $section)";
echo "<br></b></font>";

if ( check_rights($_SESSION['id'], 7)) {
   if ( $PGS_STATUS <> "OK" ) {
      echo "<table width=700 cellspacing=0 border=0 ><TR>
            <td width=10%>".$warning_pic."<td>
            <td width=90%>Le tableau n'est pas accessible par le personnel.
	    <input type='button' value='Montrer' name='montrer' onclick=\"bouton_redirect('tableau_garde_status.php?month=$month&year=$year&section=$section&equipe=$equipe&action=montrer','montrer', '".$EQ_NOM."')\"></td>
	    </tr></table>";
   }
}

if ( $print == "NO" ) {
echo "<p>";
if ( check_rights($_SESSION['id'], 5)) {
   echo " <input type='button' value='Vider' name='vider' onclick=\"bouton_redirect('tableau_garde_delete.php?month=$month&year=$year&equipe=$equipe','vider', '".$EQ_NOM."')\">";
}
if ( check_rights($_SESSION['id'], 7)) {
   echo " <input type='button' value='Remplir' name='remplir' onclick=\"bouton_redirect('processing.php?month=$month&year=$year&day=1&equipe=$equipe','remplir', '".$EQ_NOM."');\">";
}
if ( check_rights($_SESSION['id'], 7)) {
   if ( $PGS_STATUS == "OK" ) {
      echo " <input type='button' value='Masquer' name='masquer' onclick=\"bouton_redirect('tableau_garde_status.php?month=$month&year=$year&section=$section&equipe=$equipe&action=masquer','masquer', '".$EQ_NOM."')\">";
   }
}
if ($nbsections == 3 ) echo "<table width=500 cellspacing=0 border=0 >";
else echo "<table width=380 cellspacing=0 border=0 >";
echo "<tr height=60>";
// filtre personnes
echo "          <td> filtre du personnel<br>
	                 <select id='filtre' name='filtre' onchange='redirect(".$month.",".$year.",document.getElementById(\"filtre\").value,".$section.",".$equipe.")'>
	                 <option value='0'>Tout le monde</option>";
          
while ($row=@mysql_fetch_array($result)) {
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_ID=$row["P_ID"];
      echo "<option value='".$P_ID."'";
      if ($P_ID == $person ) echo " selected ";
      echo ">".strtoupper($P_NOM)." ".ucfirst($P_PRENOM)."</option>\n";
}

// filtre section
if ($nbsections == 3 ) {
	$query="select S_ID from section where S_ID in (1,2,3)";
	echo "</select></td>";
	echo "          <td>filtre des jours<br>
	                 <select id='section' name='section' onchange='redirect(".$month.",".$year.",".$person.",document.getElementById(\"section\").value,".$equipe.")'>
	                 <option value='0'>Tous les jours</option>";
	$result=mysql_query($query);
	while ($row=@mysql_fetch_array($result)) {
             $S_ID=$row["S_ID"];
     	     echo "<option value='".$S_ID."'";
      	     if ($S_ID == $section ) echo " selected ";
      	     echo ">jours section ".$S_ID."</option>\n";
	}
	echo "</select></td>";
}
//choix type de garde
$query="select distinct e.EQ_ID, e.EQ_NOM from equipe e, poste p where e.EQ_ID=p.EQ_ID and e.EQ_TYPE='GARDE'";
echo "          <td>type de garde<br>
	                 <select id='equipe' name='equipe' onchange='redirect(".$month.",".$year.",".$person.",".$section.",document.getElementById(\"equipe\").value)'>";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
             $EQ_ID=$row["EQ_ID"];
             $EQ_NOM=$row["EQ_NOM"];
     	     echo "<option value='".$EQ_ID."'";
      	     if ($EQ_ID == $equipe ) echo " selected ";
      	     echo ">".$EQ_NOM."</option>\n";
}
echo "</select></td>";


echo"      <td><a href=tableau_garde.php?month=$month&year=$year&person=$person&section=$section&equipe=$equipe&print=YES target=_blank><img src=images/printer.gif width=22 border=0 alt='imprimer le tableau'></a></td>
      <tr></table>";
}

// ===============================================
// liste des postes devant être affichés
// ===============================================

$queryp="select PS_ID, TYPE, DESCRIPTION
         from poste, equipe
	 where poste.EQ_ID=equipe.EQ_ID
	 and equipe.EQ_ID=".$equipe."
	 order by PS_ID";
$resultp=mysql_query($queryp);

if ( $print == 'NO') 
 echo "<p><table>
       <tr>
        <td class='FondMenu'>";

if ( $print == "NO" ) {
   echo "<table border=0 cellspacing=0 >";
}
else {
   echo "<table border=1 cellspacing=0 cellpadding=0 bordercolor=$mydarkcolor>";
}

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr height=15 class=TabHeader>";
echo  "<td width=90><font size=1>Jour</font></td>
      <td width=10><font size=1>S.</font></td>
      <td width=10><font size=1>J/N</font></td>";
intercase();
while ($rowp=@mysql_fetch_array($resultp)) {
      $TYPE=$rowp["TYPE"];
      $DESCRIPTION=$rowp["DESCRIPTION"];
      echo "<td bgcolor=$mydarkcolor width=100 align=center><font size=1>$DESCRIPTION</font></td>";
}
echo "</tr>";

// ===============================================
// 2 lignes par jour (j / N)
// ===============================================

$day=1;
while ( $day <= $d ) {
  if ( $EQS_ID <> 0 ) $sectionjour=get_section_pro_jour($equipe,$year, $month, $day);
  else $sectionjour=0;
   if (( $section == 0 ) or ( $section == $sectionjour )) {
      if ( is_we($month,$day ,$year) ) {
      	 $daycolor="#FFFF99";
      	 $nightcolor="#FFCC33"; }
      else {
      	 $daycolor="#FFFFFF";
      	 $nightcolor="$mylightcolor";
      }
    // jour
    if ( $EQ_JOUR == 1 ) { // si la garde est active le jour
      if (( check_rights($_SESSION['id'], 6)) && ( $print == "NO")) {
      	 echo "<tr height=9 bgcolor=$daycolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$daycolor'\" onclick=\"this.bgColor='#33FF00'; displaymanager('manual.php?month=$month&year=$year&section=$section&day=$day&equipe=$equipe&origine=tableau')\">";
      }
      else echo "<tr height=9 bgcolor=$daycolor>";
      echo "<td width=90><font color=#0000CC size=1>";
      echo date_fran($month, $day, $year);
      echo "</font></td>";
      if (($nbsections == 3 ) and ( $sectionjour <> 0 )) $img="<img src=images/".$sectionjour.".gif>";
      else $img='-';
	  echo "<td width=10 align=center>$img</td>";
      echo "<td width=10 align=center><font color=#0000CC size=1>J</font></td>";
      intercase();
      
      $resultp=mysql_query($queryp);
      while ($rowp=@mysql_fetch_array($resultp)) {
	      $PS_ID=$rowp["PS_ID"];
	      $query="select pg.P_ID, p.P_NOM, p.P_STATUT, pg.PG_STATUT, p.P_SECTION, pg.TYPE 
		  		from planning_garde pg, pompier p
                where pg.TYPE ='J'
		        and pg.PS_ID=".$PS_ID."
		        and pg.P_ID=p.P_ID
		        and pg.PG_DATE='".$year."-".$month."-".$day."'
		        order by pg.TYPE";
	      $result=mysql_query($query);
	      if ( mysql_num_rows($result) == 0 ) {
              	$PG_STATUT='SPV';
              	$P_STATUT='SPV';
              	$P_NOM='-';
              	$mycolor='#A0A0A0';
              	echo "<td width=100 align=center><font size=1>-</font></td>";
      	      }
	      else { // cas  général 1 personne
	         $row=@mysql_fetch_array($result);
       	         $P_NOM=$row["P_NOM"];
       	         $P_ID=$row["P_ID"];
       	         $P_SECTION=$row["P_SECTION"];  	
       	         $PG_STATUT=$row["PG_STATUT"];
       	         $P_STATUT=$row["P_STATUT"];
		 		 $PG_TYPE=$row["TYPE"];
       	         $boldtag1="<b>";$boldtag2="</b>";
       	         if (( $P_SECTION <> $sectionjour) and ($nbsections == 3) 
						and ( $sectionjour <> 0 ) and ($person==0) ) 
						$sectioninfo=" <img src=images/".$P_SECTION.".gif>";
	             else $sectioninfo="";
       	      	 if ( $P_STATUT == 'SPP' ) $mycolor='#FF0000';
       	      	 else $mycolor=$textcolor;
	      	 if ( $P_STATUT <> $PG_STATUT ) { // cas garde SPPV
		     	 $boldtag1=$boldtag1."<u>";
				 $boldtag2=$boldtag2."</u>";
	         }
       	     if (( $person <> 0 ) and ( $person <> $P_ID )) {
		     	$mycolor='#A0A0A0';
		     	$boldtag1="<i>";
		     	$boldtag2="</i>";
	         }
	         if ( $PG_TYPE=="J" ) echo "<td width=100 align=center><font size=1><font color=$mycolor>".$boldtag1.strtoupper($P_NOM).$sectioninfo.$boldtag2."</font></td>";
      	      }
      }
      echo "</tr>";
    } //jour
      
   // nuit
      if ( $EQ_NUIT == 1 ) { // si la garde est active la nuit
      if (( check_rights($_SESSION['id'], 6)) && ( $print == "NO")) {
      	 echo "<tr height=9 bgcolor=$nightcolor onMouseover=\"this.bgColor='yellow'\" onMouseout=\"this.bgColor='$nightcolor'\" onclick=\"this.bgColor='#33FF00'; displaymanager('manual.php?month=$month&year=$year&section=$section&day=$day&equipe=$equipe&origine=tableau')\">";
      }
      else echo "<tr height=9 bgcolor=$nightcolor>";
      if ( $EQ_JOUR == 0 ) {
      	echo "<td width=90><font color=#0000CC size=1>";
      	echo date_fran($month, $day, $year);
      	echo "</font></td>";
      	if ($nbsections == 3 ) echo "<td width=10 align=center><img src=images/".$sectionjour.".gif></td>";
      	else echo "<td width=10 align=center></td>";
      	echo "<td width=10 align=center><font color=#0000CC size=1>N</font></td>";
      }
      else {
      	echo "<td width=90 align=center><font color=#0000CC size=1>''</font></td>";
      	echo "<td width=10 align=center><font color=#0000CC size=1></font></td>";
      	echo "<td width=10 align=center><font color=#0000CC size=1>N</font></td>";
      }
      intercase();
      $resultp=mysql_query($queryp);
      while ($rowp=@mysql_fetch_array($resultp)) {
	      $PS_ID=$rowp["PS_ID"];
	      $query="select pg.P_ID, p.P_NOM,p.P_STATUT, p.P_SECTION, pg.PG_STATUT from planning_garde pg, pompier p
                where pg.TYPE='N'
		      	and pg.PS_ID=".$PS_ID."
		      	and pg.P_ID=p.P_ID
		      	and pg.PG_DATE='".$year."-".$month."-".$day."'";
	      $result=mysql_query($query);
	      $row=@mysql_fetch_array($result);
	      $sectioninfo="";
	       if ( mysql_num_rows($result) == 0 ) {
              	$PG_STATUT='SPV';
              	$P_STATUT='SPV';
              	$P_NOM='-';
	      }
	      else {
       	        $P_NOM=$row["P_NOM"];
                $P_ID=$row["P_ID"];
                $P_SECTION=$row["P_SECTION"];
       	        $PG_STATUT=$row["PG_STATUT"];
       	        $P_STATUT=$row["P_STATUT"];
       	        if (( $P_SECTION <> $sectionjour) and ($nbsections == 3) 
						and ( $sectionjour <> 0 ) and ($person==0) )  
				   $sectioninfo=" <img src=images/".$P_SECTION.".gif>";
	            else $sectioninfo="";
       	      }
       	      $boldtag1="<b>";$boldtag2="</b>";
       	     
       	      if ( $P_STATUT == 'SPP' ) $mycolor='#FF0000';
       	      else  $mycolor=$textcolor;
	      if ( $P_STATUT <> $PG_STATUT ) { // cas garde SPPV
		     	 $boldtag1=$boldtag1."<u>";
				 $boldtag2=$boldtag2."</u>";
	      }
       	  if (( $person <> 0 ) and ( $person <> $P_ID )) {
		     $mycolor='#A0A0A0';
		     $boldtag1="<i>";
		     $boldtag2="</i>";
	      }
      	      echo "<td width=100 align=center ><font size=1><font color=$mycolor>".$boldtag1.strtoupper($P_NOM).$sectioninfo.$boldtag2."</font></td>";
      }
      echo "</tr>";
      } // if $EQ_NUIT == 1
   } // if section
   $day=$day+1;
} //end loop of days
echo "</table>";
if ( $print == 'NO') echo "</td></tr></table>";
      
} // tableau termine
?>
