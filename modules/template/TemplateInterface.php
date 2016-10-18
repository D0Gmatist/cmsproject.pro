<?php

namespace Modules\template;

interface TemplateInterface {
    public function set( $name, $var );
    public function setBlock( $name, $var );
    public function loadTemplate( $tpl_name );
    public function loadFile( $matches );
    public function subLoadTemplate( $tpl_name );
    public function clearUrlDir( $var );
    public function checkModule( $matches );
    public function checkDevice( $matches );
    public function declination( $matches );
    public function checkGroup( $matches );
    public function _clear();
    public function clear();
    public function globalClear();
    public function compile( $tpl );
    public function getRealTime();
}