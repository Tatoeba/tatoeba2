pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        sh 'composer install'
      }
    }
    stage('Check') {
      steps {
        // Check for flags that are too big
        sh '! find webroot/img/flags/ -name "*.svg" -size +4k | grep .'
        // Check for PHP short open tags
        sh '! grep -norz "<?[[:space:]]" src/'
        // Check for PHP syntax errors in template config file
        sh 'php -l config/app_local.php.template'
      }
    }
    stage('Test') {
      steps {
        sh 'mysql -u jenkins -pcakephp_jenkins -e \'DROP DATABASE IF EXISTS jenkins_test; CREATE DATABASE jenkins_test\';'
        sh 'cp config/app_local.php.template config/app_local.php'
        sh 'sed -i "s/{{mysql_test_user}}/jenkins/"             config/app_local.php'
        sh 'sed -i "s/{{mysql_test_password}}/cakephp_jenkins/" config/app_local.php'
        sh 'sed -i "s/{{mysql_test_db_name}}/jenkins_test/"     config/app_local.php'
        sh 'sed -i "s/{{security_salt}}/nCwygQoRC5EgFHDRNkdWS6hps74V3y9Z/" config/app_local.php'
        sh 'vendor/bin/phpunit 2> stderr.log'
        // Make sure tests did not produce any notice/error
        sh '! grep . stderr.log'
      }
    }
  }
}
