<?php

namespace Modules\Plugins\UserPlugins;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class UserPanel {

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

	/** @var Template  */
	private $tpl;

	/** @var array  */
	private $config;

	/** @var array  */
	private $language;

	/** @var  int */
	private $_TIME;

	/**
	 * UserPanel constructor.
	 *
	 * @param           $isLogged
	 * @param array     $memberId
	 * @param array     $groupVar
	 * @param Functions $functions
	 * @param Db        $db
	 * @param Template  $tpl
	 * @param array     $config
	 * @param array     $language
	 * @param           $_TIME
	 *
	 * @internal param $action
	 */
	function __construct ( $isLogged, array $memberId, array $groupVar, Functions $functions, Db $db, Template $tpl, array $config, array $language, $_TIME ) {
		$this->isLogged = $isLogged;
		$this->memberId = $memberId;
		$this->groupVar = $groupVar;

		$this->functions = $functions;
		$this->db = $db;

		$this->tpl = $tpl;

		$this->config = $config;
		$this->language = $language;
		$this->_TIME = $_TIME;

		$this->getLoginPanel();

	}

	private function getLoginPanel () {
		$this->db->query( "SELECT 
								p.*, 
								count( pg.parser_g_parser_id ) 
									FROM 
										parser p 
											LEFT JOIN 
										parser_groups pg 
											ON ( p.parser_id = pg.parser_g_parser_id )
											WHERE 
												p.parser_user_id = '{$this->memberId['user_id']}' 
													AND 
												p.parser_type = '1' 
													AND 
												p.parser_status = '1'
													GROUP BY 
														p.parser_id
															ORDER BY 
																p.parser_date_add
																	DESC" );

		$parserActive = 0;
		while ( $row = $this->db->getRow () ) {
			$parserActive++;

			/**
			 * tpl panel
			 */
			$this->tpl->loadTemplate( 'user/parser_info.tpl' );

			$this->tpl->set( '{parser_id}', $row['parser_id'] );
			$this->tpl->set( '{parser_title}', $row['parser_title'] );

			$row['parser_date_add'] = strtotime( $row['parser_date_add'] );

			if( date( 'Ymd', $row['parser_date_add'] ) == date( 'Ymd', $this->_TIME ) ) {
				$this->tpl->set( '{date}', $this->language['time_heute'] . $this->functions->langDate( ", H:i", $row['parser_date_add'] ) );

			} else if ( date( 'Ymd', $row['parser_date_add'] ) == date( 'Ymd', ( $this->_TIME - 86400 ) ) ) {
				$this->tpl->set( '{date}', $this->language['time_gestern'] . $this->functions->langDate( ", H:i", $row['parser_date_add'] ) );

			} else {
				$this->tpl->set( '{date}', $this->functions->langDate( $this->config['timestamp_active'], $row['parser_date_add'] ) );

			}
			preg_match_all( "#\{date=(.+?)\}#i", $this->tpl->copy_template, $pregDate );
			$this->tpl->copy_template = str_replace( $pregDate[0][0], $this->functions->formatDate( $pregDate[1][0], $row['parser_date_add'] ), $this->tpl->copy_template );

			$this->tpl->compile( 'parser_info' );

		}

		/**
		 * tpl parser_info_block
		 */
		$this->tpl->loadTemplate( 'user/parser_info_block.tpl' );

		$this->tpl->set( '{parser_info}', $this->tpl->result[ 'parser_info' ] );

		$this->tpl->set( '{parser_active}', $parserActive );

		$this->tpl->compile( 'parser_info_block' );

		/**
		 * tpl panel
		 */
		$this->tpl->loadTemplate( 'user/panel.tpl' );

		if ( $this->isLogged ) {

			$this->tpl->set( '{parser_info_block}', $this->tpl->result[ 'parser_info_block' ] );

			$this->tpl->set( '{user_id}', $this->memberId['user_id'] );
			$this->tpl->set( '{user_login}', $this->memberId['user_login'] );
			$this->tpl->set( '{user_email}', $this->memberId['user_email'] );

			if ( $this->memberId['user_avatar'] != '' ) {
				$this->tpl->set( '{user_avatar}', $this->memberId['user_avatar'] );

			} else {
				$this->tpl->set( '{user_avatar}', $this->config['http_home_url'] . 'templates/' . $this->config['skin'] . '/img/avatar.png' );

			}

			$this->tpl->set( '{user_group}', $this->memberId['user_group'] );

		} else {

			$this->tpl->set( '{parser_info_block}', '' );

			$this->tpl->set( '{user_id}', '' );
			$this->tpl->set( '{user_login}', $this->language['userPanel'][1] );
			$this->tpl->set( '{user_email}', '' );
			$this->tpl->set( '{user_avatar}', $this->config['http_home_url'] . 'templates/' . $this->config['skin'] . '/img/avatar.png' );
			$this->tpl->set( '{user_group}', 5 );

		}

		$this->tpl->compile( 'user_panel' );

		$this->tpl->clear();

	}

}