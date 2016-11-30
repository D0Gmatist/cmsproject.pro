<?php

namespace Modules\Plugins\Authorization;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Plugins\MsgBox\MsgBox;
use Modules\Template\Template;

final class Authorization {
	public $functions;
	public $db;
	public $tpl;
	public $config;
	public $language;

	public $is_logged = false;
	public $member_id = array();

	/**
	 * Authorization constructor.
	 * @param Functions $functions
	 * @param Db $db
	 * @param Template $tpl
	 * @param array $config
	 * @param array $language
	 */
	function __construct ( Functions $functions, Db $db, Template $tpl, array $config, array $language ) {
		$this->functions = $functions;
		$this->db = $db;
		$this->tpl = $tpl;
		$this->config = $config;
		$this->language = $language;

		$this->functions->domain();
		$this->functions->Session();

	}

	public function registration () {


	}

	public function login ( MsgBox $msgBox ) {
		$login = $this->db->safesql( $_POST['login'] );
		$password = @md5( $_POST['password'] );

		$logged = true;
		if ( count( explode( '@', $login ) ) > 1 ) {
			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\/|\\\|\&\~\*\+]/", $login ) ) {
				$logged = false;

				$msgBox->getResult( $this->language['error'], $this->language['login'][1], 'error' );

			}
			$where = "`user_email` = '{$login}'";

		} else {
			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $login ) ) {
				$logged = false;

				$msgBox->getResult( $this->language['error'], $this->language['login'][1], 'error' );

			}
			$where = "`user_login` = '{$login}'";

		}

		if( $logged ) {
			$this->member_id = $this->db->superQuery( "SELECT * FROM users WHERE {$where}" );

			if ( $this->member_id['user_id'] AND $this->member_id['user_password'] AND $this->member_id['user_password'] == md5( $password ) ) {
				$this->cookieUp( $password );

				header( 'Location: ' . $this->config['http_home_url'] );
				die();

			} else {
				$msgBox->getResult( $this->language['error'], $this->language['login'][2], 'error' );

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

		header( 'Location: ' . $this->config['http_home_url'] );
		die();

	}

	public function getContent () {
		$this->tpl->loadTemplate( 'login.tpl' );

		switch ( $_POST['action'] ) {

			case 'forget' :

				$this->tpl->set( '{form_forget}', 'block' );
				$this->tpl->set( '{form_registration}', 'none' );
				$this->tpl->set( '{form_login}', 'none' );

				$this->tpl->set( '{forget_email}', $_POST['email'] );

				$this->tpl->set( '{registration_login}', '' );
				$this->tpl->set( '{registration_email}', '' );
				$this->tpl->set( '{registration_password}', '' );

				$this->tpl->set( '{login}', '' );
				$this->tpl->set( '{password}', '' );

				break;

			case 'registration' :

				$this->tpl->set( '{form_forget}', 'none' );
				$this->tpl->set( '{form_registration}', 'block' );
				$this->tpl->set( '{form_login}', 'none' );

				$this->tpl->set( '{forget_email}', '' );

				$this->tpl->set( '{registration_login}', $_POST['login'] );
				$this->tpl->set( '{registration_email}', $_POST['email'] );
				$this->tpl->set( '{registration_password}', '' );

				$this->tpl->set( '{login}', '' );
				$this->tpl->set( '{password}', '' );

				break;

			case 'login' :
			default:

				$this->tpl->set( '{form_forget}', 'none' );
				$this->tpl->set( '{form_registration}', 'none' );
				$this->tpl->set( '{form_login}', 'block' );

				$this->tpl->set( '{forget_email}', '' );

				$this->tpl->set( '{registration_login}', '' );
				$this->tpl->set( '{registration_email}', '' );
				$this->tpl->set( '{registration_password}', '' );

				$this->tpl->set( '{login}', $_POST['login'] );
				$this->tpl->set( '{password}', '' );

				break;

		}

		$this->tpl->compile( 'content' );

	}

}