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
->select('first_name, last_name, email, postal_code, city, country')
->from('customer')
->join('address', 'address.address_id = customer.address_id', 'left')
->join('city', 'address.city_id = city.city_id', 'left')
->join('country', 'country.country_id = city.country_id', 'left')
->edit_column('first_name', '<a href="#$3">$1 $2</a>', 'first_name, last_name, email')
->edit_column('email', '<a href="#" title="Address: $2, $3, $4">$1</a>', 'email, postal_code, city, country')
->unset_column('last_name');

echo $datatables->generate();
?>