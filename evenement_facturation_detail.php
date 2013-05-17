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
writehead();

$frmaction ="Enregistrer";
$msgerr ="";
$evenement=(isset($_POST['evenement'])?intval($_POST['evenement']):(isset($_GET['evenement'])?intval($_GET['evenement']):0));
$type=(isset($_POST['type'])?mysql_real_escape_string($_POST['type']):(isset($_GET['type'])?mysql_real_escape_string($_GET['type']):0));

// le chef, le cadre de l'événement ont toujours accès à cette fonctionnalité, les autres doivent avoir 29 et/ou 24
if (( ! check_rights($id, 29, get_section_organisatrice($evenement))) and ( get_chef_evenement($evenement) <> $_SESSION['id'] )) {
 	check_all(29);
	check_all(24);
}

if(isset($_POST['lig'])){
	$sql="delete from evenement_facturation_detail 
	where e_id = '$evenement'
	AND ef_type='$type'";
	$res = mysql_query($sql);
	echo (mysql_errno()>0?"<p>$sql<br>".mysql_error()."</p>":"");

	if(isset($_POST['btcopie'])){
		$sql="insert into evenement_facturation_detail(e_id,ef_lig,ef_type,ef_txt,ef_qte,ef_pu,ef_rem, ef_frais)
(select e_id,ef_lig,'$type',ef_txt,ef_qte,ef_pu,ef_rem, ef_frais from evenement_facturation_detail 
where e_id = '$evenement'
and ef_type='devis'
)";
		$res = mysql_query($sql);
		echo (mysql_errno()>0?"<p>$sql<br>".mysql_error()."</p>":"");
	}else{	
		$tabl_multi= $_POST['lig'];
		$TotalDoc=0;
		foreach($tabl_multi as $ligne){
			$lig=0;
			$libelle= "";
			$qte="";
			$pu="";
			$rem=0;
			$frais="PRE";
			foreach($ligne as $col => $valeur){
				if($valeur!=""){
					switch ($col){
					case "lig":
						$lig= $valeur;
						break;
					case "txt":
						$libelle= addslashes($valeur);
						break;
					case "qte":
						$qte= $valeur;
						break;
					case "pu":
						$pu= $valeur;
						break;
					case "rem":
						$rem= $valeur;
						break;						
					case "frais":
						$frais= addslashes($valeur);
						break;												
					default:
					}
				}
			}
			if(trim($libelle)!=""){
				$sql = "insert into evenement_facturation_detail(e_id,ef_lig,ef_type,ef_txt,ef_qte,ef_pu,ef_rem, ef_frais)
		values($evenement,$lig,'$type','$libelle','$qte','$pu','$rem','$frais')";
				$res = mysql_query($sql);
				echo (mysql_errno()>0?"<p>$sql<br>".mysql_error()."</p>":"");
				$TotalLigne = ($qte*$pu*(1-($rem/100)));	
				$TotalDoc += $TotalLigne;
			}

		}
			if($TotalDoc<>0){
				$sql = "update evenement_facturation set ".$type."_montant = $TotalDoc where e_id = '$evenement'";
				$res = mysql_query($sql);
				echo (mysql_errno()>0?"<p>$sql<br>".mysql_error()."</p>":"");
			}		
	}	
}// fin POST

$sql="select * from evenement_facturation_detail 
where e_id = '$evenement'
and ef_type='$type'";
$res = mysql_query($sql);
$out="";
$num=0;
$TotalDoc=0;
while($row=mysql_fetch_array($res)){
	$num++;
	$TotalLigne = ($row['ef_qte']*$row['ef_pu']*(1-($row['ef_rem']/100)));	
	$TotalDoc += $TotalLigne;
	$out.="<tr name=\"".$row['ef_lig']."\" onclick=\"ajouterligne($(this));\">";
	$out.="<td><input type=\"text\" name=\"lig[".$row['ef_lig']."][lig]\" value=\"".$row['ef_lig']."\" size=\"2\" /></td>";
	$out.="<td><select name=\"lig[".$row['ef_lig']."][frais]\"]>
	<option value=\"PRE\" ".(($row['ef_frais']=="PRE")?" selected":"").">Prestation</option>
	<option value=\"KM\" ".(($row['ef_frais']=="KM")?" selected":"").">Frais Km</option>
	<option value=\"DIV\" ".(($row['ef_frais']=="DIV")?" selected":"").">Frais Divers</option>
	</select></td>";
	$out.="<td><input type=\"text\" name=\"lig[".$row['ef_lig']."][txt]\" value=\"".stripslashes($row['ef_txt'])."\" class=\"txt\" /></td>";	
	$out.="<td><input type=\"text\" name=\"lig[".$row['ef_lig']."][qte]\" value=\"".$row['ef_qte']."\"  class=\"qte\" /></td>";
	$out.="<td><input type=\"text\" name=\"lig[".$row['ef_lig']."][pu]\" value=\"".$row['ef_pu']."\"  class=\"pu\" /></td>";
	$out.="<td><input type=\"text\" name=\"lig[".$row['ef_lig']."][rem]\" value=\"".$row['ef_rem']."\"  class=\"rem\" /></td>";
	$out.="<td><input type=\"text\" name=\"lig[".$row['ef_lig']."][tot]\" value=\"".$TotalLigne."\" class=\"TotLigne\"  onfocus=\"calculerligne(".$row['ef_lig'].");\" readonly /></td>";
	$out.="</tr>";
}
?>
<script type="text/javascript" src="js/jquery.js"></script>

<style type="text/css">
.txt{
width: 30em;
}
.qte{
width: 5em;
}
.pu{
width: 5em;
}
.rem{
width: 4em;
}
.TotLigne{
width: 5em;
}
</style>
</head>
<body>
<a href="evenement_facturation.php?evenement=<?php echo $evenement; ?>&status=<?php echo $type; ?>">Retour</a>

<form name="detail" id="" method="post" action="">
<?php
echo EbDeb("Détail $type");
?>
<table id="tableau">
<thead>
<tr>
<th>n</th>
<th>Frais</th>
<th>Libellé</th>
<th>Qté</th>
<th>PU</th>
<th>Remise %</th>
<th>Total = <span id="TotalTableau"><?php echo (isset($TotalDoc)?$TotalDoc:0);?></span></th>
</tr>
</thead>
<tbody>
<?php
echo $out; 
?>
</tbody>
</table>
<?php
echo EbFin(); 
?>


<div id="action">
<input type="hidden" name="frmaction" value="<?php echo $frmaction; ?>">
<input type="hidden" name="evenement" value="<?php echo $evenement; ?>">
<?php
if($type!="devis"){
   $query="select count(*) as NB from evenement_facturation_detail
   		   where e_id= $evenement
   		   and ef_type='devis'";
   $result=mysql_query($query);
   $row=mysql_fetch_array($result);
   if ( $row["NB"] > 0 )
	   echo "<br /><input type=\"submit\" name=\"btcopie\" value=\"Copie du devis\">";
}
?>
<br /><input type="submit" id="btaction" value="<?php echo $frmaction; ?>">
</div>
</form>

<script type="text/javascript">
var nbligne = <?php echo ($num>0?$num:1); ?>;
function rectifSaisie(saisie){
	var sortie="";
	while(true){
		virgule= saisie.indexOf(",");
		if(virgule!=-1){
			sortie+=saisie.substring(0,virgule)+".";
			saisie=saisie.substring(virgule+1);
		}// fin du if
		else {
			sortie+=saisie
			break;
		}// fin du else
	}// fin du while
	return sortie;
}// fin de rectifSaisie
function nouvelleligne(nbligne){
	return '<tr name="'+ nbligne +'" onclick="ajouterligne($(this));">' +				
	'<td><input type="hidden" name="lig['+ nbligne +'][lig]" value="'+ nbligne +'" size="2" />'+ nbligne +'</td>' +
	'<td><select name="'+'lig['+ nbligne +'][frais]'+'">' +
	'<option value="PRE">Prestation</option>' +
	'<option value="KM">Frais Km</option>' +
	'<option value="DIV">Frais Divers</option>' +
	'</select></td>' +
	'<td><input type="text" name="lig['+ nbligne +'][txt]" class="txt"/></td>' +	
	'<td><input type="text" name="lig['+ nbligne +'][qte]" class="qte"/></td>' +
	'<td><input type="text" name="lig['+ nbligne +'][pu]"  class="pu" /></td>' +
	'<td><input type="text" name="lig['+ nbligne +'][rem]" class="rem" value="0" /></td>' +
	'<td><input type="text" name="lig['+ nbligne +'][tot]" class="TotLigne" onfocus=\"calculerligne('+ nbligne +');\" readonly /></td>' +
	'</tr>';
}

// on creé la première ligne
var nouvelle_ligne = nouvelleligne(nbligne);
<?php
if ($num==0){
	?>
	$(nouvelle_ligne).prependTo("#tableau");
	<?php
}
?>
// On affiche le nombre de ligne
$("#result").html("nb ligne = " + nbligne);
function ajouterligne(ligne){
	// Si c'est la derière ligne
	if(ligne.attr('name') == nbligne){		
		// On insert la nouvelle ligne
		nbligne ++;
		var nouvelle_ligne = nouvelleligne(nbligne);
		$(nouvelle_ligne).insertAfter(ligne);		
		// on change la variable nbligne et on l'affiche 
		$("#result").html("nb ligne = " + nbligne);
	}
}
function calculerligne(ligne){
	// calculer le montant de la ligne
	var curqte= rectifSaisie($("input[@name='lig["+ligne+"][qte]']").attr("value"));
	var curpu= rectifSaisie($("input[@name='lig["+ligne+"][pu]']").attr("value"));
	var currem= rectifSaisie($("input[@name='lig["+ligne+"][rem]']").attr("value"));
	var curtot = Math.floor(((curqte * curpu)*(1-currem/100))*100)/100;
	$("input[@name='lig["+ligne+"][tot]']").val(curtot);
	calculertotal();
	// ajoute une ligne si tabulation sur dernière cellule
	if(ligne==nbligne){
		ajouterligne($("tr[@name='"+ligne+"']"));
	}	
}
function calculertotal(){
	var total = 0;
	$("input[@name$='][tot]']").each(function(){
		if($(this).attr("value")!=""){
			total = total + eval(rectifSaisie($(this).attr("value")));
		}
	});
	$("#TotalTableau").html(Math.floor(total*100)/100);
}
</script>
</body>
</html>