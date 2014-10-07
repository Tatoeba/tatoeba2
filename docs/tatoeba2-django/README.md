tatoeba2-django
---------------

This is a bridge project between the current website's schema and python/django.

- configuration:

  - change the database connection details in settings.py.template and rename it to settings.py
  - also rename either models\_managed.py or models\_unmanaged.py to models.py (the managed file allow django to drop and create tables so be careful, necessary for running tests however) 

- dependencies:
  make sure you have python 2.7, python-pip, and mysql/mysql headers then:
  ```sh
  pip install -r requirements.txt
  ```

- running it:
  running commands is usually done using:
  ```python manage.py command```

- running tests:
  you can run accompanying tests using:
  ```py.test```
