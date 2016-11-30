<?php

namespace Modules\Plugins\MsgBox;

use Modules\Template\Template;

final class MsgBox {
	/** @var Template  */
	public $tpl;

	/**
	 * Main constructor.
	 * @param Template $tpl
	 */
	function __construct ( Template $tpl ) {
		$this->tpl = $tpl;

	}

	public function getResult ( $title, $text, $type ) {
		switch ( $type ) {

			case 'error' :
				$this->tpl->loadTemplate( 'msg/error.tpl' );
				break;

			case 'success' :
				$this->tpl->loadTemplate( 'msg/success.tpl' );
				break;

			case 'default' :
				$this->tpl->loadTemplate( 'msg/default.tpl' );
				break;

		}

		$this->tpl->set ( '{title}', $title );
		$this->tpl->set ( '{text}', $text );

		$this->tpl->compile( 'msg' );

	}

}

