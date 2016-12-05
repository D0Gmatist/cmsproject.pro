<?php

namespace Modules\Mysql\Config;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

class ConfigDb {
    const DBHOST = 'localhost';
    const DBNAME = 'cmsproject';
    const DBUSER = 'cmsproject';
    const DBPASS = 'cmsproject';
    /*
    const DBUSER = 'root';
    const DBPASS = 'mR7clzJsqXzVD7k6';
    */
    const COLLATE = 'utf8';
    const SECURE_AUTH_KEY = '[xF}Fsr;iXs 9jMP~YGYa8=0?cY0h*%?x~.loiu7+d@^(h|dHZ6^=)BBL/Pr}6iV';

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

	/**
	 * @return string
	 */
	public function getSecureAuthKey() {
		return ConfigDb::SECURE_AUTH_KEY;

	}

}

