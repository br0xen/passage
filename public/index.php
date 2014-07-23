<?php

define("PHP_ROOT","../");
define("APP_ROOT","../app");

require_once(APP_ROOT.'/core/Anvil.php');
$anvil = new Anvil();
$anvil->strike();

return;

// We need the uri library for things to work
require_once(APP_ROOT.'/core/uri_library.php');
// Load up the globals
foreach($global_libraries as $alib) {
	require_once(APP_ROOT.'/libraries/'.$alib.'_library.php');
}
foreach($global_models as $amod) {
	require_once(APP_ROOT.'/models/'.$amod.'_model.php');
}
// Buffer all output for speed!
ob_start();

$uri = new Uri_library($_SERVER['REQUEST_URI']);
$uri_array = $uri->getFullArray();

$class_name = (!isset($uri_array[0]) || empty($uri_array[0]))?$default_controller:array_shift($uri_array);

while($starting_token-- > 0) { array_shift($uri_array); }
// Check if $uri->getItem(0) is a controller
if(file_exists(APP_ROOT.'/controllers/'.$class_name.'_controller.php')) {
	// File exists, set the cc_name and pop the uri_array
	$cc_name = $class_name."_controller";
} else {
	// Not a valid controller, so 404d!
	header("HTTP/1.0 404 Not Found");
	echo "Page Not Found";
	exit;
//	$cc_name = $default_controller."_controller";
}
// Pull in the requested Controller
require_once(APP_ROOT.'/controllers/'.$cc_name.'.php');

$c_class = new $cc_name;
// Were we provided a method?
$c_func = (!isset($uri_array[0]) || empty($uri_array[0]))?"index":array_shift($uri_array);
if($c_func!==false && method_exists($c_class, $c_func)) {
	call_user_func_array(array($c_class, $c_func), $uri_array);
} else {
	// Nope, hit the controller's index
	if(method_exists($c_class, 'index')) {
		call_user_func_array(array($c_class, "index"), $uri_array);
	}
}

// Flush the buffer
ob_end_flush();

?>
