<?php
// FIX - to include the base OAuth lib not in alphabetical order
require_once( APP . "plugins/oauth/helpers/kiss_oauth.php" );

/* Facebook OAuth for KISSCMS */
class Fb_OAuth extends KISS_OAuth_v2 {
	
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
		//$fb = new FB();
		//$fb->deleteCache();
		
		// convert string into an array
		parse_str( $response, $auth );
		
		if( is_array( $auth ) && array_key_exists("expires", $auth) )
			// variable expires is the number of seconds in the future - will have to convert it to a date
			$auth['expiry'] = date(DATE_ISO8601, (strtotime("now") + $auth['expires'] ) );
		
		// save to the user session 
		$_SESSION['oauth']['facebook'] = (array) $auth;
	}
	
}

?>