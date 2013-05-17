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
$id=$_SESSION['id'];
get_session_parameters();
$possibleorders= array('GRADE','NOM');

if ( ! in_array($order, $possibleorders) or $order == '' ) $order='NOM';
writehead();

if ( isset($_GET["from"]))$from=$_GET["from"];
else $from="default";

if (check_rights($id, 18)) $granted_param=true;
else $granted_param=false;
?>
<script type='text/javascript' src='checkForm.js'></script>
<script type='text/javascript' src='popupBoxes.js'></script>
<script language="JavaScript">
function displaymanager(p1,p2,p3,p4,p5,p6){
	self.location.href="qualifications.php?pompier="+p1+"&order="+p2+"&filter="+p3+"&typequalif="+p4+"&subsections="+p5+"&from="+p6;
	return true
}

function displaymanager2(p1,p2,p3,p4,p5,p6){
  	if (p5.checked) s = 1;
 	else s = 0;
	url="qualifications.php?pompier="+p1+"&order="+p2+"&filter="+p3+"&typequalif="+p4+"&subsections="+s+"&from="+p6;
	self.location.href=url;
	return true
}

function displaymanager3(p1,p2,p3){
	self.location.href="qualifications.php?pompier="+p1+"&typequalif="+p2+"&from=personnel&from="+p3;
	return true
}

function redirect1(pid) {
     url="upd_personnel.php?pompier="+pid+"&from=qualif";
     self.location.href=url;
}

function redirect2() {
     url="qualifications.php?pompier=0";
     self.location.href=url;
}

</script>
<?php
include_once ("config.php");

if (isset ($_GET["pompier"])) $pompier=$_GET["pompier"];
else $pompier=0;
$MYP_ID=intval($pompier);

echo "<body>";

$title="Compétences";

// ===============================================
// listes déroulantes de choix
// ===============================================
if ( $MYP_ID == 0 ) {
	$query2="select e.EQ_ID, p.PS_ID, p.TYPE, p.DESCRIPTION as COMMENT
         from poste p, equipe e
	 	 where p.EQ_ID=e.EQ_ID
         and e.EQ_ID=".$typequalif."	
         order by e.EQ_ID, p.PS_ID"; 
         
	$result2=mysql_query($query2);
	$num_postes = mysql_num_rows($result2);
	
	$querycnt="select count(*) as NB";
	
	$query1="select distinct P_ID , P_NOM , P_PRENOM, P_GRADE, P_STATUT, P_SECTION";
	$queryadd = " from pompier, grade
	 where P_GRADE=G_GRADE
	 and P_NOM <> 'admin' 
	 and P_OLD_MEMBER = 0
	 and P_STATUT <> 'EXT'";

	if ( $subsections == 1 ) {
  	   	$queryadd .= "\nand P_SECTION in (".get_family("$filter").")";
	}
	else {
  	   	$queryadd .= "\nand P_SECTION =".$filter;
	}
      
	$querycnt .= $queryadd;
	$query1 .= $queryadd;
	if ( $order=="NOM" ) {
   		$query1 .= "\norder by P_NOM";
	}
	else {
   		$query1 .= "\norder by G_LEVEL desc";
	}

	$resultcnt=mysql_query($querycnt);
	$rowcnt=@mysql_fetch_array($resultcnt);
	$number = $rowcnt[0];
	
	echo "<div align=center><font size=4><b>$title du personnel</b></font><i> ($number personnes)</i><p>";
	if ($nbsections == 1 ) {
		echo "<table cellspacing=0 border=0 >";
		echo "<tr>";
	}
	else {
		echo "<table cellspacing=0 border=0 >";
		echo "<tr>";
		echo "<td>".choice_section_order('qualifications.php')."</td>";
		// choix de la section
		echo "<td align=left><select id='filter' name='filter' 
		onchange=\"displaymanager('0','".$order."',document.getElementById('filter').value,'".$typequalif."','".$subsections."','".$from."')\">";
	  	display_children2(-1, 0, $filter, $nbmaxlevels, $sectionorder);
	  	echo "</select></td> ";
	  	if ( get_children("$filter") <> '' ) {
	  		if ($subsections == 1 ) $checked='checked';
	  		else $checked='';
	  		echo "<td align=center width=100><input type='checkbox' name='sub' $checked 
	   		onClick=\"displaymanager2('0','".$order."','".$filter."',document.getElementById('typequalif').value, this,'".$from."')\"/>
	   		<font size=1>inclure les<br>sous sections</td>";
		}
	}
	// choix type de garde / de compétence
	echo "<td align=right><select id='typequalif' name='typequalif' 
		onchange=\"displaymanager('0','".$order."','".$filter."',document.getElementById('typequalif').value,'".$subsections."','".$from."')\">";
	$query3="select EQ_ID, EQ_NOM from equipe";

	echo "<option value='0'>Tous types (excel seulement)</option>";
	$result3=mysql_query($query3);
	while ($row3=@mysql_fetch_array($result3)) {
    	$EQ_ID=$row3["EQ_ID"];
    	$EQ_NOM=$row3["EQ_NOM"];
    	    	if ($EQ_ID == $typequalif ) $selected='selected';
    	else $selected='';
    	echo "<option value='".$EQ_ID."' $selected>".$EQ_NOM."</option>\n";
	}
	echo "</select></td>";
	echo "<td><img src='images/xls.jpg' id='StartExcel' height='24' border='0' alt='Excel' title='Excel' onclick=\"window.open('qualifications_xls.php?filter=$filter&typequalif=$typequalif&subsections=$subsections')\" class='noprint' align='right' /></td>";
	echo "</tr>";
	
	echo "<tr><td colspan=4>";
	// ====================================
	// pagination
	// ====================================
	require_once('paginator.class.php');
	$pages = new Paginator;  
	$pages->items_total = $number;  
	$pages->mid_range = 9;  
	$pages->paginate();  
	if ( $number > 10 ) {
		echo $pages->display_pages();
		echo $pages->display_jump_menu(); 
		echo $pages->display_items_per_page(); 
		$query1 .= $pages->limit;
	}
	$result1=mysql_query($query1);
	$numberrows=mysql_num_rows($result1);

	echo "</td></tr></table>";
	
}
else {
	echo "<div align=center><font size=4>
			<b>$title pour: ".strtoupper(get_nom($MYP_ID))." ".my_ucfirst(get_prenom($MYP_ID))." <br></b></font>";
}

// ===============================================
// tout le personnel - read only
// ===============================================

if ( $MYP_ID == 0 ) { // mode display all

	$query_k="select e.EQ_ID, e.EQ_NOM, count(*) as EQNB from poste  p, equipe e
	    where e.EQ_ID=p.EQ_ID";
	if ($typequalif <> 0 ) $query_k .= " and e.EQ_ID=".$typequalif;
    $query_k .= " group by e.EQ_ID, e.EQ_NOM";
	$result_k=mysql_query($query_k);

	if ($typequalif == 0) {
		write_msgbox("Erreur", $warning_pic, "Le nombre de compétences est trop élevé. Seul la page excel peut être affichée.<br>Ou choisissez un type de compétences.",10,0);
		exit;
	}
	echo "<tr ><td colspan=2 align=center>
		<img src=images/green.gif border=0> prioritaire <img src=images/blue.gif border=0> secondaire
			</td></tr></table><p>";

	echo "<table cellspacing=0 border=0>";

	// ===============================================
	// premiere ligne du tableau
	// ===============================================

	echo "\n<tr>";
	echo "<td bgcolor=#FFFFFF width=0></td>";
	if ( $grades == 1 )echo "<td bgcolor=#FFFFFF colspan=2></td>";
	else echo "<td bgcolor=#FFFFFF></td>";

	while ($row_k=@mysql_fetch_array($result_k)) {
      	    $EQ_NOM=$row_k["EQ_NOM"];
            $EQNB=$row_k["EQNB"] * 2;
  	    echo "<td bgcolor=$mydarkcolor colspan=$EQNB align=center>
		  	<font color=#FFFFFF><b>$EQ_NOM</b></td>";
	}
	echo "</tr>";

	echo "<tr>
      <td bgcolor=$mydarkcolor width=0></td>
      <td bgcolor=$mydarkcolor width=180 >
          <a href=qualifications.php?pompier=0&order=NOM&filter=$filter&typequalif=$typequalif class=TabHeader>Nom</a></td>";

	if ( $grades == 1 ) {         
	echo "<td bgcolor=$mydarkcolor width=70>
		<a href=qualifications.php?pompier=0&order=GRADE&filter=$filter&typequalif=$typequalif class=TabHeader>Grade</a></td>";
	}

	while ($row2=@mysql_fetch_array($result2)) {
      $TYPE=$row2["TYPE"];
      $PS_ID=$row2["PS_ID"];
      $COMMENT=strip_tags($row2["COMMENT"]);
      if ( $granted_param ) 
			echo "<td bgcolor=$mydarkcolor width=40 align=center ><a href=upd_poste.php?pid=$PS_ID title=\"$COMMENT\" class=TabHeader>$TYPE</a></td>";
	  else
	  		echo "<td bgcolor=$mydarkcolor width=40 align=center class=TabHeader title=\"$COMMENT\">$TYPE</td>";
	}
	echo "<td bgcolor=$mydarkcolor width=0></td></tr>";

	// ===============================================
	// le corps du tableau
	// ===============================================
	$i=0;
	while ($row=@mysql_fetch_array($result1)) {
      $P_ID=$row["P_ID"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_SECTION=$row["P_SECTION"];
      $P_NOM=$row["P_NOM"];
      $P_GRADE=$row["P_GRADE"];
      $P_STATUT=$row["P_STATUT"];

      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor="$mylightcolor";
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      if ( check_rights($_SESSION['id'], 4, $P_SECTION)) {
	    // ligne avec lien pour modifier
	    echo "<tr bgcolor=$mycolor onMouseover=\"this.bgColor='yellow'\" 
			onMouseout=\"this.bgColor='$mycolor'\" onclick=\"this.bgColor='#33FF00'; 
			displaymanager($P_ID,'".$order."','".$filter."','".$typequalif."','".$subsections."','".$from."')\">";
      }
      else {
	   // ligne sans lien pour modifier
      	   echo "<tr bgcolor=$mycolor>";
      }
      echo "<td bgcolor=$mydarkcolor width=0></td>";
      
      echo "<td width=180><b><a href=upd_personnel.php?pompier=".$P_ID.">".strtoupper($P_NOM)." ".my_ucfirst($P_PRENOM)."</a></b>
	  		</font></td>";
      if ( $grades == 1 ) {  
	  	echo "<td width=70>".$P_GRADE."</td>";		
      }
	  $result2=mysql_query($query2);
      
      while ($row2=@mysql_fetch_array($result2)) {
      	    $PS_ID=$row2["PS_ID"];
      	    $query3="select Q_VAL, Q_EXPIRATION,  DATEDIFF(Q_EXPIRATION,NOW()) as NB 
			  	from qualification where PS_ID=".$PS_ID." and P_ID=".$P_ID;	
	        $result3=mysql_query($query3);
	        if (mysql_num_rows($result3) > 0) {
	            $row3=@mysql_fetch_array($result3);
                 $Q_VAL=$row3["Q_VAL"];
	           if ( $Q_VAL == 1 ) {
	       	      $mypic="<img src=images/green.gif border=0 title='prioritaire'>"; $selected1="selected"; $selected2="";
	           }
	           if ( $Q_VAL == 2 ) {
   	       	      $mypic="<img src=images/blue.gif border=0 title='secondaire'>";  $selected1=""; $selected2="selected";
	           }
	           $selected0="";
	           $Q_EXPIRATION=$row3["Q_EXPIRATION"];
			   $NB=$row3["NB"];
			   if ( $Q_EXPIRATION <> '') {
					if ($NB < 61) $mypic="<img src=images/yellow.gif title='expiration dans moins de 2 mois' border=0>";
 					if ($NB <= 0) $mypic="<img src=images/red.gif title='date expiration dépassée' border=0>";
 				}
     	    }
            else {
	       	    $mypic="" ; $selected0="selected"; $selected1=""; $selected2="";
	        }
   	        echo "<td width=40 align=center>
                         $mypic
		    </td>";
      }
      if ($MYP_ID <> 0) echo "</form>";
      echo "<td bgcolor=$mydarkcolor width=0></td></tr>";
	}


	// ===============================================
	// le bas du tableau
	// ===============================================
	echo "<tr>
      <td bgcolor=$mydarkcolor width=0></td>";

	echo "<td bgcolor=$mydarkcolor width=180 align=right class=TabHeader>
		<b>Total: </b></font></td>";
	if ( $grades == 1 ) echo "<td bgcolor=$mydarkcolor width=70></td>";
	$result2=mysql_query($query2);

	while ($row2=@mysql_fetch_array($result2)) {
      $TYPE=$row2["TYPE"];
      $PS_ID=$row2["PS_ID"];
      $COMMENT=strip_tags($row2["COMMENT"]);
      if ( $granted_param ) 
			echo "<td bgcolor=$mydarkcolor width=40 align=center ><a href=upd_poste.php?pid=$PS_ID title=\"$COMMENT\" class=TabHeader>$TYPE</a></td>";
	  else
	  		echo "<td bgcolor=$mydarkcolor width=40 align=center class=TabHeader title=\"$COMMENT\">$TYPE</td>";
	}
	echo "<td bgcolor=$mydarkcolor width=0></td></tr>";


	echo "<tr>
      <td bgcolor=$mydarkcolor width=0></td>
      <td bgcolor=$mydarkcolor width=130></td>";
	if ( $grades == 1 ) {  
		echo "<td bgcolor=$mydarkcolor width=70></td>";
	}
	$result2=mysql_query($query2);
	while ($row2=@mysql_fetch_array($result2)) {
      $PS_ID=$row2["PS_ID"];
      $query="select count(1) as NB 
	         from qualification q, pompier p 
	  		 where q.PS_ID=".$PS_ID." 
			 and p.P_ID=q.P_ID
			 and P_OLD_MEMBER = 0
	 		 and P_STATUT <> 'EXT'";
      if ( $subsections == 1 ) 
  	   			$query .= "\nand P_SECTION in (".get_family("$filter").")";
	  else 
  	   			$query .= "\nand P_SECTION =".$filter;
      $result=mysql_query($query);
      $row=@mysql_fetch_array($result);
      $NB=$row["NB"];
      echo "<td bgcolor=$mydarkcolor width=40 align=center><font color=#FFFFFF>
	  		<b>$NB</b></font></td>";
	}
	echo "<td bgcolor=$mydarkcolor width=0></td></tr>";
	echo "</table></div>";
}

// ===============================================
// une personne - modification
// ===============================================

else { // mode update one

// permission de modifier les compétences?
$competence_allowed=false;
$query="select distinct F_ID from poste order by F_ID";
$result=mysql_query($query);
while ($row=@mysql_fetch_array($result)) {
	if (check_rights($_SESSION['id'], $row['F_ID'], get_section_of("$MYP_ID")) ) {
		$competence_allowed=true;
		break;
	}
}
if ( $competence_allowed )  $disabled_base='';
else $disabled_base='disabled';


if ( $_SESSION['SES_BROWSER'] == "IE 6.0") $ie6 = true;
else $ie6 = false;

if ($ie6 and $typequalif == 0) $typequalif=1;

echo "<form name = 'chqualif' id='chqualif' action='save_qualif.php'>";	
// choix type de garde / de compétence
echo "<p> Type de compétences <select id='filter_one' name='filter_one' 
		onchange=\"displaymanager3('".$MYP_ID."', document.getElementById('filter_one').value,'".$from."')\">";
$query3="select EQ_ID, EQ_NOM from equipe";

if ( ! $ie6 ) echo "<option value='0'>Tous types</option>";
$result3=mysql_query($query3);
while ($row3=@mysql_fetch_array($result3)) {
    $EQ_ID=$row3["EQ_ID"];
    $EQ_NOM=$row3["EQ_NOM"];
    if ($EQ_ID == $typequalif ) $selected='selected';
    else $selected='';
    echo "<option value='".$EQ_ID."' $selected>".$EQ_NOM."</option>\n";
}
echo "</select>";


echo "<input name='pompier' type='hidden' value=".$MYP_ID.">";
echo "<input name='order' type='hidden' value=".$order.">";
echo "<input name='filter' type='hidden' value=".$filter.">";
echo "<input name='from' type='hidden' value=".$from.">";

echo "<p><table >";
echo "<tr>
<td class='FondMenu'>";
echo "<table cellspacing=0 border=0>";

$queryn="select count(*) as NB from poste where PS_USER_MODIFIABLE = 1";
$resultn=mysql_query($queryn);
$rown=@mysql_fetch_array($resultn);
$n=$rown["NB"];

$OLDEQ_NOM="NULL";
$query2="select e.EQ_ID, e.EQ_NOM, p.PS_ID, TYPE, p.DESCRIPTION, p.PS_EXPIRABLE, p.F_ID,
		 p.PS_USER_MODIFIABLE, e.EQ_TYPE
         from equipe e, poste p
	 	 where e.EQ_ID=p.EQ_ID";
if (($disabled_base == 'disabled') and ($n > 0))
	$query2 .=" and p.PS_USER_MODIFIABLE = 1";
if ( $typequalif > 0 ) $query2 .=" and e.EQ_ID=".$typequalif;	
$query2 .=" order by e.EQ_ID, p.PS_ID";

$result2=mysql_query($query2);
while ($row2=@mysql_fetch_array($result2)) {
	$EQ_NOM=$row2["EQ_NOM"];
 	$PS_ID=$row2["PS_ID"];
 	$TYPE=$row2["TYPE"];
 	$EQ_TYPE=$row2["EQ_TYPE"];
 	$DESCRIPTION=strip_tags($row2["DESCRIPTION"]);
 	$PS_EXPIRABLE=$row2["PS_EXPIRABLE"];
 	$PS_USER_MODIFIABLE=$row2["PS_USER_MODIFIABLE"];
 	$F_ID=$row2["F_ID"];
	  	
 	$query3="select Q_VAL, DATE_FORMAT(Q_EXPIRATION, '%d/%m/%Y') as Q_EXPIRATION,  
	 		DATEDIFF(Q_EXPIRATION,NOW()) as NB
	 		from qualification where P_ID=".$MYP_ID." and PS_ID=".$PS_ID;
 	$result3=mysql_query($query3);
 	$row3=@mysql_fetch_array($result3);
 	$checked1='';$checked2='';$checked0='';
 	if ($row3["Q_VAL"] == 1 ) {
	 	$checked1='checked';
	 	$myimg="<img src=images/green.gif title='prioritaire'>";
	}
 	else if ($row3["Q_VAL"] == 2 ) {
	 	$checked2='checked';
	 	$myimg="<img src=images/blue.gif title='secondaire'>";
	}
 	else {
	 	$checked0='checked';
	 	$myimg="";
	}
	$Q_EXPIRATION=$row3["Q_EXPIRATION"];
	if ( $Q_EXPIRATION == '00/00/0000' ) $Q_EXPIRATION='';
	$NB=$row3["NB"];
	if ( $Q_EXPIRATION <> '') {
		if ($NB < 61) $myimg="<img src=images/yellow.gif title='expiration dans moins de 2 mois'>";
 		if ($NB <= 0) $myimg="<img src=images/red.gif title='date expiration dépassée'>";
 	}
 	if ( $EQ_NOM <> $OLDEQ_NOM) {
 		$OLDEQ_NOM =  $EQ_NOM;
 		echo "<tr>
    		<td width=360 align=center class=TabHeader colspan=3 align=left>$EQ_NOM</td>
	    	<td width=60 align=center class=TabHeader >prioritaire</td>
			<td width=60 align=center class=TabHeader >secondaire</td>
			<td width=60 align=center class=TabHeader >non</td>";
		if ( $EQ_TYPE == 'COMPETENCE' )
			echo "<td width=100 align=center class=TabHeader >expiration</td>";
		else echo "<td width=100 class=TabHeader ></td>";
		echo "</tr>";	
 	}
 	
 	$disabled3='disabled';
 	if ( check_rights($id,$F_ID)) $disabled3='';
	
 	if ($row3["Q_VAL"] >= 1 ) $style="<b>";
 	else $style="";
 	
 	if (( $PS_USER_MODIFIABLE == 1 ) and ( $MYP_ID == $id )) {
	  	$disabled = '';
	  	$disabled3= '';
	}
	else $disabled=$disabled_base;
	
	echo "<tr>  	
			 <td bgcolor=$mylightcolor width=20>$myimg</td> 
			 <td bgcolor=$mylightcolor width=60 align=left>".$style.$TYPE."</td>
      	     <td bgcolor=$mylightcolor width=350 align=left>".$style.$DESCRIPTION."</font></td>";
			   
			echo "<td bgcolor=$mylightcolor align=center>";
			if (( $EQ_TYPE == 'COMPETENCE' ) and ( $PS_EXPIRABLE == 1 ))
			 	echo "<input type='radio' name='$PS_ID' value='1' $checked1 $disabled $disabled3
				 	onClick=\"chqualif.exp_".$PS_ID.".disabled=false\";>";
			else echo "<input type='radio' name='$PS_ID' value='1' $checked1 $disabled $disabled3>";
			echo "</td>";
			
			echo " <td bgcolor=$mylightcolor align=center>";
			if (( $EQ_TYPE == 'COMPETENCE' ) and ( $PS_EXPIRABLE == 1 ))
			 	echo "<input type='radio' name='$PS_ID' value='2' $checked2 $disabled $disabled3
				 	onClick=\"chqualif.exp_".$PS_ID.".disabled=false\";>";
			else echo "<input type='radio' name='$PS_ID' value='2' $checked2 $disabled $disabled3>";
			echo "</td>";

			echo " <td bgcolor=$mylightcolor align=center>";
			if (( $EQ_TYPE == 'COMPETENCE' ) and ( $PS_EXPIRABLE == 1 ))
				echo "<input type='radio' name='$PS_ID' value='0' $checked0 $disabled $disabled3
						onClick=\"chqualif.exp_".$PS_ID.".disabled=true\";>";
			else echo "<input type='radio' name='$PS_ID' value='0' $checked0 $disabled $disabled3>";
			echo "</td>";
			
			if ( $disabled3 == 'disabled' )
				echo "<input type=hidden name='".$PS_ID."' value='".$row3["Q_VAL"]."'>";
				
			if ( $EQ_TYPE == 'COMPETENCE' ) {
			 	echo " <td bgcolor=$mylightcolor align=center>";
				if ( $PS_EXPIRABLE == 1 ) {
				 	$disabled2='disabled';
				 	if ( $disabled == '' ) {
				 	 	if ( $checked0 == '' ) 	$disabled2='';
				 	}
					echo " <input type=text size=10 maxlength=10 name='exp_".$PS_ID."' height=8
						value='".$Q_EXPIRATION."' title='JJ/MM/AAAA' 
						onchange='checkDate(chqualif.exp_".$PS_ID.")'
						$disabled2 $disabled3>";
				}
				else {
				   echo "<input type=hidden name='exp_".$PS_ID." value=''>";
				}
				echo " </td>";	
			}
			else echo "<td bgcolor=$mylightcolor></td>";	
       echo "</tr>";
}

echo "</tr></table>";
echo "</td></tr></table>";
if ( $disabled_base == 'disabled' ) echo "<font size=1><i>Attention seules les compétences que vous avez le droit de modifier apparaissent</i></font><br>";
if (( $disabled_base == '' ) or ($n > 0)) echo "<input type='submit' value='sauver'>";
if ( $from == 'personnel' )
echo "<input type='button' value='Retour' name='Retour' onclick=\"javascript:redirect1(".$MYP_ID.");\">";
else
echo "<input type='button' value='Retour' name='Retour' onclick=\"javascript:redirect2();\">";
}
?>
