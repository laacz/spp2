<?php
header('content-type: text/html; charset=utf-8');
require('xhtml_head.php');
?>

            <div class="entry">

            <div id="comments">
                <h2>Komentāra pievienošana</h2>
                
                <? if ($error) { ?>
                <p class="error"><?=$error?></p>
                <? } ?>

                <ol id="commentslist">
        
                    <li id="c<?=$comment->id?>" class="odd">
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
    
                </ol>
    
                <h2>Ierakstīt savu sakāmo</h2>
    
                <? require('comments_form.php'); ?>
            </div>


            </div>

<?
require('xhtml_footer.php');
?>