UI Translations
===============

How to add a new UI language
----------------------------

#### Step 1

Update the `.tx/config`. In the `lang_map`, you need to
add the Transifex code and the Tatoeba code. This
allows to use the transifex command line tool `tx`.

> For instance for Italian, you would add `it:ita`.


#### Step 2

Make the language available in the drop-down box
by adding it to the `UI.languages` list in the file
`app/config/core.php.template`. Read the comments in
that file for more information.

> For instance for Italian, you would add `array('ita', null, 'Italiano'),`


#### Step 3

Push your changes and create a pull request. The language will 
be available for testing on our [dev website](https://dev.tatoeba.org)
shortly after your pull requst is merged.
