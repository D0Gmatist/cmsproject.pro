<?php

namespace Modules\Plugins\Vk;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

// street count 8973568

/**
 * Class VkGeo
 * @package Modules\Plugins\Vk
 */

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

	/** @var  VkApi */
	private $vkApi;

	/** @var int  */
	private $id_country = 0;

	/** @var int  */
	private $id_region = 0;

	/** @var array  */
	private $result;

	/** @var int  */
	public $offset = 0;

	/** @var int  */
	public $count_step = 1000;

	/**
	 * VkGeo constructor.
	 *
	 * @param bool      $isLogged
	 * @param array     $memberId
	 * @param array     $groupVar
	 * @param Functions $functions
	 * @param Db        $db
	 * @param Template  $tpl
	 * @param array     $config
	 * @param array     $language
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

		/** @var VkApi $vkApi */
		$this->vkApi = new VkApi( $this->memberId, $this->config );

		if ( trim( $_GET['action'] ) != '' ) {
			switch ( trim( $_GET['action'] ) ) {

				case 'countries' :
					$this->setCountries( 1 );
					break;

				case 'regions' :
					$this->id_country = (int)$_GET['id_country'];
					$this->id_region = 0;

					$this->setRegions( 1 );
					$this->setCities( 1 );
					break;

				case 'cities' :
					$this->id_country = (int)$_GET['id_country'];
					$this->id_region = ( $_GET['id_region'] == 'not' ) ? NULL : (int)$_GET['id_region'];
					$this->setCities( 1 );
					break;

			}
		}

	}

	/**
	 * @param bool $upStepOne
	 */
	private function updateCountries ( $upStepOne ) {
		if ( $upStepOne == true ) {
			$this->db->query( "DELETE FROM geo_countries WHERE `id_country` != ''" );

		}

		foreach ( $this->result['countries']['items'] AS $value ) {

			$value['title'] = $this->db->safeSql( stripslashes( $value['title'] ) );

			$this->db->query( "INSERT INTO
									geo_countries
										( `id_country`, `title_country` )
											VALUES
										( '{$value['id']}', '{$value['title']}' )" );

		}

		if ( count( $this->result['countries'] ) < $this->count_step ) {
			$this->offset = 0;
			$this->setCountries( 2 );

		} else {
			$this->offset = $this->offset + $this->count_step;
			$this->getCountries( false );

		}

	}

	/**
	 * @param bool $upStepOne
	 */
	private function getCountries ( $upStepOne ) {
		$vk_get = $this->vkApi->getApiCountries( 1, $this->count_step, $this->offset );

		if ( is_array( $vk_get[ 'response' ] ) AND count( $vk_get[ 'response' ] ) > 0 ) {
			$this->result['countries'] = $vk_get[ 'response' ];
			$this->updateCountries( $upStepOne );


		}

	}

	/**
	 * @param int $step
	 */
	private function setCountries ( $step ) {
		$this->result['countries'] = [];

		//$this->db->query( "SELECT * FROM geo_countries ORDER BY `important_country` DESC, `title_country` ASC" );
		$this->db->query( "SELECT * FROM geo_countries WHERE `important_country` = '1' ORDER BY `title_country` ASC" );

		while ( $row = $this->db->getRow() ) {
			$this->result['countries'][] = $row;

		}

		if ( count( $this->result['countries'] ) < 1 AND $step == 1 ) {
			$this->getCountries( true );

		}

	}

	/**
	 * @param bool $upStepOne
	 */
	private function updateRegions ( $upStepOne ) {
		if ( $upStepOne == true ) {
			$this->db->query( "DELETE FROM geo_regions WHERE `id_country` = '{$this->id_country}'" );

		}

		foreach ( $this->result['regions']['items'] AS $value ) {

			$value['title'] = $this->db->safeSql( stripslashes( $value['title'] ) );

			$this->db->query( "INSERT INTO
									geo_regions
										( `id_country`, `id_region`, `title_region` )
											VALUES
										( '{$this->id_country}', '{$value['id']}', '{$value['title']}' )" );

		}

		if ( count( $this->result['regions'] ) < $this->count_step ) {
			$this->offset = 0;
			$this->setRegions( 2 );

		} else {
			$this->offset = $this->offset + $this->count_step;
			$this->getRegions( false );

		}


	}

	private function updateNullRegions () {
		$this->db->query( "DELETE FROM geo_regions WHERE `id_country` = '{$this->id_country}'" );

		$this->db->query( "INSERT INTO
									geo_regions
										( `id_country`, `id_region`, `title_region` )
											VALUES
										( '{$this->id_country}', NULL, 'Без региона' )" );

		$this->offset = 0;
		$this->setRegions( 2 );

	}

	/**
	 * @param bool $upStepOne
	 */
	private function getRegions ( $upStepOne ) {
		$vk_get = $this->vkApi->getApiRegions( $this->id_country, $this->count_step, $this->offset );

		if ( is_array( $vk_get[ 'response' ] ) AND count( $vk_get[ 'response' ] ) > 0 ) {
			$this->result['regions'] = $vk_get[ 'response' ];
			$this->updateRegions( $upStepOne );

		} else {
			$this->updateNullRegions();

		}

	}

	/**
	 * @param int $step
	 */
	private function setRegions ( $step ) {
		$this->result['regions'] = [];

		$row = $this->db->superQuery( "SELECT `id_country` FROM geo_countries WHERE `id_country` = '{$this->id_country}' LIMIT 1" );

		if ( $this->id_country > 0 AND $this->id_country == $row['id_country'] ) {

			$this->db->query( "SELECT * FROM geo_regions WHERE `id_country` = '{$this->id_country}' ORDER BY `title_region` ASC" );

			while ( $row = $this->db->getRow () ) {
				$row['id_region'] = ( $row['id_region'] === NULL ) ? 'not' : $row['id_region'];

				$this->result['regions'][] = $row;

			}

			if ( count( $this->result['regions'] ) < 1 AND $step == 1 ) {
				$this->getRegions( true );

			}

		}

	}

	/**
	 * @param bool $upStepOne
	 */
	private function updateCities ( $upStepOne ) {
		if ( $upStepOne == true ) {
			$query = "DELETE FROM geo_cities WHERE `id_country` = '{$this->id_country}' AND `id_region` ";
			$query .= ( $this->id_region === NULL ) ? "IS NULL" : "= '{$this->id_region}'";
			$this->db->query( $query );

		}

		foreach ( $this->result['cities']['items'] AS $value ) {
			$value['title'] = $this->db->safeSql( stripslashes( $value['title'] ) );
			$value['area'] = $this->db->safeSql( stripslashes( $value['area'] ) );

			$value['important'] = ( (int)$value['important'] == 1 ) ? 1 : 0;

			if ( $this->id_region === NULL ) {
				$this->db->query( "INSERT INTO
									geo_cities
										( `id_country`, `id_region`, `id_city`, `title_city`, `area_city`, `important_city` )
											VALUES
										( '{$this->id_country}', NULL, '{$value['id']}', '{$value['title']}', '{$value['area']}', '{$value['important']}' )" );

			} else {
				$this->db->query( "INSERT INTO
									geo_cities
										( `id_country`, `id_region`, `id_city`, `title_city`, `area_city`, `important_city` )
											VALUES
										( '{$this->id_country}', '{$this->id_region}', '{$value['id']}', '{$value['title']}', '{$value['area']}', '{$value['important']}' )" );

			}

		}

		if ( count( $this->result['cities'] ) < $this->count_step ) {
			$this->offset = 0;
			$this->setCities( 2 );

		} else {
			$this->offset = $this->offset + $this->count_step;
			$this->getCities( false );

		}

	}

	/**
	 * @param bool $upStepOne
	 */
	private function getCities ( $upStepOne ) {
		$vk_get = $this->vkApi->getApiCities( $this->id_region, $this->id_country, $this->count_step, $this->offset );
		if ( is_array( $vk_get[ 'response' ] ) AND count( $vk_get[ 'response' ] ) > 0 ) {
			$this->result['cities'] = $vk_get[ 'response' ];
			$this->updateCities( $upStepOne );

		}

	}

	/**
	 * @param int $step
	 */
	private function setCities ( $step ) {
		$this->result['cities'] = [];

		$superQuery = "SELECT `id_country` FROM geo_regions WHERE `id_country` = '{$this->id_country}' AND `id_region` ";
		$superQuery .= ( $this->id_region === NULL ) ? "IS NULL" : "= '{$this->id_region}'";
		$superQuery .= " LIMIT 1";
		$row = $this->db->superQuery( $superQuery );

		$cityStatus = false;
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
			$this->getCities( true );

		}

	}

	/**
	 * @return array
	 */
	public function returnResult () {
		return $this->result;

	}

}