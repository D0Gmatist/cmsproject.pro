<?php

namespace Modules\Plugins;

use Modules\mysql\Db\Db;
use Modules\Template\Template;

abstract class PluginsAbstract {
    public $nameMod         = 'Name Mod';
    public $versionMod      = 'Version Mod';
    public $authorMod       = 'Author Mod';

    /** @var Db  */
    protected $db;
    protected $query        = 'SELECT * FROM users ORDER BY `user_id` ASC';
    protected $row          = [];
    /** @var Template  */
    protected $tpl;
    protected $tplName      = 'default';
    protected $tplResult    = '';

    /**
     * PluginsAbstract constructor.
     * @param Db $db
     * @param $query
     * @param Template $tpl
     * @param $tplName
     */
    function __construct( Db $db, $query, Template $tpl, $tplName ) {
        $this->db = $db;
        $this->query = $query;
        $this->tpl = $tpl;
        $this->tplName = $tplName;
    }

    /**
     * @param string $query
     */
    public function setQuery( $query ) {
        $this->query = $query;
    }

    /**
     * @param $num
     * @return mixed
     */
    public function getRow( $num ) {
        return $this->row[$num];
    }

    /**
     * @return array
     */
    public function getRowAll() {
        return $this->row;
    }

    /**
     * @return string
     */
    public function getTplName() {
        return $this->tplName;
    }

    /**
     * @param string $tplName
     */
    public function setTplName( $tplName ) {
        $this->tplName = $tplName;
    }

    /**
     * @return string
     */
    public function getTplResult() {
        return $this->tplResult;
    }

    /**
     * @param string $tplResult
     */
    public function setTplResult( $tplResult ) {
        $this->tplResult = $tplResult;
    }
    
}