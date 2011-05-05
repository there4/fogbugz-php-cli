<?php
/*

*/
error_reporting(E_ALL | E_STRICT);
require __DIR__ . "/lib/fogbugz.php";
require __DIR__ . "/lib/io.php";
require __DIR__ . "/lib/commands.php";

/*******************************************************************************/
/* Configuration Begin                                                         */

$config_path = $_SERVER['HOME'] . '/.fogbugz';
$config_file = $_SERVER['HOME'] . '/.fogbugz/config.php';
$config_host = 'https://learningstation.fogbugz.com';

/* Configuration End                                                           */
/*******************************************************************************/

$config = array();

if (is_readable($config_file)) {
  require_once $config_file;
}
else {
  echo "You don't seem to have a config file.\n";
  
  if (!file_exists($config_path)) {
    echo "  * Making directory: ";
    if (!mkdir($config_path, 0700)) {
      echo "failed.\n";
      echo file_get_contents(__DIR__ . '/help/config.txt');
      exit(1);
    }
    echo $config_path, "\n";
  }
  
  echo "  * Making config file: ";
  if (!@touch($config_file)) {
    echo "failed.\n";
    echo file_get_contents(__DIR__ . '/help/config.txt');
    exit(1);
  }
  echo $config_file, "\n";
  
  $config['user'] = IO::getOrQuit("  * Please enter your kiln email address:");
  $config['pass'] = IO::getOrQuit("  * Please enter your kiln password:");
  
  $file = "<?php
\$config = array(
    'user' => '".$config['user']."',
    'pass' => '".$config['pass']."'
);
/* end of file config.php */
";
  
  if (!@file_put_contents($config_file, $file)) {
    echo "  * Failed to save config.\n";
    echo file_get_contents(__DIR__ . '/help/config.txt');
    exit(1);
  }
  echo "  * Config saved\n";
}

if (empty($config)) {
  exit("Invalid config file format\n");
}

$config['host'] = $config_host;

$runner = new Commands($config['user'], $config['pass'], $config['host'], $config_path);

$runner->dispatch($_SERVER['argv']);

exit(0);

/* End of file working.php */