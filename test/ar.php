<?php namespace xp\io;

class Ar {

  public function extract($ar, $file, $to= '.') {
    $origin= fopen($ar, 'rb');
    while ($line= fgets($origin, 0xFF)) {
      if (2 !== sscanf($line, '--%d:%[^:]--', $length, $filename)) continue;
      if ($filename !== $file) {
        fseek($origin, $length, SEEK_CUR);
        continue;
      }

      $target= fopen($to.DIRECTORY_SEPARATOR.$filename, 'wb');
      $written= 0;
      while ($written < $length) {
        $written+= fwrite($target, fread($origin, min(0x1000, $length- $written)));
      }
      fclose($target);
      fclose($origin);
      return;
    }
    fclose($origin);
    throw new \Exception('Could not find '.$file.' in '.$ar);
  }
}

return new Ar();