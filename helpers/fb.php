<?php
/*
	Facebook wrapper for KISSCMS
	Simple Facebook connection with KISSCMS using the official PHP SDK
	Homepage: http://kisscms.com/plugins
	Created by Makis Tracend (@tracend)
*/

class FB {

	public $facebook;
	public $uid;
	public $loginUrl;
	public $user;
	public $request;
	private  $creds;
	public $oauth;

	// Facebook functions
	function __construct(){
		// setup
		$this->uid 		= false;
		$this->request 	= false;

		$this->config = $GLOBALS['config']['facebook'];

		// init
		$this->facebook = new Facebook(array(
			'appId' => $this->config['appId'],
			'secret' => $this->config['secret'],
			'cookie' => true
		));

		// FIX: session ID is not being passed in IE.
		// reference http://stackoverflow.com/a/8600879
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');


		/*$this->loginUrl = $this->facebook->getLoginUrl(
				array(
					'scope' => $this->config['scope'],
					'redirect_uri' => 'http://apps.facebook.com/'. FB_URI .'/'
				)
		);*/

		$this->init();

		return $this;
	}

	// for all missing methods, it is assumed that we might be calling a facebook sdk method
	function __call($method, $arguments){

		try {
			// this won't work because the $arguments are enclosed in an array...
			//$response = $this->facebook->{$method}($arguments);
			$response = call_user_func_array( array($this->facebook, $method), $arguments);
		} catch (Exception $e) {
			die('Caught exception: '.  $e->getMessage() );
		}

		return $response;
	}

	function init(){
		// load all the necessery subclasses
		$this->oauth = new FB_OAuth();
		// get user data
		$this->request = $this->parsePageSignedRequest();
		// set the access_token from the request if available
		//var_dump( $this->request );
	}


	function login(){

		// get/update the creds
		$this->creds = $this->oauth->creds();

		if( !empty($this->creds['access_token']) ){
			$this->facebook->setAccessToken($this->creds['access_token']);
		}
		// check if the credentials are empty (only the token matters?)
		return !empty($this->creds['access_token']);

	}

	function me(){
		// connect to the service to get the user object

		//Facebook Authentication part
		$uid = $this->facebook->getUser();

		if ($uid) {
			try {
				// Proceed knowing you have a logged in user who's authenticated.
				$this->user = $this->facebook->api("/me");
				//$this->request = $this->facebook->getSignedRequest();
				return $this->user;
			} catch (FacebookApiException $e) {
				//error_log($e);
				// fallback to the session values
				//$this->page = $_SESSION['fb_page'];
			}
		}

	}

	function get( $service="", $params=array() ){

		// check cache before....

		// add access_token
		if( empty($params['access_token']) ) $params['access_token'] = $this->creds['access_token'];

		$this->facebook->setAccessToken($params['access_token']);
		$query = http_build_query($params);

		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$results = $this->facebook->api( $service ."?". $query  );
		} catch (FacebookApiException $e) {
			$results = $e;
		}
		// cache result
		//$this->setCache( $service, $params, $results );

		return $results;

	}

	function post(){

	}

	function delete(){

	}

	function getAdmins(){
		$admins = array();
		$api = $this->facebook->api("/fql?q=". urlencode("SELECT uid from page_admin WHERE page_id=".FB_APPID). "&format=json-strings");
		if( !empty($api) ){
			foreach( $api['data'] as $admin ){
				if( array_key_exists('uid', $admin) ){
					$admins[] = $admin['uid'];
				}
			}
		}
		return implode(",", $admins);

	}

	private function parsePageSignedRequest() {
		// prerequisite
		if( !array_key_exists("fb", $_SESSION) ) $_SESSION["fb"] = array();

		if (isset($_REQUEST['signed_request'])) {

			$encoded_sig = null;
			$payload = null;
			list($encoded_sig, $payload) = explode('.', $_REQUEST['signed_request'], 2);
			$sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
			$data = json_decode(base64_decode(strtr($payload, '-_', '+/'), true), true);
			// save to session
			$_SESSION["fb"]["request"] = $data;
			return $data;

		} elseif( isset($_SESSION["fb"]["request"]) ){

			return $_SESSION["fb"]["request"];

		} else {
			return false;
		}
	}

	function redirect(){
		$data = array();
		$data['url'] = $this->loginUrl;
		$data['view'] = getPath('facebook/views/redirect.php');
		return $data;
	}


	// Cache
	/*
	function getCache(){
		// set up the parent container, the first time
		if( !array_key_exists("facebook", $_SESSION) ) $_SESSION['facebook']= array();
		return $_SESSION['facebook'];

	}

	function setCache( $data ){
		// save the data in the session
		foreach( $data as $key => $result ){
			$_SESSION['facebook'][$key] = $result;
		}
		// update the local variable
		$this->cache = $this->getCache();
	}

	function checkCache( $type ){
		// always discard cache on debug mode
		if( DEBUG ) return false;

		if( !empty($this->cache[$type]) ) {
			// check the date
			$valid = true;
		}

		return ( $valid ) ? true : false;
	}

	function deleteCache(){
		unset($_SESSION['facebook']);
	}
	*/
}


?>