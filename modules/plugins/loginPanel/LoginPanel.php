<?php

namespace Modules\Plugins\LoginPanel;

use Modules\Template\Template;

final class LoginPanel {
	private $member_id;
	public $tpl;

	/**
	 * LoginPanel constructor.
	 * @param array $member_id
	 * @param Template $tpl
	 */
	function __construct ( array $member_id, Template $tpl ) {
		$this->member_id = $member_id;
		$this->tpl = $tpl;

		$this->getLoginPanel();

	}

	public function getLoginPanel() {

		$this->tpl->loadTemplate( 'login_panel.tpl' );

		$this->tpl->set( '{user_id}', $this->member_id['user_id'] );
		$this->tpl->set( '{user_login}', $this->member_id['user_login'] );
		$this->tpl->set( '{user_email}', $this->member_id['user_email'] );

		if ( $this->member_id['user_avatar'] != '' ) {
			$this->tpl->set( '{user_avatar}', $this->member_id['user_avatar'] );

			$this->tpl->set( '[user_avatar]', '' );
			$this->tpl->set( '[/user_avatar]', '' );

		} else {
			$this->tpl->set( '{user_avatar}', '' );

			$this->tpl->setBlock( "'\\[user_avatar\\](.*?)\\[/user_avatar\\]'si", '' );

		}

		$this->tpl->set( '{user_group}', $this->member_id['user_group'] );
		$this->tpl->set( '{user_last_date}', $this->member_id['user_last_date'] );

		$this->tpl->compile( 'login_panel' );

		$this->tpl->clear();

	}

}