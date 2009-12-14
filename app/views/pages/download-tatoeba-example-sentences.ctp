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
		<h2><?php __("Last update")?></h2>
		<p>December 14th, 2009</p>
	</div>
	
	
	<div class="module">
	<h2>Creative commons</h2>
		<p>
		In the next update, the sentences in Tatoeba will be released under CC-BY.
		</p>
		<a rel="license" href="http://creativecommons.org/licenses/by/2.0/fr/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/2.0/fr/88x31.png" /></a>
	</div>
	
	<div class="module">
		<h2>Why not leave it in the public domain?</h2>
		<p>Some explanations <a href="http://blog.tatoeba.org/2009/12/tatoeba-update-dec-12th-2009.html">here</a>.</p>
	</div>
</div>

<div id="main_content">
	<div class="module">
		<h2>Latest files</h2>
		<p>The latest files were exported on November 6th 2009. Sorry I did not have the time yet to upload more recent files.</p>
		<p>The sentences and the links between the sentences are stored in two separate files.</p>
		<ul>
			<li><a href="http://tatoeba.org/files/2009-11-06/sentences_20091106.csv">sentences</a></li>
			<li><a href="http://tatoeba.org/files/2009-11-06/links_20091106.csv">links</a></li>
			<li><a href="http://tatoeba.org/files/2009-11-06/romaji_20091106.csv">romaji</a></li>
		</ul>
	</div>
	
	<div class="module">
		<h2>Information about the files</h2>
		<p>Each sentence has a unique id.<p>
		<p>In the links file, <strong>"1";"77"</strong> means that sentence nº77 is the translation of sentence nº1. The reciprocal link is also present. In other words, you will also have a line that say <strong>"77";"1"</strong>.</p>
		<p>You can also download the romaji for Japanese sentences. Note that the romaji has been automatically generated and is not always reliable.</p>			
		<p>The data is provided in CSV files, encoded in UTF-8 without BOM.</p>
		<p>Most of the <strong>Japanese and English</strong> sentences are from the <a href="http://www.csse.monash.edu.au/~jwb/tanakacorpus.html">Tanaka Corpus</a>, which belongs to the public domain. In other words, most of the sentences in Tatoeba are from there.</p>
		<p>The date when the files were exported is indicated in the name of the file.</p>
		<p>Some of the sentences are anoted with brackets and I was too lazy to take them out.</p>
	</div>
</div>
