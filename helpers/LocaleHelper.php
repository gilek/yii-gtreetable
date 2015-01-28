<?php

/**
* @link https://github.com/gilek/yii-gtreetable
* @copyright Copyright (c) 2015 Maciej Kłak
* @license https://github.com/gilek/yii-gtreetable/blob/master/LICENSE
*/

class LocaleHelper
{
    public static function normalize($locale) {
        $locale = strtolower(str_replace('_','-', $locale));
        if ($pos = strpos($locale, '-') !== false) {
            $a = substr($locale, 0, $pos+1);
            $b = strtoupper(substr($locale, $pos+2));
            return $a.'-'.$b;
        }
        return $locale;
    }
}
