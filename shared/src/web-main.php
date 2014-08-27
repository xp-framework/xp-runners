<?php namespace xp;

final class xar {
  public
    $position     = 0,
    $archive      = null,
    $filename     = '';
    
  // {{{ proto [:var] acquire(string archive)
  //     Archive instance handling pool function, opens an archive and reads header only once
  static function acquire($archive) {
    static $archives= [];
    static $unpack= [
      1 => 'a80id/a80*filename/a80*path/V1size/V1offset/a*reserved',
      2 => 'a240id/V1size/V1offset/a*reserved'
    ];
    
    if ('/' === $archive{0} && ':' === $archive{2}) {
      $archive= substr($archive, 1);    // Handle xar:///f:/archive.xar => f:/archive.xar
    }

    if (!isset($archives[$archive])) {
      $current= ['handle' => fopen($archive, 'rb'), 'dev' => crc32($archive)];
      $header= unpack('a3id/c1version/V1indexsize/a*reserved', fread($current['handle'], 0x0100));
      if ('CCA' != $header['id']) \raise('lang.FormatException', 'Malformed archive '.$archive);
      for ($current['index']= [], $i= 0; $i < $header['indexsize']; $i++) {
        $entry= unpack(
          $unpack[$header['version']], 
          fread($current['handle'], 0x0100)
        );
        $current['index'][rtrim($entry['id'], "\0")]= [$entry['size'], $entry['offset'], $i];
      }
      $current['offset']= 0x0100 + $i * 0x0100;
      $archives[$archive]= $current;
    }

    return $archives[$archive];
  }
  // }}}
  
  // {{{ proto bool stream_open(string path, string mode, int options, string opened_path)
  //     Open the given stream and check if file exists
  function stream_open($path, $mode, $options, $opened_path) {
    sscanf(strtr($path, ';', '?'), 'xar://%[^?]?%[^$]', $archive, $this->filename);
    $this->archive= self::acquire(urldecode($archive));
    return isset($this->archive['index'][$this->filename]);
  }
  // }}}
  
  // {{{ proto string stream_read(int count)
  //     Read $count bytes up-to-length of file
  function stream_read($count) {
    $f= $this->archive['index'][$this->filename];
    if (0 === $count || $this->position >= $f[0]) return false;

    fseek($this->archive['handle'], $this->archive['offset'] + $f[1] + $this->position, SEEK_SET);
    $bytes= fread($this->archive['handle'], min($f[0] - $this->position, $count));
    $this->position+= strlen($bytes);
    return $bytes;
  }
  // }}}
  
  // {{{ proto bool stream_eof()
  //     Returns whether stream is at end of file
  function stream_eof() {
    return $this->position >= $this->archive['index'][$this->filename][0];
  }
  // }}}
  
  // {{{ proto [:int] stream_stat()
  //     Retrieve status of stream
  function stream_stat() {
    return [
      'dev'   => $this->archive['dev'],
      'size'  => $this->archive['index'][$this->filename][0],
      'ino'   => $this->archive['index'][$this->filename][2]
    ];
  }
  // }}}

  // {{{ proto bool stream_seek(int offset, int whence)
  //     Callback for fseek
  function stream_seek($offset, $whence) {
    switch ($whence) {
      case SEEK_SET: $this->position= $offset; break;
      case SEEK_CUR: $this->position+= $offset; break;
      case SEEK_END: $this->position= $this->archive['index'][$this->filename][0] + $offset; break;
    }
    return true;
  }
  // }}}
  
  // {{{ proto int stream_tell
  //     Callback for ftell
  function stream_tell() {
    return $this->position;
  }
  // }}}
  
  // {{{ proto [:int] url_stat(string path)
  //     Retrieve status of url
  function url_stat($path) {
    sscanf(strtr($path, ';', '?'), 'xar://%[^?]?%[^$]', $archive, $file);
    $current= self::acquire(urldecode($archive));
    return isset($current['index'][$file]) ? [
      'dev'   => $current['dev'],
      'mode'  => 0100644,
      'size'  => $current['index'][$file][0],
      'ino'   => $current['index'][$file][2]
    ] : false;
  }
  // }}}
}

function path($in) {
  $qn= realpath($in);
  if (false === $qn) {
    trigger_error('[bootstrap] Classpath element ['.$in.'] not found', E_USER_ERROR);
    exit(0x3d);
  } else {
    return is_dir($qn) ? $qn.DIRECTORY_SEPARATOR : $qn;
  }
}

function scan($paths, $home= '.') {
  $include= array();
  foreach ($paths as $path) {
    if (!($d= @opendir($path))) continue;
    while ($e= readdir($d)) {
      if ('.pth' !== substr($e, -4)) continue;

      foreach (file($path.DIRECTORY_SEPARATOR.$e) as $line) {
        $line= trim($line);
        if ('' === $line || '#' === $line{0}) {
          continue;
        } else if ('!' === $line{0}) {
          $pre= true;
          $line= substr($line, 1);
        } else {
          $pre= false;
        }

        if ('~' === $line{0}) {
          $qn= $home.DIRECTORY_SEPARATOR.substr($line, 1);
        } else if ('/' === $line{0} || strlen($line) > 2 && (':' === $line{1} && '\\' === $line{2})) {
          $qn= $line;
        } else {
          $qn= $path.DIRECTORY_SEPARATOR.$line;
        }

        $pre ? array_unshift($include, path($qn)) : $include[]= path($qn);
      }
    }
    closedir($d);
  }
  return $include;
}

// Bootstrap
stream_wrapper_register('xar', 'xp\xar');
$home= getenv('HOME');
$paths= scan(['.'], $home);
$merged= false;
$bootstrap= null;
do {
  foreach ($paths as $path) {
    if (DIRECTORY_SEPARATOR === $path{strlen($path) - 1}) {
      $f= $path.'__xp.php';
    } else {
      $f= 'xar://'.$path.'?__xp.php';
    }

    if (is_file($f)) {
      // DEBUG echo '-> '.$f, "\n";
      $bootstrap= $f;
      break;
    }
  }

  if ($merged && null === $bootstrap) {
    trigger_error('[bootstrap] Cannot determine boot class path', E_USER_ERROR);
    exit(0x3d);
  } else if (!$merged) {
    // DEBUG echo "[MERGE $use, $inc]\n";
    list($use, $inc)= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
    $paths= array_merge(
      $paths,
      scan(array_unique(explode(PATH_SEPARATOR, $use)), $home),
      array_map('xp\path', explode(PATH_SEPARATOR, $inc))
    );
    $merged= true;
  }
} while (null === $bootstrap);
include $bootstrap;

// Set WEB specific handling
$webroot= getenv('WEB_ROOT') ?: $_SERVER['DOCUMENT_ROOT'].'/..';
$configd= ini_get('user_dir') ?: $webroot.'/etc';

// Set error status to 516 by default - if a fatal error occurs,
// this guarantees to at least send an error code.
if ('cgi' === PHP_SAPI) {
  header('Status: 516 Unrecoverable Error');
} else if ('cli-server' === PHP_SAPI) {
  if (is_file($_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'])) {
    return false;
  }

  header('HTTP/1.0 516 Unrecoverable Error');
  $_SERVER['SCRIPT_URL']= substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], '?#'));
  $_SERVER['SERVER_PROFILE']= getenv('SERVER_PROFILE');
  define('STDIN', fopen('php://stdin', 'rb'));
  define('STDOUT', fopen('php://stdout', 'wb'));
  define('STDERR', fopen('php://stderr', 'wb'));
} else {
  header('HTTP/1.0 516 Unrecoverable Error');
}
ini_set('error_prepend_string', '<xmp>');
ini_set('error_append_string', '</xmp>');
ini_set('html_errors', 0);

exit(\xp\scriptlet\Runner::main(array($webroot, $configd, $_SERVER['SERVER_PROFILE'], $_SERVER['SCRIPT_URL'])));
