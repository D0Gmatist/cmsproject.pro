<?php

use Modules\Plugins\MsgBox\MsgBox;

$result = [
	'success'	=> true,
	'content'	=> '',
	'msg'		=> '',

];

/** @var  $msgBox */
$msgBox = new MsgBox( $tpl );

/** @var string $action */
switch ( $action ) {
	case 'main' :
		break;

}

echo json_encode( $result );

