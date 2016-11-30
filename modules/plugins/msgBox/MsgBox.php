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

	/**
	 * @param string $title
	 * @param string $text
	 * @param string $type
	 * @param string $name
	 */
	public function getResult ( $title = false, $text, $type, $name = 'msg' ) {
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

		if ( $title == false ) {
			$this->tpl->set ( '{title}', '' );

			$this->tpl->setBlock( "'\\[title\\](.*?)\\[/title\\]'si", "" );

		} else {
			$this->tpl->set ( '{title}', $title );

			$this->tpl->set( '[title]', '' );
			$this->tpl->set( '[/title]', '' );

		}
		$this->tpl->set ( '{text}', $text );

		$this->tpl->compile( $name );

	}

}

