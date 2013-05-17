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
check_all(38);

if (isset($_GET["person"]))
$person=$_GET["person"];
else
$person=$_SESSION['id'];

$section=$_SESSION['SES_SECTION'];
$person=intval($person);
if (get_matricule($person) == '' ) {
	param_error_msg();
	exit;
}

$defaultmonth=date("n");
$defaultyear=date("Y");
if ( $gardes == 1 ) {
	// afficher le mois suivant
	if ( $defaultmonth == 12 )  {
      	$defaultmonth = 1;
      	$defaultyear= $defaultyear +1;
	}
	else $defaultmonth = $defaultmonth +1 ;
}

if (isset($_GET["month"])) {
 	$month=intval($_GET["month"]);
 	$year=intval($_GET["year"]);
}
else {
	$month=$defaultmonth;
	$year=$defaultyear;
}

$moislettres=moislettres($month);
writehead();
?>
<SCRIPT type="text/javascript">

function fillmenu(frm, menu1,menu2,person) {
year=frm.menu1.options[frm.menu1.selectedIndex].value;
month=frm.menu2.options[frm.menu2.selectedIndex].value;
url = "dispo.php?month="+month+"&year="+year+"&person="+person;
self.location.href = url;
}

//=====================================================================
// Mise à jour des totaux
//=====================================================================

//-- Global Variables
var RowsInForm = 5
//-- Updates the totals in the lower part of table.
function updateTotalJ(mybox) {
      if ( mybox.checked ) {
      	 document.dispo.totalJ.value = document.dispo.totalJ.value - (-1);
      }
      else {
	 document.dispo.totalJ.value = document.dispo.totalJ.value - 1;
      }
}
function updateTotalN(mybox) {
      if ( mybox.checked ) {
      	 document.dispo.totalN.value = document.dispo.totalN.value - (-1);
      }
      else {
	 document.dispo.totalN.value = document.dispo.totalN.value - 1;
      }
}

//=====================================================================
// choix personne
//=====================================================================
function redirect(p1,p2,p3,p4) {
     if ( p4 == 'saisie' ) {
     	url="dispo.php?person="+p1+"&month="+p2+"&year="+p3;
     	self.location.href=url;
     }
     if ( p4 == 'ouvrir' ) {
    	if ( confirm ("Attention : Vous allez permettre la saisie des disponibilités pour le mois "+p2+"/"+p3+" par tout le personnel.\nLes agents pourront de nouveau modifier leur disponibilités.\nConfirmer ?" )) {
    	  cible="tableau_garde_status.php?month="+p2+"&year="+p3+"&action=ouvrir&section=0";
	      self.location.href = cible;
        }
     }
 	 if ( p4 == 'fermer' ) {
        if ( confirm ("Attention : Vous allez bloquer la saisie des disponibilités pour le mois "+p2+"/"+p3+".\nLes agents ne pourront plus saisir ou modifier leur disponibilités pour le mois suivant.\nConfirmer ?" )) {
        	cible="tableau_garde_status.php?month="+p2+"&year="+p3+"&action=fermer&section=0";
	        self.location.href = cible;
        }
     }
     
}

//=====================================================================
// check all
//=====================================================================
function CheckAll(field,checkValue){
	var dForm = document.dispo;
	var iChecked = 0;
	// Vérif du compteur
	if(field=='J'){
	document.dispo.totalJ.value = ((checkValue!=true)?document.dispo.totalJ.value:0 );
	}
	if(field=='N'){
	document.dispo.totalN.value =  ((checkValue!=true)?document.dispo.totalN.value:0 );
	}	
	// Parcours des jours et mise à jour des cases à cocher
	for (i=0;i<dForm.length;i++)
	{
		var element = dForm[i];
		if (element.type=='checkbox'){
			//alert(element.name.substring(0,1));
			switch (element.name.substring(0,1)){
			case 'J':
				if (element.name.substring(0,1)==field){
				element.checked = ((checkValue!=true)?false:true) ;
				updateTotalJ(element);
				}
				break;
			case 'N':
				if (element.name.substring(0,1)==field){
				element.checked = ((checkValue!=true)?false:true) ;
				updateTotalN(element);
				}
				break;
			default:
			}			
		}
	}
}

</SCRIPT>
</HEAD>
<BODY>
<?php


//=====================================================================
// formulaire
//=====================================================================
$yearnext=date("Y") +1;
$yearcurrent=date("Y");
$yearprevious = date("Y") - 1;

echo "<body>";
echo "<form>";

echo "<table border=0><tr><td>";
echo "année 
<select name='menu1' onchange=\"fillmenu(this.form,this.form.menu1,this.form.menu2,'".$person."')\">";
if ($year > $yearprevious) echo "<option value='$yearprevious'>".$yearprevious."</option>";
else echo "<option value='$yearprevious' selected>".$yearprevious."</option>";
if ($year <> $yearcurrent) echo "<option value='$yearcurrent' >".$yearcurrent."</option>";
else echo "<option value='$yearcurrent' selected>".$yearcurrent."</option>";
if ($year < $yearnext)  echo "<option value='$yearnext' >".$yearnext."</option>";
else echo "<option value='$yearnext' selected>".$yearnext."</option>";
echo  "</select></td>";

echo "<td>mois <select name='menu2' onchange=\"fillmenu(this.form,this.form.menu1,this.form.menu2,'".$person."')\">";
$m=1;
while ($m <=12) {
      $monmois = $mois[$m - 1 ];
      if ( $m == $month ) echo  "<option value='$m' selected >".$monmois."</option>\n";
      else echo  "<option value= $m >".$monmois."</option>\n";
      $m=$m+1;
}
echo  "</select>";
echo "</td></tr></table>";

echo "<div align=center><font size=4><b>Disponibilités pour $moislettres $year de</b></font><br>";
echo "<select id='filtre' name='filtre' onchange=\"redirect(document.getElementById('filtre').value,'".$month."','".$year."', 'saisie')\">";
$query="select p.P_ID, p.P_PRENOM, p.P_NOM , s.S_CODE 
		from pompier p, section s
		where p.P_SECTION = s.S_ID
		and p.P_OLD_MEMBER = 0 
		and p.P_STATUT <> 'EXT'";

if (( $nbsections == 0 ) and (! check_rights($_SESSION['id'], 24))) {
	$query .= " and P_SECTION in (".get_family($section).")";
}

$query .= " order by P_NOM";
$result=mysql_query($query);

while ($row=@mysql_fetch_array($result)) {
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_ID=$row["P_ID"];
      $S_CODE=$row["S_CODE"];
      echo "<option value='".$P_ID."'";
      if ($P_ID == $person ) echo " selected ";
      if ( $nbsections <> 1 ) $cmt=' ('.$S_CODE.')';
      else $cmt ='';
      echo ">".strtoupper($P_NOM)." ".ucfirst($P_PRENOM).$cmt."</option>\n";
}
echo "</select>";
echo "</form>";

$query2="select count(*) as NB from planning_garde_status where
       PGS_STATUS='OK' and PGS_MONTH  =".$month."  and PGS_YEAR=".$year;
$result2=mysql_query($query2);
$row2=@mysql_fetch_array($result2);
$NB=$row2["NB"];


$query2="select count(*) as NB from planning_garde_status where EQ_ID = 0 and
       PGS_STATUS='OK' and PGS_MONTH  =".$month."  and PGS_YEAR=".$year;
$result2=mysql_query($query2);
$row2=@mysql_fetch_array($result2);
$NB2=$row2["NB"];

// permettre de fermer les dispos pour le mois suivant
if ( $gardes == 1  and check_rights($_SESSION['id'],7)) {
   if ( $NB > 0 ) {
      echo "<table cellspacing=0 border=0 ><TR>
            <td><img src=images/warn.png><td>
            <td>La saisie des disponibilités pour ce mois est bloquée.";
            
        if  ( $NB == $NB2 )  
	      echo " <input type='button' value='ouvrir' name='ouvrir' 
				onclick=\"redirect('".$person."','".$month."','".$year."','ouvrir')\"
				title=\"Ouvrir la saisie des disponibilités par le personnel pour ".moislettres($month)." ".$year."\">";
	    echo "</td></tr></table>";
   }
   else if ( $NB == 0 ) {
      echo " <input type='button' value='fermer' name='fermer' 
	  		onclick=\"redirect('".$person."','".$month."','".$year."','fermer')\"
			  title=\"fermer la saisie des disponibilités par le personnel pour ".moislettres($month)." ".$year."\">";
   }
}
else if ( $NB > 0 ) {
 	echo "<table cellspacing=0 border=0 ><TR>
            <td ><img src=images/warn.png><td>
            <td >La saisie des disponibilités pour ce mois est bloquée.
			</td></tr></table>";
}


//=====================================================================
// calcul : quel est le mois prochain et combien de jours possède t'il
//=====================================================================
//nb de jours du mois
$d=27;
while ( checkdate( $month, $d+1, $year) ) {
      	 $d=$d+1;
}

$query="select P_SECTION from pompier where P_ID=".$person;
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$P_SECTION=$row["P_SECTION"];

$disabled='disabled';

if ( check_rights($_SESSION['id'], 10, $P_SECTION )) $disabled="";
elseif ( $person == $_SESSION['id'] ) {
       if (
	        ((date("n") <= $month)  and (date("Y") == $year))
		    or 
			(date("n") == 12 and $month==1 and date("Y") == $year -1
		   )
		 ) $disabled="";
       // si le tableau de garde est disponible, alors on ne peut plus modifier les dispos
	   if (( $NB > 0 ) and ( $gardes == 1 )) $disabled='disabled';
       
}

//=====================================================================
// affiche le tableau
//=====================================================================

echo "<form name=dispo action='save_dispo.php'>";

echo "Tout cocher : ";
echo "J <input type=\"checkbox\" name=\"CheckAllJ\" onclick=\"CheckAll('J',this.checked);\" $disabled />";
echo "N <input type=\"checkbox\" name=\"CheckAllN\" onclick=\"CheckAll('N',this.checked);\" $disabled />";

$i=1;
echo "<input type='hidden' name='nbjours' value=$d size='20'>";
echo "<input type='hidden' name='person' value=$person size='20'>";
echo "<input type='hidden' name='month' value=$month size='20'>";
echo "<input type='hidden' name='year' value=$year size='20'>";
while ( $i <= $d ) {
      echo "<input type='hidden' name='J".$i."' value='0' size='20'>";
      echo "<input type='hidden' name='N".$i."' value='0' size='20'>";
      $i=$i+1;
}
echo "<p><table>
       <tr>
        <td class='FondMenu'>";
echo "
<table cellspacing=0 border=0 >
    <tr height=10>
      <td width='50' class=TabHeader>Lu</font></td>
      <td bgcolor=$mydarkcolor width='0'></td>
      <td width='50' class=TabHeader>Ma</td>
      <td bgcolor=$mydarkcolor width='0'></td>
      <td width='50' class=TabHeader>Me</td>
      <td bgcolor=$mydarkcolor width='0'></td>
      <td width='50' class=TabHeader>Je</td>
      <td bgcolor=$mydarkcolor width='0'></td>
      <td width='50' class=TabHeader>Ve</td>
      <td bgcolor=$mydarkcolor width='0'></td>
      <td width='50' class=TabHeader>Sa</td>
      <td bgcolor=$mydarkcolor width='0'></td>
      <td width='50' class=TabHeader>Di</td>
    </tr>
";

$l=1;
$i=1;
// le mois commence par un $jj
$jj=date("w", mktime(0, 0, 0, $month,$i,  $year));
$i=1;$k=$i;
if ( $jj == 0 ) $jj=7; // on affecte 7 au dimanche, (lundi=1)

while ( $l <= 6 ) { // boucle des semaines
  echo "\n    <tr height=20 >\n";
      // cases vides en début de mois
      while ( $k < $jj ) {
      	    echo "<td width='50' bgcolor=$mylightcolor >
     	    	  <table cellspacing=0 border=0>
	  	      <tr height=30 >
    	 	      </tr>
		  </table>
	     	  </td>\n";
 	     if ( $k < 7 ) echo "<td bgcolor=$mydarkcolor  width='0'></td>\n";
    	     $k=$k+1;
      }
      
      // jours de 1 à $d variable $i
      while (( $jj <= 7 ) &&  ($i <= $d)) { // boucle des jours de la semaine
      	    $query="select D_JOUR, D_NUIT from disponibilite
      	    where P_ID=".$person."
      	    and D_DATE='".$year."-".$month."-".$i."'";
	    $result=mysql_query($query);
     	    $row=@mysql_fetch_array($result);
     	    $D_JOUR=$row['D_JOUR'];
     	    $D_NUIT=$row['D_NUIT'];
	    if ( $D_JOUR == 1 )  $J_check = "checked";  else  $J_check = "unchecked" ;
	    if ( $D_NUIT == 1 )  $N_check = "checked";  else  $N_check = "unchecked" ;
	    if (is_we($month,$i,$year) ) $mycolor="#FFFF99" ; else  $mycolor="#FFFFFF" ;
	    if (($nbsections == 3 ) and ( get_section_pro_jour(1,$year, $month, $i) == $P_SECTION )) $mycolor="#00CC00";
	    if ( is_out($person, $year, $month, $i) <> 0 ) $mycolor="#FF0000";	
	    echo "<td width='50' bgcolor=$mycolor>
     	    	 <table cellspacing=0 border=0>
	  	      <tr height=10>
		      	  <td width='25' align=right><b>".$i." </b></td>
 	   		    <td width='25' ></td>
		      </tr>
	    	      <tr height=20>
                      	  <td width='25' align=center><font size=1>j<br><input type='checkbox' name='J".$i."' value='1' onClick='updateTotalJ(this)' $disabled $J_check></font></td>
	    	      	  <td width='25' align=center><font size=1>n<br><input type='checkbox' name='N".$i."' value='1' onClick='updateTotalN(this)' $disabled $N_check></font></td>
	              </tr>
		  </table>
	     	  </td>\n";
	     if ( $jj < 7 ) echo "<td bgcolor='$mydarkcolor'  width='0'></td>\n";
    	 $jj=$jj+1;
    	 $i=$i+1;
     }
     // cases vides en fin de tableau
     while (( $i <= ( 7 * $l +1 ) - $k ) && ( $i > $d )) {
      	    echo "<td width='50' bgcolor=$mylightcolor >
     	    	 <table cellspacing=0 border=0>
	  	      <tr height=30>
    	 	      </tr>
		  </table>
	     	  </td>\n";
	     if ( date("w", mktime(0, 0, 0, $month,$i,  $year)) <> 0 )	
		 	echo "<td bgcolor=$mydarkcolor  width='0'></td>\n";
    	 $i=$i+1;
      }

     echo "    </tr>\n";
     if ( $i > $d ) $l=7;
     else $l=$l+1;
     $jj=1;

	if ( $l <= 6 ) echo "<tr height=1><td bgcolor=$mydarkcolor width=350 colspan=13></td></tr>\n";	
}

echo "</table>";
echo "</td></tr></table>";

$query="select count(*) as totalJ from disponibilite
        where P_ID=".$person."
	and D_DATE>='".$year."-".$month."-01'
 	and D_DATE<='".$year."-".$month."-".$d."'
        and D_JOUR=1";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$totalJ=$row['totalJ'];

$query="select count(*) as totalN from disponibilite
        where P_ID=".$person."
	and D_DATE>='".$year."-".$month."-01'
 	and D_DATE<='".$year."-".$month."-".$d."'
        and D_NUIT=1";
$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$totalN=$row['totalN'];

echo "<p><table><tr><td>";

echo "<table>
       <tr>
        <td class='FondMenu'>";
echo "<table width=160 border=0 cellspacing=0><tr>";

echo "<tr>
    <td width=200 class=TabHeader colspan=2>Total mensuel</td>";
echo "</tr>";

echo "<tr bgcolor=$mylightcolor>
      <td width=100 >Jours <input class=num name=totalJ value=$totalJ onFocus=this.blur() size=2></td>
      <td width=100 >Nuits <input class=num name=totalN value=$totalN onFocus=this.blur() size=2></td>";


if ( $nbsections == 3 ) {
echo "<tr height=10>
    <td align=center width=100 bgcolor=$mylightcolor>
          	  <table width=25 cellspacing=0 border=1 bgcolor=$mydarkcolor>
          <tr height=12>
              <td bgcolor=#00CC00 ></td>
          </tr>
          </table>
	</td>
    <td width=100 bgcolor=$mylightcolor><font size=1> Section de garde </font></td>";
}
echo "</tr>";
echo "<tr height=10>
    <td align=center width=100 bgcolor=$mylightcolor>
          	  <table width=25 cellspacing=0 border=1 bgcolor=$mydarkcolor>
          <tr height=12>
              <td bgcolor=#FF0000 ></td>
          </tr>
          </table>
	</td>
    <td width=100 bgcolor=$mylightcolor><font size=1> Absent </font></td>";
echo "</tr>";
echo "<tr height=10>
    <td align=center width=100 bgcolor=$mylightcolor>
          	  <table width=25 cellspacing=0 border=1 bgcolor=$mydarkcolor>
          <tr height=12>
              <td bgcolor=#FFFF99 ></td>
          </tr>
          </table>
	</td>
    <td width=100 bgcolor=$mylightcolor><font size=1> WE/Férié </font></td>";
echo "</tr>";
echo "<tr height=10>
    <td align=center width=100 bgcolor=$mylightcolor>
          	  <table width=25 cellspacing=0 border=1 bgcolor=$mydarkcolor>
          <tr height=12>
              <td bgcolor=#FFFFFF ></td>
          </tr>
          </table>
	</td>
    <td width=100 bgcolor=$mylightcolor><font size=1> Semaine </font></td>";
echo "</tr>";

echo "</table>";
echo "</td></tr></table>";

echo "</td><td>";

// la personne habilitée peut valider les dispos
if ( $disabled == "") {
     	  echo "<td align=center> <input type='submit' value='Valider'></td>";
}

echo "</td></tr></table>";
echo "</form></div>";


?>
  
