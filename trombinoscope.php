<?php

  # written by: Nicolas MARCHE, Jean-Pierre KUNTZ
  # contact: nico.marche@free.fr
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
$possibleorders= array('G_LEVEL','P_PHOTO','P_STATUT','P_NOM','P_PRENOM','P_SECTION','P_DEBUT','P_END','C_NAME');
if ( ! in_array($order, $possibleorders) or $order == '' ) $order='P_NOM';

$fixed_company = false;
if ( $category == 'EXT' ) {
	if (! check_rights($_SESSION['id'], 37)) {
		check_all(45);
		$company=$_SESSION['SES_COMPANY'];
		$_SESSION['company'] = $company;
		$fixed_company = true;
	}
} 
else check_all(40);

$ischef=is_chef($id,$filter);

if ( isset($_GET["position"])) $position=$_GET["position"];
else $position='actif';

if ( isset($_GET["category"])) $category=$_GET["category"];
else $category='interne';

$disabled="disabled";
$envoisEmail=false;
writehead();
?>
<script type='text/javascript' src='popupBoxes.js'></script>
<script language="JavaScript">

function orderfilter(p1,p2,p3,p4,p5,p6){
	 self.location.href="trombinoscope.php?order="+p1+"&filter="+p2+"&subsections="+p3+"&position="+p4+"&category="+p5+"&company="+p6;
	 return true
}

function orderfilter2(p1,p2,p3,p4,p5,p6){
 	 if (p3.checked) s = 1;
 	 else s = 0;
	 self.location.href="trombinoscope.php?order="+p1+"&filter="+p2+"&subsections="+s+"&position="+p4+"&category="+p5+"&company="+p6;
	 return true
}

function displaymanager(p1){
	 self.location.href="upd_personnel.php?pompier="+p1;
	 return true
}

function bouton_redirect(cible) {
	 self.location.href = cible;
}

</script>
<?php
echo "</head>";
include_once ("config.php");

echo "<body>";

$querycnt="select count(*) as NB";
$query1="select distinct P_ID, P_CODE , P_NOM , P_PRENOM, P_HIDE, P_SEXE, pompier.C_ID, company.C_NAME, 
		P_GRADE, P_STATUT, P_DEBUT, P_SECTION, P_PHONE, P_PHONE2, S_CODE, section.S_ID, P_EMAIL, P_PHOTO";
         
$queryadd = " from pompier , grade, section, company 
	 where P_GRADE=G_GRADE
	 and company.C_ID = pompier.C_ID
	 and P_PHOTO is not null
	 and P_SECTION=section.S_ID
	 and P_NOM <> 'admin' ";

if ( $company >=0 ) $queryadd .= " and company.C_ID = $company";

if ( $category == 'EXT' ) {
	$queryadd .= " and P_STATUT = 'EXT'";
	$mylightcolor=$mygreencolor;
	$title='Photos du personnel extérieur';
}
else if ( $position == 'actif' ) {
	$queryadd .= " and P_OLD_MEMBER = 0 and P_STATUT <> 'EXT'";
	$title='Photos du personnel actif';
}
else {
	$queryadd .= " and P_OLD_MEMBER > 0";
	$mylightcolor=$mygreycolor;
	$title='Photos des anciens membres';
}

if ( $nbsections <> 1) {
	if ( $subsections == 1 ) {
  	   $queryadd .= "\nand P_SECTION in (".get_family("$filter").")";
	}
	else {
  	   $queryadd .= "\nand P_SECTION =".$filter;
	}
}
$querycnt .= $queryadd;
$query1 .=$queryadd. " \norder by ". $order;
if ( $order == "G_LEVEL" or $order == "P_PHOTO")  $query1 .=" desc";	

$resultcnt=mysql_query($querycnt);
$rowcnt=@mysql_fetch_array($resultcnt);
$number = $rowcnt[0];

echo "<div align=center><font size=4><b>$title</b></font><i> ($number photos)</i>";
echo "<p><table cellspacing=5 border=0 >";
echo "<tr height=40>";

echo "<td><a href=personnel.php?position=".$position."&category=".$category.">
	<img src=images/list.png border=0 title='voir la liste du personnel'></a></td>";

if ($nbsections <> 1 ) {
	if ( $_SESSION['SES_STATUT'] == 'EXT' ) {
		echo "Section";
	}
	else {
    	echo "<td>".choice_section_order('trombinoscope.php')."</td>";
	}
	echo "<td><select id='filter' name='filter' 
		onchange=\"orderfilter('".$order."',document.getElementById('filter').value,'".$subsections."','".$position."','".$category."')\">";
	  display_children2(-1, 0, $filter, $nbmaxlevels, $sectionorder);
	echo "</select>";
	
	if ($nbsections == 0  ) {
		if ( $fixed_company ) $disabled='disabled';
		else $disabled='';
		echo "<br><select id='company' name='company' title='filtre par entreprise' $disabled
		onchange=\"orderfilter('".$order."','".$filter."','".$subsections."','".$position."','".$category."',document.getElementById('company').value)\">";	
		echo "<option value='-1' 'selected'>... Pas de filtre par entreprise ...</option>";	
		$treenode=get_highest_section_where_granted($_SESSION['id'],37);
		if ( $treenode == '' ) $treenode=$mysection;
		if ( check_rights($_SESSION['id'], 24) ) $treenode=$filter;
		echo companychoice("$treenode","$company");
		echo "</select>";	
	}
	
	echo "</td>";
	if ( get_children($filter) <> '' ) {
	  if ($subsections == 1 ) $checked='checked';
	  else $checked='';
	  echo "<td align=center><input type='checkbox' name='sub' $checked
	   onClick=\"orderfilter2('".$order."',document.getElementById('filter').value, this,'".$position."','".$category."','".$company."')\"/>
	   <font size=1>inclure les<br>sous sections</td>";
	}
}
if ( check_rights($_SESSION['id'], 1) ) {
   if ( $position == 'actif' ) {
        $querynb="select count(*) as NB from pompier";
    	$resultnb=mysql_query($querynb);
		$rownb=@mysql_fetch_array($resultnb);
		$nb = $rownb[0];
   		if ( $nb <= $nbmaxpersonnes )
   		echo "<td><input type='button' value='Ajouter' name='ajouter' 
		   	onclick=\"bouton_redirect('ins_personnel.php?suggestedsection=$filter');\">";
   		else
   			echo "<font color=red><b>Vous ne pouvez plus ajouter de personnel (maximum atteint: $nbmaxpersonnes)</b></font>";
   	}
}
echo "</td>";

echo "</tr><tr><td colspan=4>";
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


echo "<table cellspacing=0 border=0>";

$nbcols=5;
// ===============================================
// le corps du tableau
// ===============================================
$i=0;
while ($row=@mysql_fetch_array($result1)) {
	  if ( $i%$nbcols == 0 ) {
		 echo "</TR><TR>";
	  }
      $P_ID=$row["P_ID"];
      $C_NAME=$row["C_NAME"];
      $P_SEXE=$row["P_SEXE"];
      $S_ID=$row["S_ID"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_NOM=$row["P_NOM"];
      $P_GRADE=$row["P_GRADE"];
      $S_CODE=$row["S_CODE"];
	  $P_PHOTO=$row["P_PHOTO"];

      if ( $P_SEXE == 'F' ) $prcolor='purple';
      else $prcolor=$mydarkcolor;
      
      if(file_exists($trombidir."/".$P_PHOTO)) $img=$trombidir."/".$P_PHOTO;
	  else if ( $P_SEXE == 'M' )  $img = 'images/male.png';
      else $img = 'images/female.png';
      
      $name=strtoupper($P_NOM)." ".ucfirst($P_PRENOM);
      if ( $grades == 1 ) { 
      	$name=$P_GRADE." ".$name;
      }
      if ($nbsections <> 1 ) {
       	if ($category == 'EXT' ) $section="<br><i>".$C_NAME."</i>";
        else $section="<br><i>".$S_CODE."</i>";
	  }
	  else $section="";
      
      echo "<td>
	  		<table>
      			<tr><td onclick='displaymanager($P_ID)'><img src='".$img."' border=0 height=140></td>
				</tr>
      			<tr><td onclick='displaymanager($P_ID)'><font color=$prcolor>
		  			".$name.$section."</font></td>
      			</tr>
      		</table>
      		</td>";
      $i++;
}
echo "</table>";
?>
