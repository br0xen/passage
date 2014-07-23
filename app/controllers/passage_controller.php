<?php if(!defined('APP_ROOT')) exit('No direct script access allowed');

class Passage_controller extends Controller {
  var $template_data = array();
  public function __construct($a) {
    parent::__construct($a);
    $this->template_data = array(
      'stylesheets' => array('/assets/css/main.css'),
      'scripts' => array('/assets/js/main.js')
    );
    $this->default_function = 'main';
    $cookie = $this->anvil->request->cookie;
    $this->template_data['door_history'] = array();
    if(isset($cookie['door_history'])
        && !empty($cookie['door_history'])) {
      $this->template_data['door_history'] = json_decode($cookie['door_history'], TRUE);
    }
    if(isset($cookie['length'])) {
      $this->template_data['length'] = $cookie['length'];
    }
    if(isset($cookie['remember_doors'])) {
      $this->template_data['remember_doors'] = $cookie['remember_doors'];
    }
  }

  public function main() {
    $this->load_view('main', $this->template_data);
  }

  public function gen() {
    $this->output_type = 'html';
    if(isset($this->anvil->request->uri_array[1])) {
      $this->output_type = $this->anvil->request->uri_array[1];
    }
    $parms = $this->anvil->request->parameters;
    if(isset($parms['pin']) && !empty($parms['pin'])
        && isset($parms['door_id']) && !empty($parms['door_id'])) {
      $pin = $parms['pin'];
      $door_id = $parms['door_id'];
      $length = (isset($parms['length']))?$parms['length']:12;

      $this->template_data['pin'] = $pin;
      $this->template_data['door_id'] = $door_id;
      $this->template_data['pw'] = $this->getPassword($pin, $door_id, $length);
      $this->template_data['length'] = $length;
      $this->anvil->request->setCookie('length', $length, 'never');
      if($parms['remember']=='on') {
        if(!in_array($door_id, $this->template_data['door_history'])) {
          $this->template_data['door_history'][] = $door_id;
          $this->anvil->request->setCookie('door_history', json_encode($this->template_data['door_history']), 'never');
        }
        $this->template_data['remember_doors'] = TRUE;
        $this->anvil->request->setCookie('remember_doors', TRUE, 'never');
      }
      $this->output();
      return;
    }
    http_response_code(400);
    echo "Invalid Input";
    return;
  }

  public function output() {
    switch($this->output_type) {
      case 'html':
        $this->load_view('main', $this->template_data);
        break;
      case 'json':
        $json_array = array('json' => array('pw' => $this->template_data['pw']));
        $this->load_view('json_result', $json_array);
        break;
      case 'raw':
        header('Content-Type: text/plain');
        echo $this->template_data['pw'];
        break;
    }
  }

  public function getPassword($pin, $door, $len=12, $valids=array('lower','upper','number','symbol')) {
    $valid_chars = '';
    if(in_array('lower', $valids)) {
      $valid_chars.='abcdefghijklmnopqrstuvwxyz';
    }
    if(in_array('upper', $valids)) {
      $valid_chars.='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    if(in_array('number', $valids)) {
      $valid_chars.='1234567890';
    }
/*
    if(in_array('number', $valids)) {
      $valid_chars.='!@#$%^&*()_+-=';
    }
*/
    $key_data = hash('sha256', $pin.' - '.$door, TRUE);
    $ret = '';
    for($i = 0; $i < $len; $i++) {
      $ret.= $valid_chars[ord($key_data[$i]) % strlen($valid_chars)];
    }
    return $ret;
  }

  public function pinChecksum() {
    $parms = $this->anvil->request->parameters;
    if(isset($parms['pin']) && !empty($parms['pin'])) {
      $checksum = hash('sha256', $parms['pin'], TRUE);
      $ret = 0;
      for($i = 0; $i < strlen($checksum); $i++) {
        $ret += ord($checksum[$i]);
      }
      $json_array = array('json' => array('checksum' => $ret));
    } else {
      $json_array = array('json' => array('error' => 'No/Invalid Pin Given'));
    }
    $this->load_view('json_result', $json_array);
  }

  public function clearHistory() {
    $this->anvil->request->clearCookie('door_history');
    $this->anvil->response->redirect('/');
  }
}
