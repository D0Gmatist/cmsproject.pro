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

	/** @var array  */
	private $config;

	/** @var array  */
	private $language;

	/** @var array  */
	private $result = [];

	/**
	 * VkParser constructor.
	 *
	 * @param string    $isLogged
	 * @param array     $memberId
	 * @param array     $groupVar
	 * @param Functions $functions
	 * @param Db        $db
	 * @param Template  $tpl
	 * @param array     $config
	 * @param array     $language
	 */
	public function __construct ( $isLogged, array $memberId, array $groupVar, Functions $functions, Db $db, Template $tpl, array $config, array $language ) {

		$this->isLogged = $isLogged;
		$this->memberId = $memberId;

		$this->groupVar = $groupVar;
		$this->functions = $functions;

		$this->db = $db;
		$this->tpl = $tpl;

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
			$this->result = [
				'content'	=> '',
				'msg' 		=> 'Отсутствует название задачи парсера',
				'status'	=> false
			];

		}

		if ( $status === true ) {
			$id_list = explode ( ',', $id_list );

			foreach ( $id_list AS $key => $val ) {
				$val = trim ( $val );

				if ( ! preg_match ( '|^[\d]+$|', $val ) ) {
					unset( $id_list[ $key ] );

				}

			}

			if ( count(  $id_list ) < 1 ) {
				$this->result = [
					'content'	=> '',
					'msg' 		=> 'Отсутствуют ID групп для парсера',
					'status'	=> false
				];

			}

		}

		if ( $status === true ) {
			$date = date( 'Y-m-d H:i:s', time() );

			$this->db->query( "INSERT INTO 
											parser 
												(
													`parser_title`, 
													`parser_user_id`, 
													`parser_sum_pay`, 
													`parser_atatus`, 
													`parser_date_add`, 
													`parser_date_result` 
												) 
													VALUES 
														(
															'{$name}', 
															'{$this->memberId['user_id']}', 
															'0.00', 
															'1', 
															'{$date}', 
															'{$date}' 
														)" );
			$parserId  = $this->db->insertId();

			foreach ( $id_list AS $id ) {
				$this->db->query( "INSERT INTO 
											parser_groups
												(
													`parser_g_parser_id`,
													`parser_g_user_id`,
													`parser_g_vk_group_id`,
													`parser_g_add_date`,
													`parser_g_status`
												)
													VALUES 
														(
															'{$parserId}',
															'{$this->memberId['user_id']}',
															'{$id}',
															'{$date}',
															'1'
														)" );

			}

			$this->result = [
				'content'	=> '',
				'msg' 		=> 'Задача успешно создана',
				'status'	=> false
			];

		}

	}

	/**
	 * @return array
	 */
	public function getResult () {
		return $this->result;

	}

}