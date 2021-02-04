                <form action="<?=getenv('REQUEST_URI')?>" method="post">
                    <dl>
                        <dt><label for="name">Kā Tu vēlies sevi dēvēt?</label></dt>
                        <dd><input type="text" name="name" class="text" value="<?=htmlspecialchars($name)?>" id="name" /></dd>
    
                        <dt><label for="email">Tavs e-pasts (neviens to neredzēs, izņemot mani)</label></dt>
                        <dd><input type="text" name="email" class="text" value="<?=htmlspecialchars($email)?>" id="email" /></dd>
    
                        <dt><label for="url">Mājaslapas adrese?</label></dt>
                        <dd><input type="text" name="url" class="text" value="<?=htmlspecialchars($url)?>" id="url" /></dd>
                        
                        <dt><label for="ccontent">Tavs komentārs:</label></dt>
                        <dd><textarea name="comment" class="text" id="ccontent" rows="5" cols="50"><?=htmlspecialchars($content)?></textarea></dd>
                        
                        <dd><input class="checkbox" type="checkbox" name="remember" id="remember" value="1" <?=$remember?'checked="checked"':''?> /> <label for="remember">Vai atcerēties Tavu vārdu, e-pastu un mājaslapas adresi?</label></dt>
                        <dd>
                        <input class="submit" type="submit" name="preview" value="Apskaties, pirms ievieto!" />
                        <? if (sppCfgVar('COMMENTS_PREVIEW_REQUIRED') && $previewed) { ?>
                            <input type="hidden" name="previewed" value="1" />
                            <input class="submit" type="submit" name="submit" value="Ievieto!" />
                        <? } ?>
                        </dd>
                        
                    </dl>
                    
                    <div class="help">

                        <h3>Formatēšana</h3>
                        <p>Iekļaujot tekstu no abām pusēm iekš <code>//</code>, tas iznāks kursīvā: <code>//teksts//</code> (<em>teksts</em>), bet treknu tekstu var dabūt ar <code>**</code> katrā pusē: <code>**teksts**</code> (<strong>teksts</strong>), savukārt pasvītrotu ar <code>__</code>: <code>__teksts__</code> (<u>teksts</u>).</p>

                        <p>Enteri tiek automātiski pārtaisīti par enteriem. Jebkurš <acronym title="HyperText Markup Language">HTML</acronym> (izņemot <code>&lt;br.*&gt;</code>) tiek parādīts, kā ievadīts (ne HTML'iski)</p>

                        <p>Jebkurš url'is (www.kaka.com, http://kaka.com/, &hellip;) tiek automātiski pārtaisīts par spiežamu prieku (<a href="http://www.kaka.com/">www.kaka.com</a>, <a href="http://kaka.com/">http://kaka.com/</a>, &hellip;)</p>

                    </div>
                    
                </form>
