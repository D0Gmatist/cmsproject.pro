<?php

use Modules\Functions\Functions;

use Modules\Mail\Mail;
use Modules\Mail\PHPMailer\PHPMailer;
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
$functions = new Functions( $config );

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
$msgBox = new MsgBox( $tpl );
/** @var  $mail */
$mail = new Mail( new PHPMailer );

/** @var  $authorization */
$authorization = new Authorization( $action, $_TIME, $functions, $db, $tpl, $msgBox, $mail, $config, $language );

/** @var  $member_id */
$member_id = $authorization->member_id;
if ( $authorization->is_logged ) {
	new LoginPanel( $config, $member_id, $tpl );

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
