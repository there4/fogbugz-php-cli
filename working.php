#!/usr/bin/env php
<?php

// https://github.com/symfony/Console/blob/master/Helper/DialogHelper.php

// If the dependencies aren't installed, we have to bail
// and offer some help.
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
  exit("\nPlease run `composer install` to install dependencies.\n\n");
}

// Bootstrap our Silex application with the Composer autoloader
$app = require __DIR__ . '/vendor/autoload.php';

// Setup the namespace for our own namespace
$app->add('FogBugz', __DIR__ . '/src');

// Include the namespaces of the components we plan to use
use Symfony\Component\Console\Application;
use Symfony\Component\Yaml\Yaml;
use FogBugz\Command;

// Add the composer information for use in version info and such.
$project = json_decode(file_get_contents(__DIR__ . '/composer.json'));

// Instantiate our Console application, reporting info from the composer
// package information in composer.json. We decorate the `$console` with
// some vars so that we can access them via the $this->getApplication()
$console = new Application($project->description, $project->version);

// Fetch the composer info into a local var, used for debugging
$console->project = $project;

// Load our application config information
$console->config = Yaml::parse(__DIR__ . '/.config.yml');

// Register our database connection info
$console->silex = new Silex\Application();

foreach (glob(__DIR__ ."/src/FogBugz/Command/*.php") as $filename) {
  $classname = "Fogbugz\Command\\" . basename($filename, ".php");
  require $filename;
  $console->add(new $classname);
}

// Execute the console app.
$console->run();

/* End of working.php */
