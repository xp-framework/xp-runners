<?php namespace xp;

class Failed extends \Exception {

}

class Error extends \Exception {

}

class Test {

  private function summarize($run, $elapsed, $ignored, $failed) {
    if ($ignored) {
      foreach ($ignored as $name => $reason) {
        printf("- %s: Ignored, %s\n", $name, $reason);
      }
      echo "\n";
    }

    if ($failed) {
      foreach ($failed as $name => $exception) {
        printf(
          "- %s: %s, %s\n",
          $name,
          substr(get_class($exception), strlen(__NAMESPACE__) + 1),
          $exception->getMessage()
        );
      }
      echo "\n";
      printf("\033[41;1;37mFAILED: %d test(s) run, %d ignored, %d failed\033[0m\n", $run, sizeof($ignored), sizeof($failed));
    } else {
      printf("\033[42;1;37mOK: %d test(s) run, %d ignored\033[0m\n", $run, sizeof($ignored));
    }

    printf("%.3f seconds taken, %.2f kB peak memory usage\n", $elapsed, memory_get_peak_usage() / 1024);
  }

  public function run($tests) {
    echo '[';
    $failed= $ignored= [];
    $total= 0;

    $start= microtime(true);
    set_error_handler(function($code, $msg, $file, $line) {
      if (0 !== error_reporting()) {
        throw new Error($msg.' @ '.$file.':'.$line);
      }
    });

    foreach ($tests as $name => $closure) {
      if ('@' === $name{0}) continue;

      if ('#' === $name{0}) {
        echo 'I';
        $ignored[substr($name, 1)]= 'prefixed with #';
        continue;
      }

      $run= new Run($tests, $closure);
      $run->process('before');

      try {
        $run->invoke();
        echo '.';
      } catch (Failed $e) {
        echo 'F';
        $failed[$name]= $e;
      } catch (\Exception $e) {
        echo 'E';
        $failed[$name]= $e;
      }

      $run->process('after');
      $total++;
    }

    $stop= microtime(true);
    restore_error_handler();

    echo "]\n\n";

    $this->summarize($total, $stop - $start, $ignored, $failed);
    return $failed ? 255 : 0;
  }
}

class Run {
  private $definition, $closure;

  public function __construct($definition, $closure) {
    $this->definition= $definition;
    $this->closure= $closure;
  }

  public function process($key, $args= []) {
    $key= '@'.$key;
    if (isset($this->definition[$key])) {
      return call_user_func_array($this->definition[$key]->bindTo($this), $args);
    }
  }

  public function invoke() {
    $this->closure->bindTo($this)->__invoke();
  }

  public function __call($name, $args) {
    return $this->process($name, $args);
  }

  private final function equals($a, $b) {
    if (is_array($a)) {
      if (sizeof($a) !== sizeof($b)) return false;
      foreach ($a as $key => $val) {
        if (!array_key_exists($key, $b)) return false;
        if (!$this->equals($a[$key], $b[$key])) return false;
      }
      return true;
    } else {
      return $a === $b;
    }
  }

  public function assertEquals($a, $b) {
    if (!$this->equals($a, $b)) {
      throw new Failed('expected '.print_r($a, true).' but was '.print_r($b, true));
    }
  }

  public function assertException($class, $message, $block) {
    try {
      $block();
      throw new Failed('expected exception '.$class.' not thrown');
    } catch (\Exception $e) {
      if (!($e instanceof $class)) {
        throw new Failed('expected exception '.$class.' but have '.get_class($e));
      } else if (null !== $message && !preg_match($message, $e->getMessage())) {
        throw new Failed('expected message '.$message.' but have '.$e->getMessage());
      }
    }
  }
}

return new Test();