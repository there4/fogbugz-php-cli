<?php

function initConfig($config_path) {

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

  $config['user'] = IO::getOrQuit("  * Your kiln email address:");
  $config['pass'] = IO::getOrQuit("  * Your kiln password:");
  $config['host'] = IO::getOrQuit("  * The url of fogbugz (including https://):");

  return writeConfig($config_file, $config);
}

function updateConfig($config_file, $config) {

  echo "Updating your config file:\n";

  $config = array_merge(array(
    'user' => '',
    'pass' => '',
    'host' => ''
  ), $config);

  $config['user'] = IO::confirmOrGet("  * Your kiln email address:", $config['user']);
  $config['pass'] = IO::confirmOrGet("  * Your kiln password:", $config['pass']);
  $config['host'] = IO::confirmOrGet("  * The url of fogbugz (including https://):", $config['host']);
  
  return writeConfig($config_file, $config);
}

function writeConfig($config_file, $config) {
  $file = "<?php
  \$config = array(
    'user' => '".$config['user']."',
    'pass' => '".$config['pass']."',
    'host' => '".$config['host']."'
  );
/* end of file config.php */
";

  if (!@file_put_contents($config_file, $file)) {
    echo "  * Failed to save config.\n";
    echo file_get_contents(__DIR__ . '/help/config.txt');
    exit(1);
  }
  echo "  * Config saved\n";
  
  return $config;
}

/* End of file getConfig.php */
