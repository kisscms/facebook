window.fbAsyncInit = function() {

	FB.init({
		appId 		: fb_appId, // App ID
		//channelUrl : '//'+window.location.hostname+'/channel', // Path to your Channel File
		status 		: true, // check login status
		cookie 		: true, // enable cookies to allow the server to access the session
		xfbml 		: true,  // parse XFBML
		oauth 		: true
	});

};
