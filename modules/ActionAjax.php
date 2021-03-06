<?php

use Modules\Plugins\MsgBox\MsgBox;
use Modules\Plugins\Vk\VkGeo;
use Modules\Plugins\Vk\VkParser;
use Modules\Plugins\Vk\VkSearchGroupForm;
use Modules\Plugins\Vk\VkSearchUserForm;

$result = [
	'success'			=> true,
	'content'			=> '',
	'msg'				=> '',

];

if ( isset( $_POST['action'] ) AND trim( $_POST['action'] ) != '' ) {
	$action = $_POST['action'];

}

/** @var  $msgBox */
$msgBox = new MsgBox( $tpl );

/** @var string $action */
if ( trim( $action ) == '' ) {
	$msgBox->getResult( 'ERROR', 'Not action info!', 'error' );

	$result['success'] = false;
	$result['msg'] = $tpl->result['msg'];

}

/** @var string $action */
switch ( $action ) {
	case 'countries' :
	case 'regions' :
	case 'cities' :
		/**
		 * @var array $groupVar
		 * @var array $config
		 * @var array $language
		 */
		$vkGeo = new VkGeo( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );
		$result['content'] = $vkGeo->returnResult();
		break;

	case 'vk_user_search' :
		/**
		 * @var array $groupVar
		 * @var array $config
		 * @var array $language
		 */
		$vkSearchUserForm = new VkSearchUserForm( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );
		$result['content'] = $vkSearchUserForm->getResult();
		if ( $result['content'] === false ) {
			$result['success'] = false;
			$result['msg'] = 'Нет результата поиска!';

		}
		break;

	case 'vk_group_search' :
		/**
		 * @var array $groupVar
		 * @var array $config
		 * @var array $language
		 */
		$vkSearchGroupForm = new VkSearchGroupForm( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );
		$result['content'] = $vkSearchGroupForm->getResult();
		if ( $result['content'] === false ) {
			$result['success'] = false;
			$result['msg'] = 'Нет результата поиска!';

		}
		break;

	case 'vk_group_parser':
		/**
		 * @var array $groupVar
		 * @var array $config
		 * @var array $language
		 */
		$vkParser = new VkParser( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );
		$result = $vkParser->getResult();
		break;

}

if ( $result['content'] == '' AND $result['msg'] == '' ) {
	$result['success'] = false;
	$result['msg'] = 'Ошибка запроса!';

}

echo json_encode( $result );

