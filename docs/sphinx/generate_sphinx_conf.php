#############################################################################
## data source definition
#############################################################################

## Note that we read in some values from app/config/database.php.
## That file, which is not under source control, is copied from 
## app/config/database.php.template (which IS under source control)
## and then manually edited.
 
<?php
define('__TAT_ROOT__', dirname(dirname(dirname(__FILE__))));
require_once(__TAT_ROOT__.'/app/config/database.php');


$configs = get_class_vars('DATABASE_CONFIG');
$sourcePath = $configs['sphinx']['indexdir'];
$sphinxLogDir = $configs['sphinx']['logdir'];

$languages = array(
    'ara' => 'Arabic',
    'eng' => 'English',
    'jpn' => 'Japanese',
    'fra' => 'French',
    'deu' => 'German',
    'spa' => 'Spanish',
    'ita' => 'Italian',
    'vie' => 'Vietnamese',
    'rus' => 'Russian',
    'cmn' => 'Chinese',
    'kor' => 'Korean',
    'nld' => 'Dutch',
    'heb' => 'Hebrew',
    'ind' => 'Indonesian',
    'por' => 'Portuguese',
    'fin' => 'Finnish',
    'bul' => 'Bulgarian',
    'ukr' => 'Ukrainian',
    'ces' => 'Czech',
    'epo' => 'Esperanto',
    'ell' => 'Greek',
    'tur' => 'Turkish',
    'swe' => 'Swedish',
    'nob' => 'Norwegian (Bokmål)',
    'zsm' => 'Malay',
    'est' => 'Estonian',
    'kat' => 'Georgian',
    'pol' => 'Polish',
    'swh' => 'Swahili',
    'lat' => 'Latin',
    'wuu' => 'Shanghainese',
    'arz' => 'Egyptian Arabic',
    'bel' => 'Belarusian',
    'hun' => 'Hungarian',
    'isl' => 'Icelandic',
    'sqi' => 'Albanian',
    'yue' => 'Cantonese',
    'afr' => 'Afrikaans',
    'fao' => 'Faroese',
    'fry' => 'Frisian',

    'bre' => 'Breton',
    'ron' => 'Romanian',
    'uig' => 'Uyghur',
    'uzb' => 'Uzbek',
    'non' => 'Norwegian (Nynorsk)',
    'srp' => 'Serbian',
    'tat' => 'Tatar',
    'yid' => 'Yiddish',

    'pes' => 'Persian',
    'nan' => 'Min Nan Chinese',
    'eus' => 'Basque',
    'slk' => 'Slovak',
    'dan' => 'Danish',
    'hye' => 'Armenian',
    'acm' => 'Iraqi Arabic',
    'san' => 'Sanskrit',
    'urd' => 'Urdu',
    'hin' => 'Hindi',
    'ben' => 'Bengali',
    'cycl' => 'CycL',
    'cat' => 'Catalan',
    'kaz' => 'Kazakh',
    'lvs' => 'Latvian',
    'bos' => 'Bosnian',
    'hrv' => 'Croatian',
    'orv' => 'Old East Slavic',
    'cha' => 'Chamorro',
    'tgl' => 'Tagalog',
    'que' => 'Quechua',
    'mon' => 'Mongolian',
    'lit' => 'Lithuanian',
    'glg' => 'Galician',
    'gle' => 'Irish',
    'ina' => 'Interlingua',
    'jbo' => 'Lojban',
    'toki' => 'Toki Pona',
    'ain' => 'Ainu',
    'scn' => 'Sicilian',
    'mal' => 'Malayalam',
    'nds' => 'Low Saxon',
    'tlh' => 'Klingon',
    'slv' => 'Slovenian',
    'tha' => 'Thai',
    'lzh' => 'Literary Chinese',
    'oss' => 'Ossetian',
    'roh' => 'Romansh',
    'vol' => 'Volapük',
    'gla' => 'Scottish Gaelic',
    'ido' => 'Ido',
    'ast' => 'Asturian',
    'ile' => 'Interlingue',
    'oci' => 'Occitan',
    'xal' => 'Kalmyk',


    'ang' => 'Old English',
    'kur' => 'Kurdish',
    'dsb' => 'Lower Sorbian',
    'hsb' => 'Upper Sorbian',
    'ksh' => 'Kölsch',
    'cym' => 'Welsh',
    'ewe' => 'Ewe',
    'sjn' => 'Sindarin',
    'tel' => 'Telugu',
    'tpi' => 'Tok Pisin',
    'qya' => 'Quenya',
    'nov' => 'Novial',
    'mri' => 'Maori',
    'ber' => 'Berber',
    'lld' => 'Ladin',


    'xho' => 'Xhosa',
    'pnb' => 'Punjabi',
    'mlg' => 'Malagasy',
    'grn' => 'Guarani',
    'lad' => 'Ladino',
    'pms' => 'Piedmontese',

    'avk' => 'Kotava',
    'tpw' => 'Old Tupi',
    'tgk' => 'Tajik',
    'mar' => 'Marathi',
    'prg' => 'Old Prussian' , 
    'npi' => 'Nepali' , 
    'mlt' => 'Maltese' , 
    'ckt' => 'Chukchi' , 
    'cor' => 'Cornish' , 
    'aze' => 'Azerbaijani' , 
    'khm' => 'Khmer' , 
    'lao' => 'Lao' , 
    'bod' => 'Tibetan' , 
    'hil' => 'Hiligaynon' , 
    'arq' => 'Algerian Arabic' , 
    'pcd' => 'Picard' , 
    'grc' => 'Ancient Greek' , 
    'amh' => 'Amharic' , 
    'awa' => 'Awadhi' , 
    'bho' => 'Bhojpuri' , 
    'cbk' => 'Chavacano' , 
    'enm' => 'Middle English' , 
    'frm' => 'Middle French' , 
    'hat' => 'Haitian Creole' , 
    'jdt' => 'Juhuri (Judeo-Tat)' , 
    'kal' => 'Greenlandic' , 
    'mhr' => 'Meadow Mari' , 
    'nah' => 'Nahuatl' , 
    'pdc' => 'Pennsylvania German' , 
    'sin' => 'Sinhala' , 
    'tuk' => 'Turkmen' , 
    'wln' => 'Walloon' , 
    'bak' => 'Bashkir' , 
    'hau' => 'Hausa' , 
    'ltz' => 'Luxembourgish' , 
    'mgm' => 'Mambae' , 
    'som' => 'Somali' , 
    'zul' => 'Zulu' , 
    'haw' => 'Hawaiian' , 
    'kir' => 'Kyrgyz' , 
    'mkd' => 'Macedonian' , 
    'mrj' => 'Hill Mari' , 
    'ppl' => 'Pipil' , 
    'yor' => 'Yoruba' , 
    'kin' => 'Kinyarwanda' , 
    'shs' => 'Shuswap' , 
    'chv' => 'Chuvash' , 
    'lkt' => 'Lakota' , 
    'ota' => 'Ottoman Turkish' , 
    'sna' => 'Shona' , 
    'mnw' => 'Mon' , 
    'nog' => 'Nogai' , 
    'sah' => 'Yakut' , 
    'abk' => 'Abkhaz' , 
    'tet' => 'Tetun' , 
    'tam' => 'Tamil' , 
    'udm' => 'Udmurt' , 
    'kum' => 'Kumyk' , 
    'crh' => 'Crimean Tatar' , 
    'nya' => 'Chinyanja' , 
    'liv' => 'Livonian' , 
    'nav' => 'Navajo' , 
    'chr' => 'Cherokee' , 
    'guj' => 'Gujarati' , //@lang
);

$languageWithStemmer = array(
    "deu"=>0,
    "spa"=>0,
    "fra"=>0,
    "nld"=>0,
    "por"=>0,
    "rus"=>0,
    "fin"=>0,
    "ita"=>0,
    "tur"=>0,
    "swe"=>0,
    "eng"=>0,
);




$cjkLanguages = array(
    "kor" => 0,
    "cmn" => 0,
    "wuu" => 0,
    "jpn" => 0,
    "yue" => 0,
    'nan' => 0,
    'ain' => 0,
    'lzh' => 0
);


?>

source default
{
    type                     = mysql
    sql_host                 = localhost
    sql_user                 = <?php echo $configs['default']['login']; echo "\n"; ?>
    sql_pass                 = <?php echo $configs['default']['password']; echo "\n"; ?>
    sql_db                   = <?php echo $configs['default']['database']; echo "\n"; ?>
    sql_sock                 = <?php echo $configs['sphinx']['socket']; echo "\n"; ?>
    sql_query_pre            = SET NAMES utf8
    sql_query_pre            = SET SESSION query_cache_type=OFF

}


index common_index
{
    index_exact_words       = 1
    charset_table           = 0..9, a..z, _, A..Z->a..z, \
<?# Latin-1 Supplement, with case folding (0080-00FF) #?>
                        U+C0..U+D6->U+E0..U+F6, U+D8..U+DE->U+F8..U+FE, U+E0..U+F6, U+F8..U+FF, \
<?# Latin extended-A, with case folding (0100-017F) #?>
                        U+100..U+177/2, U+178->U+FF, U+179..U+17E/2, U+017F, \
<?# Latin extended-B, with case folding (0180-024F) #?>
                        U+0180, U+0181->U+0253, U+0182..U+0185/2, U+0186->U+0254, U+0187->U+0188, U+0188, \
                        U+0189->U+0256, U+018A->U+0257, U+018B->U+018C, U+018C, U+018D, U+018E->U+01DD, U+018F->U+0259, \
                        U+0190->U+025B, U+0191->U+0192, U+0192, U+0193->U+0260, U+0194->U+0263, U+0195, U+0196->U+0269, U+0197->U+0268, U+0198->U+0199, \
                        U+0199..U+019B, U+019C->U+026F, U+019D->U+0272, U+019E, U+019F->U+0275, \
                        U+01A0..U+01A5/2, U+01A6->U+0280, U+01A7->U+01A8, U+01A8, \
                        U+01A9->U+0283, U+01AA, U+01AB, U+01AC->U+01AD, U+01AD, U+01AE->U+0288, U+01AF->U+01B0, \
                        U+01B0, U+01B1->U+028A, U+01B2->U+028B, U+01B3..U+01B6/2, U+01B7->U+0292, U+01B8->U+01B9, \
                        U+01BA, U+01BB, U+01BC->U+01BD, U+01BD..U+01BF, \
                        U+01C0..U+01C3, U+01C4->U+01C6, U+01C5, U+01C6, U+01C7->U+01C9, U+01C8, \
                        U+01C9..U+01CC, U+01CD..U+01DC/2, U+01DE..U+01EF/2, \
                        U+01F0, U+01F1->U+01F3, U+01F2, U+01F3, U+01F4->U+01F5, U+01F5, U+01F6->U+0195, U+01F7->U+01BF, U+01F8..U+021F/2, \
                        U+0220->U+019E, U+0221, U+0222..U+0233/2, U+0234..U+0238, \
                        U+0239, U+023A->U+2C65, U+023B->U+023C, U+023C, U+023D->U+019A, U+023E->U+2C66, U+023F, \
                        U+0240, U+0241->U+0242, U+0242, U+0243->U+0180, U+0244->U+0289, U+0245->U+028C, U+0246..U+024F/2, \
<?# Latin Extended Additional, with case folding (1E00-1EFF) #?>
                        U+1E00..U+1E95/2, U+1E96..U+1E9F, U+1EA0..U+1EFF/2, \
<?# Arabic #?>
                        U+621..U+63a, U+640..U+64a, \
                        U+66e..U+66f, U+671..U+6d3, U+6d5, U+6e5..U+6e6, U+6ee..U+6ef, U+6fa..U+6fc, U+6ff, \
<?# Greek and Coptic #?>
                        U+37a, U+386..U+389->U+3ac..U+3af, U+38c..U+38e->U+3cc..U+3ce, U+390, U+391..U+3a1->U+3b1..U+3c1,\
                        U+3a3..U+3ab->U+3c3..U+3cb, U+3ac..U+3ce, U+3d0..U+3d7, U+3d8..U+3ef/2, U+3f0..U+3f3, U+3f4->U+3b8,\
                        U+3f5, U+3f7..U+3f8/2, U+3f9->U+3f2, U+3fa..U+3fb/2, U+3fc..U+3ff,\
<?# Hebrew #?>
                        U+5d0..U+5ea, U+5f0..U+5f2,\
<?# Cyrillic #?>
                        U+410..U+42F->U+430..U+44F, U+430..U+44F,\
<?# Georgian #?>
                        U+10a0..U+10c5->U+2d00..U+2d25, U+10d0..U+10fa, U+10fc, U+2d00..U+2d25,\
<?# Bengali #?>
                        U+980..U+9FC,\
<?# Devanagari + Devanagari Extended #?>
                        U+900..U+97F, U+A8E0..U+A8FB,\
<?# Armenian + Alphabetic Presentation Forms (Armenian Small Ligatures) #?>
                        U+531..U+58A, U+FB13..U+FB17,\
<?# Malayalam #?>
                        U+D00..U+D77,\
<?# Thai #?>
                        U+E00..U+E5C,\
<?# Various Cyrillic letters; I don't get the logic #?>
                        U+492, U+493, U+4E2, U+4E3, U+49A, U+49B, U+4EE, U+4EF, U+4B2, U+4B3, U+4B6, U+4B7,\
<?# Ethiopic #?>
                        U+1200..U+135F, U+1369..U+137C, U+1380..U+1399, U+2D80..U+2DDE, U+AB01..U+AB2E


    docinfo                 = extern
    charset_type            = utf-8

}

index cjk_common_index
{

    ngram_len               = 1
    ngram_chars             = U+3000..U+2FA1F
    charset_table           = U+3000..U+2FA1F
    docinfo                 = extern
    charset_type            = utf-8


}

#################################################

<?

foreach ($languages as $lang=>$name){
    echo "

    #$name\n

    source ".$lang."_src : default
    {




        sql_query = select distinct * from (\
        select distinct s.id as id , s.text as text , s.id as id2 , t.lang_id as trans_id, s.created as created, s.user_id as user_id, (s.correctness + 128) as ucorrectness\
            from sentences s\
            left join sentences_translations st on st.sentence_id = s.id\
            left join sentences t on st.translation_id = t.id\
            where s.lang_id = (select id from languages where code = '$lang')\
        union \
        select distinct s.id as id , s.text as text , s.id as id2 , t.lang_id as trans_id, s.created as created, s.user_id as user_id, (s.correctness + 128) as ucorrectness\
            from sentences s\
            left join sentences_translations st on st.sentence_id = s.id\
            left join sentences_translations tt on tt.sentence_id = st.translation_id\
            left join sentences t on tt.translation_id = t.id\
            where s.lang_id =  (select id from languages where code = '$lang')\
        ) t 
        sql_attr_timestamp = created
        sql_attr_uint = user_id".
    /* "correctness" is an 8-bit signed integer whereas Sphinx only allows
     * unsigned intgerers (actually it allows 64-bit signed integers "bigint"s
     * but it’s a waste of space). So we add 128 an treat it as unsigned,
     * and that’s why the attribute is called "ucorrectness".
     */
"
        sql_attr_uint = ucorrectness
        sql_attr_uint = id2
        sql_attr_multi = uint trans_id from field; SELECT id FROM languages ;
    }
            ";
    // generate index for this pair
    $parent = "common_index" ;
    if (isset($cjkLanguages[$lang])) {
        $parent = "cjk_common_index";
    }
    echo "
    index ".$lang."_index : $parent
    {
        source = ".$lang."_src
        path = " . $sourcePath . DIRECTORY_SEPARATOR.$lang;

        if (isset($languageWithStemmer[$lang])) {
            echo "
        morphology              = libstemmer_$lang
        min_stemming_len        = 4
    ";
        }
    echo
    "
    }
    ";
}// end of first foreach




    echo "

index und_index : common_index
{
    type = distributed

    ";
    foreach ($languages as $lang=>$name) {
        echo "    local           = $lang"."_index\n";
    }

    echo"
}
";
?>
indexer
{
    mem_limit               = 64M
}


searchd
{
    port                    = 9312
    log                     = <?php echo $sphinxLogDir . DIRECTORY_SEPARATOR . "searchd.log\n"; ?>
    query_log               = <?php echo $sphinxLogDir . DIRECTORY_SEPARATOR . "query.log\n"; ?>
    read_timeout            = 5
    max_children            = 30

    pid_file                = <?php echo $configs['sphinx']['pidfile'] . "\n"; ?>
    max_matches             = 1000
    seamless_rotate         = 1
    preopen_indexes         = 1
    unlink_old              = 1
}


