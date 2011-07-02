<?php 
require_once('../../Datatables.php');
$datatables = new Datatables();

// MYSQL configuration
$config = array(
'username' => 'root',
'password' => '',
'database' => 'sakila',
'hostname' => 'localhost');

$datatables->connect($config);

$datatables
->select('customer_id, first_name, last_name, email')
->from('customer')
->add_column('edit', '<a href="#$1" title="Edit:$2 $3">Edit</a>', 'customer_id, first_name, last_name')
->add_column('delete', '<a href="#$1" title="Delete:$2 $3">Delete</a>', 'customer_id, first_name, last_name');

echo $datatables->generate();
?>