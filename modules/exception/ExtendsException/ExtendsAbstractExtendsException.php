<?php

namespace Modules\exception\ExtendsException;

use Modules\exception\AbstractException\AbstractExtendsException;
use Modules\exception\InterfaceException\InterfaceExtendsAbstractExtendsException;

/**
 * Class ExtendsAbstractExtendsException
 * @package modules\exception\ExtendsException
 */
class ExtendsAbstractExtendsException extends AbstractExtendsException implements InterfaceExtendsAbstractExtendsException {
    
    function __construct( $infoArray ) {
        parent::__construct( $infoArray );
    }

    public function Write(){
        $infoArray = $this->templateText();
        return <<<HTML
 <style>
.f1 {
    background: #e08787;
    border: 10px solid #dc7676;
    box-shadow: 0 8px 8px -4px rgba(0, 0, 0, 0.25);
    color: #880808;
    font-family: Arial;
    font-size: 14px;
    margin: 100px auto 0;
    position: relative;
    width: 1000px;
}
.f1_1 {
    background: rgba(255, 255, 255, 0.25);
    font-size: 26px;
    font-weight: 700;
    line-height: 26px;
    padding: 10px 8px;
    text-align: center;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.5);
}
.f1_2 {
    border: 1px dashed #f5f5f5;
    margin: 5px;
}
.f1_2:after {
    clear: both;
    content: ' ';
    display: block;
    position: relative;
}
.f1_2_1 {
    background: rgb(232, 165, 165);
    border-right: 1px dashed #f5f5f5;
    float: left;
    font-weight: 700;
    line-height: 18px;
    padding: 6px 8px;
    text-align: right;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.5);
    height: 18px;
    width: 150px;
}
.f1_2_2 {
    background: rgba(255, 255, 255, 0.2);
    box-shadow: inset 250px 0 250px -125px rgba(167, 32, 32, 0.45),inset -250px 0 250px -125px rgba(167, 32, 32, 0.45);
    font-family: monospace;
    display: block;
    line-height: 18px;
    margin-left: 166px;
    padding: 6px 8px;
    text-align: center;
    height: 18px;
}
</style>

<div class="f1">
    <div class="f1_1">{$infoArray['message']}</div>
    <div class="f1_2">
        <div class="f1_2_1">Строка: {$infoArray['line']}</div>
        <div class="f1_2_2">Файл: {$infoArray['file']}</div>
    </div>
</div>
HTML;

    }

}