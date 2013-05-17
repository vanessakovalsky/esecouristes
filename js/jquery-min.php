<script type="text/javascript" src="js/jquery.js"></script>
<style type="text/css">
@import url(js/tablesorter/themes/fnpc/style.css);
</style>
<script type="text/javascript" src="js/tablesorter/jquery.tablesorter.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
});
$('table.tablesorter').tablesorter({widgets:['zebra']});  	
$('table.tablesorter tbody tr').mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
$("table.tablesorter").bind("sortStart",function() { 
        $("#overlay").show(); 
    }).bind("sortEnd",function() { 
        $("#overlay").hide(); 
    });
</script>
<style type="text/css">
#overlay {
	top: 100px;
	left: 50%;
	position: absolute;
	margin-left: -100px;
	width: 200px;
	text-align: center;
	display: none;
	margin-top: -10px;
	background: #000;
	color: #FFF;
}
</style>
