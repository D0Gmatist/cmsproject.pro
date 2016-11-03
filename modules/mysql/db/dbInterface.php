<?php

namespace Modules\Mysql\Db;

interface DbInterface {
    public function query( $query, $show_error );
    public function getRow( $query_id );
    public function getAffectedRows();
    public function getArray( $query_id );
    public function superQuery( $query, $multi );
    public function numRows( $query_id );
    public function insertId();
    public function getResultFields( $query_id );
    public function safeSql( $source );
    public function free( $query_id );
    public function close();
    public function getRealTime();
}