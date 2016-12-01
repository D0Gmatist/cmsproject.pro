<?php

namespace Modules\Plugins\Registration;

use Modules\Functions\Functions;
use Modules\Mail\Mail;
use Modules\Mysql\Db\Db;
use Modules\Plugins\MsgBox\MsgBox;
use Modules\Template\Template;

final class Registration {

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
	private $registration = false;

	/** @var bool  */
	private $login = false;

	/** @var bool  */
	private $email = false;

	/** @var bool  */
	private $password = false;

	/** @var int  */
	private $step = 0;

	/**
	 * Registration constructor.
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

		if ( $_GET['subaction'] == 'validating' AND trim( $_GET['id'] ) != '' ) {
			$this->step = 2;
			$this->stepTwo();

		} else {
			if ( $_POST[ 'action' ] == 'registration' ) {
				$this->step = 1;
				$this->stepOne();

			}

		}

		$this->getContent();

	}

	/**
	 * @return bool|mixed|string
	 */
	private function checkLogin () {
		$this->login = $_POST['login'];

		$this->login = trim( $this->db->safeSql( htmlspecialchars( $this->login, ENT_COMPAT, $this->config['charset'] ) ) );
		$login = preg_replace( '#\s+#i', ' ', $this->login );

		if ( $this->functions->strLen( $this->login, $this->config['charset'] ) > 40 OR $this->functions->strLen( $this->login, $this->config['charset'] ) < 3 ) {
			$this->msgBox->getResult( false, $this->language['registration'][4], 'error' );
			$this->login = false;

		}

		if (
			preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\#|\/|\\\|\&\~\*\{\+]/", $this->login )
			OR
			strpos( strtolower( $login ) , '.php' ) !== false
			OR
			stripos( urlencode( $login ), "%AD" ) !== false
		) {
			$this->msgBox->getResult( false, $this->language['registration'][5], 'error' );
			$this->login = false;

		}

	}

	/**
	 * @return bool
	 */
	private function checkEmail () {
		$this->email = $_POST['email'];

		$notAllowSymbol = [
			"\x22", "\x60", "\t", '\n',
			'\r', "\n", "\r", '\\', ",",
			"/", "¬", "#", ";", ":", "~",
			"[", "]", "{", "}", ")", "(",
			"*", "^", "%", "$", "<", ">",
			"?", "!", '"', "'", " ", "&"
		];
		$this->email = trim( $this->db->safeSql( str_replace( $notAllowSymbol, '', strip_tags( stripslashes( $this->email ) ) ) ) );
		if( empty( $this->email ) OR $this->functions->strLen( $this->email, $this->config['charset'] ) > 40 OR @count( explode( "@", $this->email ) ) != 2 ) {
			$this->msgBox->getResult( false, $this->language['registration'][6], 'error' );
			return false;

		}

	}

	/**
	 * @return bool
	 */
	private function checkPassword () {
		$this->password = $_POST['password'];

		if( $this->functions->strLen( $this->password, $this->config['charset'] ) < 8 ) {
			$this->msgBox->getResult( false, $this->language['registration'][7], 'error' );
			return false;

		}

	}

	private function stepOne () {
		$this->registration = true;

		if ( $this->config['allow_registration'] != 1 ) {
			$this->msgBox->getResult( false, $this->language['registration'][1], 'error' );
			$this->registration = false;

		}

		if ( $this->registration == true AND $this->config['max_users'] > 0 ) {
			$row = $this->db->superQuery( "SELECT COUNT(*) AS count FROM users" );

			if ( $row['count'] >= $this->config['max_users'] ) {
				$this->msgBox->getResult( false, $this->language['registration'][2], 'error' );
				$this->registration = false;

			}

		}

		if ( $this->registration == true ) {
			$this->checkLogin();
			$this->checkEmail();
			$this->checkPassword();

			if ( $this->login === false OR $this->email === false OR $this->password === false ) {
				$this->registration = false;

			} else {
				$this->searchDoubleProfile();

			}

		}

		if ( $this->registration == true ) {
			if ( $this->config['mail_check'] == 1 ) {
				$row = $this->db->superQuery ( "SELECT * FROM email WHERE `name` = 'reg_mail' LIMIT 1" );

				$this->mail->doSend ( $this->config, $row[ 'html' ] );

				$row[ 'template' ] = stripslashes( $row[ 'template' ] );

				$idLink = rawurlencode( base64_encode( $this->login . "||" . $this->email . "||" . md5( $this->password ) . "||" . $this->keySha1 () ) );

				if ( strpos ( $this->config[ 'http_home_url' ], "//" ) === 0 ) {
					$sLink = "http:" . $this->config[ 'http_home_url' ];

				} else if ( strpos( $this->config[ 'http_home_url' ], "/" ) === 0 ) {
					$sLink = "http://" . $_SERVER[ 'HTTP_HOST' ] . $this->config[ 'http_home_url' ];

				} else {
					$sLink = $this->config[ 'http_home_url' ];

				}

				$row[ 'template' ] = str_replace( "{%username%}", $this->login, $row[ 'template' ] );
				$row[ 'template' ] = str_replace( "{%email%}", $this->email, $row[ 'template' ] );
				$row[ 'template' ] = str_replace( "{%validationlink%}", $sLink . "index.php?action=registration&subaction=validating&id=" . $idLink, $row[ 'template' ] );
				$row[ 'template' ] = str_replace( "{%password%}", $this->password, $row[ 'template' ] );

				$this->mail->send ( $this->email, $this->language[ 'registration' ][ 9 ], $row[ 'template' ] );

				if ( $this->mail->send_error ) {
					$this->msgBox->getResult ( false, $this->mail->smtp_msg, 'error' );
					$this->registration = false;

				} else {
					$this->step = 2;
					$this->msgBox->getResult ( false, $this->language[ 'registration' ][ 10 ], 'success' );

				}

			} else {
				$this->step = 3;
				$this->complete();

			}

		}

	}

	private function stepTwo ()	{
		$this->registration = true;

		$user_arr = explode( '||', base64_decode( @rawurldecode( trim( $_GET['id'] ) ) ) );

		$this->login = trim( $this->db->safeSql( htmlspecialchars( $user_arr[0], ENT_COMPAT, $this->config['charset'] ) ) );
		$this->email = $user_arr[1];
		$this->password = md5( $user_arr[2] );

		//$this->searchDoubleProfile();

		if ( $this->registration == true ) {
			if ( $this->keySha1 () != $user_arr[ 3 ] ) {
				$this->step = 0;
				$this->msgBox->getResult ( false, $this->language[ 'registration' ][ 11 ], 'error' );
				$this->registration = false;

			} else {
				$this->step = 3;
				$this->complete ();

			}

		}

	}

	private function complete () {
		$password = md5( md5( $this->password ) );
		$date = date( 'Y-m-d H:i:s', time() );

		$this->db->query( "INSERT INTO
								users
									( `user_login`, `user_email`, `user_password`, `user_group`, `user_last_date`, `user_reg_date` )
										VALUES
									( '{$this->login}', '{$this->email}', '{$password}', '4', '{$date}', '{$date}' )" );

		$id = $this->db->insertId();

		if ( (int)$id > 0 ) {
			$this->msgBox->getResult ( false, $this->language[ 'registration' ][ 12 ], 'success' );

			session_regenerate_id();

			$this->functions->setCookie( 'user_id', $id, 365 );
			$this->functions->setCookie( 'user_password',  md5( $this->password ), 365 );

		} else {
			$this->msgBox->getResult ( false, $this->language[ 'registration' ][ 11 ], 'error' );

		}


	}

	/**
	 * @return string
	 */
	private function keySha1 () {
		return sha1( $this->login . $this->email . $this->db->getStrongHash() . $this->config['key'] );

	}

	private function searchDoubleProfile () {
		if ( function_exists( 'mb_strtolower' ) ) {
			$searchLogin1 = trim( mb_strtolower( $this->login, $this->config['charset'] ) );

		} else {
			$searchLogin1 = trim( strtolower( $this->login ) );

		}
		$searchLogin2 = strtr( $searchLogin1, $this->language['relatesWord'] );

		$row = $this->db->superQuery( "SELECT 
												COUNT(*) AS count 
													FROM 
														users 
															WHERE 
																`user_email` = '{$this->email}' 
																	OR 
																`user_login` = '{$searchLogin1}' 
																	OR 
																LOWER( `user_login` ) REGEXP '[[:<:]]{$searchLogin2}[[:>:]]'" );

		if ( $row['count'] ) {
			$this->msgBox->getResult( false, $this->language['registration'][8], 'error' );
			$this->registration = false;

		}

	}

	private function getContent () {
		$this->tpl->loadTemplate( 'user/registration.tpl' );

		var_dump( $this->step );
		switch ( $this->step ) {

			case '3' :

				$this->tpl->setBlock( "'\\[form_registration\\](.*?)\\[/form_registration\\]'si", "" );

				$this->tpl->set( '{login}', '' );
				$this->tpl->set( '{email}', '' );
				$this->tpl->set( '{password}', '' );

				break;

			case '2' :

				$this->tpl->setBlock( "'\\[form_registration\\](.*?)\\[/form_registration\\]'si", "" );

				$this->tpl->set( '{login}', '' );
				$this->tpl->set( '{email}', '' );
				$this->tpl->set( '{password}', '' );

				break;

			case '1' :

				$this->tpl->set( '[form_registration]', "" );
				$this->tpl->set( '[/form_registration]', "" );

				$this->tpl->set( '{login}', $_POST['login'] );
				$this->tpl->set( '{email}', $_POST['email'] );
				$this->tpl->set( '{password}', '' );

				break;

			case '0' :
			default :

				$this->tpl->set( '[form_registration]', "" );
				$this->tpl->set( '[/form_registration]', "" );

				$this->tpl->set( '{login}', '' );
				$this->tpl->set( '{email}', '' );
				$this->tpl->set( '{password}', '' );

				break;

		}

		$this->tpl->compile( 'content' );

		$this->tpl->clear();

	}

}