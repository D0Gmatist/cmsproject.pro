<?php

use Modules\Plugins\MsgBox\MsgBox;
use Modules\Plugins\Vk\VkGeo;

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
		 */
		$vkGeo = new VkGeo( $isLogged, $memberId, $groupVar, $functions, $db, $tpl, $config, $language );
		$result['content'] = $vkGeo->returnResult();
		break;

}

echo json_encode( $result );

