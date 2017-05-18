<?php

//define root path
define( 'ROOT_PATH', __DIR__ );

$loader = require ROOT_PATH . '/vendor/autoload.php';
$loader->add( 'APP\\', ROOT_PATH );

//if this is not cli call initiate routes and session
if( strpos( php_sapi_name(), 'cli' ) === false )
{
	require_once ROOT_PATH . '/config/routes.php';

	\RPC\Registry::set( 'routes', $routes );

	//setup session
	$session = new \RPC\Session();
	$session->setExpire( 0 );
	$session->setPath( '/' );
	$session->start();
}

\RPC\Db::addConnection( 'default', array( 
		'adapter' 	=> getenv( 'DB_ADAPTER' ),
		'hostname' 	=> getenv( 'DB_HOSTNAME' ),
		'database' 	=> getenv( 'DB_NAME' ),
		'socket' 	=> getenv( 'DB_SOCKET' ),
		'port' 		=> getenv( 'DB_PORT' ),
		'username' 	=> getenv( 'DB_USERNAME' ),
		'password' 	=> getenv( 'DB_PASSWORD' ),
		'prefix' 	=> getenv( 'DB_PREFIX' )
	)
);

?>
