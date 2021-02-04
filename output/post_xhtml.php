<?php
header('content-type: text/html; charset=utf-8');
require('xhtml_head.php');
?>

            <div class="entry">

                <h2><a href="<?=$post->permlink?>"><?=$post->title?></a></h2>
                
                <ul class="entrydesc">
                    <li class="created">
                    <?=sppLocaleFormat('%yyyy. gada', $post->created)?>
                    <a href="<?=$post->daylink?>"><?=sppLocaleFormat('%d. %MMMM', $post->created)?></a>,
                    <?=sppLocaleFormat('%hh:%mm', $post->created)?>
                    </li>
                    <li class="comments"><a href="<?=$post->permlink?>#comments">Komentāri (<?=$post->comments_count?>)</a></li>
                </ul>

                <?=$post->content?>
                
                <? if ($post->has_content_more) { ?>
                
                    <div id="more">
                    
                        <?=$post->content_more?>
                    
                    </div>
                    
                <? } ?>

            <div id="comments">
                <h2>Komentāri</h2>
                
                <? if (!count($comments)) { ?>
                
                    <p>Pagaidām teitan nav neviena komentāra (vai arī visi ir izdzēsti:)</p>
                
                <? } else { ?>
                
                    <ol id="commentslist">
            
                        <?
                        $counter = 0;
                        foreach ($comments as $comment) {
                            $counter++; 
                            $class = Array();
                            if ($counter % 2 == 1) $class[] = 'odd';
                            if ($comment->superb == 'yes') $class[] = 'superb';
                            $class = count($class) ? join($class, ' ') : false;
                        ?>
                        <li id="c<?=$comment->id?>"<?=$class ? ' class="' . $class . '"' : ''?>>
                        <dl>
                            <dd class="author">
                                <? if ($comment->has_author_url) { ?>
                                    <a href="<?=$comment->author_url?>"><?=$comment->author_name?></a>
                                <? } else { ?>
                                    <?=$comment->author_name?>
                                <? } ?>
    
                            </dd>
                            
                            <dt>Laiks:</dt>
                                <dd class="posted">
                                    <a href="<?=$post->permlink?>#c<?=$comment->id?>"><?=sppLocaleFormat('%yyyy. gada %d. %MMMM, %hh:%mm', $comment->created)?></a>
                                </dd>
                                
                            <dt>Komentārs:</dt>
                                <dd class="comment">
                                    <?=$comment->content?>
                                    
                                </dd>
                        </dl>
                        </li>
                        
                        <? } ?>
        
                    </ol>
                <? } ?>
    
                <h2>Ierakstīt savu sakāmo</h2>
    
                <? require('comments_form.php'); ?>
            
            </div>


            </div>

<?
require('xhtml_footer.php');
?>