UI translations
===============

Our UI translations are maintained in [Transifex](https://en.wiki.tatoeba.org/articles/show/interface-translation).

The files in this folder should never be modified manually since we get them from Transifex (see below, in "How localization works").

We commit these files because it is the simplest way to ensure that anyone who clones our repo will have all the UI translations available right off the bat.

It is also useful for us to track these files as a safety measure. If one day something goes wrong with our translations in Transifex, we still have a copy in this repository and can easily restore them.


How localization works
----------------------

1. Translatable strings are wrapped with specific functions in the source code (mainly `__()`, but there are [a few others](https://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html#using-translation-functions)).
2. Whenever there are new or modified strings, we run the script [generate_pot.sh](https://github.com/Tatoeba/tatoeba2/blob/dev/tools/generate_pot.sh). It extracts all the strings from the `*.php` and `*.ctp` files into `*.pot` files.
3. We upload these `*.pot` files to [Transifex](https://en.wiki.tatoeba.org/articles/show/interface-translation), where people can translate the strings.
4. We use the script [update-translations.sh](https://github.com/Tatoeba/tatoeba2/blob/dev/tools/update-translations.sh) to download the updated `.po` files from Transifex.
5. We will usually run these scripts and commit the files before updating the Tatoeba production website.


How to add a new UI language
----------------------------

#### Step 1

If the language code on Transifex does not contain an underscore,
go to Step 2. Otherwise, if it does, such as zh\_CN, you need
to update `.tx/config`. In the `lang_map`, you need to add the
Transifex code, a colon, and the Transifex code without the part
after the underscore. This is because we do not yet support
per-country locales.

> For instance for Chinese, you would add `zh\_CN:zh`.

The part after the colon should match the language directory
under `src/Locale/`, so rename the directory if necessary.

#### Step 2

Make the language available in the drop-down box
by adding it to the `UI.languages` list in the file
`config/app_local.php.template`. Read the comments in
that file for more information.

> For instance for Italian, you would add `'ita' => ['Italiano', null],`


#### Step 3

Push your changes and create a pull request. The language will 
be available for testing on our [dev website](https://dev.tatoeba.org)
shortly after your pull request is merged.
