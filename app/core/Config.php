<?php if(!defined('APP_ROOT')) exit('No direct script access allowed');

class Config {
  private $config_file;
  private $_items = array();

  public function __construct() {
    $this->config_file = APP_ROOT.'/config.json';
    if(file_exists($this->config_file)) {
      $cfg_raw = file_get_contents($this->config_file);
      $this->_items = json_decode($cfg_raw, TRUE);
    }
    $this->_checkSetDefault('starting_token', 0);
    $this->_checkSetDefault('charset', 'UTF-8');
    $this->_checkSetDefault('csrf_protection', TRUE);
    $this->_checkSetDefault('global_models', array());
    $this->_checkSetDefault('global_libraries', array());
  }

  /**
   * Checks to see if $this->_items[$key] is set,
   * If not, sets it to $val
   */
  private function _checkSetDefault($key, $val) {
    if(!isset($this->_items[$key])) {
      $this->_items[$key] = $val;
    }
  }

  public function item($key=NULL, $val=NULL) {
    if(is_array($key)) {
      foreach($key as $k => $v) {
        $this->item($k, $v);
      }
    }

    if(isset($key)) {
      if(isset($val)) {
        // Setting
        $this->_items[$key] = $val;
      }
      if(isset($this->_items[$key])) {
        return $this->_items[$key];
      }
    }
    return FALSE;
  }

  public function items() {
    return $this->_items;
  }
}
