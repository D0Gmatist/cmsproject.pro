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
        if ( $handle = opendir( ROOT_DIR . '\modules\plugins' ) ) {
            while ( false !== ( $dir = readdir( $handle ) ) ) {
                if ( ! in_array( $dir, $this->notDir ) ) {
                    if ( file_exists( ROOT_DIR . '/modules/plugins/' . $dir . '/loader.php' ) ) {
                        require_once $dir . '/loader.php';
                    } else {
                        $fp = fopen( ROOT_DIR . '/modules/plugins/' . $dir . '/loader.php', 'w' );
                        fwrite( $fp, "<?php\n\n\$loader = '" . $dir . "';" );
                        fclose( $fp );

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
