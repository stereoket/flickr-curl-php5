<?php
/*
 * Flickr Curl Api Wrapper
 * @author Ketan Kunj Majmudar
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
	public $fail = false;
	public $cache = false;
	public $cachePeriod = 5;
	public $methodInstance = '';
	public $cachePath = '';
	public $cacheFile = '';
	
	public function __construct($userid, $debug=0) {
		$this->userid = $userid;
		$this->debug = $debug;
		ini_set('display_errors',$debug);
		date_default_timezone_set('Europe/London');
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
		$this->cacheFile = $this->cachePath . $this->method . '-' . $this->methodInstance; 
		if ($this->debug) var_dump($this->cacheFile);
// Checking to see if the cache exists and will return this version instead.
/*	echo '<br />' .$this->cacheFile;
	echo '<br />' .file_exists($this->cacheFile);
	echo '<br />' .time();
	echo '<br />' .$this->cachePeriod * 60;
	echo '<br />' .filemtime($this->cacheFile);
*/	
	
		if (file_exists($this->cacheFile) && ((time() - ($this->cachePeriod * 60)) < filemtime($this->cacheFile))){
			self::readFromCache();
			return true;
		}
	
		self::buildRequestString();
		self::sendAPIRequest();
		self::phpResponseObject();
		if ($this->debug){
			var_dump($this->rawResponse);
			var_dump($this->objectResponse);
		}
		// error handling required here
		$checkResponse = get_object_vars($this->objectResponse->attributes());
		if ($checkResponse['@attributes']['stat'] == 'fail'){
			$this->fail = true;
			$err = get_object_vars($this->objectResponse->children()->attributes());
			echo '<span class="error">'.$err['@attributes']['code'] . ' ' . $err['@attributes']['msg'].' !</span>';
			return false;
		} elseif($this->cache) {
			self::writeToCache();
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
/*
 * Cache XML response - write
 * 
 * 
 *
 */	
	public function writeToCache(){
		$f = fopen($this->cacheFile, "w");
		if (fwrite($f, $this->rawResponse) == false) {
			throw new Exception ("Can't write the file (please check the folder permissions) : " . $this->cacheFile);
			return false;
		}
		fclose($f);
		echo '<!-- Written to cache -->';
		return true;
	}
/*
 * Cache XML response - Check exsiting and read in new value
 * 
 * 
 *
 */	
	public function readFromCache(){
		// read in value from cache file - check config
		$f = fopen($this->cacheFile, "r");
		$this->rawResponse = fread($f, filesize($this->cacheFile));
		fclose($f);
		self::phpResponseObject();
		if ($this->debug){
			var_dump($this->rawResponse);
			var_dump($this->objectResponse);
		}
	echo "<!-- Read From Cache -->";
	return true;
}	
/*
 * Preapre Photos into thumbnails
 * 
 * The URL for various sizes and the output code to link the image to the original flickr photo page can be set here.
 *
 */	
	public function preparePhotoThumbnails(){
	
		$photoCount = count($this->objectResponse->photos->photo);
		for ($i=0; $i <= $photoCount; $i++){
			$photoArray[] = get_object_vars($this->objectResponse->photos->photo[$i]);
		}
		for ($i=0; $i < (count($photoArray) - 1); $i++){
			$t_url = "http://farm" . $photoArray[$i]['@attributes']['farm'] .  ".static.flickr.com/" . $photoArray[$i]['@attributes']['server'] . "/" . $photoArray[$i]['@attributes']['id'] . "_" .  $photoArray[$i]['@attributes']['secret'] . "_" . "t.jpg";
			
			$m_url = "http://farm" . $photoArray[$i]['@attributes']['farm'] .  ".static.flickr.com/" . $photoArray[$i]['@attributes']['server'] . "/" . $photoArray[$i]['@attributes']['id'] . "_" .  $photoArray[$i]['@attributes']['secret'] . "_" . "m.jpg";
			
			$imgArray[] = '<a href="http://www.flickr.com/photos/'.$this->userid.'/'.$photoArray[$i]['@attributes']['id'].'/" id="flickrLinks"><img src="'.$m_url.'" title="'.$photoArray[$i]['@attributes']['title'].'" /></a>';
		}
		return $imgArray;
	}
}  
?>