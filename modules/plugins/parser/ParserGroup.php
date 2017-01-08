<?php

namespace Modules\Plugins\Parser;


use Exception;
use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Plugins\Vk\VkApi;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

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

		for ( $i = 1; $i <= 20; $i++ ) {
			$this->memberId = $this->db->superQuery ( "SELECT * FROM users ORDER BY RAND() LIMIT 1" );

			$this->vkApi = new VkApi( $this->memberId, $this->config );

			$row = $this->db->superQuery ( "SELECT 
												* 
													FROM 
														parser_groups 
															WHERE 
																( `parser_g_count` > '0' AND `parser_g_count` > `parser_g_offset` ) 
																	AND 
																`parser_g_error` = '0'
																	LIMIT 1" );

			$this->db->query ( "UPDATE parser_groups SET `parser_g_error` = '1' WHERE `parser_g_id` = '{$row['parser_g_id']}'" );

			$result = $this->vkApi->getApiGroupsMembersUserId ( $row[ 'parser_g_vk_group_id' ], 1000, $row[ 'parser_g_offset' ] );

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
				$this->db->query ( "UPDATE parser_groups SET `parser_g_error` = '0' WHERE `parser_g_id` = '{$row['parser_g_id']}'" );

			}

			try {
				$this->db->query ( "COMMIT" );

			} catch ( Exception $e ) {
				$this->db->query ( "ROLLBACK" );

			}
			$this->db->query ( "SET autocommit = 1" );

		}

		$this->db->close();

		die();

	}

}