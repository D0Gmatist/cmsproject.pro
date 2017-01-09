<?php

namespace Modules\Plugins\Vk;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class VkApi {

	/** @var array  */
	private $vkApiUrl = [
		'method' => 'https://api.vk.com/method/',
		'database' => [
			'getCountries' 	=> 'database.getCountries?',
			'getRegions' 	=> 'database.getRegions?',
			'getCities' 	=> 'database.getCities?'

		],
		'users' => [
			'search' 		=> 'users.search?',
			'get' 			=> 'users.get?'


		],
		'groups' => [
			'search' 		=> 'groups.search?',
			'getMembers'	=> 'groups.getMembers?'

		]

	];

	/** @var array  */
	private $fieldsUser = [
		'photo_200', 'sex', 'bdate', 'city', 'country',
		'mobile_phone', 'home_phone', 'skype',
		'facebook', 'facebook_name', 'twitter',
		'site', 'followers_count'
	];

	/** @var array  */
	private $fieldsUserGruop = [
		'sex', 'bdate', 'city', 'country',
		'photo_50', 'photo_100', 'photo_200_orig',
		'photo_200', 'photo_400_orig', 'photo_max',
		'photo_max_orig', 'online', 'online_mobile',
		'lists', 'domain', 'has_mobile', 'contacts',
		'connections', 'site', 'education',
		'universities', 'schools', 'can_post',
		'can_see_all_posts', 'can_see_audio',
		'can_write_private_message', 'status',
		'last_seen', 'common_count', 'relation',
		'relatives'
	];

	/** @var array  */
	private $config;

	/** @var array  */
	private $memberId;

	/**
	 * VkApi constructor.
	 *
	 * @param array $memberId
	 * @param array $config
	 */
	function __construct ( $memberId, array $config ) {
		$this->config = $config;
		$this->memberId = $memberId;

	}

	/**
	 * @param $count
	 *
	 * @return int
	 */
	private function checkCount ( $count ) {
		return ( (int)$count >= 0 AND (int)$count < 1001 ) ? (int)$count : 1000;

	}

	/**
	 * @param $offset
	 *
	 * @return int
	 */
	private function checkOffset ( $offset ) {
		return ( (int)$offset > 0 ) ? (int)$offset : 0;

	}

	/**
	 * @param string $v
	 * @param string $url
	 * @param array  $params_count
	 *
	 * @return array
	 */
	private function getApi ( $v, $url, array $params_count ) {
		$params_count['v'] 				= $v;
		$params_count['access_token'] 	= $this->memberId[ 'user_vk_token' ];

		$params_count =  urldecode( http_build_query( $params_count ) );

//		var_dump( $this->vkApiUrl['method'] . $url . $params_count );
//		die( var_dump( json_decode( file_get_contents( $this->vkApiUrl['method'] . $url . $params_count ) ) ) );

		return json_decode( file_get_contents( $this->vkApiUrl['method'] . $url . $params_count ), true );

	}

	/**
	 * @param int $need_all
	 * @param int $count
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getApiCountries ( $need_all = 1, $count = 1000, $offset = 0 ) {
		$params_count = [];

		$params_count['need_all'] 	= $need_all;

		$params_count['count'] = $this->checkCount( $count );
		$params_count['offset'] = $this->checkOffset( $offset );

		return $this->getApi( $this->config[ 'vk_app_version58' ], $this->vkApiUrl['database']['getCountries'], $params_count );

	}

	/**
	 * @param int $id_country
	 * @param int $count
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getApiRegions ( $id_country, $count = 1000, $offset = 0 ) {
		$params_count = [];

		$params_count['country_id'] = $id_country;

		$params_count['count'] = $this->checkCount( $count );
		$params_count['offset'] = $this->checkOffset( $offset );

		return $this->getApi( $this->config[ 'vk_app_version58' ], $this->vkApiUrl['database']['getRegions'], $params_count );

	}

	/**
	 * @param int $id_region
	 * @param int $id_country
	 * @param int $count
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getApiCities ( $id_region, $id_country, $count = 1000, $offset = 0 ) {
		$params_count = [];

		$params_count['region_id'] 	= ( $id_region < 1 OR $id_region === NULL ) ? 0 : $id_region;
		$params_count['country_id'] = $id_country;

		$params_count['need_all'] = 1;
		if ( $id_region < 1 OR $id_region !== NULL ) {
			$params_count['need_all'] = 0;

		}

		$params_count['count'] = $this->checkCount( $count );
		$params_count['offset'] = $this->checkOffset( $offset );

		return $this->getApi( $this->config[ 'vk_app_version58' ], $this->vkApiUrl['database']['getCities'], $params_count );

	}

	/**
	 * @param int    $sort
	 * @param int    $count
	 * @param int    $offset
	 * @param string $q
	 * @param int    $city
	 * @param int    $country
	 * @param int    $sex
	 * @param int    $status
	 * @param array  $age
	 * @param array  $birth
	 * @param int    $online
	 * @param int    $photo
	 *
	 * @return array
	 */
	public function getApiUsersSearch (
		$sort 		= 1,
		$count 		= 1000,
		$offset 	= 0,
		$q 			= '',
		$city 		= 0,
		$country 	= 0,
		$sex 		= 0,
		$status 	= 0,
		$age 		= [],
		$birth 		= [],
		$online 	= 1,
		$photo 		= 1
	) {
		$params_count = [];

		if ( $sort !== false AND (int)$sort > 0 ) {
			$params_count[ 'sort' ] = (int)$sort;

		}

		$params_count['count'] = $this->checkCount( $count );
		$params_count['offset'] = $this->checkOffset( $offset );

		$params_count['fields'] = $this->fieldsUser;

		if ( $q !== false AND trim( $q ) != '' ) {
			$params_count['q'] = str_replace( ' ', '%20', $q );

		}

		if ( $city !== false AND (int)$city > 0 ) {
			$params_count['city'] = (int)$city;

		}

		if ( $country !== false AND (int)$country > 0 ) {
			$params_count['country'] = (int)$country;

		}

		if ( $sex !== false AND (int)$sex > 0 ) {
			$params_count['sex'] = (int)$sex;

		}

		if ( $status !== false AND (int)$status > 0 ) {
			$params_count['status'] = (int)$status;

		}

		if ( $age !== false ) {
			if ( (int)$age[0] > 0 ) {
				$params_count['age_from'] = (int)$age[0];

			}

			if ( (int)$age[1] > 0 ) {
				$params_count['age_to'] = (int)$age[1];

			}

		}

		if ( $birth !== false ) {
			if ( (int)$birth[0] > 0 ) {
				$params_count['birth_year'] = (int)$birth[0];

			}

			if ( (int)$birth[1] > 0 ) {
				$params_count['birth_month'] = (int)$birth[1];

			}

			if ( (int)$birth[2] > 0 ) {
				$params_count['birth_day'] = (int)$birth[2];

			}

		}

		if ( $online !== false AND (int)$online > 0 ) {
			$params_count['online'] = (int)$online;

		}

		if ( $photo !== false AND $photo > 0 ) {
			$params_count['has_photo'] 	= $photo;

		}

		return $this->getApi( $this->config[ 'vk_app_version58' ], $this->vkApiUrl['users']['search'], $params_count );

	}

	/**
	 * @param string $q
	 * @param string $type
	 * @param int    $country_id
	 * @param int    $city_id
	 * @param int    $sort
	 * @param int    $count
	 * @param int    $offset
	 *
	 * @return array|bool
	 */
	public function getApiGroupsSearch (
		$q 			= '',
		$type 		= '',
		$country_id = 0,
		$city_id 	= 0,
		$sort 		= 0,
		$count 		= 0,
		$offset 	= 0
	) {
		$params_count = [];

		if ( $q !== false AND trim( $q ) !== '' ) {
			$params_count[ 'q' ] = str_replace( ' ', '%20', $q );

			if ( trim( $type ) !== false AND trim( $type ) !== '' AND in_array( trim( $type ), [ 'group', 'page', 'event' ] ) ) {
				$params_count[ 'type' ] = trim( $type );

			}

			if ( $country_id !== false AND (int)$country_id > 0 ) {
				$params_count[ 'country_id' ] = (int)$country_id;

			}

			if ( $city_id !== false AND (int)$city_id > 0 ) {
				$params_count[ 'city_id' ] = (int)$city_id;

			}

			if ( in_array ( (int)$sort, [ 0, 1, 2, 3, 4, 5 ] ) ) {
				$params_count[ 'sort' ] = (int)$sort;

			}

			$params_count[ 'count' ] = $this->checkCount( $count );
			$params_count[ 'offset' ] = $this->checkOffset( $offset );

			return $this->getApi( $this->config[ 'vk_app_version56' ], $this->vkApiUrl['groups']['search'], $params_count );

		}

		return false;

	}


	public function getApiGroupsMembersUserId ( $group_id = 0, $count = 0, $offset = 0 ) {
		$params_count = [];

		if ( (int)$group_id > 0 ) {
			$params_count[ 'group_id' ] = (int)$group_id;
			$params_count[ 'sort' ] = 'id_asc';

			$params_count[ 'count' ] = $this->checkCount( $count );
			$params_count[ 'offset' ] = $this->checkOffset( $offset );

			return $this->getApi ( $this->config[ 'vk_app_version56' ], $this->vkApiUrl[ 'groups' ][ 'getMembers' ], $params_count );

		}
		return false;

	}

	public function getApiUsers( $user_ids = '' ) {
		$params_count = [];

		if ( trim( $user_ids ) != '' ) {
			$params_count[ 'user_ids' ] = $user_ids;
			$params_count[ 'fields' ] = $this->fieldsUser;

			return $this->getApi ( $this->config[ 'vk_app_version58' ], $this->vkApiUrl[ 'users' ][ 'get' ], $params_count );

		}
		return false;

	}

}
