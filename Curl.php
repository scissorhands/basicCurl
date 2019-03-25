<?php
namespace Curl;
class Curl {
	public $login;
	private $pass;
	public $verify_peer = null;
	public $curl_timeout = null;

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

class Response {
	public $httpcode;
	public $content;
	public $url;

	public function __construct($httpcode, $content, $url)
	{
		$this->httpcode = $httpcode;
		$this->content = $content;
		$this->url = $url;
	}

	public function json()
	{
		$json_obj = json_decode($this->content);
		if ( !$json_obj ) {
			$error = $this->jsondecode_error();
			throw new \Exception("Error parsing response: {$error}", 1);
		}
		return $json_obj;
	}

	public function jsondecode_error(){
		switch (json_last_error()) {
	        case JSON_ERROR_NONE:
	            return NULL;
	        break;
	        case JSON_ERROR_DEPTH:
	            return 'Maximum stack depth exceeded';
	        break;
	        case JSON_ERROR_STATE_MISMATCH:
	            return 'Underflow or the modes mismatch';
	        break;
	        case JSON_ERROR_CTRL_CHAR:
	            return 'Unexpected control character found';
	        break;
	        case JSON_ERROR_SYNTAX:
	            return 'Syntax error, malformed JSON';
	        break;
	        case JSON_ERROR_UTF8:
	            return 'Malformed UTF-8 characters, possibly incorrectly encoded';
	        break;
	        default:
	            return 'Unknown error';
	        break;
	    }
	}

}

class ApiCurl extends Curl{
	public function __construct($login = null, $pass = null )
	{
		parent::__construct($login, $pass);
	}

	public function call($url, $method = "GET", $postFields = [], $customHeaders = [])
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
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errors = curl_error($ch);
        curl_close($ch);
        if($errors){
            throw new \Exception("Curl error: {$errors}", 1);
        }
        return new Response($httpcode, $result, $url);
	}
}
?>