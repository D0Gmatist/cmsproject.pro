<?php

namespace Modules\Plugins\Vk;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class VkSearchUserForm {

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

	/** @var array  */
	private $data;

	/** @var Template  */
	private $tpl;

	/** @var int  */
	private $stepCount = 1000;

	/** @var int  */
	private $offset = 0;

	/** @var array  */
	private $rows = [];

	/** @var array  */
	private $result = [];

	/**
	 * UserPanel constructor.
	 * @param $isLogged
	 * @param array $memberId
	 * @param array $groupVar
	 * @param Functions $functions
	 * @param Db $db
	 * @param Template $tpl
	 * @param array $config
	 * @param array $language
	 * @internal param $action
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
		$this->vkApi = new VkApi( $this->memberId = $memberId, $this->config );

		if ( $_POST['method'] == 'ajax' OR $_GET['method'] == 'ajax' ) {
			$this->searchForm();

		} else {
			$this->getForm();

		}

	}

	private function getForm ()	{
		$optObj = [ '<option></option>' ];

		$this->tpl->loadTemplate( 'vk_user_search_form.tpl' );

		$age_from 	= $optObj;
		$age_to 	= $optObj;

		for ( $i = 14; $i <= 80; $i++ ) {
			$age_from[] = '<option value="' . $i . '">От ' . $i . '</option>';
			$age_to[] = '<option value="' . $i . '">До ' . $i . '</option>';

		}

		$age_from 	= implode( '', $age_from );
		$age_to 	= implode( '', $age_to );

		$this->tpl->set( '{age_from}', $age_from );
		$this->tpl->set( '{age_to}', $age_to );

		$birth_year 	= $optObj;
		$birth_month 	= $optObj;
		$birth_day 		= $optObj;

		$time = time();
		$year = date( 'Y', $time );
		for ( $i = ( $year - 14 ); $i > ( $year - 114 ); $i-- ) {
			$birth_year[] = '<option value="' . $i . '">' . $i . '</option>';

		}

		for ( $i = 1; $i <= 12; $i++ ){
			$birth_month[] = '<option value="' . $i . '">' . $this->language['date']['month_' . $i] . '</option>';

		}

		$countDayToMonth = date( 't', $time );
		for ( $i = 1; $i <= $countDayToMonth; $i++ ){
			$birth_day[] = '<option value="' . $i . '">' . $i . '</option>';

		}

		$birth_year 	= implode( '', $birth_year );
		$birth_month 	= implode( '', $birth_month );
		$birth_day 		= implode( '', $birth_day );

		$this->tpl->set( '{birth_year}', $birth_year );
		$this->tpl->set( '{birth_month}', $birth_month );
		$this->tpl->set( '{birth_day}', $birth_day );

		$this->tpl->compile( 'content' );

		$this->tpl->clear();

	}

	private function searchForm () {
		$this->data = $_GET['data'];

		$vk_get = $this->vkApi->getApiUsersSearch(
			(int)$this->data['cort'],
			$this->stepCount,
			$this->offset,
			(string)$this->data['q'],
			(int)$this->data['cities'],
			(int)$this->data['countries'],
			(int)$this->data['sex'],
			(int)$this->data['status'],
			[
				(int)$this->data['age_from'],
				(int)$this->data['age_to']
			],
			[
				(int)$this->data['birth_year'],
				(int)$this->data['birth_month'],
				(int)$this->data['birth_day']
			],
			(int)$this->data['online'],
			(int)$this->data['has_photo']
		);

		if ( is_array( $vk_get[ 'response' ] ) AND count( $vk_get[ 'response' ] ) > 0 ) {
			$this->result = $vk_get[ 'response' ];
			$this->filtrationResult();

		}

	}

	private function filtrationResult() {

		foreach ( $this->result['items'] AS $row ) {
			if ( (int)$this->data[ 'mobile_phone' ] == 1 AND trim( $row['mobile_phone'] ) == '' ) {
				continue;

			}

			if ( (int)$this->data[ 'home_phone' ] == 1 AND trim( $row['home_phone'] ) == '' ) {
				continue;

			}

			if ( (int)$this->data[ 'skype' ] == 1 AND trim( $row['skype'] ) == '' ) {
				continue;

			}

			if ( (int)$this->data[ 'facebook' ] == 1 AND trim( $row['facebook'] ) == '' ) {
				continue;

			}

			if ( (int)$this->data[ 'twitter' ] == 1 AND trim( $row['twitter'] ) == '' ) {
				continue;

			}

			if ( (int)$this->data[ 'instagram' ] == 1 AND trim( $row['instagram'] ) == '' ) {
				continue;

			}
			$this->rows[] = $row;

		}

	}

	/**
	 * @return string
	 */
	public function getResult () {

		if ( count( $this->rows ) > 0 ) {
			foreach ( $this->rows AS $row ) {
				$this->tpl->loadTemplate ( 'vk_user_search_result.tpl' );

				$this->tpl->set ( '{id}', $row[ 'id' ] );

				$this->tpl->set ( '{first_name}', $row[ 'first_name' ] );
				$this->tpl->set ( '{last_name}', $row[ 'last_name' ] );

				if ( trim ( $row[ 'nickname' ] ) == '' ) {
					$this->tpl->set ( '{nickname}', '' );

					$this->tpl->setBlock ( "'\\[nickname\\](.*?)\\[/nickname\\]'si", "" );

				} else {
					$this->tpl->set ( '{nickname}', $row[ 'nickname' ] );

					$this->tpl->set ( '[nickname]', '' );
					$this->tpl->set ( '[/nickname]', '' );

				}

				if ( trim ( $row[ 'photo_50' ] ) == '' ) {
					$this->tpl->set ( '{photo_50}', $this->config[ 'http_home_url' ].'templates/'.$this->config[ 'skin' ].'/img/avatar_50.png' );

				} else {
					$this->tpl->set ( '{photo_50}', $row[ 'photo_50' ] );

				}

				if ( trim ( $row[ 'photo_100' ] ) == '' ) {
					$this->tpl->set ( '{photo_100}', $this->config[ 'http_home_url' ].'templates/'.$this->config[ 'skin' ].'/img/avatar_100.png' );

				} else {
					$this->tpl->set ( '{photo_100}', $row[ 'photo_100' ] );

				}

				if ( trim ( $row[ 'photo_200' ] ) == '' ) {
					$this->tpl->set ( '{photo_200}', $this->config[ 'http_home_url' ].'templates/'.$this->config[ 'skin' ].'/img/avatar_200.png' );

				} else {
					$this->tpl->set ( '{photo_200}', $row[ 'photo_200' ] );

				}

				if ( trim ( $row[ 'city' ][ 'title' ] ) == '' ) {
					$this->tpl->set ( '{city}', '' );

					$this->tpl->setBlock ( "'\\[city\\](.*?)\\[/city\\]'si", "" );

				} else {
					$this->tpl->set ( '{city}', $row[ 'city' ][ 'title' ] );

					$this->tpl->set ( '[city]', '' );
					$this->tpl->set ( '[/city]', '' );

				}

				if ( trim ( $row[ 'country' ][ 'title' ] ) == '' ) {
					$this->tpl->set ( '{country}', '' );

					$this->tpl->setBlock ( "'\\[country\\](.*?)\\[/country\\]'si", "" );

				} else {
					$this->tpl->set ( '{country}', $row[ 'country' ][ 'title' ] );

					$this->tpl->set ( '[country]', '' );
					$this->tpl->set ( '[/country]', '' );

				}

				if ( (int)$this->data[ 'mobile_phone' ] == 1 AND trim ( $row[ 'mobile_phone' ] ) != '' ) {
					$this->tpl->set ( '{mobile_phone}', $row[ 'mobile_phone' ] );

					$this->tpl->set ( '[mobile_phone]', '' );
					$this->tpl->set ( '[/mobile_phone]', '' );

				} else {
					$this->tpl->set ( '{mobile_phone}', '' );

					$this->tpl->setBlock ( "'\\[mobile_phone\\](.*?)\\[/mobile_phone\\]'si", "" );

				}

				if ( (int)$this->data[ 'home_phone' ] == 1 AND trim ( $row[ 'home_phone' ] ) != '' ) {
					$this->tpl->set ( '{home_phone}', $row[ 'home_phone' ] );

					$this->tpl->set ( '[home_phone]', '' );
					$this->tpl->set ( '[/home_phone]', '' );

				} else {
					$this->tpl->set ( '{home_phone}', '' );

					$this->tpl->setBlock ( "'\\[home_phone\\](.*?)\\[/home_phone\\]'si", "" );

				}

				if ( (int)$this->data[ 'skype' ] == 1 AND trim ( $row[ 'skype' ] ) != '' ) {
					$this->tpl->set ( '{skype}', $row[ 'skype' ] );

					$this->tpl->set ( '[skype]', '' );
					$this->tpl->set ( '[/skype]', '' );

				} else {
					$this->tpl->set ( '{skype}', '' );

					$this->tpl->setBlock ( "'\\[skype\\](.*?)\\[/skype\\]'si", "" );

				}

				if ( (int)$this->data[ 'facebook' ] == 1 AND trim ( $row[ 'facebook' ] ) != '' ) {
					$this->tpl->set ( '{facebook}', $row[ 'facebook' ] );
					$this->tpl->set ( '{facebook_name}', $row[ 'facebook_name' ] );

					$this->tpl->set ( '[facebook]', '' );
					$this->tpl->set ( '[/facebook]', '' );

				} else {
					$this->tpl->set ( '{facebook}', '' );
					$this->tpl->set ( '{facebook_name}', '' );

					$this->tpl->setBlock ( "'\\[facebook\\](.*?)\\[/facebook\\]'si", "" );

				}

				if ( (int)$this->data[ 'twitter' ] == 1 AND trim ( $row[ 'twitter' ] ) != '' ) {
					$this->tpl->set ( '{twitter}', $row[ 'twitter' ] );

					$this->tpl->set ( '[twitter]', '' );
					$this->tpl->set ( '[/twitter]', '' );

				} else {
					$this->tpl->set ( '{twitter}', '' );

					$this->tpl->setBlock ( "'\\[twitter\\](.*?)\\[/twitter\\]'si", "" );

				}

				if ( (int)$this->data[ 'twitter' ] == 1 AND trim ( $row[ 'instagram' ] ) != '' ) {
					$this->tpl->set ( '{instagram}', $row[ 'instagram' ] );

					$this->tpl->set ( '[instagram]', '' );
					$this->tpl->set ( '[/instagram]', '' );

				} else {
					$this->tpl->set ( '{instagram}', '' );

					$this->tpl->setBlock ( "'\\[instagram\\](.*?)\\[/instagram\\]'si", "" );

				}

				$this->tpl->compile ( 'vk_user_search_result' );

			}

			if ( $this->tpl->result[ 'vk_user_search_result' ] ) {
				$this->tpl->loadTemplate ( 'vk_user_search_result_block.tpl' );

				$this->tpl->set ( '{vk_user_search_result}', $this->tpl->result[ 'vk_user_search_result' ] );
				$this->tpl->set ( '{count}', count( $this->rows ) );

				$this->tpl->compile ( 'vk_user_search_result_block' );

				return $this->tpl->result[ 'vk_user_search_result_block' ];

			}

			return false;

		}

		return false;

	}

}