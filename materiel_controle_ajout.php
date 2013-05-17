<?php

  # written by: Nicolas MARCHE, Jean-Pierre KUNTZ, Vanessa KOVALSKY
  # contact: nico.marche@free.fr, vanessa.kovalsky@free.fr
  # project: eBrigade
  # homepage: http://sourceforge.net/projects/ebrigade/
  # version: 2.7

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
check_all(42);
$id=$_SESSION['id'];

if ( check_rights($_SESSION['id'], 24)) $section='0';
else $section=$_SESSION['SES_SECTION'];
$mysectionparent=get_section_parent($section);

if ( isset($_GET["from"]))$from=$_GET["from"];
else $from='default';

writehead();
echo "
<STYLE type='text/css'>
.categorie{color:$mydarkcolor; background-color:$mylightcolor; font-size:10pt;}
.type{color:$mydarkcolor; background-color:white; font-size:9pt;}
</STYLE>
<script type='text/javascript' src='checkForm.js'></script>
<script type='text/javascript'>
function fermerfenetre(){
	var obj_window = window.open('', '_self');
	obj_window.opener = window;
	obj_window.focus();
	opener=self;
	self.close();
}

function redirect(){
	url='upd_materiel.php?mid=' + mid + '&addthis=' + addthis;
	self.location.href=url;
}

</script>
";
echo "</head>";
echo "<body>";

$MA_ID=intval($_GET["MA_ID"]);
//echo "l'id du materiel est : ".$MA_ID;

//=====================================================================
// selectionne le bon matériel
//=====================================================================

$query="SELECT MA_ID , S_ID
FROM materiel
WHERE MA_ID=".$MA_ID;

$result=mysql_query($query);
$row=mysql_fetch_array($result);
$MA_ID=$row["MA_ID"];
$S_ID=$row["S_ID"];

// permettre les modifications si je suis habilité sur la fonctionnalité 17 au bon niveau
// ou je suis habilité sur la fonctionnalité 24 )
if (check_rights($_SESSION['id'], 17,"$S_ID")) $responsable_materiel=true;
else $responsable_materiel=false;

if ( $responsable_materiel ) $disabled=""; 
else $disabled="disabled";

if ( $MA_EXTERNE == '1' ) {
	if (check_rights($_SESSION['id'], 24)) $disabled='';
	else $disabled='disabled';
}

// Début du formulaire

echo "<form name='materiel_controle' action='materiel_controle_save.php'>";

//=====================================================================
// ligne 1
//=====================================================================

echo "<p><TABLE>
<TR>
<TD class='FondMenu'>";

echo "<table cellspacing=0 border=0>";
echo "<tr>
      	  <td class=TabHeader colspan=2>informations matériel</td>
      </tr>";


//=====================================================================
// ligne type de contrôle
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor width=200><b>Type</b> <font color=red>*</font></td>
      	  <td bgcolor=$mylightcolor width=250 align=left>
		  <select name='MAC_TYPE' $disabled>";
		    echo "<option value=\"total\">Total</option>";
		    echo "<option value=\"rapide\">Rapide</option>";			
 	        echo "</select>";
 echo "</td>
 	 </tr>";

//=====================================================================
// ligne statut
//=====================================================================

if ( $VP_OPERATIONNEL == -1 ) $opcolor='black';
else if ( $VP_OPERATIONNEL == 1 ) $opcolor=$red;
else if ( $VP_OPERATIONNEL == 2 ) $opcolor=$orange;
else $opcolor=$green;

$query2="select VP_LIBELLE, VP_ID, VP_OPERATIONNEL
         from vehicule_position
		 where VP_OPERATIONNEL <> 0
		 order by  VP_OPERATIONNEL desc";
$result2=mysql_query($query2);

echo "<tr>
      	  <td bgcolor=$mylightcolor ><font color=$opcolor><b>Position du matériel</b> <font color=red>*</font></font></td>
      	  <td bgcolor=$mylightcolor align=left>
		<select name='VP_ID' $disabled>";
		     while ($row2=@mysql_fetch_array($result2)) {
		          $NEWVP_ID=$row2["VP_ID"];
		          $NEWVP_LIBELLE=$row2["VP_LIBELLE"];
		          $NEWVP_OPERATIONNEL=$row2["VP_OPERATIONNEL"];
		          if ($VP_ID == $NEWVP_ID) $selected='selected';
		          else $selected='';
		          echo "<option value='$NEWVP_ID' class=\"".$NEWVP_OPERATIONNEL."\" $selected>$NEWVP_LIBELLE</option>";
	     	     }
 	        echo "</select>";
echo " </td>
      </tr>";

if ( $VP_OPERATIONNEL < 0 ) {
	if ( $MA_UPDATE_DATE <> "" )
		echo "<tr> 
              <td bgcolor=$mylightcolor align=right><i>Modifié le: </i></td> 
              <td bgcolor=$mylightcolor align=left> ".$MA_UPDATE_DATE."</td> 
              </tr>"; 
       if ( $MA_UPDATE_BY <> "") 
       echo "<tr> 
              <td bgcolor=$mylightcolor align=right><i>Modifié par: </i></td> 
              <td bgcolor=$mylightcolor align=left> 
                            <a href=upd_personnel.php?pompier=$MA_UPDATE_BY > 
                            ".ucfirst(get_prenom($MA_UPDATE_BY))." ".strtoupper(get_nom($MA_UPDATE_BY))."</a></td> 
              </tr>"; 
}

//=====================================================================
// ligne commentaire
//=====================================================================

echo "<tr>
      	  <td bgcolor=$mylightcolor ><b>Commentaire</b></td>
      	  <td bgcolor=$mylightcolor align=left><input type='textarea' name='MAC_COMMENT' row='3' col='20' value=\"$MAC_COMMENT\" $disabled>";		
echo " </td>
      </tr>";
	  
//=====================================================================
// dates de prochain contrôle
//=====================================================================

// date
echo "<tr>
      	  <td bgcolor=$mylightcolor ><font color=$revision><b>Date du contrôle</b></font></td>
      	  <td bgcolor=$mylightcolor align=left>";

echo "<input class='plain' name='MAC_CONTROLE_DATE' value='".getnow()."'>";

 
//=====================================================================
// personne ayant effectuer le contrôle
//=====================================================================

$query2="select p.P_ID, p.P_PRENOM, p.P_NOM , s.S_CODE
		from pompier p, section s
   		 where S_ID= P_SECTION
		 and ( p.P_SECTION in (".get_family($S_ID).") or p.P_ID = '".$CONTROLED_BY."' )
         and p.P_CODE <> '1234'
         and (p.P_OLD_MEMBER = 0 or p.P_ID = '".$CONTROLED_BY."' )
		 order by p.P_NOM";
$result2=mysql_query($query2);

echo "<tr >
      	  <td bgcolor=$mylightcolor ><b>Personne ayant effectué le contrôle ".$warning."</b></td>
      	  <td bgcolor=$mylightcolor align=left>";		
   echo "<select id='CONTROLED_BY' name='CONTROLED_BY' $disabled>";
   echo "<option value='".$id."' $selected>".my_ucfirst(get_prenom($id))." ".my_ucfirst(get_nom($id))."</option>\n";
   while ($row2=@mysql_fetch_array($result2)) {
      $P_NOM=$row2["P_NOM"];
      $P_PRENOM=$row2["P_PRENOM"];
      $P_ID=$row2["P_ID"];
      $S_CODE=$row2["S_CODE"];
      if ( $P_ID == $AFFECTED_TO ) $selected='selected';
      else $selected="";
      if ( $nbsections <> 1 ) $cmt=" (".$S_CODE.")";
      else $cmt="";
      echo "<option value='".$P_ID."' $selected>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM).$cmt."</option>\n";
   }
echo "</select>";
echo "</td></tr>";
echo "<input type='hidden' name='MA_ID' value='$MA_ID'>";

echo "</table></tr></table>";
if ( $disabled == "") {
    echo "<p><input type='submit' value='sauver'> ";
}
echo "</form>";


	echo "<input type='button' value='Retour' name='annuler' onclick=\"javascript:history.back(1);\">";


echo "</div>";
?>

<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
</BODY>
</HTML>
