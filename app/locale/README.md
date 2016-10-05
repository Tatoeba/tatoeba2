UI Translations
===============

How to add a new UI language
----------------------------

#### Step 1

Create in here (`app/locale/`) the folder for the language. 
The name of folder should be the ISO 639-3 language code.
There may be exceptions.

> For instance if you were adding Italian as a new UI language,
you would create a folder named `ita`.


#### Step 2

Inside of the language folder, create another folder named 
`LC_MESSAGES`. In that folder, create an empty file named
`default.po`.

> For instance, for Italian, you would have a file with the path
`app/locale/ita/LC_MESSAGES/detault.po`.

If you do not create a file, you will not be able to commit the
new folders that you created. 
In fact it doesn't really matter how the file is called, but it's 
better to name it with the same name as the .po file on Transifex 
so that it gets overriden with the actual Transifex file.


#### Step 3

Update the `.tx/config`. In the `lang_map`, you need to
add the Transifex code and the Tatoeba code. This
allows to use the transifex command line tool `tx`.

> For instance for Italian, you would add `it:ita`.


#### Step 4

Make the language available in the drop-down box
by adding it to the `UI.languages` list in the file
`app/config/core.php.template`. Read the comments in
that file for more information.

> For instance for Italian, you would add `array('ita', null, 'Italiano'),`


#### Step 5

Push your changes and create a pull request.
