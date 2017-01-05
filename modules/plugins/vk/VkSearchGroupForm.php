<?php

namespace Modules\Plugins\Vk;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class VkSearchGroupForm {

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

}