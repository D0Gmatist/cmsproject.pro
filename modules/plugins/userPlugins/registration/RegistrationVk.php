<?php

namespace Modules\Plugins\UserPlugins;

use Modules\Functions\Functions;
use Modules\Mail\Mail;
use Modules\Mysql\Db\Db;
use Modules\Plugins\MsgBox\MsgBox;
use Modules\Template\Template;

final class RegistrationVk {

	/** @var array  */
	private  $fields = [
		'photo_id', 'verified', 'sex',
		'bdate', 'city', 'country', 'home_town',
		'has_photo', 'photo_50', 'photo_100',
		'photo_200_orig', 'photo_200', 'photo_400_orig',
		'photo_max', 'photo_max_orig', 'online',
		'lists', 'domain', 'has_mobile', 'contacts',
		'site', 'education', 'universities',
		'schools', 'status', 'last_seen',
		'followers_count', 'common_count',
		'occupation', 'nickname', 'relatives',
		'relation', 'personal', 'connections',
		'exports', 'wall_comments', 'activities',
		'interests', 'music', 'movies', 'tv',
		'books', 'games', 'about', 'quotes',
		'can_post', 'can_see_all_posts',
		'can_see_audio', 'can_write_private_message',
		'can_send_friend_request', 'is_favorite',
		'is_hidden_from_feed', 'timezone',
		'screen_name', 'maiden_name',
		'crop_photo', 'is_friend',
		'friend_status', 'career', 'military',
		'blacklisted', 'blacklisted_by_me'
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
	private $authorizeUrl;

	/** @var string  */
	private $accessTokenUrl;

	/** @var string  */
	private $paramsUrl;

	/** @var string  */
	private $userVkId;

	/** @var string  */
	private $userVkToken;

	/** @var array  */
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

		if ( trim( $_GET[ 'code' ] ) != '' ) {
			$this->step = 1;
			$this->getVkUserIdAndToken();

		}

		$this->getContent();

	}

	private function accessToken () {
		$url = 'https://oauth.vk.com/access_token';

		$code =  $_GET['code'];

		$accessTokenUrl = [
			'client_id'			=> '5755528',
			'client_secret'		=> 'fsNVfRlBCRhiJhVhn9D0',
			'code'				=> $code,
			'redirect_uri' 		=> HTTP_HOME_URL . $this->config['vk_app_redirect'] ,

		];

		$this->accessTokenUrl = $url . '?' . urldecode( http_build_query( $accessTokenUrl ) );

	}

	private function getVkUserIdAndToken () {
		$this->registration = true;

		$this->accessToken();

		$token = json_decode( file_get_contents( $this->accessTokenUrl ), true );

		if ( isset( $token['user_id'] ) AND isset( $token['access_token'] ) ) {
			$this->userVkId 	= $token['user_id'];
			$this->userVkToken 	= $token['access_token'];

			$this->step = 2;
			$this->getVkUserInfo();

		}

	}

	private function params () {
		$url = 'https://api.vk.com/method/users.get';

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

		$this->paramsUrl = $url . '?' . urldecode( http_build_query( $paramsUrl ) );

	}

	private function getVkUserInfo () {
		$this->params();

		$row = json_decode( file_get_contents( $this->paramsUrl ), true );

		if ( is_array( $row['response'][0] ) ) {
			$this->response =  $row['response'][0];
			$this->complete ();

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
			$result = trim( $this->db->safeSql( htmlspecialchars( $this->response['user_first_name'] . ' ' . $this->response['user_last_name'], ENT_COMPAT, $this->config['charset'] ) ) );

			if ( $this->functions->strLen( $result, $this->config['charset'] ) > 40 OR $this->functions->strLen( $result, $this->config['charset'] ) < 3 ) {
				$result = '';

			} else {
				return $result;

			}

		}

		if ( $result == '' ) {
			return crc32( microtime() . $this->response['nickname'] . $this->response['user_first_name'] . $this->response['user_last_name'] );

		}

	}

	/**
	 * @return string
	 */
	private function userFirstName() {
		$result = '';
		if ( $this->response['user_first_name'] ) {
			$result = trim( $this->db->safeSql( htmlspecialchars( $this->response['user_first_name'], ENT_COMPAT, $this->config['charset'] ) ) );

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
		if ( $this->response['user_last_name'] ) {
			$result = trim( $this->db->safeSql( htmlspecialchars( $this->response['user_last_name'], ENT_COMPAT, $this->config['charset'] ) ) );

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
		if ( trim( $this->response['user_birthday_date'] ) != '' ) {
			return date( 'Y-m-d H:i:s', strtotime( $this->response['user_birthday_date'] ) );

		}

		return $result;

	}

	/**
	 * @return int
	 */
	private function userCityId() {

		return (int)$this->response['user_city_id'];

	}

	/**
	 * @return int
	 */
	private function userCountryId() {

		return (int)$this->response['user_country_id'];

	}

	/**
	 * @return string
	 */
	private function userMobilePhone() {
		$result = '';
		if ( trim( $this->response['user_mobile_phone'] ) != '' ) {
			return trim( $this->response['user_mobile_phone'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userHomePhone() {
		$result = '';
		if ( trim( $this->response['user_home_phone'] ) != '' ) {
			return trim( $this->response['user_home_phone'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userSkype() {
		$result = '';
		if ( trim( $this->response['user_skype'] ) != '' ) {
			return trim( $this->response['user_skype'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userFacebook() {
		$result = '';
		if ( trim( $this->response['user_facebook'] ) != '' ) {
			return trim( $this->response['user_facebook'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userFacebookName() {
		$result = '';
		if ( trim( $this->response['user_facebook_name'] ) != '' ) {
			return trim( $this->response['user_facebook_name'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userTwitter() {
		$result = '';
		if ( trim( $this->response['user_twitter'] ) != '' ) {
			return trim( $this->response['user_twitter'] );

		}

		return $result;

	}

	/**
	 * @return string
	 */
	private function userSite() {
		$result = '';
		if ( trim( $this->response['user_site'] ) != '' ) {
			return trim( $this->response['user_site'] );

		}

		return $result;

	}

	/**
	 * @return int
	 */
	private function userFollowersCount() {

		return (int)$this->response['user_followers_count'];

	}

	/**
	 * @return int
	 */
	private function userCommonCount() {

		return (int)$this->response['user_common_count'];

	}

	private function complete () {
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

		if ( $this->isLogged ) {
			$set = [];
			$set[] = ( $userAvatar != '' ) 			? "`user_avatar` = '{$userAvatar}'" : '';
			$set[] = ( $userFirstName != '' ) 		? "`user_first_name` = '{$userFirstName}'" : '';
			$set[] = ( $userLastName != '' ) 		? "`user_last_name` = '{$userLastName}'" : '';
			$set[] = 								  "`user_sex` = '{$userSex}'";
			$set[] = ( $userBirthdayDate != '' )	? "`user_birthday_date` = '{$userBirthdayDate}'" : '';
			$set[] = 								  "`user_city_id` = '{$userCityId}'";
			$set[] = 								  "`user_country_id` = '{$userCountryId}'";
			$set[] = ( $userMobilePhone != '' ) 	? "`user_mobile_phone` = '{$userMobilePhone}'" : '';
			$set[] = ( $userHomePhone != '' ) 		? "`user_home_phone` = '{$userHomePhone}'" : '';
			$set[] = ( $userSkype != '' ) 			? "`user_skype` = '{$userSkype}'" : '';
			$set[] = ( $userFacebook != '' ) 		? "`user_facebook` = '{$userFacebook}'" : '';
			$set[] = ( $userFacebookName != '' ) 	? "`user_facebook_name` = '{$userFacebookName}'" : '';
			$set[] = ( $userTwitter != '' ) 		? "`user_twitter` = '{$userTwitter}'" : '';
			$set[] = ( $userSite != '' ) 			? "`user_site` = '{$userSite}'" : '';
			$set[] = 								  "`user_followers_count` = '{$userFollowersCount}'";
			$set[] = 								  "`user_common_count` = '{$userCommonCount}'";
			$set[] = 								  "`user_last_date` = '{$userRegDate}'";
			$set[] = 								  "`user_vk_id` = '{$userVkId}'";
			$set[] = 								  "`user_vk_token` = '{$userVkToken}'";

			$set = implode( ', ', $set );

			$this->db->query( "UPDATE 
									users 
										SET 
											{$set} 
												WHERE 
													`user_id` = '{$this->memberId['user_id']}'" );

			$id = $this->memberId['user_id'];

		} else {
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

			$id = $this->db->insertId();

		}

//		if ( (int)$id > 0 ) {
//			$this->msgBox->getResult ( false, $this->language[ 'registration' ][ 12 ], 'success' );
//
//			session_regenerate_id();
//
//			$this->functions->setCookie( 'user_id', $id, 365 );
//			$this->functions->setCookie( 'user_password',  md5( $password ), 365 );
//
//		} else {
//			$this->msgBox->getResult ( false, $this->language[ 'registration' ][ 11 ], 'error' );
//
//		}


	}

	private function authorize () {
		$url = 'https://oauth.vk.com/authorize';

		$authorizeUrl = [
			'client_id'     => $this->config['vk_app_id'],
			'redirect_uri'  => HTTP_HOME_URL . $this->config['vk_app_redirect'] ,
			'response_type' => 'code',
			'display' 		=> 'page',
			'scope' 		=> 'offline',
			'v' 			=> $this->config['vk_app_version'],

		];

		$this->authorizeUrl = $url . '?' . urldecode( http_build_query( $authorizeUrl ) );

	}

	private function getContent () {
		$this->authorize();

		$this->tpl->loadTemplate( 'user/registration_vk.tpl' );

		switch ( $this->step ) {

			case '1' :

				$this->tpl->setBlock( "'\\[form_registration_vk\\](.*?)\\[/form_registration_vk\\]'si", "" );

				break;

			case '0' :
			default :

				$this->tpl->set( '[form_registration_vk]', "" );
				$this->tpl->set( '[/form_registration_vk]', "" );

				break;

		}

		$this->tpl->set( '{url_vk_form}', $this->authorizeUrl );


		$this->tpl->compile( 'content' );

		$this->tpl->clear();

	}

}