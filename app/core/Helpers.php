<?php if(!defined('APP_ROOT')) exit('No direct script access allowed');
/** A bunch of Helper Functions */
class Helpers {
  private $_anvil;
  public function __construct($a) {
    $_anvil = $a;
  }
  /**
   * Remove Invisible Characters
   * This prevents sandwiching null characters
   * between ascii characters, like Java\0script.
   * 
   * @access  public
   * @param   string
   * @return  string
   */
  public function remove_invisible_characters($str, $url_encoded = TRUE) {
    $non_displayables = array();
    // Ever control char except newline (dec 10)
    // carriage return (dec 13), and horizontal tab (dec 09)
    if($url_encoded) {
      $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
      $non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
    }
    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

    do {
      $str = preg_replace($non_displayables, '', $str, -1, $count);
    } while($count);
    return $str;
  }

  /**
   * Returns HTML escaped variables
   *
   * @access  public
   * @param   mixed
   * @return  mixed
   */
  public function html_escape($var) {
    if(is_array($var)) {
      foreach($var as &$v) {
        $v = $this->html_escape($v);
      }
    } else {
      return htmlspecialchars($var, ENT_QUOTES, $this->_anvil->config->item('charset'));
    }
  }
}
