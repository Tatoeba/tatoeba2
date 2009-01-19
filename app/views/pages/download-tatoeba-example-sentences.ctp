<h2>Public domain</h2>
<p>In case you wonder, you can download without having to worry about copyrights or license, the data is released into the public domain. However, although it is not an obligation, it would be nice if you could :
<ol>
<li><a href="mailto:trang.dictionary.project@gmail.com">Let me know</a> what are you going to use the data for.</li>
<li>If you are going to use them in some other project, mention in your work where the data is from.</li>
<li><?php echo $html->link('Contribute', array('controller'=>'sentences', 'action'=>'contribute')) ?> a little.</li>
</ol>
</p>

<br/>


<h2><?php __('Latest files'); ?></h2>

<table class="download latest">

<tr>
<th><?php __('File') ?></th>
<th><?php __('Sample') ?></th>
<th><?php __('Size') ?></th>
</tr>
<tr>

<td>
<?php echo $html->link('sentences_20090118.csv','/files/sentences_20090118.csv'); ?>
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
<?php echo $html->link('links_20090118.csv','/files/links_20090118.csv'); ?>
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
	"tatoeba_ch_2008_12_06" => '5 ko',
	"tatoeba_zh-guoyu_2008_12_29" => '6 ko',
	"tatoeba_de_2008_12_06" => '115 ko',
	"tatoeba_en_2008_12_06" => '8582 ko', 
	"tatoeba_es_2008_12_06" => '65 ko',
	"tatoeba_fr_2008_12_06" => '1390 ko', 
	"tatoeba_he_2008_12_06" => '2 ko', 
	"tatoeba_it_2008_12_06" => '17 ko', 
	"tatoeba_jp_2008_12_06" => '10687 ko', 
	"tatoeba_jp2_2008_12_06" => '16804 ko',
	"tatoeba_ja_2008_12_29" => '10687 ko',
	"tatoeba_ko_2008_12_06" => '4 ko', 
	"tatoeba_nl_2008_12_06" => '2 ko',
	"tatoeba_pt_2008_12_06" => '1 ko', 
	"tatoeba_ru_2008_12_06" => '23 ko', 
	"tatoeba_vn_2008_12_06" => '19 ko');
	
echo '<table class="download older">';
	echo '<tr>';
	echo '<th>'. __('File',true) .'</th>';
	echo '<th>'. __('Sample', true) .'</th>';	
	echo '<th>'. __('Size',true) .'</th>';	
	echo '</tr>';

	foreach($samples as $fileName => $sample){
	echo '<tr>';
		echo '<td>';
		echo '<a href="downloads/'.$fileName.'.csv">'.$fileName.'.csv</a>';
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