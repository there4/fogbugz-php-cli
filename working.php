<?php

// If the dependencies aren't installed, we have to bail and offer some help.
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    exit("\nPlease run `composer install` to install dependencies.\n\n");
}

// If we're running from phar, we get these values from the stub
if (!defined("IN_PHAR")) {
    $project = json_decode(file_get_contents('composer.json'));
}

// Bootstrap our Silex application with the Composer autoloader
$app = require __DIR__ . '/vendor/autoload.php';

// Setup the namespace for our own namespace
$app->add('FogBugz', __DIR__ . '/src');

// Instantiate our Console application
$console = new FogBugz\Cli\Working(__DIR__, $project);

// Execute the console app.
$console->run();

/* End of working.php */
