<?php namespace xp;

// Set WEB specific handling
$home= getenv('WEB_ROOT') ?: $_SERVER['DOCUMENT_ROOT'].'/..';
$config= ini_get('user_dir') ?: $home.'/etc';
$cwd= $home;

require 'xar-support.php';
require 'scan-path.php';
require 'bootstrap.php';
require 'class-path.php';

// Set error status to 516 by default - if a fatal error occurs,
// this guarantees to at least send an error code.
switch (PHP_SAPI) {
  case 'cgi': {
    header('Status: 516 Unrecoverable Error');
    break;
  }

  case 'fpm-fcgi': {
    header('HTTP/1.0 516 Unrecoverable Error');
    $_SERVER['SCRIPT_URL']= substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], '?#'));
    $_SERVER['SERVER_PROFILE']= getenv('SERVER_PROFILE');
    break;
  }

  case 'cli-server': {
    if (is_file($_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'])) {
      return false;
    }
    header('HTTP/1.0 516 Unrecoverable Error');
    $_SERVER['SCRIPT_URL']= substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], '?#'));
    $_SERVER['SERVER_PROFILE']= getenv('SERVER_PROFILE');
    define('STDIN', fopen('php://stdin', 'rb'));
    define('STDOUT', fopen('php://stdout', 'wb'));
    define('STDERR', fopen('php://stderr', 'wb'));
    break;
  }

  default: {
    header('HTTP/1.0 516 Unrecoverable Error');
    break;
  }
}

ini_set('error_prepend_string', '<xmp>');
ini_set('error_append_string', '</xmp>');
ini_set('html_errors', 0);

foreach ($include as $path => $_) {
  \lang\ClassLoader::registerPath($path);
}

try {
  exit(\xp\scriptlet\Runner::main(array($home, $config, $_SERVER['SERVER_PROFILE'], $_SERVER['SCRIPT_URL'])));
} catch (\lang\SystemExit $e) {
  if ($message= $e->getMessage()) echo $message, "\n";
  exit($e->getCode());
}