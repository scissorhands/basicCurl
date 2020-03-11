<?php
namespace Curl;
class Curl {
	public $login;
	private $pass;
	public $verify_peer = null;
	public $curl_timeout = null;
	public $verify_host = null;

	public function __construct( $login = null, $pass = null )
	{
		$this->set_auth( $login, $pass );	
	}

	public function set_verify_peer($value)
	{
		$this->verify_peer = $value;
	}

	public function set_auth(  $login = null, $pass = null )
	{
		$this->login = $login;
		$this->pass = $pass;
	}

	public function set_curl_timeout($secs)
	{
		$this->curl_timeout = $secs;
	}

	public function set_verify_host($value)
	{
		$this->verify_host = $value;
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
		if($this->verify_peer !== NULL){
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, $this->verify_peer);
		}
		if($this->verify_host !== NULL){
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->verify_host);
		}
		
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		if($this->curl_timeout){
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		if( $this->login ){
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->login}:{$this->pass}");
		}

		$result = curl_exec($ch);
		$errors = curl_error($ch);
        curl_close($ch);
        if($errors){
            throw new \Exception("Curl error: {$errors}", 1);
        }
		return $result;
	}
}
?>