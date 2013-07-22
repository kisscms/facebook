window.fbAsyncInit = function() {

	FB.init({
		appId 		: fb_appId, // App ID
		//channelUrl : '//'+window.location.hostname+'/channel', // Path to your Channel File
		status 		: true, // check login status
		cookie 		: true, // enable cookies to allow the server to access the session
		xfbml 		: true,  // parse XFBML
		oauth 		: true
	});

	//console.log(FBcommon);
	fb_call = new FBcalls();

};

// Load base lib
(function(d,t) {
	var id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
	var js = d.createElement(t); js.type = 'text/javascript'; js.id = id; js.async = true;
	js.src = ("https:"==location.protocol?"https:":"http:")+"//connect.facebook.net/en_US/all.js";
	var s = document.getElementsByTagName(t)[0]; s.parentNode.insertBefore(js, s);
})(document,"script");


// Common Functions
function FBcalls() {}


FBcalls.prototype = {

	requestLogin: function(){

		FB.ui({
		  method: 'oauth',
		  client_id: fb_appId,
		  redirect_uri: 'https://apps.facebook.com/'+ fb_uri +'/'
		},
			function (response) {
				if( typeof( response ) != "undefined") {
					if(response.session) {
						//var user = JSON.parse(response.session);
						// save the userid in the form
						//$("#entry-form").find("input[name='fbid']").val(user.uid);
						top.location.href = 'https://apps.facebook.com/'+ fb_uri +'/';
					} else {
						// No session
						top.location.href = 'http://facebook.com/';
					}
				} else {
					// denied access
					top.location.href = 'http://facebook.com/';
				}
			}
		);

	},

	addToPage: function() {

		// calling the API ...
		var obj = {
			method: 'pagetab',
			redirect_uri: 'https://apps.facebook.com/'+ fb_uri +'/',
		};

		FB.ui(obj);
	},

	openTabLink: function (url){
		window.top.location.href = url;
	},

	postToProfile: function (){

	}

}

