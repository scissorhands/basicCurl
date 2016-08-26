<?php

class Curl {
	private $login;
	private $pass;

	public function __construct( $login = null, $pass = null )
	{
		$this->set_auth( $login, $pass );	
	}

	public function set_auth(  $login = null, $pass = null )
	{
		$this->login = $login;
		$this->pass = $pass;
	}

	public function call( $url, $method = "GET", $postFields = [], $customHeaders = [] )
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method );
		if( $postFields ){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields );
		}
		if( $customHeaders ){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		if( $this->login ){
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->login}:{$this->pass}");
		}

		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}
?>