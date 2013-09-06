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
check_all(43);
$mysection = $_SESSION['SES_SECTION'];
$mygroup = $_SESSION['groupe'];
$id=$_SESSION['id'];
writehead();
?>
<SCRIPT LANGUAGE="JavaScript">
	MaxNB = <?php echo $maxdestmessage; ?>;
	MaxWithoutConfirm = 10;
	function Deplacer(l1,l2,mode) {
	    if ( mode == 1) {
			if (l1.options.selectedIndex>=0) {
				o=new Option(l1.options[l1.options.selectedIndex].text,l1.options[l1.options.selectedIndex].value);
				l2.options[l2.options.length]=o;
				l1.options[l1.options.selectedIndex]=null;
			} else{
				alert("Personne n'est s�lectionn�");
			}
	    }
	    if ( mode == 2) {
	       var mylength=l2.options.length;
	       for ( i=0; i < l1.options.length; i++) {
	           o=new Option(l1.options[i].text,l1.options[i].value);
	       	   l2.options[i + mylength]=o;
    	       }
    	       var mylength=l1.options.length;
    	       var i  = mylength;
    	       while ( i > 0) {
    	       	   i= i -1;
	       	   l1.options[i]=null;
    	       }
	    }
	}
	
	function mydisplay(l1,message,mode,compteur) {
	    if ( message.length == 0 )  {
	     	alert("Le texte du message est vide");
	     	return;
	    }
	    if (l1.options.length == 0) {
	     	alert("Aucun destinataire");
	     	return;
	    }
	 	if (mode[0].checked) {
	 	 	choice="mail";
	 	 	if (l1.options.length > MaxNB) {
	 	 	 	alert("Vous avez choisi d'envoyer un mail � "+ l1.options.length +" personnes. \n Le maximum autoris� par le menu 'message' est "+ MaxNB+ "\n pour envoyer un message � un plus grand nombre de destinataires, utiliser plut�t le menu 'alerte', qui n'a pas de limitation.");
	     		return;
	 	 	}
	 	 	else if (l1.options.length > MaxWithoutConfirm) {
	 	 	   if ( confirm("Vous allez envoyer un email � "+ l1.options.length +" personnes.\nContinuer?"))
			  	 confirmed = 1;
			   else return;
			}
	 	} 
	 	else {
	 	 	if (mode[1].checked) choice="sms";
	 	 	else if (mode[2].checked) choice="flash";
	 	 	else return;
	 	 	//choice = sms or flash
	 	 	if (l1.options.length > MaxNB) {
	 	 	 	alert("Vous avez choisi d'envoyer un SMS � "+ l1.options.length +" personnes. \n Le maximum autoris� est "+ MaxNB);
	     		return;
	 	 	}
	 	 	if ( compteur.value > 160 ) {
	 	 	 	alert("La longueur des messages SMS est limit�e � 160 caract�res.\nVous avez: " + compteur.value + " carat�res.");
	 	 	 	return;
	 	 	}
	 	 	if ( confirm("Vous allez envoyer un SMS � "+ l1.options.length +" personnes.\nATTENTION l'envoi de ces SMS a un co�t.\nContinuer?"))
			  	 confirmed = 1;
			else return;
	 	}
        var dest="";
		for ( i=0; i < l1.options.length; i++) {
		      if ( i == 0 ) {
		       dest=l1.options[i].value;
      		    } 
			  else {
      		       dest=dest +","+l1.options[i].value;
   		      }  
	    }
	    url="mail_send.php?dest="+dest+"&mode="+choice+"&message="+message;
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

</SCRIPT>
</HEAD>
<?php
echo "<body>";
echo "<FORM name='formulaire' id='formulaire'>";

$disabledflash='disabled';
$disabled='disabled';

if (( check_rights($_SESSION['id'], 23) ) and ( $sms_provider <> 0)) {
 	$disabled='';
 	if ($sms_provider <> 3 ) $disabledflash='';
}


echo "<div align=center>
	  <font size=4><b> Envoyer un message</b></font>
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
	    <TD align='center' width=260><B>Personnel</B><BR>
	    <SELECT align=top name='liste1' size=6  style='width:250px'>";

	// la veille op�rationnelle doit pouvoir alerter tout le personnel sous sa responsabilit�
   if ( $nbsections <> 1 ) {
		$s=get_highest_section_where_granted($_SESSION['id'], 43);
		if ( $s <> '' ) $mysection=$s;
   }
 
   $query="select p.P_ID, p.P_PRENOM, p.P_NOM, p.P_EMAIL, p.P_PHONE , s.S_CODE
   		from pompier p, section s
		where p.P_SECTION=s.S_ID
		and p.P_OLD_MEMBER = 0
		and p.P_STATUT <> 'EXT'
		and ( p.P_EMAIL <> \"\"  or p.P_PHONE <> \"\" )";

   if (isset($_POST['SelectionMail'])){
      $query2 = $query . " AND p.P_ID in (".$_POST['SelectionMail'].")  order by p.P_NOM ";
      $query = $query . " AND p.P_ID not in (".$_POST['SelectionMail'].") ";
   } 		
   if (! check_rights($_SESSION['id'], 24)) {
      $query .= " and p.P_SECTION in (".get_family("$mysection").")";		
   }		
	
   $query .= " order by p.P_NOM";
   $result=mysql_query($query);
   while ($row=@mysql_fetch_array($result)) {
      $P_NOM=$row["P_NOM"];
      $P_PRENOM=$row["P_PRENOM"];
      $P_ID=$row["P_ID"];
      $S_CODE=$row["S_CODE"];
      $P_EMAIL=$row["P_EMAIL"];
      $P_PHONE=$row["P_PHONE"];
      if ( $nbsections <> 1 ) $add=" (".$S_CODE.")";
      echo "<option value='".$P_ID."'>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM).$add."</option>\n";	
  }

   if (isset($_POST['SelectionMail'])) {
     $result2=mysql_query($query2);
     $options2="";
     while ($row=@mysql_fetch_array($result2)) {
        $P_NOM=$row["P_NOM"];
        $P_PRENOM=$row["P_PRENOM"];
        $P_ID=$row["P_ID"];
        $S_CODE=$row["S_CODE"];
        $P_EMAIL=$row["P_EMAIL"];
        $P_PHONE=$row["P_PHONE"];
        if ( $nbsections <> 1 ) $add=" (".$S_CODE.")";
        $options2 .= "<OPTION value='".$P_ID."'>".strtoupper($P_NOM)." ".ucfirst($P_PRENOM).$add."</OPTION>\n";	
     }
  }
  else $options2 = "<OPTION value='0'></OPTION>";

echo "	 </SELECT>
           </TD>
	       <TD align='center' width=80>
	       <BR>
	       <INPUT type='button' value='>>>' onClick='Deplacer(this.form.liste1,this.form.liste2,2)'>
	       <br>
	       <INPUT type='button' value='>' onClick='Deplacer(this.form.liste1,this.form.liste2,1)'>
	       <br>
	       <INPUT type='button' value='<' onClick='Deplacer(this.form.liste2,this.form.liste1,1)'>
	       <br>
	       <INPUT type='button' value='<<<' onClick='Deplacer(this.form.liste2,this.form.liste1,2)'>
	        </TD>
	        <TD align='center' width=260><B>Destinataires</B> <i>(maximum $maxdestmessage)</i><BR>
 	        <SELECT align=top name='liste2' size=6 style='width:250px'>
			  ".$options2."			  			  
		</SELECT>		
		 </TD>
		 </tr></table></td>
	</TR>";
	
if (isset($_POST['Messagebody']))$msg=$_POST['Messagebody'];
else $msg="";

echo "<tr bgcolor=$mylightcolor>
     	  <td>
     	  <table cellspacing=0 border=0>
     	  <tr>
	  <td bgcolor=$mylightcolor align=center width=600>
	      <B>Votre message</B><BR>
	      <textarea name='mymessage' cols='63' rows='12' 
	        style='FONT-SIZE: 10pt; FONT-FAMILY: Arial;'
		  	wrap='soft' 
			onFocus='Compter(this,1000,formulaire.comptage)' 
			onKeyDown='Compter(this,1000,formulaire.comptage)' 
			onKeyUp='Compter(this,1000,formulaire.comptage)' 
			onBlur='Compter(this,1000,formulaire.comptage)'>".$msg."</textarea>
	  </td>
	  </tr></table>
	  </td>
      </tr>";
echo "<tr bgcolor=$mylightcolor>
     	  <td>
     	  <table cellspacing=0 border=0>
     	  <tr>
	  <td bgcolor=$mylightcolor align=center width=600>
	  	  <input type='text' name='comptage' size='4' readonly=readonly >
	      <input type='button' value='Envoyer' 
		  onclick='mydisplay(this.form.liste2, escape((this.form.mymessage).value),this.form.mode, this.form.comptage)'>
	  </td>
	  </tr></table>
	  </td>
      </tr>";
echo"</TABLE>";
echo "</td></tr></table>"; 
if (!isset($_POST['SelectionMail'])){
echo "<SCRIPT language='javascript'>
		document.formulaire.liste2.options.length=0;
	</SCRIPT>";
}
echo "</FORM>";

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
	 	$conn = preg_split('/:/',connectSMS_3());
	 	if ( $conn[0] == 'KO' ) $credits="ERREUR";
		else $credits = getSMSCredit_3("$conn[1]");
		$sms_url="http://www.clickatell.com/login.php?csite=clickatell";
	}
        
        if( $sms_provider == 5) {
            $credits = getSMSCredit_5();
            $sms_url = "https://www.smsmode.com";
        }
}

if (( check_rights($_SESSION['id'], 23)) and ( $sms_provider <> 0)){
	echo "<p><input type=button value='historique sms' onclick='historique();'>";
	echo "<br><table><tr><td> Il vous reste <b>".$credits." SMS.</td>";
	echo "<td><a href=".$sms_url." target=_blank>
	<img src=images/credircard.png border=0 title='voir mon compte sms'></a></td></tr></table>";
}


?>
