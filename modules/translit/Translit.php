<?php

namespace Modules\translit;

class Translit implements TranslitInterface {
    /** @var string  */
    public $var = '';
    /** @var array  */
    public $fromTo = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ї' => 'yi', 'є' => 'ye',

        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
        'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K',
        'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
        'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya', 'Ї' => 'yi', 'Є' => 'ye',

        'À'=>'A', 'à'=>'a', 'Á'=>'A', 'á'=>'a', 'Â'=>'A', 'â'=>'a', 'Ä'=>'A', 'ä'=>'a',
        'Ã'=>'A', 'ã'=>'a', 'Å'=>'A', 'å'=>'a', 'Æ'=>'AE', 'æ'=>'ae', 'Ç'=>'C', 'ç'=>'c',
        'Ð'=>'D', 'È'=>'E', 'è'=>'e', 'É'=>'E', 'é'=>'e', 'Ê'=>'E', 'ê'=>'e', 'Ì'=>'I',
        'ì'=>'i', 'Í'=>'I', 'í'=>'i', 'Î'=>'I', 'î'=>'i', 'Ï'=>'I', 'ï'=>'i', 'Ñ'=>'N',
        'ñ'=>'n', 'Ò'=>'O', 'ò'=>'o', 'Ó'=>'O', 'ó'=>'o', 'Ô'=>'O', 'ô'=>'o', 'Ö'=>'O',
        'ö'=>'o', 'Õ'=>'O', 'õ'=>'o', 'Ø'=>'O', 'ø'=>'o', 'Œ'=>'OE', 'œ'=>'oe', 'Š'=>'S',
        'š'=>'s', 'Ù'=>'U', 'ù'=>'u', 'Û'=>'U', 'û'=>'u', 'Ú'=>'U', 'ú'=>'u', 'Ü'=>'U',
        'ü'=>'u', 'Ý'=>'Y', 'ý'=>'y', 'Ÿ'=>'Y', 'ÿ'=>'y', 'Ž'=>'Z', 'ž'=>'z', 'Þ'=>'B',
        'þ'=>'b', 'ß'=>'ss', '£'=>'pf', '¥'=>'ien', 'ð'=>'eth', 'ѓ'=>'r'
    );

    public function strTrVar() {
        $this->var = strtr( $this->var, $this->fromTo );

    }

    /**
     * @param string $from
     * @param string $to
     */
    public function strReplaceVar( $from = '-', $to = '_' ) {
        $this->var = str_replace( $from, $to, $this->var );

    }

    /**
     * @param string $from
     * @param string $to
     */
    public function pregReplaceVar( $from = "/[^a-z0-9\_]+/mi", $to = '' ) {
        $this->var = preg_replace( $from, $to, $this->var );

    }

    /**
     * @param string $from
     */
    public function trimVar( $from = ' ' ) {
        $this->var = trim( $this->var, $from );

    }
    
    /**
     * @param string $var
     */
    public function setVar( $var ) {
        $this->var = $var;
    }

    /**
     * @return string
     */
    public function getVar() {
        return $this->var;
    }

}