<?php

class Response {
  private $_anvil;

	public function __construct($a) {
    $this->_anvil = $a;
	}

  public function redirect($url) {
    header('Location: '.$url);
  }
}


?>
