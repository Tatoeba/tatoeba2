#############################################################################
## data source definition
#############################################################################

<?php
/**
 *
 * You will want to change the values of these variables or use the configure_sphinx.sh to change them:
 *   - sourcePath
 *
 * In the "source default" section:
 *   - sql_user
 *   - sql_pass
 *   - sql_db
 *   - sql_sock
 *
 * In the "searchd" section:
 *   - listen (called "port" in older versions of Sphinx)
 *   - log
 *   - query_log
 *   - pid_file
 *   
 */

$sourcePath = "INDEXDIR";

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
    'nan' => 'Teochew',
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
    'pms' => 'Piemontese',

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
    'bod' => 'Standard Tibetan' , 
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
    'wln' => 'Wallon' , 
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
    'yor' => 'Yoruba' , //@lang
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
    sql_user                 = USER
    sql_pass                 = PASSWORD
    sql_db                   = DATABASE
    sql_sock                 = SOCKET

    sql_query_pre            = SET NAMES utf8
    sql_query_pre            = SET SESSION query_cache_type=OFF

}


index common_index 
{
    index_exact_words       = 1
    charset_table           = 0..9, a..z, _, A..Z->a..z, U+00C0->a, U+00C1->a, U+00C2->a, U+00C3->a, U+00C4->a, \
                        U+00C5->a, U+00C7->c, U+00C8->e, U+00C9->e, U+00CA->e, U+00CB->e, U+00CC->i, U+00CD->i, \
                        U+00CE->i, U+00CF->i, U+00D1->n, U+00D2->o, U+00D3->o, U+00D4->o, U+00D5->o, U+00D6->o, \
                        U+00D9->u, U+00DA->u, U+00DB->u, U+00DC->u, U+00DD->y, U+00E0->a, U+00E1->a, U+00E2->a, \
                        U+00E3->a, U+00E4->a, U+00E5->a, U+00E7->c, U+00E8->e, U+00E9->e, U+00EA->e, U+00EB->e, \
                        U+00EC->i, U+00ED->i, U+00EE->i, U+00EF->i, U+00F1->n, U+00F2->o, U+00F3->o, U+00F4->o, \
                        U+00F5->o, U+00F6->o, U+00F9->u, U+00FA->u, U+00FB->u, U+00FC->u, U+00FD->y, U+00FF->y, \
                        U+0100->a, U+0101->a, U+0102->a, U+0103->a, U+0104->a, U+0105->a, U+0106->c, U+0107->c, \
                        U+0108->c, U+0109->c, U+010A->c, U+010B->c, U+010C->c, U+010D->c, U+010E->d, U+010F->d, \
                        U+0112->e, U+0113->e, U+0114->e, U+0115->e, U+0116->e, U+0117->e, U+0118->e, U+0119->e, \
                        U+011A->e, U+011B->e, U+011C->g, U+011D->g, U+011E->g, U+011F->g, U+0120->g, U+0121->g, \
                        U+0122->g, U+0123->g, U+0124->h, U+0125->h, U+0128->i, U+0129->i, U+012A->i, U+012B->i, \
                        U+012C->i, U+012D->i, U+012E->i, U+012F->i, U+0130->i, U+0134->j, U+0135->j, U+0136->k, \
                        U+0137->k, U+0139->l, U+013A->l, U+013B->l, U+013C->l, U+013D->l, U+013E->l, U+0142->l, \
                        U+0143->n, U+0144->n, U+0145->n, U+0146->n, U+0147->n, U+0148->n, U+014C->o, U+014D->o, \
                        U+014E->o, U+014F->o, U+0150->o, U+0151->o, U+0154->r, U+0155->r, U+0156->r, U+0157->r, \
                        U+0158->r, U+0159->r, U+015A->s, U+015B->s, U+015C->s, U+015D->s, U+015E->s, U+015F->s, \
                        U+0160->s, U+0161->s, U+0162->t, U+0163->t, U+0164->t, U+0165->t, U+0168->u, U+0169->u, \
                        U+016A->u, U+016B->u, U+016C->u, U+016D->u, U+016E->u, U+016F->u, U+0170->u, U+0171->u, \
                        U+0172->u, U+0173->u, U+0174->w, U+0175->w, U+0176->y, U+0177->y, U+0178->y, U+0179->z, \
                        U+017A->z, U+017B->z, U+017C->z, U+017D->z, U+017E->z, U+01A0->o, U+01A1->o, U+01AF->u, \
                        U+01B0->u, U+01CD->a, U+01CE->a, U+01CF->i, U+01D0->i, U+01D1->o, U+01D2->o, U+01D3->u, \
                        U+01D4->u, U+01D5->u, U+01D6->u, U+01D7->u, U+01D8->u, U+01D9->u, U+01DA->u, U+01DB->u, \
                        U+01DC->u, U+01DE->a, U+01DF->a, U+01E0->a, U+01E1->a, U+01E6->g, U+01E7->g, U+01E8->k, \
                        U+01E9->k, U+01EA->o, U+01EB->o, U+01EC->o, U+01ED->o, U+01F0->j, U+01F4->g, U+01F5->g, \
                        U+01F8->n, U+01F9->n, U+01FA->a, U+01FB->a, U+0200->a, U+0201->a, U+0202->a, U+0203->a, \
                        U+0204->e, U+0205->e, U+0206->e, U+0207->e, U+0208->i, U+0209->i, U+020A->i, U+020B->i, \
                        U+020C->o, U+020D->o, U+020E->o, U+020F->o, U+0210->r, U+0211->r, U+0212->r, U+0213->r, \
                        U+0214->u, U+0215->u, U+0216->u, U+0217->u, U+0218->s, U+0219->s, U+021A->t, U+021B->t, \
                        U+021E->h, U+021F->h, U+0226->a, U+0227->a, U+0228->e, U+0229->e, U+022A->o, U+022B->o, \
                        U+022C->o, U+022D->o, U+022E->o, U+022F->o, U+0230->o, U+0231->o, U+0232->y, U+0233->y, \
                        U+1E00->a, U+1E01->a, U+1E02->b, U+1E03->b, U+1E04->b, U+1E05->b, U+1E06->b, U+1E07->b, \
                        U+1E08->c, U+1E09->c, U+1E0A->d, U+1E0B->d, U+1E0C->d, U+1E0D->d, U+1E0E->d, U+1E0F->d, \
                        U+1E10->d, U+1E11->d, U+1E12->d, U+1E13->d, U+1E14->e, U+1E15->e, U+1E16->e, U+1E17->e, \
                        U+1E18->e, U+1E19->e, U+1E1A->e, U+1E1B->e, U+1E1C->e, U+1E1D->e, U+1E1E->f, U+1E1F->f, \
                        U+1E20->g, U+1E21->g, U+1E22->h, U+1E23->h, U+1E24->h, U+1E25->h, U+1E26->h, U+1E27->h, \
                        U+1E28->h, U+1E29->h, U+1E2A->h, U+1E2B->h, U+1E2C->i, U+1E2D->i, U+1E2E->i, U+1E2F->i, \
                        U+1E30->k, U+1E31->k, U+1E32->k, U+1E33->k, U+1E34->k, U+1E35->k, U+1E36->l, U+1E37->l, \
                        U+1E38->l, U+1E39->l, U+1E3A->l, U+1E3B->l, U+1E3C->l, U+1E3D->l, U+1E3E->m, U+1E3F->m, \
                        U+1E40->m, U+1E41->m, U+1E42->m, U+1E43->m, U+1E44->n, U+1E45->n, U+1E46->n, U+1E47->n, \
                        U+1E48->n, U+1E49->n, U+1E4A->n, U+1E4B->n, U+1E4C->o, U+1E4D->o, U+1E4E->o, U+1E4F->o, \
                        U+1E50->o, U+1E51->o, U+1E52->o, U+1E53->o, U+1E54->p, U+1E55->p, U+1E56->p, U+1E57->p, \
                        U+1E58->r, U+1E59->r, U+1E5A->r, U+1E5B->r, U+1E5C->r, U+1E5D->r, U+1E5E->r, U+1E5F->r, \
                        U+1E60->s, U+1E61->s, U+1E62->s, U+1E63->s, U+1E64->s, U+1E65->s, U+1E66->s, U+1E67->s, \
                        U+1E68->s, U+1E69->s, U+1E6A->t, U+1E6B->t, U+1E6C->t, U+1E6D->t, U+1E6E->t, U+1E6F->t, \
                        U+1E70->t, U+1E71->t, U+1E72->u, U+1E73->u, U+1E74->u, U+1E75->u, U+1E76->u, U+1E77->u, \
                        U+1E78->u, U+1E79->u, U+1E7A->u, U+1E7B->u, U+1E7C->v, U+1E7D->v, U+1E7E->v, U+1E7F->v, \
                        U+1E80->w, U+1E81->w, U+1E82->w, U+1E83->w, U+1E84->w, U+1E85->w, U+1E86->w, U+1E87->w, \
                        U+1E88->w, U+1E89->w, U+1E8A->x, U+1E8B->x, U+1E8C->x, U+1E8D->x, U+1E8E->y, U+1E8F->y, \
                        U+1E96->h, U+1E97->t, U+1E98->w, U+1E99->y, U+1EA0->a, U+1EA1->a, U+1EA2->a, U+1EA3->a, \
                        U+1EA4->a, U+1EA5->a, U+1EA6->a, U+1EA7->a, U+1EA8->a, U+1EA9->a, U+1EAA->a, U+1EAB->a, \
                        U+1EAC->a, U+1EAD->a, U+1EAE->a, U+1EAF->a, U+1EB0->a, U+1EB1->a, U+1EB2->a, U+1EB3->a, \
                        U+1EB4->a, U+1EB5->a, U+1EB6->a, U+1EB7->a, U+1EB8->e, U+1EB9->e, U+1EBA->e, U+1EBB->e, \
                        U+1EBC->e, U+1EBD->e, U+1EBE->e, U+1EBF->e, U+1EC0->e, U+1EC1->e, U+1EC2->e, U+1EC3->e, \
                        U+1EC4->e, U+1EC5->e, U+1EC6->e, U+1EC7->e, U+1EC8->i, U+1EC9->i, U+1ECA->i, U+1ECB->i, \
                        U+1ECC->o, U+1ECD->o, U+1ECE->o, U+1ECF->o, U+1ED0->o, U+1ED1->o, U+1ED2->o, U+1ED3->o, \
                        U+1ED4->o, U+1ED5->o, U+1ED6->o, U+1ED7->o, U+1ED8->o, U+1ED9->o, U+1EDA->o, U+1EDB->o, \
                        U+1EDC->o, U+1EDD->o, U+1EDE->o, U+1EDF->o, U+1EE0->o, U+1EE1->o, U+1EE2->o, U+1EE3->o, \
                        U+1EE4->u, U+1EE5->u, U+1EE6->u, U+1EE7->u, U+1EE8->u, U+1EE9->u, U+1EEA->u, U+1EEB->u, \
                        U+1EEC->u, U+1EED->u, U+1EEE->u, U+1EEF->u, U+1EF0->u, U+1EF1->u, U+1EF2->y, U+1EF3->y, \
                        U+1EF4->y, U+1EF5->y, U+1EF6->y, U+1EF7->y, U+1EF8->y, U+1EF9->y, U+621..U+63a, U+640..U+64a, \
                        U+66e..U+66f, U+671..U+6d3, U+6d5, U+6e5..U+6e6, U+6ee..U+6ef, U+6fa..U+6fc, U+6ff, \
                        U+37a, U+386..U+389->U+3ac..U+3af, U+38c..U+38e->U+3cc..U+3ce, U+390, U+391..U+3a1->U+3b1..U+3c1,\
                        U+3a3..U+3ab->U+3c3..U+3cb, U+3ac..U+3ce, U+3d0..U+3d7, U+3d8..U+3ef/2, U+3f0..U+3f3, U+3f4->U+3b8,\
                        U+3f5, U+3f7..U+3f8/2, U+3f9->U+3f2, U+3fa..U+3fb/2, U+3fc..U+3ff,\
                        U+5d0..U+5ea, U+5f0..U+5f2,\
                        U+410..U+42F->U+430..U+44F, U+430..U+44F,\
                        U+10a0..U+10c5->U+2d00..U+2d25, U+10d0..U+10fa, U+10fc, U+2d00..U+2d25,\
                        U+C0..U+D6->U+E0..U+F6, U+D8..U+DE->U+F8..U+FE, U+178->U+FF, U+FF, U+100..U+177/2, U+179..U+17E/2,\
                        U+980..U+9FC,\
                        U+900..U+97F, U+A8E0..U+A8FB,\
                        U+531..U+58A, U+FB13..U+FB17,\
                        U+D00..U+D77,\
                        U+E00..U+E5C,\
                        U+492, U+493, U+4E2, U+4E3, U+49A, U+49B, U+4EE, U+4EF, U+4B2, U+4B3, U+4B6, U+4B7
                        

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
        select distinct s.id as id , s.text as text , s.id as id2 , t.lang_id as trans_id\
            from sentences s\
            left join sentences_translations st on st.sentence_id = s.id\
            left join sentences t on st.translation_id = t.id\
            where s.lang_id = (select id from langStats where lang = '$lang')\
        union \
        select distinct s.id as id , s.text as text , s.id as id2 , t.lang_id as trans_id\
            from sentences s\
            left join sentences_translations st on st.sentence_id = s.id\
            left join sentences_translations tt on tt.sentence_id = st.translation_id\
            left join sentences t on tt.translation_id = t.id\
            where s.lang_id =  (select id from langStats where lang = '$lang')\
        ) t 
        sql_attr_uint = id2
        sql_attr_multi = uint trans_id from field; SELECT id FROM langStats ;
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
    listen                  = 9312
    log                     = LOGDIR/searchd.log
    query_log              = LOGDIR/query.log
    read_timeout            = 5
    max_children            = 30

    pid_file                = LOGDIR/searchd.pid
    max_matches             = 1000
    seamless_rotate         = 1
    preopen_indexes         = 1
    unlink_old              = 1
}


