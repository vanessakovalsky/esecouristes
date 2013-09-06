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
check_all(43);
$id=$_SESSION['id'];
$s=get_highest_section_where_granted($id,43);
if ( $s <> '' ) $mysection=$s;
else $mysection= $_SESSION['SES_SECTION'];
$mygroup=$_SESSION['groupe'];
get_session_parameters();

if ( isset($_GET["poste"])) $poste=intval($_GET["poste"]); 
else $poste=0;

if ( isset($_GET["section"])) $section=intval($_GET["section"]); 
else $section=$mysection;

if ( isset($_GET["message"])) $message=$_GET["message"]; 
else $message='';

if ( isset($_GET["dispo"])) $dispo=mysql_real_escape_string($_GET["dispo"]); 
else $dispo='0';

writehead();

if ( $dispo == '0' ) {
	if ( $poste <> 0 ) { 
	$query="select count(distinct a.P_ID) as NB from pompier a, poste b, qualification c
		where a.P_ID=c.P_ID
		and a.P_OLD_MEMBER = 0
		and a.P_STATUT <> 'EXT'
		and b.PS_ID=c.PS_ID
		and b.PS_ID = $poste 
		and c.Q_VAL > 0
		and a.P_SECTION in (".get_family("$section").")";
	}
	else {
 	$query="select count(1) as NB from pompier
 		where P_OLD_MEMBER = 0
 		and P_STATUT <> 'EXT'
		and P_SECTION in (".get_family("$section").")";
	}
}
else {
 	if ( $poste <> 0 ) { 
	$query="select count(distinct a.P_ID) as NB from pompier a, poste b, qualification c, disponibilite d
		where a.P_ID=c.P_ID
		and d.P_ID = a.P_ID
		and a.P_OLD_MEMBER = 0
		and a.P_STATUT <> 'EXT'
		and b.PS_ID=c.PS_ID
		and b.PS_ID = $poste 
		and d.D_DATE = '".$dispo."'
		and c.Q_VAL > 0
		and d.D_JOUR + d.D_NUIT >= 1 
		and a.P_SECTION in (".get_family("$section").")";
	}
	else {
 	$query="select count(distinct p.P_ID) as NB from pompier p, disponibilite d
 		where d.P_ID =p.P_ID
 		 and p.P_OLD_MEMBER = 0
 		 and p.P_STATUT <> 'EXT'
 		 and d.D_DATE = '".$dispo."'
 		 and d.D_JOUR + d.D_NUIT >= 1 
		 and p.P_SECTION in (".get_family("$section").")";
	}
}

$year=date("Y");
$year='';
$month=date("m");
$day=date("d");


$result=mysql_query($query);
$row=@mysql_fetch_array($result);
$NB=$row["NB"];


$credits = "ERREUR";
if (( check_rights($_SESSION['id'], 23)) and ( $sms_provider <> 0)){
	if ( $sms_provider == 1 ) {
 		$credits = getSMSCredit_1();
 		$sms_url = "http://sms.pictures-on-line.net/achat_credit.htm";
	}
	if ( $sms_provider == 2 ) {
 		$credits = getSMSCredit_2();
 		$sms_url = "http://www.envoyersms.org";
	}
	if ( $sms_provider == 3 ) {
	 	$conn = preg_split("/:/",connectSMS_3());
	 	if ( $conn[0] == 'KO' ) $credits="ERREUR";
		else $credits = getSMSCredit_3("$conn[1]");
		$sms_url="http://www.clickatell.com/login.php?csite=clickatell";
	}
	if ( $sms_provider == 4 ) {
		$credits = "OK";
	}
        
        if( $sms_provider == 5) {
            $credits = getSMSCredit_5();
            $sms_url = "https://www.smsmode.com";
        }
}
?>
<SCRIPT LANGUAGE="JavaScript">

	function displaymanager(p1,p2,p3,p4){
	 self.location.href="alerte_create.php?poste="+p1+"&section="+p2+"&dispo="+p3+"&message="+p4;
	 return true
	}
	
	function envoyer(message,mode,poste,section,dispo,compteur) {
	    if ( message.length == 0 )  {
	     	alert("Le texte du message est vide");
	     	return;
	    }
	 	if (mode[0].checked) {
	 	 	choice="mail";
	 	 	if ( confirm("Vous allez envoyer un email � "+ <?php echo $NB ?> +" personnes.\nContinuer?"))
			  	 confirmed = 1;
			else return;
	 	} 
	 	else {
	 	 	if (mode[1].checked) choice="sms";
	 	 	else if (mode[2].checked) choice="flash";
	 	 	else return;
	 	 	//choice = sms or flash
	 	 	credits = <?php echo "'".$credits."'" ?> ;
		 	if ( credits == 'ERREUR' ) {
	 	 	 	alert("Vous n'avez pas de cr�dits SMS.");
	 	 	 	return;
	 	 	}
	 	 	if ( credits == '0' ) {
	 	 	 	alert("Vous n'avez plus de cr�dits SMS.");
	 	 	 	return;
	 	 	}
	 	 	if ( compteur.value > 160 ) {
	 	 	 	alert("La longueur des messages SMS est limit�e � 160 caract�res.\nVous avez: " + compteur.value + " carat�res.");
	 	 	 	return;
	 	 	}
	 	 	if ( confirm("Vous allez envoyer un SMS � "+ <?php echo $NB ?> +" personnes.\nATTENTION l'envoi de ces SMS a un co�t.\nContinuer?"))
			  	 confirmed = 1;
			else return;
	 	}
	    url="alerte_send.php?poste="+poste+"&section="+section+"&mode="+choice+"&dispo="+dispo+"&message="+message;
     	self.location.href=url;
	}
	
	
	function historique() {
     window.open('histo_sms.php', '_blank');
    }
    
    function Compter(Target, max, nomchamp) {
		StrLen = Target.value.length
		if (StrLen > max ) {
			Target.value = Target.value.substring(0,max);
			CharsLeft = max;								
		}
		else
		{
			CharsLeft = StrLen;
		}	
		nomchamp.value = CharsLeft;
	}
	
	var fenetreDetail=null;
   function voirDetail(section,poste,dispo){
	 url="destinataires.php?section="+section+"&poste="+poste+"&dispo="+dispo;
	 fenetre=window.open(url,'Note','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,   copyhistory=no,' + 'width=450' + ',height=550');
	 fenetreDetail = fenetre;
	 return true
	 //self.location.href = url;
   }

    function fermerDetail() {
	 if (fenetreDetail != null) {
	    fenetreDetail.close( );
	    fenetreDetail = null;
         }
}

</SCRIPT>
</HEAD>
<?php
echo "<body>";
echo "<FORM name='formulaire' id='formulaire'>";
$disabledflash='disabled';
$disabled='disabled';
$checked='';

if (( check_rights($_SESSION['id'], 23)) and ( $sms_provider <> 0)) {
 	$disabled='';
 	if ($sms_provider <> 3 ) $disabledflash='';
 	$checked='checked';
}

echo "<div align=center>
	  <font size=4><b> Alerter le personnel</b></font>
	  <table cellspacing=0 border=0>";

echo "<tr><td width = 20><input type='radio' name='mode' value='mail' checked/></td>
	  <td width = 50><img src=images/xfmail.png> </td>
      <td width = 100><b>e-mail</b></td>
	  <td width = 20><input type='radio' name='mode' value='sms' $disabled /></td>
	  <td width = 50 ><img src=images/phone.png> </td>
      <td width = 100><b>sms<br>normal</b></td>";
if ( $disabledflash == '' ) 
echo "<td width = 20><input type='radio' name='mode' value='flash' $disabled $disabledflash/></td>
      <td width = 50 ><img src=images/phone2.png> </td>
	  <td width = 100><b>sms<br>flash</b></td>";	 	  
echo "</tr></table>";



echo "<p><table>";
echo "<tr>
	  <td class='FondMenu'>";
echo "<TABLE cellspacing=0 border=0>
	<TR bgcolor=$mylightcolor>
	    <td>
	    <table cellspacing=0 border=0>
	    <tr>
	    <TD align='right' width=150><B>Personnel qualifi&eacute; pour:</B></td>";
  	echo "<td width=250 align=left>
	  <select id='menu1' name='menu1' 
	  onchange=\"fermerDetail();displaymanager(document.getElementById('menu1').value,'".$section."','".$dispo."',escape((this.form.mymessage).value));\">
	  <option value='0'>toutes qualifications</option>";
		$query2="select p.PS_ID, p.DESCRIPTION, e.EQ_NOM, e.EQ_ID from poste p, equipe e 
		   where p.EQ_ID=e.EQ_ID
		   order by p.EQ_ID, p.PS_ID";
		$result2=mysql_query($query2);
		$prevEQ_ID=0;
		while ($row=@mysql_fetch_array($result2)) {
      		$PS_ID=$row["PS_ID"];
      		$EQ_ID=$row["EQ_ID"];
      		$EQ_NOM=$row["EQ_NOM"];
      		if ( $prevEQ_ID <> $EQ_ID ) echo "<OPTGROUP LABEL='".$EQ_NOM."'>";
      		$prevEQ_ID=$EQ_ID;
      		$DESCRIPTION=$row["DESCRIPTION"];
      		if ($PS_ID == $poste ) $selected='selected';
      		else $selected='';
      		echo "<option value='".$PS_ID."' $selected>".$DESCRIPTION."</option>\n";
		}
		echo "</select>";
echo "</td></tr>";

if ($nbsections <> 1 ) {
 	echo "<tr><td align='right' width=150>";
 	echo "<B>Personnel de:</B></td>";
	echo "<td width=250 align=left><select id='menu2' name='menu2' 
	onchange=\"fermerDetail();displaymanager('".$poste."',document.getElementById('menu2').value,'".$dispo."',escape((this.form.mymessage).value));\">";	
	
	$level=get_level($mysection);
    if ( $level == 0 ) $mycolor=$myothercolor;
    elseif ( $level == 1 ) $mycolor=$my2darkcolor;
    elseif ( $level == 2 ) $mycolor=$my2lightcolor;
    elseif ( $level == 3 ) $mycolor=$mylightcolor;
    else $mycolor='white';
    $class="style='background: $mycolor;'";
    if ( check_rights($_SESSION['id'], 24))
   	  display_children2(-1, 0, $section, $nbmaxlevels, $sectionorder);
    else {
   		echo "<option value='$mysection' $class >".str_repeat(". ",$level)." ".
      	get_section_code($mysection)." - ".get_section_name($mysection)."</option>";
   		display_children2($mysection, $level +1, $section, $nbmaxlevels, $sectionorder);
    }
	echo "</select></td></tr>";
}


echo "<tr><td align='right' width=150>";
echo "<B>Disponibilit&eacute;:</B>";
echo "<td width=250 align=left> ";
echo " <select id='menu3' name='menu3'
	onchange=\"fermerDetail();displaymanager('".$poste."','".$section."',document.getElementById('menu3').value,escape((this.form.mymessage).value));\"
	 >
		<option value='0'> disponibles ou pas</option>";

$m0=date("n");
$y0=date("Y");
$d0=date("d");
for ($i=0; $i < 15 ; $i++) {
 	$udate=mktime (0,0,0,$m0,$d0,$y0) + $i * 24 * 60 * 60;
 	$year = date ( "Y", $udate);
 	$month = date ( "m", $udate);
 	$day = date ( "j", $udate);
 	if ( $day < 10 ) $day = "0".$day;
 	$mydate =$year."-".$month."-".$day;
 	if ( "$dispo" == "$mydate" ) $selected = 'selected';
 	else $selected = '';
	echo "<option value='".$mydate."' $selected>".$day." ".$mois[$month - 1]." ".$year."</option>";
}
		
		
echo "	</select>";
echo "</td></tr>";
echo "<tr><td align='right' width=150>";
echo "<B>Nombre d'agents:</B>";
echo "<td width=250 align=left> 
	 <input type='button' value='$NB' title='cliquer pour voir la liste des agents concern�s' onclick=\"fermerDetail(); voirDetail('".$section."','".$poste."','".$dispo."');\">";		
echo "</td>";
	
echo "</tr></table></td>
	</tr>";
echo "<tr bgcolor=$mylightcolor>
     	  <td>
     	  <table cellspacing=0 border=0>
     	  <tr>
	  <td bgcolor=$mylightcolor align=center width=550>
	      <FONT size='2'><B>Votre message</B></FONT><BR>
	      <textarea name='mymessage' cols='63' rows='12' 
	        style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;'
		  	wrap='soft' 
			onFocus='Compter(this,1000,formulaire.comptage)' 
			onKeyDown='Compter(this,1000,formulaire.comptage)' 
			onKeyUp='Compter(this,1000,formulaire.comptage)' 
			onBlur='Compter(this,1000,formulaire.comptage)'>".$message."</textarea>
	  </td>
	  </tr></table>
	  </td>
      </tr>";
      
$disabled='';
if ( $NB == 0 ) $disabled='disabled';

echo "<tr bgcolor=$mylightcolor>
     	  <td>
     	  <table cellspacing=0 border=0>
     	  <tr>
	  <td bgcolor=$mylightcolor align=center width=550>
	  	  <input type='text' name='comptage' size='4'  readonly=readonly>
	      <input type='button' value='Envoyer' $disabled
		  onclick=\"fermerDetail();envoyer(escape((this.form.mymessage).value),this.form.mode,'".$poste."','".$section."','".$dispo."', this.form.comptage)\">
	  </td>
	  </tr></table>
	  </td>
      </tr>";
echo"</TABLE>";
echo "</td></tr></table>"; 
echo "</FORM>";

if (( check_rights($_SESSION['id'], 23)) and ( $sms_provider <> 0)){
	echo "<p><input type=button value='historique sms' onclick='historique();'>";
	echo "<br><table><tr><td> Il vous reste <b>".$credits." SMS.</td>";
	echo "<td><a href=".$sms_url." target=_blank>
	<img src=images/credircard.png border=0 title='voir mon compte sms'></a></td></tr></table>";
}

?>
