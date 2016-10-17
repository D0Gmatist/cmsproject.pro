<?php

namespace Modules\mysql\config;

final class ConfigDB {
    const DBHOST = 'localhost';
    const DBNAME = 'testimonials';
    const DBUSER = 'testimonials';
    const DBPASS = 'testimonials';
    const COLLATE = 'utf8';
    const SECUREAUTHKEY = '!xI@=L<,v7-!n}G80=^pHykUU<C44)XBv>J(O{fY;mfn)2r^)?j U{%F]?oT33';

    /**
     * @return string
     */
    public function getDbHost() {
        return ConfigDB::DBHOST;
    }

    /**
     * @return string
     */
    public function getDbName() {
        return ConfigDB::DBNAME;
    }

    /**
     * @return string
     */
    public function getDbUser() {
        return ConfigDB::DBUSER;
    }

    /**
     * @return string
     */
    public function getDbPass() {
        return ConfigDB::DBPASS;
    }

    /**
     * @return string
     */
    public function getCollate() {
        return ConfigDB::COLLATE;
    }

    /**
     * @return string
     */
    public function getSecureAuthKey() {
        return ConfigDB::SECUREAUTHKEY;
    }

}

