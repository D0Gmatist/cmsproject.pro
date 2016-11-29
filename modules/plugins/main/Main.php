<?php

namespace Modules\Plugins\Main;

use Modules\Template\Template;

final class Main {
	/** @var Template  */
	public $tpl;

	/** @var array  */
	public $results = [];

	/**
	 * Main constructor.
	 * @param Template $tpl
	 */
	function __construct ( Template $tpl ) {
		$this->tpl = $tpl;

	}

	/**
	 * @param $tags
	 */
	public function setTags ( $tags ) {
		if ( is_array( $tags ) ) {
			foreach ( $tags AS $val ) {
				$this->results[] = $val;

			}

		} else {
			$this->results[] = $tags;

		}

	}

	/**
	 * @param $is_logged
	 */
	public function getResult ( $is_logged ) {
		if ( $is_logged ) {
			$this->tpl->loadTemplate( 'main.tpl' );

		} else {
			$this->tpl->loadTemplate( 'login_form.tpl' );

		}

		foreach ( $this->results AS $val ) {
			$this->tpl->set ( '{' . $val . '}', $this->tpl->result[$val] );

		}

		$this->tpl->compile( 'main' );

		echo $this->tpl->result['main'];

	}

}