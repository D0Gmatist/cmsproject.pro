<?php

@ob_start ();
@ob_implicit_flush ( 0 );
if( !defined( 'E_DEPRECATED' ) ) {
    @error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
    @ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );
} else {
    @error_reporting ( E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
    @ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
}
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );

define ( 'ENGINE', true );
define ( 'ROOT_DIR', dirname ( __FILE__ ) );
define ( 'MODULES_DIR', ROOT_DIR . '/modules' );
define ( 'PLUGINS_DIR', MODULES_DIR . '/plugins' );

header('Content-Type: text/html; charset=utf-8');

if ( $_GET['test'] == 1 ) {
	$_POST = $_GET;

}

require_once 'loader.php';
