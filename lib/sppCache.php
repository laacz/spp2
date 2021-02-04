<?php

function sppCacheLoad($id) {
    if (!sppCfgVar('CACHE_ENABLED')) return false;
    global $timer;
    $fname = sppCfgVar('CACHE_DIR') . '/' . $id;
    if (file_exists($fname) &&
        (filemtime($fname) + sppCfgVar('CACHE_TIMEOUT') > time())) {
        if ($data = @file_get_contents($fname)) {
            $timer->setMarker('Cache hit for ' . $id);
            return @unserialize($data);
        }
    }        
    $timer->setMarker('Cache timed out ' . $id);
    return false;
}

function sppCacheSave($id, $value) {
    if (!sppCfgVar('CACHE_ENABLED')) return false;
    if ($fh = @fopen(sppCfgVar('CACHE_DIR') . '/' . $id, 'w')) {
        if (@fwrite($fh, serialize($value))) {
            return @fclose($fh);
        }
    }
    return false;
}

function sppCacheRemove($id) {
    if (!sppCfgVar('CACHE_ENABLED')) return false;
    return @unlink(sppCfgVar('CACHE_DIR') . '/' . $id);
}

function sppCacheGenId() {
    $str = '';
    foreach (func_get_args() as $v) {
        $str .= '_' . sppTransliterate((string)$v);
    }
    $str = substr($str, 1, 65);
    return $str;
}


?>