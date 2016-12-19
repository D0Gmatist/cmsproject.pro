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
	private $config;

	/** @var array  */
	private $memberId;

	/**
	 * VkApi constructor.
	 *
	 * @param array $memberId
	 * @param array $config
	 */
	function __construct ( $memberId, $config ) {
		$this->config = $config;
		$this->memberId = $memberId;

	}

	/**
	 * @param string $url
	 * @param array  $params_count
	 *
	 * @return array
	 */
	private function getApi ( $url, $params_count ) {
		$params_count['v'] 				= $this->memberId[ 'vk_app_version' ];
		$params_count['access_token'] 	= $this->config[ 'user_vk_token' ];

		$params_count =  urldecode( http_build_query( $params_count ) );

		return json_decode( file_get_contents( $url . $params_count ), true );


	}

	/**
	 * @param int $need_all
	 * @param int $count_step
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getApiCountries ( $need_all = 1, $count_step = 1000, $offset = 0 ) {
		$url = 'https://api.vk.com/method/database.getCountries?';

		$params_count = [];

		$params_count['need_all'] 	= $need_all;
		$params_count['count'] 		= $count_step;
		$params_count['offset'] 	= $offset;

		return $this->getApi( $url, $params_count );

	}

	/**
	 * @param int $id_country
	 * @param int $count_step
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getApiRegions ( $id_country, $count_step = 1000, $offset = 0 ) {
		$url = 'https://api.vk.com/method/database.getRegions?';

		$params_count = [];

		$params_count['country_id'] = $id_country;
		$params_count['count'] 		= $count_step;
		$params_count['offset'] 	= $offset;

		return $this->getApi( $url, $params_count );

	}

	/**
	 * @param int $id_region
	 * @param int $id_country
	 * @param int $count_step
	 * @param int $offset
	 *
	 * @return array
	 */
	public function getApiCities ( $id_region, $id_country, $count_step = 1000, $offset = 0 ) {
		$url = 'https://api.vk.com/method/database.getCities?';

		$params_count = [];

		$params_count['region_id'] 	= ( $id_region < 1 OR $id_region === NULL ) ? 0 : $id_region;
		$params_count['country_id'] = $id_country;

		$params_count['need_all'] = 1;
		if ( $id_region < 1 OR $id_region !== NULL ) {
			$params_count['need_all'] = 0;

		}

		$params_count['count'] 	= $count_step;
		$params_count['offset'] = $offset;

		return $this->getApi( $url, $params_count );

	}


	public function getApiUsers (
		$sort = 1,
		$count_step = 1000,
		$offset = 0,
		$city = false,
		$country = false,
		$status = false,
		$age = false,
		$birth = false,
		$online = 1,
		$photo = 1
	) {
		$url = 'https://api.vk.com/method/database.getCities?';

		$params_count = [];

		$params_count['sort'] 		= $sort;
		$params_count['count'] 		= $count_step;
		$params_count['offset'] 	= $offset;
		$params_count['fields'] 	= $this->fields;

		if ( $city !== false ) {
			$params_count['city'] 		= $city;

		}

		if ( $country !== false ) {
			$params_count['country'] 	= $country;

		}

		if ( $status !== false ) {
			$params_count['status'] 	= $status;

		}

		if ( $age !== false ) {
			if ( (int)$age[0] > 0 ) {
				$params_count['age_from'] 	= $age[0];

			}

			if ( (int)$age[1] > 0 ) {
				$params_count['age_to'] 	= $age[1];

			}

		}

		if ( $birth !== false ) {
			if ( (int)$birth[0] > 0 ) {
				$params_count['birth_day'] 		= $birth[0];

			}

			if ( (int)$birth[1] > 0 ) {
				$params_count['birth_month'] 	= $birth[1];

			}

			if ( (int)$birth[2] > 0 ) {
				$params_count['birth_year'] 	= $birth[2];

			}

		}

		if ( $online !== false ) {
			$params_count['online'] 	= $online;

		}

		if ( $photo !== false ) {
			$params_count['has_photo'] 	= $photo;

		}

		return $this->getApi( $url, $params_count );

	}

}
