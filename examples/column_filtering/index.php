<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" type="image/ico" href="http://www.datatables.net/media/images/favicon.ico" />
<title>DataTables example</title>
<style type="text/css" title="currentStyle">
@import "http://jquery-datatables-column-filter.googlecode.com/svn-history/r17/trunk/media/css/demo_page.css";
@import "http://jquery-datatables-column-filter.googlecode.com/svn-history/r17/trunk/media/css/demo_table.css";
@import "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css";

</style>
<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript" src="http://datatables.net/release-datatables/media/js/jquery.dataTables.js"></script>

<script type="text/javascript" charset="utf-8">
$(document).ready(function()
  {
    var asInitVals = new Array();
	
    var oTable = $('#example').dataTable
    ({
      'bServerSide'    : true,
      'sAjaxSource'    : 'ajax.php',
    });
			
    $("tfoot input").keyup( function () {
    	/* Filter on the column (the index) of this element */
    	oTable.fnFilter( this.value, $("tfoot input").index(this) );
    } );

    /*
     * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
     * the footer
     */
    $("tfoot input").each( function (i) {
    	asInitVals[i] = this.value;
    } );
    
    $("tfoot input").focus( function () {
    	if ( this.className == "search_init" )
    	{
    		this.className = "";
    		this.value = "";
    	}
    } );
    
    $("tfoot input").blur( function (i) {
    	if ( this.value == "" )
    	{
    		this.className = "search_init";
    		this.value = asInitVals[$("tfoot input").index(this)];
    	}
    } );	

 });
</script>
</head>
<body id="dt_example">
<div id="container">
<h1>Ignited Datatables - Individual Column Filtering</h1>
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
	<tfoot>
		<tr>
			<th><input type="text" name="search_engine" value="ID" class="search_init" /></th>
			<th><input type="text" name="search_browser" value="Title" class="search_init" /></th>
			<th><input type="text" name="search_platform" value="Year" class="search_init" /></th>
			<th><input type="text" name="search_version" value="Length" class="search_init" /></th>
			<th><input type="text" name="search_grade" value="Rating" class="search_init" /></th>
		</tr>
	</tfoot>
  </table>
</div>


</body>
</html>