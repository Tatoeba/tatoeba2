<h2>Public domain</h2>
<p>You can find here the sentences collected through Tatoeba. In case you wonder, you can download without having to worry about copyrights or license, the data is released into the public domain. However, although it is not an obligation, it would be nice if you could :
<ol>
<li><a href="mailto:trang.dictionary.project@gmail.com">Let me know</a> what are you going to use the data for.</li>
<li>If you are going to use them in some other project, mention in your work where the data is from.</li>
<li><?php echo $html->link('Contribute', array('controller'=>'sentences', 'action'=>'contribute')) ?> a little.</li>
</ol>
</p>

<br/>


<h2><?php __('Latest files'); ?></h2>

<h3>Sentences in separate files</h3>

<?php
$samples = array(
	"sentences_de_20090326" => '"166501";"de";"Lass uns {etwas}{1} versuchen!"',
	"sentences_en_20090326" => '"166501";"en";"Let\'s try {something}{1}!"', 
	"sentences_es_20090326" => '"166501";"es";"Intentemos {algo}{1}!"',
	"sentences_fr_20090326" => '"2028";"fr";"Je ne supporte pas ce type."', 
	"sentences_he_20090326" => '"168314";"he";"בוקר טוב."', 
	"sentences_id_20090326" => '"331310";"id";"Ibu sedang masak di dapur."', 	
	"sentences_it_20090326" => '"166502";"it";"Devo andare a dormire."', 
	"sentences_jp_20090326" => '"168036";"jp";"きみにちょっとしたものをもってきたよ。"', 
	"sentences_jp2_20090326" => '"1";"と言う{という}~ 記号~ は|1 を 指す[03]~"',
	"sentences_ja_20090326" => '"168036";"ja";"きみにちょっとしたものをもってきたよ。"',
	"sentences_romaji_20090326" => '"1297";" kiminichottoshitamonowomottekitayo . "',
	"sentences_ko_20090326" => '"166501";"ko";"뭔가 해보자!"', 
	"sentences_nl_20090326" => '"167866";"nl";"Neemt u een kopje koffie?"',
	"sentences_pt_20090326" => '"166501";"pt";"Vamos tentar {alguma coisa}{1}!"', 
	"sentences_ru_20090326" => '"166501";"ru";"Давайте что-нибудь попробуем!"', 
	"sentences_vn_20090326" => '"166502";"vn";"Tôi phải đi ngủ."',
	"sentences_zh_20090326" => '"166501";"zh";"我们試試看！"',
	"sentences_zh-guoyu_20090326" => '"166501";"zh-guoyu";"我们試試看！"');

$sizes = array(
	"sentences_de_20090326" => '116 kb',
	"sentences_en_20090326" => '8632 kb', 
	"sentences_es_20090326" => '78 kb',
	"sentences_fr_20090326" => '1430 kb', 
	"sentences_he_20090326" => '2 kb', 
	"sentences_id_20090326" => '10 kb', 	
	"sentences_it_20090326" => '16 kb', 
	"sentences_jp_20090326" => '10777 kb', 
	"sentences_jp2_20090326" => '16883 kb',
	"sentences_ja_20090326" => '10777 kb',
	"sentences_romaji_20090326" => '9500 kb',
	"sentences_ko_20090326" => '5 kb', 
	"sentences_nl_20090326" => '3 kb',
	"sentences_pt_20090326" => '1 kb', 
	"sentences_ru_20090326" => '22 kb', 
	"sentences_vn_20090326" => '19 kb',
	"sentences_zh_20090326" => '5 kb',
	"sentences_zh-guoyu_20090326" => '6 kb');

	
echo '<table class="download older">';
	echo '<tr>';
	echo '<th>'. __('File',true) .'</th>';
	echo '<th>'. __('Sample', true) .'</th>';	
	echo '<th>'. __('Size',true) .'</th>';	
	echo '</tr>';

	foreach($samples as $fileName => $sample){
	echo '<tr>';
		echo '<td>';
		echo '<a href="/files/2009-03-26/sentences/'.$fileName.'.csv">'.$fileName.'.csv</a>';
		echo '</td>';
		
		echo '<td>';
		echo $sample;
		echo '</td>';
		
		echo '<td>';
		echo $sizes[$fileName];
		echo '</td>';		
	echo '</tr>';
	}
echo '</table>';
?>

<p>There is no difference between the "ja" and "jp" file, besides the fact that the language code is "ja" in one and "jp" in the other. Tatoeba currently uses "jp" for "Japanese" but I was told that "ja" is the standard ISO code for "Japanese" and that it would be nice to provide data with the standard code.</p>

<p>That's why there's is a "ja" and "jp" file. But they have exactly the same content. Same thing for "zh" and "zh-guoyu".</p>

<h3>Links in separate files</h3>
<?php
$links_languages = array('de', 'en', 'es', 'fr', 'jp');
echo '<table class="download older">';

echo '<tr>';
echo '<td></td>';
foreach($links_languages as $from){
	echo '<td>';
	echo 'From '.$languages->codeToName($from);
	echo '</td>';
}
echo '</tr>';

foreach($links_languages as $to){
	echo '<tr>';
	echo '<td>To '.$languages->codeToName($to).'</td>';
	foreach($links_languages as $from){
		echo '<td>';
		if($from != $to){
			echo '<a href="/files/2009-03-26/links/'.$from.'2'.$to.'.csv">'.$from.'2'.$to.'.csv</a>';
		}
		echo '</td>';
	}
	echo '</tr>';
}
echo '</table>';
?>


<h3>All in one</h3>

<table class="download latest">

<tr>
<th><?php __('File') ?></th>
<th><?php __('Sample') ?></th>
<th><?php __('Size') ?></th>
</tr>
<tr>

<td>
<?php echo $html->link('sentences_20090326.csv','/files/2009-03-26/sentences_20090326.csv'); ?>
</td>

<td>
<pre>
"1";"ch";"我们試試看！"
"2";"ch";"我需要去睡覺了。"
"3";"ch";"你在幹什麼啊? [?]"
</pre>
"$id";"$langCode";"$sentence"
</td>

<td>21059 ko</td>
</tr>



<tr>
<td>
<?php echo $html->link('links_20090326.csv','/files/2009-03-26/links_20090326.csv'); ?>
</td>

<td>
<pre>
"1";"77"
"1";"1276"
"1";"2481"
</pre>
"$sentence_id";"$translation_id"
</td>

<td>7478 ko</td>
</tr>

</table>

<h3>Information about the files</h3>
<p>The data is provided in CSV files, encoded in UTF-8 without BOM.</p>
<p>The sentences and the links between the sentences are stored in two separate files.</p>
<p>Each sentence has a unique id (contrary to the older files).<p>
<p>In the links file, <strong>"1";"77"</strong> means that sentence nº77 is the translation of sentence nº1. The reciprocal link is also present. In other words, you will also have a line that say <strong>"77";"1"</strong>.</p>

<br/>


<h2><?php __('Older files'); ?></h2>

<?php
$samples = array(
	"tatoeba_ch_2008_12_06" => '"166501";"ch";"我们試試看！"',
	"tatoeba_zh-guoyu_2008_12_29" => '"166501";"zh-guoyu";"我们試試看！"',
	"tatoeba_de_2008_12_06" => '"166501";"de";"Lass uns {etwas}{1} versuchen!"',
	"tatoeba_en_2008_12_06" => '"166501";"en";"Let\'s try {something}{1}!"', 
	"tatoeba_es_2008_12_06" => '"166501";"es";"Intentemos {algo}{1}!"',
	"tatoeba_fr_2008_12_06" => '"2028";"fr";"Je ne supporte pas ce type."', 
	"tatoeba_he_2008_12_06" => '"168314";"he";"בוקר טוב."', 
	"tatoeba_it_2008_12_06" => '"166502";"it";"Devo andare a dormire."', 
	"tatoeba_jp_2008_12_06" => '"168036";"jp";"きみにちょっとしたものをもってきたよ。"', 
	"tatoeba_jp2_2008_12_06" => '"1";"と言う{という}~ 記号~ は|1 を 指す[03]~"',
	"tatoeba_ja_2008_12_29" => '"168036";"ja";"きみにちょっとしたものをもってきたよ。"',
	"tatoeba_ko_2008_12_06" => '"166501";"ko";"뭔가 해보자!"', 
	"tatoeba_nl_2008_12_06" => '"167866";"nl";"Neemt u een kopje koffie?"',
	"tatoeba_pt_2008_12_06" => '"166501";"pt";"Vamos tentar {alguma coisa}{1}!"', 
	"tatoeba_ru_2008_12_06" => '"166501";"ru";"Давайте что-нибудь попробуем!"', 
	"tatoeba_vn_2008_12_06" => '"166502";"vn";"Tôi phải đi ngủ."');

$sizes = array(	
	"tatoeba_ch_2008_12_06" => '5 kb',
	"tatoeba_zh-guoyu_2008_12_29" => '6 kb',
	"tatoeba_de_2008_12_06" => '115 kb',
	"tatoeba_en_2008_12_06" => '8582 kb', 
	"tatoeba_es_2008_12_06" => '65 kb',
	"tatoeba_fr_2008_12_06" => '1390 kb', 
	"tatoeba_he_2008_12_06" => '2 kb', 
	"tatoeba_it_2008_12_06" => '17 kb', 
	"tatoeba_jp_2008_12_06" => '10687 kb', 
	"tatoeba_jp2_2008_12_06" => '16804 kb',
	"tatoeba_ja_2008_12_29" => '10687 kb',
	"tatoeba_ko_2008_12_06" => '4 kb', 
	"tatoeba_nl_2008_12_06" => '2 kb',
	"tatoeba_pt_2008_12_06" => '1 kb', 
	"tatoeba_ru_2008_12_06" => '23 kb', 
	"tatoeba_vn_2008_12_06" => '19 kb');
	
echo '<table class="download older">';
	echo '<tr>';
	echo '<th>'. __('File',true) .'</th>';
	echo '<th>'. __('Sample', true) .'</th>';	
	echo '<th>'. __('Size',true) .'</th>';	
	echo '</tr>';

	foreach($samples as $fileName => $sample){
	echo '<tr>';
		echo '<td>';
		echo '<a href="/files/old/'.$fileName.'.csv">'.$fileName.'.csv</a>';
		echo '</td>';
		
		echo '<td>';
		echo $sample;
		echo '</td>';
		
		echo '<td>';
		echo $sizes[$fileName];
		echo '</td>';		
	echo '</tr>';
	}
echo '</table>';
?>

<h3>Information about the files</h3>
<p>The data is provided in CSV files, encoded in UTF-8 without BOM. There are three fields : id, language and sentence. Except for the tanaka B line where it's only id and language. Fields are terminated by ; and enclosed by ".</p>
<pre>"id";"lang";"sentence"</pre>
<p>Sentences with a same id have (or should have) the same meaning.</p>

<h3>Other things you may want to know</h3>
<p>The <strong>Japanese and English</strong> sentences which have an id between <strong>1 and 166500</strong> are from the <a href="http://www.csse.monash.edu.au/~jwb/tanakacorpus.html">Tanaka Corpus</a>, which belongs to the public domain. Needless to say, most of the sentences in Tatoeba are from there.</p>
<p>The date when the files were exported is indicated in the name of the file.</p>
<p>Some of the sentences are anoted with brackets and I was too lazy to take them out.</p>