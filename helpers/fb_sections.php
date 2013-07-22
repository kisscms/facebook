<?php

class FB_Comments extends Section {

	function __construct($view=false, $vars=false, $data=false){
		parent::__construct($view,$vars);
		$this->data['options'] = $this->getOptions($data);
		$this->render();
	}

	private function getOptions($data=false){
		// currently options are not variable...
		$options = array(
				"href" => url($_SERVER["REQUEST_URI"]),
				"width" => "600",
				"colorscheme" => "light",
				"num_posts" => "10"
		);

		return $options;
	}

}

?>