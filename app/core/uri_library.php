<?php

/* The URI Library is necessary for the framework
 * to succesfully route
 */
class Uri_library {
	private $uri_array = array();
	public function __construct($uri=NULL) {
		if(isset($uri)) {
			$this->parseURI($uri);
		}
	}

	public function parseURI($uri=NULL) {
		if(substr($uri,0,10)=="/index.php") {
			$uri = substr($uri,10);
		}
		$uri=substr($uri,1);
		$this->uri_array = explode("/",$uri);
	}

	public function getFullArray() {
		return $this->uri_array;
	}

	public function getItem($iid=0) {
		if(isset($this->uri_array[$iid])) {
			return $this->uri_array[$iid];
		}
		return false;
	}

	public function redirect($url=NULL) {
		if(isset($url)) {
			header('Location: '.$url);
		}
	}
}


?>
