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

if(isset($_POST['min_length']) && $_POST['min_length'] != '')
 $datatables->where('length >=', $_POST['min_length']);
 
if(isset($_POST['max_length']) && $_POST['max_length'] != '')
 $datatables->where('length <=', $_POST['max_length']);
 
echo $datatables->generate();
?>