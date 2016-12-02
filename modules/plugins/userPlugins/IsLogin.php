<?php

namespace Modules\Plugins\UserPlugins;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;

final class IsLogin {
	private $functions;
	private $db;
	private $config;
	private $time;

	public $isLogged = false;
	public $memberId = [];


	function __construct ( $action, Functions $functions, Db $db, array $config, $time ) {
		$this->functions = $functions;
		$this->db = $db;
		$this->config = $config;
		$this->time = $time;

		if ( $action == 'logout' ) {
			$this->logout();

		}

		$this->isLogged();

		if ( $this->isLogged ) {
			$this->loginUpdate();

		} else {
			$this->noLogin();
			$this->memberId['user_group'] = 5;

		}

	}

	private function isLogged () {
		if ( isset( $_COOKIE['user_id'] ) AND intval( $_COOKIE['user_id'] ) > 0 AND $_COOKIE['user_password'] ) {
			$this->memberId = $this->db->superQuery( "SELECT * FROM users WHERE `user_id` = '" . intval( $_COOKIE['user_id'] ) . "'" );

			if ( $this->memberId['user_id'] AND $this->memberId['user_password'] AND $this->memberId['user_password'] == md5( $_COOKIE['user_password'] ) ) {
				$this->isLogged = true;

				session_regenerate_id();

				$this->functions->setCookie( "user_id", $this->memberId['user_id'], 365 );
				$this->functions->setCookie( "user_password", $_COOKIE['user_password'], 365 );

			} else {
				$this->memberId = [];
				$this->isLogged = false;

			}

		}

	}

	private function loginUpdate () {
		if ( $this->config['online_status'] ) {
			$sTime = 1200;

		} else {
			$sTime = 14400;

		}

		if ( ( $this->memberId['user_last_date'] + $sTime ) < $this->time ) {
			$this->db->query( "UPDATE LOW_PRIORITY users SET `user_last_date` = '" . date( 'Y-m-d H:i:s', $this->time ) . "' WHERE `user_id` = '{$this->memberId['user_id']}'" );

		}

	}

	private function noLogin () {
		$this->memberId = [];
		$this->isLogged = false;
		$this->functions->setCookie( 'user_id', '', 0 );
		$this->functions->setCookie( 'user_password', '', 0 );

	}

	private function logout () {
		$this->memberId = [];
		$this->isLogged = false;

		$this->functions->setCookie( 'user_id', '', 0 );
		$this->functions->setCookie( 'user_password', '', 0 );
		$this->functions->setCookie( session_name(), '', 0 );

		@session_destroy();
		@session_unset();

		header( 'Location: ' . $this->config['http_home_url'] );
		die();

	}

}