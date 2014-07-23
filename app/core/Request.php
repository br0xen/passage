<?php

/* The URI Library is necessary for the framework
 * to succesfully route
 */
class Request {
  public $type;
  public $parameters;
  public $cookie;
  public $files;
  public $server;

  public $uri_array;

  private $_anvil;

  private static $request_params = null;
  
	public function __construct($a) {
    $this->_anvil = $a;
    $this->createFromGlobals();
	}

  public function createFromGlobals() {
    $this->type = strtoupper($_SERVER['REQUEST_METHOD']);
    if($this->type == 'GET') {
      $this->parameters = $_GET;
    } else {
      $this->parameters = $this->process_data();
    }
    $this->cookie = $_COOKIE;
    $this->files = $_FILES;
    $this->server = $_SERVER;
    $this->uri_array = $this->uriToArray();
  }

  /** Is this an HTTPS request? */
  public function isSecure() {
    return !(!isset($this->server['HTTPS']) 
              || empty($this->server['HTTPS'])
              || strtolower($this->server['HTTPS']) === 'off');
  }

  /** Pull the JSON Payload **/
  public function json() {
    if(!self::$request_params) {
      $payload = file_get_contents('php://input');
      if(is_array($payload)) {
        self::$request_params = $payload;
      } else if((substr($payload, 0, 1) == "{")
          && (substr($payload, (strlen($payload)-1), 1) == "}")) {
        self::$request_params = json_decode($payload);
      } else {
        parse_str($payload, self::$request_params);
      }
    }
    return (object)self::$request_params;
  }

  public function post($index=NULL, $xss_clean=FALSE) {
    return $this->process_data($index, $xss_clean);
  }
  public function put($index=NULL, $xss_clean=FALSE) {
    return $this->process_data($index, $xss_clean);
  }
  public function patch($index=NULL, $xss_clean=FALSE) {
    return $this->process_data($index, $xss_clean);
  }
  public function delete($index=NULL, $xss_clean=FALSE) {
    return $this->process_data($index, $xss_clean);
  }

  public function process_data($index=NULL, $xss_clean=FALSE) {
    $request_vars = (array)$this->json();
    if($index==NULL && !empty($request_vars)) {
      $post = array();
      foreach(array_keys($request_vars) as $key) {
        $post[$key] = $this->_fetch_from_array($request_vars, $key, $xss_clean);
      }
      return $post;
    }
    return $this->_fetch_from_array($request_vars, $index, $xss_clean);
  }

  private function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE) {
    if(!isset($array[$index])) {
      return FALSE;
    }

    if($xss_clean === TRUE) {
      
    }
    return $array[$index];
  }

  public function setCookie($key, $val, $expire=0, $path='/', $domain=NULL, $secure=0) {
    if($expire == 'never') { 
      // We can't do never... Set it to 10 years, should be good enough.
      $expire = time()+60*60*24*365*10;
    }
    if(!isset($path) && ($this->_anvil->config->item('cookie_path') !== FALSE)) {
      $path = $this->_anvil->config->item('cookie_path');
    }
    if(!isset($domain)) {
      if($this->_anvil->config->item('cookie_domain') !== FALSE) {
        $domain = $this->_anvil->config->item('cookie_domain');
      } else {
        $domain = $this->_anvil->request->server['HTTP_HOST'];
      }
    }
    setcookie($key, $val, $expire, $path, $domain, $secure);
  }

  public function clearCookie($key) {
    $this->setCookie($key, '', time()-3600*24*365);
  }

  /**
   * URI Parsing Functions
   */
  public function uriToArray($st = 0, $uri=NULL) {
    $uri = (isset($uri)?$uri:$this->server['REQUEST_URI']);
    if(substr($uri,0,10)=='/index.php') {
      $uri = substr($uri,10);
    }
    $uri=substr($uri,1);
    $uri_array = preg_split('^[\/\?]^', $uri);
    $uri_array = array_slice($uri_array, $st);
    return $uri_array;
  }

  /**
   * Parse URI into key=>value pairs
   */
  public function uriToPairs($st = 0, $uri=NULL) {
    $uri = (isset($uri)?$uri:$this->server['REQUEST_URI']);
    $uri_array = $this->uriToArray($st, $uri);
    $pair_array = array();
    $key = '';
    foreach($uri_array as $a_val) {
      if(empty($key)) {
        $key = $a_val;
      } else {
        $pair_array[$key] = $a_val;
        $key = '';
      }
    }
    if(!empty($key)) {
      $pair_array[$key] = '';
    }
    return $pair_array;
  }
}


?>
