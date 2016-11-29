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

require_once ROOT_DIR . '/modules/config.php';

/** @var  $module */
$action = 'main';
if ( isset( $_GET['action'] ) AND trim( $_GET['action'] ) != '' ) {
	$action = $_GET['action'];

}

/** @var  $member_id */
$member_id = [];

/** @var  $config */
date_default_timezone_set( $config['date_adjust'] );

/** @var  $tpl */
$tpl = new Template( new MobileDetect(),  new Translate(), $config );
define ( 'TPL_DIR', $tpl->dir );

/** @var  $db */
$db = new Db( new ConfigDb );

/** @var  $functions */
$functions = new Functions();

/** @var  $_TIME */
$_TIME = time();

/** @var  $_IP */
$_IP =  $functions->getIp();

/** @var  $authorization */
$authorization = new Authorization( $functions, $db, $config );

if ( $action == 'logout' ) {
	$authorization->logout();

} else if ( $_POST['action'] == 'login' ) {
	$authorization->login();

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

}

$main = new Main( $tpl );
$main->setTags( [ 'login_panel' ] );
$main->getResult( $authorization->is_logged );
