<?php

namespace Modules\mysql\config;

interface ConfigDBInterface {
    public function getDbHost();
    public function getDbName();
    public function getDbUser();
    public function getDbPass();
    public function getCollate();
}
