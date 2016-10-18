<?php

namespace Modules\translit\translitInterface;

interface TranslitInterface {
    public function strTrVar();
    public function strReplaceVar( $from, $to );
    public function pregReplaceVar( $from, $to );
    public function trimVar( $from );
    public function setVar( $var );
    public function getVar();
}