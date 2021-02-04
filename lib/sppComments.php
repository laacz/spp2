<?php

$name       = isset($_POST['name'])      ? $_POST['name']         : false;
$email      = isset($_POST['email'])     ? $_POST['email']        : false;
$url        = isset($_POST['url'])       ? $_POST['url']          : false;
$content    = isset($_POST['comment'])   ? $_POST['comment']      : false;
$hash       = isset($_POST['hash'])      ? $_POST['hash']         : false;
$preview    = isset($_POST['preview'])   ? $_POST['preview']      : false;
$submit     = isset($_POST['submit'])    ? $_POST['submit']       : false;
$previewed  = isset($_POST['previewed']) ? $_POST['previewed']    : $preview;
$remember   = isset($_POST['remember'])  ? $_POST['remember']     : false;
$forget     = false;
$commenting = $preview || $submit;

$name  = $name  || $name == ''  ? $name  : (isset($_COOKIE['spp_name']) ? $_COOKIE['spp_name'] : false);
$email = $email || $email == '' ? $email : (isset($_COOKIE['spp_email']) ? $_COOKIE['spp_email'] : false);
$url   = $url   || $url == ''   ? $url   : (isset($_COOKIE['spp_url']) ? $_COOKIE['spp_url'] : false);

$error = false;

if ($commenting) {
    
    if ($remember) {
        setcookie('spp_name',  $name,  time() + sppCfgVar('COOKIES_EXPIRATION'), sppCfgVar('PATH'));
        setcookie('spp_email', $email, time() + sppCfgVar('COOKIES_EXPIRATION'), sppCfgVar('PATH'));
        setcookie('spp_url',   $url,   time() + sppCfgVar('COOKIES_EXPIRATION'), sppCfgVar('PATH'));
        $remember = true;
    } else {
        setcookie('spp_name',  false, time() - 24 * 60 * 60, sppCfgVar('PATH'));
        setcookie('spp_email', false, time() - 24 * 60 * 60, sppCfgVar('PATH'));
        setcookie('spp_url',   false, time() - 24 * 60 * 60, sppCfgVar('PATH'));
        $remember = false;
        $forget = true;
    }
    
    $comment = new stdClass();
    $comment->entry_id = $post->id;
    $comment->author_ip = getenv('REMOTE_ADDR');
    $comment->author_name = $name;
    $comment->author_email = $email;
    $comment->author_url = $url;
    $comment->content = $content;
    $comment->created = time();
    $comment->status = 'published';
    $comment = sppSetUpComment($comment, false);
    
    $output = 'comment';
    if ($name && $content) {
        
        if ($submit) {
            if (sppCfgVar('COMMENTS_PREVIEW_REQUIRED')) {
                if ($previewed) {
                    sppAddComment($DB, $comment);
                    header('Location: ' . $post->permlink);
                } else {
                    $error = 'Pirms pievienot komentāru, tas ir jāapskata!!!';
                }
            } else {
                sppAddComment($DB, $comment);
                header('Location: ' . $post->permlink);
            }
        }
        
    } else {
        if (!$name && !$content) {
            $error = 'Tu neesi ievadījis savu vārdu (iesauku) un pašu komentāru!';
        } else if (!$name) {
            $error = 'Tu neesi ievadījis savu vārdu (iesauku)!';
        } else if (!$content) {
            $error = 'Tu neesi ievadījis pašu komentāru!';
        }
    }
    
    $comment->content = sppFormat($comment->content, 'plain');
    
}

$remember = $remember ? $remember : ($forget ? false : (isset($_COOKIE['spp_name']) || isset($_COOKIE['spp_email']) || isset($_COOKIE['spp_url'])));

?>