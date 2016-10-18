<?php

namespace Modules\mysql\config;

use Modules\mysql\config\configDBInterface\ConfigDBInterface;

final class ConfigDB implements ConfigDBInterface {
    const DBHOST = 'localhost';
    const DBNAME = 'testimonials';
    const DBUSER = 'testimonials';
    const DBPASS = 'testimonials';
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

