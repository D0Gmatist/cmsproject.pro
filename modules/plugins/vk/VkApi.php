<?php

namespace Modules\Plugins\Vk;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class VkApi {

	/** @var array  */
	private $config;

	/** @var array  */
	private $memberId;

	/**
	 * VkApi constructor.
	 *
	 * @param $memberId
	 * @param $config
	 */
	function __construct ( $memberId, $config ) {
		$this->config = $config;
		$this->memberId = $memberId;

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

		$params_count =  urldecode( http_build_query( [
			'need_all'     		=> $need_all,
			'count'     		=> $count_step,
			'offset'			=> $offset,
			'v'		 			=> $this->memberId[ 'vk_app_version' ],
			'access_token' 		=> $this->config[ 'user_vk_token' ]

		] ) );

		return json_decode( file_get_contents( $url . $params_count ), true );

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

		$params_count =  urldecode( http_build_query( [
			'country_id' 		=> $id_country,
			'count' 			=> $count_step,
			'offset'			=> $offset,
			'v'		 			=> $this->memberId[ 'vk_app_version' ],
			'access_token' 		=> $this->config[ 'user_vk_token' ]

		] ) );

		return json_decode( file_get_contents( $url . $params_count ), true );

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

		$need_all = 1;
		if ( $id_region < 1 OR $id_region !== NULL ) {
			$need_all = 0;

		}
		$id_region = ( $id_region < 1 OR $id_region === NULL ) ? 0 : $id_region;
		$params_count =  http_build_query( [
			'region_id' 		=> $id_region,
			'country_id' 		=> $id_country,
			'need_all' 			=> $need_all,
			'count' 			=> $count_step,
			'offset'			=> $offset,
			'v'		 			=> $this->memberId[ 'vk_app_version' ],
			'access_token' 		=> $this->config[ 'user_vk_token' ]

		] );

		return json_decode( file_get_contents( $url . $params_count ), true );

	}

}
