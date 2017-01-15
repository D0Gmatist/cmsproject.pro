<?php

namespace Modules\Plugins\Parser;


use Exception;
use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Plugins\Vk\VkApi;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

/**
 * Class ParserGroup
 * @package Modules\Plugins\Parser
 */

class ParserGroup {

	/** @var Functions  */
	private $functions;

	/** @var Db  */
	private $db;

	/** @var array  */
	private $config;

	/** @var array  */
	private $language;

	/** @var array  */
	private $memberId = [];

	/** @var int  */
	private $countStep = 1000;

	/**
	 * ParserGroup constructor.
	 *
	 * @param Functions $functions
	 * @param Db        $db
	 * @param array     $config
	 * @param array     $language
	 */
	public function __construct ( Functions $functions, Db $db, array $config, array $language ) {

		$this->functions = $functions;
		$this->db = $db;

		$this->config = $config;
		$this->language = $language;

		$this->vkApi = new VkApi( $this->memberId, $this->config );

		$this->foreachGroup();

		$this->db->close();

		die();

	}

	private function foreachGroup() {
		if ( $this->getParserGroupIdUser() === true ) {
			$this->foreachGroup();

		}

	}

	private function getParserGroupIdUser () {
		$this->memberId = $this->db->superQuery ( "SELECT * FROM users ORDER BY RAND() LIMIT 1" );

		$dateCheck = date( 'Y-m-d H:i:s', time() + 300 );

		$row = $this->db->superQuery ( "SELECT 
												* 
													FROM 
														parser_groups 
															WHERE 
																( `parser_g_count` > '0' AND `parser_g_count` > `parser_g_offset` ) 
																	AND 
																( `parser_g_lock` = '0' OR ( ( `parser_g_lock` = '1' AND `parser_g_up_date` <= '{$dateCheck}' ) OR ( `parser_g_lock` = '1' AND `parser_g_up_date` IS NULL ) ) )
																	ORDER BY 
																		`parser_g_add_date`
																			ASC,
																		`parser_g_id`
																			ASC
																				LIMIT 1" );

		if ( ! $row['parser_g_id'] ) {
			return false;

		}

		$dateUp = date( 'Y-m-d H:i:s', time() );

		$this->db->query ( "UPDATE parser_groups SET `parser_g_lock` = '1', `parser_g_up_date` = '{$dateUp}' WHERE `parser_g_id` = '{$row['parser_g_id']}'" );

		$result = $this->vkApi->getApiGroupsMembersUserId ( $row[ 'parser_g_vk_group_id' ], $this->countStep, $row[ 'parser_g_offset' ] );

		$this->db->query ( "SET autocommit = 0" );
		$this->db->query ( "START TRANSACTION" );

		if ( (int)$result[ 'response' ][ 'count' ] > 0 AND is_array ( $result[ 'response' ][ 'users' ] ) AND count ( $result[ 'response' ][ 'users' ] ) > 0 ) {
			$date = date ( 'Y-m-d H:i:s', time () );

			foreach ( $result[ 'response' ][ 'users' ] AS $idUser ) {
				$this->db->query ( "INSERT IGNORE INTO
														parser_users
															( 
																`parser_u_parser_id`, 
																`parser_u_user_id`, 
																`parser_u_vk_user_id`, 
																`parser_u_add_date` 
															)
																VALUES
																	( 
																		'{$row['parser_g_parser_id']}', 
																		'{$row['parser_g_user_id']}', 
																		'{$idUser}', 
																		'{$date}' 
																	)" );

				$this->db->query ( "UPDATE parser_groups SET parser_g_offset = parser_g_offset + 1 WHERE `parser_g_id` = '{$row['parser_g_id']}'" );

			}
			$this->db->query ( "UPDATE parser_groups SET `parser_g_lock` = '0' WHERE `parser_g_id` = '{$row['parser_g_id']}'" );

		}

		if (
			(int)$result[ 'response' ][ 'count' ] > 0
			AND
			is_array ( $result[ 'response' ][ 'users' ] )
			AND
			count ( $result[ 'response' ][ 'users' ] ) < $this->countStep
			AND
			$row['parser_g_offset'] > 0
			AND
			$row['parser_g_count'] > 0
		) {
			$this->db->query ( "UPDATE
										parser_groups 
											SET 
												`parser_g_lock` = '3',
												`parser_g_error_text` = 'Парсинг закончен'
													WHERE 
														`parser_g_id` = '{$row['parser_g_id']}'" );

		}

		try {
			$this->db->query ( "COMMIT" );

		} catch ( Exception $e ) {
			$this->db->query ( "ROLLBACK" );

		}
		$this->db->query ( "SET autocommit = 1" );

		return true;

	}

}