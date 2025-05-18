#!/bin/bash
set -e

/var/www-prod/bin/cake session_gc
