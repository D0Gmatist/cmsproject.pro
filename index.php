<?

use Modules\MobileDetect\MobileDetect;
use Modules\Mysql\Config\ConfigDb;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;
use Modules\Translate\Translate;

@ob_start ();
@ob_implicit_flush ( 0 );
if( !defined( 'E_DEPRECATED' ) ) {
    @error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
    @ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );
} else {
    @error_reporting ( E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
    @ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
}
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );

define ( 'ENGINE', true );
define ( 'ROOT_DIR', dirname ( __FILE__ ) );
define ( 'TEMPLATE_DIR', ROOT_DIR . '/errorTemplate' );

header('Content-Type: text/html; charset=utf-8');

$module = 'home';
$member_id = false;
$is_logged = false;

require_once 'loader.php';

$db = new Db( new ConfigDb );

//$sql = $db->query( "SELECT * FROM users ORDER BY `user_id` ASC" );

$tpl = new Template( new MobileDetect(),  new Translate(), 'Default' );
define ( 'TPL_DIR', $tpl->dir );

$tpl->loadTemplate( 'main.tpl' );
$tpl->compile( 'main' );
echo $tpl->result['main'];
var_dump( $_GET );
$tpl->globalClear();
$db->close();