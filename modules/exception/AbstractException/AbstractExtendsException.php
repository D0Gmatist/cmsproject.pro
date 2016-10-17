<?php

namespace Modules\exception\AbstractException;

/**
 * Class AbstractExtendsException
 * @package modules\exception\AbstractException
 */
abstract class AbstractExtendsException {
    private $infoArray = [];

    /**
     * AbstractExtendsException constructor.
     * @param $infoArray
     */
    function __construct( $infoArray ) {
        $this->infoArray = $infoArray;
    }

    /**
     * @return mixed
     */
    public function templateText(){
        return $this->infoArray;
    }

}
