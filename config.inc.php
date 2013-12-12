<?php

// location of your git executable
define('GIT_EXEC', '/usr/bin/git');

// delimeter character, don't change this unless you change functions
// from git-utils.inc.php
define('DELIMETER', '|');

// root directory for your repositories
define('ROOT_DIR', '/srv/www/htdocs/_intern/gitrepos/');

// date format
define('DATE_FORMAT','d.m.Y h:m');

// REMEMBER TO REMOVE THIS LINE AFTER YOU HAVE MADE CONFIGURATIONS
// die('Remember to set configuration from '.__FILE__.' and remove the line which starts with "die"');