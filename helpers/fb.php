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
	
	// Facebook functions
	function __construct(){ 
		// setup 
		$this->uid 		= false;
		$this->request 	= false;
		
		// init
		$facebook = $this->facebook = new Facebook(array(
		  'appId' => FB_APPID,
		  'secret' => FB_SECRET,
		  'cookie' => true,
		));
		
		//Facebook Authentication part
		$this->uid = $facebook->getUser();
		
		$this->loginUrl = $facebook->getLoginUrl(
				array(
					'scope' => $GLOBALS['config']['facebook']['scope'],
					'redirect_uri' => 'http://apps.facebook.com/'. FB_URI .'/'
				)
		);
	 	
		if ($this->uid) {
		  try {
			// Proceed knowing you have a logged in user who's authenticated.
			$this->user = $facebook->api("/".$this->uid);
			$this->request = $facebook->getSignedRequest();
			
		  } catch (FacebookApiException $e) {
			//error_log($e);
			// fallback to the session values
			
			$this->page = $_SESSION['fb_page'];
		  }
		}
 	
		return $this;
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
	
	function redirect(){
		$data = array();
		$data['url'] = $this->loginUrl;
		$data['view'] = getPath('facebook/views/redirect.php');
		return $data;
	}
	
	
	// Cache
	
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
	
}


?>