<div id="fb-root"><!-- --></div>
<script type="text/javascript" data-type="require">
	// backwards compatibility...
	var fb_appId = <?=$config['facebook']['appId']?>;
	var fb_uri = "<?=$config['facebook']['uri']?>";
</script>
// Load base lib
<script type="text/javascript" src="//connect.facebook.net/en_US/all.js" data-type="require" data-path="facebook"></script>
<script type="text/javascript" src="<?=url("/assets/js/helpers/facebook.js")?>" data-type="require" data-path="facebook-init" data-deps="facebook"></script>
