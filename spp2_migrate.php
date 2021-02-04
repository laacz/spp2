<?php

set_time_limit(0);
require_once('config.php');
header('content-type: text/plain; charset=utf-8');
require_once('DB.php');
$DB =& DB::Connect(DB_DSN);
$DB->setFetchMode(DB_FETCHMODE_OBJECT);

if (isset($_GET['articles'])) {

    $sqlstr = 'SELECT * FROM articles ORDER BY id DESC';
    $articles = $DB->getAll($sqlstr);
    echo count($articles) . ' article(s)' . "\n";
    
    $DB->query('SET character_set_connection = utf8');
    $DB->query('SET character_set_client = utf8');
    $DB->query('SET character_set_results = utf8');
    $i = 0;
    foreach ($articles as $a) {
        $a->subject = trim(strip_tags($a->subject));
        
        $sqlstr = '
        INSERT INTO
            spp_entries
        SET
            id = ' . $DB->quoteSmart($a->id) . ',
            title = ' . iconv('windows-1257', 'utf-8', $DB->quoteSmart($a->subject)) . ',
            description = ' . $DB->quoteSmart('') . ',
            content = ' . iconv('windows-1257', 'utf-8', $DB->quoteSmart($a->content)) . ',
            content_more = ' . $DB->quoteSmart('') . ',
            created = ' . $DB->quoteSmart($a->date . ' ' . $a->time) . ',
            modified = ' . $DB->quoteSmart(NULL) . ',
            comments_count = ' . $DB->quoteSmart(0) . ',
            nice_name = ' . $DB->quoteSmart($a->permlink) . ',
            status = ' . $DB->quoteSmart((int)$a->status ? 'published' : 'draft') . ',
            type = ' . $DB->quoteSmart('xhtml') . '
        ';
        #echo $sqlstr;
        $DB->query($sqlstr);
        echo mysql_error();
        $i++;
        if ($i % 100 == 0) {
            echo $i . ' out of ' . count($articles) . ' done' . "\n";
            flush();
        }
    }

}

if (isset($_GET['blabla'])) {

    $sqlstr = 'SELECT * FROM blabla ORDER BY id DESC';
    $comments = $DB->getAll($sqlstr);
    echo count($comments) . ' comment(s)' . "\n";
    $DB->query('SET character_set_connection = utf8');
    $DB->query('SET character_set_client = utf8');
    $DB->query('SET character_set_results = utf8');
    
    $i = 0;
    foreach ($comments as $c) {
        
        $q = explode('-', $c->ip);
        $c->ip = $q[0];
        
        $sqlstr = '
        INSERT INTO
            spp_comments
        SET
            id = ' . $DB->quoteSmart($c->id) . ',
            entry_id = ' . $DB->quoteSmart($c->objectid) . ',
            author_ip = ' . $DB->quoteSmart($c->ip) . ',
            author_name = ' . iconv('windows-1257', 'utf-8', $DB->quoteSmart($c->nick)) . ',
            author_email = ' . iconv('windows-1257', 'utf-8', $DB->quoteSmart($c->mail)) . ',
            author_url = ' . $DB->quoteSmart(NULL) . ',
            content = ' . $DB->quoteSmart(iconv('windows-1257', 'utf-8', $c->body)) . ',
            created = ' . $DB->quoteSmart($c->date) . ',
            superb = ' . $DB->quoteSmart($c->superb) . ',
            modified = ' . $DB->quoteSmart(NULL) . ',
            status = ' . $DB->quoteSmart((int)$c->status ? 'published' : 'deleted') . '
        ';
        #echo $sqlstr;
        $DB->query($sqlstr);
        if (mysql_error()) {
            echo "\n" . mysql_error() . "\n";
            echo "\n" . $sqlstr . "\n";
        }
        $i++;
        if ($i % 1000 == 0) {
            echo $i . ' out of ' . count($comments) . ' done' . "\n";
            flush();
        }
    }

}

if (isset($_GET['blablacount'])) {

    $sqlstr = '
    SELECT
        COUNT(c.id) AS cnt,
        a.id AS aid
    FROM
        spp_comments c,
        spp_entries a
    WHERE
        a.id = c.entry_id AND
        a.status = \'published\'
    GROUP BY
        aid
    ';
    $commcnt = $DB->getAll($sqlstr);
    echo count($commcnt) . ' commented entry(s)' . "\n";
    
    $i = 0;
    foreach ($commcnt as $c) {
        
        $sqlstr = 'UPDATE spp_entries set comments_count = ' . $c->cnt . ' WHERE id = ' . $c->aid;
        $DB->query($sqlstr);
        $i++;
        if ($i % 100 == 0) {
            echo $i . ' out of ' . count($commcnt) . ' done' . "\n";
            flush();
        }
    }

}
?>