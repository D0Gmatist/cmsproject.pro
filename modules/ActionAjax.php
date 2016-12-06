<?php

use Modules\Plugins\MsgBox\MsgBox;
use Modules\Plugins\Vk\VkGeo;

$result = [
	'success'			=> true,
	'content'			=> '',
	'msg'				=> '',

];

/** @var  $msgBox */
$msgBox = new MsgBox( $tpl );

if ( isset( $_POST['action'] ) AND trim( $_POST['action'] ) == '' ) {
	$msgBox->getResult( 'ERROR', 'Not action info!', 'error' );

	$result['success'] = false;
	$result['msg'] = $tpl->result['msg'];

}

/** @var  $msgBox */
$msgBox = new MsgBox( $tpl );

/** @var string $action */
switch ( $_POST['action'] ) {
	case 'countries' :
	case 'regions' :
	case 'cities' :
		/**
		 * @var array $groupVar
		 * @var array $config
		 */
		$vkGeo = new VkGeo( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );
		$result['content'] = $vkGeo->getResult();
		break;

}

$result['post'] = $_POST;

echo json_encode( $result );

