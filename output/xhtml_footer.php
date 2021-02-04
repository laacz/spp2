        </div>
        <div id="sidebar">
            
            <h2>Sveiks, ceļiniek!</h2>
            
            <p>Tu atrodies Kaspara Foigta personiskajā lapā. Te tu vari atrast (un, protams, arī neatrast) saites un domas par tīmekļa tehnoloģijām, dzīvi, ziemeļbriežiem un <a href="<?=sppCfgVar('PATH')?>/search?q=tatu">T.a.t.U.</a><br />
            <a href="#" class="readmore">Lasīt vēl</a></p>
            
            <form action="#">
                <h2><label for="q">Lāčuvista</label></h2>
                <p><input type="text" class="text" id="q" name="q" value="q" />
                <input type="submit" class="submit" value="Aidā" /></p>
            </form>

            <h2>Tūļi</h2>
            
            <ul>
                <li><a href="#">Translita konvertors</a></li>
                <li><a href="#">Lietvārdu locītājs</a></li>
                <li><a href="#">Formu satura autoseivs iekš JS</a></li>
                <li><a href="#">Quick lookup iekš JS</a></li>
            </ul>
        
            <h2>Arhīvs</h2>
            
            <ul><? foreach ($archive_months as $month) { ?>
           
                <li><a href="<?=$month->permlink?>"><?=$month->year?>. gada <?=$locale['monthsLong'][$month->month]?></a></li>
            <? } ?></ul>
        
        </div>

        <div id="footer">
        <p>
            <a href="#">Par projektu</a>
            &middot;
            <a href="#">Reklāma</a>
            &middot;
            <a href="#">Kontakti</a>
        </p>
        
        <p>
            Manis paša radīts ir šis lērums viss. Izdomāts (bet ne zīmēts), glaudīts, lamāts sists. 1996-2004<br />
            Visas tiesības ir&hellip; nu, jūs jau zināt, kur&hellip;<br />
            Spēcināts ar SPP 3.0b (prerelīze)<br />
            Ģenerācijas laiks: <? $timer->stop(); echo number_format($timer->timeElapsed(), 2); ?>s.
        </p>
        
        </div>

    </div>
</body>
</html>