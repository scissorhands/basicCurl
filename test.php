<?php 
require 'Curl.php';
use Curl\Curl as Curl;
use Curl\ApiCurl as ApiCurl;
function simple_call( $url )
{
	$curl = new Curl();
	$result = $curl->call( $url );
	return $result;
}

function api_call($url)
{
	$curl = new ApiCurl();
	$result = $curl->call( $url );
	return $result;
}

$test_url = "http://rest-service.guides.spring.io/greeting";
$response = api_call( $test_url );
print_r($response);

?>