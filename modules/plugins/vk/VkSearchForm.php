<?php

namespace Modules\Plugins\Vk;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class VkSearchForm {

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

	/** @var array  */
	private $result;

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

		$this->tpl->loadTemplate( 'vk_search_form.tpl' );

		$age_from 	= [ '<option></option>' ];
		$age_to 	= [ '<option></option>' ];

		for ( $i = 14; $i <= 80; $i++ ) {
			$age_from[] = '<option value="' . $i . '">От ' . $i . '</option>';
			$age_to[] = '<option value="' . $i . '">До ' . $i . '</option>';

		}

		$age_from 	= implode( '', $age_from );
		$age_to 	= implode( '', $age_to );

		$this->tpl->set( '{age_from}', $age_from );
		$this->tpl->set( '{age_to}', $age_to );


		$birth_year 	= [ '<option></option>' ];
		$birth_month 	= [ '<option></option>' ];
		$birth_day 	= [ '<option></option>' ];

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
		$data = $_GET['data'];

		$vk_get = $this->vkApi->getApiUsers(
			(int)$data['cort'],
			100,
			1,
			(string)$data['q'],
			(int)$data['cities'],
			(int)$data['countries'],
			(int)$data['sex'],
			(int)$data['status'],
			[ (int)$data['age_from'], (int)$data['age_to'] ],
			[ (int)$data['birth_year'], (int)$data['birth_month'], (int)$data['birth_day'] ],
			(int)$data['online'],
			(int)$data['has_photo']
		);

		if ( is_array( $vk_get[ 'response' ] ) AND count( $vk_get[ 'response' ] ) > 0 ) {
			$this->result = $vk_get[ 'response' ];

		}

	}

	/**
	 * @return array
	 */
	public function getResult () {

		foreach ( $this->result['items'] AS $row ) {
			$this->tpl->loadTemplate( 'vk_search_result.tpl' );

			$this->tpl->set( '{id}', $row['id'] );

			$this->tpl->set( '{first_name}', $row['first_name'] );
			$this->tpl->set( '{last_name}', $row['last_name'] );

			if ( trim( $row['nickname'] ) == '' ) {
				$this->tpl->set( '{nickname}', '' );

				$this->tpl->setBlock( "'\\[nickname\\](.*?)\\[/nickname\\]'si", "" );

			} else {
				$this->tpl->set( '{nickname}', $row['nickname'] );

				$this->tpl->set( '[nickname]', '' );
				$this->tpl->set( '[/nickname]', '' );

			}

			if ( trim( $row['photo_50'] ) == '' ) {
				$this->tpl->set( '{photo_50}', $this->config['http_home_url'] . 'templates/' . $this->config['skin'] . '/img/avatar.png' );

			} else {
				$this->tpl->set ( '{photo_50}', $row[ 'photo_50' ] );

			}

			if ( trim( $row['photo_100'] ) == '' ) {
				$this->tpl->set( '{photo_100}', $this->config['http_home_url'] . 'templates/' . $this->config['skin'] . '/img/avatar.png' );

			} else {
				$this->tpl->set ( '{photo_100}', $row[ 'photo_100' ] );

			}

			if ( trim( $row['photo_200'] ) == '' ) {
				$this->tpl->set( '{photo_200}', $this->config['http_home_url'] . 'templates/' . $this->config['skin'] . '/img/avatar.png' );

			} else {
				$this->tpl->set ( '{photo_200}', $row[ 'photo_200' ] );

			}

			if ( trim( $row[ 'city' ][ 'title' ] ) == '' ) {
				$this->tpl->set ( '{city}', '' );

				$this->tpl->setBlock( "'\\[city\\](.*?)\\[/city\\]'si", "" );

			} else {
				$this->tpl->set ( '{city}', $row[ 'city' ][ 'title' ] );

				$this->tpl->set( '[city]', '' );
				$this->tpl->set( '[/city]', '' );

			}

			if ( trim( $row[ 'country' ][ 'title' ] ) == '' ) {
				$this->tpl->set ( '{country}', '' );

				$this->tpl->setBlock( "'\\[country\\](.*?)\\[/country\\]'si", "" );

			} else {
				$this->tpl->set ( '{country}', $row[ 'country' ][ 'title' ] );

				$this->tpl->set( '[country]', '' );
				$this->tpl->set( '[/country]', '' );

			}

			if ( trim( $row['mobile_phone'] ) == '' ) {
				$this->tpl->set( '{mobile_phone}', '' );

				$this->tpl->setBlock( "'\\[mobile_phone\\](.*?)\\[/mobile_phone\\]'si", "" );

			} else {
				$this->tpl->set( '{mobile_phone}', $row['mobile_phone'] );

				$this->tpl->set( '[mobile_phone]', '' );
				$this->tpl->set( '[/mobile_phone]', '' );

			}

			if ( trim( $row['home_phone'] ) == '' ) {
				$this->tpl->set( '{home_phone}', '' );

				$this->tpl->setBlock( "'\\[home_phone\\](.*?)\\[/home_phone\\]'si", "" );

			} else {
				$this->tpl->set( '{home_phone}', $row['home_phone'] );

				$this->tpl->set( '[home_phone]', '' );
				$this->tpl->set( '[/home_phone]', '' );

			}

			if ( trim( $row['skype'] ) == '' ) {
				$this->tpl->set( '{skype}', '' );

				$this->tpl->setBlock( "'\\[skype\\](.*?)\\[/skype\\]'si", "" );

			} else {
				$this->tpl->set( '{skype}', $row['skype'] );

				$this->tpl->set( '[skype]', '' );
				$this->tpl->set( '[/skype]', '' );

			}

			if ( trim( $row['facebook'] ) == '' ) {
				$this->tpl->set( '{facebook}', '' );
				$this->tpl->set( '{facebook_name}', '' );

				$this->tpl->setBlock( "'\\[facebook\\](.*?)\\[/facebook\\]'si", "" );

			} else {
				$this->tpl->set( '{facebook}', $row['facebook'] );
				$this->tpl->set( '{facebook_name}', $row['facebook_name'] );

				$this->tpl->set( '[facebook]', '' );
				$this->tpl->set( '[/facebook]', '' );

			}

			if ( trim( $row['twitter'] ) == '' ) {
				$this->tpl->set( '{twitter}', '' );

				$this->tpl->setBlock( "'\\[twitter\\](.*?)\\[/twitter\\]'si", "" );

			} else {
				$this->tpl->set( '{twitter}', $row['twitter'] );

				$this->tpl->set( '[twitter]', '' );
				$this->tpl->set( '[/twitter]', '' );

			}

			if ( trim( $row['instagram'] ) == '' ) {
				$this->tpl->set( '{instagram}', '' );

				$this->tpl->setBlock( "'\\[instagram\\](.*?)\\[/instagram\\]'si", "" );

			} else {
				$this->tpl->set( '{instagram}', $row['instagram'] );

				$this->tpl->set( '[instagram]', '' );
				$this->tpl->set( '[/instagram]', '' );

			}

			$this->tpl->compile( 'vk_search_result' );

		}

		return $this->tpl->result[ 'vk_search_result' ];

	}

}