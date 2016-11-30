<?php

use Modules\Functions\Functions;

use Modules\MobileDetect\MobileDetect;

use Modules\Mysql\Config\ConfigDb;
use Modules\Mysql\Db\Db;

use Modules\Plugins\Authorization\Authorization;
use Modules\Plugins\LoginPanel\LoginPanel;
use Modules\Plugins\Main\Main;
use Modules\Plugins\MsgBox\MsgBox;

use Modules\Template\Template;
use Modules\Translate\Translate;

/** @var $config */
require_once MODULES_DIR . '/config.php';

/** @var $language */
require_once ROOT_DIR . '/Language/loader.php';


/** @var  $module */
$action = 'main';
if ( isset( $_GET['action'] ) AND trim( $_GET['action'] ) != '' ) {
	$action = $_GET['action'];

}
$module = $action;

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
/** @var  $_TIME */
$_TIME = time();
/** @var  $_IP */
$_IP =  $functions->getIp();

/** @var  $db */
$db = new Db( new ConfigDb );

/** @var  $tpl */
$tpl = new Template( new MobileDetect, new Translate, $config );
define ( 'TPL_DIR', $tpl->dir );

/** @var  $main */
$main = new Main( $tpl );
/** @var  $msg */
$msg = new MsgBox( $tpl );

/** @var  $authorization */
$authorization = new Authorization( $functions, $db, $tpl, $config, $language );

if ( $action == 'logout' ) {
	$authorization->logout();

} else if ( $_POST['action'] == 'login' ) {
	$authorization->login( $msg );

} else if ( $_POST['action'] == 'registration' ) {
	$authorization->registration();

} else {
	$authorization->isLogged();

}

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
		header( 'Location: ' . $config['http_home_url'] . 'login' );
		die();

	}

}

switch ( $action ) {
	case 'main' :
		$tpl->loadTemplate( 'xxx.tpl' );
		$tpl->compile( 'content' );
		break;

	case 'login' :
		$authorization->getContent();
		break;

}

$main->setTags( ['msg'] );
$main->setTags( [ 'login_panel' ] );
$main->setTags( [ 'content' ] );

$main->getResult( $replaceUrl );
