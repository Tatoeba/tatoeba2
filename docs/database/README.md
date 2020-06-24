Tatoeba MySQL database
======================

In this folder you will find information about Tatoeba's database.


Version
-------
Tatoeba uses a MySQL database.

Version used on the prod server: 5.5.38-0+wheezy1 (Debian)



Folders content
------------------

### import

Contains scripts to fill the tables with initial data.
The scripts in this folder should be run after the ones in the `tables` folder.

### procedures

Contains the procedures. These procedures are not needed to make the website work.
The scripts in this folder should be run after the ones in the `tables` folder.

### scripts

Contains various scripts. There's nothing important in here if you're just trying 
to re-create a copy of Tatoeba's database.

### tables

This is the most important folder. 
The scripts in this folder will create the tables in the database.
Each file contains the script to create the corresponding table.
Most of the files contain a bit of documentation about each field, so you may refer
to these files to find out what is stored in a certain field.

### triggers

Contains the triggers. You will need to create the triggers for some feature to work properly.
The scripts in this folder should be run after the ones in the `tables` folder.

### updates

Whenever a change is made to the database, it is included in a script 
that is added to this folder. The file name is the date when the changes were made.
This is useful for developers who already have a local Tatoeba and need to update 
their database.

**Important:** the update scripts must be executed from the Tatoeba root folder.
