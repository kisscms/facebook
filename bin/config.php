<?php


//===============================================
// Configuration
//===============================================

if( class_exists('Config') && method_exists(new Config(),'register')){ 

	// Register variables
	Config::register("facebook", "name", "App Name");
	Config::register("facebook", "appId", "01234567890");
	Config::register("facebook", "secret", "012345678901234567890123456789");
	Config::register("facebook", "uri", "appname");
	Config::register("facebook", "scope", "email,status_update,publish_stream");
	Config::register("facebook", "dev_site", "http://url/of/dev/app");
	Config::register("facebook", "admins", "");
	//Config::register("facebook", "fileUpload", "false");
	//Config::register("facebook", "cookie", "true");
	
	// Definitions
	define('FB_APPID', $GLOBALS['config']['facebook']['appId']);
	define('FB_SECRET', $GLOBALS['config']['facebook']['secret']);
	define('FB_URI', $GLOBALS['config']['facebook']['uri']);
	define('FB_ADMINS', $GLOBALS['config']['facebook']['admins']);
	
}

?>