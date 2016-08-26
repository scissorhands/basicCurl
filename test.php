<?php 
require 'Curl.php';

function simple_call( $url )
{
	$curl = new Curl();
	$result = $curl->call( $url );
	return $result;
}

$test_url = "http://rest-service.guides.spring.io/greeting";
$response = simple_call( $test_url );
echo $response;

?>