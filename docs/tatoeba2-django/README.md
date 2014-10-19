tatoeba2-django
---------------

This is a bridge project between the current website's schema and python/django.

- Memory:
  - Make sure that you have at least 1.2 GB of memory to run the script.
  - If you encounter the message "Killed" before the script finishes, you need to add more.

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
  - If you will be running the script without the test suite: 
     - Copy models\_unmanaged.py to models\models.py. 
  - If you need to run the test suite:
     - Copy models\_managed.py to models\models.py. WARNING: Don't do this on a production server.

- Running the script:
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
  - Remember that the test suite requires the managed models.py file. See "Configuration" above.