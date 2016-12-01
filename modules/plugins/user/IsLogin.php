<?php

namespace Modules\Plugins\User\IsLogin;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;

final class IsLogin {
	private $functions;
	private $db;
	private $config;
	private $time;

	public $is_logged = false;
	public $member_id = [];


	function __construct ( $action, Functions $functions, Db $db, array $config, $time ) {
		$this->functions = $functions;
		$this->db = $db;
		$this->config = $config;
		$this->time = $time;

		if ( $action == 'logout' ) {
			$this->logout();

		}

		$this->isLogged();

		if ( $this->is_logged ) {
			$this->loginUpdate();

		} else {
			$this->noLogin();
			$this->member_id['user_group'] = 5;

		}

	}

	private function isLogged () {
		if ( isset( $_COOKIE['user_id'] ) AND intval( $_COOKIE['user_id'] ) > 0 AND $_COOKIE['user_password'] ) {
			$this->member_id = $this->db->superQuery( "SELECT * FROM users WHERE `user_id` = '" . intval( $_COOKIE['user_id'] ) . "'" );

			if ( $this->member_id['user_id'] AND $this->member_id['user_password'] AND $this->member_id['user_password'] == md5( $_COOKIE['user_password'] ) ) {
				$this->is_logged = true;

				session_regenerate_id();

				$this->functions->setCookie( "user_id", $this->member_id['user_id'], 365 );
				$this->functions->setCookie( "user_password", $_COOKIE['user_password'], 365 );

			} else {
				$this->member_id = [];
				$this->is_logged = false;

			}

		}

	}

	private function loginUpdate () {
		if ( $this->config['online_status'] ) {
			$sTime = 1200;

		} else {
			$sTime = 14400;

		}

		if ( ( $this->member_id['user_last_date'] + $sTime ) < $this->time ) {
			$this->db->query( "UPDATE LOW_PRIORITY users SET `user_last_date` = '" . date( 'Y-m-d H:i:s', $this->time ) . "' WHERE `user_id` = '{$this->member_id['user_id']}'" );

		}

	}

	private function noLogin () {
		$this->member_id = [];
		$this->is_logged = false;
		$this->functions->setCookie( 'user_id', '', 0 );
		$this->functions->setCookie( 'user_password', '', 0 );

	}

	private function logout () {
		$this->member_id = array ();
		$this->is_logged = false;

		$this->functions->setCookie( 'user_id', '', 0 );
		$this->functions->setCookie( 'user_password', '', 0 );
		$this->functions->setCookie( session_name(), '', 0 );

		@session_destroy();
		@session_unset();

		header( 'Location: ' . $this->config['http_home_url'] );
		die();

	}

}