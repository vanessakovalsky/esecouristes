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

if (isset($_GET["print"])) $print=mysql_real_escape_string($_GET["print"]);
else $print='NO';
if (isset($_GET["equipe"])) $equipe=mysql_real_escape_string($_GET["equipe"]);
else $equipe=1;

writehead();
?>
<SCRIPT>
function redirect(equipe) {
	 url="grille_depart.php?equipe="+equipe;
	 self.location.href = url;
}

function displaymanager(p1){
	 self.location.href="upd_vehicule.php?vid="+p1;
	 return true
}

</SCRIPT>
<?php

function intercase() {
global $print, $mydarkcolor;
	 if ( $print == "NO" ) {
	    echo "<td bgcolor=$mydarkcolor width=0></td>";
         }
}

if ( $print == "YES" ) {
   echo "<body onload='javascript:window.print()'>";
}
else {
   echo "<body>";
}

echo "<div align=center><font size=4><b>Grille de départ par défaut</b><br>";

if ( $print <> 'YES' ) {
	//choix type de garde
	$query="select distinct e.EQ_ID, e.EQ_NOM from equipe e, poste p 
			where e.EQ_ID=p.EQ_ID
			and e.EQ_TYPE='GARDE'";
	echo "<table><tr><td>type de garde
	                 <select id='equipe' name='equipe' 
					 onchange='redirect(document.getElementById(\"equipe\"	).value)'>";
	$result=mysql_query($query);
	while ($row=@mysql_fetch_array($result)) {
             $EQ_ID=$row["EQ_ID"];
             $EQ_NOM=$row["EQ_NOM"];
     	     echo "<option value='".$EQ_ID."'";
      	     if ($EQ_ID == $equipe ) echo " selected ";
      	     echo ">".$EQ_NOM."</option>\n";
	}
	echo "</select></td>";


	echo"      <td><a href=grille_depart.php?print=YES target=_blank>
		<img src=images/printer.gif width=22 border=0 alt='imprimer la grille'></a></td>
      </tr></table>";
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
   echo "<p><table border=1 cellspacing=0 cellpadding=0 bordercolor=$mydarkcolor>";
}

// ===============================================
// premiere ligne du tableau
// ===============================================

echo "<tr height=15 class=TabHeader>";
echo "<td width=100 align=center>Type</td>";
echo  "<td width=90>Véhicule</td>";
intercase();
while ($rowp=@mysql_fetch_array($resultp)) {
      $PS_ID=$rowp["PS_ID"];
      $TYPE=$rowp["TYPE"];
      $DESCRIPTION=$rowp["DESCRIPTION"];
      echo "<td bgcolor=$mydarkcolor width=100 align=center><font size=1><a href=upd_poste.php?pid=$PS_ID class=TabHeader>$DESCRIPTION</a></font></td>";
}
echo "</tr>";

// ===============================================
// 2 lignes par jour (j / N)
// ===============================================
$oldusage="";
$query1="select distinct v.V_ID ,v.VP_ID, v.TV_CODE, v.V_MODELE, v.EQ_ID,vp.VP_LIBELLE, 
		tv.TV_LIBELLE, vp.VP_OPERATIONNEL, v.V_IMMATRICULATION, v.V_COMMENT, v.V_KM, 
		v.V_ANNEE, tv.TV_USAGE, s.S_ID, s.S_DESCRIPTION, 
		DATE_FORMAT(v.V_ASS_DATE, '%d-%m-%Y') as V_ASS_DATE,
		DATE_FORMAT(v.V_CT_DATE, '%d-%m-%Y') as V_CT_DATE,
		DATE_FORMAT(v.V_REV_DATE, '%d-%m-%Y') as V_REV_DATE
        from vehicule v, type_vehicule tv, vehicule_position vp, section s
		where v.TV_CODE=tv.TV_CODE
		and v.EQ_ID=$equipe
		and s.S_ID=v.S_ID
		and vp.VP_ID=v.VP_ID
		order by TV_USAGE desc, TV_CODE";

$result1=mysql_query($query1);
$number=mysql_num_rows($result1);
$i=0;
while ($row=@mysql_fetch_array($result1)) {
      $TV_CODE=$row["TV_CODE"];
      $V_COMMENT=$row["V_COMMENT"];
      $V_ID=$row["V_ID"];
      $VP_LIBELLE=$row["VP_LIBELLE"];
      $TV_LIBELLE=$row["TV_LIBELLE"];
      $VP_OPERATIONNEL=$row["VP_OPERATIONNEL"];
      $V_IMMATRICULATION=$row["V_IMMATRICULATION"];
      $V_COMMENT=$row["V_COMMENT"];
      $V_MODELE=$row["V_MODELE"];
      $EQ_ID=$row["EQ_ID"];
      $V_KM=$row["V_KM"];
      $V_ANNEE=$row["V_ANNEE"];
      $V_ASS_DATE=$row["V_ASS_DATE"];
      $V_CT_DATE=$row["V_CT_DATE"];
      $V_REV_DATE=$row["V_REV_DATE"];
      $V_MODELE=$row["V_MODELE"];
      $S_ID=$row["S_ID"];
      $S_DESCRIPTION=get_section_name($S_ID); 
      
      $i=$i+1;
      if ( $i%2 == 0 ) {
      	 $mycolor=$mylightcolor;
      }
      else {
      	 $mycolor="#FFFFFF";
      }
      if ( $VP_OPERATIONNEL == 1) $opcolor=$red;      
	  else if ( my_date_diff(getnow(),$V_ASS_DATE) < 0 ) {
	  		$opcolor=$red;
	  		$VP_LIBELLE = "assurance périmée";
	  }
	  else if ( my_date_diff(getnow(),$V_CT_DATE) < 0 ) {
	  		$opcolor=$red;
	  		$VP_LIBELLE = "CT périmé";	  
	  }
	  else if ( $VP_OPERATIONNEL == 2) {
	  	$opcolor=$orange;
	  }
	  else if (( my_date_diff(getnow(),$V_REV_DATE) < 0 ) and ( $VP_OPERATIONNEL <> 1)) {
	  	$opcolor=$orange;
		$VP_LIBELLE = "révision à faire";
	  }  
      else $opcolor=$green;
      
      if ( $row["TV_USAGE"] <> $oldusage ) {
        if ( $oldusage <> "" ) {
        	echo "<tr height=2>
				<td width=100 bgcolor=white>
				<td width=100 bgcolor=$mydarkcolor>";
			intercase();
        	$resultp=mysql_query($queryp);
        	while ($rowp=@mysql_fetch_array($resultp)) {
        		echo "<td width=100 bgcolor=$mydarkcolor>";
      		}
      	}
        echo "<tr height=10 bgcolor=$mycolor 
		onMouseover=\"this.bgColor='yellow'\" 
		onMouseout=\"this.bgColor='$mycolor'\" 
		onclick=\"this.bgColor='#33FF00'; displaymanager($V_ID)\" >";
	  	$TV_USAGE=$row["TV_USAGE"];
	  	$queryn="select count(*) as NB 
		  		 from vehicule v, type_vehicule tv
				 where v.TV_CODE=tv.TV_CODE
				 and v.EQ_ID=$equipe
				 and tv.TV_USAGE='".$TV_USAGE."'";
		$resultn=mysql_query($queryn);
		$rown=@mysql_fetch_array($resultn);
      	echo "<td width=100 rowspan=".$rown["NB"]." class=TabHeader>
		  	$TV_USAGE</td>";
	  }
	  else
	    echo "<tr height=10 bgcolor=$mycolor 
		onMouseover=\"this.bgColor='yellow'\" 
		onMouseout=\"this.bgColor='$mycolor'\" 
		onclick=\"this.bgColor='#33FF00'; displaymanager($V_ID)\" >";
		
      $oldusage=$row["TV_USAGE"]; 
      
      echo "<td width=100><font color=$opcolor><b>$TV_CODE $V_IMMATRICULATION</b></td>";
	  intercase();
      $resultp=mysql_query($queryp);
      while ($rowp=@mysql_fetch_array($resultp)) {
      	$PS_ID=$rowp["PS_ID"];
      
      	$query="select e.ROLE_ID, tvr.ROLE_NAME
      		  from equipage e, type_vehicule_role tvr
			  where e.V_ID=$V_ID
      		  and e.PS_ID=$PS_ID
			  and tvr.ROLE_ID=e.ROLE_ID
			  and tvr.TV_CODE='".$TV_CODE."'";
		$result=mysql_query($query);
        $row=@mysql_fetch_array($result);
        $ROLE_NAME=$row["ROLE_NAME"];
        if ($ROLE_NAME == '') $ROLE_NAME="-";
        if ( $ROLE_NAME == 'conducteur' ) $ROLE_NAME='<b>conducteur</b>';
        echo "<td width=100 align=center><font size=1>$ROLE_NAME</font></td>";
      }  
      echo "</tr>";
      
} //end loop vehicules
echo "</table>";
if ( $print == 'NO') echo "</td></tr></table>";
      
?>
