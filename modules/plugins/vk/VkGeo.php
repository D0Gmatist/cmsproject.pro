<?php

namespace Modules\Plugins\Vk;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class VkGeo {

	/** @var bool  */
	private $isLogged = false;

	/** @var array  */
	private $memberId;

	/** @var array  */
	private $groupVar;

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

	/** @var int  */
	private $id_country = 0;

	/** @var int  */
	private $id_region = 0;

	/** @var array  */
	private $result = [];

	/** @var int array  */
	public $offset = 0;

	/** @var int  */
	public $count_step = 1000;

	/**
	 * VkCountries constructor.
	 * @param $isLogged
	 * @param array $memberId
	 * @param array $groupVar
	 * @param Functions $functions
	 * @param Db $db
	 * @param Template $tpl
	 * @param array $config
	 * @param array $language
	 */
	function __construct ( $isLogged, array $memberId, array $groupVar, Functions $functions, Db $db, Template $tpl, array $config, array $language ) {
		$this->isLogged = $isLogged;
		$this->memberId = $memberId;
		$this->groupVar = $groupVar;

		$this->functions = $functions;
		$this->db = $db;

		$this->config = $config;
		$this->language = $language;

		$this->tpl = $tpl;

		if ( trim( $_GET['action'] ) != '' ) {
			switch ( trim( $_GET['action'] ) ) {

				case 'countries' :
					$this->getCountries( 1 );
					break;

				case 'regions' :
					$this->id_country = (int)$_GET['id_country'];
					$this->id_region = 0;
					$this->getRegions( 1 );
					$this->getCities( 1 );
					break;

				case 'cities' :
					$this->id_country = (int)$_GET['id_country'];
					$this->id_region = ( $_GET['id_region'] == 'not' ) ? NULL : (int)$_GET['id_region'];
					$this->getCities( 1 );
					break;

			}
		}

	}

	/**
	 * @param $upStepOne
	 */
	private function updateCountries( $upStepOne ) {
		if ( $upStepOne == true ) {
			$this->db->query( "DELETE FROM geo_countries WHERE `id_country` != ''" );

		}

		foreach ( $this->result['countries'] AS $value ) {

			$value['title'] = $this->db->safeSql( stripslashes( $value['title'] ) );

			$this->db->query( "INSERT INTO
									geo_countries
										( `id_country`, `title_country` )
											VALUES
										( '{$value['cid']}', '{$value['title']}' )" );

		}

		if ( count( $this->result['countries'] ) < $this->count_step ) {
			$this->offset = 0;
			$this->getCountries( 2 );

		} else {
			$this->offset = $this->offset + $this->count_step;
			$this->setCountries( false );

		}

	}

	/**
	 * @param $upStepOne
	 */
	private function setCountries ( $upStepOne ) {
		$url = 'https://api.vk.com/method/database.getCountries?';

		$params_count =  urldecode( http_build_query( [
			'need_all'     		=> '1',
			'count'     		=> $this->count_step,
			'offset'			=> $this->offset,
			'v'		 			=> $this->memberId[ 'vk_app_version' ],
			'access_token' 		=> $this->config[ 'user_vk_token' ]

		] ) );

		$vk_get = json_decode( file_get_contents( $url . $params_count ), true );

		if ( is_array( $vk_get[ 'response' ] ) AND count( $vk_get[ 'response' ] ) > 0 ) {
			$this->result['countries'] = $vk_get[ 'response' ];
			$this->updateCountries( $upStepOne );


		}

	}

	/**
	 * @param $step
	 */
	private function getCountries ( $step ) {
		$this->result['countries'] = [];

		$this->db->query( "SELECT * FROM geo_countries ORDER BY `title_country` ASC" );

		while ( $row = $this->db->getRow() ) {
			$this->result['countries'][] = $row;

		}

		if ( count( $this->result['countries'] ) < 1 AND $step == 1 ) {
			$this->setCountries( true );

		}

	}

	/**
	 * @param $upStepOne
	 */
	private function updateRegions( $upStepOne ) {
		if ( $upStepOne == true ) {
			$this->db->query( "DELETE FROM geo_regions WHERE `id_country` = '{$this->id_country}'" );

		}

		foreach ( $this->result['regions'] AS $value ) {

			$value['title'] = $this->db->safeSql( stripslashes( $value['title'] ) );

			$this->db->query( "INSERT INTO
									geo_regions
										( `id_country`, `id_region`, `title_region` )
											VALUES
										( '{$this->id_country}', '{$value['region_id']}', '{$value['title']}' )" );

		}

		if ( count( $this->result['regions'] ) < $this->count_step ) {
			$this->offset = 0;
			$this->getRegions( 2 );

		} else {
			$this->offset = $this->offset + $this->count_step;
			$this->setRegions( false );

		}


	}

	private function updateNullRegions() {
		$this->db->query( "DELETE FROM geo_regions WHERE `id_country` = '{$this->id_country}'" );

		$this->db->query( "INSERT INTO
									geo_regions
										( `id_country`, `id_region`, `title_region` )
											VALUES
										( '{$this->id_country}', NULL, 'Без региона' )" );

		$this->offset = 0;
		$this->getRegions( 2 );

	}

	/**
	 * @param $upStepOne
	 */
	private function setRegions( $upStepOne ) {
		$url = 'https://api.vk.com/method/database.getRegions?';

		$params_count =  urldecode( http_build_query( [
			'country_id' 		=> $this->id_country,
			'count' 			=> $this->count_step,
			'offset'			=> $this->offset,
			'v'		 			=> $this->memberId[ 'vk_app_version' ],
			'access_token' 		=> $this->config[ 'user_vk_token' ]

		] ) );

		$vk_get = json_decode( file_get_contents( $url . $params_count ), true );

		if ( is_array( $vk_get[ 'response' ] ) AND count( $vk_get[ 'response' ] ) > 0 ) {
			$this->result['regions'] = $vk_get[ 'response' ];
			$this->updateRegions( $upStepOne );

		} else {
			$this->updateNullRegions();

		}

	}

	/**
	 * @param $step
	 */
	private function getRegions ( $step ) {
		$this->result['regions'] = [];

		$row = $this->db->superQuery( "SELECT `id_country` FROM geo_countries WHERE `id_country` = '{$this->id_country}' LIMIT 1" );

		if ( $this->id_country > 0 AND $this->id_country == $row['id_country'] ) {

			$this->db->query ( "SELECT * FROM geo_regions WHERE `id_country` = '{$this->id_country}' ORDER BY `title_region` ASC" );

			while ( $row = $this->db->getRow () ) {
				$row['id_region'] = ( $row['id_region'] === NULL ) ? 'not' : $row['id_region'];

				$this->result['regions'][] = $row;

			}

			if ( count( $this->result['regions'] ) < 1 AND $step == 1 ) {
				$this->setRegions( true );

			}

		}

	}

	/**
	 * @param $upStepOne
	 */
	private function updateCities( $upStepOne ) {
		if ( $upStepOne == true ) {
			$query = "DELETE FROM geo_cities WHERE `id_country` = '{$this->id_country}' AND `id_region` ";
			$query .= ( $this->id_region === NULL ) ? "IS NULL" : "= '{$this->id_region}'";
			$this->db->query( $query );

		}

		foreach ( $this->result['cities'] AS $value ) {
			$value['title'] = $this->db->safeSql( stripslashes( $value['title'] ) );
			$value['area'] = $this->db->safeSql( stripslashes( $value['area'] ) );

			$value['important'] = ( (int)$value['important'] == 1 ) ? 1 : 0;

			if ( $this->id_region === NULL ) {
				$this->db->query( "INSERT INTO
									geo_cities
										( `id_country`, `id_region`, `id_city`, `title_city`, `area_city`, `important_city` )
											VALUES
										( '{$this->id_country}', NULL, '{$value['cid']}', '{$value['title']}', '{$value['area']}', '{$value['important']}' )" );

			} else {
				$this->db->query( "INSERT INTO
									geo_cities
										( `id_country`, `id_region`, `id_city`, `title_city`, `area_city`, `important_city` )
											VALUES
										( '{$this->id_country}', '{$this->id_region}', '{$value['cid']}', '{$value['title']}', '{$value['area']}', '{$value['important']}' )" );

			}

		}

		if ( count( $this->result['cities'] ) < $this->count_step ) {
			$this->offset = 0;
			$this->getCities( 2 );

		} else {
			$this->offset = $this->offset + $this->count_step;
			$this->setCities( false );

		}

	}

	/**
	 * @param $upStepOne
	 */
	private function setCities ( $upStepOne ) {
		$url = 'https://api.vk.com/method/database.getCities?';

		$need_all = 1;
		if ( $this->id_region < 1 OR $this->id_region !== NULL ) {
			$need_all = 0;

		}
		$id_region = ( $this->id_region < 1 OR $this->id_region === NULL ) ? 0 : $this->id_region;
		$params_count =  http_build_query( [
			'region_id' 		=> $id_region,
			'country_id' 		=> $this->id_country,
			'need_all' 			=> $need_all,
			'count' 			=> $this->count_step,
			'offset'			=> $this->offset,
			'v'		 			=> $this->memberId[ 'vk_app_version' ],
			'access_token' 		=> $this->config[ 'user_vk_token' ]

		] );

		$vk_get = json_decode( file_get_contents( $url . $params_count ), true );

		if ( is_array( $vk_get[ 'response' ] ) AND count( $vk_get[ 'response' ] ) > 0 ) {
			$this->result['cities'] = $vk_get[ 'response' ];
			$this->updateCities( $upStepOne );

		}

	}

	/**
	 * @param $step
	 */
	private function getCities ( $step ) {
		$this->result['cities'] = [];

		$superQuery = "SELECT `id_country` FROM geo_regions WHERE `id_country` = '{$this->id_country}' AND `id_region` ";
		$superQuery .= ( $this->id_region === NULL ) ? "IS NULL" : "= '{$this->id_region}'";
		$superQuery .= " LIMIT 1";
		$row = $this->db->superQuery( $superQuery );

		if ( $this->id_country == $row['id_country'] OR $_GET['action'] == 'regions' ) {
			$cityStatus = true;

			$query = "SELECT * FROM geo_cities WHERE `id_country` = '{$this->id_country}' AND `id_region` ";
			$query .= ( $this->id_region === NULL ) ? "IS NULL" : "= '{$this->id_region}'";
			$query .= " ORDER BY `important_city` DESC, `title_city` ASC";
			$this->db->query( $query );

		}

		if ( $cityStatus == true ) {
			while ( $row = $this->db->getRow () ) {
				$this->result['cities'][] = $row;

			}

		}

		if ( count( $this->result['cities'] ) < 1 AND $step == 1 ) {
			$this->setCities( true );

		}

	}

	/**
	 * @return array
	 */
	public function getResult () {
		return $this->result;

	}

}