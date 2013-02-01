<meta property="fb:app_id" content="<?=FB_APPID?>" />
<meta property="fb:admins" content="<?=FB_ADMINS?>" />
<?
$GLOBALS['client']['fb']['key'] = $config['facebook']['appId'];
$GLOBALS['client']['fb']['uri'] = $config['facebook']['uri'];
$GLOBALS['client']['fb']['token']  = ( empty($_SESSION['oauth']['facebook']['access_token']) ) ? false : $_SESSION['oauth']['facebook']['access_token'];
?>
