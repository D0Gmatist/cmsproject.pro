<?php

use Modules\Functions\Functions;
use Modules\MobileDetect\MobileDetect;
use Modules\Mysql\Config\ConfigDb;
use Modules\Mysql\Db\Db;
use Modules\Plugins\Authorization\Authorization;
use Modules\Plugins\LoginPanel\LoginPanel;
use Modules\Plugins\Main\Main;
use Modules\Template\Template;
use Modules\Translate\Translate;

/** @var $config */
require_once ROOT_DIR . '/modules/config.php';

/** @var  $module */
$action = 'main';
if ( isset( $_GET['action'] ) AND trim( $_GET['action'] ) != '' ) {
	$action = $_GET['action'];

}

/** @var  $member_id */
$member_id = [];

/** @var  $functions */
$functions = new Functions();

/** @var  $replaceUrl */
$replaceUrl = false;

/** @var  $homeUrl */
$homeUrl = $functions->cleanUrl( $config['http_home_url'] );

if ( $homeUrl AND $functions->cleanUrl( $_SERVER['HTTP_HOST'] ) != $homeUrl ) {
	/** @var  $replaceUrl */
	$replaceUrl =[];
	$replaceUrl[0] = $homeUrl;
	$replaceUrl[1] = $functions->cleanUrl( $_SERVER['HTTP_HOST'] );

}

$config['http_home_url'] = explode( 'index.php', strtolower ( $_SERVER['PHP_SELF'] ) );
$config['http_home_url'] = reset( $config['http_home_url'] );

/** @var  $config */
date_default_timezone_set( $config['date_adjust'] );

/** @var  $tpl */
$tpl = new Template( new MobileDetect(), new Translate(), $config );
define ( 'TPL_DIR', $tpl->dir );

/** @var  $db */
$db = new Db( new ConfigDb );

/** @var  $_TIME */
$_TIME = time();

/** @var  $_IP */
$_IP =  $functions->getIp();

/** @var  $authorization */
$authorization = new Authorization( $functions, $db, $config, $tpl );

if ( $action == 'logout' ) {
	$authorization->logout( $config['http_home_url'] );

} else if ( $_POST['action'] == 'login' ) {
	$authorization->login();

} else {
	$authorization->isLogged();

}

$main = new Main( $tpl );

if ( $authorization->is_logged ) {
	$authorization->loginUpdate( $_TIME );
	/** @var  $member_id */
	$member_id = $authorization->member_id;

	/** @var  $loginPanel */
	$loginPanel = new LoginPanel( $config, $member_id, $tpl );

} else {
	$authorization->noLogin();
	$member_id['user_group'] = 5;

	if ( $config['no_login'] == 1 AND $action != 'login' ) {
		header( 'Location: ' . $config['http_home_url'] . 'login/' );
		die();

	}

}

$main->setTags( [ 'content' ] );
$main->setTags( [ 'login_panel' ] );

switch ( $action ) {
	case 'login' :
		$authorization->getContent();
		break;

}

$main->getResult( $replaceUrl );
