<?php

class Controller {
  protected $anvil;
  public $default_function = '';
	public function __construct($a) {
    $this->anvil = $a;
  }

  public function index() {
    $func_name = $this->default_function;
    if(isset($this->anvil->request->uri_array[0])) {
      if($this->anvil->request->uri_array[0] == $this->anvil->active_controller) {
        if(isset($this->anvil->request->uri_array[1])) {
          $func_name = $this->anvil->request->uri_array[1];
        }
      } else if(!empty($this->anvil->request->uri_array[0])) {
        $func_name = $this->anvil->request->uri_array[0];
      }
    }
    if(method_exists($this, $func_name)) {
      return $this->$func_name();
    } else {
      header("HTTP/1.0 404 Not Found");
      echo "Page Not Found";
      exit;
    }
  }

	public function load_models($model=NULL) {
		$this->load_model($model);
	}
	public function load_model($model=NULL) {
		// All models end with '_model'
		if(is_array($model)) {
			foreach($model as $k=>$m) {
				$model[$k]=$m."_model";
			}
		} else {
			$model.="_model";
		}
		$this->_load_files($model, "models");
	}

	public function load_libraries($library=NULL) {
		$this->load_library($library);
	}
	public function load_library($library=NULL) {
		// All libraries end with '_library'
		if(is_array($library)) {
			foreach($library as $k=>$l) {
				$library[$k]=$l."_library";
			}
		} else {
			$library.="_library";
		}
		$this->_load_files($library, "libraries");
	}

	public function load_views($views=NULL, $vars=array()) {
		$this->load_view($views,$vars);
	}
	public function load_view($views=NULL, $vars=array()) {
		// No restrictions on view names
		$this->_load_files($views, "views", true, $vars);
	}

	// Runs through a potential array of files
	// Checks for existence, then _load_file
	public function _load_files($a=null, $func=null, $multi=false, $vars=array()) {
		if(isset($a) && isset($func)) {
			if(is_array($a)) {
				foreach($a as $aa) {
					$f = APP_ROOT."/".$func."/".$aa.".php";
					$this->_load_file($f, ($multi===true), $vars);
				}
			} else {
				$f = APP_ROOT."/".$func."/".$a.".php";
				$this->_load_file($f, ($multi===true), $vars);
			}
		}
	}

	// Checks if the file exists and includes it
	public function _load_file($filename=NULL,$multi=false,$vars=null) {
		if(isset($vars)&&is_array($vars)) {
			extract($vars);
		}
		if(isset($filename) && file_exists($filename)) {
			if($multi) {
				include($filename);
			} else {
				require_once($filename);
			}
		}
	}
}
