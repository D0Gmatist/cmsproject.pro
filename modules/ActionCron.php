<?php

use Modules\Plugins\Parser\ParserCheckGroup;
use Modules\Plugins\Parser\ParserGroup;
use Modules\Plugins\Parser\ParserUser;

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
	case 'parser_check_group' :
		/**
		 * @var $config
		 * @var $language
		 */
		$parserGroup = new ParserCheckGroup( $functions, $db, $config, $language );
		break;

	case 'parser_group' :
		/**
		 * @var $config
		 * @var $language
		 */
		$parserGroup = new ParserGroup( $functions, $db, $config, $language );
		break;

	case 'parser_user' :
		/**
		 * @var $config
		 * @var $language
		 */
		$parserUser = new ParserUser( $functions, $db, $config, $language );
		break;

}
