<?php
App::import('Vendor', 'Autotranscription');

class AutotranscriptionTestCase extends CakeTestCase {
    function startTest() {
        $this->AT =& new Autotranscription();
    }

    function endTest() {
        unset($this->AT);
    }

    function assertRomaji($kanas, $expectedRomaji) {
        $result = $this->AT->tokenizedJapaneseWithReadingsToRomaji($kanas);
        $this->assertEqual($expectedRomaji, $result);
    }

    function testTokenizedJapaneseWithReadingsToRomaji() {
        $testReadings = array(
            /* Basic sentences */
            '[申|もう]し[訳|わけ]ありません' => 'mōshiwakearimasen',
            'あんまり [無理|むり] する な よ' => 'anmari muri suru na yo',

            /* Little tsus */
            'やった' => 'yatta',
            'やっった' => 'yatta',

            /* Punctuation */
            /* various common symbols */
            'あっ 、 すみません ！' => 'a, sumimasen!',
            '「 いつ から ？ 」 「 [今日|きょう] で [3|さん] [日|にち] [目|め] 」' => '"itsu kara?" "kyō de san nichi me"',
            '[二|ふた]つ  [空白|くうはく]' => 'futatsu  kūhaku',
            /* ・ and ＝ */
            'ジョン ・ スミス' => 'jon sumisu',
            'ジャン ＝ バティスト ・ ポクラン' => 'jan-batisuto pokuran',
            /* … */
            'そして 、 [山|やま] へ [向|む]かった … … 。' => 'soshite, yama e mukatta…….',
            'ん……' => 'n……',
            '[彼女|かのじょ] が … … [考|かんが]えた' => 'kanojo ga…… kangaeta',
            'なるほど 。 … … そう な の か 。' => 'naruhodo. …… sō na no ka.',
            '「 … 」' => '"…"',
            'あっ' => 'a…',
            /* ー and 〜 */
            '「 え ーー 、 [恥|は]ずかしい です よー 」' => '"eee, hazukashii desu yoo"',
            '「 オオーー ！ 」' => '"oooo!"',
            'いい なー 。' => 'ii naa.',
            'パートナー' => 'pātonā',
            'そう だ ぜー' => 'sō da zee',
            '[２|に] 〜 [３|さん] [週間|しゅうかん]' => 'ni~san shūkan',

            /* Long vowels (based on modified Hepburn) */
            /* a + a */
            'さあ'               => 'saa',    /* simple kana word */
            '[邪|じゃ][悪|あく]' => 'jaaku',  /* with word-border */
            '[場|ば][合|あい]'   => 'baai',
            'お[婆|ばあ]さん'    => 'obāsan', /* without word-border */
            '[麻雀|まあじゃん]'  => 'mājan',
            /* i + i */
            'お[兄|にい]さん' => 'oniisan',
            '[美味|おい]しい' => 'oishii',
            '[新潟|にいがた]' => 'niigata',
            '[灰色|はいいろ]' => 'haiiro',
            /* u + u */
            '[食|く]う'          => 'kuu',    /* verb terminal part */
            '[湖|みずうみ]'      => 'mizuumi',/* a special case */
            '[数|すう][学|がく]' => 'sūgaku',
            '[注|ちゅう][意|い]' => 'chūi',
            '[巨乳|ぎゅうにゅう]'=> 'gyūnyū',
            '[急速|きゅうそく]'  => 'kyūsoku',
            '[集団|しゅうだん]'  => 'shūdan',
            '[重要|じゅうよう]'  => 'jūyō',
            '[中心|ちゅうしん]'  => 'chūshin',
            '[誤謬|ごびゅう]'    => 'gobyū',
            'ぴゅう'             => 'pyū',
            'ぐうたら'           => 'gūtara',
            '[日向|ひゅうか]'    => 'hyūka',
            '[美勇士|みゅうじ]'  => 'myūji',
            '[憂鬱|ゆううつ]'    => 'yūutsu',
            '[留学|りゅうがく]'  => 'ryūgaku',
            '[人数|にんずう]'    => 'ninzū',
            /* e + e */
            '[濡|ぬ]れ[縁|えん]' => 'nureen',
            'お[姉|ねえ]さん'    => 'onēsan',
            /* o + o */
            '[小|こ][躍|おど]り' => 'koodori',  /* with word-border */
            '[雄|お][々|お]しい' => 'ooshii',   /* with word-border */
            '[氷|こおり]'        => 'kōri',     /* without word-border */
            '[遠|とお][回|まわ]り' => 'tōmawari',
            '[大|おお][阪|さか]' => 'ōsaka',
            '[通|どお]り'        => 'dōri',
            '[頬|ほお]'          => 'hō',
            '[真岡|もおか]'      => 'mooka',
            '[語音|ごおん]'      => 'goon',
            '[真岡|のおがた]'    => 'nōgata',
            /* o + u */
            '[追|お]う'          => 'ou',     /* verb terminal part */
            '[王子|おうじ]'      => 'ōji',
            '[動物|どうぶつ]'    => 'dōbutsu',
            '[合計|ごうけい]'    => 'gōkei',
            '[想像|そうぞう]'    => 'sōzō',
            '[能|のう]'          => 'nō',
            '[法律|ほうりつ]'    => 'hōritsu',
            '[冒険|ぼうけん]'    => 'bōken',
            'ぽうっと'           => 'pōtto',
            '[電報|でんぽう]'    => 'denpō',
            '[妄想|もうそう]'    => 'mōsō',
            '[利用|りよう]'      => 'riyō',
            '[火曜日|かようび]'  => 'kayōbi',
            '[東京|とうきょう]'  => 'tōkyō',
            '[浪人|ろうにん]'    => 'rōnin',
            '[格子|こうし]'      => 'kōshi',
            '[学校|がっこう]'    => 'gakkō',
            '[子|こ][馬|うま]'   => 'kouma',  /* with word-border */
            '[今日|きょう]'      => 'kyō',
            '[餃子|ぎょうざ]'    => 'gyōza',
            '[紹介|しょうかい]'  => 'shōkai',
            '[冗談|じょうだん]'  => 'jōdan',
            '[女房|にょうぼう]'  => 'nyōbō',
            '[表現|ひょうげん]'  => 'hyōgen',
            '[病気|びょうき]'    => 'byōki',
            '[発表|はっぴょう]'  => 'happyō',
            '[名字|みょうじ]'    => 'myōji',
            '[両方|りょうほう]'  => 'ryōhō',
            '[調子|ちょうし]'    => 'chōshi',
            /* e + i */
            '[学生|がくせい]'    => 'gakusei',
            /* others */
            '[軽|かるい]'        => 'karui',
            /* loanwords */
            'セーラー[服|ふく]' => 'sērāfuku',
            'パーティー'        => 'pātī',
            'スーパーマン'      => 'sūpāman',
            'コード'            => 'kōdo',

            /* n disambiguation case */
            '[案内|あんない]'   => 'annai',
            '[群馬|ぐんま]'     => 'gunma',
            '[金曜日|きんようび]' => 'kin\'yōbi',
            '[簡易|かんい]'     => 'kan\'i',
            '[信用|しんよう]'   => 'shin\'yō',
            'オンエア'          => 'on\'ea',
            'ゴールデンウィーク'=> 'gōruden\'wīku',

            /* double consonants */
            '[結果|けっか]'   => 'kekka',
            'さっさと'        => 'sassato',
            'ずっと'          => 'zutto',
            '[切符|きっぷ]'   => 'kippu',
            '[雑誌|ざっし]'   => 'zasshi',
            '[一緒|いっしょ]' => 'issho',
            'こっち'          => 'kotchi', /* special case for -ch */
            '[抹茶|まっちゃ]' => 'matcha',
            '[三つ|みっつ]'   => 'mittsu',

            /* particles */
            'これ は これ は'  => 'kore wa kore wa',
            'うち へ ようこそ' => 'uchi e yōkoso',
            'はっ はい！'      => 'ha… hai!',
            '[恥|は]ずかしい'  => 'hazukashii',
            '[腹|はら] [減|へ]った' => 'hara hetta',
            'これ を'          => 'kore o',
        );
        foreach ($testReadings as $kana => $romaji)
            $this->assertRomaji($kana, $romaji);
    }
}
