<?php

namespace Modules\Plugins\UserPanel;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

final class UserPanel {

	/** @var bool  */
	private $isLogged = false;

	/** @var array  */
	private $memberId;

	/** @var array  */
	private $groupVar;

	/** @var Functions  */
	private $functions;

	/** @var Db  */
	private $db;

	/** @var array  */
	private $config;

	/** @var array  */
	private $language;

	/** @var Template  */
	private $tpl;

	/**
	 * UserPanel constructor.
	 * @param $isLogged
	 * @param array $memberId
	 * @param array $groupVar
	 * @param Functions $functions
	 * @param Db $db
	 * @param Template $tpl
	 * @param array $config
	 * @param array $language
	 * @internal param $action
	 */
	function __construct ( $isLogged, array $memberId, array $groupVar, Functions $functions, Db $db, Template $tpl, array $config, array $language ) {
		$this->isLogged = $isLogged;
		$this->memberId = $memberId;
		$this->groupVar = $groupVar;

		$this->functions = $functions;
		$this->db = $db;

		$this->config = $config;
		$this->language = $language;

		$this->tpl = $tpl;

		$this->getLoginPanel();

		$this->groupVar = $groupVar;
	}

	private function getLoginPanel () {

		$this->tpl->loadTemplate( 'user/user_panel.tpl' );

		if ( $this->isLogged ) {
			$this->tpl->set( '{user_id}', $this->memberId['user_id'] );
			$this->tpl->set( '{user_login}', $this->memberId['user_login'] );
			$this->tpl->set( '{user_email}', $this->memberId['user_email'] );

			if ( $this->memberId['user_avatar'] != '' ) {
				$this->tpl->set( '{user_avatar}', $this->memberId['user_avatar'] );

			} else {
				$this->tpl->set( '{user_avatar}', $this->config['http_home_url'] . 'templates/' . $this->config['skin'] . '/img/avatar.png' );

			}

			$this->tpl->set( '{user_group}', $this->memberId['user_group'] );

		} else {
			$this->tpl->set( '{user_id}', '' );
			$this->tpl->set( '{user_login}', $this->language['userPanel'][1] );
			$this->tpl->set( '{user_email}', '' );
			$this->tpl->set( '{user_avatar}', $this->config['http_home_url'] . 'templates/' . $this->config['skin'] . '/img/avatar.png' );
			$this->tpl->set( '{user_group}', 5 );

		}

		$this->tpl->compile( 'user_panel' );

		$this->tpl->clear();

	}

}