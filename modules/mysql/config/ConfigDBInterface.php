<?php

namespace Modules\Mysql\Config;

interface ConfigDbInterface {
    public function getDbHost();
    public function getDbName();
    public function getDbUser();
    public function getDbPass();
	public function getCollate();
	public function getSecureAuthKey();
}
