<?php

namespace Modules\VarsSerialize;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class VarsSerialize {

	/** @var Functions  */
	private $functions;

	/** @var Db  */
	private $db;

	/** @var array  */
	private $config;

	/** @var array  */
	private $language;

	/** @var array  */
	private $result = false;

	/**
	 * VarsSerialize constructor.
	 * @param Functions $functions
	 * @param Db $db
	 * @param array $config
	 * @param array $language
	 */
	function __construct ( Functions $functions, Db $db, array $config, array $language ) {
		$this->functions = $functions;
		$this->db = $db;
		$this->config = $config;
		$this->language = $language;

	}

	public function initial ( $table, $name ) {
		$this->result = false;

		$this->getVars ( $table );

		if ( ! is_array ( $this->result ) ) {
			$this->result = [];

			$this->db->query( "SELECT * FROM `{$table}` ORDER BY `{$name}` ASC" );
			while ( $row = $this->db->getRow() ) {
				$this->result[$row[$name]] = [];

				foreach ( $row as $key => $value ) {
					$this->result[$row[$name]][$key] = stripslashes ( $value );

				}

			}
			$this->setVars ( $table, $this->result );
			$this->db->free ();

		}

		return $this->result;

	}

	private function setVars( $file, $data ) {
		if ( is_array( $data ) OR is_int( $data ) ) {

			$file = $this->functions->toTranslate( $file, $this->language['langTranslate'], true, false );
			$fp = fopen( ROOT_DIR . '/cache/system/' . $file . '.php', 'wb+' );
			fwrite( $fp, serialize( $data ) );
			fclose( $fp );

			@chmod( ROOT_DIR . '/cache/system/' . $file . '.php', 0666 );

		}

	}

	private function getVars( $file ) {
		$file = $this->functions->toTranslate( $file, $this->language['langTranslate'], true, false );
		$data = @file_get_contents( ROOT_DIR . '/cache/system/' . $file . '.php' );

		if ( $data !== false ) {
			$data = unserialize( $data );

			if ( is_array($data) OR is_int( $data ) ) {
				$this->result = $data;

			}

		}

	}

}