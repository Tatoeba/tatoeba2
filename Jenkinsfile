pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        sh 'composer install'
        sh 'cp config/app_local.php.template config/app_local.php'
        sh 'sed -i "s/{{mysql_test_user}}/jenkins/"             config/app_local.php'
        sh 'sed -i "s/{{mysql_test_password}}/cakephp_jenkins/" config/app_local.php'
        sh 'sed -i "s/{{mysql_test_db_name}}/jenkins_test/"     config/app_local.php'
        sh 'sed -i "s/{{security_salt}}/nCwygQoRC5EgFHDRNkdWS6hps74V3y9Z/" config/app_local.php'
        sh 'bin/cake asset_compress build -f'
      }
    }
    stage('Check') {
      steps {
        sh './check.sh'
      }
    }
    stage('Test') {
      steps {
        sh 'mysql -u jenkins -pcakephp_jenkins -e \'DROP DATABASE IF EXISTS jenkins_test; CREATE DATABASE jenkins_test\';'
        sh 'vendor/bin/phpunit 2> stderr.log'
        // Make sure tests did not produce any notice/error
        sh '! grep . stderr.log'
      }
    }
  }
}
