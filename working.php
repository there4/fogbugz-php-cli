<?php
/*

*/
error_reporting(E_ALL | E_STRICT);
require __DIR__ . "/lib/fogbugz.php";
require __DIR__ . "/lib/io.php";
require __DIR__ . "/lib/termcolor.php";
require __DIR__ . "/lib/commands.php";
require __DIR__ . "/lib/getConfig.php";

/*******************************************************************************/
/* Configuration Begin                                                         */

$config_path = $_SERVER['HOME'] . '/.fogbugz';
$config_file = $_SERVER['HOME'] . '/.fogbugz/config.php';

/* Configuration End                                                           */
/*******************************************************************************/

$config = array();

if (is_readable($config_file)) {
  require_once $config_file;
}
else {
  echo "You don't seem to have a config file.\n";
  $config = initConfig($config_path);
}

// If the file is bad, bail
if (empty($config)) {
  exit("Invalid config file format\n");
}

// We made some changes to the format. If we need more data, let's prompt again
// and give it some defaults
if (empty($config['host'])) {
  $config = updateConfig($config_file, $config);
}

$runner = new Commands($config['user'], $config['pass'], $config['host'], $config_path);
$runner->config = $config;
$runner->dispatch($_SERVER['argv']);

exit(0);

/* End of file working.php */