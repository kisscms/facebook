<div id="fb-root"><!-- --></div>
<script type="text/javascript" data-type="require">
	// backwards compatibility...
	var fb_appId = <?=$config['facebook']['appId']?>;
	var fb_uri = "<?=$config['facebook']['uri']?>";
	// new vars
	var fb = {
		key : "<?=$config['facebook']['appId']?>", 
		uri : "<?=$config['facebook']['uri']?>",
		token : <? echo ( empty($_SESSION['oauth']['facebook']['access_token']) ) ? false : '"'.$_SESSION['oauth']['facebook']['access_token'] .'"'; ?>
	}
</script>
<script type="text/javascript" src="<?=url("/js/facebook.js")?>" data-type="require" data-path="facebook"></script>
