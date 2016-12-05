<?php

namespace Modules\Template;

use Modules\MobileDetect\MobileDetect;
use Modules\Translate\Translate;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

class Template {

    /** @var string  */
    public $dir;

    /** @var MobileDetect  */
    public $mobileDetect;

    /** @var Translate  */
    public $translate;

    /** @var array  */
    public $config;

	/** @var array  */
	private $memberId;

	/** @var bool  */
    public $smartPhone = false;

    /** @var bool  */
    public $tablet = false;

    /** @var bool  */
    public $desktop = true;

    /** @var array  */
    public $data = [];

    /** @var array  */
    public $block_data = [];

    /** @var null  */
    public $template = null;

    /** @var null  */
    public $copy_template = null;

    /** @var string  */
    public $include_mode = 'tpl';

    /** @var int  */
    public $template_parse_time = 0;

    /** @var bool  */
    public $allowPhpInclude = true;

    /** @var array  */
    public $result = [];

	/**
	 * Template constructor.
	 * @param MobileDetect $mobileDetect
	 * @param Translate $translate
	 * @param $config
	 * @param array $memberId
	 */
    function __construct( MobileDetect $mobileDetect, Translate $translate, array $config, array $memberId ) {
        $this->dir = ROOT_DIR . '/templates/' . $config['skin'];
        $this->mobileDetect = $mobileDetect;
        $this->translate = $translate;
        $this->config = $config;
		$this->memberId = $memberId;

        if ( $this->mobileDetect->isMobile() ) {
            $this->smartPhone   = true;
            $this->desktop      = false;

        }

        if ( $this->mobileDetect->isTablet() ) {
            $this->smartPhone   = false;
            $this->desktop      = false;
            $this->tablet       = true;

        }

	}

    /**
     * @param $name
     * @param $var
     */
    public function set( $name, $var ) {
        if ( is_array( $var ) AND count( $var ) ) {
            foreach ( $var AS $key => $key_var ) {
                $this->set( $key, $key_var );
            }
        } else {
            $var = str_ireplace( "{include", "&#123;include", $var );
            $var = str_ireplace( "{custom", "&#123;custom", $var );
            $this->data[ $name ] = $var;
        }
    }

    /**
     * @param $name
     * @param $var
     */
    public function setBlock( $name, $var ) {
        if ( is_array( $var ) AND count( $var ) ) {
            foreach ( $var AS $key => $key_var ) {
                $this->setBlock( $key, $key_var );

            }

        } else {
            $var = str_ireplace( "{include", "&#123;include", $var );
            $var = str_ireplace( "{custom", "&#123;custom", $var );
            $this->block_data[ $name ] = $var;

        }

    }

    /**
     * @param $tpl_name
     * @return bool
     */
    public function loadTemplate( $tpl_name ) {
        $time_before = $this->getRealTime();
        $tpl_name = str_replace( chr( 0 ), '', $tpl_name );
        $url = @parse_url( $tpl_name );
        $file_path = dirname( $this->clearUrlDir( $url['path'] ) );
        $tpl_name = pathinfo( $url['path'] );

        #################################################
        $this->translate->setVar( $tpl_name['basename'] );
        $this->translate->strReplaceVar();
        $this->translate->strReplaceVar( ' ' );
        $this->translate->pregReplaceVar( "/[^a-z0-9\_.]+/mi" );
        $this->translate->strTrVar();
        $this->translate->trimVar();
        $tpl_name = $this->translate->getVar();
        #################################################

        $type = explode( '.', $tpl_name );
        $type = strtolower( end( $type ) );

        if ( $type != 'tpl' ) {
            $this->template = 'Not Allowed Template Name: ' . str_replace( ROOT_DIR, '', $this->dir ) . '/' . $tpl_name;
            $this->copy_template = $this->template;

            return '';

        }

        if ( $file_path AND $file_path != '.' ) {
            $tpl_name = $file_path . '/' . $tpl_name;

        }

        if( stripos( $tpl_name, '.php' ) !== false ) {
            $this->template = 'Not Allowed Template Name: ' . str_replace( ROOT_DIR, '', $this->dir ) . '/' . $tpl_name;
            $this->copy_template = $this->template;

            return '';

        }

        if( $tpl_name == '' || ! file_exists( $this->dir . '/' . $tpl_name ) ) {
            $this->template = 'Template not found: ' . str_replace( ROOT_DIR, '', $this->dir ) . '/' . $tpl_name;
            $this->copy_template = $this->template;

            return '';

        }
        $this->template = file_get_contents( $this->dir . '/' . $tpl_name );

		if ( strpos( $this->template, '{*' ) !== false ) {
            $this->template = preg_replace( "'\\{\\*(.*?)\\*\\}'si", '', $this->template );

        }
		$this->template = $this->checkModule( $this->template );

		if ( strpos( $this->template, '[group=' ) !== false OR strpos( $this->template, '[not-group=' ) !== false ) {
			$this->template = $this->checkGroup( $this->template );

		}

		if ( strpos( $this->template, '[smartphone]' ) !== false ) {
            $this->template = preg_replace_callback( "#\\[(smartphone)\\](.*?)\\[/smartphone\\]#is", [ &$this, 'checkDevice' ], $this->template );

        }

        if ( strpos( $this->template, '[not-smartphone]' ) !== false ) {
            $this->template = preg_replace_callback( "#\\[(not-smartphone)\\](.*?)\\[/not-smartphone\\]#is", [ &$this, 'checkDevice' ], $this->template );

        }

        if ( strpos( $this->template, '[tablet]' ) !== false) {
            $this->template = preg_replace_callback( "#\\[(tablet)\\](.*?)\\[/tablet\\]#is", [ &$this, 'checkDevice' ], $this->template );

        }

        if ( strpos( $this->template, '[not-tablet]' ) !== false ) {
            $this->template = preg_replace_callback( "#\\[(not-tablet)\\](.*?)\\[/not-tablet\\]#is", [ &$this, 'checkDevice' ], $this->template );

        }

        if ( strpos( $this->template, '[desktop]' ) !== false ) {
            $this->template = preg_replace_callback( "#\\[(desktop)\\](.*?)\\[/desktop\\]#is", [ &$this, 'checkDevice' ], $this->template );

        }

        if ( strpos( $this->template, '[not-desktop]' ) !== false) {
            $this->template = preg_replace_callback( "#\\[(not-desktop)\\](.*?)\\[/not-desktop\\]#is", [ &$this, 'checkDevice' ], $this->template );

        }

        if( strpos( $this->template, '{include file=' ) !== false ) {
            $this->include_mode = 'tpl';
            $this->template = preg_replace_callback( "#\\{include file=['\"](.+?)['\"]\\}#i", [ &$this, 'loadFile' ], $this->template );

        }
        $this->copy_template = $this->template;
		$this->template_parse_time += $this->getRealTime() - $time_before;

        return true;

    }

    /**
     * @param array $matches
     * @return string
     */
    public function loadFile( $matches = [] ) {
        $name = $matches[1];
        $name = str_replace( chr( 0 ), '', $name );
        $name = str_replace( '..', '', $name );
        $url = @parse_url ( $name );
        $type = explode( '.', $url['path'] );
        $type = strtolower( end( $type ) );

        if ( $type == 'tpl' ) {
            return $this->subLoadTemplate( $name );

        }

        if ( $this->include_mode == 'php' ) {
            if ( ! $this->allowPhpInclude ) {
                return '';

            }

            if ( $type != 'php' ) {
                return "To connect permitted only files with the extension: .tpl or .php";

            }

            if ( $url['path']{0} == '/' ) {
                $file_path = dirname( ROOT_DIR . $url[ 'path' ] );

            } else {
                $file_path = dirname( ROOT_DIR . "/" . $url[ 'path' ] );

            }
            $file_name = pathinfo( $url['path'] );
            $file_name = $file_name['basename'];

			$chmodValue = false;

            if ( stristr( php_uname( "s" ) , "windows" ) === false ) {
                $chmodValue = @decoct( @fileperms( $file_path ) ) % 1000;

            }

            if ( stristr( dirname( $url['path'] ) , 'uploads' ) !== false ) {
                return 'Include files from directory /uploads/ is denied';

            }

            if ( stristr( dirname( $url['path'] ) , 'templates' ) !== false ) {
                return 'Include files from directory /templates/ is denied';

            }

            if ( stristr( dirname( $url['path'] ) , 'engine/data' ) !== false ) {
                return 'Include files from directory /engine/data/ is denied';

            }

            if ( stristr( dirname( $url['path'] ) , 'engine/cache' ) !== false ) {
                return 'Include files from directory /engine/cache/ is denied';

            }

            if ( stristr( dirname( $url['path'] ) , 'engine/inc' ) !== false ) {
                return 'Include files from directory /engine/inc/ is denied';

            }

            if ( $chmodValue == 777 ) {
                return "File {$url['path']} is in the folder, which is available to write (CHMOD 777). For security purposes the connection files from these folders is impossible. Change the permissions on the folder that it had no rights to the write.";

            }

            if ( !file_exists($file_path . '/' . $file_name) ) {
                return "File {$url['path']} not found.";

            }
            $url['query'] = str_ireplace( [ 'file_path', 'file_name', '_GET', '_FILES', '_POST', '_REQUEST', '_SERVER', '_COOKIE', '_SESSION' ] , 'Filtered', $url['query'] );

            if( substr_count( $this->template, '{include file=' ) < substr_count( $this->copy_template, '{include file=' ) ) {
                return 'Filtered';

            }

            if ( isset( $url['query'] ) AND $url['query'] ) {
                $module_params = [];
                parse_str( $url['query'], $module_params );
                extract( $module_params, EXTR_SKIP );
                unset( $module_params );

            }
            ob_start();

            $tpl = new Template( new MobileDetect(), new Translate(), $this->config, $this->memberId );
            $tpl->dir = TPL_DIR;

            include $file_path . '/' . $file_name;

            return ob_get_clean();

        }

        return '{include file="' . $name . '"}';

    }

    /**
     * @param $tpl_name
     * @return mixed|string
     */
    public function subLoadTemplate( $tpl_name ) {
        $tpl_name = str_replace( chr( 0 ), '', $tpl_name );
        $url = @parse_url( $tpl_name );
        $file_path = dirname( $this->clearUrlDir( $url['path'] ) );
        $tpl_name = pathinfo( $url['path'] );

        #################################################
        $this->translate->setVar( $tpl_name['basename'] );
        $this->translate->strReplaceVar();
        $this->translate->strReplaceVar( ' ' );
        $this->translate->pregReplaceVar( "/[^a-z0-9\_.]+/mi" );
        $this->translate->strTrVar();
        $this->translate->trimVar();
        $tpl_name = $this->translate->getVar();
        #################################################

        $type = explode( '.', $tpl_name );
        $type = strtolower( end( $type ) );

        if ( $type != 'tpl' ) {
            return 'Not Allowed Template Name: ' . $tpl_name;

        }

        if ( $file_path AND $file_path != '.' ) {
            $tpl_name = $file_path . '/' . $tpl_name;

        }

        if ( strpos( $tpl_name, '/templates/' ) === 0 ) {
            $tpl_name = str_replace( '/templates/', '', $tpl_name );
            $templatefile = ROOT_DIR . '/templates/' . $tpl_name;

        } else {
            $templatefile = $this->dir . '/' . $tpl_name;

        }

        if( $tpl_name == '' || !file_exists( $templatefile ) ) {
            $templatefile = str_replace( ROOT_DIR,'',$templatefile );
            return "Template not found: " . $templatefile;

        }

        if( stripos( $templatefile, '.php' ) !== false ) {
            return 'Not Allowed Template Name: ' . $tpl_name;

        }
        $template = file_get_contents( $templatefile );

        if ( strpos( $template, '{*' ) !== false ) {
            $template = preg_replace( "'\\{\\*(.*?)\\*\\}'si", '', $template );

        }
        $template = $this->checkModule( $template );

        if ( strpos( $template, '[group=' ) !== false OR strpos( $template, '[not-group=' ) !== false ) {
            $template = $this->checkGroup( $template );

        }

        if ( strpos( $template, '[smartphone]' ) !== false ) {
            $template = preg_replace_callback( "#\\[(smartphone)\\](.*?)\\[/smartphone\\]#is", [ &$this, 'checkDevice' ], $template );

        }

        if ( strpos( $template, "[not-smartphone]" ) !== false ) {
            $template = preg_replace_callback( "#\\[(not-smartphone)\\](.*?)\\[/not-smartphone\\]#is", [ &$this, 'checkDevice' ], $template );

        }

        if ( strpos( $template, "[tablet]" ) !== false ) {
            $template = preg_replace_callback( "#\\[(tablet)\\](.*?)\\[/tablet\\]#is", [ &$this, 'checkDevice' ], $template );

        }

        if ( strpos( $template, "[not-tablet]" ) !== false ) {
            $template = preg_replace_callback( "#\\[(not-tablet)\\](.*?)\\[/not-tablet\\]#is", [ &$this, 'checkDevice' ], $template );

        }

        if ( strpos( $template, "[desktop]" ) !== false ) {
            $template = preg_replace_callback( "#\\[(desktop)\\](.*?)\\[/desktop\\]#is", [ &$this, 'checkDevice' ], $template );

        }

        if ( strpos( $template, "[not-desktop]" ) !== false ) {
            $template = preg_replace_callback( "#\\[(not-desktop)\\](.*?)\\[/not-desktop\\]#is", [ &$this, 'checkDevice' ], $template );

        }

        return $template;

    }
    
    /**
     * @param $var
     * @return mixed|string
     */
    public function clearUrlDir( $var ) {
        if ( is_array( $var ) ) {
            return '';

        }

        $var = str_ireplace( '.php', '', $var );
        $var = str_ireplace( '.php', '.ppp', $var );
        $var = trim( strip_tags( $var ) );
        $var = str_replace( "\\", "/", $var );
        $var = preg_replace( "/[^a-z0-9\/\_\-]+/mi", '', $var );
        $var = preg_replace( '#[\/]+#i', '/', $var );

        return $var;

    }

    /**
     * @param $matches
     * @return mixed
     */
    public function checkModule( $matches ) {
        global $module;

        $regex = '/\[(aviable|available|not-aviable|not-available)=(.+?)\]((?>(?R)|.)*?)\[\/\1\]/is';

        if ( is_array( $matches ) ) {
            $aviable = $matches[2];
            $block = $matches[3];

            if ( $matches[1] == 'aviable' OR $matches[1] == 'available' ) {
                $action = true;

            } else {
                $action = false;

            }
            $aviable = explode( '|', $aviable );

            if ( $action ) {
                if( ! ( in_array( $module, $aviable ) ) and ( $aviable[0] != 'global' ) ) {
                    $matches = '';

                } else {
                    $matches = $block;

                }

            } else {
                if ( ( in_array( $module, $aviable ) ) ) {
                    $matches = '';

                } else {
                    $matches = $block;

                }

            }

        }

        return preg_replace_callback( $regex, [ &$this, 'checkModule' ], $matches );

    }

    /**
     * @param array $matches
     * @return mixed|string
     */
    public function checkDevice( $matches = [] ) {
        $block = $matches[2];
        $device = $this->desktop;

        if ( $matches[1] == 'smartphone' OR $matches[1] == 'tablet' OR $matches[1] == "desktop" ) {
            $action = true;

        } else {
            $action = false;

        }

        if ( $matches[1] == 'smartphone' OR $matches[1] == 'not-smartphone' ) {
            $device = $this->smartPhone;

        }
        if ( $matches[1] == 'tablet' OR $matches[1] == 'not-tablet' ) {
            $device = $this->tablet;

        }

        if( $action ) {
            if( ! $device ) {
                return '';

            }

        } else {
            if( $device ) {
                return '';

            }

        }

        return $block;

    }

    /**
     * @param array $matches
     * @return string
     */
    public function declination( $matches = [] ) {
        $matches[1] = str_replace( ' ', '', $matches[1] );
        $matches[1] = intval( $matches[1] );
        $words = explode( '|', trim( $matches[2] ) );
        $parts_word = [];

        switch ( count( $words ) ) {

            case 1:
                $parts_word[0] = $words[0];
                $parts_word[1] = $words[0];
                $parts_word[2] = $words[0];
                break;

            case 2:
                $parts_word[0] = $words[0];
                $parts_word[1] = $words[0].$words[1];
                $parts_word[2] = $words[0].$words[1];
                break;

            case 3:
                $parts_word[0] = $words[0];
                $parts_word[1] = $words[0].$words[1];
                $parts_word[2] = $words[0].$words[2];
                break;

            case 4:
                $parts_word[0] = $words[0].$words[1];
                $parts_word[1] = $words[0].$words[2];
                $parts_word[2] = $words[0].$words[3];
                break;
        }

        $word = $matches[1]%10 == 1 && $matches[1]%100 != 11 ? $parts_word[0] : ( $matches[1]%10 >= 2 && $matches[1]%10 <= 4 && ( $matches[1]%100 < 10 || $matches[1]%100 >= 20) ? $parts_word[1] : $parts_word[2] );

        return $word;

    }

    /**
     * @param $matches
     * @return mixed
     */
    public function checkGroup( $matches ) {
        $regex = '/\[(group|not-group)=(.+?)\]((?>(?R)|.)*?)\[\/\1\]/is';

        if ( is_array( $matches ) ) {
			$groups = $matches[2];
            $block = $matches[3];

            if ( $matches[1] == 'group' ) {
                $action = true;

            } else {
                $action = false;

            }
            $groups = explode( ',', $groups );

            if ( $action ) {
                if ( ! in_array( $this->memberId['user_group'], $groups ) ) {
                    $matches = '';

                } else {
                    $matches = $block;

                }

            } else {
                if ( in_array( $this->memberId['user_group'], $groups ) ) {
                    $matches = '';

                } else {
                    $matches = $block;

                }

            }

        }

		return preg_replace_callback( $regex, [ &$this, 'checkGroup' ], $matches );

    }

    public function _clear() {
        $this->data = [];
        $this->block_data = [];
        $this->copy_template = $this->template;

    }

    public function clear() {
        $this->data = [];
        $this->block_data = [];
        $this->copy_template = null;
        $this->template = null;

    }

    public function globalClear() {
        $this->data = [];
        $this->block_data = [];
        $this->copy_template = null;
        $this->template = null;
        $this->result = [];

    }

    /**
     * @param $tpl
     */
    public function compile( $tpl ) {
        $find_preg      = [];
        $replace_preg   = [];
        $find           = [];
        $replace        = [];
        $time_before = $this->getRealTime();

        if ( count( $this->block_data ) ) {
            foreach ( $this->block_data AS $key_find => $key_replace ) {
                $find_preg[]    = $key_find;
                $replace_preg[] = $key_replace;

            }
            $this->copy_template = preg_replace( $find_preg, $replace_preg, $this->copy_template );

        }

        foreach ( $this->data as $key_find => $key_replace ) {
            $find[] = $key_find;
            $replace[] = $key_replace;

        }
        $this->copy_template = str_ireplace( $find, $replace, $this->copy_template );

        if ( strpos( $this->copy_template, '[declination=' ) !== false ) {
            $this->copy_template = preg_replace_callback ( "#\\[declination=(.+?)\\](.+?)\\[/declination\\]#is", [ &$this, 'declination' ], $this->copy_template );

        }

        if( strpos( $this->template, "{include file=" ) !== false ) {
            $this->include_mode = 'php';
            $this->copy_template = preg_replace_callback( "#\\{include file=['\"](.+?)['\"]\\}#i", [ &$this, 'loadFile' ], $this->copy_template );

        }
        $this->copy_template = $this->globalTags( $this->copy_template );

        if( isset( $this->result[$tpl] ) ) {
            $this->result[$tpl] .= $this->copy_template;

        } else {
            $this->result[$tpl] = $this->copy_template;

        }
        $this->_clear();
        $this->template_parse_time += $this->getRealTime() - $time_before;

    }

    /**
     * @param $template
     * @return mixed
     */
    public function globalTags( $template ) {
        return str_ireplace( '{THEME}', $this->config['http_home_url'] . 'templates/' . $this->config['skin'], $template );

    }

    /**
     * @return float
     */
    public function getRealTime() {
        list ( $seconds, $microSeconds ) = explode( ' ', microtime() );
        return ( ( float ) $seconds + ( float ) $microSeconds );

    }

}