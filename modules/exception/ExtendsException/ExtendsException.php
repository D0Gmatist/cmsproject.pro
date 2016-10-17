<?php

namespace Modules\exception\ExtendsException;
use Exception;
use Modules\exception\InterfaceException\InterfaceExtendsException;

/**
 * Class ExtendsException
 * @package test\ExtendsTest
 */
class ExtendsException extends Exception implements InterfaceExtendsException {

    /**
     * ExtendsException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    function __construct( $message, $code, Exception $previous = null ) {
        parent::__construct( $message, $code, $previous );

    }

    /**
     * @return array
     */
    public function getError() {
        $infoArray = [];
        $infoArray['line']      = $this->line;
        
        $file = explode( $_SERVER['HTTP_HOST'], $this->file );
        $infoArray['file']      = $_SERVER['HTTP_HOST'] . $file[1];

        $infoArray['message']   = $this->message;

        return $infoArray;
    }

}
