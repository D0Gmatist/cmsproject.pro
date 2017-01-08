<?php

use Modules\Mail\Mail;
use Modules\Mail\PHPMailer\PHPMailer;
use Modules\Plugins\Main\Main;
use Modules\Plugins\MsgBox\MsgBox;
use Modules\Plugins\Parser\AddParser;
use Modules\Plugins\UserPlugins\Authorization;
use Modules\Plugins\UserPlugins\LoginForm;
use Modules\Plugins\UserPlugins\Registration;
use Modules\Plugins\UserPlugins\UserPanel;
use Modules\Plugins\Vk\VkLogin;
use Modules\Plugins\Vk\VkSearchGroupForm;
use Modules\Plugins\Vk\VkSearchUserForm;

/** @var  $msgBox */
$msgBox = new MsgBox( $tpl );

/** @var  $main */
$main = new Main( $tpl );

/**
 * @var array $memberId
 * @var array $groupVar
 * @var array $config
 * @var array $language
 */
new UserPanel( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language, $_TIME );

/** @var string $action */
switch ( $action ) {
	case 'main' :
		break;

	case 'vk_group_search' :
		$pageTitle = [ 'Поиск групп', '' ];
		new VkSearchGroupForm( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );
		break;

	case 'vk_user_search' :
		$pageTitle = [ 'Поиск пользователей', '' ];
		new VkSearchUserForm( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );
		break;

	case 'login' :
		$pageTitle = [ 'Авторизация', '' ];
		if ( $_POST[ 'action' ] == 'login' ) {
			new Authorization( $isLogged, $memberId, $action, $functions, $db, $tpl, $msgBox, new Mail( new PHPMailer() ), $config, $language );

		} else if ( $_POST[ 'action' ] == 'registration' ) {
			new Registration( $isLogged, $memberId, $action, $functions, $db, $tpl, $msgBox, new Mail( new PHPMailer() ), $config, $language );

		}
		new LoginForm( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );
		break;

	case 'vk_login' :
		$pageTitle = [ 'Авторизация', '' ];
		new VkLogin( $isLogged, $memberId, $action, $functions, $db, $tpl, $msgBox, new Mail( new PHPMailer() ), $config, $language );
		break;

}

$main->setTags ( [ 'msg' ] );
$main->setTags ( [ 'user_panel' ] );
$main->setTags ( [ 'content' ] );

/**
 * @var array $replaceUrl
 * @var array $pageTitle
 */
$main->getResult ( $replaceUrl, $pageTitle, $script );
