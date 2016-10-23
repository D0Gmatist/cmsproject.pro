<?php

namespace Modules\extendsException;

use Exception;

class ExtendsException extends Exception {
    private $msg;

    /**
     * ExtendsException constructor.
     * @param string $msg
     * @param int $code
     * @param Exception $previous
     */
    function __construct( $msg, $code, Exception $previous ) {
        parent::__construct( $msg, $code, $previous );

        $this->msg = $msg;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

}