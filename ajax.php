<?php 
require_once('Datatables.php');
$datatables = new Datatables();

// MYSQL configuration
$config = array(
'username' => 'root',
'password' => '',
'database' => 'sakila',
'hostname' => 'localhost');

$datatables->connect($config);

$datatables
->select('film.film_id as id, title, description, release_year')
->from('film')
->join('film_category', 'film_category.film_id = film.film_id' )
->join('category', 'category.category_id = film_category.category_id' )
->select('name');

echo $datatables->generate();	 

?>