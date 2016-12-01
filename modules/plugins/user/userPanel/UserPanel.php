<?php

namespace Modules\Plugins\UserPanel;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

final class UserPanel {

	/** @var bool  */
	private $is_logged = false;

	/** @var array  */
	private $member_id;

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
	 * @param $is_logged
	 * @param array $member_id
	 * @param $action
	 * @param Functions $functions
	 * @param Db $db
	 * @param Template $tpl
	 * @param array $config
	 * @param array $language
	 */
	function __construct ( $is_logged, array $member_id, $action, Functions $functions, Db $db, Template $tpl, array $config, array $language ) {
		$this->is_logged = $is_logged;
		$this->member_id = $member_id;

		$this->functions = $functions;
		$this->db = $db;

		$this->config = $config;
		$this->language = $language;

		$this->tpl = $tpl;

		$this->getLoginPanel();

	}

	private function getLoginPanel () {

		$this->tpl->loadTemplate( 'user/user_panel.tpl' );

		$this->tpl->set( '{user_id}', $this->member_id['user_id'] );
		$this->tpl->set( '{user_login}', $this->member_id['user_login'] );
		$this->tpl->set( '{user_email}', $this->member_id['user_email'] );

		if ( $this->member_id['user_avatar'] != '' ) {
			$this->tpl->set( '{user_avatar}', $this->member_id['user_avatar'] );

		} else {
			$this->tpl->set( '{user_avatar}', $this->config['http_home_url'] . 'templates/' . $this->config['skin'] . '/img/avatar.png' );

		}

		$this->tpl->set( '{user_group}', $this->member_id['user_group'] );
		$this->tpl->set( '{user_last_date}', $this->member_id['user_last_date'] );

		$this->tpl->compile( 'user_panel' );

		$this->tpl->clear();

	}

}