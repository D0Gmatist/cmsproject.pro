<?php

namespace Modules\plugins;

class PluginsAutoload {
    /** @var array  */
    private $notDir = [ '.', '..', 'loader.php', 'PluginsAbstract.php', 'PluginsAutoload.php' ];

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
                    var_dump( file_exists( ROOT_DIR . '/modules/plugins/' . $dir . '/loader.php' ) );
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

    /**
     * @param $file
     * @param $error_info
     */
    public function displayError( $file, $error_info ) {

        $error = 'Отсутствует файл подключения плагина: ' . $file;

        echo <<<HTML
<?xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>PHP Error</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-style: normal;
	color: #000000;
}
.form {
    width: 700px;
    margin: 100px auto 0;
    border: 1px solid #D9D9D9;
    background-color: #F1EFEF;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
    -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
    box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
}
.top {
  color: #ffffff;
  font-size: 15px;
  font-weight: bold;
  padding-left: 20px;
  padding-top: 10px;
  padding-bottom: 10px;
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.75);
  background-color: #AB2B2D;
  background-image: -moz-linear-gradient(top, #CC3C3F, #982628);
  background-image: -ms-linear-gradient(top, #CC3C3F, #982628);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#CC3C3F), to(#982628));
  background-image: -webkit-linear-gradient(top, #CC3C3F, #982628);
  background-image: -o-linear-gradient(top, #CC3C3F, #982628);
  background-image: linear-gradient(top, #CC3C3F, #982628);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#CC3C3F', endColorstr='#982628',GradientType=0 ); 
  background-repeat: repeat-x;
  border-bottom: 1px solid #ffffff;
}
.box {
	margin: 10px;
	padding: 4px;
	background-color: #EFEDED;
	border: 1px solid #DEDCDC;

}
</style>
</head>
<body>
	<div class="form">
		<div class="top">PHP Error!</div>
		<div class="box">Error Info: <strong>{$error_info}</strong></div>
		<div class="box"><strong>{$error}</strong></div>
	</div>		
</body>
</html>
HTML;
        exit();

    }

}

new PluginsAutoload();
