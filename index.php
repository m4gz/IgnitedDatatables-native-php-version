<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" type="image/ico" href="http://www.datatables.net/media/images/favicon.ico" />
<title>DataTables example</title>
<style type="text/css" title="currentStyle">
@import "http://datatables.net/release-datatables/media/css/demo_page.css";
@import "http://datatables.net/release-datatables/media/css/demo_table.css";
</style>
<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="http://datatables.net/release-datatables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function()
  {
    $('#example').dataTable
    ({
      "bProcessing": true,
      'bServerSide'    : true,
      'bAutoWidth'     : false,
      'sPaginationType': 'full_numbers',
      'sAjaxSource'    : 'ajax.php'
    });
  });
</script>
</head>
<body id="dt_example">
<div id="container">
  <table border="0" cellpadding="4" cellspacing="0" class="display" id="example">
    <thead>
      <tr>
        <th width="10%">Film ID</th>
        <th width="20%">Title</th>
        <th width="45%">Description</th>
        <th width="10%">Year</th>
        <th width="15%">Category</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>loading...</td>
      </tr>
    </tbody>
  </table>
</div>
</body>
</html>