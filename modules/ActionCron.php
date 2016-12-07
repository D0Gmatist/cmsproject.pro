<?php

use Modules\Plugins\Vk\VkGeo;

$result = [
	'success'			=> true,
	'content'			=> '',
	'msg'				=> '',

];

if ( ! isset( $action ) OR trim( $action ) == '' ) {
	die();

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
		$result['content'] = $vkGeo->getResult();
		break;

}

echo json_encode( $result );

