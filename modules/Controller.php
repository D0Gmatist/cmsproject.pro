<?php

use Modules\Functions\Functions;

use Modules\MobileDetect\MobileDetect;

use Modules\Mysql\Config\ConfigDb;
use Modules\Mysql\Db\Db;

use Modules\Plugins\UserPlugins\IsLogin;

use Modules\Template\Template;
use Modules\Translate\Translate;
use Modules\VarsSerialize\VarsSerialize;

/** @var array $script */
$script = [];

/** @var $config */
require_once MODULES_DIR . '/config.php';
define ( 'HTTP_HOME_URL', $config['http_home_url'] );

$script['http_home_url'] = $config['http_home_url'];

/** @var $language */
require_once ROOT_DIR . '/language/loader.php';

/** @var  $module */
$action = 'main';
if ( isset( $_GET['action'] ) AND trim( $_GET['action'] ) != '' ) {
	$action = $_GET['action'];

}
$module = $action;

$script['action'] = $module;

/** @var array $page_title */
$pageTitle = [ 'Главная', '' ];

/** @var bool $isLogged */
$isLogged = false;

/** @var array $memberId */
$memberId = [];

/** @var Functions $functions */
$functions = new Functions( $config, $language );

$functions->domain();
$functions->Session();

/** @var bool $replaceUrl */
$replaceUrl = false;

/** @var  $homeDomain */
$homeDomain = $functions->cleanUrl( $config['http_home_url'] );

if ( $homeDomain AND $functions->cleanUrl( $_SERVER['HTTP_HOST'] ) != $homeDomain ) {
	/** @var  $replaceUrl */
	$replaceUrl =[];
	$replaceUrl[0] = $homeDomain;
	$replaceUrl[1] = $functions->cleanUrl( $_SERVER['HTTP_HOST'] );

}

$config['http_home_url'] = explode( 'index.php', strtolower ( $_SERVER['PHP_SELF'] ) );
$config['http_home_url'] = reset( $config['http_home_url'] );

$script['home'] = $config['http_home_url'];

/** @var  $config */
date_default_timezone_set( $config['date_adjust'] );

/** @var  $_TIME */
$_TIME = time();

/** @var  $_IP */
$_IP =  $functions->getIp();

/** @var  $db */
$db = new Db( new ConfigDb );

/** @var  $varsSerialize */
$varsSerialize = new VarsSerialize( $functions, $db, $config, $language );
$groupVar = $varsSerialize->initial( 'groups', 'group_id' );

/** @var  $isLogin */
$isLogin = new IsLogin( $action, $functions, $db, $config, $_TIME );
$isLogged = $isLogin->isLogged;
$memberId = $isLogin->memberId;

/** @var  $tpl */
$tpl = new Template( new MobileDetect, new Translate, $config, $memberId );
define ( 'TPL_DIR', $tpl->dir );

$actionCron = [ 'parser_check_group', 'parser_group', 'parser_user' ];

if ( $_POST['method'] == 'ajax' OR $_GET['method'] == 'ajax' ) {
	require_once 'ActionAjax.php';

} else if ( $_GET['method'] == 'cron' AND in_array( $_GET['action'], $actionCron ) AND $_GET['key'] == $config['cron_key'] ) {
	require_once 'ActionCron.php';

} else {
	require_once 'Action.php';

}

$tpl->globalClear();
$db->close();
