tatoeba2-django
---------------

This project houses a bridge project and scripts written in python/django for tatoeba's current schema.

- Dependencies:
  - Make sure you have the python2.7, python-dev, and python-pip packages.
  - You should also have the mysql/mysql headers, but if you have Tatoeba set up, you probably do.
  - If you need any of the packages, use a sequence of commands like this to retrieve them:
  ```sh
  apt-get update
  apt-get install python-dev
  apt-get install python-pip
  etc.
  ```
  - Execute:
  ```sh
  pip install -r requirements.txt
  ```

- Configuration:
  - Copy settings.py.template to settings.py in the same directory.
  - Change settings in settings.py if necessary.
  - To Allow the manage command to drop or create table, change the MANAGE_DB variable in settings.py

- Running a manage.py script:
  - Use:
  ```sh
  python manage.py script_name_without_extension [args]
  ```
  - For example:
  ```sh
  python manage.py deduplicate --dry-run --log-dir /var/tmp
  ```

- Running the test suite:
  - You can run the accompanying test suite using:
  ```sh
  py.test
  ```
  - Remember that the test suite requires django to be able to manage tables.
