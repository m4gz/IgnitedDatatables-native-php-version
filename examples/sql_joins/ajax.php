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
->select('first_name, last_name, email')
->from('customer')
->join('address', 'address.address_id = customer.address_id', 'left')
->select('postal_code')
->join('city', 'address.city_id = city.city_id', 'left')
->select('city')
->join('country', 'country.country_id = city.country_id', 'left')
->select('country');

echo $datatables->generate();
?>