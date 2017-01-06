<?php

namespace Modules\Plugins\Vk;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class VkApi {

	/** @var array  */
	private $fields = [
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

	/** @var array  */
	private $fieldsGruop = [
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
		return ( (int)$count > 0 AND (int)$count < 1001 ) ? (int)$count : 1000;

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
	 * @param string $url
	 * @param array  $params_count
	 *
	 * @return array
	 */
	private function getApi ( $url, array $params_count ) {
		$params_count['v'] 				= $this->config[ 'vk_app_version' ];
		$params_count['access_token'] 	= $this->memberId[ 'user_vk_token' ];

		$params_count =  urldecode( http_build_query( $params_count ) );

		return json_decode( file_get_contents( $url . $params_count ), true );

	}

	/**
	 * @param int $need_all
	 * @param int $count
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getApiCountries ( $need_all = 1, $count = 1000, $offset = 0 ) {
		$url = 'https://api.vk.com/method/database.getCountries?';

		$params_count = [];

		$params_count['need_all'] 	= $need_all;

		$params_count['count'] = $this->checkCount( $count );
		$params_count['offset'] = $this->checkOffset( $offset );

		return $this->getApi( $url, $params_count );

	}

	/**
	 * @param int $id_country
	 * @param int $count
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getApiRegions ( $id_country, $count = 1000, $offset = 0 ) {
		$url = 'https://api.vk.com/method/database.getRegions?';

		$params_count = [];

		$params_count['country_id'] = $id_country;

		$params_count['count'] = $this->checkCount( $count );
		$params_count['offset'] = $this->checkOffset( $offset );

		return $this->getApi( $url, $params_count );

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
		$url = 'https://api.vk.com/method/database.getCities?';

		$params_count = [];

		$params_count['region_id'] 	= ( $id_region < 1 OR $id_region === NULL ) ? 0 : $id_region;
		$params_count['country_id'] = $id_country;

		$params_count['need_all'] = 1;
		if ( $id_region < 1 OR $id_region !== NULL ) {
			$params_count['need_all'] = 0;

		}

		$params_count['count'] = $this->checkCount( $count );
		$params_count['offset'] = $this->checkOffset( $offset );

		return $this->getApi( $url, $params_count );

	}

	/**
	 * @param int   $sort
	 * @param int   $count
	 * @param int   $offset
	 * @param bool  $q
	 * @param bool  $city
	 * @param bool  $country
	 * @param bool  $sex
	 * @param bool  $status
	 * @param array $age
	 * @param array $birth
	 * @param int   $online
	 * @param int   $photo
	 *
	 * @return array
	 */
	public function getApiUsers (
		$sort 		= 1,
		$count 		= 1000,
		$offset 	= 0,
		$q 			= false,
		$city 		= false,
		$country 	= false,
		$sex 		= false,
		$status 	= false,
		$age 		= [],
		$birth 		= [],
		$online 	= 1,
		$photo 		= 1
	) {
		$url = 'https://api.vk.com/method/users.search?';

		$params_count = [];

		if ( $sort !== false AND (int)$sort > 0 ) {
			$params_count[ 'sort' ] = (int)$sort;

		}

		$params_count['count'] = $this->checkCount( $count );
		$params_count['offset'] = $this->checkOffset( $offset );

		$params_count['fields'] = $this->fields;

		if ( $q !== false AND trim( $q ) != '' ) {
			$params_count['q'] = $q;

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

		return $this->getApi( $url, $params_count );

	}

	/**
	 * @param bool $q
	 * @param bool $country_id
	 * @param int  $city_id
	 * @param int  $sort
	 * @param int  $offset
	 * @param int  $count
	 *
	 * @return array
	 */
	public function getApiGroupsSearch ( $q = false, $country_id = false, $city_id = 0, $sort = 0, $offset = 0, $count = 0 ) {
		$url = 'https://api.vk.com/method/groups.search?';

		$params_count = [];

		if ( $q !== false AND trim( $q ) != '' ) {
			$params_count['q'] = $q;

		}

		if ( $country_id !== false AND (int)$country_id > 0 ) {
			$params_count['country_id'] = (int)$country_id;

		}

		if ( $city_id !== false AND (int)$city_id > 0 ) {
			$params_count['city_id'] = (int)$city_id;

		}

		if ( in_array( (int)$sort, [ 0, 1, 2, 3, 4, 5 ] ) ) {
			$params_count['sort'] = (int)$sort;

		}

		$params_count['count'] = $this->checkCount( $count );
		$params_count['offset'] = $this->checkOffset( $offset );

		return $this->getApi( $url, $params_count );

	}

}
