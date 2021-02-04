<?php

# Initializes locale $name (lv_LV, en_US, ...)
function sppLocale($name) {
    global $locale;
    require_once(sppCfgVar('DIR') . '/lib/locales/' . $name . '.php');
}

# Outputs date and/or time according to specified $format.
# $time can be either string or UNIX timestamp. If it is
# string, it should be parseable by strtotime();
function sppLocaleFormat($format, $time) {
    global $locale;
    $parts = sppLocaleParse($time);

    $str = $format;
    
    $str = str_replace('%yyyy', $parts->year, $str);
    $str = str_replace('%yyy', $parts->year, $str);
    $str = str_replace('%yy', $parts->year % 100, $str);
    $str = str_replace('%y', $parts->year % 10, $str);

    $str = str_replace('%dddd', $locale['weekDaysLong'][$parts->weekday], $str);
    $str = str_replace('%ddd', $locale['weekDaysShort'][$parts->weekday], $str);
    $str = str_replace('%dd', str_pad($parts->date, 2, '0', STR_PAD_LEFT), $str);
    $str = str_replace('%d', (int)$parts->date, $str);

    $str = str_replace('%MMMM', $locale['monthsLong'][(int)$parts->month], $str);
    $str = str_replace('%MMM', $locale['monthsShort'][(int)$parts->month], $str);
    $str = str_replace('%MM', str_pad((int)$parts->month, 2, '0', STR_PAD_LEFT), $str);
#    echo '['. str_pad((int)$parts->month, 2, '0', STR_PAD_LEFT) . ']';
    $str = str_replace('%M', (int)$parts->month, $str);

    $str = str_replace('%zzz', ($parts->timezone < 0 ? '-' : '') . str_pad((int)($parts->timezone / 60 / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad((int)($parts->timezone / 60) % 60, 2, '0', STR_PAD_LEFT), $str);
    $str = str_replace('%zz', ($parts->timezone < 0 ? '-' : '') . str_pad((int)($parts->timezone / 60 / 60), 2, '0', STR_PAD_LEFT), $str);
    $str = str_replace('%z', ($parts->timezone < 0 ? '-' : '') . (int)($parts->timezone / 60 / 60), $str);
    
    $str = str_replace('%hh', str_pad($parts->hours % 12, 2, '0', STR_PAD_LEFT), $str);
    $str = str_replace('%h', (int)$parts->hours % 12, $str);

    $str = str_replace('%HH', str_pad($parts->hours, 2, '0', STR_PAD_LEFT), $str);
    $str = str_replace('%H', (int)$parts->hours, $str);

    $str = str_replace('%mm', str_pad($parts->minutes, 2, '0', STR_PAD_LEFT), $str);
    $str = str_replace('%m', (int)$parts->minutes, $str);

    $str = str_replace('%ss', str_pad($parts->seconds, 2, '0', STR_PAD_LEFT), $str);
    $str = str_replace('%s', (int)$parts->seconds, $str);

    $str = str_replace('%tt', $parts->hours > 12 ? $locale['PM'] : $locale['AM'], $str);
    $str = str_replace('%t', $parts->hours > 12 ? $locale['shortPM'] : $locale['shortAM'], $str);

    $str = str_replace('%gg', $parts->year >= 0 ? $locale['ad'] : $locale['bd'], $str);

    return $str;
}

# Returns Object containing different parts of supplied datetime
# $time can be either string or UNIX timestamp. If it is
# string, it should be parseable by strtotime();
function sppLocaleParse($timestamp) {
    $ts = is_string($timestamp) ? strtotime($timestamp) : $timestamp;
    $parts = date('Y|||m|||d|||H|||i|||s|||w|||Z', $ts);
    $q = new stdClass();
    list ($q->year, $q->month, $q->date, $q->hours, $q->minutes, $q->seconds, $q->weekday, $q->timezone) = explode('|||', $parts);
    return $q;
}


?>