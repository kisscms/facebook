
<div id="fb-root"><!-- --></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?=FB_APPID?>";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script> 
  FB.init({appId: "<?=FB_APPID?>", status: true, cookie: true, oauth : true });
  
  // Common Functions

  function addToPage() {

    // calling the API ...
    var obj = {
      method: 'pagetab',
      redirect_uri: 'http://apps.facebook.com/<?=FB_URI?>/',
    };

    FB.ui(obj);
  }
  
  function openTabLink(url){
	  window.top.location.href = url;
  }
  
  function postToProfile(){
	  
  }

</script>