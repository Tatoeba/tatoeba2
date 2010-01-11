<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<div id="annexe_content">	
	<div class="module">
		<h2>Warning</h2>
		<p>The data you will find here will NOT be useful unless you are coding a language tool or doing some work on data processing.</p>
		<p>If you want data that you can use as a humble language learner, you can check out the <?php echo $html->link('lists section', array("controller"=>"sentences_lists")); ?> where you can build your own lists of sentences or view others' lists and print them.</p>
	</div>
	
	<div class="module">
		<h2>Creative commons</h2>
		<p>These files are released under CC-BY.</p>
		<a rel="license" href="http://creativecommons.org/licenses/by/2.0/fr/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/2.0/fr/88x31.png" /></a>
	
		<p>For those who wonder why we're not leaving the data in the public domain, some explanations <a href="http://blog.tatoeba.org/2009/12/tatoeba-update-dec-12th-2009.html">here</a>.</p>
	</div>
	
	<div class="module">
		<h2>Questions?</h2>
		<p>If you have questions or requests, feel free to <?php echo $html->link("contact us", array("controller"=>"pages", "action"=>"contact")); ?>. In general we answer quickly.</p>
	</div>
</div>

<div id="main_content">
	
	<div class="module">
		<h2>Latest files</h2>
		<table class="downloads">
			<!-- Sentences -->
			<tr>
				<th style="width:150px">File</th>
				<th>Description</th>
			</tr>
			<tr>
				<td>
					<div><strong>Download:</strong> <a href="http://tatoeba.org/files/2010-01-11/sentences_20100111.csv">sentences</a></div>
					<div><strong>Date:</strong> Jan 11th, 2009</div>
					<div><strong>Size:</strong> 21.4 MB</div>
				</td>
				<td>
					<p><strong>Fields:</strong> "id"; "lang"; "text"</p>
					<p>Contains all the sentences. Each sentence is associated to a unique id and a language code (<a href="http://en.wikipedia.org/wiki/List_of_ISO_639-3_codes">ISO 639-3</a>).</p>
			</tr>
			
			<!-- Links -->
			<tr>
				<td>
					<div><strong>Download:</strong> <a href="http://tatoeba.org/files/2010-01-11/links_20100111.csv">links</a></div>
					<div><strong>Date:</strong> Jan 11th, 2009</div>
					<div><strong>Size:</strong> 7.9 MB</div>
				</td>
				<td>
					<p><strong>Fields:</strong> "sentence_id"; "translation_id"</p>
					<p>Contains the links between the sentences. <strong>"1";"77"</strong> means that sentence nº77 is the translation of sentence nº1. The reciprocal link is also present. In other words, you will also have a line that say <strong>"77";"1"</strong>.</p>
				</td>
			</tr>
			
			<!-- Romaji -->
			<tr>
				<td>
					<div><strong>Download:</strong> <a href="http://tatoeba.org/files/2009-11-06/romaji_20091106.csv">romaji</a></div>
					<div><strong>Date:</strong> Nov 6th, 2009</div>
					<div><strong>Size:</strong> 9 MB</div>
				</td>
				<td>
					<p><strong>Fields:</strong> "sentence_id"; "text"</p>
					<p>Contains the romaji for Japanese sentences. Note that the romaji has been automatically generated and is not always reliable.</p>
				</td>
			</tr> 
			
			<!-- Tanaka B lines -->
			<tr>
				<td>
					<div><strong>Download:</strong> <a href="http://tatoeba.org/files/2010-01-11/jpn_indices_20100111.csv">jpn_indices</a></div>
					<div><strong>Date:</strong> Jan 11th, 2009</div>
					<div><strong>Size:</strong> 17.7 MB</div>
				</td>
				<td>
					<p><strong>Fields:</strong> "sentence_id"; "meaning_id", "text"</p>
					<p>Contains the equivalent of the "B lines" in the file of the Tanaka Corpus distributed by Jim Breen (cf. Current format, <a href="http://www.csse.monash.edu.au/~jwb/tanakacorpus.html">on this page</a>). Each entry is associated to a pair of Japanese/English sentences. <strong>sentence_id</strong> refers to the id of the Japanese sentence. <strong>meaning_id</strong> refers to the id of the English sentence.</p>
				</td>
			</tr> 
		</table>
	</div>
	
	<div class="module">
		<h2>General information about the files</h2>
		<p>The data is provided in CSV files, encoded in UTF-8 without BOM. Fields are terminated by a semi-colon and enclosed by double quotes.</p>
		<p>Most of the Japanese and English sentences are from the <a href="http://www.csse.monash.edu.au/~jwb/tanakacorpus.html">Tanaka Corpus</a>, which belongs to the public domain. In other words, most of the sentences in Tatoeba are from there. Note that this corpus will now be maintained from Tatoeba, so you will find the most up-to-date data here.</p>
		<p>Some of the sentences are anoted with brackets and Trang was too lazy to strip them off. In case you wonder, they were used to indicate the correspondance of words between a sentence and its translations. For instance <em>I am {happy}{1}</em> and <em>Je suis {content}{1}</em>. The brackets here indicate that <em>happy</em> and <em>content</em> mean the same thing.</p>
	</div>
</div>