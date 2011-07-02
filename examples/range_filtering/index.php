<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" type="image/ico" href="http://www.datatables.net/media/images/favicon.ico" />
<title>DataTables example</title>
<style type="text/css" title="currentStyle">
@import "http://datatables.net/release-datatables/media/css/demo_page.css";
@import "http://datatables.net/release-datatables/media/css/demo_table.css";
@import "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css";

</style>
<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript" src="http://datatables.net/release-datatables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function()
  {
    var oTable = $('#example').dataTable
    ({
      'bServerSide'    : true,
      'sAjaxSource'    : 'ajax.php',
	  'fnServerData': function(sSource, aoData, fnCallback)
	    {

          aoData.push( { "name": "min_length", "value": $( "#min_length" ).val() },
                       { "name": "max_length", "value": $( "#max_length" ).val() } );
	      $.ajax
            ({
              'dataType': 'json',
              'type'    : 'POST',
              'url'     : sSource,
              'data'    : aoData,
              'success' : fnCallback
            }); 
	    },
    });
  
  	$( "#slider-range" ).slider({
		range: true,
		min: 46,
		max: 185,
		values: [ 46, 185 ],
		slide: function( event, ui ) {
			$( "#min_length" ).val(ui.values[ 0 ]);
			$( "#max_length" ).val(ui.values[ 1 ]);
		},
		stop: function(event, ui) { 
			oTable.fnDeleteRow();
		}
	});
	$( "#min_length" ).val( $( "#slider-range" ).slider( "values", 0 ));
	$( "#max_length" ).val( $( "#slider-range" ).slider( "values", 1 ));
 
 });
</script>
</head>
<body id="dt_example">
<div id="container">
<h1>Ignited Datatables - Range Filtering</h1>
  <table border="0" cellpadding="4" cellspacing="0" class="display" id="example">
    <thead>
      <tr>
        <th width="10%">Film ID</th>
        <th width="55%">Title</th>
        <th width="10%">Release Year</th>
        <th width="10%">Length</th>
        <th width="15%">Rating</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>loading...</td>
      </tr>
    </tbody>
  </table>
  
<div class="demo" style='width:250px;'>
<p>
	<label for="amount">Min length range:</label>
	<input type="text" id="min_length" style="border:0; color:#f6931f; font-weight:bold" disabled=disabled /><br>
	<label for="amount">Max length range:</label>
	<input type="text" id="max_length" style="border:0; color:#f6931f; font-weight:bold;" disabled=disabled />
</p>
<div id="slider-range"></div>
</div><!-- End demo -->

</div>


</body>
</html>