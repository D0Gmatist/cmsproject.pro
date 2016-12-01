<?php

namespace Modules\Plugins\Authorization;

use Modules\Functions\Functions;
use Modules\Mail\Mail;
use Modules\Mysql\Db\Db;
use Modules\Plugins\MsgBox\MsgBox;
use Modules\Template\Template;

final class Authorization {
	private $functions;
	private $db;
	private $tpl;
	private $config;
	private $language;

	public $is_logged = false;
	public $member_id = array();

	private $registration = false;

	/**
	 * Authorization constructor.
	 * @param $action
	 * @param $time
	 * @param Functions $functions
	 * @param Db $db
	 * @param Template $tpl
	 * @param MsgBox $msgBox
	 * @param Mail $mail
	 * @param array $config
	 * @param array $language
	 */
	function __construct ( $action, $time, Functions $functions, Db $db, Template $tpl, MsgBox $msgBox, Mail $mail, array $config, array $language ) {
		$this->action = $action;
		$this->time = $time;
		$this->functions = $functions;
		$this->db = $db;
		$this->tpl = $tpl;
		$this->msgBox = $msgBox;
		$this->mail = $mail;
		$this->config = $config;
		$this->language = $language;

		$this->functions->domain();
		$this->functions->Session();

		if ( $this->action == 'logout' ) {
			$this->logout ();

		} else {
			if ( $_GET['subaction'] == 'validating' AND trim( $_GET['id'] ) != '' ) {
				$this->validating();

			} else {
				if ( $_POST[ 'action' ] == 'login' ) {
					$this->login ();

				} else if ( $_POST[ 'action' ] == 'registration' ) {
					$this->registration ();

				} else {
					$this->isLogged ();

				}

			}

		}

		if ( $this->is_logged ) {
			$this->loginUpdate( $this->time );

		} else {
			$this->noLogin();
			$this->member_id['user_group'] = 5;

		}

	}

	/**
	 * @param bool $login
	 * @return bool|mixed|string
	 */
	private function checkRegLogin ( $login = false ) {
		$status = true;

		if ( $login == false ) {
			$login = $_POST['login'];

		}
		$login = trim( $this->db->safeSql( htmlspecialchars( $login, ENT_COMPAT, $this->config['charset'] ) ) );
		$login = preg_replace( '#\s+#i', ' ', $login );

		if ( $this->functions->strLen( $login, $this->config['charset'] ) > 40 OR $this->functions->strLen( $login, $this->config['charset'] ) < 3 ) {
			$this->msgBox->getResult( false, $this->language['registration'][4], 'error', 'msg_registration' );
			$status = false;

		}

		if (
			preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\#|\/|\\\|\&\~\*\{\+]/", $login )
			OR
			strpos( strtolower ( $login ) , '.php' ) !== false
			OR
			stripos( urlencode( $login ), "%AD" ) !== false
		) {
			$this->msgBox->getResult( false, $this->language['registration'][5], 'error', 'msg_registration' );
			$status = false;

		}

		if ( $status === false ) {
			return $status;

		}

		return $login;

	}

	/**
	 * @param bool $email
	 * @return bool|string
	 */
	private function checkRegEmail ( $email = false ) {
		$status = true;

		if ( $email == false ) {
			$email = $_POST['email'];

		}
		$notAllowSymbol = [
			"\x22", "\x60", "\t", '\n',
			'\r', "\n", "\r", '\\', ",",
			"/", "¬", "#", ";", ":", "~",
			"[", "]", "{", "}", ")", "(",
			"*", "^", "%", "$", "<", ">",
			"?", "!", '"', "'", " ", "&"
		];
		$email = trim( $this->db->safeSql( str_replace( $notAllowSymbol, '', strip_tags( stripslashes( $email ) ) ) ) );

		if( empty( $email ) OR strlen( $email ) > 40 OR @count( explode( "@", $email ) ) != 2 ) {
			$this->msgBox->getResult( false, $this->language['registration'][6], 'error', 'msg_registration' );
			$status = false;

		}

		if ( $status === false ) {
			return $status;

		}

		return $email;

	}

	/**
	 * @param bool $password
	 * @return bool
	 */
	private function checkRegPassword ( $password = false ) {
		$status = true;

		if ( $password == false ) {
			$password = $_POST['password'];

		}
		if( strlen( $password ) < 8 ) {
			$this->msgBox->getResult( false, $this->language['registration'][7], 'error', 'msg_registration' );
			$status = false;

		}

		if ( $status === false ) {
			return $status;

		}

		return $password;

	}

	public function validating () {
		$user_arr = explode( '||', base64_decode( @rawurldecode( trim( $_GET['id'] ) ) ) );

		$regLogin = trim( $this->db->safeSql( htmlspecialchars( $user_arr[0], ENT_COMPAT, $this->config['charset'] ) ) );
		$regEmail = $this->checkRegEmail( $user_arr[1] );
		$regPassword = md5( $user_arr[2] );

		$strongHash = $this->db->getStrongHash();

		if( sha1( $regLogin . $regEmail . $strongHash . $this->config['key'] ) == $user_arr[3] ) {
			$this->msgBox->getResult( false, $this->language['registration'][11], 'error', 'msg_registration' );
			$this->registration = false;

		}

	}

	public function registration () {
		$this->registration = true;
		$login = false;
		$email = false;
		$password = false;

		if ( $this->config['allow_registration'] != 1 ) {
			$this->msgBox->getResult( false, $this->language['registration'][1], 'error', 'msg_registration' );
			$this->registration = false;

		}

		if ( $this->registration == true AND $this->config['max_users'] > 0 ) {
			$row = $this->db->superQuery( "SELECT COUNT(*) AS count FROM users" );

			if ( $row['count'] >= $this->config['max_users'] ) {
				$this->msgBox->getResult( false, $this->language['registration'][2], 'error', 'msg_registration' );
				$this->registration = false;

			}

		}

		if ( $this->registration == true AND $this->is_logged == true ) {
			$this->msgBox->getResult( false, $this->language['registration'][3], 'error', 'msg_registration' );
			$this->registration = false;

		}

		if ( $this->registration == true ) {
			$login = $this->checkRegLogin();
			$email = $this->checkRegEmail();
			$password = $this->checkRegPassword();

			if ( $login === false OR $email === false OR $password === false ) {
				$this->registration = false;

			}

		}

		if ( $this->registration == true ) {
			if ( function_exists( 'mb_strtolower' ) ) {
				$searchLogin1 = trim( mb_strtolower( $login, $this->config['charset'] ) );

			} else {
				$searchLogin1 = trim( strtolower( $login ) );

			}
			$searchLogin2 = strtr( $searchLogin1, $this->language['relatesWord'] );

			$row = $this->db->superQuery( "SELECT 
												COUNT(*) AS count 
													FROM 
														users 
															WHERE 
																`user_email` = '{$email}' 
																	OR 
																`user_login` = '{$searchLogin1}' 
																	OR 
																LOWER( `user_login` ) REGEXP '[[:<:]]{$searchLogin2}[[:>:]]'" );

			if ( $row['count'] ) {
				$this->msgBox->getResult( false, $this->language['registration'][8], 'error', 'msg_registration' );
				$this->registration = false;

			}

		}

		if ( $this->registration == true ) {
			$strongHash = $this->db->getStrongHash();

			$row = $this->db->superQuery( "SELECT * FROM email WHERE `name` = 'reg_mail' LIMIT 1" );

			$this->mail->doSend( $this->config, $row['html'] );

			$row['template'] = stripslashes( $row['template'] );

			$idLink = rawurlencode( base64_encode( $login . "||" . $email . "||" . md5( $password ) . "||" . sha1( $login . $email . $strongHash . $this->config['key'] ) ) );

			if ( strpos( $this->config['http_home_url'], "//" ) === 0 ) {
				$sLink = "http:" . $this->config['http_home_url'];

			} else if ( strpos( $this->config['http_home_url'], "/" ) === 0 ) {
				$sLink = "http://" . $_SERVER['HTTP_HOST'] . $this->config['http_home_url'];

			} else {
				$sLink = $this->config['http_home_url'];

			}

			$row['template'] = str_replace( "{%username%}", $login, $row['template'] );
			$row['template'] = str_replace( "{%email%}", $email, $row['template'] );
			$row['template'] = str_replace( "{%validationlink%}", $sLink . "index.php?action=login&subaction=validating&id=" . $idLink, $row['template'] );
			$row['template'] = str_replace( "{%password%}", $password, $row['template'] );

			$this->mail->send( $email, $this->language['registration'][9], $row['template'] );

			if( $this->mail->send_error ) {
				$this->msgBox->getResult( false, $this->mail->smtp_msg, 'error', 'msg_registration' );
				$this->registration = false;

			} else {
				$this->msgBox->getResult( false, $this->language['registration'][10], 'success', 'msg_registration' );

			}


		}


	}

	public function login () {
		$login = $this->db->safesql( $_POST['login'] );
		$password = @md5( $_POST['password'] );

		$logged = true;
		if ( count( explode( '@', $login ) ) > 1 ) {
			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\/|\\\|\&\~\*\+]/", $login ) ) {
				$logged = false;

				$this->msgBox->getResult( false, $this->language['login'][1], 'error', 'msg_login' );

			}
			$where = "`user_email` = '{$login}'";

		} else {
			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $login ) ) {
				$logged = false;

				$this->msgBox->getResult( false, $this->language['login'][1], 'error', 'msg_login' );

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
				$this->msgBox->getResult( false, $this->language['login'][2], 'error', 'msg_login' );

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

				$this->tpl->set( '{forget_email}', $_POST['mail'] );

				$this->tpl->set( '{registration_login}', '' );
				$this->tpl->set( '{registration_email}', '' );
				$this->tpl->set( '{registration_password}', '' );

				$this->tpl->set( '{login}', '' );
				$this->tpl->set( '{password}', '' );

				$this->tpl->set( '{msg_forget}', $this->tpl->result['msg_forget'] );
				$this->tpl->set( '{msg_registration}', '' );
				$this->tpl->set( '{msg_login}', '' );

				break;

			case 'registration' :

				$this->tpl->set( '{form_forget}', 'none' );
				$this->tpl->set( '{form_registration}', 'block' );
				$this->tpl->set( '{form_login}', 'none' );

				$this->tpl->set( '{forget_email}', '' );

				$this->tpl->set( '{registration_login}', $_POST['login'] );
				$this->tpl->set( '{registration_email}', $_POST['mail'] );
				$this->tpl->set( '{registration_password}', '' );

				$this->tpl->set( '{login}', '' );
				$this->tpl->set( '{password}', '' );

				$this->tpl->set( '{msg_forget}', '' );
				$this->tpl->set( '{msg_registration}', $this->tpl->result['msg_registration'] );
				$this->tpl->set( '{msg_login}', '' );

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

				$this->tpl->set( '{msg_forget}', '' );
				$this->tpl->set( '{msg_registration}', '' );
				$this->tpl->set( '{msg_login}', $this->tpl->result['msg_login'] );

				break;

		}

		$this->tpl->compile( 'content' );

	}

}