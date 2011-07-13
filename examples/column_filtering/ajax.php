<?php 
require_once('../../Datatables.php');
$datatables = new Datatables();  // for mysqli  =>  $datatables = new Datatables('mysqli'); 

// MYSQL configuration
$config = array(
'username' => 'root',
'password' => '',
'database' => 'sakila',
'hostname' => 'localhost');

$datatables->connect($config);

$datatables
->select('film_id, title, release_year, length, rating')
->from('film');
 
echo $datatables->generate();
?>