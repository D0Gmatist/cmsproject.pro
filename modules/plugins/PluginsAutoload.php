<?php

namespace Modules\Plugins;

class PluginsAutoload {
    /** @var array  */
    private $notDir = [ '.', '..', 'loader.php', 'PluginsAutoload.php' ];

    /**
     * PluginsAutoload constructor.
     */
    function __construct() {
        $this->scanDir();
    }

    private function scanDir() {
    	/** @var  $handle */
        if ( $handle = opendir( PLUGINS_DIR ) ) {
            while ( false !== ( $dir = readdir( $handle ) ) ) {
                if ( ! in_array( $dir, $this->notDir ) ) {
					if ( is_readable( PLUGINS_DIR . '/' . $dir . '/loader.php' ) ) {
						require_once $dir . '/loader.php';

					}

                }

            }
            closedir( $handle );

        }

    }

    /**
     * @param array $notDir
     */
    public function setNotDir( $notDir ) {
        $this->notDir = $notDir;
    }

}

new PluginsAutoload();
