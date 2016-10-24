<?php

namespace Modules\plugins;

class AutoloadPlugins {
    private $notDir = [ '.', '..', 'loader.php', 'PluginsAbstract.php', 'AutoloadPlugins.php' ];

    function __construct() {
        $this->scanDir();
    }

    private function scanDir () {
        if ( $handle = opendir( ROOT_DIR . '\modules\plugins' ) ) {
            while ( false !== ( $file = readdir( $handle ) ) ) {
                if ( ! in_array( $file, $this->notDir ) ) {
                    require_once $file . '/loader.php';
                }

            }
            closedir( $handle );

        }

    }

}

new AutoloadPlugins();