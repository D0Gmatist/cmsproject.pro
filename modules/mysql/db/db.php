<?php

namespace Modules\mysql\db;

use Modules\errorTemplate\ErrorTemplate;
use Modules\mysql\config\ConfigDB;

final class db implements dbInterface {
    /** @var bool|ConfigDB  */
    private $configDb = false;
    /** @var bool|ErrorTemplate  */
    private $errorTemplate = false;
    /** @var bool  */
    private $db_id = false;
    private $query_id = false;
    /** @var int  */
    private $query_num = 0;
    private $mysql_error_num = 0;
    private $MySQL_time_taken = 0;
    /** @var array  */
    private $query_list = array();
    /** @var string  */
    private $mysql_error = '';
    private $mysql_version = '';

    /**
     * db constructor.
     * @param ConfigDB $configDb
     * @param ErrorTemplate $errorTemplate
     */
    function __construct( ConfigDB $configDb, ErrorTemplate $errorTemplate ) {
        $this->configDb = $configDb;
        $this->errorTemplate = $errorTemplate;
    }

    /**
     * @param $db_user
     * @param $db_pass
     * @param $db_name
     * @param string $db_location
     * @param int $show_error
     * @return bool
     */
    private function connect( $db_user, $db_pass, $db_name, $db_location = 'localhost', $show_error = 1 ) {
        $db_location = explode( ":", $db_location );
        if ( isset( $db_location[1] ) ) {
            $this->db_id = @mysqli_connect( $db_location[0], $db_user, $db_pass, $db_name, $db_location[1] );
        } else {
            $this->db_id = @mysqli_connect( $db_location[0], $db_user, $db_pass, $db_name );
        }
        if ( ! $this->db_id ) {
            if( $show_error == 1 ) {
                $this->displayError( mysqli_connect_error(), '1' );
            } else {
                return false;
            }
        }
        $this->mysql_version = mysqli_get_server_info( $this->db_id );
        if( ! defined( $this->configDb->getCollate() ) ) {
            define ( $this->configDb->getCollate(), 'cp1251' );
        }
        mysqli_set_charset ( $this->db_id , $this->configDb->getCollate() );
        return true;
    }

    /**
     * @param $query
     * @param bool $show_error
     * @return bool|\mysqli_result
     */
    public function query( $query, $show_error = true ) {
        $time_before = $this->getRealTime();
        if( ! $this->db_id ) {
            $this->connect( $this->configDb->getDbUser(), $this->configDb->getDbPass(), $this->configDb->getDbName(), $this->configDb->getDbHost() );
        }
        if( ! ( $this->query_id = mysqli_query( $this->db_id, $query ) ) ) {
            $this->mysql_error = mysqli_error( $this->db_id );
            $this->mysql_error_num = mysqli_errno( $this->db_id );
            if( $show_error ) {
                $this->displayError( $this->mysql_error, $this->mysql_error_num, $query );
            }
        }
        $this->MySQL_time_taken += $this->getRealTime() - $time_before;
        $this->query_list[] = array( 
                                'time'  => ($this->getRealTime() - $time_before 
                                ),
                                'query' => $query,
                                'num'   => ( count( $this->query_list ) + 1 ) );
        $this->query_num ++;
        return $this->query_id;
    }

    /**
     * @param string $query_id
     * @return array|null
     */
    public function getRow( $query_id = '' ) {
        if ( $query_id == '' ) {
            $query_id = $this->query_id;
        }
        return mysqli_fetch_assoc( $query_id );
    }

    /**
     * @return int
     */
    public function getAffectedRows() {
        return mysqli_affected_rows( $this->db_id );
    }

    /**
     * @param string $query_id
     * @return array|null
     */
    public function getArray( $query_id = '' ) {
        if ($query_id == '') {
            $query_id = $this->query_id;
        }
        return mysqli_fetch_array( $query_id );
    }

    /**
     * @param $query
     * @param bool $multi
     * @return array|null
     */
    public function superQuery( $query, $multi = false ) {
        if( ! $multi ) {
            $this->query( $query );
            $data = $this->getRow();
            $this->free();
            return $data;
        } else {
            $this->query( $query );
            $rows = array();
            while( $row = $this->getRow() ) {
                $rows[] = $row;
            }
            $this->free();
            return $rows;
        }
    }

    /**
     * @param string $query_id
     * @return int
     */
    public function numRows( $query_id = '' ) {
        if ( $query_id == '' ) {
            $query_id = $this->query_id;
        }
        return mysqli_num_rows( $query_id );
    }

    /**
     * @return int|string
     */
    public function insertId() {
        return mysqli_insert_id( $this->db_id );
    }

    /**
     * @param string $query_id
     * @return array
     */
    public function getResultFields( $query_id = '' ) {
        $fields = [];
        if ( $query_id == '' ) {
            $query_id = $this->query_id;
        }
        while ( $field = mysqli_fetch_field( $query_id ) ) {
            $fields[] = $field;
        }
        return $fields;
    }

    /**
     * @param $source
     * @return string
     */
    public function safeSql( $source ) {
        if( ! $this->db_id ) {
            $this->connect( $this->configDb->getDbUser(), $this->configDb->getDbPass(), $this->configDb->getDbName(), $this->configDb->getDbHost() );
        }
        if ( $this->db_id) {
            return mysqli_real_escape_string ($this->db_id, $source );
        } else {
            return addslashes($source);
        }
    }

    /**
     * @param string $query_id
     */
    public function free( $query_id = '' ) {
        if ( $query_id == '' ) {
            $query_id = $this->query_id;
        }
        @mysqli_free_result( $query_id );
    }

    public function close() {
        @mysqli_close( $this->db_id );
        $this->db_id = false;
    }

    /**
     * @return float
     */
    public function getRealTime() {
        list( $seconds, $microSeconds ) = explode( ' ', microtime() );
        return ( (float)$seconds + (float)$microSeconds );
    }

    /**
     * @param $error
     * @param $error_num
     * @param string $query
     */
    private function displayError( $error, $error_num, $query = '' ) {
        $template = $this->errorTemplate;
        $template->displayError( $error, $error_num, $query );
        exit();
    }

}