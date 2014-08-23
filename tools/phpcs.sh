#!/bin/sh
# call phpcs 

./PHP_CodeSniffer-1.2.1/scripts/phpcs --report-width=150 $@ 
