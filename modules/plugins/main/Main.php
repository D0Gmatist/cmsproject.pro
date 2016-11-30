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

	public function getResult ( $replaceUrl ) {
		$this->tpl->loadTemplate( 'main.tpl' );

		$this->results = array_unique( $this->results );

		foreach ( $this->results AS $val ) {
			$this->tpl->set ( '{' . $val . '}', $this->tpl->result[$val] );

		}

		$this->tpl->compile( 'main' );

		if ( $replaceUrl ) {
			$this->tpl->result['main'] = str_replace ( $replaceUrl[0] . '/', $replaceUrl[1] . '/', $this->tpl->result['main'] );

		}

		echo $this->tpl->result['main'];

	}

}