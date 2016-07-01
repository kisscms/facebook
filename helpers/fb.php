<?php
/*
	Facebook wrapper for KISSCMS
	Simple Facebook connection with KISSCMS using the official PHP SDK
	Homepage: http://kisscms.com/plugins
	Created by Makis Tracend (@tracend)
*/

if( !class_exists("FB") ){

class FB {

	public $name = "facebook";
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
		$this->facebook = new Facebook\Facebook([
			'app_id' => $this->config['appId'],
			'app_secret' => $this->config['secret'],
			'default_graph_version' => 'v2.5',
			//'default_access_token' => '{access-token}', // optional
			'cookie' => true
		]);
		// application object
		//$this->app = new Facebook\FacebookApp($this->config['appId'], $this->config['secret']);
		$this->app = $this->facebook->getApp();

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
		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->facebook->getOAuth2Client();
		// load all the necessery subclasses
		$this->oauth = new Fb_OAuth();
		// pass the OAuth2 client
		$this->oauth->client = $oAuth2Client;
		// get user data
		$this->request = $this->parsePageSignedRequest();
		// set the access_token from the request if available
		//var_dump( $this->request );
	}


	function login(){

		// check the request in case the token is already recieved
		if( $this->request && array_key_exists('oauth_token' , $this->request) ){
			// save this token back to oauth->creds()
			$this->creds = $this->oauth->creds( array( 'access_token' => $this->request['oauth_token'] ) );
		} else {
			// get/update the creds from the oauth
			$this->creds = $this->oauth->creds();
		}

		if( !empty($this->creds['access_token']) ){
			// v5 SDK
			$_SESSION['facebook_access_token'] = $this->creds['access_token'];
			$this->facebook->setDefaultAccessToken( $this->creds['access_token'] );
			// legacy
			//$this->facebook->setAccessToken($this->creds['access_token']);
		}
		// check if the credentials are empty (only the token matters?)
		return !empty($this->creds['access_token']);
	}

	// connects to the service to get the user object
	function me( $fields="id,name,picture,link,about" ){
		// make sure we're logged in...
		$login = $this->login();
		// exit now if we're not logged in
		if (!$login) return false;
		//
		$token = $this->creds['access_token'];
		// ping Facebook Graph for the (updated) user details)
		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$api = $this->facebook->get("/me?fields=". $fields, $token);
			// get data and save for later
			$body = $api->getBody();
			// parse to an array
			$this->user = ( is_scalar($body) ) ? json_decode($body, true) : $body;
			//$this->request = $this->facebook->getSignedRequest();
			// normalize data
			if( !empty($this->user['picture']['data']['url']) ) $this->user['picture'] = $this->user['picture']['data']['url']; // error control?

			return $this->user;
		} catch (FacebookApiException $e) {
			//error_log($e);
			// fallback to the session values
			//$this->page = $_SESSION['fb_page'];
			return false;
		}
		// in any other case...
		return false;

	}

	function get( $service="", $params=array() ){

		// check cache before....

		// add access_token
		if( empty($params['access_token']) ) $params['access_token'] = $this->creds['access_token'];
		// why do I need to do this again?
		$this->facebook->setDefaultAccessToken($params['access_token']);
		$query = http_build_query($params);

		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$results = $this->facebook->get( $service ."?". $query  );
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
		$token = $this->getAccessToken();
		// prerequisite
		if( !$token ) return null;
		//
		$api = $this->facebook->get("/". FB_APPID ."/roles", $token->getValue() );
		$body = $api->getBody();
		if( !empty($body) ){
			// parse to an array
			if( is_scalar($body) ) $body = json_decode($body, true);
			//
			foreach( $body['data'] as $admin ){
				if( array_key_exists('user', $admin) ){
					$admins[] = $admin['user'];
				}
			}
		}
		return implode(",", $admins);

	}

	// getting an app level access token
	function getAccessToken(){
		$token = $this->app->getAccessToken();
		return $token;
	}

	// simplified version of Facebook's getLoginUrl
	function getLoginUrl( $type="website" ){
		$options = array();
		// localhost always uses OAuth?
		if( $type=="website" || IS_LOCALHOST ){
			// do nothing - defaults should be ok
		} else if( $type=="tab" ){
			// get page tab from request
			$options['redirect_uri'] = "https://facebook.com/". $this->request["page"]["id"] ."?sk=app_". FB_APPID;
		} else if ( $type=="canvas" ){
			$options['redirect_uri'] = "https://apps.facebook.com/". $GLOBALS['config']['facebook']['uri'];
		} else {
			// also include $type == app ?
		}
		return Fb_OAuth::link( $options, false);
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
			// restore signed request in the $_REQUEST obj to let the SDK find it
			$_REQUEST['signed_request'] = $_SESSION["fb"]["request"];
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
}

?>
