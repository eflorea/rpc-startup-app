<?php

require "../autoloader.php";

$router = new \RPC\Router;
$router->setRewriteRules( \RPC\Registry::get( 'routes' ) );
$router->run();

?>
