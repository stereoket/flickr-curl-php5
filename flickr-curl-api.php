<?php
/*
 * Flickr Curl Api Wrapper
 * 
 * Simple Curl wrapper that sets up a call to the flickr API and produces a php object and raw response data in json or xml.
 *
 */
class FlickrCurlAPI {

	public $endpoint = 'http://api.flickr.com/services/rest/';
	private $apiKey = '';
	public $method = '';
	public $params = '';
	public $paramString = '';
	public $userid = '';
	public $rawResponse = '';
	public $objectResponse = '';
	public $debug = 0;
	
	public function __construct($userid, $debug=0) {
		$this->userid = $userid;
		$this->debug = $debug;
		ini_set('display_errors',$debug);
	return true;
	}

/*
 * Builds the Request String used in the REST call 
 * 
 * 
 *
 */
	public function buildRequestString() {
		$this->paramString = "?" . 'method='.$this->method . '&user_id=' . $this->userid .'&api_key=' . $this->apiKey;
		foreach($this->params as $key => $paramsval){
			$this->paramString .= '&';
			$this->paramString .= $key.'='.$paramsval;
		}
	return true;
	}

/*
 * Make the Call to flickr API
 * 
 * 
 *
 */
	public function api_flickr_call() {
		self::buildRequestString();
		self::sendAPIRequest();
		self::phpResponseObject();
		if ($this->debug){
			var_dump($this->rawResponse);
			var_dump($this->objectResponse);
		}
	return true;
	}
/*
 * The Actual Curl request to the flickr SERVER
 * 
 * 
 *
 */
	public function sendAPIRequest(){
		$curl = curl_init($this->endpoint.$this->paramString);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		$this->rawResponse = curl_exec($curl);
// error handling required here
		curl_close($curl);
	return true;
	}
/*
 * Formatting the Raw response to json or simpleXML 
 * 
 * 
 *
 */	
	public function phpResponseObject(){
		if(isset($this->params['format']) && $this->params['format'] == 'json'){
			$this->objectResponse = json_decode(preg_replace('/.+?({.+}).+/','$1',$this->rawResponse), true);
		} else {
			$this->objectResponse = simplexml_load_string($this->rawResponse);      
		}  
	return true;
	}
}  
?>