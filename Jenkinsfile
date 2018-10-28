pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        sh 'composer install'
      }
    }
    stage('Test') {
      steps {
        sh 'mysql -u jenkins -pcakephp_jenkins -e \'DROP DATABASE IF EXISTS jenkins_test; CREATE DATABASE jenkins_test\';'
        writeFile file: 'app/Config/database.php', text: '''<?php
class DATABASE_CONFIG {
    public $test = array(
        \'datasource\' => \'Database/Mysql\',
        \'host\'       => \'localhost\',
        \'database\'   => \'jenkins_test\',
        \'login\'      => \'jenkins\',
        \'password\'   => \'cakephp_jenkins\',
        \'encoding\'   => \'utf8\',
    );

    public $sphinx = array(
        \'host\' => \'localhost\',
        \'port\' => 9312,
        \'sphinxql_port\' => 9306,
        \'indexdir\' => \'/var/sphinx/indices\',
        \'socket\' => \'/run/mysqld/mysqld.sock\',
        \'logdir\' => \'/var/sphinx/log\',
        \'pidfile\' => \'/var/run/sphinxsearch/searchd.pid\',
        \'binlog_path\' => \'/var/lib/sphinxsearch/data\',
    );
}'''
        sh 'cp app/Config/core.php.template app/Config/core.php'
        sh './app/Console/cake test --stderr app AllTests'
      }
    }
  }
}
