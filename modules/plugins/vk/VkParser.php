<?php

namespace Modules\Plugins\Vk;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Plugins\MsgBox\MsgBox;
use Modules\Template\Template;

class VkParser {

	/** @var string  */
	private $isLogged;

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

	/** @var MsgBox  */
	private $msgBox;

	/** @var array  */
	private $config;

	/** @var array  */
	private $language;

	/**
	 * VkParser constructor.
	 *
	 * @param string    $isLogged
	 * @param array     $memberId
	 * @param array     $groupVar
	 * @param Functions $functions
	 * @param Db        $db
	 * @param Template  $tpl
	 * @param MsgBox    $msgBox
	 * @param array     $config
	 * @param array     $language
	 */
	public function __construct ( $isLogged, array $memberId, array $groupVar, Functions $functions, Db $db, Template $tpl, MsgBox $msgBox, array $config, array $language ) {

		$this->isLogged = $isLogged;
		$this->memberId = $memberId;

		$this->groupVar = $groupVar;
		$this->functions = $functions;

		$this->db = $db;
		$this->tpl = $tpl;

		$this->msgBox = $msgBox;

		$this->config = $config;
		$this->language = $language;

		if ( $_POST['action'] == 'vk_group_parser' ) {
			switch ( $_POST['step'] ) {
				case 'add':
					$this->addGroupParser();
					break;
			}

		}

	}

	private function addGroupParser () {
		$status = true;
		$name = $_POST['data']['name'];
		$id_list = $_POST['data']['id_list'];

		$name = trim( $this->db->safeSql( $name ) );
		if ( $name == '' ) {
			$status = false;

		}

		if ( $status === true ) {
			$id_list = explode( ',', $id_list );

			foreach ( $id_list AS $key => $val ) {
				$val = trim( $val );

				if ( ! preg_match( '|^[\d]+$|', $val ) ) {
					unset( $id_list[$key] );

				}

			}

		}

		$date = date( 'Y-m-d H:i:s', time() );

		foreach ( $id_list AS $id ) {
			$this->db->query( "INSERT INTO 
											parser_groups
												(
													`parser_user_id`,
													`parser_name`,
													`parser_group_id`,
													`parser_add_date`,
													`parser_status`
												)
													VALUES 
														(
															'{$this->memberId['user_id']}',
															'{$name}',
															'{$id}',
															'{$date}',
															'1'
														)" );

		}



	}

}