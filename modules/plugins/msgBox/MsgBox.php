<?php

namespace Modules\Plugins\MsgBox;

use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

class MsgBox {
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
	 * @param bool $title
	 * @param $text
	 * @param $type
	 */
	public function getResult ( $title = false, $text, $type ) {
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

		$this->tpl->compile( 'msg' );

	}

}

