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
$mycompany=$_SESSION['SES_COMPANY'];

$pid=intval($_GET["pid"]);
if ($id == $pid) $allowed=true;
else if ( $mycompany == get_company($pid) and check_rights($_SESSION['id'], 45) and $mycompany > 0) {
	$allowed=true;
}
else check_all(40);

header("Content-type: application/vnd.ms-excel; name='excel'");
header('Content-Disposition: attachment; filename="participations.xls"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header("Expires: 0");
$charset="ISO-8859-15";

echo  "<html>";
echo  "<head>
<meta http-equiv=\"Content-type\" content=\"text/html;charset=".$charset."\" />
<style id=\"Classeur1_16681_Styles\"></style>
<style type=\"text/css\">";
echo  "</style>
</head>
<body>
<div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\">";
echo  "\n"."<table x:num border=1 cellpadding=0 cellspacing=0 width=100% style=\"border-collapse: collapse\">";

$sql = "select te.te_libelle, e.e_code, e.e_libelle, date_format(eh.eh_date_debut,'%d-%m-%Y') 'datedeb', eh.eh_date_debut sortdate,
        date_format(eh.eh_debut, '%H:%i') eh_debut, 
		date_format(eh.eh_fin, '%H:%i') eh_fin,
	    date_format(eh.eh_date_fin,'%d-%m-%Y') 'datefin',
	    e.e_lieu,
	    date_format(ep.ep_date_debut,'%d-%m-%Y') 'epdatedeb',
	    date_format(ep.ep_debut, '%H:%i') ep_debut, date_format(ep.ep_fin, '%H:%i') ep_fin,
	    date_format(ep.ep_date_fin,'%d-%m-%Y') 'epdatefin',
	    ep.ep_flag1,
		ep.ep_comment, 
		ep.tp_id,
		eh.eh_id
        from evenement e, evenement_participation ep, type_evenement te, evenement_horaire eh
        where e.e_code = ep.e_code
        AND eh.e_code = ep.e_code
        AND eh.eh_id = ep.eh_id
        AND te.te_code = e.te_code
        AND  ep.p_id = '$pid'
        AND e.e_canceled = 0
        
        union all
        select e.eq_nom te_libelle, 0 e_code, e.eq_nom e_libelle, date_format(pg.pg_date,'%d-%m-%Y') 'datedeb', 
		pg.pg_date sortdate,
        pg.type eh_debut, 
		'' eh_fin,
        '' datefin,
        '' e_lieu,
        '' epdatedeb,
        '' ep_debut,
        '' ep_fin,
        '' epdatefin,
        0 ep_flag1,
        '' ep_comment,
        0 tp_id,
        0 eh_id
        from planning_garde pg, equipe e
        where e.eq_id = pg.eq_id
        and pg.p_id='$pid'
        order by sortdate desc, eh_debut desc";

$result = mysql_query($sql);
$num=mysql_num_rows($result);

echo "<p><b>Toutes les participations de ".ucfirst(get_prenom($pid))." ".strtoupper(get_nom($pid))."</b>";

if ( $num > 0 ) {
   echo "<tr>
   		  <td>Type</td>
          <td>Date début</td>
          <td>Date fin</td>
      	  <td>Heure début</td>
      	  <td>Heure fin</td>
      	  <td>Lieu</td>
      	  <td>Description</td>
      	  <td>Fonction</td>
      	  <td>Statut</td>
      	  <td>Commentaire</td>
      	 </tr>";

   $i=0;
   while ($row=@mysql_fetch_array($result)) {
       $E_CODE=$row["e_code"];
       $TE_LIBELLE=$row["te_libelle"];
       $E_LIBELLE=$row["e_libelle"];
       $E_LIEU=$row["e_lieu"];
       $EH_DEBUT=$row["eh_debut"];
       $EH_DATE_DEBUT=$row["datedeb"];
       $EH_DATE_FIN=$row["datefin"];
       $EH_FIN=$row["eh_fin"];
       $EP_FLAG1=$row["ep_flag1"];
       $EP_COMMENT=$row["ep_comment"];
	   $fonction=get_fonction($row["tp_id"]);
	   
	   if ( $EP_FLAG1 == 1 ) $statut='Salarié';
	   else $statut='';
	   
	    
	   if ( $row['e_code'] == 0 ) {
         //garde
           echo "<tr>
		   <td>".$TE_LIBELLE."</td>
           <td colspan=2>".$EH_DATE_DEBUT." </td>";
           if ( $row['e_debut'] == 'J' )  echo "<td colspan=2>Jour</td>";
           if ( $row['e_debut'] == 'N' )  echo "<td colspan=2>Nuit</td>";
           echo "<td>".$E_LIEU."</td>
		   <td colspan=4>".$E_LIBELLE."</td>
		   </tr>";
       }
       else {
         // evenement
         
         if ( $row['epdatedeb'] == "" ) {
      		$datedeb=$row['datedeb'];
      		$datefin=$row['datefin'];
      		$debut=$row['eh_debut'];
      		$fin=$row['eh_fin'];
      	 }
      	 else {
       		$datedeb=$row['epdatedeb'];
      		$datefin=$row['epdatefin'];
      		$debut=$row['ep_debut'];
      		$fin=$row['ep_fin'];     	 
      	 }
	   
	  $n=get_nb_sessions($row['e_code']);
      if ( $n > 1 ) $part=" partie ".$row['eh_id']."/".$n;
      else $part="";
	   
       echo "<tr>
	  	  <td>".$TE_LIBELLE."</td> 
      	  <td>".$datedeb."</td> 
		  <td>".$datefin."</td>
      	  <td>".$debut."</td>
      	  <td>".$fin."</td>
      	  <td>".$E_LIEU."</td>
      	  <td>".$E_LIBELLE.$part."</td>
      	  <td>".$fonction."</td>
		  <td>".$statut."</td>
		  <td>".$EP_COMMENT."</td>
		</tr>";
	  }
   }
   echo "</table>";
}
else {
     echo "<p><b>Aucune activité enregistrée.</b>";
}
echo "</body>
</html>";

?>
