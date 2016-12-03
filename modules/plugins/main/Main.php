<?php

namespace Modules\Plugins\Main;

use Modules\Template\Template;

final class Main {
	/** @var Template  */
	public $tpl;

	/** @var array  */
	public $tags = [];

	/** @var array  */
	public $tagsResult = [];

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
				$this->tags[] = $val;

			}

		} else {
			$this->tags[] = $tags;

		}

	}

	/**
	 * @param array $replaceUrl
	 * @param array $pageTitle
	 */
	public function getResult ( $replaceUrl, $pageTitle ) {
		$this->tpl->loadTemplate( 'main.tpl' );

		$this->tags = array_unique( $this->tags );

		foreach ( $this->tags AS $val ) {
			$this->tpl->set ( '{' . $val . '}', $this->tpl->result[$val] );

		}

		if ( $pageTitle[0] ) {
			$this->tpl->set ( '{page_title}', $pageTitle[0] );

			$this->tpl->set( '[page_title]', "" );
			$this->tpl->set( '[/page_title]', "" );

			if ( $pageTitle[1] ) {
				$this->tpl->set ( '{page_title_small}', $pageTitle[1] );

				$this->tpl->set( '[page_title_small]', "" );
				$this->tpl->set( '[/page_title_small]', "" );

			} else {
				$this->tpl->set ( '{page_title_small}', '' );

				$this->tpl->setBlock( "'\\[page_title_small\\](.*?)\\[/page_title_small\\]'si", "" );

			}

		} else {
			$this->tpl->set ( '{page_title}', '' );
			$this->tpl->set ( '{page_title_small}', '' );

			$this->tpl->setBlock( "'\\[page_title\\](.*?)\\[/page_title\\]'si", "" );
			$this->tpl->setBlock( "'\\[page_title_small\\](.*?)\\[/page_title_small\\]'si", "" );

		}

		$this->tpl->compile( 'main' );

		if ( $replaceUrl ) {
			$this->tpl->result['main'] = str_replace ( $replaceUrl[0] . '/', $replaceUrl[1] . '/', $this->tpl->result['main'] );

		}

		echo $this->tpl->result['main'];

	}

}