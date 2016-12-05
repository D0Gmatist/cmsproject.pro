<?php

namespace Modules\Plugins\UserPlugins;

use Modules\Functions\Functions;
use Modules\Mail\Mail;
use Modules\Mysql\Db\Db;
use Modules\Plugins\MsgBox\MsgBox;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class Authorization {

	/** @var bool  */
	private $isLogged = false;

	/** @var array  */
	private $memberId;

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

	/** @var MsgBox  */
	private $msgBox;

	/** @var Mail  */
	private $mail;

	/** @var bool  */
	private $logged = true;

	/**
	 * Authorization constructor.
	 * @param $isLogged
	 * @param array $memberId
	 * @param $action
	 * @param Functions $functions
	 * @param Db $db
	 * @param Template $tpl
	 * @param MsgBox $msgBox
	 * @param Mail $mail
	 * @param array $config
	 * @param array $language
	 */
	function __construct ( $isLogged, array $memberId, $action, Functions $functions, Db $db, Template $tpl, MsgBox $msgBox, Mail $mail, array $config, array $language ) {
		$this->isLogged = $isLogged;
		$this->memberId = $memberId;

		$this->functions = $functions;
		$this->db = $db;

		$this->config = $config;
		$this->language = $language;

		$this->tpl = $tpl;
		$this->msgBox = $msgBox;
		$this->mail = $mail;

		if ( $_POST[ 'action' ] == 'login' ) {
			$this->login();

		}

	}

	private function login () {
		$login = $this->db->safesql( $_POST['login'] );
		$password = @md5( $_POST['password'] );

		if ( count( explode( '@', $login ) ) > 1 ) {
			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\/|\\\|\&\~\*\+]/", $login ) ) {
				$this->logged = false;
				$this->msgBox->getResult( false, $this->language['authorization'][1], 'error' );

			}
			$where = "`user_email` = '{$login}'";

		} else {
			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $login ) ) {
				$this->logged = false;
				$this->msgBox->getResult( false, $this->language['authorization'][1], 'error' );

			}
			$where = "`user_login` = '{$login}'";

		}

		if( $this->logged ) {
			$row = $this->db->superQuery( "SELECT * FROM users WHERE {$where}" );

			if ( $row['user_id'] AND $row['user_password'] AND $row['user_password'] == md5( $password ) ) {
				session_regenerate_id();

				$this->functions->setCookie( "user_id", $row['user_id'], 365 );
				$this->functions->setCookie( "user_password", $password, 365 );

				header( 'Location: ' . $this->config['http_home_url'] );
				die();

			} else {
				$this->msgBox->getResult( false, $this->language['authorization'][2], 'error' );

			}

		}

	}

}