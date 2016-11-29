<?php

namespace Modules\Plugins\Authorization;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;

final class Authorization {
	public $functions;
	public $db;
	public $config;

	public $is_logged = false;
	public $member_id = array();

	/**
	 * Authorization constructor.
	 * @param Functions $functions
	 * @param Db $db
	 * @param array $config
	 */
	function __construct ( Functions $functions, Db $db, array $config  ) {
		$this->functions = $functions;
		$this->db = $db;
		$this->config = $config;

		$this->functions->domain ();
		$this->functions->Session();

	}

	public function login () {
		$login = $this->db->safesql( $_POST['login'] );
		$password = @md5( $_POST['password'] );

		$logged = true;
		if ( count( explode( '@', $login ) ) > 1 ) {
			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\/|\\\|\&\~\*\+]/", $login ) ) {
				$logged = false;

			}
			$where = "`user_email` = '{$login}'";

		} else {
			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $login ) ) {
				$logged = false;

			}
			$where = "`user_login` = '{$login}'";

		}

		if( $logged ) {
			$this->member_id = $this->db->superQuery( "SELECT * FROM users WHERE {$where}" );

			if ( $this->member_id['user_id'] AND $this->member_id['user_password'] AND $this->member_id['user_password'] == md5( $password ) ) {
				$this->cookieUp( $password );

			}

		}

	}

	public function isLogged () {
		if ( isset( $_COOKIE['user_id'] ) AND intval( $_COOKIE['user_id'] ) > 0 AND $_COOKIE['user_password'] ) {
			$this->member_id = $this->db->superQuery( "SELECT * FROM users WHERE `user_id` = '" . intval( $_COOKIE['user_id'] ) . "'" );

			if ( $this->member_id['user_id'] AND $this->member_id['user_password'] AND $this->member_id['user_password'] == md5( $_COOKIE['user_password'] ) ) {
				$this->cookieUp( $_COOKIE['user_password'] );

			} else {
				$this->member_id = array ();
				$this->is_logged = false;

			}

		}

	}

	/**
	 * @param $_TIME
	 */
	public function loginUpdate ( $_TIME ) {
		if ( $this->config['online_status'] ) {
			$sTime = 1200;

		} else {
			$sTime = 14400;

		}

		if ( ( $this->member_id['lastdate'] + $sTime ) < $_TIME ) {
			$this->db->query( "UPDATE LOW_PRIORITY users SET `user_last_date` = '" . date( 'Y-m-d H:i:s', $_TIME ) . "' WHERE `user_id` = '{$this->member_id['user_id']}'" );

		}

		if ( ! $this->functions->allowedIp( $this->member_id['allowed_ip'] ) ) {
			$this->noLogin();

			//new MsgBox( 'login_err', 'ip_block_login' );

		}

	}

	private function cookieUp ( $password ) {
		$this->is_logged = TRUE;

		session_regenerate_id();

		$this->functions->setCookie( "user_id", $this->member_id['user_id'], 365 );
		$this->functions->setCookie( "user_password", $password, 365 );


	}

	public function noLogin () {
		$this->member_id = array ();
		$this->is_logged = false;
		$this->functions->setCookie( 'user_id', '', 0 );
		$this->functions->setCookie( 'user_password', '', 0 );

	}

	public function logout () {
		$this->member_id = array ();
		$this->is_logged = false;

		$this->functions->setCookie( 'user_id', '', 0 );
		$this->functions->setCookie( 'user_password', '', 0 );
		$this->functions->setCookie( session_name(), '', 0 );

		@session_destroy();
		@session_unset();

		header( 'Location: ' . str_replace( 'index.php', '', $_SERVER['PHP_SELF'] ) );
		die();

	}

}