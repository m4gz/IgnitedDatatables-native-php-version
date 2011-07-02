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
->select('cat_id, name, count_pages')
->from('(SELECT category.category_id as cat_id, name, COUNT(category.category_id) as count_pages
         FROM category 
         LEFT JOIN film_category ON film_category.category_id = category.category_id
		 GROUP BY category.category_id) as category');

echo $datatables->generate();
?>