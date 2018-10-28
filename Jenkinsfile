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
        sh './app/Console/cake test --stderr app AllTests'
      }
    }
  }
}