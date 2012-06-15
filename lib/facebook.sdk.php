<?php 

// Location of your Facebook SDK
// - optionally define a different location for localhost and production
if(IS_LOCALHOST){
	include_once( realpath("../../") ."/facebook_sdk/facebook.php");
} else {
	include_once( realpath("../../") ."/facebook_sdk/facebook.php");	
}
?>