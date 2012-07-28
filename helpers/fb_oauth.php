<?php
// FIX - to include the base OAuth lib not in alphabetical order
require_once( realpath("../") . "/app/plugins/oauth/helpers/kiss_oauth.php" );

/* Facebook OAuth for KISSCMS */
class Fb_OAuth extends KISS_OAuth_v2 {
	
	function  __construct( $api="fb", $url="https://graph.facebook.com/oauth") {

		$this->url = array(
			'authorize' 		=> "https://www.facebook.com/dialog/oauth", 
			'access_token' 		=> $url ."/access_token", 
			'refresh_token' 	=> $url ."/access_token"
		);
		
		$this->redirect_uri = url("/oauth/api/". $api);
		
		$this->client_id = $GLOBALS['config']['facebook']['appId'];
	 	$this->client_secret = $GLOBALS['config']['facebook']['secret'];
		
		$this->token = ( empty($_SESSION['oauth']['facebook']['access_token']) ) ? false : $_SESSION['oauth']['facebook']['access_token'];
	 	$this->refresh_token = ( empty($_SESSION['oauth']['facebook']['refresh_token']) ) ? false : $_SESSION['oauth']['facebook']['refresh_token'];
	 	
	}
	
	function save( $response ){
		
		// erase the existing creds
		unset($_SESSION['oauth']['facebook']);
		//$fb = new FB();
		//$fb->deleteCache();
		
		// convert string into an array
		parse_str( $response, $auth );
		
		if( is_array( $auth ) && array_key_exists("expires", $auth) )
			// variable expires is the number of seconds in the future - will have to convert it to a date
			$auth['expiry'] = date(DATE_ISO8601, (strtotime("now") + $auth['expires'] ) );
		
		// save to the user session 
		$_SESSION['oauth']['facebook'] = $auth;
	}
	
}

?>