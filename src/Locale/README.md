UI translations
===============

Our UI translations are maintained in [Transifex](https://en.wiki.tatoeba.org/articles/show/interface-translation).

The files in this folder should never be modifed manually since they are generated from Transifex (see below, in "How localization works").

We commit these files because it is the simplest way to ensure that anyone who clones our repo will have all the UI translations available right off the bat.

It is also useful for us to track these files as a safety measure. If one day something goes wrong with our translations in Transifex, we still have a copy in this repository and can easily restore them.


How localization works
----------------------

1. Strings that translatable are put into specific functions in the source code (mainly `__()`, but there are [a few others](https://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html#using-translation-functions)).
2. Whenever there are new strings or strings that have been modified, we run the script [generate_pot.sh](https://github.com/Tatoeba/tatoeba2/blob/dev/tools/generate_pot.sh). It extracts all the strings from the `*.php` and `*.ctp` files into `*.pot` files.
3. We upload these `*.pot` files on a platform called [Transifex](https://en.wiki.tatoeba.org/articles/show/interface-translation), where people can translate the strings.
4. We use the script [update-translations.py](https://github.com/Tatoeba/tatoeba2/blob/dev/docs/update-translations.py) to download the updated `.po` files from Transifex. 
5. We will usually run these scripts and commit the files before updating the Tatoeba production website.


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
