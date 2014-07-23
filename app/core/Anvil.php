<?php if(!defined('APP_ROOT')) exit('No direct script access allowed');

require_once(APP_ROOT.'/core/Config.php');
require_once(APP_ROOT.'/core/Helpers.php');
require_once(APP_ROOT.'/core/Request.php');
require_once(APP_ROOT.'/core/Security.php');
require_once(APP_ROOT.'/core/Response.php');
require_once(APP_ROOT.'/core/Controller.php');
require_once(APP_ROOT.'/core/Model.php');

class Anvil {
  public $config;
  public $helpers;
  public $request;
  public $security;
  public $response;
  public $active_controller;

  public function __construct() {
    $this->config = new Config();
    $this->helpers = new Helpers($this);
    $this->request = new Request($this);
    $this->response = new Response($this);
    $this->security = new Security($this);
  }

  /**
   * Strike the Anvil!
   */
  public function strike() {
    ob_start();
    $uri_array = $this->request->uri_array;
    $class_name = (!isset($uri_array[0]) || empty($uri_array[0]))
          ? $this->config->item('default_controller') : array_shift($uri_array);
    $start_token = $this->config->item('starting_token');
    while($start_token-- > 0) { array_shift($uri_array); }
    $cc_name = '';
    if(!file_exists(APP_ROOT.'/controllers/'.$class_name.'_controller.php')) {
      $class_name = $this->config->item('default_controller');
    }
    $cc_name = $class_name.'_controller';
    $this->active_controller = $class_name;
    // Ok, pull in the requested Controller
    require_once(APP_ROOT.'/controllers/'.$cc_name.'.php');
    $c_class = new $cc_name($this);
    $c_class->index();
    // The controller should take care of the rest.
    
    ob_end_flush();
  }
}
