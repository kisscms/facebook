window.fbAsyncInit = function() {
	
	
	FB.init({
	  appId      : fb_appId, // App ID
	  //channelUrl : '//'+window.location.hostname+'/channel', // Path to your Channel File
	  status     : true, // check login status
	  cookie     : true, // enable cookies to allow the server to access the session
	  xfbml      : true,  // parse XFBML
	  oauth : true 
	});
	
	//console.log(FBcommon);
	fb_call = new FBcalls();
}


// Common Functions
function FBcalls() {}


FBcalls.prototype = {
	
	addToPage: function() {

		// calling the API ...
		var obj = {
		  method: 'pagetab',
		  redirect_uri: 'http://apps.facebook.com/fb_appId/',
		};
		
		FB.ui(obj);
	}, 

	openTabLink: function (url){
		window.top.location.href = url;
	}, 
	
	postToProfile: function (){
	  
	}
	
}

