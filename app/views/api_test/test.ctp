<?php
   echo $html->script(JS_PATH . 'apitest.js', true);
?>
<h1>API test form</h1>

<h2>search()</h2>

<?php
   echo $form->create(false, array(
           "controller" => "api_test",
           "action" => "search",
       "class" => "test_form"
       )
   );
?>

<!--
{
    "jsonrpc" : "2.0",
    "id" : 0,
    "method" : "search",
    "params" : {
        "version" : 1,
        "query": "all the kings men",
        "from" : "eng",
        "to" : "jpn",
        "page" : [0,15],
        "options" : 1
    }
}

{"jsonrpc":"2.0","id":0,"method":"search","params":{"v":1,"q":"all the kings men","f":"eng","t":"jpn","p":[0,15],"o":1}}
-->

<div id="query_displays">
<textarea cols="45" rows="15" class="search_query_display" id="query_display">

</textarea>
<textarea cols="45" rows="5" class="search_query_display" id="query_minified_display">

</textarea>
</div>

<fieldset id="HeaderFieldset">
<label for="jsonrpc">jsonrpc</label>
<input type="text" name="jsonrpc" short="j" value="2.0" /><br/>

<label for="jsonrpc">id</label>
<input type="text" name="id" short="i" value="0" /><br/>

<label for="jsonrpc">method</label>
<input type="text" name="method" short="m" value="search" /><br/><br/>
</fieldset>

<fieldset id="ParamsFieldset">
<legend>params</legend>
<label for="jsonrpc">version</label>
<input type="text" name="version" short="v" value="1" /><br/>

<label for="jsonrpc">from</label>
<input type="text" name="from" short="f" value="eng" /><br/>

<label for="jsonrpc">to</label>
<input type="text"  name="to" short="t" value="jpn" /><br/>

<label for="jsonrpc">query</label>
<input type="text" name="query" short="q" value="all the kings men" /><br/>

<label for="jsonrpc">page</label>
<input type="text" name="page" short="p" value="[0,15]" /><br/>

<label for="jsonrpc">options</label>
<input type="text" name="options" short="o" value="1" /><br/><br/>
</fieldset>


<input type="button" value="submit" id="submit_button"/>

<?php
   echo $form->end();
?>