<?php

/**
 * Command line input from StdIn
 *
 * @author Craig Davis <craig.davis@learningstation.com>
 */
class IO {
  /**
  * read a line from stdin
  *
  * echo "Enter your name: ";
  * $name = IO::read();
  * echo "Enter your age: ";
  * $age = IO::read();
  * echo "Hi $name, Isn't it Great to be $age years old?";
  *
  * @param int $length max length to fetch
  *
  * @return string stdin input
  */
  public static function read($length = 255) {
    if (!isset($GLOBALS['StdinPointer'])) {
      $GLOBALS['StdinPointer'] = fopen('php://stdin','r');
    }
    $line = fgets($GLOBALS['StdinPointer'], $length);
    return trim($line);
  }

  /**
   * getOrQuit function
   *
   * @param string $prompt
   * @param string $type [number|string]
   *
   * @return mixed value of prompt
   */
  public static function getOrQuit($prompt = "", $type = "string") {
    $value = null;

    echo $prompt, " (q to quit) ";

    while($value == null) {
      $value = IO::read();
      if ($value == 'q') {
        exit(0);
      }
      elseif (($type == "string") && !is_string($value)) {
        echo "- Please enter a string. (q to quit) \n";
        $value = null;
      }
      elseif (($type == "number") && !is_numeric($value)) {
        echo "- Please enter a number. (q to quit) \n";
        $value = null;
      }
    }
    return $value;
  }

  /**
   * confirmOrGet function
   *
   * @param string $prompt
   * @param string $type [number|string]
   *
   * @return mixed value of prompt
   */
  public static function confirmOrGet($prompt = "", $confirm = "", $type = "string") {
    $value = null;

    if ($confirm == "") {
      return self::getOrQuit($prompt, $type);
    }

    echo $prompt, " (", $confirm, ")" ," (Y, N, q to quit) ";

    while($value == null) {
      $value = IO::read();
      if ($value == 'q') {
        exit(0);
      }
      elseif (strtolower($value) == 'y') {
        return $confirm;
      }
      else {
        return self::getOrQuit("  New value:", $type);
      }
    }
    return $value;
  }

}

/* End of file io.php */