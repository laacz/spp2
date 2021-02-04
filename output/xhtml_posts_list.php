<? foreach ($posts as $post) { ?>
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
                    <p><a href="<?=$post->permlink?>#more" class="readmore">Lasīt tālāk</a></p>
                <? } ?>

            </div>
<? } ?>
