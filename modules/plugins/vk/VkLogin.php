<?php

namespace Modules\Plugins\Vk;

use Modules\Functions\Functions;
use Modules\Mail\Mail;
use Modules\Mysql\Db\Db;
use Modules\Plugins\MsgBox\MsgBox;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

/**
 * Class VkLogin
 * @package Modules\Plugins\Vk
 */

final class VkLogin {

	/** @var array  */
	private $fields = [
		'photo_200', 'sex', 'bdate', 'city', 'country',
		'mobile_phone', 'home_phone', 'skype',
		'facebook', 'facebook_name', 'twitter',
		'site', 'followers_count', 'common_count'
	];

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

	/** @var int  */
	private $step = 0;

	/** @var string  */
	private $accessTokenUrl;

	/** @var string  */
	private $paramsUrl;

	/** @var string  */
	private $userVkId;

	/** @var string  */
	private $userVkToken;

	/** @var string  */
	private $response;

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

		if ( trim( $_GET[ 'code' ] ) != '' AND ! $isLogged ) {
			$this->step = 1;
			$this->getVkUserIdAndToken();

		}

	}

	private function accessToken () {
		$url = 'https://oauth.vk.com/access_token?';

		$code =  $_GET['code'];

		$accessTokenUrl = [
			'client_id'			=> $this->config['vk_app_id'],
			'client_secret'		=> $this->config['vk_app_secret'],
			'code'				=> $code,
			'redirect_uri' 		=> HTTP_HOME_URL . $this->config['vk_login'],

		];

		$this->accessTokenUrl = json_decode( file_get_contents( $url . urldecode( http_build_query( $accessTokenUrl ) ) ), true );

	}

	private function getVkUserIdAndToken () {
		$this->registration = true;

		$this->accessToken();

		if ( (int)$this->accessTokenUrl['user_id'] > 0 AND isset( $this->accessTokenUrl['access_token'] ) ) {
			$this->userVkId 	= $this->accessTokenUrl['user_id'];
			$this->userVkToken 	= $this->accessTokenUrl['access_token'];
			$this->step = 2;
			$this->getVkUserInfo();

		}

	}

	private function params () {
		$url = 'https://api.vk.com/method/users.get?';

		$userVkIdObj = [ $this->userVkId ];
		if ( ! is_array( $this->userVkId  ) ) {
			$userVkId = explode( ',', $this->userVkId );
			$userVkIdObj = [ $userVkId[0] ];

		}

		$paramsUrl = [
			'user_ids'			=> $userVkIdObj,
			'fields'			=> $this->fields,
			'access_token'		=> $this->userVkToken,
			'v'					=> '5.8',

		];

		$this->paramsUrl = json_decode( file_get_contents( $url . urldecode( http_build_query( $paramsUrl ) ) ), true );

	}

	private function getVkUserInfo () {
		$this->params();

		if ( is_array( $this->paramsUrl['response'][0] ) ) {
			$this->response =  $this->paramsUrl['response'][0];
			if ( $this->searchDouble ( $this->userVkId ) > 0 ) {
				$this->login();

			} else {
				$this->registration();

			}

		}

	}

	/**
	 * @return string
	 */
	private function userLogin() {
		$result = '';
		if ( $this->response['nickname'] ) {
			$result = trim( $this->db->safeSql( htmlspecialchars( $this->response['nickname'], ENT_COMPAT, $this->config['charset'] ) ) );

			if ( $this->functions->strLen( $result, $this->config['charset'] ) > 40 OR $this->functions->strLen( $result, $this->config['charset'] ) < 3 ) {
				$result = '';

			} else {
				return $result;

			}

		}

		if ( $result == '' ) {
			$result = trim( $this->db->safeSql( htmlspecialchars( $this->response['first_name'] . ' ' . $this->response['last_name'], ENT_COMPAT, $this->config['charset'] ) ) );

			if ( $this->functions->strLen( $result, $this->config['charset'] ) > 40 OR $this->functions->strLen( $result, $this->config['charset'] ) < 3 ) {
				$result = '';

			} else {
				return $result;

			}

		}

		if ( $result == '' ) {
			return crc32( microtime() . $this->response['nickname'] . $this->response['first_name'] . $this->response['last_name'] );

		}

	}

	/**
	 * @return string
	 */
	private function userFirstName() {
		$result = '';
		if ( $this->response['first_name'] ) {
			$result = trim( $this->db->safeSql( htmlspecialchars( $this->response['first_name'], ENT_COMPAT, $this->config['charset'] ) ) );

			if ( $result == '' ) {
				return '';

			}

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userLastName() {
		$result = '';
		if ( $this->response['last_name'] ) {
			$result = trim( $this->db->safeSql( htmlspecialchars( $this->response['last_name'], ENT_COMPAT, $this->config['charset'] ) ) );

			if ( $result == '' ) {
				return '';

			}

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userAvatar() {
		$result = '';
		if ( trim( $this->response['photo_200'] ) != '' ) {
			return trim( $this->response['photo_200'] );

		}

		return $result;

	}

	/**
	 * @return int
	 */
	private function userSex() {
		switch ( $this->response['sex'] ) {
			case 2 :
				$result = 2;
				break;

			case 1 :
				$result = 1;
				break;

			default :
				$result = 0;

		}

		return $result;

	}

	/**
	 * @return false|string
	 */
	private function userBirthdayDate() {
		$result = '';
		if ( trim( $this->response['bdate'] ) != '' ) {
			return date( 'Y-m-d H:i:s', strtotime( $this->response['bdate'] ) );

		}

		return $result;

	}

	/**
	 * @return int
	 */
	private function userCityId() {

		return (int)$this->response['city']['id'];

	}

	/**
	 * @return int
	 */
	private function userCountryId() {

		return (int)$this->response['country']['id'];

	}

	/**
	 * @return string
	 */
	private function userMobilePhone() {
		$result = '';
		if ( trim( $this->response['mobile_phone'] ) != '' ) {
			return trim( $this->response['mobile_phone'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userHomePhone() {
		$result = '';
		if ( trim( $this->response['home_phone'] ) != '' ) {
			return trim( $this->response['home_phone'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userSkype() {
		$result = '';
		if ( trim( $this->response['skype'] ) != '' ) {
			return trim( $this->response['skype'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userFacebook() {
		$result = '';
		if ( trim( $this->response['facebook'] ) != '' ) {
			return trim( $this->response['facebook'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userFacebookName() {
		$result = '';
		if ( trim( $this->response['facebook_name'] ) != '' ) {
			return trim( $this->response['facebook_name'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userTwitter() {
		$result = '';
		if ( trim( $this->response['twitter'] ) != '' ) {
			return trim( $this->response['twitter'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userSite() {
		$result = '';
		if ( trim( $this->response['site'] ) != '' ) {
			return trim( $this->response['site'] );

		}

		return $result;

	}

	/**
	 * @return int
	 */
	private function userFollowersCount() {

		return (int)$this->response['followers_count'];

	}

	/**
	 * @return int
	 */
	private function userCommonCount() {

		return (int)$this->response['common_count'];

	}

	private function registration () {
		$date = date( 'Y-m-d H:i:s', time() );
		$password = crc32( md5( $date ) );

		$userLogin				= $this->userLogin();
		$userAvatar 			= $this->userAvatar();
		$userFirstName 			= $this->userFirstName();
		$userLastName 			= $this->userLastName();
		$userSex 				= $this->userSex();
		$userBirthdayDate 		= $this->userBirthdayDate();
		$userCityId 			= $this->userCityId();
		$userCountryId 			= $this->userCountryId();
		$userMobilePhone 		= $this->userMobilePhone();
		$userHomePhone 			= $this->userHomePhone();
		$passwordSave 			= md5( md5( $password ) );
		$userSkype 				= $this->userSkype();
		$userFacebook			= $this->userFacebook();
		$userFacebookName		= $this->userFacebookName();
		$userTwitter 			= $this->userTwitter();
		$userSite 				= $this->userSite();
		$userFollowersCount 	= $this->userFollowersCount();
		$userCommonCount 		= $this->userCommonCount();
		$userLastDate 			= $date;
		$userRegDate 			= $date;
		$userVkId				= $this->userVkId;
		$userVkToken			= $this->userVkToken;

		$this->db->query( "INSERT INTO
									users
										( 
											`user_group`, 
											`user_login`, 
											`user_avatar`, 
											`user_first_name`, 
											`user_last_name`, 
											`user_sex`, 
											`user_birthday_date`, 
											`user_city_id`, 
											`user_country_id`, 
											`user_mobile_phone`, 
											`user_home_phone`, 
											`user_password`, 
											`user_email`, 
											`user_skype`, 
											`user_facebook`, 
											`user_facebook_name`, 
											`user_twitter`, 
											`user_site`, 
											`user_followers_count`, 
											`user_common_count`, 
											`user_last_date`, 
											`user_reg_date`, 
											`user_vk_id`, 
											`user_vk_token`
										)
											VALUES 
													( 
														4, 
														'{$userLogin}', 
														'{$userAvatar}', 
														'{$userFirstName}', 
														'{$userLastName}', 
														'{$userSex}', 
														'{$userBirthdayDate}', 
														'{$userCityId}', 
														'{$userCountryId}', 
														'{$userMobilePhone}', 
														'{$userHomePhone}', 
														'{$passwordSave}', 
														'', 
														'{$userSkype}', 
														'{$userFacebook}', 
														'{$userFacebookName}', 
														'{$userTwitter}', 
														'{$userSite}', 
														'{$userFollowersCount}', 
														'{$userCommonCount}', 
														'{$userLastDate}', 
														'{$userRegDate}', 
														'{$userVkId}', 
														'{$userVkToken}' 
													)" );

		$user_id = (int)$this->db->insertId();
		if ( $user_id > 0 ) {
			$this->cookie( $user_id, $userVkToken );

		} else {
			$this->msgBox->getResult ( false, $this->language['vk_login'][3], 'error' );

		}

	}

	private function login () {
		$obj = [];
		$obj['user_avatar'] 			= $this->userAvatar();
		$obj['user_first_name'] 		= $this->userFirstName();
		$obj['user_last_name'] 			= $this->userLastName();
		$obj['user_sex'] 				= $this->userSex();
		$obj['user_birthday_date'] 		= $this->userBirthdayDate();
		$obj['user_city_id'] 			= $this->userCityId();
		$obj['user_country_id'] 		= $this->userCountryId();
		$obj['user_mobile_phone'] 		= $this->userMobilePhone();
		$obj['user_home_phone'] 		= $this->userHomePhone();
		$obj['user_skype'] 				= $this->userSkype();
		$obj['user_facebook'] 			= $this->userFacebook();
		$obj['user_facebook_name'] 		= $this->userFacebookName();
		$obj['user_twitter'] 			= $this->userTwitter();
		$obj['user_site'] 				= $this->userSite();
		$obj['user_followers_count']	= $this->userFollowersCount();
		$obj['user_common_count'] 		= $this->userCommonCount();
		$obj['user_last_date'] 			= date( 'Y-m-d H:i:s', time() );
		$obj['user_vk_token'] 			= $this->userVkToken;

		$setUpdate = [];
		foreach ( $obj AS $k => $v ) {
			if ( $v != false AND trim( $v ) != '' ) {
				$setUpdate[] = "`{$k}` = '{$v}'";
			}

		}

		$setUpdate = implode( ', ', $setUpdate );

		$this->db->query( "UPDATE 
									users 
										SET 
											{$setUpdate} 
												WHERE 
													`user_id` = '{$this->memberId['user_id']}'" );

		if ( (int)$this->memberId['user_id'] > 0 ) {
			$this->cookie( $this->memberId['user_id'], $obj['user_vk_token'] );

		} else {
			$this->msgBox->getResult ( false, $this->language['vk_login'][2], 'error' );

		}

	}

	/**
	 * @param $user_id
	 * @param $user_vk_token
	 */
	private function cookie ( $user_id, $user_vk_token ) {
		session_regenerate_id();

		$this->functions->setCookie( "user_id", $user_id, 365 );
		$this->functions->setCookie( "user_vk", md5( md5( $user_vk_token ) ), 365 );

		header( 'Location: ' . $this->config['http_home_url'] );
		die();

	}

	private function searchDouble ( $userVkId ) {
		$status = false;
		$this->memberId = $this->db->superQuery( "SELECT * FROM users WHERE `user_vk_id` = '{$userVkId}' LIMIT 1" );

		if ( $this->memberId['user_id'] ) {
			return $this->memberId['user_id'];

		} else {
			$this->memberId = false;

		}

		return $status;

	}

}