<div id="fb-root"><!-- --></div>
<script type="text/javascript" data-type="require">
	var fb_appId = <?=$config['facebook']['appId']?>;
	var fb_uri = "<?=$config['facebook']['uri']?>";
	(function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'http://connect.facebook.net/en_US/all.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
</script>
<script type="text/javascript" src="<?=url("js/facebook.js")?>" data-type="require"></script>
