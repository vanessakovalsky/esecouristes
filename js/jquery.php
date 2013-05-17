<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>

<script type="text/javascript" src="js/tablesorter/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="js/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>

<style type="text/css" >@import url(js/datepicker/ui.datepicker.css);</style>
<script type="text/javascript" src="js/datepicker/ui.datepicker.js"></script>
<script type="text/javascript" src="js/datepicker/ui.datepicker-fr.js"></script>

<style type="text/css" >@import url(js/tabs/ui.tabs.css);</style>

<script type="text/javascript">
$(document).ready(function() {
	$('table#exportTable').tablesorter({widgets:['zebra']});  
	//$('table#exportTable').tablesorterPager({container: $("#pager")});
	//$('table.Filters').columnFilters();  
	
	$.datepicker.setDefaults({showOn: 'button', buttonImageOnly: true, 
buttonImage: 'images/calbtn.gif', buttonText: 'Calendrier', firstDay: 1, dateFormat: 'dd-mm-yy',numberOfMonths: 2});
	$('#dtdb').datepicker({beforeShow: customRange});     
	$('#dtfn').datepicker({beforeShow: customRange});   
	
	$('table#exportTable tbody tr').mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
	$('table#exportTable tbody tr:odd + :not(.SousTotal) + :not(.Total)').addClass('odd');
	$('table#exportTable tbody tr:even + :not(.SousTotal) + :not(.Total)').addClass('even');
});

function customRange(input) { 
	return {minDate: (input.id == 'dtfn' ? $('#dtdb').datepicker('getDate') : null), 
maxDate: (input.id == 'dtdb' ? $('#dtfn').datepicker('getDate') : null)}; 
}
</script>
