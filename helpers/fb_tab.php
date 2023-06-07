<?
// FIX - to include the base OAuth lib not in alphabetical order
//$kiss_auth = getPath("helpers/auth.php");
//( $kiss_auth ) ? require_once( $kiss_auth ) : die("Failed to load class KISS_Auth");

// Main Tab controller
class FB_Tab {

	public $facebook;
	private $name;
	private $data;
	private $options;
	private $template;
	private $request;

	function __construct($controller_path,$web_folder,$default_controller,$default_function){

		// exit if there is no name
		//if( !$name ) return;

		//$defaults = array(
		//	"ssl" => false
		//);

		$this->config = $GLOBALS['config']['facebook'];

		// save variables
		//$this->name = $name;
		//$this->options = array_merge( $defaults, $options);

		// init
		$this->facebook = new Facebook(array(
			'appId' => $this->config['appId'],
			'secret' => $this->config['secret'],
			'cookie' => true
		));


		// FIX: session ID was not being passed in IE.
		// reference http://stackoverflow.com/a/8600879
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

		// get user data
		$this->request = $this->parsePageSignedRequest();
		// Get the current access token
		//$this->access_token = $this->facebook->getAccessToken();

		// post-processing
		if( $this->request ){
			//$this->getPage();
			//$this->getTabInfo();
			//$this->checkSSL();
		}
		// initialize template
		//$this->template = new miniTemplate( array( "path" => $_SERVER['DOCUMENT_ROOT'] . $this->options["template_uri"], "ext" => "html" ) );

		// render page
		//$this->render();

		//return parent::__construct($controller_path,$web_folder,$default_controller,$default_function);
	}

	/*
	public function render( $view, $vars=false ){

		// first pass the data we've gathered (if any)
		$this->template->set("data", $this->data);

		// set the view for the content
		$this->template->set("view", $view);

		// loop through the extra variables
		if($vars){
			foreach( $vars as $k => $v ){
				$this->template->set($k, $v);
			}
		}

		$output = $this->template->render();

		// final output
		echo $output;

	}
	*/
	/*
	public function login( $view ){

	}
	*/

	// Helpers

	public function getLike(){
		return ($this->request) ? $this->request->page->liked : false;
	}

	public function getUser(){
		//var_dump($this->access_token);
		//var_dump($this->request);
		return $this->facebook->getUser();
		//return json_decode(file_get_contents( "https://graph.facebook.com/me?access_token=". $this->access_token));
	}

	private function getPage(){
		$page_id = $this->request->page->id;
		$page = $this->facebook->api("/$page_id");
		// save for later...
		$this->data["page"] = $page;
	}

	private function getTabInfo(){
		$tab = array();

		// set the url of the page we are rendering
		$tab["url"] = $this->url();
		// get the page related link (users see)
		if ($this->options["ssl"] ) {
			// tab link has to be a secure link
			$tab["link"] = preg_replace("/^http:\/\//i", 'https://', $this->data["page"]["link"]."/app_".FB_AppID);
		} else {
			$tab["link"] = $this->data["page"]["link"] ."/app_".FB_AppID;
		}

		$this->data["tab"] = $tab;
	}

	private function checkSSL(){

		$secure = $this->options["ssl"];

		// Use only SSL
		if($secure && $_SERVER["HTTPS"] != "on") {
			// getTabInfo() has already converted the link to ssl
			echo "<script> top.location.href = '". $this->data["tab"]["link"] ."';</script>";
			exit();
		}

	}

	private function parsePageSignedRequest() {
		if (isset($_REQUEST['signed_request'])) {

			$encoded_sig = null;
			$payload = null;
			list($encoded_sig, $payload) = explode('.', $_REQUEST['signed_request'], 2);
			$sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
			$data = json_decode(base64_decode(strtr($payload, '-_', '+/'), true));
			// save to session
			$_SESSION["fb_signed_request"] = $data;
			return $data;

		} elseif( isset($_SESSION["fb_signed_request"]) ){

			return $_SESSION["fb_signed_request"];

		} else {
			return false;
		}
	}

	// public methods
	public function url(){
		$url = ($_SERVER["HTTPS"] == "on") ? "https" : "http";
		$url.= "://". $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		return $url;
	}

	public function expired( $date, $timezone=false){

		// (optionally) set timezone
		if($timezone) date_default_timezone_set( $timezone );

		$now = time("now");
		$expiry = strtotime($date);

		return ($expiry - $now < 0);
	}

}

?>
