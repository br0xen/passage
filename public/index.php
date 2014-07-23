<?php

define("PHP_ROOT","../");
define("APP_ROOT","../app");

require_once(APP_ROOT.'/core/Anvil.php');
$anvil = new Anvil();
$anvil->strike();

return;

?>
