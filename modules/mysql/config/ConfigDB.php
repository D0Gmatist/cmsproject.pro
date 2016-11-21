<?php

namespace Modules\Mysql\Config;

final class ConfigDb implements ConfigDbInterface {
    const DBHOST = 'localhost';
    const DBNAME = 'cmsproject';
    const DBUSER = 'root';
    const DBPASS = 'mR7clzJsqXzVD7k6';
    const COLLATE = 'utf8';

    /**
     * @return string
     */
    public function getDbHost() {
        return ConfigDb::DBHOST;
    }

    /**
     * @return string
     */
    public function getDbName() {
        return ConfigDb::DBNAME;
    }

    /**
     * @return string
     */
    public function getDbUser() {
        return ConfigDb::DBUSER;
    }

    /**
     * @return string
     */
    public function getDbPass() {
        return ConfigDb::DBPASS;
    }

    /**
     * @return string
     */
    public function getCollate() {
        return ConfigDb::COLLATE;
    }

}

