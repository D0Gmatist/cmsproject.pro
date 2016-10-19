<?

use Modules\mysql\config\ConfigDB;
use Modules\mysql\db\db;

use Modules\mobileDetect\MobileDetect;
use Modules\template\Template;
use Modules\translit\Translit;

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
define ( 'TEMPLATE_DIR', ROOT_DIR . '/template' );

header('Content-Type: text/html; charset=utf-8');

$module = 'home';
$member_id = false;
$is_logged = false;

require_once 'loader.php';

$db = new db( new ConfigDB );
//$sql = $db->query( "SELECT * FROM dle_users ORDER BY `user_id` ASC" );

$tpl = new Template( new MobileDetect(),  new Translit(), 'Default' );
define ( 'TPL_DIR', $tpl->dir );

$tpl->loadTemplate( 'main.tpl' );
$tpl->compile( 'main' );
echo $tpl->result['main'];

$tpl->globalClear();
$db->close();


class Test1 {
    static function fnTest1 ( $val ) {
        $count = 0;
        return function( $fnTest2 ) use ( $val, &$count ) {
            echo $count . '<br>';
            echo $fnTest2->val . '<br>';
            if ( $count > $fnTest2->val ) {
                echo '+++<br>';
            } else {
                echo '---<br>';
            }
            $count = $fnTest2->val;

        };
    }
}

class Test2 {
    private $callback;

    /**
     * @param $val
     */
    public function regVal1( $val ) {
        if ( is_callable( $val ) ) {
            $this->callback[] = $val;
        }
    }

    public function regVal2( Test $val ) {
        foreach( $this->callback AS $callback ) {
            call_user_func( $callback, $val );
        }
    }
}

class Test {
    public $val = 0;
    function __construct( $val ){
        $this->val = $val;
    }
}

$test1 = new Test2();
$test1->regVal1( Test1::fnTest1( 2 ) );
$test1->regVal2( new Test( 3 ) );
$test1->regVal2( new Test( 1 ) );
$test1->regVal2( new Test( 2 ) );

