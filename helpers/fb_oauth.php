<?php
// FIX - to include the base OAuth lib not in alphabetical order
$oauth = getPath("helpers/kiss_oauth.php");
( $oauth ) ? require_once( $oauth ) : die("The site is offline as a nessesary plugin is missing. Please install oauth: github.com/kisscms/oauth");

if( !class_exists("Fb_OAuth") ){

/* Facebook OAuth for KISSCMS */
class Fb_OAuth extends KISS_OAuth_v2 {
	public $client;

	function  __construct( $api="facebook", $url="https://graph.facebook.com/oauth") {

		$this->url = array(
			'authorize' 		=> "https://www.facebook.com/dialog/oauth",
			'access_token' 		=> $url ."/access_token",
			'refresh_token' 	=> $url ."/access_token"
		);

		// FIX: duplicate the appId so it can be used by the parent __construct
		$GLOBALS['config']['facebook']['key'] = $GLOBALS['config']['facebook']['appId'];

		$this->redirect_uri = url("/oauth/api/fb");

		parent::__construct( $api, $url );

	}

	function save( $response ){

		// erase the existing creds
		unset($_SESSION['oauth']['facebook']);
		unset($_SESSION['user']);

		// convert string into an array
		parse_str( $response, $auth );

		if( is_array( $auth ) && array_key_exists("expires", $auth) )
			// variable expires is the number of seconds in the future - will have to convert it to a date
			$auth['expiry'] = date(DATE_ISO8601, (strtotime("now") + $auth['expires'] ) );

		// save to the user session
		$_SESSION['oauth']['facebook'] = (array) $auth;
	}


	function checkToken(){
		// Facebook specific methods...
		$token = $this->getToken();
		// prerequisite
		if( $token && isset($this->client) ){
			// Get the access token metadata from /debug_token
			$tokenMetadata = $this->client->debugToken( $token );
			// pickup expiry date
			$expiry = $tokenMetadata->getExpiresAt();
			// FIX: convert to string
			$expiry = $expiry->format($expiry::W3C);
			// Validation (these will throw FacebookSDKException's when they fail)
			//$tokenMetadata->validateAppId( $this->config['appId'] );
			// If you know the user ID this access token belongs to, you can validate it here
			//$tokenMetadata->validateUserId('123');
			//$tokenMetadata->validateExpiration();

			// extend creds
			if( !is_array( $_SESSION['oauth'][$this->api] ) )
				$_SESSION['oauth'][$this->api] = array();

			array_merge( $_SESSION['oauth'][$this->api], array(
				"access_token" => $token,
				"expiry" => $expiry
			));
		}
		// continue...
		return parent::checkToken();
	}

	function getToken(){
		$accessToken = null;
		if( !empty( $_SESSION['oauth'][$this->api]["access_token"] ) )
			$accessToken = $_SESSION['oauth'][$this->api]["access_token"];
		/*
		//array( 'access_token' => (string) $token->getValue() );
		$token = $this->getAccessToken();



		if (! $token->isLongLived()) {
			// Exchanges a short-lived access token for a long-lived one
			try {
				$token = $oAuth2Client->getLongLivedAccessToken($token);
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				//echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
			}
		}
		//
		try {
			$accessToken = $this->app->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			//echo 'Graph returned an error: ' . $e->getMessage();
			//exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			//echo 'Facebook SDK returned an error: ' . $e->getMessage();
			//exit;
		}
		*/
		return $accessToken;
	}

	function updateToken( $data=array() ){
		// prerequisite
		if( !is_array( $data ) ) return;
	}

}
}

?>
