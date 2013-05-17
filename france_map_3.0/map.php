<?php
if (!isset($mapChemin)) {
	$mapChemin = '';
}
?>

<?php
// Getion des Entetes HTML
if (!isset($urlExec)) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Carte de France Flash avec France-Map.fr</title>
</head>

<body>';
	
}
?>

<script language="javascript">AC_FL_RunContent = 0;</script>
<script src="<?php echo $mapChemin; ?>js/AC_RunActiveContent.js" language="javascript"></script>
<script language="javascript">
	if (AC_FL_RunContent == 0) {
		alert("Cette page nécessite le fichier AC_RunActiveContent.js.");
	} else {
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0',
			'width', '450',
			'height', '450',
			'src', 'france_map_3.0?mapChemin=<?php echo $mapChemin; ?>',
			'quality', 'high',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'align', 'middle',
			'play', 'true',
			'loop', 'true',
			'scale', 'showall',
			'wmode', 'window',
			'devicefont', 'false',
			'id', 'france_map_3.0',
			'bgcolor', '#ffffff',
			'name', 'france_map_3.0',
			'menu', 'true',
			'allowFullScreen', 'false',
			'allowScriptAccess','sameDomain',
			'movie', '<?php echo $mapChemin; ?>swf/france_map_3.0?mapChemin=<?php echo $mapChemin; ?>',
			'salign', ''
			); //end AC code
	}
</script>
<noscript>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="450" height="450" id="france_map_3.0" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="allowFullScreen" value="false" />
	<param name="movie" value="<?php echo $mapChemin; ?>swf/france_map_3.0.swf?mapChemin=<?php echo $mapChemin; ?>" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />	<embed src="<?php echo $mapChemin; ?>swf/france_map_3.0.swf?mapChemin=<?php echo $mapChemin; ?>" quality="high" bgcolor="#ffffff" width="450" height="450" name="france_map_3.0" align="middle" allowscriptaccess="sameDomain" allowfullscreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</noscript>

<?php
// Getion des Entetes HTML
if (!isset($urlExec)) {
	echo '</body>
</html>';
}
?>
