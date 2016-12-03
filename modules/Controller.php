<?php

use Modules\Functions\Functions;

use Modules\Mail\Mail;
use Modules\Mail\PHPMailer\PHPMailer;
use Modules\MobileDetect\MobileDetect;

use Modules\Mysql\Config\ConfigDb;
use Modules\Mysql\Db\Db;

use Modules\Plugins\Main\Main;
use Modules\Plugins\MsgBox\MsgBox;

use Modules\Plugins\UserPlugins\Authorization;
use Modules\Plugins\UserPlugins\AuthorizationVk;
use Modules\Plugins\UserPlugins\Registration;
use Modules\Plugins\UserPlugins\RegistrationVk;
use Modules\Plugins\UserPlugins\IsLogin;
use Modules\Plugins\UserPlugins\UserPanel;

use Modules\Template\Template;
use Modules\Translate\Translate;
use Modules\VarsSerialize\VarsSerialize;

/** @var $config */
require_once MODULES_DIR . '/config.php';
define ( 'HTTP_HOME_URL', $config['http_home_url'] );

/** @var $language */
require_once ROOT_DIR . '/language/loader.php';

/** @var  $module */
$action = 'main';
if ( isset( $_GET['action'] ) AND trim( $_GET['action'] ) != '' ) {
	$action = $_GET['action'];

}
$module = $action;

/** @var  $page_title */
$pageTitle = [ 'Главная', '' ];

/** @var  $isLogged */
$isLogged = false;

/** @var  $memberId */
$memberId = [];

/** @var  $functions */
$functions = new Functions( $config );

$functions->domain();
$functions->Session();

/** @var  $replaceUrl */
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

/** @var  $main */
$main = new Main( $tpl );

/** @var  $msgBox */
$msgBox= new MsgBox( $tpl );

new UserPanel( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );

switch ( $action ) {
	case 'main' :
		break;

	case 'login' :
		$pageTitle = [ 'Авторизация', '' ];
		new Authorization( $isLogged, $memberId, $action, $functions, $db, $tpl, $msgBox, new Mail( new PHPMailer() ), $config, $language );
		break;

	case 'registration' :
		$pageTitle = [ 'Регистрация', '' ];
		new Registration( $isLogged, $memberId, $action, $functions, $db, $tpl, $msgBox, new Mail( new PHPMailer() ), $config, $language );
		break;

	case 'login_vk' :
		$pageTitle = [ 'Авторизация', 'через VK' ];
		new AuthorizationVk( $isLogged, $memberId, $action, $functions, $db, $tpl, $msgBox, new Mail( new PHPMailer() ), $config, $language );
		break;

	case 'registration_vk' :
		$pageTitle = [ 'Регистрация', 'через VK' ];
		new RegistrationVk( $isLogged, $memberId, $action, $functions, $db, $tpl, $msgBox, new Mail( new PHPMailer() ), $config, $language );
		break;


}

$main->setTags( ['msg'] );
$main->setTags( [ 'user_panel' ] );
$main->setTags( [ 'content' ] );

$main->getResult( $replaceUrl, $pageTitle );
