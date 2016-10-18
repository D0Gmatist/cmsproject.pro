<?php

/*
$var = '123123-ad_as dsadADAWD Wфыв/.фйцукывффффф.в,ы""ФЦЫВФ В!@#$ %^&*()';
$tr = new Translit();
$tr->setVar( $var );
$tr->strReplaceVar();
$tr->strReplaceVar( ' ' );
$tr->pregReplaceVar();
$tr->strTrVar();
$tr->trimVar();
$tr->trimVar( '_' );
$var = $tr->getVar();
// result: 123123_ad_as_dsadADAWD_W
*/

namespace Modules\translit;

interface TranslitInterface {
    public function strTrVar();
    public function strReplaceVar( $from, $to );
    public function pregReplaceVar( $from, $to );
    public function trimVar( $from );
    public function setVar( $var );
    public function getVar();
}