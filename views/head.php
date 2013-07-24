<meta property="fb:app_id" content="<?=FB_APPID?>" />
<meta property="fb:admins" content="<?=FB_ADMINS?>" />
<?
// these vars will be available in the KISSCMS client object
$GLOBALS['client']['fb']['appId'] = $config['facebook']['appId'];
$GLOBALS['client']['fb']['uri'] = $config['facebook']['uri'];
$GLOBALS['client']['fb']['token']  = ( empty($_SESSION['oauth']['facebook']['access_token']) ) ? false : $_SESSION['oauth']['facebook']['access_token'];
?>
<script type="text/javascript" data-type="require">
	var fb_appId = <?=$config['facebook']['appId']?>;
</script>
<? // Load base lib ?>
<script type="text/javascript" src="//connect.facebook.net/en_US/all.js" data-type="require" data-path="facebook"></script>
<script type="text/javascript" src="<?=url("/assets/js/helpers/facebook.js")?>" data-type="require" data-path="facebook-helper" data-deps="facebook"></script>
