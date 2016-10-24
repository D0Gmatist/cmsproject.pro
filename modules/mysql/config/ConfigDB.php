<?php

namespace Modules\mysql\config;

final class ConfigDB implements ConfigDBInterface {
    const DBHOST = 'localhost';
    const DBNAME = 'cmsproject';
    const DBUSER = 'cmsproject';
    const DBPASS = 'cmsproject';
    const COLLATE = 'utf8';

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

}

