<?php namespace xp;

final class xar {
  public
    $position     = 0,
    $archive      = null,
    $filename     = '';
    
  // {{{ proto [:var] acquire(string archive)
  //     Archive instance handling pool function, opens an archive and reads header only once
  static function acquire($archive) {
    static $archives= array();
    static $unpack= array(
      1 => 'a80id/a80*filename/a80*path/V1size/V1offset/a*reserved',
      2 => 'a240id/V1size/V1offset/a*reserved'
    );
    
    if ('/' === $archive{0} && ':' === $archive{2}) {
      $archive= substr($archive, 1);    // Handle xar:///f:/archive.xar => f:/archive.xar
    }

    if (!isset($archives[$archive])) {
      $current= array('handle' => fopen($archive, 'rb'), 'dev' => crc32($archive));
      $header= unpack('a3id/c1version/V1indexsize/a*reserved', fread($current['handle'], 0x0100));
      if ('CCA' != $header['id']) \raise('lang.FormatException', 'Malformed archive '.$archive);
      for ($current['index']= array(), $i= 0; $i < $header['indexsize']; $i++) {
        $entry= unpack(
          $unpack[$header['version']], 
          fread($current['handle'], 0x0100)
        );
        $current['index'][rtrim($entry['id'], "\0")]= array($entry['size'], $entry['offset'], $i);
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
    return array(
      'dev'   => $this->archive['dev'],
      'size'  => $this->archive['index'][$this->filename][0],
      'ino'   => $this->archive['index'][$this->filename][2]
    );
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
    return isset($current['index'][$file]) ? array(
      'dev'   => $current['dev'],
      'mode'  => 0100644,
      'size'  => $current['index'][$file][0],
      'ino'   => $current['index'][$file][2]
    ) : false;
  }
  // }}}
}

stream_wrapper_register('xar', 'xp\xar');
