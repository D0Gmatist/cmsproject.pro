<?php

namespace Modules\Plugins\Parser;


use Exception;
use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Plugins\Vk\VkApi;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

/**
 * Class ParserUser
 * @package Modules\Plugins\Parser
 */

class ParserUser {

	/** @var Functions  */
	private $functions;

	/** @var Db  */
	private $db;

	/** @var array  */
	private $config;

	/** @var array  */
	private $language;

	/** @var array  */
	private $memberId = [];

	/** @var int  */
	private $countStep = 200;

	private $step = 0;

	/** @var array  */
	private $response = [];

	/**
	 * ParserGroup constructor.
	 *
	 * @param Functions $functions
	 * @param Db        $db
	 * @param array     $config
	 * @param array     $language
	 */
	public function __construct ( Functions $functions, Db $db, array $config, array $language ) {

		$this->functions = $functions;
		$this->db = $db;

		$this->config = $config;
		$this->language = $language;

		$this->vkApi = new VkApi( $this->memberId, $this->config );

		$this->foreachUser();

		$this->db->close();

		die();

	}

	private function foreachUser() {
		if ( $this->getParserUser() === true /*AND $this->step < 5 */) {
			$this->step++;
			$this->foreachUser();

		}

	}

	private function getParserUser () {
		$this->memberId = $this->db->superQuery ( "SELECT * FROM users ORDER BY RAND() LIMIT 1" );

		$dateCheck = date( 'Y-m-d H:i:s', time() + 300 );

		$this->db->query ( "SELECT 
								* 
									FROM 
										parser_users 
											WHERE 
												`parser_u_lock` = '0' 
												OR 
												( 
													( `parser_u_lock` = '1' AND `parser_u_up_date` <= '{$dateCheck}' ) 
													OR 
													( `parser_u_lock` = '1' AND `parser_u_up_date` IS NULL ) 
												)
													ORDER BY 
														`parser_u_add_date`
															ASC,
														`parser_u_id`
															ASC 
																LIMIT {$this->countStep}" );

		$vk_user_ids = [];
		while ( $row = $this->db->getRow () ) {
			$vk_user_ids[] = $row['parser_u_vk_user_id'];

		}

		if ( count( $vk_user_ids ) < 1 ) {
			return false;

		}
		$vk_user_ids = implode( ',', $vk_user_ids );

		$dateUp = date( 'Y-m-d H:i:s', time() );

		$this->db->query ( "UPDATE parser_users SET `parser_u_lock` = '1', `parser_u_up_date` = '{$dateUp}' WHERE `parser_u_vk_user_id` IN ({$vk_user_ids})" );

		$result = $this->vkApi->getApiUsers ( $vk_user_ids );

		$this->db->query ( "SET autocommit = 0" );
		$this->db->query ( "START TRANSACTION" );

		if ( is_array ( $result[ 'response' ] ) AND count ( $result[ 'response' ] ) > 0 ) {

			foreach ( $result[ 'response' ] AS $response ) {
				$parser_u_vk_user_id = (int)$response['id'];

				if ( $parser_u_vk_user_id > 0 ) {
					$this->response = $response;

					$obj = [];
					$obj[ 'parser_u_lock' ] = '2';
					$obj[ 'parser_u_avatar' ] = $this->userAvatar ();
					$obj[ 'parser_u_first_name' ] = $this->userFirstName ();
					$obj[ 'parser_u_last_name' ] = $this->userLastName ();
					$obj[ 'parser_u_sex' ] = $this->userSex ();
					$obj[ 'parser_u_birthday_date' ] = $this->userBirthdayDate ();
					$obj[ 'parser_u_city_id' ] = $this->userCityId ();
					$obj[ 'parser_u_country_id' ] = $this->userCountryId ();
					$obj[ 'parser_u_mobile_phone' ] = $this->userMobilePhone ();
					$obj[ 'parser_u_home_phone' ] = $this->userHomePhone ();
					$obj[ 'parser_u_skype' ] = $this->userSkype ();
					$obj[ 'parser_u_facebook' ] = $this->userFacebook ();
					$obj[ 'parser_u_facebook_name' ] = $this->userFacebookName ();
					$obj[ 'parser_u_twitter' ] = $this->userTwitter ();
					$obj[ 'parser_u_site' ] = $this->userSite ();
					$obj[ 'parser_u_followers_count' ] = $this->userFollowersCount ();

					$setUpdate = [];
					foreach ( $obj AS $k => $v ) {
						if ( $v != false AND trim ( $v ) != '' ) {
							$setUpdate[] = "`{$k}` = '{$v}'";
						}

					}

					$setUpdate = implode ( ', ', $setUpdate );

					$this->db->query ( "UPDATE 
											parser_users 
												SET 
													{$setUpdate} 
														WHERE 
															`parser_u_vk_user_id` = '{$parser_u_vk_user_id}'" );

				}

			}

		}

		try {
			$this->db->query ( "COMMIT" );

		} catch ( Exception $e ) {
			$this->db->query ( "ROLLBACK" );

		}
		$this->db->query ( "SET autocommit = 1" );

		return true;

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
			return trim( $this->db->safeSql( htmlspecialchars( $this->response['mobile_phone'], ENT_COMPAT, $this->config['charset'] ) ) );

		}
		return $result;

	}

	/**
	 * @return string
	 */
	private function userHomePhone() {
		$result = '';
		if ( trim( $this->response['home_phone'] ) != '' ) {
			return trim( $this->db->safeSql( htmlspecialchars( $this->response['home_phone'], ENT_COMPAT, $this->config['charset'] ) ) );

		}
		return $result;

	}

	/**
	 * @return string
	 */
	private function userSkype() {
		$result = '';
		if ( trim( $this->response['skype'] ) != '' ) {
			return trim( $this->db->safeSql( htmlspecialchars( $this->response['skype'], ENT_COMPAT, $this->config['charset'] ) ) );

		}
		return $result;

	}

	/**
	 * @return string
	 */
	private function userFacebook() {
		$result = '';
		if ( trim( $this->response['facebook'] ) != '' ) {
			return trim( $this->db->safeSql( htmlspecialchars( $this->response['facebook'], ENT_COMPAT, $this->config['charset'] ) ) );

		}
		return $result;

	}

	/**
	 * @return string
	 */
	private function userFacebookName() {
		$result = '';
		if ( trim( $this->response['facebook_name'] ) != '' ) {
			return trim( $this->db->safeSql( htmlspecialchars( $this->response['facebook_name'], ENT_COMPAT, $this->config['charset'] ) ) );

		}
		return $result;

	}

	/**
	 * @return string
	 */
	private function userTwitter() {
		$result = '';
		if ( trim( $this->response['twitter'] ) != '' ) {
			return trim( $this->db->safeSql( htmlspecialchars( $this->response['twitter'], ENT_COMPAT, $this->config['charset'] ) ) );

		}
		return $result;

	}

	/**
	 * @return string
	 */
	private function userSite() {
		$result = '';
		if ( trim( $this->response['site'] ) != '' ) {
			return trim( $this->db->safeSql( htmlspecialchars( $this->response['site'], ENT_COMPAT, $this->config['charset'] ) ) );

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

}