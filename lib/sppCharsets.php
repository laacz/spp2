<?php

function convEngine() {
    $extensionsExt = defined('PHP_OS') && strpos(PHP_OS, 'win') !== false ? 'dll' : 'so';
    if (function_exists('recode_string')) {
        return 'recode';
    } else if (function_exists('iconv')) {
        return 'iconv';
    } else if (function_exists('mb_convert_encoding')) {
        return 'mbstring';
    } else {
        if (extension_loaded('recode') || (!extension_loaded('recode') && !dl('recode.' . $extensionsExt))) {
            return 'recode';
        } else if (extension_loaded('iconv') || (!extension_loaded('iconv') && !dl('iconv.' . $extensionsExt))) {
            return 'iconv';
        } else if (extension_loaded('mbstring') || (!extension_loaded('mbstring') && !dl('mbstring.' . $extensionsExt))) {
            return 'mbstring';
        } else {
            return false;
        }
    }
}

function convCharset($from, $to, $string) {
    global $convEngine;
    if ($convEngine) {
        switch ($convEngine) {
            case 'recode':
                $result = recode_string($from . '..' . $to, $string);
                break;
            case 'iconv' :
                $result = iconv($from, $to, $string);
                break;
            case 'mbstring' :
                $result = mb_convert_encoding($string, $to, $from);
                break;
            default :
                $result = $string;
        }
    } else {
        $result = $string;
    }
    return $result ? $result : $string;
}

?>