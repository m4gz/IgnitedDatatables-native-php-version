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
->add_column('edit', '<a href="#$1">Edit</a>', 'customer_id')
->add_column('delete', '<a href="#$1">Delete</a>', 'customer_id')
->edit_column('first_name', '$1', 'callback_strtolower(first_name)')
->edit_column('first_name', '$1', 'callback_ucwords(first_name)')
->edit_column('email', '$1', 'callback_strtolower(email)')
->edit_column('email', '<i>$2..$1</i>', 'callback_strstr(email,@), callback_substr(email,0,3)');

echo $datatables->generate();
?>