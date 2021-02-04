<?php

require_once 'Benchmark/Timer.php';
$timer = new Benchmark_Timer();
$timer->start();

#header('content-type: text/plain; charset=utf-8');
error_reporting(E_ALL);

require_once('config.php');

require_once('DB.php');
$timer->setMarker('require DB.php');
require_once('lib/sppConfig.php');
$timer->setMarker('require sppConfig.php');
require_once('lib/sppLocale.php');
$timer->setMarker('require sppLocale.php');
require_once('lib/sppTranslitTable.php');
$timer->setMarker('require sppTranslitTable.php');
require_once('lib/sppCache.php');
$timer->setMarker('require sppCache.php');


#$convEngine = convEngine();
sppLocale(LOCALE);
#$locale = new sppLocale(LOCALE);
$timer->setMarker('sppLocale(' . LOCALE . ')');


function sppHash($str) {
    return md5(sppCfgVar('COMMENTS_PASSPHRASE') . $str);
}

function sppTransliterate($str, $tolowercase = true) {
    global $translitTable;
    
    $s = $str;
    $s = trim($str);
    foreach ($translitTable as $from=>$to) {
        $s = str_replace($from, $to, $s);
    }
    $s = preg_replace('/[^a-zA-Z09\_]/', '_', $s);
    
    while (strpos($s, '__') !== false) {
        $s = str_replace('__', '_', $s);
    }
    
    if ($tolowercase) {
        $s = strtolower($s);
    }
    
    return $s;
}

function sppError($msg, $die = false) {
    echo $msg;
    if ($die) die();
}

function sppCfgVar($key, $default = false) {
    return defined($key) ? constant($key) : $default;
}

function sppParseUri() {
    if (sppCfgVar('CLEAN_URIS')) {
        $uri = getenv('REQUEST_URI');
        $uri = str_replace(sppCfgVar('PATH'), '', $uri);
    } else {
        $uri = isset($_GET['uri']) ? $_GET['uri'] : '';
    }
    $uri = preg_replace('/^\/+/', '', $uri);
    $uri = preg_replace('/\/+$/', '', $uri);
    $uri = preg_replace('/\?.*$/', '', $uri);
    return $uri;
    #return explode('/', $uri);
}

function sppGetPostsRecent(&$DB) {
    $cacheId = 'posts_recent';
    if ($posts = sppCacheLoad($cacheId)) {
        return $posts;
    }
    $sqlstr = '
    SELECT
        *
    FROM
        ' . sppCfgVar('DB_PREFIX') . 'entries
    WHERE
        status = ' . $DB->quoteSmart('published') . ' OR (
            status = ' . $DB->quoteSmart('future') . ' AND
            created < ' . $DB->quoteSmart(date('Y-m-d H:i:s')) . '
        )
    ORDER BY
        created DESC
    ';
    $rs = $DB->limitQuery($sqlstr, 0, $DB->quoteSmart(sppCfgVar('RECENT_POSTS')));
    $posts = Array();
    
    if (!PEAR::isError($rs)) {
        while ($row = $rs->fetchRow()) {
            $posts[] = sppSetUpPost($row);
        }
        sppCacheSave($cacheId, $posts);
        return $posts;
    }
    return false;
}

function sppGetPostsByDate(&$DB, $year, $month, $day = false) {
    
    $cacheId = 'posts_recent_' . $year . '_' . $month . '_' . $day;
    if ($posts = sppCacheLoad($cacheId)) {
        return $posts;
    }

    $datestr = '(
            EXTRACT(YEAR FROM created) = ' . $DB->quoteSmart($year) . ' AND
            EXTRACT(MONTH FROM created) = ' . $DB->quoteSmart($month) . '';
    if ($day) {
        $datestr .= ' AND
            EXTRACT(DAY FROM created) = ' . $DB->quoteSmart($day) . '';
    }
    $datestr .= '
        )';
    
    $sqlstr = '
    SELECT
        *
    FROM
        ' . sppCfgVar('DB_PREFIX') . 'entries
    WHERE
        (
            status = ' . $DB->quoteSmart('published') . ' OR
            status = ' . $DB->quoteSmart('future') . '
        ) AND ' . $datestr . '
    ORDER BY
        created DESC
    ';
    $rs = $DB->query($sqlstr);
    $posts = Array();
    
    
    if (!PEAR::isError($rs)) {
        while ($row = $rs->fetchRow()) {
            $posts[] = sppSetUpPost($row);
        }
        sppCacheSave($cacheId, $posts);
        return $posts;
    }
    return false;
}

function sppGetPostByDateId(&$DB, $year, $month, $day, $permlink) {
    
    #$datestr = $year . '-' . $month . '-' . $day;
    
    $sqlstr = '
    SELECT
        *
    FROM
        ' . sppCfgVar('DB_PREFIX') . 'entries
    WHERE
        (
            status = ' . $DB->quoteSmart('published') . ' OR
            status = ' . $DB->quoteSmart('future') . '
        ) AND
        EXTRACT(YEAR FROM created)  = ' . $DB->quoteSmart($year) . ' AND
        EXTRACT(MONTH FROM created) = ' . $DB->quoteSmart($month) . ' AND
        EXTRACT(DAY FROM created)   = ' . $DB->quoteSmart($day) . ' AND
        nice_name = ' . $DB->quoteSmart($permlink) . '
    ';
    
#    echo myDump($sqlstr, false);
    
    $post = $DB->getRow($sqlstr);
    
    if ($post) {
        $post = sppSetUpPost($post);
        return $post;
    }
    
    return false;
    
}

function myDump($var, $html = true) {
    $ret = '';
    if ($html) {
        $ret .= '<pre style="text-align: left;background-color: #fff; color: #000; border: 1px solid #c00; font-size: 10pt; font-family: courier new;">';
        $ret .= htmlspecialchars(print_r($var, true));
        $ret .= '</pre>';
    } else {
        $ret .= print_r($var, true);
    }
        
    return $ret;
}

function sppSetUpPost($post) {
    $entry = $post;
    $entry->permlink = sppCfgVar('PATH') . '/' . str_replace('-', '/', substr($entry->created, 0, 10)) . '/' . $entry->nice_name;

    $entry->daylink = sppCfgVar('PATH') . '/' . str_replace('-', '/', substr($entry->created, 0, 10));
    $entry->monthlink = sppCfgVar('PATH') . '/' . str_replace('-', '/', substr($entry->created, 0, 7));
    $entry->yearlink = sppCfgVar('PATH') . '/' . str_replace('-', '/', substr($entry->created, 0, 4));

    $entry->description = sppFormat($entry->description, $entry->type);

    $entry->content = sppFormat($entry->content, $entry->type);

    $entry->has_content_more = strlen(trim($entry->content_more)) > 0;
    $entry->content_more = sppFormat($entry->content_more, $entry->type);
    
    return $entry;
    
}

function sppSetUpComment($comment, $formatContent = true) {
    $entry = $comment;
    
    $entry->has_author_url = strlen(trim($entry->author_url)) != 0;
    $entry->created = isset($entry->created) ? (!is_string($entry->created) ? date('Y-m-d H:i:s', $entry->created): $entry->created) : date('Y-m-d H:i:s');
    $entry->modified = isset($entry->modified) ? (!is_string($entry->modified) ? date('Y-m-d H:i:s', $entry->modified): $entry->modified) : date('Y-m-d H:i:s');
    $entry->permlink = strlen(trim($entry->author_url)) != 0;
    if ($formatContent) {
        $tmp = explode("\n", $entry->content);
        foreach ($tmp as $k=>$v) {
            $tmp[$k] = trim($v);
        }
        $entry->content = join($tmp, "\n");

        while (strpos($entry->content, "\n\n\n")) {
            $entry->content = str_replace("\n\n\n", "\n\n", $entry->content);
        }
        $entry->content = sppFormat($entry->content, 'plain');
    }
    
    return $entry;
    
}

function sppGetArchiveMonths(&$DB) {
    # Note: "EXTRACT(part FROM field)" is SQLstandard
    $cacheId = 'archive_months';

    if ($months = sppCacheLoad($cacheId)) {
        return $months;
    }

    $sqlstr = '
    SELECT DISTINCT
        EXTRACT(YEAR FROM created) AS year,
        EXTRACT(MONTH FROM created) AS month
    FROM
        ' . sppCfgVar('DB_PREFIX') . 'entries
    WHERE
        status = ' . $DB->quoteSmart('published') . ' OR
        (status = ' . $DB->quoteSmart('future') . ' AND created < ' . $DB->quoteSmart(date('Y-m-d H:i:s')) . ')
    ORDER BY
        year DESC,
        month DESC
    ';

    if ($months = $DB->getAll($sqlstr)) {
        foreach ($months as $k=>$v) {
            $v->permlink = sppCfgVar('PATH') . '/' . $v->year . '/' . str_pad($v->month, 2, '0', STR_PAD_LEFT);
            $months[$k] = $v;
        }
        sppCacheSave($cacheId, $months);
        return $months;
    }

    return Array();
}


function sppGetCommentsByPostId(&$DB, $id) {
    
    $cacheId = 'comments_' . $id;

    if ($comments = sppCacheLoad($cacheId)) {
        return $comments;
    }

    $sqlstr = '
    SELECT
        *
    FROM
        ' . sppCfgVar('DB_PREFIX') . 'comments
    WHERE
        entry_id = ' . $DB->quoteSmart($id) . ' AND
        status = ' . $DB->quoteSmart('published') . '
    ORDER BY
        created ASC
    ';
    $rs = $DB->query($sqlstr);
    $comments = Array();
    
    if (!PEAR::isError($rs)) {
        while ($row = $rs->fetchRow()) {
            $comments[] = sppSetUpComment($row);
        }
        sppCacheSave($cacheId, $comments);
        return $comments;
    }
    return false;
}

function sppAddComment(&$DB, $comment) {
    
    $cacheId = 'comments_' . $id;
    sppCacheRemove($cacheId);
    $sqlstr = '
    INSERT INTO
        ' . sppCfgVar('DB_PREFIX') . 'comments
    (
        entry_id,
        author_ip,
        author_name,
        author_email,
        author_url,
        content,
        created,
        modified,
        status
    ) VALUES (
        ' . $DB->quoteSmart($comment->entry_id) . ',
        ' . $DB->quoteSmart($comment->author_ip) . ',
        ' . $DB->quoteSmart($comment->author_name) . ',
        ' . $DB->quoteSmart($comment->author_email) . ',
        ' . $DB->quoteSmart($comment->author_url) . ',
        ' . $DB->quoteSmart($comment->content) . ',
        ' . $DB->quoteSmart($comment->created) . ',
        NULL,
        ' . $DB->quoteSmart($comment->status) . '
    )
    ';
    $rs = $DB->query($sqlstr);

    $sqlstr = '
    SELECT
        COUNT(id)
    FROM
        ' . sppCfgVar('DB_PREFIX') . 'comments
    WHERE
        entry_id = ' . $DB->quoteSmart($comment->entry_id) . ' AND
        status = ' . $DB->quoteSmart('published') . '
    ';
    $count = $DB->getOne($sqlstr);

    $sqlstr = '
    UPDATE
        ' . sppCfgVar('DB_PREFIX') . 'entries
    SET
        comments_count = ' . $DB->quoteSmart($count) . '
    WHERE
        id = ' . $DB->quoteSmart($comment->entry_id) . '
    ';
    $rs = $DB->query($sqlstr);
    return !PEAR::isError($rs);
}

function sppFormat($str, $type) {
    $res = trim($str);
    switch ($type) {
        case 'plain' :
            $res = htmlspecialchars($res);
            $res = preg_replace_callback('/((http(s?):\/\/)|(www\.))([\:\/#\?=&\+%;\-\w\.\~\^]+)/i', 'sppUrlCallback', $res);
            $res = '<p>' . str_replace("\n", '<br />', $res) . '</p>';
            break;
    }
    return $res;
}

function sppUrlCallback($m) {
    $maxlength = 40;

    # Convert back from htmlspecialchars all &amp;s to &
    $m[0] = str_replace('&amp;', '&', $m[0]);
    # Url will be used later
    $url = $m[0];
    # Convert all & to &amp;'s, but preserver already existing &amp;'s
    $m[0] = preg_replace('/&(?!amp;)/', '&amp;\1', $m[0]);

    if (strlen($url) > $maxlength) {
        $url = htmlspecialchars(substr($url, 0, $maxlength)) . '&hellip;';
    }
    
    $return = '<a href="' . $m[0] . '" title="' . $m[0] . '"';
    $return .= '>' . $url . '</a>';
    
    return $return;
}


#echo setlocale(LC_ALL, 0);
#setlocale(LC_ALL, sppCfgVar('LOCALE'));

#$config = new sppConfig($config);
$timer->setMarker('Start processing');

$DB = DB::Connect(sppCfgVar('DB_DSN'));
if (PEAR::isError($DB)) {
    sppError($DB->getMessage(), true);
}

$DB->setFetchMode(DB_FETCHMODE_OBJECT);
$DB->query('SET character_set_connection = utf8');
$DB->query('SET character_set_client = utf8');
$DB->query('SET character_set_results = utf8');
$timer->setMarker('DB connected and set up');

$uri = sppParseUri();
$timer->setMarker('Parsed URI');

$archive_months = sppGetArchiveMonths($DB);
$timer->setMarker('Got archive months');

$output = '404';

if (preg_match('/^(atom|rss)?$/', $uri, $matches)) {
    $otype = str_replace('/', '', isset($matches[1]) ? $matches[1] : 'xhtml');
    $output = 'index_' . $otype . '';
    
    $posts = sppGetPostsRecent($DB);
    $timer->setMarker('Got recent posts');
    
} else if (preg_match('/^(\d{4})$/', $uri, $matches)) {
    $output = 'year';
} else if (preg_match('/^(\d{4})\/(\d{1,2})$/', $uri, $matches)) {
    $posts = sppGetPostsByDate($DB, $matches[1], $matches[2]);
    if ($posts) $output = 'month';
    $timer->setMarker('Got posts by date (month)');
} else if (preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/', $uri, $matches)) {
    $posts = sppGetPostsByDate($DB, $matches[1], $matches[2], $matches[3]);
    if ($posts) $output = 'day';
    $timer->setMarker('Got posts by date');
} else if (preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})\/([a-z0-9_\-]+)(\/comments)?(\/rss|\/atom)?$/i', $uri, $matches)) {
    $date = checkdate($matches[2], $matches[3], $matches[1]);
    if ($date) {
        $otype = str_replace('/', '', isset($matches[6]) ? $matches[6] : 'xhtml');
        $hide_post = isset($matches[5]) && $matches[5] == '/comments';
        $post = spPGetPostByDateId($DB, $matches[1], $matches[2], $matches[3], $matches[4]);        
        $timer->setMarker('Got post by date and id');
        if ($post) {
            $comments = sppGetCommentsByPostId($DB, $post->id);
            $timer->setMarker('Got comments');
            $output = 'post_' . $otype;
            require_once('lib/sppComments.php');
            $timer->setMarker('Processed sppComments.php');
        }
    }
}
$timer->setMarker('Done main processing');

$file = 'output/' . $output . '.php';
if (file_exists($file)) {
    require($file);
} else {
    die('cannot open ' . $file);
}
$timer->stop();
#echo '<div style="text-align: left; background-color: #fff;">';
#$timer->display();
#echo '</div>';


?>
